<p>
	<label><?php _e('Layout mode', 'asg')?></label>
	<select name="asg[layout][mode]" id="asg-layout-mode">
		<option value="horizontal-flow" <?php selected($gallery['layout']['mode'], 'horizontal-flow')?>><?php _e('Horizontal flow', 'asg')?></option>
		<option value="vertical-flow" <?php selected($gallery['layout']['mode'], 'vertical-flow')?>><?php _e('Vertical flow', 'asg')?></option>
		<option value="usual" <?php selected($gallery['layout']['mode'], 'usual')?>><?php _e('Grid', 'asg')?></option>
	</select>
</p>
<p id="asg-image-width">
	<label><?php _e('Image width', 'asg')?></label>
	<input type="number" name="asg[layout][width]" value="<?php echo esc_attr($gallery['layout']['width']) ?>" placeholder="240">px
</p>
<p id="asg-image-height">
	<label><?php _e('Image height', 'asg')?></label>
	<input type="number" name="asg[layout][height]" value="<?php echo esc_attr($gallery['layout']['height']) ?>" placeholder="190">px
</p>
<p id="asg-image-gap">
	<label><?php _e('Gap', 'asg')?></label>
	<input type="number" name="asg[layout][gap]" value="<?php echo esc_attr($gallery['layout']['gap']) ?>" placeholder="190">px
</p>
<p id="asg-image-alignment">
	<label><?php _e('Alignment', 'asg') ?></label>
	<select name="asg[layout][align]">
		<option value="center" <?php selected($gallery['layout']['align'], 'center') ?>><?php _e('Center', 'asg') ?></option>
		<option value="left" <?php selected($gallery['layout']['align'], 'left') ?>><?php _e('Left', 'asg') ?></option>
		<option value="right" <?php selected($gallery['layout']['align'], 'right') ?>><?php _e('Right', 'asg') ?></option>
	</select>
</p>
<p id="asg-disallow-hanging-images">
	<label><?php _e('Hanging images', 'asg')?></label>
	<select name="asg[layout][hanging]">
		<option value="show" <?php selected($gallery['layout']['hanging'], 'show')?>><?php _e('Show', 'asg')?></option>
		<option value="hide" <?php selected($gallery['layout']['hanging'], 'hide')?>><?php _e('Hide', 'asg')?></option>
	</select>
</p>
