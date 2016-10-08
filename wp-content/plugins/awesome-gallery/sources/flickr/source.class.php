<?php
class ASG_Flickr_Source extends ASG_Http_Source {
	var $id_regex = "/\d{3,10}@N\d{2,3}/";

	function __construct($options) {
		$this->slug = 'flickr';
		$this->name = __('Flickr', 'asg');
		parent::__construct($options);
	}

	function get_flickr_url($action, $key, $params = array()) {
		$params = wp_parse_args($params, array('method' => $action, 'api_key' => $key, 'format' => 'php_serial'));
		return add_query_arg(urlencode_deep($params), 'https://api.flickr.com/services/rest/');
	}

	function flickr_get($method, $params = array(), $options = null) {
		$url = $this->get_flickr_url($method, $this->source['key'], $params);
		$data = $this->http_get_cached($url, $options);
		if (is_wp_error($data))
			return $data;
		if (preg_match('/^<\?xml/', $data)) {
			$data = simplexml_load_string($data);
			$code = (string)$data->err['code'][0];
			$message = (string)$data->err['msg'][0];
			return new WP_Error($code, $message);
		}
		$data = unserialize($data);
		if ($data['stat'] != 'ok')
			return new WP_Error($data['code'], $data['message']);
		return $data;
	}

	function ping() {
		$data = $this->flickr_get('flickr.photos.getRecent');
		if (is_wp_error($data))
			return $data;
		if (isset($data['photos']) && count($data['photos']))
			return $data['photos'];
		return null;
	}

	function find_user_id() {
		if (preg_match("/\d+@N\d{1,3}/", $this->source['username']))
			return $this->source['username'];
		$data = $this->flickr_get('flickr.people.findByUsername', array('username' => $this->source['username']));
		if (is_wp_error($data))
			return $data;
		$user = $data['user'];
		if (!isset($user['nsid']))
			return new WP_Error('', 'User id not found');
		return $user['nsid'];
	}

	function ping_user() {
		$id = $this->find_user_id();
		if (is_wp_error($id))
			return $id;
		$data = $this->flickr_get('flickr.people.getPublicPhotos', array('user_id' => $id));
		if (is_wp_error($data))
			return $data;
		if (isset($data['photos']) && count($data['photos']))
			return $data['photos'];
		return null;
	}

	function get_photo_url($data, $allow_smaller = false, $options = array()) {
		if (isset($this->source['image_size']) && $this->source['image_size']) {
			$letter = $this->source['image_size'];
			return "http://farm{$data['farm']}.staticflickr.com/{$data['server']}/{$data['id']}_{$data['secret']}_{$letter}.jpg";
		}
		if (isset($data['primary']))
			return "http://farm{$data['farm']}.staticflickr.com/{$data['server']}/{$data['primary']}_{$data['secret']}.jpg";
		else {
			foreach(array('b', 'c', 'z', '-', 'n', 'm', 't', 'q', 's') as $letter)
				if ($data["url_" . $letter]){
					return "http://farm{$data['farm']}.staticflickr.com/{$data['server']}/{$data['id']}_{$data['secret']}_{$letter}.jpg";
				}
		}
	}

	function get_photosets() {
		$user_id = $this->find_user_id();
		if (is_wp_error($user_id))
			return $user_id;
		$data = $this->flickr_get('flickr.photosets.getList', array(
			'user_id' => $user_id,
			'per_page' => 500
		));
		$result = array();
		foreach ($data['photosets']['photoset'] as $photoset) {
			$result [] = array(
				'id' => $photoset['id'],
				'cover' => asg_get_image_url($this->get_photo_url($photoset), array('width' => 240, 'height' => 240)),
				'title' => $photoset['title']['_content']
			);
		}

		return $result;
	}

	function get_groups() {
		$user_id = $this->find_user_id();
		if (is_wp_error($user_id))
			return $user_id;
		$data = $this->flickr_get('flickr.people.getPublicGroups', array(
			'user_id' => $user_id,
			'per_page' => 500
		));
		$result = array();
		foreach ($data['groups']['group'] as $group) {
			$result [] = array(
				'id' => $group['nsid'],
				'cover' => ($group['iconserver'] > 0 ? 'http://farm' . $group['iconfarm'] . '.staticflickr.com/' . $group['iconserver'] . '/buddyicons/' . $group['nsid'] . '.jpg' : 'http://www.flickr.com/images/buddyicon.gif'),
				'title' => $group['name']
			);
		}
		return $result;
	}

