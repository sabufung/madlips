<?php global $post ?>
<ul class="steps">
	<li>
		<a class="button button-hero" href="https://instagram.com/accounts/login/" target="_blank"><?php _e('Log in to Instagram')?></a>
		<h2><span class="step-number">1.</span> Log in to Instagram</h2>
	</li>
	<li>
		<a class="button button-hero" href="http://instagram.com/developer/clients/register/" target="_blank"><?php _e('Create an application')?></a>
		<h2><span class="step-number">2.</span> Create an Instagram application / client.</h2>
		<p>Please copy these values into the application form:</p>
		<strong>Website</strong>:&nbsp;&nbsp;<em class="copy"><?php bloginfo('url') ?></em>
		<br>
		<strong>OAuth redirect_uri</strong>:&nbsp;&nbsp; <em class="copy" id="instagram-redirect-uri"><?php echo $this->get_redirect_url($post->ID) ?></em>
		<br>
		<strong>Make sure to put something to Application description</strong>
	</li>
	<li id="instagram-copy-keys-block">
		<a class="button button-hero" href="http://instagram.com/developer/clients/manage/" target="_blank"><?php _e('Open Application list')?></a>
		<h2><span class="step-number">3.</span> Copy application keys.</h2>
		<p>Please copy here the next values from the application configuration:</p>
		<label class="wizard-label">CLIENT ID:</label>
		<input name="asg[sources][instagram][client_id]" id="instagram-client-id" type="text" value="<?php echo esc_attr($source['client_id']) ?>" data-value="model.client_id">
		<br>
		<label class="wizard-label" for="">CLIENT SECRET:</label>
		<input name="asg[sources][instagram][client_secret]" id="instagram-client-secret" type="text" value="<?php echo esc_attr($source['client_secret']) ?>" data-value="model.client_secret">
	</li>
	<li id="instagram-authorize-block">
		<a class="button button-hero" href="#" id="instagram-authorize"><?php _e('Authorize')?></a>
		<h2><span class="step-number">4.</span> Authorize your application in Instagram</h2>
		<label class="wizard-label">ACCESS TOKEN:</label>
		<input name="asg[sources][instagram][access_token]" id="instagram-access-token" type="text" value="<?php echo esc_attr($source['access_token']) ?>" data-value="model.access_token">
		<small><?php _e('Click "Authorize" to receive a new token', 'asg')?></small>
	</li>
	<li id="instagram-check-auth-block">
		<a class="button button-hero" href="#" id="instagram-check-auth" target="_blank"><?php _e('Check authorization')?></a>
		<h2><span class="step-number">5.</span> Check your authorization</h2>
		<strong><?php _e('Token check result is:')?></strong>
		<span id="instagram-auth-result"><?php _e('Unkkown', 'asg')?></span>
		<span class="spinner" id="auth-spinner"></span>
		<p><?php _e('Please note that this token will expire at some moment. A message will be sent to admin email once the token will expire.', 'asg')?></p>
	</li>
	<li id="instagram-check-data-block">
		<div class="action-button">
			<span class="spinner"></span>
			<a class="button button-hero" href="#" id="instagram-check-data" target="_blank"><?php _e('Check data availability')?></a>
		</div>
		<h2><span class="step-number">6.</span> Choose instagram feed</h2>
		<label class="wizard-label"><?php _e('SOURCE', 'asg')?></label>
		<select name="asg[sources][instagram][feed_type]" id="instagram-feed-type" data-value="model.feed_type">
			<option value="other-user" <?php selected($source['feed_type'], 'other-user')?>><?php _e('User\'s images (enter your login to show your images)', 'asg')?></option>
			<option value="my-feed" <?php selected($source['feed_type'], 'my-feed')?>><?php _e('Users I follow', 'asg')?></option>
			<option value="liked" <?php selected($source['feed_type'], 'liked')?>><?php _e('Images I liked', 'asg')?></option>
			<option value="hashtag" <?php selected($source['feed_type'], 'hashtag') ?>><?php _e('Hashtag', 'asg') ?></option>
		</select>
		<div id="instagram-hashtag-wrapper">
			<label class="wizard-label"><?php _e('HASHTAG', 'asg')?></label>
			<input type="text" name="asg[sources][instagram][hashtag]" id="instagram-hashtag" value="<?php echo esc_attr($source['hashtag']) ?>" data-value="model.hashtag">
			<small><?php _e('Please only enter one hashtag, multiple ones are not supported.', 'asg') ?></small>
		</div>
		<div id="instagram-other-user-wrapper">
			<label class="wizard-label"><?php _e('USER\'S LOGIN', 'asg')?></label>
			<input type="text" name="asg[sources][instagram][other_user_login]" id="instagram-user-login" value="<?php echo esc_attr($source['other_user_login']) ?>" data-value="model.other_user_login">
		</div>
	</li>
	<li id="instagram-settings-block">
		<h2><span class="step-number">7.</span> Adjust settings</h2>
		<p>
			<label class="wizard-label"><?php _e('TITLE LINE 1', 'asg')?></label>
				<select name="asg[sources][instagram][caption_1]">
					<option value="login" <?php selected($source['caption_1'], 'login')?>><?php _e('Instagram Login', 'asg')?></option>
					<option value="fullname" <?php selected($source['caption_1'], 'fullname')?>><?php _e('Instagram Full name', 'asg')?></option>
					<option value="caption" <?php selected($source['caption_1'], 'caption')?>><?php _e('Image caption', 'asg')?></option>
					<option value="none" <?php selected($source['caption_1'], 'none')?>><?php _e('None', 'asg')?></option>
				</select>
		</p>
		<p>
			<label class="wizard-label"><?php _e('TITLE LINE 2', 'asg') ?></label>
			<select name="asg[sources][instagram][caption_2]">
				<option value="login" <?php selected($source['caption_2'], 'login')?>><?php _e('Instagram Login', 'asg')?></option>
				<option value="fullname" <?php selected($source['caption_2'], 'fullname')?>><?php _e('Instagram Full name', 'asg')?></option>
				<option value="caption" <?php selected($source['caption_2'], 'caption')?>><?php _e('Image caption', 'asg')?></option>
				<option value="none" <?php selected($source['caption_2'], 'none')?>><?php _e('None', 'asg')?></option>
			</select>
		</p>
		<p>
			<label class="wizard-label"><?php _e('LINK MODE', 'asg')?></label>
			<select name="asg[sources][instagram][link]" id="instagram-link-type" data-value="model.link">
				<option value="lightbox" <?php selected($source['link'], 'lightbox')?>><?php _e('Link to lightbox', 'asg')?></option>
				<option value="same-window" <?php selected($source['link'], 'same-window')?>><?php _e('Link to Instagram (same window)', 'asg')?></option>
				<option value="new-window" <?php selected($source['link'], 'new-window')?>><?php _e('Link to Instagram (new window)', 'asg')?></option>
				<option value="no-link" <?php selected($source['link'], 'no-link')?>><?php _e('No link', 'asg')?></option>
			</select>
		</p>
		<div class="lightbox-options">
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 1', 'asg')?></label>
				<select name="asg[sources][instagram][lightbox_caption_1]">
					<option value="login" <?php selected($source['lightbox_caption_1'], 'login')?>><?php _e('Instagram Login', 'asg')?></option>
					<option value="fullname" <?php selected($source['lightbox_caption_1'], 'fullname')?>><?php _e('Instagram Full name', 'asg')?></option>
					<option value="caption" <?php selected($source['lightbox_caption_1'], 'caption')?>><?php _e('Image caption', 'asg')?></option>
					<option value="none" <?php selected($source['lightbox_caption_1'], 'none')?>><?php _e('None', 'asg')?></option>
				</select>
			</p>
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 2', 'asg') ?></label>
				<select name="asg[sources][instagram][lightbox_caption_2]">
					<option value="login" <?php selected($source['lightbox_caption_2'], 'login')?>><?php _e('Instagram Login', 'asg')?></option>
					<option value="fullname" <?php selected($source['lightbox_caption_2'], 'fullname')?>><?php _e('Instagram Full name', 'asg')?></option>
					<option value="caption" <?php selected($source['lightbox_caption_2'], 'caption')?>><?php _e('Image caption', 'asg')?></option>
					<option value="none" <?php selected($source['lightbox_caption_2'], 'none')?>><?php _e('None', 'asg')?></option>
				</select>
			</p>
		</div>
	</li>
</ul>
