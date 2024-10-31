<?php

/**
 * Handles logic for the post edit screen.
 *
 * @since 2.0
 */
final class OffsproutAddEditorButton {

    /**
     * Initialize hooks.
     *
     * @since 1.8
     * @return void
     */
    static public function init()
    {
        $permissions = Offsprout_Model::get_permissions();

        if( isset( $permissions['access_page_builder'] ) && ! $permissions['access_page_builder'] )
            return;

        if( Offsprout_Model::has_gutenberg() ){
            /* Actions */
            add_action( 'admin_footer', __CLASS__ . '::print_gutenberg_button' );
        } else {
        }
        /* Actions */
        add_action('current_screen',                __CLASS__ . '::init_rendering');

        /* Filters */
        add_filter('redirect_post_location',        __CLASS__ . '::redirect_post_location');
        add_filter('page_row_actions',              __CLASS__ . '::render_row_actions_link');
        add_filter('post_row_actions',              __CLASS__ . '::render_row_actions_link');
    }

    /**
     * Sets the body class, loads assets and renders the UI
     * if we are on a post type that supports the builder.
     *
     * @since 2.0
     * @return void
     */
    static public function init_rendering()
    {
        global $pagenow;

        if ( in_array( $pagenow, array( 'post.php', 'post-new.php') ) ) {

            //$post_types = array( 'post', 'page' );
            //$screen		= get_current_screen();

            //if ( in_array( $screen->post_type, $post_types ) ) {
                //add_filter( 'admin_body_class',         __CLASS__ . '::body_class', 99 );
                add_action( 'admin_enqueue_scripts',    __CLASS__ . '::styles_scripts' );
                add_action( 'edit_form_after_title',    __CLASS__ . '::render' );
            //}
        }
    }

    /**
     * Enqueues the CSS/JS for the post edit screen.
     *
     * @since 2.0
     * @return void
     */
    static public function styles_scripts() {
        global $wp_version;

        // Styles
        wp_enqueue_style( 'offsprout-admin-posts', OCB_ADMIN_CSS_DIR . 'offsprout-admin-posts.css', array(), OCB_VERSION );

        // Scripts
        wp_enqueue_script( 'offsprout-admin-posts', OCB_ADMIN_JS_DIR . 'offsprout-admin-posts.js', array(), OCB_VERSION );
    }

    /**
     * Adds classes to the post edit screen body class.
     *
     * @since 2.0
     * @param string $classes The existing body classes.
     * @return string The body classes.
     */
    static public function body_class( $classes = '' ) {
        global $wp_version;

        // Builder body class
        if ( true ) {
            $classes .= ' offsprout-builder-enabled';
        }

        // Pre WP 3.8 body class
        if ( version_compare( $wp_version, '3.8', '<' ) ) {
            $classes .= ' offsprout-pre-wp-3-8';
        }

        return $classes;
    }

    /**
     * Renders the HTML for the post edit screen.
     *
     * @since 2.0
     * @return void
     */
    static public function render() {
        global $post;

        $post_type_obj 	= get_post_type_object ( $post->post_type );
        $post_type_name = strtolower( $post_type_obj->labels->singular_name );
        $enabled 		= true;

        $builder = get_post_meta( $post->ID, 'ocb_tree_content', true );
        $is_builder_active = get_post_meta( $post->ID, 'ocb_active', true );

        $editer_active = '';
        $builder_active = '';

        //if there has never been a builder, automatically redirect when the builder tab is clicked
        $has_had_builder = $builder ? '' : 'offsprout-redirect';

        //If ocb_active value is saved, use that, otherwise use whether or not page builder content has been saved
        if( $is_builder_active !== '' ){
            $builder_active = $is_builder_active == 1 ? 'offsprout-active' : '';
            $editer_active = $is_builder_active == 0 ? 'offsprout-active' : '';
        } else {
            if( $builder )
                $builder_active = 'offsprout-active';
        }

        if( ! $editer_active && ! $builder_active )
            $editer_active = 'offsprout-active';

        //Use this input to tell wp_insert_post_data filter not to save content
        $hidden_no_save = '';
        if( $builder_active )
            $hidden_no_save = '<input type="hidden" name="offsprout-builder-active" value="1" />';

        ?>
        <div class="offsprout-builder-admin">
            <div class="offsprout-builder-admin-tabs">
                <a href="javascript:void(0);" onclick="return false;" class="offsprout-enable-editor <?php echo $editer_active; ?>"><?php _e('Text Editor', 'offsprout-builder'); ?></a>
                <a href="javascript:void(0);" onclick="return false;" class="offsprout-enable-builder <?php echo $builder_active; ?> <?php echo $has_had_builder; ?>"><?php echo Offsprout_Model::get_branding(); ?></a>
            </div>
            <div class="offsprout-builder-admin-ui">
                <a href="<?php echo Offsprout_Model::get_edit_url(); ?>" class="offsprout-launch-builder button button-primary button-large"><?php printf( _x( 'Launch %s', '%s stands for custom branded "Page Builder" name.', 'offsprout-builder' ), Offsprout_Model::get_branding() ); ?></a>
                <?php echo $hidden_no_save; ?>
            </div>
        </div>
        <?php
    }

