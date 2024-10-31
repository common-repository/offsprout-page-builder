<?php

/**
 * Class Offsprout_Force_Frontend_SSL
 *
 * from wp-force-ssl plugin
 *
 * Only instantiated by WP setting ocb_force_ssl in Offsprout_Start
 */
class Offsprout_Force_Frontend_SSL{
    public function __construct() {
        define('FORCE_SSL' , true);

        if ( defined('FORCE_SSL') )
            add_action('template_redirect', array( $this, 'force_ssl' ) );
    }

    function force_ssl(){
        if ( FORCE_SSL && ! is_ssl() ) {
            wp_redirect('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'], 301 );
            exit();
        }
    }
}
new Offsprout_Force_Frontend_SSL();