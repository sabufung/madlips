<li>
	<?php global $wp_taxonomies ?>
	<select name="asg[sources][posts][taxonomies][]">
		<?php foreach($wp_taxonomies as $taxonomy => $args): ?>
			<option value="<?php echo esc_attr($taxonomy) ?>" <?php selected($taxonomy, $filter['taxonomy'])?>><?php echo esc_html((isset($args->label) && isset($args->labels->name)) ? $args->labels->name : $taxonomy) ?></option>
		<?php endforeach ?>
	</select>
	<input type="text" name="asg[sources][posts][tags][]" placeholder="<?php _e('comma-separated term slugs', 'asg')?>" value="<?php echo esc_attr($filter['tags']) ?>" style="min-width: 220px;">
	<select name="asg[sources][posts][operators][]">
		<option value="IN" <?php selected('IN', isset($filter['operator']) ? $filter['operator'] : null) ?>>IN</option>
		<option value="NOT IN" <?php selected('NOT IN', isset($filter['operator']) ? $filter['operator'] : null) ?>>NOT IN</option>
		<option value="OR" <?php selected('OR', isset($filter['operator']) ? $filter['operator'] : null) ?>>OR</option>
		<option value="AND" <?php selected('AND', isset($filter['operator']) ? $filter['operator'] : null) ?>>AND</option>
	</select>
	<button class="button remove"><?php _e('Remove', 'asg')?></button>
</li>
