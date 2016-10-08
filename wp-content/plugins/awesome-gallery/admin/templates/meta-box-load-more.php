<p>
	<label><?php _e('Load more style', 'asg')?></label>
	<select name="asg[load_more][style]">
		<option value="off" <?php selected($gallery['load_more']['style'], 'off')?>><?php _e('Disable. Only load the first page', 'asg')?></option>
		<option value="load-more" <?php selected($gallery['load_more']['style'], 'load-more')?>><?php _e('Load more button', 'asg')?></option>
		<option value="endless" <?php selected($gallery['load_more']['style'], 'endless')?>><?php _e('Endless scroll', 'asg')?></option>
	</select>
</p>
<p id="load-more-per-page">
	<label><?php _e('Images per page', 'asg')?></label>
	<input name="asg[load_more][page_size]" value="<?php echo esc_html($gallery['load_more']['page_size']) ?>" type="number">
</p>
<div id="asg-load-more-mode">
	<p>
		<label><?php _e('Load more button text', 'asg')?></label>
		<input name="asg[load_more][load_more_text]" value="<?php echo esc_html($gallery['load_more']['load_more_text']) ?>" type="text">
	</p>
	<p>
		<label><?php _e('Loading text', 'asg')?></label>
		<input name="asg[load_more][loading_text]" value="<?php echo esc_html($gallery['load_more']['loading_text']) ?>" type="text">
	</p>
	<p>
		<label><?php _e('All items loaded', 'asg')?></label>
		<input name="asg[load_more][all_images_loaded]" value="<?php echo esc_html($gallery['load_more']['all_images_loaded']) ?>" type="text">
	</p>
</div>
<p>
	<label><?php _e('Load more width', 'asg') ?></label>
	<select name="asg[load_more][width]">
		<option value="full" <?php selected($gallery['load_more']['width'], 'full')?>>
			<?php _e('Full width', 'asg')?>
		</option>
		<option value="button" <?php selected($gallery['load_more']['width'], 'button')?>>
			<?php _e('Button', 'asg')?>
		</option>
	</select>
</p>
<p>
	<label><?php _e('Background color', 'asg') ?></label>
	<input name="asg[load_more][background_color]"
	       value="<?php echo esc_attr ($gallery['load_more']['background_color']) ?>" type="text" />
</p>
<p>
	<label><?php _e('Background color when all images are loaded', 'asg') ?></label>
	<input name="asg[load_more][background_color_loaded]"
	       value="<?php echo esc_attr ($gallery['load_more']['background_color_loaded']) ?>" type="text"/>
</p>
<p>
	<label><?php _e('Color', 'asg') ?></label>
	<input name="asg[load_more][color]"
	       value="<?php echo esc_attr ($gallery['load_more']['color']) ?>" type="text"/>
</p>
<p>
	<label><?php _e('Color when all images are loaded', 'asg') ?></label>
	<input name="asg[load_more][color_loaded]"
	       value="<?php echo esc_attr ($gallery['load_more']['color_loaded']) ?>" type="text"/>
</p>
<p>
	<label><?php _e('Shadow width', 'asg') ?></label>
	<input name="asg[load_more][shadow_width]"
	       value="<?php echo esc_attr ($gallery['load_more']['shadow_width']) ?>" type="text"/>
</p>
<p>
	<label><?php _e('Shadow color', 'asg') ?></label>
	<input name="asg[load_more][shadow_color]"
	       value="<?php echo esc_attr ($gallery['load_more']['shadow_color']) ?>" type="text"/>
</p>
<p>
	<label><?php _e('Border radius', 'asg') ?></label>
	<input name="asg[load_more][border_radius]" type="text"
	       value="<?php echo esc_attr($gallery['load_more']['border_radius']) ?>">
</p>
<p>
	<label><?php _e('Horizontal padding', 'asg') ?></label>
	<input name="asg[load_more][horizontal_padding]" type="number"
	       value="<?php echo esc_attr($gallery['load_more']['horizontal_padding']) ?>">
</p>
<p>
	<label><?php _e('Vertical padding', 'asg') ?></label>
	<input name="asg[load_more][vertical_padding]" type="number"
	       value="<?php echo esc_attr($gallery['load_more']['vertical_padding']) ?>">
</p>
