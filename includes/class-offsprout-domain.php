<?php

class Offsprout_Domain{

    /**
     * Update the options in the site that need to be updated
     *
     * @param $domain
     * @param $force_http
     */
    public static function update_site_url_options( $domain, $force_http = false ){
        $http = $force_http ? $force_http :
            ( is_ssl() ? 'https://' : 'http://' );

        update_option( 'siteurl', $http . $domain );
        update_option( 'home', $http . $domain );
    }

    /**
     * Update multisite options
     *
     * @param $blog_id
     * @param $domain
     */
    public static function update_multisite_blog_details( $blog_id, $domain ){
        $blog = get_blog_details( $blog_id );
        $blog->domain = $domain;
        $blog->siteurl = 'http://' . $domain;
        update_blog_details( $blog_id, $blog );
    }

    /**
     * @param $domain
     * @param bool $blog_id
     * @param int $active
     * @param bool $delete_old
     */
    public static function map_domain( $domain, $blog_id = false, $active = 1, $delete_old = false ){

        if( $blog_id == false )
            $blog_id = get_current_blog_id();

        //For the Domain Mapping Plugin
        global $wpdb;

        //Delete any other domains for this blog ID if the delete_old option is set
        /*if( $delete_old )
            $wpdb->delete( $wpdb->dmtable, array( 'blog_id' => $blog_id ) );*/

        //See if there are already an entries for this blog id
        $existing_domains = $wpdb->get_col(
            $wpdb->prepare("
                    SELECT domain FROM {$wpdb->dmtable}
                    WHERE blog_id = %d
                ",
                array( $blog_id ) )
        );

        do_action('dm_handle_actions_init', $domain);

        //if so, and we're creating a new active domain entry for a particular blog_id, then switch other domains to secondary
        if( is_array( $existing_domains ) && ! empty( $existing_domains ) && $active ){

            foreach ( $existing_domains as $existing_domain ) {

                if ( $existing_domain != $domain ) {

                    do_action( 'dm_handle_actions_primary', $existing_domain );

                    $wpdb->update( $wpdb->dmtable, array( 'active' => 0 ), array( 'domain' => $existing_domain, 'blog_id' => $blog_id ) );

                }

            }

        }

        //See if there's already an entry for this domain - if so, then just update the existing entry
        $existing_blog_id = $wpdb->get_var(
            $wpdb->prepare("
                    SELECT blog_id FROM {$wpdb->dmtable}
                    WHERE domain = '%s'
                ",
                array( $domain ) )
        );

        if( $existing_blog_id ){

            if( $active )
                do_action('dm_handle_actions_add', $domain);

            do_action('dm_handle_actions_primary', $domain);

            $wpdb->update( $wpdb->dmtable, array( 'active' => $active ), array( 'domain' => $domain, 'blog_id' => $blog_id ) );

        } else {

            do_action('dm_handle_actions_add', $domain);

            if( strpos( $domain, 'www.' ) !== false ){

                $non_www = str_replace( 'www.', '', $domain );

                $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->dmtable} ( `blog_id`, `domain`, `active` ) VALUES ( %d, %s, %d )", $blog_id, $domain, $active ) );
                $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->dmtable} ( `blog_id`, `domain`, `active` ) VALUES ( %d, %s, %d )", $blog_id, $non_www, 0 ) );

            } else {

                $wpdb->query( $wpdb->prepare( "INSERT INTO {$wpdb->dmtable} ( `blog_id`, `domain`, `active` ) VALUES ( %d, %s, %d )", $blog_id, $domain, $active ) );

            }

        }

    }

    public static function find_and_replace( $new, $old, $options = array( 'custom', 'content', 'options' ), $type = 'replace', $link = false ){
        self::new_domain_replace_links( $new, $old, $options, $type, $link );
    }

    /**
     * Replace the links and other data in the content
     *
     * @param $domain
     * @param bool $olddomain
     * @param array $options
     * @return array
     */
    public static function new_domain_replace_links( $domain, $olddomain = false, $options = array( 'custom', 'content', 'options' ), $type = 'replace', $links = true ){

        global $wpdb;

        if( $olddomain == false ){
            $olddomain = get_site_url();
        }

        if( $links ) {

            $olddomain = esc_url( $olddomain );
            $olddomain = str_replace( 'http://', '', $olddomain );
            $olddomain = str_replace( 'https://', '', $olddomain );

        } else {

            if( strpos( $olddomain, ' ' ) !== false )
                $olddomain = str_replace( ' ', '+', $olddomain );

        }

        if( $links ) {

            $domain = esc_url( $domain );
            $domain = str_replace( 'http://', '', $domain );
            $domain = str_replace( 'https://', '', $domain );

        } else {

            if ( strpos( $domain, ' ' ) !== false )
                $domain = str_replace( ' ', '+', $domain );

        }

        $results = array();

        $queries = array(
            'content' =>		array("UPDATE $wpdb->posts SET post_content = replace(post_content, %s, %s)",  __('Content Items (Posts, Pages, Custom Post Types, Revisions)','offsprout') ),
            'excerpts' =>		array("UPDATE $wpdb->posts SET post_excerpt = replace(post_excerpt, %s, %s)", __('Excerpts','offsprout') ),
            'attachments' =>	array("UPDATE $wpdb->posts SET guid = replace(guid, %s, %s) WHERE post_type = 'attachment'",  __('Attachments','offsprout') ),
            'links' =>			array("UPDATE $wpdb->links SET link_url = replace(link_url, %s, %s)", __('Links','offsprout') ),
            'custom' =>			array("UPDATE $wpdb->postmeta SET meta_value = replace(meta_value, %s, %s)",  __('Custom Fields','offsprout') ),
            'guids' =>			array("UPDATE $wpdb->posts SET guid = replace(guid, %s, %s)",  __('GUIDs','offsprout') )
        );

        foreach($options as $option){
            if( $option == 'custom' ){
                $n = 0;
                $row_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->postmeta" );
                $page_size = 10000;
                $pages = ceil( $row_count / $page_size );

                $results['pages'] = $pages;

                for( $page = 0; $page < $pages; $page++ ) {
                    $current_row = 0;
                    $start = $page * $page_size;
                    $end = $start + $page_size;
                    $pmquery = "SELECT * FROM $wpdb->postmeta WHERE meta_value <> ''";
                    $items = $wpdb->get_results( $pmquery );
                    $results['items'] = $items;
                    foreach( $items as $item ){
                        $value = $item->meta_value;

                        if( strpos( $value, $olddomain ) !== false )
                            $results['yes'][$item->meta_key] = $value;
                        else
                            $results['no'][$item->meta_key] = $value;

                        if( trim($value) == '' )
                            continue;

                        $edited = self::new_domain_unserialize_replace( $olddomain, $domain, $value, false, $type );

                        if( $edited != $value ){

                            $fix = $wpdb->update( $wpdb->postmeta, array( 'meta_value' => $edited ), array( 'meta_id' => $item->meta_id ) );

                            if( $fix ){

                                $results['changed'][$item->meta_key] = array( 'old' => $value, 'new' => $edited );

                                //Need to delete the cache since we're manually updating the meta value and this is normally done through update_post_meta
                                wp_cache_delete($item->post_id, 'post_meta');
                                $n++;

                            }else {
                                $results['changed'][$item->meta_key] = 'Could not run query on meta_id ' . $item->meta_id . 'in table ' . $wpdb->postmeta;
                            }
                        }
                    }
                }
                $results[$option] = array($n, $queries[$option][1]);
            }
            elseif( $option == 'options' ){
                $n = 0;
                $row_count = $wpdb->get_var( "SELECT COUNT(*) FROM $wpdb->options" );
                $page_size = 10000;
                $pages = ceil( $row_count / $page_size );

                $results['pages'] = $pages;

                for( $page = 0; $page < $pages; $page++ ) {
                    $current_row = 0;
                    $start = $page * $page_size;
                    $end = $start + $page_size;
                    $pmquery = "SELECT * FROM $wpdb->options WHERE option_value <> ''";
                    $items = $wpdb->get_results( $pmquery );
                    $results['items'] = $items;
                    foreach( $items as $item ){
                        $value = $item->option_value;

                        if( strpos( $value, $olddomain ) !== false )
                            $results['yes'][$item->option_name] = $value;
                        else
                            $results['no'][$item->option_name] = $value;

                        if( trim($value) == '' )
                            continue;

                        $edited = self::new_domain_unserialize_replace( $olddomain, $domain, $value, false, $type );

                        $update = $wpdb->update( $wpdb->options, array( 'option_value' => $edited ), array( 'option_id' => $item->option_id ) );

                    }
                }

                $results[$option] = array($n, $queries[$option][1]);

            }
            elseif( $option == 'content' ){

                if( ! $type || $type == 'replace' ){
                    $result = $wpdb->query( $wpdb->prepare( $queries[$option][0], $olddomain, $domain) );
                    $results[$option] = array($result, $queries[$option][1]);
                } else {

                    $results = $wpdb->get_results( "SELECT * FROM $wpdb->posts", ARRAY_A );
                    foreach( $results as $each ){

                        $post_content = self::replace_data( $olddomain, $domain, $each['post_content'], $type );
                        $wpdb->update( $wpdb->posts, array( 'post_content' => $post_content ), array( 'ID' => $each['ID'] ) );

                    }

                }
            } else {
                $result = $wpdb->query( $wpdb->prepare( $queries[$option][0], $olddomain, $domain) );
                $results[$option] = array($result, $queries[$option][1]);
            }
        }
        return $results;

    }

    public static function new_domain_unserialize_replace( $from = '', $to = '', $data = '', $serialised = false, $type = false ) {
        try {
            if ( is_string( $data ) && ( $unserialized = @unserialize( $data ) ) !== false ) {
                $data = self::new_domain_unserialize_replace( $from, $to, $unserialized, true );
            }
            elseif ( is_array( $data ) ) {
                $_tmp = array( );
                foreach ( $data as $key => $value ) {
                    $_tmp[ $key ] = self::new_domain_unserialize_replace( $from, $to, $value, false );
                }
                $data = $_tmp;
                unset( $_tmp );
            }
            else {
                if ( is_string( $data ) )
                    $data = self::replace_data( $from, $to, $data, $type );

            }
            if ( $serialised )
                return serialize( $data );
        } catch( Exception $error ) {
        }
        return $data;
    }

    public static function replace_data( $from, $to, $data, $type ){

        switch( $type ){
            case 'clean':
                $data = preg_replace( '@<([/]*)span([^>]*)>@', '', $data );
                $data = preg_replace( '@<!--\[if gte mso 9]>([^\[]*)\[endif]-->@', '', $data );
                $data = preg_replace( '@<p([^>]*)>@', '<p>', $data );
                break;

            case 'divs':
                $data = preg_replace( '@<([/]*)div([^>]*)>@', '', $data );
                break;

            case 'relativeSlash':
                $data = preg_replace( '@<a([a-zA-Z-"=\s]*)href="(?!http://)@', '<a$1href="/', $data );
                break;

            case 'replace':
            default:
                $data = str_replace( $from, $to, $data );

                //Because sprouts are saved as encoded data, this replaces things like domain.com/sites/10 that have slashes
                $data = str_replace( urlencode( $from ), urlencode( $to ), $data );
                break;
        }

        return $data;

    }
}