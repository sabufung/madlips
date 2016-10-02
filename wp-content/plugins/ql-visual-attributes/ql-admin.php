<?php
/**
 * Creates a settings page for the plugin
 * @author	QueryLoop
 * @since	1.0.0
 */
class QL_Visual_Attributes_Admin {

	var $settings;
	var $settings_page_id;
	var $basefile;
	var $prefix;
	var $plugin_data = array();
	var $dialog_nonce = '';
	
	function __construct ( $args = array() ) {
		$this->prefix = $args['prefix'] . '_';
		$this->settings = get_option( $this->prefix . 'settings' );
		$this->dialog_nonce = $this->prefix . '_dialog_nonce';

		if ( is_admin() ) {
			$this->settings_page_id = $this->prefix . 'options';
			$this->basefile = $args['basefile'];

			add_action( 'admin_init', array( $this, 'admin_init' ) );
			add_action( 'admin_menu', array( $this, 'plugin_menu' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_script_and_style' ) );

			add_action( 'woocommerce_product_bulk_edit_end', array( $this, 'product_bulk_edit_add' ) );
			add_action( 'woocommerce_product_bulk_edit_save', array( $this, 'product_bulk_edit_save' ) );

			register_activation_hook( $this->basefile, array( $this, 'activate' ) );
			register_deactivation_hook( $this->basefile, array( $this, 'deactivate' ) );
			register_uninstall_hook( $this->basefile, 'ql_visual_attributes_uninstall_hook' );
		}
	}

	/**
	 * Saves options added to product bulk edit.
	 *
	 * @since 1.0.5
	 *
	 * @param $product
	 */
	function product_bulk_edit_save( $product ) {
		if ( ! empty( $_REQUEST['_disable_va'] ) ) {
			if ( 'yes' == $_REQUEST['_disable_va'] ) {
				update_post_meta( $product->id, '_disable_va', 'yes' );
			} else {
				update_post_meta( $product->id, '_disable_va', '' );
			}
		}
	}

	/**
	 * Adds options to product bulk edit.
	 * _disable_va: enable or disable Visual Attributes.
	 *
	 * @since 1.0.5
	 */
	function product_bulk_edit_add() {
		?>
		<label>
			<span class="title"><?php esc_html_e( 'Visual Attributes?', 'woocommerce' ); ?></span>
			    <span class="input-text-wrap">
			    	<select class="disable_va" name="_disable_va">
						<?php
						$options = array(
							'' => __( '&mdash; No Change &mdash;', 'queryloop' ),
							'setting' => __( 'Use General State Option', 'queryloop' ),
							'no' => __( 'Yes', 'queryloop' ),
							'yes' => __( 'No', 'queryloop' )
						);
						foreach ( $options as $key => $value ) {
							echo '<option value="' . esc_attr( $key ) . '">' . esc_html( $value ) . '</option>';
						}
						?>
					</select>
			</span>
		</label>
		<?php
	}

	/**
	 * Creates the contextual help for this plugin
	 * @param string
	 * @return string
	 * @since 1.0.0
	 */
	function help() {
		
		$screen = get_current_screen();
			
		$html = '<h5>' . __( 'Welcome!', 'queryloop' ) . '</h5>';
		$html .= '<p>' . sprintf( __( 'Thanks for purchasing %s. This plugin displays attributes as images, icons or colors.', 'queryloop' ), $this->plugin_data['Name'] ) . '</p>';
		$html .= '<h5>' . __( 'About the settings', 'queryloop' ) . '</h5>';
		$html .= '<p>' . __( 'The settings in this page set default values to be used throughout the site.', 'queryloop' ) . '</p>';
		
		$html .= '<p><em>' . sprintf( __( '%s created by Elio Rivero. Follow %s on Twitter for the latest updates.', 'queryloop' ),
			$this->plugin_data['Name'],
			'<a href="http://twitter.com/eliorivero">@eliorivero</a>'
		) . '</em></p>';

		$screen->add_help_tab( array(
			'id'      => 'ql-help-main',
			'title'   => __( 'Introduction', 'queryloop' ),
			'content' => $html,
		));

	}
	
	/**
	 * Creates the options page for this plugin
	 * @since 1.0.0
	 */
	function options_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __('You do not have sufficient permissions to access this page.', 'queryloop') );
		}
		?>
		<div class="wrap">
			<h2><?php echo $this->plugin_data['Name']; ?></h2>
			
