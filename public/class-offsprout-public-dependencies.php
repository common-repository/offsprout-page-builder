<?php
class Offsprout_Public_Dependencies{
    function __construct() {
        require_once OCB_DIR . 'public/class-offsprout-shortcodes.php';

        if( OCB_IFRAME ) {
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js' ) );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ), 22 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_bootstrap' ), 20 );
        } else{
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_js' ), 198 );
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_css' ), 198 );

            //we want bootstrap to load before the theme
            add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_bootstrap' ), 20 );

            //Needs to be loaded so that templates have CSS
            add_action( 'wp_head', array( $this, 'add_site_css' ) );

            //Needs to be loaded on init so that OCB_EDIT is defined
            add_action( 'init', array( $this, 'add_classes' ), 99 );
        }

        add_action( 'wp_footer', array( $this, 'add_global_template_css' ) );
        add_filter( 'script_loader_tag', array( $this, 'add_defer_async' ), 10 );

        if( ! OCB_EDIT ){
            add_filter( 'the_content', array( $this, 'process_content') );
        }
    }

    function add_classes(){
        if( ! OCB_EDIT ) {
            add_action( 'wp_head', array( $this, 'remove_content_formatting') );
            add_action( 'wp_head', array( $this, 'output_skin_css') );
            add_filter( 'body_class', array( $this, 'body_classes' ) );
        }
    }

    /**
     * Echos a link element to google fonts if google fonts is active
     * ex: <link href="https://fonts.googleapis.com/css?family=Asap+Condensed:400,700,700i|Roboto:400,900" rel="stylesheet">
     *
     * @param $site_settings
     */
    function link_google_fonts( $site_settings ){
        $fonts = array('body', 'body_header', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'accent');
        $font_strings = array();

        foreach( $fonts as $font ){
            if(
                ! isset( $site_settings[$font . '_font'] )
                || ! isset( $site_settings[$font . '_font']['choice'] )
                || $site_settings[$font . '_font']['choice'] == 'default'
            )
                continue;

            $the_font = $site_settings[$font . '_font'];
            $choice = $the_font['choice'];
            $variants = isset( $the_font['variants'] ) ? $the_font['variants'] : array('400');
            $the_variant = isset( $the_font['variant'] ) ? $the_font['variant'] : '400';
            $source = isset( $the_font['source'] ) ? $the_font['source'] : 'google';

            if( $source == 'builtIn' )
                continue;

            if( ! in_array( $the_variant,  $variants ) )
                $variants[] = $the_variant;

            $font_string = str_replace( ' ', '+', $choice );

            $new_variants = array();

            foreach( $variants as $variant ){
                if( $variant == 'italic' ) $variant = '400i';
                //$variant = str_replace( 'regular', '400', $variant );
                $variant = str_replace( 'italic', 'i', $variant );
                $new_variants[] = $variant;
            }

            sort($new_variants);

            $font_string .= ':' . implode(',', $new_variants);

            $font_strings[] = $font_string;

        }

        if( ! empty( $font_strings ) ) {
            $font_string = implode('|', $font_strings);
            echo '<link href="https://fonts.googleapis.com/css?family=' . $font_string . '" rel="stylesheet">';
        }
    }

    function body_classes( $classes ){
        if( ! is_user_logged_in() )
            array_push( $classes, 'ocb-logged-out' );

        return $classes;
    }

    function enqueue_js(){
        wp_enqueue_script( 'tether', OCB_MAIN_DIR . 'library/other/tether.min.js', array('jquery'), OCB_VERSION, true );
        //wp_enqueue_script( 'tether', 'https://npmcdn.com/tether@1.2.4/dist/js/tether.min.js', array('jquery'), OCB_VERSION, true );
        wp_enqueue_script( 'bootstrap-js', OCB_MAIN_DIR . 'library/bootstrap/js/bootstrap.min.js', array('tether', 'jquery'), OCB_VERSION, true );
        //wp_enqueue_script( 'other-plugins', OCB_MAIN_DIR . 'library/other/plugins.min.js', array('tether', 'jquery'), OCB_VERSION, true );
        //wp_enqueue_script( 'other-custom', OCB_MAIN_DIR . 'library/other/custom.min.js', array('other-plugins'), OCB_VERSION, true );
        wp_enqueue_script( 'masonry' );
        //wp_enqueue_script( 'swiper-js', OCB_MAIN_DIR . 'library/swiper/js/swiper.jquery.min.js', array( 'jquery' ), OCB_VERSION, true );

        wp_enqueue_script( 'offsprout-public', OCB_MAIN_DIR . 'builder/app/build/js/public.js', array('bootstrap-js', 'masonry'), OCB_VERSION, true );
        wp_enqueue_script( 'webfont', OCB_MAIN_DIR . 'library/other/webfont.js', array(), 1, true );

        $site_settings = Offsprout_Model::get_site_settings();

        if( ! OCB_EDIT ){
            $site_url = get_site_url();
            $domain = untrailingslashit( str_replace('http://', '', str_replace( 'https://', '', $site_url ) ) );
            $structure = get_post_meta( get_the_ID(), 'ocb_structure', true );

            $variables = array(
                'root'                  => esc_url_raw( rest_url() ),
                'ajaxURL'               => admin_url('admin-ajax.php', ( isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' ) ),
                'siteUrl'               => $site_url,
                'domain'                => $domain,
                'stockUrl'              => OCB_MAIN_DIR . 'library/stock/',
                'mainDir'               => OCB_MAIN_DIR,
                'postId'                => get_the_ID(),
                'postType'              => get_post_type(),
                'postStatus'            => get_post_status(),
                'isSSL'                 => is_ssl(),
                'structure'             => $structure,
                'permissions'           => Offsprout_Model::get_permissions()
            );

            wp_localize_script( 'offsprout-public', 'OCBGlobalSettings', $variables );
        }

        if( isset( $site_settings['google_maps'] ) && isset( $site_settings['google_maps']['text'] ) )
            wp_enqueue_script( 'google-maps', 'https://maps.googleapis.com/maps/api/js?key=' . $site_settings['google_maps']['text'] . '&callback=ocbInitAllMaps', array(), 3, true );
    }

    function enqueue_css(){
        wp_enqueue_style( 'offsprout-css', OCB_MAIN_DIR . 'builder/app/build/offsprout.css', array('bootstrap'), OCB_VERSION );
        wp_enqueue_style( 'fontawesome', OCB_MAIN_DIR . 'library/fontawesome/font-awesome.min.css', array() );

        if( ! OCB_EDIT ) {

            $site_css_file = get_option( 'ocb_global_css_filename' );

            if ( $site_css_file ) {
                $site_css_filename = Offsprout_Model::get_full_css_filepath( $site_css_file );
                $dependencies = array( 'offsprout-css' );
                if ( wp_get_theme()->get( 'Name' ) == 'Offsprout' )
                    $dependencies[] = 'offsprout-theme-style';
                wp_enqueue_style( 'offsprout-site-css', $site_css_filename, $dependencies );
            }

        }

        $site_settings = Offsprout_Model::get_site_settings();
        $has_subscription = Offsprout_Model::has_subscription();

        if( isset( $site_settings['linea_icons'] ) && isset( $site_settings['linea_icons']['yes'] ) )
            wp_enqueue_style( 'linea', OCB_MAIN_DIR . 'library/linea/styles.css', array() );
    }

    function enqueue_bootstrap(){
        //wp_enqueue_style( 'bootstrap', OCB_MAIN_DIR . 'library/bootstrap/css/bootstrap.min.css', array(), OCB_REQS_VERSION );
        wp_enqueue_style( 'bootstrap', OCB_MAIN_DIR . 'builder/app/build/bootstrap.css', array() );
    }

    /**
     * Will search for an image in css like background-image:url(https://site.com/image.jpg?featured_image=large) that has a query string
     * and it will replace it with whatever the current featured image url is
     *
     * @param $css
     * @return mixed
     */
    static function replace_featured_background_urls( $css ){
        preg_match_all( '~\bbackground(-image)?\s*:url(.*?)\(\s*(\'|")?(?<image>.*?)\3?\s*\)~i', $css, $matches );

        $featured_image_fallback = false;

        if( isset( $matches['image'] ) ) {
            foreach ( $matches['image'] as $match ) {
                $url = parse_url( $match );

                $size = 'full';

                if( isset( $url['query'] ) ){
                    $query = explode( '&', $url['query'] );
                    foreach( $query as $pair ){
                        $the_pair = explode( '=', $pair );
                        if( $the_pair[0] == 'featured_image' ){
                            $size = $the_pair[1];
                            break;
                        }
                    }

                    //Want to only have images with query vars so that images like the logo background aren't replaced
                    $featured_image = wp_get_attachment_image_src( get_post_thumbnail_id(), $size );
                    if( isset( $featured_image[0] ) && $featured_image[0] ) {
                        $featured_image = $featured_image[0];
                    } else {
                        if( $featured_image_fallback === false ){
                            $site_settings = Offsprout_Model::get_site_settings();
                            if( isset( $site_settings['featured_image_fallback'] ) && isset( $site_settings['featured_image_fallback']['url'] ) && $site_settings['featured_image_fallback']['url'] ){
                                $featured_image_fallback = $site_settings['featured_image_fallback']['url'];
                            } else {
                                $featured_image_fallback = '';
                            }
                        }
                        if( $featured_image_fallback === '' )
                            $featured_image = 'https://s3-us-west-2.amazonaws.com/s.cdpn.io/142996/slider-2.jpg';
                        else
                            $featured_image = $featured_image_fallback;
                    }
                    $css = str_replace( $match, $featured_image, $css );
                }

            }
        }

        return $css;
    }

    /**
     * Adds CSS inline
     *
     * Why not in a file? http://ottopress.com/2011/tutorial-using-the-wp_filesystem/
     *
     */
    function add_site_css(){
        $site_settings = Offsprout_Model::get_site_settings();

        if( isset( $site_settings['use_google_fonts'] ) && isset( $site_settings['use_google_fonts']['yes'] ) && $site_settings['use_google_fonts']['yes'] ){
            $this->link_google_fonts( $site_settings );
        }

        $site_css_file = get_option( 'ocb_global_css_filename' );

        if( $site_css_file && ! OCB_EDIT ){

            //need to handle featured image replacement

        } else {
            $site_css = get_option( 'ocb_site_css' );
            $site_css = isset( $site_css['css'] )
                ? is_array( $site_css['css'] ) ? '' : $site_css['css']
                : is_array( $site_css ) && empty( $site_css ) ? '' : $site_css;


            if( $site_css ) {
                $site_css = self::replace_featured_background_urls( $site_css );
                echo '<style type="text/css" class="ocb-site">' . $site_css . '</style>';
            }
        }

        if( ! OCB_EDIT ){
            $skins_used = get_post_meta( get_the_ID(), 'ocb_skins_used', true );
            $skins_used = is_array( $skins_used ) ? $skins_used : array();

            $structure_id = Offsprout_Model::safe_get_structure( false, 'default', 'id' );

            //Structure skins
            if( $structure_id ){
                $structure_skins = get_post_meta( $structure_id, 'ocb_skins_used', true );
                $structure_skins = is_array( $structure_skins ) ? $structure_skins : array();

                $skins_used = array_merge( $skins_used, $structure_skins );
            }

            $skins = get_option( 'ocb_site_skins' );

            $skin_css = '';
            foreach( $skins_used as $skin ){
                if( isset( $skins[$skin] ) ){
                    $skin_css .= is_array( $skins[$skin]['css'] ) ? '' : $skins[$skin]['css'];
                    $skin_css .= is_array( $skins[$skin]['standardCSS'] ) ? '' : $skins[$skin]['standardCSS'];
                }
            }

            if( $skin_css ) {
                $skin_css = self::replace_featured_background_urls( $skin_css );
                echo '<style type="text/css" class="ocb-skins">' . $skin_css . '</style>';
            }
        }

        $page_css = get_post_meta( get_the_ID(), 'ocb_page_css', true );
        $page_css = isset( $page_css['css'] ) ? $page_css['css'] : $page_css;

        if( $page_css ) {
            $page_css = self::replace_featured_background_urls( $page_css );
            echo '<style type="text/css" class="ocb-page">' . $page_css . '</style>';
        }
    }

    function remove_content_formatting(){
        if( get_the_ID() && get_post_meta( get_the_ID(), 'ocb_tree_content', true ) ) {
            remove_filter( 'the_content', 'wpautop' );
        }
    }

    function add_defer_async($tag){

        $scripts_to_include = array('maps.googleapis.com');

        foreach($scripts_to_include as $include_script){
            if( strpos( $tag, $include_script ) === false )
                return $tag;
        }

        return str_replace( ' src', ' defer async src', $tag );
    }

    public function add_global_template_css(){
        global $ocb_global_css;

        if( $ocb_global_css && ( ! OCB_EDIT || get_post_type( get_the_ID() ) != 'ocb_structure' ) ) {
            $ocb_global_css = self::replace_featured_background_urls( $ocb_global_css );

            echo '<style id="global-template-css" type="text/css">' . $ocb_global_css . '</style>';
        }

        //Add the skin CSS if it hasn't already been added by process_content() which would be the case if this is an archive
        self::add_extra_skin_css();
    }

    /**
     * Takes the post_content and will search for skin tags in the form of {{ocbskin:settings:skinjog2goxl4yz7u4t3y:moduleImage}}
     * Replaces them with the appropriate classes from the skin
     *
     * @param $post_content
     * @return mixed
     */
    public function process_content( $post_content ){
        $new_post_content = Offsprout_Model::replace_strings( $post_content );

        return $new_post_content;
    }

    /**
     * Outputs add_extra_skin_css in wp_head
     * Need it to be late enough that it gets populated by the skin replace routine.
     */
    public function output_skin_css(){
        self::add_extra_skin_css();
    }

    /**
     * Gets extra skins from Structures
     * Structure skins aren't loaded in add_site_css()
     * Compares to make sure the skin hasn't already been loaded
     *
     * @param bool $return
     * @return string
     */
    public static function add_extra_skin_css( $return = false ){
        //$ocb_skins_used gets populated during the skin replace routine
        global $ocb_skins_used;
        global $ocb_skins_css_added;

        //Add Structure skins
        if( is_array( $ocb_skins_used ) && ! empty( $ocb_skins_used ) && ! $ocb_skins_css_added ){
            $skins = get_option( 'ocb_site_skins' );

            $skins_used = get_post_meta( get_the_ID(), 'ocb_skins_used', true );
            $skins_used = is_array( $skins_used ) ? $skins_used : array();

            $structure_id = Offsprout_Model::safe_get_structure( false, 'default', 'id' );

            if( $structure_id ){
                $structure_skins = get_post_meta( $structure_id, 'ocb_skins_used', true );
                $structure_skins = is_array( $structure_skins ) ? $structure_skins : array();

                $skins_used = array_merge( $skins_used, $structure_skins );
            }

            $skin_css = '';
            foreach( $ocb_skins_used as $skin ){
                if( ! in_array( $skin, $skins_used ) && isset( $skins[$skin] ) ){
                    $skin_css .= $skins[$skin]['css'];
                    $skin_css .= $skins[$skin]['standardCSS'];
                }
            }

            if( $skin_css ) {
                $skin_css = self::replace_featured_background_urls( $skin_css );
                $skin_output = '<style type="text/css" class="ocb-skins-used">' . $skin_css . '</style>';
                $ocb_skins_css_added = true;

                if( $return )
                    return $skin_output;

                echo $skin_output;
            }
        }
    }
}