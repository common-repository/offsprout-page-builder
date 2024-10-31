<?php

class Offsprout_Model{

    /**
     * Retrieve the site settings or a specific option in the site settings
     *
     * WARNING: it is possible that using this function will retrieve an outdated value if the value has changed during the course of a request
     * If you want to be sure to get the most recent value, pass TRUE for $force
     *
     * @since 2.2
     * @param bool $key a specific property in the site settings array to return
     * @param bool $force Force retrieval of site settings from the option again in cases where the global may be out of date
     * @return bool|mixed|void
     */
    static public function get_site_settings( $key = false, $force = false ){
        global $ocb_site_settings;

        if( $ocb_site_settings == false || $force )
            $ocb_site_settings = get_option( 'ocb_site_settings' );

        $return = $ocb_site_settings;

        if( $key )
            $return = isset( $ocb_site_settings[$key] ) ? $ocb_site_settings[$key] : false;

        return $return;
    }

    /**
     * Update a specific key in the site settings
     *
     * WARNING: it is possible that using this function will retrieve an outdated value if the value has changed during the course of a request
     * If you want to be sure to get the most recent value, pass TRUE for $force
     *
     * @since 2.2
     * @param $key
     * @param $value
     * @param bool $force Force retrieval of the site settings from the options again in cases where the global may be out of date
     */
    static public function update_site_setting( $key, $value, $force = false ){
        global $ocb_site_settings;

        if( $ocb_site_settings == false || $force )
            $ocb_site_settings = get_option( 'ocb_site_settings' );

        $ocb_site_settings[$key] = $value;

        update_option( 'ocb_site_settings', $ocb_site_settings );
    }

    /**
     * Update multiple keys in the site settings
     *
     * WARNING: it is possible that using this function will retrieve an outdated value if the value has changed during the course of a request
     * If you want to be sure to get the most recent value, pass TRUE for $force
     *
     * @since 2.2
     * @param $array
     * @param bool $force Force retrieval of the site settings from the options again in cases where the global may be out of date
     */
    static public function update_site_settings_array( $array, $force = false ){
        global $ocb_site_settings;

        if( $ocb_site_settings == false || $force )
            $ocb_site_settings = get_option( 'ocb_site_settings' );

        foreach( $array as $key => $value ){
            $ocb_site_settings[$key] = $value;
        }

        update_option( 'ocb_site_settings', $ocb_site_settings );
    }

    /**
     * Detects whether the site is Gutenberg-compatible
     */
    static public function has_gutenberg(){
        global $wp_version;

        return function_exists( 'the_gutenberg_project' ) || version_compare( $wp_version, '5.0', '>=' );
    }

    /**
     * Detects whether the builder is active for the post/page
     */
    static public function is_builder_active(){
        global $post;

        if( ! is_object( $post ) || ! isset( $post->ID ) )
            return 0;

        $builder = get_post_meta( $post->ID, 'ocb_tree_content', true );
        $is_builder_active = get_post_meta( $post->ID, 'ocb_active', true );

        $builder_active = false;

        //If ocb_active value is saved, use that, otherwise use whether or not page builder content has been saved
        if( $is_builder_active == 1 || $builder )
            $builder_active = true;

        return $builder_active;
    }

    /**
     * Returns an option from the database for
     * the admin settings page.
     *
     * @since 2.0
     * @param string $key The option key.
     * @param bool $network_override Whether to allow the network admin setting to be overridden on subsites.
     * @return mixed
     */
    static public function get_admin_settings_option( $key, $network_override = true ) {
        // Get the site-wide option if we're in the network admin.
        if ( is_network_admin() ) {
            $value = get_site_option( $key );
        }
        // Get the site-wide option if network overrides aren't allowed.
        else if ( ! $network_override && class_exists( 'Offsprout_Multisite_Settings' ) ) {
            $value = get_site_option( $key );
        }
        // Network overrides are allowed. Return the subsite option if it exists.
        else if ( class_exists( 'Offsprout_Multisite_Settings' ) ) {
            $value = get_option( $key );
            $value = false === $value ? get_site_option( $key ) : $value;
        }
        // This must be a single site install. Get the single site option.
        else {
            $value = get_option( $key );
        }

        return $value;
    }

    /**
     * Returns a builder edit URL for a post.
     *
     * @since 2.0
     * @param int $post_id The post id to get an edit url for.
     * @return string
     */
    static public function get_edit_url( $post_id = false ) {
        if ( false === $post_id ) {
            global $post;
        } else {
            $post = get_post( $post_id );
        }

        return set_url_scheme( add_query_arg( 'pageEdit', 1, get_permalink( $post->ID ) ) );
    }

    /**
     * Returns the custom branding string.
     *
     * @since 2.0
     * @return string
     */
    static public function get_branding() {
        if ( class_exists( 'Offsprout_White_Label' ) ) {
            return Offsprout_White_Label::get_branding();
        }

        return __( 'Builder', 'offsprout' );
    }