			<form action="options.php" method="post">
				<?php settings_fields( $this->prefix . 'settings' ); ?>
				<?php do_settings_sections( $this->prefix . 'options' ); ?>
				<input class="button-primary" name="<?php _e( 'Submit','queryloop' ); ?>" type="submit" value="<?php esc_attr_e( 'Save Changes', 'queryloop' ); ?>" />
			</form>
		</div>
		<?php
	}
	
	/**
	 * Defines the settings field for the plugin options page.
	 * @since 1.0.0
	 */
	function admin_init() {

		register_setting( $this->prefix . 'settings', $this->prefix . 'settings', array( $this, 'validate_options' ) );

		add_settings_section( $this->prefix . 'general', __( 'General Settings', 'queryloop' ), array( $this, 'main_desc' ), $this->prefix . 'options' );

		add_settings_section( $this->prefix . 'customcss', __( 'Global Styling', 'queryloop' ), array( $this, 'customcss_desc' ), $this->prefix . 'options' );

		$sections['general'] = array(
			array(
				'id' => 'status_sel',
				'label' => __( 'General State', 'queryloop' ),
				'type' => 'select',
				'default' => 'enabled',
				'options' => array(
					__( 'Enabled', 'queryloop' ) => 'enabled',
					__( 'Disabled', 'queryloop' ) => 'disabled',
				),
				'help' => __( 'You can enable or disable Visual Attributes on each product.', 'queryloop' )
			),
			array(
				'id' => 'usage_sel',
				'label' => __( 'Context of Use', 'queryloop' ),
				'type' => 'select',
				'default' => 'both',
				'options' => array(
					__( 'Mobile &amp; Desktop', 'queryloop' ) => 'both',
					__( 'Mobile Only', 'queryloop' ) => 'mobile',
					__( 'Desktop Only', 'queryloop' ) => 'desktop',
				),
				'help' => __( 'When to display Visual Attributes.', 'queryloop' )
			),
			array(
				'id' => 'delete_on_uninstall_chk',
				'label' => __( 'Remove Options on Uninstall', 'queryloop' ),
				'type' => 'checkbox',
				'default' => 1,
				'help' => __( 'Check this to remove options on uninstall.', 'queryloop' ),
			),
		);

		$sections['customcss'] = array(
			array(
				'id' => 'optionborder_int',
				'label' => __( 'Option Border Size', 'queryloop' ),
				'type' => 'text',
				'default' => 2,
				'class' => 'small-text',
				'help' => __( 'Border width of option in pixels. Enter 0 to remove.', 'queryloop' )
			),
			array(
				'id' => 'option_color',
				'label' => __( 'Border Color', 'queryloop' ),
				'type' => 'color',
				'default' => 'inherit'
			),
			array(
				'id' => 'optionselected_color',
				'label' => __( 'Selected Border', 'queryloop' ),
				'type' => 'color',
				'default' => 'inherit'
			),
			array(
				'id' => 'imagesize_int',
				'label' => __( 'Image Width', 'queryloop' ),
				'type' => 'text',
				'default' => 64,
				'class' => 'small-text',
				'help' => __( 'Maximum width of image option in pixels.', 'queryloop' )
			),
			array(
				'id' => 'iconsize_int',
				'label' => __( 'Icon Size', 'queryloop' ),
				'type' => 'text',
				'default' => 32,
				'class' => 'small-text',
				'help' => __( 'Size of icon option in pixels.', 'queryloop' )
			),
			array(
				'id' => 'icon_color',
				'label' => __( 'Icon Color', 'queryloop' ),
				'type' => 'color',
				'default' => 'inherit'
			),
			array(
				'id' => 'iconbg_color',
				'label' => __( 'Icon Background', 'queryloop' ),
				'type' => 'color',
				'default' => 'inherit'
			),
			array(
				'id' => 'colorsize_int',
				'label' => __( 'Color Swatch Size', 'queryloop' ),
				'type' => 'text',
				'default' => 32,
				'class' => 'small-text',
				'help' => __( 'Square size of color swatch option in pixels.', 'queryloop' )
			),
			array(
				'id' => 'textwidth_int',
				'label' => __( 'Text Label Width', 'queryloop' ),
				'type' => 'text',
				'default' => 32,
				'class' => 'small-text',
				'help' => __( 'Minimum width of text labels in pixels.', 'queryloop' )
			),
			array(
				'id' => 'textheight_int',
				'label' => __( 'Text Label Height', 'queryloop' ),
				'type' => 'text',
				'default' => 0,
				'class' => 'small-text',
				'help' => __( 'Minimum height of text labels in pixels. Default is 0 for auto adjust.', 'queryloop' )
			),
			array(
				'id' => 'ttfront_color',
				'label' => __( 'Tooltip Text', 'queryloop' ),
				'type' => 'color',
				'default' => 'inherit'
			),
			array(
				'id' => 'ttbg_color',
				'label' => __( 'Tooltip Background', 'queryloop' ),
				'type' => 'color',
				'default' => 'inherit'
			),
			array(
				'id' => 'customcss',
				'label' => __( 'Custom CSS', 'queryloop' ),
				'type' => 'textarea',
				'default' => '',
				'class' => 'large-text code',
				'help' => __( 'Enter custom CSS to adjust styling details.', 'queryloop' ),
				'options' => '15'
			),
			array(
				'id' => 'customjs',
				'label' => __( 'Custom JavaScript', 'queryloop' ),
				'type' => 'textarea',
				'default' => '',
				'class' => 'large-text code',
				'options' => '15',
				'help' => __( 'Enter custom JavaScript to be run on footer.', 'queryloop' )
			)
		);
		
		foreach ($sections as $key => $fields) {
			foreach($fields as $field){
				add_settings_field(	$this->prefix . $field['id'], $field['label'], array(&$this, $field['type']),
					$this->prefix . 'options', $this->prefix . $key,
					array( 'field_id' => $field['id'],	'field_default' => $field['default'],
						   'field_class' => isset($field['class'])? $field['class'] : null,
						   'field_help' => isset($field['help'])? $field['help'] : null,
						   'field_ops' => isset($field['options'])? $field['options'] : null )
				);
			}
		}
	}

	/**
	 * When the plugin is activated, setup some options on the database
	 * @since 1.0.0
	 */
	function activate() {
		$defaults = array(
			'status_sel' => 'enabled',
			'usage_sel' => 'both',
			'delete_on_uninstall_chk' => null,

			'optionborder_int' => 2,
			'option_color' => 'inherit',
			'optionselected_color' => 'inherit',
			'imagesize_int' => 64,
			'iconsize_int' => 32,
			'icon_color' => 'inherit',
			'iconbg_color' => 'inherit',
			'colorsize_int' => 32,
			'textwidth_int' => 32,
			'textheight_int' => 0,
			'ttfront_color' => 'inherit',
			'ttbg_color' => 'inherit',

			'customcss' => '',
			'customjs' => '',
		);
		add_option( $this->prefix . 'settings', $defaults );
	}
	
	/**
	 * Validates options trying to be saved. Specific sentences are required for each value.
	 * @param array
	 * @return array
	 * @since 1.0.0
	 */
	function validate_options($input){
		$options = $this->settings;
		
		//Validate colors
		foreach ( $input as $key => $value ) {
			if ( strpos( $key,'_color' ) ) {
				$options[$key] = $value;
				if ( ! preg_match( '/#[0-9A-F]{3,6}_[0-9]\.[0-9]{2}/i', $options[$key] ) ) {
					$options[$key] = '';
				}
			}
			elseif ( strpos($key,'_int' ) ) {
				$options[$key] = $value;
				if ( ! preg_match( '/[0-9]$/i', $options[$key] ) ) {
					switch ( $key ) {
						case 'textheight_int':
							$options[$key] = 0;
							break;
					}
				}
			}
			elseif ( strpos($key,'_alpha' ) ) {
				$options[$key] = $value;
				if ( ! preg_match( '/[0|1]|[0-9]\.[0-9]{2}$/i', $options[$key] ) ) {
					switch ( $key ) {
						case 'numberposts_int':
							$options[$key] = 3;
							break;
					}
				}
			}
			elseif ( strpos( $key,'_sel' ) ) {
				$options[$key] = $input[$key];
			}
		}
		
		// Transfer checkboxes
		foreach ( $options as $key => $value ) {
			if( strpos($key, '_chk') ) {
				$options[$key] = $input[$key];
			}
		}
		$uninstallable_plugins = (array) get_option('uninstall_plugins');
		if ( isset( $input['delete_on_uninstall_chk'] ) ) {
			$options['delete_on_uninstall_chk'] = 'on';
			$uninstallable_plugins[plugin_basename($this->basefile)] = true;
		} else {
			unset( $uninstallable_plugins[plugin_basename($this->basefile)] );
		}
		update_option('uninstall_plugins', $uninstallable_plugins);

		// Sanitize custom CSS declarations
		$options['customcss'] = strip_tags( $input['customcss'] );
		$options['customjs'] = strip_tags( $input['customjs'] );

		return $options;
	}
	
	/**
	 * Callback for settings section
	 * @since 1.0.0
	 */
	function main_desc() {
		echo '<p>' . __( 'Set default settings for the plugin.', 'queryloop' ) . '</p>';
	}

	/**
	 * Callback for custom CSS section
	 * @since 1.0.0
	 */
	function customcss_desc() {
		echo '<p>' . __( 'Use this section to tweak the style.', 'queryloop' ) . '</p>';
	}

	/**
	 * Creates a checkbox control
	 * @param array
	 * @since 1.0.0
	 */
	function checkbox($args) {
		extract($args);
		$options = $this->settings;

		if ( isset ( $options[$field_id] ) ) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}
		echo "<label for='".$this->prefix."$field_id'><input $checked id='".$this->prefix."$field_id' name='".$this->prefix."settings[$field_id]' type='checkbox' />";
		if( isset($field_help) ) echo " $field_help";
		echo '</label>';
	}
	
	
	/**
	 * Creates a select element
	 * @param array
	 * @since 1.0.0
	 */
	function select($args) {
		extract($args);
		$options = $this->settings;
		$options[$field_id] = isset($options[$field_id])? $options[$field_id] : $field_default;
		$class = ( isset($field_class) )? "class='$field_class'" : "";
		echo "<select id='".$this->prefix."$field_id' $class name='".$this->prefix."settings[$field_id]'>";
		foreach($field_ops as $name => $value){
			if( isset($options[$field_id]) ){
				if( $value == $options[$field_id]) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
			} else {
				$selected = '';
			}
			echo "<option value='$value' $selected>" . $name . '</option>';
		}
		echo '</select>';
		if( isset($field_help) ){
			echo "<br/><span class='description'>$field_help</span>";
		}
	}

	/**
	 * Creates a select element with multiple attribute
	 * @param array
	 * @since 1.0.0
	 */
	function select_multiple($args) {
		extract($args);
		$options = $this->settings;
		$options[$field_id] = isset($options[$field_id])? $options[$field_id] : $field_default;
		$class = ( isset($field_class) )? "class='$field_class'" : "";
		echo "<select id='".$this->prefix."$field_id' $class name='".$this->prefix."settings[$field_id][]' multiple size='6'>";
		foreach($field_ops as $name => $value){
			if( isset($options[$field_id]) ){
				if( in_array($value, $options[$field_id]) ) {
					$selected = 'selected="selected"';
				} else {
					$selected = '';
				}
			} else {
				$selected = '';
			}
			echo "<option value='$value' $selected>" . $name . '</option>';
		}
		echo '</select>';
		if( isset($field_help) ){
			echo "<br/><span class='description'>$field_help</span>";
		}
	}
	
	/**
	 * Creates radio buttons
	 * @param array
	 * @since 1.0.0
	 */
	function radio( $args ) {
		extract( $args );
		$options = $this->settings;
		$options[$field_id] = isset( $options[$field_id] ) ? $options[$field_id] : $field_default;
		$class = isset( $field_class ) ? "class='$field_class'" : "";
		foreach( $field_ops as $name => $value ) {
			if( isset( $options[$field_id] ) ) {
				if( $value == $options[$field_id] ) {
					$checked = 'checked="checked"';
				} else {
					$checked = '';
				}
			} else {
				$checked = '';
			}
			echo "<label for='".$this->prefix."$field_id-$value'><input id='".$this->prefix."$field_id-$value' $class name='".$this->prefix."settings[$field_id]' type='radio' value='$value' $checked /> $name</label>";
			echo '<br/>';
		}
		if( isset( $field_help ) ) {
			echo "<span class='description'>$field_help</span>";
		}
	}

	/**
	 * Creates a color picker
	 * @param array
	 * @since 1.0.0
	 */
	function color( $args ) {
		extract( $args );
		$options = $this->settings;
		$options[$field_id] = isset( $options[$field_id] ) ? $options[$field_id] : $field_default;
		$colorpicker = $this->prefix.'color_' . $field_id;
		$class = isset($field_class) ? $field_class : 'inherit';
		if ( '' == $options[$field_id] ) {
			$options[$field_id] = ' ';
		}
		$color = $this->decode_color( $options[$field_id] );

		echo '<div class="ql-color-picker-wrap">';

		echo '<span class="pick-color">' . __( 'Pick Color', 'queryloop' ) . '</span>';
		echo '<input class="ql-color-picker" value="' . $color->hex . '" data-opacity="' . $color->a . '" type="text" />';
		echo "<input id='$colorpicker' name='".$this->prefix."settings[$field_id]' type='hidden' value='{$options[$field_id]}' class='wp-color-picker $class' />";


		echo '</div><!--/.ql-color-picker-wrap-->';
		if ( isset( $field_help ) ) {
			echo "<br/><span class='description'>$field_help</span>";
		}
	}

	/**
	 * From a color in JSON format, returns an array with 0 : hexadecimal color and 1 : opacity
	 *
	 * @since 1.0.0
	 *
	 * @param $val string Color in JSON format. Example {"color":"2b9e4b","opacity":"0.63"}
	 *
	 * @return mixed
	 */
	function decode_color( $val = '' ) {
		$color = explode( '_', $val );
		$hex_a = new stdClass();
		$hex_a->hex = isset( $color[0] ) ? esc_attr( $color[0] ) : '';
		$hex_a->a = isset( $color[1] ) ? esc_attr( $color[1] ) : '1';
		return $hex_a;
	}

	/**
	 * Creates a textarea
	 *
	 * @since 1.0.0
	 *
	 * @param array
	 */
	function textarea( $args ) {
		extract( $args );
		$options = $this->settings;
		$options[$field_id] = isset( $options[$field_id] ) ? $options[$field_id] : $field_default;
		$class = isset($field_class ) ? "class='$field_class'" : "";
		echo "<textarea id='".$this->prefix."$field_id' $class rows='$field_ops' name='".$this->prefix."settings[$field_id]'>{$options[$field_id]}</textarea>";
		if ( isset( $field_help ) ) {
			echo "<br/><span class='description'>$field_help</span>";
		}
	}

	/**
	 * Creates a number input field
	 * @param array
	 * @since 1.0.0
	 */
	function step( $args ) {
		extract( $args );
		$options = $this->settings;
		$options[$field_id] = isset( $options[$field_id] ) ? $options[$field_id] : $field_default;
		$class = isset($field_class) ? "class='$field_class'" : '';
		echo "<input id='".$this->prefix."$field_id' $class name='".$this->prefix."settings[$field_id]' type='number' value='{$options[$field_id]}' />";
		if ( isset( $field_help ) ) {
			echo "<br/><span class='description'>$field_help</span>";
		}
	}
	
	/**
	 * Creates a text input field
	 * @param array
	 * @since 1.0.0
	 */
	function text( $args ) {
		extract( $args );
		$options = $this->settings;
		$options[$field_id] = isset( $options[$field_id] ) ? $options[$field_id] : $field_default;
		$class = ( isset($field_class) )? "class='$field_class'" : "";
		echo "<input id='".$this->prefix."$field_id' $class name='".$this->prefix."settings[$field_id]' type='text' value='{$options[$field_id]}' />";
		if ( isset( $field_help ) ) {
			echo "<br/><span class='description'>$field_help</span>";
		}
	}
	
	/**
	 * Creates Settings link on plugins list page.
	 * @param array
	 * @param string
	 * @return array
	 * @since 1.0.0
	 */
	function settings_link( $links, $file ) {
		if ( $file == plugin_basename( $this->basefile ) ) {
			$links[] = "<a href='options-general.php?page=".$this->settings_page_id."'><b>" . __( 'Settings', 'queryloop' ) . "</b></a>";
			$links[] = sprintf( '<a href="%s" title="%s">%s</a>', 'http://queryloop.com/forums/visual-attributes', __( 'Access the support forums', 'queryloop' ), __( 'Support', 'queryloop' ) );
		}
		return $links;
	}
	
	/**
	 * Adds Settings link on plugins page. Create options page on wp-admin.
	 * @since 1.0.0
	 */
	function plugin_menu() {
		$this->plugin_data = get_plugin_data( $this->basefile );
		$page_title = $this->plugin_data['Name'];
		add_filter( 'plugin_action_links', array( $this, 'settings_link' ), -10, 2 );
		$op = add_options_page( $page_title, preg_replace( '/QueryLoop/i', 'QL', $page_title ), 'manage_options', $this->settings_page_id, array( $this, 'options_page' ) );
		add_action( 'load-' . $op, array( $this, 'help' ) );
	}

	/**
	 * Get plugin setting
	 * @param string $key Settings key.
	 * @param mixed $default Default value to be used if there's not a setting set.
	 * @return mixed If setting exists, returns it, otherwise the default value if it was passed, otherwise, false.
	 * @since 1.0.0
	 */
	function get( $key, $default = null ) {
		if ( isset( $this->settings[$key] ) ) {
			return $this->settings[$key];
		} elseif ( ! is_null( $default ) ) {
			return $default;
		} else {
			return false;
		}
	}
	
	/**
	 * Get and echo plugin setting
	 * @param string
	 * @since 1.0.0
	 */
	function gecho( $key ) {
		echo $this->get( $key );
	}

	/**
	 * Get and echo plugin setting
	 * @param array $args List that includes
	 * 		setting key to retrieve
	 * 		CSS selector to use
	 * 		CSS property to set
	 * @param bool $echo Echo or return
	 * @return string
	 * @since 1.0.0
	 */
	function get_style( $args, $echo = true ) {
		extract( $args = wp_parse_args( $args, array(
			'key' => '',
			'selector' => '',
			'prop' => 'color',
			'default' => 'inherit',
			'props' => false
		)), EXTR_SKIP );

		$out = '';
		$propout = '';
		if ( is_array( $props ) ) {
			foreach ( $props as $property => $prop_key ) {
				if ( is_array( $prop_key ) ) {
					$property_key = $prop_key['key'];
				} else {
					$property_key = $prop_key;
				}
				$prop_val = $this->get( $property_key );
				if ( $default !== $prop_val && '' !== $prop_val && false !== $prop_val ) {
					// Color properties.
					if ( 'color' == $property || ( false !== stripos( $property, '-color' ) ) ) {
						if ( isset( $prop_key['none'] ) ) {
							$propout .= "\t{$prop_key['none']}: none;\n";
						}
						$color = $this->decode_color( $prop_val );
						$propout .= "\t$property: $color->hex;\n";
						if ( '1.00' != $color->a ) {
							$propout .= "\t$property: rgba(" .$this->get_rgba( $color->hex, $color->a ).');'."\n";
						}
					// Non color properties.
					} else {
						$after = isset( $prop_key['after'] )? $prop_key['after'] : '';
						$propout .= "\t$property: $prop_val{$after};\n";
					}
				}
			}
		} else {
			$prop_val = $this->get( $key );
			if ( $default !== $prop_val && '' !== $prop_val && false !== $prop_val ) {
				$propout .= "$prop: $prop_val;";
			}
		}
		if ( '' != $propout ) {
			$out .= "$selector {\n";
				$out .= "\t$propout";
			$out .= "\n}\n";
		}
		if ( $echo )
			echo $out;

		return $out;
	}

	/**
	 * Convert color from hexadecimal to RGBA.
	 *
	 * @param $hex
	 * @param string $alpha
	 * @return string
	 */
	function get_rgba( $hex, $alpha = '' ) {
	   $hex = str_replace( '#', '', $hex );

	   if ( strlen( $hex ) == 3 ) {
	      $r = hexdec( substr( $hex, 0, 1).substr( $hex, 0, 1 ) );
	      $g = hexdec( substr( $hex, 1, 1).substr( $hex, 1, 1 ) );
	      $b = hexdec( substr( $hex, 2, 1).substr( $hex, 2, 1 ) );
	   } else {
	      $r = hexdec( substr( $hex, 0, 2 ) );
	      $g = hexdec( substr( $hex, 2, 2 ) );
	      $b = hexdec( substr( $hex, 4, 2 ) );
	   }
	   return $r . ', ' . $g . ', ' . $b . ', ' . $alpha;
	}

	/**
	 * When the plugin is deactivated, run this function.
	 * @since 1.0.0
	 */
	function deactivate() {
		// Uncomment the following line to clear the settings
		//delete_option( $this->prefix . 'settings' );
	}

	/**
	 * Enqueue scripts and styles needed
	 * @since 1.0.0
	 */
	function admin_enqueue_script_and_style( $hook ) {
		if ( 'settings_page_'.$this->prefix.'options' == $hook ) {
			if ( ! wp_script_is( 'ql-color-picker-js' ) ) {
				wp_enqueue_style( 'ql-color-picker', QL_VISUAL_ATTRIBUTES_URI . '/css/color-picker.css', array(), false, 'screen' );
				wp_enqueue_script( 'ql-color-picker-js', QL_VISUAL_ATTRIBUTES_URI . '/js/color-picker.js', array('jquery'), false );
			}
			wp_enqueue_style( $this->prefix.'settings', QL_VISUAL_ATTRIBUTES_URI . '/css/ql-admin-style.css' );
			if ( is_rtl() ) {
				wp_enqueue_style( $this->prefix . 'admin-rtl', QL_VISUAL_ATTRIBUTES_URI . '/css/ql-admin-rtl.css' );
			}
			wp_enqueue_script( $this->prefix.'settings', QL_VISUAL_ATTRIBUTES_URI . '/js/ql-admin-script.js' );
		}
	}

	/**
	 * Returns array with post types, including post and page
	 * @return array Public post types active in installation
	 * @since 1.0.0
	 */
	public function get_post_types() {
		$args = array(
			'public' => true,
			'_builtin' => false
		);
		$post_types = get_post_types( $args, 'objects' );
		$cpt_list = array();
		$cpt_list[__( 'Posts', 'queryloop' )] = 'post';
		$cpt_list[__( 'Pages', 'queryloop' )] = 'page';
		foreach ( $post_types as $post_type ) {
			$cpt_list[$post_type->labels->name] = $post_type->name;
		}
		return $cpt_list;
	}

	/**
	 * Returns an array with
	 * @return array
	 * @since 1.0.0
	 */
	public function get_binary_select() {
		return array(
			__('Yes', 'queryloop') => 'yes',
			__('No', 'queryloop') => 'no'
		);
	}

} // class end

/**
 * When the plugin is deactivated, run this function.
 * @since 1.0.0
 */
function ql_visual_attributes_uninstall_hook() {
	// if uninstall not called from WordPress exit
	if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
		exit ();
	}

	$ql_visual_attributes_settings = get_option( 'ql_visual_attributes_settings' );

	if ( isset( $ql_visual_attributes_settings['delete_on_uninstall_chk'] ) ) {
		delete_option( 'ql_visual_attributes_settings' );
	}
}
?>