<?php

abstract class ASG_Source{
	protected $slug;
	protected $name;
	public $source;
	public $use_direct_url = false;
	function __construct($options){
		if (isset($options['sources']) && $options['sources']){
			$this->options = $options;
			$this->source = $this->options['sources'][$this->slug];
			$this->source['id'] = $this->options['id'];
		} else {
			$this->source = $options;
		}

	}

	function get_max_images(){
		return 500;
	}

	function get_link_attributes($image, $data){
		$attributes = array();
		if ($this->source['link'] == 'new-window')
			$attributes['target'] = '_blank';
		return $attributes;
	}

	function get_link_mode(){
		return $this->source['link'];
	}

	function build_image($item, $options){
		$image = new ASG_Image();
		if (!$image->url = $this->get_image_url($item, $options)){
			return null;
		}

		$size = $this->fetch_image_size($item, $image->url, $options);
		if (is_wp_error($size)){
			return null;
		}
		$image->thumbnail_url = $this->get_thumbnail_url($item, $options);
		$image->width = $size['width'];
		$image->height = $size['height'];
		if ($caption1 = $this->get_caption('caption_1', $item))
			$image->caption_1 = $caption1;
		if ($caption2 = $this->get_caption('caption_2', $item))
			$image->caption_2 = $caption2;
		if ($lightbox_caption_1 = $this->get_caption('lightbox_caption_1', $item))
			$image->lightbox_caption_1 = $lightbox_caption_1;
		if ($lightbox_caption_2 = $this->get_caption('lightbox_caption_2', $item))
			$image->lightbox_caption_2 = $lightbox_caption_2;
		$image->tags = $this->get_tags($item, $options);
		$image->slug = $this->get_slug($item, $options);
		$attr = $this->get_link_attributes($image, $item);
		if (!empty($attr))
			$image->link_attr = $attr;
		if ($this->source['link'] == 'same-window' || $this->source['link'] == 'new-window'){
			$image->link_url = $this->get_permalink($item, $options);
		} else {
			if ($this->source['link'] == 'lightbox'){
				$image->lightbox_url = $this->get_lightbox_url($item, $options);
			}
		}
		if ($meta = $this->get_meta($item, $options))
			$image->meta = $meta;
		if ($this->source['link'] == 'new-window')
			$image->new_window = true;
		return $image;
	}

	function get_slug($data, $options = array()){
		return $data['id'];
	}


	function get_meta($data, $options = array()){return null;}


	function fetch_image_size($item, $url, $options){
		$transient_name = "asg_size" . md5($url);
		if (false === ($size = get_transient($transient_name))){
			$size = $this->fetch_image_size_uncached($url);
			if (is_wp_error($size)){
				return $size;
			}
			set_transient($transient_name, $size,  3600 * 24); // Store image sizes for 1 day
		}
		return $size;
	}

	function fetch_image_size_uncached($url){
		$response = asg_remote_get($url, array(
			'headers' => array("Range" => 'bytes=0-131072')
		));
		if (is_wp_error($response))
			return $response;
		$image = @imagecreatefromstring($response['body']);
		if (!$image)
			return new WP_Error('Error fetching image size for URL ' . $url);
		$size = array(
				'width' => imagesx($image),
				'height' => imagesy($image)
		);
		@imagedestroy($image);
		return $size;
	}

	function get_thumbnail_url($image, $options){
		return $this->get_image_url($image, $options);
	}
	function get_lightbox_url($image, $options){
		return $this->get_image_url($image, $options);
	}
}

class ASG_Http_Source extends ASG_Source{
	protected $sequential = false;
	function __construct($id){
		parent::__construct($id);
	}

	function http_get_cached($url, $options = array()){
		return asg_http_get_cached($url, $options);
	}


	function truncate($text, $length = 200){
		$length = abs((int)$length);
		if (strlen($text) > $length) {
		  $text = preg_replace("/^(.{1,$length})(\s.*|$)/s", '\\1...', $text);
		}
		return $text;
	}
	function sanitize_html($description){
		return trim(preg_replace('/<a[^>]*><\/a>/mi', '', preg_replace('/(^(<(br|\/p|p><\/p)\s*+(\/>|>))*+)|(((<(br|p><\/p)\s*+(\/>|>))|:)*+$)/mi', '',
					strip_tags($description, '<span><br/><i><b><strong><italic><br><a><font><em>'))));
	}

