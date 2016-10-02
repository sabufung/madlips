<?php
/**
 * Specific admin functionality and front end.
 * @author	QueryLoop
 * @since	1.0.0
 */
class QL_Visual_Attributes {

	var $prefix = '';
	var $cpt = 'product';
	var $is_mobile;
	var $icons;
	var $vas;
	var $post_id;
	var $is_show_on_loop = false;

	/**
	 * Cache the name of the attribute currently being rendered.
	 * Used for example in maybe_show_name() to fetch post meta only once.
	 *
	 * @since 1.0.0
	 */
	var $current_attribute_name = '';

	/**
	 * Cache whether name should be displayed or not for the attribute currently being rendered.
	 *
	 * @since 1.0.0
	 */
	var $current_show_name_status = '';

	/**
	 * Main initialization
	 *
	 * @since 1.0.0
	 *
	 * @param array $args
	 */
	function __construct( $args = array() ) {
		$this->prefix = $args['prefix'];
		$this->admin = $args['admin'];

		if ( is_admin() ) {
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_meta_box_assets' ) );
			add_action( 'woocommerce_product_write_panel_tabs', array( $this, 'add_field_tab' ) );
			add_action( 'woocommerce_product_data_panels', array( $this, 'add_fields' ) );
			add_action( 'woocommerce_process_product_meta', array( $this, 'save_fields' ) );
			add_action( 'wp_ajax_queryloop_va_update', array( $this, 'ajax_update_fields' ) );
			add_action( 'wp_ajax_queryloop_va_status', array( $this, 'ajax_set_status' ) );
		} else {
			add_action( 'wp_enqueue_scripts', array( $this, 'register_scripts_and_styles' ) );
			add_action( 'template_redirect', array( $this, 'display' ) );
			// Next line coming up soon
			//add_filter( 'wc_get_template', array( $this, 'replace_additional_info_attributes' ), 10, 2 );
		}
	}

	/**
	 * Replace attribute display in Additional Information tab.
	 *
	 * @since 1.1.x
	 */
	function replace_additional_info_attributes( $located, $template_name ) {
		if ( 'single-product/product-attributes.php' == $template_name ) {
			$located = str_replace( 'woocommerce', 'ql-visual-attributes', $located );
		}
		return $located;
	}

	/**
	 * Check if VA will be rendered or not, and if so, loads required assets.
	 *
	 * @since 1.0.2
	 */
	function display() {
		$post_id = get_the_ID();

		// Allow to write all variations as JSON encoded data in <form> data-product_variations attribute.
		add_filter( 'woocommerce_ajax_variation_threshold', array( $this, 'variation_threshold' ) );

		if ( $this->is_enabled( $post_id ) ) {
			add_action( 'woocommerce_before_add_to_cart_button', array( $this, 'render_attributes' ) );
		}

		if ( 'yes' == get_post_meta( $post_id, '_only_va', true ) ) {
			add_filter( 'woocommerce_product_tabs', array( $this, 'disable_additional_info' ), 20 );
		}

		/**
		 * Filters the hook used to show visual attributes on shop loop.
		 *
		 * @since 1.0.9
		 *
		 * @param string
		 */
		$hook = apply_filters( 'queryloop_visual_attributes_show_on_loop_hook', 'woocommerce_after_shop_loop_item' );
		add_action( $hook, array( $this, 'show_on_loop' ) );
	}

	/**
	 * Disables Additional Information tab.
	 *
	 * @param $tabs
	 *
	 * @return mixed
	 */
	function disable_additional_info( $tabs ) {
		unset( $tabs['additional_information'] );
		return $tabs;
	}

	/**
	 * Add tab.
	 *
	 * @since 1.0.0
	 */
	function add_field_tab() {
		?>
		<li class="visual_attributes_tab">
			<a href="#visual_attributes"><?php _e( 'Visual Attributes', 'queryloop' ); ?></a>
		</li>
		<?php
	}

	/**
	 * Add panel and render the fields.
	 *
	 * @since 1.0.0
	 */
	function add_fields() {
		global $thepostid, $post;
		$post_id = empty( $thepostid ) ? $post->ID : $thepostid;
		$product_type = 'variable';
		if ( function_exists( 'get_product' ) ) {
			$product = get_product( $post_id );
			if ( ! $product->is_type( 'variable' ) ) {
				$product_type = 'other';
			}
		}
		?>
		<div id="visual_attributes" class="panel woocommerce_options_panel visual_attributes">
			<h3 class="va-general-heading"><?php _e( 'General Settings', 'queryloop' ); ?></h3>
			<?php
			woocommerce_wp_select(
				array(
					'id'    => '_disable_va',
					'label' => __( 'Disable Visual Attributes', 'queryloop' ),
					'options' => array(
						'setting' => __( 'Use General State Option', 'queryloop' ),
						'yes' => __( 'Yes', 'queryloop' ),
						'no' => __( 'No', 'queryloop' ),
					),
				)
			);
			woocommerce_wp_select(
				array(
					'id'    => '_name_style',
					'label' => __( 'Attribute Name Style', 'queryloop' ),
					'options' => array(
						'label'   => __( 'Below Attribute', 'queryloop' ),
						'tooltip' => __( 'Tooltip', 'queryloop' ),
					),
				)
			);
			if ( 'variable' != $product_type ) {
				woocommerce_wp_checkbox(
					array(
						'id'    => '_only_va',
						'label' => __( 'Hide Additional Information Tab', 'queryloop' ),
					)
				);
			}
			?>
			<h3 class="va-general-heading"><?php _e( 'Attribute Settings', 'queryloop' ); ?></h3>
			<p class="va-update-wrap">
				<a class="va-update button" href="#"><?php _e( 'Update Attributes List', 'queryloop' ); ?></a>
			</p>

			<div class="js-va-clear">
				<?php $this->render_fields( $post_id ); ?>
			</div>

		</div>
		<?php
	}

	/**
	 * AJAX action to check whether to show the fields or not.
	 *
	 * @since 1.0.0
	 */
	function ajax_set_status() {
		check_ajax_referer( $this->prefix . '-nonce', 'nonce' );

		// Verify data and check status
		if ( isset( $_POST['data'] ) && 'disabled' != $this->admin->get( 'status_sel', 'enabled' ) ) {
			wp_send_json_error();
		} else {
			wp_send_json_success();
		}
	}

	/**
	 * Checks if Visual Attributes are enabled or not.
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id ID of the product.
	 *
	 * @return bool
	 */
	function is_enabled( $post_id ) {
		$create = true;
		$disable = get_post_meta( $post_id, '_disable_va', true );
		if ( 'yes' != $disable ) {
			if ( 'no' == $disable || 'disabled' != $this->admin->get( 'status_sel', 'enabled' ) ) {
				$context = $this->admin->get( 'usage_sel', 'both' );
				if ( class_exists( 'ThemesRobot_Mobile_Detect' ) ) {
					$detect = new ThemesRobot_Mobile_Detect;
				} else {
					require_once 'class-mobile-detect.php';
					$detect = new QL_Visual_Attributes_Mobile_Detect;
				}
				if ( 'both' != $context ) {
					$this->is_mobile = $detect->isMobile();
					if ( ( 'mobile' == $context && ! $this->is_mobile ) || ( 'desktop' == $context && $this->is_mobile ) ) {
						$create = false;
					}
				}
			} else {
				$create = false;	
			}
		} else {
			$create = false;
		}
		return $create;
	}

	/**
	 * Determines whether visual attributes will be displayed or not.
	 *
	 * @param $post_id ID of the product.
	 * @param $attribute
	 *
	 * @return bool
	 */
	function rendering_condition( $post_id, $attribute ) {
		$product_type = 'variable';
		$product = function_exists( 'wc_get_product' ) ? wc_get_product( $post_id ) : get_product( $post_id );

		if ( is_object( $product ) && ! $product->is_type( 'variable' ) ) {
			$product_type = 'other';
		}

		if ( 'variable' == $product_type ) {
			$condition = 1 == $attribute['is_variation'] && ( ( 0 == $attribute['is_taxonomy'] && ! empty( $attribute['value'] ) ) || 1 == $attribute['is_taxonomy'] );
		} else {
			$condition = 1 == $attribute['is_visible'] && ( ( 0 == $attribute['is_taxonomy'] && ! empty( $attribute['value'] ) ) || 1 == $attribute['is_taxonomy'] );
		}

		/**
		 * Filters the condition that states whether an attribute can be converted into a visual attribute or not.
		 *
		 * @since 1.0.6
		 *
		 * @param bool $condition
		 * @param array $attribute
		 * @param string $product_type
		 */
		return apply_filters( 'queryloop_visual_attributes_condition', $condition, $attribute, $product_type );
	}

