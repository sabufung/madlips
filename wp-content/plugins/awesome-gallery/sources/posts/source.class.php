<?php
class ASG_Posts_Source extends ASG_Source{
	function __construct($options){
		$this->slug = 'posts';
		$this->name = 'Posts';
		parent::__construct($options);
	}

	function get_images($options = array()){
		$result = array();
		if (isset($options['images']) && $options['images']){
			$ids = array_unique(
				array_map('intval', preg_split("/\s*,\s*/", $options['images'])));
			$query = new WP_Query(array('post__in' => array($ids), 'ignore_sticky_posts' => true,
				'post_type' => 'any', 'post_status' => 'any', 'paged' => $page, 'posts_per_page' => $per_page));
			$posts = $query->get_posts();
		} else {
			if ($this->source['source'] == 'featured'){
				$posts = $this->get_posts();
			} else {
				$posts = $this->get_attachments();
			}
		}
		foreach($posts as $post){
			if ($post){
				$image = $this->build_image($post, $options);
				if ($image){
					$result []= $image;
					$image->attachment_id = $post->ID;
				}

			}
		}
		return $result;
	}

	function get_slug($data, $options = array()){
		return $data->ID . "-" . $data->post_name;
	}

	function get_meta($data, $options = array()){
		$meta = wp_get_attachment_metadata($data->ID);
		if (isset($meta['image_meta'])){
			return $meta['image_meta'];
		}
		return null;
	}

	function get_image_url($data, $options){
		$src = $this->get_image_src($data);
		return asg_fix_image_url($src[0]);
	}
	function fetch_image_size($data, $url, $options){
		$src = $this->get_image_src($data);
		return array('width' => $src[1], 'height' => $src[2]);
	}

	function get_permalink($data, $options){
		if ($data->post_type == 'attachment'){
			$data = get_post($data->post_parent);
		}
		return get_permalink($data);
	}

	function get_attachments(){
		$result = array();
		$posts = $this->get_posts();
		while (count($result) < $this->get_max_images() && count($posts)){
			$post = array_shift($posts);
			foreach(get_children(array(
				'post_parent' => $post->ID,
				'post_type' => 'attachment',
				'numberposts' => -1, 'post_mime_type' => 'image')) as $attachment){
				$result[] = $attachment;
			}

		}
		return $result;
	}

	function get_posts(){
		$query = array(
			'post_type' => $this->source['post_type'],
			'posts_per_page' => $this->get_max_images(),
			'orderby' => $this->source['orderby'],
			'order' => $this->source['order'],
			'tax_query' => $this->build_tax_query(),
			'meta_query' => $this->build_meta_query(),
		);
		if (trim($this->source['ids'])){
			if ($this->source['include_exclude'] == 'include'){
				$query['post__in'] = array_map('trim', explode(',', trim($this->source['ids'])));
				if ($query['orderby'] == 'ID')
					$query['orderby'] = 'post__in';
			}
			else{
				$query['post__not_in'] = array_map('trim', explode(',', trim($this->source['ids'])));
			}
		}
		return get_posts($query);
	}

	function get_caption($source, $post){
		$source = $this->source[$source];
		switch ($source) {
			case '':
				return null;
			case 'title':
				return $post->post_title;
			case 'excerpt':
				return do_shortcode($post->post_excerpt);
			case 'content':
				return do_shortcode($post->post_content);
			case 'datetime':
				return mysql2date(get_option('date_format'), $post->post_date) . get_post_time(get_option('time_format'), false, $post, true);
			case 'date':
				return mysql2date(get_option('date_format'), $post->post_date);
			case 'tags':
				return implode($this->get_tags($post), ', ');
			default:
				if (preg_match('/^custom_field_/', $source)){
					$field = preg_replace("/^custom_field_/", '', $source);
					$value = get_post_meta($post->ID, $field, true);
					return apply_filters('asg_custom_field', $value, $source, $post);
				}
		}

	}

	function get_tags($post){
		$tags = array();
		if ($post->post_type == 'attachment' && $this->source['post_type'] != 'attachment')
			$post = get_post($post->post_parent);
		foreach(wp_get_object_terms($post->ID, $this->source['tags_taxonomy']) as $tag_data){
			$tags []= $tag_data->name;
		}
		return $tags;
	}

	function get_image_src($post){
		if ($post->post_type != 'attachment'){
			return wp_get_attachment_image_src(get_post_thumbnail_id($post->ID), 'original');
		}
		return wp_get_attachment_image_src($post->ID, 'original');
	}

	function build_meta_query(){
		$meta_query = array();
		for ($i = 0; $i < count($this->source['meta_keys']); $i++ ){
			$meta_query []= array(
				'key' => $this->source['meta_keys'][$i],
				'compare' => $this->source['meta_operators'][$i],
				'value' => $this->source['meta_values'][$i],
				'type' => $this->source['meta_types'][$i]
			);
		}
		return $meta_query;
	}
	function build_tax_query(){
		$tax_query = array();
		for ($i = 0; $i < count($this->source['taxonomies']); $i++){
			$tax_query []= array(
				'taxonomy' => $this->source['taxonomies'][$i],
				'field' => 'slug',
				'terms' => array_map('trim', explode(',', $this->source['tags'][$i])),
				'operator' => $this->source['operators'][$i]
			);
		}
		return $tax_query;
	}
}

global $asg_sources;
$asg_sources['posts'] = 'ASG_Posts_Source';
