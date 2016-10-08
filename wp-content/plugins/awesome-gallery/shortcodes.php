<?php

class ASG_Shortcodes{
	private $attr_stack;
	function __construct(){
		$this->attr_stack = array();
		add_shortcode('awesome-gallery',  array($this, '_shortcode'));
		add_shortcode('awesome_gallery', array($this, '_shortcode'));
		add_shortcode('awesomegallery', array($this, '_shortcode'));
		add_shortcode('awesomeGallery', array($this, '_shortcode'));
		if (get_option('asg_shortcode_hack')){
			add_filter('the_content', array($this, '_shortcode_hack'), 1000);
		}
		add_action('init', array($this, 'register_visual_composer'));


	}

	function register_visual_composer(){
		if (!function_exists('vc_map'))
			return;
		$galleries = get_posts(array('post_type' => ASG_POST_TYPE, 'posts_per_page' => -1));
		$params = array();
		foreach($galleries as $gallery){
			$params[$gallery->post_title] = $gallery->ID;
		}
		vc_map( array(
				"name" => __("Awesome Gallery", 'asg'),
				"base" => "awesome_gallery",
				"class" => "",
				"category" => __('Content', 'asg'),
				//'admin_enqueue_js' => array(get_template_directory_uri().'/vc_extend/bartag.js'),
				//'admin_enqueue_css' => array(get_template_directory_uri().'/vc_extend/bartag.css'),
				"params" => array(
						array(
								"type" => "dropdown",
								"holder" => "div",
								"class" => "",
								"heading" => __("Text"),
								"param_name" => "id",
								"value" => $params,
								"description" => __("Please enter gallery ID here.", 'asg')
						)
				)
		) );
	}


	function _shortcode($attr = array(), $content = null){
		if (!isset($attr['id']) || !$attr['id'])
			return '<div class="asg-load-error">' . __("Please add an \"id\" attribute to the Awesome Gallery shortcode", 'asg') . '</div>';
		$id = $attr['id'];
		if (get_option('asg_shortcode_hack')){
			array_push($this->attr_stack, $attr);
			return "<!--asg-$id-->";
		}
		return $this->render_gallery($id, $attr);
	}

	function _shortcode_hack($content){
		return preg_replace_callback('/<\!\-\-asg-(\d+)\-\->/', array($this, '_replace_callback'), $content);
	}

	function _replace_callback($matches){
		$attr = array_pop($this->attr_stack);

		return $this->render_gallery($matches[1], $attr);
	}

	function render_gallery($id, $attributes = array()){
		if (!$id)
			return;
		ob_start();
		awesome_gallery($id, $attributes);
		return ob_get_clean();
	}
}
global $asg_shortcodes;
$asg_shortcodes = new ASG_Shortcodes();