	/**
	 * Helper function to retrieve value from new data structure.
	 * Check if the visual attribute option is available in the collection and returns it.
	 * Otherwise returns empty string to replicate get_post_meta return.
	 *
	 * @since 1.0.5
	 *
	 * @param $key
	 *
	 * @return string
	 */
	function get_vas( $key ) {
		// New data structure, all in a single custom field
		if ( isset( $this->vas[$key] ) ) {
			return $this->vas[$key];
		}
		// Backwards compatible, get custom field separately
		return get_post_meta( $this->post_id, $key, true );
	}

	/**
	 * Generates the fields to be displayed
	 *
	 * @since 1.0.0
	 *
	 * @param $post_id
	 */
	function render_fields( $post_id ) {
		?>
		<div class="options_group hide_if_external">
			<?php
			// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
			$attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

			// Flag to know whether to add the icon list or not.
			$add_icons = false;

			if ( ! empty( $attributes ) ) : foreach( $attributes as $attribute ) :

				if ( $this->rendering_condition( $post_id, $attribute ) ) :
					$attribute_slug = '';
					if ( 1 == $attribute['is_taxonomy'] ) {
						$attr_tax = get_taxonomy( $attribute['name'] );
						$attribute_name = $attr_tax->labels->name;
						$attribute_slug = $attr_tax->name;
						$terms = wp_get_post_terms( $post_id, $attribute['name'] );
					} else {
						$attribute_name = $attribute['name'];
						$terms = $this->get_attribute_list( $attribute['value'] );
					}
					/**
					 * Sanitized attribute name to be used as part of field id.
					 *
					 * @var string $attribute_attr
					 */
					if ( '' != $attribute_slug ) {
						$attribute_attr = $attribute_slug;
					} else {
						$attribute_attr = esc_attr( sanitize_title( $attribute['name'] ) );
					}
					?>

					<h3><?php echo $attribute_name; ?></h3>

					<?php
					// Use standard dropdown
					$dropdown_id = '_va_dropdown_'.$attribute_attr;
					$dropdown = get_post_meta( $post_id, $dropdown_id, true );
					?>
					<div class="form-field va-standard-dropdown">
						<input type="checkbox" <?php checked( $dropdown, 1 ); ?> class="va-use-dropdown" data-toggle="<?php echo $attribute_attr; ?>" />
						<input type="hidden" name="<?php echo $dropdown_id; ?>" value="<?php echo $dropdown; ?>" id="<?php echo $dropdown_id; ?>" />
						<label for="<?php echo $dropdown_id; ?>">
							<?php _e( 'Use Standard Dropdown', 'queryloop' ); ?>
						</label>
					</div>

					<div class="form-field va-show-on-loop-wrapper <?php echo 'va-toggle-'.$attribute_attr; ?>">
		
						<?php
						// Show on loop
						$show_on_loop_id = '_va_show_on_loop_'.$attribute_attr;
						$show_on_loop = get_post_meta( $post_id, $show_on_loop_id, true );
						?>
						<label for="<?php echo $show_on_loop_id; ?>">
							<?php _e( 'Show on Shop Loop', 'queryloop' ); ?><br/>
						</label>
					
						<?php
						// Multi select attribute
						if ( $attribute['is_taxonomy'] ) :

							global $wc_product_attributes;
							$attribute_taxonomy = $wc_product_attributes[ $attribute['name'] ];
							if ( 'select' === $attribute_taxonomy->attribute_type ) : ?>

								<select multiple="multiple" data-placeholder="<?php _e( 'Select terms', 'queryloop' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="<?php echo $show_on_loop_id; ?>[]">
									<?php
									$all_terms = get_terms( $attribute['name'], 'orderby=name&hide_empty=0' );
									if ( $all_terms ) {
										foreach ( $all_terms as $term ) {
											echo '<option value="' . esc_attr( $term->slug ) . '" ' . selected( in_array( $term->slug, $show_on_loop ), true, false ) . '>' . $term->name . '</option>';
										}
									}
									?>
								</select>
								<button class="button plus select_all_attributes"><?php _e( 'Select all', 'queryloop' ); ?></button>
								<button class="button minus select_no_attributes"><?php _e( 'Select none', 'queryloop' ); ?></button>

							<?php elseif ( 'text' == $attribute_taxonomy->attribute_type ) : ?>

								<input type="text" name="<?php echo $show_on_loop_id; ?>" class="va-show-on-loop-text" value="<?php

									// Text attributes should list terms pipe separated
									echo is_array( $show_on_loop ) ? esc_attr( implode( ' ' . WC_DELIMITER . ' ', $show_on_loop ) ) : $show_on_loop;

								?>" placeholder="<?php echo esc_attr( sprintf( __( '"%s" separate terms', 'queryloop' ), WC_DELIMITER ) ); ?>" />

							<?php endif; ?>

						<?php else : ?>

							<textarea name="<?php echo $show_on_loop_id; ?>" cols="5" rows="5" placeholder="<?php echo esc_attr( sprintf( __( 'Enter the attribute names as you entered in Attributes side tab separating them with "%s".', 'queryloop' ), WC_DELIMITER ) ); ?>"><?php echo esc_textarea( is_array( $show_on_loop ) ? esc_attr( implode( ' ' . WC_DELIMITER . ' ', $show_on_loop ) ) : $show_on_loop ); ?></textarea>

						<?php endif; ?>

						<p class="howto"><small><?php _e( 'Select attributes to display in shop and product category and tag view.', 'queryloop' ); ?></small></p>
					
					</div><!--/.woocommerce_attribute_data-->

					<?php
					// Select whether to show the attribute name: no, below, tooltip
					$showname_id = '_va_showname_'.$attribute_attr;
					$showname = get_post_meta( $post_id, $showname_id, true );
					?>
					<div class="form-field va-showname <?php echo 'va-toggle-'.$attribute_attr; ?>">
						<select name="<?php echo $showname_id; ?>" id="<?php echo $showname_id; ?>">
							<option value="" <?php selected( $showname, '' ); ?>><?php _e( 'No', 'queryloop' ); ?></option>
							<option value="below" <?php selected( $showname, 'below' ); ?>><?php _e( 'Single Only', 'queryloop' ); ?></option>
							<option value="always" <?php selected( $showname, 'always' ); ?>><?php _e( 'Always (single and loop)', 'queryloop' ); ?></option>
							<!--<option value="tooltip"<?php selected( $showname, 'tooltip' ); ?>><?php _e( 'Tooltip', 'queryloop' ); ?></option>-->
						</select>
						<label for="<?php echo $showname_id; ?>"><?php _e( 'Show Attribute Name', 'queryloop' ); ?></label>
					</div>

					<?php
					$this->vas = get_post_meta( $post_id, '_va_collection', true );
					$this->post_id = $post_id;

					foreach ( $terms as $full_term ) :
						/**
						 * Term slug to be used as part of field id.
						 *
						 * @var string $term
						 */
						$term = '';
						/**
						 * Term name used as field label.
						 *
						 * @var string $term_name
						 */
						$term_name = '';

						// If it's an attribute taxonomy, it's an object
						$is_taxonomy = is_object( $full_term );
						if ( $is_taxonomy ) {
							$term = $full_term->slug;
							$term_name = $full_term->name;
						} else {
							// otherwise, it's an array item
							$term = esc_attr( sanitize_title( $full_term ) );
							$term_name = $full_term;
						}
						?>

						<div class="form-field <?php echo 'va-toggle-'.$attribute_attr; ?>">

							<label><?php echo $term_name; ?></label>

							<?php
							// Type Selector
							$type_id = '_va_type_'.$attribute_attr.$term;
							$type = $this->get_vas( $type_id );
							$type = $type ? $type : 'image';
							$preset = $is_taxonomy ? $this->get_preset( $type_id, $full_term->term_id, 'type' ) : '';
							?>
							<div class="va-selector-wrap">
								<a href="#" data-type="image" class="va-selector"><?php _e( 'Image', 'queryloop' ); ?></a>
								<a href="#" data-type="icon" class="va-selector"><?php _e( 'Icon', 'queryloop' ); ?></a>
								<a href="#" data-type="color" class="va-selector"><?php _e( 'Color', 'queryloop' ); ?></a>
								<a href="#" data-type="text" class="va-selector"><?php _e( 'Text', 'queryloop' ); ?></a>
								<?php $this->input_field( array(
									'id' => $type_id,
									'class' => 'va_type',
									'default' => $preset,
								) ); ?>
							</div>
							<!-- /.va-selector-wrap -->

							<div class="va-types">
							<?php
							// Image
							$image_id = '_va_image_'.$attribute_attr.$term;
							$preset = $is_taxonomy ? $this->get_preset( $image_id, $full_term->term_id, 'image', 'image' ) : '';
							?>
							<div class="image-picker-wrap va_brick hidden">
								<div class="va-preview"></div>
								<a class="button open-media" href="#"><?php _e( 'Select Image', 'queryloop' ); ?></a>
								<?php $this->input_field( array(
									'id' => $image_id,
									'class' => 'va_image',
									'default' => $preset,
								) ); ?>
							</div>
							<!-- /.image-picker-wrap -->

							<?php
							// Icon
							$icon_id = '_va_icon_'.$attribute_attr.$term;
							$add_icons = true;
							$attr_term = $attribute_attr.$term;
							$preset = $is_taxonomy ? $this->get_preset( $icon_id, $full_term->term_id, 'icon', 'icon' ) : '';
							?>
							<div class="icon-picker-wrap va_brick hidden" data-term="<?php echo $attribute_attr.$term; ?>">
								<div class="va-preview-icon icon-preview-<?php echo $attr_term; ?>">
									<i class=""></i>
								</div>

								<?php foreach( $this->get_icon_sets() as $key => $icon ) : ?>
									<a class="open-icons open-<?php echo "$key-$attr_term"; ?> button" href="#"><?php echo $icon['button_label']; ?></a><br>
								<?php endforeach;

								$this->input_field( array(
									'id' => $icon_id,
									'class' => 'va_icon selected-icon-'.$attr_term,
									'default' => $preset,
								) ); ?>

								<?php
								// Icon Color
								$icon_color_id = '_va_icon_color_'.$attribute_attr.$term;
								$color = $this->admin->decode_color( $this->get_vas( $icon_color_id ) );
								$preset = $is_taxonomy ? $this->get_preset( $icon_color_id, $full_term->term_id, 'icon_color', 'color' ) : '';
								if ( empty( $color->hex ) && is_object( $preset ) ) {
									$color->hex = empty( $preset->hex ) ? '' : $preset->hex;
									$color->a = empty( $preset->a ) ? '' : $preset->a;
								}
								?>
								<div class="va-color-picker icon-color-picker clear">
									<span class="color-label"><?php _e( 'Front Color', 'queryloop' ); ?></span>
									<div class="color-picker-wrap">
										<span class="pick-color"><?php _e( 'Pick Color', 'queryloop' ); ?></span>
										<input class="ql-color-picker" value="<?php echo $color->hex; ?>" data-opacity="<?php echo $color->a; ?>" type="text" />
										<?php $this->input_field( array(
											'id' => $icon_color_id,
											'class' => 'va_color',
											'default' => is_object( $preset ) ? "#{$preset->hex}_{$preset->a}" : null,
										) ); ?>
									</div>
									<!-- /.icon-color-picker -->
								</div>

								<?php
								// Icon Background Color
								$icon_color_bg_id = '_va_icon_color_bg_'.$attribute_attr.$term;
								$color = $this->admin->decode_color( $this->get_vas( $icon_color_bg_id ) );
								$preset = $is_taxonomy ? $this->get_preset( $icon_color_bg_id, $full_term->term_id, 'icon_color_bg', 'color' ) : '';
								if ( empty( $color->hex ) && is_object( $preset ) ) {
									$color->hex = empty( $preset->hex ) ? '' : $preset->hex;
									$color->a = empty( $preset->a ) ? '' : $preset->a;
								}
								?>
								<div class="va-color-picker icon-color-picker">
									<span class="color-label"><?php _e( 'Background Color', 'queryloop' ); ?></span>
									<div class="color-picker-wrap">
										<span class="pick-color"><?php _e( 'Pick Color', 'queryloop' ); ?></span>
										<input class="ql-color-picker" value="<?php echo $color->hex; ?>" data-opacity="<?php echo $color->a; ?>" type="text" />
										<?php $this->input_field( array(
											'id' => $icon_color_bg_id,
											'class' => 'va_color',
											'default' => is_object( $preset ) ? "#{$preset->hex}_{$preset->a}" : null,
										) ); ?>
									</div>
									<!-- /.icon-color-picker -->
								</div>
							</div>
							<!-- /.icon-picker-wrap -->

							<?php
							// Color
							$color_id = '_va_color_'.$attribute_attr.$term;
							$color = $this->admin->decode_color( $this->get_vas( $color_id ) );
							$preset = $is_taxonomy ? $this->get_preset( $color_id, $full_term->term_id, 'color', 'color' ) : '';
							if ( empty( $color->hex ) && is_object( $preset ) ) {
								$color->hex = empty( $preset->hex ) ? '' : $preset->hex;
								$color->a = empty( $preset->a ) ? '' : $preset->a;
							}
							?>
							<div class="color-picker-wrap va_brick hidden">
								<span class="pick-color"><?php _e( 'Pick Color', 'queryloop' ); ?></span>
								<input class="ql-color-picker" value="<?php echo $color->hex; ?>" data-opacity="<?php echo $color->a; ?>" type="text" />
								<?php $this->input_field( array(
									'id' => $color_id,
									'class' => 'va_color',
									'default' => is_object( $preset ) ? "#{$preset->hex}_{$preset->a}" : null,
								) ); ?>
							</div>
							<!-- /.color-picker-wrap -->

							<?php
							// Text
							$text_id = '_va_text_'.$attribute_attr.$term;
							$preset = $is_taxonomy ? $this->get_preset( $text_id, $full_term->term_id, 'text' ) : '';
							?>
							<div class="text-picker-wrap va_brick hidden">
								<?php $this->input_field( array(
									'id' => $text_id,
									'class' => 'va_text',
									'default' => $preset,
									'type' => 'text',
								) ); ?>

								<?php
								// Text Color
								$text_color_id = '_va_text_color_'.$attribute_attr.$term;
								$color = $this->admin->decode_color( $this->get_vas( $text_color_id ) );
								$preset = $is_taxonomy ? $this->get_preset( $text_color_id, $full_term->term_id, 'text_color', 'color' ) : '';
								if ( empty( $color->hex ) && is_object( $preset ) ) {
									$color->hex = empty( $preset->hex ) ? '' : $preset->hex;
									$color->a = empty( $preset->a ) ? '' : $preset->a;
								}
								?>
								<div class="va-color-picker icon-color-picker clear">
									<span class="color-label"><?php _e( 'Front Color', 'queryloop' ); ?></span>
									<div class="color-picker-wrap">
										<span class="pick-color"><?php _e( 'Pick Color', 'queryloop' ); ?></span>
										<input class="ql-color-picker" value="<?php echo $color->hex; ?>" data-opacity="<?php echo $color->a; ?>" type="text" />
										<?php $this->input_field( array(
											'id' => $text_color_id,
											'class' => 'va_color',
											'default' => is_object( $preset ) ? "#{$preset->hex}_{$preset->a}" : null,
										) ); ?>
									</div>
									<!-- /.icon-color-picker -->
								</div>

								<?php
								// Text Background Color
								$text_color_bg_id = '_va_text_color_bg_'.$attribute_attr.$term;
								$color = $this->admin->decode_color( $this->get_vas( $text_color_bg_id ) );
								$preset = $is_taxonomy ? $this->get_preset( $text_color_bg_id, $full_term->term_id, 'text_color_bg', 'color' ) : '';
								if ( empty( $color->hex ) && is_object( $preset ) ) {
									$color->hex = empty( $preset->hex ) ? '' : $preset->hex;
									$color->a = empty( $preset->a ) ? '' : $preset->a;
								}
								?>
								<div class="va-color-picker icon-color-picker">
									<span class="color-label"><?php _e( 'Background Color', 'queryloop' ); ?></span>
									<div class="color-picker-wrap">
										<span class="pick-color"><?php _e( 'Pick Color', 'queryloop' ); ?></span>
										<input class="ql-color-picker" value="<?php echo $color->hex; ?>" data-opacity="<?php echo $color->a; ?>" type="text" />
										<?php $this->input_field( array(
											'id' => $text_color_bg_id,
											'class' => 'va_color',
											'default' => is_object( $preset ) ? "#{$preset->hex}_{$preset->a}" : null,
										) ); ?>
									</div>
									<!-- /.icon-color-picker -->
								</div>
							</div>
							<!-- /.text-picker-wrap -->

							</div>
							<!-- /.va-types -->
						</div>
						<!-- /.form-field -->

					<?php endforeach; // terms ?>

				<?php endif; // is variation and custom attrib and value not empty or tax attrib ?>

			<?php endforeach; // attributes as attribute ?>

			<?php else: ?>

				<div class="va-help">
					<p><?php _e( 'Looks like you haven\'t added attributes yet.', 'queryloop' ); ?></p>
					<ol>
						<li><p><?php _e( 'Start by adding attributes in the Attributes tab.', 'queryloop' ); ?></p></li>
						<li><p><?php _e( 'If it\'s a variable product, define the variations in the Variations tab.', 'queryloop' ); ?></p></li>
						<li><p><?php _e( 'Once you\'re done, come back here and add visual elements. If you had defined a preset for the attribute, it will be already filled in so you just need to publish your product.', 'queryloop' ); ?></p></li>
					</ol>
				</div>
			
			<?php endif; // not empty attributes

			if ( $add_icons ) {
				require_once QL_VISUAL_ATTRIBUTES_DIR . 'icons/icons.php';
			}

			/**
			 * Action hook to add icon sets.
			 */
			do_action( 'queryloop_visual_attributes_icon_picker_sets' );
			?>

		</div>
	<?php
	}

