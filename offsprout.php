<?php
/*
Plugin Name: Offsprout Page Builder
Plugin URI: https://offsprout.com
Description: The only WordPress page builder built specifically for design agencies and freelancers
Version: 2.15.2
Author: Offsprout
License: GPL2

Offsprout Page Builder is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 2 of the License, or
any later version.

Offsprout Page Builder is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with Offsprout Page Builder.

If not, see https://www.gnu.org/licenses/old-licenses/gpl-2.0.en.html.
*/

define( 'OCB_PLUGIN_FILE', __FILE__ );
define( 'OCB_TESTING', false );

/**
 * These provide values to the installer and override certain global options
 */
function offsprout_plugin_constants(){

    $constants = array(

        //Override the first template site option
        'OCB_TEMPLATE_SITE_1' => '',

        //Override the second template site option
        'OCB_TEMPLATE_SITE_2' => '',

        //The url of the first template site, saved during installation
        'OCB_TEMPLATE_SITE_DEFAULT_1' => 'https://basic.t.offsprout.com',

        //The url of the second template site, saved during installation
        'OCB_TEMPLATE_SITE_DEFAULT_2' => '',

    );

    $constants = apply_filters( 'offsprout_plugin_constants', $constants );

    foreach( $constants as $key => $value ){
        define( $key, $value );
    }

}
add_action( 'plugins_loaded', 'offsprout_plugin_constants' );

//Contains the functions below that get us started
require_once dirname(__FILE__) . '/class-offsprout-start.php';

Offsprout_Start::define_constants();
Offsprout_Start::bootstrap();
Offsprout_Start::installation();

do_action( 'after_offsprout_plugin_loaded' );