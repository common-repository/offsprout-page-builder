var ocbInitializePlugins = function(){
    clearTimeout(ocbInitializeTimer);
    ocbInitializeTimer = setTimeout(function(){
        ocbInitMasonry();
        ocbInitBeforeAfter();
        ocbInitWaypoints();
        ocbInitJarallax();
        ocbInitAllMaps();
        ocbInitProgressBars();
        ocbInitSliders();
        ocbInitPosts();
        ocbReplaceChildClassAdd();
        ocbInitNav(); //MUST BE LAST BECAUSE THE PREVIOUS WILL OVERWRITE
        jQuery('[data-toggle="tooltip"]').tooltip();
    }, 300)
};

var ocbReInitializeTimer = null;
var ocbInitializeTimer = null;
var ocbReInitializeSubsetTimer = null;
var ocbReInitializeLazyTimer = null;

/**
 *
 * @param topWindow if we're rendering content in the top window, pass true
 */
var ocbReInitializePlugins = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    clearTimeout(ocbReInitializeTimer);
    ocbReInitializeTimer = setTimeout(function(){
        ocbReInitMasonry( topWindow );
        ocbInitBeforeAfter();
        ocbInitJarallax( topWindow );
        ocbInitAllMaps( topWindow );
        ocbInitProgressBarsNoWaypoint( topWindow );
        ocbReInitSliders( topWindow );
        ocbInitPosts( topWindow );
        ocbReplaceChildClassAdd( topWindow );
        ocbInitNav(); //MUST BE LAST BECAUSE THE PREVIOUS WILL OVERWRITE
        ocbInitNavVisible();
        jQuery('[data-toggle="tooltip"]').tooltip();
    }, 500)
};

var ocbInitLazy = function( topWindow ){
    clearTimeout(ocbReInitializeLazyTimer);
    ocbReInitializeLazyTimer = setTimeout(function(){

        if( topWindow == undefined ){
            topWindow = false;
        }

        var theDoc = ocbGetDocument( topWindow );
        var theWindow = ocbGetWindow( topWindow );

        var lazy = theDoc.getElementsByClassName('ocb-lazy');

        for( var i = 0; i < lazy.length; i++ ){
            var dataBg = jQuery(lazy[i]).attr('data-bg');
            var dataSrc = jQuery(lazy[i]).attr('data-src');
            if( dataBg ){
                jQuery(lazy[i]).attr('style', 'background-image: ' + dataBg);
            }
            if( dataSrc ){
                jQuery(lazy[i]).attr('src', dataSrc);
            }
        }

        jQuery('.wow').show();

    }, 500);
};

/**
 * Especially important for things like parallax with skins where a routine needs to be run on other objects that
 * may have been affected by the changing of a skin
 */
var ocbReInitializeSubset = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    clearTimeout(ocbReInitializeSubsetTimer);
    ocbReInitializeSubsetTimer = setTimeout(function(){
        ocbInitJarallax();
    }, 500)
};

var ocbDoNeededInitializePlugins = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    setTimeout(function() {
        ocbInitJarallax( topWindow );
        ocbInitProgressBarsNoWaypoint( topWindow );
        ocbInitNavVisible( topWindow );
        ocbInitPosts( topWindow );
    }, 300);
};

var ocbHideShow = function(id){
    var theDoc = window.ocbGetDocument();
    var element = theDoc.getElementById('module-' + id);
    if( element == null ) element = theDoc.getElementById('column-' + id);
    if( element == null ) element = theDoc.getElementById('row-' + id);
    element.style.display = 'none';
    if( ! element.className.includes('animated') ){
        element.className += ' animated';
    }
    setTimeout(function(){
        element.style.display = 'block';
    }, 100);
};

var ocbGetDocument = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    if( topWindow ) return document;

    var iframeJS = document.getElementById('ocb-workspace-iframe');
    var iframeDocument = null;
    var theDoc = document;
    if( iframeJS !== null ){
        iframeDocument = iframeJS.contentDocument || iframeJS.contentWindow.document;
        theDoc = iframeDocument;
    }

    return theDoc;
};

var ocbGetWindow = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    if( topWindow ) return window;

    var iframeJS = document.getElementById('ocb-workspace-iframe');
    var theWindow = window;
    if( iframeJS !== null ){
        theWindow = iframeJS.contentWindow;
    }

    return theWindow;
};

/********* OFFSPROUT NAV **********/

var ocbInitNav = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theDoc = ocbGetDocument( topWindow );
    var theWindow = ocbGetWindow( topWindow );

    jQuery('.ocb-menu-desktop').addClass('ocb-menu-visible');

    jQuery(theDoc).find('.ocb-menu-item, .ocb-menu-item-child').each(function(){
        var parser = document.createElement('a');
        var href = jQuery(this).attr('href');
        if( ! href ) return;
        if( href.startsWith('#') ) return;
        parser.href = href;
        if( theWindow.location.pathname == parser.pathname ){
            jQuery(this).addClass('active');
            if( jQuery(this).hasClass('ocb-menu-item-child') ){
                jQuery(this).closest('.ocb-menu-item-wrap').find('.ocb-menu-item').addClass('active');
            }
        }
    });
};

var ocbInitNavVisible = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theDoc = ocbGetDocument( false );

    setTimeout(function(){
        jQuery(theDoc).find('.ocb-menu-desktop').addClass('ocb-menu-visible');
    }, 200);
};

/********* OFFSPROUT WAYPOINTS **********/

var ocbInitWaypoints = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theDoc = ocbGetDocument( topWindow );
    var theWindow = ocbGetWindow( topWindow );

    if( theDoc == undefined || theDoc.body == undefined || ! theDoc.body.className.includes('ocb-logged-out') ) return;

    var waypoints = theDoc.getElementsByClassName('ocb-sticky');

    for( var i = 0; i < waypoints.length; i++ ){
        theWindow.ocbInitWaypoint(waypoints[i], waypoints[i].dataset, topWindow);
    }
};

var ocbInitWaypoint = function(element){
    if( jQuery(element).closest('.sticky-wrapper').length ) return;

    var width = jQuery(element).innerWidth();
    var minStickyWidth = parseInt(jQuery(element).attr('data-ocb-sticky-width'));
    var outerWidth = parseInt(window.outerWidth) || parseInt(window.innerWidth);

    if( outerWidth < minStickyWidth ) return;

    var stickyStop = jQuery(element).attr('data-ocb-sticky-stop');
    var offset = jQuery(element).attr('data-ocb-sticky-offset');
    if( offset == undefined || offset == false ) offset = 0;

    var height = 0; //if we want to make this work when logged in, need to figure this out

    jQuery(element).css({width: width + 'px', maxWidth: '100%'});
    var sticky = new Waypoint.Sticky({
        element: jQuery(element),
        offset: offset
    });

    if( stickyStop ) {
        jQuery('#row-' + stickyStop).waypoint(function (direction) {
            if (direction == 'up') {
                jQuery(element).css('top', '');
            } else if (direction == 'down') {
                var fromTop = jQuery(window).scrollTop() - height;
                jQuery(element).css({top: fromTop + 'px'})
            }
            jQuery(element).toggleClass('stuck', direction === 'up');
            jQuery(element).toggleClass('sticky-surpassed', direction === 'down');
        }, {
            offset: function () {
                return jQuery(element).outerHeight();
            }
        });
    }
};

/********* OFFSPROUT POSTS **********/

var ocbInitPosts = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theDoc = ocbGetDocument( topWindow );
    var theWindow = ocbGetWindow( topWindow );
    var posts = theDoc.getElementsByClassName('ocb-posts');

    if( theWindow.ocbInitSinglePosts == undefined ) return;

    for( var i = 0; i < posts.length; i++ ){
        theWindow.ocbInitSinglePosts(posts[i], posts[i].dataset, topWindow);
    }

    jQuery('.ocb-post-image').each(function(){
        //Remove featured images if no featured image (brittle way to do this)
        if( jQuery(this).css('background-image') == 'url("' + window.location.href + '")' ){
            jQuery(this).closest('.ocb-post-image-wrap').remove();
        }
    })
};

var ocbReInitSinglePosts = function(id){
    var theWindow = ocbGetWindow();

    theWindow.ocbSinglePostsTimeout = setTimeout(function () {
        var theDoc = ocbGetDocument();
        var posts = theDoc.getElementById('ocb-posts-' + id);
        theWindow.ocbInitSinglePosts(posts, posts.dataset);
    }, 300);
};

var ocbSinglePostsTries = 0;
var ocbSinglePostsTimeout = null;
var ocbSinglePostsTriesTimeout = null;

/**
 * Takes the template post and uses that with whatever posts are found in the OCBPosts object,
 * replacing {{properties}} with their corresponding values
 *
 * @param element
 * @param data
 * @param topWindow
 */
var ocbInitSinglePosts = function(element, data, topWindow){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theWindow = ocbGetWindow();
    //console.log('ocbInitSinglePosts', theWindow.OCBPosts);
    var theId = jQuery(element).attr('data-posts-id');
    var excerptLength = jQuery(element).attr('data-excerpt-length');
    var dateFormat = jQuery(element).attr('data-date-format');
    var noPosts = jQuery(element).attr('data-no-posts-message');
    var includeSearch = jQuery(element).attr('data-no-posts-search');
    clearTimeout( theWindow.ocbSinglePostsTimeout );

    if( theWindow.OCBPosts == undefined && topWindow == false ){
        theWindow.ocbSinglePostsTries++;

        if( theWindow.ocbSinglePostsTries <= 3 ) {

            theWindow.ocbSinglePostsTriesTimeout = setTimeout(function () {
                theWindow.ocbInitSinglePosts(element, data);
            }, 500);

        }

        return;
    }

    clearTimeout( theWindow.ocbSinglePostsTriesTimeout );

    let thePosts = [];

    if( theWindow.OCBPosts != undefined && theWindow.OCBPosts[theId] != undefined ){
        thePosts = theWindow.OCBPosts[theId];
    }

    //Use dummy content if template preview
    if( topWindow ){
        if( theWindow.OCBPosts == undefined ) theWindow.OCBPosts = {};

        const dummyPost = {
            ID: 657,
            category: [
                {
                    name: "Category",
                    slug: "category"
                }
            ],
            comment_count: 1,
            comment_status: 'open',
            filter: 'raw',
            guid:"http://php.hgv.test/?p=657",
            menu_order: 0,
            meta: [
                {
                    key: 'ocb_testimonial_config',
                    value: {
                        attribution: 'John Doe',
                        attribution_description: 'CEO of Company'
                    }
                }
            ],
            ocb_author:'<a href="http://php.hgv.test/author/wordpress/">Author Name</a>',
            ocb_category:'<a href="http://php.hgv.test/category/category/">Category</a>',
            ocb_date: [
                {
                    date: "01-31-2050",
                    format: "m-d-Y"
                },
                {
                    date: "01.31.2050",
                    format: "m.d.Y"
                },
                {
                    date: "31-01-2050",
                    format: "d-m-Y"
                },
                {
                    date: "31.01.2050",
                    format: "d.m.Y"
                },
                {
                    date: "Monday, January 31, 2050",
                    format: "l, F j, Y"
                },
                {
                    date: "January 31, 2050",
                    format: "F j, Y"
                },
                {
                    date: "January 31st, 2050",
                    format: "F jS, Y"
                },
                {
                    date: "January, 2050",
                    format: "F, Y"
                },
                {
                    date: "January 31",
                    format: "F j"
                },
                {
                    date: "January 31st",
                    format: "F jS"
                },
                {
                    date: "2050",
                    format: "Y"
                }
            ],
            ocb_excerpt:"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum bibendum quis est ut facilisis. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Vestibulum bibendum quis est ut facilisis.",
            ocb_featured_image_url:"https://s3-us-west-2.amazonaws.com/s.cdpn.io/142996/slider-2.jpg",
            ocb_pagination: null,
            ocb_tag:'<a href="http://php.hgv.test/tag/tag/">Tag</a>',
            ocb_url:"https://offsprout.com/",
            ping_status:"open",
            pinged:"",
            post_author:"1",
            post_content:"<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nam tellus mauris, laoreet vel quam vitae, dictum molestie mauris. Maecenas eget venenatis quam. Nam non nisi est.</p>",
            post_content_filtered:"",
            post_date:"2050-01-31 16:56:49",
            post_date_gmt:"2050-01-31 16:56:49",
            post_excerpt:"",
            post_mime_type:"",
            post_modified:"2050-01-31 19:25:15",
            post_modified_gmt:"2050-01-31 19:25:15",
            post_name:"post-5",
            post_parent: 0,
            post_password:"",
            post_status:"publish",
            post_tag: {
                name: 'tag',
                slug: 'tag'
            },
            post_title:"Post Title",
            post_type:"post",
            to_ping:""
        };

        thePosts = [
            dummyPost,
            dummyPost,
            dummyPost,
            dummyPost,
            dummyPost,
            dummyPost
        ];
    } else {
        
    }

    var replacements = ['post_title', 'post_content', 'ocb_url', 'ocb_excerpt', 'comment_count', 'ocb_category', 'ocb_tag', 'ocb_author', 'ocb_featured_image_url', 'ocb_date'];
    var wrap = jQuery(element).find('.ocb-posts-wrap:not(.ocb-posts-wrap-public)');

    if( thePosts.length && thePosts.search == undefined ){
        var template = jQuery(element).find('.ocb-post-template');
        var clone = template.clone();
        var pagination = jQuery(element).find('.ocb-posts-pagination');

        clone.removeClass('ocb-post-template');

        var cloneHtml = clone.html();
        wrap.html('');
        clone.html('');
        pagination.html('');
        wrap.append(clone);

        if( cloneHtml == undefined ) return;

        for( var i = 0; i < thePosts.length; i++ ){

            var newCloneHTML = cloneHtml;

            for( var j = 0; j < replacements.length; j++ ){
                let theValue = thePosts[i][replacements[j]];

                if( theValue != undefined ) {
                    if (replacements[j] == 'ocb_date') {
                        //let theDate = theValue.find(date => date.format == dateFormat);
                        let theDate = ocbAltFind( theValue, function(date){
                            return date.format == dateFormat;
                        });
                        let date = theDate != undefined && theDate.date != undefined ? theDate.date : '';
                        newCloneHTML = newCloneHTML.replaceAll('{{ocb_date}}', date);
                    } else if( replacements[j] == 'ocb_excerpt' && excerptLength ) {
                        newCloneHTML = newCloneHTML.replaceAll( '{{ocb_excerpt}}', theValue.substring(0,excerptLength) + '...' );
                    } else {
                        newCloneHTML = newCloneHTML.replaceAll( '{{' + replacements[j] + '}}', theValue );
                    }
                } else {
                    newCloneHTML = newCloneHTML.replaceAll( '{{' + replacements[j] + '}}', '' );
                }

            }

            //Meta replacement
            if( thePosts[i].meta != undefined && thePosts[i].meta.length ){
                for( var k = 0; k < thePosts[i].meta.length; k++ ){
                    if( typeof thePosts[i].meta[k].value == 'object' ){

                        //For meta values that are arrays, the array key is designated after the | in the postVar
                        for (var key in thePosts[i].meta[k].value) {
                            if (thePosts[i].meta[k].value.hasOwnProperty(key)) {
                                newCloneHTML = newCloneHTML.replaceAll( '{{' + thePosts[i].meta[k].key + '|' + key + '}}', thePosts[i].meta[k].value[key] );
                            }
                        }
                    } else {

                        //For meta values that are strings, use the meta key as the postVar
                        if (thePosts[i].meta[k].value.hasOwnProperty(key)) {
                            newCloneHTML = newCloneHTML.replaceAll( '{{' + thePosts[i].meta[k].key + '}}', thePosts[i].meta[k].value[key] );
                        }
                    }
                }
            }

            //Taxonomy replacement
            if( thePosts[i].ocb_taxonomies != undefined ){
                for( var taxKey in thePosts[i].ocb_taxonomies ){
                    if (thePosts[i].ocb_taxonomies.hasOwnProperty(taxKey)) {
                        newCloneHTML = newCloneHTML.replaceAll( '{{' + taxKey + '}}', thePosts[i].ocb_taxonomies[taxKey] );
                    }
                }
            }

            //Other replacement
            if( thePosts[i].ocb_other != undefined ){
                for( var otherKey in thePosts[i].ocb_other ){
                    if (thePosts[i].ocb_other.hasOwnProperty(otherKey)) {
                        newCloneHTML = newCloneHTML.replaceAll( '{{' + otherKey + '}}', thePosts[i].ocb_other[otherKey] );
                    }
                }
            }

            newCloneHTML = newCloneHTML.replace('animated fadeIn');

            var toInsert = jQuery(newCloneHTML);

            if( thePosts[i].ocb_featured_image_url == false || thePosts[i].ocb_featured_image_url == undefined ){
                toInsert.find('.ocb-post-image-wrap').remove();
            }

            clone.append(toInsert);
        }

        wrap.append(clone);

        if( thePosts[0].ocb_pagination != undefined && thePosts[0].ocb_pagination != false ){
            pagination.html(thePosts[0].ocb_pagination);
        }
    } else {
        let search = includeSearch == 'yes' ? thePosts.search : '';
        wrap.html(search + '<p>' + noPosts + '</p>');
    }
};

var ocbAltFind = function(arr, callback) {
    for (var i = 0; i < arr.length; i++) {
        var match = callback(arr[i]);
        if (match) {
            return arr[i];
            break;
        }
    }
}


/********* OFFSPROUT SLIDER **********/
var ocbAllSliders = {};

var ocbInitSliders = function(){
    var theDoc = ocbGetDocument();
    var theWindow = ocbGetWindow();
    var sliders = theDoc.getElementsByClassName('ocb-slider-container');

    for( var i = 0; i < sliders.length; i++ ){
        theWindow.ocbInitSingleSlider(sliders[i], sliders[i].dataset);
    }
};

var ocbReInitSliders = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theDoc = ocbGetDocument( topWindow );
    var theWindow = ocbGetWindow( topWindow );
    var sliders = theDoc.getElementsByClassName('ocb-slider-container');

    for( var i = 0; i < sliders.length; i++ ){
        theWindow.ocbReInitSingleSlider(sliders[i].getAttribute('data-sliderid'));
    }
};

var ocbReInitSingleSlider = function(id){
    setTimeout(function(){
        var theDoc = ocbGetDocument();
        var theWindow = ocbGetWindow();
        var slider = theDoc.getElementById('ocb-slider-' + id);
        if( slider != null ){
            theWindow.ocbInitSingleSlider(slider, slider.dataset);
        }
    }, 100);
};

var ocbSliderNext = function(event){
    var active = parseInt(jQuery(event.data.slider).attr('data-activeslide'));
    var newActive = ocbSliderGetNext(active, jQuery(event.data.slider).attr('data-slidenumber'));
    ocbSliderNewActive(event, active, newActive);
};

var ocbSliderGetNext = function(active, total){
    return parseInt(active) == parseInt(total) ? 1 : parseInt(active) + 1;
};

var ocbSliderPrev = function(event){
    var active = parseInt(jQuery(event.data.slider).attr('data-activeslide'));
    var newActive = ocbSliderGetPrev(active, jQuery(event.data.slider).attr('data-slidenumber'));
    ocbSliderNewActive(event, active, newActive);
};

var ocbSliderGetPrev = function(active, total){
    return parseInt(active) == 1 ? parseInt(total) : parseInt(active) - 1;
};

var ocbSliderNav = function(event){
    var active = parseInt(jQuery(event.data.slider).attr('data-activeslide'));
    var newActive = jQuery(this).attr('data-index');
    ocbSliderNewActive(event, active, newActive);
};

var ocbSliderNewActive = function(event, active, newActive){
    if( active == newActive ) return;

    ocbSliderSet(event.data.slider, newActive)
};

var ocbSliderSet = function(slider, newActive){
    jQuery(slider).find('.ocb-slider-slide').removeClass('prev');
    jQuery(slider).find('.ocb-slider-slide').removeClass('next');
    jQuery(slider).find('.ocb-slider-slide').removeClass('active');
    jQuery(slider).find('.ocb-slider-slide:nth-child(-n+' + ( newActive - 1 ) + ')').addClass('prev');
    jQuery(slider).find('.ocb-slider-slide:nth-child(n+' + ( newActive + 1 ) + ')').addClass('next');
    jQuery(slider).find('.ocb-slider-slide-' + newActive).addClass('active');
    jQuery(slider).attr('data-activeslide', newActive);
    jQuery(slider).find('.ocb-slider-bullet').removeClass('active');
    jQuery(slider).find('.ocb-slider-bullet:nth-child(' + newActive + ')').addClass('active');
};

var ocbSliderPlay = function(options){
    clearInterval(ocbSliderInterval[options.id]);
    var defaults = {
        autoplay: 1,
        autoplayStopOnLast: 0,
        time: 5000
    };
    var newOptions = Object.assign(defaults, options);

    if( newOptions.autoplay ) {
        ocbSliderInterval[options.id] = setInterval(function () {
            var total = jQuery(newOptions.event.data.slider).attr('data-slidenumber');
            var current = jQuery(newOptions.event.data.slider).attr('data-activeslide');
            if( ! ocbSliderPaused && ( ! newOptions.autoplayStopOnLast || current != total ) ){
                ocbSliderNext(newOptions.event);
            }
        }, newOptions.time);
    }
};

var ocbSliderInterval = [];

var ocbSliderPaused = false;

