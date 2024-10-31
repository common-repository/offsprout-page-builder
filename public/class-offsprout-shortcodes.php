<?php

class Offsprout_Shortcodes{
    public function __construct() {

        add_shortcode( 'ocb_global_template', array( $this, 'global_template' ) );
        add_shortcode( 'ocb_featured_image', array( $this, 'featured_image' ) );
        add_shortcode( 'ocb_posts_object', array( $this, 'posts_object' ) );
        add_shortcode( 'ocb_widget', array( $this, 'widget' ) );

    }

    public function global_template( $atts ){

        global $ocb_global_css;

        if( ! $ocb_global_css )
            $ocb_global_css = '';

        $atts = shortcode_atts( array(
            'template_id' => 0,
            'id' => '',
            'type' => 'row',
            'row_class' => '',
            'sticky_start' => '',
            'sticky_stop' => '',
            'sticky_width' => 768,
            'width' => '',
            'sticky_offset' => 0,
            'target' => '',
            'overlay' => ''
        ), $atts, 'ocb_global_template' );
        $id = $atts['template_id'];

        if( ! $id ) return '';

        $templates = get_posts( array(
            'post_type' => 'ocb_template',
            'meta_key'   => 'ocb_id',
            'meta_value' => $id,
        ) );

        $template = isset( $templates[0] ) ? $templates[0] : false;

        if( ! $template ) return __( 'Global not found', 'offsprout' );
        $content = do_shortcode( $template->post_content );

        $content = Offsprout_Model::replace_strings( $content );

        //Add CSS
        $css = get_post_meta( $template->ID, 'ocb_object_css', true );

        if( ! OCB_EDIT ){

            $skins_used = get_post_meta( $template->ID, 'ocb_skins_used', true );
            $skins_used = is_array( $skins_used ) ? $skins_used : array();
            $skins = get_option( 'ocb_site_skins' );

            foreach( $skins_used as $skin ){
                if( isset( $skins[$skin] ) ){
                    $css .= $skins[$skin]['css'];
                    $css .= $skins[$skin]['standardCSS'];
                }
            }


        }

        global $ocb_structure_content;

        if( ! OCB_EDIT || ! $ocb_structure_content ){
            $ocb_global_css .= $css;
        }

        //ocb_var_dump( $id );

        $classes = isset( $atts['row_class'] ) && $atts['row_class'] ? $atts['row_class'] : "ocb-object ocb-{$atts['type']}";
        $sticky_start = isset( $atts['sticky_start'] ) && $atts['sticky_start'] ? ' data-ocb-sticky-start="' . $atts['sticky_start'] . '"' : "";
        $sticky_offset = isset( $atts['sticky_offset'] ) && $atts['sticky_offset'] ? ' data-ocb-sticky-offset="' . $atts['sticky_offset'] . '"' : "";
        $sticky_stop = isset( $atts['sticky_stop'] ) && $atts['sticky_stop'] ? ' data-ocb-sticky-stop="' . $atts['sticky_stop'] . '"' : "";
        $sticky_width = isset( $atts['sticky_width'] ) && $atts['sticky_width'] ? ' data-ocb-sticky-width="' . $atts['sticky_width'] . '"' : "";
        $target = isset( $atts['target'] ) && $atts['target'] ? ' data-ocb-target="' . $atts['target'] . '"' : "";
        $overlay = isset( $atts['overlay'] ) && $atts['overlay'] ? ' data-ocb-overlay="' . $atts['overlay'] . '"' : "";
        $width = isset( $atts['width'] ) && $atts['width'] ? $atts['width'] : false;

        //Get width if it's a column
        $style = '';
        if( $atts['type'] == 'column' ){
            if( ! $width ){
                $tree = get_post_meta( $template->ID, 'ocb_tree_content', true );
                $width = $tree['width'];
                $style = "style='width:{$width}%'";
            } else {
                $style = "style='width:{$width}'";
            }
        }

        $id = '';

        $id = "id='{$atts['type']}-{$atts['id']}";

        if( $atts['type'] == 'module' )
            $id .= '-parent';

        $id .= "'";

        //ocb_var_dump( $atts );

        return "<div {$id} class='{$classes}' data-ocb-id='{$atts['id']}' {$style}{$sticky_start}{$sticky_stop}{$sticky_offset}{$sticky_width}{$target}{$overlay}>{$content}</div>";
    }

