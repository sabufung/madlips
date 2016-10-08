<?php

class ASG_Gallery_Editor {
	var $gallery;
	function __construct(){
		add_action('admin_enqueue_scripts', array($this, '_admin_enqueue_scripts'));
		add_filter('post_updated_messages', array($this, '_post_updated_messages'));
		add_action('add_meta_boxes_' . ASG_POST_TYPE, array($this, '_add_meta_boxes'));

		add_action('edit_form_after_editor', array($this, '_edit_form_after_title'));
		// Remove Quick Edit action
		add_filter('post_row_actions', array($this, '_post_row_actions'), 10, 2);

		add_action('wp_ajax_asg-preview', array($this, '_preview'));

		add_action('save_post', array($this, '_save_post'), 10, 2);
		add_action('admin_footer', array($this, '_admin_footer'));

		add_action('admin_action_asg-gallery-clone', array($this, '_clone'));
	}

	function _clone(){
		$id = $_REQUEST['id'];
		check_admin_referer('asg-clone-' . $id);
		$post = get_post($id);
		$post->ID = null;
		$post->post_title = $post->post_title . " " . __('(copy)', 'asg');
		$newid = wp_insert_post($post);
		foreach(get_post_custom($id) as $key => $value){
			if (!in_array($key, array('_edit_lock'))){
				foreach($value as $k => $value_item)
					add_post_meta($newid, $key, maybe_unserialize($value_item), true);
			}
		}
		wp_redirect(admin_url('post.php?action=edit&post=' . $newid));
		exit;
	}

	function get_google_fonts(){
		if (false === ($fonts = get_transient('asg_fonts'))){
			$fonts = wp_remote_get('https://www.googleapis.com/webfonts/v1/webfonts?key=AIzaSyDSpTwW0s_wuysfud2LSssvVOEvHD0ltOs');
			if (!is_wp_error($fonts)){
				$fonts = $this->recode_fonts($fonts['body']);
				set_transient('asg_fonts', $fonts, 3600 * 24);
			}
		}
		if (is_wp_error($fonts) || strlen($fonts) < 512){
			$fonts = file_get_contents(ASG_PATH . "assets/fonts.json");
		}
		if (false === $fonts)
			return array();
		$fonts = json_decode($fonts);
		return $fonts;
	}

	function recode_fonts($fonts){
		$fonts = json_decode($fonts);
		$recoded = array();
		foreach($fonts->items as $item){
			$recoded []= array(
				'family' => $item->family,
				'variants' => $item->variants
			);
		}
		return json_encode(array('items' => $recoded));
	}

	function _preview(){
		global $asg_sources;
		$data = wp_parse_args(stripslashes_deep($_REQUEST['data']));
		foreach(array_keys($asg_sources) as $key){
			$data['asg']['sources'][$key]['link'] = 'no-link';
		}
		$id = $data['post_ID'];
		$gallery = new ASG_Gallery($id, $data['asg']);
		$gallery->render(true);
		exit;
	}

	function _admin_enqueue_scripts(){
		global $post_type, $hook_suffix, $asg_source_editors;
		if ($post_type != ASG_POST_TYPE || !in_array($hook_suffix, array('post.php', 'post-new.php'))){
			return;
		}
		wp_enqueue_media();
		wp_enqueue_script('jquery-ui');
		wp_enqueue_script('rivets', ASG_URL . 'assets/admin/js/rivets.js', array('backbone'), ASG_VERSION);
		asg_enqueue_scripts();
		asg_enqueue_styles();
		wp_enqueue_style('asg-gallery-editor', ASG_URL . "assets/admin/css/gallery-editor.css", null, ASG_VERSION);
		asg_enqueue_script('asg-image-selector', 'assets/admin/js/image-selector');
		asg_enqueue_script('asg-gallery-selector', "assets/admin/js/external-gallery-selector", array('media-editor'), ASG_VERSION);
		asg_enqueue_script('asg-preview', "assets/admin/js/preview", array('media-editor'), ASG_VERSION);
		asg_enqueue_script('asg-source-editor', "assets/admin/js/source-editor", array('asg-gallery-selector', 'media-editor'), ASG_VERSION);
		foreach($asg_source_editors as $slug => $editor){
			$source_slug = 'asg-source-' . $slug;
			asg_enqueue_script($source_slug, $editor->get_js_editor_path(), array('asg-gallery-selector'), ASG_VERSION);
			$sources []= $source_slug;
		}
		asg_enqueue_script('asg-gallery-editor', 'assets/admin/js/gallery-editor', $sources);
		wp_localize_script('asg-gallery-editor', 'asgGalleryEditor', array('admin' => true));
	}

	function _admin_footer(){
		global $hook_suffix, $post, $asg_source_editors;
		if (($hook_suffix == 'post.php' or $hook_suffix == 'post-new.php') && $post && $post->post_type == ASG_POST_TYPE){
			foreach($asg_source_editors as $slug => $source)
				$source->render_editor_invisibles();
		}
	}

