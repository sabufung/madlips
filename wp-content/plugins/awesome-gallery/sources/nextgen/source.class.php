<?php
class ASG_NEXTGEN_Source extends ASG_Http_Source{
	var $c_use_ngg_thumbnails;
	var $storage;

	function __construct($options){
		$this->slug = 'nextgen';
		$this->name = 'NEXTGEN';
		parent::__construct($options);
	}

	function get_galleries(){
		global $wpdb;
		$galleries = $wpdb->get_results($a = "SELECT * FROM {$wpdb->prefix}ngg_gallery ORDER BY name");
		$result = array();
		foreach ($galleries as $gallery){
			$storage = $this->get_storage();
			$thumb = $storage->get_image_url($gallery->previewpic);
			$result []= array(
				'id' => $gallery->gid,
				'cover' => $thumb,
				'title' => $gallery->name
			);
		}
		return $result;
	}

	function get_slug($data, $options = array()){
		return $data->pid;
	}

	function get_storage(){
		if (!$this->storage)
			$this->storage = C_Gallery_Storage::get_instance();
		return $this->storage;
	}
	function get_image_url($image, $options){
		$storage = $this->get_storage();
		return $storage->get_image_url($image);

	}

	function use_ngg_thumbnails($image, $options){
		$settings = C_Settings_Model::get_instance();
		$width = $settings->thumbwidth;
		$height = $settings->thumbheight;
		$image = new nggImage($image);
		$metadata = $image->meta_data;
		return ($width >= (int)$options['width'] * 2 && $height >= (int)$options['height'] * 2) &&
			isset($metadata['thumbnail']) && $metadata['thumbnail']['width'] > (int)$options['width'] * 2 && $metadata['thumbnail']['height'] > (int)$options['height'] * 2;
	}

	function get_thumbnail_url($image, $options){
		return $this->get_image_url($image, $options);
	}

	function get_images($options = array()){
		global $wpdb;
		$where = '';
		if (!class_exists('C_Settings_Model'))
			return new WP_Error(__('NEXTGEN gallery is not installed. Please install and activate NEXTGEN gallery plugin.', 'asg'));
		if ($this->source['gallery']){
			if ($gallery = $this->get_gallery($this->source['gallery']))
				$options['gallery'] = $gallery;
			$where .= "galleryid = " . (int)$this->source['gallery'];
		}
		$where .= " AND exclude <> 1 ";
		$orderby = $this->source['order_by'];
		$order = $this->source['order'];
		$limit = (int)$this->get_max_images();
		$offset = 0;
		$images = $wpdb->get_results("SELECT * FROM {$wpdb->nggpictures} p INNER JOIN {$wpdb->nggallery} g ON p.galleryid = g.gid  WHERE $where ORDER BY $orderby $order LIMIT $limit OFFSET $offset");
		$result = array();
		foreach($images as $data){
			$image = $this->build_image($data, $options);
			if ($image)
				$result []= $image;
		}
		return $result;
	}

	function unserialize_image_metadata($image){
		if (class_exists('Mixin_DataMapper_Driver_Base'))
			return Mixin_DataMapper_Driver_Base::unserialize($image->meta_data);
		if (class_exists('Ngg_Serializable'))
			return Ngg_Serializable::unserialize($image->meta_data);
		return maybe_unserialize($image->meta_data);
	}
	
	function fetch_image_size($image, $url, $options){
		$image = new nggImage($image);
		if ($image->meta_data && isset($image->meta_data['width']) && $image->meta_data['height']){
				return array('width' => $image->meta_data['width'], 'height' =>  $image->meta_data['height']);
		} else{
			return parent::fetch_image_size($image, $url, $options);
		}
	}
	

	function get_permalink($image, $options){
		return $this->get_image_url($image, $options);
	}

	function get_gallery($id){
		global $wpdb;
		$gallery = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}ngg_gallery WHERE gid = " . (int)$id);
		if (count($gallery))
			return $gallery[0];
		return null;
	}

	function get_caption($name, $item){
		$source = $this->source[$name];
		if (in_array($source, array('filename', 'alttext', 'description'))){
			return $item->$source;
		}
		if ($source == 'datetime')
			return mysql2date(get_option('date_format') . ' ' . get_option('time_format'), $item->imagedate);
		if ($source == 'date')
			return mysql2date(get_option('date_format'), $item->imagedate);
		if ($source == 'excerpt')
			return $this->truncate($item->description);
		return '';
	}

	function get_meta($image, $options = array()){
		$image = new nggImage($image);
		if ($image->meta_data)
			return array_intersect_key($image->meta_data, array_flip(array('camera', 'shutter_speed', 'aperture', 'iso', 'flash', 'copyright', 'focal_length')));
		return null;
	}

	function get_tags($image, $options){
		$img = new nggImage($image);
		$data = $img->meta_data;
		$tags = array();
		if (isset($data['keywords']) && $data['keywords']){
			$tags = preg_split('/,\s*/', $data['keywords']);
		}
		foreach(wp_get_object_terms($image->pid, 'ngg_tag', 'fields=names') as $term){
			$tags []= $term;
		};
		return $tags;
	}

}




global $asg_sources;
$asg_sources['nextgen'] = 'ASG_NEXTGEN_Source';
