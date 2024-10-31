(function($){

    /**
     * Helper class for dealing with the builder's admin
     * settings page.
     *
     * @class OffsproutAllAdminSettings
     * @since 1.0
     */
    OffsproutAllAdminSettings = {

        /**
         * Initializes the builder's admin settings page.
         *
         * @since 1.0
         * @method init
         */
        init: function () {
            this._bind();
        },

        /**
         * Binds events for the builder's admin settings page.
         *
         * @since 1.0
         * @access private
         * @method _bind
         */
        _bind: function () {
            $('#ocb-do-ssl-change-1').on('click', OffsproutAllAdminSettings._fixSSL1);
            $('#ocb-do-ssl-change-2').on('click', OffsproutAllAdminSettings._fixSSL2);

            setTimeout(function(){
                $('#ocb-do-cache-purge').on('click', OffsproutAllAdminSettings._doCachePurge);
            }, 1000);

            if( OffsproutAllAdminSettings._getQueryString( 'highlightSOF' ) == 1 ){

                var message = '<p>For "Your homepage displays," select "a static page" and make sure the "Homepage" is set to the homepage that you would like, then click Save.</p>' +
                    '<p>If that option does not save, it is likely due to your host\'s caching. We can try to make saving work by <a id="ocb-do-cache-purge" href="#">clicking here</a>.</p>';

                if( OffsproutAllAdminSettings._getQueryString( 'SOFPurge' ) == 1 ){
                    message = '<p>Try saving again now. If that option still doesn\'t save, please contact your host. Otherwise, <a href="' + window.location.origin + '?pageEdit=1">return to homepage</a>.</p>';
                }

                $('#front-static-pages')
                    .css({border: '2px solid red'})
                    .prepend('<div class="notice notice-error">' + message + '</div>');
                $('input[name="show_on_front"][value="page"]').css({outline: '2px solid green'});
                $('#page_on_front').css({outline: '2px solid green'});
            }
        },

        _fixSSL: function (which, e) {
            e.preventDefault();
            OffsproutAllAdminSettings._sendSSLAjax( which )
        },

        _fixSSL1: function (e) {
            OffsproutAllAdminSettings._fixSSL(1, e);
        },

        _fixSSL2: function (e) {
            OffsproutAllAdminSettings._fixSSL(2, e);
        },

        _sendSSLAjax: function (which) {
            if (which == undefined) which = 1;

            var sendData = {
                action: 'ocb_fix_ssl',
                which: parseInt( which ),
                nonce: OffsproutAdminSettingsConfig.nonce
            };

            $.post(OffsproutAdminSettingsConfig.ajaxURL, sendData, function (data) {

                console.log('ssl finished', data);
                window.location.reload();

            });

        },

        _doCachePurge: function (e) {
            e.preventDefault();

            var sendData = {
                action: 'ocb_do_cache_purge',
                nonce: OffsproutAdminSettingsConfig.nonce
            };

            $.post(OffsproutAdminSettingsConfig.ajaxURL, sendData, function (data) {

                window.location = window.location.href + '&SOFPurge=1';

            });

        },

        /**
         * Get the value of a querystring
         * @param  {String} field The field to get the value of
         * @param  {String} url   The URL to get the value from (optional)
         * @return {String}       The field value
         */
        _getQueryString: function ( field, url ) {
            var href = url ? url : window.location.href;
            var reg = new RegExp( '[?&]' + field + '=([^&#]*)', 'i' );
            var string = reg.exec(href);
            return string ? string[1] : null;
        }

    };

    /* Initializes the builder's admin settings. */
    $(function(){
        OffsproutAllAdminSettings.init();
    });

})(jQuery);