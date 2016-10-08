<?php global $post ?>
<ul class="steps">
	<li>
		<a class="button button-hero" href="http://facebook.com/" target="_blank"><?php _e('Sign in to Facebook', 'asg')?></a>
		<h2><span class="step-number">1.</span> Sign in to Facebook</h2>
	</li>
	<li>
		<a class="button button-hero" href="https://developers.facebook.com/apps" target="_blank"><?php _e('Create a Facebook app', 'asg')?></a>
		<h2><span class="step-number">2.</span> Create a Facebook app</h2>
		<p><?php _e('Click "Create App" button and follow Facebook instructions.', 'asg')?></p>
		<p>Please make sure to open "Advanced" settings tab and copy the next URL into "Valid OAuth redirect URIs"
			field.</p>
	</li>

	<li id="facebook-copy-keys-block">
		<a class="button button-hero" href="http://developers.facebook.com/apps" target="_blank"><?php _e('Open Application list', 'asg')?></a>
		<h2><span class="step-number">3.</span> Copy application keys.</h2>
		<p>Please copy here the next values from the application configuration:</p>
		<label class="wizard-label">APP ID:</label>
		<input name="asg[sources][facebook][app_id]" id="facebook-id" type="text" value="<?php echo esc_attr($source['app_id']) ?>" data-value="model.app_id">
		<br>
		<label class="wizard-label" for="">APP SECRET:</label>
		<input name="asg[sources][facebook][app_secret]" id="facebook-secret" type="text" value="<?php echo esc_attr($source['app_secret']) ?>" data-value="model.app_secret">
	</li>
	<li id="facebook-oauth-block">
		<a class="button button-hero" href="<?php echo ($source['app_id']) ? "https://developers.facebook.com/apps/{$source['app_id']}/advanced?ref=nav" : 'javascript: alert("Please enter app ID first")' ?>"
		target="_blank"><?php _e('Open Application settings', 'asg')?></a>
		<h2><span class="step-number">4.</span>Copy OAuth redirect URL</h2>
		<p>Please copy the URL below into Facebook app settings</p>
		<strong>Valid OAuth redirect URIs</strong>:&nbsp;&nbsp; <em class="copy" id="facebook-redirect-uri"><?php echo
			admin_url('admin.php?') ?></em>
		<br>
	</li>
	<li id="facebook-authenticate-block" data-redirect-url="<?php echo admin_url('admin.php?action=asg-facebook-auth')?>" friend-redirect-url="<?php echo admin_url('admin.php?action=asg-facebook-auth-show') ?>">
		<a id="facebook-authenticate" class="button button-hero" href="#"><?php _e('Authenticate', 'asg')?></a>
		<h2><span class="step-number">5.</span> Authenticate the application</h2>
		<p><?php _e('Please log in to Facebook using the account that owns the album you want to use first or use the public albums.', 'asg') ?></p>
		<label class="wizard-label"><?php _e('ACCESS TOKEN')?>:</label><input id="facebook-access-token" name="asg[sources][facebook][access_token]" value="<?php echo esc_attr($source['access_token'])?>" type="text" data-value="model.access_token">
		<br>
		<label class="wizard-label"><?php _e('TOKEN EXPIRES')?>:</label><input id="facebook-token-expires" name="asg[sources][facebook][token_expires]" value="<?php echo esc_attr($source['token_expires'])?>" type="text" data-value="model.token_expires">
		<br>
		<label class="wizard-label">Authentication URL:</label>
		<span class="auth-url"></span>
		<br><small><?php _e('You can copy this URL and set it to your friend to get an access to his photos. He will need to copy the auth token and user ID received', 'asg')?></small>
	</li>
	<li id="facebook-check-access-token-block">
		<div class="action-button">
			<span class="spinner"></span>
			<a id="facebook-check-access-token" class="button button-hero" href="#"><?php _e('Check access token', 'asg')?></a>
		</div>
		<h2><span class="step-number">6.</span> Check access token.</h2>
	</li>
	<li id="facebook-check-user-block">
		<div class="action-button">
			<span class="spinner"></span>
			<a id="facebook-check-user" class="button button-hero" href="#"><?php _e('Check user', 'asg')?></a>
		</div>
		<h2><span class="step-number">7.</span> Choose Facebook user or page.</h2>
		<label class="wizard-label" for="">FACEBOOK USER/PAGE/EVENT ID:</label>
		<input name="asg[sources][facebook][username]" id="facebook-username" type="text" value="<?php echo esc_attr($source['username']) ?>" data-value="model.username">
		<small><?php _e('Please note that this ID is not your username or Facebook page URL. Use "me" or page / user id received from your friend at the previous step.', 'asg') ?></small>
	</li>
	<li id="facebook-select-album-block">
		<a id="facebook-preview" class="button button-hero" href="#"><?php _e('Preview the result', 'asg')?></a>
		<h2><span class="step-number">8.</span>Choose a photoset</h2>
		<p>
			<?php _e('Only your own and public albums are visible. Please check your Facebook album\'s privacy settings if you don\'t see the album you need', 'asg') ?>
		</p>
		<label class="wizard-label" for="">TAKE IMAGES FROM:</label>
		<select name="asg[sources][facebook][source_type]" id="facebook-select-source-type">
			<option value="photos" <?php selected($source['source_type'], 'photos')?>><?php _e('User\'s / page\'s photos', 'asg')?></option>
			<option value="album" <?php selected($source['source_type'], 'album')?>><?php _e('Album', 'asg')?></option>
		</select>

		<input name="asg[sources][facebook][source]" value="<?php echo $source['source'] ?>" id="facebook-source" type="hidden" data-value="model.source">
		<input name="asg[sources][facebook][source_name]" id="facebook-source-name-input" value="<?php echo esc_attr($source['source_name']) ?>"  type="hidden" data-value="model.source_name">
		<button class="button" id="facebook-select-album"><?php _e('Select album', 'asg')?></button>
		<br>
		<label class="wizard-label" id="facebook-current-source-label"><?php _e('CURRENT ALBUM:', 'asg')?></label>
		<span id="facebook-source-name" data-text="model.source_name"><?php echo esc_html($source['source_name'])?></span>
	</li>
	<li id="facebook-settings-block">
		<div class="action-button">
			<a class="button button-hero" href="#"><?php _e('Preview', 'asg')?></a>
		</div>
		<h2><span class="step-number">9.</span><?php _e('Adjust settings', 'asg')?>.</h2>
		<label class="wizard-label">CAPTION LINE 1:</label>
		<select name="asg[sources][facebook][caption_1]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
			<option value="" <?php echo selected($source['caption_1'], '')?>><?php _e('None', 'asg')?></option>
			<option value="category" <?php echo selected($source['caption_1'], 'category')?>><?php _e('Category', 'asg')?></option>
			<option value="name" <?php echo selected($source['caption_1'], 'name')?>><?php _e('Image title', 'asg')?></option>
			<option value="from" <?php echo selected($source['caption_1'], 'from') ?>><?php _e('User', 'asg') ?></option>
			<option value="place" <?php echo selected($source['caption_1'], 'place')?>><?php _e('Place', 'asg')?></option>
			<option value="tags" <?php echo selected($source['caption_1'], 'tags')?>><?php _e('Tags', 'asg')?></option>
		</select>
		<br>
		<label class="wizard-label">CAPTION LINE 2:</label>
		<select name="asg[sources][facebook][caption_2]" value="<?php echo esc_attr($source['caption_2']) ?>" data-value="model.caption_2">
			<option value="" <?php echo selected($source['caption_2'], '')?>><?php _e('None', 'asg')?></option>
			<option value="category" <?php echo selected($source['caption_2'], 'category')?>><?php _e('Category', 'asg')?></option>
			<option value="name" <?php echo selected($source['caption_2'], 'name')?>><?php _e('Image title', 'asg')?></option>
			<option value="from" <?php echo selected($source['caption_2'], 'from') ?>><?php _e('User', 'asg') ?></option>
			
			<option value="tags" <?php echo selected($source['caption_2'], 'tags')?>><?php _e('Tags', 'asg')?></option>
		</select>
		<p>
			<label class="wizard-label"><?php _e('LINK MODE', 'asg')?></label>
			<select name="asg[sources][facebook][link]" id="instagram-link-type" data-value="model.link">
				<option value="lightbox" <?php selected($source['link'], 'lightbox')?>><?php _e('Link to lightbox', 'asg')?></option>
				<option value="same-window" <?php selected($source['link'], 'same-window')?>><?php _e('Link to facebook.com (same window)', 'asg')?></option>
				<option value="new-window" <?php selected($source['link'], 'new-window')?>><?php _e('Link to facebook.com (new window)', 'asg')?></option>
				<option value="no-link" <?php selected($source['link'], 'no-link')?>><?php _e('No link', 'asg')?></option>
			</select>
		</p>
		<div class="lightbox-options">
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 1', 'asg')?></label>
				<select name="asg[sources][facebook][lightbox_caption_1]">
					<option value="" <?php echo selected($source['lightbox_caption_1'], '')?>><?php _e('None', 'asg')?></option>
					<option value="category" <?php echo selected($source['lightbox_caption_1'], 'category')?>><?php _e('Category', 'asg')?></option>
					<option value="name" <?php echo selected($source['lightbox_caption_1'], 'name')?>><?php _e('Image title', 'asg')?></option>
					<option value="from" <?php echo selected($source['lightbox_caption_1'], 'from') ?>><?php _e('User', 'asg') ?></option>
					
					<option value="tags" <?php echo selected($source['lightbox_caption_1'], 'tags')?>><?php _e('Tags', 'asg')?></option>
				</select>
			</p>
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 2', 'asg') ?></label>
				<select name="asg[sources][facebook][lightbox_caption_2]">
					<option value="" <?php echo selected($source['lightbox_caption_2'], '')?>><?php _e('None', 'asg')?></option>
					<option value="category" <?php echo selected($source['lightbox_caption_2'], 'category')?>><?php _e('Category', 'asg')?></option>
					<option value="name" <?php echo selected($source['lightbox_caption_2'], 'name')?>><?php _e('Image title', 'asg')?></option>
					<option value="from" <?php echo selected($source['lightbox_caption_2'], 'from') ?>><?php _e('User', 'asg') ?></option>
					<option value="tags" <?php echo selected($source['lightbox_caption_2'], 'tags')?>><?php _e('Tags', 'asg')?></option>
				</select>
			</p>
		</div>
	</li>
</ul>