	/**
	 * If entry in visual attributes array doesn't exist, checks if a related term meta preset exists and returns it. Otherwise returns an empty string.
	 * 
	 * @since 1.0.9
	 * 
	 * @param string $visual_attribute_id Text ID of the entry to check in visual attributes array.
	 * @param int $term_id Attribute term ID.
	 * @param string $meta_key Meta data key to check looking for the preset.
	 * @param string $type Type of attribute meta data to return, image, icon, color.
	 * 
	 */
	function get_preset( $visual_attribute_id, $term_id, $meta_key, $type = '' ) {
		global $ql_visual_attributes_term_meta;
		/**
		 * Filters what happens when all visual attributes at product level are empty or a single visual attribute is empty.
		 * 
		 * @since 1.1.5
		 * 
		 * @param bool $condition If entry in visual attributes is cleared, fetch the preset again.
		 * @param string $visual_attribute_id Key to check in visual attributes array.
		 *
		 * @return bool
		 */
		$fetch_preset_condition = apply_filters( 'queryloop_visual_attributes_get_preset_condition', ! empty( $this->vas ) && ! empty( $this->vas[$visual_attribute_id] ), $visual_attribute_id );
		
		if ( $fetch_preset_condition ) {
			$preset = '';
		} else {
			switch ( $type ) {
				case 'image':
					$preset = $ql_visual_attributes_term_meta->get_term_image_url( $term_id, $meta_key, 'json' );
					break;
				case 'icon':
					$preset = $ql_visual_attributes_term_meta->get_term_icon( $term_id, $meta_key, 'class' );
					break;
				case 'color':
					$preset = $ql_visual_attributes_term_meta->get_term_color( $term_id, $meta_key, 'object' );
					break;
				default:
					$preset = $ql_visual_attributes_term_meta->get_term_meta( $term_id, $meta_key );
					break;
			}
		}
		/**
		 * Filters the preset returned.
		 *
		 * @since 1.0.9
		 * 
		 * @param string $preset The preset returned.
		 * @param string $visual_attribute_id Text ID of the entry to check in visual attributes array.
		 * @param int $term_id Attribute term ID.
		 * @param string $meta_key Meta data key to check looking for the preset.
		 * @param string $type Type of attribute meta data to return, image, icon, color.
		 */
		return apply_filters( 'queryloop_visual_attributes_get_preset', $preset, $visual_attribute_id, $term_id, $meta_key, $type );
	}

