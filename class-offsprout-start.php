<?php

class Offsprout_Start{

    /**
     * Defines constants used throughout the plugin
     */
    static function define_constants(){
        define( 'OCB_VERSION', '2.15.2' );
        define( 'OCB_REQS_VERSION', '1.1' );
        define( 'OCB_SPROUT_VERSION', 4 );
        define( 'OCB_DIR', trailingslashit( dirname(__FILE__) ) );
        define( 'OCB_BASENAME', basename( dirname(__FILE__) ) );
        define( 'OCB_ADMIN_CSS_DIR', trailingslashit( plugins_url( '/admin/css', __FILE__ ) ) );
        define( 'OCB_ADMIN_JS_DIR', trailingslashit( plugins_url( '/admin/js', __FILE__ ) ) );
        define( 'OCB_MAIN_DIR', trailingslashit( plugins_url( '', __FILE__ ) ) );

        //Needed for user checking
        include_once(ABSPATH . 'wp-includes/pluggable.php');

        //Needed for user checking and early debug
        require_once OCB_DIR . 'admin/debug/class-offsprout-debug.php';
        require_once OCB_DIR . 'includes/class-offsprout-model.php';

        if( isset( $_GET['offsproutIframe'] ) && $_GET['offsproutIframe'] == '1')
            define( 'OCB_IFRAME', true );
        else
            define( 'OCB_IFRAME', false );

        if( isset( $_GET['pageEdit'] ) && current_user_can( 'edit_posts' ) ) {
            $permissions = Offsprout_Model::get_permissions();

            if( isset( $permissions['access_page_builder'] ) && ! $permissions['access_page_builder'] ){
                define( 'OCB_EDIT', false );
            } else {
                define( 'OCB_EDIT', true );
            }
        } else {
            define( 'OCB_EDIT', false );
        }
    }

    /**
     * Requires all of the necessary Offsprout files
     */
    static function bootstrap(){
        do_action( 'before_ocb_bootstrap' );

        require_once OCB_DIR . 'includes/class-offsprout-meta-database.php';
        require_once OCB_DIR . 'includes/class-offsprout-include.php';
        //require_once OCB_DIR . 'admin/debug/class-offsprout-debug.php';
        require_once OCB_DIR . 'includes/class-offsprout-site-data.php';
        require_once OCB_DIR . 'includes/class-offsprout-post-data.php';
        require_once OCB_DIR . 'includes/class-offsprout-company-data.php';
        require_once OCB_DIR . 'includes/class-offsprout-utility-data.php';
        require_once OCB_DIR . 'includes/class-offsprout-woocommerce-data.php';
        require_once OCB_DIR . 'includes/class-offsprout-connector-shortcode.php';
        require_once OCB_DIR . 'includes/class-offsprout-replace.php';
        //require_once OCB_DIR . 'includes/class-offsprout-model.php';
        require_once OCB_DIR . 'includes/class-offsprout-wp-toolbar.php';
        require_once OCB_DIR . 'includes/class-offsprout-post-types.php';
        require_once OCB_DIR . 'includes/class-offsprout-page-templates.php';
        require_once OCB_DIR . 'admin/class-offsprout-database.php';

        if( get_option( 'ocb_force_ssl' ) == 1 ){
            require_once OCB_DIR . 'admin/class-offsprout-force-frontend-ssl.php';
        }

        add_action( 'plugins_loaded', array( 'Offsprout_Page_Templates', 'get_instance' ) );

        if( is_admin() ){
            require_once OCB_DIR . 'admin/class-offsprout-admin-dependencies.php';
            new Offsprout_Admin_Dependencies();
        } else {
            require_once OCB_DIR . 'public/class-offsprout-public-dependencies.php';
            new Offsprout_Public_Dependencies();
        }

        add_action( 'init', __CLASS__ . '::load_builder_or_toolbar', 99 );

        require_once OCB_DIR . 'api/class-offsprout-api-extensions.php';

        if( isset( $_GET['ocb_revert_ssl_setting'] ) && $_GET['ocb_revert_ssl_setting'] == 'yes' ){
            Offsprout_Model::revert_ssl();
        }

        do_action( 'after_ocb_bootstrap' );
    }

    /**
     * Load on init because it uses OCB_EDIT which is defined on init
     */
    static function load_builder_or_toolbar(){
        if( OCB_EDIT ) {

            do_action( 'before_ocb_load_builder' );

            require_once OCB_DIR . 'builder/class-offsprout-builder.php';
            new Offsprout_Builder();

        } else {

            do_action( 'before_ocb_load_toolbar' );

            require_once OCB_DIR . 'includes/class-offsprout-wp-toolbar.php';

        }
    }

    /**
     * Registers the activation hook and installs templates
     */
    static function installation(){
        include_once OCB_DIR . 'admin/class-offsprout-installation.php';
        register_activation_hook( OCB_PLUGIN_FILE, array( 'Offsprout_Installation', 'install' ) );
        Offsprout_Installation::maybe_redirect();
    }

}

if( ! function_exists( 'get_domain' ) ){
    function get_domain(){
        $url = get_option( 'siteurl' );
        $domain = str_replace('http://', '', $url);
        $domain = str_replace('https://', '', $domain);
        $domain = str_replace('www.', '', $domain);
        return $domain;
    }
}