<div class="wrap <?php Offsprout_Admin_Settings::render_page_class(); ?>">

    <h2 class="ocb-settings-heading">
        <?php Offsprout_Admin_Settings::render_page_heading(); ?>
    </h2>

    <?php Offsprout_Admin_Settings::render_update_message(); ?>

    <div class="ocb-settings-nav">
        <ul>
            <?php Offsprout_Admin_Settings::render_nav_items(); ?>
        </ul>
    </div>

    <div class="ocb-settings-content">
        <?php Offsprout_Admin_Settings::render_forms(); ?>
    </div>
</div>
