<?php

class Offsprout_Installation {

    static $install_ran_option = 'ocb_install_ran';
    static $templates_installed_option = 'ocb_templates_installed';
    static $plus_templates_installed_option = 'ocb_plus_templates_installed';
    static $install_redirect_transient = '_offsprout_activation_admin_notice';

    /**
     * If the installation hasn't run yet, set a transient that is used to run a redirect
     */
    static function install(){
        //See if we've run the install before
        if ( Offsprout_Model::get_admin_settings_option( self::$install_ran_option ) == true )
            return;

        set_transient( self::$install_redirect_transient, true, 30 );
    }

    /**
     * Add an action to do a redirect
     */
    static function maybe_redirect(){
        add_action( 'admin_init', __CLASS__ . '::redirect_on_activation', 1 );
    }

    /**
     * Redirect to the template install screen
     */
    static function redirect_on_activation(){
        // Bail if no activation transient is set.
        if ( ! get_transient( self::$install_redirect_transient ) ) {
            return;
        }

        self::do_install();

        // Delete the activation transient.
        delete_transient( self::$install_redirect_transient );

        // Redirect to the install page.
        if( defined('OCBP_VERSION') && defined('OCBT_VERSION') ){
            $should_create_site = false;
            if( function_exists( 'ocb_get_structure' ) ) {

                //Returns the full post
                $structure_post = ocb_get_structure( false, 'default' );

                if ( ! is_object( $structure_post ) ) {

                    //If $structure_post is not an object, that means no structure post exists
                    //In that case we need to prompt the user to create a site, or at least create a structure
                    $should_create_site = true;

                }

            }

            if ( $should_create_site ) {
                wp_safe_redirect( add_query_arg( array(
                    'pageEdit' => 1,
                    'forceSiteGrower' => 1
                ), home_url( '/' ) ) );
            } else {
                wp_safe_redirect( add_query_arg( array(
                    'page' => 'offsprout-settings#installation',
                ), admin_url( 'options-general.php' ) ) );
            }
        } elseif( ! is_multisite() ){
            wp_safe_redirect( add_query_arg( array(
                'page' => 'offsprout-settings#installation',
            ), admin_url( 'options-general.php' ) ) );
        }
    }

    /**
     * NOT CURRENTLY USED
     *
     * Instead of this function, we redirect to the settings page so that we can activate the plugin separately from
     * installing the templates. This also gives us the opportunity to show the progress.
     */
    static function do_install(){
        //Import the templates
        //self::import_all_templates();

        //Set permalinks
        //Need pretty permalinks for templates to work
        if( ! get_option( 'permalink_structure' ) ){
            update_option( 'permalink_structure', '/%postname%/' );
            flush_rewrite_rules();
        }


        //Save an option that says that we've imported
        self::did_install();
    }

    /**
     * NOT CURRENTLY USED
     *
     * Imports all templates
     *
     * Instead, we are importing templates one at a time
     */
    static function import_all_templates(){
        $template_files = array(
            'offsprout-object-templates.xml',
            'offsprout-page-templates.xml'
        );

        foreach( $template_files as $file ){
            self::import_template_file( $file );
        }
    }

    /**
     * Helper to get the full file location
     *
     * @param $file
     * @return string
     */
    static function get_full_template_file_path( $file ){
        return OCB_DIR . 'admin/templates/' . $file;
    }

    /**
     * Imports a template file given the file name
     *
     * @param $file
     * @return array
     */
    static function import_template_file( $file ){
        include_once OCB_DIR . 'admin/importer/class-offsprout-importer.php';

        $importer = new Offsprout_Importer();

        return $importer->import( self::get_full_template_file_path( $file ) );
    }

    /**
     * Takes external image URLs from the template site and converts them to internal URLs in post content of templates
     *
     * @param $file
     */
    static function replace_external_urls( $file ){
        $old_url = 'http://template.offsproutdemo.com/wp-content/plugins/offsprout/library/stock/';
        $old_url2 = 'http://template.offsproutdemo.com/wp-content/plugins/offsprout-page-builder-old/library/stock/';
        $new_url = OCB_MAIN_DIR . 'library/stock/';
        $file = self::get_full_template_file_path( $file );

        $str = file_get_contents( $file );
        $str = str_replace( $old_url, $new_url, $str );
        $str = str_replace( $old_url2, $new_url, $str );
        file_put_contents( $file, $str );
    }

    /**
     * Saves an option that tells this class that the installation has run so that it doesn't run again
     */
    static function did_install(){
        Offsprout_Model::update_admin_settings_option( self::$install_ran_option, 'yes' );
    }

    /**
     * Deletes the option that tells this class that the installation has run so that it can be run again
     */
    static function reset_installation(){
        delete_option( self::$install_ran_option );
    }

    /**
     * Performs the installation process again
     */
    static function reinstall(){
        self::reset_installation();
        self::install();
    }
}
