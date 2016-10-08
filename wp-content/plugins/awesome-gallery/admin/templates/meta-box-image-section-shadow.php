<p>
	<label><?php _e('Show', 'asg') ?></label>
	<select name="asg[shadow][mode]">
		<option value="off"  <?php selected($gallery['shadow']['mode'], 'off') ?>>
			<?php _e('Do not show', 'asg') ?>
		</option>
		<option value="on"  <?php selected($gallery['shadow']['mode'], 'on') ?>>
			<?php _e('Show all the time', 'asg') ?>
		</option>
		<option value="on-hover"  <?php selected($gallery['shadow']['mode'], 'on-hover') ?>>
			<?php _e('Show on hover', 'asg') ?>
		</option>
		<option value="off-hover"  <?php selected($gallery['shadow']['mode'], 'off-hover') ?>>
			<?php _e('Hide on hover', 'asg') ?>
		</option>
	</select>
<p>
	<label><?php _e('Radius', 'asg')?></label>
	<input type="number" name="asg[shadow][radius]" value="<?php echo esc_attr($gallery['shadow']['radius']) ?>" placeholder="3" step="1">
</p>
<p>
	<label><?php _e('Opacity', 'asg')?></label>
	<input type="number" name="asg[shadow][opacity]" value="<?php echo esc_attr($gallery['shadow']['opacity']) ?>" placeholder="0.2" step="0.01">
</p>
<p>
	<label><?php _e('Color', 'asg')?></label>
	<input type="text" name="asg[shadow][color]" value="<?php echo esc_attr($gallery['shadow']['color']) ?>" placeholder="#000000">
</p>
