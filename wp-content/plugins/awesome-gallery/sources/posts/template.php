<ul class="steps">
	<li id="nextgen-select-gallery-block">
		<h2><span class="step-number">1.</span><?php _e('Setup post type and filtering') ?></h2>
		<div id="post-settings">
			<h3><?php _e('Post filtering', 'asg')?></h3>
			<div class="inside">
				<div class="section" id="posts-post-type">
					<label for="auto-post-type" class="larger"><?php _e('Post type')?></label>
					<div class="column-1">
						<select name="asg[sources][posts][post_type]">
							<?php global $wp_post_types?>
							<?php foreach($wp_post_types as $type => $args): ?>
								<?php if (!in_array($type, array('revision', 'nav_menu_item', 'asg'))): ?>
									<option value="<?php echo esc_attr($type) ?>" <?php selected($source['post_type'], $type) ?>><?php echo (isset($args->labels) && isset($args->labels->plural_name)) ? esc_html($args->labels->plural_name) : (isset($args->label) ? esc_html($args->label): $type) ?></option>
								<?php endif ?>
							<?php endforeach ?>
						</select>
						<?php _e('order by', 'asg')?>
						<select name="asg[sources][posts][orderby]">
							<?php foreach(array('date' => __('Date', 'asg'), 'none' => __('None', 'asg'), 'ID' => 'ID', 'author' => __('Author', 'asg'), 'title' => __('Title', 'asg'), 'name' => __('Name', 'asg'), 'modified' => __('Modification date', 'asg'), 'rand' => __('Rand', 'asg'), 'comment_count' => __('Comment count', 'asg'), 'menu_order' => __('Menu position')) as $value => $label): ?>
								<option value="<?php echo esc_attr($value) ?>" <?php selected($source['orderby'], $value) ?>><?php echo esc_html($label) ?></option>
							<?php endforeach ?>
						</select>
						<select name="asg[sources][posts][order]">
							<option value="DESC" <?php selected($source['order'], 'DESC')?>>DESC</option>
							<option value="ASC" <?php selected($source['order'], 'ASC')?>>ASC</option>
						</select>
					</div>
					<br class="clear">
				</div>
				<div class="section" id="posts-taxonomy-filters">
					<label class="larger"><?php _e('Tagged with', 'asg')?></label>
					<div class="column-1">
						<ul class="filters">
							<?php for ($i = 0; $i < count($source['taxonomies']); $i++ ):?>
								<?php $filter = array('taxonomy' => $source['taxonomies'][$i], 'tags' => $source['tags'][$i], 'operator' => $source['operators'][$i]) ?>
								<?php require('filter-taxonomy.php') ?>
							<?php endfor ?>
						</ul>
						<button class="button add" ><?php _e('Add filter', 'asg')?></button>
					</div>
					<br class="clear">
				</div>
				<div class="section" id="posts-custom-field-filters">
					<label class="larger"><?php _e('Custom fields filters', 'asg')?></label>
					<div class="column-1">
						<ul>
							<?php for ($i = 0; $i < count($source['meta_operators']); $i++ ):?>
								<?php $filter = array(
									'meta_key' => $source['meta_keys'][$i],
									'meta_value' => $source['meta_values'][$i],
									'meta_operator' => $source['meta_operators'][$i],
									'meta_type' => $source['meta_types'][$i]) ?>
								<?php require('filter-custom-field.php') ?>
							<?php endfor ?>

						</ul>
						<button class="button add" ><?php _e('Add filter', 'asg')?></button>
					</div>
					<br class="clear">
				</div>
				<div class="section" id="posts-ids">
					<label class="larger"><?php _e('Include / exclude individual posts', 'asg') ?></label>
					<div class="column-1">
						<select name="asg[sources][posts][include_exclude]">
							<option value="include" <?php selected($source['include_exclude'], 'include') ?>><?php _e('Include selected IDs', 'asg') ?></option>
							<option value="exclude" <?php selected($source['include_exclude'], 'exclude') ?>><?php _e('Exclude selected IDs', 'asg') ?></option>
						</select>
						<br>
						<input name="asg[sources][posts][ids]" value="<?php echo esc_attr($source['ids']) ?>" placeholder="<?php _e('Comma-separated IDs hera', 'asg') ?>" type="text">
					</div>
				</div>
			</div>
		</div>
	</li>
	<li>
		<h2><span class="step-number">2.</span> <?php _e('Take images from', 'asg')?>.</h2>
		<p>
			<label class="wizard-label"><?php _e('Image source', 'asg') ?>:</label>
			<select name="asg[sources][posts][source]" value="<?php echo esc_attr($source['source']) ?>">
				<option value="featured" <?php selected($source['source'], 'featured') ?>><?php _e('Post featured image', 'asg') ?></option>
				<option value="attachments" <?php selected($source['source'], 'attachments') ?>><?php _e('Attached images', 'asg') ?></option>
			</select>
		</p>
		<p>
			<label class="wizard-label"><?php _e('Fetch tags from taxonomy', 'asg') ?></label>
			<select name="asg[sources][posts][tags_taxonomy]">
				<?php global $wp_taxonomies ?>
					<?php foreach($wp_taxonomies as $taxonomy => $args): ?>
						<option value="<?php echo esc_attr($taxonomy) ?>" <?php selected($taxonomy, $source['tags_taxonomy'])?>><?php echo esc_html((isset($args->label) && isset($args->labels->name)) ? $args->labels->name : $taxonomy) ?></option>
					<?php endforeach ?>
			</select>
		</p>
	</li>
	<li id="posts-settings-block">
		<div class="action-button">
			<a class="button button-hero" href="#"><?php _e('Preview', 'asg')?></a>
		</div>
		<h2><span class="step-number">3.</span> <?php _e('Adjust settings', 'asg')?>.</h2>
		<p>
			<label class="wizard-label"><?php _e('CAPTION LINE 1', 'asg') ?>:</label>
			<select name="asg[sources][posts][caption_1]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
				<option value="" <?php echo selected($source['caption_1'], '')?>><?php _e('None', 'asg')?></option>
				<option value="title" <?php echo selected($source['caption_1'], 'title')?>><?php _e('Title', 'asg')?></option>
				<option value="excerpt" <?php echo selected($source['caption_1'], 'excerpt')?>><?php _e('Excerpt', 'asg')?></option>
				<option value="content" <?php echo selected($source['caption_1'], 'content')?>><?php _e('Content', 'asg')?></option>
				<option value="datetime" <?php echo selected($source['caption_1'], 'datetime')?>><?php _e('Date + time', 'asg')?></option>
				<option value="date" <?php echo selected($source['caption_1'], 'date')?>><?php _e('Date', 'asg')?></option>
				<option value="tags" <?php echo selected($source['caption_1'], 'date')?>><?php _e('Tags', 'asg')?></option>
				<?php foreach($this->get_custom_field_values() as $value): ?>
					<option value="custom_field_<?php echo esc_attr($value->meta_key )?>" <?php selected($source['caption_1'], "custom_field_" . $value->meta_key)?>><?php _e('Custom field:', 'asg') ?><?php echo esc_html($value->meta_key)?></option>
				<?php endforeach ?>
			</select>
		</p>
		<p>
			<label class="wizard-label">CAPTION LINE 2:</label>
			<select name="asg[sources][posts][caption_2]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
				<option value="" <?php echo selected($source['caption_2'], '')?>><?php _e('None', 'asg')?></option>
				<option value="title" <?php echo selected($source['caption_2'], 'title')?>><?php _e('Title', 'asg')?></option>
				<option value="excerpt" <?php echo selected($source['caption_2'], 'excerpt')?>><?php _e('Excerpt', 'asg')?></option>
				<option value="content" <?php echo selected($source['caption_2'], 'content')?>><?php _e('Content', 'asg')?></option>
				<option value="datetime" <?php echo selected($source['caption_2'], 'datetime')?>><?php _e('Date + time', 'asg')?></option>
				<option value="date" <?php echo selected($source['caption_2'], 'date')?>><?php _e('Date', 'asg')?></option>
				<option value="tags" <?php echo selected($source['caption_2'], 'date')?>><?php _e('Tags', 'asg')?></option>
				<?php foreach($this->get_custom_field_values() as $value): ?>
					<option value="custom_field_<?php echo esc_attr($value->meta_key )?>" <?php selected($source['caption_2'], "custom_field_" . $value->meta_key)?>><?php _e('Custom field:', 'asg') ?><?php echo esc_html($value->meta_key)?></option>
				<?php endforeach ?>
			</select>
		</p>
		<p>
			<label class="wizard-label"><?php _e('LINK MODE', 'asg')?></label>
			<select name="asg[sources][posts][link]" id="posts-link-type" data-value="model.link">
				<option value="lightbox" <?php selected($source['link'], 'lightbox')?>><?php _e('Link to image lightbox', 'asg')?></option>
				<option value="same-window" <?php selected($source['link'], 'same-window')?>><?php _e('Link to the original (same window)', 'asg')?></option>
				<option value="new-window" <?php selected($source['link'], 'new-window')?>><?php _e('Link to the original (new window)', 'asg')?></option>
				<option value="no-link" <?php selected($source['link'], 'no-link')?>><?php _e('No link', 'asg')?></option>
			</select>
		</p>
		<div class="lightbox-options">
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 1', 'asg')?></label>
				<select name="asg[sources][posts][lightbox_caption_1]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
					<option value="" <?php echo selected($source['lightbox_caption_1'], '')?>><?php _e('None', 'asg')?></option>
					<option value="title" <?php echo selected($source['lightbox_caption_1'], 'title')?>><?php _e('Title', 'asg')?></option>
					<option value="excerpt" <?php echo selected($source['lightbox_caption_1'], 'excerpt')?>><?php _e('Excerpt', 'asg')?></option>
					<option value="content" <?php echo selected($source['lightbox_caption_1'], 'content')?>><?php _e('Content', 'asg')?></option>
					<option value="datetime" <?php echo selected($source['lightbox_caption_1'], 'datetime')?>><?php _e('Date + time', 'asg')?></option>
					<option value="date" <?php echo selected($source['lightbox_caption_1'], 'date')?>><?php _e('Date', 'asg')?></option>
					<option value="tags" <?php echo selected($source['lightbox_caption_1'], 'date')?>><?php _e('Tags', 'asg')?></option>
					<?php foreach($this->get_custom_field_values() as $value): ?>
						<option value="custom_field_<?php echo esc_attr($value->meta_key )?>" <?php selected($source['lightbox_caption_1'], "custom_field_" . $value->meta_key)?>><?php _e('Custom field:', 'asg') ?><?php echo esc_html($value->meta_key)?></option>
					<?php endforeach ?>
				</select>
			</p>
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 2', 'asg') ?></label>
				<select name="asg[sources][posts][lightbox_caption_2]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
					<option value="" <?php echo selected($source['lightbox_caption_2'], '')?>><?php _e('None', 'asg')?></option>
					<option value="title" <?php echo selected($source['lightbox_caption_2'], 'title')?>><?php _e('Title', 'asg')?></option>
					<option value="excerpt" <?php echo selected($source['lightbox_caption_2'], 'excerpt')?>><?php _e('Excerpt', 'asg')?></option>
					<option value="content" <?php echo selected($source['lightbox_caption_2'], 'content')?>><?php _e('Content', 'asg')?></option>
					<option value="datetime" <?php echo selected($source['lightbox_caption_2'], 'datetime')?>><?php _e('Date + time', 'asg')?></option>
					<option value="date" <?php echo selected($source['lightbox_caption_2'], 'date')?>><?php _e('Date', 'asg')?></option>
					<option value="tags" <?php echo selected($source['lightbox_caption_2'], 'date')?>><?php _e('Tags', 'asg')?></option>
					<?php foreach($this->get_custom_field_values() as $value): ?>
						<option value="custom_field_<?php echo esc_attr($value->meta_key )?>" <?php selected($source['lightbox_caption_2'], "custom_field_" . $value->meta_key)?>><?php _e('Custom field:', 'asg') ?><?php echo esc_html($value->meta_key)?></option>
					<?php endforeach ?>
				</select>
			</p>
		</div>
	</li>
</ul>
