(function($) {
    "use strict";
    
    // How much pixels should the target element be at least close to the scrollTop to trigger navigation change
    var navigationSensitivity   = 10;
    
    // Duration of scrolling when a navigation link is clicked
    var scrollDuration          = 1000;
    
    // Counter animation duration
    var counterDuration         = 3000;
    
    // Delay between slide changes
    var slideDuration           = 5000;
    
    // Window resize end detection delay
    var windowResizeEndDelay    = 100;
    
    // Latitude of google maps destination location
    var gmapsLatitude           = 44.5403;
    
    // Longitude of google maps destination location
    var gmapsLongitude          = -78.5463;
    
    // Scroll page to element
    function scrollToElement(target, duration) {

        if(!target.length) return;

        $.scrollTo({left: 0, top: Math.max(0, target.offset().top - $('.navbar').height() - $('#wpadminbar').height())}, {
            duration: duration ? duration : scrollDuration,
            easing:   $.bez([0.13, 0.71, 0.30, 0.94])
        });
    }
    
    // Resize collapsable menu
    function resizeCollapsableMenu() {
        $('.navbar-collapse').css('max-height', Math.round($(window).height() * 0.75) + 'px');
    }
    
    // Keep main slider aspect ratio constant
    function keepSliderRatio() {
        var $slider     = $('.slider');
        var $content    = $slider.find('.slide:visible .content');
        var minHei      = Math.round($slider.width() * 940 / 1440);
        var conHei      = 0 < $content.length ? $content.height() * ($(window).width() > 991 ? 2 : 1.25) : 0;
        // $slider.height(Math.max(minHei, conHei));
        $slider.height(conHei);
        $content.css('top', (Math.max(minHei, conHei) - $content.height()) / 2);
        $('.slider .slide > .image').each(function() {
            $(this).load(keepSliderRatio);
            var $this   = $(this);
            var natWid  = $this.get(0).naturalWidth;
            var natHei  = $this.get(0).naturalHeight;
            if("undefined" === typeof natWid || "undefined" === typeof natHei) return true;
            var tarWid  = $('.slider').width();
            var tarHei  = $('.slider').height();
            var width   = tarWid;
            var height  = width * natHei / natWid;
            if(height < tarHei) {
                height  = tarHei;
                width   = height * natWid / natHei;
            }
            $(this).css({width: width, height: height, marginLeft: (tarWid - width) / 2, marginTop: (tarHei - height) / 2});
        });
    }
    
    // Spy the navigation for scrolling
    var menu_links = $('.nav > li > a[href^="#"],.nav > li.current_page_item > a[data-anchor]').filter(function(){
        var s = $(this).data('anchor') ? '#'+$(this).data('anchor') : $(this).attr('href');
        if($(s).length)
            return true;
        else
            return false;
    }).sort(function(a, b){
        var as = $(a).data('anchor') ? '#'+$(a).data('anchor') : $(a).attr('href');
        var bs = $(b).data('anchor') ? '#'+$(b).data('anchor') : $(b).attr('href');
        return $(as).offset().top - $(bs).offset().top;
    });
    var menu_links_parents = menu_links.parent();
    var scrollSpyNavigation_flag = true;
    var scrollSpyNavigation_loop_flag = false;
    var scrollSpyNavigation_loop_time = 100;
    $('.nav > li.current_page_item > a[data-anchor]').not(menu_links).parent().addClass('biznex-no-anchor');
    function scrollSpyNavigation(){
        if(scrollSpyNavigation_flag){
            scrollSpyNavigation_flag = false;
            scrollSpyNavigation_action();
            setTimeout(scrollSpyNavigation_loop, scrollSpyNavigation_loop_time);
        }else{
            scrollSpyNavigation_loop_flag = true;
        }
    }
    function scrollSpyNavigation_loop(){
        if(scrollSpyNavigation_loop_flag){
            scrollSpyNavigation_loop_flag = false;
            scrollSpyNavigation_action();
            setTimeout(scrollSpyNavigation_loop, scrollSpyNavigation_loop_time);
        }else{
            scrollSpyNavigation_flag = true;
        }
    }
    function scrollSpyNavigation_action() {

        if(!menu_links.length) return;

        var delta = 20;

        var targetOffset = $(window).scrollTop() + $('.navbar').height() + $('#wpadminbar').height() + delta;
        var i = -1;
        var i_parent;
        var i_buffer;

        while(i+1<menu_links.length&&targetOffset>=$(menu_links.eq(i+1).data('anchor')?'#'+menu_links.eq(i+1).data('anchor'):menu_links.eq(i+1).attr('href')).offset().top)i++;

        i_buffer = i;
        while(i_buffer>0&&($(menu_links.eq(i).data('anchor')?'#'+menu_links.eq(i).data('anchor'):menu_links.eq(i).attr('href')).offset().top)===($(menu_links.eq(i_buffer-1).data('anchor')?'#'+menu_links.eq(i_buffer-1).data('anchor'):menu_links.eq(i_buffer-1).attr('href')).offset().top))i_buffer--;

        menu_links_parents.filter('.active').each(function(index,element){

            var t = $(element);
            var t_link = t.children('a');
            var t_link_index = menu_links.index(t_link);

            if(t_link_index<i_buffer||t_link_index>i)
                t.removeClass('active');

        });

        if(i_buffer>-1)
            while(i_buffer<=i){

                menu_links.eq(i_buffer).parent().addClass('active');
                i_buffer++;

            }
    }
    $(window).load(function(){
        scrollSpyNavigation();
    });

    var is_mobile_ios = /(iPad|iPhone|iPod)/g.test(navigator.userAgent);

    if(is_mobile_ios){
        console.log('ios online');
        $('.parallax').css('background-attachment', 'scroll');
        $(window).bind('touchmove',function(){
            scrollParallaxElements();
        });
        $(window).bind('touchmove',function(e){
            console.log('touch - '+$(window).scrollTop());
        });
    }
    
    // Resize parallax elements (background-size, sorry IE8)
    var resizeParallaxElements_action;
    function resizeParallaxElements() {
        resizeParallaxElements_action();
    }
    if(!is_mobile_ios)
        resizeParallaxElements_action = function(){
            $('.parallax').each(function() {
                var $this = $(this);
                var vWid  = $(window).width() + $(window).width() / $this.data('speed');
                var vHei  = $(window).height() + $(window).height() / $this.data('speed');
                var nWid  = $this.data('natural-width');
                var nHei  = $this.data('natural-height');
                var clb   = function() {
                    $this.data('natural-width', nWid);
                    $this.data('natural-height', nHei);
                    var width  = vWid;
                    var height = Math.ceil(width * nHei / nWid);
                    if (height < vHei) {
                        height = vHei;
                        width  = Math.ceil(height * nWid / nHei);
                    }
                    $this.css('background-size', width + 'px ' + height + 'px');
                };
                if ("undefined" === typeof nWid || "undefined" === typeof nHei) {
                    var img    = new Image();
                    img.onload = function() {
                        nWid   = img.naturalWidth;
                        nHei   = img.naturalHeight;
                        clb();
                    };
                    img.src    = $this.css('background-image').replace(/^url\(/i, '').replace(/\)$/, '');
                } else {
                    clb();
                }
            });
            scrollParallaxElements();
        };
    else
        resizeParallaxElements_action = function(){
            $('.parallax').each(function() {
                var $this = $(this);
                var vWid  = $(window).width() + $(window).width() / $this.data('speed');
                var vHei  = $(window).height() + $(window).height() / $this.data('speed');
                var nWid  = $this.data('natural-width');
                var nHei  = $this.data('natural-height');
                var clb   = function() {
                    $this.data('natural-width', nWid);
                    $this.data('natural-height', nHei);
                    var width  = vWid;
                    var height = Math.ceil(width * nHei / nWid);
                    if (height < vHei) {
                        height = vHei;
                        width  = Math.ceil(height * nWid / nHei);
                    }
                    $this.css('background-size', width + 'px ' + height + 'px');
                };
                if ("undefined" === typeof nWid || "undefined" === typeof nHei) {
                    var img    = new Image();
                    img.onload = function() {
                        nWid   = img.naturalWidth;
                        nHei   = img.naturalHeight;
                        clb();
                    };
                    img.src    = $this.css('background-image').replace(/^url\(/i, '').replace(/\)$/, '');
                } else {
                    clb();
                }
            });
            scrollParallaxElements();
        };
    
    // Scroll parallax elements
    var scrollParallaxElements_action;
    function scrollParallaxElements() {
        scrollParallaxElements_action($(window).scrollTop());
    }
    if(!is_mobile_ios)
        scrollParallaxElements_action = function(scrolltop){
            $('.parallax').each(function() {
                var $this   = $(this);
                $this.css('background-position', '50% -' + (($(window).height() + $this.height() - Math.max(0, Math.min($(window).height() + $this.height(), $this.offset().top + $this.height() - scrolltop))) / $this.data('speed')) + 'px');
            });
        };
    else
        scrollParallaxElements_action = function(scrolltop){console.log('scrolled - '+scrolltop);return;
            $('.parallax').each(function() {
                var $this   = $(this);
                var x = Math.min(-Math.min($this.offset().top-scrolltop,$(window).height()),$this.height());
                $this.css('background-position', '50% ' + String(x-x/Number($this.data('speed'))) + 'px');
            });
        };
    
    // Should trigger animate counters?
    function animateCounters() {

        if(!$('.counters').length) return;

        if($(window).scrollTop() + $(window).height() > $('.counters').offset().top) {
            $('.counter').each(function() {
                animateCounter($(this));
            });
        }
    }
    
    // Animate counter
    function animateCounter($counter) {
        if (true === $counter.data('animation-started')) {
            return;
        }
        $counter.data('animation-started', true);
        $counter.animate({dummy: 1}, {
            duration: counterDuration,
            easing:   $.bez([0.13, 0.71, 0.30, 0.94]),
            step:     function(now) {
                var $this  = $(this);
                var $val   = $this.find('.value');
                var $left  = $this.find('.left > img');
                var $right = $this.find('.right > img');
                var value  = parseInt($val.data('value'));
                var right  = Math.min(0, -180 + 3.60 * now * value);
                var left   = Math.max(-180, -360 + 3.60 * now * value);
                $val.html(Math.round(now * value) + '%');
                $left.css({
                    '-webkit-transform': 'rotate(' + left + 'deg)',
                    '-ms-transform':     'rotate(' + left + 'deg)',
                    'transform':         'rotate(' + left + 'deg)'
                });
                $right.css({
                    '-webkit-transform': 'rotate(' + right + 'deg)',
                    '-ms-transform':     'rotate(' + right + 'deg)',
                    'transform':         'rotate(' + right + 'deg)'
                });
            }
        });
    }
    
    // Binds blog posts accordion
    function bindBlogPosts() {
        $('.posts').on('click', '.type-post a.line', function(e) {
            e.preventDefault();
            var $post = $(this).parents('.type-post').first();
            if($post.is('.open')) {
                $post.css('padding-top', 0).removeClass('open').stop().animate({height: 70}, {duration: 500, easing: $.bez([0.13, 0.71, 0.30, 0.94])});
            } else {
                $('.posts .type-post.open').css('padding-top', 0).removeClass('open').stop().animate({height: 70}, {duration: 500, easing: $.bez([0.13, 0.71, 0.30, 0.94])});
                $post.css('height', 'auto');
                var line_height_diff = Math.max($post.addClass('open').children('.content').prev('.line').height() - 70, 0);
                var height = $post.height() + line_height_diff;
                $post.css({height: 70, paddingTop: line_height_diff});
                $post.stop().animate({height: height}, {duration: 500, easing: $.bez([0.13, 0.71, 0.30, 0.94]), complete: function() {
                    scrollToElement($post, 350);
                }});
            }
        });
    }
    
    // Show or hide back to top arrow
    function toggleToTop() {
        if($(window).scrollTop() < navigationSensitivity) {
            $('.to-top').stop().animate({opacity: 0}, {duration: 350, easing: $.bez([0.13, 0.71, 0.30, 0.94])});
        } else {
            $('.to-top').stop().animate({opacity: 1}, {duration: 350, easing: $.bez([0.13, 0.71, 0.30, 0.94])});
        }
    }
    
    // Load more posts
    function postsLoad() {
        $('.posts_load').unbind('click').bind('click', function(e) {
            e.preventDefault();
            var t = $(this);
            var t_options = t.data('options');
            t.addClass('posts_load_progress');
            t_options.page++;
            jQuery.post(t_options.url, t_options, function(result){
                if(''===result){
                    t.remove();
                }else{
                    result = $(result);
                    result.css('opacity', '0');
                    t.prev('.posts').append(result);
                    t.removeClass('posts_load_progress');
                    result.each(function(index, element){
                        setTimeout(function(){
                            $(element).css('opacity', 1);
                        }, 50*index);
                    });
                }
            });
        });
    }

    // Load contact form
    function loadContactForm(){

        $('.contact-form').each(function(){

            var form = $(this);

            var form_result = form.find('.result');

            var form_submit = form.find('[type="submit"]');

            form.submit(function(e){

                e.preventDefault();

                form_submit.prop('disabled', true);

                form_result.text('Sending..');

                $.post(biznex.ajaxurl, form.serialize(), function (result) {

                    form_result.text(result);

                    form_submit.prop('disabled', false);

                });

            })

        });

    }

    function loadSubscription(){

        $('.subscribe>form').tt_subscription({
            success : function(result,config,form){
                $(form).find('[name="email"]').val('');
                $(form).next('.result').text(result);
            },
            error: function(error,config,form){
                $(form).next('.result').text(error);
            },
            required: function(config,form){
                $(form).next('.result').text(config.required_msg);
            },
            invalid_email: function(config,form){
                $(form).next('.result').text(config.invalid_email_msg);
            }
        });

    }

    function adjustHeaderPadding(){
        var adjustHeaderPadding_action = function(){
            $('body').css({
                paddingTop: $('header').height()
            });
        };
        adjustHeaderPadding_action();
        $(window).resize(function(){
            adjustHeaderPadding_action();
        });
    }

    // Portfolio pagination
    function portfolioPagination(){

        $('.portfolio+.page-numbers').each(function(){
            var t = $(this);
            t.on('click','.page-number.clickable',function(){
                console.log('xx-number');
            });
            t.on('click','.prev',function(){
                console.log('xx-prev');
            });
            t.on('click','.next',function(){
                console.log('xx-next');
            });
        });

    }

    // Team description
    function teamDescription(){

        $('.team.team-with-description').each(function(){
            var t = $(this);
            t.find('.item .image').click(function(){
                t.next('.team-member').remove();
                t.after($(this).closest('.item').find('.team-member').clone().removeAttr('style'));
            });
        });

    }
    
    // Perform various stylings once the DOM is ready
    $(document).ready(function() {

        teamDescription();

        portfolioPagination();

        scrollSpyNavigation();

        loadContactForm();

        loadSubscription();

        adjustHeaderPadding();

        // Prevent responsive navigation toggle from scrolling to top
        $('.navbar-toggle').bind('click', function(e) {
            e.preventDefault();
        });
        
        // Resize collapsable menu
        resizeCollapsableMenu();
        $(window).bind('resize-end', resizeCollapsableMenu);
        
        // Bind navigation links
        $('.nav li a[href^="#"],.nav li.current_page_item > a[data-anchor]').bind('click', function(e) {
            var s = $(this).data('anchor') ? '#'+$(this).data('anchor') : $(this).attr('href');
            e.preventDefault();
            scrollToElement($(s));
        });
        
        // Resize parallax elements
        resizeParallaxElements();
        $(window).bind('resize-end', resizeParallaxElements);
        
        // Initial scroll parallax elements
        scrollParallaxElements();
        
        // Testimonials slider
        var testimonialsLocked = false;
        $('.testimonials').find('li').each(function() {
            var $ul     = $(this);
            var $bullet = $('<a />');
            $bullet.attr('href', 'javascript:;').append('<img src="'+biznex.root+'/img/spacer.gif" alt="" />').data('target', $ul);
            $bullet.bind('click', function(e, instant) {
                e.preventDefault();
                var $this = $(this);
                if (testimonialsLocked || $this.hasClass('active')) {
                    return;
                }
                testimonialsLocked = true;
                var $old = $('.bullets a.active').removeClass('active').data('target');
                var $new = $this.addClass('active').data('target');
                var clb  = function() {
                    if ("undefined" !== typeof $old && null !== $old) {
                        $old.removeClass('active');
                    }
                    $new.addClass('active').animate({opacity: 1}, 350, $.bez([0.13, 0.71, 0.30, 0.94]), function() {
                        testimonialsLocked = false;
                    });
                };
                $new.css('opacity', 0);
                if (instant) {
                    $('.testimonials').addClass('instant');
                    setTimeout(function() {
                        $('.testimonials').removeClass('instant');
                    }, 1);
                }
                $('.testimonials').height($new.outerHeight());
                if ("undefined" !== typeof $old && null !== $old) {
                    $old.animate({opacity: 0}, 350, $.bez([0.13, 0.71, 0.30, 0.94]), clb);
                } else {
                    clb();
                }
            });
            if ($ul.hasClass('active')) {
                $bullet.addClass('active');
            }
            $('.bullets').append($bullet);
        });
        $('.bullets a').first().triggerHandler('click', true);
        $(window).bind('resize-end', function() {
            $('.bullets a.active').removeClass('active').triggerHandler('click');
        });
        
        // Easy Responsive Tabs
        $(".vertical-tabs").easyResponsiveTabs({
            type: 'vertical', //Types: default, vertical, accordion           
            width: 'auto', //auto or any custom width
            fit: true // 100% fits in a container
        });
        
        // Blog posts accordion
        bindBlogPosts();

        // Load more posts
        postsLoad();
        
        // Portfolio zoom lightbox
        $('.zoom').nivoLightbox();
        
        // Back to top arrow
        toggleToTop();
        $('.to-top').bind('click', function(e) {
            e.preventDefault();
            scrollToElement($('body'));
        });
        
        // Initialize google map
        /*var map = new google.maps.Map($('.map').get(0), {
            center: new google.maps.LatLng(gmapsLatitude, gmapsLongitude),
            zoom: 10,
            mapTypeId: google.maps.MapTypeId.ROADMAP,
            scrollwheel: false
        });
        new google.maps.Marker({
            position: new google.maps.LatLng(gmapsLatitude, gmapsLongitude),
            map: map,
            icon: './img/marker.png'
        });*/
    
        // Bind main slider
        keepSliderRatio();
        if($('.slider>.slide').length>1){
            $('.slider').bind('next', function() {
                var $this   = $(this);
                var $active = $this.find('.slide.active');
                if(0 === $active.length) {
                    $active = $this.find('.slide').last();
                }
                var $next   = $active.next('.slide');
                if(0 === $next.length) {
                    $next   = $this.find('.slide').first();
                }
                $active.removeClass('active').animate({opacity: 0}, {duration: 2000, easing: $.bez([0.13, 0.71, 0.30, 0.94])}, function() {
                    $(this).hide();
                });
                $next.addClass('active').css('opacity', 0);
                setTimeout(function() {
                    $next.show().animate({opacity: 1}, {duration: 2000, easing: $.bez([0.13, 0.71, 0.30, 0.94])});
                    keepSliderRatio();
                }, 10);
                setTimeout(function() {
                    $this.triggerHandler('next');
                }, slideDuration);
            });
            setTimeout(function() {
                $('.slider').triggerHandler('next');
            }, slideDuration);
        }
    });
    
    // Perform operations upon scrolling the window
    $(window).scroll(function() {
        // Spy navigation
        scrollSpyNavigation();
        
        // Scroll parallax elements
        scrollParallaxElements();
        
        // Should animate counters?
        animateCounters();
        
        // Should show back to top arrow?
        toggleToTop();
    });
    
    // Bind window resize end event
    $(window).resize(function() {
        var $this   = $(this);
        clearTimeout($this.data('resize-timeout'));
        $this.data('resize-timeout', setTimeout(function() {
            $this.triggerHandler('resize-end');
        }, windowResizeEndDelay));
    });
    
    // Keep main slider's ratio constant
    $(window).bind('resize-end', keepSliderRatio);
    
    // Trigger sliders
    $(window).load(function() {
        $('.portfolio.flexslider').flexslider({
            animation: "slide",
            animationLoop: false,
            itemWidth: 280,
            itemMargin: 0,
            easing: $.bez([0.13, 0.71, 0.30, 0.94]),
            prevText: '',
            nextText: '',
            controlNav: false
        });
        $('.flexslider.mini').flexslider({
            animation: "slide",
            animationLoop: false,
            itemWidth: 940,
            itemMargin: 0,
            easing: $.bez([0.13, 0.71, 0.30, 0.94]),
            prevText: '',
            nextText: '',
            controlNav: true
        });
        keepSliderRatio();
    });
})(jQuery);