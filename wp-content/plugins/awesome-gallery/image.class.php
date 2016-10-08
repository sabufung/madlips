<?php

class ASG_Image extends ASG_VisualElement {
	var $url;
	var $thumbnail_url;
	var $width;
	var $height;

	var $caption_1;
	var $caption_2;
	var $tags;
	var $link_url;
	var $slug;
	var $meta;

	var $lightbox_url;
	var $lightbox_caption_1;
	var $lightbox_caption_2;
	var $link_attr = array();
	var $options;
	// WP Attachment ID when available
	var $attachment_id;

	function is_link(){
		return isset($this->lightbox_url) && $this->lightbox_url || isset($this->link_url) && $this->link_url;
	}

	function render_caption_attributes($options){
		$class = 'asg-image-caption-wrapper';
		$caption = $options['caption'];
		$attr = array();
		$class .= " asg-position-" . $caption['position'];
		$class .= " asg-effect-" . $caption['effect'];
		$class .= " asg-mode-" . $caption['mode'];

		if ($caption['mode'] == 'on-hover' && $caption['effect'] == 'slide')
			$class .= " asg-on-hover";
		if ($caption['mode'] == 'off-hover' && $caption['effect'] == 'slide')
			$class .= " asg-off-hover";


		$attr['class'] = $class;
		$this->render_attributes($attr);
	}
	function render_image_attributes($options){
		$attr = array(
				'class' => 'asg-image',
				'data-height' => $this->height,
				'data-width' => $this->width,
				'data-tags' => implode(', ', $this->tags),
		);
		if ($this->slug)
			$attr['data-slug'] = $this->slug;
		if ($options['image']['blur'] && $options['image']['blur'] != 'off'){
			$attr['data-blur'] = $options['image']['blur'];
		}
		if ($options['image']['bw'] && $options['image']['bw'] != 'off'){
			$attr['data-bw'] = $options['image']['bw'];
		}
		if (asg_get_active_lightbox() == 'jetpack'){
			$attr['data-meta'] = json_encode($this->meta);
			$attr['data-attachment-id'] = $this->attachment_id;
		}
		$this->render_attributes($attr);
	}
	function render_overlay_attributes($options){
		$class = 'asg-image-overlay';
		$overlay = $options['overlay'];
		$attr = array();
		$class .= " asg-effect-" . $overlay['effect'];
		$class .= " asg-mode-" . $overlay['mode'];

		if ($overlay['mode'] == 'on-hover' && $overlay['effect'] == 'slide')
			$class .= " asg-on-hover";
		if ($overlay['mode'] == 'off-hover' && $overlay['effect'] == 'slide')
			$class .= " asg-off-hover";
		$attr['class'] = $class;
		$this->render_attributes($attr);
	}

	function render_link_attributes(){
		$attr = wp_parse_args($this->link_attr, array('class' => 'asg-image-wrapper'));
		if ($this->lightbox_url){
			$attr['class'] .= " asg-lightbox";
			$attr['href'] = $this->lightbox_url;
		} else {
			$attr['href'] = $this->link_url;
		}
		$this->render_attributes($attr);
	}

	function has_overlay($options){
		return $options['overlay']['mode'] && $options['overlay']['mode'] != 'off';
	}
	function has_caption(){
		return ($this->caption_1 || $this->caption_2) && $this->options['caption']['mode'] != 'off';
	}


	function has_lightbox_caption(){
		return ($this->lightbox_caption_1 || $this->lightbox_caption_2) && $this->lightbox_url;
	}
}
