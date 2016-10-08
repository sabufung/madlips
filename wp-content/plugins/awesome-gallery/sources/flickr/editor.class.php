<?php

class ASG_Flickr_Source_Editor extends ASG_Source_Editor {
	var $slug = 'flickr';
	var $name = 'Flickr';

	function __construct(){
		add_action('wp_ajax_asg-flickr-ping', array($this, '_ping'));
		add_action('wp_ajax_asg-flickr-ping-user', array($this, '_ping_user'));
		add_action('wp_ajax_asg-flickr-get-photosets', array($this, '_get_photosets'));
		add_action('wp_ajax_asg-flickr-get-groups', array($this, '_get_groups'));
	}

	function _get_photosets(){
		header('Content-type: text/json');
		$source = new ASG_Flickr_Source(stripslashes_deep($_REQUEST['data']));
		$data = $source->get_photosets();
		echo json_encode(array('success' => true, 'data' => $data));
		exit;
	}

	function _get_groups(){
		header('Content-type: text/json');
		$source = new ASG_Flickr_Source(stripslashes_deep($_REQUEST));
		$data = $source->get_groups();
		echo json_encode(array('success' => true, 'data' => $data));
		exit;

	}

	function _ping(){
		$source = new ASG_Flickr_Source(stripslashes_deep($_REQUEST));
		$response = $source->ping();
		if (is_wp_error($response)){
			echo __('Error', 'asg') . ": " . $response->get_error_message();
		} else {
			_e('Keys are OK', 'asg');
		}
		exit;
	}

	function _ping_user(){
		$source = new ASG_Flickr_Source(stripslashes_deep($_REQUEST));
		$response = $source->ping_user();
		if (is_wp_error($response)){
			echo __('Error', 'asg') . ": " . $response->get_error_message();
		} else {
			_e('User is OK', 'asg');
		}
		exit;
	}

	function get_defaults(){
		return array(
			'key' => '',
			'secret' =>'',
			'username' => '',
			'source_type' => 'photostream',
			'source' => '',
			'source_name' => '',
			'link' => 'lightbox',
			'caption_1' => 'title',
			'caption_2' => 'description',
			'lightbox_caption_1' => 'title',
			'lightbox_caption_2' => 'description',
			'image_size' => null
		);
	}


}

global $asg_source_editors;
$asg_source_editors['flickr']= new ASG_Flickr_Source_Editor;
