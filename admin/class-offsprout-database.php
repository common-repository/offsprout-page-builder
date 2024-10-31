<?php

/**
 * Handles logic for creating and updating database tables.
 *
 * @since 2.0
 */
class Offsprout_Admin_Database {

    /**
     * Initializes the admin settings.
     *
     * @since 1.0
     * @return void
     */
    static public function init() {
        //self::create_skins_tables();
    }

    /**
     * Create the tables to hold the module skins
     *
     * id
     * moduleType: type of module
     * classes: array of classes to be applied on various dom objects (includes both module and standard)
     * moduleCss: the custom css of the module
     * moduleSettings: the design settings of the module
     * standardCss: the standard custom css of the module
     * standardSettings: the standard settings of the module
     */
    static public function create_skins_tables(){
        global $wpdb;

        $table_name = $wpdb->prefix . 'ocb_skins';

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE $table_name (
            id varchar(25) DEFAULT '' NOT NULL,
            name varchar(50) DEFAULT '' NOT NULL,
            moduleType varchar(25) DEFAULT '' NOT NULL,
            classes text NOT NULL,
            moduleCss text NOT NULL,
            moduleSettings text NOT NULL,
            standardCss text NOT NULL,
            standardClasses text NOT NULL,
            standardSettings text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $sql );

        add_option( 'offsprout_db_version', OCB_VERSION );
    }

}

Offsprout_Admin_Database::init();