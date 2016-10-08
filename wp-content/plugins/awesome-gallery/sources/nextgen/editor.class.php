<?php
class ASG_NEXTGEN_Source_Editor extends ASG_Source_Editor {
	var $slug = 'nextgen';
	var $name = 'NEXTGEN';
	
	function __construct(){
		add_action('wp_ajax_asg-nextgen-get-galleries', array($this, '_get_galleries'));
	}
	
	function _get_galleries(){
		global $wp_db;
		header('Content-type: text/json');
		$source = new ASG_NEXTGEN_Source(stripslashes_deep($_REQUEST));
		$data = $source->get_galleries();
		echo json_encode(array('success' => true, 'data' => $data));
		exit;
	}
	
	function get_defaults(){
		return array(
			'gallery' => null,
			'gallery_name' => '',
			'order_by' => 'sortorder',
			'order' => 'ASC',
			'caption_1' => 'filename',
			'caption_2' => 'excerpt',
			'link' => 'lightbox',
			'lightbox_caption_1' => 'filename',
			'lightbox_caption_2' => 'description'
		);
	}
}


global $asg_source_editors;
$asg_source_editors['nextgen'] = new ASG_NEXTGEN_Source_Editor;