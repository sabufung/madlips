<?php /*
Plugin Name: Awesome Gallery
Plugin URI: http://codecanyon.net
Description: Yet another gallery plugin, but awesome.
Author: Nikolay Karev
Version: 1.5.19
*/

require_once(ABSPATH . "/wp-admin/includes/plugin.php");
load_plugin_textdomain('asg', false, dirname(plugin_basename(__FILE__)) . '/languages/');
$asg_plugin_data = get_plugin_data(__FILE__);
define('ASG_VERSION', $asg_plugin_data['Version']);

define('ASG_REQUIRED_WP', '3.7');
define('ASG_MAIN', __FILE__);
define('ASG_PATH', dirname(__FILE__) . "/");
define('ASG_URL', trailingslashit(plugins_url(basename(dirname(__FILE__)))));
define('ASG_TIMTHUMB_URL', ASG_URL . "resize.php");
//define('ASG_NO_CACHE', true);
//define('ASG_NO_BACKUP', true);
require(ASG_PATH . "constants.php");
require(ASG_PATH . 'functions.php');
require(ASG_PATH . 'admin/support.class.php');
require(ASG_PATH . 'admin/environment.class.php');
global $asg_envrironment;
global $asg_source_editors;
global $asg_sources;

$asg_environment = new ASG_Environment();
$asg_source_editors = array();
$asg_sources = array();

if (!defined('ASG_MAX_IMAGES'))
	define('ASG_MAX_IMAGES', 500);

if ($asg_environment->load_requirements_met()){
	require(ASG_PATH . 'load.php');
	if (is_admin()){
		new Karevn_Updater_2_0(
				'http://api.karevn.com/plugins',
				'awesome-gallery',
				plugin_basename(ASG_MAIN));
	}

}