    /**
     * Returns the custom branding icon URL.
     *
     * @since 2.0
     * @return string
     */
    static public function get_branding_icon() {
        if ( class_exists( 'Offsprout_White_Label' ) ) {
            return Offsprout_White_Label::get_branding_icon();
        }

        return '';
    }

    /**
     * Return an array of post types that the builder
     * is enabled to work with.
     *
     * @since 2.0
     * @return array
     */
    static public function get_post_types() {
        $value = self::get_admin_settings_option( 'ocb_builder_post_types', true );

        if ( ! $value ) {
            $value = array( 'page', 'post', 'product', 'offsprout-template' );
        } else {
            $value[] = 'offsprout-template';
        }

        return apply_filters( 'offsprout_post_types', $value );
    }

    /**
     * Checks to see if the builder can be enabled for
     * the current post in the main query.
     *
     * @since 2.0
     * @return bool
     */
    static public function is_post_editable() {
        global $wp_the_query;

        if ( is_singular() && isset( $wp_the_query->post ) ) {

            $post		= $wp_the_query->post;
            $post_types = self::get_post_types();
            $user_can	= current_user_can( 'edit_post', $post->ID );

            if ( in_array( $post->post_type, $post_types ) && $user_can ) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get the post id of a ocb_structure or ocb_template post based on the custom $id
     *
     * @param $id
     * @param string $type
     * @return bool
     */
    static public function get_post_id_from_custom( $id, $type = 'ocb_template' ){
        $templates = get_posts( array(
            'post_type' => $type,
            'meta_key'   => 'ocb_id',
            'meta_value' => $id,
        ) );

        $template = isset( $templates[0] ) ? $templates[0] : false;

        return $template ? $template->ID : false;
    }

    /**
     * Updates an option from the admin settings page.
     *
     * @since 2.0
     * @param string $key The option key.
     * @param mixed $value The value to update.
     * @return mixed
     */
    static public function update_admin_settings_option( $key, $value ) {
        // Update the site-wide option since we're in the network admin.
        if ( is_network_admin() ) {
            update_site_option( $key, $value );
        } else {
            update_option( $key, $value );
        }
    }

    /**
     * Checks to see if multisite is supported.
     *
     * @since 2.0
     * @return bool
     */
    static public function multisite_support() {
        return is_multisite();
    }

    /**
     * Centralized option for checking permissions
     *
     * @param string $capability manage_options, manage_network
     * @param string $action like regular-nonce (backend), super-nonce (backend), sprout-logged (frontend)
     * @param string $method POST/GET
     * @return bool
     */
    static public function nonce_perm_check( $capability = 'manage_options', $action = 'offsprout-admin', $method = 'POST' ){

        $passed = false;

        if( $method == 'POST' ){
            $sent = $_POST;
        } else {
            $sent = $_GET;
        }

        if( current_user_can( $capability ) && isset( $sent['nonce'] ) && wp_verify_nonce( $sent['nonce'], $action ) )
            $passed = true;
        else
            $passed = false;

        if( $capability == 'manage_network' ){
            $passed = is_super_admin() ? true : false;
        }

        return $passed;

    }

    /**
     * Centralized option for checking permission according to the builder role of the user
     *
     * @param $capability
     * @return bool
     */
    static public function perm_check( $capability ){
        return true;
    }

    /**
     * Returns whether or not the premium theme is installed
     *
     * @return bool
     */
    static public function has_offsprout_theme(){
        $theme = wp_get_theme();
        $theme_name = $theme->get( 'Name' );
        $has_theme = $theme_name == 'Offsprout';
        return $has_theme;
    }

    /**
     * Returns whether or not the offsprout pro is installed
     *
     * @return bool
     */
    static public function has_offsprout_pro(){
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
        return is_plugin_active( 'offsprout-pro/offsprout-pro.php' );
    }

    /**
     * Returns whether or not the offsprout woocommerce is installed
     *
     * @return bool
     */
    static public function has_offsprout_woocommerce(){
        include_once ABSPATH . '/wp-admin/includes/plugin.php';
        return is_plugin_active( 'offsprout-woocommerce/offsprout-woocommerce.php' );
    }

    /**
     * Returns whether or not the latest version of the premium theme is installed
     *
     * @return bool
     */
    static public function has_older_offsprout_theme(){
        $has_theme = Offsprout_Model::has_offsprout_theme();

        return ( $has_theme && version_compare( OCBT_VERSION, '1.4.3', '<' ) );
    }

    /**
     * Returns whether or not the latest version of offsprout pro is installed
     *
     * @return bool
     */
    static public function has_older_offsprout_pro(){
        $has_pro = Offsprout_Model::has_offsprout_pro();

        return ( $has_pro && version_compare( OCBP_VERSION, '1.5', '<' ) );
    }

    /**
     * Returns whether or not the latest version of offsprout woocommerce is installed
     *
     * @return bool
     */
    static public function has_older_offsprout_woocommerce(){
        $has_woocommerce = Offsprout_Model::has_offsprout_woocommerce();

        return ( $has_woocommerce && version_compare( OCBW_VERSION, '1.0.19', '<' ) );
    }

    /**
     * Returns whether or not plus upgrade has been verified
     *
     * @return bool
     */
    static public function has_subscription(){
        $has_pro = Offsprout_Model::has_offsprout_pro();
        $has_theme = Offsprout_Model::has_offsprout_theme();
        $has_subscription = get_option('ocb_has_subscription');

        return $has_subscription || $has_pro || $has_theme;
    }

    /**
     * Returns whether or not plus upgrade has been verified
     *
     * @return bool
     */
    static public function has_subscription_option(){
        return get_option('ocb_has_subscription');
    }

    /**
     * Returns the permissions array
     *
     * @param bool $user
     * @return array
     */
    static public function get_permissions( $user = false ){
        $permissions = array();

        if( $user == false )
            $user = get_current_user_id();

        foreach( self::get_permissions_structure() as $permission => $array ){
            if( isset( $array['requirement'] ) && $array['requirement'] ){
                $permissions[$permission] = $array['requires'];
            } else {
                $permissions[$permission] = true;
            }
        }

        $preview_role = false;
        $preview_permissions = array();
        $user_permissions = array();

        if( isset( $_GET['builderPermission'] ) && $_GET['builderPermission'] ){
            $preview_role = $_GET['builderPermission'];
        }

        $user_role = get_user_meta( $user, 'ocb_builder_role', true );

        $roles = get_option( 'ocb_builder_roles' );

        if( is_array( $roles ) ) {

            foreach ( $roles as $index => $role ) {
                if ( $preview_role && $role['name'] == $preview_role ) {
                    $preview_permissions = $roles[$index]['permissions'];
                }
                if ( $user_role && $role['name'] == $user_role ) {
                    $user_permissions = $roles[$index]['permissions'];
                }
            }

        }

        $permissions = array_merge( $permissions, $user_permissions, $preview_permissions );

        return $permissions;
    }

    static public function get_permissions_structure(){
        $has_theme = Offsprout_Model::has_offsprout_theme();
        $has_pro = Offsprout_Model::has_offsprout_pro();
        $has_subscription = Offsprout_Model::has_subscription() || $has_pro;
        $has_maps_api = false;

        $site_settings = get_option( 'ocb_site_settings' );
        if( isset( $site_settings['google_maps'] ) && isset( $site_settings['google_maps']['text'] ) && $site_settings['google_maps']['text'] ){
            $has_maps_api = true;
        }

        $permissions = array(
            'manage_site_settings'      => array(
                'requirement' => false,
                'group' => 'Site Settings'
            ),
            'duplicate_modules'         => array(
                'requirement' => false,
                'group' => 'Modules'
            ),
            'duplicate_columns'         => array(
                'requirement' => false,
                'group' => 'Columns'
            ),
            'duplicate_rows'            => array(
                'requirement' => false,
                'group' => 'Rows'
            ),
            'duplicate_pages'           => array(
                'requirement' => false,
                'group' => 'New Content'
            ),
            'duplicate_posts'           => array(
                'requirement' => false,
                'group' => 'New Content'
            ),
            'duplicate_custom'          => array(
                'requirement' => false,
                'group' => 'New Content'
            ),
            'delete_modules'            => array(
                'requirement' => false,
                'group' => 'Modules'
            ),
            'delete_columns'            => array(
                'requirement' => false,
                'group' => 'Columns'
            ),
            'delete_rows'               => array(
                'requirement' => false,
                'group' => 'Rows'
            ),
            'edit_modules_standard'     => array(
                'requirement' => false,
                'group' => 'Modules'
            ),
            'edit_columns_standard'     => array(
                'requirement' => false,
                'group' => 'Columns'
            ),
            'edit_rows_standard'        => array(
                'requirement' => false,
                'group' => 'Rows'
            ),
            'edit_modules_design'     => array(
                'requirement' => false,
                'group' => 'Modules'
            ),
            'edit_columns_design'     => array(
                'requirement' => false,
                'group' => 'Columns'
            ),
            'edit_rows_design'        => array(
                'requirement' => false,
                'group' => 'Rows'
            ),
            'change_modules_skin'     => array(
                'requirement' => false,
                'group' => 'Modules'
            ),
            'change_columns_skin'     => array(
                'requirement' => false,
                'group' => 'Columns'
            ),
            'change_rows_skin'        => array(
                'requirement' => false,
                'group' => 'Rows'
            ),
            'create_skins'     => array(
                'requirement' => false,
                'group' => 'Skins'
            ),
            'edit_skins'     => array(
                'requirement' => false,
                'group' => 'Skins'
            ),
            'delete_skins'        => array(
                'requirement' => false,
                'group' => 'Skins'
            ),
            'edit_modules_settings'     => array(
                'requirement' => false,
                'group' => 'Modules'
            ),
            'edit_modules_inline'     => array(
                'requirement' => false,
                'group' => 'Modules'
            ),
            'edit_columns_width'         => array(
                'requirement' => false,
                'group' => 'Columns'
            ),
            'use_object_templates'      => array(
                'requirement' => false,
                'group' => 'Object Templates'
            ),
            'use_page_templates'        => array(
                'requirement' => false,
                'group' => 'New Content'
            ),
            'move_modules'              => array(
                'requirement' => false,
                'group' => 'Modules'
            ),
            'move_columns'              => array(
                'requirement' => false,
                'group' => 'Columns'
            ),
            'move_rows'                 => array(
                'requirement' => false,
                'group' => 'Rows'
            ),
            'add_objects'               => array(
                'requirement' => false,
                'group' => 'Objects'
            ),
            'add_pages'                 => array(
                'requirement' => false,
                'group' => 'New Content'
            ),
            'add_posts'                 => array(
                'requirement' => false,
                'group' => 'New Content'
            ),
            'access_wordpress_admin'    => array(
                'requirement' => false,
                'group' => 'Access'
            ),
            'access_page_builder'    => array(
                'requirement' => false,
                'group' => 'Access'
            ),
            'access_wordpress_post_admin' => array(
                'requirement' => false,
                'group' => 'Access'
            ),
            'access_frontend_menu'      => array(
                'requirement' => false,
                'group' => 'Access'
            ),
            'access_help_docs'          => array(
                'requirement' => false,
                'group' => 'Access'
            ),
            'save_as_homepage'          => array(
                'requirement' => false,
                'group' => 'Site Settings'
            )
        );

        $permissions = apply_filters( 'ocb_permissions', $permissions );

        $extra_permissions = array(
            'edit_structures'           => array(
                'requirement' => true,
                'requires' => $has_theme,
                'group' => 'Structures'
            ),
            'edit_globals'              => array(
                'requirement' => true,
                'requires' => $has_pro,
                'group' => 'Globals'
            ),
            'edit_tree_templates'       => array(
                'requirement' => true,
                'requires' => $has_pro,
                'group' => 'Page Templates'
            ),
            'create_structures'         => array(
                'requirement' => true,
                'requires' => $has_theme,
                'group' => 'Structures'
            ),
            'create_globals'            => array(
                'requirement' => true,
                'requires' => $has_pro,
                'group' => 'Globals'
            ),
            'create_templates'          => array(
                'requirement' => true,
                'requires' => $has_pro,
                'group' => 'Object Templates'
            ),
            'create_tree_templates'     => array(
                'requirement' => true,
                'requires' => $has_pro,
                'group' => 'Page Templates'
            ),
            'apply_new_structures'      => array(
                'requirement' => true,
                'requires' => $has_theme,
                'group' => 'Structures'
            ),
            'delete_templates'          => array(
                'requirement' => true,
                'requires' => $has_pro,
                'group' => 'Object Templates'
            ),
            'delete_tree_templates'     => array(
                'requirement' => true,
                'requires' => $has_pro,
                'group' => 'Page Templates'
            ),
            'use_globals'               => array(
                'requirement' => true,
                'requires' => $has_pro,
                'group' => 'Globals'
            ),
            'subscription_only_options' => array(
                'requirement' => true,
                'requires' => $has_subscription,
                'builder_role' => false,
                'group' => 'Other'
            ),
            'use_connector'             => array(
                'requirement' => true,
                'requires' => $has_theme,
                'group' => 'Modules'
            ),
            'has_theme'                 => array(
                'requirement' => true,
                'requires' => $has_theme,
                'builder_role' => false
            ),
            'has_pro'                   => array(
                'requirement' => true,
                'requires' => $has_pro,
                'builder_role' => false
            ),
            'has_subscription'          => array(
                'requirement' => true,
                'requires' => $has_subscription,
                'builder_role' => false
            ),
            'has_maps_api'              => array(
                'requirement' => true,
                'requires' => $has_maps_api,
                'builder_role' => false
            ),
        );

        $permissions = array_merge( $permissions, $extra_permissions );

        return $permissions;
    }

    /**
     * Creates a menu array out of the standard WP menus that is structured like the custom menus in navigation module
     *
     * @param array $options
     * @return array the menu
     */
    static public function get_wordpress_menu_array( $options = array() ){
        $id = isset( $options['menu_id'] ) ? (int) $options['menu_id'] : false;

        $items = wp_get_nav_menu_items( $id );

        $array = array();
        $top_level = array();
        $children = array();
        $grandchildren = array();
        $parents = array(); //parents of a given item

        foreach ( (array) $items as $item ) {

            if( ! is_object( $item ) )
                continue;

            if( $item->menu_item_parent ){

                $parents[$item->ID] = $item->menu_item_parent;

                if( in_array( $item->menu_item_parent, $top_level ) ){
                    $children[] = $item->ID;

                    if( ! isset( $array[$item->menu_item_parent]['children'] ) ){
                        $array[$item->menu_item_parent]['children'] = array();
                    }

                    $array[$item->menu_item_parent]['children'][$item->ID] = self::get_wordpress_menu_array_item( $item );

                } elseif( in_array( $item->menu_item_parent, $children ) ){
                    $grandchildren[] = $item->ID;

                    $the_top_level = $parents[$item->menu_item_parent];

                    if( ! isset( $array[$the_top_level]['children'] ) ){
                        $array[$the_top_level]['children'] = array();
                    }

                    if( ! isset( $array[$the_top_level]['children'][$item->menu_item_parent]['children'] ) ){
                        $array[$the_top_level]['children'][$item->menu_item_parent]['children'] = array();
                    }

                    $array[$the_top_level]['children'][$item->menu_item_parent]['children'][] = self::get_wordpress_menu_array_item( $item );


                }

            } else {

                $top_level[] = $item->ID;
                $array[$item->ID] = self::get_wordpress_menu_array_item( $item );

            }

        }

        //Remove the ID as array keys for top level and children
        $array = array_values( $array );
        foreach( $array as $key => $top ){
            if( isset( $top['children'] ) ){
                $array[$key]['children'] = array_values( $array[$key]['children'] );
            }
        }

        return $array;
    }

    static function get_wordpress_menu_array_item( $item ){
        $the_item = array(
            'name' => $item->title,
            'url' => $item->url,
            'id' => 'menu-' . $item->ID,
            'post_id' => $item->ID,
            'order' => isset( $item->menu_order ) ? $item->menu_order : '0',
            'classes' => is_array( $item->classes ) && ! empty( $item->classes ) ? implode( ' ', $item->classes ) : ''
        );

        if( isset( $item->children ) && is_array( $item->children ) && count( $item->children ) ){
            $the_item['children'] = $item->children;
        }

        return $the_item;
    }

    /**
     * Creates a menu array out of page hierarchy that is structured like the custom menus in navigation module
     * so that we can have an option where pages are auto-added
     *
     * Only contains top and children - no grandchildren
     *
     * @param $options
     * @return array
     */
    static public function get_page_hierarchy_menu_array( $options = array() ){
        $r = array(
            'depth' => 1,
            'number' => 100,
            'sort_column' => 'menu_order'
        );

        $pages = get_pages( $r );

        $exclude = isset( $options['exclude'] ) && is_array( $options['exclude'] ) && ! empty( $options['exclude'] ) ? $options['exclude'] : array();
        $start = isset( $options['start'] ) && is_array( $options['start'] ) && ! empty( $options['start'] ) ? $options['start'] : array();
        $end = isset( $options['end'] ) && is_array( $options['end'] ) && ! empty( $options['end'] ) ? $options['end'] : array();

        $array = array();
        $start_array = array();
        $end_array = array();

        if ( ! empty( $pages ) ) {

            foreach ( (array) $pages as $page ) {

                if(
                    ! empty( $exclude ) &&
                    ( in_array( $page->ID, $exclude ) || in_array( $page->post_parent, $exclude ) )
                )
                    continue;
                
                //Add the beginning array
                if( ! empty( $start ) && in_array( $page->ID, $start ) ) {
                    $start_array[$page->ID] = self::get_page_menu_array_item( $page );
                    continue;
                }

                //Add the beginning array
                if( ! empty( $end ) && in_array( $page->ID, $end ) ) {
                    $end_array[$page->ID] = self::get_page_menu_array_item( $page );
                    continue;
                }

                if( $page->post_parent ){

                    if( ! isset( $array[$page->post_parent]['children'] ) ){
                        $array[$page->post_parent]['children'] = array();
                    }

                    $array[$page->post_parent]['children'][] = self::get_page_menu_array_item( $page );

                } else {

                    $array[$page->ID] = self::get_page_menu_array_item( $page );

                }

            }


            //Start with these items
            if( ! empty( $start ) && ! empty( $start_array ) ){
                //ocb_var_dump( $start_array );
                $new_start_array = array();
                foreach( $start as $id ){
                    $new_start_array[$id] = $start_array[$id];
                }
                $start_array = $new_start_array;
            }

            //End with these items
            if( ! empty( $end ) && ! empty( $end_array ) ){
                //ocb_var_dump( $end_array );
                $new_end_array = array();
                foreach( $end as $id ){
                    $new_end_array[$id] = $end_array[$id];
                }
                $end_array = $new_end_array;
            }

        }

        $array = array_merge( $start_array, $array, $end_array );
        $array = array_values( $array );

        return $array;
    }
    
    static public function get_page_menu_array_item( $page ){
        return array(
            'name' => $page->post_title,
            'url' => get_permalink( $page->ID ),
            'id' => 'menu-' . $page->ID,
            'post_id' => $page->ID
        );
    }

    /**
     * Outputs HTML with the page hierarchy menu for the navigation module
     *
     * @param $options
     * @return string
     */
    static public function get_menu_html( $options = array() ){

        $type = isset( $options['type'] ) ? $options['type'] : 'page_hierarchy';

        if( $type == 'page_hierarchy' )
            $page_array = self::get_page_hierarchy_menu_array( $options );
        elseif( $type == 'wordpress' )
            $page_array = self::get_wordpress_menu_array( $options );

        $html = '';

        if( ! empty( $page_array ) ){

            foreach( $page_array as $page ){

                $html .= "<div class='ocb-menu-item-wrap'>";

                if( isset( $page['children'] ) && isset( $page['id'] ) ) {

                    $html .= "<a id='{$page['id']}' class='ocb-menu-item ocb-menu-item-has-children' href='{$page['url']}' data-ocb-remaining-class='ocb-menu-item ocb-menu-item-has-children'>{$page['name']}</a>";

                    $html .= "<div class='ocb-menu-item-children' data-ocb-remaining-class='ocb-menu-item-children'>";

                    foreach( $page['children'] as $child ) {
                        $html .= "<div class='ocb-menu-item-child-wrap text-left'>";

                        if( isset( $child['children'] ) && isset( $child['id'] ) ){

                            $html .= "<a id='{$child['id']}' class='ocb-menu-item-child ocb-specific-color ocb-menu-item-has-grandchildren' href='{$child['url']}' data-ocb-remaining-class='ocb-menu-item-child ocb-specific-color ocb-menu-item-has-grandchildren'>{$child['name']}</a>";

                            $html .= "<div class='ocb-menu-item-grandchildren'>";

                            foreach( $child['children'] as $grandchild ){

                                $html .= "<div class='ocb-menu-item-grandchild-wrap text-left'>";
                                $html .= "<a id='{$grandchild['id']}' class='ocb-menu-item-child ocb-specific-color' href='{$grandchild['url']}' data-ocb-remaining-class='ocb-menu-item-child ocb-specific-color'>{$grandchild['name']}</a>";
                                $html .= "</div>";

                            }

                            $html .= "</div>";
                        } else {
                            $html .= "<a id='{$child['id']}' class='ocb-menu-item-child ocb-specific-color' href='{$child['url']}' data-ocb-remaining-class='ocb-menu-item-child ocb-specific-color'>{$child['name']}</a>";
                        }

                        $html .= "</div>";

                    }

                    $html .= "</div>";

                } elseif( isset( $page['id'] ) ) {

                    $html .= "<a id='{$page['id']}' class='ocb-menu-item' href='{$page['url']}' data-ocb-remaining-class='ocb-menu-item'>{$page['name']}</a>";

                }

                $html .= "</div>";

            }

        }

        return $html;
    }

    /**
     * Wrapper for WP_query.
     *
     * Adds some performance enhancing defaults. Adapted from bulk delete plugin by sudar
     *
     * @param array $options List of options
     *
     * @return array Result array
     */
    static public function bulk_query( $options ){
        $defaults = array(
            'cache_results'          => false, // don't cache results
            'update_post_meta_cache' => false, // No need to fetch post meta fields
            'update_post_term_cache' => false, // No need to fetch taxonomy fields
            'no_found_rows'          => true,  // No need for pagination
            'fields'                 => 'ids', // retrieve only ids
            'posts_per_page'         => -1,    // retrieve all
            'post_status'            => 'any'
        );

        $options = wp_parse_args( $options, $defaults );

        $wp_query = new WP_Query();

        return $wp_query->query( $options );
    }

    /**
     * Easy way to delete a lot of content at once
     *
     * @param array $options query options
     * @return int
     */
    static public function bulk_delete( $options ){
        $post_ids = self::bulk_query( $options );

        $posts_deleted = 0;

        foreach ( $post_ids as $post_id ) {
            wp_delete_post( $post_id, true );
        }

        $posts_deleted += count( $post_ids );

        return $posts_deleted;
    }

    /**
     * Try to figure out whether there is an SSL difference between admin and the front end
     *
     * If not, it will return 0.
     * If so, it will return 1 if admin is SSL and front end is NOT, 2 if admin is NOT SSL and front end is
     *
     * @return int
     */
    static public function different_ssl(){
        $different = 0;
        $site_url = get_site_url();
        $home_url = get_home_url();

        if( is_admin() ){
            $is_ssl = is_ssl();

            if( $is_ssl ){
                if( strpos( $site_url, 'http:' ) !== false || strpos( $home_url, 'http:' ) !== false )
                    $different = 1;
            } else {
                if( strpos( $site_url, 'https:' ) !== false || strpos( $home_url, 'https:' ) !== false )
                    $different = 2;
            }
        } else {
            if( defined( 'FORCE_SSL_ADMIN' ) && FORCE_SSL_ADMIN ){
                if( strpos( $site_url, 'http:' ) !== false || strpos( $home_url, 'http:' ) !== false )
                    $different = 1;
            }
        }

        return $different;
    }

    /**
     * If we tried to rectify different_ssl case 1, this will revert back
     */
    static public function revert_ssl(){

        $ssl_option = get_option( 'ocb_force_ssl' );

        if( $ssl_option == 1 ) {

            //update the home_url and site_url options
            update_option( 'home', str_replace( 'https:', 'http:', get_home_url() ) );
            update_option( 'siteurl', str_replace( 'https:', 'http:', get_site_url() ) );

            //save an option to force the front end to load https
            update_option( 'ocb_force_ssl', false );

        } elseif( $ssl_option == 2 ){

            force_ssl_admin( false );

            //save an option to force the front end to load https
            update_option( 'ocb_force_ssl', false );

        }

    }

    /**
     * Change the domain of a site from a staging url to a live URL
     *
     * @param $domain
     * @param array $options
     * @return array
     */
    static function launch_domain( $domain, $options = array() ){

        if( ! self::perm_check( 'launch_domain' ) ){
            echo json_encode( array(
                'error' => true,
                'message' => __( 'You do not have permission to switch this domain.', 'offsprout' )
            ) );
            die();
        }

        $result = array(
            'error' => true,
            'message' => __( 'Something went wrong and your domain was not switched. Please contact support.', 'offsprout' )
        );

        $default_options = array(
            'is_multisite'          => is_multisite(),
            'domain_map'            => false,
            'blog_id'               => is_multisite() ? get_current_blog_id() : false,
            'delete_existing_map'   => false,
            'make_active_domain'    => true
        );

        $options = array_merge( $default_options, $options );

        extract( $options );

        $domain = str_replace( 'https://', '', $domain );
        $domain = str_replace( 'http://', '', $domain );

        require_once OCB_DIR . 'includes/class-offsprout-domain.php';

        if( $is_multisite ){

            if( $blog_id )
                switch_to_blog( $blog_id );

            Offsprout_Domain::new_domain_replace_links( $domain );
            Offsprout_Domain::map_domain( $domain, $blog_id, $make_active_domain );
            Offsprout_Domain::update_site_url_options( $domain );
            Offsprout_Domain::update_multisite_blog_details( $blog_id, $domain );

            if( $blog_id )
                restore_current_blog();

            $result['message'] = __( 'Your domain was successfully mapped. Please make sure your DNS records are set appropriately.', 'offsprout' );

        } else {

            Offsprout_Domain::new_domain_replace_links( $domain );
            Offsprout_Domain::update_site_url_options( $domain );

            $result['message'] = __( 'Your domain was successfully switched.', 'offsprout' );

        }

        $result['error'] = false;

        update_option( 'blog_public', 1 );

        return $result;

    }

    /**
     * Change the domain of a site from a staging url to a live URL
     *
     * @param $ssl
     * @return array
     */
    static function ssl_link_replace( $ssl ){

        if( ! self::perm_check( 'launch_domain' ) ){
            echo json_encode( array(
                'error' => true,
                'message' => __( 'You do not have permission to change ssl links.', 'offsprout' )
            ) );
            die();
        }

        $result = array(
            'error' => true,
            'message' => __( 'Something went wrong and your links were not updated. Please contact support.', 'offsprout' )
        );

        require_once OCB_DIR . 'includes/class-offsprout-domain.php';

        $domain = self::get_domain();
        $http = $ssl ? 'https://' : 'http://';
        $old_http = $ssl ? 'http://' : 'https://';

        Offsprout_Domain::find_and_replace( $http . $domain, $old_http . $domain, array( 'custom', 'content', 'options' ), 'replace', false );
        Offsprout_Domain::update_site_url_options( $domain, $http );

        $result['message'] = __( 'SSL was updated successfully.', 'offsprout' );
        $result['error'] = false;

        return $result;

    }

    static public function get_domain(){
        $site_url = get_site_url();
        return untrailingslashit( str_replace('http://', '', str_replace( 'https://', '', $site_url ) ) );
    }

    static public function get_ocb_upload_basedir(){
        $upload_dir = wp_upload_dir();
        $upload_dir = isset( $upload_dir['basedir'] ) ? $upload_dir['basedir'] . '/ocb/' : false;

        return $upload_dir;
    }

    static public function get_global_css_filename(){
        return 'ocb-global-' . time() . '.css';
    }

    static public function get_full_css_filepath( $name = false ){
        $upload_dir = wp_upload_dir();
        if( ! $name )
            $name = self::get_global_css_filename();

        return isset( $upload_dir['baseurl'] ) ? $upload_dir['baseurl'] . '/ocb/' . $name : false;
    }

    /**
     * When site settings are saved, this function is triggered using a filter in the options api to generate a CSS file with the site CSS
     *
     * @param $css new CSS to save
     */
    static function save_global_css_file( $css ){
        $css_filename = self::get_global_css_filename();

        if( ! $css_filename )
            return;

        $basedir = self::get_ocb_upload_basedir();

        // check if CSS has changed
        $current_file = get_option( 'ocb_global_css_filename' );
        $current_css = file_get_contents( $basedir . $current_file );

        if( $current_css == $css )
            return;

        // delete existing file
        if( $current_file )
            unlink( $basedir . $current_file );

        //Make directory if it doesn't exist yet
        if( ! is_dir( untrailingslashit( $basedir ) ) )
            mkdir( untrailingslashit( $basedir ), 0755, true );

        //Create new file
        $new_file = self::get_ocb_upload_basedir() . $css_filename;

        //Create the file
        $success = file_put_contents( $new_file, $css );

        //Save the name of the file so that we can enqueue it and delete it when a new version is created
        if( $success )
            update_option( 'ocb_global_css_filename', $css_filename );
    }

    /**
     * Clear object cache in hosts like WP Engine, especially after the Site Grower
     */
    static public function clear_cache(){
        wp_cache_flush();
    }

    /**
     * Clear all of the template cache for a domain
     * @param $location (int/string)
     */
    static public function clear_template_cache( $location = 1 ){
        global $wpdb;

        if( is_integer( $location ) ){
            $domain = Offsprout_Model::get_site_settings( 'template_remote_' . $location );

            if( isset( $domain['text'] ) )
                $domain = (string) $domain['text'];
            else
                $domain = (string) $domain;
        } else {
            $domain = (string) $location;
        }

        $wpdb->query(
            "DELETE FROM `" . $wpdb->options . "` WHERE `option_name` LIKE ('_transient_temp_" . $domain . "%')"
        );

        $wpdb->query(
            "DELETE FROM `" . $wpdb->options . "` WHERE `option_name` LIKE ('_site_transient_temp_" . $domain . "%')"
        );
    }

    /**
     * Returns the PHP memory in bytes
     *
     * @return int|string
     */
    static function get_memory_limit() {
        $val = ini_get('memory_limit');

        $val = trim($val);

        preg_match('/^(\d+)(.)$/', $val, $matches);
        $last = isset( $matches[2] ) ? strtolower( $matches[2] ) : strtolower( $val[ strlen($val)-1 ] );
        $val = isset( $matches[1] ) ? $matches[1] : $val;

        switch($last) {
            // The 'G' modifier is available since PHP 5.1.0
            case 'g':
                $val *= 1024;
            case 'm':
                $val *= 1024;
            case 'k':
                $val *= 1024;
        }

        return $val;
    }

    /**
     * Offsprout wrapper for wp_revisions_to_keep()
     *
     * @param bool|object $post
     * @return int
     */
    static function get_revision_limit( $post = false ){
        $num = 5;

        if( $post && is_object( $post ) ){
            $num = wp_revisions_to_keep( $post );
        } else {
            $defined_num = defined( 'WP_POST_REVISIONS' ) ? WP_POST_REVISIONS : false;

            if( $defined_num && is_int( $defined_num ) && $defined_num != -1 )
                $num = $defined_num;
        }

        return $num;
    }

    static function get_template_memory_auto(){
        $memory = self::get_memory_limit();
        $value = 25;
        $mb = ( 1024 * 1024 );

        if( $memory < ( 33 * $mb ) )
            $value = 10;
        elseif( $memory < ( 65 * $mb ) )
            $value = 25;
        elseif( $memory < ( 129 * $mb ) )
            $value = 50;
        elseif( $memory < ( 513 * $mb ) )
            $value = 100;
        elseif( $memory > 513 )
            $value = 'max';

        return $value;
    }

    static function replace_strings( $content, $values = false ){
        global $ocb_replace_functions;

        $default = array(
            'skin' => array( 'class' => 'Offsprout_Replace', 'function' => 'replace_skin_classes', 'values' => get_option( 'ocb_site_skins' ) ),
            'featuredImage' => array( 'class' => 'Offsprout_Replace', 'function' => 'replace_featured_image' ),
            'logoSrc' => array( 'class' => 'Offsprout_Replace', 'function' => 'replace_logo_image' ),
            'posts' => array( 'class' => 'Offsprout_Replace', 'function' => 'replace_posts' )
        );

        $ocb_replace_functions = apply_filters( 'ocb_replace_functions', $default );

        //Will match something like {{ocbr:skin:settings:skinjpn88qtnm9u03qfa4:buttonCombinedClasses}}{{ocbu:skinjpn88qtnm9u03qfa4|settings|linked|button_skin|choice}}
        $new_content = preg_replace_callback('/\{\{ocbr:([a-zA-Z0-9-_]+)[:]?([a-zA-Z0-9-_]*)[:]?([a-zA-Z0-9-_\s]*)[:]?([a-zA-Z0-9-_\s]*)}}(?:\{\{ocbu:)*([a-zA-Z0-9-_|\s]*)(?:}})?/', function($matches) use($values){

            global $ocb_replace_functions;

            $replace = '';

            $callable = isset( $ocb_replace_functions[$matches[1]] ) ? $ocb_replace_functions[$matches[1]] : false;

            if( ! $callable )
                return $replace;

            $the_function = isset( $callable['function'] ) ? $callable['function'] : false;
            $the_class = isset( $callable['class'] ) ? $callable['class'] : false;
            $values = $values != false ? $values : isset( $callable['values'] ) ? $callable['values'] : false;

            if( ! $the_function || ! $the_class )
                return $replace;

            return $the_class::$the_function( $matches, $values );

        }, $content);

        return $new_content;
    }

    /**
     * Safe way to return a structure post from any Offsprout product (checks to make sure function exists)
     *
     * Will return a structure post
     * First checks to see if the current post has a structure post set
     * Then checks to see if there is a default structure post for the current type
     *
     * Will return false if no structure post is found
     *
     * @param bool $id
     * @param string $type
     * @param string $return_type - post or id
     * @return bool
     */
    static function safe_get_structure( $id = false, $type = 'default', $return_type = 'post' ){
        if( function_exists( 'ocb_get_structure' ) )
            return ocb_get_structure( $id, $type, $return_type );

        return false;
    }
}