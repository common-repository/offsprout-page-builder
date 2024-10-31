<script type="text/javascript">

    OffsproutAdminSettingsConfig = {
        ajaxURL: '<?php echo admin_url('admin-ajax.php', ( isset( $_SERVER["HTTPS"] ) ? 'https://' : 'http://' ) ) ?>',
        hasGutenberg: <?php echo Offsprout_Model::has_gutenberg() ? 1 : 0 ?>,
        builderActive: <?php echo Offsprout_Model::is_builder_active() ? 1 : 0 ?>,
        nonce: '<?php echo wp_create_nonce('offsprout-admin'); ?>',
        installationDone: <?php echo Offsprout_Model::get_admin_settings_option( Offsprout_Installation::$install_ran_option ) ? 1 : 0 ?>
    };

    OffsproutAdminSettingsStrings = {
        deselectAll: '<?php esc_attr_e( 'Deselect All', 'offsprout' ); ?>',
        noneSelected: '<?php esc_attr_e( 'None Selected', 'offsprout' ); ?>',
        select: '<?php esc_attr_e( 'Select...', 'offsprout' ); ?>',
        selected: '<?php esc_attr_e( 'Selected', 'offsprout' ); ?>',
        selectAll: '<?php esc_attr_e( 'Select All', 'offsprout' ); ?>',
        selectFile: '<?php esc_attr_e( 'Select File', 'offsprout' ); ?>',
        uninstall: '<?php esc_attr_e( 'Please type "uninstall" in the box below to confirm that you really want to uninstall the page builder and all of its data.', 'offsprout' ); ?>'
    };

</script>
