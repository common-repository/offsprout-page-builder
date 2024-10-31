<div id="ocb-templates-form" class="ocb-settings-form">

    <h3 class="ocb-settings-header"><?php _e( 'Install Templates', 'offsprout' ); ?></h3>

    <p><?php _e( 'Offsprout comes with a bunch of templates so you can hit the ground running!', 'offsprout' ) ?></p>

    <p id="ocb-install-templates"><button class="button">Install</button></p>

    <p id="ocb-settings-ajax-1" class="ocb-settings-ajax"><?php _e( 'Installing page templates...', 'offsprout' ) ?></p>

    <p id="ocb-settings-ajax-2" class="ocb-settings-ajax"><?php _e( 'Installing row templates...', 'offsprout' ) ?></p>

    <p id="ocb-settings-ajax-3" class="ocb-settings-ajax"><?php _e( 'Installing Plus page templates...', 'offsprout' ) ?></p>

    <p id="ocb-settings-ajax-4" class="ocb-settings-ajax"><?php _e( 'Installing Plus row templates...', 'offsprout' ) ?></p>

    <div id="ocb-settings-ajax-3-success" class="ocb-settings-ajax">
        <p><?php _e( 'Mission Accomplished!', 'offsprout' ) ?></p>
    </div>

    <div id="ocb-settings-ajax-3-failure" class="ocb-settings-ajax">
        <p><?php _e( 'We were unable to import all of the templates. But that\'s okay! Try again here:', 'offsprout' ) ?></p>
        <button id="ocb-install-template-again" class="button">Try Installing Templates Again</button>
    </div>

    <div id="ocb-settings-ajax-3-failure-2" class="ocb-settings-ajax">
        <p><?php _e( 'Looks like your host may not have enough memory to import templates. Don\'t worry though. You can still use all of the features of the Offsprout builder!', 'offsprout' ) ?></p>
    </div>

</div>
