(function($){

    /**
     * Helper class for dealing with the builder's admin
     * settings page.
     *
     * @class OffsproutAdminSettings
     * @since 1.0
     */
    OffsproutAdminSettings = {

        /**
         * An instance of wp.media used for uploading logos.
         *
         * @since 1.4.6
         * @access private
         * @property {Object} _logoUploader
         */
        _logoUploader: null,

        /**
         * Initializes the builder's admin settings page.
         *
         * @since 1.0
         * @method init
         */
        init: function()
        {
            this._bind();
            this._maybeInstallTemplates();
            this._initNav();
            this._initNetworkOverrides();
            this._initLicenseSettings();
            //this._initMultiSelects();
            //this._initUserAccessSelects();
            //this._initUserAccessNetworkOverrides();
            this._templatesOverrideChange();
        },

        /**
         * Binds events for the builder's admin settings page.
         *
         * @since 1.0
         * @access private
         * @method _bind
         */
        _bind: function()
        {
            $('.ocb-settings-nav a').on('click', OffsproutAdminSettings._navClicked);
            $('#ocb-show-installation-message').on('click', OffsproutAdminSettings._showInstallationMessage);
            $('#ocb-show-installation-errors').on('click', OffsproutAdminSettings._showInstallationErrors);
            $('.ocb-override-ms-cb').on('click', OffsproutAdminSettings._overrideCheckboxClicked);
            $('.ocb-ua-override-ms-cb').on('click', OffsproutAdminSettings._overrideUserAccessCheckboxClicked);
            $('.ocb-module-all-cb').on('click', OffsproutAdminSettings._moduleAllCheckboxClicked);
            $('.ocb-module-cb').on('click', OffsproutAdminSettings._moduleCheckboxClicked);
            $('input[name=ocb-templates-override]').on('keyup click', OffsproutAdminSettings._templatesOverrideChange);
            $('input[name=ocb-upload-logo]').on('click', OffsproutAdminSettings._showLogoUploader);
            $('#ocb-remove-logo-screen').on('click', OffsproutAdminSettings._deleteCustomLogo);
            $('#uninstall-form').on('submit', OffsproutAdminSettings._uninstallFormSubmit);
            $( '.ocb-settings-form .dashicons-editor-help' ).tipTip();
            $('#ocb-install-template-again').on('click', OffsproutAdminSettings._tryTemplatesAgain);
            $('#ocb-install-templates').on('click', OffsproutAdminSettings._installTemplateClick);
            $('#ocb-revert-ssl-changes').on('click', OffsproutAdminSettings._revertSSL);
        },

        _revertSSL: function(){
            var sendData = {
                action: 'ocb_revert_ssl',
                nonce: OffsproutAdminSettingsConfig.nonce
            };

            $.post(OffsproutAdminSettingsConfig.ajaxURL, sendData, function (data) {

                window.location.reload();

            });
        },

        /**
         * Trigger ajax to install the built-in templates
         *
         * @private
         */
        _maybeInstallTemplates: function()
        {
            var onLicense    = 'installation' == window.location.hash.replace( '#', '' );

            if ( onLicense && ! OffsproutAdminSettingsConfig.installationDone ) {
                this._sendInstallationAjax(1);
            } else if(
                ! window.location.hash.includes( 'pro-' )
                && ! window.location.hash.includes( 'theme-' )
                && ! window.location.hash.includes( 'woocommerce-' )
            ){
                window.location.hash = 'welcome';
                OffsproutAdminSettings._navClicked('#welcome');
            }
        },

        _tryTemplatesAgain: function()
        {
            $('#ocb-settings-ajax-message').hide().html('');
            $('#ocb-settings-ajax-errors').hide().html('');
            $('.ocb-settings-ajax').hide();
            $('.ocb-settings-hide-show').hide();

            OffsproutAdminSettings._sendInstallationAjax(1, true, true);
        },

        _installTemplateClick: function(){
            $('#ocb-settings-ajax-message').hide().html('');
            $('#ocb-settings-ajax-errors').hide().html('');
            $('.ocb-settings-ajax').hide();
            $('.ocb-settings-hide-show').hide();

            OffsproutAdminSettings._sendInstallationAjax(1, true);
        },

        _sendInstallationAjax: function(step, success, again)
        {
            if( step == undefined ) step = 1;
            if( success == undefined ) success = true;
            if( again == undefined ) again = false;

            var sendData = {
                action: 'ocb_install_templates',
                step: step,
                nonce: OffsproutAdminSettingsConfig.nonce
            };

            $('#ocb-settings-ajax-' + step).show();
            $('#ocb-install-templates').hide();

            $.post(OffsproutAdminSettingsConfig.ajaxURL, sendData, function(data){

                var newData = JSON.parse(data);
                var errors = newData.errors;
                var message = newData.message;
                var finished = newData.finished;

                console.log('errors', errors);

                if( errors.length ){
                    for( var i = 0; i < errors.length; i++ ){
                        console.log('error', errors[i]);
                        $('#ocb-settings-ajax-errors').append($(errors[i]));
                    }
                    success = false;
                }

                if( message ){
                    $('#ocb-settings-ajax-message').append($(message));
                }

                if( finished == undefined || finished == false ){
                    OffsproutAdminSettings._sendInstallationAjax((step + 1), success, again);
                } else {
                    if( success ){
                        $('#ocb-settings-ajax-3-success').show();
                        $('#ocb-show-installation-message').show();
                        window.location.hash = 'welcome';
                        OffsproutAdminSettings._navClicked('#welcome');
                        OffsproutAdminSettings._finishInstallation();
                    } else {
                        if( again ){
                            $('#ocb-settings-ajax-3-failure-2').show();
                            $('#ocb-discover-offsprout').show();
                        } else {
                            $('#ocb-settings-ajax-3-failure').show();
                            $('#ocb-show-installation-errors').show();
                        }
                    }
                }

            });

        },

        _finishInstallation: function(){
            var sendData = {
                action: 'ocb_finish_installation',
                nonce: OffsproutAdminSettingsConfig.nonce
            };

            $.post(OffsproutAdminSettingsConfig.ajaxURL, sendData, function(data){});
        },
        
        _showInstallationMessage: function(){
            var $message = $('#ocb-settings-ajax-message');
            if( $message.hasClass('active') ){
                $message.removeClass('active').slideUp();
            } else {
                $message.addClass('active').slideDown();
            }
        },
        
        _showInstallationErrors: function(){
            var $errors = $('#ocb-settings-ajax-errors');
            if( $errors.hasClass('active') ){
                $errors.removeClass('active').slideUp();
            } else {
                $errors.addClass('active').slideDown();
            }
        },

        /**
         * Initializes the nav for the builder's admin settings page.
         *
         * @since 1.0
         * @access private
         * @method _initNav
         */
        _initNav: function()
        {
            var links  = $('.ocb-settings-nav a'),
                hash   = window.location.hash,
                active = hash === '' ? [] : links.filter('[href~="'+ hash +'"]');

            $('a.ocb-active').removeClass('ocb-active');
            $('.ocb-settings-form').hide();

            if(hash === '' || active.length === 0) {
                active = links.eq(0);
            }

            active.addClass('ocb-active');
            $('#ocb-'+ active.attr('href').split('#').pop() +'-form').fadeIn();
        },

        /**
         * Fires when a nav item is clicked.
         *
         * @since 1.0
         * @access private
         * @method _navClicked
         */
        _navClicked: function( screen )
        {
            if( screen == undefined || screen == false || typeof screen != 'string' ) screen = $(this).attr('href');

            if(screen.indexOf('#') > -1) {
                $('a.ocb-active').removeClass('ocb-active');
                $('.ocb-settings-form').hide();
                $(this).addClass('ocb-active');
                $('#ocb-'+ screen.split('#').pop() +'-form').fadeIn();
            }
        },

        /**
         * Initializes the checkboxes for overriding network settings.
         *
         * @since 1.0
         * @access private
         * @method _initNetworkOverrides
         */
        _initNetworkOverrides: function()
        {
            $('.ocb-override-ms-cb').each(OffsproutAdminSettings._initNetworkOverride);
        },

        /**
         * Initializes a checkbox for overriding network settings.
         *
         * @since 1.0
         * @access private
         * @method _initNetworkOverride
         */
        _initNetworkOverride: function()
        {
            var cb      = $(this),
                content = cb.closest('.ocb-settings-form').find('.ocb-settings-form-content');

            if(this.checked) {
                content.show();
            }
            else {
                content.hide();
            }
        },

        /**
         * Fired when a network override checkbox is clicked.
         *
         * @since 1.0
         * @access private
         * @method _overrideCheckboxClicked
         */
        _overrideCheckboxClicked: function()
        {
            var cb      = $(this),
                content = cb.closest('.ocb-settings-form').find('.ocb-settings-form-content');

            if(this.checked) {
                content.show();
            }
            else {
                content.hide();
            }
        },

        /**
         * Initializes custom multi-selects.
         *
         * @since 1.10
         * @access private
         * @method _initMultiSelects
         */
        _initMultiSelects: function()
        {
            $( 'select[multiple]' ).multiselect( {
                selectAll: true,
                texts: {
                    deselectAll     : OffsproutAdminSettingsStrings.deselectAll,
                    noneSelected    : OffsproutAdminSettingsStrings.noneSelected,
                    placeholder     : OffsproutAdminSettingsStrings.select,
                    selectAll       : OffsproutAdminSettingsStrings.selectAll,
                    selectedOptions : OffsproutAdminSettingsStrings.selected
                }
            } );
        },

        /**
         * Initializes user access select options.
         *
         * @since 1.10
         * @access private
         * @method _initUserAccessSelects
         */
        _initUserAccessSelects: function()
        {
            var config  = OffsproutAdminSettingsConfig,
                options = null,
                role    = null,
                select  = null,
                key     = null,
                hidden  = null;

            $( '.ocb-user-access-select' ).each( function() {

                options = [];
                select  = $( this );
                key     = select.attr( 'name' ).replace( 'fl_user_access[', '' ).replace( '][]', '' );

                for( role in config.roles ) {
                    options.push( {
                        name    : config.roles[ role ],
                        value   : role,
                        checked : 'undefined' == typeof config.userAccess[ key ] ? false : config.userAccess[ key ][ role ]
                    } );
                }

                select.multiselect( 'loadOptions', options );
            } );
        },

        /**
         * Initializes the checkboxes for overriding user access
         * network settings.
         *
         * @since 1.0
         * @access private
         * @method _initUserAccessNetworkOverrides
         */
        _initUserAccessNetworkOverrides: function()
        {
            $('.ocb-ua-override-ms-cb').each(OffsproutAdminSettings._initUserAccessNetworkOverride);
        },

        /**
         * Initializes a checkbox for overriding user access
         * network settings.
         *
         * @since 1.0
         * @access private
         * @method _initUserAccessNetworkOverride
         */
        _initUserAccessNetworkOverride: function()
        {
            var cb     = $(this),
                select = cb.closest('.ocb-user-access-setting').find('.ms-options-wrap');

            if(this.checked) {
                select.show();
            }
            else {
                select.hide();
            }
        },

        /**
         * Fired when a network override checkbox is clicked.
         *
         * @since 1.0
         * @access private
         * @method _overrideCheckboxClicked
         */
        _overrideUserAccessCheckboxClicked: function()
        {
            var cb     = $(this),
                select = cb.closest('.ocb-user-access-setting').find('.ms-options-wrap');

            if(this.checked) {
                select.show();
            }
            else {
                select.hide();
            }
        },

        /**
         * Fires when the "all" checkbox in the list of enabled
         * modules is clicked.
         *
         * @since 1.0
         * @access private
         * @method _moduleAllCheckboxClicked
         */
        _moduleAllCheckboxClicked: function()
        {
            if($(this).is(':checked')) {
                $('.ocb-module-cb').prop('checked', true);
            }
        },

        /**
         * Fires when a checkbox in the list of enabled
         * modules is clicked.
         *
         * @since 1.0
         * @access private
         * @method _moduleCheckboxClicked
         */
        _moduleCheckboxClicked: function()
        {
            var allChecked = true;

            $('.ocb-module-cb').each(function() {

                if(!$(this).is(':checked')) {
                    allChecked = false;
                }
            });

            if(allChecked) {
                $('.ocb-module-all-cb').prop('checked', true);
            }
            else {
                $('.ocb-module-all-cb').prop('checked', false);
            }
        },

        /**
         * @since 1.7.4
         * @access private
         * @method _initLicenseSettings
         */
        _initLicenseSettings: function()
        {
            $( '.ocb-new-license-form .button' ).on( 'click', OffsproutAdminSettings._newLicenseButtonClick );
        },

        /**
         * @since 1.7.4
         * @access private
         * @method _newLicenseButtonClick
         */
        _newLicenseButtonClick: function()
        {
            $( '.ocb-new-license-form' ).hide();
            $( '.ocb-license-form' ).show();
        },

        /**
         * Fires when the templates override setting is changed.
         *
         * @since 1.6.3
         * @access private
         * @method _templatesOverrideChange
         */
        _templatesOverrideChange: function()
        {
            var input 			= $('input[name=ocb-templates-override]'),
                val 			= input.val(),
                overrideNodes 	= $( '.ocb-templates-override-nodes' ),
                toggle 			= false;

            if ( 'checkbox' == input.attr( 'type' ) ) {
                toggle = input.is( ':checked' );
            }
            else {
                toggle = '' !== val;
            }

            overrideNodes.toggle( toggle );
        },

        /**
         * Shows the media library lightbox for uploading logos.
         *
         * @since 1.4.6
         * @access private
         * @method _showLogoUploader
         */
        _showLogoUploader: function()
        {
            if(OffsproutAdminSettings._logoUploader === null) {
                OffsproutAdminSettings._logoUploader = wp.media({
                    title: OffsproutAdminSettingsStrings.selectFile,
                    button: { text: OffsproutAdminSettingsStrings.selectFile },
                    library : { type : 'image' },
                    multiple: false
                });
            }

            OffsproutAdminSettings._logoUploader.once('select', $.proxy(OffsproutAdminSettings._logoFileSelected, this));
            OffsproutAdminSettings._logoUploader.open();
        },

        /**
         * Callback for when an logo set file is selected.
         *
         * @since 1.4.6
         * @access private
         * @method _logoFileSelected
         */
        _logoFileSelected: function()
        {
            var file = OffsproutAdminSettings._logoUploader.state().get('selection').first().toJSON();

            console.log('file', file);
            var url = file.sizes.large != undefined && file.sizes.large.url ? file.sizes.large.url : file.sizes.full.url;

            $( '#ocb-logo-preview' ).html('<img style="max-width: 100%" src="' + url + '" />' );
            $( 'input[name=ocb-new-logo]' ).val( url );
        },

        /**
         * Fires when the delete link for an icon set is clicked.
         *
         * @since 1.4.6
         * @access private
         * @method _deleteCustomIconSet
         */
        _deleteCustomLogo: function()
        {
            $( 'input[name=ocb-remove-logo]' ).val( 1 );
            $( '#ocb-logo-preview' ).html('');
        },

        /**
         * Fires when the uninstall button is clicked.
         *
         * @since 1.0
         * @access private
         * @method _uninstallFormSubmit
         * @return {Boolean}
         */
        _uninstallFormSubmit: function()
        {
            var result = prompt(OffsproutAdminSettingsStrings.uninstall.replace(/&quot;/g, '"'), '');

            if(result == 'uninstall') {
                return true;
            }

            return false;
        }
    };

    /* Initializes the builder's admin settings. */
    $(function(){
        OffsproutAdminSettings.init();
    });

})(jQuery);