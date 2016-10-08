<?php

class ASG_Gallery extends ASG_VisualElement {
	private $source;
	private $gallery;
	private $options;
	private $id;
	static $grid_count = 0;
	function __construct($id, $attr = array()) {
		$this->id = $id;
		$post = get_post($id);
		$this->slug = $post->post_name;
		$this->gallery = $this->options = asg_parse_args(asg_get_gallery($post), wp_parse_args($attr, array('id' => $this->id)));
		$this->source = $this->create_source();
	}


	
	function create_source(){
		global $asg_sources;
		$source_class = $asg_sources[$this->gallery['source']];
		return new $source_class($this->gallery);
	}
	function str_rand($length = 8){
		$alphabet='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
		return substr(str_shuffle(str_repeat($alphabet, $length)), 0, $length);
	}

	function parse_font_weight($style){
		if (in_array($style, array('', 'regular', 'italic')))
			return '400';
		if ($style == 'bold')
			return '700';
		if (preg_match('/^(\d{3,4})/', $style, $matches))
			return $matches[0];
		return '400';
	}

	function parse_font_style($style){
		if (in_array($style, array('', 'italic')))
			return $style;

		if (preg_match('/^(\d{3,4}.+)/', $style, $matches))
			return preg_replace('/^(\d{3,4})/', '', $style);
		return 'normal';
	}
	function get_font_families(){
		return array_unique(array_filter(array($this->gallery['caption']['font1']['family'],
			$this->gallery['caption']['font2']['family'])));
	}
	function render($preview = false) {
		global $wp;
		$gallery = $this->gallery;
		$classes = array();
		if ($this->gallery['image']['blur'] != 'off')
			$classes []= "asg-" . $this->gallery['image']['blur'] . "-blur";
		if ($this->gallery['image']['bw'] != 'off')
			$classes []= "asg-" . $this->gallery['image']['bw'] . "-bw";
		if ((isset($this->gallery['layout']['align']) && $this->gallery['layout']['align']))
			$classes []= 'asg-align-' . $this->gallery['layout']['align'];
		$font_families = $this->get_font_families();
		if ($font_families){
			echo '<link rel="stylesheet" type="text/css" href="http://fonts.googleapis.com/css?family=' . urlencode(implode('|', $font_families)) . '"></link>';
		}
		$rand = ++ ASG_Gallery::$grid_count;
		require(ASG_PATH . "stylesheet.css.php");
		$images = $this->get_images();
		?>
		<?php if (current_user_can('manage_options') && !$preview && !get_option('asg_hide_buttons', false)): ?>
			<div class="awesome-gallery-edit-wrapper">
				<a href="<?php echo admin_url("post.php?post={$this->id}&action=edit") ?>"
				   class="edit-grid"><?php _e('Edit gallery', 'asg') ?></a>
				<?php if (!is_wp_error($images)): ?>
				<a href="<?php echo admin_url('admin.php?page=asg-image-troubleshooting-easy') ?>"><?php _e('Images not showing?', 'asg') ?></a>
				<?php endif ?>
				<a href="<?php echo admin_url('edit.php?post_type=awesome-gallery&page=support&url=http://' .
				$_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI']) ?>"><?php _e('Ask for support',
						'asg') ?></a>
			</div>
		<?php endif ?>
		<?php if (is_wp_error($images)): ?>
			<?php $messages = $images->get_error_message() ?>
			<?php if (is_array($messages)): ?>
				<?php $messages = implode(', ', $messages) ?>
			<?php endif ?>
			<div class="asg-load-error"><?php _e('Can\'t display images:', 'asg')?> <?php echo esc_html($messages) ?></div>
		<?php else: ?>
			<div class="asg <?php echo implode($classes, ' ') ?>" id="awesome-gallery-<?php echo $this->id ?>-<?php echo $rand ?>" data-slug="<?php echo esc_attr($this->slug) ?>">
				<?php if ($this->options['filters']['enabled']): ?>
					<div <?php $this->render_filters_attributes() ?>>
						<div class="asg-filter"><a href="#" data-tag=""><?php echo $this->options['filters']['all']?></a></div>
						<?php foreach($this->get_tags($images) as $tag): ?>
							<?php if (trim($tag)): ?>
								<div class="asg-filter"><a href="#" data-tag="<?php echo esc_attr(strtolower(trim($tag))) ?>"><?php echo esc_html($tag)?></a></div>
							<?php endif ?>
						<?php endforeach ?>
					</div>
				<?php endif ?>
				<div class="asg-images"><?php $this->render_images($images) ?></div>
				<?php if ($this->options['load_more']['style'] && $this->options['load_more']['style'] != 'off' && count($images) > $this->gallery['load_more']['page_size']): ?>
					<div class="asg-bottom">
						<?php if ($this->options['load_more']['style'] == 'load-more'): ?>
							<div class="asg-load-more <?php echo $this->options['load_more']['width'] == 'full' ? 'asg-full-width' : '' ?>" href="#"><?php _e($this->options['load_more']['load_more_text']) ?></div>
						<?php endif ?>
					</div>
				<?php endif ?>
			</div>
			<script type="text/javascript">
			var initialize = function(){
				setTimeout(function(){new AwesomeGallery("<?php echo $this->id ?>-<?php echo $rand ?>", <?php echo json_encode($this->get_settings($preview))?>);
			}, 1);}
			if (typeof(AwesomeGallery) != 'undefined'){
				initialize();
			}
			else {
				jQuery(initialize);
			}
			</script>
		<?php endif ?>
	<?php
	}

	function render_images($images){
		require(ASG_PATH . "templates/images.php");
	}

	function render_filters_attributes(){
		$attr = array('class' => 'asg-filters');
		$attr['class'] .= " asg-align-" . $this->options['filters']['align'];
		if ($this->options['filters']['sort'])
			$attr['data-sort'] = $this->options['filters']['sort'];
		$attr['data-all'] = $this->options['filters']['all'];
		$attr['data-list'] = trim($this->options['filters']['list']);
		$this->render_attributes($attr);
	}

	function ping() {
		$transient = "asg_ping_" . $this->id;
		if (!get_transient($transient)){
			set_transient($transient, true, 400);
			wp_schedule_single_event(time() + 200, 'asg_refresh_gallery', array('id' => $this->id));
		} 
	}


	function get_settings($preview = false) {
		$error = null;
		if ($binder = get_option('asg_scroll_binder'))
			$load_more['binder'] = $binder;
		return array(
			'id' => $this->id,
			'layout' => array(
				'mode' => $this->gallery['layout']['mode'],
				'width' => (int)$this->gallery['layout']['width'],
				'height' => (int)$this->gallery['layout']['height'],
				'gap' => (int)$this->gallery['layout']['gap'],
				'border' => (int)$this->gallery['border']['width'],
				'allowHanging' => $this->gallery['layout']['hanging'] == 'show',
			),
			'lightbox' => $preview ? array('name' => null) : array(
					'name' => $this->source->get_link_mode() == 'lightbox' ? asg_get_active_lightbox() : null,
					'settings' => $this->get_lightbox_options()),
			'filters' => $this->gallery['filters'],
			'load_more' => $this->gallery['load_more']
		);
	}

	function get_link_attr(){
		return array();
	}

	function get_lightbox_options(){
		switch (asg_get_active_lightbox()){
			case 'prettyphoto':
				return array(
						'theme' => get_option('asg_prettyphoto_theme', 'pp_default'),
						'slideshow' => true,
						'hook' => 'data-rel'
				);
			default:
				return array();
		};
	}


	function get_images($nocache = false) {
		$images = $this->source->get_images(array(
			'width' => $this->gallery['layout']['width'],
			'height' => $this->gallery['layout']['height'],
			'images' => isset($this->gallery['images']) ? $this->gallery['images'] : '',
			'limit' => $this->gallery['load_more']['style'] == 'off' ? $this->gallery['load_more']['page_size'] : null 
		));
		if (is_wp_error($images))
			return $images;
		foreach($images as $image){
			$image->url = $this->get_image_url($url = $image->url);
			$image->thumbnail_url = $this->get_image_url($image->thumbnail_url);
		}
		return $images;
	}
	
	function get_tags($images){
		$tags = array();
		if ($this->gallery['filters']['list']){
			foreach(array_map('trim', explode(',', $this->gallery['filters']['list'])) as $filter)
				$tags [] = $filter;
		} else {
			foreach($images as $image)
				foreach($image->tags  as $tag){
					$tags []= trim($tag);
				}
		}
		return array_unique($tags);
	}
	

	function get_image_url($url, $pixel_ratio = 1){
		switch ($this->gallery['layout']['mode']){
			case 'horizontal-flow':
				$options = array(
					'height' => $this->gallery['layout']['height']
				);
				break;
			case 'vertical-flow':
				$options = array(
					'width' => $this->gallery['layout']['width']
				);
				break;
			default:
				$options = array(
					'width' => ($this->gallery['layout']['width'] - $this->gallery['border']['width']),
					'height' => ($this->gallery['layout']['height'] - $this->gallery['border']['width'])
				);
		}
		$options['pixel_ratio'] = $pixel_ratio;
		return asg_get_image_url($url, $options);
	}

	function get_per_page() {
		return $this->gallery['load_more']['per_page'];
	}

}
