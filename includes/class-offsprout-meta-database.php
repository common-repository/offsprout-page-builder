<?php

class Offsprout_Meta_Database {

    private $db_option = 'ocb_meta_db_version';
    private $db_version = '1';

    public function __construct() {
        $this->create_table();

        add_filter( 'get_post_metadata', array( 'Offsprout_Meta_Database', 'filter_get_meta' ), 10, 4 );
        add_filter( 'update_post_metadata', array( 'Offsprout_Meta_Database', 'filter_update_meta' ), 10, 5 );
    }

    static function get_table_name(){
        global $wpdb;

        return $wpdb->prefix . 'ocb_revisions';
    }

    function create_table(){

        $table_version = get_option( $this->db_option );

        if( ! $table_version )
            $table_version = 0;

        if( $table_version < $this->db_version ){
            $this->do_create_table();
        }

    }

    function do_create_table(){

        global $wpdb;

        $charset_collate = $wpdb->get_charset_collate();
        $table_name = self::get_table_name();

        $sql = "CREATE TABLE $table_name (
          meta_id bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT,
          post_id bigint(20) UNSIGNED NOT NULL,
          meta_key varchar(255),
          meta_value longtext,
          PRIMARY KEY  (meta_id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        update_option( $this->db_option, $this->db_version );

    }

    static function allow_regular_meta(){
        return array(
            'ocb_tree_content_backups', 'ocb_tree_content'
        );
    }

    static function filter_update_meta( $check, $object_id, $meta_key, $meta_value, $prev_value ){

        if( strpos( $meta_key, 'ocb_tree_content' ) === false || in_array( $meta_key, self::allow_regular_meta() ) )
            return null;

        $result = self::update_meta( $object_id, $meta_key, $meta_value );

        //Short circuit the update
        return false;
    }

    static function filter_get_meta( $check, $object_id, $meta_key, $single ){

        if( strpos( $meta_key, 'ocb_tree_content' ) === false || in_array( $meta_key, self::allow_regular_meta() ) )
            return null;

        $result = self::get_meta( $object_id, $meta_key, $single );

        //Return meta
        return $result;
    }

    static function update_meta( $post_id, $meta_key, $meta_value, $run_convert = true ){
        global $wpdb;

        if( ! is_serialized( $meta_value ) )
            $meta_value = maybe_serialize( $meta_value );

        $table = self::get_table_name();

        $existing = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_id FROM {$table} WHERE post_id = %d AND meta_key = %s",
                $post_id,
                $meta_key
            )
        );

        if( $existing ){
            $result = $wpdb->update( $table, array( 'meta_value' => $meta_value ), array( 'meta_id' => $existing ) );
        } else{
            $result = $wpdb->insert( $table, array( 'meta_value' => $meta_value, 'post_id' => $post_id, 'meta_key' => $meta_key ) );
        }

        //Run the conversion
        if( $run_convert )
            self::convert( $post_id );

