<?php

/**
 * Class Offsprout_Include
 *
 * filter the retrieval of options and some API functions
 */
class Offsprout_Include{

    function __construct() {
        add_filter( 'option_ocb_site_settings', array( $this, 'filter_get_ocb_site_settings' ) );
        add_filter( 'pre_update_option_ocb_site_settings', array( $this, 'filter_update_ocb_site_settings' ) );
        add_filter( 'ocb_api_update_option', array( $this, 'save_global_css_file' ), 10, 2 );
        add_filter( 'wp_insert_post_data', __CLASS__ . '::no_save_content', 10, 2 );

        add_action( 'wp_enqueue_scripts', array( $this, 'login_session_expired' ) );
        add_action( 'login_enqueue_scripts', array( $this, 'login_session_expired_styles' ) );
        add_filter( 'wp_refresh_nonces', array( $this, 'refresh_nonces' ), 30, 2 );

        add_action( 'ocb_save_page_backup', array( $this, 'backup_ocb_tree_content' ), 10, 2 );
        add_action( 'save_post', array( $this, 'save_meta_revisions' ), 10, 2 );
        add_action( 'wp_restore_post_revision', array( $this, 'restore_meta_revisions' ), 10, 2 );
        add_action( 'wp_revisions_to_keep', array( $this, 'limit_revisions' ), 9, 2 );

        add_filter( 'content_save_pre', array( $this, 'ocb_targeted_link_rel' ), 9 );

        /*add_filter( 'rest_pre_insert_post', function ( $prepared_post, WP_REST_Request $request ) {
            // The key is same as we set in JS code
            $is_gutenberg_post = $request->get_param( 'isGutenbergPost' );

            Offsprout_Debug::write_debug( $prepared_post );

            // Update your flags here, like
            $GLOBALS['is_gutenberg_post'] = $is_gutenberg_post;

            return $prepared_post;
        }, 10, 2 );

        add_action( 'wp_insert_post', function ( $post_id, $post ) {

            if ( ! empty( $GLOBALS['is_gutenberg_post'] ) ) {
                return;
            }

        }, 10, 2 );*/

        //Gutenberg
        //add_action( 'enqueue_block_editor_assets', array( $this, 'gutenberg_enqueue' ) );
    }

    static function separate_site_options(){
        return array(
            'global_css',
            'testimonials',
        );
    }

    /**
     * Show login modal in builder if session expired
     */
    function login_session_expired() {
        // only add scripts and styles in builder mode
        if ( OCB_EDIT ) {

            // add javascript file
            wp_register_script( 'wp_auth_check', '/wp-includes/js/wp-auth-check.js' , array('heartbeat'), false, 1);
            wp_localize_script( 'wp_auth_check', 'authcheckL10n', array(
                'beforeunload' => __('Your session has expired. You can log in again from this page or go to the login page.'),
                'interval' => apply_filters( 'wp_auth_check_interval', 1 * MINUTE_IN_SECONDS ), // default interval is 3 minutes
            ) );
            wp_enqueue_script ('wp_auth_check');

            // add css file
            wp_enqueue_style( 'wp_auth_check','/wp-includes/css/wp-auth-check.css', array( 'dashicons' ), NULL, 'all' );

            // add the login html to the page
            add_action( 'wp_print_footer_scripts', 'wp_auth_check_html', 5 );
        }
    }

    // make sure the stylesheet appears on the lightboxed login iframe
    function login_session_expired_styles() {
        wp_enqueue_style( 'wp_auth_check','/wp-includes/css/wp-auth-check.css', array( 'dashicons' ), NULL, 'all' );
    }

    /**
     * Refresh the nonce in the builder so that the builder can be saved after modal login
     * Triggered by heartbeat
     *
     * @param $response
     * @param $data
     * @return mixed
     */
    public function refresh_nonces( $response, $data ) {
        $response['offsprout-refresh-nonce'] = [
            'wp_rest' => wp_create_nonce( 'wp_rest' ),
        ];

        return $response;
    }

