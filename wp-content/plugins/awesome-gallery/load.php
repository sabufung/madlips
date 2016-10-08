<?php
require(ASG_PATH . "vendor/OAuth.php");
require(ASG_PATH . "visual-element.class.php");
require(ASG_PATH . "image.class.php");
require(ASG_PATH . 'integration.php');
require(ASG_PATH . 'source.class.php');
require(ASG_PATH . 'gallery.class.php');
require(ASG_PATH . 'sources/load.php');
require(ASG_PATH . 'admin/source-editor.class.php');
require(ASG_PATH . "sources/load-editors.php");
require(ASG_PATH . 'shortcodes.php');
require(ASG_PATH . 'widgets.php');
require(ASG_PATH . 'jetpack-hacks.class.php');
if (is_admin()){
	require('admin/load.php');
} else {
}
