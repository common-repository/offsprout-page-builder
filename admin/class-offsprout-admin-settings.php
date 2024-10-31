<?php

/**
 * Handles logic for the admin settings page.
 *
 * @since 2.0
 */
class Offsprout_Admin_Settings {

    /**
     * Holds any errors that may arise from
     * saving admin settings.
     *
     * @since 1.0
     * @var array $errors
     */
    static public $errors = array();

    /**
     * Initializes the admin settings.
     *
     * @since 1.0
     * @return void
     */
    static public function init() {
        add_action( 'after_setup_theme', __CLASS__ . '::init_hooks', 11 );
    }

    /**
     * Adds the admin menu and enqueues CSS/JS if we are on
     * the builder admin settings page.
     *
     * @since 1.0
     * @return void
     */
    static public function init_hooks() {
        if ( ! is_admin() ) {
            return;
        }

        add_action( 'admin_menu', __CLASS__ . '::menu' );

        if ( isset( $_REQUEST['page'] ) && 'offsprout-settings' == $_REQUEST['page'] ) {
            add_action( 'admin_enqueue_scripts', __CLASS__ . '::styles_scripts' );
            add_filter( 'admin_footer_text', array( __CLASS__, '_filter_admin_footer_text' ) );
            self::save();
        }

        add_action( 'admin_enqueue_scripts', __CLASS__ . '::all_admin_scripts' );
    }

    /**
     * Enqueues the needed CSS/JS for the builder's admin settings page.
     *
     * @since 1.0
     * @return void
     */
    static public function styles_scripts() {
        // Styles
        wp_enqueue_style( 'offsprout-builder-admin-settings', OCB_MAIN_DIR . 'admin/css/offsprout-builder-admin-settings.css', array(), OCB_VERSION );
        wp_enqueue_style( 'jquery-multiselect', OCB_MAIN_DIR . 'admin/css/jquery.multiselect.css', array(), OCB_VERSION );
        wp_enqueue_style( 'jquery-tiptip', OCB_MAIN_DIR . 'admin/css/jquery.tiptip.css', array(), OCB_VERSION );

        // Scripts
        wp_enqueue_script( 'offsprout-builder-admin-settings', OCB_MAIN_DIR . 'admin/js/offsprout-admin-settings.js', array(), OCB_VERSION );
        wp_enqueue_script( 'jquery-multiselect', OCB_MAIN_DIR . 'admin/js/jquery.multiselect.js', array( 'jquery' ), OCB_VERSION );
        wp_enqueue_script( 'jquery-tiptip', OCB_MAIN_DIR . 'admin/js/jquery.tiptip.min.js', array( 'jquery' ), OCB_VERSION, true );

        // Media Uploader
        wp_enqueue_media();
    }

    static function all_admin_scripts(){
        wp_enqueue_script( 'offsprout-builder-all-admin-settings', OCB_MAIN_DIR . 'admin/js/offsprout-all-admin-settings.js', array(), OCB_VERSION );
    }

    /**
     * Renders the admin settings menu.
     *
     * @since 1.0
     * @return void
     */
    static public function menu() {
        $permissions = Offsprout_Model::get_permissions();

        //If the user can't access this menu, don't add it - permission is in Pro
        if( isset( $permissions['access_page_builder_admin'] ) && ! $permissions['access_page_builder_admin'] )
            return;

        if ( current_user_can( 'delete_users' ) ) {

            $title = Offsprout_Model::get_branding();
            $cap   = 'delete_users';
            $slug  = 'offsprout-settings';
            $func  = __CLASS__ . '::render';

            add_submenu_page( 'options-general.php', $title, $title, $cap, $slug, $func );
        }
    }

    /**
     * Renders the admin settings.
     *
     * @since 1.0
     * @return void
     */
    static public function render() {
        include OCB_DIR . 'admin/screens/admin-settings.php';
    }

    /**
     * Renders the page class for network installs and single site installs.
     *
     * @since 1.0
     * @return void
     */
    static public function render_page_class() {
        if ( Offsprout_Model::multisite_support() ) {
            echo 'ocb-settings-network-admin';
        } else {
            echo 'ocb-settings-single-install';
        }
    }