	function _add_meta_boxes($post){
		global $wp_meta_boxes;
		// Remove all the third party meta boxes - we don't need them
		foreach(array('advanced', 'normal', 'side') as $priority)
			$wp_meta_boxes[ASG_POST_TYPE][$priority] = array();
		// Add customized Publish block.
		add_meta_box('submitdiv', __('Actions'), array($this, '_submitdiv_meta_box'), ASG_POST_TYPE, 'side', 'default');
		add_meta_box('asg-presets', __('Presets', 'asg'), array($this, '_presets_meta_box'), ASG_POST_TYPE, 'side', 'default');
		add_meta_box('asg-layout', __('Layout', 'asg'), array($this, '_layout_meta_box'), ASG_POST_TYPE, 'side', 'default');
		add_meta_box('asg-image', __('Image options', 'asg'), array($this, '_image_meta_box'), ASG_POST_TYPE, 'normal', 'default');
		add_meta_box('asg-custom-css', __('Custom CSS', 'asg'), array($this, '_custom_css_meta_box'), ASG_POST_TYPE,
			'normal', 'default');
		add_meta_box('asg-load-more', __('Load More / Endless scroll', 'asg'), array($this, '_load_more_meta_box'), ASG_POST_TYPE, 'side', 'default');
		add_meta_box('asg-filters', __('Filters', 'asg'), array($this, '_filters_meta_box'), ASG_POST_TYPE, 'side', 'default');
	}

	function _border_meta_box($post){
		$gallery = $this->gallery;
		require('templates/meta-box-border.php');
	}

	function _caption_meta_box($post){
		$gallery = $this->gallery;
		require('templates/meta-box-caption.php');
	}

	function _custom_css_meta_box($post){
		$gallery = $this->gallery;
		require('templates/meta-box-custom-css.php');
	}

	function _image_meta_box($post){
		$gallery = $this->gallery;
		require('templates/meta-box-image.php');
	}

	function _style_meta_box($post){
		$gallery = $this->gallery;
		require('templates/meta-box-style.php');
	}

	function _layout_meta_box($post){
		$gallery = $this->gallery = asg_get_gallery($post);
		require('templates/meta-box-layout.php');
	}

	function _presets_meta_box($post){
		$gallery = $this->gallery;
		require('templates/meta-box-presets.php');
	}

	function _load_more_meta_box($post){
		$gallery = $this->gallery;
		require('templates/meta-box-load-more.php');
	}

	function _filters_meta_box($post){
		$gallery = $this->gallery;
		require('templates/meta-box-filters.php');
	}

	function _post_row_actions($actions, $post){
		if ($post->post_type != ASG_POST_TYPE)
			return $actions;
		// Remove Quick Edit action
		unset($actions['inline hide-if-no-js']);
		return $actions;
	}

	
	function _post_updated_messages($messages){
		$messages[ASG_POST_TYPE][1] = __('Gallery updated.', 'asg');
		$messages[ASG_POST_TYPE][6] = __('Gallery created.', 'asg');
		return $messages;
	}
	

	function _edit_form_after_title(){
		global $post;
		if ($post->post_type != ASG_POST_TYPE)
			return;
		$gallery = asg_get_gallery($post);
		require('templates/edit-form-after-title.php');
	}

	function parse_query($str) {
		// Separate all name-value pairs
		$pairs = explode('&', $str);
		$data = array();
		foreach($pairs as $pair) {

			// Pull out the names and the values
			list($name, $value) = explode('=', $pair, 2);
			$name = urldecode($name);
			$value = urldecode($value);
			if (strpos($name, '[') !== false){
				$matches = array();
				preg_match_all('/\[([^\]]*)\]/', $name, $matches);
				$keys = $matches[1];
				array_unshift($keys, preg_replace('/\[.*/', '', $name));
				$this->array_deep_assign($data, $keys, $value);
			} else {
				$data[$name] = $value;
			}

		}
		return $data;
	}

	function array_deep_assign(&$subject, $keys, $value){
		$key = array_shift($keys);
		if (count($keys)){
			if (!isset($subject[$key]))
				$subject[$key] = array();
			$this->array_deep_assign($subject[$key], $keys, $value);
		} else {
			if ($key)
				$subject[$key] = $value;
			else
				$subject []= $value;
		}
	}

	function _save_post($id, $post){
		if ($post->post_type != ASG_POST_TYPE)
			return;
		if (isset($_REQUEST['asg-hack'])){
			$data = $this->parse_query(stripslashes($_REQUEST['asg-hack']));
		}
		$gallery = null;
		if (isset($data) && isset($data['asg'])){
			$gallery = $data['asg'];
			if (isset($_REQUEST['preset']) && $_REQUEST['preset']){
				$presets = asg_get_presets();
				$preset_data = $presets[(int)$_REQUEST['preset']]['data'];
				$gallery = asg_parse_args(asg_parse_args($gallery, asg_get_default_preset()), $preset_data);
			}
			//Cleanup
			delete_post_meta($post->ID, '_asg');
			$json = addslashes(json_encode($gallery));
			update_post_meta($post->ID, '_asg_json', $json);
		}
		remove_action('save_post', array($this, '_save_post'));
		if (isset($_REQUEST['post_title']))
			wp_update_post(array('ID' => $id, 'post_name' => sanitize_title(stripslashes($_POST['post_title']))));
		add_action('save_post', array($this, '_save_post'));

		if ($post->post_status == 'draft'){
			wp_update_post(array('ID' => $id, 'post_status' => 'publish'));
		}
	}
	function _submitdiv_meta_box($post){
		global $action;
		$post_type = $post->post_type;
		$post_type_object = get_post_type_object($post_type);
		$can_publish = current_user_can($post_type_object->cap->publish_posts);
		require('templates/meta-box-publish.php');
	}
}
new ASG_Gallery_Editor;
