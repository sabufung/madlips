<?php
class ASG_Settings{
	function __construct(){
		add_action('admin_menu', array($this, '_admin_menu'));
		add_action('admin_init', array($this, '_admin_init'));
		add_action('admin_enqueue_scripts', array($this, '_enqueue_scripts'));
	}

	function _admin_menu(){
		add_options_page(__('Awesome Gallery', 'asg'), __('Awesome Gallery', 'asg'), 'manage_options',
		'asg-options', array($this, 'build_option_pages'));
	}

	function _admin_init(){
		$settings = array(
			'asg_new_jquery' => 'intval',
			'asg_shortcode_hack' => 'intval',
			'asg_cdn_host' => 'stripslashes',
			'asg_scroll_binder' => 'stripslashes',
			'asg_lightbox' => 'stripslashes',
			'asg_link_rel' => 'stripslashes',
			'asg_link_custom_attr_name' => 'stripslashes',
			'asg_link_custom_attr_value' => 'stripslashes',
			'asg_link_class' => 'stripslashes',
			'asg_prettyphoto_theme' => 'stripslashes',
			'asg_processing_engine' => 'stripslashes',
			'asg_disable_buttons' => 'intval'
		);
		foreach($settings as $setting => $filter)
			register_setting('asg-options', $setting, $filter);
	}

	function _enqueue_scripts(){
		global $hook_suffix;
		if ($hook_suffix != 'settings_page_asg-options')
			return;
		wp_enqueue_style('asg-settings', ASG_URL . "assets/admin/css/settings.css");
	}

