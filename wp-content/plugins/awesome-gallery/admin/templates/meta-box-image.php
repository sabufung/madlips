<ul class="asg-tabs">
	<li class="asg-current"><a href="#asg-image-caption"><?php _e('Caption', 'asg') ?></a></li>
	<li><a href="#asg-image-border"><?php _e('Border', 'asg') ?></a></li>
	<li><a href="#asg-image-shadow"><?php _e('Shadow', 'asg') ?></a></li>
	<li><a href="#asg-image-effects"><?php _e('Effects', 'asg') ?></a></li>
	<li><a href="#asg-image-overlay"><?php _e('Overlay', 'asg') ?></a></li>
</ul>
<ul class="asg-panels">
	<?php foreach(array('caption', 'border', 'shadow', 'effects', 'overlay') as $panel): ?>
		<li id="asg-image-<?php echo $panel ?>" class="<?php echo $panel == 'caption' ? 'asg-current' : '' ?>"><?php require("meta-box-image-section-$panel.php") ?></li>
	<?php endforeach ?>
</ul>
<br class="clear">
