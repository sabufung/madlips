<?php $id = (int)$this->id . "-" . $rand ?>
<style type="text/css">
	<?php if ((int)$gallery['border']['width'] > 0 || (int)$gallery['border']['radius'] > 0): ?>
		#awesome-gallery-<?php echo $id ?> .asg-image {
		<?php if ((int)$gallery['border']['width'] > 0): ?> border-style: solid;
			border-width: <?php echo $gallery['border']['width']?>px;
			border-color: <?php echo asg_color($gallery['border']['color']) ?>;
		<?php endif ?>
		<?php if ((int)$gallery['border']['radius'] > 0): ?> border-radius: <?php echo (int)$gallery['border']['radius']?>px;
			-o-border-radius: <?php echo (int)$gallery['border']['radius']?>px;
			-ms-border-radius: <?php echo (int)$gallery['border']['radius']?>px;
			-moz-border-radius: <?php echo (int)$gallery['border']['radius']?>px;
			-webkit-border-radius: <?php echo (int)$gallery['border']['radius']?>px;
		<?php endif ?>
		}
	<?php endif ?>

	<?php if ((int)$gallery['border']['width'] >0  || (int)$gallery['border']['width2'] > 0): ?>
		#awesome-gallery-<?php echo $id ?> .asg-image .asg-image-wrapper{
			border-style: solid;
			border-width: <?php echo $gallery['border']['width2']?>px;
			border-color: <?php echo asg_color($gallery['border']['color2']) ?>;
	}
	<?php if ((int)$gallery['border']['radius'] > 0): ?>
		#awesome-gallery-<?php echo $id ?> .asg-image .asg-image-wrapper,
		#awesome-gallery-<?php echo $id ?> .asg-image .asg-image-overlay{
			border-radius: <?php echo (int)$gallery['border']['radius'] - (int)$gallery['border']['width2'] - 1 ?>px;
			-o-border-radius: <?php echo (int)$gallery['border']['radius'] - (int)$gallery['border']['width2'] - 1?>px;
			-ms-border-radius: <?php echo (int)$gallery['border']['radius'] - (int)$gallery['border']['width2'] - 1 ?>px;
			-moz-border-radius: <?php echo (int)$gallery['border']['radius'] - (int)$gallery['border']['width2'] - 1?>px;
			-webkit-border-radius: <?php echo (int)$gallery['border']['radius'] - (int)$gallery['border']['width'] - 1?>px;

		}
	<?php if ((int)$gallery['border']['width'] > 0 && (int)$gallery['border']['width2'] == 0 ||
		(int)$gallery['border']['width2'] > 0 && (int)$gallery['border']['width'] == 0): ?>
	#awesome-gallery-<?php echo $id ?> .asg-image .asg-image-wrapper img{
		-webkit-border-radius: <?php echo (int)$gallery['border']['radius'] - ((int)$gallery['border']['width2']) - (int)$gallery['border']['width'] - 1 ?>px;
	}
	<?php endif ?>
	<?php endif ?>
	<?php endif ?>
	<?php if (in_array($gallery['shadow']['mode'], array('off-hover', 'on-hover'))): ?>
		#awesome-gallery-<?php echo $id ?> .asg-image{
			-webkit-transition: 0.2s all;
			-moz-transition: 0.2s all;
			-ms-transition: 0.2s all;
			-o-transition: 0.2s all;
			transition: 0.2s all;
		}
	<?php endif ?>
	<?php if (in_array($gallery['shadow']['mode'], array('on', 'off-hover'))): ?>
		#awesome-gallery-<?php echo $id ?> .asg-image{
			box-shadow: 0px 0px <?php echo (int)$gallery['shadow']['radius'] ?>px  <?php echo asg_color($gallery['shadow']['color'], $gallery['shadow']['opacity']) ?>;

		}
		<?php if ($gallery['shadow']['mode'] == 'off-hover'): ?>
		#awesome-gallery-<?php echo $id ?> .asg-image:hover{
			box-shadow: none;
		}
	    <?php endif ?>
	<?php endif ?>
	<?php if ('on-hover' == $gallery['shadow']['mode']): ?>
		#awesome-gallery-<?php echo $id ?> .asg-image:hover{
			box-shadow: 0px 0px <?php echo (int)$gallery['shadow']['radius'] ?>px  <?php echo asg_color($gallery['shadow']['color'], $gallery['shadow']['opacity']) ?>;
		}
	<?php endif ?>
	#awesome-gallery-<?php echo $id ?> .asg-filters .asg-filter a {
		color: <?php echo asg_color($gallery['filters']['color'])?>;
		background-color: <?php echo asg_color($gallery['filters']['background_color'])?>;
		<?php if ((int)$gallery['filters']['border_radius'] > 0): ?> border-radius: <?php echo (int)$gallery['filters']['border_radius']?>px;
			-o-border-radius: <?php echo (int)$gallery['filters']['border_radius']?>px;
			-ms-border-radius: <?php echo (int)$gallery['filters']['border_radius']?>px;
			-moz-border-radius: <?php echo (int)$gallery['filters']['border_radius']?>px;
			-webkit-border-radius: <?php echo (int)$gallery['filters']['border_radius']?>px;
		<?php endif ?>

	}

	#awesome-gallery-<?php echo $id?> .asg-filters .asg-filter.asg-active a {
		color: <?php echo asg_color($gallery['filters']['accent_color'])?>;
		background-color: <?php echo asg_color($gallery['filters']['accent_background_color'])?>;
	}

	#awesome-gallery-<?php echo $id?> .asg-image-overlay {
		background-color: <?php echo asg_color($gallery['overlay']['color']) ?>;
		background-color: <?php echo asg_color($gallery['overlay']['color'], $gallery['overlay']['opacity']) ?>;
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo (float)$gallery['overlay']['opacity'] * 100 ?>)";
		filter: alpha(opacity=<?php echo (float)$gallery['overlay']['opacity'] * 100 ?>);
	}
	<?php if ($gallery['overlay']['image']): ?>
		#awesome-gallery-<?php echo $id?> .asg-image-overlay{
			<?php $image = wp_get_attachment_image_src($gallery['overlay']['image'], 'original')?>
			background-image: url('<?php echo asg_get_image_url(preg_replace("%^" . preg_quote(home_url()) . "%", '', $image[0]), array('width' => $image[1] / 2.0, 'height' => $image[2] / 2.0)) ?>');
			background-position: center center;
			background-repeat: no-repeat;
		}
		@media only screen and (min--moz-device-pixel-ratio: 2),
			only screen and (-o-min-device-pixel-ratio: 2/1),
			only screen and (-webkit-min-device-pixel-ratio: 2),
			only screen and (min-device-pixel-ratio: 2){
			#awesome-gallery-<?php echo $id?> .asg-image-overlay{
				<?php $image = wp_get_attachment_image_src($gallery['overlay']['image'], 'original')?>
				background-image: url('<?php echo $image[0] ?>');
				background-size: <?php echo $image[1] / 2.0 ?>px <?php echo $image[2] / 2.0 ?>px;
			}
		}

	<?php endif ?>
	#awesome-gallery-<?php echo $id?> .asg-image-caption-wrapper {
		text-align: <?php echo $gallery['caption']['align']?>;
		background-color: <?php echo asg_color($gallery['caption']['background_color']) ?>;
		<?php if (!($gallery['caption']['position'] == 'bottom' && $gallery['caption']['effect'] == 'slide' &&
			$gallery['caption']['mode'] == 'on-hover')): ?>
		background-color: <?php echo asg_color($gallery['caption']['background_color'], $gallery['caption']['opacity']) ?>;
		-ms-filter: "progid:DXImageTransform.Microsoft.Alpha(Opacity=<?php echo (float)$gallery['caption']['opacity'] * 100 == 0 ? 50 : (float)$gallery['caption']['opacity'] * 100  ?>)";
		filter: alpha(opacity=<?php echo (float)$gallery['caption']['opacity'] * 100 == 0 ? 50 : (float)$gallery['caption']['opacity'] * 100  ?>);
		<?php endif ?>
	}

	<?php if ($gallery['caption']['mode'] == 'on-hover' && $gallery['caption']['effect'] == 'fade' || $gallery['caption']['effect'] == 'none'): ?>
		#awesome-gallery-<?php echo $id?> .asg-image : hover .asg-image-caption-wrapper {
			opacity: 1;
		}
	<?php endif ?>
	<?php if ($gallery['caption']['mode'] == 'off-hover' && $gallery['caption']['effect'] == 'fade'): ?>
		#awesome-gallery-<?php echo $id?> .asg-image : hover .asg-image-caption-wrapper {
			opacity: 0;
		}
	<?php endif ?>
	#awesome-gallery-<?php echo $id?> .asg-image-caption1,
	#awesome-gallery-<?php echo $id?> .asg-image-caption1 a{
		color: <?php echo asg_color($gallery['caption']['color'])?>;
	}

	#awesome-gallery-<?php echo $id?> .asg-image-caption2,
	#awesome-gallery-<?php echo $id?> .asg-image-caption2 a{
		color: <?php echo asg_color($gallery['caption']['color2'])?>;
	}

	#awesome-gallery-<?php echo $id ?> .asg-bottom .asg-load-more{
		background-color: <?php echo asg_color($gallery['load_more']['background_color'])?>;
		color: <?php echo asg_color($gallery['load_more']['color'])?>;
		<?php if ((int)$gallery['load_more']['shadow_width'] > 0): ?> box-shadow: <?php echo (int)$gallery['load_more']['shadow_width'] / 2.0 ?>px <?php echo
			$gallery['load_more']['shadow_width'] ?>px 0 0 <?php echo asg_color($gallery['load_more']['shadow_color'])?>;
		<?php endif ?>
	}

	#awesome-gallery-<?php echo $id ?> .asg-bottom .asg-all-loaded {
		border-radius: 0;
		-webkit-border-radius: 0;
		-moz-border-radius: 0;
		-ms-border-radius: 0;
		-o-border-radius: 0;
	}

	#awesome-gallery-<?php echo $id ?> .asg-bottom .asg-all-loaded {
		background-color: <?php echo asg_color($gallery['load_more']['background_color_loaded'])?>;
		color: <?php echo asg_color($gallery['load_more']['color_loaded']) ?>;
	}

	#awesome-gallery-<?php echo $id ?> .asg-bottom .asg-all-loaded {
		border-bottom-color: <?php echo asg_color($gallery['load_more']['shadow_color_loaded']) ?>;
	}

	#awesome-gallery-<?php echo $id ?> .asg-bottom > div {
		-webkit-border-radius: <?php echo $gallery['load_more']['border_radius'] ?>px;
		-moz-border-radius: <?php echo $gallery['load_more']['border_radius'] ?>px;
		-ms-border-radius: <?php echo $gallery['load_more']['border_radius'] ?>px;
		-o-border-radius: <?php echo $gallery['load_more']['border_radius'] ?>px;
		border-radius: <?php echo $gallery['load_more']['border_radius'] ?>px;

		padding: <?php echo (int)$gallery['load_more']['vertical_padding'] ?>px <?php echo (int)