    /**
     * Reattach the global_css property to the site options
     *
     * @param $site_settings
     * @return mixed
     */
    function filter_get_ocb_site_settings( $site_settings ){

        $separate_options = self::separate_site_options();

        foreach( $separate_options as $option ){
            $separate_option = get_option( 'ocb_site_settings_' . $option );

            if( $separate_option )
                $site_settings[$option] = $separate_option;
        }


        return $site_settings;

    }

    /**
     * Saving global_css with the site settings seems to cause issues so break it up into its own option
     *
     * @param $site_settings
     * @return mixed
     */
    function filter_update_ocb_site_settings( $site_settings ){

        $separate_options = self::separate_site_options();

        foreach( $separate_options as $option ){
            if( isset( $site_settings[$option] ) ) {
                $separate_value = $site_settings[$option];
                unset( $site_settings[$option] );
                update_option( 'ocb_site_settings_' . $option, $separate_value );
            }
        }

        return $site_settings;

    }

    function save_global_css_file( $value, $option ){
        if( $option != 'ocb_site_css' )
            return $value;

        Offsprout_Model::save_global_css_file( $value );

        return $value;
    }

    /**
     * Don't save content here if builder is active and there is builder content
     *
     * @param $data
     * @param $postarr
     */
    static public function no_save_content( $data, $postarr ){
        if(
            (
                ( isset( $postarr['offsprout-builder-active'] ) && $postarr['offsprout-builder-active'] == 1 )
                || get_transient( 'ocb_gutenberg_saving' ) == $postarr['ID']
            )
            && get_post_meta( $postarr['ID'], 'ocb_tree_content', true )
        ){

            if( isset( $data['content'] ) )
                unset( $data['content'] );

            if( isset( $data['post_content'] ) )
                unset( $data['post_content'] );

            delete_transient('ocb_gutenberg_saving');
            update_post_meta( $postarr['ID'], 'ocb_active', 1 );

        } elseif( isset( $postarr['ID'] ) ) {

            update_post_meta( $postarr['ID'], 'ocb_active', 0 );

        }

        return $data;
    }

    /**
     * Save backup versions of ocb_tree_content
     *
     * @param $info
     * @param $value
     */
    public function backup_ocb_tree_content( $info, $value ){

        //get the backup
        $backups = get_post_meta( $info['post_id'], 'ocb_tree_content_backups', true );
        if( $backups == false )
            $backups = array();

        $time = time();
        $new_key = 'ocb_tree_content_' . $time;

        //add to the front of the array
        array_unshift( $backups, $time );

        $num = Offsprout_Model::get_revision_limit( $info['post_id'] );

        //if more than 5 items, chop off the last one
        if ( count( $backups ) > $num ) {
            $to_deletes = $backups;
            $to_deletes = array_slice( $to_deletes, $num );

            $new_backups = $backups;
            array_splice( $new_backups, $num );
            $backups = $new_backups;

            if( is_array( $to_deletes ) ){
                foreach( $to_deletes as $to_delete ){
                    delete_post_meta( $info['post_id'], 'ocb_tree_content_' . $to_delete );
                }
            }
        }

        //update_post_meta( $info['post_id'], 'ocb_tree_content_backup', $backup );
        update_post_meta( $info['post_id'], 'ocb_tree_content_backups', $backups );
        update_post_meta( $info['post_id'], $new_key, $value );

        self::delete_extra_backups( $info['post_id'], $backups );
    }

    /**
     * Posts were saving more than the 5 backups that they should have
     *
     * @param $post_id
     * @param bool $backups
     */
    static function delete_extra_backups( $post_id, $backups = false ){

        global $wpdb;

        if( ! $backups )
            $backups = get_post_meta( $post_id, 'ocb_tree_content_backups', true );

        $backups = array_map( function( $key ){
            return 'ocb_tree_content_' . $key;
        }, $backups );

        $table = $wpdb->prefix . 'postmeta';

        //Get all values with ocb_tree_content for this post
        $values = $wpdb->get_results(
            $wpdb->prepare(
                "SELECT meta_key FROM {$table} WHERE post_id = %d AND meta_key LIKE '%ocb_tree_content%'",
                $post_id
            )
        );

        foreach( $values as $value ){

            if( ! preg_match( '/ocb_tree_content_[\d]{10}/', $value->meta_key ) )
                continue;

            if( ! in_array( $value->meta_key, $backups ) ){
                delete_post_meta( $post_id, $value->meta_key );
            }

        }

    }