        return $result;
    }

    static function get_meta( $post_id, $meta_key, $single ){
        global $wpdb;

        $table = self::get_table_name();
        $value = $wpdb->get_var(
            $wpdb->prepare(
                "SELECT meta_value FROM {$table} WHERE post_id = %d AND meta_key = %s",
                $post_id,
                $meta_key
            )
        );

        if( ! $value ){
            remove_filter( 'get_post_metadata', array( 'Offsprout_Meta_Database', 'filter_get_meta' ), 10 );

            //If no value, see if the value exists in the wordpress meta table
            $value = get_post_meta( $post_id, $meta_key, $single );

            add_filter( 'get_post_metadata', array( 'Offsprout_Meta_Database', 'filter_get_meta' ), 10, 4 );

            if( $single )
                $value = array( $value );

            return $value;
        }

        $value = unserialize( $value );

        if( $single )
            $value = array( $value );

        return $value;
    }

    static function delete_meta( $post_id, $meta_key ){
        global $wpdb;

        $result = $wpdb->delete( self::get_table_name(), array( 'post_id' => $post_id, 'meta_key' => $meta_key ) );

        return $result;
    }

    /**
     * If a post doesn't have meta in this table yet, run a conversion to store the meta in this table rather than in post meta
     *
     * @param $post_id int
     */
    static function convert( $post_id ){

        global $wpdb;

        $post_id = (int) $post_id;
        $table = $wpdb->prefix . 'postmeta';

        //Get all values with ocb_tree_content for this post
        $values = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_key FROM {$table} WHERE post_id = %d AND meta_key LIKE '%ocb_tree_content%'",
                $post_id
            )
        );

        //Save them to this table
        if( $values ){

            foreach( $values as $value ){

                //Will already be serialized so don't re-serialize
                if( ! in_array( $value->meta_key, self::allow_regular_meta() ) ){

                    $full = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT * FROM {$table} WHERE post_id = %d AND meta_key = %s",
                            $post_id,
                            $value->meta_key
                        )
                    );

                    self::update_meta( $full->post_id, $full->meta_key, $full->meta_value, false );

                    //Delete the post_meta values
                    delete_post_meta( $full->post_id, $full->meta_key );
                }


            }

        }

        self::delete_extra_backups( $post_id );

    }

    /**
     * If a post doesn't have meta in this table yet, run a conversion to store the meta in this table rather than in post meta
     *
     */
    static function convert_all(){

        global $wpdb;

        $table = $wpdb->prefix . 'postmeta';

        //Get all values with ocb_tree_content for this post
        $values = $wpdb->get_results( "SELECT meta_key, post_id FROM {$table} WHERE meta_key LIKE '%ocb_tree_content_%'" );

        //Limit to values that are backups
        $values = array_filter( $values, function( $value ){
            if( in_array( $value->meta_key, self::allow_regular_meta() ) )
                return false;

            return true;
        });

        //Sort results by post_id
        usort( $values, function( $a, $b ){
            if ($a->post_id == $b->post_id) {
                return 0;
            }
            return ($a->post_id < $b->post_id) ? -1 : 1;
        });

        $count = count( $values );
        $i = 1;

        //Save them to this table
        if( $values ){

            $current_id = 0;

            foreach( $values as $value ){

                //Will already be serialized so don't re-serialize
                if( ! in_array( $value->meta_key, self::allow_regular_meta() ) ){

                    $full = $wpdb->get_row(
                        $wpdb->prepare(
                            "SELECT * FROM {$table} WHERE post_id = %d AND meta_key = %s",
                            $value->post_id,
                            $value->meta_key
                        )
                    );

                    self::update_meta( $full->post_id, $full->meta_key, $full->meta_value, false );

                    //Delete the post_meta values
                    delete_post_meta( $full->post_id, $full->meta_key );

                    //If we're moving on to another post, it's not the first post, or we're operating on the final post, delete extra backups
                    if( ( $value->post_id != $current_id && $current_id != 0 ) || $i == $count )
                        self::delete_extra_backups( $current_id );

                    if( $i == $count )
                        self::delete_extra_backups( $value->post_id );

                    $current_id = $value->post_id;
                    $i++;
                }

            }

        }

    }

    /**
     * Make sure we're only keeping the backups in ocb_tree_content_backups
     *
     * @param $post_id
     */
    static function delete_extra_backups( $post_id ){

        global $wpdb;

        $table = self::get_table_name();

        //Get all values with ocb_tree_content for this post
        $values = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_key FROM {$table} WHERE post_id = %d AND meta_key LIKE '%ocb_tree_content%'",
                $post_id
            )
        );

        if( is_array( $values ) ){

            $backups = get_post_meta( $post_id, 'ocb_tree_content_backups', true );

            $backups = array_map( function( $key ){
                return 'ocb_tree_content_' . $key;
            }, $backups );

            foreach( $values as $value ){

                if( ! preg_match( '/ocb_tree_content_[\d]{10}/', $value->meta_key ) )
                    continue;

                if( ! in_array( $value->meta_key, $backups ) ){
                    self::delete_meta( $post_id, $value->meta_key );
                }

            }
        }


    }

}
new Offsprout_Meta_Database();

function offsprout_update_meta_db(){
    if( isset( $_GET['oupdate_meta_db'] ) && $_GET['oupdate_meta_db'] == 1 && is_user_logged_in() ){
        Offsprout_Meta_Database::convert( get_the_ID() );
    }
    if( isset( $_GET['oupdate_meta_db_all'] ) && $_GET['oupdate_meta_db_all'] == 1 && is_user_logged_in() ){
        Offsprout_Meta_Database::convert_all();
    }
}
add_action( 'wp_footer', 'offsprout_update_meta_db' );