<script>window.opener.jQuery('#instagram-access-token').val("<?php echo $_REQUEST['insta_token'] ?>").change(); window.close();</script>
<div class="wrap">
	<h2><?php _e('Instagram access granted', 'asg')?></h2>
	<strong><?php _e('Please copy this access token to the gallery page:', 'asg')?>
	<p style="font-size: 16px; margin-bottom: 30px; font-weight: 200">
		<?php echo $_REQUEST['insta_token']?>
	</p>
	<button class="button button-primary" onclick="window.close()"><?php _e('Close this tab', 'asg')?></button>
</div>