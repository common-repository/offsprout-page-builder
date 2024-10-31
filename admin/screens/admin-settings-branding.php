<?php

$upgrade_url       = '';
$branding = get_option( 'ocb_admin_branding' );
$logo = isset( $branding['logo'] ) ? '<img style="max-width: 100%" src="' . $branding['logo'] . '" /><div id="ocb-remove-logo-screen">Remove</div>' : '';

if( isset( $_POST['ocb-new-logo'] ) && $_POST['ocb-new-logo'] )
    $logo = '<img style="max-width: 100%" src="' . $_POST['ocb-new-logo'] . '" /><div id="ocb-remove-logo-screen">Remove</div>';

if( isset( $_POST['ocb-remove-logo'] ) && $_POST['ocb-remove-logo'] )
    $logo = '';

if(
    isset( $_POST['ocb-branding-nonce'] )
    && wp_verify_nonce( $_POST['ocb-branding-nonce'], 'branding' )
){
    if(
        isset( $_POST['ocb-new-logo'] )
        && $_POST['ocb-new-logo']
    ){
        update_option( 'ocb_admin_branding', array( 'logo' => $_POST['ocb-new-logo'] ) );
    }
    if(
        isset( $_POST['ocb-remove-logo'] )
        && $_POST['ocb-remove-logo']
    ){
        update_option( 'ocb_admin_branding', array() );
    }
}

?>
<div id="ocb-branding-form" class="ocb-settings-form">

    <h3 class="ocb-settings-form-header"><?php _e( 'Branding', 'offsprout' ); ?></h3>

    <?php if ( ! defined( 'OCB_THEME_VERSION' ) ) : ?>

        <div class="ocb-settings-form-content ocb-branding-page-content">
            <p>
                <?php _e( 'By default, Offsprout comes with no branding. However, you can upgrade to the Offsprout Theme to include your own branding.', 'offsprout' ); ?>
            </p>
        </div>

    <?php else : ?>

        <div class="ocb-settings-form-content ocb-branding-page-content">
            <p>
                <?php _e( 'Upload a logo that your clients will see in places like the menu.', 'offsprout' ); ?>
            </p>

            <div id="ocb-logo-preview" style="margin-top: 25px">
                <?php echo $logo ?>
            </div>

        </div>

        <form id="logo-form" action="" method="post">
            <input type="button" name="ocb-upload-logo" class="button" value="<?php esc_attr_e( 'Upload Logo', 'offsprout' ); ?>" />
            <p class="submit">
                <input type="submit" name="ocb-save-branding" class="button-primary" value="<?php esc_attr_e( 'Save Branding Settings', 'fl-builder' ); ?>" />
                <input type="hidden" name="ocb-new-logo" value="" />
                <input type="hidden" name="ocb-remove-logo" value="" />
                <?php wp_nonce_field('branding', 'ocb-branding-nonce'); ?>
            </p>
        </form>

    <?php endif; ?>
</div>