    /**
     * Renders the admin settings page heading.
     *
     * @since 1.0
     * @return void
     */
    static public function render_page_heading() {
        $icon = Offsprout_Model::get_branding_icon();
        $name = Offsprout_Model::get_branding();

        if ( ! empty( $icon ) ) {
            echo '<img src="' . $icon . '" />';
        }

        echo '<span>' . sprintf( _x( '%s Settings', '%s stands for custom branded "Page Builder" name.', 'offsprout' ), Offsprout_Model::get_branding() ) . '</span>';
    }

    /**
     * Renders the update message.
     *
     * @since 1.0
     * @return void
     */
    static public function render_update_message() {
        if ( ! empty( self::$errors ) ) {
            foreach ( self::$errors as $message ) {
                echo '<div class="error"><p>' . $message . '</p></div>';
            }
        } elseif ( ! empty( $_POST ) && ! isset( $_POST['email'] ) ) {
            echo '<div class="updated"><p>' . __( 'Settings updated!', 'offsprout' ) . '</p></div>';
        }
    }

    /**
     * Renders the nav items for the admin settings menu.
     *
     * @since 1.0
     * @return void
     */
    static public function render_nav_items() {
        $item_data = apply_filters( 'ocb_builder_admin_settings_nav_items', array(
            'installation' => array(
                'title' => __( 'Installation', 'offsprout' ),
                'show' => is_multisite() ? false : ! Offsprout_Model::get_admin_settings_option( Offsprout_Installation::$install_ran_option ),
                'priority' => 10
            ),
            'welcome' => array(
                'title' 	=> __( 'Welcome', 'offsprout' ),
                'show'		=> true,
                'priority'	=> 50,
            ),
            'templates' => array(
                'title' => __( 'Templates', 'offsprout' ),
                'show' => false,
                'priority' => 60,
            ),
            'branding' => array(
                'title' 	=> __( 'Branding', 'offsprout' ),
                'show'		=> true,
                'priority'	=> 100,
            ),
            'tools' => array(
                'title' 	=> __( 'Tools', 'offsprout' ),
                'show'		=> true,
                'priority'	=> 700,
            ),
            /*'modules' => array(
                'title' 	=> __( 'Modules', 'offsprout' ),
                'show'		=> true,
                'priority'	=> 300,
            ),
            'post-types' => array(
                'title' 	=> __( 'Post Types', 'offsprout' ),
                'show'		=> true,
                'priority'	=> 400,
            ),
            'user-access' => array(
                'title' 	=> __( 'User Access', 'offsprout' ),
                'show'		=> true,
                'priority'	=> 500,
            ),

            'support' => array(
                'title' 	=> __( 'Support', 'offsprout' ),
                'show'		=> true,
                'priority'	=> 800,
            ),*/
        ) );

        $sorted_data = array();

        foreach ( $item_data as $key => $data ) {
            $data['key'] = $key;
            $sorted_data[ $data['priority'] ] = $data;
        }

        ksort( $sorted_data );

        foreach ( $sorted_data as $data ) {
            if ( $data['show'] ) {
                echo '<li><a href="#' . $data['key'] . '">' . $data['title'] . '</a></li>';
            }
        }
    }

    /**
     * Renders the admin settings forms.
     *
     * @since 1.0
     * @return void
     */
    static public function render_forms() {
        // Installation
        if ( ! Offsprout_Model::get_admin_settings_option( Offsprout_Installation::$install_ran_option ) ){
            self::render_form( 'installation' );
        }

        // Installation
        if ( ! Offsprout_Model::get_admin_settings_option( Offsprout_Installation::$install_ran_option ) ){
            self::render_form( 'installation' );
        }

        // Templates
        self::render_form( 'templates' );

        // Welcome
        self::render_form( 'welcome' );

        // Branding
        self::render_form( 'branding' );

        // Tools
        self::render_form( 'tools' );

        /*// Modules
        self::render_form( 'modules' );

        // Post Types
        self::render_form( 'post-types' );

        // User Access
        self::render_form( 'user-access' );

        // Support
        self::render_form( 'support' );*/

        // Let extensions hook into form rendering.
        do_action( 'ocb_admin_settings_render_forms' );
    }

    /**
     * Renders an admin settings form based on the type specified.
     *
     * @since 1.0
     * @param string $type The type of form to render.
     * @return void
     */
    static public function render_form( $type ) {
        if ( self::has_support( $type ) ) {
            include OCB_DIR . 'admin/screens/admin-settings-' . $type . '.php';
        }
    }

