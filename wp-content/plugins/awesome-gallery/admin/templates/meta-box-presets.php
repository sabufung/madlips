<div class="field">
	<label><?php _e('Load preset:', 'asg') ?></label>
	<select name="preset">
		<?php foreach ( asg_get_presets() as $key => $preset): ?>
			<option value="<?php echo esc_attr($key) ?>"><?php echo esc_html($preset['name']) ?></option>
		<?php endforeach ?>
	</select>
</div>
<div class="field">
	<button class="button"><?php _e('Load the preset', 'asg') ?></button>
	<br class="clear" />
</div>
