<?php

/**
 * Handles logic for site data properties.
 *
 * @since 1.0
 */
final class Offsprout_Utility_Data {

    public static function year( $settings ){
        return date( 'Y' );
    }

    public static function month( $settings ){
        return date( $settings->format );
    }

    public static function day( $settings ){
        return date( $settings->format );
    }

}