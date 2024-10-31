<?php

class Offsprout_Replace{

    static function replace_skin_classes( $matches, $array_skins ){
        global $ocb_skins_used;

        $prop = 'classes';

        if( $matches[2] == 'settings' ){
            $prop = 'classes';
        } elseif( $matches[2] == 'standard' ){
            $prop = 'standardClasses';
        }
        
        $skin_id = $matches[3];
        $specific_class = $matches[4];

        //If there's a fifth set of matches, we're using the uses modifier ocbu:
        if( isset( $matches[5] ) ){
            $path = explode('|', $matches[5]);
            if( is_array( $path ) && count( $path ) == 5 ){
                if( isset( $array_skins[$path[0]] )
                    && isset( $array_skins[$path[0]][$path[1]] )
                    && isset( $array_skins[$path[0]][$path[1]][$path[2]] )
                    && isset( $array_skins[$path[0]][$path[1]][$path[2]][$path[3]] )
                    && isset( $array_skins[$path[0]][$path[1]][$path[2]][$path[3]][$path[4]] )
                ) {
                    $skin_id = $array_skins[$path[0]][$path[1]][$path[2]][$path[3]][$path[4]];
                }
            }
        }

        //We need to load the CSS of this skin now too
        if( ! is_array( $ocb_skins_used ) )
            $ocb_skins_used = array();

        if( ! in_array( $skin_id, $ocb_skins_used ) )
            $ocb_skins_used[] = $skin_id;

        $replace = isset( $array_skins[$skin_id] ) && isset( $array_skins[$skin_id][$prop] ) && isset( $array_skins[$skin_id][$prop][$specific_class] ) ? $array_skins[$skin_id][$prop][$specific_class] : '';
        
        $replace = trim( str_replace('--- ocb-has-skin ', '', explode( '{{', $replace )[0] ) );

        return $replace;
    }

    static function replace_featured_image( $matches, $image = false ){

        $size = isset( $matches[2] ) ? $matches[2] : 'full';
        $image = is_array( $image ) ? $image[0] : wp_get_attachment_image_src( get_post_thumbnail_id(), $size )[0];

        //If there's no featured image, check to see if there's a backup featured image
        if( ! $image ){
            $site_settings = get_option( 'ocb_site_settings' );
            if( isset( $site_settings['featured_image_fallback'] ) && isset( $site_settings['featured_image_fallback']['url'] ) && $site_settings['featured_image_fallback']['url'] ){
                $image = $site_settings['featured_image_fallback']['url'];
            }
        }

        return $image;
    }

    static function replace_logo_image( $matches, $image = false ){
        $site_settings = get_option( 'ocb_site_settings' );
        return isset( $site_settings['logo'] ) && isset( $site_settings['logo']['url'] ) ? $site_settings['logo']['url'] : '';
    }

    /**
     * Not a typical replace function - does not get called by replace_strings
     *
     * Replaces Post data in the post template
     *
     * @param $content
     * @param $values
     * @param $atts
     * @param $post_index
     * @return string
     */
    static function replace_posts( $content, $values, $atts, $post_index = 0 ){
        $replacements = ['post_title', 'post_content', 'ocb_url', 'ocb_excerpt', 'comment_count', 'ocb_category', 'ocb_tag', 'ocb_author', 'ocb_featured_image_url', 'ocb_date'];

        foreach( $replacements as $replacement ){

            $full_replacement = '{{' . $replacement . '}}';
            if( ! $values->$replacement && $values->$replacement !== 0 && $values->$replacement !== '0' ) {
                $content = str_replace( $full_replacement, '', $content );
            };

            $the_replacement = $values->$replacement;

            if( $replacement == 'post_content' ){
                $the_replacement = Offsprout_Model::replace_strings( $the_replacement );
            }
            if( $replacement == 'ocb_date' ){
                if( is_array( $the_replacement ) ) {
                    foreach ( $the_replacement as $date ) {
                        if ( $date['format'] == $atts['date_format'] ) {
                            $the_replacement = $date['date'];
                        }
                    }
                }
            }
            if( $replacement == 'ocb_excerpt' ){
                if( strlen( $the_replacement ) > (int) $atts['excerpt_length'] ){
                    $the_replacement = substr( $the_replacement, 0, $atts['excerpt_length'] ) . '...';
                }
            }

            if( ! is_array( $the_replacement ) )
                $content = str_replace( $full_replacement, $the_replacement, $content );
        }

        //Meta Replacement
        if( isset( $values->meta ) ){
            foreach( $values->meta as $index => $meta ){
                if( is_array( $meta->value ) || is_object( $meta->value ) ){

                    //cast object to array (only works on single dimensional objects)
                    if( is_object( $meta->value ) )
                        $meta->value = get_object_vars( $meta->value );

                    foreach( $meta->value as $meta_key => $meta_value ){

                        $full_replacement = '{{' . $meta->key . '|' . $meta_key . '}}';
                        $the_replacement = $meta_value;

                        $content = str_replace( $full_replacement, $the_replacement, $content );

                    }
                } else {

                    $full_replacement = '{{' . $meta->key . '}}';
                    $the_replacement = $meta->value;

                    $content = str_replace( $full_replacement, $the_replacement, $content );

                }
            }
        }

        //Taxonomy Replacement
        if( isset( $values->ocb_taxonomies ) ){
            foreach( $values->ocb_taxonomies as $key => $tax ){
                $full_replacement = '{{' . $key . '}}';
                $the_replacement = $tax;
                $content = str_replace( $full_replacement, $the_replacement, $content );
            }
        }

        //Other Replacement
        if( isset( $values->ocb_other ) ){
            foreach( $values->ocb_other as $key => $other ){
                $full_replacement = '{{' . $key . '}}';
                $the_replacement = $other;
                $content = str_replace( $full_replacement, $the_replacement, $content );
            }
        }

        //Index Class
        if( isset( $atts['index_class'] ) && $atts['index_class'] ){
            $content = str_replace( '{{index_class}}', $atts['index_class'] . ( $post_index + 1 ), $content );
        }

        return $content;
    }

}