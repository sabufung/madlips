<li>
	<select name="asg[sources][posts][meta_keys][]">
		<?php foreach($this->get_custom_field_values() as $result): ?>
			<option value="<?php echo esc_attr($result->meta_key )?>" <?php selected(isset($filter['meta_key']) ? $filter['meta_key'] : null, $result->meta_key)?>><?php echo esc_html($result->meta_key)?></option>
		<?php endforeach ?>
	</select>
	<select name="asg[sources][posts][meta_operators][]">
		<option value="=" <?php selected(isset($filter['meta_operator']) ? $filter['meta_operator'] : null, '=')?>>=</option>
		<option value="!=" <?php selected(isset($filter['meta_operator']) ? $filter['meta_operator'] : null, '!=')?>>!=</option>
		<option value="<" <?php selected(isset($filter['meta_operator']) ? $filter['meta_operator'] : null, '<')?>>&lt;</option>
		<option value=">" <?php selected(isset($filter['meta_operator']) ? $filter['meta_operator'] : null, '>')?>>&gt;</option>
		<option value="<=" <?php selected(isset($filter['meta_operator']) ? $filter['meta_operator'] : null, '<=')?>>&lt;=</option>
		<option value=">=" <?php selected(isset($filter['meta_operator']) ? $filter['meta_operator'] : null, '>=')?>>&gt;=</option>
	</select>
	<input type="text" name="asg[sources][posts][meta_values][]" placeholder="<?php _e('value', 'asg') ?>" value="<?php echo esc_attr(isset($filter['meta_value']) ? $filter['meta_value'] : null)?>">
	<select name="asg[sources][posts][meta_types][]">
		<option value="CHAR" <?php selected(isset($filter['meta_type']) ? $filter['meta_type'] : null, 'CHAR')?>>CHAR</option>
		<option value="NUMERIC" <?php selected(isset($filter['meta_type']) ? $filter['meta_type'] : null, 'NUMERIC')?>>NUMERIC</option>
		<option value="BINARY" <?php selected(isset($filter['meta_type']) ? $filter['meta_type'] : null, 'BINARY')?>>BINARY</option>
		<option value="DATE" <?php selected(isset($filter['meta_type']) ? $filter['meta_type'] : null, 'DATE')?>>DATE</option>
		<option value="DATETIME" <?php selected(isset($filter['meta_type']) ? $filter['meta_type'] : null, 'DATETIME')?>>DATETIME</option>
		<option value="DECIMAL" <?php selected(isset($filter['meta_type']) ? $filter['meta_type'] : null, 'DECIMAL')?>>DECIMAL</option>
	</select>
	<button class="button remove"><?php _e('Remove', 'asg')?></button>
</li>