	function build_option_pages(){
	?>
		<div class="wrap">
			<div class="icon32" id="icon-options-general"><br></div>
			<h2><?php _e('Awesome Gallery Settings', 'asg')?></h2>
			<form method="post" action="options.php" id="asg-settings">
				<?php settings_fields( 'asg-options' ) ?>
				<ul>
					<li class="clear-after"><h3><?php _e('General', 'asg') ?></h3></li>
					<li class="clear-after">
						<label class="asg-options-label"><?php _e('Hide buttons at frontend', 'asg') ?></label>
						<p class="inputs">
							<label class="checkbox-label"><input type="checkbox" name="asg_disable_buttons" value="1"
								<?php echo checked(get_option('asg_disable_buttons')) ?>>
							</label>
							<em><?php _e('This will disable the buttons appearing to the admins next to the gallery', 'asg') ?></em>
						</p>
					</li>
					<li class="clear-after"><h3><?php _e('Compatibility', 'asg')?></h3></li>
					<li class="clear-after">
						<label class="asg-options-label"><?php _e('Use shortcode hack', 'asg')?></label>
						<p class="inputs">
							<label class="checkbox-label"><input type="checkbox" name="asg_shortcode_hack" value="1"
									<?php echo checked(get_option('asg_shortcode_hack')) ?>></label><em><?php _e('Try
									 this if Awesome Gallery looks strange on your site', 'asg') ?>
							</em>
						</p>
					</li>
					<li class="clear-after">
						<label class="asg-options-label"><?php _e('Force new jQuery version', 'asg')?></label>
						<p class="inputs">
							<label class="checkbox-label"><input type="checkbox" name="asg_new_jquery"
							                                     value="1" <?php echo checked(get_option
								('asg_new_jquery')) ?>></label><em><?php _e('Try this if Awesome Gallery does
								not work at your site', 'asg')?></em>
						</p>
					</li>
					<li class="clear-after"><h3><?php _e('Image Processing', 'asg') ?></h3></li>
					<li class="clear-after">
						<label class="asg-options-label"><?php _e('Processing engine', 'asg' )?></label>
						<p class="inputs" style="margin-left: 200px">
							<input type="radio" name="asg_processing_engine" value="timthumb" <?php checked(asg_is_photon_active() ? get_option('asg_processing_engine', 'timthumb') : 'timthumb', 'timthumb') ?>>
							<?php _e("TimThumb", 'asg') ?><br>
							<input type="radio" name="asg_processing_engine" value="photon" <?php checked(get_option('asg_processing_engine', 'timthumb'), 'photon') ?> <?php echo (asg_is_photon_active()) ? '' : 'disabled="disabled"' ?>>
							<?php _e('Photon', 'asg') ?>
							<?php if (!asg_is_photon_active()): ?>
									<strong>
										&mdash; JetPack Photon is not installed or active. <a href="admin.php?page=asg-image-troubleshooting-easy">Learn how to activate it</a>.
									</strong>
							<?php endif ?><br>
						</p>
					</li>
					<li class="clear-after">
						<label class="asg-options-label"><?php _e('CDN host (only for TimThumb)', 'asg')?></label>
						<p class="inputs">
							<input name="asg_cdn_host" value="<?php echo esc_attr(get_option('asg_cdn_host')) ?>"
							       class="regular-text" type="text" placeholder="like 'http://test.cloudfront.net'">
							<em><?php _e('Please make double sure you know what you are doing!', 'asg') ?></em>
						</p>
					</li>
					<li class="clear-after"><h3><?php _e('CDN', 'asg')?></li>
					<li class="clear-after"></li>
					<li class="clear-after"><h3><?php _e('Lightbox integration', 'asg') ?></h3></li>
					<li class="clear-after">
						<label class="asg-options-label"><?php _e('Lightbox', 'asg')?></label>
						<p class="inputs" style="margin-left: 200px">
							<input type="radio" name="asg_lightbox" value="uberbox" <?php checked('uberbox', asg_get_active_lightbox()) ?>><?php _e('Uberbox', 'uber-grid')?></input>
							<br>
							<input type="radio" name="asg_lightbox" value="magnific-popup" <?php checked('magnific-popup', asg_get_active_lightbox()) ?>><?php _e('Magnific popup', 'asg') ?></input>
							<br>
							<input type="radio" name="asg_lightbox" value="swipebox" <?php checked('swipebox', asg_get_active_lightbox()) ?>><?php _e('Swipebox', 'asg') ?></input>
							<br>
							<input type="radio" name="asg_lightbox" value="prettyphoto" <?php checked('prettyphoto', asg_get_active_lightbox()) ?>><?php _e('Prettyphoto', 'asg')?></input>
							<br>
							<input type="radio" name="asg_lightbox" value="ilightbox" <?php checked('ilightbox', asg_get_active_lightbox()) ?>>
									<?php _e('iLightbox', 'asg') ?>
									&mdash;
							</input>
							<?php if (asg_is_ilightbox_available()): ?>
								<strong>Installed and active.</strong>
							<?php else: ?>
								<strong>Not installed or inactive.</strong>
								<?php echo sprintf(__('You can buy iLightbox at the <a href="%s" target="_blank">CodeCanyon</a>, install and activate it', 'asg'), 'http://codecanyon.net/item/ilightbox-revolutionary-lightbox-for-wordpress/3939523') ?>
							<?php endif ?>
							<br>
							<input type="radio" name="asg_lightbox" value="jetpack" <?php checked('jetpack', asg_get_active_lightbox()) ?>>
								<?php _e('JetPack carousel', 'asg') ?>
								&mdash;
							<?php if (asg_is_jetpack_available()): ?>
									<strong>Installed and active.</strong>
							<?php else: ?>
									<strong><?php _e('JetPack not installed, not activated, or Carousel module is inactive', 'asg') ?></strong>
									<a href="admin.php?page=asg-image-troubleshooting-easy"><?php _e('Install Jetpack', 'asg') ?></a>
							<?php endif ?>
							<br>
							<input type="radio" name="asg_lightbox" value="foobox" <?php checked('foobox', asg_get_active_lightbox()) ?>>
							<?php _e('FooBox', 'asg') ?>
							&mdash;
							<?php if (asg_is_foobox_available()): ?>
								<strong>Installed and active.</strong>
							<?php else: ?>
								<strong><?php _e('FooBox is not installed, or was not activated.', 'asg') ?></strong>
								<?php echo sprintf(__('You can buy FooBox at: <a href="%s" target="_blank">its official site</a>', 'asg'), 'http://http://getfoobox.com') ?>
							<?php endif ?>
						</p>
					</li>
					<li class="clear-after"><h4><?php _e('PrettyPhoto options', 'asg') ?></h4></li>
					<li class="clear-after">
						<label class="asg-options-label"><?php _e('PrettyPhoto skin', 'asg')?></label>
						<p class="inputs">
							<select name="asg_prettyphoto_theme">
								<option value="pp_default" <?php selected('default', get_option('asg_prettyphoto_theme')) ?>><?php _e('Default', 'asg') ?></option>
								<option value="facebook" <?php selected('facebook', get_option('asg_prettyphoto_theme')) ?>><?php _e('Facebook', 'asg') ?></option>
								<option value="dark_rounded" <?php selected('dark_rounded', get_option('asg_prettyphoto_theme')) ?>><?php _e('Dark rounded', 'asg') ?></option>
								<option value="dark_square" <?php selected('dark_square', get_option('asg_prettyphoto_theme')) ?>><?php _e('Dark square', 'asg') ?></option>
								<option value="light_rounded" <?php selected('light_rounded', get_option('asg_prettyphoto_theme')) ?>><?php _e('Light rounded', 'asg') ?></option>
								<option value="light_square" <?php selected('light_square', get_option('asg_prettyphoto_theme')) ?>><?php _e('Light square', 'asg') ?></option>
							</select>
						</p>
					</li>

					<li class="clear-after"><h3><?php _e('Endless scroll', 'asg') ?></h3></li>
					<li class="clear-after">
						<label class="asg-options-label"><?php _e('Endless scroll binder selector', 'asg') ?></label>
						<p class="inputs">
							<input name="asg_scroll_binder" value="<?php echo esc_attr(get_option('asg_scroll_binder')) ?>" class="regular-text" type="text" placeholder="jQuery selector here">
							<em><?php _e('jQuery selector for your binder element if some scrolling plugin is used') ?></em>
						</p>
					</li>

				</ul>
				<?php submit_button(); ?>
			</form>
		</div>
<?php

	}
}
new ASG_Settings;
?>
