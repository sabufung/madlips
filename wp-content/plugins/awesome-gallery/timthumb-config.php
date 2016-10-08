<?php
require(dirname(__FILE__) . "/constants.php");
$config_path = dirname(dirname(dirname(__FILE__))) .  "/awesome-gallery-timthumb-config.php";
if (file_exists($config_path)) require( $config_path );

if (!defined('WP_CONTENT_DIR')) define('WP_CONTENT_DIR', 'wp-content');
if (!defined('ASG_CACHE_DIR'))
	define('ASG_CACHE_DIR', 'awesome-gallery-cache');
if (!defined('UPLOADS_DIRECTORY'))
	define('UPLOADS_DIRECTORY',
			dirname(dirname(dirname(dirname(__FILE__)))) . "/" . WP_CONTENT_DIR . '/uploads');

/* Crazy hack to force HTTP image fetching. This fixes issues with sites hosted in mapped directories */
if (!defined('NO_HOST_HACK'))
	$_SERVER['HTTP_HOST'] = 'foo.bar';
/*if (isset($_SERVER['HTTP_REFERER'])){
	$url = parse_url($_SERVER['HTTP_REFERER']);
	if (strtolower($url['host']) == strtolower($_SERVER['SERVER_NAME'])){
		define('ALLOW_ALL_EXTERNAL_SITES', true);
	}
}*/
if (!defined('ALLOW_ALL_EXTERNAL_SITES'))
	define('ALLOW_ALL_EXTERNAL_SITES', true);

if (!defined('MAX_FILE_SIZE'))
	define ('MAX_FILE_SIZE', 10485760);

if (!defined('MEMORY_LIMIT')) define ('MEMORY_LIMIT', '128M');


if (!defined('LOCAL_FILE_BASE_DIRECTORY') && !@$_SERVER['DOCUMENT_ROOT']){
	$local_base = dirname(dirname(dirname(dirname(__FILE__))));
	$src = stripslashes($_REQUEST['src']);
	$url = parse_url($src);

	$base = $local_base;
	$path = array_filter(explode('/', $url['path']));
	$segments = '';
	$trim = false;
	foreach($path as $segment){
		if (strlen($segment)){
			$segments = $segments . "/" . $segment;
			if (strpos($base, $segments) === false){
				if ($trim)
					$local_base = substr($local_base, 0, strpos($base, dirname($segments)));
				break;
			} else {
				$trim = true;
			}
		}
	}
	define('LOCAL_FILE_BASE_DIRECTORY', $local_base);
}
if (!defined('FILE_CACHE_DIRECTORY')){
	$cache_directory = dirname(dirname(dirname(__FILE__))) . "/" . ASG_CACHE_DIR;
	if (!(file_exists($cache_directory) && is_writable($cache_directory) ||
		!file_exists($cache_directory) && is_writable(dirname($cache_directory)))){
		$cache_directory =  UPLOADS_DIRECTORY. "/" . ASG_CACHE_DIR;
	}
	define('FILE_CACHE_DIRECTORY', $cache_directory);
}

if (!defined('DEFAULT_S'))
	define('DEFAULT_S', 1);

if (isset($_REQUEST['zoom'])){
	$zoom = (float)$_REQUEST['zoom'];
	if (isset($_GET['w']))
		$_GET['w'] = $_GET['w'] * $zoom;
	if (isset($_GET['h']))
		$_GET['h'] = $_GET['h'] * $zoom;
}