	/**
	 * Output a hidden input box.
	 *
	 * @since 1.0.0
	 *
	 * @access public
	 * @param array $field
	 * @return void
	 */
	function input_field( $field ) {
		if ( $value = $this->get_vas( $field['id'] ) ) {
			$va_value = $value;
		} else {
			$va_value = isset( $field['default'] ) ? $field['default'] : '';
		}
		$field['class'] = isset( $field['class'] ) ? $field['class'] : '';
		$field['type'] = isset( $field['type'] ) ? $field['type'] : 'hidden';

		echo '<input type="' . $field['type'] . '" class="' . esc_attr( $field['class'] ) . '" name="' . esc_attr( $field['id'] ) . '" id="' . esc_attr( $field['id'] ) . '" value="' . esc_attr( $va_value ) .  '" /> ';
	}

	/**
	 * AJAX action to update the fields.
	 *
	 * @since 1.0.0
	 */
	function ajax_update_fields() {
		check_ajax_referer( $this->prefix . '-nonce', 'nonce' );
		$post_id = $_POST['post_id'];

		// Save Fields
		if ( isset( $_POST['data'] ) ) {
			parse_str( $_POST['data'], $attributes );
			if ( ! empty( $attributes ) ) {
				$collection = array();
				foreach ( $attributes as $field => $value ) {
					if ( false !== stripos( $field, '_va_dropdown_' ) ) {
						// Save option to use standard dropdown
						update_post_meta( $post_id, $field, isset( $value ) && 1 == $value ? 1 : 0 );
					} elseif ( false !== stripos( $field, '_va_show_on_loop_' ) ) {
						// Save option to use standard dropdown
						update_post_meta( $post_id, $field, $value );
					} elseif ( false !== stripos( $field, '_va_showname_' ) ) {
						// Save option to display attribute name
						update_post_meta( $post_id, $field, $value );
					} elseif ( ! empty( $value ) ) {
						$collection[$field] = esc_attr( $value );
					}
				}
				// Save visual attributes
				update_post_meta( $post_id, '_va_collection', array_filter( $collection ) );
			}
		}

		ob_start();
		$this->render_fields( $post_id );
		$out = ob_get_clean();

		// Display Fields
		wp_send_json_success( $out );
	}