var ocbInitSingleSlider = function(element, data, slide){
    if( data == undefined ){
        data = jQuery(element).data();
    }
    if( slide == undefined ){
        slide = false;
    }
    
    var $element = jQuery(element);

    $element.find('.ocb-slider-control').remove();

    var id = $element.attr('data-sliderid');
    var $slider = jQuery('#ocb-slider-' + id);
    var autoplay = $element.attr('data-autoplay') == false || $element.attr('data-autoplay') == 'false' ? 0 : parseInt($element.attr('data-autoplay'));
    var autoplayStopOnLast = $element.attr('data-autoplaystoponlast') == 'false' ? 0 : $element.attr('data-autoplaystoponlast');
    var effect = $element.attr('data-effect');
    var slidenumber = $slider.find('.ocb-slider-slide').length;
    var navigation = $element.attr('data-navigation');
    var pagination = $element.attr('data-pagination');
    var paginationColor = $element.attr('data-paginationcolor');
    var navigationColor = $element.attr('data-navigationcolor');
    var activeSlide = $element.attr('data-activslide');
    var pauseOnHover = $element.attr('data-pauseonhover');
    var paginationType = $element.attr('data-paginationtype');
    var loop = autoplayStopOnLast ? false : true;

    var $leftNavDom = null;
    var $rightNavDom = null;
    var $pagination = null;

    //Add pagination bullets
    if( pagination == 1 ) {
        var paginationColorClass = '';
        if( paginationColor != 'default' && paginationColor != 'custom' ){
            paginationColorClass = paginationColor + '-background';
        }
        var $bullet = jQuery('<div class="ocb-slider-bullet ' + paginationColorClass + '"></div>');
        $pagination = jQuery('<div class="ocb-slider-pagination ocb-save-remove-element ocb-slider-control ocb-slider-pagination-type-' + paginationType + '"></div>');
        for( var i = 1; i <= slidenumber; i++ ){
            var $newBullet = $bullet.clone();
            $newBullet.addClass('ocb-slider-bullet-' + i);
            $newBullet.attr('data-index', i);
            $pagination.append($newBullet);
        }
    }

    if( navigation == 1 ){
        var navigationColorClass = '';
        if( navigationColor != 'default' && navigationColor != 'custom' ){
            navigationColorClass = navigationColor + '-color';
        }
        $leftNavDom = jQuery('<div class="ocb-slider-button-prev ocb-slider-button-navigation ocb-slider-control ocb-save-remove-element ' + navigationColorClass + '"><i class="fa fa-chevron-left" /></div>');
        $rightNavDom = jQuery('<div class="ocb-slider-button-next ocb-slider-button-navigation ocb-slider-control ocb-save-remove-element ' + navigationColorClass + '"><i class="fa fa-chevron-right" /></div>');
    }

    if( activeSlide == undefined ){
        activeSlide = 1;
    }
    $slider
        .append($pagination)
        .append($leftNavDom)
        .append($rightNavDom);

    $slider
        .attr('data-activeslide', activeSlide)
        .attr('data-slidenumber', slidenumber);

    if( pauseOnHover == 1 ) {
        $slider
            .hover(function () { //mouse enter
                ocbSliderPaused = true;
            }, function () { //mouse leave
                ocbSliderPaused = false;
            });
    }

    if( $leftNavDom != null && $rightNavDom != null ){
        $leftNavDom.on('click', { slider: $slider }, ocbSliderPrev);
        $rightNavDom.on('click', { slider: $slider }, ocbSliderNext);
    }

    if( pagination == 1 && $pagination != null ){
        $pagination.on('click', '.ocb-slider-bullet', { slider: $slider }, ocbSliderNav);
    }

    ocbSliderPlay({
        autoplay: autoplay ? 1 : 0,
        autoplayStopOnLast: autoplayStopOnLast,
        time: autoplay ? autoplay : 5000,
        id: id,
        event: {
            data: {
                slider: $slider
            }
        }
    });

    ocbSliderSet($slider, activeSlide);

    var theWindow = ocbGetWindow();
    theWindow.ocbAllSliders[id] = $slider;
};

/********* PROGRESS BAR **********/
var ocbAllProgressBars = {};
var ocbProgressBarTimeout = null;

var ocbInitProgressBars = function(){
    var theDoc = ocbGetDocument();
    var counters = theDoc.getElementsByClassName('ocb-number-counter');

    for( var i = 0; i < counters.length; i++ ){
        jQuery(counters[i]).waypoint({
            handler: function(direction) {
                ocbInitSingleProgressBar(this.element, this.element.dataset);
            },
            offset: '90%',
            triggerOnce: true
        });
    }
};

var ocbInitProgressBarsNoWaypoint = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theDoc = ocbGetDocument( topWindow );
    var counters = theDoc.getElementsByClassName('ocb-number-counter');

    for( var i = 0; i < counters.length; i++ ){
        ocbInitSingleProgressBar(counters[i], counters[i].dataset);
    }
};

/**
 * Called by the settings panel to re-init
 * @param id
 */
var ocbReInitSingleProgressBar = function(id){
    clearTimeout(ocbProgressBarTimeout);
    ocbProgressBarTimeout = setTimeout(function(){
        var theWindow = ocbGetWindow();
        var theDocument = ocbGetDocument();
        var counterWrap = theDocument.getElementById('module-' + id);
        var counter = counterWrap.getElementsByClassName('ocb-number-counter');
        if( theWindow.ocbAllProgressBars[id] != undefined ){
            theWindow.ocbAllProgressBars[id].destroy();
        }
        if( window.ocbAllProgressBars[id] != undefined ){
            window.ocbAllProgressBars[id].destroy();
        }
        theWindow.ocbInitSingleProgressBar(counter[0], counter[0].dataset);
    }, 500);
};

var ocbInitSingleProgressBar = function(element, data){
    if( data == undefined ){
        data = jQuery(element).data();
    }

    jQuery(element).html('');

    var bar = new ProgressBar[data.shape](element, {
        color: data.startColor,
        // This has to be the same size as the maximum width to
        // prevent clipping
        strokeWidth: parseInt(data.endWidth),
        trailWidth: parseInt(data.trailWidth),
        trailColor: data.trailColor,
        easing: data.easing,
        duration: parseInt(data.duration),
        text: {
            autoStyleContainer: false,
            style: {
                color: data.textColor,
                position: data.shape == 'Line' ? 'relative' : 'absolute',
                left: data.shape == 'Line' ? 'initial' : '50%',
                top: data.shape == 'Line' ? 'initial' : '50%',
                textAlign: data.shape == 'Line' ? data.textAlign : 'center',
                padding: 0,
                margin: 0,
                fontSize: data.fontSize,
                transform: {
                    prefix: true,
                    value: data.shape == 'Line' ? 'initial' : 'translate(-50%, -50%)'
                }
            }
        },
        from: { color: data.startColor, width: parseInt(data.startWidth) },
        to: { color: data.endColor, width: parseInt(data.endWidth) },
        // Set default step function for all animate calls
        step: function(state, circle) {
            circle.path.setAttribute('stroke', state.color);
            circle.path.setAttribute('stroke-width', state.width);

            var value = Math.round(circle.value() * parseInt(data.max));
            if (value === 0) {
                circle.setText('');
            } else {
                circle.setText(data.textBefore + value + data.textAfter);
            }

        }
    });

    var theWindow = ocbGetWindow();
    theWindow.ocbAllProgressBars[data.id] = bar;

    //bar.set(data.progress);
    bar.animate(parseFloat(data.progress));  // Number from 0.0 to 1.0
};

/********* GOOGLE MAPS **********/

/**
 * Global with all of the Google maps on the page
 * @type {Array}
 */
var ocbAllGoogleMaps = [];

/**
 * Finds and initializes all google maps
 */
var ocbInitAllMaps = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    /*var theDoc = window.ocbGetDocument();
    var maps = theDoc.getElementsByClassName('ocb-map');

    for( var i = 0; i < maps.length; i++ ){
        window.ocbInitSingleMap(maps[i]);
    }*/
    var theWindow = ocbGetWindow( topWindow );
    jQuery('.ocb-map').each(function(){
        theWindow.ocbInitSingleMap(this);
    })
};

/**
 * Initializes a single Google map
 * @param element - the DOM element
 * @param data - specific data that will be used instead of the data attributes if set
 */
var ocbInitSingleMap = function(element, data){
    if( data == undefined ){
        data = jQuery(element).data();
    }

    if (! OCBGlobalSettings.permissions.has_maps_api){
        console.log('Please set your Google Maps API key in Site Settings > API Keys. Then save, and refresh the page to see your map.');
        return;
    }

    var geocoder = new google.maps.Geocoder();
    var theDoc = ocbGetDocument;

    geocoder.geocode({
        'address': data.center
    }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK) {
            var map = new google.maps.Map(element, {
                zoom: parseInt(data.zoom),
                center: results[0].geometry.location,
                styles: window.ocbGoogleMapStyles[data.mapSkin],
                scrollwheel: false
            });
            var mapIconWidth = 70;
            var mapIconHeight = 96;
            var mapIconColor = data.markerColor == undefined ? '#000000' : data.markerColor;
            mapIconColor = mapIconColor.replace('#', '');
            var mapIcon = 'data:image/svg+xml,<svg%20xmlns%3D"http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg"%20xmlns%3Axlink%3D"http%3A%2F%2Fwww.w3.org%2F1999%2Fxlink"%20width%3D"' + mapIconWidth + '"%20height%3D"' + mapIconHeight + '"%20version%3D"1.1"%20x%3D"0px"%20y%3D"0px"%20viewBox%3D"0%200%20100%20125"%20enable-background%3D"new%200%200%20100%20100"%20xml%3Aspace%3D"preserve"><g><rect%20x%3D"-192"%20width%3D"185"%20height%3D"99"%2F><rect%20y%3D"-36"%20width%3D"100"%20height%3D"30"%2F><line%20fill%3D"%23' + mapIconColor + '"%20stroke%3D"%23FFFFFF"%20stroke-miterlimit%3D"10"%20x1%3D"8"%20y1%3D"-14.5"%20x2%3D"18"%20y2%3D"-14.5"%2F><line%20fill%3D"%23' + mapIconColor + '"%20stroke%3D"%23FFFFFF"%20stroke-miterlimit%3D"10"%20x1%3D"-179"%20y1%3D"16.5"%20x2%3D"-162"%20y2%3D"16.5"%2F><rect%20x%3D"-179"%20y%3D"58"%20fill%3D"none"%20width%3D"35"%20height%3D"32.5"%2F><rect%20x%3D"-136.5"%20y%3D"58"%20fill%3D"none"%20width%3D"35"%20height%3D"32.5"%2F><rect%20x%3D"-94"%20y%3D"58"%20fill%3D"none"%20width%3D"35"%20height%3D"32.5"%2F><rect%20x%3D"-50"%20y%3D"58"%20fill%3D"none"%20width%3D"35"%20height%3D"32.5"%2F><rect%20x%3D"-126.514"%20y%3D"34.815"%20fill%3D"%23' + mapIconColor + '"%20width%3D"10.261"%20height%3D"10.185"%2F><rect%20x%3D"-126.477"%20y%3D"31.766"%20fill%3D"%23' + mapIconColor + '"%20width%3D"0.522"%20height%3D"2.337"%2F><rect%20x%3D"-116.812"%20y%3D"31.766"%20fill%3D"%23' + mapIconColor + '"%20width%3D"0.523"%20height%3D"2.337"%2F><rect%20x%3D"-127"%20y%3D"32.337"%20fill%3D"%23' + mapIconColor + '"%20width%3D"11.233"%20height%3D"0.572"%2F><g><rect%20x%3D"-83.805"%20y%3D"33.844"%20fill%3D"%23' + mapIconColor + '"%20width%3D"10.305"%20height%3D"10.156"%2F><rect%20x%3D"-76.809"%20y%3D"28.707"%20fill%3D"%23' + mapIconColor + '"%20width%3D"3.308"%20height%3D"3.261"%2F><%2Fg><rect%20x%3D"-178.5"%20y%3D"22.5"%20fill%3D"%23' + mapIconColor + '"%20stroke%3D"%23FFFFFF"%20stroke-miterlimit%3D"10"%20width%3D"30"%20height%3D"30"%2F><rect%20x%3D"-136.5"%20y%3D"22.5"%20fill%3D"%23' + mapIconColor + '"%20stroke%3D"%23FFFFFF"%20stroke-miterlimit%3D"10"%20width%3D"30"%20height%3D"30"%2F><rect%20x%3D"-93.5"%20y%3D"22.5"%20fill%3D"%23' + mapIconColor + '"%20stroke%3D"%23FFFFFF"%20stroke-miterlimit%3D"10"%20width%3D"30"%20height%3D"30"%2F><rect%20x%3D"-49.5"%20y%3D"22.5"%20fill%3D"%23' + mapIconColor + '"%20stroke%3D"%23FFFFFF"%20stroke-miterlimit%3D"10"%20width%3D"30"%20height%3D"30"%2F><%2Fg><g><path%20fill%3D"%23' + mapIconColor + '"%20d%3D"M49.898%2C11C35.48%2C11%2C23.795%2C22.688%2C23.795%2C37.105c0%2C4.137%2C0.986%2C8.035%2C2.699%2C11.51L49.898%2C83l23.402-34.385%20%20%20C75.014%2C45.14%2C76%2C41.242%2C76%2C37.105C76%2C22.688%2C64.314%2C11%2C49.898%2C11%20M50.222%2C50.535v0.08c-0.11-0.004-0.215-0.035-0.325-0.043%20%20%20c-0.108%2C0.008-0.213%2C0.039-0.322%2C0.043v-0.08c-6.025-0.484-10.782-5.459-10.782-11.609c0-6.152%2C4.757-11.127%2C10.782-11.611v-0.08%20%20%20c0.109%2C0.004%2C0.214%2C0.035%2C0.322%2C0.043c0.11-0.008%2C0.215-0.039%2C0.325-0.043v0.08c6.026%2C0.484%2C10.783%2C5.459%2C10.783%2C11.611%20%20%20C61.005%2C45.076%2C56.248%2C50.051%2C50.222%2C50.535"%2F><%2Fg><%2Fsvg>';
            var titleString = '<h4>' + data.markerTitle + '</h4>';
            var contentString = decodeURI(data.markerDescription);
            var mapDirections = '';

            if( parseInt(data.mapDirections) ){
                mapDirections = '<p><a href="https://maps.google.com?daddr=' + data.center.replace(' ', '+') + '" target="_blank">Get Directions</a>'
            }

            //put map into a global so that it can be re initialized
            ocbAllGoogleMaps.concat(map);

            if( data.marker == 'center' ) {
                var marker = new google.maps.Marker({
                    position: results[0].geometry.location,
                    map: map,
                    icon: mapIcon
                });
                if( data.markerInfoShow ){
                    var infowindow = new google.maps.InfoWindow({
                        content: titleString + contentString + mapDirections
                    });
                    infowindow.open(map, marker);
                } else {
                    marker.addListener('click', function() {
                        var infowindow = new google.maps.InfoWindow({
                            content: titleString + contentString + mapDirections
                        });
                        infowindow.open(map, marker);
                    });
                }
            } else if( data.marker == 'custom' ){
                for( var i = 0; i < data.markers.length; i++ ){
                    var theMarker = new google.maps.Marker({
                        position: results[0].geometry.location,
                        map: map,
                        icon: mapIcon
                    });
                }
            }
        } else {
            console.log('Geocode was not successful for the following reason: ' + status);
        }
    });
};

var ocbReInitSingleMapTimeout = null;

/**
 * Re-initialize single map - triggered as a callback by the settings panel
 */
var ocbReInitSingleMap = function(id){
    //Don't want to be constantly trying to re init
    clearTimeout(ocbReInitSingleMapTimeout);

    ocbReInitSingleMapTimeout = setTimeout(function(){
        var theWindow = ocbGetWindow();
        var theDocument = ocbGetDocument();
        var theMap = theDocument.getElementById(id + '-map');
        //var theMapClass = theMap.getElementsByClassName('ocb-map')[0];
        jQuery(theMap).html('');
        ocbInitSingleMap(theMap, theMap.dataset);
    }, 1000);
};

/********* MASONRY **********/
var ocbAllMasonry = {};

var ocbInitMasonry = function(){
    var theDoc = window.ocbGetDocument();
    var masonry = theDoc.getElementsByClassName('ocb-masonry-grid');

    for( var i = 0; i < masonry.length; i++ ){
        window.ocbInitSingleMasonry(masonry[i], masonry[i].dataset);
    }
};

var ocbReInitMasonry = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theDoc = ocbGetDocument( topWindow );
    var masonry = theDoc.getElementsByClassName('ocb-masonry-grid');

    for( var i = 0; i < masonry.length; i++ ){
        window.ocbReInitSingleMasonry(masonry[i], masonry[i].dataset, false );
    }
};

/**
 *
 * @param element
 * @param data
 * @param wait for the imageLoaded progress
 */
var ocbInitSingleMasonry = function(element, data, wait){
    if( wait == undefined ){
        wait = true;
    }

    var $grid = jQuery(element).masonry({
        itemSelector: '.ocb-masonry-item',
        percentPosition: true,
        columnWidth: '.grid-sizer'
    });

    if( wait ) {
        $grid.imagesLoaded().progress(function () {
            $grid.masonry();
        });
    } else {
        $grid.masonry('reloadItems')
    }

    window.ocbAllMasonry[data.id] = $grid;
};

var ocbReInitSingleMasonry = function(id){
    setTimeout(function(){
        var iframeJS = document.getElementById('ocb-workspace-iframe');
        var iframeDocument = iframeJS.contentDocument || iframeJS.contentWindow.document;
        var masonryWrap = iframeDocument.getElementById('module-' + id);
        if( masonryWrap == null ) return;
        var masonry = masonryWrap.getElementsByClassName('ocb-masonry-grid');
        let slideIndex = 0;
        if( iframeJS.contentWindow.ocbAllMasonry[id] != undefined ){
            //iframeJS.contentWindow.ocbAllMasonry[id].masonry('destroy');
        }
        if( window.ocbAllSliders[id] != undefined ){
            //window.ocbAllSliders[id].masonry('destroy');
        }
        iframeJS.contentWindow.ocbInitSingleMasonry(masonry[0], masonry[0].dataset, false);
    }, 1000);
};

/*var ocbInitializeMasonry = function(){
    var $grid = jQuery('.ocb-masonry-grid').masonry({
        itemSelector: '.ocb-masonry-item',
        percentPosition: true,
        columnWidth: '.grid-sizer'
    });
    // layout Masonry after each image loads
    $grid.imagesLoaded().progress( function() {
        $grid.masonry();
    });
};*/

var ocbChangeGalleryImage = function(element, previous){
    var id = jQuery(element).data('module-id');
    var index = jQuery(element).data('image-index');
    var $imageView = jQuery(element).closest('.ocb-image-view');
    var $imageGroup = jQuery('body').find('#' + id);
    var totalImages = $imageGroup.find('.ocb-gallery-item').length;
    var newIndex;

    if( previous ){
        newIndex = index - 1;
    } else {
        newIndex = index + 1;
    }

    if( newIndex < 0 ) newIndex = 0;
    if( newIndex == totalImages ) newIndex = newIndex - 1;

    var $newImage = $imageGroup.find('.ocb-gallery-item[data-image-index="' + newIndex + '"]');
    var newImageURL = $newImage.data('image-full');

    var $image = $imageView.find('img');
    $image.attr('src', newImageURL).hide();
    $imageView.imagesLoaded( function() {
        $image.fadeIn();
    });
    $imageView.find('.ocb-image-view-navigation').data('image-index', newIndex);
};

