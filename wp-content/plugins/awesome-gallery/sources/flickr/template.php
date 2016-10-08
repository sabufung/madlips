<?php global $post ?>
<ul class="steps">
	<li>
		<a class="button button-hero" href="http://flickr.com/signin" target="_blank"><?php _e('Log in to Flickr', 'asg')?></a>
		<h2><span class="step-number">1.</span> Sign in to Flickr</h2>
	</li>
	<li>
		<a class="button button-hero" href="http://www.flickr.com/services/apps/create/apply" target="_blank"><?php _e('Create an application', 'asg')?></a>
		<h2><span class="step-number">2.</span> Create a Flickr application.</h2>
	</li>
	<li id="flickr-copy-keys-block">
		<a class="button button-hero" href="http://www.flickr.com/services/apps/" target="_blank"><?php _e('Open Application list', 'asg')?></a>
		<h2><span class="step-number">3.</span> Copy application keys.</h2>
		<p>Please copy here the next values from the application configuration:</p>
		<label class="wizard-label">KEY:</label>
		<input name="asg[sources][flickr][key]" id="flickr-key" type="text" value="<?php echo esc_attr($source['key']) ?>" data-value="model.key">
		<br>
		<label class="wizard-label" for="">SECRET:</label>
		<input name="asg[sources][flickr][secret]" id="flickr-secret" type="text" value="<?php echo esc_attr($source['secret']) ?>" data-value="model.secret">
	</li>
	<li id="flickr-check-keys-block">
		<div class="action-button">
			<span class="spinner"></span>
			<a id="flickr-check-keys" class="button button-hero" href="#"><?php _e('Check keys', 'asg')?></a>
		</div>
		<h2><span class="step-number">3.</span> Check if keys were copied right and app is registered.</h2>
	</li>
	<li id="flickr-check-user-block">
		<div class="action-button">
			<span class="spinner"></span>
			<a id="flickr-check-user" class="button button-hero" href="#"><?php _e('Check user', 'asg')?></a>
		</div>
		<h2><span class="step-number">4.</span> Choose Flickr user.</h2>
		<label class="wizard-label" for="">FLICKR USERNAME:</label>
		<input name="asg[sources][flickr][username]" id="flickr-username" type="text" value="<?php echo esc_attr($source['username']) ?>" data-value="model.username">
	</li>
	<li id="flickr-select-gallery">
		<a id="flickr-preview" class="button button-hero" href="#"><?php _e('Preview the result', 'asg')?></a>
		<h2><span class="step-number">5.</span>Choose a photoset</h2>
		<label class="wizard-label" for="">TAKE IMAGES FROM:</label>
		<select name="asg[sources][flickr][source_type]" id="flickr-source-type">
			<option value="photostream" <?php selected($source['source_type'], 'photostream')?>><?php _e('User\'s photostream', 'asg')?></option>
			<option value="favorites" <?php selected($source['source_type'], 'favorites')?>><?php _e('User\'s favorites', 'asg')?></option>
			<option value="group" <?php selected($source['source_type'], 'group')?>><?php _e('Group\'s pool', 'asg')?></option>
			<option value="photoset" <?php selected($source['source_type'], 'photoset')?>><?php _e('User\'s photoset', 'asg')?></option>
		</select>
		<input name="asg[sources][flickr][source]" value="<?php echo $source['source'] ?>" id="flickr-source" type="text"  placeholder="<?php _e('Group ID', 'asg') ?>">
		<input name="asg[sources][flickr][source_name]" id="flickr-source-name-input" value="<?php echo esc_attr($source['source_name']) ?>"  type="hidden">
		<button class="button" id="flickr-select-group"><?php _e('Select group', 'asg')?></button>
		<button class="button" id="flickr-select-photoset"><?php _e('Select photoset', 'asg')?></button>
		<br>
		<label class="wizard-label" id="flickr-current-source-label"><?php _e('CURRENT GALLERY:', 'asg')?></label>
		<span id="flickr-source-name"><?php echo esc_html($source['source_name'])?></span>
	</li>
	<li id="flickr-settings-block">
		<div class="action-button">
			<a class="button button-hero" href="#"><?php _e('Preview', 'asg')?></a>
		</div>
		<h2><span class="step-number">6.</span><?php _e('Adjust settings', 'asg')?>.</h2>
		<label class="wizard-label">Flickr image size:</label>
		<select name="asg[sources][flickr][image_size]" data-value="model.image_size">
			<option value="" <?php echo selected($source['image_size'], '') ?>>Auto</option>
			<option value="o" <?php echo selected($source['image_size'], 'o') ?>>Original</option>
			<option value="n" <?php echo selected($source['image_size'], 'n') ?>>320px</option>
			<option value="z" <?php echo selected($source['image_size'], 'z') ?>>640px</option>
			<option value="c" <?php echo selected($source['image_size'], 'c') ?>>800px</option>
			<option value="b" <?php echo selected($source['image_size'], 'b') ?>>1024px</option>
			<option value="h" <?php echo selected($source['image_size'], 'h') ?>>1600px</option>
			<option value="k" <?php echo selected($source['image_size'], 'k') ?>>2048px</option>

		</select>
		<br>
		<label class="wizard-label">CAPTION LINE 1:</label>
		<select name="asg[sources][flickr][caption_1]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
			<option value="" <?php echo selected($source['caption_1'], '')?>><?php _e('None', 'asg')?></option>
			<option value="title" <?php echo selected($source['caption_1'], 'title')?>><?php _e('Title', 'asg')?></option>
			<option value="description" <?php echo selected($source['caption_1'], 'description')?>><?php _e('Description', 'asg')?></option>
			<option value="tags" <?php echo selected($source['caption_1'], 'tags')?>><?php _e('Tags', 'asg')?></option>
			<option value="ownername" <?php echo selected($source['caption_1'], 'ownername')?>><?php _e('Owner', 'asg')?></option>
			<option value="views" <?php echo selected($source['caption_1'], 'views')?>><?php _e('Views', 'asg')?></option>
		</select>
		<br>
		<label class="wizard-label">CAPTION LINE 2:</label>
		<select name="asg[sources][flickr][caption_2]" value="<?php echo esc_attr($source['caption_2']) ?>" data-value="model.caption_2">
			<option value="" <?php echo selected($source['caption_2'], '')?>><?php _e('None', 'asg')?></option>
			<option value="title" <?php echo selected($source['caption_2'], 'title')?>><?php _e('Title', 'asg')?></option>
			<option value="description" <?php echo selected($source['caption_2'], 'description')?>><?php _e('Description', 'asg')?></option>
			<option value="tags" <?php echo selected($source['caption_2'], 'tags')?>><?php _e('Tags', 'asg')?></option>
			<option value="ownername" <?php echo selected($source['caption_2'], 'ownername')?>><?php _e('Owner', 'asg')?></option>
			<option value="views" <?php echo selected($source['caption_2'], 'views')?>><?php _e('Views', 'asg')?></option>
		</select>
		<p>
			<label class="wizard-label"><?php _e('LINK MODE', 'asg')?></label>
			<select name="asg[sources][flickr][link]" id="instagram-link-type" data-value="model.link">
				<option value="lightbox" <?php selected($source['link'], 'lightbox')?>><?php _e('Link to lightbox', 'asg')?></option>
				<option value="same-window" <?php selected($source['link'], 'same-window')?>><?php _e('Link to flickr.com (same window)', 'asg')?></option>
				<option value="new-window" <?php selected($source['link'], 'new-window')?>><?php _e('Link to flickr.com (new window)', 'asg')?></option>
				<option value="no-link" <?php selected($source['link'], 'no-link')?>><?php _e('No link', 'asg')?></option>
			</select>
		</p>
		<div class="lightbox-options">
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 1', 'asg')?></label>
				<select name="asg[sources][flickr][lightbox_caption_1]">
					<option value="" <?php echo selected($source['lightbox_caption_1'], '')?>><?php _e('None', 'asg')?></option>
					<option value="title" <?php echo selected($source['lightbox_caption_1'], 'title')?>><?php _e('Title', 'asg')?></option>
					<option value="description" <?php echo selected($source['lightbox_caption_1'], 'description')?>><?php _e('Description', 'asg')?></option>
					<option value="tags" <?php echo selected($source['lightbox_caption_1'], 'tags')?>><?php _e('Tags', 'asg')?></option>
					<option value="ownername" <?php echo selected($source['lightbox_caption_1'], 'ownername')?>><?php _e('Owner', 'asg')?></option>
					<option value="views" <?php echo selected($source['lightbox_caption_1'], 'views')?>><?php _e('Views', 'asg')?></option>
				</select>
			</p>
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 2', 'asg') ?></label>
				<select name="asg[sources][flickr][lightbox_caption_2]">
					<option value="" <?php echo selected($source['lightbox_caption_2'], '')?>><?php _e('None', 'asg')?></option>
					<option value="title" <?php echo selected($source['lightbox_caption_2'], 'title')?>><?php _e('Title', 'asg')?></option>
					<option value="description" <?php echo selected($source['lightbox_caption_2'], 'description')?>><?php _e('Description', 'asg')?></option>
					<option value="tags" <?php echo selected($source['lightbox_caption_2'], 'tags')?>><?php _e('Tags', 'asg')?></option>
					<option value="ownername" <?php echo selected($source['lightbox_caption_2'], 'ownername')?>><?php _e('Owner', 'asg')?></option>
					<option value="views" <?php echo selected($source['lightbox_caption_2'], 'views')?>><?php _e('Views', 'asg')?></option>
				</select>
			</p>
		</div>
	</li>
</ul>
