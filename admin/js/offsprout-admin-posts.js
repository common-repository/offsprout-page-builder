(function($){

    /**
     * Helper class for dealing with the post edit screen.
     *
     * @class FLBuilderAdminPosts
     * @since 1.0
     * @static
     */
    OffsproutBuilderAdminPosts = {

        /**
         * Initializes the builder for the post edit screen.
         *
         * @since 1.0
         * @method init
         */
        init: function()
        {
            if( OffsproutAdminSettingsConfig.builderActive ){
                $( 'body' ).addClass('offsprout-builder-enabled');
            } else {
                $( 'body' ).addClass('offsprout-builder-disabled');
            }

            var newThis = this;
            newThis.cache = {};

            if( OffsproutAdminSettingsConfig.hasGutenberg ){

                setTimeout(function(){

                    newThis.cache.$gutenberg = $( '#editor' );
                    newThis.cache.$switchMode = $( $( '#offsprout-gutenberg-button-switch-mode' ).html() );
                    newThis.cache.$toolbar = newThis.cache.$gutenberg.find( '.edit-post-header-toolbar' );

                    newThis.cache.$toolbar.append( newThis.cache.$switchMode );
                    newThis.cache.$switchEditor = newThis.cache.$switchMode.find( '#offsprout-switch-mode-button-editor' );
                    newThis.cache.$switchBuilder = newThis.cache.$switchMode.find( '#offsprout-switch-mode-button-builder' );

                    newThis.cache.$switchEditor.on('click', newThis._gutenbergEditorClicked);
                    newThis.cache.$switchBuilder.on('click', newThis._gutenbergBuilderClicked);

                    OffsproutBuilderAdminPosts._buildPanel();
                    OffsproutBuilderAdminPosts._onSave();

                }, 1);

            }

            newThis.cache.$enableEditor = $('.offsprout-enable-editor');
            newThis.cache.$enableBuilder = $('.offsprout-enable-builder');

            newThis.cache.$enableEditor.on('click', newThis._enableEditorClicked);
            newThis.cache.$enableBuilder.on('click', newThis._enableBuilderClicked);

            if( newThis.cache.$enableBuilder.hasClass('offsprout-active') ){
                newThis._enableBuilderClicked( newThis.cache.$enableBuilder );
            }

            //$('.offsprout-launch-builder').on('click', this._launchBuilderClicked);
        },

        _onSave: function(){
            var self = this;

            //Super hacky way to tell WordPress that Gutenberg is saving the post so that post_content isn't saved
            //Save a transient that says which post is being saved and check for that transient during wp_insert_post_data in Offsprout_Includes
            //Really Gutenberg?!
            self.cache.clickBlock = false;

            self.cache.$gutenberg.find( '.editor-post-publish-button' ).on('click', function(e){

                if( ! OffsproutAdminSettingsConfig.builderActive )
                    return;

                var that = $(this);
                that.css('opacity', 0.5);

                if( ! self.cache.clickBlock ) {
                    e.stopPropagation();

                    var sendData = {
                        action: 'ocb_set_gutenberg_transient',
                        postId: $('#post_ID').val(),
                        nonce: OffsproutAdminSettingsConfig.nonce
                    };

                    $.post(OffsproutAdminSettingsConfig.ajaxURL, sendData, function (data) {
                        self.cache.clickBlock = true;
                        setTimeout(function(){
                            that.css('opacity', 1);
                        }, 100);
                        that.click();
                    });

                } else {

                    self.cache.clickBlock = false;

                }
            })
        },

        _buildPanel: function() {
            var self = this;

            if ( ! $( '#offsprout-editor' ).length && OffsproutAdminSettingsConfig.builderActive ) {
                self.cache.$editorPanel = $( $( '#offsprout-gutenberg-panel' ).html() );
                //self.cache.$gutenbergBlockList = self.cache.$gutenberg.find( '.editor-block-list__layout, .editor-post-text-editor' );
                //self.cache.$gutenbergBlockList.after( self.cache.$editorPanel );
                self.cache.$gutenbergBlockList = self.cache.$gutenberg.find( '.block-editor-block-list__layout' );
                self.cache.$gutenbergBlockList.before( self.cache.$editorPanel );
            }
        },

        _gutenbergEditorClicked: function(){
            var confirm = window.confirm("Are you sure you want to switch to the regular text editor? Changes made here are not compatible with the page builder.");
            if (confirm == true) {
                $('input[name="offsprout-builder-active"]').remove();
                $('body').removeClass('offsprout-builder-enabled').addClass('offsprout-builder-disabled');
                OffsproutBuilderAdminPosts._toggleBuilderActive( 0 );
                $(window).resize();
            }
        },

        _gutenbergBuilderClicked: function(){
            $('body').addClass('offsprout-builder-enabled').removeClass('offsprout-builder-disabled');
            $('.offsprout-launch-builder').after('<input type="hidden" name="offsprout-builder-active" value="1" />');
            OffsproutBuilderAdminPosts._toggleBuilderActive( 1 );

            var doSubmit = $('.offsprout-enable-builder').hasClass('offsprout-redirect');
            OffsproutBuilderAdminPosts._gutenbergLaunchBuilder(doSubmit);
        },

        /**
         * Fires when the text editor button is clicked
         * and switches the current post to use that
         * instead of the builder.
         *
         * @since 1.0
         * @access private
         * @method _enableEditorClicked
         */
        _enableEditorClicked: function()
        {
            var confirm = window.confirm("Are you sure you want to switch to the regular text editor? Changes made here are not compatible with the page builder.");
            if (confirm == true) {
                $('.offsprout-builder-admin-tabs a').removeClass('offsprout-active');
                $('input[name="offsprout-builder-active"]').remove();
                $(this).addClass('offsprout-active');
                $('body').removeClass('offsprout-builder-enabled');
                OffsproutBuilderAdminPosts._toggleBuilderActive( 0 );
                $(window).resize();
            }

            //OffsproutBuilderAdminPosts._enableEditorComplete;
        },

        _toggleBuilderActive: function( active ){
            var sendData = {
                action: 'ocb_toggle_builder_active',
                active: active,
                postId: $('#post_ID').val(),
                nonce: OffsproutAdminSettingsConfig.nonce
            };
            $.post(OffsproutAdminSettingsConfig.ajaxURL, sendData, function(data){
                OffsproutAdminSettingsConfig.builderActive = active;
            });
        },

        /**
         * Callback for enabling the editor.
         *
         * @since 1.0
         * @access private
         * @method _enableEditorComplete
         */
        _enableEditorComplete: function()
        {
            $(window).resize();
        },

        /**
         * Callback for enabling the editor.
         *
         * @since 1.0
         * @access private
         * @method _enableBuilderClicked
         */
        _enableBuilderClicked: function()
        {
            $('.offsprout-builder-admin-tabs a').removeClass('offsprout-active');
            $('.offsprout-enable-builder').addClass('offsprout-active');
            $('body').addClass('offsprout-builder-enabled');
            $('.offsprout-launch-builder').after('<input type="hidden" name="offsprout-builder-active" value="1" />');
            OffsproutBuilderAdminPosts._toggleBuilderActive( 1 );

            var doSubmit = $('.offsprout-enable-builder').hasClass('offsprout-redirect');
            OffsproutBuilderAdminPosts._launchBuilder(doSubmit);
        },

        /**
         * Fires when the page builder button is clicked
         * and switches the current post to use that
         * instead of the text editor.
         *
         * @since 1.0
         * @access private
         * @method _launchBuilderClicked
         * @param {Object} e An event object.
         */
        _launchBuilderClicked: function(e)
        {
            e.preventDefault();

            OffsproutBuilderAdminPosts._launchBuilder();
        },

        /**
         * Callback for enabling the builder.
         *
         * @since 1.0
         * @access private
         * @method _launchBuilder
         */
        _launchBuilder: function( submit )
        {
            if( submit == undefined ) submit = false;

            var redirect = $('.offsprout-launch-builder').attr('href'),
                title    = $('#title');

            if(typeof title !== 'undefined' && title.val() === '') {
                title.val('Post #' + $('#post_ID').val());
            }

            $(window).off('beforeunload');
            $('body').addClass('offsprout-builder-enabled');
            $('.offsprout-builder-loading').show();

            if( submit ) {
                $('form#post').append('<input type="hidden" name="offsprout-builder-redirect" value="' + redirect + '" />');
                $('form#post').submit();
            }
        },

        /**
         * Callback for enabling the builder.
         *
         * @since 1.0
         * @access private
         * @method _launchBuilder
         */
        _gutenbergLaunchBuilder: function( submit )
        {
            if( submit == undefined ) submit = false;

            var postId = $('#post_ID').val();

            var redirect = $('#offsprout-switch-mode-button-builder').attr('data-redirect'),
                title    = $('.editor-post-title__input'),
                theTitle = '';

            if(typeof title !== 'undefined' && title.val() === '') {
                title.val('Post #' + postId);
                theTitle = title.val();
            }

            $(window).off('beforeunload');
            $('body').addClass('offsprout-builder-enabled');
            $('.offsprout-builder-loading').show();

            if( submit ) {

                var sendData = {
                    action: 'ocb_publish_gutenberg_post',
                    title: theTitle,
                    postId: postId,
                    nonce: OffsproutAdminSettingsConfig.nonce
                };
                $.post(OffsproutAdminSettingsConfig.ajaxURL, sendData, function (data) {
                    window.location = redirect;
                });

            }
        }
    };

    /* Initializes the post edit screen. */
    $(function(){
        OffsproutBuilderAdminPosts.init();
    });

})(jQuery);