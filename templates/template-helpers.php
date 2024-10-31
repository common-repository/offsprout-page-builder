<?php

function no_content_get_header() {

    ?>
    <!DOCTYPE html>
    <html <?php language_attributes(); ?> class="no-js">
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <link rel="profile" href="http://gmpg.org/xfn/11">
        <?php wp_head(); ?>
    </head>

    <body <?php body_class(); ?>>
    <?php
    do_action( 'ocb_content_body_before' );

}

function no_content_get_footer() {
    do_action( 'ocb_content_body_after' );
    wp_footer();
    ?>
    </body>
    </html>
    <?php
}

function ocb_page_elements( $which = 'index' ) {
    //Don't want to duplicate content at the bottom of a structure
    if( $which != 'search' && $which != 'woocommerce' )
        the_content();
}
add_action( 'ocb_page_elements', 'ocb_page_elements' );