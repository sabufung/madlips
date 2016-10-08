<input type="hidden" id="asg-hack" name="asg-hack" />
<?php global $asg_source_editors ?>

<a href="edit.php?post_type=awesome-gallery&amp;page=support&amp;url=<?php echo  urlencode(home_url() . $_SERVER['REQUEST_URI']) ?>" target="_blank" class="button-primary" id="support"><?php _e('Support', 'asg')?></a>
<a href="admin.php?page=asg-image-troubleshooting-easy" target="_blank" class="button-primary" style="float: right; margin-right: 1em; margin-left: 1.5em"><?php _e('Images not showing?') ?></a>
<a href="options-general.php?page=asg-options" class="button" id="settings" target="_blank"><?php _e('Global settings', 'asg') ?></a>
<?php if ($post->post_status == 'publish'): ?>
	<div id="shortcode">
		<label><?php _e('Shortcode', 'asg') ?>:</label> [awesome-gallery id=<?php echo $post->ID?>]
	</div>
<?php endif ?>
<br class="clear"/>
<h2 class="nav-tab-wrapper" id="sources-tabs">
	<?php foreach($asg_source_editors as $slug => $source): ?>
	<a href="#<?php echo esc_attr($source->slug) ?>" id="source-<?php echo esc_attr($source->slug) ?>-tab" class="nav-tab <?php echo $gallery['source'] == $source->slug ? 'nav-tab-active' : '' ?>"><?php echo esc_html($source->name) ?></a>
	<?php endforeach ?>
</h2>
<input id="current-source" type="hidden" value="<?php echo $gallery['source'] ?>" name="asg[source]">
<div id="sources">
<?php foreach($asg_source_editors as $slug => $source): ?>
	<div class="source <?php echo $gallery['source'] == $source->slug ? 'source-settings-active' : '' ?>" id="source-<?php echo $source->slug?>-settings">
		<?php $source->render_editor_tab($gallery['sources'][$source->slug]) ?>
	</div>
<?php endforeach ?>
</div>
