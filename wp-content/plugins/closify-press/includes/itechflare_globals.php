<?php

//Defined Textdomain
define('ITECHTEXTDOMAIN', wp_get_theme()->get( 'TextDomain' ));
define('ITECHTHEMENAME', wp_get_theme()->get( 'Name' ));

// metaboxes directory constant
define( 'CUSTOM_METABOXES_DIR', get_template_directory_uri() . '/admin/metaboxes' );

global $itech_globals;

$itech_globals = array();

$itech_globals['errors'] = array();
$itech_globals['notifications'] = array();
$itech_globals['version'] = '1.3';
$itech_globals['domain'] = 'itech_closify';
$itech_globals['wpurl'] = get_bloginfo('wpurl').'/';
$itech_globals['admin_url'] = $itech_globals['wpurl'].'wp-admin/';
$itech_globals['nonce_action'] = 'itech_closify_submission';
$itech_globals['option_name'] = 'itech_plugin_data';
$itech_globals['destination_folder'] = 'uploads';
$itech_globals['max_file_size'] = 1024 * 1024 * 10;

?>
