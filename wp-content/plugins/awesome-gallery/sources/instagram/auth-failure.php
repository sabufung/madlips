<div class="wrap">
<h2><?php _e('Access denied by Instagram', 'asg')?></h2>
<p style="font-size: 16px; margin-bottom: 30px; font-weight: 200">
	<?php _e('Most probably, you clicked "Cancel" button at Instagram authorization page.', 'asg')?>
	<?php _e('Please close this tab and try to click "Authorize" button again.', 'asg')?>

</p>

	<strong><?php _e('Error message was', 'asg') ?>:</strong> <?php echo esc_html(stripslashes($_REQUEST['message']))
	?>
	<br>
	<br>
<button class="button button-primary" onclick="window.close()"><?php _e('Close this tab', 'asg')?></button>
</div>
