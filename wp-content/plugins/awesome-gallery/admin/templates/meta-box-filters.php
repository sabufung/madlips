<p id="asg-filters-mode">
	<label><?php _e('Enabled', 'asg')?></label>
	<select name="asg[filters][enabled]">
		<option value="1" <?php selected($gallery['filters']['enabled'], 1)?>><?php _e('Enable', 'asg')?></option>
		<option value="" <?php selected($gallery['filters']['enabled'], false)?>><?php _e('Disable', 'asg')?></option>
	</select>
</p>
<p id="asg-filters-alignment">
	<label><?php _e('Align', 'asg')?></label>
	<select name="asg[filters][align]">
		<option value="left" <?php selected($gallery['filters']['align'], 'left')?>><?php _e('Left', 'asg')?></option>
		<option value="right" <?php selected($gallery['filters']['align'], 'right')?>><?php _e('Right', 'asg')?></option>
		<option value="center" <?php selected($gallery['filters']['align'], 'center')?>><?php _e('Center', 'asg')?></option>
	</select>
</p>
<p id="asg-filters-sorting">
	<label><?php _e('Sorting', 'asg')?></label>
	<select name="asg[filters][sort]">
		<option value="1" <?php selected($gallery['filters']['sort'], 1)?>><?php _e('Enable', 'asg')?></option>
		<option value="" <?php selected($gallery['filters']['sort'], false)?>><?php _e('Disable', 'asg')?></option>
	</select>
</p>
<p><label><?php _e('Color: ', 'uber-grid')?></label><input type="text" name="asg[filters][color]" value="<?php echo esc_attr($gallery['filters']['color']) ?>"></p>
<p><label><?php _e('Background color: ', 'uber-grid')?></label><input type="text" name="asg[filters][background_color]" value="<?php echo esc_attr($gallery['filters']['background_color']) ?>"></p>
<p><label><?php _e('Accent color: ', 'uber-grid')?></label><input type="text" name="asg[filters][accent_color]" value="<?php echo esc_attr($gallery['filters']['accent_color']) ?>"></p>
<p><label><?php _e('Accent background color: ', 'uber-grid')?></label><input type="text" name="asg[filters][accent_background_color]" value="<?php echo esc_attr($gallery['filters']['accent_background_color']) ?>"></p>
<p><label><?php _e('Border radius: ', 'uber-grid')?></label><input type="text" name="asg[filters][border_radius]" value="<?php echo esc_attr($gallery['filters']['border_radius']) ?>"></p>
<p id="asg-filters-all">
	<label><?php _e('All wording', 'asg')?></label>
	<input type="text" name="asg[filters][all]" value="<?php echo esc_attr($gallery['filters']['all']) ?>">
</p>
<p id="asg-filters-list">
	<label><?php _e('Only show filters:', 'asg') ?></label>
	<textarea name="asg[filters][list]"><?php echo esc_textarea(trim($gallery['filters']['list'])) ?></textarea>
	<small><?php _e('Comma-separated filter list', 'asg') ?></small>
</p>