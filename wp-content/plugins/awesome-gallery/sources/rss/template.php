<ul class="steps">
	<li id="rss-url-block">
		<div class="action-button">
			<span class="spinner"></span>
			<a class="button button-hero" href="#"><?php _e('Check feed availability', 'asg')?></a>
		</div>
		<h2><span class="step-number">1.</span> Enter RSS URL</h2>
		<label class="wizard-label">URL:</label>
		<input name="asg[sources][rss][url]" id="rss-url" type="text" value="<?php echo esc_attr($source['url']) ?>" data-value="model.url">
		<br>
	</li>

	<li id="rss-settings-block">
		<div class="action-button">
			<a class="button button-hero" href="#"><?php _e('Preview', 'asg')?></a>
		</div>
		<h2><span class="step-number">3.</span><?php _e('Adjust settings', 'asg')?>.</h2>
		<label class="wizard-label">CAPTION LINE 1:</label>
		<select name="asg[sources][rss][caption_1]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
			<option value="" <?php echo selected($source['caption_1'], '')?>><?php _e('None', 'asg')?></option>
			<option value="author" <?php echo selected($source['caption_1'], 'author')?>><?php _e('Author', 'asg')?></option>
			<option value="title" <?php echo selected($source['caption_1'], 'title')?>><?php _e('Title', 'asg')?></option>
			<option value="tags" <?php echo selected($source['caption_1'], 'tags')?>><?php _e('Tags', 'asg')?></option>
			<option value="description" <?php echo selected($source['caption_1'], 'description')?>><?php _e('Description', 'asg')?></option>
			<option value="excerpt" <?php echo selected($source['caption_1'], 'excerpt')?>><?php _e('20-word excerpt', 'asg')?></option>
		</select>
		<br>
		<label class="wizard-label">CAPTION LINE 2:</label>
		<select name="asg[sources][rss][caption_2]" value="<?php echo esc_attr($source['caption_2']) ?>" data-value="model.caption_2">
			<option value="" <?php echo selected($source['caption_2'], '')?>><?php _e('None', 'asg')?></option>
			<option value="author" <?php echo selected($source['caption_2'], 'author')?>><?php _e('Author', 'asg')?></option>
			<option value="title" <?php echo selected($source['caption_2'], 'title')?>><?php _e('Title', 'asg')?></option>
			<option value="tags" <?php echo selected($source['caption_2'], 'tags')?>><?php _e('Tags', 'asg')?></option>
			<option value="description" <?php echo selected($source['caption_2'], 'description')?>><?php _e('Description', 'asg')?></option>
			<option value="excerpt" <?php echo selected($source['caption_2'], 'excerpt')?>><?php _e('20-word excerpt', 'asg')?></option>
		</select>
		<p>
			<label class="wizard-label"><?php _e('LINK MODE', 'asg')?></label>
			<select name="asg[sources][rss][link]" id="rss-link-type" data-value="model.link">
				<option value="lightbox" <?php selected($source['link'], 'lightbox')?>><?php _e('Link to image lightbox', 'asg')?></option>
				<option value="same-window" <?php selected($source['link'], 'same-window')?>><?php _e('Link to the original (same window)', 'asg')?></option>
				<option value="new-window" <?php selected($source['link'], 'new-window')?>><?php _e('Link to the original (new window)', 'asg')?></option>
				<option value="no-link" <?php selected($source['link'], 'no-link')?>><?php _e('No link', 'asg')?></option>
			</select>
		</p>
		<div class="lightbox-options">
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 1', 'asg')?></label>
				<select name="asg[sources][rss][lightbox_caption_1]">
					<option value="" <?php echo selected($source['lightbox_caption_1'], '')?>><?php _e('None', 'asg')?></option>
					<option value="author" <?php echo selected($source['lightbox_caption_1'], 'author')?>><?php _e('Author', 'asg')?></option>
					<option value="title" <?php echo selected($source['lightbox_caption_1'], 'title')?>><?php _e('Title', 'asg')?></option>
					<option value="tags" <?php echo selected($source['lightbox_caption_1'], 'tags')?>><?php _e('Tags', 'asg')?></option>
					<option value="description" <?php echo selected($source['lightbox_caption_1'], 'description')?>><?php _e('Description', 'asg')?></option>
					<option value="excerpt" <?php echo selected($source['lightbox_caption_1'], 'excerpt')?>><?php _e('20-word excerpt', 'asg')?></option>
				</select>
			</p>
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 2', 'asg') ?></label>
				<select name="asg[sources][rss][lightbox_caption_2]">
					<option value="" <?php echo selected($source['lightbox_caption_2'], '')?>><?php _e('None', 'asg')?></option>
					<option value="author" <?php echo selected($source['lightbox_caption_2'], 'author')?>><?php _e('Author', 'asg')?></option>
					<option value="title" <?php echo selected($source['lightbox_caption_2'], 'title')?>><?php _e('Title', 'asg')?></option>
					<option value="tags" <?php echo selected($source['lightbox_caption_2'], 'tags')?>><?php _e('Tags', 'asg')?></option>
					<option value="description" <?php echo selected($source['lightbox_caption_2'], 'description')?>><?php _e('Description', 'asg')?></option>
					<option value="excerpt" <?php echo selected($source['lightbox_caption_2'], 'excerpt')?>><?php _e('20-word excerpt', 'asg')?></option>
				</select>
			</p>
		</div>
	</li>
</ul>
