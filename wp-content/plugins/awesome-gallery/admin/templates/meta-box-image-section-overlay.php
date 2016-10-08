<p id="overlay-type">
	<label><?php _e('Overlay type', 'asg')?></label>
	<select name="asg[overlay][mode]">
		<option value="off" <?php selected($gallery['overlay']['mode'], 'off') ?>><?php _e('Off', 'asg')?></option>
		<option value="on-hover" <?php selected($gallery['overlay']['mode'], 'on-hover') ?>><?php _e('On-hover', 'asg')?></option>
		<option value="off-hover" <?php selected($gallery['overlay']['mode'], 'off-hover') ?>><?php _e('Off-hover', 'asg')?></option>
		<option value="on" <?php selected($gallery['overlay']['mode'], 'on') ?>><?php _e('Constantly on', 'asg')?></option>
	</select>
</p>
<p id="overlay-color">
	<label><?php _e('Overlay color', 'asg')?></label>
	<input type="text" name="asg[overlay][color]" value="<?php echo esc_attr($gallery['overlay']['color']) ?>" placeholder="#000">
</p>
<p id="overlay-opacity">
	<label><?php _e('Overlay opacity', 'asg')?></label>
	<input type="number" name="asg[overlay][opacity]" value="<?php echo esc_attr($gallery['overlay']['opacity']) ?>" step="any" placeholder="0.3" max="1" min="0">
</p>
<p id="overlay-type">
	<label><?php _e('Overlay effect', 'asg')?></label>
	<select name="asg[overlay][effect]">
		<option value="none" <?php selected($gallery['overlay']['effect'], 'none') ?>><?php _e('None', 'asg')?></option>
		<option value="fade" <?php selected($gallery['overlay']['effect'], 'fade') ?>><?php _e('Fade', 'asg')?></option>
		<option value="slide" <?php selected($gallery['overlay']['effect'], 'slide') ?>><?php _e('Slide', 'asg')?></option>
	</select>
</p>

<div id="overlay-image" class="field">
	<label><?php _e('Overlay image', 'asg')?></label>
	<div class="image-selector no-image">
		<input name="asg[overlay][image]" value="<?php echo $gallery['overlay']['image'] ?>" type="hidden">
		<?php if ($gallery['overlay']['image']): ?>
			<img src="<?php echo esc_url(asg_get_wp_image_src($gallery['overlay']['image'])) ?>">
		<?php endif ?>
		<div class="overlay"></div>
		<div class="actions-wrapper">
			<button class="select-image button "><?php _e('Select image', 'asg')?></button>
			<br>
			<a href="#" class="image-delete"><?php _e('Remove image', 'asg')?></a>
		</div>
	</div>
	<br class="clear">
</div>
