<script>window.opener.jQuery('#500px-access-token').val("<?php echo $access_token['oauth_token'] ?>").change();window.opener.jQuery('#500px-access-token-secret').val("<?php echo $access_token['oauth_token_secret'] ?>").change(); window.close();</script>
<div class="wrap">
	<h2><?php _e('500px access granted', 'asg')?></h2>
	<strong><?php _e('Please copy these access tokens to the gallery page:', 'asg')?>
		<p style="font-size: 16px; margin-bottom: 30px; font-weight: 200">
			AUTH TOKEN:
			<?php echo $access_token['oauth_token']?>
		</p>
		<p style="font-size: 16px; margin-bottom: 30px; font-weight: 200">
			AUTH TOKEN SECRET:
			<?php echo $access_token['oauth_token_secret']?>
		</p>
		<button class="button button-primary" onclick="window.close()"><?php _e('Close this tab', 'asg')?></button>
</div>