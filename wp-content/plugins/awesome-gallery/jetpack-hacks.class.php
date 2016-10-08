<?php
class ASG_Jetpack_Hacks{
	function __construct(){
		add_action('wp_ajax_get_attachment_comments', array($this, '_get_attachment_comments'), 1);
		add_action('wp_ajax_nopriv_get_attachment_comments', array($this, '_get_attachment_comments'), 1);
		add_filter('jetpack_photon_add_query_string_to_domain', array($this, '_jetpack_photon_add_query_string_to_domain'), 10, 3);
	}

	function _jetpack_photon_add_query_string_to_domain($result, $host){
		if (preg_match('/^fbcdn\-.*akamaihd\.net$/', $host)){
			return true;
		}
		return $result;
	}
	function _get_attachment_comments(){
		if ($_REQUEST['attachment_id'] == 'asg-hack'){
			header('Content-type: text/json');
			echo json_encode(array());
			exit;
		}

	}
}

new ASG_Jetpack_Hacks;