var ocbReplaceChildClassAdd = function(){
    jQuery('.ocb-child-class-add').each(function(){

        //Classes and child should have equal array lengths
        var classes = jQuery(this).data('ocb-classes');
        classes = classes != undefined ? classes.split('|') : [];

        var child = jQuery(this).data('ocb-child-target');
        child = child != undefined ? child.split('|') : [];

        if( classes.length != child.length ){
            console.log('ocb-child-class-add has unequal array lengths')
        }

        for( var i = 0; i < classes.length; i++ ){
            var j = 0;
            jQuery(this).find(child[i]).each(function(){
                var baseChildClasses = jQuery(this).data('ocb-remaining-class');
                if( baseChildClasses == undefined ) baseChildClasses = '';
                var finalClasses = baseChildClasses + ' ' + classes[i];
                jQuery(this).attr('class', finalClasses);
                j++;
            });
        }

    });
};
/*var ocbInitJarallax = function(){
    var iframeJS = document.getElementById('ocb-workspace-iframe');

    if (iframeJS == null) return;

    var iframeDocument = iframeJS.contentDocument || iframeJS.contentWindow.document;
    var parallax = iframeDocument.querySelectorAll('.jarallax');
    var imageBackgrounds = iframeDocument.querySelectorAll('.ocb-item-background-image');
    var videoBackgrounds = iframeDocument.querySelectorAll('.ocb-item-background-video');
    var gradientBackgrounds = iframeDocument.querySelectorAll('.ocb-item-background-gradient');
    var colorBackgrounds = iframeDocument.querySelectorAll('.ocb-item-background-color');

    if( iframeJS.contentWindow.jarallax == undefined || typeof iframeJS.contentWindow.jarallax != 'function' ) return;

    iframeJS.contentWindow.jarallax(parallax, 'destroy');
    iframeJS.contentWindow.jarallax(gradientBackgrounds, 'destroy');
    iframeJS.contentWindow.jarallax(colorBackgrounds, 'destroy');
    iframeJS.contentWindow.jarallax(imageBackgrounds, 'destroy');
    iframeJS.contentWindow.jarallax(videoBackgrounds, 'destroy');
    iframeJS.contentWindow.jarallax(parallax);
};*/
var ocbInitJarallax = function( topWindow ){
    if( topWindow == undefined ){
        topWindow = false;
    }

    var theDoc = ocbGetDocument( topWindow );
    var theWindow = ocbGetWindow( topWindow );

    var parallax = theDoc.querySelectorAll('.jarallax');
    var imageBackgrounds = theDoc.querySelectorAll('.ocb-item-background-image');
    var videoBackgrounds = theDoc.querySelectorAll('.ocb-item-background-video');
    var gradientBackgrounds = theDoc.querySelectorAll('.ocb-item-background-gradient');
    var colorBackgrounds = theDoc.querySelectorAll('.ocb-item-background-color');

    if( theWindow.jarallax == undefined || typeof theWindow.jarallax != 'function' ) return;

    theWindow.jarallax(parallax, 'destroy');
    theWindow.jarallax(gradientBackgrounds, 'destroy');
    theWindow.jarallax(colorBackgrounds, 'destroy');
    theWindow.jarallax(imageBackgrounds, 'destroy');
    theWindow.jarallax(videoBackgrounds, 'destroy');
    theWindow.jarallax(parallax);
};
var ocbReInitLayerJarallaxTimer = null;
var ocbReInitLayerJarallax = function(id){
    clearTimeout(ocbReInitLayerJarallaxTimer);
    ocbReInitLayerJarallaxTimer = setTimeout(function(){
        let fullId = 'row-' + id;
        if( id.startsWith('mod') ){
            fullId = 'module-' + id;
        } else if( id.startsWith('col') ){
            fullId = 'column-' + id;
        }
        var iframeJS = document.getElementById('ocb-workspace-iframe');
        var iframeDocument = iframeJS.contentDocument || iframeJS.contentWindow.document;
        var parallaxWrap = iframeDocument.getElementById(fullId);

        if( parallaxWrap == null ) return;

        //Since jarallax adds HTML to the dom that is not controlled by React, we need to remove this HTML on save
        // Therefore all jarallax has the ocb-save-remove-contents class
        var parallax = parallaxWrap.querySelectorAll('.jarallax');

        if( iframeJS.contentWindow.jarallax == undefined || typeof iframeJS.contentWindow.jarallax != 'function' ) return;

        //iframeJS.contentWindow.jarallax(parallax, 'destroy');
        iframeJS.contentWindow.jarallax(parallax, 'destroy');
        iframeJS.contentWindow.jarallax(parallax)
    }, 500, id)
};
var ocbReInitJarallax = function(id){
    console.log('ocbReInitJarallax');

    setTimeout(function(){
        let fullId = 'row-' + id;
        if( id.startsWith('mod') ){
            fullId = 'module-' + id;
        } else if( id.startsWith('col') ){
            fullId = 'column-' + id;
        }
        var iframeJS = document.getElementById('ocb-workspace-iframe');
        var iframeDocument = iframeJS.contentDocument || iframeJS.contentWindow.document;
        var parallaxWrap = iframeDocument.getElementById(fullId);

        if( parallaxWrap == null ) return;

        //Since jarallax adds HTML to the dom that is not controlled by React, we need to remove this HTML on save
        // Therefore all jarallax has the ocb-save-remove-contents class
        var parallax = parallaxWrap.querySelectorAll('.ocb-item-background > .ocb-save-remove-contents');

        //Also need to find .ocb-item-background-image items that are no longer jarallax and destroy those
        // so that background image size and positioning work
        var imageBackgrounds = parallaxWrap.querySelectorAll('.ocb-item-background-image');
        var videoBackgrounds = parallaxWrap.querySelectorAll('.ocb-item-background-video');
        var gradientBackgrounds = parallaxWrap.querySelectorAll('.ocb-item-background-gradient');
        var colorBackgrounds = parallaxWrap.querySelectorAll('.ocb-item-background-color');

        if( iframeJS.contentWindow.jarallax == undefined || typeof iframeJS.contentWindow.jarallax != 'function' ) return;

        //iframeJS.contentWindow.jarallax(parallax, 'destroy');
        iframeJS.contentWindow.jarallax(parallax, 'destroy');
        iframeJS.contentWindow.jarallax(gradientBackgrounds, 'destroy');
        iframeJS.contentWindow.jarallax(colorBackgrounds, 'destroy');
        iframeJS.contentWindow.jarallax(imageBackgrounds, 'destroy');
        iframeJS.contentWindow.jarallax(videoBackgrounds, 'destroy');
        iframeJS.contentWindow.jarallax(parallax)
    }, 500, id)
};

var ocbUpdateURLParameter = function(url, param, paramVal){
    var TheAnchor = null;
    var newAdditionalURL = "";
    var tempArray = url.split("?");
    var baseURL = tempArray[0];
    var additionalURL = tempArray[1];
    var temp = "";

    if (additionalURL)
    {
        var tmpAnchor = additionalURL.split("#");
        var TheParams = tmpAnchor[0];
        TheAnchor = tmpAnchor[1];
        if(TheAnchor)
            additionalURL = TheParams;

        tempArray = additionalURL.split("&");

        for (var i=0; i<tempArray.length; i++)
        {
            if(tempArray[i].split('=')[0] != param)
            {
                newAdditionalURL += temp + tempArray[i];
                temp = "&";
            }
        }
    }
    else
    {
        var tmpAnchor = baseURL.split("#");
        var TheParams = tmpAnchor[0];
        TheAnchor  = tmpAnchor[1];

        if(TheParams)
            baseURL = TheParams;
    }

    if(TheAnchor)
        paramVal += "#" + TheAnchor;

    var rows_txt = temp + "" + param + "=" + paramVal;
    return baseURL + "?" + newAdditionalURL + rows_txt;
};

jQuery( document ).ready(function( $ ) {
    var $body = $('body');

    $body.on('click', 'a[href*=\\#]', function() {
        if( this.hash.includes('#ftoc') ) {
            //Let TOC take care of it
        } else if( this.hash.includes('##') ){
            var $overlay = jQuery(this.hash.replace('##', '#'));
            if ( $overlay.length ) {
                $overlay.addClass('active');
                $overlay.find('.ocb-remove-overlay').remove();
                var $remove = jQuery('<div class="ocb-remove-overlay"><i class="fa fa-remove" /></div>');
                $remove.on('click', function(){
                    $overlay.removeClass('active');
                    jQuery('body').removeClass('ocb-overlay-active');
                });
                $overlay.append($remove);
                jQuery('body').addClass('ocb-overlay-active').append($overlay);
                return false;
            }
        } else if (location.hostname == this.hostname) {
            var $target = jQuery(this.hash);
            if ($target.length) {
                var targetOffset = $target.offset().top;
                jQuery('html,body').animate({scrollTop: targetOffset}, {duration:1000,easing:'easeInOutQuart'});
                return false;
            }
        }

    });

    //Active Tab
    if( window.location.hash ){
        var target = window.location.hash;
        var tabsObject = jQuery(window.location.hash).closest('.ocb-module-tabs');
        tabsObject.find('.ocb-tab-content-item').removeClass('active');
        tabsObject.find('.ocb-tab-nav-item').removeClass('active');
        tabsObject.find(target).addClass('active');
        tabsObject.find('.ocb-tab-nav-item[data-target="' + window.location.hash + '"]').addClass('active');
    }

    $body.on('click', '.ocb-tab-nav-item', function(){
        var target = $(this).data('target');
        var tabsObject = $(this).closest('.ocb-module-tabs');
        $(this).siblings('.ocb-tab-nav-item').removeClass('active');
        $(this).addClass('active');
        tabsObject.find('.ocb-tab-content-item').removeClass('active');
        tabsObject.find(target).addClass('active');
    });

    $body.on('click', '.ocb-accordion-nav-item', function(){
        var accordionObject = $(this).closest('.ocb-module-accordion');
        var target = $(this).data('target');
        if( $(this).hasClass('active') ){
            accordionObject.find(target).removeClass('active').slideUp();
            $(this).removeClass('active');
        } else {
            accordionObject.find('.ocb-accordion-nav-item').removeClass('active');
            $(this).addClass('active');
            accordionObject.find('.ocb-accordion-content-item').removeClass('active').slideUp();
            accordionObject.find(target).addClass('active').slideDown();
        }
    });

    $body.on('click', '.ocb-gallery-item', function(){
        var fullImageSrc = jQuery(this).data('image-full');
        var id = jQuery(this).closest('.ocb-the-module').attr('id');
        var index = jQuery(this).data('image-index');
        var selectedImage = jQuery('<div class="ocb-image-view">' +
            '<div class="ocb-image-view-close" onclick="jQuery(this).closest(\'.ocb-image-view\').remove()"><i class="fa fa-remove" /></div>' +
            '<div class="ocb-image-view-image">' +
                '<img src="' + fullImageSrc + '" />' +
            '</div>' +
            '<div class="ocb-image-view-navigation ocb-image-view-previous" data-image-index="' + index + '" data-module-id="' + id + '" onclick="ocbChangeGalleryImage(this, true)">' +
                '<i class="fa fa-chevron-left" />' +
            '</div>' +
            '<div class="ocb-image-view-navigation ocb-image-view-next" data-image-index="' + index + '" data-module-id="' + id + '" onclick="ocbChangeGalleryImage(this)">' +
                '<i class="fa fa-chevron-right" />' +
            '</div>' +
        '</div>');
        $body.append(selectedImage);
    });

    $body.on('click', '.ocb-video-modal-trigger', function(){
        var modal = jQuery(this).closest('.ocb-video-modal-container').find('.ocb-video-modal');
        var videoURL = jQuery(this).data('modal-url');
        var isUpload = jQuery(this).data('modal-upload');
        var modalClone = modal.clone();
        if( isUpload ){
            modalClone.find('.embed-container').append('<video autoplay><source src="' + videoURL + '" /></video>');
        } else {
            modalClone.find('.embed-container').append('<iframe src="' + videoURL + '" frameBorder="0" allowFullScreen></iframe>');
        }
        modalClone.appendTo('body').fadeIn();
    });

    $body.on('click', '.ocb-hide-parent', function(){
        jQuery(this).closest('.ocb-hide-parent-target').hide();
    });

    $body.on('click', '.ocb-remove-parent', function(){
        jQuery(this).closest('.ocb-remove-parent-target').remove();
    });

    var $wooQuantity = jQuery('.ocb-woo-add-to-cart-quantity');
    $wooQuantity.find('.qty').each(function(){
        var $thisWooQuantity = jQuery(this).closest('.ocb-woo-add-to-cart-quantity');
        jQuery(this).change(function(e){
            var newWooVal = e.target.value;
            var currentWooLink = $thisWooQuantity.find('a').attr('href');
            var newWooLink = ocbUpdateURLParameter(currentWooLink, 'quantity', newWooVal);
            $thisWooQuantity.find('a').attr( 'href', newWooLink );
        });
    });

    ocbReplaceChildClassAdd();
    jQuery('.ocb-menu-desktop').css({opacity:0}).addClass('ocb-menu-visible').animate({opacity:1});

    setTimeout(ocbInitializePlugins,1000);

    var myLazyLoad = new LazyLoad({
        elements_selector: ".ocb-lazy:not(.d-none)",
        container: document.querySelector('body'),
        callback_set: function(el){
            if( jQuery(el).is('video') && jQuery(el).hasClass('loading') ){
                jQuery(el).removeClass('loading');
                jQuery(el).addClass('loaded');
                el.load();

                if( jQuery(el).hasClass('ocb-video-autoplay') ){
                    setTimeout(function(){
                        var videoPromise = el.play();
                        videoPromise.then(function(){
                            // Autoplay started!
                        }).catch(function(error){
                            jQuery(el).attr('controls', 1);
                        });
                    }, 100);
                }
            }
        }
    });

});

/**
 * Regex matchAll from http://cwestblog.com/2013/02/26/javascript-string-prototype-matchall/
 * Used to grab all shortcode instances in text modules
 *
 * @param regexp
 * @returns {*}
 */
String.prototype.regexMatchAll = function(regexp) {
    var matches = [];
    this.replace(regexp, function() {
        var arr = ([]).slice.call(arguments, 0);
        var extras = arr.splice(-2);
        arr.index = extras[0];
        arr.input = extras[1];
        matches.push(arr);
    });
    return matches.length ? matches : null;
};

/**
 * From http://stackoverflow.com/questions/1144783/how-to-replace-all-occurrences-of-a-string-in-javascript
 * @param search
 * @param replacement
 * @returns {string}
 */
String.prototype.replaceAll = function (find, replace) {
    var str = this;
    return str.replace(new RegExp(find.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'g'), replace);
};

/**
 * https://stackoverflow.com/questions/2332811/capitalize-words-in-string
 * @param lower
 * @returns {string}
 */
String.prototype.capitalize = function(lower) {
    return (lower ? this.toLowerCase() : this).replace(/(?:^|\s)\S/g, function(a) { return a.toUpperCase(); });
};


/**
 * https://tc39.github.io/ecma262/#sec-array.prototype.find
 * polyfill for array.find
 */
if (!Array.prototype.find) {
    Object.defineProperty(Array.prototype, 'find', {
        value: function(predicate) {
            // 1. Let O be ? ToObject(this value).
            if (this == null) {
                throw new TypeError('"this" is null or not defined');
            }

            var o = Object(this);

            // 2. Let len be ? ToLength(? Get(O, "length")).
            var len = o.length >>> 0;

            // 3. If IsCallable(predicate) is false, throw a TypeError exception.
            if (typeof predicate !== 'function') {
                throw new TypeError('predicate must be a function');
            }

            // 4. If thisArg was supplied, let T be thisArg; else let T be undefined.
            var thisArg = arguments[1];

            // 5. Let k be 0.
            var k = 0;

            // 6. Repeat, while k < len
            while (k < len) {
                // a. Let Pk be ! ToString(k).
                // b. Let kValue be ? Get(O, Pk).
                // c. Let testResult be ToBoolean(? Call(predicate, T, « kValue, k, O »)).
                // d. If testResult is true, return kValue.
                var kValue = o[k];
                if (predicate.call(thisArg, kValue, k, o)) {
                    return kValue;
                }
                // e. Increase k by 1.
                k++;
            }

            // 7. Return undefined.
            return undefined;
        }
    });
}

