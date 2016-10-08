
<?php if (is_wp_error($images)): ?>
		<div class="asg-load-error"><?php echo esc_html($images->get_error_message()) ?></div>
<?php else: ?>
		<?php foreach($images as $image): ?>
			<?php require(dirname(__FILE__) . "/image.php") ?>
		<?php endforeach ?>
<?php endif ?>
