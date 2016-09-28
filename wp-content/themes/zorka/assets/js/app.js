(function($) {
    "use strict";
    var Core = {
        initialized: false,
		timeOut_search: null,

        initialize: function() {

            if (this.initialized) return;
            this.initialized = true;

            this.build();
            Core.events();
        },
        build : function() {
            Core.owlCarousel();
            Core.page_animsition();
            Core.process_product();
            Core.process_footer();
			Core.affix_header(2, true);
			Core.menu_product_category();
			Core.menu_core_process();
			Core.language_selector();
            Core.float_header_background();
            Core.setPositionPageTitle();
            Core.setResponsiveProductShortcode();
            Core.setOverlayVC();
        },

        events : function() {
			Core.window_scroll();
			Core.window_resize();
			Core.goTop();
			// Anchors Position
			$("a[data-hash]").on("click", function(e) {
				e.preventDefault();
				Core.anchorsPosition(this);
				return false;
			});
            Core.process_Blog();
			Core.login_link_event();
			Core.search_popup_process();
			Core.search_box_header_process();
			Core.document_click();
            $('.wpb_map_wraper,.zorka-text-widget').on('click', Core.onMapClickHandler);
			Core.product_category_search();

		},
        setOverlayVC : function() {
            $('[data-overlay-image]').each(function() {
                var $selector =$(this);
                setTimeout(function() {
                    var overlay_image = $selector.data('overlay-image');
                    var overlay_opacity = $selector.data('overlay-opacity');
                    var html = '<div class="overlay-bg-vc" style="background-image: url('+ overlay_image +') ;background-repeat:repeat; opacity:'+overlay_opacity+'"></div>';
                    $selector.prepend(html);
                }, 100);
            });
            $('[data-overlay-color]').each(function() {
                var $selector =$(this);
                setTimeout(function() {
                    var overlay_color = $selector.data('overlay-color');
                    var html = '<div class="overlay-bg-vc" style="background-color: '+ overlay_color +'"></div>';
                    $selector.prepend(html);
                }, 100);
            });
        },
		product_category_search: function() {
			$('.search-header-wrapper .product-category >  span').click(function() {
				$('> ul', $(this).parent()).slideToggle();
			});

			$('.search-header-wrapper .product-category ul span').click(function() {
				var $this = $(this);
				var id = $this.attr('data-id');
				var text = $this.text();
				$('.search-header-wrapper .product-category > ul').slideToggle();
				var $cate_current = $('.search-header-wrapper .product-category >  span');
				$cate_current.attr('data-id', id);
				$cate_current.text(text);
			});
		},
		menu_core_process: function() {
			$('.main-menu li.menu-item, .left-menu li.menu-item').each(function() {
				if ($(this).hasClass('menu-item-has-children')) {
					$('> a', this).append('<b class="g-caret"></b>');
				}
			});
			$('.main-menu li.menu-item > a > b.g-caret, .left-menu li.menu-item > a > b.g-caret').click(function(event){
				event.preventDefault();

				var $menu_item = $(this).parent().parent();
				$menu_item.toggleClass('in');
				$('> ul.sub-menu', $menu_item).slideToggle();
			});
			$('.nav-menu-toggle-wrapper .nav-menu-toggle-inner').click(function(){
				$(this).toggleClass('in');
				$('.main-menu, .left-menu').slideToggle();
			});
			$(window).resize(function() {
				if (Core.is_desktop()) {
					$('.nav-menu-toggle-wrapper .nav-menu-toggle-inner').removeClass('in');
					$('.main-menu, .left-menu').css('display','');
					$('.main-menu ul.sub-menu, .left-menu ul.sub-menu').css('display','');
				}
			});
		},
		language_selector: function() {
			var $selected = $('#lang_sel_list li > a.lang_sel_sel');
			var langStr = 'en';
			if ($selected.length > 0) {
				langStr = $selected.parent().attr('class');
				if (langStr) {
					langStr = langStr.replace('icl-','');
				}
				else {
					langStr = 'en';
				}
				$('.language-selector > li > span').text($('.language-selector > li > span').text() + ': ' + langStr);
			}
		},
		document_click: function() {
			$(document).click(function(event){
				if (($(event.target).closest(".search-header-wrapper").length === 0)
					|| ($(event.target).closest('.search-header-wrapper .product-category').length !== 0)) {
					$('.search-header-result').html('');
					$('.search-header-wrapper .seach-header-input').val('');
				}

				if ($(event.target).closest('.search-header-wrapper .product-category').length === 0) {
					$('.search-header-wrapper .product-category > ul').slideUp();
				}
			});
		},
		anchorsPosition : function(obj,time) {
			var target = jQuery(obj).attr("href");
			if ($(target).length > 0 ) {
				var _scrollTop = $(target).offset().top;
				if ($('#wpadminbar').length > 0) {
					_scrollTop -= $('#wpadminbar').outerHeight();
				}
				$("html,body").animate({scrollTop: _scrollTop}, time,'swing',function(){});
			}
		},
		window_scroll: function() {
            $(window).on('scroll',function(event){
				Core.go_top_scroll();
				Core.affix_header_scroll();
            });
		},
		goTop : function() {
			$('.gotop').click(function(){
				$('html,body').animate({scrollTop: '0px'}, 800);
				return false;
			});
		},
		go_top_scroll: function() {
			if ($(window).scrollTop() > $(window).height()/2){
				$('.gotop').addClass('in');
			}
			else{
				$('.gotop').removeClass('in');
			}
		},
		window_resize: function() {
			$(window).resize(function(){
				if($('#wpadminbar').length > 0) {
					$('body').attr('data-offset', $('#wpadminbar').outerHeight() + 1);
				}
				if($('#wpadminbar').length > 0) {
					$('body').attr('data-offset', $('#wpadminbar').outerHeight() + 1);
				}
                Core.processWidthAudioPlayer();
                Core.process_footer();
				Core.affix_header(0, true);
				Core.affix_header_scroll();
                Core.float_header_background();
                Core.setProductListResponsive();
                Core.setResponsiveProductShortcode();
			});
		},
        owlCarousel : function() {
            $('div.owl-carousel:not(.manual)').each(function(){
                var slider = $(this);

                var defaults = {
                    // Most important owl features
                    items : 5,
                    itemsCustom : false,
                    itemsDesktop : [1199,4],
                    itemsDesktopSmall : [980,3],
                    itemsTablet: [768,2],
                    itemsTabletSmall: false,
                    itemsMobile : [479,1],
                    singleItem : false,
                    itemsScaleUp : false,

                    //Basic Speeds
                    slideSpeed : 200,
                    paginationSpeed : 800,
                    rewindSpeed : 1000,

                    //Autoplay
                    autoPlay : false,
                    stopOnHover : false,

                    // Navigation
                    navigation : false,
                    navigationText : ["<i class='fa fa-angle-left'></i>","<i class='fa fa-angle-right'></i>"],
                    rewindNav : true,
                    scrollPerPage : false,

                    //Pagination
                    pagination : true,
                    paginationNumbers: false,

                    // Responsive
                    responsive: true,
                    responsiveRefreshRate : 200,
                    responsiveBaseWidth: window,

                    // CSS Styles
                    baseClass : "owl-carousel",
                    theme : "owl-theme",

                    //Lazy load
                    lazyLoad : false,
                    lazyFollow : true,
                    lazyEffect : "fade",

                    //Auto height
                    autoHeight : false,

                    //JSON
                    jsonPath : false,
                    jsonSuccess : false,

                    //Mouse Events
                    dragBeforeAnimFinish : true,
                    mouseDrag : true,
                    touchDrag : true,

                    //Transitions
                    transitionStyle : false,

                    // Other
                    addClassActive : false,

                    //Callbacks
                    beforeUpdate : false,
                    afterUpdate : false,
                    beforeInit: false,
                    afterInit: false,
                    beforeMove: false,
                    afterMove: false,
                    afterAction: false,
                    startDragging : false,
                    afterLazyLoad : false
                };

                var config = $.extend({}, defaults, slider.data("plugin-options"));
                var fucStr_afterInit = config.afterInit;
                var fuc_afterInit = function(){
                    eval(fucStr_afterInit);
                };
                if (config.afterInit != false) {
                    config.afterInit = fuc_afterInit;
                }

                var fucStr_afterMove = config.afterMove;

                var fuc_afterMove = function(){
                    eval(fucStr_afterMove);
                };
                if (config.afterMove != false) {
                    config.afterMove = fuc_afterMove;
                }



                // Initialize Slider


                slider.owlCarousel(config);
            });

            $('.ryl-text-carousel').each(function() {
                var $this = $(this);

                $this.owlCarousel({
                    singleItem : true,
                    items: 1,
                    pagination: true,
                    navigation: $this.hasClass('has-nav'),
                    slideSpeed: 300,
                    mouseDrag: false,
                    transitionStyle : "ryl-text",
                    afterAction: updateSliderIndex,
                    afterMove: false,
                    beforeMove : false,
                    autoHeight : true
                });

                function updateSliderIndex() {
                    var items = $this.children().find('.owl-pagination .owl-page').length;
                    var index = $this.children().find('.owl-pagination .owl-page.active').index() + 1;
                    $this.attr('data-index', (index + "/" + items));
                }
            });
        },

        // Disable scroll zooming and bind back the click event
        onMapMouseleaveHandler : function (event) {
            var that = jQuery(this);

            that.on('click',Core.onMapClickHandler);
            that.off('mouseleave', Core.onMapMouseleaveHandler);
            that.find('iframe').css("pointer-events", "none");
        },

        onMapClickHandler : function (event) {
            var that = jQuery(this);

            // Disable the click handler until the user leaves the map area
            that.off('click', Core.onMapClickHandler);

            // Enable scrolling zoom
            that.find('iframe').css("pointer-events", "auto");

            // Handle the mouse leave event
            that.on('mouseleave', Core.onMapMouseleaveHandler);
        },
        process_Blog : function() {
            Core.blog_infinite_scroll();
            Core.blog_jplayer();
            Core.blog_load_more();
            if ($('.entry-tag-social .entry-share-wrapper').length == 0) {
                if ($('.entry-tag-social .entry-tag-wrapper').length == 0) {
                    $('.entry-tag-social').remove();
                } else {
                    $('.entry-tag-social .entry-tag-wrapper').addClass('entry-tag-full-wrapper');
                }
            }
        },
        blog_load_more : function() {
            $('.blog-load-more').on('click',function(event){
                event.preventDefault();
                var $this = $(this).button('loading');
                var link = $(this).attr('data-href');
                var contentWrapper = '.blog-inner';
                var element = 'article';

                $.get(link,function(data) {
                    var next_href = $('.blog-load-more',data).attr('data-href');
                    var $newElems = $(element,data).css({
                        opacity: 0
                    });

                    $(contentWrapper).append($newElems);

                    $newElems.imagesLoaded(function () {
                        Core.owlCarousel();
                        Core.blog_jplayer();

                        $newElems.animate({
                            opacity: 1
                        });
                    });

                    if (typeof(next_href) == 'undefined') {
                        $this.hide();
                    } else {
                        $this.button('reset');
                        $this.attr('data-href',next_href);
                    }

                });
            });
        },
        blog_jplayer : function() {
            $('.jp-jplayer').each(function () {
                var $this = $(this),
                    url = $this.data('audio'),
                    title = $this.data('title'),
                    type = url.substr(url.lastIndexOf('.') + 1),
                    player = '#' + $this.data('player'),
                    audio = {};
                audio[type] = url;
                audio['title'] = title;
                $this.jPlayer({
                    ready              : function () {
                        $this.jPlayer('setMedia', audio);
                    },
                    swfPath            : '../plugins/jquery.jPlayer',
                    cssSelectorAncestor: player
                });
            });
            Core.processWidthAudioPlayer();
        },
        processWidthAudioPlayer : function(){
            setTimeout(function(){
                $('.jp-audio').each(function(){
                    var _width = $(this).outerWidth() - $('.jp-play-pause',this).outerWidth() - $('.jp-volume',this).outerWidth() - 46;
                    $('.jp-progress',this).width(_width);

                });
            },100);
        },
        blog_infinite_scroll : function() {
            $('.blog-inner').infinitescroll({
                navSelector  	: "#infinite_scroll_button",
                nextSelector 	: "#infinite_scroll_button a",
                itemSelector 	: "article",
                loading : {
                    'selector' : '#infinite_scroll_loading',
                    'img' : zorka_theme_url + 'assets/images/ajax-loader.gif',
                    'msgText' : 'Loading...',
                    'finishedMsg' : ''
                }
            }, function(newElements, data, url){
                var $newElems = $(newElements).css({
                    opacity: 0
                });
                $newElems.imagesLoaded(function () {
                    Core.owlCarousel();
                    Core.blog_jplayer();
                    $newElems.animate({
                        opacity: 1
                    });
                });

            });
        },
		login_link_event: function() {
			$('.zorka-login-link-sign-in, .zorka-login-link-sign-up').off('click').click(function(event) {
				event.preventDefault();
				var action_name = 'zorka_login';
				if ($(this).hasClass('zorka-login-link-sign-up')) {
					action_name = 'zorka_sign_up'
				}
				var popupWrapper = '#zorka-popup-login-wrapper';
				Core.show_loading();
				$.ajax({
					type   : 'POST',
					data   : 'action=' + action_name,
					url    : zorka_ajax_url,
					success: function (html) {
						Core.hide_loading();
						if ($(popupWrapper).length) {
							$(popupWrapper).remove();
						}
						$('body').append(html);

						$(popupWrapper).modal();

						$('#zorka-popup-login-form').submit(function(event) {
							var input_data = $('#zorka-popup-login-form').serialize();
							Core.show_loading();
							jQuery.ajax({
								type   : 'POST',
								data   : input_data,
								url    : zorka_ajax_url,
								success: function (html) {
									Core.hide_loading();
									var response_data = jQuery.parseJSON(html);
									if (response_data.code < 0) {
										jQuery('.login-message', '#zorka-popup-login-form').html(response_data.message);
									}
									else {
										window.location.reload();
									}
								},
								error  : function (html) {
									Core.hide_loading();
								}
							});
							event.preventDefault();
							return false;
						});
					},
					error  : function (html) {
						Core.hide_loading();
					}
				});
			});
		},
		show_loading: function() {
			$('body').addClass('overflow-hidden');
			if ($('.loading-wrapper').length == 0) {
				$('body').append('<div class="loading-wrapper"><span class="spinner-double-section-far"></span></div>');
			}
		},
		hide_loading: function() {
			$('.loading-wrapper').fadeOut(function() {
				$('.loading-wrapper').remove();
				$('body').removeClass('overflow-hidden');
			});
		},
		search_box_header_process: function(){
			$('.search-header-wrapper .seach-header-input').on('keyup', function(event){
				var s_timeOut_search = null;
				if (event.altKey || event.ctrlKey || event.shiftKey || event.metaKey) {
					return;
				}

				var keys = ["Control", "Alt", "Shift"];
				if (keys.indexOf(event.key) != -1) return;
				switch (event.which) {
					case 37:
					case 39:
						break;
					case 27:	// ESC
						$('.search-header-result').html('');
						$(this).val('');
						break;
					case 38:	// UP
						s_up();
						break;
					case 40:	// DOWN
						s_down();
						break;
					case 13:	//ENTER
						var $item = $('li.selected a', '.search-header-result');
						if ($item.length == 0) {
							event.preventDefault();
							return false;
						}
						s_enter();
						event.preventDefault();
						break;
					default:
						clearTimeout(Core.timeOut_search);
						Core.timeOut_search = setTimeout(s_search, 500);
						break;
				}
			});

			function s_search() {
				var keyword = $('.search-header-wrapper .seach-header-input').val();
				var $result = $('.search-header-wrapper .search-header-result');
				var $icon = $('.search-header-wrapper > .search-header-inner > i.fa');

				if (keyword.length < 3) {
					$result.html('');
					return;
				}
				$icon.attr('class','fa fa-spin fa-spinner');
				$.ajax({
					type   : 'POST',
					data   : 'action=result_search_product&keyword=' + keyword + '&cate_id=' + $('.search-header-wrapper .product-category >  span').attr('data-id'),
					url    : zorka_ajax_url,
					success: function (data) {
						$icon.attr('class','fa fa-search');
						var html = '';
						if (data) {
							var items = $.parseJSON(data);
							if (items.length) {
								html +='<ul>';
								if (items[0]['id'] == -1) {
									html += '<li class="selected">' + items[0]['title']  + '</li>';
								}
								else {
									$.each(items, function (index) {
										if (index == 0) {
											html += '<li class="selected">';
										}
										else {
											html += '<li>';
										}

										html += '<a href="' + this['guid'] + '">';
										html += this['thumb'];
										html += this['title'] + '</a>';
										html += '<div class="price">' + this['price'] + '</div>';
										html += '</li>';
									});
								}
							}
							else {
								html = '</ul>';
							}
						}
						$result.html(html);
						$result.scrollTop(0);
					},
					error : function(data) {
						$icon.attr('class','fa fa-search');
					}
				});
			}
			function s_up(){
				var $item = $('li.selected', '.search-header-result');
				if ($('li', '.search-header-result').length < 2) return;
				var $prev = $item.prev();
				$item.removeClass('selected');
				if ($prev.length) {
					$prev.addClass('selected');
				}
				else {
					$('li:last', '.search-header-result').addClass('selected');
					$prev = $('li:last', '.search-header-result');
				}
				if ($prev.position().top < $('.search-header-result').scrollTop()) {
					$('.search-header-result').scrollTop($prev.position().top);
				}
				else if ($prev.position().top + $prev.outerHeight() > $('.search-header-result').scrollTop() + $('.search-header-result').height()) {
					$('.search-header-result').scrollTop($prev.position().top - $('.search-header-result').height() + $prev.outerHeight());
				}
			}
			function s_down() {
				var $item = $('li.selected', '.search-header-result');
				if ($('li', '.search-header-result').length < 2) return;
				var $next = $item.next();
				$item.removeClass('selected');
				if ($next.length) {
					$next.addClass('selected');
				}
				else {
					$('li:first', '.search-header-result').addClass('selected');
					$next = $('li:first', '.search-header-result');
				}
				if ($next.position().top < jQuery('.search-header-result').scrollTop()) {
					$('.search-header-result').scrollTop($next.position().top);
				}
				else if ($next.position().top + $next.outerHeight() > $('.search-header-result').scrollTop() + $('.search-header-result').height()) {
					$('.search-header-result').scrollTop($next.position().top - $('.search-header-result').height() + $next.outerHeight());
				}
			}
			function s_enter() {
				var $item = $('li.selected a', '.search-header-result');
				if ($item.length > 0) {
					window.location = $item.attr('href');
				}
			}
		},
		search_popup_process: function () {
			$('header .icon-search-menu').click(function(event){
				event.preventDefault();
				Core.search_popup_open();
			});
			$('.zorka-dismiss-modal, .modal-backdrop', '#zorka-modal-search').click(function(){
				Core.search_popup_close();
			});
			$('.zorka-search-wrapper button > i.ajax-search-icon').click(function(){
				s_search();
			});

			// search
			$('#search-ajax', '#zorka-modal-search').on('keyup', function(event){
				var s_timeOut_search = null;
				if (event.altKey || event.ctrlKey || event.shiftKey || event.metaKey) {
					return;
				}

				var keys = ["Control", "Alt", "Shift"];
				if (keys.indexOf(event.key) != -1) return;
				switch (event.which) {
					case 27:	// ESC
						Core.search_popup_close();
						break;
					case 38:	// UP
						s_up();
						break;
					case 40:	// DOWN
						s_down();
						break;
					case 13:	//ENTER
						var $item = $('li.selected a', '#zorka-modal-search');
						if ($item.length == 0) {
							event.preventDefault();
							return false;
						}
						s_enter();
						break;
					default:
						clearTimeout(Core.timeOut_search);
						Core.timeOut_search = setTimeout(s_search, 500);
						break;
				}
			});

			function s_up(){
				var $item = $('li.selected', '#zorka-modal-search');
				if ($('li', '#zorka-modal-search').length < 2) return;
				var $prev = $item.prev();
				$item.removeClass('selected');
				if ($prev.length) {
					$prev.addClass('selected');
				}
				else {
					$('li:last', '#zorka-modal-search').addClass('selected');
					$prev = $('li:last', '#zorka-modal-search');
				}
				if ($prev.position().top < jQuery('#zorka-modal-search .ajax-search-result').scrollTop()) {
					jQuery('#zorka-modal-search .ajax-search-result').scrollTop($prev.position().top);
				}
				else if ($prev.position().top + $prev.outerHeight() > jQuery('#zorka-modal-search .ajax-search-result').scrollTop() + jQuery('#zorka-modal-search .ajax-search-result').height()) {
					jQuery('#zorka-modal-search .ajax-search-result').scrollTop($prev.position().top - jQuery('#zorka-modal-search .ajax-search-result').height() + $prev.outerHeight());
				}
			}
			function s_down() {
				var $item = $('li.selected', '#zorka-modal-search');
				if ($('li', '#zorka-modal-search').length < 2) return;
				var $next = $item.next();
				$item.removeClass('selected');
				if ($next.length) {
					$next.addClass('selected');
				}
				else {
					$('li:first', '#zorka-modal-search').addClass('selected');
					$next = $('li:first', '#zorka-modal-search');
				}
				if ($next.position().top < jQuery('#zorka-modal-search .ajax-search-result').scrollTop()) {
					jQuery('#zorka-modal-search .ajax-search-result').scrollTop($next.position().top);
				}
				else if ($next.position().top + $next.outerHeight() > jQuery('#zorka-modal-search .ajax-search-result').scrollTop() + jQuery('#zorka-modal-search .ajax-search-result').height()) {
					jQuery('#zorka-modal-search .ajax-search-result').scrollTop($next.position().top - jQuery('#zorka-modal-search .ajax-search-result').height() + $next.outerHeight());
				}
			}
			function s_enter() {
				var $item = $('li.selected a', '#zorka-modal-search');
				if ($item.length > 0) {
					window.location = $item.attr('href');
				}
			}
			function s_search() {
				var keyword = $('input[type="search"]', '#zorka-modal-search').val();
				if (keyword.length < 2) {
					$('.ajax-search-result', '#zorka-modal-search').html('');
					return;
				}
				$('.ajax-search-icon', '#zorka-modal-search').addClass('fa fa-spinner fa-spin');
				$('.ajax-search-icon', '#zorka-modal-search').removeClass('icon-search');
				$.ajax({
					type   : 'POST',
					data   : 'action=result_search&keyword=' + keyword,
					url    : zorka_ajax_url,
					success: function (data) {
						$('.ajax-search-icon', '#zorka-modal-search').removeClass('fa fa-spinner fa-spin');
						$('.ajax-search-icon', '#zorka-modal-search').addClass('icon-search');
						var html = '';
						if (data) {
							var items = $.parseJSON(data);
							if (items.length) {
								html +='<ul>';
								if (items[0]['id'] == -1) {
									html += '<li class="selected">' + items[0]['title']  + '</li>';
								}
								else {
									$.each(items, function (index) {
										if (index == 0) {
											html += '<li class="selected">';
										}
										else {
											html += '<li>';
										}
										if (this['title'] == null || this['title'] == '') {
											html += '<a href="' + this['guid'] + '">' + this['date'] + '</a>';
										}
										else {
											html += '<a href="' + this['guid'] + '">' + this['title'] + '</a>';
											html += '<span>' + this['date'] + ' </span>';
										}

										html += '</li>';
									});
								}


								html +='</ul>';
							}
							else {
								html = '';
							}
						}
						$('.ajax-search-result', '#zorka-modal-search').html(html);
						jQuery('#zorka-modal-search .ajax-search-result').scrollTop(0);
					},
					error : function(data) {
						$('.ajax-search-icon', '#zorka-modal-search').removeClass('fa fa-spinner fa-spin');
						$('.ajax-search-icon', '#zorka-modal-search').addClass('icon-search');
					}
				});
			}
		},
		search_popup_open : function() {
			if (!$('#zorka-modal-search').hasClass('in')) {
				$('#zorka-modal-search').show();
				setTimeout(function() {
					$('#zorka-modal-search').addClass('in');
				}, 300);
				$('#search-ajax', '#zorka-modal-search').focus();
				$('#search-ajax', '#zorka-modal-search').val('');
				$('.ajax-search-result', '#zorka-modal-search').html('')
			}
		},
		search_popup_close : function() {
			if ($('#zorka-modal-search').hasClass('in')) {
				$('#zorka-modal-search').removeClass('in');
				setTimeout(function(){
					$('#zorka-modal-search').hide();
				}, 300);
			}
		},
        page_animsition: function() {

            $(".animsition").animsition({

                inClass               :   'fade-in',
                outClass              :   'fade-out',
                inDuration            :    1500,
                outDuration           :    800,
                linkElement           :   '.x-menu-a-text:not([href^="#"]):not([target="_blank"]):not([href^="javascript"])',
                //linkElement   :   'a:not([target="_blank"]):not([href^=#])',
                loading               :    true,
                loadingParentElement  :   'body', //animsition wrapper element
                loadingClass          :   'animsition-loading',
                unSupportCss          : [ 'animation-duration',
                    '-webkit-animation-duration',
                    '-o-animation-duration'
                ],
                //"unSupportCss" option allows you to disable the "animsition" in case the css property in the array is not supported by your browser.
                //The default setting is to disable the "animsition" in a browser that does not support "animation-duration".

                overlay               :   false,

                overlayClass          :   'animsition-overlay-slide',
                overlayParentElement  :   'body'
            });
        },
        process_product: function() {
            Core.product_sale_countdown();
            Core.add_cart_quantity();
            Core.checkout();
            Core.tooltip();
            Core.product_quick_view();
            Core.product_animated();
            Core.product_category();
        },
		product_sale_countdown : function() {
            $('.product-deal-countdown').each(function(){
                var date_end = $(this).data('date-end');
                var $this = $(this);
                $(this).countdown(date_end,function(event){
                    count_down_callback(event,$this);
                }).on('update.countdown', function(event) {
                    count_down_callback(event,$this);
                });

            });

            function count_down_callback(event,$this) {
                var seconds = parseInt(event.offset.seconds);
                var minutes = parseInt(event.offset.minutes);
                var hours = parseInt(event.offset.hours);
                var days = parseInt(event.offset.totalDays);

                if ((seconds == 0)&& (minutes == 0) && (hours == 0) && (days == 0)) {
                    $($this).remove();
                    return;
                }


                $('.countdown-day',$this).text(days);
                $('.countdown-hours',$this).text(hours);
                $('.countdown-minutes',$this).text(minutes);
                $('.countdown-seconds',$this).text(seconds);
            }
        },
        add_cart_quantity : function() {
           $(document).off('click','.quantity .btn-number').on('click','.quantity .btn-number',function(event) {
                event.preventDefault();
                var type = $(this).data('type');
                var input =$('input',$(this).parent());
                var current_value = parseInt(input.val());

               var max = input.attr('max');
               if (typeof(max) == 'undefined' ) {
                   max = 100;
               }

               var min = input.attr('min');
               if (typeof(min) == 'undefined' ) {
                   min = 0;
               }
               if (!isNaN(current_value)) {
                    if (type == 'minus') {
                        if(current_value > min) {
                            input.val(current_value - 1).change();
                        }
                        if(parseInt(input.val()) == min) {
                            $(this).attr('disabled', true);
                        }
                    }

                    if (type == 'plus') {

                        if(current_value < max) {
                            input.val(current_value + 1).change();
                        }
                        if(parseInt(input.val()) == max) {
                            $(this).attr('disabled', true);
                        }
                    }
                } else {
                    input.val(0);
                }
            });


            $('input','.quantity').focusin(function(){
                $(this).data('oldValue', $(this).val());
            });

            $('input','.quantity').on('change',function() {
                var input = $(this);
                var max = input.attr('max');
                if (typeof(max) == 'undefined' ) {
                    max = 100;
                }

                var min = input.attr('min');
                if (typeof(min) == 'undefined' ) {
                    min = 0;
                }

                var current_value = parseInt(input.val());

                if(current_value >= min) {
                    $(".btn-number[data-type='minus']",$(this).parent()).removeAttr('disabled');
                } else {
                    alert('Sorry, the minimum value was reached');
                    $(this).val($(this).data('oldValue'));
                }

                if(current_value <= max) {
                    $(".btn-number[data-type='plus']",$(this).parent()).removeAttr('disabled');
                } else {
                    alert('Sorry, the maximum value was reached');
                    $(this).val($(this).data('oldValue'));
                }

            });

        },
        checkout : function() {

            setTimeout(function(){
                $('form.checkout_coupon').show();
            },300);

            $('#accordion').on('show.bs.collapse', function () {
                $('#accordion .in').collapse('hide');
            });


            var checkout_method = $('input[name="checkout-method"]').val();
            if (checkout_method == 'guest') {
                $('#heading-create-account').parent().fadeOut();
                $('#button_create_account_continue').attr('href','#billing-address');
            } else {
                $('#heading-create-account').parent().fadeIn();
                $('#button_create_account_continue').attr('href','#create-account');
            }


            $('input[name="checkout-method"]').on('change',function(){
                var checkout_method = $(this).val();
                if (checkout_method == 'guest') {
                    $('#heading-create-account').parent().fadeOut();
                    $('#button_create_account_continue').attr('href','#billing-address');
                } else {
                    $('#heading-create-account').parent().fadeIn();
                    $('#button_create_account_continue').attr('href','#create-account');
                }
            });


            $('a[data-toggle="collapse"]','.woocommerce-checkout').on('click',function(){
                var tab = $('a[href="'+$(this).attr('href')+'"]','.panel-heading');
                var tab_panel = tab.parent().next();
                setTimeout(function(){
                    if (tab_panel.hasClass('in')){
                        tab.attr('aria-expanded','true');
                    } else {
                        tab.attr('aria-expanded','false');
                    }
                },400);
            });


        },
        tooltip : function() {
            if ($().tooltip) {

                $('[data-toggle="tooltip"]').tooltip();

                $('.yith-wcwl-add-to-wishlist','.product-function').tooltip({
                    title : zorka_constant.product_wishList
                });

                $('.compare','.product-function').tooltip({
                    title : zorka_constant.product_compare
                });

                $('.yith-wcwl-add-to-wishlist','.product-button').tooltip({
                    title : zorka_constant.product_wishList
                });

                $('.compare','.product-button').tooltip({
                    title : zorka_constant.product_compare
                });
                $('.add_to_cart_button','.product-style-two .product-button').each(function(){
                    var $clone = $(this).clone(true);
                    $($clone).html('');
                    var $buttonWrap = $('<span class="add_cart_button_wrap"></span>');
                    $($buttonWrap).append($clone);
                    $(this).after($buttonWrap);
                    $(this).remove();
                });
                $("body").bind("added_to_cart", function(event,fragments, cart_hash, $thisbutton) {
                    var button = $thisbutton.parent();
                    setTimeout(function() {
                        button.tooltip('hide').attr('title', zorka_constant.product_view_cart).tooltip('fixTitle');
                    }, 500);
                })

                $('.add_cart_button_wrap','.product-style-two .product-button').tooltip({
                    title : zorka_constant.product_add_to_cart
                });
            }
        },
        product_quick_view : function() {
            $('.product-quick-view').on('click',function(event){
                event.preventDefault();
                var product_id = $(this).data('product_id');
                var popupWrapper = '#popup-product-quick-view-wrapper';
                Core.show_loading();
                $.ajax({
                    url : zorka_ajax_url,
                    data : {
                        action : 'product_quick_view',
                        id :  product_id
                    },
                    success : function(html) {
                        Core.hide_loading();
                        if ($(popupWrapper).length) {
                            $(popupWrapper).remove();
                        }
                        $('body').append(html);
                        Core.add_cart_quantity();
                        Core.product_sale_countdown();
                        $(popupWrapper).modal();
                    },
                    error  : function (html) {
                        Core.hide_loading();
                    }
                });

            });
        },
        product_animated : function() {
            var window_width = $(window).width();
            $('.product_animated').each(function(){
                var col = parseInt( $(this).data('col'),10);
                if (isNaN(col)) {
                    col = 0;
                }
                var index = 0;
                $('div.product-item-wrapper:not(".umScaleIn")',$(this)).each(function (i) {
                    var el = $(this);

                    if ((col > 0) && ( i % col == 0)){
                        index = 0;
                    }
                    var animation_delay = index * 300;
                    index++;
                    if (window_width > 991) {
                        el.css({
                            '-webkit-animation-delay':  animation_delay +'ms',
                            '-moz-animation-delay':     animation_delay +'ms',
                            '-ms-animation-delay' : animation_delay +'ms',
                            '-o-animation-delay' : animation_delay + 'ms',
                            'animation-delay':          animation_delay + 'ms'
                        });
                    }
                    el.waypoint(function(){
                        el.addClass('animated').addClass('umScaleIn');
                    },{
                        triggerOnce: true,
                        offset: '90%'
                    });
                });
            });
        },
        product_category : function() {
            $('li','.widget_product_categories').each(function(){
                if ($(this).children('ul.children').length > 0) {
                    if ($(this).hasClass('current-cat') || $(this).hasClass('current-cat-parent')) {
                        $(this).prepend('<span class="c-caret pe-7s-angle-down"></span>');
                    } else {
                        $(this).prepend('<span class="c-caret pe-7s-angle-right"></span>');
                    }

                }
            });

            $(document).on('click','.widget_product_categories .c-caret',function(){
                if ($(this).parent().children('ul').is(':hidden')) {
                    $(this).removeClass('pe-7s-angle-right').addClass('pe-7s-angle-down');
                    $(this).parent().children('ul').slideDown(250, 'linear');
                } else {
                    $(this).parent().removeClass('cat-open');
                    $(this).removeClass('pe-7s-angle-down').addClass('pe-7s-angle-right');
                    $(this).parent().children('ul').slideUp(250, 'linear');
                }
            });

        },
        process_footer : function() {
           var $window = $(window);		//Window object
           var $footer = $('.enable-parallax-footer');
           var $body = $('body');
           if (($window.width() >= 992) && ($window.height() >= $footer.height())) {
                $body.css({
                   'padding-bottom' : $footer.height() + 'px'
                });
               $body.removeClass('footer-static');
           } else {
               $body.addClass('footer-static');
           }

            setTimeout(function(){
                $footer.css({'visibility' : 'visible'});
            },2000);
        },
		affix_header: function(try_count, isFirst) {
			var $header = $('header.main-header');
            if ($header.hasClass('sticky-disable')) {
                return;
            }
			var $admin_bar = $('#wpadminbar');
			var top = 0;
			if ($admin_bar.length > 0) {
				$header.css('top', $admin_bar.outerHeight() + 'px');
			}

			$header.attr('affix-offset',$header.outerHeight());
			var affix_translate = $header.outerHeight() - $('.menu-wrapper', $header).outerHeight();
			if (isFirst && !$header.hasClass('header-4') && !$header.hasClass('header-8')) {
				$('body').css('padding-top',$header.outerHeight() + 'px');
			}

			if ($('style#affix-style').length == 0) {
				$('body').append('<style id="affix-style"></style>');
			}
			var css = 'header.main-header.affix-header {-webkit-transform: translateY(-' + affix_translate + 'px);';
			css += '-moz-transform:    translateY(-' + affix_translate + 'px);';
			css += '-ms-transform:     translateY(-' + affix_translate + 'px);';
			css += '-o-transform:      translateY(-' + affix_translate + 'px);';
			css += 'transform:         translateY(-' + affix_translate + 'px);';
			css += '}';
			$('style#affix-style').html(css);

			if (try_count > 0) {
				setTimeout(function(){
					Core.affix_header(try_count-1, false);
				}, 500);
			}
		},
		affix_header_scroll: function() {
			var $header = $('header.main-header');
            if ($header.hasClass('sticky-disable')) {
                return;
            }
			var affix_top = $header.attr('affix-offset');
			if (!Core.is_desktop()) {
				$header.removeClass('affix-header');
				return;
			}
			if ($(window).scrollTop() > affix_top){
				if (!$header.hasClass('affix-header')) {
					$header.addClass('affix-header');
				}
			}
			else{
				if ($header.hasClass('affix-header')) {
					$('footer.main-footer').css('opacity','0');
					$header.removeClass('affix-header');
					setTimeout(function(){
						$('footer.main-footer').css('opacity','');
					}, 500);
				}
			}
		},
		is_desktop: function() {
			var responsive_breakpoint = 991;
			var $menu = $('.x-nav-menu');
			if (($menu.length > 0) && (typeof ($menu.attr('responsive-breakpoint')) != "undefined" ) && !isNaN(parseInt($menu.attr('responsive-breakpoint'), 10)) ) {
				responsive_breakpoint = parseInt($menu.attr('responsive-breakpoint'), 10);
			}
			return window.matchMedia('(min-width: ' + (responsive_breakpoint + 1)  + 'px)').matches;
		},
		menu_product_category: function() {
			$('ul.product-category-dropdown ul.children').each(function() {
				$(this).parent().addClass('has-children');
			});
		},
        float_header_background : function() {
            var _height = $('.main-header').height();
            $('.float-header-background').css('height',_height + 'px');

        },
        setProductListResponsive:function(){
            var window_width = $(window).width();
            if(window_width <=992 ){
                if(!$('.product-style-four').hasClass('container')){
                    $('.product-style-four').addClass('container')
                }
            }else
                $('.product-style-four').removeClass('container')
        },
        setPositionPageTitle:function(){
            var sectionTitle = $('.page-title-wrapper');
            if( $('header').hasClass('header-4')
                || $('header').hasClass('header-8')
                ){
                if(sectionTitle!=null && typeof sectionTitle!='undefined'){
                    var headerHeight = $('header').outerHeight();
                    var bufferTop = 75;
                    var bufferBottom = 100;
                    if($('body').hasClass('page'))
                        bufferBottom = 150;
                    $(sectionTitle).css('padding-top',headerHeight + bufferTop);
                    $(sectionTitle).css('padding-bottom',bufferBottom);

                    var pageTitleInner = $('.page-title-inner');
                    $(pageTitleInner).css('height','auto');
                    $(pageTitleInner).css('position','relative');
                    $(pageTitleInner).css('transition','all 0.5s');
                    $(pageTitleInner).css('-webkit-transition','all 0.5s ease-in-out');
                    $(pageTitleInner).css('-moz-transition','all 0.5s ease-in-out');
                    $(pageTitleInner).css('-o-transition','all 0.5s ease-in-out');
                    $(pageTitleInner).css('-ms-transition','all 0.5s ease-in-out');
                    $(window).on('scroll', function (event) {
                        if($(window).scrollTop() > 0){
                            $(pageTitleInner).css('opacity','0');
                        }else{
                            $(pageTitleInner).css('opacity','1');
                        }
                    });


                }
            }
        },
        setResponsiveProductShortcode:function(){
            var product_listing = $('.product-listing','.shortcode-product.product-style-four');
            if(product_listing!=null && product_listing!='undefined'){
                var col = $(product_listing).attr('data-col');
                var totalItem = $('.product-item-wrapper', product_listing).length;
                var windowWidth = $(window).width();
                if(windowWidth>=1349 && windowWidth<=1499){
                    col = 4;
                    var remain = totalItem % col;
                    if(remain > 0){
                        var index = (totalItem - remain);
                        var i = 1;
                        $(".product-item-wrapper", product_listing).each(function(){
                            if(i > index)
                                $(this).hide();
                            else
                                $(this).show();
                            i++;
                        })
                    }
                }else{
                    $(".product-item-wrapper", product_listing).show();
                }
            }
        }

    };
    $(document).ready(function(){
        Core.initialize();
    });
})(jQuery);