<?php

/**
 * Handles the base logic for connecting fields in the
 * builder UI to dynamic data such as the post title.
 *
 * @since 1.0
 */
final class Offsprout_Connector_Shortcode {

    /**
     * Initialize hooks.
     *
     * @since 1.0
     * @return void
     */
    static public function init() {
        // Filters
        add_filter( 'ocb_before_render_shortcodes',       __CLASS__ . '::parse_shortcodes' );

        // Shortcodes
        add_shortcode( 'ocbconnect',                      __CLASS__ . '::parse_shortcode' );
        add_shortcode( 'ocb-if',                          __CLASS__ . '::parse_conditional_shortcode' );
    }

    /**
     * Parse all "ocb" shortcodes here instead of relying on do_shortcode
     * since as of WordPress 4.2.3 that doesn't allow you to put shortcodes
     * in HTML attributes or styles.
     *
     * @since 1.0
     * @param string $content
     * @return string
     */
    static public function parse_shortcodes( $content ) {
        $pattern = get_shortcode_regex( array( 'ocb' ) );
        $content = preg_replace_callback( "/$pattern/", 'do_shortcode_tag', $content );
        return $content;
    }

    /**
     * Connects a field connection through a shortcode.
     *
     * @since 1.0
     * @param array $attrs
     * @return string
     */
    static public function parse_shortcode( $attrs ) {
        global $post;
        global $ocb_connector_default;

        if ( ! isset( $attrs ) || ! isset( $attrs[0] ) ) {
            return '';
        }

        $type     = explode( ':', $attrs[0] );
        $settings = null;

        if ( count( $type ) < 2 ) {
            return '';
        }

        if ( count( $attrs ) > 1 ) {
            unset( $attrs[0] );
            $settings = (object) $attrs;
        }

        $the_class = 'Offsprout_Post_Data';

        switch( $type[0] ){
            case 'social':
                $the_class = 'Offsprout_Site_Data';
                break;
            case 'site':
                $the_class = 'Offsprout_Site_Data';
                break;
            case 'company':
                $the_class = 'Offsprout_Company_Data';
                break;
            case 'utility':
                $the_class = 'Offsprout_Utility_Data';
                break;
            case 'woocommerce':
                $archive_functions = array('result_count');

                if( in_array( $type[1], $archive_functions ) )
                    $the_return = Offsprout_WooCommerce_Data::product_archive( $type[1], $settings );
                else
                    $the_return = Offsprout_WooCommerce_Data::single_product( $type[1], $settings );

                return $the_return;

                break;
        }

        $the_class = apply_filters( 'ocb_connector_shortcode_data_class', $the_class, $type[0] );

        if( ! class_exists( $the_class ) )
            return 'Connection data not found';

        $the_function = $type[1];

        if( ! method_exists( $the_class, $the_function ) )
            return 'Connection data not found';

        $the_return = $the_class::$the_function( $settings );

        if( ! $the_return && $ocb_connector_default )
            $the_return = $the_function;

        return $the_return;
    }

    /**
     * Parses conditional ocb-if shortcodes.
     *
     * @since 1.0
     * @param array  $attrs
     * @param string $content
     * @return string
     */
    static public function parse_conditional_shortcode( $attrs, $content = '' ) {
        if ( ! isset( $attrs ) || ! isset( $attrs[0] ) ) {
            return __( 'Incorrect ocb-if shortcode attributes.', 'offsprout' );
        }

        $parts = explode( ':', $attrs[0] );

        if ( count( $parts ) < 2 ) {
            return __( 'Incorrect ocb-if shortcode attributes.', 'offsprout' );
        }

        $not      = 0 === strpos( $parts[0], '!' ); // @codingStandardsIgnoreLine
        $attrs[0] = str_replace( '!', '', $attrs[0] );
        $value    = self::parse_shortcode( $attrs );

        if ( $not && empty( $value ) ) {
            return do_shortcode( $content );
        } elseif ( ! $not && $value ) {
            return do_shortcode( $content );
        }

        return '';
    }

    static public function return_value_or_default( $value, $default = '' ){

        if( ! $value )
            $value = $default;

        return $value;

    }
}

Offsprout_Connector_Shortcode::init();
