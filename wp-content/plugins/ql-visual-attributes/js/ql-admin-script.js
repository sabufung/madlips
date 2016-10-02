/**
 * QueryLoop Plugin Admin Script
 * http://queryloop.com
 */
;var QL_VisualAttributes_Admin = {};

(function($){

	'use strict';

	String.prototype.sanitize = function(){ return this.replace(/[!"#$%&'()*+.\/:;<>?@^`{|}~]/g, "\\$&") };

	QL_VisualAttributes_Admin = {

		init: function() {
			$('body').trigger('wc-enhanced-select-init');
			this.setVisibility();
			this.showOnLoop();
			this.color();
			this.typeSelect();
			this.openMedia();
			this.iconPicker();
		},

		setVisibility: function() {
			var self = this, $va = $('#visual_attributes');
			self.disableVA( $va.find('#_disable_va') );
			$('#_disable_va').on('change', function(){
				self.disableVA( $(this) );
			});
			$va.find('.va-use-dropdown').each(function(){
				self.dropdownAction( $(this) );
			});
			$va.on('click', '.va-use-dropdown', function(){
				self.dropdownAction( $(this) );
			});
		},

		showOnLoop: function() {
			var $va = $('#visual_attributes');
			$va
			.on('click', 'button.select_all_attributes', function(){
				$(this).closest('.form-field').find('select option').attr("selected","selected");
				$(this).closest('.form-field').find('select').change();
				return false;
			})
			.on('click', 'button.select_no_attributes', function(){
				$(this).closest('.form-field').find('select option').removeAttr("selected");
				$(this).closest('.form-field').find('select').change();
				return false;
			});
		},

		disableVA: function( $self ){
			var status = $self.val();
			if ( 'no' == status ) {
				$('.js-va-clear').stop().slideDown();
			} else if ( 'yes' == status ) {
				$('.js-va-clear').stop().slideUp();
			} else if ( 'setting' == status ) {
				$.post( ajaxurl,
				{
					post_id: woocommerce_admin_meta_boxes.post_id,
					data: status,
					action: 'queryloop_va_status',
					nonce: ql_visual_attributes_adminjs.nonce
				},
				function (response) {
					if ( response.success ) {
						$('.js-va-clear').stop().slideUp();
					} else {
						$('.js-va-clear').stop().slideDown();
					}
				});
			}
		},

		dropdownAction: function( $self ) {
			if ( $self.prop('checked') ) {
				$('.va-toggle-' + $self.data('toggle').sanitize()).stop().slideUp();
				$self.prop('checked', 1);
				$self.next().val(1);
			} else {
				$('.va-toggle-' + $self.data('toggle').sanitize()).stop().slideDown();
				$self.prop('checked', 0);
				$self.next().val(0);
			}
		},

		color: function(){
			// Initialize color pickers
			$('.ql-color-picker').each(function(){
				var $color = $(this),
					$field = $color.next(),
					$label = $color.prev(),
					value = '';
				$color.minicolors( {
					opacity: true,
					change: function(hex, opacity) {
						if ( '' != hex ) {
							value = hex + '_' + opacity;
							$field.val( value );
							$label.hide();
							$(this).next().show();
						} else {
							$field.val('');
							$label.show();
							$(this).next().hide();
						}
					},
					show: function() {
						$label.hide();
					},
					hide: function() {
						if ( '' === $color.val() ) {
							$label.show();
						}
					}
				});
				$color.after(
					$('<a href="#" class="clear-color" style="display:none">&times;</a>').on( 'click', function(e){
						e.preventDefault();
						$color.minicolors( 'value', '' );
						$color.minicolors( 'opacity', '' );
						$field.val('');
						$label.show();
						$(this).hide();
					})
				);
				if ( '' !== $color.val() ) {
					$color.next().show();
					$label.hide();
				}
			});
			$('body').on( 'input paste keyup', '.ql-color-picker', function(e){
				var $field = $(e.target);
				if ( '' !== $field.val() ) {
					$field.parent().prev().hide();
				} else {
					$field.parent().prev().show();
				}
			});
		},

		typeSelect: function() {
			var $va = $('.visual_attributes');
			$va.find('.va_type').each(function(){
				var $self = $(this),
					type = $self.val();
				if ( '' === type ) {
					type = 'image';
				}
				$self.closest( '.va-selector-wrap' ).find( '[data-type="' + type + '"]' ).addClass( 'selected' );
				$self.closest( '.form-field' ).find( '.va_brick.' + type + '-picker-wrap' ).removeClass( 'hidden' );
			});
			$va.on('click', '.va-selector', function(e){
				e.preventDefault();
				var $self = $(this),
					$container = $self.closest('.form-field');
				$container.find('.va_type').val($self.data('type'));
				$self.siblings().removeClass('selected');
				$self.addClass('selected');
				$container.find('.va_brick').hide();
				$container.find('.' + $self.data('type') + '-picker-wrap').show();
			});
		},

		// Open Media interface
		openMedia: function() {
			var file_frame = '',
				$va = $('.visual_attributes');

			$va.find('.open-media').each(function(){
				var $self = $(this),
					$container = $self.closest('.form-field'),
					$field = $container.find('.va_image');

				if ( '' != $field.val() ) {
					var saved_attachment = $.parseJSON( $field.val() );
					$('.va-preview', $container).append($('<a href="#" class="remove-image dashicons dashicons-no-alt"></a><img src="' + saved_attachment.thumbnail + '" />').css('display', 'inline-block'));
				}

				$(this).on('click', function(e){
					e.preventDefault();

					file_frame = wp.media.frames.file_frame = wp.media({
						title: $(this).data('uploader-title'),
						library: {
							type: ['image']
						},
						button: {
							text: $(this).data('uploader-button-text')
						},
						multiple: false
					});

					file_frame.on( 'select', function() {
						var attachment = file_frame.state().get('selection').first().toJSON(),
							attachmentThumbnail = attachment.sizes.thumbnail? attachment.sizes.thumbnail.url : attachment.sizes.full.url;

						var $imgPreview = $('<a href="#" class="remove-image dashicons dashicons-no-alt"></a><img src="' + attachmentThumbnail + '" />').css('display', 'inline-block');

						var $preview = $('.va-preview', $container),
							$close = $('a', $preview ),
							$image = $('img', $preview);

						$field.val( JSON.stringify( {
							'id': attachment.id,
							'url': attachment.url,
							'thumbnail': attachmentThumbnail
						} ) );

						if( $close.length > 0 ) {
							$close.remove();
						}
						if( $image.length > 0 ) {
							$image.remove();
						}

						$preview.append( $imgPreview ).fadeIn();
					});

					file_frame.open();
				});

				// Remove image
				$va.on('click', '.remove-image', function(e){
					e.preventDefault();
					$field.val('');
					$(this).next().remove();
					$(this).remove();
				});

			});
		},

		iconPicker: function(){
			$('.visual_attributes').find('.icon-picker-wrap').each(function(){
				var $self = $(this),
					term = $self.data('term'),
					icon = $self.find('.va_icon').val();

				if ( '' !== icon ) {
					$self.find( '.va-preview-icon' ).addClass( 'va-preview-on' ).find( 'i' ).addClass( icon );
					$( '<a class="remove-icon dashicons dashicons-no-alt" href="#"></a>' ).prependTo( $self.find( '.va-preview-icon' ) );
				}

				_.each( ql_visual_attributes_adminjs.icons, function( u ){
					$self.find('.open-' + u + '-' + term.sanitize()).qlIconPicker({
						'field'   : '.selected-icon-'+term.sanitize(),
						'preview' : '.icon-preview-'+term.sanitize(),
						'icons'   : '.' + u + '-set'
					});
				} );
			});
		},

		refresh: function() {
			var self = this,
				$vaClear = $('.js-va-clear');
			$('.va-update').on('click', function(e){
				e.preventDefault();

				var attributes_data = $vaClear.find('input, select, textarea').serialize();
				
				$vaClear.find('select.wc-enhanced-select').filter(function(){ return $(this).val() === null; }).each(function(){
					attributes_data += encodeURI( '&' + $(this).attr('name').sanitize() );
				});


				if ( window.confirm( ql_visual_attributes_adminjs.confirm_update ) ) {
					$vaClear.block({ message: null, overlayCSS: { background: '#fff', opacity: 0.6 } });
					$.post(
						ajaxurl,
						{
							post_id: woocommerce_admin_meta_boxes.post_id,
							data: attributes_data,
							action: 'queryloop_va_update',
							nonce: ql_visual_attributes_adminjs.nonce
						},
						function ( response ) {
							if ( response.success ) {
								$vaClear.empty().append( response.data );
								self.init();
								$vaClear.unblock();
							} 
						}
					);
				}
			});
		}
	};

	$(document).ready(function() {

		QL_VisualAttributes_Admin.init();
		QL_VisualAttributes_Admin.refresh();

	});

})(jQuery);