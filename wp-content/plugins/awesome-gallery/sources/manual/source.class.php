<?php

class ASG_ManualSource extends ASG_Source{
	function __construct($options){
		$this->slug = 'manual';
		$this->name = 'Manual';
		parent::__construct($options);
	}

	function get_images($options){
		$images = array();
		for($i = 0; $i < count($this->source['images']); $i ++){
			$data = $this->source['images'][$i];
			$image = $this->build_image($data, wp_parse_args(array('offset' => $i), $options));
			if ($image)
				$images []= $image;
		}
		return $images;
	}

	function build_image($data, $options){
		$image = parent::build_image($data, $options);
		if ($image && !is_wp_error($image)){
			if (isset($data['lightbox']['image']))
				$image->attachment_id = $data['lightbox']['image'];
		}
		return $image;
	}

	function get_slug($data, $options = array()){
		if (isset($data['lightbox']['image']) && $data['lightbox']['image']){
			return $data['lightbox']['image'];
		}
		return $data['image'];
	}

	function get_permalink($data){
		return isset($data['url']) ? $data['url'] : null;
	}

	function get_caption($type, $data){
		switch ($type){
			case 'caption_1':
				return $data['title'];
			case 'caption_2':
				return $data['description'];
			case 'lightbox_caption_1':
				return $data['lightbox']['title'];
			case 'lightbox_caption_2':
				return $data['lightbox']['description'];
		}
	}

	function get_meta($data, $options = array()){
		if (!isset($data['lightbox']['image']))
			return null;
		$meta = wp_get_attachment_metadata($data['lightbox']['image']);
		if (isset($meta['image_meta'])){
			return $meta['image_meta'];
		}
	}

	function get_tags($data){
		return array_unique(array_map('trim', explode(',', $data['tags'])));
	}

	function fetch_image_size($data, $url, $options){
		$meta = wp_get_attachment_metadata($data['image']);
		if (!is_wp_error($meta) && !empty($meta))
			return array('width' => $meta['width'], 'height' => $meta['height']);
		else return parent::fetch_image_size($data, $url[0], $options);
	}

	function get_image_url($data, $options){
		$url = wp_get_attachment_image_src($data['image'], 'original');
		return $url[0];
	}

	function get_lightbox_url($data, $options){
		if ($data['lightbox']['image']){
			$url = wp_get_attachment_image_src($data['lightbox']['image'], 'original');
			if ($url)
				return $url[0];
		}
		if ($data['url'])
			return $data['url'];
		return $this->get_image_url($data, $options);
	}

}

global $asg_sources;
$asg_sources['manual'] = 'ASG_ManualSource';
