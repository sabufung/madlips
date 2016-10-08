<?php
class ASG_GalleryList {
	function __construct(){
		add_action('init', array($this, '_init'));
	}

	function _init(){
		if (is_admin()){
			wp_enqueue_style('asg-admin', ASG_URL . "assets/admin/css/admin.css");
		}
	}
}

new ASG_GalleryList;