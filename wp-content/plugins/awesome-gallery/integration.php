<?php
class ASG_Integration {
	function __construct(){
		add_action('init', array($this, '_register_post_types'));
		add_action('admin_print_scripts-post.php', array($this, '_enqueue_scripts'), 99);
		add_action('admin_print_scripts-post-new.php', array($this, '_enqueue_scripts'), 99);
		if (!is_admin()){
			add_action('wp_enqueue_scripts', array($this, '_enqueue_scripts'), 99);
		}
		add_action('wp_head', array($this, '_wp_head'));
	}
	function _register_post_types(){
		register_post_type(ASG_POST_TYPE, array(
			'label' => __('Awesome Gallery', 'asg'),
			'labels' => array(
				'add_new' => __('Add new gallery', 'asg'),
				'add_new_item' => __('Add new gallery', 'asg'),
				'edit_item' => __('Edit gallery', 'asg'),
				'search_items' => __('Search gallery', 'asg'),
				'not_found' => __('No galleries yet', 'asg')

			),
			'public' => false,
			'show_ui' => true,
			'supports' => array('title')
		));
	}

	function _enqueue_scripts(){
		global $wp_scripts;
		if (is_admin())
			return;
		if (!get_option('asg_new_jquery')){
			asg_enqueue_scripts();
		}
		asg_enqueue_styles();
	}

	function _wp_head(){

		if (get_option('asg_new_jquery') && !is_admin()){
			?>
			<script type="text/javascript">
				if (typeof(jQuery) != 'undefined' && jQuery)
					asgjQueryBackup = jQuery;
				if (typeof($) != 'undefined' && $)
					asg$Backup = $;
			</script>
			<script type="text/javascript" src="<?php echo ASG_URL . "vendor/jquery-1.8.3.js" ?>"></script>
			<script type="text/javascript">
				window.asgjQuery = jQuery.noConflict(true);
			</script>
			<?php switch (asg_get_active_lightbox()):
				case 'magnific-popup': ?>
					<script type="text/javascript" src="<?php echo ASG_URL . "vendor/jquery.magnific-popup.js?ver=" . ASG_VERSION ?>"></script>
				<?php break ?>
				<?php case 'prettyphoto': ?>
					<script type="text/javascript" src="<?php echo ASG_URL . "vendor/prettyphoto/jquery.prettyPhoto.js?ver=" . ASG_VERSION ?>"></script>
				<?php break ?>
				<?php case 'swipebox': ?>
					<script type="text/javascript" src="<?php echo ASG_URL . "vendor/swipebox/jquery.swipebox.js?ver=" . ASG_VERSION ?>"></script>
				<?php break; ?>
				<?php case 'uberbox': ?>
					<script type="text/javascript" src="<?php echo includes_url() . "/js/underscore.min.js?ver=" . ASG_VERSION ?>"></script>
					<script type="text/javascript" src="<?php echo includes_url() . "/js/backbone.min.js?ver=" . ASG_VERSION ?>"></script>
					<script type="text/javascript" src="<?php echo ASG_URL . "vendor/backbone.marionette.js?ver=" . ASG_VERSION ?>"></script>
					<script type="text/javascript" src="<?php echo ASG_URL . "vendor/uberbox/dist/templates.js?ver=" . ASG_VERSION ?>"></script>
					<script type="text/javascript" src="<?php echo ASG_URL . "vendor/uberbox/dist/uberbox.js?ver=" . ASG_VERSION ?>"></script>
				<?php break ?>
			<?php endswitch ?>
			<script type="text/javascript" src="<?php echo ASG_URL . "assets/js/awesome-gallery.js?ver=" . ASG_VERSION ?>"></script>
			<script type="text/javascript">
				if (typeof(asgjQueryBackup) != 'undefined' && asgjQueryBackup)
					jQuery = asgjQueryBackup;
				if (typeof(asg$Backup) != 'undefined' && asg$Backup)
					$ = asg$Backup;

			</script>
		<?php
		}
	}
}
new ASG_Integration;
