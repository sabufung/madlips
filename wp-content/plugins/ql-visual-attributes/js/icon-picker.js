/**
 * Icon Picker by QueryLoop
 * Author: @eliorivero
 * http://queryloop.com
 * License: GPLv2 (or later)
 */
;(function ( $ ) {

	var defaults = {
			'field'      : '',
			'preview'    : '',
			'icons'      : '',
			'mode'       : 'dialog',
			'closeOnPick': 'yes',
			'save'       : 'class',
			'iconSet'    : '',
			'iconSetName': ''
		};

	function QL_Icon_Picker ( element, options ) {
		this.element = element;
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;
		this.init();
	}

	QL_Icon_Picker.prototype = {

		init: function(){

			var picker = this;

			$(picker.element).on('click', function(e){
				e.preventDefault();
				picker.settings.iconSetName = picker.settings.icons.replace(/-set/, '').replace('.','');
				picker.settings.iconSet = picker.settings.icons;

				// Initialize picker
				picker.iconPick();

				// Show icon picker
				picker.showPicker( $(picker.settings.iconSet), picker.settings.mode );
			});

			// If there was nothing provided for preview
			if ( '' === picker.settings.preview ) {
				picker.settings.preview = $(picker.element).prev();
			} else {
				picker.settings.preview = $(picker.settings.preview);
			}

			if ( '' === picker.settings.field ) {
				picker.settings.field = $(picker.element).next();
			} else {
				picker.settings.field = $(picker.settings.field);
			}

			if ( '' !== picker.settings.field.val() ) {
				picker.addRemoveIcon();
				picker.settings.preview.addClass('icon-preview-on');
			}

			// Remove icon
			picker.settings.preview.on('click', 'a', function(e){
				e.preventDefault();
				picker.settings.field.val('');
				picker.settings.preview.removeClass('icon-preview-on').find('i').removeClass();
				$(this).hide();
			});

			// Prepare display styles, inline and dialog
			if ( 'inline' == picker.settings.mode ) {
				$(picker.settings.icons).addClass('inline');
			} else if ( 'dialog' == picker.settings.mode ) {
				var $body = $('body');
				$body.on('click', '.ql-picker-close, .ql-picker-overlay', function(e){
					e.preventDefault();
					picker.closePicker($(picker.settings.iconSet), picker.settings.mode);
				});
				$body.on('mouseenter mouseleave', '.ql-picker-close', function(e){
					if( 'mouseenter' == e.type ) {
						$(this).addClass('wp-ui-notification');
					} else {
						$(this).removeClass('wp-ui-notification');
					}
				});
				if ( $('.ql-picker-overlay').length <= 0 ) {
					$body.append('<div class="ql-picker-overlay"/>').append('<a href="#" class="ql-picker-close"/>');
				}
				$(picker.settings.icons).addClass('dialog');
			}
		},

		addRemoveIcon: function() {
			var picker = this;
			if ( picker.settings.preview.find( '.remove-icon' ).length > 0 ) {
				picker.settings.preview.find( '.remove-icon' ).show();
			} else {
				picker.settings.preview.append( $('<a class="remove-icon dashicons dashicons-no-alt" href="#"></a>') );
			}
		},

		iconPick:function(){
			var picker = this;
			$(picker.settings.icons)
			.on('click', 'li', function(e){
				e.preventDefault();
				var $icon = $(this),
					icon = $icon.data( picker.settings.save );

				// Mark as selected
				$('.icon-selected').removeClass('icon-selected');
				$icon.addClass('icon-selected');

				// Save icon value to field
				picker.settings.field.val( icon );

				// Close icon picker
				if ( 'yes' == picker.settings.closeOnPick ) {
					picker.closePicker( $icon.closest(picker.settings.icons), picker.settings.mode );
				}

				// Set preview
				if ( '' != picker.settings.preview ) {
					picker.setPreview( $icon.data( 'class' ) );
				}

				// Add button to remove icon
				picker.addRemoveIcon();

				// Broadcast event passing the selected icon.
				$('body').trigger('qliconselected', icon);
			})
			.on('mouseenter mouseleave', 'li', function(e){
				if( 'mouseenter' == e.type ) {
					$(this).addClass('wp-ui-highlight');
				} else {
					$(this).removeClass('wp-ui-highlight');
				}
			})
			.on('keyup paste search', '.icon-search', function(e){
				var $container = $(e.delegateTarget),
					$self = $(e.target),
					text = $self.val();
				if ( '' !== text ) {
					$container.find( 'li[class*="' + text + '"]' ).siblings().hide();
					$container.find( 'li[class*="' + text + '"]' ).show();
				} else {
					$container.find( 'li' ).show();
				}
			});
		},

		setPreview: function( preview ){
			var picker = this;
			picker.settings.preview.addClass('icon-preview-on').find('i').removeClass()
				.addClass( picker.settings.iconSetName )
				.addClass( preview );
			picker.settings.preview.find('a').show();
		},

		showPicker: function( $icons, mode ){
			if ( 'inline' == mode ) {
				$icons.toggleClass('inline-open');
			} else if ( 'dialog' == mode ) {
				$('.ql-picker-close, .ql-picker-overlay').addClass('ql-visible');
				$icons.addClass('dialog-open');
			}
			// Broadcast event when the picker is shown passing the picker mode.
			$('body').trigger('qliconpickershow', mode);
		},

		closePicker: function( $icons, mode ){
			// Remove event so they don't fire from a different picker
			$(this.settings.icons).off('click', 'li');

			if ( 'inline' == mode ) {
				$icons.removeClass('inline-open');
			} else if ( 'dialog' == mode ) {
				$('.ql-picker-close, .ql-picker-overlay').removeClass('ql-visible');
				$icons.removeClass('dialog-open');
			}
			// Broadcast event when the picker is closed passing the picker mode.
			$('body').trigger('qliconpickerclose', mode);
		}
	};

	$.fn.qlIconPicker = function ( options ) {
		this.each(function() {
			if ( !$.data( this, 'plugin_qlIconPicker' ) ) {
				$.data( this, 'plugin_qlIconPicker', new QL_Icon_Picker( this, options ) );
			}
		});
		return this;
	};

})( jQuery );