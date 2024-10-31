<?php

/**
 * Class Offsprout_Builder
 *
 * Only should include admin functionality for the builder - should not include anything non-admin facing
 */
class Offsprout_Builder{

    function __construct() {
        $this->both();

        if( OCB_IFRAME )
            $this->only_iframe();
        else
            $this->only_full();
    }

    function both(){
        add_action( 'wp_enqueue_scripts', array( $this, 'offsprout_filters' ), 1 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js' ), 199 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_vendor_js' ), 195 );
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ), 199 );
        add_filter( 'show_admin_bar', '__return_false' );
        add_action( 'wp_footer', array( $this, 'load_editor' ) );
    }

    function only_full(){
        add_filter( 'template_include', array( $this, 'load_builder_template' ), 199 );
        add_action( 'wp_enqueue_scripts', array( $this, 'remove_all_scripts' ), 190 );
        add_action( 'wp_enqueue_scripts', array( $this, 'remove_all_styles' ), 190 );
        add_action( 'wp_head', array( $this, 'load_preloader' ) );
        add_action( 'wp_head', array( $this, 'load_preloader_version_check' ) );

        //For Widget Module Controls
        add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_widgets' ), 199 );
        add_action( 'wp_footer', array( $this,    'do_widget_render_control_template_scripts' ) );
        add_action( 'wp_footer', array( $this,    'do_widget_actions' ) );

        //Need to iterate through the widget global and do enqueue_admin_scripts() to make sure all of the scripts are loaded
        foreach( $GLOBALS['wp_widget_factory']->widgets as $class => $object ){
            if( method_exists( $object, 'enqueue_admin_scripts' ) )
                $object->enqueue_admin_scripts();
        }
    }

    function only_iframe(){
        //update_option( 'ocb_site_settings', array() );
        //var_dump( get_option( 'ocb_site_settings' ) );
        add_action( 'wp_footer', array( $this, 'backup_load_builder' ) );
        add_filter( 'the_content', array( $this, 'load_builder' ), 100 );
        add_action( 'wp_head', array( $this, 'workspace_html_padding' ) );
        add_filter( 'body_class', array( $this, 'add_body_classes' ) );
    }

    function add_body_classes( $classes ){
        $classes[] = 'ocb-workspace';
        return $classes;
    }

    function do_widget_render_control_template_scripts(){
        //For Widget Module Controls
        try{

            //Do a foreach instead and iterate through the widgets global
            foreach( $GLOBALS['wp_widget_factory']->widgets as $class => $object ){
                if( method_exists( $object, 'render_control_template_scripts' ) )
                    $object->render_control_template_scripts();
            }

        } catch( Exception $e ){
            //do something
        }
    }

    function do_widget_actions(){
        /** This action is documented in wp-admin/admin-footer.php */
        do_action( 'admin_footer-widgets.php' );
    }

    function enqueue_widgets(){
        $wp_scripts = new WP_Scripts();
        wp_default_scripts( $wp_scripts );
        //wp_enqueue_script( 'moxiejs' );
        //wp_enqueue_script( 'plupload' );
        //wp_enqueue_script( 'underscore' );
        //wp_enqueue_script( 'backbone' );
        //wp_enqueue_script( 'imgareaselect' );
        //wp_enqueue_script( 'json2' );
        //wp_enqueue_script( 'wp-api-request' );
        //wp_enqueue_script( 'media-models' );
        //wp_enqueue_script( 'media-views' );
        $suffix = ''; //'.min';
        wp_enqueue_script( 'media-widgets', "/wp-admin/js/widgets/media-widgets$suffix.js", array( 'jquery', 'media-models', 'media-views', 'wp-api-request' ) );
        wp_enqueue_script( 'media-audio-widget', "/wp-admin/js/widgets/media-audio-widget$suffix.js", array( 'media-widgets', 'media-audiovideo' ) );
        wp_enqueue_script( 'media-image-widget', "/wp-admin/js/widgets/media-image-widget$suffix.js", array( 'media-widgets' ) );
        wp_enqueue_script( 'media-video-widget', "/wp-admin/js/widgets/media-video-widget$suffix.js", array( 'media-widgets', 'media-audiovideo' ) );
        wp_enqueue_script( 'text-widgets', "/wp-admin/js/widgets/text-widgets$suffix.js", array( 'jquery', 'editor', 'wp-util' ) );

        wp_enqueue_style( 'widgets' );
        wp_enqueue_style( 'media-views' );

        /** This action is documented in wp-admin/admin-header.php */
        do_action( 'admin_print_scripts-widgets.php' );
    }

    function enqueue_js(){
        global $wp_version;

        //4.9.6 updated the tinymce to a new version that is incompatible with the old version so this is needed to ensure tinyMCE compatibility with older WP versions
        if ( version_compare( $wp_version, '4.9.6', '<' ) )
            wp_enqueue_script( 'tinymce-js', OCB_MAIN_DIR . 'library/tinymce_old/tinymce.min.js', array('jquery'), OCB_REQS_VERSION, true );
        else
            wp_enqueue_script( 'tinymce-js', OCB_MAIN_DIR . 'library/tinymce/tinymce.min.js', array('jquery'), OCB_REQS_VERSION, true );

        wp_enqueue_script( 'offsprout-builder', OCB_MAIN_DIR . 'builder/app/build/bundle.js', array('offsprout-reqs'), OCB_VERSION, true );
        wp_enqueue_media();

        $site_settings = get_option( 'ocb_site_settings' );
        $featured_image_fallback = '';
        if( isset( $site_settings['featured_image_fallback'] ) && isset( $site_settings['featured_image_fallback']['url'] ) && $site_settings['featured_image_fallback']['url'] ){
            $featured_image_fallback = $site_settings['featured_image_fallback']['url'];
        }

        $comment_image_size = '';
        if( isset( $site_settings['comment_image_size'] ) && isset( $site_settings['comment_image_size']['useDefault'] ) && $site_settings['comment_image_size']['useDefault'] == 'custom' ){
            $comment_image_size = (int) $site_settings['comment_image_size']['value'];
        }

        $use_template_remotes = 1;
        if( isset( $site_settings['use_template_remotes'] ) && isset( $site_settings['use_template_remotes']['yes'] ) )
            $use_template_remotes = (int) $site_settings['use_template_remotes']['yes'];

        $template_remote_1 = 'https://basic.t.offsprout.com';
        if( defined( 'OCB_TEMPLATE_SITE_1' ) && OCB_TEMPLATE_SITE_1 )
            $template_remote_1 = OCB_TEMPLATE_SITE_1;
        elseif( isset( $site_settings['template_remote_1'] ) && isset( $site_settings['template_remote_1']['text'] ) && $site_settings['template_remote_1']['text'] )
            $template_remote_1 = trailingslashit( esc_url( $site_settings['template_remote_1']['text'] ) );

        $template_remote_2 = '';
        if( defined( 'OCB_TEMPLATE_SITE_2' ) && OCB_TEMPLATE_SITE_2 )
            $template_remote_1 = OCB_TEMPLATE_SITE_2;
        elseif( isset( $site_settings['template_remote_2'] ) && isset( $site_settings['template_remote_2']['text'] ) && $site_settings['template_remote_2']['text'] )
            $template_remote_2 = trailingslashit( esc_url( $site_settings['template_remote_2']['text'] ) );
        
        $template_industry = 'Generic';
        if( isset( $site_settings['template_industry'] ) && isset( $site_settings['template_industry']['choice'] ) && $site_settings['template_industry']['choice'] )
            $template_industry = $site_settings['template_industry']['choice'];

        $template_theme = 'Basic';
        if( isset( $site_settings['template_theme'] ) && isset( $site_settings['template_theme']['choice'] ) && $site_settings['template_theme']['choice'] )
            $template_theme = $site_settings['template_theme']['choice'];

        $post_type = get_post_type();

        $has_subscription = Offsprout_Model::has_subscription();

        $site_url = get_site_url();
        $domain = untrailingslashit( str_replace('http://', '', str_replace( 'https://', '', $site_url ) ) );
        //update_option('ocb_tutorials', false);
        $tutorials = get_option('ocb_tutorials');
        $category_base = get_option( 'category_base' );

        if( ! is_array( $tutorials ) )
            $tutorials = array();

        $variables = array(
            'root'                  => esc_url_raw( rest_url() ),
            'ajaxURL'               => admin_url('admin-ajax.php', ( isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' ) ),
            'adminURL'              => admin_url('', ( isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' ) ),
            'siteUrl'               => $site_url,
            'domain'                => $domain,
            'stockUrl'              => OCB_MAIN_DIR . 'library/stock/',
            'mainDir'               => OCB_MAIN_DIR,
            'nonce'                 => wp_create_nonce( 'wp_rest' ),
            'postId'                => get_the_ID(),
            'userId'                => get_current_user_id(),
            'postType'              => $post_type,
            'currentURL'            => strtok($_SERVER["REQUEST_URI"],'?'),
            'currentTitle'          => get_the_title(),
            'currentCategory'       => get_the_category(),
            'categoryBase'          => $category_base ? $category_base : 'category',
            'currentTag'            => get_the_tags(),
            'postStatus'            => get_post_status(),
            'isSSL'                 => is_ssl(),
            '_wp_page_template'     => get_post_meta( get_the_ID(), '_wp_page_template', true ),
            'editUrl'               => get_edit_post_link( get_the_ID() ),
            'featuredImage'         => array(
                'full' => wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' )[0],
                'large' => wp_get_attachment_image_src( get_post_thumbnail_id(), 'large' )[0],
                'medium' => wp_get_attachment_image_src( get_post_thumbnail_id(), 'medium' )[0],
                'thumbnail' => wp_get_attachment_image_src( get_post_thumbnail_id(), 'thumbnail' )[0]
            ),
            'featuredImageFallback' => $featured_image_fallback,
            'commentImageSize'      => $comment_image_size,
            'tutorials'             => array(
                'add_modules_drag'          => isset( $tutorials['add_modules_drag'] ) ? $tutorials['add_modules_drag'] : true,
                'add_modules_column'        => isset( $tutorials['add_modules_column'] ) ? $tutorials['add_modules_column'] : true,
                'right_click_modules'       => isset( $tutorials['right_click_modules'] ) ? $tutorials['right_click_modules'] : true
            ),
            'useRemoteTemplates'    => $use_template_remotes ? true : false,
            'remoteTemplates'       => array(
                $template_remote_1,
                $template_remote_2
            ),
            'templates'             => array(
                'industry'              => $template_industry,
                'theme'                 => $template_theme,
                'subRemoteOne'          => '',
            ),
            'activePlugins'         => get_option('active_plugins'),
            'icons'                 => array(
                'linea'                 => $has_subscription,
            ),
            'returnOnSave'          => isset( $_GET['returnOnSave'] ) ? get_permalink( $_GET['returnOnSave'] ) : false,
            'forceSiteGrower'       => isset( $_GET['forceSiteGrower'] ) ? (int) $_GET['forceSiteGrower'] : 0,
            'permissions'           => Offsprout_Model::get_permissions(),
            'builderRole'           => isset( $_GET['builderPermission'] ) ? $_GET['builderPermission'] : get_user_meta( get_current_user_id(), 'ocb_builder_role', true ),
            'templateMemoryAuto'    => Offsprout_Model::get_template_memory_auto()
        );

        $variables = apply_filters( 'ocb_global_settings', $variables );

        wp_localize_script( 'offsprout-builder', 'OCBGlobalSettings', $variables );
    }

    function enqueue_vendor_js(){
        wp_enqueue_script( 'offsprout-reqs', OCB_MAIN_DIR . 'builder/app/build/vendor.js', array('jquery', 'tinymce-js'), OCB_VERSION, true );
    }

    function enqueue_css(){
        wp_enqueue_style( 'offsprout-admin-css', OCB_MAIN_DIR . 'builder/app/build/bundle.css', array('offsprout-css'), OCB_VERSION );
    }

    function toolbar(){

    }

    function load_preloader(){
        echo self::get_preloader( 5000 );
    }

    function load_preloader_version_check(){
        $older_pro = Offsprout_Model::has_older_offsprout_pro();
        $older_theme = Offsprout_Model::has_older_offsprout_theme();
        $older_woo = Offsprout_Model::has_older_offsprout_woocommerce();
        $needs_update = ( $older_pro || $older_theme || $older_woo );

        if( $needs_update ){
            ?>
            <div id="offsprout-preloader-message"><div id="offsprout-preloader-message-errors" class="offsprout-font">
            <h1 class="offsprout-font">You Must Update Offsprout</h1>
            <?php
        }

        if( $older_pro ){
            ?>
            <div class="offsprout-preloader-message-error">
                <p>You must update to the latest version of Offsprout Pro</p>
            </div>
            <?php
        }

        if( $older_theme ){
            ?>
            <div class="offsprout-preloader-message-error">
                <p>You must update to the latest version of the Offsprout Theme</p>
            </div>
            <?php
        }

        if( $older_woo ){
            ?>
            <div class="offsprout-preloader-message-error">
                <p>You must update to the latest version of Offsprout WooCommerce</p>
            </div>
            <?php
        }

        if( $needs_update ){
            ?>
            <a href="<?php echo admin_url( 'update-core.php' ) ?>" target="_blank">See Updates</a>
            </div></div>
            <?php
        }
    }

    /**
     * Load the editor so that the link builder is available in the TinyMCE
     */
    function load_editor(){
        wp_editor('', 'ocb-tinymce');
    }

    function offsprout_filters(){
        echo '
        <script type="text/javascript">
            // Array.findIndex() polyfill
            // https://tc39.github.io/ecma262/#sec-array.prototype.findIndex
            if (!Array.prototype.findIndex) {
              Object.defineProperty(Array.prototype, \'findIndex\', {
                value: function(predicate) {
                 // 1. Let O be ? ToObject(this value).
                  if (this == null) {
                    throw new TypeError(\'"this" is null or not defined\');
                  }

                  var o = Object(this);

                  // 2. Let len be ? ToLength(? Get(O, "length")).
                  var len = o.length >>> 0;

                  // 3. If IsCallable(predicate) is false, throw a TypeError exception.
                  if (typeof predicate !== \'function\') {
                    throw new TypeError(\'predicate must be a function\');
                  }

                  // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
                  var thisArg = arguments[1];

                  // 5. Let k be 0.
                  var k = 0;

                  // 6. Repeat, while k < len
                  while (k < len) {
                    // a. Let Pk be ! ToString(k).
                    // b. Let kValue be ? Get(O, Pk).
                    // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
                    // d. If testResult is true, return k.
                    var kValue = o[k];
                    if (predicate.call(thisArg, kValue, k, o)) {
                      return k;
                    }
                    // e. Increase k by 1.
                    k++;
                  }

                  // 7. Return -1.
                  return -1;
                },
                configurable: true,
                writable: true
              });
            }

            OffsproutFilter = {
                filters: {},
                apply: function( filter, item, args ){
                    if( OffsproutFilter.filters[filter] == undefined ) return item;
                    if( args == undefined ) args = {};

                    OffsproutFilter.filters[filter] = OffsproutFilter.filters[filter].sort(function (a, b) {
                        return a.priority - b.priority;
                    });

                    for( var i = 0; i < OffsproutFilter.filters[filter].length; i++ ){
                        item = OffsproutFilter.filters[filter][i].func( item, args );
                    }

                    return item;
                },
                add: function( filter, func, priority = 10 ){
                    if( OffsproutFilter.filters[filter] == undefined ) OffsproutFilter.filters[filter] = [];
                    if( priority == undefined ) priority = 10;

                    OffsproutFilter.filters[filter] = OffsproutFilter.filters[filter].concat({func: func, priority: priority});
                },
                /**
                * Easy way to add a field to a module or in the Site Settings
                *
                * ex: return OffsproutFilter.applyNewSettings( "siteSettings", siteSettings );
                *
                * @param filter  the filter to use, ex: siteSettings
                * @param setting  the field object
                * @param defaultSettings  the default settings for this new field
                * @param addAfter the ID of the field to add the new field after
                */
                addToSettings: function( filter, setting, defaultSettings, addAfter = false ){
                    if( OffsproutFilter.settings[filter] == undefined ) OffsproutFilter.settings[filter] = [];

                    OffsproutFilter.settings[filter] = OffsproutFilter.settings[filter].concat({setting, defaultSettings, addAfter})
                },
                /**
                * Easy way to add a field to a setting group in a module or in the Site Settings
                *
                * ex: return OffsproutFilter.applyNewSettings( \'siteSettings\', siteSettings );
                *
                * @param filter  the filter to use, ex: siteSettings
                * @param groupId  the group to add the field to
                * @param setting  the field object
                * @param defaultSettings  the default settings for this new field
                * @param addAfter the ID of the field to add the new field after
                */
                addToSettingsGroup: function( filter, groupId, setting, defaultSettings, addAfter = false){
                    if( OffsproutFilter.settings[filter] == undefined ) OffsproutFilter.settings[filter] = [];

                    OffsproutFilter.settings[filter] = OffsproutFilter.settings[filter].concat({groupId, setting, defaultSettings, addAfter})
                },
                applyNewSettings: function( filter, fields ){
                    if( OffsproutFilter.settings[filter] == undefined ) return fields;

                    var newFields = JSON.parse(JSON.stringify( fields ));

                    for( var i = 0; i < OffsproutFilter.settings[filter].length; i++ ){
                        for( var j = 0; j < newFields.fields.length; j++ ){
                            if( newFields.fields[j].id == OffsproutFilter.settings[filter][i].groupId ){
                                newFields.fields[j].items = newFields.fields[j].items.concat(OffsproutFilter.settings[filter][i].setting.id);
                            }
                        }
                        if( OffsproutFilter.settings[filter][i].addAfter != undefined && OffsproutFilter.settings[filter][i].addAfter != false ){
                            var insertIndex = newFields.fields.findIndex( function( value, index ) {
                                return value.id == OffsproutFilter.settings[filter][i].addAfter;
                            });
                            newFields.fields.splice(insertIndex + 1, 0, OffsproutFilter.settings[filter][i].setting);
                        } else {
                            newFields.fields = newFields.fields.concat(OffsproutFilter.settings[filter][i].setting);
                        }
                        newFields.default[OffsproutFilter.settings[filter][i].setting.id] = OffsproutFilter.settings[filter][i].defaultSettings;
                    }

                    return newFields;
                },
                settings: {}
            };

            OffsproutAction = {
                actions: {},
                do: function( action, args = {} ){
                    if( OffsproutAction.actions[action] == undefined ) return;
                    if( args == undefined ) args = {};

                    OffsproutAction.actions[action] = OffsproutAction.actions[action].sort(function (a, b) {
                        return a.priority - b.priority;
                    });

                    for( var i = 0; i < OffsproutAction.actions[action].length; i++ ){
                        OffsproutAction.actions[action][i].func( args );
                    }
                },
                add: function( action, func, priority = 10 ){
                    if( OffsproutAction.actions[action] == undefined ) OffsproutAction.actions[action] = [];
                    if( priority == undefined ) priority = 10;

                    OffsproutAction.actions[action] = OffsproutAction.actions[action].concat({func: func, priority: priority});
                }
            };

            OffsproutSiteGrower = {};
        </script>
        ';
    }

    static function get_preloader( $how_long = 500 ){
        $double_long = $how_long * 2;

        //Just in case the builder doesn't load, we want to remove the preloader
        $stop_preloader = '<script type="text/javascript">
            jQuery(document).ready(function() {
                if(jQuery("#offsprout-preloader-background").length > 0) {
                    setTimeout(function(){
                        jQuery("#offsprout-preloader-text").html("Still Loading");
                    }, ' . $how_long . ');
                    setTimeout(function(){
                        jQuery("#offsprout-preloader-background").fadeOut(300);
                    }, ' . $double_long . ');
                }
            });
        </script>';

        $branding = Offsprout_Model::get_branding();

        if( isset( $_GET['forceSiteGrower'] ) ){
            if( isset( $_GET['siteGrowerStepName'] ) ){
                $branding = esc_html( $_GET['siteGrowerStepName'] );
                $branding = str_replace( '-', ' ', $branding );
            } else {
                $branding = __( 'Site Grower', 'offsprout' );
            }
        }

        return $stop_preloader .
        '<div id="offsprout-preloader-background">
            <div id="offsprout-spinners">
                <div id="offsprout-preloader">
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                    <span></span>
                </div>
            </div>
            <div id="offsprout-preloader-text" class="offsprout-font">
                Loading ' . $branding . '
            </div>
        </div>';
    }

    function load_builder( $content ){
        return '<div id="offsprout-builder-content"></div>';
    }

    function load_builder_template( $template ){
        $template = OCB_DIR . 'templates/offsprout-editor.php';
        return $template;
    }

    /**
     * This outputs a div to render styles (SiteOptionsRender) into in the case that there's no content module
     */
    function backup_load_builder(){
        echo '<div id="offsprout-backup-builder-content"></div>';
    }

    /**
     * Remove all non-Offsprout javascript from the parent window - we only need it in the child window
     */
    function remove_all_scripts() {
        global $wp_scripts;
        $wp_scripts->queue = array();
    }

    /**
     * Remove all non-Offsprout css sheets from the parent window - we only need it in the child window
     */
    function remove_all_styles() {
        global $wp_styles;
        $wp_styles->queue = array();
    }

    /**
     * Make sure that the controls have enough room to breath
     */
    function workspace_html_padding(){
        echo '<style type="text/css">html{padding: 70px 30px 240px 70px; background: #aaaaaa;}</style>';
    }

}