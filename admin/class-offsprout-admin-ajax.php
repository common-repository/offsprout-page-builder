<?php

class Offsprout_Admin_Ajax{

    function __construct(){
        add_action( 'wp_ajax_ocb_install_templates', array( $this, 'install_templates' ) );
        add_action( 'wp_ajax_ocb_install_plus', array( $this, 'install_plus' ) );
        add_action( 'wp_ajax_ocb_finish_installation', array( $this, 'finish_installation' ) );
        add_action( 'wp_ajax_ocb_fix_ssl', array( $this, 'fix_ssl' ) );
        add_action( 'wp_ajax_ocb_revert_ssl', array( $this, 'revert_ssl' ) );
        add_action( 'wp_ajax_ocb_do_cache_purge', array( $this, 'do_cache_purge' ) );
        add_action( 'wp_ajax_ocb_toggle_builder_active', array( $this, 'toggle_builder_active' ) );
        add_action( 'wp_ajax_ocb_publish_gutenberg_post', array( $this, 'publish_gutenberg_post' ) );
        add_action( 'wp_ajax_ocb_set_gutenberg_transient', array( $this, 'set_gutenberg_transient' ) );
    }

    function install_templates(){
        if( ! Offsprout_Model::nonce_perm_check() )
            die( 'You do not have permission to import templates' );

        $update_array = array(
            'use_template_remotes' => array(
                'yes' => 1
            ),
            'template_remote_1' => array(
                'text' => defined( 'OCB_TEMPLATE_SITE_DEFAULT_1' ) && OCB_TEMPLATE_SITE_DEFAULT_1 ? OCB_TEMPLATE_SITE_DEFAULT_1 : ''
            )
        );

        Offsprout_Model::update_site_settings_array( $update_array, true );

        //Need pretty permalinks for templates to work
        if( ! get_option( 'permalink_structure' ) ){
            update_option( 'permalink_structure', '/%postname%/' );
            flush_rewrite_rules();
        }

        echo json_encode( array( 'message' => '', 'errors' => array(), 'finished' => 1 ) );

        die();
    }

    function install_templates_from_file(){
        if( ! Offsprout_Model::nonce_perm_check() )
            die( 'You do not have permission to import templates' );

        $template_files = array(
            'offsprout-page-templates.xml',
            'offsprout-object-templates.xml',
        );

        $templates_installed_options = array( 'Offsprout_Installation::$templates_installed_option' );

        if( Offsprout_Model::has_offsprout_pro() || Offsprout_Model::has_offsprout_theme() ){
            $template_files[] = 'offsprout-page-templates-2.xml';
            $template_files[] = 'offsprout-object-templates-2.xml';
            $templates_installed_options[] = Offsprout_Installation::$plus_templates_installed_option;
        }

        self::do_template_install( $template_files, $templates_installed_options );
    }

    function install_plus(){
        if( ! Offsprout_Model::nonce_perm_check( 'manage_options', 'wp_rest' ) )
            die( 'You do not have permission to import templates' );

        $template_files = array(
            'offsprout-page-templates-2.xml',
            'offsprout-object-templates-2.xml',
        );

        self::do_template_install( $template_files, Offsprout_Installation::$plus_templates_installed_option );
    }

    /**
     * Install templates from XML files
     *
     * @param array $template_files
     * @param array/string $complete_option - the name of the option to save if the templates are installed successfully
     */
    static function do_template_install( $template_files, $complete_option = array() ){
        $step = isset( $_POST['step'] ) ? (int) $_POST['step'] : false;

        $file = isset( $template_files[ $step - 1 ] ) ? $template_files[ $step - 1 ] : false;

        if ( $file ) {

            Offsprout_Installation::replace_external_urls( $file );
            echo json_encode( Offsprout_Installation::import_template_file( $file ) );

        } else {

            echo json_encode( array( 'message' => '', 'errors' => array(), 'finished' => 1 ) );

            if( $complete_option && ! is_array( $complete_option ) ){
                Offsprout_Model::update_admin_settings_option( $complete_option, 'yes' );
            } elseif( is_array( $complete_option ) && ! empty( $complete_option ) ){
                foreach( $complete_option as $option ){
                    Offsprout_Model::update_admin_settings_option( $option, 'yes' );
                }
            }

        }

        die();
    }

