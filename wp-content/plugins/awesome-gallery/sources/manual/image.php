<li class="image">
	<h3>
		<span class="handle"></span>
		<span class="heading"><?php echo esc_html(trim($image['title']) ? trim($image['title']) : "Image $index") ?></span>
	</h3>
	<div class="image-content">
		<div class="section">
			<label class="huge"><?php _e('Image', 'asg')?></label>
			<div class="columns-2">
				<div class="column">
					<div class="field">
						<label><?php _e('Main image', 'asg')?></label>
						<div class="image-selector no-image asg-manual-main-image">
							<input name="asg[sources][manual][images][<?php echo $index ?>][image]" value="<?php echo $image['image'] ?>" type="hidden">
							<?php if ($image['image']): ?>
								<img src="<?php echo esc_url(asg_get_wp_image_src($image['image'])) ?>">
							<?php endif ?>
							<div class="overlay"></div>
							<div class="actions-wrapper">
								<button class="select-image button "><?php _e('Select image', 'asg')?></button>
								<br>
								<a href="#" class="image-delete"><?php _e('Remove image', 'asg')?></a>
							</div>
						</div>
					</div>
				</div>
				<div class="column">
					<div class="field">
						<label><?php _e('Title', 'asg')?></label>
						<input type="text" name="asg[sources][manual][images][<?php echo $index ?>][title]" value="<?php echo esc_attr($image['title']) ?>" class="full-width title">
					</div>
					<div class="field">
						<label><?php _e('Description', 'asg')?></label>
						<textarea name="asg[sources][manual][images][<?php echo $index ?>][description]" class="full-width description"><?php echo esc_textarea($image['description'])?></textarea>
					</div>
					<div class="field">
						<label><?php _e('Link URL', 'asg')?></label>
						<input type="text" name="asg[sources][manual][images][<?php echo $index ?>][url]" value="<?php echo esc_attr($image['url']) ?>" class="full-width">
					</div>
					<div class="field">
						<label><?php _e('Tags (comma-separated)', 'asg')?></label>
						<input type="text" name="asg[sources][manual][images][<?php echo $index ?>][tags]" value="<?php echo esc_attr($image['tags']) ?>" class="full-width">
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
		<div class="section collapsed">
			<label class="huge"><?php _e('Lightbox')?></label>
			<div class="columns-2">
				<div class="column">
					<div class="field">
						<label><?php _e('Lightbox image (leave empty for video or google map)', 'asg')?></label>
						<div class="image-selector no-image asg-manual-lightbox-image">
							<input name="asg[sources][manual][images][<?php echo $index ?>][lightbox][image]" value="<?php echo $image['lightbox']['image'] ?>" type="hidden">
							<?php if ($image['lightbox']['image']): ?>
								<img src="<?php echo esc_attr(asg_get_wp_image_src($image['lightbox']['image'])) ?>">
							<?php endif ?>
							<div class="overlay"></div>
							<div class="actions-wrapper">
								<button class="select-image button "><?php _e('Select image', 'asg')?></button>
								<br>
								<a href="#" class="image-delete"><?php _e('Remove image', 'asg')?></a>
							</div>
						</div>
					</div>
				</div>
				<div class="column">
					<div class="field">
						<label><?php _e('Title', 'asg')?></label>
						<input type="text" name="asg[sources][manual][images][<?php echo $index ?>][lightbox][title]" value="<?php echo esc_attr($image['lightbox']['title']) ?>" class="full-width asg-lightbox-title">
					</div>
					<div class="field">
						<label><?php _e('Description', 'asg')?></label>
						<textarea name="asg[sources][manual][images][<?php echo $index ?>][lightbox][description]" class="full-width asg-lightbox-description"><?php echo esc_textarea($image['lightbox']['description'])?></textarea>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
		<div class="section submitdiv">
			<a href="#" class="cell-cancel"><?php echo _e('Cancel', 'asg')?></a>
			<span class="separator">|</span>
			<a href="#" class="cell-delete"><?php echo _e('Remove', 'asg')?></a>
		</div>
	</div>
</li>