	function fetch_raw_images_with_backup($page, $per_page, $state, $options){
		list($data, $state) = $this->fetch_raw_images($page, $per_page, $state, $options);
		$cached = false;
		if (is_wp_error($data) && defined('ASG_NO_BACKUP') && ASG_NO_BACKUP)
			return array($data, null);
		if (is_wp_error($data)){
			$error = $data;
			$this->send_notification_if_needed($data);
			$cached_data = get_post_meta($this->source['id'], "_asg-{$this->slug}-cache-" . $page . "-" . $per_page, true);
			$state = get_post_meta($this->source['id'], "_asg-{$this->slug}-cache-state" . $page . "-" . $per_page,
				true);
			if ($cached_data){
				$data = json_decode($cached_data, true);
				if ($data)
					$cached = true;
				else {
					return array($error, null);
				}
			} else {
				return array($error, null);
			}
		}
		if (!$cached && (isset($data[0]) && !is_object($data[0]))) {
			update_post_meta($this->source['id'], "_asg-{$this->slug}-cache-" . $page . "-" . $per_page, str_replace( '\\', '\\\\', json_encode($data)));
			//update_post_meta($this->source['id'], "_asg_{$this->slug}_cache_" . $page . "_" . $per_page, $data);
			update_post_meta($this->source['id'], "_asg-{$this->slug}-cache-state" . $page . "-" . $per_page, $state);
			delete_post_meta($this->source['id'], $this->get_notification_sent_meta_name());
		}
		return array($data, $state);
	}

	function get_notification_sent_meta_name(){
		return "_asg_{$this->slug}_notification_sent";
	}

	function send_notification_if_needed($error){
		$notification_sent =get_post_meta($this->source['id'], $this->get_notification_sent_meta_name(), true);
		if (!$notification_sent){
			$this->send_data_unavailable_notification($error);
			update_post_meta($this->source['id'], $this->get_notification_sent_meta_name(), 1);
		}
	}

	function send_data_unavailable_notification($error){
		$message = sprintf($this->name . " "  . __("data unavailable for the gallery built. A cached copy of data is still shown to site visitors,
		but it is not refreshed. It is recommended to receive a new access token by clicking \"Authorize\" at the
		gallery editor page %s", 'asg'), admin_url("post.php?post={$this->source['id']}&action=edit"));
		$message .= "\r\n\r\nTech details: \r\n";
		$message .= implode("\r\n", $error->get_error_messages());
		@wp_mail(get_option('admin_email'), get_bloginfo('name') . " – " . __('gallery data unavailable', 'asg'), $message);
	}


	function get_images_sequential($page, $per_page, $options){
		$current_page = 1;
		$state = null;
		$images = array();
		$needed = $page * $per_page;
		$fetch_page = 1;
		while(count($images) < $needed){
			list($images_page, $state) = $this->fetch_raw_images_with_backup($fetch_page, $per_page, $state, $options);
			if (is_wp_error($images_page))
				return $images_page;
			$images = array_merge($images, $images_page);
			if (!$state)
				break;
			$fetch_page++;
		}
		return array_slice($images, min($per_page * ($page - 1), count($images)), min($per_page, count($images)));
	}

	function get_images_by_page($page, $per_page, $options){
		list($data_array, $state) = $this->fetch_raw_images_with_backup($page, $per_page, array(), $options);
		return $data_array;
	}

	function get_images($options = array()){
		$options = wp_parse_args($options, array(
			'limit' => $this->get_max_images()
		));
		$offset = $page - 1 * $per_page;
		$result = array();
		$page = 1;
		$per_page = min($this->get_max_images(), ASG_MAX_IMAGES);
		do {
			if ($this->sequential)
				$data_array = $this->get_images_sequential($page, $per_page, $options);
			else
				$data_array = $this->get_images_by_page($page, $per_page, $options);
			if (is_wp_error($data_array))
				return $data_array;
			foreach($data_array as $image_data){
				$image_args = wp_parse_args(array('offset' => ($offset ++) + 1), $options);
				$image = $this->build_image($image_data,$image_args);
				if (!$image || is_wp_error($image))
					continue;
				$result []= $image;
			}
			$page++;
		} while (count($result) < ASG_MAX_IMAGES && count($data_array));
		if (count($result) > $options['limit'])
			$result = array_slice($result, 0, $options['limit']);
		return $result;
	}
}
