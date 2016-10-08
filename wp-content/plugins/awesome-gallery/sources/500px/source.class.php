<?php

class ASG_500px_Source extends ASG_Http_Source {
	var $categories;
	function __construct($options){
		$this->name = '500px';
		$this->slug = '500px';
		$this->categories =  array('' => __('Everything' ,'asg'),
			'0' => __('Uncategorized','asg'),
			'10' => __('Abstract', 'asg'),
			'11' => __('Animals', 'asg'),
			'5' => __('Black and White', 'asg'),
			'1' => __('Celebrities', 'asg'),
			'9' => __('City and Architecture', 'asg'),
			'15' => __('Commercial', 'asg'),
			'16' => __('Concert', 'asg'),
			'20' => __('Family', 'asg'),
			'14' => __('Fashion', 'asg'),
			'2' => __('Film', 'asg'),
			'24' => __('Fine Art', 'asg'),
			'23' => __('Food', 'asg'),
			'3' => __('Journalism', 'asg'),
			'8' => __('Landscapes', 'asg'),
			'12' => __('Macro', 'asg'),
			'18' => __('Nature', 'asg'),
			'4' => __('Nude', 'asg'),
			'7' => __('People', 'asg'),
			'19' => __('Performing Arts', 'asg'),
			'17' => __('Sport', 'asg'),
			'18' => __('Still life', 'asg'),
			'21' => __('Street', 'asg'),
			'26' => __('Transportation', 'asg'),
			'13' => __('Travel', 'asg'),
			'22' => __('Underwater', 'asg'),
			'23' => __('Urban Exploration', 'asg'),
			'25' => __('Wedding', 'asg'),
			'27' => __('Urban Exploration', 'asg')
		);
		parent::__construct($options);
	}
	
	function get_max_images(){
		return 100;
	}

	function fetch_raw_images($page, $per_page, $options = array()){
		$feature = 'photos';
		$options = array('image_size' => 5);
		if ($this->source['sorting']){
			$options['sort'] = $this->source['sorting'];
		}
		$options['rpp'] = $per_page;
		$options['page'] = $page;
		
		switch ($this->source['source_type']) {
			case 'popular':
			case 'upcoming':
			case 'editors':
			case 'fresh_today':
			case 'fresh_yesterday':
			case 'fresh_week':
				$options['feature'] = $this->source['source_type'];
				$path = "photos";
				$options['only'] = $this->source['category'];
				break;
			case 'user':
				$path = 'photos';
				$options['feature'] = 'user';
				$options['only'] = $this->source['category'];
				$options['username'] = $this->source['username'];
				break;
			case 'user_friends':
				$options['feature'] = 'user_friends';
				$path = 'photos';//$this->source['username'] . "/friends"; 
				$options['only'] = $this->source['category'];
				$options['username'] = $this->source['username'];
				break;
			case 'user_favorites':
				$path = 'photos';//$this->source['username'] . "/favourites"; 
				$options['feature'] = 'user_favorites';
				$options['username'] = $this->source['username'];
				break;
			case 'user_collection':
				$path = "collections/" . $this->source['collection'];
				break;
		}
		$data = $this->get_data($path, $options);
		if (is_wp_error($data))
			return array($data, null);
		$data = $data['photos'];
		return  array($data, null);
	}

	function get_data($path, $options = array()){

		$url = add_query_arg(urlencode_deep($options), "https://api.500px.com/v1/$path");
		$oauth = new ASG_500px_OAuth(
				$this->source['consumer_key'],
				$this->source['consumer_secret'],
				$this->source['access_token'],
				$this->source['access_token_secret']
		);
		$data = $oauth->get($url);
		if (is_wp_error($data)){
			return $data;
		}
		return json_decode($data, true);
	}

	function get_collections(){
		$oauth = new ASG_500px_OAuth(
				$this->source['consumer_key'],
				$this->source['consumer_secret'],
				$this->source['access_token'],
				$this->source['access_token_secret']
		);
		$data = $oauth->get('https://api.500px.com/v1/collections');
		if (is_string($data))
			$data = json_decode($data);
		if (!$data)
			return new WP_Error(1, __('Error fetching collections', 'asg)'));
		foreach($data->collections as $collection){
			$first_photo = null;
			if (count($collection->photos)){
				$first_photo = $collection->photos[0];
			}
			$collections []= array(
				'id' => $collection->id,
				'title' => $collection->title,
				'cover' => $first_photo ? asg_get_image_url($first_photo->image_url, array('height' => 180)) : null
			);
		}
		return $collections;
	}

	function fetch_image_size($data, $url, $options){
		return array('width' => $data['width'], 'height' => $data['height']);
	}

	function get_permalink($data, $options){
		return "http://500px.net/photo/{$data['id']}";
	}

	function get_image_url($data, $options){
		if (isset($data['images'])){
			$sizes = array();
			foreach($data['images'] as $image){
				$sizes[$image['size']] = $image['url'];
			}
			$sizes_available = array_keys($sizes);
			sort($sizes_available);
			return $sizes[$sizes_available[count($sizes_available) - 1]];
		}
		return $data['image_url'];
	}

	function get_lightbox_url($data, $options){
		return $data['image_url'];
	}

	function get_meta($data, $options = array()){
		$result = array();
		foreach(array('camera', 'lens', 'focal_length', 'iso', 'shutter_speed', 'aperture') as $param){
			if (isset($data[$param]))
			$result[$param] = $data[$param];
		}
		if ($result){
			return $result;
		}
		return null;
	}

	function get_tags($data){
		if (isset($this->categories[(string)$data['category']]))
			return array($this->categories[(string)$data['category']]);
		return array();
	}

	function get_caption($name, $data){
		$source = $this->source[$name];
		if (!$source)
			return null;
		switch ($source){
			case 'username':
				return $data['user']['username'];
			case 'user_full_name':
				return $data['user']['user_full_name'];
			case 'category':
				return $this->categories[$data[$category]];
			default:
				return $data[$this->source[$name]];
		};
	}

	function ping(){
		$data = $this->get_data('photos', array('feature' => 'popular'));
		return isset($data['photos']);
	}

	function ping_token(){
		$oauth = new ASG_500px_OAuth(
			$this->source['consumer_key'],
			$this->source['consumer_secret'],
			$this->source['access_token'],
			$this->source['access_token_secret']
		);
		$data = $oauth->get('https://api.500px.com/v1/collections');
		return $data !== null;
	}


}

global $asg_sources;
$asg_sources['500px'] = 'ASG_500px_Source';
