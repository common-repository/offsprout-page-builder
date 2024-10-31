<?php

class Offsprout_Page_Templates{

    /**
     * A reference to an instance of this class.
     */
    private static $instance;

    /**
     * The array of templates that this plugin tracks.
     */
    protected $templates;

    /**
     * Returns an instance of this class.
     */
    public static function get_instance() {

        if ( null == self::$instance ) {
            self::$instance = new Offsprout_Page_Templates();
        }

        return self::$instance;

    }

    /**
     * Initializes the plugin by setting filters and administration functions.
     */
    private function __construct() {

        $this->templates = array();

        $this->includes();


        // Add a filter to the attributes metabox to inject template into the cache.
        if ( version_compare( floatval( get_bloginfo( 'version' ) ), '4.7', '<' ) ) {

            // 4.6 and older
            add_filter(
                'page_attributes_dropdown_pages_args',
                array( $this, 'register_project_templates' )
            );

        } else {

            // Add a filter to the wp 4.7 version attributes metabox
            add_filter(
                'theme_page_templates', array( $this, 'add_new_template' )
            );

        }

        // Add a filter to the save post to inject out template into the page cache
        add_filter(
            'wp_insert_post_data',
            array( $this, 'register_project_templates' )
        );


        // Add a filter to the template include to determine if the page has our
        // template assigned and return it's path
        add_filter(
            'template_include',
            array( $this, 'view_project_template')
        );

        // Add your templates to this array.
        $this->templates = array(
            'ocb-full-width.php' => __( 'OCB Full Width', 'offsprout' ),
            'ocb-landing-page.php' => __( 'OCB Landing Page', 'offsprout' ),
        );

        //Only add this template if we're a logged in user in the front end
        if( is_user_logged_in() && ! is_admin() ){
            $this->templates = array_merge( $this->templates, array(
                'offsprout-editor.php' => __( 'Offsprout Editor', 'offsprout' )
            ) );
        }

        $this->templates = apply_filters( 'ocb_wp_template_list', $this->templates );

        add_filter( 'body_class', array( $this, 'body_class' ) );

    }

    private function includes() {
        require_once OCB_DIR . 'templates/template-helpers.php';
    }

    public function body_class( $classes ) {

        $template = get_page_template_slug();

        if ( false !== $template && array_key_exists( $template, $this->templates ) ) {
            $classes[] = 'ocb-using-page-template';
            $classes[] 	= 'ocb-template-' . get_template();
        }

        return $classes;
    }

    /**
     * Adds our template to the page dropdown for v4.7+
     *
     */
    public function add_new_template( $posts_templates ) {
        $posts_templates = array_merge( $posts_templates, $this->templates );
        return $posts_templates;
    }

    /**
     * Adds our template to the pages cache in order to trick WordPress
     * into thinking the template file exists where it doens't really exist.
     */
    public function register_project_templates( $atts ) {

        // Create the key used for the themes cache
        $cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );

        // Retrieve the cache list.
        // If it doesn't exist, or it's empty prepare an array
        $templates = wp_get_theme()->get_page_templates();
        if ( empty( $templates ) ) {
            $templates = array();
        }

        // New cache, therefore remove the old one
        wp_cache_delete( $cache_key , 'themes');

        // Now add our template to the list of templates by merging our templates
        // with the existing templates array from the cache.
        $templates = array_merge( $templates, $this->templates );

        // Add the modified cache to allow WordPress to pick it up for listing
        // available templates
        wp_cache_add( $cache_key, $templates, 'themes', 1800 );

        return $atts;

    }

    /**
     * Checks if the template is assigned to the page
     */
    public function view_project_template( $template ) {

        // Get global post
        global $post;

        // Return template if post is empty
        if ( ! $post ) {
            return $template;
        }

        // Return default template if we don't have a custom one defined
        if ( ! isset( $this->templates[get_post_meta(
                $post->ID, '_wp_page_template', true
            )] ) ) {
            return $template;
        }

        $the_template = get_post_meta( $post->ID, '_wp_page_template', true );

        $file = OCB_DIR . 'templates/' . $the_template;

        $file = apply_filters( 'ocb_edd_add_page_template_file', $file, $the_template );

        // Just to be safe, we check if the file exist first
        if ( file_exists( $file ) ) {
            return $file;
        } else {
            echo $file;
        }

        // Return template
        return $template;

    }

}