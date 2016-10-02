<?php
/**
 * Plugin Name: QueryLoop Visual Attributes
 * Plugin URI: http://demo.queryloop.com/visual-attributes/
 * Description: Display product attributes visually, as images, icons or colors.
 * Author: Elio Rivero
 * Author URI: http://queryloop.com/
 * Version: 1.1.7
 * Domain Path: /languages
 * Text Domain: queryloop
 *
 * See the changelog in the documentation.
 */

/**
 * Prepares the execution of the different components
 * @author	QueryLoop
 * @since	1.0.0
 */
class QL_Visual_Attributes_Main {

	function __construct() {

		// Load localization file
		add_action( 'plugins_loaded', array( $this, 'localization' ) );

		// Check if WooCommerce is active
		if ( $this->is_active( 'woocommerce/woocommerce.php' ) ) {

			define( 'QL_VISUAL_ATTRIBUTES_URI', plugins_url( '' , __FILE__ ) );
			define( 'QL_VISUAL_ATTRIBUTES_DIR', plugin_dir_path( __FILE__ ) );

			$basefile = QL_VISUAL_ATTRIBUTES_DIR . basename(__FILE__);

			$name = 'ql_visual_attributes';

			// Create Settings Page
			require_once 'ql-admin.php';
			$GLOBALS[$name.'_admin'] = new QL_Visual_Attributes_Admin( array(
				'basefile' 	=> $basefile,
				'prefix' 	=> $name
			));

			add_action( 'woocommerce_after_register_taxonomy', array( $this, 'init_attribute_term_meta' ) );

			// Create Functionality
			require_once 'ql-main.php';
			$GLOBALS[$name] = new QL_Visual_Attributes( array(
				'basefile'	=> $basefile,
				'prefix' 	=> $name,
				'admin'		=> $GLOBALS[$name.'_admin']
			));

			// Create Widget
			//require_once 'ql-widgets.php';
			//add_action( 'widgets_init', array( $this, 'register_widgets' ), 11 );

			// Load custom functions
			if ( is_file( QL_VISUAL_ATTRIBUTES_DIR . 'user-functions.php' ) ) {
				require_once QL_VISUAL_ATTRIBUTES_DIR . 'user-functions.php';
			}

		} else {
			add_action( 'admin_notices', array( $this, 'disabled_notice' ) );
		}

	}

	/**
	 * Initialize routines to add meta data to attribute terms.
	 *
	 * @since 1.0.9
	 */
	function init_attribute_term_meta() {
		global $wc_product_attributes;
		if ( isset( $_REQUEST['taxonomy'] ) && in_array( $_REQUEST['taxonomy'], array_keys( $wc_product_attributes ) ) ) {
			// Initialize term meta and meta boxes in taxonomies
			$meta_list = array(
				$_REQUEST['taxonomy'] => array(
					'info' => array(
						'type' => 'info',
						'label' => __( 'Visual Attributes', 'queryloop' ),
						'description' => __( 'Set presets for Visual Attributes so when you create a new product and add this attribute, the options you setup here will be pre-filled in.', 'queryloop' ),
						'label_tag' => 'h3',
					),
					'type' => array(
						'type' => 'radio',
						'label' => __( 'Type', 'queryloop' ),
						'options' => array(
							'image' => __( 'Image', 'queryloop' ),
							'icon'  => __( 'Icon', 'queryloop' ),
							'color' => __( 'Color', 'queryloop' ),
							'text'  => __( 'Text', 'queryloop' ),
						),
						'default' => 'image',
					),
					'image' => array(
						'type' => 'image',
						'label' => __( 'Image', 'queryloop' ),
						'button_label' => __( 'Select Image', 'queryloop' ),
					),
					'icon' => array(
						'type' => 'icon',
						'label' => __( 'Icon', 'queryloop' ),
						'icon_set' => 'genericon',
						'button_label' => __( 'Select Icon', 'queryloop' ),
					),
					'icon_color' => array(
						'type' => 'color',
						'description' => __( 'Icon Color', 'queryloop' ),
						'button_label' => __( 'Select Color', 'queryloop' ),
					),
					'icon_color_bg' => array(
						'type' => 'color',
						'description' => __( 'Icon Background Color', 'queryloop' ),
						'button_label' => __( 'Select Color', 'queryloop' ),
					),
					'color' => array(
						'type' => 'color',
						'label' => __( 'Color', 'queryloop' ),
					),
					'text' => array(
						'type' => 'text',
						'label' => __( 'Text', 'queryloop' ),
					),
					'text_color' => array(
						'type' => 'color',
						'description' => __( 'Text Color', 'queryloop' ),
						'button_label' => __( 'Select Color', 'queryloop' ),
					),
					'text_color_bg' => array(
						'type' => 'color',
						'description' => __( 'Text Background Color', 'queryloop' ),
						'button_label' => __( 'Select Color', 'queryloop' ),
					),
				),
			);
		} else {
			$meta_list = array();
		}
		require_once 'class-ql-term-meta.php';
		$GLOBALS['ql_visual_attributes_term_meta'] = new QL_Visual_Attributes_Term_Meta( 'product', $meta_list );
	}

	/**
	 * Initialize localization routines
	 *
	 * @since 1.0.0
	 */
	function localization() {
		load_plugin_textdomain( 'queryloop', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}

	/**
	 * Add a notice in the admin prompting user to install and enable WooCommerce.
	 *
	 * @since 1.0.0
	 */
	function disabled_notice() {
		echo '<div class="update-nag">';
		_e( 'You must have <strong>WooCommerce installed and activated</strong> for Visual Attributes to work.', 'queryloop' );
		echo '</div>';
	}

	/**
	 * Checks if the required plugin is active in network or single site.
	 *
	 * @param $plugin
	 *
	 * @return bool
	 */
	function is_active( $plugin ) {
		$network_active = false;
		if ( is_multisite() ) {
			$plugins = get_site_option( 'active_sitewide_plugins' );
			if ( isset( $plugins[$plugin] ) ) {
				$network_active = true;
			}
		}
		return in_array( $plugin, get_option( 'active_plugins' ) ) || $network_active;
	}

	/**
	 * Register plugin widget.
	 *
	 * @since 1.1.0
	 */
	function register_widgets() {
		register_widget( 'QL_Visual_Attributes_Browse' );
	}
}

// Initialize plugin
new QL_Visual_Attributes_Main();