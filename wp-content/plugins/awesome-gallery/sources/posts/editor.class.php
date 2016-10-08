<?php
class ASG_Posts_Source_Editor extends ASG_Source_Editor {
	var $slug = 'posts';
	var $name = 'Posts';

	function __construct(){

	}

	function render_editor_invisibles(){
		echo "<div style='display: none'>";
		echo '<div id="posts-taxonomy-filter-template">';
		$filter = array(
			'taxonomy' => '',
			'tags' => ''
		);
		require('filter-taxonomy.php');
		echo "</div>";
		echo '<div id="posts-custom-field-filter-template">';
		$filter = array(
			'meta_key' => '',
			'meta_value' => '',
			'meta_operator' => '',
			'meta_type' => '',
		);
		require('filter-custom-field.php');
		echo "</div></div>";
	}

	function get_defaults(){
		return array(
			'post_type' => 'post',
			'orderby' => 'date',
			'order' => 'DESC',
			'source' => 'featured',
			'tags_taxonomy' => 'post_tags',
			'ids' => '',
			'include_exclude' => 'include',
			'taxonomies' => array(),
			'meta_keys' => array(),
			'meta_types' => array(),
			'meta_operators' => array(),
			'caption_1' => 'title',
			'caption_2' => 'tags',
			'lightbox_caption_1' => 'title',
			'lightbox_caption_2' => 'excerpt',
			'link' => 'lightbox'
		);
	}

	function get_custom_field_values(){
		global $wpdb;
		$values = $wpdb->get_results("SELECT DISTINCT(meta_key) FROM `$wpdb->postmeta` ORDER BY meta_key");
		$result = array();
		foreach($values as $value){
			if (!preg_match('/^_asg_/', $value->meta_key))
				$result []= $value;
		}
		return $result;
	}

}

global $asg_source_editors;
$asg_source_editors['posts'] = new ASG_Posts_Source_Editor;