    public function featured_image( $atts ){

        $atts = shortcode_atts( array(
            'classes' => 0,
            'id' => '',
            'alt' => '',
            'title' => '',
            'src' => '',
            'size' => 'large',
        ), $atts, 'ocb_featured_image' );

        $image_src = $atts['src'];

        $id = get_post_thumbnail_id();
        $featured_image = wp_get_attachment_image_src( $id, $atts['size'] );
        $alt = $atts['alt'] && $atts['alt'] != 'undefined' ? $atts['alt'] : get_post_meta( $id, '_wp_attachment_image_alt', true );
        $title = $atts['title'] && $atts['title'] != 'undefined' ? $atts['title'] : get_the_title( $id );

        if( isset( $featured_image[0] ) && $featured_image[0] )
            $image_src = $featured_image[0];

        return "<img id='{$atts['id']}' class='{$atts['classes']}' alt='{$alt}' title='{$title}' src='{$image_src}' />";
    }

    public function posts_object( $atts, $content ){
        $a = shortcode_atts( array(
            'query' => 'main',
            'module_id' => false,
            'orderby' => 'date',
            'order' => 'DESC',
            'offset' => 0,
            'number' => 10,
            'date_format' => 'm d Y',
            'featured_image_size' => 'large',
            'category_filter' => '',
            'tag_filter' => '',
            'author_filter' => '',
            'post_filter' => '',
            'post_filter_type' => 'in',
            'category_filter_type' => 'in',
            'tag_filter_type' => 'in',
            'author_filter_type' => 'in',
            'custom_filter' => '',
            'custom_filter_type' => 'in',
            'custom_filter_taxonomy' => '',
            'post_type' => 'post',
            'page_filter_type' => 'specific',
            'page_filter_specific' => '',
            'page_filter_specific_type' => 'in',
            'page_filter_children' => '',
            'page_filter_children_type' => 'in',
            'post_id' => 0,
            'version' => 1,
            'excerpt_length' => 160,
            'no_posts_message' => 'No posts found',
            'no_posts_search' => 'yes',
            'index_class' => '',
            'with_meta' => '',
            'with_tax' => '',
            'include_paging' => 1
        ), $atts );

        $query = array();

        $id = $a['module_id'];

        $query['post_type'] = $a['post_type'];
        $query['orderby'] = $a['orderby'];
        $query['order'] = $a['order'];
        $query['ocb_date_format'] = $a['date_format'];
        $query['ocb_featured_image_size'] = $a['featured_image_size'];

        //For whatever reason, the query_var needs to be page on the homepage and paged on other pages
        if( is_front_page() )
            $paged = get_query_var('page')  ? get_query_var('page')  : 1;
        else
            $paged = get_query_var('paged') ? get_query_var('paged') : 1;

        if( $a['post_type'] == 'post' ){

            if( $a['offset'] && $paged <= 1 )
                $query['offset'] = (int) $a['offset'];

            $query['posts_per_page'] = (int) $a['number'];

            if( $paged )
                $query['paged'] = $paged;

            if( $a['author_filter'] ){
                if( $a['author_filter_type'] == 'in' )
                    $query['author__in'] = explode( ',', $a['author_filter'] );
                elseif( $a['author_filter_type'] == 'not_in' )
                    $query['author__not_in'] = explode( ',', $a['author_filter'] );
            }

            if( $a['category_filter'] ){
                if( $a['category_filter_type'] == 'in' )
                    $query['category__in'] = explode( ',', $a['category_filter'] );
                elseif( $a['category_filter_type'] == 'not_in' )
                    $query['category__not_in'] = explode( ',', $a['category_filter'] );
                elseif( $a['category_filter_type'] == 'not_in' )
                    $query['category__and'] = explode( ',', $a['category_filter'] );
            } elseif( $a['category_filter_type'] == 'in_related' ) {
                $query['category__in'] = wp_list_pluck( get_the_category(), 'term_id' );
                $query['post__not_in'] = array( get_the_ID() );
            }

            if( $a['tag_filter'] ){
                if( $a['tag_filter_type'] == 'in' )
                    $query['tag__in'] = explode( ',', $a['tag_filter'] );
                elseif( $a['tag_filter_type'] == 'not_in' )
                    $query['tag__not_in'] = explode( ',', $a['tag_filter'] );
                elseif( $a['tag_filter_type'] == 'not_in' )
                    $query['tag__and'] = explode( ',', $a['tag_filter'] );
            } elseif( $a['tag_filter_type'] == 'in_related' ) {
                $query['tag__in'] = wp_list_pluck( get_the_tags(), 'term_id' );
                $query['post__not_in'] = array( get_the_ID() );
            }

        } elseif( $a['post_type'] == 'page' ){

            if( $a['page_filter_type'] == 'specific' ){

                if( $a['page_filter_specific'] ){
                    if( $a['page_filter_specific_type'] == 'in' )
                        $query['post__in'] = explode( ',', $a['page_filter_specific'] );
                    elseif( $a['page_filter_specific_type'] == 'not_in' )
                        $query['post__not_in'] = explode( ',', $a['page_filter_specific'] );
                }

            } elseif( $a['page_filter_type'] == 'children_specific' ){

                if( $a['page_filter_children'] ){
                    if( $a['page_filter_children_type'] == 'in' )
                        $query['post_parent__in'] = explode( ',', $a['page_filter_children'] );
                    elseif( $a['page_filter_children_type'] == 'not_in' )
                        $query['post_parent__not_in'] = explode( ',', $a['page_filter_children'] );
                }

            } elseif( $a['page_filter_type'] == 'children_current' ){

                $query['post_parent__in'] = explode( ',', $a['post_id'] );

            }

        } else {

            if( $a['category_filter'] ){
                if( $a['category_filter_type'] == 'in' )
                    $query['category__in'] = explode( ',', $a['category_filter'] );
                elseif( $a['category_filter_type'] == 'not_in' )
                    $query['category__not_in'] = explode( ',', $a['category_filter'] );
                elseif( $a['category_filter_type'] == 'not_in' )
                    $query['category__and'] = explode( ',', $a['category_filter'] );
            } elseif( $a['category_filter_type'] == 'in_related' ) {
                $query['category__in'] = wp_list_pluck( get_the_category(), 'term_id' );
                $query['post__not_in'] = array( get_the_ID() );
            }

            if( $a['tag_filter'] ){
                if( $a['tag_filter_type'] == 'in' )
                    $query['tag__in'] = explode( ',', $a['tag_filter'] );
                elseif( $a['tag_filter_type'] == 'not_in' )
                    $query['tag__not_in'] = explode( ',', $a['tag_filter'] );
                elseif( $a['tag_filter_type'] == 'not_in' )
                    $query['tag__and'] = explode( ',', $a['tag_filter'] );
            } elseif( $a['tag_filter_type'] == 'in_related' ) {
                $query['tag__in'] = wp_list_pluck( get_the_tags(), 'term_id' );
                $query['post__not_in'] = array( get_the_ID() );
            }

            if( $a['custom_filter'] && isset( $a['custom_filter_taxonomy'] ) && $a['custom_filter_taxonomy'] ){
                $custom_tax = $a['custom_filter_taxonomy'];

                if( $a['custom_filter_type'] == 'in' ){
                    $query['tax_query'] = array(
                        array(
                            'taxonomy' => $custom_tax,
                            'field'    => 'term_id',
                            'terms'    => explode( ',', $a['custom_filter'] ),
                            'operator' => 'IN'
                        ),
                    );
                } elseif( $a['custom_filter_type'] == 'not_in' ){
                    $query['tax_query'] = array(
                        array(
                            'taxonomy' => $custom_tax,
                            'field'    => 'term_id',
                            'terms'    => explode( ',', $a['custom_filter'] ),
                            'operator' => 'NOT IN'
                        ),
                    );
                }
            }

        }

        if( $a['post_type'] != 'page' ){
            if( $a['post_filter'] ){
                if( $a['post_filter_type'] == 'in' )
                    $query['post__in'] = explode( ',', $a['post_filter'] );
                elseif( $a['post_filter_type'] == 'not_in' )
                    $query['post__not_in'] = explode( ',', $a['post_filter'] );
            }
        }

        $meta_array = false;

        if( $a['with_meta'] ){
            $meta_array = explode( ',', $a['with_meta'] );
        }

        $taxonomies = array( 'category', 'post_tag' );

        if( $a['with_tax'] ){
            $taxonomies = array_merge( $taxonomies, explode( ',', $a['with_tax'] ) );
        }

        global $wp_query;

        if( $a['query'] == 'main' ){

            //If the shortcode is getting the main query, but the main query is for a singular post or page, get a default query
            if( is_singular() ){
                $new_query = array(
                    'post_type' => 'post',
                );
            } else {
                $new_query = $wp_query->query;
            }

            if( $a['number'] )
                $new_query['posts_per_page'] = $a['number'];

        } else {
            if( $a['number'] )
                $query['posts_per_page'] = $a['number'];

            $new_query = $query;

        }

        //WooCommerce ordering
        if( class_exists( 'WC_Query' ) ){
            if ( isset( $_GET['max_price'] ) || isset( $_GET['min_price'] ) ) { // WPCS: input var ok, CSRF ok.
                $meta_query = wc_get_min_max_price_meta_query( $_GET ); // WPCS: input var ok, CSRF ok.
                $meta_query['price_filter'] = true;

                $new_query['meta_query'] = $meta_query;

                if( ! isset( $new_query['orderby'] ) ||$new_query['orderby'] == 'menu_order title' ){
                    $_GET['orderby'] = 'popularity';
                }
            }

            if( $a['orderby'] == 'woocommerce' ){
                $catalog_ordering = WC()->query->get_catalog_ordering_args();
                if( $catalog_ordering )
                    $new_query = array_merge( $new_query, $catalog_ordering );
            }

            $tax_query = WC()->query->get_tax_query(array(), ( $a['query'] == 'main' ));
            if( $tax_query )
                $new_query['tax_query'] = $tax_query;
        }

        $new_query      = apply_filters( 'ocb_post_shortcode_query', $new_query, $a );
        $meta_array     = apply_filters( 'ocb_post_shortcode_meta', $meta_array, $a );
        $taxonomies     = apply_filters( 'ocb_post_shortcode_taxonomies', $taxonomies, $a );
        $other          = apply_filters( 'ocb_post_shortcode_other', array(), $a );

        $pagination = array( 'include' => $a['include_paging'] );

        $posts = Offsprout_Post_Data::get_query( $new_query, $meta_array, $taxonomies, $other, $pagination );

        Offsprout_Debug::write_debug( $posts );

        $the_return = '';

        if( $a['version'] == 1 ){
            $the_return = "<script>
                (function(){
                if( window.OCBPosts == undefined ){ window.OCBPosts = {}; }
                window.OCBPosts.{$id} = " . json_encode( $posts ) . "
                }());
                </script>";
        } else if( $a['version'] == 2 ){
            if( ! count( $posts ) || ( isset( $posts['none_found'] ) && $posts['none_found'] ) ){
                $the_return = '<div class="mb-3">'. $a['no_posts_message'] . '</div>';
                if( $a['no_posts_search'] == 'yes' ){
                    ob_start();
                    get_search_form();
                    $the_return .= ob_get_clean();
                }
            } else {
                foreach( $posts as $index => $the_post ){
                    $the_return .= Offsprout_Replace::replace_posts( $content, $the_post, $a, $index );
                }
                if( isset( $posts[0]->ocb_pagination ) ){
                    $the_return .= '<div class="ocb-posts-pagination">' . $posts[0]->ocb_pagination . '</div>';
                }
            }
        }


        return $the_return;
    }

