<?php
/**
 * QueryLoop Plugin Term Meta
 *
 * @package      QueryLoop
 * @subpackage   Admin
 * @author       QueryLoop <hello@queryloop.com>
 * @copyright    Copyright (c) QueryLoop
 * @license      http://opensource.org/licenses/gpl-2.0.php GNU Public License
 * @since        1.0.0
*/

/**
 * Manages term meta for taxonomies
 *
 * @author	QueryLoop
 *
 * @since	1.0.0
 */
class QL_Visual_Attributes_Term_Meta {

	/**
	 * @var string Name of this Custom Post Type.
	 */
	var $post_type = '';

	/**
	 * @var string Name of table to store term meta.
	 */
	var $term_table = '';

	/**
	 * @var string Taxonomies where this term meta will be added.
	 */
	var $taxonomies = array();

	/**
	 * @var string List of controls to add. This is an associative array where the first level corresponds to a taxonomy key that encompasses the controls.
	 */
	var $meta_list = array();

	/**
	 * @var array List of metas for a term.
	 */
	var $term_meta = array();

	/**
	 * @var bool Whether to add icons or not.
	 */
	var $add_icons = false;

	/**
	 * Initialize class.
	 * 
	 * @since 1.0.0
	 * 
	 * @param array $args Associative list where each key is a taxonomy where term meta will be added and its value is an array defining the controls to add.
	 */
	function __construct( $post_type, $args = array(), $term_table = 'taxonomy_term' ) {
		$this->post_type = $post_type;
		$this->meta_list = $args;
		$this->term_table = $term_table;

		add_action( 'init', array( $this, 'term_meta_init' ) );
		add_action( 'switch_blog',  array( $this, 'term_meta_init' ), 0 );
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_footer', array( $this, 'admin_footer' ) );
	}