$gallery['load_more']['horizontal_padding'] ?>px;
	}

	<?php if ($font_families || $gallery['caption']['font1']['style'] || $gallery['caption']['font2']['style'] ||
	$gallery['caption']['font1']['size'] || $gallery['caption']['font2']['size']): ?>
	#awesome-gallery-<?php echo $id ?> .asg-image-caption1 {
		<?php if ($this->gallery['caption']['font1']['family']): ?> font-family: "<?php echo $this->gallery['caption']['font1']['family'] ?>", 'Helvetica Neue', Helvetica, sans-serif;
		<?php endif ?>
		<?php if ($this->gallery['caption']['font1']['style']): ?> font-weight: <?php echo $this->parse_font_weight($this->gallery['caption']['font1']['style']) ?>;
			font-style: <?php echo $this->parse_font_style($this->gallery['caption']['font1']['style'])?>;
		<?php endif ?>
		<?php if ($this->gallery['caption']['font1']['size']): ?> font-size: <?php echo $this->gallery['caption']['font1']['size'] ?>px;
			line-height: 1.4;
		<?php endif ?>
	}
	<?php endif ?>

	#awesome-gallery-<?php echo $id ?> .asg-image-caption2 {
	<?php if ($this->gallery['caption']['font2']['family']): ?> font-family: "<?php echo $this->gallery['caption']['font2']['family'] ?>", 'Helvetica Neue', Helvetica, sans-serif;
	<?php endif ?> <?php if ($this->gallery['caption']['font2']['style']): ?> font-weight: <?php echo $this->parse_font_weight($this->gallery['caption']['font2']['style']) ?>;
		font-style: <?php echo $this->parse_font_style($this->gallery['caption']['font2']['style'])?>;
	<?php endif ?> <?php if ($this->gallery['caption']['font2']['size']): ?> font-size: <?php echo $this->gallery['caption']['font2']['size'] ?>px;
		line-height: 1.4;
	<?php endif ?>
	}

	<?php foreach(asg_get_custom_css_sections() as $title => $section): ?>
		<?php foreach($section as $name => $data): ?>
			<?php if (trim($this->gallery['custom_css'][$name])): ?>
				#awesome-gallery-<?php echo $id ?> <?php echo $data['selector'] ?>{
					<?php echo $this->gallery['custom_css'][$name] ?>
				}
			<?php endif ?>
		<?php endforeach ?>
	<?php endforeach ?>



</style>
