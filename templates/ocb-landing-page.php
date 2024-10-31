<?php
/* Template Name: OCB Landing Page */

no_content_get_header();

do_action( 'ocb_before_content_wrapper' );

while ( have_posts() ) : the_post();
    do_action( 'ocb_page_elements' ); // Give your elements priorities so that they hook in the right place.
endwhile;

do_action( 'ocb_after_content_wrapper' );

no_content_get_footer();