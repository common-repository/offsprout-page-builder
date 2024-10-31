<?php

/**
 * Handles logic for page data post properties.
 *
 * @since 1.0
 */
final class Offsprout_Post_Data {

    static public function post_title(){
        $title = get_the_title();

        if( is_archive() ){
            $title = get_the_archive_title();
        } elseif( is_search() ){
            $title = __( 'Search results for: "', 'offsprout' ) . get_search_query() . '"';
        }

        return $title;
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function post_excerpt( $settings ) {
        add_filter( 'excerpt_length', __CLASS__ . '::excerpt_length_filter' );
        add_filter( 'excerpt_more', __CLASS__ . '::excerpt_more_filter' );

        $excerpt = apply_filters( 'the_excerpt', get_the_excerpt() );

        remove_filter( 'excerpt_length', __CLASS__ . '::excerpt_length_filter' );
        remove_filter( 'excerpt_more', __CLASS__ . '::excerpt_more_filter' );

        if( ! $excerpt )
            $excerpt = 'There is no post excerpt for this post';

        return $excerpt;
    }

    /**
     * @since 1.0
     * @param string $length
     * @return string
     */
    static public function excerpt_length_filter( $length ) {
        return $length;
    }

    /**
     * @since 1.0
     * @param string $more
     * @return string
     */
    static public function excerpt_more_filter( $more ) {
        return $more;
    }

    /**
     * @since 1.0
     * @return string
     */
    static public function post_content() {
        $content = apply_filters( 'the_content', get_the_content() );

        return $content;
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function post_link( $settings ) {
        $href = get_permalink();

        if ( 'title' == $settings->text ) {
            $title = the_title_attribute( array(
                'echo' => false,
            ) );
            $text  = get_the_title();
        } elseif( 'link' == $settings->text ) {
            $title = the_title_attribute( array(
                'echo' => false,
            ) );
            $text = $href;
        } else {
            $title = esc_attr( $settings->custom_text );
            $text  = $settings->custom_text;
        }

        return "<a href='{$href}' title='{$title}'>{$text}</a>";
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function post_date( $settings ) {
        return get_the_date( $settings->format );
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function post_modified_date( $settings ) {
        return get_the_modified_date( $settings->format );
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function post_featured_image( $settings ) {
        global $post;

        if ( 'image' == $settings->display ) {

            $class = 'default' == $settings->align ? '' : 'align' . $settings->align;
            $image = get_the_post_thumbnail( $post, $settings->size, array(
                'itemprop' => 'image',
                'class' => $class,
            ) );

            if ( $image && 'yes' == $settings->linked ) {

                $href  = get_the_permalink();
                $title = the_title_attribute( array(
                    'echo' => false,
                ) );

                return "<a href='{$href}' title='{$title}'>{$image}</a>";
            } else {
                return $image;
            }
        } elseif ( 'url' == $settings->display ) {
            return get_the_post_thumbnail_url( $post, $settings->size );
        } elseif ( 'alt' == $settings->display ) {
            return get_post_meta( get_post_thumbnail_id( $post->ID ), '_wp_attachment_image_alt', true );
        } else {

            $image = get_post( get_post_thumbnail_id( $post->ID ) );

            if ( 'title' == $settings->display ) {
                return $image->post_title;
            } elseif ( 'caption' == $settings->display ) {
                return $image->post_excerpt;
            } elseif ( 'description' == $settings->display ) {
                return $image->post_content;
            }
        }
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return array
     */
    static public function get_featured_image_url( $settings ) {
        global $post;

        $id  = '';
        $url = '';

        if ( has_post_thumbnail( $post ) ) {
            $id  = get_post_thumbnail_id( $post->ID );
            $url = get_the_post_thumbnail_url( $post, $settings->size );
        } elseif ( isset( $settings->default_img_src ) ) {
            $id  = $settings->default_img;
            $url = $settings->default_img_src;
        }

        return array(
            'id'  => $id,
            'url' => $url,
        );
    }

    /**
     * @since 1.0
     * @return array
     */
    static public function get_attached_images() {
        global $post;

        return array_keys( get_attached_media( 'image', $post->ID ) );
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function post_terms( $settings ) {
        global $post;

        $post_id = is_object( $post ) ? $post->ID : 0;
        $separator = property_exists( $settings, 'separator' ) ? $settings->separator : '';
        $taxonomy = property_exists( $settings, 'taxonomy' ) ? $settings->taxonomy : '';

        if( $taxonomy == 'tags' )
            $taxonomy = 'post_tag';

        if( ! taxonomy_exists( $taxonomy ) )
            return 'Could not retrieve ' . $taxonomy;

        $terms_list = $taxonomy ? get_the_term_list( $post_id, $taxonomy, '', $separator, '' ) : '';

        return Offsprout_Connector_Shortcode::return_value_or_default( $terms_list, 'No post terms' );
    }

    /**
     * @since 1.0
     * @return array
     */
    static public function get_taxonomy_options() {
        $taxonomies = get_taxonomies( array(
            'public' => true,
            'show_ui' => true,
        ), 'objects' );
        $result     = array();

        foreach ( $taxonomies as $slug => $data ) {

            if ( stristr( $slug, 'fl-builder' ) ) {
                continue;
            }

            $result[ $slug ] = $data->label;
        }

        return $result;
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function post_comments_number( $settings ) {
        $zero = isset( $settings->none_text ) ? $settings->none_text : '';
        $one = isset( $settings->one_text ) ? $settings->one_text : '';
        $more = isset( $settings->more_text ) ? $settings->more_text : '';
        $link = isset( $settings->link ) ? $settings->link : false;

        ob_start();

        if ( '1' == $link || 'yes' == $link ) {
            comments_popup_link( $zero, $one, $more );
        } else {
            comments_number( $zero, $one, $more );
        }

        return Offsprout_Connector_Shortcode::return_value_or_default( ob_get_clean(), 'No comments' );
    }

    /**
     * @since 1.0
     * @return string
     */
    static public function get_comments_url() {
        global $post;

        return get_comments_link( $post->ID );
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function author_name( $settings ) {

        $user = get_userdata( get_the_author_meta( 'ID' ) );
        $name = '';

        if ( ! $user ) {
            return '';
        }

        switch ( $settings->show ) {

            case 'display':
                $name = $user->display_name;
                break;

            case 'first':
                $name = get_user_meta( $user->ID, 'first_name', true );
                break;

            case 'last':
                $name = get_user_meta( $user->ID, 'last_name', true );
                break;

            case 'firstlast':
                $first = get_user_meta( $user->ID, 'first_name', true );
                $last  = get_user_meta( $user->ID, 'last_name', true );
                $name  = $first . ' ' . $last;
                break;

            case 'lastfirst':
                $first = get_user_meta( $user->ID, 'first_name', true );
                $last  = get_user_meta( $user->ID, 'last_name', true );
                $name  = $last . ', ' . $first;
                break;

            case 'nickname':
                $name = $user->user_nicename;
                break;

            case 'username':
                $name = $user->user_login;
                break;
        }

        if ( $name && isset( $settings->link ) && 'yes' == $settings->link ) {
            $settings->type = $settings->link_type;
            $name = '<a href="' . self::get_author_url( $settings ) . '">' . $name . '</a>';
        }

        return $name;
    }

    /**
     * @since 1.0
     * @return string
     */
    static public function author_bio() {
        return Offsprout_Connector_Shortcode::return_value_or_default( get_the_author_meta( 'description' ), __( 'This author has not written a bio', 'offsprout') );
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function author_url( $settings ) {

        $id  = get_the_author_meta( 'ID' );
        $url = '';

        if ( 'archive' == $settings->type ) {
            $url = get_author_posts_url( $id );
        } elseif ( 'website' == $settings->type ) {
            $user = get_userdata( $id );
            $url  = $user->user_url;
        }

        return $url;
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function author_image( $settings ) {
        $size   = ! is_numeric( $settings->size ) ? 512 : $settings->size;
        $avatar = get_avatar( get_the_author_meta( 'ID' ), $size );

        if ( '1' == $settings->link || 'yes' == $settings->link ) {
            $settings->type = $settings->link_type;
            $avatar = '<a href="' . self::author_url( $settings ) . '">' . $avatar . '</a>';
        }

        return $avatar;
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function get_author_profile_picture_url( $settings ) {

        // We get the url like this because not all custom avatar plugins filter get_avatar_url.
        $size = ! is_numeric( $settings->size ) ? 512 : $settings->size;
        $avatar = get_avatar( get_the_author_meta( 'ID' ), $size );
        preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $avatar, $matches, PREG_SET_ORDER );
        $url = ! empty( $matches ) && isset( $matches[0][1] ) ? $matches[0][1] : '';

        if ( ! $url && isset( $settings->default_img_src ) ) {
            $url = $settings->default_img_src;
        }

        return $url;
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function author_meta( $settings ) {

        if ( empty( $settings->field ) ) {
            return '';
        }

        return get_user_meta( get_the_author_meta( 'ID' ), $settings->field, true );
    }

    /**
     * @since 1.0
     * @param object $settings
     * @return string
     */
    static public function post_custom( $settings ) {
        global $post;

        if ( empty( $settings->field ) ) {
            return '';
        }

        $meta = get_post_meta( $post->ID, $settings->field, true );
        $output = $meta;

        if( $settings->type ){
            switch( $settings->type ){
                case 'link':
                    $anchor = isset( $settings->anchor ) ? (string) $settings->anchor : 'Link';
                    $attributes = isset( $settings->custom_attributes ) ? (string) $settings->custom_attributes : '';
                    $output = "<a href='{$meta}' {$attributes}>{$anchor}</a>";
                    break;
                case 'image':
                    $attributes = isset( $settings->custom_attributes ) ? (string) $settings->custom_attributes : '';
                    $output = "<img src='{$meta}' {$attributes} />";
                    break;
            }
        }

        return $output;
    }

    static public function get_query( $query, $meta, $taxonomies = array( 'category', 'post_tag' ), $other = array(), $pagination = array( 'include' => true ) ){

        global $wp_query;
        $original_query = $wp_query;

        $query      = apply_filters( 'ocb_post_query_query', $query );

        $the_wp_query = new WP_Query( $query );

        $wp_query = $the_wp_query;

        $posts = $the_wp_query->posts;

        if( is_array( $posts ) ){

            foreach( $posts as $key => $post ){

                $meta       = apply_filters( 'ocb_post_query_meta', $meta );
                $taxonomies = apply_filters( 'ocb_post_query_taxonomies', $taxonomies );
                $other      = apply_filters( 'ocb_post_query_other', $other, $post );

                if( ! empty( $taxonomies ) ){
                    $posts[$key]->ocb_taxonomies = array();
                    foreach( $taxonomies as $tax ){
                        $the_tax = wp_get_post_terms( $post->ID, $tax );
                        $posts[$key]->{$tax} = $the_tax;

                        $terms = wp_get_post_terms( $post->ID, $tax );
                        if( ! is_wp_error( $terms ) ){
                            $the_terms = array();
                            foreach( $terms as $term ){
                                $the_terms[] = '<a href="' . get_term_link( $term->term_id ) . '">' . $term->name . '</a>';
                            }
                            $posts[$key]->ocb_taxonomies['tax_' . $tax] = implode( ', ', $the_terms );
                        }
                    }
                }

                if( ! empty( $meta ) ){
                    $posts[$key]->{'meta'} = array();
                    foreach( $meta as $each ){
                        $meta_object = new stdClass();

                        $meta_object->{'key'} = $each;
                        $meta_object->{'value'} = get_post_meta( $post->ID, $each, true );
                        $posts[$key]->{'meta'}[] = $meta_object;
                    }
                }

                $excerpt = get_post_field( 'post_excerpt', $post->ID );
                $excerpt = $excerpt ? $excerpt : wp_trim_words( get_post_field( 'post_content', $post->ID ) );

                if( ! $excerpt ){
                    $allowed_html = array(
                        'p' => array(),
                        'span' => array(),
                        'blockquote' => array(),
                        'a' => array(),
                        'br' => array(),
                        'em' => array(),
                        'strong' => array(),
                    );
                    $excerpt = wp_kses( $post->post_content, $allowed_html );
                }

                $posts[$key]->{ "ocb_excerpt" } = Offsprout_Model::replace_strings( do_shortcode( $excerpt ) );
                //$posts[$key]->{ "ocb_date" } = mysql2date( $query['ocb_date_format'], $post->post_date );
                $posts[$key]->{ "ocb_date" } = array(
                    array(
                        'format' => 'm-d-Y',
                        'date' => mysql2date( 'm-d-Y', $post->post_date )
                    ),
                    array(
                        'format' => 'm.d.Y',
                        'date' => mysql2date( 'm.d.Y', $post->post_date )
                    ),
                    array(
                        'format' => 'd-m-Y',
                        'date' => mysql2date( 'd-m-Y', $post->post_date )
                    ),
                    array(
                        'format' => 'd.m.Y',
                        'date' => mysql2date( 'd.m.Y', $post->post_date )
                    ),
                    array(
                        'format' => 'l, F j, Y',
                        'date' => mysql2date( 'l, F j, Y', $post->post_date )
                    ),
                    array(
                        'format' => 'F j, Y',
                        'date' => mysql2date( 'F j, Y', $post->post_date )
                    ),
                    array(
                        'format' => 'F jS, Y',
                        'date' => mysql2date( 'F jS, Y', $post->post_date )
                    ),
                    array(
                        'format' => 'F, Y',
                        'date' => mysql2date( 'F, Y', $post->post_date )
                    ),
                    array(
                        'format' => 'F j',
                        'date' => mysql2date( 'F j', $post->post_date )
                    ),
                    array(
                        'format' => 'F jS',
                        'date' => mysql2date( 'F jS', $post->post_date )
                    ),
                    array(
                        'format' => 'Y',
                        'date' => mysql2date( 'Y', $post->post_date )
                    )
                );
                $posts[$key]->{ "ocb_author" } = '<a href="' . get_author_posts_url( $post->post_author ) . '">' . get_userdata( $post->post_author )->display_name . '</a>';
                $posts[$key]->{ "ocb_url" } = get_permalink( $post->ID );
                $posts[$key]->{ "ocb_featured_image_url" } = get_the_post_thumbnail_url( $post->ID, 'large' );

                $categories = array();
                $category_terms = wp_get_post_terms( $post->ID, 'category' );
                if( ! is_wp_error( $category_terms ) ){
                    foreach( $category_terms as $term ){
                        $categories[] = '<a href="' . get_term_link( $term->term_id ) . '">' . $term->name . '</a>';
                    }
                    $posts[$key]->{ "ocb_category" } = implode( ', ', $categories );
                }

                $tags = array();
                $tags_terms = wp_get_post_terms( $post->ID, 'post_tag' );
                if( ! is_wp_error( $tags_terms ) ) {
                    foreach ( $tags_terms as $term ) {
                        $tags[] = '<a href="' . get_term_link( $term->term_id ) . '">' . $term->name . '</a>';
                    }
                    $posts[$key]->{ "ocb_tag" } = implode( ', ', $tags );
                }

                if( $key == 0 && $pagination['include'] ){
                    global $wp_query;

                    //get_query_var( 'paged' ) doesn't seem to return the right page number for tax page numbers but the number is accurate in the query
                    $current_page = (int) get_query_var( 'paged' );

                    if( ! $current_page )
                        $current_page = isset( $wp_query->query ) && isset( $wp_query->query['paged'] ) ? (int) $wp_query->query['paged'] : 0;

                    $posts[$key]->{ "ocb_pagination" } = paginate_links( array(
                        'base'         => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
                        'total'        => $the_wp_query->max_num_pages,
                        'current'      => max( 1, $current_page ),
                        'format'       => '?paged=%#%',
                        'show_all'     => false,
                        'type'         => 'plain',
                        'end_size'     => 1,
                        'mid_size'     => 2,
                        'prev_next'    => true,
                        'prev_text'    => __( '« Previous', 'offsprout' ),
                        'next_text'    => __( 'Next »', 'offsprout' ),
                        'add_args'     => false,
                        'add_fragment' => '',
                    ) );
                }

                if( ! empty( $other ) ){
                    $posts[$key]->ocb_other = array();
                    foreach( $other as $prop => $callback ){
                        $posts[$key]->ocb_other[$prop] = $callback( $posts[$key] );
                    }
                }
            }

            wp_reset_postdata();

        }

        $wp_query = $original_query;
        wp_reset_query();

        if( empty( $posts ) ){
            $posts = array(
                'none_found' => true,
                'search' => get_search_form( false )
            );
        }

        return $posts;
    }
}
