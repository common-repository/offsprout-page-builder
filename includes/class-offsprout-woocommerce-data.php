<?php

/**
 * Handles logic for site data properties.
 *
 * @since 1.0
 */
final class Offsprout_WooCommerce_Data {

    static function single_product( $which, $settings ){
        global $product;

        if( ! $product ) return str_replace( 'get_', '', $which );

        switch( $which ){
            case 'get_date_on_sale_from':
            case 'get_date_on_sale_to':
                $date = $product->$which();
                $date = strtotime( $date );
                $date = date( $settings->format, $date );

                $return = $date;

                break;

            case 'tags':
                $return = wc_get_product_tag_list( $product->get_id() );
                break;

            case 'categories':
                $return = wc_get_product_category_list( $product->get_id() );
                break;

            case 'get_availability':
                $availability = $product->get_availability();
                $return = $availability['availability'] ? $availability['availability'] : __( 'In Stock', 'offsprout' );
                break;

            case 'get_rating_count':
                if( $settings->stars == 'all' )
                    $return = $product->get_rating_counts();
                else
                    $return = $product->get_rating_count( $settings->stars );

                break;

            default:
                $return = $product->$which();
        }

        return $return;
    }

    static function product_archive( $which, $settings ){

        if( ! is_archive() ) return str_replace( 'woocommerce_', '', $which );

        switch( $which ){
            case 'result_count':
                $return = isset( $GLOBALS['wp_query'] ) ? $GLOBALS['wp_query']->post_count : 0;
                break;
            default:
                $return = $which();
        }

        return $return;
    }

}