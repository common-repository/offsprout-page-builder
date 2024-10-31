<?php

/**
 * Handles logic for site data properties.
 *
 * @since 1.0
 */
final class Offsprout_Site_Data {

    static function build_site_data(){
        global $ocb_site_data;

        if( ! $ocb_site_data ){
            $ocb_site_data = get_option( 'ocb_site_settings' );
        }

        return $ocb_site_data;
    }

    static function get_site_data( $option, $property ){
        $data = self::build_site_data();

        if( isset( $data[$option] ) && isset( $data[$option][$property] ) ){
            return $data[$option][$property];
        }

        return '';
    }

    static function get_social( $option, $property, $settings ){
        $url = self::get_site_data( $option, $property );

        if( ! $url )
            return '';

        $return = $url;

        if( isset( $settings->linked ) && $settings->linked ){

            $link_text = isset( $settings->link_text ) && $settings->link_text ? $settings->link_text : $url;
            $return = '<a href="' . $url . '">' . $link_text . '</a>';

        }

        return $return;
    }

    static function facebook( $settings ){
        return self::get_social( 'facebook_url', 'text', $settings );
    }

    static function linkedin( $settings ){
        return self::get_social( 'linkedin_url', 'text', $settings );
    }

    static function twitter( $settings ){
        return self::get_social( 'twitter_url', 'text', $settings );
    }

    static function googleplus( $settings ){
        return self::get_social( 'googleplus_url', 'text', $settings );
    }

    static function instagram( $settings ){
        return self::get_social( 'instagram_url', 'text', $settings );
    }

    static function youtube( $settings ){
        return self::get_social( 'youtube_url', 'text', $settings );
    }

    static function vimeo( $settings ){
        return self::get_social( 'vimeo_url', 'text', $settings );
    }

    static function pinterest( $settings ){
        return self::get_social( 'pinterest_url', 'text', $settings );
    }

    static function yelp( $settings ){
        return self::get_site_data( 'yelp_url', 'text', $settings );
    }

    static function site_title( $settings ){
        $site_title = get_bloginfo( 'name' );
        $return = $site_title;

        if( isset( $settings->linked ) && $settings->linked )
            $return = '<a href="' . get_bloginfo( 'url' ) . '">' . $site_title . '</a>';

        return $return;
    }

    static function site_tagline(){
        return get_bloginfo( 'description' );
    }

}