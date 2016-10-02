/**
 * QueryLoop Plugin Script
 * http://queryloop.com
 */
var QL_VisualAttributes, qlva;

(function($){

	'use strict';

	Object.defineProperty( String.prototype, 'sanitize', {
		value: function () {
			return this.replace(/[\s!"#$%&'()*+,.\/:;<>?@^`{|}~]/g, "\\$&");
		}
	});

	QL_VisualAttributes = {

		ready: function(){
			$('.ql-visual-attributes:not(.va-show-on-loop)').each(function(){

				var $va = $(this),
					$form = $va.closest('form'),
					$vars = $form.find('.variations'),
					pickerSelector = '.va-picker';

				if ( 'reclick' === qlva.reclick ) {
					$va.addClass('va-reclick');
				} else {
					pickerSelector += ':not(.va-hidden)';
				}

				// Attach events to visual options.
				$va.on('click', pickerSelector, function(e){
					e.preventDefault();
					var $self = $(this),
						attribute = $self.data('attribute').sanitize(),
						$select = $vars.find('#' + attribute ),
						originalOptionSelector = 'option[value=' + String( $self.data('term') ).sanitize() + ']';

					if ( ! $self.hasClass('va-selected') ) {
						var $thisOption = $select.trigger('focusin').find( originalOptionSelector );
						if ( $thisOption.length > 0 ) {
							$thisOption.prop('selected', true).trigger('change');
						} else {
							$form.find( '.reset_variations' ).trigger( 'click' );
							setTimeout(function(){
								$select.find( originalOptionSelector ).prop('selected', true).trigger('change');
							}, 250);
						}
						$self.removeClass('va-hidden').addClass('va-selected').siblings( 'a[data-attribute=' + attribute + ']' ).removeClass('va-selected');
					} else if ( qlva.secondClickDeselects ) {
						$self.removeClass('va-selected').siblings('a[data-attribute=' + attribute + ']').removeClass('va-selected');
						$select.val('').trigger('change');
					}

					// Event: after this option was selected. Receives the selected link object as argument.
					$('body').trigger( 'ql_visual_attributes_option_selected', [$self] );
				});

				var $jckwt = $va.closest('.product').find( '#jckWooThumbs_img_wrap, .jck-wt-all-images-wrap' );

				// Attach events to standard select
				$va.find('select').each(function(){
					$(this)
						.on('focusin', function(){
							var $self = $(this),
								$options = $vars.find('#' + $self.data('attribute').sanitize()).trigger('focusin').find('option'),
								selectedOption = $self.find('option:selected').val();
							// There's a case where a .trigger('change'); is needed. See VA demo.
							$self.empty().append( $options.clone() ).find('option:selected').prop('selected', false);
							if ( '' !== selectedOption ) {
								$self.find('option[value=' + String( selectedOption ).sanitize() + ']').prop('selected', true);
							}
						})
						.on('change', function(){
							var $self = $(this),
								$select = $vars.find('#' + $self.data('attribute').sanitize()),
								selectedOption = $self.find('option:selected').val();

							$select.find('option:selected').prop('selected', false);
							if ( $jckwt.length > 0 ) {	$jckwt.addClass('reset'); }
							if ( '' !== selectedOption ) {
								$form.trigger( 'check_variations', [ '', false ] );
								$select.find('option[value=' + String( selectedOption ).sanitize() + ']').prop('selected', true).trigger('change');
							} else {
								$select.trigger('change');
							}
							if ( $jckwt.length > 0 ) { $jckwt.removeClass('reset'); }

							// Event: after this option was selected. Receives the <select> object as argument.
							$('body').trigger( 'ql_visual_attributes_option_selected', [$self] );
						});
				});

			});
		},

		load: function(){
			$('.ql-visual-attributes:not(.va-show-on-loop)').each(function(){

				var $va = $(this),
					$form = $va.closest('form');

				// Listen to changes for variations composed of many attributes
				$form.on('update_variation_values', function(e, variations){
					window.requestAnimationFrame(function () {
						QL_VisualAttributes.updateVisualAttributes( $va, variations, e );
					})
				});

				$form.find('.reset_variations').prependTo($va).on( 'click', function(e) {
					e.preventDefault();
					$va.find('.va-selected').removeClass('va-selected');
					$va.find('select').prop('selectedIndex', 0).val('').change();

					// Event: after options have been reset
					$('body').trigger( 'ql_visual_attributes_options_reset' );
				}).css({ 'visiblity': 'hidden', 'display': 'block' });

				// Setup dropdowns and visual attributes that are initially selected
				$va.find('.va-start-selected').each(function(){
					var $self = $(this);

					// Disable initial autonomous check that removes attributes not matching any variation
					QL_VisualAttributes.firstCheck[$va.closest('form').data('product_id')] = false;

					// Update dropdowns and visual attributes
					$form.find( '#' + $self.data('attribute') ).trigger('change');
					// Highlight initially selected attribute.
					$self.addClass( 'va-selected' );//.trigger('click').trigger('click');
				});

				$form.trigger('check_variations');

				// Finally, show visual attributes
				$va.slideDown(300);

			});

			$('.shop_attributes.ql-visual-attributes').each(function(){
				$(this).find('tr:odd').removeClass('alt');
			});

			var $body = $('body');

			// Event: after events are attached on window load
			$body.trigger( 'ql_visual_attributes_load' );

			// Tooltips
			QL_VisualAttributes.refreshAllTooltips();
			// Check single tooltip when mouse is over attribute
			$body.on( 'mouseenter', '.va-tooltip .va-picker', function(){
				QL_VisualAttributes.refreshTooltip( $(this) );
			});
			// Update tooltip placement when viewport size changes
			var didResize = false;
			$(window).resize(function() {
				didResize = true;
			});
			setInterval(function() {
				if ( didResize ) {
					didResize = false;
					QL_VisualAttributes.refreshAllTooltips();
				}
			}, 500);
		},

		firstCheck: [],

		updateVisualAttributes: function( $va, variations, event ) {

			var product_id = $va.closest('form').data('product_id');

			var $selected= $va.find('.va-selected');
			var selected_attr = $selected.data('attribute'); // pa_color
			var selected_term = $selected.data('term'); // red

			var attribs = [], dismiss = [];
			if ( 'undefined' === typeof this.firstCheck[ product_id ] ) {
				this.firstCheck[ product_id ] = true;
			}

			$va.find('.va-picker').addClass('va-hidden');

			var filterFormElement = Array.prototype.filter.bind(event.currentTarget);

			var selected = filterFormElement(function (elm) {
				return $(elm).data('attribute_name') && !!elm.value;
			});

			if (0 === selected.length) {
				$va.find('.va-picker').removeClass('va-hidden');
			}

			selected.forEach(function (selectEl) {
				var attribute = $(selectEl).attr('id');
				var attribute_name = $(selectEl).attr('name');
				var term = selectEl.value
				$('[data-attribute="' + attribute  + '"][data-term="' + term + '"]').removeClass('va-hidden');

				var affected = variations.filter(function (variation) {
					return variation.attributes[attribute_name] === term || variation.attributes[attribute_name] === '';
				});

				affected.forEach(function (variation) {
					Object.keys(variation.attributes).forEach(function (curr_attr) {
						if (variation.attributes[curr_attr] === '') {
							$('[data-attribute=' + curr_attr.replace('attribute_', '') + ']').removeClass('va-hidden')
						} else {
							$('[data-attribute=' + curr_attr.replace('attribute_', '') + '][data-term=' + variation.attributes[curr_attr] + ']').removeClass('va-hidden')
						}
					});
				});
			});

			$('body').trigger( 'ql_visual_attributes_options_updated', $va.find('.va-picker').not('.va-hidden') );
		},

		refreshTooltip: function( $picker ) {
			var $tooltip = $picker.find( '.va-info' );
			if ( $tooltip.length > 0 ) {
				var $detect = false,
					tooltipW = $tooltip.outerWidth( true ),
					tooltipH = $tooltip.outerHeight( true ),
					pickerOffset = $picker.offset(),
					pickerLeft = pickerOffset.left,
					pickerTop = pickerOffset.top,
					pickerRight = pickerLeft + $picker.outerWidth(),
					parentDetectH = false,
					parentDetectV = false,
					parentDetectHR = false;

				if ( qlva.tooltipEdgeDetect ) {
					$detect = $(qlva.tooltipEdgeDetect);
				}

				if ( 'object' === typeof $detect ) {
					var detectOffset = $detect.offset();
					parentDetectH = ( pickerLeft - detectOffset.left ) <= tooltipW;
					parentDetectV = ( pickerTop - detectOffset.top ) <= tooltipH;
					parentDetectHR = ( $detect.outerWidth( true ) - pickerRight ) <= tooltipW;
				}

				// Left edge
				if ( parentDetectH || ( ( pickerLeft - $(window).scrollLeft() ) <= tooltipW ) ) {
					$picker.addClass( 'va-tooltip-left' ).removeClass( 'va-tooltip-right' );
				} else {
					$picker.removeClass( 'va-tooltip-left' );
				}

				// Right edge
				if ( parentDetectHR || ( ( $(window).width() - pickerRight ) <= tooltipW ) ) {
					$picker.addClass( 'va-tooltip-right' ).removeClass( 'va-tooltip-left' );
				} else {
					$picker.removeClass( 'va-tooltip-right' );
				}

				// Top edge
				if ( parentDetectV || ( ( pickerTop - $(window).scrollTop() ) <= tooltipH ) ) {
					$picker.addClass( 'va-tooltip-bottom' );
				} else {
					$picker.removeClass( 'va-tooltip-bottom' );
				}
			}
		},

		refreshAllTooltips: function() {
			$('.va-tooltip .va-picker').each(function(){
				QL_VisualAttributes.refreshTooltip( $(this) );
			});
		},

		parseVars: function(){
			$.each( qlva, function(i, v) {
				if ( 'false' === v || 'true' === v ) {
					qlva[i] = 'false' !== v;
				} else if ( typeof v === 'string' && ! v.match(/[a-z]/i) && parseInt(v) ) {
					qlva[i] = parseInt(v);
				} else if ( typeof v === 'string' && ! v.match(/[a-z]/i) && parseFloat(v) ) {
					qlva[i] = parseFloat(v);
				}
			});
		},

		isTouch: function() {
			return 'true' === qlva.isMobile;
		}
	};

	QL_VisualAttributes.parseVars();

	$(document).ready(function() {
		QL_VisualAttributes.ready();
	});

	$(window).load(function() {
		QL_VisualAttributes.load();
	});

}(jQuery));