    /**
     * Renders the action link for post listing pages.
     *
     * @since 2.0
     * @param array $actions
     * @return array The array of action data.
     */
    static public function render_row_actions_link( $actions = array() ) {
        global $post;

        if ( current_user_can( 'edit_post', $post->ID ) && wp_check_post_lock( $post->ID ) === false ) {

            $post_types = array( 'post', 'page' );

            if ( in_array( $post->post_type, $post_types ) ) {
                $actions['offsprout-builder'] = '<a href="' . Offsprout_Model::get_edit_url( $post->ID ) . '">Page Builder</a>';
            }
        }

        return $actions;
    }

    /**
     * Where to redirect this post on save.
     *
     * @since 2.0
     * @param string $location
     * @return string The location to redirect this post on save.
     */
    static public function redirect_post_location( $location )
    {
        if ( isset( $_POST['offsprout-builder-redirect'] ) ) {
            $location = $_POST['offsprout-builder-redirect'];
        }

        return $location;
    }

    static public function print_gutenberg_button() {
        global $post;

        if( ! is_object( $post ) ) return;

        $post_type_obj 	= get_post_type_object ( $post->post_type );
        $post_type_name = strtolower( $post_type_obj->labels->singular_name );
        $enabled 		= true;

        $builder = get_post_meta( $post->ID, 'ocb_tree_content', true );
        $is_builder_active = get_post_meta( $post->ID, 'ocb_active', true );

        $editer_active = '';
        $builder_active = '';

        //if there has never been a builder, automatically redirect when the builder tab is clicked
        $has_had_builder = $builder ? '' : 'offsprout-redirect';

        //If ocb_active value is saved, use that, otherwise use whether or not page builder content has been saved
        if( $is_builder_active !== '' ){
            $builder_active = $is_builder_active == 1 ? 'offsprout-active' : '';
            $editer_active = $is_builder_active == 0 ? 'offsprout-active' : '';
        } else {
            if( $builder )
                $builder_active = 'offsprout-active';
        }

        if( ! $editer_active && ! $builder_active )
            $editer_active = 'offsprout-active';

        //Use this input to tell wp_insert_post_data filter not to save content
        //NOT CURRENTLY WORKING FOR GUTENBERG - USING TRANSIENT METHOD INSTEAD
        //ocb_gutenberg_saving
        $hidden_no_save = '';
        if( $builder_active )
            $hidden_no_save = '<input type="hidden" name="offsprout-builder-active" value="1" />';

        ?>
        <script id="offsprout-gutenberg-button-switch-mode" type="text/html">
            <div id="offsprout-switch-mode">
                <?php echo $hidden_no_save; ?>
                <button id="offsprout-switch-mode-button-editor" type="button" class="button button-primary button-large offsprout-enable-editor <?php echo $builder_active; ?>">
                    <span class="offsprout-switch-mode-on"><?php echo __( '&#8592; Back to WordPress Editor', 'offsprout' ); ?></span>
                </button>
                <button id="offsprout-switch-mode-button-builder" type="button" class="button button-primary button-large offsprout-enable-builder <?php echo $editer_active; ?> <?php echo $has_had_builder; ?>" data-redirect="<?php echo Offsprout_Model::get_edit_url(); ?>">
					<span class="offsprout-switch-mode-off">
                        <?php echo __( 'Edit with Builder', 'offsprout' ); ?>
					</span>
                </button>
            </div>
        </script>

        <script id="offsprout-gutenberg-panel" type="text/html">
            <div id="offsprout-editor">
                <a href="<?php echo Offsprout_Model::get_edit_url(); ?>" id="offsprout-go-to-edit-page-link" class="offsprout-launch-builder">
                    <div id="offsprout-editor-button" class="button button-primary button-hero">
                        <?php printf( _x( 'Launch %s', '%s stands for custom branded "Page Builder" name.', 'offsprout-builder' ), Offsprout_Model::get_branding() ); ?>
                    </div>
                </a>
            </div>
        </script>
        <?php
    }
}

OffsproutAddEditorButton::init();