    function finish_installation(){
        if( ! Offsprout_Model::nonce_perm_check() )
            die( 'You do not have permission to finish installation' );

        Offsprout_Installation::did_install();
    }

    /**
     * If part of the site is loading over https and part is regular (like admin https and site http, this should fix)
     */
    function fix_ssl(){
        if( ! Offsprout_Model::nonce_perm_check() )
            die( 'You do not have permission to change SSL settings' );

        $which = isset( $_POST['which'] ) ? (int) $_POST['which'] : false;

        //This means the admin is https and the front end is http
        if( $which == 1 ){

            //update the home_url and site_url options
            update_option( 'home', str_replace( 'http:', 'https:', get_home_url() ) );
            update_option( 'siteurl', str_replace( 'http:', 'https:', get_site_url() ) );

            //save an option to force the front end to load https
            update_option( 'ocb_force_ssl', 1 );

        } elseif( $which == 2 ){

            force_ssl_admin( true );

        }

        echo json_encode( array( 'message' => $which, 'errors' => array(), 'finished' => 1 ) );

        die();
    }

    /**
     * If part of the site is loading over https and part is regular (like admin https and site http, this should fix)
     */
    function revert_ssl(){
        if( ! Offsprout_Model::nonce_perm_check() )
            die( 'You do not have permission to change SSL settings' );

        Offsprout_Model::revert_ssl();

        echo json_encode( array( 'message' => 'Success!', 'errors' => array(), 'finished' => 1 ) );

        die();
    }

    /**
     * Try to purge the cache through ajax - helpful in the initial build when show_on_front isn't saving as "page"
     */
    function do_cache_purge(){
        if( ! Offsprout_Model::nonce_perm_check() )
            die( 'You do not have permission to purge cache' );

        Offsprout_Model::clear_cache();

        echo json_encode( array( 'message' => 'Success!', 'errors' => array(), 'finished' => 1 ) );

        die();
    }

    function toggle_builder_active(){
        if( ! Offsprout_Model::nonce_perm_check( 'edit_posts' ) )
            die( 'You do not have permission to toggle page builder' );

        if( isset( $_POST['postId'] ) ){
            $value = isset( $_POST['active'] ) ? $_POST['active'] : ! get_post_meta( $_POST['postId'], 'ocb_active', true );
            update_post_meta( $_POST['postId'], 'ocb_active', $value );
        }

        die();
    }

    function publish_gutenberg_post(){
        if( ! Offsprout_Model::nonce_perm_check( 'edit_posts' ) )
            die( 'You do not have permission to toggle page builder' );

        $post_id = isset( $_POST['postId'] ) ? (int) $_POST['postId'] : 0;
        $post_title = isset( $_POST['title'] ) ? (string) $_POST['title'] : 0;

        if( $post_id ){
            wp_insert_post(
                array(
                    'ID' => $post_id,
                    'post_title' => $post_title
                )
            );
        }

        die();
    }

    /**
     * Sets a transient that is checked in a filter on wp_insert_post_data
     * that indicates that the post is being saved by gutenberg so that post content doesn't get saved
     */
    function set_gutenberg_transient(){
        if( ! Offsprout_Model::nonce_perm_check( 'edit_posts' ) )
            die( 0 );

        $post_id = isset( $_POST['postId'] ) ? (int) $_POST['postId'] : 0;
        $has_tree = get_post_meta( $post_id, 'ocb_tree_content', true );

        if( $has_tree ){
            set_transient( 'ocb_gutenberg_saving', $post_id, 10 );
        }

        die( 1 );
    }

}

new Offsprout_Admin_Ajax();