    public function widget( $atts, $content ){
        $a = shortcode_atts( array(
            'type' => '',
            'widget_id' => '',
            'compatibility' => 0
        ), $atts );

        //Replace empty JSON values
        $content = str_replace( ':\'\',', ':"",', $content );

        //Replace JSON keys 'key':
        $content = preg_replace( '/\'([a-zA-Z0-9-_]*)\':/', "\"$1\":", $content );

        //Replace JSON values :'anything here',
        //Also replace " within those matches with '
        //https://stackoverflow.com/questions/7124778/how-to-match-anything-up-until-this-sequence-of-characters-in-a-regular-expres/7124976
        $content = preg_replace_callback( '/:\'(.*?(?=\',))\',/', function($matches){
            $match = str_replace( '"', '\'', $matches[1] );
            $replace = ":\"$match\",";

            return $replace;
        }, $content );

        $content = preg_replace_callback( '/:"(.*?(?=",))",/', function($matches){
            $match = str_replace( '"', '\'', $matches[1] );
            $replace = ":\"$match\",";

            return $replace;
        }, $content );

        //Replaces weird quote characters
        if( $a['compatibility'] ){
            $content = str_replace( '&#8221;', '""', $content);
            $content = str_replace( '&#8216;', '"', $content );
            $content = str_replace( '&#8217;', '"', $content );
            $content = str_replace( '&#8242;', '"', $content );
        }

        //ocb_var_dump( $content );

        //This one turns :”, into :"", because an empty value was being returned as ”
        //$content = str_replace( ':&#8221;,', ':"",', $content );

        //In structures, ' is used instead of "
        //$content = str_replace( '\'', '"', $content );

        if( $a['type'] ){
            if( ! class_exists( $a['type'] ) )
                return 'Widget type does not exist';

            ob_start();

            //Add the id and name properties because some widgets seem to expect it
            the_widget( $a['type'], json_decode( $content ), array( 'widget_id' => $a['widget_id'], 'id' => $a['widget_id'], 'name' => '' ) );

            $output = ob_get_clean();

            if( $output )
                return $output;
        }


        if( defined('REST_REQUEST') && REST_REQUEST )
            return 'Widget: ' . $a['type'];

        return '';
    }
}

new Offsprout_Shortcodes();