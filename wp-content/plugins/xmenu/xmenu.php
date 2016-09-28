<?php
/*
Plugin Name: XMenu - The Ultimate WordPress Mega Menu
Plugin URI: http://www.g5plus.net
Description: Easily create beautiful, flexible, responsive mega menus
Author: G5Theme
Author URI: http://www.g5plus.net
Version: 1.0.0.0
*/
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;
if ( !class_exists( 'XMenu' ) ) :
final class XMenu {
	private static $instance;

	public static function init() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new XMenu;
			self::$instance->define_constants();
			self::$instance->includes();
			self::$instance->process_filter();
		}

		return self::$instance;
	}

	private function define_constants(){
		// Plugin version
		if( ! defined( 'XMENU_VERSION' ) ) { define( 'XMENU_VERSION', '1.0.0.0' ); }
		// Plugin prefix
		if( ! defined( 'XMENU_PREFIX' ) ) { define( 'XMENU_PREFIX', 'xmenu_' ); }
		// Plugin Folder URL
		if( ! defined( 'XMENU_URL' ) ) {define( 'XMENU_URL', plugin_dir_url( __FILE__ ) );}
		//if( ! defined( 'XMENU_URL' ) ) {define( 'XMENU_URL', plugins_url() . '/xmenu/' );}
		// Plugin Folder Path
		if( ! defined( 'XMENU_DIR' ) ){define( 'XMENU_DIR', plugin_dir_path( __FILE__ ) );}
		if( ! defined( 'XMENU_BASENAME' ) ){ define( 'XMENU_BASENAME' , plugin_basename(__FILE__) );}
		if( ! defined( 'XMENU_BASEDIR' ) ){define( 'XMENU_BASEDIR' , dirname( plugin_basename(__FILE__) ) );}

		if( ! defined( 'XMENU_MENU_ITEM_WIDGET_AREAS' ) ){define( 'XMENU_MENU_ITEM_WIDGET_AREAS' , 'xmenu_menu_item_widget_areas' );}
		if( ! defined( 'XMENU_MENU_WIDGET_AREAS_ID' ) ){define( 'XMENU_MENU_WIDGET_AREAS_ID' , 'xmenu_menu_item_' );}
		if( ! defined( 'XMENU_SETTING_OPTIONS' ) ){define( 'XMENU_SETTING_OPTIONS' , 'xmenu_setting_option' );}

	}

	private function includes() {
		require_once XMENU_DIR . 'inc/global.php';
		require_once XMENU_DIR . 'inc/toolbar.php';
		require_once XMENU_DIR . 'inc/menu-item.php';
		require_once XMENU_DIR . 'inc/functions.php';
		require_once XMENU_DIR . 'inc/XMenuWalker.class.php';
		require_once XMENU_DIR . 'admin/admin.php';
	}

	private function process_filter() {
		add_filter('wp_nav_menu_args',array($this, 'replace_walker_to_xmenu'));
		add_filter('xmenu_custom_content', 'do_shortcode');
	}

	function replace_walker_to_xmenu($args) {
		$settings = get_option(XMENU_SETTING_OPTIONS);
		$is_xmenu = false;
		$menu_slug = '';

		if (isset($settings['integration-nav_menu']) && $args['menu']) {
			if (is_object($args['menu'])) {
				if (array_key_exists($args['menu']->slug, $settings['integration-nav_menu'])) {
					$menu_slug = $args['menu']->slug;
					$is_xmenu = true;
				}
			}
			else {
				$term = get_term($args['menu'], 'nav_menu');

				if (!$term) {
					$term = get_term_by('slug', $args['menu'], 'nav_menu');
				}
				if ($term) {
					if ((array_key_exists($term->slug, $settings['integration-nav_menu']))) {
						$menu_slug = $term->slug;
						$is_xmenu = true;
					}
				}
			}
		}


		if (isset($settings['integration-theme_location']) && !empty($args['theme_location']) && !$is_xmenu) {
			if (array_key_exists($args['theme_location'], $settings['integration-theme_location'])) {
				$theme_locations = get_nav_menu_locations();
				if (isset($theme_locations[$args['theme_location']])) {
					$menu_obj = get_term( $theme_locations[$args['theme_location']], 'nav_menu' );
					if ($menu_obj) {
						$menu_slug = $menu_obj->slug;
						$is_xmenu = true;
					}
				}
			}
		}
		if ($is_xmenu) {
			$settings = get_option(XMENU_SETTING_OPTIONS. '_' . $menu_slug);
			if ($settings === false) {
				$settings = get_option(XMENU_SETTING_OPTIONS);
			}
			ob_start();
			?>
				<div class="x-nav-menu-toggle">
					<?php echo apply_filters('xmenu_toggle_inner_before',''); ?>
					<div class="x-nav-menu-toggle-inner" data-ref="menu-<?php echo esc_attr($menu_slug) ?>">
						<?php echo apply_filters('xmenu_filter_mobile_toggle_icon', '<div class="x-nav-menu-toggle-icon"> <span></span></div>');?> <?php echo apply_filters('xmenu_filter_mobile_toggle_text', '<span>'. __('Menu','xmenu')) . '</span>';?>
					</div>
					<?php echo apply_filters('xmenu_toggle_inner_after',''); ?>
				</div>
			<?php
			$xmenu_toggle = ob_get_clean();

			$xmenu_attrs = array();
			$xmenu_attrs['data-breakpoint'] = isset($settings['setting-responsive-breakpoint']) && !empty($settings['setting-responsive-breakpoint']) ? $settings['setting-responsive-breakpoint'] : '991';

			$xmenu_attr_str = '';
			foreach ($xmenu_attrs as $key => $value) {
				$xmenu_attr_str .= $key . '="'. esc_attr($value) . '" ';
			}

			$args['walker'] = new XMenuWalker();
			$args['menu_id'] = 'menu-' . $menu_slug;
			$args['menu_class'] = 'x-nav-menu x-nav-menu-horizontal';
			$args['menu_class'] .= ' x-nav-menu' . (empty($menu_slug) ? '' : '_' . $menu_slug );
			$args['items_wrap'] =
				apply_filters('xmenu_filter_toggle', $xmenu_toggle)
				. apply_filters('xmenu_nav_filter_before', '')
				. '<ul id="%1$s" class="%2$s" ' . $xmenu_attr_str . '>' . apply_filters('xmenu_filter_before','') . '%3$s' . apply_filters('xmenu_filter_after','')
				. '</ul>'
				. apply_filters('xmenu_nav_filter_after', '');

			if ($settings !== false) {
				if (isset($settings['transition']) && $settings['transition'] != 'none') {
					$args['menu_class'] .= ' ' . $settings['transition'];
				}
				if (isset($settings['transition']) && !empty($settings['menu-bar-align'])) {
					$args['menu_class'] .= ' ' . $settings['menu-bar-align'];
				}
			}
		}
		return $args;
	}
}
endif;

if( !function_exists( '_XMENU' ) ){
	function _XMENU() {
		return XMenu::init();
	}
	_XMENU();
}