    static public function get_revision_meta_keys(){
        return array( 'ocb_tree_content', 'ocb_page_css', 'ocb_skins_used' );
    }

    /**
     * Add meta to revisions
     * https://johnblackbourn.com/post-meta-revisions-wordpress/
     *
     * @param $post_id
     * @param $post
     */
    public function save_meta_revisions( $post_id, $post ){

        $parent_id = wp_is_post_revision( $post_id );
        $meta_keys = self::get_revision_meta_keys();

        if ( $parent_id ) {

            $parent  = get_post( $parent_id );

            foreach( $meta_keys as $key ){
                $meta = get_post_meta( $parent->ID, $key, true );

                if ( false !== $meta )
                    add_metadata( 'post', $post_id, $key, $meta );
            }

        }

    }

    /**
     * Restores meta when post is restored
     *
     * @param $post_id
     * @param $revision_id
     */
    public function restore_meta_revisions( $post_id, $revision_id ) {

        $post     = get_post( $post_id );
        $revision = get_post( $revision_id );
        $meta_keys = self::get_revision_meta_keys();

        foreach( $meta_keys as $key ){
            $meta  = get_metadata( 'post', $revision->ID, $key, true );

            if ( false !== $meta )
                update_post_meta( $post_id, $key, $meta );
            else
                delete_post_meta( $post_id, $key );
        }

    }

    /**
     * Limit the number of revisions that WordPress stores to avoid memory issues when saving
     *
     * @param $num
     * @param $post
     * @return int
     */
    public function limit_revisions( $num, $post ){
        $defined_num = defined( 'WP_POST_REVISIONS' ) ? WP_POST_REVISIONS : false;

        if( $defined_num && is_int( $defined_num ) )
            $num = $defined_num;
        else
            $num = 5;

        return $num;
    }

    /**
     * Based on wp_targeted_link_rel()
     *
     * Adds rel noreferrer and noopener to all HTML A elements that have a target.
     *
     * @since 5.1.0
     *
     * @param string $text Content that may contain HTML A elements.
     * @return string Converted content.
     */
    function ocb_targeted_link_rel( $text ) {
        remove_filter( 'content_save_pre', 'wp_targeted_link_rel' );

        // Don't run (more expensive) regex if no links with targets.
        if ( stripos( $text, 'target' ) === false || stripos( $text, '<a ' ) === false || is_serialized( $text ) || stripos( $text, 'rel=' ) !== false ) {
            return $text;
        }

        $script_and_style_regex = '/<(script|style).*?<\/\\1>/si';

        preg_match_all( $script_and_style_regex, $text, $matches );
        $extra_parts = $matches[0];
        $html_parts  = preg_split( $script_and_style_regex, $text );

        foreach ( $html_parts as &$part ) {
            $part = preg_replace_callback( '|<a\s([^>]*target\s*=[^>]*)>|i', 'wp_targeted_link_rel_callback', $part );
        }

        $text = '';
        for ( $i = 0; $i < count( $html_parts ); $i++ ) {
            $text .= $html_parts[ $i ];
            if ( isset( $extra_parts[ $i ] ) ) {
                $text .= $extra_parts[ $i ];
            }
        }

        return $text;
    }

    public function gutenberg_enqueue(){
        wp_register_script( 'offsprout-reqs', OCB_MAIN_DIR . 'builder/app/build/vendor.js' );
        wp_enqueue_script(
            'offsprout-isgutenberg-script',
            OCB_MAIN_DIR . 'admin/js/offsprout-gb-bundle.js',
            ['wp-editor', 'wp-element', 'wp-i18n', 'wp-edit-post', 'wp-plugins', 'wp-element', 'wp-data', 'wp-url', 'wp-compose', 'lodash', 'offsprout-reqs']
        );
    }

}
new Offsprout_Include();