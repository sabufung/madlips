<?php if (!class_exists('C_NextGEN_Bootstrap')): ?>
	<strong><?php echo sprintf(__('Please install and activate <a href="%s">NEXTGEN gallery plugin</a> in order to enable this tab.', 'asg'), 'http://http://wordpress.org/plugins/nextgen-gallery/')?></strong>
<?php else: ?>
	<ul class="steps">
		<li id="nextgen-select-gallery-block">
			<h2><span class="step-number">1.</span> Choose a gallery</h2>
			<p>
				<label class="wizard-label"><?php _e('GALLERY', 'asg')?>:</label>
				<button class="button" id="nextgen-select-gallery"><?php _e('Select gallery', 'asg')?></button>
				<button class="button" id="nextgen-reset-gallery"><?php _e('Reset to none', 'asg')?></button>
				<strong><?php _e('Selected', 'asg')?>:</strong>
				<span id="nextgen-gallery-name" data-text="model.gallery_name"></span>
				<input id="nextgen-gallery-name" type="hidden" name="asg[sources][nextgen][gallery_name]" value="<?php echo esc_attr($source['gallery_name']) ?>" data-value="model.gallery_name">
				<input id="nextgen-gallery" type="hidden" name="asg[sources][nextgen][gallery]" value="<?php echo esc_attr($source['gallery']) ?>" data-value="model.gallery">
			</p>
			<p>
				<label class="wizard-label"><?php _e('ORDER BY', 'asg')?>:</label>
				<select name="asg[sources][nextgen][order_by]">
					<option value="sortorder" <?php selected($source['order_by'], 'sortorder')?>><?php _e('Gallery order', 'asg')?></option>
					<option value="filename" <?php selected($source['order_by'], 'filename')?>><?php _e('Filename', 'asg')?></option>
					<option value="description" <?php selected($source['order_by'], 'description')?>><?php _e('Description', 'asg')?></option>
					<option value="alttext" <?php selected($source['order_by'], 'alttext')?>><?php _e('Alt value', 'asg')?></option>
					<option value="rand()" <?php selected($source['order_by'], 'rand()')?>><?php _e('Random', 'asg')?></option>
					<option value="imagedate" <?php selected($source['order_by'], 'imagedate')?>><?php _e('Date', 'asg')?></option>
				</select>

			</p>
			<p>
				<label class="wizard-label"><?php _e('ORDER', 'asg') ?>:</label>
				<select name="asg[sources][nextgen][order]">
					<option value="ASC" <?php selected($source['order'], 'ASC')?>><?php _e('ASC', 'asg')?></option>
					<option value="DESC" <?php selected($source['order'], 'DESC')?>><?php _e('DESC', 'asg')?></option>
				</select>
			</p>
		</li>
		<li id="nextgen-settings-block">
			<div class="action-button">
				<a class="button button-hero" href="#"><?php _e('Preview', 'asg')?></a>
			</div>
			<h2><span class="step-number">3.</span> <?php _e('Adjust settings', 'asg')?>.</h2>
			<p>
				<label class="wizard-label"><?php _e('CAPTION LINE 1', 'asg') ?>:</label>
				<select name="asg[sources][nextgen][caption_1]" value="<?php echo esc_attr($source['caption_1']) ?>" data-value="model.caption_1">
					<option value="" <?php echo selected($source['caption_1'], '')?>><?php _e('None', 'asg')?></option>
					<option value="filename" <?php echo selected($source['caption_1'], 'filename')?>><?php _e('Filename', 'asg')?></option>
					<option value="alttext" <?php echo selected($source['caption_1'], 'alttext')?>><?php _e('Alt text', 'asg')?></option>
					<option value="description" <?php echo selected($source['caption_1'], 'description')?>><?php _e('Description text', 'asg')?></option>
					<option value="excerpt" <?php echo selected($source['caption_1'], 'excerpt')?>><?php _e('Description excerpt', 'asg')?></option>
					<option value="datetime" <?php echo selected($source['caption_1'], 'datetime')?>><?php _e('Date + time', 'asg')?></option>
					<option value="date" <?php echo selected($source['caption_1'], 'date')?>><?php _e('Date', 'asg')?></option>
				</select>
			</p>
			<p>
				<label class="wizard-label">CAPTION LINE 2:</label>
				<select name="asg[sources][nextgen][caption_2]" value="<?php echo esc_attr($source['caption_2']) ?>" data-value="model.caption_2">
					<option value="" <?php echo selected($source['caption_2'], '')?>><?php _e('None', 'asg')?></option>
					<option value="filename" <?php echo selected($source['caption_2'], 'filename')?>><?php _e('Filename', 'asg')?></option>
					<option value="alttext" <?php echo selected($source['caption_2'], 'alttext')?>><?php _e('Alt text', 'asg')?></option>
					<option value="description" <?php echo selected($source['caption_2'], 'description')?>><?php _e('Description text', 'asg')?></option>
					<option value="excerpt" <?php echo selected($source['caption_2'], 'excerpt')?>><?php _e('Description excerpt', 'asg')?></option>
					<option value="datetime" <?php echo selected($source['caption_2'], 'datetime')?>><?php _e('Date + time', 'asg')?></option>
					<option value="date" <?php echo selected($source['caption_2'], 'date')?>><?php _e('Date', 'asg')?></option>

				</select>
			</p>
			<p>
				<label class="wizard-label"><?php _e('LINK MODE', 'asg')?></label>
				<select name="asg[sources][nextgen][link]" id="nextgen-link-type" data-value="model.link">
					<option value="lightbox" <?php selected($source['link'], 'lightbox')?>><?php _e('Link to image lightbox', 'asg')?></option>
					<option value="same-window" <?php selected($source['link'], 'same-window')?>><?php _e('Link to the original (same window)', 'asg')?></option>
					<option value="new-window" <?php selected($source['link'], 'new-window')?>><?php _e('Link to the original (new window)', 'asg')?></option>
					<option value="no-link" <?php selected($source['link'], 'no-link')?>><?php _e('No link', 'asg')?></option>
				</select>
			</p>
			<div class="lightbox-options">
				<p>
					<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 1', 'asg')?></label>
					<select name="asg[sources][nextgen][lightbox_caption_1]" value="<?php echo esc_attr($source['lightbox_caption_1']) ?>" data-value="model.lightbox_caption_1">
						<option value="" <?php echo selected($source['lightbox_caption_1'], '')?>><?php _e('None', 'asg')?></option>
						<option value="filename" <?php echo selected($source['lightbox_caption_1'], 'filename')?>><?php _e('Filename', 'asg')?></option>
						<option value="alttext" <?php echo selected($source['lightbox_caption_1'], 'alttext')?>><?php _e('Alt text', 'asg')?></option>
						<option value="description" <?php echo selected($source['lightbox_caption_1'], 'description')?>><?php _e('Description text', 'asg')?></option>
						<option value="excerpt" <?php echo selected($source['lightbox_caption_1'], 'excerpt')?>><?php _e('Description excerpt', 'asg')?></option>
						<option value="datetime" <?php echo selected($source['lightbox_caption_1'], 'datetime')?>><?php _e('Date + time', 'asg')?></option>
						<option value="date" <?php echo selected($source['lightbox_caption_1'], 'date')?>><?php _e('Date', 'asg')?></option>

					</select>
				</p>
				<p>
					<label class="wizard-label"><?php _e('LIGHTBOX TITLE LINE 2', 'asg') ?></label>
					<select name="asg[sources][nextgen][lightbox_caption_2]" value="<?php echo esc_attr($source['lightbox_caption_2']) ?>" data-value="model.lightbox_caption_2">
						<option value="" <?php echo selected($source['lightbox_caption_2'], '')?>><?php _e('None', 'asg')?></option>
						<option value="filename" <?php echo selected($source['lightbox_caption_2'], 'filename')?>><?php _e('Filename', 'asg')?></option>
						<option value="alttext" <?php echo selected($source['lightbox_caption_2'], 'alttext')?>><?php _e('Alt text', 'asg')?></option>
						<option value="description" <?php echo selected($source['lightbox_caption_2'], 'description')?>><?php _e('Description text', 'asg')?></option>
						<option value="excerpt" <?php echo selected($source['lightbox_caption_2'], 'excerpt')?>><?php _e('Description excerpt', 'asg')?></option>
						<option value="datetime" <?php echo selected($source['lightbox_caption_2'], 'datetime')?>><?php _e('Date + time', 'asg')?></option>
						<option value="date" <?php echo selected($source['lightbox_caption_2'], 'date')?>><?php _e('Date', 'asg')?></option>
					</select>
				</p>
			</div>
		</li>
	</ul>
<?php endif ?>
