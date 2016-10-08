<?php
class ASG_RSS_Source_Editor extends ASG_Source_Editor {
	var $slug = 'rss';
	var $name = 'RSS';


	function __construct(){
		add_action('wp_ajax_asg-rss-check-url', array($this, '_ping'));
	}

	function _ping(){
		$feed = fetch_feed(stripslashes($_REQUEST['url']));
		if (is_wp_error($feed)){
			echo __('Error', 'asg') . ": ";
			echo $feed->get_error_message();
			exit;
		}
		$items = $feed->get_items(0, 5);
		if (!count($items)){
			_e('No RSS data found', 'asg');
			exit;
		}
		_e('RSS is OK', 'asg');
		exit;
	}

	function get_defaults(){
		return array(
			'url' => '',
			'link' => 'lightbox',
			'caption_1' => 'name',
			'caption_2' => 'rating',
			'lightbox_caption_1' => 'name',
			'lightbox_caption_2' => 'rating'
		);
	}

}

global $asg_source_editors;
$asg_source_editors['rss'] = new ASG_RSS_Source_Editor;