    /**
     * Renders the action for a form.
     *
     * @since 1.0
     * @param string $type The type of form being rendered.
     * @return void
     */
    static public function render_form_action( $type = '' ) {
        if ( is_network_admin() ) {
            echo network_admin_url( '/settings.php?page=offsprout-multisite-settings#' . $type );
        } else {
            echo admin_url( '/options-general.php?page=offsprout-settings#' . $type );
        }
    }

    /**
     * Returns the action for a form.
     *
     * @since 1.0
     * @param string $type The type of form being rendered.
     * @return string The URL for the form action.
     */
    static public function get_form_action( $type = '' ) {
        if ( is_network_admin() ) {
            return network_admin_url( '/settings.php?page=offsprout-multisite-settings#' . $type );
        } else {
            return admin_url( '/options-general.php?page=offsprout-settings#' . $type );
        }
    }

    /**
     * Checks to see if a settings form is supported.
     *
     * @since 1.0
     * @param string $type The type of form to check.
     * @return bool
     */
    static public function has_support( $type ) {
        return file_exists( OCB_DIR . 'admin/screens/admin-settings-' . $type . '.php' );
    }

    /**
     * Adds an error message to be rendered.
     *
     * @since 1.0
     * @param string $message The error message to add.
     * @return void
     */
    static public function add_error( $message ) {
        self::$errors[] = $message;
    }

    /**
     * Saves the admin settings.
     *
     * @since 1.0
     * @return void
     */
    static public function save() {
        // Only admins can save settings.
        if ( ! current_user_can( 'delete_users' ) ) {
            return;
        }

        self::save_enabled_modules();
        self::save_enabled_post_types();
        self::uninstall();

        // Let extensions hook into saving.
        do_action( 'offsprout_admin_settings_save' );
    }

    /**
     * Saves the enabled modules.
     *
     * @since 1.0
     * @access private
     * @return void
     */
    static private function save_enabled_modules() {
        if ( isset( $_POST['offsprout-modules-nonce'] ) && wp_verify_nonce( $_POST['offsprout-modules-nonce'], 'modules' ) ) {

            $modules = array();

            if ( is_array( $_POST['offsprout-modules'] ) ) {
                $modules = array_map( 'sanitize_text_field', $_POST['offsprout-modules'] );
            }

            Offsprout_Model::update_admin_settings_option( '_ocb_builder_enabled_modules', $modules );
        }
    }

    /**
     * Saves the enabled post types.
     *
     * @since 1.0
     * @access private
     * @return void
     */
    static private function save_enabled_post_types() {
        if ( isset( $_POST['offsprout-post-types-nonce'] ) && wp_verify_nonce( $_POST['offsprout-post-types-nonce'], 'post-types' ) ) {

            if ( is_network_admin() ) {
                $post_types = sanitize_text_field( $_POST['offsprout-post-types'] );
                $post_types = str_replace( ' ', '', $post_types );
                $post_types = explode( ',', $post_types );
            } else {

                $post_types = array();

                if ( isset( $_POST['offsprout-post-types'] ) && is_array( $_POST['offsprout-post-types'] ) ) {
                    $post_types = array_map( 'sanitize_text_field', $_POST['offsprout-post-types'] );
                }
            }

            Offsprout_Model::update_admin_settings_option( '_ocb_builder_post_types', $post_types );
        }
    }

    /**
     * Uninstalls the builder and all of its data.
     *
     * @since 2.0
     * @access private
     * @return void
     */
    static private function uninstall() {
        if ( ! current_user_can( 'delete_plugins' ) ) {
            return;
        } elseif ( isset( $_POST['offsprout-uninstall'] ) && wp_verify_nonce( $_POST['offsprout-uninstall'], 'uninstall' ) ) {

            $uninstall = apply_filters( 'ocb_builder_uninstall', true );

            if ( $uninstall ) {
                //do uninstall
            }
        }
    }

    /**
     * Adds a link to leave a review
     *
     * @since 2.0
     */
    static function _filter_admin_footer_text( $text ) {

        $stars = '<a target="_blank" href="https://wordpress.org/support/plugin/offsprout-page-builder-old/reviews/#new-post" >adding your &#9733;&#9733;&#9733;&#9733;&#9733; review</a>';

        return sprintf( __( 'Is Offsprout helping your business? Help Offsprout grow by %1$s!', 'offsprout' ), $stars );
    }
}

Offsprout_Admin_Settings::init();