var ocbGoogleMapStyles = {
    subdued: [{"featureType":"all","elementType":"geometry.fill","stylers":[{"weight":"2.00"}]},{"featureType":"all","elementType":"geometry.stroke","stylers":[{"color":"#9c9c9c"}]},{"featureType":"all","elementType":"labels.text","stylers":[{"visibility":"on"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"landscape.man_made","elementType":"geometry.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#eeeeee"}]},{"featureType":"road","elementType":"labels.text.fill","stylers":[{"color":"#7b7b7b"}]},{"featureType":"road","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#46bcec"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#c8d7d4"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"color":"#070707"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"color":"#ffffff"}]}],
    flat: [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"administrative.country","elementType":"geometry.fill","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"labels.text.fill","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative.country","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"administrative.province","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"administrative.locality","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi.business","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45}]},{"featureType":"road","elementType":"geometry.stroke","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"simplified"}]},{"featureType":"road.highway","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"transit.station","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#485b77"},{"visibility":"on"}]}],
    ultraLight: [{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#ffffff"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]}],
    midnight: [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"color":"#ffffff"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"color":"#000000"},{"lightness":13}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#144b53"},{"lightness":14},{"weight":1.4}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#08304b"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#0c4152"},{"lightness":5}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#0b434f"},{"lightness":25}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#000000"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#0b3d51"},{"lightness":16}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"}]},{"featureType":"transit","elementType":"all","stylers":[{"color":"#146474"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#021019"}]}],
    code: [{"featureType":"all","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"all","elementType":"labels","stylers":[{"visibility":"off"},{"saturation":"-100"}]},{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#000000"},{"lightness":40},{"visibility":"off"}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"off"},{"color":"#000000"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#000000"},{"lightness":17},{"weight":1.2}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":20}]},{"featureType":"landscape","elementType":"geometry.fill","stylers":[{"color":"#4d6059"}]},{"featureType":"landscape","elementType":"geometry.stroke","stylers":[{"color":"#4d6059"}]},{"featureType":"landscape.natural","elementType":"geometry.fill","stylers":[{"color":"#4d6059"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"lightness":21}]},{"featureType":"poi","elementType":"geometry.fill","stylers":[{"color":"#4d6059"}]},{"featureType":"poi","elementType":"geometry.stroke","stylers":[{"color":"#4d6059"}]},{"featureType":"road","elementType":"geometry","stylers":[{"visibility":"on"},{"color":"#7f8d89"}]},{"featureType":"road","elementType":"geometry.fill","stylers":[{"color":"#7f8d89"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#7f8d89"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#7f8d89"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":18}]},{"featureType":"road.arterial","elementType":"geometry.fill","stylers":[{"color":"#7f8d89"}]},{"featureType":"road.arterial","elementType":"geometry.stroke","stylers":[{"color":"#7f8d89"}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":16}]},{"featureType":"road.local","elementType":"geometry.fill","stylers":[{"color":"#7f8d89"}]},{"featureType":"road.local","elementType":"geometry.stroke","stylers":[{"color":"#7f8d89"}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#000000"},{"lightness":19}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#2b3638"},{"visibility":"on"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#2b3638"},{"lightness":17}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#24282b"}]},{"featureType":"water","elementType":"geometry.stroke","stylers":[{"color":"#24282b"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels.text.fill","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels.text.stroke","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels.icon","stylers":[{"visibility":"off"}]}],
    mutedBlue: [{"featureType":"all","stylers":[{"saturation":0},{"hue":"#e7ecf0"}]},{"featureType":"road","stylers":[{"saturation":-70}]},{"featureType":"transit","stylers":[{"visibility":"off"}]},{"featureType":"poi","stylers":[{"visibility":"off"}]},{"featureType":"water","stylers":[{"visibility":"simplified"},{"saturation":-60}]}],
    lightDark: [{"featureType":"administrative","elementType":"labels.text.fill","stylers":[{"color":"#444444"}]},{"featureType":"administrative.land_parcel","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"landscape","elementType":"all","stylers":[{"color":"#f2f2f2"}]},{"featureType":"landscape.natural","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"on"},{"color":"#052366"},{"saturation":"-70"},{"lightness":"85"}]},{"featureType":"poi","elementType":"labels","stylers":[{"visibility":"simplified"},{"lightness":"-53"},{"weight":"1.00"},{"gamma":"0.98"}]},{"featureType":"poi","elementType":"labels.icon","stylers":[{"visibility":"simplified"}]},{"featureType":"road","elementType":"all","stylers":[{"saturation":-100},{"lightness":45},{"visibility":"on"}]},{"featureType":"road","elementType":"geometry","stylers":[{"saturation":"-18"}]},{"featureType":"road","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"road.highway","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"road.arterial","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"road.local","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"all","stylers":[{"color":"#57677a"},{"visibility":"on"}]}]
};

/*************************************************** THIRD PARTY PLUGINS ***************************************************************************/

/*!
 * Name    : Just Another Parallax [Jarallax]
 * Version : 1.10.4
 * Author  : nK <https://nkdev.info>
 * GitHub  : https://github.com/nk-o/jarallax
 */!function(o){var n={};function i(e){if(n[e])return n[e].exports;var t=n[e]={i:e,l:!1,exports:{}};return o[e].call(t.exports,t,t.exports,i),t.l=!0,t.exports}i.m=o,i.c=n,i.d=function(e,t,o){i.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},i.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},i.t=function(t,e){if(1&e&&(t=i(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var o=Object.create(null);if(i.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var n in t)i.d(o,n,function(e){return t[e]}.bind(null,n));return o},i.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return i.d(t,"a",t),t},i.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},i.p="",i(i.s=11)}([,,function(e,t,o){"use strict";e.exports=function(e){"complete"===document.readyState||"interactive"===document.readyState?e.call():document.attachEvent?document.attachEvent("onreadystatechange",function(){"interactive"===document.readyState&&e.call()}):document.addEventListener&&document.addEventListener("DOMContentLoaded",e)}},,function(o,e,t){"use strict";(function(e){var t;t="undefined"!=typeof window?window:void 0!==e?e:"undefined"!=typeof self?self:{},o.exports=t}).call(this,t(5))},function(e,t,o){"use strict";var n,i="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e};n=function(){return this}();try{n=n||Function("return this")()||(0,eval)("this")}catch(e){"object"===("undefined"==typeof window?"undefined":i(window))&&(n=window)}e.exports=n},,,,,,function(e,t,o){e.exports=o(12)},function(e,t,o){"use strict";var n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},i=l(o(2)),a=o(4),r=l(o(13));function l(e){return e&&e.__esModule?e:{default:e}}var s=a.window.jarallax;if(a.window.jarallax=r.default,a.window.jarallax.noConflict=function(){return a.window.jarallax=s,this},void 0!==a.jQuery){var c=function(){var e=arguments||[];Array.prototype.unshift.call(e,this);var t=r.default.apply(a.window,e);return"object"!==(void 0===t?"undefined":n(t))?t:this};c.constructor=r.default.constructor;var u=a.jQuery.fn.jarallax;a.jQuery.fn.jarallax=c,a.jQuery.fn.jarallax.noConflict=function(){return a.jQuery.fn.jarallax=u,this}}(0,i.default)(function(){(0,r.default)(document.querySelectorAll("[data-jarallax]"))})},function(e,j,S){"use strict";(function(e){Object.defineProperty(j,"__esModule",{value:!0});var d=function(e,t){if(Array.isArray(e))return e;if(Symbol.iterator in Object(e))return function(e,t){var o=[],n=!0,i=!1,a=void 0;try{for(var r,l=e[Symbol.iterator]();!(n=(r=l.next()).done)&&(o.push(r.value),!t||o.length!==t);n=!0);}catch(e){i=!0,a=e}finally{try{!n&&l.return&&l.return()}finally{if(i)throw a}}return o}(e,t);throw new TypeError("Invalid attempt to destructure non-iterable instance")},t=function(){function n(e,t){for(var o=0;o<t.length;o++){var n=t[o];n.enumerable=n.enumerable||!1,n.configurable=!0,"value"in n&&(n.writable=!0),Object.defineProperty(e,n.key,n)}}return function(e,t,o){return t&&n(e.prototype,t),o&&n(e,o),e}}(),p="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},o=a(S(2)),n=a(S(14)),i=S(4);function a(e){return e&&e.__esModule?e:{default:e}}var s=-1<navigator.userAgent.indexOf("MSIE ")||-1<navigator.userAgent.indexOf("Trident/")||-1<navigator.userAgent.indexOf("Edge/"),r=function(){for(var e="transform WebkitTransform MozTransform".split(" "),t=document.createElement("div"),o=0;o<e.length;o++)if(t&&void 0!==t.style[e[o]])return e[o];return!1}(),b=void 0,v=void 0,l=void 0,c=!1,u=!1;function m(e){b=i.window.innerWidth||document.documentElement.clientWidth,v=i.window.innerHeight||document.documentElement.clientHeight,"object"!==(void 0===e?"undefined":p(e))||"load"!==e.type&&"dom-loaded"!==e.type||(c=!0)}m(),i.window.addEventListener("resize",m),i.window.addEventListener("orientationchange",m),i.window.addEventListener("load",m),(0,o.default)(function(){m({type:"dom-loaded"})});var f=[],y=!1;function g(){if(f.length){l=void 0!==i.window.pageYOffset?i.window.pageYOffset:(document.documentElement||document.body.parentNode||document.body).scrollTop;var t=c||!y||y.width!==b||y.height!==v,o=u||t||!y||y.y!==l;u=c=!1,(t||o)&&(f.forEach(function(e){t&&e.onResize(),o&&e.onScroll()}),y={width:b,height:v,y:l}),(0,n.default)(g)}}var h=!!e.ResizeObserver&&new e.ResizeObserver(function(e){e&&e.length&&(0,n.default)(function(){e.forEach(function(e){e.target&&e.target.jarallax&&(c||e.target.jarallax.onResize(),u=!0)})})}),x=0,w=function(){function u(e,t){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,u);var o=this;o.instanceID=x++,o.$item=e,o.defaults={type:"scroll",speed:.5,imgSrc:null,imgElement:".jarallax-img",imgSize:"cover",imgPosition:"50% 50%",imgRepeat:"no-repeat",keepImg:!1,elementInViewport:null,zIndex:-100,disableParallax:!1,disableVideo:!1,automaticResize:!0,videoSrc:null,videoStartTime:0,videoEndTime:0,videoVolume:0,videoPlayOnlyVisible:!0,onScroll:null,onInit:null,onDestroy:null,onCoverImage:null};var n=o.$item.getAttribute("data-jarallax"),i=JSON.parse(n||"{}");n&&console.warn("Detected usage of deprecated data-jarallax JSON options, you should use pure data-attribute options. See info here - https://github.com/nk-o/jarallax/issues/53");var a=o.$item.dataset||{},r={};if(Object.keys(a).forEach(function(e){var t=e.substr(0,1).toLowerCase()+e.substr(1);t&&void 0!==o.defaults[t]&&(r[t]=a[e])}),o.options=o.extend({},o.defaults,i,r,t),o.pureOptions=o.extend({},o.options),Object.keys(o.options).forEach(function(e){"true"===o.options[e]?o.options[e]=!0:"false"===o.options[e]&&(o.options[e]=!1)}),o.options.speed=Math.min(2,Math.max(-1,parseFloat(o.options.speed))),(o.options.noAndroid||o.options.noIos)&&(console.warn("Detected usage of deprecated noAndroid or noIos options, you should use disableParallax option. See info here - https://github.com/nk-o/jarallax/#disable-on-mobile-devices"),o.options.disableParallax||(o.options.noIos&&o.options.noAndroid?o.options.disableParallax=/iPad|iPhone|iPod|Android/:o.options.noIos?o.options.disableParallax=/iPad|iPhone|iPod/:o.options.noAndroid&&(o.options.disableParallax=/Android/))),"string"==typeof o.options.disableParallax&&(o.options.disableParallax=new RegExp(o.options.disableParallax)),o.options.disableParallax instanceof RegExp){var l=o.options.disableParallax;o.options.disableParallax=function(){return l.test(navigator.userAgent)}}if("function"!=typeof o.options.disableParallax&&(o.options.disableParallax=function(){return!1}),"string"==typeof o.options.disableVideo&&(o.options.disableVideo=new RegExp(o.options.disableVideo)),o.options.disableVideo instanceof RegExp){var s=o.options.disableVideo;o.options.disableVideo=function(){return s.test(navigator.userAgent)}}"function"!=typeof o.options.disableVideo&&(o.options.disableVideo=function(){return!1});var c=o.options.elementInViewport;c&&"object"===(void 0===c?"undefined":p(c))&&void 0!==c.length&&(c=d(c,1)[0]);c instanceof Element||(c=null),o.options.elementInViewport=c,o.image={src:o.options.imgSrc||null,$container:null,useImgTag:!1,position:/iPad|iPhone|iPod|Android/.test(navigator.userAgent)?"absolute":"fixed"},o.initImg()&&o.canInitParallax()&&o.init()}return t(u,[{key:"css",value:function(t,o){return"string"==typeof o?i.window.getComputedStyle(t).getPropertyValue(o):(o.transform&&r&&(o[r]=o.transform),Object.keys(o).forEach(function(e){t.style[e]=o[e]}),t)}},{key:"extend",value:function(o){var n=arguments;return o=o||{},Object.keys(arguments).forEach(function(t){n[t]&&Object.keys(n[t]).forEach(function(e){o[e]=n[t][e]})}),o}},{key:"getWindowData",value:function(){return{width:b,height:v,y:l}}},{key:"initImg",value:function(){var e=this,t=e.options.imgElement;return t&&"string"==typeof t&&(t=e.$item.querySelector(t)),t instanceof Element||(t=null),t&&(e.options.keepImg?e.image.$item=t.cloneNode(!0):(e.image.$item=t,e.image.$itemParent=t.parentNode),e.image.useImgTag=!0),!!e.image.$item||(null===e.image.src&&(e.image.src=e.css(e.$item,"background-image").replace(/^url\(['"]?/g,"").replace(/['"]?\)$/g,"")),!(!e.image.src||"none"===e.image.src))}},{key:"canInitParallax",value:function(){return r&&!this.options.disableParallax()}},{key:"init",value:function(){var e=this,t={position:"absolute",top:0,left:0,width:"100%",height:"100%",overflow:"hidden",pointerEvents:"none"},o={};if(!e.options.keepImg){var n=e.$item.getAttribute("style");if(n&&e.$item.setAttribute("data-jarallax-original-styles",n),e.image.useImgTag){var i=e.image.$item.getAttribute("style");i&&e.image.$item.setAttribute("data-jarallax-original-styles",i)}}if("static"===e.css(e.$item,"position")&&e.css(e.$item,{position:"relative"}),"auto"===e.css(e.$item,"z-index")&&e.css(e.$item,{zIndex:0}),e.image.$container=document.createElement("div"),e.css(e.image.$container,t),e.css(e.image.$container,{"z-index":e.options.zIndex}),s&&e.css(e.image.$container,{opacity:.9999}),e.image.$container.setAttribute("id","jarallax-container-"+e.instanceID),e.$item.appendChild(e.image.$container),e.image.useImgTag?o=e.extend({"object-fit":e.options.imgSize,"object-position":e.options.imgPosition,"font-family":"object-fit: "+e.options.imgSize+"; object-position: "+e.options.imgPosition+";","max-width":"none"},t,o):(e.image.$item=document.createElement("div"),e.image.src&&(o=e.extend({"background-position":e.options.imgPosition,"background-size":e.options.imgSize,"background-repeat":e.options.imgRepeat,"background-image":'url("'+e.image.src+'")'},t,o))),"opacity"!==e.options.type&&"scale"!==e.options.type&&"scale-opacity"!==e.options.type&&1!==e.options.speed||(e.image.position="absolute"),"fixed"===e.image.position)for(var a=0,r=e.$item;null!==r&&r!==document&&0===a;){var l=e.css(r,"-webkit-transform")||e.css(r,"-moz-transform")||e.css(r,"transform");l&&"none"!==l&&(a=1,e.image.position="absolute"),r=r.parentNode}o.position=e.image.position,e.css(e.image.$item,o),e.image.$container.appendChild(e.image.$item),e.onResize(),e.onScroll(!0),e.options.automaticResize&&h&&h.observe(e.$item),e.options.onInit&&e.options.onInit.call(e),"none"!==e.css(e.$item,"background-image")&&e.css(e.$item,{"background-image":"none"}),e.addToParallaxList()}},{key:"addToParallaxList",value:function(){f.push(this),1===f.length&&g()}},{key:"removeFromParallaxList",value:function(){var o=this;f.forEach(function(e,t){e.instanceID===o.instanceID&&f.splice(t,1)})}},{key:"destroy",value:function(){var e=this;e.removeFromParallaxList();var t=e.$item.getAttribute("data-jarallax-original-styles");if(e.$item.removeAttribute("data-jarallax-original-styles"),t?e.$item.setAttribute("style",t):e.$item.removeAttribute("style"),e.image.useImgTag){var o=e.image.$item.getAttribute("data-jarallax-original-styles");e.image.$item.removeAttribute("data-jarallax-original-styles"),o?e.image.$item.setAttribute("style",t):e.image.$item.removeAttribute("style"),e.image.$itemParent&&e.image.$itemParent.appendChild(e.image.$item)}e.$clipStyles&&e.$clipStyles.parentNode.removeChild(e.$clipStyles),e.image.$container&&e.image.$container.parentNode.removeChild(e.image.$container),e.options.onDestroy&&e.options.onDestroy.call(e),delete e.$item.jarallax}},{key:"clipContainer",value:function(){if("fixed"===this.image.position){var e=this,t=e.image.$container.getBoundingClientRect(),o=t.width,n=t.height;if(!e.$clipStyles)e.$clipStyles=document.createElement("style"),e.$clipStyles.setAttribute("type","text/css"),e.$clipStyles.setAttribute("id","jarallax-clip-"+e.instanceID),(document.head||document.getElementsByTagName("head")[0]).appendChild(e.$clipStyles);var i="#jarallax-container-"+e.instanceID+" {\n           clip: rect(0 "+o+"px "+n+"px 0);\n           clip: rect(0, "+o+"px, "+n+"px, 0);\n        }";e.$clipStyles.styleSheet?e.$clipStyles.styleSheet.cssText=i:e.$clipStyles.innerHTML=i}}},{key:"coverImage",value:function(){var e=this,t=e.image.$container.getBoundingClientRect(),o=t.height,n=e.options.speed,i="scroll"===e.options.type||"scroll-opacity"===e.options.type,a=0,r=o,l=0;return i&&(a=n<0?n*Math.max(o,v):n*(o+v),1<n?r=Math.abs(a-v):n<0?r=a/n+Math.abs(a):r+=Math.abs(v-o)*(1-n),a/=2),e.parallaxScrollDistance=a,l=i?(v-r)/2:(o-r)/2,e.css(e.image.$item,{height:r+"px",marginTop:l+"px",left:"fixed"===e.image.position?t.left+"px":"0",width:t.width+"px"}),e.options.onCoverImage&&e.options.onCoverImage.call(e),{image:{height:r,marginTop:l},container:t}}},{key:"isVisible",value:function(){return this.isElementInViewport||!1}},{key:"onScroll",value:function(e){var t=this,o=t.$item.getBoundingClientRect(),n=o.top,i=o.height,a={},r=o;if(t.options.elementInViewport&&(r=t.options.elementInViewport.getBoundingClientRect()),t.isElementInViewport=0<=r.bottom&&0<=r.right&&r.top<=v&&r.left<=b,e||t.isElementInViewport){var l=Math.max(0,n),s=Math.max(0,i+n),c=Math.max(0,-n),u=Math.max(0,n+i-v),d=Math.max(0,i-(n+i-v)),p=Math.max(0,-n+v-i),m=1-2*(v-n)/(v+i),f=1;if(i<v?f=1-(c||u)/i:s<=v?f=s/v:d<=v&&(f=d/v),"opacity"!==t.options.type&&"scale-opacity"!==t.options.type&&"scroll-opacity"!==t.options.type||(a.transform="translate3d(0,0,0)",a.opacity=f),"scale"===t.options.type||"scale-opacity"===t.options.type){var y=1;t.options.speed<0?y-=t.options.speed*f:y+=t.options.speed*(1-f),a.transform="scale("+y+") translate3d(0,0,0)"}if("scroll"===t.options.type||"scroll-opacity"===t.options.type){var g=t.parallaxScrollDistance*m;"absolute"===t.image.position&&(g-=n),a.transform="translate3d(0,"+g+"px,0)"}t.css(t.image.$item,a),t.options.onScroll&&t.options.onScroll.call(t,{section:o,beforeTop:l,beforeTopEnd:s,afterTop:c,beforeBottom:u,beforeBottomEnd:d,afterBottom:p,visiblePercent:f,fromViewportCenter:m})}}},{key:"onResize",value:function(){this.coverImage(),this.clipContainer()}}]),u}(),$=function(e){("object"===("undefined"==typeof HTMLElement?"undefined":p(HTMLElement))?e instanceof HTMLElement:e&&"object"===(void 0===e?"undefined":p(e))&&null!==e&&1===e.nodeType&&"string"==typeof e.nodeName)&&(e=[e]);for(var t=arguments[1],o=Array.prototype.slice.call(arguments,2),n=e.length,i=0,a=void 0;i<n;i++)if("object"===(void 0===t?"undefined":p(t))||void 0===t?e[i].jarallax||(e[i].jarallax=new w(e[i],t)):e[i].jarallax&&(a=e[i].jarallax[t].apply(e[i].jarallax,o)),void 0!==a)return a;return e};$.constructor=w,j.default=$}).call(this,S(5))},function(e,t,o){"use strict";var n=o(4),i=n.requestAnimationFrame||n.webkitRequestAnimationFrame||n.mozRequestAnimationFrame||function(e){var t=+new Date,o=Math.max(0,16-(t-a)),n=setTimeout(e,o);return a=t,n},a=+new Date;var r=n.cancelAnimationFrame||n.webkitCancelAnimationFrame||n.mozCancelAnimationFrame||clearTimeout;Function.prototype.bind&&(i=i.bind(n),r=r.bind(n)),(e.exports=i).cancel=r}]);
//# sourceMappingURL=jarallax.min.js.map


/*!
 * Name    : Video Background Extension for Jarallax
 * Version : 1.0.1
 * Author  : nK <https://nkdev.info>
 * GitHub  : https://github.com/nk-o/jarallax
 */!function(o){var i={};function n(e){if(i[e])return i[e].exports;var t=i[e]={i:e,l:!1,exports:{}};return o[e].call(t.exports,t,t.exports,n),t.l=!0,t.exports}n.m=o,n.c=i,n.d=function(e,t,o){n.o(e,t)||Object.defineProperty(e,t,{enumerable:!0,get:o})},n.r=function(e){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(e,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(e,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"==typeof t&&t&&t.__esModule)return t;var o=Object.create(null);if(n.r(o),Object.defineProperty(o,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var i in t)n.d(o,i,function(e){return t[e]}.bind(null,i));return o},n.n=function(e){var t=e&&e.__esModule?function(){return e.default}:function(){return e};return n.d(t,"a",t),t},n.o=function(e,t){return Object.prototype.hasOwnProperty.call(e,t)},n.p="",n(n.s=6)}([,,function(e,t,o){"use strict";e.exports=function(e){"complete"===document.readyState||"interactive"===document.readyState?e.call():document.attachEvent?document.attachEvent("onreadystatechange",function(){"interactive"===document.readyState&&e.call()}):document.addEventListener&&document.addEventListener("DOMContentLoaded",e)}},,function(o,e,t){"use strict";(function(e){var t;t="undefined"!=typeof window?window:void 0!==e?e:"undefined"!=typeof self?self:{},o.exports=t}).call(this,t(5))},function(e,t,o){"use strict";var i,n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e};i=function(){return this}();try{i=i||Function("return this")()||(0,eval)("this")}catch(e){"object"===("undefined"==typeof window?"undefined":n(window))&&(i=window)}e.exports=i},function(e,t,o){e.exports=o(7)},function(e,t,o){"use strict";var i=l(o(8)),n=l(o(4)),a=l(o(2)),r=l(o(10));function l(e){return e&&e.__esModule?e:{default:e}}n.default.VideoWorker=n.default.VideoWorker||i.default,(0,r.default)(),(0,a.default)(function(){"undefined"!=typeof jarallax&&jarallax(document.querySelectorAll("[data-jarallax-video]"))})},function(e,t,o){"use strict";e.exports=o(9)},function(e,t,o){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var n="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(e){return typeof e}:function(e){return e&&"function"==typeof Symbol&&e.constructor===Symbol&&e!==Symbol.prototype?"symbol":typeof e},a=function(){function i(e,t){for(var o=0;o<t.length;o++){var i=t[o];i.enumerable=i.enumerable||!1,i.configurable=!0,"value"in i&&(i.writable=!0),Object.defineProperty(e,i.key,i)}}return function(e,t,o){return t&&i(e.prototype,t),o&&i(e,o),e}}();function i(){this._done=[],this._fail=[]}i.prototype={execute:function(e,t){var o=e.length;for(t=Array.prototype.slice.call(t);o--;)e[o].apply(null,t)},resolve:function(){this.execute(this._done,arguments)},reject:function(){this.execute(this._fail,arguments)},done:function(e){this._done.push(e)},fail:function(e){this._fail.push(e)}};var r=0,l=0,u=0,p=0,s=0,d=new i,y=new i,c=function(){function i(e,t){!function(e,t){if(!(e instanceof t))throw new TypeError("Cannot call a class as a function")}(this,i);var o=this;o.url=e,o.options_default={autoplay:!1,loop:!1,mute:!1,volume:100,showContols:!0,startTime:0,endTime:0},o.options=o.extend({},o.options_default,t),o.videoID=o.parseURL(e),o.videoID&&(o.ID=r++,o.loadAPI(),o.init())}return a(i,[{key:"extend",value:function(o){var i=arguments;return o=o||{},Object.keys(arguments).forEach(function(t){i[t]&&Object.keys(i[t]).forEach(function(e){o[e]=i[t][e]})}),o}},{key:"parseURL",value:function(e){var t,o,i,n,a,r=!(!(t=e.match(/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/))||11!==t[1].length)&&t[1],l=!(!(o=e.match(/https?:\/\/(?:www\.|player\.)?vimeo.com\/(?:channels\/(?:\w+\/)?|groups\/([^\/]*)\/videos\/|album\/(\d+)\/video\/|video\/|)(\d+)(?:$|\/|\?)/))||!o[3])&&o[3],u=(i=e.split(/,(?=mp4\:|webm\:|ogv\:|ogg\:)/),n={},a=0,i.forEach(function(e){var t=e.match(/^(mp4|webm|ogv|ogg)\:(.*)/);t&&t[1]&&t[2]&&(n["ogv"===t[1]?"ogg":t[1]]=t[2],a=1)}),!!a&&n);return r?(this.type="youtube",r):l?(this.type="vimeo",l):!!u&&(this.type="local",u)}},{key:"isValid",value:function(){return!!this.videoID}},{key:"on",value:function(e,t){this.userEventsList=this.userEventsList||[],(this.userEventsList[e]||(this.userEventsList[e]=[])).push(t)}},{key:"off",value:function(o,i){var n=this;this.userEventsList&&this.userEventsList[o]&&(i?this.userEventsList[o].forEach(function(e,t){e===i&&(n.userEventsList[o][t]=!1)}):delete this.userEventsList[o])}},{key:"fire",value:function(e){var t=this,o=[].slice.call(arguments,1);this.userEventsList&&void 0!==this.userEventsList[e]&&this.userEventsList[e].forEach(function(e){e&&e.apply(t,o)})}},{key:"play",value:function(e){var t=this;t.player&&("youtube"===t.type&&t.player.playVideo&&(void 0!==e&&t.player.seekTo(e||0),YT.PlayerState.PLAYING!==t.player.getPlayerState()&&t.player.playVideo()),"vimeo"===t.type&&(void 0!==e&&t.player.setCurrentTime(e),t.player.getPaused().then(function(e){e&&t.player.play()})),"local"===t.type&&(void 0!==e&&(t.player.currentTime=e),t.player.paused&&t.player.play()))}},{key:"pause",value:function(){var t=this;t.player&&("youtube"===t.type&&t.player.pauseVideo&&YT.PlayerState.PLAYING===t.player.getPlayerState()&&t.player.pauseVideo(),"vimeo"===t.type&&t.player.getPaused().then(function(e){e||t.player.pause()}),"local"===t.type&&(t.player.paused||t.player.pause()))}},{key:"mute",value:function(){var e=this;e.player&&("youtube"===e.type&&e.player.mute&&e.player.mute(),"vimeo"===e.type&&e.player.setVolume&&e.player.setVolume(0),"local"===e.type&&(e.$video.muted=!0))}},{key:"unmute",value:function(){var e=this;e.player&&("youtube"===e.type&&e.player.mute&&e.player.unMute(),"vimeo"===e.type&&e.player.setVolume&&e.player.setVolume(e.options.volume),"local"===e.type&&(e.$video.muted=!1))}},{key:"setVolume",value:function(){var e=0<arguments.length&&void 0!==arguments[0]&&arguments[0],t=this;t.player&&e&&("youtube"===t.type&&t.player.setVolume&&t.player.setVolume(e),"vimeo"===t.type&&t.player.setVolume&&t.player.setVolume(e),"local"===t.type&&(t.$video.volume=e/100))}},{key:"getVolume",value:function(t){var e=this;e.player?("youtube"===e.type&&e.player.getVolume&&t(e.player.getVolume()),"vimeo"===e.type&&e.player.getVolume&&e.player.getVolume().then(function(e){t(e)}),"local"===e.type&&t(100*e.$video.volume)):t(!1)}},{key:"getMuted",value:function(t){var e=this;e.player?("youtube"===e.type&&e.player.isMuted&&t(e.player.isMuted()),"vimeo"===e.type&&e.player.getVolume&&e.player.getVolume().then(function(e){t(!!e)}),"local"===e.type&&t(e.$video.muted)):t(null)}},{key:"getImageURL",value:function(t){var o=this;if(o.videoImage)t(o.videoImage);else{if("youtube"===o.type){var e=["maxresdefault","sddefault","hqdefault","0"],i=0,n=new Image;n.onload=function(){120!==(this.naturalWidth||this.width)||i===e.length-1?(o.videoImage="https://img.youtube.com/vi/"+o.videoID+"/"+e[i]+".jpg",t(o.videoImage)):(i++,this.src="https://img.youtube.com/vi/"+o.videoID+"/"+e[i]+".jpg")},n.src="https://img.youtube.com/vi/"+o.videoID+"/"+e[i]+".jpg"}if("vimeo"===o.type){var a=new XMLHttpRequest;a.open("GET","https://vimeo.com/api/v2/video/"+o.videoID+".json",!0),a.onreadystatechange=function(){if(4===this.readyState&&200<=this.status&&this.status<400){var e=JSON.parse(this.responseText);o.videoImage=e[0].thumbnail_large,t(o.videoImage)}},a.send(),a=null}}}},{key:"getIframe",value:function(e){this.getVideo(e)}},{key:"getVideo",value:function(l){var u=this;u.$video?l(u.$video):u.onAPIready(function(){var e=void 0;if(u.$video||((e=document.createElement("div")).style.display="none"),"youtube"===u.type){u.playerOptions={},u.playerOptions.videoId=u.videoID,u.playerOptions.playerVars={autohide:1,rel:0,autoplay:0,playsinline:1},u.options.showContols||(u.playerOptions.playerVars.iv_load_policy=3,u.playerOptions.playerVars.modestbranding=1,u.playerOptions.playerVars.controls=0,u.playerOptions.playerVars.showinfo=0,u.playerOptions.playerVars.disablekb=1);var t=void 0,o=void 0;u.playerOptions.events={onReady:function(t){u.options.mute?t.target.mute():u.options.volume&&t.target.setVolume(u.options.volume),u.options.autoplay&&u.play(u.options.startTime),u.fire("ready",t),setInterval(function(){u.getVolume(function(e){u.options.volume!==e&&(u.options.volume=e,u.fire("volumechange",t))})},150)},onStateChange:function(e){u.options.loop&&e.data===YT.PlayerState.ENDED&&u.play(u.options.startTime),t||e.data!==YT.PlayerState.PLAYING||(t=1,u.fire("started",e)),e.data===YT.PlayerState.PLAYING&&u.fire("play",e),e.data===YT.PlayerState.PAUSED&&u.fire("pause",e),e.data===YT.PlayerState.ENDED&&u.fire("ended",e),e.data===YT.PlayerState.PLAYING?o=setInterval(function(){u.fire("timeupdate",e),u.options.endTime&&u.player.getCurrentTime()>=u.options.endTime&&(u.options.loop?u.play(u.options.startTime):u.pause())},150):clearInterval(o)}};var i=!u.$video;if(i){var n=document.createElement("div");n.setAttribute("id",u.playerID),e.appendChild(n),document.body.appendChild(e)}u.player=u.player||new window.YT.Player(u.playerID,u.playerOptions),i&&(u.$video=document.getElementById(u.playerID),u.videoWidth=parseInt(u.$video.getAttribute("width"),10)||1280,u.videoHeight=parseInt(u.$video.getAttribute("height"),10)||720)}if("vimeo"===u.type){u.playerOptions="",u.playerOptions+="player_id="+u.playerID,u.playerOptions+="&autopause=0",u.playerOptions+="&transparent=0",u.options.showContols||(u.playerOptions+="&badge=0&byline=0&portrait=0&title=0"),u.playerOptions+="&autoplay="+(u.options.autoplay?"1":"0"),u.playerOptions+="&loop="+(u.options.loop?1:0),u.$video||(u.$video=document.createElement("iframe"),u.$video.setAttribute("id",u.playerID),u.$video.setAttribute("src","https://player.vimeo.com/video/"+u.videoID+"?"+u.playerOptions),u.$video.setAttribute("frameborder","0"),e.appendChild(u.$video),document.body.appendChild(e)),u.player=u.player||new Vimeo.Player(u.$video),u.player.getVideoWidth().then(function(e){u.videoWidth=e||1280}),u.player.getVideoHeight().then(function(e){u.videoHeight=e||720}),u.options.startTime&&u.options.autoplay&&u.player.setCurrentTime(u.options.startTime),u.options.mute?u.player.setVolume(0):u.options.volume&&u.player.setVolume(u.options.volume);var a=void 0;u.player.on("timeupdate",function(e){a||(u.fire("started",e),a=1),u.fire("timeupdate",e),u.options.endTime&&u.options.endTime&&e.seconds>=u.options.endTime&&(u.options.loop?u.play(u.options.startTime):u.pause())}),u.player.on("play",function(e){u.fire("play",e),u.options.startTime&&0===e.seconds&&u.play(u.options.startTime)}),u.player.on("pause",function(e){u.fire("pause",e)}),u.player.on("ended",function(e){u.fire("ended",e)}),u.player.on("loaded",function(e){u.fire("ready",e)}),u.player.on("volumechange",function(e){u.fire("volumechange",e)})}if("local"===u.type){u.$video||(u.$video=document.createElement("video"),u.options.showContols&&(u.$video.controls=!0),u.options.mute?u.$video.muted=!0:u.$video.volume&&(u.$video.volume=u.options.volume/100),u.options.loop&&(u.$video.loop=!0),u.$video.setAttribute("playsinline",""),u.$video.setAttribute("webkit-playsinline",""),u.$video.setAttribute("id",u.playerID),e.appendChild(u.$video),document.body.appendChild(e),Object.keys(u.videoID).forEach(function(e){var t,o,i,n;t=u.$video,o=u.videoID[e],i="video/"+e,(n=document.createElement("source")).src=o,n.type=i,t.appendChild(n)})),u.player=u.player||u.$video;var r=void 0;u.player.addEventListener("playing",function(e){r||u.fire("started",e),r=1}),u.player.addEventListener("timeupdate",function(e){u.fire("timeupdate",e),u.options.endTime&&u.options.endTime&&this.currentTime>=u.options.endTime&&(u.options.loop?u.play(u.options.startTime):u.pause())}),u.player.addEventListener("play",function(e){u.fire("play",e)}),u.player.addEventListener("pause",function(e){u.fire("pause",e)}),u.player.addEventListener("ended",function(e){u.fire("ended",e)}),u.player.addEventListener("loadedmetadata",function(){u.videoWidth=this.videoWidth||1280,u.videoHeight=this.videoHeight||720,u.fire("ready"),u.options.autoplay&&u.play(u.options.startTime)}),u.player.addEventListener("volumechange",function(e){u.getVolume(function(e){u.options.volume=e}),u.fire("volumechange",e)})}l(u.$video)})}},{key:"init",value:function(){this.playerID="VideoWorker-"+this.ID}},{key:"loadAPI",value:function(){if(!l||!u){var e="";if("youtube"!==this.type||l||(l=1,e="https://www.youtube.com/iframe_api"),"vimeo"!==this.type||u||(u=1,e="https://player.vimeo.com/api/player.js"),e){var t=document.createElement("script"),o=document.getElementsByTagName("head")[0];t.src=e,o.appendChild(t),t=o=null}}}},{key:"onAPIready",value:function(e){if("youtube"===this.type&&("undefined"!=typeof YT&&0!==YT.loaded||p?"object"===("undefined"==typeof YT?"undefined":n(YT))&&1===YT.loaded?e():d.done(function(){e()}):(p=1,window.onYouTubeIframeAPIReady=function(){window.onYouTubeIframeAPIReady=null,d.resolve("done"),e()})),"vimeo"===this.type)if("undefined"!=typeof Vimeo||s)"undefined"!=typeof Vimeo?e():y.done(function(){e()});else{s=1;var t=setInterval(function(){"undefined"!=typeof Vimeo&&(clearInterval(t),y.resolve("done"),e())},20)}"local"===this.type&&e()}}]),i}();t.default=c},function(e,t,o){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(){var e=0<arguments.length&&void 0!==arguments[0]?arguments[0]:u.default.jarallax;if(void 0===e)return;var t=e.constructor,i=t.prototype.init;t.prototype.init=function(){var o=this;i.apply(o),o.video&&!o.options.disableVideo()&&o.video.getVideo(function(e){var t=e.parentNode;o.css(e,{position:o.image.position,top:"0px",left:"0px",right:"0px",bottom:"0px",width:"100%",height:"100%",maxWidth:"none",maxHeight:"none",margin:0,zIndex:-1}),o.$video=e,o.image.$container.appendChild(e),t.parentNode.removeChild(t)})};var l=t.prototype.coverImage;t.prototype.coverImage=function(){var e=this,t=l.apply(e),o=!!e.image.$item&&e.image.$item.nodeName;if(t&&e.video&&o&&("IFRAME"===o||"VIDEO"===o)){var i=t.image.height,n=i*e.image.width/e.image.height,a=(t.container.width-n)/2,r=t.image.marginTop;t.container.width>n&&(n=t.container.width,i=n*e.image.height/e.image.width,a=0,r+=(t.image.height-i)/2),"IFRAME"===o&&(i+=400,r-=200),e.css(e.$video,{width:n+"px",marginLeft:a+"px",height:i+"px",marginTop:r+"px"})}return t};var o=t.prototype.initImg;t.prototype.initImg=function(){var e=this,t=o.apply(e);return e.options.videoSrc||(e.options.videoSrc=e.$item.getAttribute("data-jarallax-video")||null),e.options.videoSrc?(e.defaultInitImgResult=t,!0):t};var n=t.prototype.canInitParallax;t.prototype.canInitParallax=function(){var o=this,e=n.apply(o);if(!o.options.videoSrc)return e;var t=new r.default(o.options.videoSrc,{autoplay:!0,loop:!0,showContols:!1,startTime:o.options.videoStartTime||0,endTime:o.options.videoEndTime||0,mute:o.options.videoVolume?0:1,volume:o.options.videoVolume||0});if(t.isValid())if(e){if(t.on("ready",function(){if(o.options.videoPlayOnlyVisible){var e=o.onScroll;o.onScroll=function(){e.apply(o),o.isVisible()?t.play():t.pause()}}else t.play()}),t.on("started",function(){o.image.$default_item=o.image.$item,o.image.$item=o.$video,o.image.width=o.video.videoWidth||1280,o.image.height=o.video.videoHeight||720,o.options.imgWidth=o.image.width,o.options.imgHeight=o.image.height,o.coverImage(),o.clipContainer(),o.onScroll(),o.image.$default_item&&(o.image.$default_item.style.display="none")}),o.video=t,!o.defaultInitImgResult)return"local"!==t.type?(t.getImageURL(function(e){o.image.src=e,o.init()}),!1):(o.image.src="data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7",!0)}else o.defaultInitImgResult||t.getImageURL(function(e){var t=o.$item.getAttribute("style");t&&o.$item.setAttribute("data-jarallax-original-styles",t),o.css(o.$item,{"background-image":'url("'+e+'")',"background-position":"center","background-size":"cover"})});return e};var a=t.prototype.destroy;t.prototype.destroy=function(){var e=this;e.image.$default_item&&(e.image.$item=e.image.$default_item,delete e.image.$default_item),a.apply(e)}};var r=i(o(8)),u=i(o(4));function i(e){return e&&e.__esModule?e:{default:e}}}]);

/*!
 * Name    : Elements Extension for Jarallax
 * Version : 1.0.0
 * Author  : nK <https://nkdev.info>
 * GitHub  : https://github.com/nk-o/jarallax
 */!function(n){var o={};function r(t){if(o[t])return o[t].exports;var e=o[t]={i:t,l:!1,exports:{}};return n[t].call(e.exports,e,e.exports,r),e.l=!0,e.exports}r.m=n,r.c=o,r.d=function(t,e,n){r.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:n})},r.r=function(t){"undefined"!=typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},r.t=function(e,t){if(1&t&&(e=r(e)),8&t)return e;if(4&t&&"object"==typeof e&&e&&e.__esModule)return e;var n=Object.create(null);if(r.r(n),Object.defineProperty(n,"default",{enumerable:!0,value:e}),2&t&&"string"!=typeof e)for(var o in e)r.d(n,o,function(t){return e[t]}.bind(null,o));return n},r.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return r.d(e,"a",e),e},r.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},r.p="",r(r.s=0)}([function(t,e,n){t.exports=n(1)},function(t,e,n){"use strict";var o=r(n(2));function r(t){return t&&t.__esModule?t:{default:t}}(0,r(n(3)).default)(),(0,o.default)(function(){"undefined"!=typeof jarallax&&jarallax(document.querySelectorAll("[data-jarallax-element]"))})},function(t,e,n){"use strict";t.exports=function(t){"complete"===document.readyState||"interactive"===document.readyState?t.call():document.attachEvent?document.attachEvent("onreadystatechange",function(){"interactive"===document.readyState&&t.call()}):document.addEventListener&&document.addEventListener("DOMContentLoaded",t)}},function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=function(){var t=0<arguments.length&&void 0!==arguments[0]?arguments[0]:i.default.jarallax;if(void 0===t)return;var e=t.constructor;["initImg","canInitParallax","init","destroy","clipContainer","coverImage","isVisible","onScroll","onResize"].forEach(function(p){var f=e.prototype[p];e.prototype[p]=function(){var t=this,e=arguments||[];if("initImg"===p&&null!==t.$item.getAttribute("data-jarallax-element")&&(t.options.type="element",t.pureOptions.speed=t.$item.getAttribute("data-jarallax-element")||t.pureOptions.speed),"element"!==t.options.type)return f.apply(t,e);switch(t.pureOptions.threshold=t.$item.getAttribute("data-threshold")||"",p){case"init":var n=t.pureOptions.speed.split(" ");t.options.speed=t.pureOptions.speed||0,t.options.speedY=n[0]?parseFloat(n[0]):0,t.options.speedX=n[1]?parseFloat(n[1]):0;var o=t.pureOptions.threshold.split(" ");t.options.thresholdY=o[0]?parseFloat(o[0]):null,t.options.thresholdX=o[1]?parseFloat(o[1]):null;break;case"onResize":var r=t.css(t.$item,"transform");t.css(t.$item,{transform:""});var i=t.$item.getBoundingClientRect();t.itemData={width:i.width,height:i.height,y:i.top+t.getWindowData().y,x:i.left},t.css(t.$item,{transform:r});break;case"onScroll":var a=t.getWindowData(),s=(a.y+a.height/2-t.itemData.y-t.itemData.height/2)/(a.height/2),l=s*t.options.speedY,u=s*t.options.speedX,c=l,d=u;null!==t.options.thresholdY&&l>t.options.thresholdY&&(c=0),null!==t.options.thresholdX&&u>t.options.thresholdX&&(d=0),t.css(t.$item,{transform:"translate3d("+d+"px,"+c+"px,0)"});break;case"initImg":case"isVisible":case"clipContainer":case"coverImage":return!0}return f.apply(t,e)}})};var o,r=n(4),i=(o=r)&&o.__esModule?o:{default:o}},function(n,t,e){"use strict";(function(t){var e;e="undefined"!=typeof window?window:void 0!==t?t:"undefined"!=typeof self?self:{},n.exports=e}).call(this,e(5))},function(t,e,n){"use strict";var o,r="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t};o=function(){return this}();try{o=o||Function("return this")()||(0,eval)("this")}catch(t){"object"===("undefined"==typeof window?"undefined":r(window))&&(o=window)}t.exports=o}]);
//# sourceMappingURL=jarallax-element.min.js.map

/*!
 * imagesLoaded PACKAGED v4.1.3
 * JavaScript is all like "You images are done yet or what?"
 * MIT License
 */

!function(e,t){"function"==typeof define&&define.amd?define("ev-emitter/ev-emitter",t):"object"==typeof module&&module.exports?module.exports=t():e.EvEmitter=t()}("undefined"!=typeof window?window:this,function(){function e(){}var t=e.prototype;return t.on=function(e,t){if(e&&t){var i=this._events=this._events||{},n=i[e]=i[e]||[];return-1==n.indexOf(t)&&n.push(t),this}},t.once=function(e,t){if(e&&t){this.on(e,t);var i=this._onceEvents=this._onceEvents||{},n=i[e]=i[e]||{};return n[t]=!0,this}},t.off=function(e,t){var i=this._events&&this._events[e];if(i&&i.length){var n=i.indexOf(t);return-1!=n&&i.splice(n,1),this}},t.emitEvent=function(e,t){var i=this._events&&this._events[e];if(i&&i.length){var n=0,o=i[n];t=t||[];for(var r=this._onceEvents&&this._onceEvents[e];o;){var s=r&&r[o];s&&(this.off(e,o),delete r[o]),o.apply(this,t),n+=s?0:1,o=i[n]}return this}},t.allOff=t.removeAllListeners=function(){delete this._events,delete this._onceEvents},e}),function(e,t){"use strict";"function"==typeof define&&define.amd?define(["ev-emitter/ev-emitter"],function(i){return t(e,i)}):"object"==typeof module&&module.exports?module.exports=t(e,require("ev-emitter")):e.imagesLoaded=t(e,e.EvEmitter)}("undefined"!=typeof window?window:this,function(e,t){function i(e,t){for(var i in t)e[i]=t[i];return e}function n(e){var t=[];if(Array.isArray(e))t=e;else if("number"==typeof e.length)for(var i=0;i<e.length;i++)t.push(e[i]);else t.push(e);return t}function o(e,t,r){return this instanceof o?("string"==typeof e&&(e=document.querySelectorAll(e)),this.elements=n(e),this.options=i({},this.options),"function"==typeof t?r=t:i(this.options,t),r&&this.on("always",r),this.getImages(),h&&(this.jqDeferred=new h.Deferred),void setTimeout(function(){this.check()}.bind(this))):new o(e,t,r)}function r(e){this.img=e}function s(e,t){this.url=e,this.element=t,this.img=new Image}var h=e.jQuery,a=e.console;o.prototype=Object.create(t.prototype),o.prototype.options={},o.prototype.getImages=function(){this.images=[],this.elements.forEach(this.addElementImages,this)},o.prototype.addElementImages=function(e){"IMG"==e.nodeName&&this.addImage(e),this.options.background===!0&&this.addElementBackgroundImages(e);var t=e.nodeType;if(t&&d[t]){for(var i=e.querySelectorAll("img"),n=0;n<i.length;n++){var o=i[n];this.addImage(o)}if("string"==typeof this.options.background){var r=e.querySelectorAll(this.options.background);for(n=0;n<r.length;n++){var s=r[n];this.addElementBackgroundImages(s)}}}};var d={1:!0,9:!0,11:!0};return o.prototype.addElementBackgroundImages=function(e){var t=getComputedStyle(e);if(t)for(var i=/url\((['"])?(.*?)\1\)/gi,n=i.exec(t.backgroundImage);null!==n;){var o=n&&n[2];o&&this.addBackground(o,e),n=i.exec(t.backgroundImage)}},o.prototype.addImage=function(e){var t=new r(e);this.images.push(t)},o.prototype.addBackground=function(e,t){var i=new s(e,t);this.images.push(i)},o.prototype.check=function(){function e(e,i,n){setTimeout(function(){t.progress(e,i,n)})}var t=this;return this.progressedCount=0,this.hasAnyBroken=!1,this.images.length?void this.images.forEach(function(t){t.once("progress",e),t.check()}):void this.complete()},o.prototype.progress=function(e,t,i){this.progressedCount++,this.hasAnyBroken=this.hasAnyBroken||!e.isLoaded,this.emitEvent("progress",[this,e,t]),this.jqDeferred&&this.jqDeferred.notify&&this.jqDeferred.notify(this,e),this.progressedCount==this.images.length&&this.complete(),this.options.debug&&a&&a.log("progress: "+i,e,t)},o.prototype.complete=function(){var e=this.hasAnyBroken?"fail":"done";if(this.isComplete=!0,this.emitEvent(e,[this]),this.emitEvent("always",[this]),this.jqDeferred){var t=this.hasAnyBroken?"reject":"resolve";this.jqDeferred[t](this)}},r.prototype=Object.create(t.prototype),r.prototype.check=function(){var e=this.getIsImageComplete();return e?void this.confirm(0!==this.img.naturalWidth,"naturalWidth"):(this.proxyImage=new Image,this.proxyImage.addEventListener("load",this),this.proxyImage.addEventListener("error",this),this.img.addEventListener("load",this),this.img.addEventListener("error",this),void(this.proxyImage.src=this.img.src))},r.prototype.getIsImageComplete=function(){return this.img.complete&&void 0!==this.img.naturalWidth},r.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.img,t])},r.prototype.handleEvent=function(e){var t="on"+e.type;this[t]&&this[t](e)},r.prototype.onload=function(){this.confirm(!0,"onload"),this.unbindEvents()},r.prototype.onerror=function(){this.confirm(!1,"onerror"),this.unbindEvents()},r.prototype.unbindEvents=function(){this.proxyImage.removeEventListener("load",this),this.proxyImage.removeEventListener("error",this),this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype=Object.create(r.prototype),s.prototype.check=function(){this.img.addEventListener("load",this),this.img.addEventListener("error",this),this.img.src=this.url;var e=this.getIsImageComplete();e&&(this.confirm(0!==this.img.naturalWidth,"naturalWidth"),this.unbindEvents())},s.prototype.unbindEvents=function(){this.img.removeEventListener("load",this),this.img.removeEventListener("error",this)},s.prototype.confirm=function(e,t){this.isLoaded=e,this.emitEvent("progress",[this,this.element,t])},o.makeJQueryPlugin=function(t){t=t||e.jQuery,t&&(h=t,h.fn.imagesLoaded=function(e,t){var i=new o(this,e,t);return i.jqDeferred.promise(h(this))})},o.makeJQueryPlugin(),o});

// ProgressBar.js 1.0.1
// https://kimmobrunfeldt.github.io/progressbar.js
// License: MIT

!function(a){if("object"==typeof exports&&"undefined"!=typeof module)module.exports=a();else if("function"==typeof define&&define.amd)define([],a);else{var b;b="undefined"!=typeof window?window:"undefined"!=typeof global?global:"undefined"!=typeof self?self:this,b.ProgressBar=a()}}(function(){var a;return function b(a,c,d){function e(g,h){if(!c[g]){if(!a[g]){var i="function"==typeof require&&require;if(!h&&i)return i(g,!0);if(f)return f(g,!0);var j=new Error("Cannot find module '"+g+"'");throw j.code="MODULE_NOT_FOUND",j}var k=c[g]={exports:{}};a[g][0].call(k.exports,function(b){var c=a[g][1][b];return e(c?c:b)},k,k.exports,b,a,c,d)}return c[g].exports}for(var f="function"==typeof require&&require,g=0;g<d.length;g++)e(d[g]);return e}({1:[function(b,c,d){(function(){var b=this||Function("return this")(),e=function(){"use strict";function e(){}function f(a,b){var c;for(c in a)Object.hasOwnProperty.call(a,c)&&b(c)}function g(a,b){return f(b,function(c){a[c]=b[c]}),a}function h(a,b){f(b,function(c){"undefined"==typeof a[c]&&(a[c]=b[c])})}function i(a,b,c,d,e,f,g){var h,i,k,l=f>a?0:(a-f)/e;for(h in b)b.hasOwnProperty(h)&&(i=g[h],k="function"==typeof i?i:o[i],b[h]=j(c[h],d[h],k,l));return b}function j(a,b,c,d){return a+(b-a)*c(d)}function k(a,b){var c=n.prototype.filter,d=a._filterArgs;f(c,function(e){"undefined"!=typeof c[e][b]&&c[e][b].apply(a,d)})}function l(a,b,c,d,e,f,g,h,j,l,m){v=b+c+d,w=Math.min(m||u(),v),x=w>=v,y=d-(v-w),a.isPlaying()&&(x?(j(g,a._attachment,y),a.stop(!0)):(a._scheduleId=l(a._timeoutHandler,s),k(a,"beforeTween"),b+c>w?i(1,e,f,g,1,1,h):i(w,e,f,g,d,b+c,h),k(a,"afterTween"),j(e,a._attachment,y)))}function m(a,b){var c={},d=typeof b;return"string"===d||"function"===d?f(a,function(a){c[a]=b}):f(a,function(a){c[a]||(c[a]=b[a]||q)}),c}function n(a,b){this._currentState=a||{},this._configured=!1,this._scheduleFunction=p,"undefined"!=typeof b&&this.setConfig(b)}var o,p,q="linear",r=500,s=1e3/60,t=Date.now?Date.now:function(){return+new Date},u="undefined"!=typeof SHIFTY_DEBUG_NOW?SHIFTY_DEBUG_NOW:t;p="undefined"!=typeof window?window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.oRequestAnimationFrame||window.msRequestAnimationFrame||window.mozCancelRequestAnimationFrame&&window.mozRequestAnimationFrame||setTimeout:setTimeout;var v,w,x,y;return n.prototype.tween=function(a){return this._isTweening?this:(void 0===a&&this._configured||this.setConfig(a),this._timestamp=u(),this._start(this.get(),this._attachment),this.resume())},n.prototype.setConfig=function(a){a=a||{},this._configured=!0,this._attachment=a.attachment,this._pausedAtTime=null,this._scheduleId=null,this._delay=a.delay||0,this._start=a.start||e,this._step=a.step||e,this._finish=a.finish||e,this._duration=a.duration||r,this._currentState=g({},a.from)||this.get(),this._originalState=this.get(),this._targetState=g({},a.to)||this.get();var b=this;this._timeoutHandler=function(){l(b,b._timestamp,b._delay,b._duration,b._currentState,b._originalState,b._targetState,b._easing,b._step,b._scheduleFunction)};var c=this._currentState,d=this._targetState;return h(d,c),this._easing=m(c,a.easing||q),this._filterArgs=[c,this._originalState,d,this._easing],k(this,"tweenCreated"),this},n.prototype.get=function(){return g({},this._currentState)},n.prototype.set=function(a){this._currentState=a},n.prototype.pause=function(){return this._pausedAtTime=u(),this._isPaused=!0,this},n.prototype.resume=function(){return this._isPaused&&(this._timestamp+=u()-this._pausedAtTime),this._isPaused=!1,this._isTweening=!0,this._timeoutHandler(),this},n.prototype.seek=function(a){a=Math.max(a,0);var b=u();return this._timestamp+a===0?this:(this._timestamp=b-a,this.isPlaying()||(this._isTweening=!0,this._isPaused=!1,l(this,this._timestamp,this._delay,this._duration,this._currentState,this._originalState,this._targetState,this._easing,this._step,this._scheduleFunction,b),this.pause()),this)},n.prototype.stop=function(a){return this._isTweening=!1,this._isPaused=!1,this._timeoutHandler=e,(b.cancelAnimationFrame||b.webkitCancelAnimationFrame||b.oCancelAnimationFrame||b.msCancelAnimationFrame||b.mozCancelRequestAnimationFrame||b.clearTimeout)(this._scheduleId),a&&(k(this,"beforeTween"),i(1,this._currentState,this._originalState,this._targetState,1,0,this._easing),k(this,"afterTween"),k(this,"afterTweenEnd"),this._finish.call(this,this._currentState,this._attachment)),this},n.prototype.isPlaying=function(){return this._isTweening&&!this._isPaused},n.prototype.setScheduleFunction=function(a){this._scheduleFunction=a},n.prototype.dispose=function(){var a;for(a in this)this.hasOwnProperty(a)&&delete this[a]},n.prototype.filter={},n.prototype.formula={linear:function(a){return a}},o=n.prototype.formula,g(n,{now:u,each:f,tweenProps:i,tweenProp:j,applyFilter:k,shallowCopy:g,defaults:h,composeEasingObject:m}),"function"==typeof SHIFTY_DEBUG_NOW&&(b.timeoutHandler=l),"object"==typeof d?c.exports=n:"function"==typeof a&&a.amd?a(function(){return n}):"undefined"==typeof b.Tweenable&&(b.Tweenable=n),n}();!function(){e.shallowCopy(e.prototype.formula,{easeInQuad:function(a){return Math.pow(a,2)},easeOutQuad:function(a){return-(Math.pow(a-1,2)-1)},easeInOutQuad:function(a){return(a/=.5)<1?.5*Math.pow(a,2):-.5*((a-=2)*a-2)},easeInCubic:function(a){return Math.pow(a,3)},easeOutCubic:function(a){return Math.pow(a-1,3)+1},easeInOutCubic:function(a){return(a/=.5)<1?.5*Math.pow(a,3):.5*(Math.pow(a-2,3)+2)},easeInQuart:function(a){return Math.pow(a,4)},easeOutQuart:function(a){return-(Math.pow(a-1,4)-1)},easeInOutQuart:function(a){return(a/=.5)<1?.5*Math.pow(a,4):-.5*((a-=2)*Math.pow(a,3)-2)},easeInQuint:function(a){return Math.pow(a,5)},easeOutQuint:function(a){return Math.pow(a-1,5)+1},easeInOutQuint:function(a){return(a/=.5)<1?.5*Math.pow(a,5):.5*(Math.pow(a-2,5)+2)},easeInSine:function(a){return-Math.cos(a*(Math.PI/2))+1},easeOutSine:function(a){return Math.sin(a*(Math.PI/2))},easeInOutSine:function(a){return-.5*(Math.cos(Math.PI*a)-1)},easeInExpo:function(a){return 0===a?0:Math.pow(2,10*(a-1))},easeOutExpo:function(a){return 1===a?1:-Math.pow(2,-10*a)+1},easeInOutExpo:function(a){return 0===a?0:1===a?1:(a/=.5)<1?.5*Math.pow(2,10*(a-1)):.5*(-Math.pow(2,-10*--a)+2)},easeInCirc:function(a){return-(Math.sqrt(1-a*a)-1)},easeOutCirc:function(a){return Math.sqrt(1-Math.pow(a-1,2))},easeInOutCirc:function(a){return(a/=.5)<1?-.5*(Math.sqrt(1-a*a)-1):.5*(Math.sqrt(1-(a-=2)*a)+1)},easeOutBounce:function(a){return 1/2.75>a?7.5625*a*a:2/2.75>a?7.5625*(a-=1.5/2.75)*a+.75:2.5/2.75>a?7.5625*(a-=2.25/2.75)*a+.9375:7.5625*(a-=2.625/2.75)*a+.984375},easeInBack:function(a){var b=1.70158;return a*a*((b+1)*a-b)},easeOutBack:function(a){var b=1.70158;return(a-=1)*a*((b+1)*a+b)+1},easeInOutBack:function(a){var b=1.70158;return(a/=.5)<1?.5*(a*a*(((b*=1.525)+1)*a-b)):.5*((a-=2)*a*(((b*=1.525)+1)*a+b)+2)},elastic:function(a){return-1*Math.pow(4,-8*a)*Math.sin((6*a-1)*(2*Math.PI)/2)+1},swingFromTo:function(a){var b=1.70158;return(a/=.5)<1?.5*(a*a*(((b*=1.525)+1)*a-b)):.5*((a-=2)*a*(((b*=1.525)+1)*a+b)+2)},swingFrom:function(a){var b=1.70158;return a*a*((b+1)*a-b)},swingTo:function(a){var b=1.70158;return(a-=1)*a*((b+1)*a+b)+1},bounce:function(a){return 1/2.75>a?7.5625*a*a:2/2.75>a?7.5625*(a-=1.5/2.75)*a+.75:2.5/2.75>a?7.5625*(a-=2.25/2.75)*a+.9375:7.5625*(a-=2.625/2.75)*a+.984375},bouncePast:function(a){return 1/2.75>a?7.5625*a*a:2/2.75>a?2-(7.5625*(a-=1.5/2.75)*a+.75):2.5/2.75>a?2-(7.5625*(a-=2.25/2.75)*a+.9375):2-(7.5625*(a-=2.625/2.75)*a+.984375)},easeFromTo:function(a){return(a/=.5)<1?.5*Math.pow(a,4):-.5*((a-=2)*Math.pow(a,3)-2)},easeFrom:function(a){return Math.pow(a,4)},easeTo:function(a){return Math.pow(a,.25)}})}(),function(){function a(a,b,c,d,e,f){function g(a){return((n*a+o)*a+p)*a}function h(a){return((q*a+r)*a+s)*a}function i(a){return(3*n*a+2*o)*a+p}function j(a){return 1/(200*a)}function k(a,b){return h(m(a,b))}function l(a){return a>=0?a:0-a}function m(a,b){var c,d,e,f,h,j;for(e=a,j=0;8>j;j++){if(f=g(e)-a,l(f)<b)return e;if(h=i(e),l(h)<1e-6)break;e-=f/h}if(c=0,d=1,e=a,c>e)return c;if(e>d)return d;for(;d>c;){if(f=g(e),l(f-a)<b)return e;a>f?c=e:d=e,e=.5*(d-c)+c}return e}var n=0,o=0,p=0,q=0,r=0,s=0;return p=3*b,o=3*(d-b)-p,n=1-p-o,s=3*c,r=3*(e-c)-s,q=1-s-r,k(a,j(f))}function b(b,c,d,e){return function(f){return a(f,b,c,d,e,1)}}e.setBezierFunction=function(a,c,d,f,g){var h=b(c,d,f,g);return h.displayName=a,h.x1=c,h.y1=d,h.x2=f,h.y2=g,e.prototype.formula[a]=h},e.unsetBezierFunction=function(a){delete e.prototype.formula[a]}}(),function(){function a(a,b,c,d,f,g){return e.tweenProps(d,b,a,c,1,g,f)}var b=new e;b._filterArgs=[],e.interpolate=function(c,d,f,g,h){var i=e.shallowCopy({},c),j=h||0,k=e.composeEasingObject(c,g||"linear");b.set({});var l=b._filterArgs;l.length=0,l[0]=i,l[1]=c,l[2]=d,l[3]=k,e.applyFilter(b,"tweenCreated"),e.applyFilter(b,"beforeTween");var m=a(c,i,d,f,k,j);return e.applyFilter(b,"afterTween"),m}}(),function(a){function b(a,b){var c,d=[],e=a.length;for(c=0;e>c;c++)d.push("_"+b+"_"+c);return d}function c(a){var b=a.match(v);return b?(1===b.length||a[0].match(u))&&b.unshift(""):b=["",""],b.join(A)}function d(b){a.each(b,function(a){var c=b[a];"string"==typeof c&&c.match(z)&&(b[a]=e(c))})}function e(a){return i(z,a,f)}function f(a){var b=g(a);return"rgb("+b[0]+","+b[1]+","+b[2]+")"}function g(a){return a=a.replace(/#/,""),3===a.length&&(a=a.split(""),a=a[0]+a[0]+a[1]+a[1]+a[2]+a[2]),B[0]=h(a.substr(0,2)),B[1]=h(a.substr(2,2)),B[2]=h(a.substr(4,2)),B}function h(a){return parseInt(a,16)}function i(a,b,c){var d=b.match(a),e=b.replace(a,A);if(d)for(var f,g=d.length,h=0;g>h;h++)f=d.shift(),e=e.replace(A,c(f));return e}function j(a){return i(x,a,k)}function k(a){for(var b=a.match(w),c=b.length,d=a.match(y)[0],e=0;c>e;e++)d+=parseInt(b[e],10)+",";return d=d.slice(0,-1)+")"}function l(d){var e={};return a.each(d,function(a){var f=d[a];if("string"==typeof f){var g=r(f);e[a]={formatString:c(f),chunkNames:b(g,a)}}}),e}function m(b,c){a.each(c,function(a){for(var d=b[a],e=r(d),f=e.length,g=0;f>g;g++)b[c[a].chunkNames[g]]=+e[g];delete b[a]})}function n(b,c){a.each(c,function(a){var d=b[a],e=o(b,c[a].chunkNames),f=p(e,c[a].chunkNames);d=q(c[a].formatString,f),b[a]=j(d)})}function o(a,b){for(var c,d={},e=b.length,f=0;e>f;f++)c=b[f],d[c]=a[c],delete a[c];return d}function p(a,b){C.length=0;for(var c=b.length,d=0;c>d;d++)C.push(a[b[d]]);return C}function q(a,b){for(var c=a,d=b.length,e=0;d>e;e++)c=c.replace(A,+b[e].toFixed(4));return c}function r(a){return a.match(w)}function s(b,c){a.each(c,function(a){var d,e=c[a],f=e.chunkNames,g=f.length,h=b[a];if("string"==typeof h){var i=h.split(" "),j=i[i.length-1];for(d=0;g>d;d++)b[f[d]]=i[d]||j}else for(d=0;g>d;d++)b[f[d]]=h;delete b[a]})}function t(b,c){a.each(c,function(a){var d=c[a],e=d.chunkNames,f=e.length,g=b[e[0]],h=typeof g;if("string"===h){for(var i="",j=0;f>j;j++)i+=" "+b[e[j]],delete b[e[j]];b[a]=i.substr(1)}else b[a]=g})}var u=/(\d|\-|\.)/,v=/([^\-0-9\.]+)/g,w=/[0-9.\-]+/g,x=new RegExp("rgb\\("+w.source+/,\s*/.source+w.source+/,\s*/.source+w.source+"\\)","g"),y=/^.*\(/,z=/#([0-9]|[a-f]){3,6}/gi,A="VAL",B=[],C=[];a.prototype.filter.token={tweenCreated:function(a,b,c,e){d(a),d(b),d(c),this._tokenData=l(a)},beforeTween:function(a,b,c,d){s(d,this._tokenData),m(a,this._tokenData),m(b,this._tokenData),m(c,this._tokenData)},afterTween:function(a,b,c,d){n(a,this._tokenData),n(b,this._tokenData),n(c,this._tokenData),t(d,this._tokenData)}}}(e)}).call(null)},{}],2:[function(a,b,c){var d=a("./shape"),e=a("./utils"),f=function(a,b){this._pathTemplate="M 50,50 m 0,-{radius} a {radius},{radius} 0 1 1 0,{2radius} a {radius},{radius} 0 1 1 0,-{2radius}",this.containerAspectRatio=1,d.apply(this,arguments)};f.prototype=new d,f.prototype.constructor=f,f.prototype._pathString=function(a){var b=a.strokeWidth;a.trailWidth&&a.trailWidth>a.strokeWidth&&(b=a.trailWidth);var c=50-b/2;return e.render(this._pathTemplate,{radius:c,"2radius":2*c})},f.prototype._trailString=function(a){return this._pathString(a)},b.exports=f},{"./shape":7,"./utils":8}],3:[function(a,b,c){var d=a("./shape"),e=a("./utils"),f=function(a,b){this._pathTemplate="M 0,{center} L 100,{center}",d.apply(this,arguments)};f.prototype=new d,f.prototype.constructor=f,f.prototype._initializeSvg=function(a,b){a.setAttribute("viewBox","0 0 100 "+b.strokeWidth),a.setAttribute("preserveAspectRatio","none")},f.prototype._pathString=function(a){return e.render(this._pathTemplate,{center:a.strokeWidth/2})},f.prototype._trailString=function(a){return this._pathString(a)},b.exports=f},{"./shape":7,"./utils":8}],4:[function(a,b,c){b.exports={Line:a("./line"),Circle:a("./circle"),SemiCircle:a("./semicircle"),Path:a("./path"),Shape:a("./shape"),utils:a("./utils")}},{"./circle":2,"./line":3,"./path":5,"./semicircle":6,"./shape":7,"./utils":8}],5:[function(a,b,c){var d=a("shifty"),e=a("./utils"),f={easeIn:"easeInCubic",easeOut:"easeOutCubic",easeInOut:"easeInOutCubic"},g=function h(a,b){if(!(this instanceof h))throw new Error("Constructor was called without new keyword");b=e.extend({duration:800,easing:"linear",from:{},to:{},step:function(){}},b);var c;c=e.isString(a)?document.querySelector(a):a,this.path=c,this._opts=b,this._tweenable=null;var d=this.path.getTotalLength();this.path.style.strokeDasharray=d+" "+d,this.set(0)};g.prototype.value=function(){var a=this._getComputedDashOffset(),b=this.path.getTotalLength(),c=1-a/b;return parseFloat(c.toFixed(6),10)},g.prototype.set=function(a){this.stop(),this.path.style.strokeDashoffset=this._progressToOffset(a);var b=this._opts.step;if(e.isFunction(b)){var c=this._easing(this._opts.easing),d=this._calculateTo(a,c),f=this._opts.shape||this;b(d,f,this._opts.attachment)}},g.prototype.stop=function(){this._stopTween(),this.path.style.strokeDashoffset=this._getComputedDashOffset()},g.prototype.animate=function(a,b,c){b=b||{},e.isFunction(b)&&(c=b,b={});var f=e.extend({},b),g=e.extend({},this._opts);b=e.extend(g,b);var h=this._easing(b.easing),i=this._resolveFromAndTo(a,h,f);this.stop(),this.path.getBoundingClientRect();var j=this._getComputedDashOffset(),k=this._progressToOffset(a),l=this;this._tweenable=new d,this._tweenable.tween({from:e.extend({offset:j},i.from),to:e.extend({offset:k},i.to),duration:b.duration,easing:h,step:function(a){l.path.style.strokeDashoffset=a.offset;var c=b.shape||l;b.step(a,c,b.attachment)},finish:function(a){e.isFunction(c)&&c()}})},g.prototype._getComputedDashOffset=function(){var a=window.getComputedStyle(this.path,null);return parseFloat(a.getPropertyValue("stroke-dashoffset"),10)},g.prototype._progressToOffset=function(a){var b=this.path.getTotalLength();return b-a*b},g.prototype._resolveFromAndTo=function(a,b,c){return c.from&&c.to?{from:c.from,to:c.to}:{from:this._calculateFrom(b),to:this._calculateTo(a,b)}},g.prototype._calculateFrom=function(a){return d.interpolate(this._opts.from,this._opts.to,this.value(),a)},g.prototype._calculateTo=function(a,b){return d.interpolate(this._opts.from,this._opts.to,a,b)},g.prototype._stopTween=function(){null!==this._tweenable&&(this._tweenable.stop(),this._tweenable=null)},g.prototype._easing=function(a){return f.hasOwnProperty(a)?f[a]:a},b.exports=g},{"./utils":8,shifty:1}],6:[function(a,b,c){var d=a("./shape"),e=a("./circle"),f=a("./utils"),g=function(a,b){this._pathTemplate="M 50,50 m -{radius},0 a {radius},{radius} 0 1 1 {2radius},0",this.containerAspectRatio=2,d.apply(this,arguments)};g.prototype=new d,g.prototype.constructor=g,g.prototype._initializeSvg=function(a,b){a.setAttribute("viewBox","0 0 100 50")},g.prototype._initializeTextContainer=function(a,b,c){a.text.style&&(c.style.top="auto",c.style.bottom="0",a.text.alignToBottom?f.setStyle(c,"transform","translate(-50%, 0)"):f.setStyle(c,"transform","translate(-50%, 50%)"))},g.prototype._pathString=e.prototype._pathString,g.prototype._trailString=e.prototype._trailString,b.exports=g},{"./circle":2,"./shape":7,"./utils":8}],7:[function(a,b,c){var d=a("./path"),e=a("./utils"),f="Object is destroyed",g=function h(a,b){if(!(this instanceof h))throw new Error("Constructor was called without new keyword");if(0!==arguments.length){this._opts=e.extend({color:"#555",strokeWidth:1,trailColor:null,trailWidth:null,fill:null,text:{style:{color:null,position:"absolute",left:"50%",top:"50%",padding:0,margin:0,transform:{prefix:!0,value:"translate(-50%, -50%)"}},autoStyleContainer:!0,alignToBottom:!0,value:null,className:"progressbar-text"},svgStyle:{display:"block",width:"100%"},warnings:!1},b,!0),e.isObject(b)&&void 0!==b.svgStyle&&(this._opts.svgStyle=b.svgStyle),e.isObject(b)&&e.isObject(b.text)&&void 0!==b.text.style&&(this._opts.text.style=b.text.style);var c,f=this._createSvgView(this._opts);if(c=e.isString(a)?document.querySelector(a):a,!c)throw new Error("Container does not exist: "+a);this._container=c,this._container.appendChild(f.svg),this._opts.warnings&&this._warnContainerAspectRatio(this._container),this._opts.svgStyle&&e.setStyles(f.svg,this._opts.svgStyle),this.svg=f.svg,this.path=f.path,this.trail=f.trail,this.text=null;var g=e.extend({attachment:void 0,shape:this},this._opts);this._progressPath=new d(f.path,g),e.isObject(this._opts.text)&&null!==this._opts.text.value&&this.setText(this._opts.text.value)}};g.prototype.animate=function(a,b,c){if(null===this._progressPath)throw new Error(f);this._progressPath.animate(a,b,c)},g.prototype.stop=function(){if(null===this._progressPath)throw new Error(f);void 0!==this._progressPath&&this._progressPath.stop()},g.prototype.destroy=function(){if(null===this._progressPath)throw new Error(f);this.stop(),this.svg.parentNode.removeChild(this.svg),this.svg=null,this.path=null,this.trail=null,this._progressPath=null,null!==this.text&&(this.text.parentNode.removeChild(this.text),this.text=null)},g.prototype.set=function(a){if(null===this._progressPath)throw new Error(f);this._progressPath.set(a)},g.prototype.value=function(){if(null===this._progressPath)throw new Error(f);return void 0===this._progressPath?0:this._progressPath.value()},g.prototype.setText=function(a){if(null===this._progressPath)throw new Error(f);null===this.text&&(this.text=this._createTextContainer(this._opts,this._container),this._container.appendChild(this.text)),e.isObject(a)?(e.removeChildren(this.text),this.text.appendChild(a)):this.text.innerHTML=a},g.prototype._createSvgView=function(a){var b=document.createElementNS("http://www.w3.org/2000/svg","svg");this._initializeSvg(b,a);var c=null;(a.trailColor||a.trailWidth)&&(c=this._createTrail(a),b.appendChild(c));var d=this._createPath(a);return b.appendChild(d),{svg:b,path:d,trail:c}},g.prototype._initializeSvg=function(a,b){a.setAttribute("viewBox","0 0 100 100")},g.prototype._createPath=function(a){var b=this._pathString(a);return this._createPathElement(b,a)},g.prototype._createTrail=function(a){var b=this._trailString(a),c=e.extend({},a);return c.trailColor||(c.trailColor="#eee"),c.trailWidth||(c.trailWidth=c.strokeWidth),c.color=c.trailColor,c.strokeWidth=c.trailWidth,c.fill=null,this._createPathElement(b,c)},g.prototype._createPathElement=function(a,b){var c=document.createElementNS("http://www.w3.org/2000/svg","path");return c.setAttribute("d",a),c.setAttribute("stroke",b.color),c.setAttribute("stroke-width",b.strokeWidth),b.fill?c.setAttribute("fill",b.fill):c.setAttribute("fill-opacity","0"),c},g.prototype._createTextContainer=function(a,b){var c=document.createElement("div");c.className=a.text.className;var d=a.text.style;return d&&(a.text.autoStyleContainer&&(b.style.position="relative"),e.setStyles(c,d),d.color||(c.style.color=a.color)),this._initializeTextContainer(a,b,c),c},g.prototype._initializeTextContainer=function(a,b,c){},g.prototype._pathString=function(a){throw new Error("Override this function for each progress bar")},g.prototype._trailString=function(a){throw new Error("Override this function for each progress bar")},g.prototype._warnContainerAspectRatio=function(a){if(this.containerAspectRatio){var b=window.getComputedStyle(a,null),c=parseFloat(b.getPropertyValue("width"),10),d=parseFloat(b.getPropertyValue("height"),10);e.floatEquals(this.containerAspectRatio,c/d)||(console.warn("Incorrect aspect ratio of container","#"+a.id,"detected:",b.getPropertyValue("width")+"(width)","/",b.getPropertyValue("height")+"(height)","=",c/d),console.warn("Aspect ratio of should be",this.containerAspectRatio))}},b.exports=g},{"./path":5,"./utils":8}],8:[function(a,b,c){function d(a,b,c){a=a||{},b=b||{},c=c||!1;for(var e in b)if(b.hasOwnProperty(e)){var f=a[e],g=b[e];c&&l(f)&&l(g)?a[e]=d(f,g,c):a[e]=g}return a}function e(a,b){var c=a;for(var d in b)if(b.hasOwnProperty(d)){var e=b[d],f="\\{"+d+"\\}",g=new RegExp(f,"g");c=c.replace(g,e)}return c}function f(a,b,c){for(var d=a.style,e=0;e<p.length;++e){var f=p[e];d[f+h(b)]=c}d[b]=c}function g(a,b){m(b,function(b,c){null!==b&&void 0!==b&&(l(b)&&b.prefix===!0?f(a,c,b.value):a.style[c]=b)})}function h(a){return a.charAt(0).toUpperCase()+a.slice(1)}function i(a){return"string"==typeof a||a instanceof String}function j(a){return"function"==typeof a}function k(a){return"[object Array]"===Object.prototype.toString.call(a)}function l(a){if(k(a))return!1;var b=typeof a;return"object"===b&&!!a}function m(a,b){for(var c in a)if(a.hasOwnProperty(c)){var d=a[c];b(d,c)}}function n(a,b){return Math.abs(a-b)<q}function o(a){for(;a.firstChild;)a.removeChild(a.firstChild)}var p="Webkit Moz O ms".split(" "),q=.001;b.exports={extend:d,render:e,setStyle:f,setStyles:g,capitalize:h,isString:i,isFunction:j,isObject:l,forEachObject:m,floatEquals:n,removeChildren:o}},{}]},{},[4])(4)});

/*! WOW - v1.1.3 - 2016-05-06
 * Copyright (c) 2016 Matthieu Aussaguel;*/(function(){var a,b,c,d,e,f=function(a,b){return function(){return a.apply(b,arguments)}},g=[].indexOf||function(a){for(var b=0,c=this.length;c>b;b++)if(b in this&&this[b]===a)return b;return-1};b=function(){function a(){}return a.prototype.extend=function(a,b){var c,d;for(c in b)d=b[c],null==a[c]&&(a[c]=d);return a},a.prototype.isMobile=function(a){return/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(a)},a.prototype.createEvent=function(a,b,c,d){var e;return null==b&&(b=!1),null==c&&(c=!1),null==d&&(d=null),null!=document.createEvent?(e=document.createEvent("CustomEvent"),e.initCustomEvent(a,b,c,d)):null!=document.createEventObject?(e=document.createEventObject(),e.eventType=a):e.eventName=a,e},a.prototype.emitEvent=function(a,b){return null!=a.dispatchEvent?a.dispatchEvent(b):b in(null!=a)?a[b]():"on"+b in(null!=a)?a["on"+b]():void 0},a.prototype.addEvent=function(a,b,c){return null!=a.addEventListener?a.addEventListener(b,c,!1):null!=a.attachEvent?a.attachEvent("on"+b,c):a[b]=c},a.prototype.removeEvent=function(a,b,c){return null!=a.removeEventListener?a.removeEventListener(b,c,!1):null!=a.detachEvent?a.detachEvent("on"+b,c):delete a[b]},a.prototype.innerHeight=function(){return"innerHeight"in window?window.innerHeight:document.documentElement.clientHeight},a}(),c=this.WeakMap||this.MozWeakMap||(c=function(){function a(){this.keys=[],this.values=[]}return a.prototype.get=function(a){var b,c,d,e,f;for(f=this.keys,b=d=0,e=f.length;e>d;b=++d)if(c=f[b],c===a)return this.values[b]},a.prototype.set=function(a,b){var c,d,e,f,g;for(g=this.keys,c=e=0,f=g.length;f>e;c=++e)if(d=g[c],d===a)return void(this.values[c]=b);return this.keys.push(a),this.values.push(b)},a}()),a=this.MutationObserver||this.WebkitMutationObserver||this.MozMutationObserver||(a=function(){function a(){"undefined"!=typeof console&&null!==console&&console.warn("MutationObserver is not supported by your browser."),"undefined"!=typeof console&&null!==console&&console.warn("WOW.js cannot detect dom mutations, please call .sync() after loading new content.")}return a.notSupported=!0,a.prototype.observe=function(){},a}()),d=this.getComputedStyle||function(a,b){return this.getPropertyValue=function(b){var c;return"float"===b&&(b="styleFloat"),e.test(b)&&b.replace(e,function(a,b){return b.toUpperCase()}),(null!=(c=a.currentStyle)?c[b]:void 0)||null},this},e=/(\-([a-z]){1})/g,this.WOW=function(){function e(a){null==a&&(a={}),this.scrollCallback=f(this.scrollCallback,this),this.scrollHandler=f(this.scrollHandler,this),this.resetAnimation=f(this.resetAnimation,this),this.start=f(this.start,this),this.scrolled=!0,this.config=this.util().extend(a,this.defaults),null!=a.scrollContainer&&(this.config.scrollContainer=document.querySelector(a.scrollContainer)),this.animationNameCache=new c,this.wowEvent=this.util().createEvent(this.config.boxClass)}return e.prototype.defaults={boxClass:"wow",animateClass:"animated",offset:0,mobile:!0,live:!0,callback:null,scrollContainer:null},e.prototype.init=function(){var a;return this.element=window.document.documentElement,"interactive"===(a=document.readyState)||"complete"===a?this.start():this.util().addEvent(document,"DOMContentLoaded",this.start),this.finished=[]},e.prototype.start=function(){var b,c,d,e;if(this.stopped=!1,this.boxes=function(){var a,c,d,e;for(d=this.element.querySelectorAll("."+this.config.boxClass),e=[],a=0,c=d.length;c>a;a++)b=d[a],e.push(b);return e}.call(this),this.all=function(){var a,c,d,e;for(d=this.boxes,e=[],a=0,c=d.length;c>a;a++)b=d[a],e.push(b);return e}.call(this),this.boxes.length)if(this.disabled())this.resetStyle();else for(e=this.boxes,c=0,d=e.length;d>c;c++)b=e[c],this.applyStyle(b,!0);return this.disabled()||(this.util().addEvent(this.config.scrollContainer||window,"scroll",this.scrollHandler),this.util().addEvent(window,"resize",this.scrollHandler),this.interval=setInterval(this.scrollCallback,50)),this.config.live?new a(function(a){return function(b){var c,d,e,f,g;for(g=[],c=0,d=b.length;d>c;c++)f=b[c],g.push(function(){var a,b,c,d;for(c=f.addedNodes||[],d=[],a=0,b=c.length;b>a;a++)e=c[a],d.push(this.doSync(e));return d}.call(a));return g}}(this)).observe(document.body,{childList:!0,subtree:!0}):void 0},e.prototype.stop=function(){return this.stopped=!0,this.util().removeEvent(this.config.scrollContainer||window,"scroll",this.scrollHandler),this.util().removeEvent(window,"resize",this.scrollHandler),null!=this.interval?clearInterval(this.interval):void 0},e.prototype.sync=function(b){return a.notSupported?this.doSync(this.element):void 0},e.prototype.doSync=function(a){var b,c,d,e,f;if(null==a&&(a=this.element),1===a.nodeType){for(a=a.parentNode||a,e=a.querySelectorAll("."+this.config.boxClass),f=[],c=0,d=e.length;d>c;c++)b=e[c],g.call(this.all,b)<0?(this.boxes.push(b),this.all.push(b),this.stopped||this.disabled()?this.resetStyle():this.applyStyle(b,!0),f.push(this.scrolled=!0)):f.push(void 0);return f}},e.prototype.show=function(a){return this.applyStyle(a),a.className=a.className+" "+this.config.animateClass,null!=this.config.callback&&this.config.callback(a),this.util().emitEvent(a,this.wowEvent),this.util().addEvent(a,"animationend",this.resetAnimation),this.util().addEvent(a,"oanimationend",this.resetAnimation),this.util().addEvent(a,"webkitAnimationEnd",this.resetAnimation),this.util().addEvent(a,"MSAnimationEnd",this.resetAnimation),a},e.prototype.applyStyle=function(a,b){var c,d,e;return d=a.getAttribute("data-wow-duration"),c=a.getAttribute("data-wow-delay"),e=a.getAttribute("data-wow-iteration"),this.animate(function(f){return function(){return f.customStyle(a,b,d,c,e)}}(this))},e.prototype.animate=function(){return"requestAnimationFrame"in window?function(a){return window.requestAnimationFrame(a)}:function(a){return a()}}(),e.prototype.resetStyle=function(){var a,b,c,d,e;for(d=this.boxes,e=[],b=0,c=d.length;c>b;b++)a=d[b],e.push(a.style.visibility="visible");return e},e.prototype.resetAnimation=function(a){var b;return a.type.toLowerCase().indexOf("animationend")>=0?(b=a.target||a.srcElement,b.className=b.className.replace(this.config.animateClass,"").trim()):void 0},e.prototype.customStyle=function(a,b,c,d,e){return b&&this.cacheAnimationName(a),a.style.visibility=b?"hidden":"visible",c&&this.vendorSet(a.style,{animationDuration:c}),d&&this.vendorSet(a.style,{animationDelay:d}),e&&this.vendorSet(a.style,{animationIterationCount:e}),this.vendorSet(a.style,{animationName:b?"none":this.cachedAnimationName(a)}),a},e.prototype.vendors=["moz","webkit"],e.prototype.vendorSet=function(a,b){var c,d,e,f;d=[];for(c in b)e=b[c],a[""+c]=e,d.push(function(){var b,d,g,h;for(g=this.vendors,h=[],b=0,d=g.length;d>b;b++)f=g[b],h.push(a[""+f+c.charAt(0).toUpperCase()+c.substr(1)]=e);return h}.call(this));return d},e.prototype.vendorCSS=function(a,b){var c,e,f,g,h,i;for(h=d(a),g=h.getPropertyCSSValue(b),f=this.vendors,c=0,e=f.length;e>c;c++)i=f[c],g=g||h.getPropertyCSSValue("-"+i+"-"+b);return g},e.prototype.animationName=function(a){var b;try{b=this.vendorCSS(a,"animation-name").cssText}catch(c){b=d(a).getPropertyValue("animation-name")}return"none"===b?"":b},e.prototype.cacheAnimationName=function(a){return this.animationNameCache.set(a,this.animationName(a))},e.prototype.cachedAnimationName=function(a){return this.animationNameCache.get(a)},e.prototype.scrollHandler=function(){return this.scrolled=!0},e.prototype.scrollCallback=function(){var a;return!this.scrolled||(this.scrolled=!1,this.boxes=function(){var b,c,d,e;for(d=this.boxes,e=[],b=0,c=d.length;c>b;b++)a=d[b],a&&(this.isVisible(a)?this.show(a):e.push(a));return e}.call(this),this.boxes.length||this.config.live)?void 0:this.stop()},e.prototype.offsetTop=function(a){for(var b;void 0===a.offsetTop;)a=a.parentNode;for(b=a.offsetTop;a=a.offsetParent;)b+=a.offsetTop;return b},e.prototype.isVisible=function(a){var b,c,d,e,f;return c=a.getAttribute("data-wow-offset")||this.config.offset,f=this.config.scrollContainer&&this.config.scrollContainer.scrollTop||window.pageYOffset,e=f+Math.min(this.element.clientHeight,this.util().innerHeight())-c,d=this.offsetTop(a),b=d+a.clientHeight,e>=d&&b>=f},e.prototype.util=function(){return null!=this._util?this._util:this._util=new b},e.prototype.disabled=function(){return!this.config.mobile&&this.util().isMobile(navigator.userAgent)},e}()}).call(this);

new WOW({
    offset: 60
}).init();

/*!
 Waypoints - 4.0.1
 Copyright © 2011-2016 Caleb Troughton
 Licensed under the MIT license.
 https://github.com/imakewebthings/waypoints/blob/master/licenses.txt
 */
!function(){"use strict";function t(o){if(!o)throw new Error("No options passed to Waypoint constructor");if(!o.element)throw new Error("No element option passed to Waypoint constructor");if(!o.handler)throw new Error("No handler option passed to Waypoint constructor");this.key="waypoint-"+e,this.options=t.Adapter.extend({},t.defaults,o),this.element=this.options.element,this.adapter=new t.Adapter(this.element),this.callback=o.handler,this.axis=this.options.horizontal?"horizontal":"vertical",this.enabled=this.options.enabled,this.triggerPoint=null,this.group=t.Group.findOrCreate({name:this.options.group,axis:this.axis}),this.context=t.Context.findOrCreateByElement(this.options.context),t.offsetAliases[this.options.offset]&&(this.options.offset=t.offsetAliases[this.options.offset]),this.group.add(this),this.context.add(this),i[this.key]=this,e+=1}var e=0,i={};t.prototype.queueTrigger=function(t){this.group.queueTrigger(this,t)},t.prototype.trigger=function(t){this.enabled&&this.callback&&this.callback.apply(this,t)},t.prototype.destroy=function(){this.context.remove(this),this.group.remove(this),delete i[this.key]},t.prototype.disable=function(){return this.enabled=!1,this},t.prototype.enable=function(){return this.context.refresh(),this.enabled=!0,this},t.prototype.next=function(){return this.group.next(this)},t.prototype.previous=function(){return this.group.previous(this)},t.invokeAll=function(t){var e=[];for(var o in i)e.push(i[o]);for(var n=0,r=e.length;r>n;n++)e[n][t]()},t.destroyAll=function(){t.invokeAll("destroy")},t.disableAll=function(){t.invokeAll("disable")},t.enableAll=function(){t.Context.refreshAll();for(var e in i)i[e].enabled=!0;return this},t.refreshAll=function(){t.Context.refreshAll()},t.viewportHeight=function(){return window.innerHeight||document.documentElement.clientHeight},t.viewportWidth=function(){return document.documentElement.clientWidth},t.adapters=[],t.defaults={context:window,continuous:!0,enabled:!0,group:"default",horizontal:!1,offset:0},t.offsetAliases={"bottom-in-view":function(){return this.context.innerHeight()-this.adapter.outerHeight()},"right-in-view":function(){return this.context.innerWidth()-this.adapter.outerWidth()}},window.Waypoint=t}(),function(){"use strict";function t(t){window.setTimeout(t,1e3/60)}function e(t){this.element=t,this.Adapter=n.Adapter,this.adapter=new this.Adapter(t),this.key="waypoint-context-"+i,this.didScroll=!1,this.didResize=!1,this.oldScroll={x:this.adapter.scrollLeft(),y:this.adapter.scrollTop()},this.waypoints={vertical:{},horizontal:{}},t.waypointContextKey=this.key,o[t.waypointContextKey]=this,i+=1,n.windowContext||(n.windowContext=!0,n.windowContext=new e(window)),this.createThrottledScrollHandler(),this.createThrottledResizeHandler()}var i=0,o={},n=window.Waypoint,r=window.onload;e.prototype.add=function(t){var e=t.options.horizontal?"horizontal":"vertical";this.waypoints[e][t.key]=t,this.refresh()},e.prototype.checkEmpty=function(){var t=this.Adapter.isEmptyObject(this.waypoints.horizontal),e=this.Adapter.isEmptyObject(this.waypoints.vertical),i=this.element==this.element.window;t&&e&&!i&&(this.adapter.off(".waypoints"),delete o[this.key])},e.prototype.createThrottledResizeHandler=function(){function t(){e.handleResize(),e.didResize=!1}var e=this;this.adapter.on("resize.waypoints",function(){e.didResize||(e.didResize=!0,n.requestAnimationFrame(t))})},e.prototype.createThrottledScrollHandler=function(){function t(){e.handleScroll(),e.didScroll=!1}var e=this;this.adapter.on("scroll.waypoints",function(){(!e.didScroll||n.isTouch)&&(e.didScroll=!0,n.requestAnimationFrame(t))})},e.prototype.handleResize=function(){n.Context.refreshAll()},e.prototype.handleScroll=function(){var t={},e={horizontal:{newScroll:this.adapter.scrollLeft(),oldScroll:this.oldScroll.x,forward:"right",backward:"left"},vertical:{newScroll:this.adapter.scrollTop(),oldScroll:this.oldScroll.y,forward:"down",backward:"up"}};for(var i in e){var o=e[i],n=o.newScroll>o.oldScroll,r=n?o.forward:o.backward;for(var s in this.waypoints[i]){var a=this.waypoints[i][s];if(null!==a.triggerPoint){var l=o.oldScroll<a.triggerPoint,h=o.newScroll>=a.triggerPoint,p=l&&h,u=!l&&!h;(p||u)&&(a.queueTrigger(r),t[a.group.id]=a.group)}}}for(var c in t)t[c].flushTriggers();this.oldScroll={x:e.horizontal.newScroll,y:e.vertical.newScroll}},e.prototype.innerHeight=function(){return this.element==this.element.window?n.viewportHeight():this.adapter.innerHeight()},e.prototype.remove=function(t){delete this.waypoints[t.axis][t.key],this.checkEmpty()},e.prototype.innerWidth=function(){return this.element==this.element.window?n.viewportWidth():this.adapter.innerWidth()},e.prototype.destroy=function(){var t=[];for(var e in this.waypoints)for(var i in this.waypoints[e])t.push(this.waypoints[e][i]);for(var o=0,n=t.length;n>o;o++)t[o].destroy()},e.prototype.refresh=function(){var t,e=this.element==this.element.window,i=e?void 0:this.adapter.offset(),o={};this.handleScroll(),t={horizontal:{contextOffset:e?0:i.left,contextScroll:e?0:this.oldScroll.x,contextDimension:this.innerWidth(),oldScroll:this.oldScroll.x,forward:"right",backward:"left",offsetProp:"left"},vertical:{contextOffset:e?0:i.top,contextScroll:e?0:this.oldScroll.y,contextDimension:this.innerHeight(),oldScroll:this.oldScroll.y,forward:"down",backward:"up",offsetProp:"top"}};for(var r in t){var s=t[r];for(var a in this.waypoints[r]){var l,h,p,u,c,d=this.waypoints[r][a],f=d.options.offset,w=d.triggerPoint,y=0,g=null==w;d.element!==d.element.window&&(y=d.adapter.offset()[s.offsetProp]),"function"==typeof f?f=f.apply(d):"string"==typeof f&&(f=parseFloat(f),d.options.offset.indexOf("%")>-1&&(f=Math.ceil(s.contextDimension*f/100))),l=s.contextScroll-s.contextOffset,d.triggerPoint=Math.floor(y+l-f),h=w<s.oldScroll,p=d.triggerPoint>=s.oldScroll,u=h&&p,c=!h&&!p,!g&&u?(d.queueTrigger(s.backward),o[d.group.id]=d.group):!g&&c?(d.queueTrigger(s.forward),o[d.group.id]=d.group):g&&s.oldScroll>=d.triggerPoint&&(d.queueTrigger(s.forward),o[d.group.id]=d.group)}}return n.requestAnimationFrame(function(){for(var t in o)o[t].flushTriggers()}),this},e.findOrCreateByElement=function(t){return e.findByElement(t)||new e(t)},e.refreshAll=function(){for(var t in o)o[t].refresh()},e.findByElement=function(t){return o[t.waypointContextKey]},window.onload=function(){r&&r(),e.refreshAll()},n.requestAnimationFrame=function(e){var i=window.requestAnimationFrame||window.mozRequestAnimationFrame||window.webkitRequestAnimationFrame||t;i.call(window,e)},n.Context=e}(),function(){"use strict";function t(t,e){return t.triggerPoint-e.triggerPoint}function e(t,e){return e.triggerPoint-t.triggerPoint}function i(t){this.name=t.name,this.axis=t.axis,this.id=this.name+"-"+this.axis,this.waypoints=[],this.clearTriggerQueues(),o[this.axis][this.name]=this}var o={vertical:{},horizontal:{}},n=window.Waypoint;i.prototype.add=function(t){this.waypoints.push(t)},i.prototype.clearTriggerQueues=function(){this.triggerQueues={up:[],down:[],left:[],right:[]}},i.prototype.flushTriggers=function(){for(var i in this.triggerQueues){var o=this.triggerQueues[i],n="up"===i||"left"===i;o.sort(n?e:t);for(var r=0,s=o.length;s>r;r+=1){var a=o[r];(a.options.continuous||r===o.length-1)&&a.trigger([i])}}this.clearTriggerQueues()},i.prototype.next=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints),o=i===this.waypoints.length-1;return o?null:this.waypoints[i+1]},i.prototype.previous=function(e){this.waypoints.sort(t);var i=n.Adapter.inArray(e,this.waypoints);return i?this.waypoints[i-1]:null},i.prototype.queueTrigger=function(t,e){this.triggerQueues[e].push(t)},i.prototype.remove=function(t){var e=n.Adapter.inArray(t,this.waypoints);e>-1&&this.waypoints.splice(e,1)},i.prototype.first=function(){return this.waypoints[0]},i.prototype.last=function(){return this.waypoints[this.waypoints.length-1]},i.findOrCreate=function(t){return o[t.axis][t.name]||new i(t)},n.Group=i}(),function(){"use strict";function t(t){this.$element=e(t)}var e=window.jQuery,i=window.Waypoint;e.each(["innerHeight","innerWidth","off","offset","on","outerHeight","outerWidth","scrollLeft","scrollTop"],function(e,i){t.prototype[i]=function(){var t=Array.prototype.slice.call(arguments);return this.$element[i].apply(this.$element,t)}}),e.each(["extend","inArray","isEmptyObject"],function(i,o){t[o]=e[o]}),i.adapters.push({name:"jquery",Adapter:t}),i.Adapter=t}(),function(){"use strict";function t(t){return function(){var i=[],o=arguments[0];return t.isFunction(arguments[0])&&(o=t.extend({},arguments[1]),o.handler=arguments[0]),this.each(function(){var n=t.extend({},o,{element:this});"string"==typeof n.context&&(n.context=t(this).closest(n.context)[0]),i.push(new e(n))}),i}}var e=window.Waypoint;window.jQuery&&(window.jQuery.fn.waypoint=t(window.jQuery)),window.Zepto&&(window.Zepto.fn.waypoint=t(window.Zepto))}();

/*!
 Waypoints Sticky Element Shortcut - 4.0.1
 Copyright © 2011-2016 Caleb Troughton
 Licensed under the MIT license.
 https://github.com/imakewebthings/waypoints/blob/master/licenses.txt
 */
!function(){"use strict";function t(s){this.options=e.extend({},i.defaults,t.defaults,s),this.element=this.options.element,this.$element=e(this.element),this.createWrapper(),this.createWaypoint()}var e=window.jQuery,i=window.Waypoint;t.prototype.createWaypoint=function(){var t=this.options.handler;this.waypoint=new i(e.extend({},this.options,{element:this.wrapper,handler:e.proxy(function(e){var i=this.options.direction.indexOf(e)>-1,s=i?this.$element.outerHeight(!0):"";this.$wrapper.height(s),this.$element.toggleClass(this.options.stuckClass,i),t&&t.call(this,e)},this)}))},t.prototype.createWrapper=function(){this.options.wrapper&&this.$element.wrap(this.options.wrapper),this.$wrapper=this.$element.parent(),this.wrapper=this.$wrapper[0]},t.prototype.destroy=function(){this.$element.parent()[0]===this.wrapper&&(this.waypoint.destroy(),this.$element.removeClass(this.options.stuckClass),this.options.wrapper&&this.$element.unwrap())},t.defaults={wrapper:'<div class="sticky-wrapper" />',stuckClass:"stuck",direction:"down right"},i.Sticky=t}();

/*
 jQuery Easing
 */
!function(n){"function"==typeof define&&define.amd?define(["jquery"],function(e){return n(e)}):"object"==typeof module&&"object"==typeof module.exports?exports=n(require("jquery")):n(jQuery)}(function(n){function e(n){var e=7.5625,t=2.75;return n<1/t?e*n*n:n<2/t?e*(n-=1.5/t)*n+.75:n<2.5/t?e*(n-=2.25/t)*n+.9375:e*(n-=2.625/t)*n+.984375}void 0!==n.easing&&(n.easing.jswing=n.easing.swing);var t=Math.pow,u=Math.sqrt,r=Math.sin,i=Math.cos,a=Math.PI,c=1.70158,o=1.525*c,s=2*a/3,f=2*a/4.5;n.extend(n.easing,{def:"easeOutQuad",swing:function(e){return n.easing[n.easing.def](e)},easeInQuad:function(n){return n*n},easeOutQuad:function(n){return 1-(1-n)*(1-n)},easeInOutQuad:function(n){return n<.5?2*n*n:1-t(-2*n+2,2)/2},easeInCubic:function(n){return n*n*n},easeOutCubic:function(n){return 1-t(1-n,3)},easeInOutCubic:function(n){return n<.5?4*n*n*n:1-t(-2*n+2,3)/2},easeInQuart:function(n){return n*n*n*n},easeOutQuart:function(n){return 1-t(1-n,4)},easeInOutQuart:function(n){return n<.5?8*n*n*n*n:1-t(-2*n+2,4)/2},easeInQuint:function(n){return n*n*n*n*n},easeOutQuint:function(n){return 1-t(1-n,5)},easeInOutQuint:function(n){return n<.5?16*n*n*n*n*n:1-t(-2*n+2,5)/2},easeInSine:function(n){return 1-i(n*a/2)},easeOutSine:function(n){return r(n*a/2)},easeInOutSine:function(n){return-(i(a*n)-1)/2},easeInExpo:function(n){return 0===n?0:t(2,10*n-10)},easeOutExpo:function(n){return 1===n?1:1-t(2,-10*n)},easeInOutExpo:function(n){return 0===n?0:1===n?1:n<.5?t(2,20*n-10)/2:(2-t(2,-20*n+10))/2},easeInCirc:function(n){return 1-u(1-t(n,2))},easeOutCirc:function(n){return u(1-t(n-1,2))},easeInOutCirc:function(n){return n<.5?(1-u(1-t(2*n,2)))/2:(u(1-t(-2*n+2,2))+1)/2},easeInElastic:function(n){return 0===n?0:1===n?1:-t(2,10*n-10)*r((10*n-10.75)*s)},easeOutElastic:function(n){return 0===n?0:1===n?1:t(2,-10*n)*r((10*n-.75)*s)+1},easeInOutElastic:function(n){return 0===n?0:1===n?1:n<.5?-(t(2,20*n-10)*r((20*n-11.125)*f))/2:t(2,-20*n+10)*r((20*n-11.125)*f)/2+1},easeInBack:function(n){return(c+1)*n*n*n-c*n*n},easeOutBack:function(n){return 1+(c+1)*t(n-1,3)+c*t(n-1,2)},easeInOutBack:function(n){return n<.5?t(2*n,2)*(7.189819*n-o)/2:(t(2*n-2,2)*((o+1)*(2*n-2)+o)+2)/2},easeInBounce:function(n){return 1-e(1-n)},easeOutBounce:e,easeInOutBounce:function(n){return n<.5?(1-e(1-2*n))/2:(1+e(2*n-1))/2}})});

/*
 https://github.com/verlok/lazyload#api
 */
var _extends=Object.assign||function(t){for(var e=1;e<arguments.length;e++){var n=arguments[e];for(var o in n)Object.prototype.hasOwnProperty.call(n,o)&&(t[o]=n[o])}return t},_typeof="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t};!function(t,e){"object"===("undefined"==typeof exports?"undefined":_typeof(exports))&&"undefined"!=typeof module?module.exports=e():"function"==typeof define&&define.amd?define(e):t.LazyLoad=e()}(this,function(){"use strict";function t(t,e,n){var o=e._settings;!n&&i(t)||(C(o.callback_enter,t),R.indexOf(t.tagName)>-1&&(N(t,e),I(t,o.class_loading)),E(t,e),a(t),C(o.callback_set,t))}var e={elements_selector:"img",container:document,threshold:300,thresholds:null,data_src:"src",data_srcset:"srcset",data_sizes:"sizes",data_bg:"bg",class_loading:"loading",class_loaded:"loaded",class_error:"error",load_delay:0,callback_load:null,callback_error:null,callback_set:null,callback_enter:null,callback_finish:null,to_webp:!1},n=function(t){return _extends({},e,t)},o=function(t,e){return t.getAttribute("data-"+e)},r=function(t,e,n){var o="data-"+e;null!==n?t.setAttribute(o,n):t.removeAttribute(o)},a=function(t){return r(t,"was-processed","true")},i=function(t){return"true"===o(t,"was-processed")},s=function(t,e){return r(t,"ll-timeout",e)},c=function(t){return o(t,"ll-timeout")},l=function(t){return t.filter(function(t){return!i(t)})},u=function(t,e){return t.filter(function(t){return t!==e})},d=function(t,e){var n,o=new t(e);try{n=new CustomEvent("LazyLoad::Initialized",{detail:{instance:o}})}catch(t){(n=document.createEvent("CustomEvent")).initCustomEvent("LazyLoad::Initialized",!1,!1,{instance:o})}window.dispatchEvent(n)},f=function(t,e){return e?t.replace(/\.(jpe?g|png)/gi,".webp"):t},_="undefined"!=typeof window,v=_&&!("onscroll"in window)||/(gle|ing|ro)bot|crawl|spider/i.test(navigator.userAgent),g=_&&"IntersectionObserver"in window,h=_&&"classList"in document.createElement("p"),b=_&&function(){var t=document.createElement("canvas");return!(!t.getContext||!t.getContext("2d"))&&0===t.toDataURL("image/webp").indexOf("data:image/webp")}(),m=function(t,e,n,r){for(var a,i=0;a=t.children[i];i+=1)if("SOURCE"===a.tagName){var s=o(a,n);p(a,e,s,r)}},p=function(t,e,n,o){n&&t.setAttribute(e,f(n,o))},y=function(t,e){var n=b&&e.to_webp,r=o(t,e.data_src),a=o(t,e.data_bg);if(r){var i=f(r,n);t.style.backgroundImage='url("'+i+'")'}if(a){var s=f(a,n);t.style.backgroundImage=s}},w={IMG:function(t,e){var n=b&&e.to_webp,r=e.data_srcset,a=t.parentNode;a&&"PICTURE"===a.tagName&&m(a,"srcset",r,n);var i=o(t,e.data_sizes);p(t,"sizes",i);var s=o(t,r);p(t,"srcset",s,n);var c=o(t,e.data_src);p(t,"src",c,n)},IFRAME:function(t,e){var n=o(t,e.data_src);p(t,"src",n)},VIDEO:function(t,e){var n=e.data_src,r=o(t,n);m(t,"src",n),p(t,"src",r),t.load()}},E=function(t,e){var n=e._settings,o=t.tagName,r=w[o];if(r)return r(t,n),e._updateLoadingCount(1),void(e._elements=u(e._elements,t));y(t,n)},I=function(t,e){h?t.classList.add(e):t.className+=(t.className?" ":"")+e},L=function(t,e){h?t.classList.remove(e):t.className=t.className.replace(new RegExp("(^|\\s+)"+e+"(\\s+|$)")," ").replace(/^\s+/,"").replace(/\s+$/,"")},C=function(t,e){t&&t(e)},O=function(t,e,n){t.addEventListener(e,n)},k=function(t,e,n){t.removeEventListener(e,n)},x=function(t,e,n){O(t,"load",e),O(t,"loadeddata",e),O(t,"error",n)},A=function(t,e,n){k(t,"load",e),k(t,"loadeddata",e),k(t,"error",n)},z=function(t,e,n){var o=n._settings,r=e?o.class_loaded:o.class_error,a=e?o.callback_load:o.callback_error,i=t.target;L(i,o.class_loading),I(i,r),C(a,i),n._updateLoadingCount(-1)},N=function(t,e){var n=function n(r){z(r,!0,e),A(t,n,o)},o=function o(r){z(r,!1,e),A(t,n,o)};x(t,n,o)},R=["IMG","IFRAME","VIDEO"],S=function(e,n,o){t(e,o),n.unobserve(e)},M=function(t){var e=c(t);e&&(clearTimeout(e),s(t,null))},j=function(t,e,n){var o=n._settings.load_delay,r=c(t);r||(r=setTimeout(function(){S(t,e,n),M(t)},o),s(t,r))},D=function(t){return t.isIntersecting||t.intersectionRatio>0},T=function(t){return{root:t.container===document?null:t.container,rootMargin:t.thresholds||t.threshold+"px"}},U=function(t,e){this._settings=n(t),this._setObserver(),this._loadingCount=0,this.update(e)};return U.prototype={_manageIntersection:function(t){var e=this._observer,n=this._settings.load_delay,o=t.target;n?D(t)?j(o,e,this):M(o):D(t)&&S(o,e,this)},_onIntersection:function(t){t.forEach(this._manageIntersection.bind(this))},_setObserver:function(){g&&(this._observer=new IntersectionObserver(this._onIntersection.bind(this),T(this._settings)))},_updateLoadingCount:function(t){this._loadingCount+=t,0===this._elements.length&&0===this._loadingCount&&C(this._settings.callback_finish)},update:function(t){var e=this,n=this._settings,o=t||n.container.querySelectorAll(n.elements_selector);this._elements=l(Array.prototype.slice.call(o)),!v&&this._observer?this._elements.forEach(function(t){e._observer.observe(t)}):this.loadAll()},destroy:function(){var t=this;this._observer&&(this._elements.forEach(function(e){t._observer.unobserve(e)}),this._observer=null),this._elements=null,this._settings=null},load:function(e,n){t(e,this,n)},loadAll:function(){var t=this;this._elements.forEach(function(e){t.load(e)})}},_&&function(t,e){if(e)if(e.length)for(var n,o=0;n=e[o];o+=1)d(t,n);else d(t,e)}(U,window.lazyLoadOptions),U});

/*
 https://www.cssscript.com/touch-friendly-image-comparison-slider-javascript/
 */
var ocbInitBeforeAfter = function() {

    var elsH = document.querySelectorAll(".ocb-image-splitter .ocb-image-splitter-mover");
    var i = elsH.length;
    while (i--) {
        var moverWidth = elsH[i].getBoundingClientRect().width;
        var imgLeft = elsH[i].nextElementSibling;
        var imgRight = imgLeft.nextElementSibling;
        var width = imgRight.getBoundingClientRect().width;
        var height = imgRight.getBoundingClientRect().height;
        elsH[i].style.left = width / 2 - moverWidth / 2 + 'px';
        //imgLeft.style.clip = "rect(0px, " + width / 2 + "px, " + height + "px, 0px)";
        imgLeft.style.clip = "rect(0px, " + width / 2 + "px, 9999px, 0px)";
        var mouseDownX = 0;
        var X;
        elsH[i].addEventListener("mousedown", function(e) {
            X = e.clientX;
            mouseDownX = 1;
        });
        elsH[i].addEventListener("mouseup", function(e) {
            mouseDownX = 0;
        });
        elsH[i].addEventListener("mouseout", function(e) {
            mouseDownX = 0;
        });

        elsH[i].addEventListener("touchstart", function(e) {
            X = e.touches[0].clientX;
            mouseDownX = 1;
        });
        elsH[i].addEventListener("touchend", function(e) {
            mouseDownX = 0;
        });

        elsH[i].addEventListener("mousemove", function(e) {
            if (mouseDownX) {
                this.style.left = parseInt(this.style.left) + (event.clientX - X) + "px";
                X = event.clientX;
                this.nextElementSibling.style.clip = "rect(0px, " + (this.getBoundingClientRect().width / 2 + parseInt(this.style.left)) + "px, " + this.getBoundingClientRect().height + "px, 0px)";
            }
        });

        elsH[i].addEventListener("touchmove", function(e) {
            if (mouseDownX) {
                this.style.left = parseInt(this.style.left) + (e.touches[0].clientX - X) + "px";
                X = e.touches[0].clientX;
                this.nextElementSibling.style.clip = "rect(0px, " + (this.getBoundingClientRect().width / 2 + parseInt(this.style.left)) + "px, " + this.getBoundingClientRect().height + "px, 0px)";
            }
        });
    }

    window.addEventListener("resize", function(f) {
        var elsHre = document.querySelectorAll(".ocb-image-splitter .ocb-image-splitter-mover");
        var ii = elsHre.length;
        while (ii--) {
            var moverWidth = elsHre[ii].getBoundingClientRect().width;
            var imgLeft = elsHre[ii].nextElementSibling;
            var imgRight = imgLeft.nextElementSibling;
            var width = imgRight.getBoundingClientRect().width;
            var height = imgRight.getBoundingClientRect().height;
            elsHre[ii].style.left = width / 2 - moverWidth / 2 + 'px';
            imgLeft.style.clip = "rect(0px, " + width / 2 + "px, " + height + "px, 0px)";
        }
    });

};