	/**
	 * List of controls to render. Uses list of controls if they were defined in class instantiation.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	function get_control_list( $tax = '' ) {
		if ( empty( $tax ) ) {
			$screen = get_current_screen();
			$tax = $screen->taxonomy;
		}
		$controls = isset( $this->meta_list[$tax] ) && is_array( $this->meta_list[$tax] ) ? $this->meta_list[$tax] : array();
		/**
		 * Filter the list of controls.
		 * 
		 * @since 1.0.0
		 * 
		 * @param array $controls A list of controls if it was defined on class instantiation or an empty array otherwise.
		 */
		return apply_filters( 'queryloop_' . $tax . '_term_meta_controls', $controls );
	}

	/**
	 * Load term meta table and initialize term meta environment.
	 *
	 * @since 1.0.0
	 */
	function term_meta_init() {
		global $wpdb;
		$term_meta_table = "{$this->term_table}meta";
		$wpdb->{$term_meta_table} = $wpdb->prefix . $term_meta_table;
		$wpdb->tables[] = $term_meta_table;
		$this->maybe_create_term_meta_table();
	}

	/**
	 * Start term meta admin routines
	 *
	 * @since 1.0.0
	 */
	function admin_init() {
		$this->taxonomies = array_keys( $this->meta_list );
		if ( $this->taxonomies ) {
			foreach ( $this->taxonomies as $tax ) {
				add_action( $tax . '_add_form_fields', array( $this, 'add_term_fields' ) );
				add_action( $tax . '_edit_form_fields', array( $this, 'edit_term_fields' ), 10,2 );
				add_action( 'created_' . $tax, array( $this, 'save_term_meta' ), 10, 2 );
				add_action( 'edit_' . $tax, array( $this, 'save_term_meta' ), 10, 2 );
			}
		}
	}

	/**
	 * Setup table for term meta if it doesn't exist
	 *
	 * @since 1.0.0
	 */
	function maybe_create_term_meta_table() {
		$option_name = "{$this->term_table}_exists";
		if ( ! get_option( $option_name ) ) {

			global $wpdb;
			$wpdb->hide_errors();

			$collate = '';
			if ( $wpdb->has_cap( 'collation' ) ) {
				if ( ! empty( $wpdb->charset ) ) {
					$collate = "DEFAULT CHARACTER SET $wpdb->charset";
				}
				if ( ! empty( $wpdb->collate ) ) {
					$collate = "COLLATE $wpdb->collate";
				}
			}

			require_once ABSPATH . 'wp-admin/includes/upgrade.php';

			dbDelta( "
				CREATE TABLE IF NOT EXISTS {$wpdb->prefix}{$this->term_table}meta (
				meta_id bigint(20) NOT NULL AUTO_INCREMENT,
				{$this->term_table}_id bigint(20) NOT NULL default 0,

				meta_key varchar(255) DEFAULT NULL,
				meta_value longtext DEFAULT NULL,

				UNIQUE KEY meta_id (meta_id)
			) {$collate};" );

			add_option( $option_name, true );
		}
	}

	/**
	 * Display custom taxonomy meta fields in Edit Taxonomy screen.
	 *
	 * @since 1.0.0
	 */
	function add_term_fields() {
		if ( isset( $_GET['taxonomy'] ) && in_array( $_GET['taxonomy'], $this->taxonomies ) ) {
			foreach ( $this->get_control_list() as $meta_key => $control ) {
				$this->render_control( array_merge(
					array(
						'meta_key' => $meta_key,
					),
					$control
				) );
			}
		}
	}

	/**
	 * Display custom taxonomy meta fields in Edit Taxonomy screen.
	 *
	 * @since 1.0.0
	 *
	 * @param object $term
	 * @param string $taxonomy
	 */
	function edit_term_fields( $term, $taxonomy ) {
		if ( isset( $taxonomy ) && in_array( $taxonomy, $this->taxonomies ) ) {
			foreach ( $this->get_control_list() as $meta_key => $control ) {
				$this->render_control( array_merge(
					array(
						'meta_key' => $meta_key,
						'meta_value' => $this->get_term_meta( $term->term_id, $meta_key ),
					),
					$control
				) );
			}
		}
	}

	/**
	 * Enqueues styles and scripts used in term add and edit screens.
	 *
	 * @since 1.0.0
	 */
	function admin_enqueue_scripts() {
		$screen = get_current_screen();
		if ( in_array( $screen->taxonomy, $this->taxonomies ) ) {
			wp_enqueue_media();
			$url_here = plugins_url( '' , __FILE__ );
			wp_enqueue_style( 'ql-term-meta-admin', $url_here . '/css/ql-term-meta-admin.css' );
			wp_enqueue_script( 'ql-term-meta-admin', $url_here . '/js/ql-term-meta-admin.js', array( 'jquery', 'underscore' ) );

			wp_register_style( 'ql-icon-picker', $url_here . '/css/icon-picker.css', array(), false, 'screen' );
			wp_register_style( 'ql-icon-set-genericon', plugins_url( '' , __FILE__ ) . '/icons/gi/genericons.css', array( 'ql-icon-picker' ), false, 'screen' );
			wp_register_script( 'ql-icon-picker', $url_here . '/js/icon-picker.js' );

			wp_register_style( 'ql-color-picker', $url_here . '/css/color-picker.css', array(), false, 'screen' );
			wp_register_script( 'ql-color-picker', $url_here . '/js/color-picker.js' );

			/**
			 * Fires after the scripts and styles have been enqueued and/or registered.
			 * 
			 * @since 1.0.0
			 * 
			 * @param object $screen Object with information about this screen. The most useful might be $screen->taxonomy to know where we are.
			 */
			do_action( 'queryloop_term_meta_admin_scripts', $screen );
		}
	}

	/**
	 * Renders a single control based on arguments passed. Works for add and edit term screens.
	 * 
	 * @since 1.0.0
	 * 
	 * @param array $args
	 * 
	 * @return string
	 */
	function render_control( $args = array() ) {
		$args = wp_parse_args( $args, array(
			'meta_key' 	  => '',
			'meta_value'  => '',
			'type' 		  => '',
			'label' 	  => '',
			'default' 	  => null,
			'options'  	  => array(),
			'icon_set'	  => 'genericon',
			'description' => '',
		) );
		$meta_key = $args['meta_key'];
		// if there's a value, use it, otherwise use default
		$term_value = empty( $args['meta_value'] ) ? $args['default'] : $args['meta_value'];
		$is_term_edit = $this->is_term_edit();
		$wrapping_tag = $is_term_edit ? 'tr' : 'div';
		?>
		<<?php echo $wrapping_tag; ?> class="form-field <?php echo "$this->post_type-tax-meta"; ?>">

			<?php if ( $is_term_edit ) : echo '<th scope="row" valign="top">'; endif; ?>
				<?php if ( 'info' != $args['type'] ) : ?>
					<label for="<?php echo esc_attr( $meta_key ); ?>"><?php echo wp_kses_data( $args['label'] ); ?></label>
				<?php endif; ?>
			<?php if ( $is_term_edit ) : echo '</th><td>'; endif;

			switch ( $args['type'] ) {
				case 'text':
				case 'phone':
				case 'email':
				case 'number':
				case 'url': ?>
					<input name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>" type="<?php echo esc_attr( $args['type'] ); ?>" value="<?php echo esc_attr( $term_value ); ?>" />
				<?php break;

				case 'textarea': ?>
					<textarea name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>" rows="5" cols="50" class="large-text"><?php echo esc_textarea( $term_value ); ?></textarea>
				<?php break;

				case 'select': ?>
					<select name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>">
						<?php foreach ( $args['options'] as $value => $option ) : ?>
							<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $term_value ); ?>><?php echo esc_html( $option ); ?></option>
						<?php endforeach; ?>
					</select>
				<?php break;

				case 'checkbox': ?>
					<label class="description"><input name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>" type="checkbox" value="1" <?php checked( $term_value ); ?> /><?php echo wp_kses_post( $args['description'] ); ?></label>
				<?php break;

				case 'radio': ?>
					<fieldset>
						<?php foreach ( $args['options'] as $value => $option ) : ?>
							<label class="description"><input type="radio" name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>" value="<?php echo esc_attr( $value ); ?>" <?php checked( $value, $term_value ); ?>><?php echo esc_html( $option ); ?></label><br />
						<?php endforeach; ?>
					</fieldset>
				<?php break;

				case 'image': ?>
					<div class="image-select-wrap">
						<div class="image-preview"></div>
						<a class="button open-media" data-uploader-title="<?php echo esc_attr( __( 'Select Image', 'queryloop' ) ) ?>" data-uploader-button="<?php echo esc_attr( __( 'Select', 'queryloop' ) ) ?>"><?php echo isset( $args['button_label'] ) ? $args['button_label'] : $args['label']; ?></a>
						<input name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>" type="hidden" value="<?php echo esc_attr( $term_value ); ?>" />
					</div>
					<!-- /.image-select-wrap -->
				<?php break;

				case 'icon':
					$this->add_icons = true;
					if ( 'genericon' == $args['icon_set'] ) {
						wp_enqueue_style( 'ql-icon-set-genericon' );
					}
					?>
					<div class="icon-select-wrap">
						<div class="icon-preview"><i class="<?php echo esc_attr( $term_value ); ?> "></i></div>
						<a class="open-icons button" href="#" data-icon-set="<?php echo esc_attr( $args['icon_set'] ); ?>"><?php echo esc_html( isset( $args['button_label'] ) ? $args['button_label'] : $args['label'] ); ?></a>
						<input name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>" type="hidden" value="<?php echo esc_attr( $term_value ); ?>" />
					</div>
					<!-- /.icon-select-wrap -->
				<?php break;

				case 'color':
					if ( ! wp_style_is( 'ql-color-picker' ) ) {
						wp_enqueue_style( 'ql-color-picker' );
						wp_enqueue_script( 'ql-color-picker' );
					}
					$color = $this->decode_color( $term_value );
					?>
					<div class="color-select-wrap">
						<span class="pick-color"><?php echo esc_html( isset( $args['button_label'] ) ? $args['button_label'] : $args['label'] ); ?></span>
						<input class="qltm-color-picker" value="<?php echo $color->hex; ?>" data-opacity="<?php echo $color->a; ?>" type="text" />
						<input name="<?php echo esc_attr( $meta_key ); ?>" id="<?php echo esc_attr( $meta_key ); ?>" type="hidden" value="<?php echo esc_attr( $term_value ); ?>" />
					</div>
					<!-- /.color-select-wrap -->
				<?php break;

				case 'info':
					echo "<{$args['label_tag']}>" . wp_kses_data( $args['label'] ) . "</{$args['label_tag']}>";
					echo wp_kses_post( $args['description'] );
				break;

				default:
					/**
					 * Fires if the control type is not recognized allowing to add custom controls.
					 * Hook to this action using your own type as suffix for the action.
					 * 
					 * @since 1.0.0
					 * 
					 * @param array $args Array of settings passed to initialize the control.
					 * @param string $meta_key ID of term meta.
					 * @param mixed $term_value Current value of term meta.
					 */
					do_action( 'queryloop_term_meta_control_' . $args['type'], $args, $meta_key, $term_value );
			} ?>

			<?php if ( ! empty( $args['description'] ) && ! in_array( $args['type'], array('checkbox', 'info' ) ) ) : ?>
				<p class="description"><?php echo wp_kses_post( $args['description'] ); ?></p>
			<?php endif; ?>

			<?php if ( $is_term_edit ) : echo '</td>'; endif; ?>

		</<?php echo $wrapping_tag; ?>>
		<?php
	}

	/**
	 * From a color in JSON format, returns an array with 0 : hexadecimal color and 1 : opacity
	 *
	 * @since 1.0.0
	 *
	 * @param $val string Color in JSON format. Example {"hex":"2b9e4b","opacity":"0.63"}
	 *
	 * @return mixed
	 */
	function decode_color( $val = '' ) {
		$color = json_decode( $val );
		$hex_a = new stdClass();
		$hex_a->hex = isset( $color->hex ) ? esc_attr( $color->hex ) : '';
		$hex_a->a = isset( $color->opacity) ? esc_attr( $color->opacity ) : '1';
		return $hex_a;
	}

	/**
	 * Add icons if they're necessary.
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	function admin_footer() {
		if ( $this->add_icons ) {
			wp_enqueue_style( 'ql-icon-picker' );
			wp_enqueue_script( 'ql-icon-picker' );
			require_once plugin_dir_path( __FILE__ ) . 'icons/icons.php';
		}
	}

	/**
	 * Checks if it's the term edit screen.
	 * 
	 * @since 1.0.0
	 * 
	 * @return bool
	 */
	function is_term_edit() {
		return isset( $_GET['tag_ID'] );
	}

	/**
	 * Save custom taxonomy meta fields.
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 * @param $tt_id
	 * @param $taxonomy
	 */
	function save_term_meta( $term_id, $tt_id ) {
		if ( isset( $_POST['taxonomy'] ) && isset( $this->meta_list[$_POST['taxonomy']] ) ) {
			foreach ( $this->meta_list[$_POST['taxonomy']] as $meta_key => $control ) {
				if ( isset( $_POST[$meta_key] ) ) {
					$this->update_term_meta( $term_id, $meta_key, $_POST[$meta_key] );
				} else {
					$this->update_term_meta( $term_id, $meta_key, '' );
				}
			}
		}
	}

	/**
	 * Updates meta data array in specified key in term
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 * @param string $meta_key
	 * @param $meta_value
	 * @param string $prev_value
	 *
	 * @return bool
	 */
	function update_term_meta( $term_id, $meta_key, $meta_value, $prev_value = '' ) {
		return update_metadata( $this->term_table, $term_id, $meta_key, $meta_value, $prev_value );
	}

	/**
	 * Adds meta data array in specified key in term
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 * @param string $meta_key
	 * @param $meta_value
	 * @param bool $unique
	 *
	 * @return bool
	 */
	function add_term_meta( $term_id, $meta_key, $meta_value, $unique = false ){
		return add_metadata( $this->term_table, $term_id, $meta_key, $meta_value, $unique );
	}

	/**
	 * Deletes meta data array in specified key in term
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 * @param string $meta_key
	 * @param string $meta_value
	 * @param bool $delete_all
	 *
	 * @return bool
	 */
	function delete_term_meta( $term_id, $meta_key, $meta_value = '', $delete_all = false ) {
		return delete_metadata( $this->term_table, $term_id, $meta_key, $meta_value, $delete_all );
	}

	/**
	 * Returns meta data in term by an specified key
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 * @param string $meta_key
	 * @param bool $single
	 *
	 * @return array|string
	 */
	function get_term_meta( $term_id, $meta_key, $single = true ) {
		if ( $term_meta = get_metadata( $this->term_table, $term_id, $meta_key, $single ) ) {
			return $term_meta;
		} elseif ( isset( $default ) ) {
			return $default;
		}
		return '';
	}

	/**
	 * Returns the URL of an image.
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 * @param string $meta_key
	 * @param string $return Whether to return the entire json string, the markup, or, by default, the image URL.
	 * @param string $size
	 *
	 * @return string
	 */
	function get_term_image_url( $term_id, $meta_key, $return = 'url', $size = 'medium' ) {
		if ( $meta_value = $this->get_term_meta( $term_id, $meta_key ) ) {
			if ( 'json' == $return ) {
				return $meta_value;
			} else {
				$image = json_decode( $meta_value );
				if ( 'markup' == $return ) {
					return wp_get_attachment_image( $image->id, $size );
				} else {
					 $img = wp_get_attachment_image_src( $image->id, $size );
					return $img[0];
				}
			}
		}
		return '';
	}

	/**
	 * If mode is 'markup', returns an icon in a <i> tag. If it's 'class', returns only the CSS classes.
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 * @param string $meta_key
	 * @param string $return Whether to return the markup or only the classes
	 *
	 * @return string
	 */
	function get_term_icon( $term_id, $meta_key, $return = 'class' ) {
		if ( $meta_value = $this->get_term_meta( $term_id, $meta_key ) ) {
			if ( ! wp_style_is( 'ql-icon-set-genericon' ) ) {
				wp_enqueue_style( 'ql-icon-set-genericon', plugins_url( '' , __FILE__ ) . '/icons/gi/genericons.css', array(), false, 'screen' );
			}
			return 'markup' == $return ? '<i class="' . esc_attr( $meta_value ) . '"></i>' : $meta_value;
		}
		return '';
	}

	/**
	 * Returns an array of hex color and opacity.
	 *
	 * @since 1.0.0
	 *
	 * @param int $term_id
	 * @param string $meta_key
	 * @param string $return If 'background-color' or 'color' Whether to return object or color and opacity as CSS properties.
	 *
	 * @return string|object
	 */
	function get_term_color( $term_id, $meta_key, $return = 'object' ) {
		if ( $meta_value = $this->get_term_meta( $term_id, $meta_key ) ) {
			$hex_a = $this->decode_color( $meta_value );
			switch ( $return ) {
				case 'object':
					return $hex_a;
					break;
				case 'color':
				case 'background-color':
				case 'background':
					$out = "$return: $hex_a->hex;";
					if ( '1' != $hex_a->a ) {
						$out .= " opacity: $hex_a->a;";
					}
					return $out;
					break;
			}
		}
		return '';
	}

} // class end