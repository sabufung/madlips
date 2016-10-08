<div <?php $image->render_image_attributes($this->options) ?>>
	<?php if ($image->is_link()): ?>
		<a <?php $image->render_link_attributes() ?>>
	<?php else: ?>
		<div class="asg-image-wrapper">
	<?php endif ?>
				<img src="<?php echo esc_attr($image->thumbnail_url) ?>" <?php if (!empty($image->caption_1)): ?>alt="<?php echo esc_attr($image->caption_1) ?>"<?php endif ?>/>
				<?php if ($image->has_caption()): ?>
						<div <?php $image->render_caption_attributes($this->options) ?>>
							<?php if ($image->caption_1): ?>
									<div class="asg-image-caption1"><?php echo $image->caption_1 ?></div>
							<?php endif ?>
							<?php if ($image->caption_2): ?>
								<div class="asg-image-caption2"><?php echo $image->caption_2 ?></div>
							<?php endif ?>
						</div>
				<?php endif ?>
			<?php if ($image->has_overlay($this->options)): ?>
				<div <?php $image->render_overlay_attributes($this->options) ?>></div>
			<?php endif ?>
	<?php if ($image->is_link()): ?>
		</a>
	<?php else: ?>
		</div>
	<?php endif ?>
	<?php if ($image->has_lightbox_caption()): ?>
		<div class="asg-lightbox-content">
			<?php if ($image->lightbox_caption_1): ?><div class="asg-lightbox-caption1"><?php echo $image->lightbox_caption_1 ?></div><?php endif ?>
			<?php if ($image->lightbox_caption_2): ?><div class="asg-lightbox-caption2"><?php echo $image->lightbox_caption_2 ?></div><?php endif ?>
		</div>
	<?php endif ?>
</div>
