/**
 * Created by hoantv on 2015-03-26.
 */
(function($) {
	"use strict";
	var APP = {
		timeOutHoverMenu: null,

		initialize: function() {
			APP.event();
			APP.append_xmenu_toggle();
		},
		append_xmenu_toggle: function() {
			$('.x-nav-menu-toggle-inner').each(function() {
				$(this).click(function() {
					$(this).toggleClass('x-in');
					var data_rel = $(this).attr('data-ref');
					$('#' + data_rel).slideToggle();
				});
			});
		},
		event: function() {
			APP.window_resize();
			APP.menu_event();
			APP.window_scroll();
			APP.tabs_position(5);
		},
		window_scroll: function(){
			$(window).on('scroll',function(event){
				$('.x-nav-menu > li').each(function() {
					APP.process_menu_position(this);
				});
			});
		},
		window_resize: function() {
			$(window).resize(function() {
				APP.tabs_position(5);
				$('.x-nav-menu > li').each(function() {
					APP.process_menu_position(this);
					APP.process_tab_padding(this);
				});
				$('.x-nav-menu').each(function(){
					var menu_id = $(this).attr('id');
					if (APP.is_desktop(menu_id)) {
						$(this).css('display','');
						$('ul.x-sub-menu', this).css('display','');
						$('.x-nav-menu-toggle-inner', $(this).parent()).removeClass('x-in');
					}
				});
			});
		},
		tabs_position: function(number_retry) {
			$('.x-sub-menu-tab').each(function(){
				var $this = $(this);
				var tab_left_width = $(this).parent().outerWidth();
				if ($('> li.x-menu-active', this).length == 0) {
					$('> li:first-child', this).addClass('x-menu-active');
				}
				$('> li', this).each(function(){
					$('> ul', this).css('left', tab_left_width + 'px');
				});

				$('> li.x-menu-active', this).each(function(){
					APP.tab_position($(this));
				});
			});
			if (number_retry > 0) {
				setTimeout(function() {
					APP.tabs_position(number_retry - 1);
				}, 500);
			}
		},
		tab_position: function($tab) {
			var tab_right_height = 0;
			if ($('> ul', $tab).length != 0) {
				tab_right_height = $('> ul', $tab).outerHeight();
			}
			$tab.parent().css('min-height', tab_right_height + 'px');
		},
		menu_event: function() {
			$('.x-sub-menu-tab > li:first-child').addClass('x-menu-active');
			$('.x-nav-menu > li').each(function() {
				APP.process_menu_position(this);
				APP.process_tab_padding(this);
			});

			$('.x-nav-menu').each(function(){
				var transition_name = APP.get_transition_name(this);
				var menu_id = $(this).attr('id');
				APP.transition_menu(this, transition_name, menu_id);
				APP.process_menu_mobile_click(this, menu_id);

				$('.x-sub-menu-tab > li', this).hover(function(){
					if (!APP.is_desktop(menu_id)) {
						return;
					}
					$('> li', $(this).parent()).removeClass('x-menu-active');
					$(this).addClass('x-menu-active');
					APP.tab_position($(this));
				}, function(){
				});
			});


		},
		process_menu_mobile_click: function(target, menu_id) {
			$('li.x-menu-item > a > b.x-caret', target).click(function(event){
				if (APP.is_desktop(menu_id)) {
					return;
				}
				event.preventDefault();
				$(this).parent().parent().toggleClass('x-sub-menu-open');
				$('> ul.x-sub-menu', $(this).parent().parent()).slideToggle();
			});
		},
		process_tab_padding: function(target) {
			var $this = $(target);
			if ($this.hasClass('x-sub-menu-multi-column')) {
				var $tab = $('> ul.x-sub-menu > li.x-tabs', $this);
				if ($tab.length > 0) {
					$(' > ul.x-sub-menu', $this).addClass('no-padding');
				}
			}
			$('> ul.x-sub-menu > li').each(function() {
				APP.process_tab_padding(this);
			});
		},
		transition_menu: function(target, transition_name, menu_id) {
			var $this = $(target);
			$('> li.x-menu-item', $this).each(function(){
				var transition_name_current = APP.get_transition_name($('> ul', this));
				if (transition_name_current == '') {
					transition_name_current = transition_name;
				}
				var time_out_duration = 300;
				if (transition_name_current == '') {
					time_out_duration = 100;
				}
				$(this).hover(function() {
					if (!APP.is_desktop(menu_id)) {
						return;
					}

					var $this_li = $(this);
					clearTimeout(APP.timeOutHoverMenu);
					APP.timeOutHoverMenu = setTimeout(function() {
						$this_li.addClass('x-active');
						if (transition_name_current != '') {
							$('> ul', $this_li).addClass(transition_name_current);
						}

					}, time_out_duration);
				},
				function() {
					if (!APP.is_desktop(menu_id)) {
						return;
					}
					clearTimeout(APP.timeOutHoverMenu);
					if ($(this).hasClass('x-active')) {
						$('> ul', this).addClass(transition_name_current + '-out');
						var $this_li = $(this);
						setTimeout(function(){
							if (transition_name_current != '') {
								$('> ul', $this_li).removeClass(transition_name_current);
								$('> ul', $this_li).removeClass(transition_name_current + '-out');
							}
							$($this_li).removeClass('x-active');
						}, time_out_duration);
					}
				});

				if (!$(this).hasClass('x-sub-menu-multi-column')) {
					APP.transition_menu($('> ul.x-sub-menu', this), transition_name, menu_id);
				}
			});
		},

		get_transition_name: function(target) {
			var transition_name = '';
			if ($(target).hasClass('x-animate-slide-up')){
				transition_name = 'x-slide-up';
			}
			else if ($(target).hasClass('x-animate-slide-down')){
				transition_name = 'x-slide-down';
			}
			else if ($(target).hasClass('x-animate-slide-left')){
				transition_name = 'x-slide-left';
			}
			else if ($(target).hasClass('x-animate-slide-right')){
				transition_name = 'x-slide-right';
			}
			else if ($(target).hasClass('x-animate-fade-in')){
				transition_name = 'x-fade-in';
			}
			else if ($(target).hasClass('x-animate-sign-flip')){
				transition_name = 'x-sign-flip';
			}
			return transition_name;
		},

		process_menu_position: function(target) {
			var $this = $(target);
			var $menuBar = $('.x-nav-menu');
			var $parentMenu =  $(target).parent();
			if ($this.hasClass('x-pos-left-menu-parent')) {
				APP.process_position_left_menu_parent(target);
			}
			else if ($this.hasClass('x-pos-right-menu-parent')) {
				APP.process_position_right_menu_parent(target);
			}
			else if ($this.hasClass('x-pos-center-menu-parent')) {
				APP.process_position_center_menu_parent(target);
			}
			else if ($this.hasClass('x-pos-left-menu-bar')) {
				APP.process_position_left_menu_bar(target);
			}
			else if ($this.hasClass('x-pos-right-menu-bar')) {
				APP.process_position_right_menu_bar(target);
			}
			else if ($this.hasClass('x-pos-full')) {
				//None
			}
			else {
				APP.process_position_right_menu_parent(target);
			}
		},

		get_margin_left: function(target) {
			var margin_left = $(target).css('margin-left');
			try {
				margin_left = parseInt(margin_left.replace('px',''), 10);
			}
			catch (ex) {
				margin_left = 0;
			}
			return margin_left;
		},
		process_position_left_menu_parent: function(target) {
			var $this = $(target);
			var $menuBar = $('.x-nav-menu');
			var $sub_menu = $('> ul.x-sub-menu', $this);
			if ($sub_menu.length == 0) {
				return;
			}

			if ($menuBar.outerWidth() <= $sub_menu.outerWidth()) {
				$sub_menu.css('left','0');
				$sub_menu.css('right','0');
			}
			else {
				var margin_left = APP.get_margin_left(target);

				var right = $menuBar.outerWidth() - $(target).outerWidth() - $(target).position().left - margin_left;
				if ($(target).outerWidth() + $(target).position().left + margin_left < $sub_menu.outerWidth()) {
					$sub_menu.css('left','0');
					$sub_menu.css('right','auto');
				}
				else {
					$sub_menu.css('left','auto');
					$sub_menu.css('right',right + 'px');
				}
			}
		},
		process_position_right_menu_parent: function(target) {
			var $this = $(target);
			var $menuBar = $('.x-nav-menu');
			var $sub_menu = $('> ul.x-sub-menu', $this);
			if ($sub_menu.length == 0) {
				return;
			}
			var margin_left = APP.get_margin_left(target);

			if ($menuBar.outerWidth() <= $sub_menu.outerWidth()) {
				$sub_menu.css('left','0');
				$sub_menu.css('right','0');
			}
			else {
				if ($menuBar.outerWidth() - $(target).position().left - margin_left < $sub_menu.outerWidth()) {
					$sub_menu.css('left','auto');
					$sub_menu.css('right','0');
				}
				else {
					$sub_menu.css('left',($(target).position().left + margin_left) + 'px');
					$sub_menu.css('right', 'auto');
				}
			}
		},
		process_position_center_menu_parent: function(target) {
			var $this = $(target);
			var $menuBar = $('.x-nav-menu');
			var $sub_menu = $('> ul.x-sub-menu', $this);
			if ($sub_menu.length == 0) {
				return;
			}
			if ($menuBar.outerWidth() <= $sub_menu.outerWidth()) {
				$sub_menu.css('left','0');
				$sub_menu.css('right','0');
			}
			else {
				var margin_left = APP.get_margin_left(target);
				var left = ($sub_menu.outerWidth() - $this.outerWidth() - margin_left)/2;
				if (left > $(target).position().left) {
					$sub_menu.css('left','0');
					$sub_menu.css('right','auto');
				}
				else if (left > $menuBar.outerWidth() - $(target).outerWidth() - $(target).position().left) {
					$sub_menu.css('left','auto');
					$sub_menu.css('right','0');
				}
				else {
					$sub_menu.css('left', ($(target).position().left - left) + 'px');
					$sub_menu.css('right', 'auto');
				}
			}
		},
		process_position_left_menu_bar: function(target) {
			var $this = $(target);
			var $sub_menu = $('> ul.x-sub-menu', $this);
			$sub_menu.css('left','0');
			$sub_menu.css('right','auto');
		},
		process_position_right_menu_bar: function(target) {
			var $this = $(target);
			var $sub_menu = $('> ul.x-sub-menu', $this);
			$sub_menu.css('left','auto');
			$sub_menu.css('right','0');
		},

		is_desktop: function(menu_id) {
			var responsive_breakpoint = 991;
			var $menu = $('#' + menu_id);
			if ((typeof ($menu.attr('data-breakpoint')) != "undefined")
				&& !isNaN(parseInt($menu.attr('data-breakpoint'), 10)) ) {
				responsive_breakpoint = parseInt($('#' + menu_id).attr('data-breakpoint'), 10);
			}

			return window.matchMedia('(min-width: ' + (responsive_breakpoint + 1)  + 'px)').matches;
		}
	}
	$(document).ready(function(){
		APP.initialize();
	});
})(jQuery);