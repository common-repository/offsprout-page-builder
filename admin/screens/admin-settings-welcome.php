<?php

$blog_post_url     = '';
$change_logs_url   = '';
$upgrade_url       = '';
$support_url       = '';
$faqs_url          = '';
$forums_url        = '';
$docs_url          = '';

?>
<div id="ocb-welcome-form" class="ocb-settings-form">

    <h3 class="ocb-settings-form-header"><?php _e( 'Welcome to Offsprout!', 'offsprout' ); ?></h3>

    <div class="ocb-settings-form-content ocb-welcome-page-content">

        <p><strong><?php _e( 'Thank you for choosing Offsprout, the only WordPress builder built specifically for design agencies and freelancers!', 'offsprout' ); ?></strong>

            <?php if ( ! defined( 'OCB_THEME_VERSION' ) ) : ?>
                <?php printf( __( '', 'offsprout' ), $upgrade_url ); ?>
            <?php else : ?>
                <?php _e( '', 'offsprout' ); ?>
            <?php endif; ?>

        </p>
        <p><?php _e( 'It\'s highly recommended that you watch the short video below to get a sense of the full potential of Offsprout:', 'offsprout' ); ?></p>

        <iframe width="560" height="315" src="https://www.youtube.com/embed/IHdvimp0JWY" frameborder="0" allow="autoplay; encrypted-media" allowfullscreen></iframe>

        <h4><?php _e( 'Build your first page.', 'offsprout' ); ?></h4>

        <div class="ocb-welcome-col-wrap">

            <div class="ocb-welcome-col">

                <p><a href="<?php echo admin_url(); ?>post-new.php?post_type=page" class="ocb-welcome-big-link"><?php _e( 'Pages → Add New', 'offsprout' ); ?></a></p>

                <p><?php _e( 'Add a new page and use the Offsprout Builder by clicking the Page Builder tab shown in the image.', 'offsprout' ); ?></p>

            </div>

            <div class="ocb-welcome-col">
                <img class="ocb-welcome-img" src="<?php echo OCB_MAIN_DIR . 'admin/images/page-builder-tab.jpg'?>" alt="">
            </div>

        </div>

    </div>
</div>