	/**
	 * Save fields in additional panel
	 *
	 * @since 1.0.0
	 */
	function save_fields( $post_id ) {
		// Save checkbox: whether user wants Visual Attributes or not
		update_post_meta( $post_id, '_disable_va', isset( $_POST['_disable_va'] ) && in_array( $_POST['_disable_va'], array( 'setting', 'yes', 'no' ) ) ? $_POST['_disable_va'] : 'setting' );
		update_post_meta( $post_id, '_name_style', isset( $_POST['_name_style'] ) && in_array( $_POST['_name_style'], array( 'label', 'tooltip' ) ) ? $_POST['_name_style'] : 'label' );
		update_post_meta( $post_id, '_only_va', isset( $_POST['_only_va'] ) ? 'yes' : 'no' );

		$attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

		if ( ! empty( $attributes ) ) {
			$collection = array();
			foreach ( $attributes as $attribute ) {
				if ( $this->rendering_condition( $post_id, $attribute ) ) {
					if ( 1 == $attribute['is_taxonomy'] ) {
						$terms = wp_list_pluck( get_terms( $attribute['name'], array( 'hide_empty' => false ) ), 'slug' );
					} else {
						$terms = $this->get_attribute_list( $attribute['value'] );
					}

					/**
					 * Sanitized attribute name to be used as part of field id.
					 *
					 * @var string $attribute_attr
					 */
					$attribute_attr = esc_attr( sanitize_title( $attribute['name'] ) );

					// Save option to use standard dropdown
					$dropdown_id = '_va_dropdown_'.$attribute_attr;
					update_post_meta( $post_id, $dropdown_id, isset( $_POST[$dropdown_id] ) && '1' == $_POST[$dropdown_id] ? 1 : 0 );

					$show_on_loop_id = '_va_show_on_loop_'.$attribute_attr;
					update_post_meta( $post_id, $show_on_loop_id, isset( $_POST[$show_on_loop_id] ) ? $_POST[$show_on_loop_id] : array() );

					// Save option to display attribute name
					$showname_id = '_va_showname_'.$attribute_attr;
					update_post_meta( $post_id, $showname_id, isset( $_POST[$showname_id] ) ? $_POST[$showname_id] : '' );

					foreach ( $terms as $term_raw ) {
						/**
						 * Term slug to be used as part of field id.
						 *
						 * @var string $term
						 */
						$term = esc_attr( sanitize_title( $term_raw ) );
						$fields = array( '_va_type_', '_va_image_', '_va_icon_', '_va_icon_color_', '_va_icon_color_bg_', '_va_color_', '_va_text_', '_va_text_color_', '_va_text_color_bg_' );
						foreach ( $fields as $field ) {
							$value = isset( $_POST[$field.$attribute_attr.$term] ) ? $_POST[$field.$attribute_attr.$term] : '';
							
							if ( ! empty( $value ) ) {
								$collection[$field.$attribute_attr.$term] = esc_attr( $value );
							}
						}
					}
				}
			}
			update_post_meta( $post_id, '_va_collection', array_filter( $collection ) );
		}
	}

	/**
	 * Define icons to use
	 *
	 * @since 1.0.5
	 *
	 * @return array
	 */
	function get_icon_sets() {
		if ( !isset( $this->icons ) ) {
			$this->icons = apply_filters( 'queryloop_visual_attributes_icon_sets', array(
					'genericon' => array(
						'button_label' => __( 'Pick Genericon', 'queryloop' ),
						'url' => '/icons/gi/genericons.css',
					),
					'fa' => array(
						'button_label' => __( 'Pick FontAwesome', 'queryloop' ),
						'url' => '/icons/fa/css/font-awesome.min.css',
					),
				)
			);
		}
		return $this->icons;
	}

