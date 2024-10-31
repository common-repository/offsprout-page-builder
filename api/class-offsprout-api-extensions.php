<?php

class Offsprout_API_Extensions extends WP_REST_Controller{

    function register_routes(){
        $namespace = 'offsprout/';
        $version = 'v2';

        register_rest_route( $namespace . $version, '/meta/(?P<metaKey>[\S]+)/(?P<postId>[\d]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_meta' ),
                'permission_callback' => array( $this, 'get_meta_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'create_meta' ),
                'permission_callback' => array( $this, 'create_meta_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'update_meta' ),
                'permission_callback' => array( $this, 'update_meta_permissions_check' )
            ),
            array(
                'methods'  => WP_REST_Server::DELETABLE,
                'callback' => array( $this, 'delete_meta' ),
                'permission_callback' => array( $this, 'delete_meta_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/usermeta/(?P<metaKey>[\S]+)/(?P<userId>[\d]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_usermeta' ),
                'permission_callback' => array( $this, 'get_meta_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'create_usermeta' ),
                'permission_callback' => array( $this, 'create_meta_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'update_usermeta' ),
                'permission_callback' => array( $this, 'update_meta_permissions_check' )
            ),
            array(
                'methods'  => WP_REST_Server::DELETABLE,
                'callback' => array( $this, 'delete_usermeta' ),
                'permission_callback' => array( $this, 'delete_meta_permissions_check' )
            )
        ) );

        /*register_rest_route( $namespace . $version, '/shortcode/(?P<shortcode>[\S]+)/(?P<shortcodeArgs>[\S\s]+)/(?P<shortcodeContent>[\S\s]+)/(?P<postId>[\d]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_shortcode_output' ),
                'permission_callback' => array( $this, 'get_shortcode_permissions_check' )
            )
        ) );*/

        register_rest_route( $namespace . $version, '/shortcode/(?P<shortcode>[\S]+)/(?P<postId>[\d]+)', array(
            array(
                'methods'         => 'POST,PUT',
                'callback'        => array( $this, 'get_shortcode_output' ),
                'permission_callback' => array( $this, 'get_shortcode_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/embed', array(
            array(
                'methods'         => 'POST,PUT',
                'callback'        => array( $this, 'get_embed_output' ),
                'permission_callback' => array( $this, 'get_shortcode_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/settings/(?P<setting>[\S]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_option' ),
                'permission_callback' => array( $this, 'get_options_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'create_option' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'update_option' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::DELETABLE,
                'callback'        => array( $this, 'delete_option' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            ),
        ) );

        register_rest_route( $namespace . $version, '/post_type/(?P<type>[\S]+)/(?P<postId>[\d]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_of_post_type' ),
                'permission_callback' => array( $this, 'get_meta_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/post_type/(?P<type>[\S]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_of_post_type' ),
                'permission_callback' => array( $this, 'get_meta_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/taxonomy/(?P<type>[\S]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_of_taxonomy' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/templates/(?P<customId>[\S]+)', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_template' ),
                'permission_callback' => array( $this, 'get_template_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'create_template' ),
                'permission_callback' => array( $this, 'create_template_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'update_template' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            ),
            array(
                'methods'         => WP_REST_Server::DELETABLE,
                'callback'        => array( $this, 'delete_template' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            ),
        ) );

        register_rest_route( $namespace . $version, '/structures/(?P<postId>[\d]+)', array(
            array(
                'methods'         => WP_REST_Server::EDITABLE,
                'callback'        => array( $this, 'update_structure' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/all_templates', array(
            array(
                'methods'         => 'POST,PUT',
                'callback'        => array( $this, 'get_templates' ),
                'permission_callback' => array( $this, 'get_template_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/all_structures', array(
            array(
                'methods'         => WP_REST_Server::READABLE,
                'callback'        => array( $this, 'get_structures' ),
                'permission_callback' => array( $this, 'get_template_permissions_check' )
            ),
        ) );

        register_rest_route( $namespace . $version, '/query', array(
            array(
                'methods'         => 'POST,PUT',
                'callback'        => array( $this, 'get_query' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            ),
        ) );

        register_rest_route( $namespace . $version, '/utility', array(
            array(
                'methods'         => 'POST,PUT',
                'callback'        => array( $this, 'do_utility' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            ),
        ) );

        register_rest_route( $namespace . $version, '/custom/(?P<action>[\S]+)', array(
            array(
                'methods'         => WP_REST_Server::CREATABLE,
                'callback'        => array( $this, 'create_custom' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            ),
            array(
                'methods'         => 'POST,PUT',
                'callback'        => array( $this, 'do_custom' ),
                'permission_callback' => array( $this, 'options_permissions_check' )
            )
        ) );

        register_rest_route( $namespace . $version, '/custom_user/(?P<action>[\S]+)', array(
            array(
                'methods'         => 'POST,PUT',
                'callback'        => array( $this, 'do_custom_user' ),
                'permission_callback' => array( $this, 'edit_users_permissions_check' )
            )
        ) );
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_shortcode_permissions_check( $request ) {
        //return true; <--use to make readable by all
        return current_user_can( 'edit_posts' );
    }

    /**
     * Check if a given request has access to get items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_meta_permissions_check( $request ) {
        //return true; <--use to make readable by all
        return current_user_can( 'edit_posts' );
    }

    /**
     * Check if a given request has access to create items
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function create_meta_permissions_check( $request ) {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Check if a given request has access to update a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function update_meta_permissions_check( $request ) {
        return $this->create_meta_permissions_check( $request );
    }

    /**
     * Check if a given request has access to delete a specific item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function delete_meta_permissions_check( $request ) {
        return $this->create_meta_permissions_check( $request );
    }

    /**
     * Check if a given request has access to get a setting
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function get_options_permissions_check( $request ) {
        return current_user_can( 'edit_posts' );
    }

    /**
     * Check if a given request has access to manipulate site settings
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|bool
     */
    public function options_permissions_check( $request ) {
        return current_user_can( 'manage_options' );
    }

    /**
     * Check if a user can get templates
     *
     * @return bool
     */
    public function get_template_permissions_check(){
        return current_user_can('manage_options');
    }

    /**
     * Check if user can edit other users
     *
     * @return bool
     */
    public function edit_users_permissions_check(){
        return current_user_can( 'edit_users' );
    }

    /**
     * Get a shortcode item
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_shortcode_output( $request ) {
        $shortcode = $request->get_param( 'shortcode' );
        //$shortcode_args = urldecode( $request->get_param( 'shortcodeArgs' ) );
        //$shortcode_content = urldecode( $request->get_param( 'shortcodeContent' ) );
        $json_payload = $request->get_json_params();
        $shortcode_args = isset( $json_payload['args'] ) ? $json_payload['args'] : false;
        $shortcode_content = isset( $json_payload['content'] ) ? $json_payload['content'] : false;

        $post_id = (int) $request->get_param( 'postId' );

        if( $post_id ){
            global $post;
            $post = get_post( $post_id, OBJECT );
            setup_postdata( $post );
        }

        $the_shortcode = '[';

        if( $shortcode != 'NO_SHORTCODE' ){
            $the_shortcode .= $shortcode;
        } else {
            return new WP_Error( 'noShortcode', __( 'Could not find any shortcode data for shortcode ' . $shortcode, 'offsprout' ), array( 'status' => 405 ) );
        }

        if( $shortcode_args != 'NO_SHORTCODE_ARGS' ){
            $the_shortcode .= $shortcode_args;
        }

        $the_shortcode .= ']';

        if( $shortcode_content != 'NO_SHORTCODE_CONTENT' ){
            $the_shortcode .= $shortcode_content;
            $the_shortcode .= '[/' . $shortcode . ']';
        }

        $data = do_shortcode( $the_shortcode );

        if ( $data ) {
            return new WP_REST_Response( $data, 200 );
        }

        return new WP_REST_Response( 'No data for ' . $shortcode, 200 );
    }

    /**
     * Get oEmbed output
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_embed_output( $request ) {
        $embed = $request->get_json_params();

        //$this->write_debug( $embed );

        $data = isset( $embed['embed'] ) ? wp_oembed_get( $embed['embed'] ) : false;

        //$this->write_debug( $data );

        if ( $data ) {
            return new WP_REST_Response( $data, 200 );
        }

        return new WP_Error( 'noMetaData', __( 'Could not find any embed data for embed ' . $embed['embed'], 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_meta( $request ) {
        $id = (int) $request->get_param( 'postId' );
        $meta_key = (string) $request->get_param( 'metaKey' );

        $data = get_post_meta( $id, $meta_key, true );

        if ( $data ) {
            return new WP_REST_Response( $data, 200 );
        }

        if( $meta_key == 'ocb_tree_content' ){
            return new WP_REST_Response( 1, 200 );
        }

        return new WP_Error( 'noMetaData', __( 'Could not find any meta data for meta_key ' . $meta_key . ' with post_id ' . $id, 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Create one item from the collection
     *
     * I DO NOT THINK THIS IS USED CURRENTLY
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function create_meta( $request ) {
        $id = (int) $request->get_param( 'postId' );
        $meta_key = (string) $request->get_param( 'metaKey' );
        $json_payload = $request->get_json_params();

        $value = isset( $json_payload['metaValue'] ) ? $json_payload['metaValue'] : $json_payload;

        $data = update_post_meta( $id, $meta_key, $value );

        if ( $data ) {
            return new WP_REST_Response( $request, 200 );
        }

        return new WP_Error( 'noCreateMetaData', __( 'Could not create meta data for meta_key ' . $meta_key . ' with post_id ' . $id, 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Update one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function update_meta( $request ) {
        $id = (int) $request->get_param( 'postId' );
        $meta_key = (string) $request->get_param( 'metaKey' );
        $json_payload = $request->get_json_params();

        if ( is_array( $json_payload ) ) {
            $value = isset( $json_payload['metaValue'] ) ? $json_payload['metaValue'] : $json_payload;

            if( $meta_key == 'ocb_tree_content' ){
                $info = array(
                    'user_id' => get_current_user_id(),
                    'post_id' => $id
                );

                do_action( 'ocb_save_page', $info, $value );
            } elseif( $meta_key == 'ocb_tree_content_backup' ){
                $info = array(
                    'user_id' => get_current_user_id(),
                    'post_id' => $id
                );

                do_action( 'ocb_save_page_backup', $info, $value );
                return new WP_REST_Response( $request, 200 );
            }

            update_post_meta( $id, $meta_key, $value );
            return new WP_REST_Response( $request, 200 );
        }

        return new WP_Error( 'noUpdateMetaData', __( 'Could not update meta data for meta_key ' . $meta_key . ' with post_id ' . $id, 'offsprout' ), array( 'status' => 405 ) );

    }

    /**
     * Delete one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function delete_meta( $request ) {
        $id = (int) $request->get_param( 'postId' );
        $meta_key = (string) $request->get_param( 'metaKey' );

        $deleted = delete_post_meta( $id, $meta_key );
        if (  $deleted  ) {
            return new WP_REST_Response( $request, 200 );
        }

        return new WP_Error( 'cant-delete', __( 'message', 'text-domain'), array( 'status' => 405 ) );
    }

    /**
     * Get one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Response
     */
    public function get_usermeta( $request ) {
        $user_id = (int) $request->get_param( 'userId' );
        $meta_key = (string) $request->get_param( 'metaKey' );

        $data = get_user_meta( $user_id, $meta_key, true );

        switch( $meta_key ){
            case 'ocb_builder_role':
                $roles = get_option( 'ocb_builder_roles' );

                if( $data && is_array( $data ) && isset( $roles[$data] ) ){
                    $data = $roles[$data];
                } else {
                    $data = Offsprout_Model::get_permissions_structure();
                }

                break;
        }
        if ( $data ) {
            return new WP_REST_Response( $data, 200 );
        }

        return new WP_Error( 'noMetaData', __( 'Could not find any meta data for meta key ' . $meta_key . ' with user id ' . $user_id, 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Create one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function create_usermeta( $request ) {
        $user_id = (int) $request->get_param( 'userId' );
        $meta_key = (string) $request->get_param( 'metaKey' );
        $json_payload = $request->get_json_params();

        $data = update_user_meta( $user_id, $meta_key, $json_payload );
        if ( $data ) {
            return new WP_REST_Response( $request, 200 );
        }

        return new WP_Error( 'noCreateMetaData', __( 'Could not create meta data for meta_key ' . $meta_key . ' with user_id ' . $user_id, 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Update one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function update_usermeta( $request ) {
        $user_id = (int) $request->get_param( 'userId' );
        $meta_key = (string) $request->get_param( 'metaKey' );
        $json_payload = $request->get_json_params();

        //$this->write_debug( $meta_key );
        //$this->write_debug( $json_payload );

        if ( is_array( $json_payload ) ) {
            $value = isset( $json_payload['metaValue'] ) ? $json_payload['metaValue'] : $json_payload;
            update_user_meta( $user_id, $meta_key, $value );
            return new WP_REST_Response( $request, 200 );
        }

        return new WP_Error( 'noUpdateMetaData', __( 'Could not update meta data for meta_key ' . $meta_key . ' with user_id ' . $user_id, 'offsprout' ), array( 'status' => 405 ) );

    }

    /**
     * Delete one item from the collection
     *
     * @param WP_REST_Request $request Full data about the request.
     * @return WP_Error|WP_REST_Request
     */
    public function delete_usermeta( $request ) {
        $user_id = (int) $request->get_param( 'userId' );
        $meta_key = (string) $request->get_param( 'metaKey' );

        $deleted = delete_post_meta( $user_id, $meta_key );
        if ( $deleted ) {
            return new WP_REST_Response( $request, 200 );
        }

        return new WP_Error( 'cant-delete', __( 'Could not delete the usermeta', 'text-domain'), array( 'status' => 405 ) );
    }

    /**
     * Retrieve an standard WordPress setting
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_option( $request ) {
        $option = (string) $request->get_param( 'setting' );

        $data = get_option( $option );

        if ( $data || $option == 'ocb_site_settings' || $data === false ) {
            return new WP_REST_Response( $data, 200 );
        }

        return new WP_Error( 'settingNotFoundOrFalse', __( 'Could not find a value for ' . $option, 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Update a standard WordPress setting
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function update_option( $request ) {
        $option = (string) $request->get_param( 'setting' );
        $json_payload = $request->get_json_params();

        $property = isset( $json_payload['property'] ) ? (string) $json_payload['property'] : '';
        $force = isset( $json_payload['force'] ) ? (int) $json_payload['force'] : 0;

        if ( $json_payload ) {
            $value = isset( $json_payload['value'] ) ? $json_payload['value'] : $json_payload;

            $value = apply_filters( 'ocb_api_update_option', $value, $option, $property );

            if( $property ){
                $current_option = get_option( $option );
                if( is_array( $current_option ) ){
                    $current_option[$property] = $value;
                    $updated = update_option( $option, $current_option );
                } else {
                    if( $force ){
                        $current_option = array();
                        $current_option[$property] = $value;
                        $updated = update_option( $option, $current_option );
                    } else {
                        return new WP_Error( 'settingNotUpdated', __( 'Cannot update option ' . $option . ' property ' . $property, 'offsprout' ), array( 'status' => 405 ) );
                    }
                }
            } else {
                $updated = update_option( $option, $value );
            }


            if( $updated )
                return new WP_REST_Response( 1, 200 );

            if( $updated === false )
                return new WP_REST_Response( 2, 200 );
        } else {
            $updated = update_option( $option, $json_payload );

            if( $updated )
                return new WP_REST_Response( 3, 200 );

            if( $updated === false )
                return new WP_REST_Response( 4, 200 );
        }

        return new WP_Error( 'settingNotUpdated', __( 'Setting ' . $option . ' could not be updated', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Delete a standard WordPress setting
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function delete_option( $request ) {
        $option = (string) $request->get_param( 'setting' );
        $json_payload = $request->get_json_params();

        $property = isset( $json_payload['property'] ) ? (string) $json_payload['property'] : '';

        if ( $json_payload ) {
            if( $property ){
                $current_option = get_option( $option );
                if( is_array( $current_option ) ){
                    unset( $current_option[$property] );
                    $deleted = update_option( $option, $current_option );
                } else {
                    $deleted = false;
                }
            } else {
                $deleted = delete_option( $option );
            }


            if( $deleted )
                return new WP_REST_Response( 1, 200 );

            if( $deleted === false )
                return new WP_REST_Response( 2, 200 );
        } else {
            $deleted = delete_option( $option );

            if( $deleted )
                return new WP_REST_Response( 3, 200 );

            if( $deleted === false )
                return new WP_REST_Response( 4, 200 );
        }

        return new WP_Error( 'settingNotDeleted', __( 'Setting ' . $option . ' could not be deleted', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Get published posts of type
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_of_post_type( $request ){
        $type = $request->get_param( 'type' );
        //$post_id = $request->get_param( 'postId' );

        $query = new WP_Query(array(
            'post_type' => $type,
            'posts_per_page' => 1000,
            'post_status' => ( $type != 'attachment' ? 'publish' : 'any' )
        ));

        $return = array();

        while ($query->have_posts()) {
            $query->the_post();
            $post = get_post();
            unset( $post->post_content );
            $post->permalink = get_permalink();
            $return[] = $post;
        }

        if ( $return ) {
            return new WP_REST_Response( $return, 200 );
        }

        return new WP_Error( 'noPosts', __( 'Could not find any posts of type ' . $type, 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Get ocb_template posts with their meta values
     *
     * May want to use get_custom instead
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_templates_old( $request ){
        $json_payload = $request->get_json_params();

        $tax_query = array();
        $type           = isset( $json_payload['type'] )        ? sanitize_text_field( $json_payload['type'] )        : false;
        $module_type    = isset( $json_payload['moduleType'] )  ? sanitize_text_field( $json_payload['moduleType'] )  : false;

        $global         =
            isset( $json_payload['global'] )
            && $json_payload['global'] != 'no'
            && $json_payload['global'] != false
                ? 'yes' : 'no';

        if( $type ){
            $tax_query[] = array(
                'taxonomy' => 'ocb_template_type',
                'field'    => 'slug',
                'terms'    => $type,
            );
        }

        if( $module_type ){
            $tax_query[] = array(
                'taxonomy' => 'ocb_template_module_type',
                'field'    => 'slug',
                'terms'    => $module_type,
            );
        }

        if( $global ){
            $tax_query[] = array(
                'taxonomy' => 'ocb_template_global',
                'field'    => 'slug',
                'terms'    => $global,
            );
        }

        //For now let's not use the tax query below because we're going to filter through javascript
        //'tax_query' => $tax_query
        $return = get_posts( array(
            'post_type' => 'ocb_template',
            'numberposts' => 500
        ) );

        foreach( $return as $key => $template ){
            $return[$key]->{"css"}                  = get_post_meta( $template->ID, 'ocb_object_css', true );
            $return[$key]->{"object"}               = get_post_meta( $template->ID, 'ocb_tree_content', true );
            $return[$key]->{"templateGlobal"}       = wp_get_post_terms( $template->ID, 'ocb_template_global' )[0]->slug;
            $return[$key]->{"templateType"}         = wp_get_post_terms( $template->ID, 'ocb_template_type' )[0]->slug;
            $return[$key]->{"templateModuleType"}   = wp_get_post_terms( $template->ID, 'ocb_template_module_type' )[0]->slug;
            $return[$key]->{"ID"}                   = get_post_meta( $template->ID, 'ocb_id', true );
        }

        //$this->write_debug( $tax_query );

        if( $return ){

            return new WP_REST_Response( $return, 200 );

        }

        return new WP_Error( 'noTemplates', __( 'Could not find any templates', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function get_templates( $request ){
        //This ensures that connector items that don't have values return a default value
        global $ocb_connector_default;
        $ocb_connector_default = true;

        $json_payload = $request->get_json_params();

        $type           = isset( $json_payload['type'] )        ? sanitize_text_field( $json_payload['type'] )        : false;

        switch( $type ){
            case 'page':
                $result = $this->get_all_page_templates( $request );
                break;
            default:
                $result = $this->get_all_templates( $request );
        }

        return $result;
    }

    /**
     * Utility for generating a template cache key
     *
     * @param string $domain of the remote templates
     * @param string $type page/row
     * @param bool $chunk which chunk it is starting with 1
     * @param bool $number how much templates should be in this chunk
     * @param array $tax the tax query
     * @return string
     */
    public static function get_template_cache_key( $domain, $type = 'template', $tax = false, $chunk = false, $number = false ){
        $domain = untrailingslashit( esc_url( $domain ) );

        $key = $domain . '_templates_' . $type;

        if( $chunk !== false )
            $key .= '_' . $chunk;

        if( $number !== false )
            $key .= '_number' . $number;

        if( $tax !== false && is_array( $tax ) ){
            foreach( $tax as $item ){
                $key .= $item['taxonomy'] . $item['terms'];
            }
        }

        return 'temp_' . $domain . '_' . md5( $key );
    }

    /**
     * Get ocb_template posts with their meta values
     *
     * May want to use get_custom instead
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_all_templates( $request ){
        $json_payload = $request->get_json_params();

        $subset = isset( $json_payload['number'] ) ? true : false;
        $number = isset( $json_payload['number'] ) ? (int) $json_payload['number'] : 1000;
        $offset = isset( $json_payload['offset'] ) ? (int) $json_payload['offset'] : 0;
        $template_remote_1 = isset( $json_payload['template_remote_1'] ) ? esc_url( $json_payload['template_remote_1'] ): false;
        $template_remote_2 = isset( $json_payload['template_remote_2'] ) ? esc_url( $json_payload['template_remote_2'] ): false;
        $tax_query = isset( $json_payload['tax_query'] ) ? (array) $json_payload['tax_query'] : false;

        $cache = isset( $json_payload['cache'] ) ? $json_payload['cache'] : false;

        //If we're just retrieving cache, exit this and just return result of get_cache_template_chunk
        if( $cache ) {
            $results = $this->get_cache_template_chunk( $request, 'page', $tax_query );

            if( $results )
                return $results;
        }

        $query = array(
            'post_type' => 'ocb_template',
            'numberposts' => $number,
            'offset' => $offset
        );

        if( $tax_query )
            $query['tax_query'] = $tax_query;

        //Retrieve the template posts
        $return = get_posts( $query );

        foreach( $return as $index => $content ){
            $return[$index]->{ "taxonomies" } = new stdClass();
            foreach( array( 'ocb_template_global', 'ocb_template_type', 'ocb_template_folder', 'ocb_template_module_type', 'ocb_template_industry', 'ocb_template_theme' ) as $key ){
                $terms = wp_get_post_terms( $content->ID, $key );
                if( is_wp_error( $terms ) ){
                    $return[$index]->{ "taxonomies" }->{ $key } = '';
                } else {
                    $first_term = isset( $terms[0] ) ? $terms[0] : false;
                    if( $first_term )
                        $return[$index]->{ "taxonomies" }->{ $key } = $first_term->name;
                    else
                        $return[$index]->{ "taxonomies" }->{ $key } = '';
                }
            }

            $return[$index]->{ "meta" } = new stdClass();
            foreach ( array( 'ocb_object_css', 'ocb_tree_content', 'ocb_id', 'ocb_template_requirements' ) as $key ) {
                $return[$index]->{ "meta" }->{$key} = get_post_meta( $content->ID, $key, true );
            }

            $return[$index]->{"post_content_shortcode"} = Offsprout_Model::replace_strings( do_shortcode( $content->post_content ) );
        }

        if( $subset ){

            $full_return = $return;

        } else {

            $full_return = array(
                'internal' => $return,
                'template_remote_1' => array(),
                'template_remote_2' => array()
            );

            //Add in the cached templates from remote sites
            $template_remote_1 = $template_remote_1 ? $template_remote_1 : Offsprout_Model::get_site_settings( 'template_remote_1' )['text'];
            $template_remote_2 = $template_remote_2 ? $template_remote_2 : Offsprout_Model::get_site_settings( 'template_remote_2' )['text'];

            $template_remote_1_result = get_transient( self::get_template_cache_key( $template_remote_1, 'template', $tax_query ) );
            $template_remote_2_result = get_transient( self::get_template_cache_key( $template_remote_2, 'template', $tax_query ) );

            $full_return['template_remote_1'] = $template_remote_1_result ? $template_remote_1_result : array();
            $full_return['template_remote_2'] = $template_remote_2_result ? $template_remote_2_result : array();

        }

        if( ! empty( $return ) || ! empty( $template_remote_1_result ) || ! empty( $template_remote_2_result ) ){

            return new WP_REST_Response( $full_return, 200 );

        }

        return new WP_REST_Response( $full_return, 200 );
    }

    /**
     * Get ocb_template posts with their meta values
     *
     * May want to use get_custom instead
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_all_page_templates( $request ){
        $json_payload = $request->get_json_params();

        $subset = isset( $json_payload['number'] ) ? true : false;
        $number = isset( $json_payload['number'] ) ? (int) $json_payload['number'] : 1000;
        $offset = isset( $json_payload['offset'] ) ? (int) $json_payload['offset'] : 0;
        $template_remote_1 = isset( $json_payload['template_remote_1'] ) ? esc_url( $json_payload['template_remote_1'] ): false;
        $template_remote_2 = isset( $json_payload['template_remote_2'] ) ? esc_url( $json_payload['template_remote_2'] ): false;
        $tax_query = isset( $json_payload['tax_query'] ) ? (array) $json_payload['tax_query'] : false;

        $cache = isset( $json_payload['cache'] ) ? $json_payload['cache'] : false;

        //If we're just retrieving cache, exit this and just return result of get_cache_template_chunk
        if( $cache )
            return $this->get_cache_template_chunk( $request, 'page', $tax_query );

        $query = array(
            'post_type' => 'ocb_tree_template',
            'numberposts' => $number,
            'offset' => $offset
        );

        if( $tax_query )
            $query['tax_query'] = $tax_query;

        $return = get_posts( $query );

        foreach( $return as $index => $content ){

            $return[$index]->{ "taxonomies" } = new stdClass();
            foreach( array( 'ocb_template_folder', 'ocb_template_industry', 'ocb_template_theme' ) as $key ){
                $terms = wp_get_post_terms( $content->ID, $key );
                if( is_wp_error( $terms ) ){
                    $return[$index]->{ "taxonomies" }->{ $key } = '';
                } else {
                    $first_term = isset( $terms[0] ) ? $terms[0] : false;
                    if( $first_term )
                        $return[$index]->{ "taxonomies" }->{ $key } = $first_term->name;
                    else
                        $return[$index]->{ "taxonomies" }->{ $key } = '';
                }
            }

            $return[$index]->{ "meta" } = new stdClass();
            foreach ( array( 'ocb_page_css', 'ocb_tree_content', 'ocb_structure', 'ocb_id', 'ocb_template_post_type', '_wp_page_template', 'ocb_template_requirements' ) as $key ) {
                $return[$index]->{ "meta" }->{$key} = get_post_meta( $content->ID, $key, true );
            }

            $structure = get_post_meta( $content->ID, 'ocb_structure', true );

            if( Offsprout_Model::has_offsprout_theme() && $structure ) {
                $structure_id = Offsprout_Model::get_post_id_from_custom( $structure, 'ocb_structure' );
                $structure_post = get_post( $structure_id );
                $structure_content = $structure_post->post_content;
                if ( !isset( $return[$index]->ocb_page_css ) )
                    $return[$index]->ocb_page_css = '';

                $return[$index]->ocb_page_css .= get_post_meta( $structure_id, 'ocb_page_css', true );

                $return[$index]->{"post_content"} = do_shortcode( str_replace( '[ocb_content_module]', $content->post_content, $structure_content ) );
            } else {
                $return[$index]->ocb_page_css = get_post_meta( $content->ID, 'ocb_page_css', true );
            }

            $the_post_content = do_shortcode( $content->post_content );

            //replaces the skin tags with the classes from the skin
            $return[$index]->{"post_content_shortcode"} = Offsprout_Model::replace_strings( $the_post_content );

        }

        if( $subset ){

            $full_return = $return;

        } else {

            //Add in the cached templates from remote sites
            $full_return = array(
                'internal' => $return,
                'template_remote_1' => array(),
                'template_remote_2' => array()
            );

            //Add in the cached templates from remote sites
            $template_remote_1 = $template_remote_1 ? $template_remote_1 : Offsprout_Model::get_site_settings( 'template_remote_1' )['text'];
            $template_remote_2 = $template_remote_2 ? $template_remote_2 : Offsprout_Model::get_site_settings( 'template_remote_2' )['text'];

            $template_remote_1_result = get_transient( self::get_template_cache_key( $template_remote_1, 'page', $tax_query ) );
            $template_remote_2_result = get_transient( self::get_template_cache_key( $template_remote_2, 'page', $tax_query ) );

            $full_return['template_remote_1'] = $template_remote_1_result ? $template_remote_1_result : array();
            $full_return['template_remote_2'] = $template_remote_2_result ? $template_remote_2_result : array();

        }

        if( ! empty( $return ) || ! empty( $template_remote_1_result ) || ! empty( $template_remote_2_result ) ){

            return new WP_REST_Response( $full_return, 200 );

        }

        return new WP_REST_Response( $full_return, 200 );
    }

    /**
     * Retrieves templates from cache
     *
     * It may be possible that cache could expire for some chunks and not others and thus a mix of cache and fresh remote templates would be retrieved
     *
     * @param $request
     * @param string $type
     * @param $tax_query
     * @return WP_REST_Response
     */
    public function get_cache_template_chunk( $request, $type = 'page', $tax_query = false ){

        $json_payload = $request->get_json_params();

        $number = isset( $json_payload['number'] ) ? (int) $json_payload['number'] : false;
        $chunk = isset( $json_payload['chunk'] ) ? (int) $json_payload['chunk'] : 1;
        $location = isset( $json_payload['location'] ) ? (int) $json_payload['location'] : 1;
        $tax_query = isset( $json_payload['tax_query'] ) ? (array) $json_payload['tax_query'] : false;

        $template_remote = isset( $json_payload['template_remote_' . $location] ) ? esc_url( $json_payload['template_remote_' . $location] ) : Offsprout_Model::get_site_settings( 'template_remote_' . $location )['text'];

        $result = get_transient( self::get_template_cache_key( $template_remote, $type, $tax_query, $chunk, $number ) );

        if( $result == false ){
            return new WP_REST_Response( 'no cache', 200 );
        }

        return new WP_REST_Response( $result, 200 );

    }

    /**
     * Get a single template from its customId
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_template( $request ){
        global $ocb_connector_default;

        $ocb_connector_default = true;

        $id = $request->get_param( 'customId' );
        $id = $this->get_post_id_from_custom( $id );
        $return = false;

        if( $id ){

            $return                         = get_post( $id );
            $return->{"object"}             = get_post_meta( $id, 'ocb_tree_content', true );
            $return->{"css"}                = get_post_meta( $id, 'ocb_object_css', true );

            $global = wp_get_post_terms( $id, 'ocb_template_global' );
            if( isset( $global[0] ) && isset( $global[0]->slug ) )
                $global = $global[0]->slug;
            $return->{"templateGlobal"} = $global;

            $type = wp_get_post_terms( $id, 'ocb_template_type' );
            if( isset( $type[0] ) && isset( $type[0]->slug ) )
                $type = $type[0]->slug;
            $return->{"templateType"} = $type;
            
            $module_type = wp_get_post_terms( $id, 'ocb_template_module_type' );
            if( isset( $module_type[0] ) && isset( $module_type[0]->slug ) )
                $module_type = $module_type[0]->slug;
            $return->{"templateModuleType"} = $module_type;

        }

        if( $return ){

            return new WP_REST_Response( $return, 200 );

        }

        return new WP_Error( 'noTemplate', __( 'Could not find any templates with ID: ' . $id, 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Create an ocb_template type post
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function create_template( $request ){
        $json_payload = $request->get_json_params();

        $object         = isset( $json_payload['object'] )  ? $json_payload['object']   : false;
        $type           = isset( $object['type'] )          ? sanitize_text_field( $object['type'] )          : 'module';
        $module_type    = isset( $object['moduleType'] )    ? sanitize_text_field( $object['moduleType'] )     : 'text';
        $css            = isset( $json_payload['css'] )     ? $json_payload['css']      : '';
        $html           = isset( $json_payload['html'] )    ? $json_payload['html']     : '';
        $title          = isset( $json_payload['title'] )   ? sanitize_text_field( $json_payload['title']  )   : 'Template';

        $module_global  =
            isset( $json_payload['global'] )
            && $json_payload['global'] != 'no'
            && $json_payload['global'] != false
                ? 'yes' : 'no';

        /*$this->write_debug(array(
            'type' => $type,
            'module_type' => $module_type,
            'module_global' => $module_global,
            'title' => $title,
            'css' => $css,
            'html' => $html,
            'object' => $object
        ));*/

        $id = wp_insert_post(
            array(
                'post_type' => 'ocb_template',
                'post_title' => $title,
                'post_status' => 'publish',
                'post_content' => $html
            )
        );

        if( $type )
            wp_set_object_terms( $id, $type, 'ocb_template_type' );

        if( $module_type )
            wp_set_object_terms( $id, $module_type, 'ocb_template_module_type' );

        if( $module_global )
            wp_set_object_terms( $id, $module_global, 'ocb_template_global' );


        //$this->write_debug( $title . ' ' . ( $module_global ? 'Global' : 'Template' ) . ' saved with id: ' . $id . ' and type: ' . $type . ' and moduleType: ' . $module_type );

        if ( is_array( $json_payload ) ) {
            $custom_id = $this->generate_id( $id );
            update_post_meta( $id, 'ocb_id', $custom_id );
            update_post_meta( $id, 'ocb_tree_content', $object );
            update_post_meta( $id, 'ocb_object_css', $css );
            return new WP_REST_Response( $custom_id, 200 );
        }

        return new WP_Error( 'noCreateTemplate', __( 'Could not create the template', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function create_template_permissions_check(){
        return current_user_can('manage_options');
    }

    /**
     * SHOULD BE REPLACED BY OFFSPROUT_MODEL->get_post_id_from_custom
     *
     * @param $id
     * @param string $type
     * @return bool
     */
    private function get_post_id_from_custom( $id, $type = 'ocb_template' ){
        $templates = get_posts( array(
            'post_type' => $type,
            'meta_key'   => 'ocb_id',
            'meta_value' => $id,
        ) );

        $template = isset( $templates[0] ) ? $templates[0] : false;

        return $template ? (int) $template->ID : false;
    }

    /**
     * Update an ocb_template post type
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function update_template( $request ){
        $json_payload = $request->get_json_params();
        $id = $request->get_param( 'customId' );

        if( $id == 1 )
            return WP_Error( 'noUpdateTemplate', __( 'Could not update the template', 'offsprout' ), array( 'status' => 405 ) );

        $id = $this->get_post_id_from_custom( $id );

        $object   = isset( $json_payload['object'] )    ? $json_payload['object']   : false;
        $css      = isset( $json_payload['css'] )       ? $json_payload['css']      : '';
        $html     = isset( $json_payload['html'] )      ? $json_payload['html']     : '';
        $skins    = isset( $json_payload['skins'] )     ? $json_payload['skins']     : '';

        $success = wp_update_post(
            array(
                'ID' => $id,
                'post_content' => $html
            )
        );

        update_post_meta( $id, 'ocb_tree_content', $object );
        update_post_meta( $id, 'ocb_object_css', $css );
        update_post_meta( $id, 'ocb_skins_used', $skins );

        //$this->write_debug( $id );
        //$this->write_debug( $object );

        if( ! get_post_meta( $id, 'ocb_id', true ) ){
            update_post_meta( $id, 'ocb_id', $this->generate_id( $id ) );
        }

        if ( $success ) {
            return new WP_REST_Response( $id, 200 );
        }

        return new WP_Error( 'noUpdateTemplate', __( 'Could not update the template', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Delete an ocb_template post
     *
     * @param $request
     */
    public function delete_template( $request ){
        $id = $request->get_param( 'customId' );

        $id = $this->get_post_id_from_custom( $id );

        wp_delete_post( $id );

        /*$templates = get_posts( array( 'post_type' => 'ocb_template' ) );

        foreach( $templates as $template ){
            $this->write_debug( $template );
            wp_delete_post( $template->ID );
        }*/

    }

    /**
     * Update a ocb_structure post
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function update_structure( $request ){
        $json_payload = $request->get_json_params();
        $id = (int) $request->get_param( 'postId' );

        $content = isset( $json_payload['content'] ) ? $json_payload['content'] : false;

        //$this->write_debug( $json_payload );

        $success = wp_update_post(
            array(
                'ID' => $id,
                'post_content' => $content
            )
        );

        if( ! get_post_meta( $id, 'ocb_id', true ) ){
            update_post_meta( $id, 'ocb_id', $this->generate_id( $id ) );
        }

        if ( $success ) {
            return new WP_REST_Response( $id, 200 );
        }

        return new WP_Error( 'noUpdateStructure', __( 'Could not update the structure', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Get all of the ocb_structure posts
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_structures( $request ){
        //$json_payload = $request->get_json_params();
        //Make sure that connector shortcodes make sense
        global $ocb_connector_default;

        $ocb_connector_default = true;

        $return = get_posts( array(
            'post_type' => 'ocb_structure',
            'numberposts' => 500
        ) );

        global $ocb_global_css;
        global $ocb_structures_info;
        $ocb_structures_info = array(
            'is_structure' => true
        );

        foreach( $return as $key => $template ){
            $return[$key]->{"object"}       = get_post_meta( $template->ID, 'ocb_tree_content', true );
            $return[$key]->{"css"}          = get_post_meta( $template->ID, 'ocb_page_css', true );
            $return[$key]->{"post_content"} = do_shortcode( $template->post_content );
            $return[$key]->{"global_css"}   = $ocb_global_css;
            $return[$key]->{"unique_id"}    = get_post_meta( $template->ID, 'ocb_id', true );
        }

        if( $return ){

            return new WP_REST_Response( $return, 200 );

        }

        return new WP_Error( 'noStructures', __( 'Could not find any structures', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Given an $id, will create a new post/page/content from a saved ocb_template template
     *
     * @param $id
     * @param bool $title
     * @param $full_template - fallback for creating a page based on a remote template that was passed in
     * @return int|WP_Error
     */
    public function create_from_template( $id, $title = false, $full_template = false ){
        $template         = get_post( $id );

        if(
            ( isset( $full_template['remote_template'] ) && $full_template['remote_template'] )
            || ( ! $template && isset( $full_template['meta'] ) )
        ){
            $meta = $full_template['meta'];

            $tree               = isset( $meta['ocb_tree_content'] ) ? $meta['ocb_tree_content'] : false;
            $css                = isset( $meta['ocb_page_css'] ) ? $meta['ocb_page_css'] : false;
            $post_type          = isset( $meta['ocb_template_post_type'] ) ? $meta['ocb_template_post_type'] : false;
            $structure          = false;
            $wp_page_template   = isset( $meta['_wp_page_template'] ) ? $meta['_wp_page_template'] : false;
            $post_title         = $title ? $title : $full_template['post_title'];
            $post_content       = $full_template['post_content'];

        } else {
            $tree             = get_post_meta( $id, 'ocb_tree_content', true );
            $css              = get_post_meta( $id, 'ocb_page_css', true );
            $post_type        = get_post_meta( $id, 'ocb_template_post_type', true );
            $structure        = get_post_meta( $id, 'ocb_structure', true );
            $wp_page_template = get_post_meta( $id, '_wp_page_template', true );
            $post_title       = $title ? $title : $template->post_title;
            $post_content     = $template->post_content;
        }

        $new_post = wp_insert_post( array(
            'post_content' => $post_content,
            'post_title' => sanitize_text_field( $post_title ),
            'post_status' => 'draft',
            'post_type' => sanitize_text_field( $post_type )
        ) );

        update_post_meta( $new_post, 'ocb_tree_content', $tree );
        update_post_meta( $new_post, 'ocb_page_css', $css );

        if( $structure && $template )
            //Save the structure if there is one (not available if only the plugin is active)
            update_post_meta( $new_post, 'ocb_structure', sanitize_text_field( $structure ) );
        else {
            //If only the plugin is installed, there is no ocb_structure, so save the _wp_page_template
            update_post_meta( $new_post, '_wp_page_template', sanitize_text_field( $wp_page_template ) );
        }

        return $new_post;
    }

    public static function type_needs_unique_id(){
        return array( 'ocb_structure', 'ocb_template', 'ocb_tree_template' );
    }

    public function get_of_taxonomy( $request ){
        $taxonomy = $request->get_param( 'type' );

        if( $taxonomy ){
            $terms = get_terms( array(
                'hide_empty' => false,
                'taxonomy' => $taxonomy
            ) );

            return new WP_REST_Response( $terms, 200 );
        }

        return new WP_Error( 'noTaxonomy', __( 'Need to include a taxonomy', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Creates content of a custom post type
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function create_custom( $request ){
        $json_payload = $request->get_json_params();

        $post_type    = isset( $json_payload['post_type'] )     ? sanitize_text_field( $json_payload['post_type'] ) : 'page';
        $title        = isset( $json_payload['title'] )         ? sanitize_text_field( $json_payload['title'] )     : '[No Title]';
        $content      = isset( $json_payload['content'] )       ? $json_payload['content']                          : '';
        $status       = isset( $json_payload['status'] )        ? sanitize_text_field( $json_payload['status'] )    : 'draft';
        $meta         = isset( $json_payload['meta'] )          ? (array) $json_payload['meta']                     : array();
        $taxonomies   = isset( $json_payload['taxonomies'] )    ? (array) $json_payload['taxonomies']               : array();
        $from_template= isset( $json_payload['from_template'] ) ? (bool) $json_payload['from_template']             : false;
        $full_template= isset( $json_payload['full_template'] ) ? (array) $json_payload['full_template']            : false;
        $needs_id     = isset( $json_payload['needs_id'] )      ? (bool) $json_payload['needs_id']                  : false;
        $no_edit      = isset( $json_payload['no_edit'] )       ? (bool) $json_payload['no_edit']                  : false;

        //Create a new post/page/content from a template
        if( $from_template ){
            $template = isset( $json_payload['template'] ) ? $json_payload['template'] : false;
            $new_id = $this->create_from_template( $template, $title, $full_template );

            if( $new_id ){
                if( $status == 'publish' ){
                    wp_update_post( array(
                        'ID' => $new_id,
                        'post_status' => $status
                    ) );
                }

                return new WP_REST_Response( array(
                    'permalink' => $no_edit ? get_permalink( $new_id ) :  add_query_arg( 'pageEdit', 1, get_permalink( $new_id ) ),
                    'customId' => '',
                    'id' => $new_id,
                    'post_title' => $title
                ), 200 );
            }

            return new WP_Error( 'noCreate', __( 'Could not create', 'offsprout' ), array( 'status' => 405 ) );
        }

        //Create a new $post_type with $content
        $id = wp_insert_post(
            array(
                'post_type' => $post_type,
                'post_title' => $title,
                'post_status' => $status,
                'post_content' => $content
            )
        );

        //These post types need a unique ID so that those IDs can be referenced in templates rather than post IDs
        // This allows importing ocb_structure and ocb_template types while maintaining references, even though post IDs will be different
        $need_unique_ids = self::type_needs_unique_id();

        if( ! empty( $meta ) ){
            foreach( $meta as $key => $value ){
                //don't save the ocb_structure meta value for ocb_structure post type
                if( ! ( $post_type == 'ocb_structure' && $key == 'ocb_structure' ) )
                    update_post_meta( $id, $key, $value );
            }
        }

        if( ! empty( $taxonomies ) ){
            foreach( $taxonomies as $key => $value ){
                wp_set_object_terms( $id, $value, $key );
            }
        }

        $custom_id = $this->generate_id( $id );

        if( in_array( $post_type, $need_unique_ids ) || $needs_id )
            update_post_meta( $id, 'ocb_id', $custom_id );

        //If we're creating a structure using another structure as a starting point, we need to get the
        // tree for that structure and save that as the meta here.
        //We should also make sure that it's published
        if( $post_type == 'ocb_structure' && isset( $meta['ocb_structure'] ) ){
            $structure = $this->get_post_id_from_custom( $meta['ocb_structure'], $post_type );

            $structure_tree = get_post_meta( $structure, 'ocb_tree_content', true );
            $structure_css = get_post_meta( $structure, 'ocb_object_css', true );

            update_post_meta( $id, 'ocb_tree_content', $structure_tree );
            update_post_meta( $id, 'ocb_object_css', $structure_css );

            wp_update_post( array(
                'ID' => $id,
                'post_status' => 'publish'
            ) );
        }

        $the_permalink = $no_edit ? get_permalink( $id ) :  add_query_arg( 'pageEdit', 1, get_permalink( $id ) );

        if( $id )
            return new WP_REST_Response( array(
                'permalink' => $the_permalink,
                'customId' => $custom_id,
                'id' => $id,
                'post_title' => $title
            ), 200 );

        return new WP_Error( 'noCreate', __( 'Could not create', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Will pass to the appropriate method
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function do_custom( $request ){
        $action = $request->get_param( 'action' );

        switch( $action ){
            case 'get':
                return $this->get_custom( $request );
            case 'update':
                return $this->update_custom( $request );
            case 'delete':
                return $this->delete_custom( $request );
        }

        return new WP_Error( 'noCustomAction', __( 'You must set a valid custom action: get, update, or delete', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Get posts of a given custom post type
     *
     * Can use withMeta to return with given meta values
     * Can use withTaxonomies to return with given taxonomy values
     * Can use pluck to only return certain fields
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function get_custom( $request ){
        $json_payload = $request->get_json_params();

        $post_type        = isset( $json_payload['post_type'] )         ? $json_payload['post_type']            : 'page';
        $id               = isset( $json_payload['id'] )                ? (int) $json_payload['id']             : false;
        $status           = isset( $json_payload['status'] )            ? $json_payload['status']               : 'publish';
        $number           = isset( $json_payload['number'] )            ? (int) $json_payload['number']         : 500;
        $pluck            = isset( $json_payload['pluck'] )             ? (array) $json_payload['pluck']        : false;
        $tax_query        = isset( $json_payload['tax_query'] )         ? (array) $json_payload['tax_query']    : false;

        //$page should be 0 index so that 0 is the first set of posts, 1 is the second set of posts, etc.
        $page             = isset( $json_payload['page'] )              ? (int) $json_payload['page']           : 0;
        $leave_shortcodes = isset( $json_payload['leave_shortcodes'] )  ? (bool) $json_payload['leave_shortcodes'] : false;
        $leave_urls       = isset( $json_payload['leave_urls'] )        ? (bool) $json_payload['leave_urls']    : false;
        $meta_query       = isset( $json_payload['meta_query'] )        ? (array) $json_payload['meta_query']    : false;

        //Tells the API to retrieve these values - not used as part of the query
        $with_meta        = isset( $json_payload['withMeta'] )          ? (array) $json_payload['withMeta']      : false;
        $with_taxonomies  = isset( $json_payload['withTaxonomies'] )    ? (array) $json_payload['withTaxonomies'] : false;
        $with_permalinks  = isset( $json_payload['withPermalinks'] )    ? (bool) $json_payload['withPermalinks'] : false;

        $offset = $page * $number;

        //Make sure that connector shortcodes make sense
        if( $post_type == 'ocb_template' || $post_type == 'ocb_tree_template' || $post_type == 'ocb_structure' ){
            global $ocb_connector_default;

            $ocb_connector_default = true;
        }

        //Get single if an ID was passed
        if( $id ){

            global $wp_embed;

            $return = get_post( $id );

            if( $with_taxonomies ){
                $return->{ "taxonomies" } = new stdClass();
                foreach( $with_taxonomies as $key  ){
                    $terms = wp_get_post_terms( $id, $key );
                    $first_term = isset( $terms[0] ) ? $terms[0] : false;
                    if( $first_term )
                        $return->{ "taxonomies" }->{ $key } = $first_term->slug;
                }
            }

            if( $with_meta ){
                foreach( $with_meta as $key ){
                    $return->{ $key } = get_post_meta( $id, $key, true );
                }
            }

            if( $post_type == 'ocb_tree_template' ){
                $structure = get_post_meta( $id, 'ocb_structure', true );

                if( Offsprout_Model::has_offsprout_theme() && $structure ) {
                    $structure_id = Offsprout_Model::get_post_id_from_custom( $structure );
                    $structure_post = get_post( $structure_id );
                    $structure_content = $structure_post->post_content;
                    $return->{"post_content"} = str_replace( '[ocb_content_module]', $return->post_content, $structure_content );
                }
            }

            $content = $return->post_content;

            /*
             * Render embeds, but leave shortcodes
             */
            if( ! $leave_urls ) {

                //find URLs on their own line
                //uses https://gist.github.com/joshhartman/5380593
                preg_match_all( '|^\s*(https?://[^\s"]+)\s*$|im', $return->post_content, $embeds );

                //replace them with embed code
                foreach ( $embeds[1] as $embed ) {
                    $successful_oembed = wp_oembed_get( $embed );
                    if ( $successful_oembed )
                        $content = str_replace( $embed, $successful_oembed, $content );
                }

            }

            if( $with_permalinks )
                $return->{ "ocb_permalink" } = get_permalink( $id );

            //optionally leave shortcodes
            if( $leave_shortcodes )
                remove_filter( 'the_content', 'do_shortcode', 11 );

            $return->{ "post_content_rendered" } = apply_filters( 'the_content', $content );

        } else {

            $post_array = array(
                'post_type' => $post_type,
                'post_status' => $status,
                'posts_per_page' => $number,
                'offset' => $offset
            );

            if( $meta_query )
                $post_array['meta_query'] = $meta_query;

            if( $tax_query )
                $post_array['tax_query'] = $tax_query;

            $return = get_posts( $post_array );

            if( ( $with_meta || $with_taxonomies || $with_permalinks ) && ! empty( $return ) ){
                foreach( $return as $index => $content ){
                    if( $with_taxonomies ){
                        $return[$index]->{ "taxonomies" } = new stdClass();
                        foreach( $with_taxonomies as $key ){
                            $terms = wp_get_post_terms( $content->ID, $key );
                            if( is_wp_error( $terms ) ){
                                $return[$index]->{ "taxonomies" }->{ $key } = '';
                            } else {
                                $first_term = isset( $terms[0] ) ? $terms[0] : false;
                                if( $first_term )
                                    $return[$index]->{ "taxonomies" }->{ $key } = $first_term->name;
                                else
                                    $return[$index]->{ "taxonomies" }->{ $key } = '';
                            }
                        }
                    }

                    if( $with_meta ) {
                        $return[$index]->{ "meta" } = new stdClass();
                        foreach ( $with_meta as $key ) {
                            $return[$index]->{ "meta" }->{$key} = get_post_meta( $content->ID, $key, true );
                        }
                    }

                    if( $with_permalinks )
                        $return[$index]->{ "ocb_permalink" } = get_permalink( $content->ID );

                    if( $post_type == 'ocb_tree_template' ){
                        $structure = get_post_meta( $content->ID, 'ocb_structure', true );

                        if( Offsprout_Model::has_offsprout_theme() && $structure ) {
                            $structure_id = Offsprout_Model::get_post_id_from_custom( $structure, 'ocb_structure' );
                            $structure_post = get_post( $structure_id );
                            $structure_content = $structure_post->post_content;
                            if ( !isset( $return[$index]->ocb_page_css ) )
                                $return[$index]->ocb_page_css = '';

                            $return[$index]->ocb_page_css .= get_post_meta( $structure_id, 'ocb_page_css', true );

                            $return[$index]->{"post_content"} = do_shortcode( str_replace( '[ocb_content_module]', $content->post_content, $structure_content ) );
                        } else {
                            $return[$index]->ocb_page_css = get_post_meta( $content->ID, 'ocb_page_css', true );
                        }
                    }

                }
            }

            foreach( $return as $index => $content ){
                $return[$index]->{"post_content_shortcode"} = do_shortcode( $content->post_content );
            }

            //$this->write_debug( $return );

        }

        if( $return ){

            //Allow the plucking of specific properties in the return object so that we're returning smaller objects if we want
            if( $pluck && ! $id ){

                $new_array = array();

                if( is_array( $pluck ) ){

                    foreach( $return as $index => $post ) {

                        $new_array[$index] = array();

                        foreach ( $pluck as $prop ) {

                            $new_array[$index][$prop] = $return[$index]->{ $prop };

                        }

                    }

                } else {
                    $new_array = wp_list_pluck( $return, $pluck );
                }

                $return = $new_array;

            }

            return new WP_REST_Response( $return, 200 );

        }

        return new WP_Error( 'noContent', __( 'Could not find any content', 'offsprout' ), array( 'status' => 405 ) );
    }

    /**
     * Update a post with a custom post type
     *
     * Can include a meta object with key value pairs to update meta values for this custom post
     * Can include a taxonomy object with key value pairs to update taxonomy values for this custom post
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function update_custom( $request ){
        $json_payload = $request->get_json_params();

        $id           = isset( $json_payload['id'] )            ? (int) $json_payload['id']             : false;
        $meta         = isset( $json_payload['meta'] )          ? (array) $json_payload['meta']         : false;
        $taxonomies   = isset( $json_payload['taxonomies'] )    ? (array) $json_payload['taxonomies']   : false;
        $post         = isset( $json_payload['post'] )          ? (array) $json_payload['post']         : false;

        if( ! $id )
            return new WP_Error( 'noId', __( 'No post id specified', 'offsprout' ), array( 'status' => 405 ) );

        if( $post ) {

            $post['ID'] = $id;

            //This filter can remove things like inputs and data attributes which affects forms, etc.
            remove_filter('content_save_pre', 'wp_filter_post_kses');

            //We may want to still use kses but allow more tags/attributes in which case we can use this filter
            //add_filter( 'wp_kses_allowed_html', array( $this, 'update_allow_html' ), 10, 2 );

            wp_update_post( $post );

        }

        if( $meta ){
            foreach( $meta as $key => $value ){
                update_post_meta( $id, $key, $value );
            }
        }

        if( $taxonomies ){
            foreach( $taxonomies as $key => $value ){
                wp_set_object_terms( $id, $value, $key );
            }
        }

        $the_post = get_post( $id );

        if( in_array( $the_post->post_type, self::type_needs_unique_id() ) ) {
            $the_post->{"customId"} = get_post_meta( $id, 'ocb_id', true );
        }

        return new WP_REST_Response( $the_post, 200 );
    }

    /**
     * WP KSES was stripping input tags when saving things like forms.
     *
     * @param $allowedtags
     * @param $context
     */
    public function update_allow_html( $allowedtags, $context ){

        $allowed_atts = array(
            'action'     => array(),
            'align'      => array(),
            'alt'        => array(),
            'class'      => array(),
            'data'       => array(),
            'data-*'       => array(),
            'dir'        => array(),
            'for'        => array(),
            'height'     => array(),
            'href'       => array(),
            'id'         => array(),
            'lang'       => array(),
            'method'     => array(),
            'name'       => array(),
            'novalidate' => array(),
            'placeholder'=> array(),
            'rel'        => array(),
            'rev'        => array(),
            'src'        => array(),
            'style'      => array(),
            'tabindex'   => array(),
            'target'     => array(),
            'title'      => array(),
            'type'       => array(),
            'value'      => array(),
            'width'      => array(),
            'xml:lang'   => array(),
        );
        $allowedtags['form']     = $allowed_atts;
        $allowedtags['label']    = $allowed_atts;
        $allowedtags['input']    = $allowed_atts;
        $allowedtags['textarea'] = $allowed_atts;
        $allowedtags['iframe']   = $allowed_atts;
        $allowedtags['script']   = $allowed_atts;
        $allowedtags['style']    = $allowed_atts;
        $allowedtags['strong']   = $allowed_atts;
        $allowedtags['small']    = $allowed_atts;
        $allowedtags['table']    = $allowed_atts;
        $allowedtags['span']     = $allowed_atts;
        $allowedtags['abbr']     = $allowed_atts;
        $allowedtags['code']     = $allowed_atts;
        $allowedtags['pre']      = $allowed_atts;
        $allowedtags['div']      = $allowed_atts;
        $allowedtags['img']      = $allowed_atts;
        $allowedtags['h1']       = $allowed_atts;
        $allowedtags['h2']       = $allowed_atts;
        $allowedtags['h3']       = $allowed_atts;
        $allowedtags['h4']       = $allowed_atts;
        $allowedtags['h5']       = $allowed_atts;
        $allowedtags['h6']       = $allowed_atts;
        $allowedtags['ol']       = $allowed_atts;
        $allowedtags['ul']       = $allowed_atts;
        $allowedtags['li']       = $allowed_atts;
        $allowedtags['em']       = $allowed_atts;
        $allowedtags['hr']       = $allowed_atts;
        $allowedtags['br']       = $allowed_atts;
        $allowedtags['tr']       = $allowed_atts;
        $allowedtags['td']       = $allowed_atts;
        $allowedtags['p']        = $allowed_atts;
        $allowedtags['a']        = $allowed_atts;
        $allowedtags['b']        = $allowed_atts;
        $allowedtags['i']        = $allowed_atts;

        return $allowedtags;

    }

    /**
     * Delete a post with a custom post type
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function delete_custom( $request ){
        $json_payload = $request->get_json_params();

        $post_id = isset( $json_payload['post_id'] ) ? (int) $json_payload['post_id'] : false;
        $custom_id = isset( $json_payload['ocb_id'] ) ? (string) $json_payload['ocb_id'] : false;
        $post_type = isset( $json_payload['post_type'] ) ? (string) $json_payload['post_type'] : false;

        if( $post_id ){
            wp_delete_post( $post_id );

            return new WP_REST_Response( true, 200 );
        }

        if( $custom_id ){
            $post_id = Offsprout_Model::get_post_id_from_custom( $custom_id, $post_type );
            wp_delete_post( $post_id );

            return new WP_REST_Response( true, 200 );
        }

        return new WP_Error( 'noContent', __( 'Could not find any content', 'offsprout' ), array( 'status' => 405 ) );
    }

    private function generate_id( $id ){
        //Add the 'a' so that no unique ids are numerical like post IDs
        return 'a' . uniqid( $id );
    }

    private function write_debug( $print, $overwrite = false ){
        $file = 'debug.txt';
        $current = file_get_contents($file);
        ob_start();
        print_r( $print );
        if( $overwrite ){
            $current = ob_get_clean();
            $current .= "\n";
        } else {
            $current .= ob_get_clean();
            $current .= "\n";
        }
        file_put_contents($file, $current);
    }

    /**
     * Will pass to the appropriate method
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function do_custom_user( $request ){
        $action = $request->get_param( 'action' );

        switch( $action ){
            case 'get':
                return $this->get_custom_user( $request );
            case 'update':
                return $this->update_custom_user( $request );
            case 'delete':
                return $this->delete_custom_user( $request );
        }

        return new WP_Error( 'noCustomAction', __( 'You must set a valid custom action: get, update, or delete', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function get_custom_user( $request ){
        $json_payload = $request->get_json_params();

        $id               = isset( $json_payload['id'] )                ? (int) $json_payload['id']             : false;
        $number           = isset( $json_payload['number'] )            ? (int) $json_payload['number']         : 500;
        $pluck            = isset( $json_payload['pluck'] )             ? (array) $json_payload['pluck']        : false;

        //Tells the API to retrieve these values - not used as part of the query
        $with_meta        = isset( $json_payload['withMeta'] )          ? (array) $json_payload['withMeta']      : false;

        //Get single user if an ID was passed
        if( $id ){

            $return = get_user_by( 'id', $id );

            if( $with_meta ){
                foreach( $with_meta as $key ){
                    $return->{ "meta" }->{ $key } = get_user_meta( $id, $key, true );
                }
            }

        } else {

            $return = get_users( array(
                'number' => $number
            ) );

            if( ( $with_meta ) && ! empty( $return ) ){
                foreach( $return as $index => $user ){

                    if( $with_meta ) {
                        $return[$index]->{ "meta" } = new stdClass();
                        foreach ( $with_meta as $key ) {
                            $return[$index]->{ "meta" }->{$key} = get_user_meta( $user->ID, $key, true );
                        }
                    }

                }
            }

            //$this->write_debug( $return );

        }

        if( $return ){

            //Allow the plucking of specific properties in the return object so that we're returning smaller objects if we want
            if( $pluck && ! $id ){

                $new_array = array();

                if( is_array( $pluck ) ){

                    foreach( $return as $index => $post ) {

                        $new_array[$index] = array();

                        foreach ( $pluck as $prop ) {

                            $new_array[$index][$prop] = $return[$index]->{ $prop };

                        }

                    }

                } else {
                    $new_array = wp_list_pluck( $return, $pluck );
                }

                $return = $new_array;

            }

            return new WP_REST_Response( $return, 200 );

        }

        return new WP_Error( 'noContent', __( 'Could not find any users', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function update_custom_user( $request ){
        $json_payload = $request->get_json_params();

        $id           = isset( $json_payload['id'] )            ? (int) $json_payload['id']             : false;
        $meta         = isset( $json_payload['meta'] )          ? (array) $json_payload['meta']         : false;
        $user         = isset( $json_payload['user'] )          ? (array) $json_payload['user']         : false;

        if( ! $id )
            return new WP_Error( 'noId', __( 'No user id specified', 'offsprout' ), array( 'status' => 405 ) );

        if( $user ) {

            wp_update_user( $user );

        }

        if( $meta ){
            foreach( $meta as $key => $value ){
                update_user_meta( $id, $key, $value );
            }
        }

        return new WP_REST_Response( true, 200 );
    }

    public function delete_custom_user( $request ){

    }

    public function get_query( $request ){
        $json_payload = $request->get_json_params();

        $query            = isset( $json_payload['query'] )             ? (array) $json_payload['query']            : false;
        $with_meta        = isset( $json_payload['withMeta'] )          ? (array) $json_payload['withMeta']         : false;
        $with_taxonomies  = isset( $json_payload['withTaxonomies'] )    ? (array) $json_payload['withTaxonomies']   : false;

        $posts = Offsprout_Post_Data::get_query( $query, $with_meta, $with_taxonomies );

        return new WP_REST_Response( $posts, 200 );
    }

    /**
     * Directs to various utility functions
     *
     * @param $request
     * @return WP_Error|WP_REST_Response
     */
    public function do_utility( $request ){
        $action = $request->get_param( 'action' );

        switch( $action ){
            case 'deleteAllPosts':
                return $this->utility_delete_all_posts( $request );
            case 'deleteAllPages':
                return $this->utility_delete_all_pages( $request );
            case 'deleteAllContent':
                return $this->utility_delete_all_content( $request );
            case 'clearCache':
                return $this->utility_clear_all_cache( $request );
            case 'maybeSavePermalinks':
                return $this->utility_maybe_save_permalinks( $request );
            case 'launchDomain':
                return $this->utility_launch_domain( $request );
            case 'sslChangeLinks':
                return $this->utility_ssl_change_links( $request );
            case 'cacheRemoteTemplates':
                return $this->utility_cache_remote_templates( $request );
            case 'deleteRemoteTemplateCache':
                return $this->delete_remote_template_cache( $request );
            case 'forceDeleteRemoteTemplateCache':
                return $this->force_delete_remote_template_cache( $request );
            case 'getPageHierarchyMenu':
                return $this->get_page_hierarchy_menu( $request );
            case 'getWPMenu':
                return $this->get_wordpress_menu( $request );
            case 'getAllWPNavMenus':
                return $this->get_all_wp_nav_menus( $request );
            case 'getWidgetTypes':
                return $this->get_widget_types( $request );
            case 'getWidgetForm':
                return $this->get_widget_form( $request );
            case 'getCleanedWidgetValues':
                return $this->get_cleaned_widget_values( $request );
            case 'getPostTypes':
                return $this->get_post_types( $request );
            case 'duplicatePost':
                return $this->duplicate_post( $request );
            case 'getTemplateTaxonomy':
                return $this->get_template_taxonomy( $request );
        }

        return new WP_Error( 'noCustomAction', __( 'You must set a valid custom action: get, update, or delete', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function utility_delete_all_posts( $request ){
        $json_payload = $request->get_json_params();

        $number = Offsprout_Model::bulk_delete( array(
            'post_type' => 'post'
        ) );

        return new WP_REST_Response( $number . __( ' posts deleted' ), 200 );
    }

    public function utility_delete_all_pages( $request ){
        $json_payload = $request->get_json_params();

        $number = Offsprout_Model::bulk_delete( array(
            'post_type' => 'page'
        ) );

        return new WP_REST_Response( $number . __( ' pages deleted' ), 200 );
    }

    public function utility_delete_all_content( $request ){
        $json_payload = $request->get_json_params();

        $number = Offsprout_Model::bulk_delete( array(
            'post_type' => array( 'post', 'page' )
        ) );

        return new WP_REST_Response( $number . __( ' pages and posts deleted' ), 200 );
    }

    public function utility_maybe_save_permalinks( $request ){
        //Need pretty permalinks for templates to work
        if( ! get_option( 'permalink_structure' ) ){
            update_option( 'permalink_structure', '/%postname%/' );
        }
        flush_rewrite_rules();
    }

    public function utility_clear_all_cache( $request ){
        Offsprout_Model::clear_cache();
        return new WP_REST_Response( __( 'cache has been deleted' ), 200 );
    }

    public function utility_launch_domain( $request ){
        $json_payload = $request->get_json_params();

        $domain = isset( $json_payload['domain'] ) ? esc_url( $json_payload['domain'] ) : false;

        $result = Offsprout_Model::launch_domain( $domain );

        return new WP_REST_Response( $result['message'], 200 );
    }

    public function utility_ssl_change_links( $request ){
        $json_payload = $request->get_json_params();

        $ssl = isset( $json_payload['ssl'] ) ? (int) $json_payload['ssl'] : false;

        $result = Offsprout_Model::ssl_link_replace( $ssl );

        return new WP_REST_Response( $result['message'], 200 );
    }

    public function utility_cache_remote_templates( $request ){
        $json_payload = $request->get_json_params();

        $templates = isset( $json_payload['templates'] ) ? $json_payload['templates'] : false;
        $url = isset( $json_payload['url'] ) ? esc_url( $json_payload['url'] ) : false;
        $type = isset( $json_payload['type'] ) && $json_payload['type'] ? $json_payload['type'] : 'object';
        $chunk = isset( $json_payload['chunk'] ) ? (int) $json_payload['chunk'] : false;
        $number = isset( $json_payload['number'] ) ? (int) $json_payload['number'] : false;
        $tax_query = isset( $json_payload['tax_query'] ) ? (array) $json_payload['tax_query'] : false;

        $template_remote_1 = Offsprout_Model::get_site_settings( 'template_remote_1' );
        $template_remote_2 = Offsprout_Model::get_site_settings( 'template_remote_2' );

        $cache_time = 24 * 60 * 60;

        if( $url == $template_remote_1['text'] )
            $cache_time = $template_remote_1['cache'];
        elseif( $url == $template_remote_2['text'] )
            $cache_time = $template_remote_2['cache'];

        if( strpos( $url, 'templates.offsprout.com' ) !== false || strpos( $url, 't.offsprout.com' ) !== false )
            $cache_time = 3 * 24 * 60 * 60;

        set_transient( self::get_template_cache_key( $url, $type, $tax_query, $chunk, $number ), $templates, $cache_time );

        return new WP_REST_Response( 'Cached templates from ' . $url . ' for ' . $cache_time . ' seconds.', 200 );
    }

    public function delete_remote_template_cache( $request ){
        $json_payload = $request->get_json_params();

        $url = isset( $json_payload['url'] ) ? esc_url( $json_payload['url'] ) : false;
        $tax_query = isset( $json_payload['tax_query'] ) ? (array) $json_payload['tax_query'] : false;

        if( strpos( $url, 'templates.offsprout.com' ) !== false || strpos( $url, 't.offsprout.com' ) !== false )
            return new WP_REST_Response( 'Cannot delete offsprout template cache.', 200 );

        Offsprout_Model::clear_template_cache( $url );

        return new WP_REST_Response( 'Deleted cached templates.', 200 );
    }

    public function force_delete_remote_template_cache( $request ){
        Offsprout_Model::clear_template_cache( 1 );
        Offsprout_Model::clear_template_cache( 2 );

        return new WP_REST_Response( 'Deleted cached templates.', 200 );
    }

    /**
     * used by the navigation module to return a page hierarchy menu
     *
     * @param $request
     * @return WP_REST_Response
     */
    public function get_page_hierarchy_menu( $request ){
        //$json_payload = $request->get_json_params();

        $menu = Offsprout_Model::get_page_hierarchy_menu_array();

        return new WP_REST_Response( $menu, 200 );
    }

    /**
     * used by the navigation module to return a wordpress menu
     *
     * @param $request
     * @return WP_REST_Response
     */
    public function get_wordpress_menu( $request ){
        //$json_payload = $request->get_json_params();

        $menu = Offsprout_Model::get_wordpress_menu_array( $request->get_json_params() );

        return new WP_REST_Response( $menu, 200 );
    }

    public function get_all_wp_nav_menus( $request ){

        $menus = wp_get_nav_menus( $args = array() );

        return new WP_REST_Response( $menus, 200 );
    }

    public function get_widget_types( $request ){

        if( isset( $GLOBALS['wp_widget_factory'] ) )
            return new WP_REST_Response( $GLOBALS['wp_widget_factory']->widgets, 200 );

        return new WP_Error( 'noWidgetsGlobal', __( 'Could not find the widgets global', 'offsprout' ), array( 'status' => 405 ) );

    }

    public function get_widget_form( $request ){
        $json_payload = $request->get_json_params();

        $widget = isset( $json_payload['widget'] ) ? esc_attr( $json_payload['widget'] ) : false;
        $widget_values = isset( $json_payload['widgetValues'] ) && $json_payload['widgetValues'] ? (array) $json_payload['widgetValues'] : array();

        if( $widget && isset( $GLOBALS['wp_widget_factory'] ) ){

            //Make sure that admin calls like is_plugin_active() work
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            //Run the update function to sanitize the widget values
            if( ! empty( $widget_values ) && method_exists( $GLOBALS['wp_widget_factory']->widgets[$widget], 'update' ) ){
                $widget_values = $GLOBALS['wp_widget_factory']->widgets[$widget]->update( $widget_values, array() );
            }

            ob_start();
            $GLOBALS['wp_widget_factory']->widgets[$widget]->form( $widget_values );
            $form = ob_get_clean();

            return new WP_REST_Response( $form, 200 );
        }

        return new WP_Error( 'noWidgetFound', __( 'Could not find that widget type', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function get_cleaned_widget_values( $request ){
        $json_payload = $request->get_json_params();

        $widget = isset( $json_payload['widget'] ) ? esc_attr( $json_payload['widget'] ) : false;
        $widget_values = isset( $json_payload['widgetValues'] ) && $json_payload['widgetValues'] ? (array) $json_payload['widgetValues'] : array();

        if( $widget && isset( $GLOBALS['wp_widget_factory'] ) ){

            //Make sure that admin calls like is_plugin_active() work
            include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

            //Run the update function to sanitize the widget values
            if( ! empty( $widget_values ) && method_exists( $GLOBALS['wp_widget_factory']->widgets[$widget], 'update' ) ){
                $widget_values = $GLOBALS['wp_widget_factory']->widgets[$widget]->update( $widget_values, array() );
            }

            return new WP_REST_Response( $widget_values, 200 );
        }

        return new WP_Error( 'noWidgetFound', __( 'Could not find that widget type', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function get_post_types( $request ){
        $json_payload = $request->get_json_params();
        unset( $json_payload['action' ] );

        $post_types = get_post_types( $json_payload, 'objects' );

        if( ! empty( $post_types ) ){

            return new WP_REST_Response( $post_types, 200 );
        }

        return new WP_Error( 'noPostTypesFound', __( 'Could not find any post types', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function duplicate_post( $request ){

        $json_payload = $request->get_json_params();

        $post_id = isset( $json_payload['post_id'] ) ? (int) $json_payload['post_id'] : false;
        $title = isset( $json_payload['post_title'] ) ? $json_payload['post_title'] : false;

        if ( ! $post_id )
            return new WP_Error( 'couldNotDuplicate', __( 'Must include a post_id to duplicate', 'offsprout' ), array( 'status' => 405 ) );

        $post = get_post( $post_id );

        if ( $post->post_type == 'revision' )
            return new WP_Error( 'couldNotDuplicate', __( 'Cannot duplicate a revision', 'offsprout' ), array( 'status' => 405 ) );

        $status = 'draft';

        $new_post = array(
            'menu_order' => $post->menu_order,
            'comment_status' => $post->comment_status,
            'ping_status' => $post->ping_status,
            'post_author' => get_current_user_id(),
            'post_content' => $post->post_content,
            'post_excerpt' => $post->post_excerpt,
            'post_mime_type' => $post->post_mime_type,
            'post_parent' => $new_post_parent = empty($parent_id)? $post->post_parent : $parent_id,
            'post_password' => $post->post_password,
            'post_status' => $new_post_status = (empty($status))? $post->post_status: $status,
            'post_title' => $title,
            'post_type' => $post->post_type,
        );

        //Some hook was
        //ob_start();

        $new_post_id = wp_insert_post($new_post);

        //$junk = ob_get_clean();

        if ( ! $new_post_id )
            return new WP_Error( 'couldNotDuplicate', __( 'Could not duplicate the content', 'offsprout' ), array( 'status' => 405 ) );;

        delete_post_meta( $new_post_id, '_dp_original' );
        add_post_meta( $new_post_id, '_dp_original', $post->ID );

        $post_meta_keys = get_post_custom_keys( $post->ID );
        $meta_blacklist = explode( ",", get_option( 'duplicate_post_blacklist' ) );

        if ($meta_blacklist == "")
            $meta_blacklist = array();

        $meta_keys = array_diff( $post_meta_keys, $meta_blacklist );

        foreach ( $meta_keys as $meta_key ) {

            $meta_values = get_post_custom_values( $meta_key, $post->ID );

            foreach ( $meta_values as $meta_value ) {
                $meta_value = maybe_unserialize( $meta_value );
                add_post_meta( $new_post_id, $meta_key, $meta_value );
            }

        }

        global $wpdb;
        if ( isset( $wpdb->terms ) ) {
            // Clear default category (added by wp_insert_post)
            wp_set_object_terms( $new_post_id, NULL, 'category' );

            $post_taxonomies = get_object_taxonomies( $post->post_type );
            $taxonomies_blacklist = array();

            $taxonomies = array_diff( $post_taxonomies, $taxonomies_blacklist );

            foreach ( $taxonomies as $taxonomy ) {

                $post_terms = wp_get_object_terms( $post->ID, $taxonomy, array( 'orderby' => 'term_order' ) );
                $terms = array();

                for ( $i=0; $i < count( $post_terms ); $i++ ) {
                    $terms[] = $post_terms[$i]->slug;
                }

                wp_set_object_terms( $new_post_id, $terms, $taxonomy );

            }

        }

        if( $new_post_id ){
            return new WP_REST_Response( array( 'permalink' => get_permalink( $new_post_id ) ), array( 'status' => 200 ) );
        }

        return new WP_Error( 'couldNotDuplicate', __( 'Could not duplicate the content', 'offsprout' ), array( 'status' => 405 ) );
    }

    public function get_template_taxonomy( $request ){
        $template_remote_1 = Offsprout_Model::get_site_settings( 'template_remote_1' );
        $template_remote_2 = Offsprout_Model::get_site_settings( 'template_remote_2' );
    }

}

function offsprout_register_api_extensions() {
    $controller = new Offsprout_API_Extensions();
    $controller->register_routes();
}

add_action( 'rest_api_init', 'offsprout_register_api_extensions' );