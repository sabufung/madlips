<?php global $post ?>
<ul class="steps">
	<li>
		<a class="button button-hero" href="https://500px.com/login" target="_blank"><?php _e('Sign in to 500px', 'asg')?></a>
		<h2><span class="step-number">1.</span> Sign in to 500px</h2>
	</li>
	<li>
		<a class="button button-hero" href="http://500px.com/settings/applications" target="_blank"><?php _e('Register an application', 'asg')?></a>
		<h2><span class="step-number">2.</span> Register an application</h2>
		<p><?php _e('Click "register your application" button and fill the form.', 'asg')?></p>
	</li>
	<li id="500px-copy-keys-block">
		<a class="button button-hero" href="http://500px.com/settings/applications" target="_blank"><?php _e('Open Application list', 'asg')?></a>
		<h2><span class="step-number">3.</span> Copy application keys.</h2>
		<p>Click "See application details" next to your application and copy next fields:</p>
		<label class="wizard-label">CONSUMER KEY:</label>
		<input name="asg[sources][500px][consumer_key]" type="text" value="<?php echo esc_attr($source['consumer_key']) ?>" data-value="model.consumer_key">
		<br>
		<label class="wizard-label" for="">APP SECRET:</label>
		<input name="asg[sources][500px][consumer_secret]"  type="text" value="<?php echo esc_attr($source['consumer_secret']) ?>" data-value="model.consumer_secret">
	</li>
	<li id="500px-check-keys-block">
		<div class="action-button">
			<span class="spinner"></span>
			<a class="button button-hero" href="#"><?php _e('Check keys', 'asg')?></a>
		</div>
		<h2><span class="step-number">4.</span> Check keys.</h2>
	</li>
	<li id="500px-data-block">
		<h2><span class="step-number">5.</span> Configure Data source</h2>
		<label class="wizard-label" for="">DATA SOURCE:</label>
		<select name="asg[sources][500px][source_type]" data-value="model.source_type">
			<option value="popular" <?php selected($source['source_type'], 'popular') ?>><?php _e('Popular', 'asg')?></option>
			<option value="upcoming" <?php selected($source['source_type'], 'upcoming') ?>><?php _e('Upcoming', 'asg')?></option>
			<option value="editors" <?php selected($source['source_type'], 'editors') ?>><?php _e('Editor\'s', 'asg')?></option>
			<option value="fresh_today" <?php selected($source['source_type'], 'fresh_today') ?>><?php _e('Fresh today', 'asg')?></option>
			<option value="fresh_yesterday" <?php selected($source['source_type'], 'fresh_yesterday') ?>><?php _e('Fresh yesterday', 'asg')?></option>
			<option value="fresh_week" <?php selected($source['source_type'], 'fresh_week') ?>><?php _e('Fresh week', 'asg')?></option>
			<option value="user" <?php selected($source['source_type'], 'user') ?>><?php _e('User\'s photos', 'asg')?></option>
			<option value="user_friends" <?php selected($source['source_type'], 'user_friends') ?>><?php _e('User\'s friend\'s photos', 'asg')?></option>
			<option value="user_favorites" <?php selected($source['source_type'], 'user_favorites') ?>><?php _e('User\'s favorites', 'asg')?></option>
			<option value="user_collection" <?php selected($source['source_type'], 'user_collection') ?>><?php _e('User\'s collection', 'asg')?></option>
		</select>
		<div id="500px-user-options">
			<label class="wizard-label">USERNAME:</label>
			<input name="asg[sources][500px][username]" type="text" value="<?php echo esc_attr($source['username'])?>" data-value="model.username">
		</div>

		<div id="500px-user-collection">
			<div id="500px-oauth">
				<label class="wizard-label"><?php _e('ACCESS TOKEN', 'asg') ?></label>
				<input name="asg[sources][500px][access_token]" id="500px-access-token" value="<?php echo esc_attr($source['access_token'])?>" type="text" data-value="model.access_token">
				<button id="500px-oauth-authenticate" class="button"><?php _e('Receive a token', 'asg')?></button>
			</div>
			<div>
				<label class="wizard-label"><?php _e('ACCESS TOKEN SECRET', 'asg') ?></label>
				<input name="asg[sources][500px][access_token_secret]" id="500px-access-token-secret" value="<?php echo esc_attr($source['access_token_secret'])?>" type="text" data-value="model.access_token_secret">
				<button id="500px-oauth-check-token" class="button"><?php _e('Check the tokens', 'asg')?></button>
			</div>
			<label class="wizard-label">COLLECTION:</label>
			<button class="button" id="500px-select-collection"><?php _e('Select a collection', 'asg') ?></button>
			<input type="hidden" name="asg[sources][500px][collection_name]" value="<?php echo esc_attr($source['collection_name'])?>" data-value="model.collection_name">
			<input type="hidden" name="asg[sources][500px][collection]" value="<?php echo esc_attr($source['collection'])?>" data-value="model.collection">
			<span id="500px-current-collection" data-text="model.collection_name"><?php echo esc_html($source['collection_name']) ?></span>
		</div>
		<div id="asg-500px-category">
			<label class="wizard-label">CATEGORY: </label>
			<select name="asg[sources][500px][category]" data-value="model.category">
				<?php $categories = array(
					'' => __('Everything' ,'asg'),
					'0' => __('Uncategorized','asg'),
					'10' => __('Abstract', 'asg'),
					'11' => __('Animals', 'asg'),
					'5' => __('Black and White', 'asg'),
					'1' => __('Celebrities', 'asg'),
					'9' => __('City and Architecture', 'asg'),
					'15' => __('Commercial', 'asg'),
					'16' => __('Concert', 'asg'),
					'20' => __('Family', 'asg'),
					'14' => __('Fashion', 'asg'),
					'2' => __('Film', 'asg'),
					'24' => __('Fine Art', 'asg'),
					'23' => __('Food', 'asg'),
					'3' => __('Journalism', 'asg'),
					'8' => __('Landscapes', 'asg'),
					'12' => __('Macro', 'asg'),
					'18' => __('Nature', 'asg'),
					'4' => __('Nude', 'asg'),
					'7' => __('People', 'asg'),
					'19' => __('Performing Arts', 'asg'),
					'17' => __('Sport', 'asg'),
					'18' => __('Still life', 'asg'),
					'21' => __('Street', 'asg'),
					'26' => __('Transportation', 'asg'),
					'13' => __('Travel', 'asg'),
					'22' => __('Underwater', 'asg'),
					'23' => __('Urban Exploration', 'asg'),
					'25' => __('Wedding', 'asg')
					) ?>
					<?php foreach($categories as $index => $category): ?>
						<option value="<?php echo esc_attr($category) ?>" <?php selected($source['category'], $category) ?>><?php echo esc_html($category)?></option>
					<?php endforeach ?>
			</select>
		</div>
		<div id="asg-500px-sorting">
			<label class="wizard-label">SORTING:</label>
			<select name="asg[sources][500px][sorting]" data-value="model.sorting" >
				<option value="" <?php selected($source['sorting'], '')?>><?php _e('No sorting', 'asg')?></option>
				<option value="created_at" <?php selected($source['sorting'], 'created_at')?>><?php _e('Creation date', 'asg')?></option>
				<option value="rating" <?php selected($source['sorting'], 'rating')?>><?php _e('Rating', 'asg')?></option>
				<option value="times_viewed" <?php selected($source['sorting'], 'times_viewed')?>><?php _e('Views', 'asg')?></option>
				<option value="votes_count" <?php selected($source['sorting'], 'votes_count')?>><?php _e('Votes', 'asg')?></option>
				<option value="favorites_count" <?php selected($source['sorting'], 'favorites_count')?>><?php _e('Favorited', 'asg')?></option>
				<option value="comments_count" <?php selected($source['sorting'], 'comments_count')?>><?php _e('Comments count', 'asg')?></option>
				<option value="taken_at" <?php selected($source['sorting'], 'taken_at')?>><?php _e('Taken at', 'asg')?></option>
			</select>
	</li>
	<li id="500px-settings-block">
		<div class="action-button">
			<a class="button button-hero" href="#"><?php _e('Preview', 'asg')?></a>
		</div>
		<h2><span class="step-number">6.</span><?php _e('Adjust settings', 'asg')?>.</h2>
		<label class="wizard-label">CAPTION LINE 1:</label>
		<select name="asg[sources][500px][caption_1]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
			<option value="" <?php echo selected($source['caption_1'], '')?>><?php _e('None', 'asg')?></option>
			<option value="name" <?php echo selected($source['caption_1'], 'name')?>><?php _e('Name', 'asg')?></option>
			<option value="description" <?php echo selected($source['caption_1'], 'description')?>><?php _e('Description', 'asg')?></option>
			<option value="times_viewed" <?php echo selected($source['caption_1'], 'times_viewed')?>><?php _e('Views', 'asg')?></option>
			<option value="rating" <?php echo selected($source['caption_1'], 'rating')?>><?php _e('Rating', 'asg')?></option>
			<option value="category" <?php echo selected($source['caption_1'], 'category')?>><?php _e('Views', 'asg')?></option>
			<option value="favorites_count" <?php echo selected($source['caption_1'], 'favorites_count')?>><?php _e('Favorited', 'asg')?></option>
			<option value="comments_count" <?php echo selected($source['caption_1'], 'comments_count')?>><?php _e('Comments', 'asg')?></option>
			<option value="username" <?php echo selected($source['caption_1'], 'username')?>><?php _e('Username', 'asg')?></option>
			<option value="user_full_name" <?php echo selected($source['caption_1'], 'user_full_name')?>><?php _e('User full name', 'asg')?></option>
		</select>
		<br>
		<label class="wizard-label">CAPTION LINE 2:</label>
		<select name="asg[sources][500px][caption_2]" value="<?php echo esc_attr($source['caption_2']) ?>" data-value="model.caption_2">
			<option value="" <?php echo selected($source['caption_2'], '')?>><?php _e('None', 'asg')?></option>
			<option value="name" <?php echo selected($source['caption_2'], 'name')?>><?php _e('Name', 'asg')?></option>
			<option value="description" <?php echo selected($source['caption_2'], 'description')?>><?php _e('Description', 'asg')?></option>
			<option value="times_viewed" <?php echo selected($source['caption_2'], 'times_viewed')?>><?php _e('Views', 'asg')?></option>
			<option value="rating" <?php echo selected($source['caption_2'], 'rating')?>><?php _e('Rating', 'asg')?></option>
			<option value="category" <?php echo selected($source['caption_2'], 'category')?>><?php _e('Views', 'asg')?></option>
			<option value="favorites_count" <?php echo selected($source['caption_2'], 'favorites_count')?>><?php _e('Favorited', 'asg')?></option>
			<option value="comments_count" <?php echo selected($source['caption_2'], 'comments_count')?>><?php _e('Comments', 'asg')?></option>
			<option value="username" <?php echo selected($source['caption_2'], 'username')?>><?php _e('Username', 'asg')?></option>
			<option value="user_full_name" <?php echo selected($source['caption_2'], 'user_full_name')?>><?php _e('User full name', 'asg')?></option>
		</select>
		<p>
			<label class="wizard-label"><?php _e('LINK MODE', 'asg')?></label>
			<select name="asg[sources][500px][link]" id="rss-link-type" data-value="model.link">
				<option value="lightbox" <?php selected($source['link'], 'lightbox')?>><?php _e('Link to lightbox', 'asg')?></option>
				<option value="same-window" <?php selected($source['link'], 'same-window')?>><?php _e('Link to 500px.com (same window)', 'asg')?></option>
				<option value="new-window" <?php selected($source['link'], 'new-window')?>><?php _e('Link to 500px.com (new window)', 'asg')?></option>
				<option value="no-link" <?php selected($source['link'], 'no-link')?>><?php _e('No link', 'asg')?></option>
			</select>
		</p>
		<div class="lightbox-options">
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 1', 'asg')?></label>
				<select name="asg[sources][500px][lightbox_caption_1]">
					<option value="" <?php echo selected($source['lightbox_caption_1'], '')?>><?php _e('None', 'asg')?></option>
					<option value="name" <?php echo selected($source['lightbox_caption_1'], 'name')?>><?php _e('Name', 'asg')?></option>
					<option value="description" <?php echo selected($source['lightbox_caption_1'], 'description')?>><?php _e('Description', 'asg')?></option>
					<option value="times_viewed" <?php echo selected($source['lightbox_caption_1'], 'times_viewed')?>><?php _e('Views', 'asg')?></option>
					<option value="rating" <?php echo selected($source['lightbox_caption_1'], 'rating')?>><?php _e('Rating', 'asg')?></option>
					<option value="category" <?php echo selected($source['lightbox_caption_1'], 'category')?>><?php _e('Views', 'asg')?></option>
					<option value="favorites_count" <?php echo selected($source['lightbox_caption_1'], 'favorites_count')?>><?php _e('Favorited', 'asg')?></option>
					<option value="comments_count" <?php echo selected($source['lightbox_caption_1'], 'comments_count')?>><?php _e('Comments', 'asg')?></option>
					<option value="username" <?php echo selected($source['lightbox_caption_1'], 'username')?>><?php _e('Username', 'asg')?></option>
					<option value="user_full_name" <?php echo selected($source['lightbox_caption_1'], 'user_full_name')?>><?php _e('User full name', 'asg')?></option>
				</select>
			</p>
			<p>
				<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 2', 'asg') ?></label>
				<select name="asg[sources][500px][lightbox_caption_2]">
					<option value="" <?php echo selected($source['lightbox_caption_2'], '')?>><?php _e('None', 'asg')?></option>
					<option value="name" <?php echo selected($source['lightbox_caption_2'], 'name')?>><?php _e('Name', 'asg')?></option>
					<option value="description" <?php echo selected($source['lightbox_caption_2'], 'description')?>><?php _e('Description', 'asg')?></option>
					<option value="times_viewed" <?php echo selected($source['lightbox_caption_2'], 'times_viewed')?>><?php _e('Views', 'asg')?></option>
					<option value="rating" <?php echo selected($source['lightbox_caption_2'], 'rating')?>><?php _e('Rating', 'asg')?></option>
					<option value="category" <?php echo selected($source['lightbox_caption_2'], 'category')?>><?php _e('Views', 'asg')?></option>
					<option value="favorites_count" <?php echo selected($source['lightbox_caption_2'], 'favorites_count')?>><?php _e('Favorited', 'asg')?></option>
					<option value="comments_count" <?php echo selected($source['lightbox_caption_2'], 'comments_count')?>><?php _e('Comments', 'asg')?></option>
					<option value="username" <?php echo selected($source['lightbox_caption_2'], 'username')?>><?php _e('Username', 'asg')?></option>
					<option value="user_full_name" <?php echo selected($source['lightbox_caption_2'], 'user_full_name')?>><?php _e('User full name', 'asg')?></option>
				</select>
			</p>
		</div>
	</li>
</ul>
