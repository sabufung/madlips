<?php
class ASG_Source_Editor {
	public $name;
	public $slug;
	public $js_editor;
	
	function get_defaults(){
		return array();
	}
	
	function get_image_defaults(){}
	
	
	function render_editor_invisibles(){}
		
	function get_js_editor_path(){
		return "sources/{$this->slug}/editor";
	}
	
	function render_editor_tab($source){
		$path = ASG_PATH . "sources/{$this->slug}/template.php";
		require($path);
	}
	

}