	function fetch_raw_images2($page, $per_page, $options) {
		$user_id = $this->find_user_id();
		if (is_wp_error($user_id))
			return $user_id;
		$extras = 'description,tags,owner_name,url_k,url_h,url_b,url_c,url_z,url_-,url_m,url_n,url_s,url_t';
		switch ($this->source['source_type']) {
			case 'photostream':
				return $this->flickr_get('flickr.people.getPublicPhotos', array(
					'user_id' => $user_id,
					'per_page' => $per_page,
					'page' => $page,
					'extras' => $extras
				), $options);
				break;
			case 'photoset':
				$data = $this->flickr_get('flickr.photosets.getPhotos', array(
					'photoset_id' => $this->source['source'],
					'per_page' => $per_page,
					'page' => $page,
					'extras' => $extras
				), $options);
				if (is_wp_error($data) && (int)$page > 1 && $data->get_error_message(1) == 'Photoset not found'){
					return array('photoset' => array('photo' => array()));
				}
				if (is_wp_error($data))
					return $data;
				return $data;
				break;
			case 'group':
				$group_id = $this->get_group_id($this->source['source']);
				if (is_wp_error($group_id))
					return $group_id;
				return $this->flickr_get('flickr.groups.pools.getPhotos', array(
					'group_id' => $group_id,
					'per_page' => $per_page,
					'page' => $page,
					'extras' => $extras
				), $options);
				if (is_wp_error($data))
					return $data;
				break;
			case 'favorites':
				return $this->flickr_get('flickr.favorites.getPublicList', array(
					'user_id' => $user_id,
					'per_page' => $per_page,
					'page' => $page,
					'extras' => $extras
				), $options);
				if (is_wp_error($data))
					return $data;
				$data = $data['photos']['photo'];
				break;
			default:
				return new WP_Error('500', 'Strange flickr data source');
				break;
		}
	}

	function fetch_raw_images($page, $per_page, $options) {
		$images = $this->fetch_raw_images2($page, $per_page, $options);
		if (is_wp_error($images))
			return array($images, null);
		if (isset($images['photoset']['photo']))
			return array($images['photoset']['photo'], null);
		if (isset($images['photos']['photo']))
			return array($images['photos']['photo'], null);
		return array(new WP_Error(500, 'Wrong response'), null);
	}



	function fetch_image_size($data, $url, $options){
		foreach(array('b', 'c', 'z', '-', 'n', 'm', 't', 'q', 's') as $size) {
			if (isset($data['height_' . $size]) && isset($data['width_' . $size]))
				return array('width' => $data['width_' . $size], 'height' => $data['height_' . $size]);
		}

		return parent::fetch_image_size($data, $url, $options);
	}

	function get_tags($data, $options){
		if (!isset($data['tags']) || !$data['tags'])
			return array();
		$tags = array_filter(explode(' ', $data['tags']), array($this, 'filter_tags'));
		return $tags;
	}

	function filter_tags($tag){
		return preg_replace('/vision:[^\s]+/', '', $tag);
	}

	function get_image_url($data, $options){
		return $this->get_photo_url($data, true, $options);
	}

	function get_lightbox_url($data, $options){
		return $this->get_photo_url($data);
	}

	function get_permalink($data, $options){
		$base_url = $this->get_owner_base_url($data['ownername']);
		return $base_url . $data['id'] . "/";
	}

	function get_group_id($group_id_or_url){
		if (preg_match($this->id_regex, $group_id_or_url))
			return $group_id_or_url;
		$group_id_or_url = preg_replace("/^(http(s)?:)?(\/\/)?(www\.)?flickr.com\/groups\//", '', $group_id_or_url);
		$group_id_or_url = preg_replace("/\/(pool|discuss|members|rules)\/?$/", '', $group_id_or_url);

		$hash = md5($group_id_or_url);
		if (!($data = get_transient("asg_fl_group_$hash")) || true){
			$response = $this->getGroupInfo(array('group_path_alias' => $group_id_or_url));
			if (is_wp_error($response))
				return $response;
			$data = $response['group'];
			set_transient("asg_fl_group_$hash", $data, 2 * 3600);
		}
		return $data['id'];
	}
	function getGroupInfo($param = array()){
		return $this->flickr_get('flickr.groups.getInfo', $param);
	}
	function get_owner_base_url($owner){
		$hash = md5($owner);
		if (!($data = get_transient("asg_own_$hash"))){
			$id = $this->find_user_id($owner);
			$response = $this->flickr_get('flickr.people.getInfo', array('user_id' => $id));
			if (is_wp_error($response))
				return $response;
			$data = $response['person']['photosurl']['_content'];
			set_transient("asg_own_$hash", $data, 2 * 3600);
		}
		return $data;
	}

	function get_caption($source, $data) {
		$source = $this->source[$source];
		if (!$source)
			return null;
		switch ($source) {
			case 'tags':
				return implode(', ', $this->get_tags($data, array()));
			case 'title':
			case 'ownername':
			case 'views':
				return isset($data[$source]) ? $data[$source] : '';
			case 'description':
				return isset($data['description']['_content']) ? $data['description']['_content'] : '';
		}
	}


}

global $asg_sources;
$asg_sources['flickr'] = 'ASG_Flickr_Source';
