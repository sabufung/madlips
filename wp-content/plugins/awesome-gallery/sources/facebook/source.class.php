<?php
class ASG_Facebook_Source extends ASG_Http_Source{
	function __construct($options){
		$this->slug = 'facebook';
		$this->name = __('facebook', 'asg');
		$this->sequential = true;
		parent::__construct($options);
	}


	function generate_access_token(){
		$data = $this->http_get_cached(add_query_arg(array(
			'client_id' => $this->source['app_id'],
			'client_secret' => $this->source['app_secret'],
			'grant_type' => 'client_credentials'
		), 'https://graph.facebook.com/oauth/access_token'));
		if (is_wp_error($data))
			return $data;
		if (!preg_match('/access_token=/', $data))
			return new WP_Error(500, __('Invalid keys', 'asg'));
		return preg_replace('/access_token=/', '', $data);
	}

	function check_access_token(){
		$data = $this->call_fb('me');
		if (is_wp_error($data))
			return false;
		if (isset($data['id']))
			return true;
		return false;
	}

	function call_fb($url, $options = array()){
		$url = add_query_arg('access_token', $this->source['access_token'], "https://graph.facebook.com/v2.0/" . $url);
		$data = $this->http_get_cached($url, $options);
		if (is_wp_error($data)){
			$message = json_decode($data->get_error_message(), true);
			return new WP_Error($data->get_error_code(), $message['error']['message']);
		}
		return json_decode($data, true);
	}

	function find_user_or_page(){
		if (!trim($this->source['username']))
			return new WP_Error(404, __('Please enter user or page name', 'asg'));
		$data = $this->call_fb($this->get_sanitized_username());
		return $data;
	}

	function get_sanitized_username(){
		return preg_replace('/\/\s*/', '',
				preg_replace('|^(https?://)?(www\.?)?facebook\.com/?|', '', $this->source['username'])
		);
	}

	function get_albums(){
		$url = $this->get_sanitized_username();
		$data = $this->call_fb($url . "/albums?fields=name,from,cover_photo");
		if (is_wp_error($data))
			return $data;
		$result = array();
		$no_paging = false;
		while(!$no_paging){
			foreach($data['data'] as $album){
				if (!empty($album['cover_photo'])){
					$photo_id = $this->call_fb($album['cover_photo']);
					if (!is_wp_error($photo_id))
						$photo_id = $photo_id['images'][0]['source'];

				} else {
					$photo_id = new WP_Error(0, 'No photo');
				}

				$album_data = array(
					'id' => $album['id'],
					'cover' => is_wp_error($photo_id) ? '' : asg_get_image_url($photo_id, array('height' => 180)),
					'title' => $album['name']
				);
				$result []= $album_data;
			}
			if (isset($data['paging']['cursors']['after'])){
				$data = $this->call_fb($url . "/albums?after=" . $data['paging']['cursors']['after']);
				if (is_wp_error($data))
					return $data;
			} else {
				$no_paging = true;
			}
		}
		return $result;
	}

	function fetch_raw_images($page, $per_page, $after, $options){
		if ($this->source['source_type'] == 'album'){
			$url = $this->source['source'] . "/photos";
		} else {
			$url = $this->get_sanitized_username() . "/photos/uploaded";
		}
		$url = add_query_arg(array(
				'limit' => $per_page,
				'after' => $after,
				'fields' => 'source,name,from,name_tags,tags,height,width,link'),
			$url);
		$response = $this->call_fb($url, $options);
		if (is_wp_error($response))
			return array($response, null);
		if (!isset($response['data']))
			return array(new WP_Error(), null);
		if (isset($response['paging']['cursors']['after']))
			$max_id = $response['paging']['cursors']['after'];
		else
			$max_id = null;
		return array($response['data'], $max_id);
	}

	function fetch_image_size($data, $url, $options){
		return array('width' => $data['width'], 'height' => $data['height']);
	}

	function get_image_url($data, $options){
		return $data['source'];
	}

	function get_permalink($data, $options){
		return $data['link'];
	}

	function get_caption($source, $data){
		$source = $this->source[$source];
		if (!$source)
			return null;
		switch ($source) {
			case 'place':
				return (isset($data['place'])) ? $data['place']['name'] : '';
			case 'category':
				return isset($data['from']['category']) ? $data['from']['category'] : '';
			case 'from':
				return isset($data['from']['name']) ? $data['from']['name'] : '';
			case 'name':
				return isset($data['name']) ? $data['name'] : '';
			case 'caption':
				return isset($data['caption']) ? $data['caption'] : '';
			case 'tags':
				return implode(', ', $this->get_tags($data));
			default:
				return new WP_Error();
		}
	}

	function get_tags($data){
		if (!isset($data['tags']['data']))
			return array();
		$tags = array();
		foreach($data['tags']['data'] as $tag){
			$tags []= $tag['name'];
		}
		return $tags;
	}



}

global $asg_sources;
$asg_sources['facebook'] = 'ASG_Facebook_Source';
