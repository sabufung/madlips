/**
 * QueryLoop Term Meta Admin Script
 * Author: @eliorivero
 * http://queryloop.com
 */
;var QL_Term_Meta_Admin = {}, qltm = qltm || {};

(function($){

	'use strict';

	String.prototype.sanitize = function(){ return this.replace(/[!"#$%&'()*+.\/:;<>?@^`{|}~]/g, "\\$&") };

	QL_Term_Meta_Admin = {

		init: function() {
			this.selectImage();
			this.iconPicker();
			this.colorPicker();
		},

		colorPicker: function(){
			// Initialize color pickers
			$('.qltm-color-picker').each(function() {
				var $color = $(this),
					$field = $color.next(),
					$label = $color.prev(),
					value = '';
				
				if ( $color.data('minicolors-initialized') ) {
					$color.minicolors( 'destroy' );
				}
				$color.minicolors( {
					opacity: true,
					change: function(hex, opacity) {
						if ( '' != hex ) {
							$field.val( JSON.stringify( {
								hex: hex,
								opacity: opacity
							}));
							$label.hide();
							$(this).next().show();
						} else {
							$field.val('');
							$label.show();
							$(this).next().hide();
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
				if ( '' != $color.val() ) {
					$color.next().show();
					$label.hide();
				}
			});
		},

		// Open Media interface
		openMedia: function() {

			var media;

			qltm.media = media = {};

			_.extend( media, { view: {}, controller: {} } );

			media.openingButton = {};

			_.extend( media, {
				frame: function( frameTitle ) {
					if ( this._frame ) {
						return this._frame;
					}

					this._frame = wp.media( {
						title: frameTitle,
						library: {
							type: ['image']
						},
						multiple: false
					} );

					this._frame.state( 'library' ).on( 'select', this.select );

					return this._frame;
				},

				select: function() {
					var settings = wp.media.view.settings,
						attachment = this.get( 'selection' ).first().toJSON(),
						attachmentThumbnail = attachment.sizes.thumbnail? attachment.sizes.thumbnail.url : attachment.sizes.full.url,
						$container = media.openingButton.closest( '.image-select-wrap' ),
						$preview = $('.image-preview', $container),
						$close = $('a', $preview),
						$image = $('img', $preview);

					$container.find('input').val( JSON.stringify( {
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

					$preview.append( $('<a href="#" class="remove-media dashicons dashicons-no-alt"></a><img src="' + attachmentThumbnail + '" />') ).fadeIn();
				},

				init: function() {
					$( '.open-media' ).on( 'click', function( e ) {
						e.preventDefault();
						media.openingButton = $(this);
						media.frame( media.openingButton.data('uploader-title') ).open();
					});
				}
			} );

			$( media.init );
		},

		selectImage: function() {
			$( '.image-select-wrap' ).each(function(){
				var $self = $(this),
					$field = $self.find('input');
				if ( '' != $field.val() ) {
					var savedAttachment = $.parseJSON( $field.val() );
					$self.find('.image-preview').append($('<a href="#" class="remove-media dashicons dashicons-no-alt"></a><img src="' + savedAttachment.thumbnail + '" />'));
				}
				$self.on('click', '.remove-media', function(e){
					e.preventDefault();
					var $self = $(this);
					$self.closest( '.image-select-wrap' ).find( 'input' ).val( '' );
					$self.next().slideUp(function(){
						$(this).remove();	
					});
					$self.remove();
				});
			});
			this.openMedia();
		},

		iconPicker: function(){
			$('.open-icons').each(function(){
				var $self = $(this);
				$self.qlIconPicker( {
					'icons': '.' + $self.data('icon-set') + '-set'
				} );
			});
		},
	};

	$(document).ready(function() {
		QL_Term_Meta_Admin.init();
	});

})(jQuery);