	/**
	 * Add scripts and styles for additional product panel.
	 *
	 * @since 1.0.0
	 */
	function enqueue_meta_box_assets() {
		global $post, $typenow;
		if ( ( isset( $typenow ) && $typenow == $this->cpt ) || ( isset( $post ) && is_object( $post ) && $this->cpt === $post->post_type ) ) {
			wp_enqueue_media();
			if ( ! wp_script_is( 'ql-color-picker-js' ) ) {
				wp_enqueue_style( 'ql-color-picker', QL_VISUAL_ATTRIBUTES_URI . '/css/color-picker.css', array(), false, 'screen' );
				wp_enqueue_script( 'ql-color-picker-js', QL_VISUAL_ATTRIBUTES_URI . '/js/color-picker.js' );
			}

			foreach( $this->get_icon_sets() as $key => $icon ) {
				wp_enqueue_style( 'ql-icon-picker-'.$key, QL_VISUAL_ATTRIBUTES_URI . $icon['url'], array(), false, 'screen' );
			}

			wp_enqueue_style( 'ql-icon-picker', QL_VISUAL_ATTRIBUTES_URI . '/css/icon-picker.css', array(), false, 'screen' );
			wp_enqueue_script( 'ql-icon-picker-js', QL_VISUAL_ATTRIBUTES_URI . '/js/icon-picker.js' );

			wp_enqueue_style( $this->prefix . '-meta-box', QL_VISUAL_ATTRIBUTES_URI . '/css/ql-metabox.css' );

			if ( is_rtl() ) {
				wp_enqueue_style( $this->prefix . '-metabox-rtl', QL_VISUAL_ATTRIBUTES_URI . '/css/ql-metabox-rtl.css' );
			}

			wp_enqueue_script( $this->prefix . '-admin', QL_VISUAL_ATTRIBUTES_URI . '/js/ql-admin-script.js' );

			$params = array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'nonce' => wp_create_nonce( $this->prefix . '-nonce' ),
				'confirm_update' => __( 'This will save the current visual attributes and update the list. Do you want to proceed?', 'queryloop' ),
				'icons' => array_keys( $this->get_icon_sets() ),
			);

			wp_localize_script( $this->prefix . '-admin', $this->prefix . '_adminjs', apply_filters( 'queryloop_visual_attributes_admin_js_vars', $params ) );
		}
	}

	/**
	 * Displays visual attributes in loops.
	 *
	 * @since 1.0.9
	 */
	function show_on_loop() {
		$post_id = get_the_ID();

		// If it's not enabled, exit.
		if ( ! $this->is_enabled( $post_id ) ) {
			return;
		}

		// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
		$attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );

		if ( ! empty( $attributes ) ) :
			$attributes_to_show = array();

			foreach( $attributes as $attribute ) :

				if ( $this->rendering_condition( $post_id, $attribute ) ) :
					$attribute_slug = '';
					if ( $attribute['is_taxonomy'] ) {
						$attr_tax = get_taxonomy( $attribute['name'] );
						$attribute_slug = $attr_tax->name;
					}
					/**
					 * Sanitized attribute name to be used as part of field id.
					 *
					 * @var string $attribute_attr
					 */
					if ( '' != $attribute_slug ) {
						$attribute_attr = $attribute_slug;
					} else {
						$attribute_attr = esc_attr( sanitize_title( $attribute['name'] ) );
					}

					$show_on_loop_id = '_va_show_on_loop_' . $attribute_attr;
					$show_on_loop = get_post_meta( $post_id, $show_on_loop_id, true );
					// Can be empty string or array with a single empty element
					if ( ! empty( $show_on_loop ) ) {
						// If it's string, make it array
						if ( ! is_array( $show_on_loop ) ) {
							$show_on_loop = array_map( array( $this, 'prepare_attribute_name' ), $this->get_attribute_list( $show_on_loop ) );
						}
						// Make sure it's not empty after clearing empty elements
						$show_on_loop = array_filter( $show_on_loop );
						if ( ! empty( $show_on_loop ) ) {
							$attribute['show_on_loop'] = $show_on_loop;
							$attributes_to_show[$attribute_attr] = $attribute;
						}
					}

				endif;

			endforeach;
			
			if ( ! empty( $attributes_to_show ) ) {
				$this->render_attributes( $attributes_to_show );
			}

		endif;
	}

	/**
	 * Sanitizes and escapes attribute names.
	 *
	 * @since 1.0.9
	 *
	 * @param string $name Attribute name.
	 *
	 * @return string
	 */
	function prepare_attribute_name( $name ) {
		return esc_attr( sanitize_title( $name ) );
	}

	/**
	 * Allow to write the JSON encoded data for all variations available in product.
	 *
	 * @since 1.1.2
	 *
	 * @param int Number of allowed variations. By default is 20.
	 *
	 * @return int A large number so all available variations are written to variations form.
	 */
	function variation_threshold( $number ) {
		return apply_filters( 'queryloop_visual_attributes_variation_threshold', 1000000 );
	}

	/**
	 * Displays visual variations elements on front end.
	 *
	 * @since 1.0.0
	 */
	function render_attributes( $custom_attribute_set = '', $in_tab = false ) {
		$post_id = get_the_ID();

		$product = wc_get_product( $post_id );

		$this->is_show_on_loop = ! empty( $custom_attribute_set );

		$is_wc_24 = $this->check_wc_24();

		if ( $this->is_show_on_loop ) {
			// Hand-picked attribute set that user wants to display in loop
			$attributes = $custom_attribute_set;
		} else {
			// Product attributes - taxonomies and custom, ordered, with visibility and variation attributes set
			$attributes = maybe_unserialize( get_post_meta( $post_id, '_product_attributes', true ) );
		}

		// If there are no attributes to work with, exit.
		if ( empty( $attributes ) ) {
			return;
		}

		// Main plugin style.
		wp_enqueue_style( $this->prefix . '-css' );
		if ( is_rtl() ) {
			wp_enqueue_style( $this->prefix . '-css-rtl' );
		}

		//$available_variations = $product->get_available_variations();
		if ( $product->is_type( 'variable' ) ) {
			$product_attributes = $product->get_variation_attributes();
			$selected_attributes = $product->get_variation_default_attributes();
			$ql_wrap_class = 'va-variable';
			// Main plugin script
			wp_enqueue_script( $this->prefix . '-js' );
		} else {
			$product_attributes = $product->get_attributes();
			$selected_attributes = array();
			$ql_wrap_class = '';
		}

		if ( $this->is_show_on_loop ) {
			$ql_wrap_class .= ' va-show-on-loop';
		}

		if ( $label_style = get_post_meta( $post_id, '_name_style', true ) ) {
			$ql_wrap_class .= ' va-' . $label_style;
		} else {
			$ql_wrap_class .= ' va-label';
		}

		/**
		 * Action hook to insert content before the visual attributes area.
		 *
		 * @since 1.0.2
		 *
		 * @param int $post_id The ID of the current product.
		 */
		do_action( 'queryloop_visual_attributes_before', $post_id ); ?>

		<?php if ( ! $in_tab ) : ?>
		<div class="ql-visual-attributes <?php echo esc_attr( $ql_wrap_class ); ?>">
			<div class="va-separator clear"></div>
		<?php endif; ?>

			<?php foreach( $attributes as $attribute ) :

				if ( $this->rendering_condition( $post_id, $attribute ) ) :
					$attribute_slug = '';
					if ( 1 == $attribute['is_taxonomy'] ) {

						$orderby = wc_attribute_orderby( $attribute['name'] );
						$args = array();

						switch ( $orderby ) {
							case 'name' :
								$args = array( 'orderby' => 'name', 'hide_empty' => false, 'menu_order' => false );
								break;
							case 'id' :
								$args = array( 'orderby' => 'id', 'order' => 'ASC', 'menu_order' => false, 'hide_empty' => false );
								break;
							case 'menu_order' :
								$args = array( 'menu_order' => 'ASC', 'hide_empty' => false );
								break;
						}

						// Get all terms sorted to use for next step
						$all_terms = get_terms( $attribute['name'], $args );
						// If the attribute is erroneous and user is logged in
						if ( is_wp_error( $all_terms ) ) {
							if ( is_user_logged_in() && current_user_can( 'manage_options' ) && isset( $all_terms->errors ) ) {
								foreach ( $all_terms->errors as $error_code => $error_messages ) {
									echo "<p>{$attribute['name']} - $error_code: ";
									foreach ( $error_messages as $error_message ) {
										echo $error_message;
									}
									echo '</p>';
								}
							}
							continue;
						}
						$all_terms = wp_list_pluck( $all_terms, 'slug' );
						if ( $product->is_type( 'variable' ) ) {
							// Get terms used for variations in this product and sort them accordingly
							$terms = array_intersect( $all_terms, $product_attributes[$attribute['name']] );
						} else {
							$terms = wc_get_product_terms( $product->id, $attribute['name'], array( 'fields' => 'names' ) );
						}

						$attr_tax = get_taxonomy( $attribute['name'] );
						$attribute_name = $attr_tax->labels->name;
						$attribute_slug = $attr_tax->name;
					} else {
						$attribute_name = $attribute['name'];
						$terms = $this->get_attribute_list( $attribute['value'] );
					}
					/**
					 * Sanitized attribute name to be used as part of field id.
					 *
					 * @var string $attribute_attr
					 */
					if ( '' != $attribute_slug ) {
						$attribute_attr = $attribute_slug;
					} else {
						$attribute_attr = esc_attr( sanitize_title( $attribute['name'] ) );
					}

					// Set selected attribute
					if ( isset( $_REQUEST[ 'attribute_' . $attribute_attr ] ) ) {
						$selected_value = $_REQUEST[ 'attribute_' . $attribute_attr ];
					} elseif ( isset( $selected_attributes[ $attribute_attr ] ) ) {
						$selected_value = $selected_attributes[ $attribute_attr ];
					} else {
						$selected_value = '';
					}
					?>

					<?php if ( $in_tab ) : ?>
					<tr class="<?php echo esc_attr( $ql_wrap_class ); ?>"><th><?php echo $attribute_name; ?></th><td>
					<?php else: ?>
					<label class="va-attribute-label"><?php echo $attribute_name; ?></label>
					<?php endif; ?>

					<?php
					// Standard dropdown
					$dropdown_id = '_va_dropdown_'.$attribute_attr;
					$dropdown = get_post_meta( $post_id, $dropdown_id, true );
					/**
					 * Filter the condition to use a dropdown instead of a visual attribute.
					 *
					 * @since 1.1.1
					 * 
					 * @param bool Whether user set the attribute to use dropdown.
					 * @param string $attribute_attr Current attribute term.
					 * @return bool
					 */
					if ( apply_filters( 'queryloop_visual_attributes_use_dropdown', '1' == $dropdown && ! $in_tab, $attribute_attr ) ) : if ( ! $this->is_show_on_loop ) : ?>
						<div class="va-pickers">
							<?php if ( $product->is_type( 'variable' ) ) : ?>
							<select id="<?php echo $attribute_attr; ?>" name="<?php echo "attribute_$attribute_attr"; ?>" data-attribute="<?php echo $attribute_attr; ?>">
								<option value=""><?php echo __( 'Choose an option', 'queryloop' ) ?>&hellip;</option>

								<?php foreach ( $terms as $term_raw ) :

									/**
									 * Term slug to be used as part of field id.
									 *
									 * @var string $term
									 */
									$term = esc_attr( sanitize_title( $term_raw ) );
									$datas = 'data-attribute="' . $attribute_attr . '" data-term="' . esc_attr( $is_wc_24 ? $term_raw : $term ) . '"';
									$sel_val = sanitize_title( $selected_value );
									$current = sanitize_title( $term_raw );
									echo '<option class="va-option" value="' . esc_attr( $is_wc_24 ? $term_raw : $term ) . '" ' . selected( $sel_val, $current, false ) . " $datas >" . esc_html( apply_filters( 'woocommerce_variation_option_name', $this->get_attribute_name( $attribute_attr, $term_raw ) ) ) . '</option>';

								endforeach; // dropdown terms ?>

							</select>
								<?php else: // is not variable, just write the attribute names

								$values = array_map( 'trim', $terms );
								echo apply_filters( 'woocommerce_attribute', wpautop( wptexturize( implode( ', ', $values ) ) ), $attribute, $values );

							endif; // variable ?>
						</div>
						<div class="va-separator clear"></div>
						<?php
						// Move on to next attribute
						continue;
					endif; /* is show on loop */ continue; endif; // end isset dropdown and 1 == dropdown
					
					// Begin Visual Attributes ?>
					<div class="va-pickers">
						<?php
						$this->vas = get_post_meta( $post_id, '_va_collection', true );
						$this->post_id = $post_id;
						foreach ( $terms as $term_raw ) :
							/**
							 * Term slug to be used as part of field id.
							 *
							 * @var string $term
							 */
							$term = esc_attr( sanitize_title( $term_raw ) );
							if ( $this->is_show_on_loop && $product->is_type( 'variable' ) ) {
								if ( ! in_array( $term, $custom_attribute_set[$attribute_attr]['show_on_loop'] ) ) {
									continue;
								}
								$link = 'href="' . esc_url( add_query_arg( 'attribute_' . $attribute_attr, esc_attr( $term ), get_permalink() ) ) . '"';
							} else {
								$link = '';
							}
							/**
							 * Filters the link applied to attributes shown on shop loop.
							 *
							 * @since 1.0.9
							 * 
							 * @param string $link The href attribute with the URL pointing to single view.
							 */
							$link = apply_filters( 'queryloop_visual_attributes_show_on_loop_link', $link );
							$datas = 'data-attribute="' . $attribute_attr . '" data-term="' . esc_attr( $is_wc_24 ? $term_raw : $term ) . '"';

							// If there's a pre-selected value, set va-selected or va-hidden accordingly
							if ( ! empty( $selected_value ) ) {
								/**
								 * CSS class specifying that the item is selected by default.
								 *
								 * @var string $selected
								 */
								$selected = sanitize_title( $selected_value ) == $term ? 'va-start-selected' : '';
							} else {
								$selected = '';
							}

							// Type Selector
							$type_id = '_va_type_'.$attribute_attr.$term;
							$type = $this->get_vas( $type_id );
							$type = $type ? $type : 'image';

							switch ( $type ) :

								case 'image':
									// Image
									$image_id = '_va_image_'.$attribute_attr.$term;
									$image =  $this->get_vas( $image_id );
									$image = json_decode( html_entity_decode( $image ) );
									if ( isset( $image->thumbnail ) ) {
										/**
										 * Filters the markup for the attribute.
										 *
										 * @since 1.0.7
										 * @since 1.1.3 Passes image object.
										 *
										 * @param string HTML markup for the visual picker.
										 * @param object Image object with id, url and thumbnail properties.
										 */
										echo apply_filters( 'queryloop_visual_attributes_picker', '<a class="va-picker va-picker-image ' . esc_attr( $selected ) . '" ' . $datas . ' title="' . esc_attr( $this->get_attribute_name( $attribute_attr, $term_raw ) ) . '" ' . $link . '><img class="va-picker-item va-image" src="' . esc_url( $image->thumbnail ) . '" alt="' . esc_attr( $term ) . '"/>' . $this->display_name( $attribute_attr, $post_id, $term_raw ) . '</a>', $image );
									}
									break;

								case 'icon':
									// Icon
									$icon_id = '_va_icon_'.$attribute_attr.$term;
									if ( $icon =  $this->get_vas( $icon_id ) ) :
										// Add icon sets
										foreach( array_keys( $this->get_icon_sets() ) as $key ) {
											if ( !wp_script_is( "ql-icon-picker-$key" ) ) {
												if ( false !== stripos( $icon, $key ) ) {
													wp_enqueue_style( "ql-icon-picker-$key" );
												}
											}
										}
										$icon_color =  $this->get_vas( '_va_icon_color_'.$attribute_attr.$term );
										$icon_color_bg =  $this->get_vas( '_va_icon_color_bg_'.$attribute_attr.$term );
										if ( $icon_color || $icon_color_bg ) {
											$icon_style = 'style="';
											$icon_style .= $icon_color? $this->build_color_style( $icon_color, 'color', false ) : '';
											$icon_style .= $icon_color_bg? $this->build_color_style( $icon_color_bg, 'background-color', false ) : '';
											$icon_style .= '"';
										} else {
											$icon_style = '';
										}
										/**
										 * Filters the markup for the attribute.
										 *
										 * @since 1.0.7
										 *
										 * @param string
										 */
										echo apply_filters( 'queryloop_visual_attributes_picker', '<a class="va-picker va-picker-icon ' . esc_attr( $selected ) . '" ' . $datas . ' title="' . esc_attr( $this->get_attribute_name( $attribute_attr, $term_raw ) ) . '" ' . $link . '><i class="va-picker-item va-icon ' . esc_attr( $icon ) . '" ' . $icon_style . '></i>' . $this->display_name( $attribute_attr, $post_id, $term_raw ) . '</a>' );
									endif;
									break;

								case 'color':
									// Color
									$color_id = '_va_color_'.$attribute_attr.$term;

									if ( $color =  $this->get_vas( $color_id ) ) :
										$color_style = $this->build_color_style( $color, 'background-color' );
										/**
										 * Filters the markup for the attribute.
										 *
										 * @since 1.0.7
										 *
										 * @param string
										 */
										echo apply_filters( 'queryloop_visual_attributes_picker', '<a class="va-picker va-picker-color ' . esc_attr( $selected ) . '" ' . $datas . ' title="' . esc_attr( $this->get_attribute_name( $attribute_attr, $term_raw ) ) . '" ' . $link . '><span class="va-picker-item va-color" ' . $color_style . '></span>' . $this->display_name( $attribute_attr, $post_id, $term_raw ) . '</a>');
									endif;
									break;

								case 'text':
									// Text
									$text_id = '_va_text_'.$attribute_attr.$term;
									if ( $text =  $this->get_vas( $text_id ) ) :
										$text_color =  $this->get_vas( '_va_text_color_'.$attribute_attr.$term );
										$text_color_bg =  $this->get_vas( '_va_text_color_bg_'.$attribute_attr.$term );
										if ( $text_color || $text_color_bg ) {
											$text_style = 'style="';
											$text_style .= $text_color? $this->build_color_style( $text_color, 'color', false ) : '';
											$text_style .= $text_color_bg? $this->build_color_style( $text_color_bg, 'background-color', false ) : '';
											$text_style .= '"';
										} else {
											$text_style = '';
										}
										/**
										 * Filters the markup for the attribute.
										 *
										 * @since 1.0.7
										 *
										 * @param string
										 */
										echo apply_filters( 'queryloop_visual_attributes_picker', '<a class="va-picker va-picker-text ' . esc_attr( $selected ) . '" ' . $datas . ' title="' . esc_attr( $this->get_attribute_name( $attribute_attr, $term_raw ) ) . '" ' . $link . '><span class="va-picker-item va-text ' . esc_attr( $text ) . '" ' . $text_style . '>' . esc_html( $text ) . '</span>' . $this->display_name( $attribute_attr, $post_id, $term_raw ) . '</a>' );
									endif;
									break;

							endswitch;
							?>

						<?php endforeach; // terms ?>
					</div>
					<!-- /.va-pickers -->

				<?php if ( $in_tab ) : ?>
				</td></tr>
				<?php else: ?>
				<div class="va-separator clear"></div>
				<?php endif; ?>

				<?php endif; // is variation ?>

			<?php endforeach; ?>

		<?php if ( ! $in_tab ) : ?>
		</div><!-- /.ql-visual-variations -->
		<?php endif; ?>

		<?php 
		/**
		 * Action hook to insert content after the visual attributes area.
		 *
		 * @since 1.0.2
		 *
		 * @param int $post_id The ID of the current product.
		 */
		do_action( 'queryloop_visual_attributes_after', $post_id ); ?>

	<?php
	}

	/**
	 * Checks if user is using WooCommerce 2.4 or previous.
	 * This is to know whether to use sanitized or unsanitized value for inline attributes
	 *
	 * @since 1.1.1
	 *
	 * @param string $attribute
	 * @param int $post_id
	 */
	function check_wc_24() {
		global $woocommerce;
		return version_compare( $woocommerce->version, '2.4', '>=' );
	}

	/**
	 * Checks if the attribute name should be displayed. User choice is cached for all terms of an attribute
	 * to avoid fetching post meta more than once.
	 *
	 * @since 1.1.1
	 *
	 * @param string $attribute
	 * @param int $post_id
	 */
	function maybe_show_name( $attribute, $post_id ) {
		if ( $this->current_attribute_name != $attribute ) {
			$showname = get_post_meta( $post_id, '_va_showname_' . $attribute, true );
			$this->current_show_name_status = ( $showname && '' != $showname ) && ( ( $this->is_show_on_loop && 'always' == $showname ) || ! $this->is_show_on_loop );
			$this->current_attribute_name = $attribute;
		}
		return $this->current_show_name_status;
	}

	/**
	 * Returns the attribute name if it's meant to be displayed.
	 *
	 * @since 1.0.2
	 * @since 1.1.1 Verification of whether the name should be displayed or not is moved to maybe_show_name()
	 *
	 * @param string $attribute
	 * @param int $post_id
	 * @param string $term_raw
	 */
	function display_name( $attribute, $post_id, $term_raw ) {
		
		$info = '';
		if ( $this->maybe_show_name( $attribute, $post_id ) ) {
			$name = '<span class="va-name">' . $this->get_attribute_name( $attribute, $term_raw ) . '</span>';
			$description = '';
			$info_with_desc = '';
			/*if ( $description = $this->get_attribute_description( $attribute, $term_raw ) ) {
				$description = '<span class="va-description">' . $description . '</span>';
				$info_with_desc = ' va-has-description';
			}*/
			$info = '<span class="va-info' . $info_with_desc . '">' . $name . $description . '</span>';
		}

		/**
		 * Filters the markup for the attribute name.
		 *
		 * @since 1.0.2
		 *
		 * @param string $info
		 */
		return apply_filters( 'queryloop_attribute_display_name', $info );
	}

	/**
	 * Returns the attribute name.
	 *
	 * @since 1.0.2
	 *
	 * @param string $attribute
	 * @param string $term_raw
	 *
	 * @return mixed|void
	 */
	function get_attribute_name( $attribute, $term_raw ) {
		$term = get_term_by( 'slug', $term_raw, $attribute );
		/**
		 * Filters the attribute name returned.
		 *
		 * @since 1.0.2
		 *
		 * @param string
		 */
		return apply_filters( 'queryloop_get_attribute_name', $term ? $term->name : $term_raw );
	}

	/**
	 * Returns the attribute description.
	 *
	 * @since 1.1.1
	 *
	 * @param string $attribute
	 * @param string $term_raw
	 *
	 * @return mixed|void
	 */
	function get_attribute_description( $attribute, $term_raw ) {
		$term = get_term_by( 'slug', $term_raw, $attribute );
		/**
		 * Filters the attribute name returned.
		 *
		 * @since 1.1.1
		 *
		 * @param string
		 */
		return apply_filters( 'queryloop_get_attribute_description', $term ? $term->description : '' );
	}

	/**
	 * Returns the list of product attributes separating them by WC_DELIMITER and applying trim to each one.
	 *
	 * @since 1.0.2
	 *
	 * @param array $attributes
	 *
	 * @return array
	 */
	function get_attribute_list( $attributes = array() ) {
		return array_map( 'trim', explode( WC_DELIMITER, $attributes ) );
	}

	/**
	 * Register scripts and styles for enqueue
	 *
	 * @since 1.0.0
	 */
	function register_scripts_and_styles() {
		// Register icon sets
		foreach( $this->get_icon_sets() as $key => $icon ) {
			wp_register_style( 'ql-icon-picker-'.$key, QL_VISUAL_ATTRIBUTES_URI . $icon['url'], array(), false, 'screen' );
		}

		// Main plugin style.
		wp_register_style( $this->prefix . '-css', QL_VISUAL_ATTRIBUTES_URI . '/css/ql-plugin-style.css' );
		wp_add_inline_style( $this->prefix . '-css', $this->get_custom_css() );
		if ( is_rtl() ) {
			wp_register_style( $this->prefix . '-css-rtl', QL_VISUAL_ATTRIBUTES_URI . '/css/ql-plugin-rtl.css', array( $this->prefix . '-css' ) );
		}

		// Main plugin script
		wp_register_script( $this->prefix . '-js', QL_VISUAL_ATTRIBUTES_URI . '/js/ql-plugin-script.js', array( 'jquery' ), false, true );

		wp_localize_script( $this->prefix . '-js', 'qlva', apply_filters( 'queryloop_visual_attributes_js_vars',
			array(
				'isMobile' => $this->is_mobile ? 'true' : 'false',
				'reclick' => 'reclick',
				'tooltipEdgeDetect' => 'false',
				'secondClickDeselects' => 'true',
			)
		));

		// The hooked function checks if the $this->prefix . '-js' script has been enqueued.
		add_action( 'wp_footer', array( $this, 'output_custom_js' ), 77 );
	}

	/**
	 * Builds a color style property.
	 *
	 * @since 1.0.0
	 *
	 * @param string $color Color in format #rrggbb_1.00 to be parsed.
	 * @param string $property CSS property to modify.
	 * @param bool $style Whether to wrap in a style="" for attribute usage.
	 *
	 * @return string
	 */
	function build_color_style( $color, $property = 'color', $style = true ) {
		$color = $this->admin->decode_color( $color );
		$color_style = '';
		if ( isset( $color->hex ) ) {
			$color_style .= "$property: " . $color->hex . ';';
		}
		if ( isset( $color->a ) ) {
			$color_style .= "$property: rgba(" . $this->admin->get_rgba( $color->hex, $color->a ).');';
		}
		if ( $style ) {
			$color_style = 'style="' . esc_attr( $color_style ) . '"';
		}
		return $color_style;
	}

	/**
	 * Outputs custom styling at the end of the site
	 *
	 * @since 1.0.0
	 */
	function get_custom_css() {

		$out = '';

		// Plugin settings styling
		$rules = array(
			array(
				'selector' => '.woocommerce .va-picker .va-picker-item',
				'props' => array(
					'border-color' => array(
						'key' => 'option_color',
					),
					'border-width' => array(
						'key' => 'optionborder_int',
						'after' => 'px',
					),
				)
			),
			array(
				'selector' => '.woocommerce .va-selected .va-picker-item',
				'props' => array(
					'border-color' => array(
						'key' => 'optionselected_color',
					),
				)
			),
			array(
				'selector' => '.woocommerce .va-picker img',
				'props' => array(
					'max-width' => array(
						'key' => 'imagesize_int',
						'after' => 'px',
					),
				)
			),
			array(
				'selector' => '.woocommerce .va-picker .va-icon',
				'props' => array(
					'width' => array(
						'key' => 'iconsize_int',
						'after' => 'px',
					),
					'height' => array(
						'key' => 'iconsize_int',
						'after' => 'px',
					)
				)
			),
			array(
				'selector' => '.woocommerce .va-picker .va-icon:before',
				'props' => array(
					'font-size' => array(
						'key' => 'iconsize_int',
						'after' => 'px',
					),
				)
			),
			array(
				'selector' => '.woocommerce .va-picker .va-icon',
				'props' => array(
					'color' => array(
						'key' => 'icon_color',
					),
					'background-color' => array(
						'key' => 'iconbg_color',
					),
				)
			),
			array(
				'selector' => '.woocommerce .va-tooltip .va-info',
				'props' => array(
					'color' => array(
						'key' => 'ttfront_color',
					),
					'background-color' => array(
						'key' => 'ttbg_color',
					),
				)
			),
			array(
				'selector' => '.va-tooltip .va-picker:hover .va-info:after',
				'props' => array(
					'border-top-color' => array(
						'key' => 'ttbg_color',
					),
				)
			),
			array(
				'selector' => '.va-tooltip .va-picker.va-tooltip-bottom:hover .va-info:after',
				'props' => array(
					'border-bottom-color' => array(
						'key' => 'ttbg_color',
					),
				)
			),
			array(
				'selector' => '.woocommerce .va-picker .va-color',
				'props' => array(
					'width' => array(
						'key' => 'colorsize_int',
						'after' => 'px',
					),
					'height' => array(
						'key' => 'colorsize_int',
						'after' => 'px',
					)
				)
			),
			array(
				'selector' => '.woocommerce .va-picker .va-text',
				'props' => array(
					'min-width' => array(
						'key' => 'textwidth_int',
						'after' => 'px',
					),
					'min-height' => array(
						'key' => 'textheight_int',
						'after' => 'px',
					),
				)
			),
		);
		foreach ( $rules as $rule ) {
			$out .= $this->admin->get_style( $rule, false );
		}

		// Add Custom Styling.
		if ( '' !== ( $customcss = $this->admin->get( 'customcss' ) ) ) {
			$out .= "\n" . $customcss;
		}

		return $out;
	}

	/**
	 * Outputs custom JavaScript at the end of the site
	 *
	 * @since 1.0.0
	 */
	function output_custom_js() {
		if ( wp_script_is( $this->prefix . '-js' ) ) {
			// Add Custom JavaScript.
			$js = $this->admin->get( 'customjs' );

			if ( '' != $js  && $js ) {
				echo "\n<!-- Visual Attributes JS -->\n<script type=\"text/javascript\">\n$js\n</script>\n<!-- Visual Attributes JS -->\n";
			}
		}
	}
}