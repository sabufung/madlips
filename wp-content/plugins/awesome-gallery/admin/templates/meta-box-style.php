<p>
	<label><?php _e('Style', 'asg')?></label>
	<select name="asg[style][style]" value="<?php echo $gallery['style']['style'] ?>">
		<option value="default" <?php selected($gallery['style']['style'], 'default')?>><?php _e('Default', 'asg')?></option>
		<option value="custom" <?php selected($gallery['style']['style'], 'custom')?>><?php _e('Custom CSS', 'asg')?></option>
		<option value="builder" <?php selected($gallery['style']['style'], 'builder')?>><?php _e('Use builder', 'asg')?></option>
		<?php foreach(asg_get_style_presets() as $preset): ?>
			<option value="<?php echo esc_attr($preset) ?>" <?php selected($gallery['style']['style'], $preset)?>><?php echo esc_html($preset) ?></option>
		<?php endforeach ?>
	</select>
</p>