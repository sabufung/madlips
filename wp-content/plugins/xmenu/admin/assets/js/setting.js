/**
 * Created by hoantv on 2015-03-26.
 */
(function($) {
	"use strict";
	var XMENU_SETTING = {
		initialize: function() {
			XMENU_SETTING.event();
		},

		event: function() {
			XMENU_SETTING.group_setting_event();
			XMENU_SETTING.open_setting_add_menu();
		},
		group_setting_event: function() {
			$('.xmenu-settings .setting-left li[data-ref]').click(function(){
				if ($(this).hasClass('active')) {
					return;
				}
				var data_ref =  $(this).attr('data-ref');
				console.log(data_ref);
				$('.xmenu-settings .setting-left li[data-ref]').removeClass('active');
				$(this).addClass('active');
				$('.xmenu-settings .setting-right table[data-ref]').removeClass('active');
				$('.xmenu-settings .setting-right table[data-ref="' + data_ref + '"]').addClass('active');
			});
			$('#xmenu-save-setting').click(function(){
				$('#xmenu-save-setting i').attr('class', 'fa fa-spin fa-spinner');
				var data_post = $("#xmenu_settings").serialize();
				$.ajax({
					url:xmenu_meta.ajax_url,
					type   : 'POST',
					data   : data_post,
					dataType : 'json',
					success: function(data) {
						$('#xmenu-save-setting i').attr('class', 'fa fa-save');
						if (data.code < 0) {
							alert(data.message);
							return;
						}
					},
					error: function(data) {
						$('#xmenu-save-setting i').attr('class', 'fa fa-save');
					}
				});
			});
			$('#xmenu-delete-setting').click(function() {
				if (!confirm(xmenu_meta.delete_setting_confirm)) {
					return;
				}
				$('#xmenu-delete-setting i').attr('class', 'fa fa-spin fa-spinner');
				var data_post = {
					action: 'xmenu_delete_setting',
					menu_slug: $('#xmenu_menu_slug').val()
				};
				$.ajax({
					url:xmenu_meta.ajax_url,
					type   : 'POST',
					data   : data_post,
					dataType : 'json',
					success: function(data) {
						if (data.code < 0) {
							$('#xmenu-delete-setting i').attr('class', 'fa fa-remove');
							alert(data.message);
							return;
						}
						window.location.href = window.location.origin + window.location.pathname + '?page=xmenu-settings';
					},
					error: function(data) {
						$('#xmenu-delete-setting i').attr('class', 'fa fa-remove');
					}
				});
			});
		},
		open_setting_add_menu: function() {
			$('#setting-add-menu').click(function(){
				$('#xmenu-setting-popup').fadeIn();
			});
			$('#setting-add-menu-close').click(function() {
				$('#xmenu-setting-popup').fadeOut();
			});
			$('#xmenu-create-button').click(function() {
				if ($('#setting-add-menu-close').hasClass('fa-spin')) {
					return;
				}
				if (($('#xmenu-select-create').val() == null) || ($('#xmenu-select-create').val() == '')) {
					return;
				}
				var data_post = {
					menu_slug: $('#xmenu-select-create').val(),
					action:'xmenu_setting_create'
				};
				$('#setting-add-menu-close').attr('class', 'fa fa-spin fa-spinner');
				$.ajax({
					url:xmenu_meta.ajax_url,
					type   : 'POST',
					data   : data_post,
					dataType : 'json',
					success: function(data) {
						if (data.code < 0) {
							$('#setting-add-menu-close').attr('class', 'fa fa-close');
							alert(data.message);
							return;
						}
						window.location.href = window.location.origin + window.location.pathname + '?page=xmenu-settings&menu=' + $('#xmenu-select-create').val();
					},
					error: function(data) {
						$('#setting-add-menu-close').attr('class', 'fa fa-close');
					}
				});
			});
		}

	}
	$(document).ready(function(){
		XMENU_SETTING.initialize();
	});
})(jQuery);