<?php

class Offsprout_Admin_Dependencies{
    function __construct() {
        $this->require_files();

        add_action( 'init', array( $this, 'check_permission' ) );
        add_action( 'admin_notices', array( $this, 'ssl_notice' ) );
        add_action( 'admin_notices', array( $this, 'version_mismatch_notice' ) );
        add_action( 'admin_print_scripts', __CLASS__ . '::admin_vars' );
    }

    function check_permission(){
        $permissions = Offsprout_Model::get_permissions();

        $uri = isset( $_SERVER['REQUEST_URI'] ) ? $_SERVER['REQUEST_URI'] : false;
        $is_post_screen = strpos( $uri, '/post.php') !== false;
        $is_edit_screen = isset( $_GET['action'] ) && $_GET['action'] == 'edit' ? true : false;

        if( $is_post_screen && $is_edit_screen ){
            if( isset( $permissions['access_wordpress_post_admin'] ) && ! $permissions['access_wordpress_post_admin'] )
                wp_safe_redirect( add_query_arg( 'pageEdit', 1, get_home_url() ) );
        } else {
            if( isset( $permissions['access_wordpress_admin'] ) && ! $permissions['access_wordpress_admin'] )
                wp_safe_redirect( add_query_arg( 'pageEdit', 1, get_home_url() ) );
        }
    }

    function require_files(){
        require_once OCB_DIR . 'admin/class-offsprout-add-editor-button.php';
        require_once OCB_DIR . 'admin/class-offsprout-admin-settings.php';

        if( defined('DOING_AJAX') && DOING_AJAX ){
            require_once OCB_DIR . 'admin/class-offsprout-admin-ajax.php';
        }
    }

    public static function admin_vars(){
        include OCB_DIR . 'admin/screens/admin-settings-js-config.php';
    }

    /**
     * Adds a notice in WP Admin to explain why it's important that the whole site load over either http or https (preferable)
     *
     * Mixed http and https cause problems uploading images on the front end as well as querying the API
     *
     * This was confirmed with WP Engine on 2/1/18
     */
    public function ssl_notice(){
        // Check transient, if available display notice
        $different_ssl = Offsprout_Model::different_ssl();
        if( $different_ssl && ( defined('WP_SITEURL') || defined('WP_HOME') ) ){

            if( $different_ssl == 1 ){
                ?> <div class="notice notice-error">
                    <p>Your admin area is loading over https, however your site is loading over insecure http. This is known to cause errors in plugins that allow front end media uploads or API interactions.</p><?php

                if( defined( 'WP_SITEURL' ) && strpos( WP_SITEURL, 'http:' ) !== false )
                    ?> <p>WP_SITEURL constant is defined and contains http. This must be changed to https.</p> <?php

                if( defined( 'WP_HOME' ) && strpos( WP_HOME, 'http:' ) !== false )
                    ?> <p>WP_HOME constant is defined and contains http. This must be changed to https. <?php

                ?> </div> <?php
            } else {
                ?>
                <div class="notice notice-error">
                    <p>Your admin area is loading over insecure http, however your site is loading over https. This is known to cause errors in plugins that allow front end media uploads or API interactions. If you have a valid SSL certificate, we can safely fix this for you by forcing the admin to load over https. <a id="ocb-do-ssl-change-2" href="#">Change this for me.</a></p>
                </div>
                <?php
            }

        } elseif ( $different_ssl == 1 ) {

            ?>
            <div class="notice notice-error">
                <p>Your admin area is loading over https, however your site is loading over insecure http. This is known to cause errors in plugins that allow front end media uploads or API interactions. We can safely fix this for you by changing your WordPress Address URL and your Site Address URL to their https versions and additionally forcing the front end to load over https. <a id="ocb-do-ssl-change-1" href="#">Change this for me.</a></p>
            </div>
            <?php

        } elseif( $different_ssl == 2 ){

            ?>
            <div class="notice notice-error">
                <p>Your admin area is loading over insecure http, however your site is loading over https. This is known to cause errors in plugins that allow front end media uploads or API interactions. We can safely fix this for you by forcing the admin to load over https. <a id="ocb-do-ssl-change-2" href="#">Change this for me.</a></p>
            </div>
            <?php

        }
    }

    public function version_mismatch_notice(){

        global $current_screen;

        $update_link = '<a href="' . admin_url( 'update-core.php' ) . '">See Updates.</a>';

        if( $current_screen->base == 'update-core' )
            $update_link = '';

        if( Offsprout_Model::has_older_offsprout_pro() ){
            echo '<div class="notice notice-error">
                <p>You must update to the latest version of Offsprout Pro. ' . $update_link . '</p>
            </div>';
        }

        if( Offsprout_Model::has_older_offsprout_theme() ){
            echo '<div class="notice notice-error">
                <p>You must update to the latest version of the Offsprout Theme. ' . $update_link . '</p>
            </div>';
        }

        if( Offsprout_Model::has_older_offsprout_woocommerce() ){
            echo '<div class="notice notice-error">
                <p>You must update to the latest version of Offsprout WooCommerce. ' . $update_link . '</p>
            </div>';
        }

    }
}