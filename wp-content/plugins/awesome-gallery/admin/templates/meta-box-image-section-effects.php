<p>
	<label><?php _e('Image blur', 'asg') ?></label>
	<select name="asg[image][blur]">
		<option value="off"  <?php selected($gallery['image']['blur'], 'off') ?>>
			<?php _e('Off', 'asg') ?>
		</option>
		<option value="on-hover"  <?php selected($gallery['image']['blur'], 'on-hover') ?>>
			<?php _e('On-hover', 'asg') ?>
		</option>
		<option value="off-hover"  <?php selected($gallery['image']['blur'], 'off-hover') ?>>
			<?php _e('Off-hover', 'asg') ?>
		</option>
	</select>
	<small><?php _e('Excluding IE10', 'asg') ?></small>
</p>
<p>
	<label><?php _e('Black & white', 'asg') ?></label>
	<select name="asg[image][bw]">
		<option value="off"  <?php selected($gallery['image']['bw'], 'off') ?>><?php _e('Off', 'asg') ?></option>
		<option value="on" <?php selected($gallery['image']['bw'], 'on') ?>><?php _e('On', 'asg') ?></option>
		<option value="on-hover"  <?php selected($gallery['image']['bw'], 'on-hover') ?>><?php _e('On-hover', 'asg') ?></option>
		<option value="off-hover"  <?php selected($gallery['image']['bw'], 'off-hover') ?>>
			<?php _e('Off-hover', 'asg') ?>
		</option>
	</select>
	<small><?php _e('Excluding IE10', 'asg') ?></small>
</p>
