<?php

class Offsprout_WP_Toolbar{

    static public function init(){
        $permissions = Offsprout_Model::get_permissions();

        if( ! isset( $permissions['access_page_builder'] ) || $permissions['access_page_builder'] ){
            add_action('admin_bar_menu', __CLASS__ . '::admin_bar_menu', 999);
        }
    }

    /**
     * Adds the page builder button to the WordPress admin bar.
     *
     * @since 1.0
     * @param object $wp_admin_bar An instance of the WordPress admin bar.
     * @return void
     */
    static public function admin_bar_menu($wp_admin_bar) {
        global $wp_the_query;

        if ( Offsprout_Model::is_post_editable() ) {

            $wp_admin_bar->add_node( array(
                'id'    => 'offsprout-frontend-edit-link',
                'title' => '<style> #wp-admin-bar-offsprout-frontend-edit-link .ab-icon:before { content: "\f116" !important; top: 2px; margin-right: 3px; } </style><span class="ab-icon"></span>' . Offsprout_Model::get_branding(),
                'href'  => Offsprout_Model::get_edit_url( $wp_the_query->post->ID )
            ));

        } elseif( is_admin() ){

            $wp_admin_bar->add_node( array(
                'id'    => 'offsprout-frontend-edit-link',
                'title' => '<style> #wp-admin-bar-offsprout-frontend-edit-link .ab-icon:before { content: "\f116" !important; top: 2px; margin-right: 3px; } </style><span class="ab-icon"></span>' . Offsprout_Model::get_branding(),
                'href'  => home_url( '/?pageEdit=1' )
            ));

        }
    }

}

Offsprout_WP_Toolbar::init();