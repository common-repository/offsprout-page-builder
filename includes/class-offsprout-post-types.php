<?php

/**
 * Class Offsprout_Post_Types
 *
 * Build post types for:
 * - testimonials - ocb_testimonial
 * - templates - ocb_template
 * - global - ocb_template
 * - page templates - ocb_tree_template
 *
 */
class Offsprout_Post_Types{
    function __construct() {
        add_action( 'init', array( $this, 'create_template_post_type') );
    }
    function create_template_post_type() {
        register_post_type( 'ocb_testimonial',
            array(
                'taxonomies' => array( 'tag' ),
                'delete_with_user' => false,
                'show_in_rest' => true,
                'public' => false, //needs to be public so it can be edited
                'show_ui' => false,
                'exclude_from_search' => true,
                'show_in_nav_menus' => false,
                'menu_position' => 21,
                'labels' => array(
                    'name' => __( 'Testimonials', 'offsprout' ),
                    'singular_name' => __( 'Testimonial', 'offsprout' ),
                    'not_found'	=> __( 'No testimonials found', 'offsprout' ),
                    'not_found_in_trash' => __( 'No testimonials found in trash', 'offsprout' ),
                ),
            )
        );

        register_post_type( 'ocb_template',
            array(
                'taxonomies' => array( 'ocb_template_type', 'ocb_template_module_type', 'ocb_template_global', 'ocb_template_folder', 'ocb_template_theme', 'ocb_template_industry' ),
                'delete_with_user' => false,
                'show_in_rest' => true,
                'public' => false, //needs to be public so it can be edited
                'show_ui' => false,
                'exclude_from_search' => true,
                'show_in_nav_menus' => false,
                'menu_position' => 21,
                'labels' => array(
                    'name' => __( 'Object Templates', 'offsprout' ),
                    'singular_name' => __( 'Object Template', 'offsprout' ),
                    'not_found'	=> __( 'No templates found', 'offsprout' ),
                    'not_found_in_trash' => __( 'No templates found in trash', 'offsprout' ),
                ),
            )
        );

        register_post_type( 'ocb_tree_template',
            array(
                'taxonomies' => array( 'ocb_template_folder' ),
                'public' => false, //needs to be public so it can be edited
                'show_ui' => false,
                'exclude_from_search' => true,
                'show_in_nav_menus' => false,
                'menu_position' => 21,
                'delete_with_user' => false,
                'show_in_rest' => true,
                'labels' => array(
                    'name' => __( 'Page Templates', 'offsprout' ),
                    'edit_item' => __( 'Edit Page Template', 'offsprout' ),
                    'new_item' => __( 'New Page Template', 'offsprout' ),
                    'view_item' => __( 'View Page Template', 'offsprout' ),
                    'items_archive' => __( 'Page Template Archive', 'offsprout' ),
                    'search_items' => __( 'Search Page Templates', 'offsprout' ),
                    'not_found'	=> __( 'No templates found', 'offsprout' ),
                    'not_found_in_trash' => __( 'No templates found in trash', 'offsprout' ),
                    'singular_name' => __( 'Page Template', 'offsprout' )
                ),
            )
        );

        $template_type_args = array( 'labels' => array(
            'name'              => _x( 'Template Types', 'taxonomy general name', 'offsprout' ),
            'singular_name'     => _x( 'Template Type', 'taxonomy singular name', 'offsprout' ),
        ) );

        $template_folder_args = array( 'labels' => array(
            'name'              => _x( 'Template Folders', 'taxonomy general name', 'offsprout' ),
            'singular_name'     => _x( 'Template Folder', 'taxonomy singular name', 'offsprout' ),
        ) );

        $template_theme_args = array( 'labels' => array(
            'name'              => _x( 'Template Themes', 'taxonomy general name', 'offsprout' ),
            'singular_name'     => _x( 'Template Theme', 'taxonomy singular name', 'offsprout' ),
        ) );

        $template_industry_args = array( 'labels' => array(
            'name'              => _x( 'Template Industries', 'taxonomy general name', 'offsprout' ),
            'singular_name'     => _x( 'Template Industry', 'taxonomy singular name', 'offsprout' ),
        ) );

        $module_type_args = array( 'labels' => array(
            'name'              => _x( 'Module Types', 'taxonomy general name', 'offsprout' ),
            'singular_name'     => _x( 'Module Type', 'taxonomy singular name', 'offsprout' ),
        ) );

        $template_global_args = array( 'labels' => array(
            'name'              => _x( 'Global Status', 'taxonomy general name', 'offsprout' ),
            'singular_name'     => _x( 'Global Status', 'taxonomy singular name', 'offsprout' ),
        ) );

        register_taxonomy( 'ocb_template_type', 'ocb_template', $template_type_args );
        register_taxonomy( 'ocb_template_module_type', 'ocb_template', $module_type_args);
        register_taxonomy( 'ocb_template_global', 'ocb_template', $template_global_args );
        register_taxonomy( 'ocb_template_folder', array( 'ocb_tree_template', 'ocb_template' ), $template_folder_args );
        register_taxonomy( 'ocb_template_theme', array( 'ocb_tree_template', 'ocb_template' ), $template_theme_args );
        register_taxonomy( 'ocb_template_industry', array( 'ocb_tree_template', 'ocb_template' ), $template_industry_args );
    }
}

new Offsprout_Post_Types();