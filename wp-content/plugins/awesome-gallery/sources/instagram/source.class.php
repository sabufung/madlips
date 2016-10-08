<?php
class ASG_Instagram_Source extends ASG_Http_Source {

	function __construct($options) {
		$this->slug = 'instagram';
		$this->name = 'Instagram';
		$this->sequential = true;
		parent::__construct($options);
	}

	function get_image_url($data, $options){
		$largest = $this->get_largest_resolution($data);
		return $largest['url'];
	}

	function fetch_image_size($item, $url, $options){
		$largest = $this->get_largest_resolution($item);
		return array('width' => $largest['width'], 'height' => $largest['height']);
	}

	function get_permalink($data, $options){
		return $data['link'];
	}

	function get_tags($data, $options){
		return $data['tags'];
	}


	function get_largest_resolution($image_data) {
		$width = 0;
		$largest = null;
		foreach ($image_data['images'] as $name => $data) {
			if ($data['width'] > $width)
				$largest = $data;
		}
		return $largest;
	}

	function get_caption($source, $image_data) {
		$source = $this->source[$source];
		switch ($source) {
			case 'none':
				return '';
			case 'login':
				return $image_data['caption']['from']['username'];
			case 'fullname':
				return $image_data['caption']['from']['full_name'];
			default:
				return $image_data['caption']['text'];
				break;
		}
	}

	function fetch_raw_images($page, $limit, $max_id = null, $options = null) {
		$prefix = 'https://api.instagram.com/v1/';
		switch ($this->source['feed_type']){
			case 'my-feed':
				$url = $prefix . 'users/self/feed';
				break;
			case 'liked':
				$url = $prefix . 'users/self/media/liked';
				break;
			case 'hashtag':
				$tag = strtolower(preg_replace('/^#/', '', $this->source['hashtag']));
				$url = $prefix . "tags/" . $tag . "/media/recent";
				break;
			default:
				$user_id = $this->get_user_id();
				if (is_wp_error($user_id))
					return array($user_id, null);
				$url = $prefix . "users/{$user_id}/media/recent";
		}
		if ($limit)
			$url = add_query_arg('count', $limit, $url);
		if ($max_id)
			$url = add_query_arg('max_id', $max_id, $url);
		$url = add_query_arg('access_token', $this->source['access_token'], $url);
		$result = $this->http_get_cached($url, $options);
		if (is_wp_error($result))
			return array($result, null);
		$result = json_decode($result, true);
		return array($result['data'], isset($result['pagination']['next_max_id']) ? $result['pagination']['next_max_id'] : null);
	}

	function get_user_id() {
		$url = add_query_arg(urlencode_deep(array(
			'q' => strtolower($this->source['other_user_login']),
			'access_token' => $this->source['access_token']
		)), 'https://api.instagram.com/v1/users/search');
		$response = $this->http_get_cached($url);
		if (is_wp_error($response))
			return $response;
		$users = json_decode($response);
		foreach ($users->data as $user) {
			if ($user->username == $this->source['other_user_login']) {
				return $user->id;
			}
		}
		return new WP_Error(-1, 'User not found');
	}
}

global $asg_sources;
$asg_sources['instagram'] = 'ASG_Instagram_Source';
