<?php
/**
 * Frontend Uploader Settings
 */
if ( !class_exists( 'Closify_Settings' ) ):
class Closify_Settings {

	private $settings_api;

	function __construct() {
		$this->settings_api = new WeDevs_Settings_API;
        
		add_action( 'admin_init', array( $this, 'action_current_screen' ) );
		add_action( 'admin_menu', array( $this, 'action_admin_menu' ) );
	}

	/**
	 * Only run if current screen is plugin settings or options.php
	 * @return [type] [description]
	 */
	function action_current_screen() {

        $this->settings_api->set_sections( $this->get_settings_sections() );
        $this->settings_api->set_fields( $this->get_settings_fields() );

        // Initialize settings
        $this->settings_api->admin_init($this);

	}

	/**
	 * Get post types for checkbox option
	 * @return array of slug => label for registered post types
	 */
	static function get_post_types() {
		$closify_public_post_types = get_post_types( array( 'public' => true ), 'objects' );
		foreach( $closify_public_post_types as $slug => $post_object ) {
			if ( $slug == 'attachment' ) {
				unset( $closify_public_post_types[$slug] );
				continue;
			}
			$closify_public_post_types[$slug] = $post_object->labels->name;
		}
		return $closify_public_post_types;
	}

	function action_admin_menu() {
        add_submenu_page( 'edit.php?post_type=closify', __( 'Closify Settings', 'closify-press' ), __( 'Closify Settings', 'closify-press' ), 'edit_posts', 'closify_settings', array( $this, 'plugin_page' ) );
	}

	function get_settings_sections() {
		$sections = array(
			array(
				'id' => 'closify_settings',
				'title' => __( 'Closify Settings', 'closify-press' ),
			)
		);
		return $sections;
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	static function get_settings_fields() {;
    	
		// Update media sized for thumbnail option
        $media_sizes = array();
        foreach (get_intermediate_image_sizes() as $s) {
            if (isset($_wp_additional_image_sizes[$s])) {
                $width = intval($_wp_additional_image_sizes[$s]['width']);
                $height = intval($_wp_additional_image_sizes[$s]['height']);
            } else {
                $width = get_option($s.'_size_w');
                $height = get_option($s.'_size_h');
            }
            if($width != '' && $height!='')
              $media_sizes[$s] = $s.' ('.$width.' x '.$height.')';
        }
        $media_sizes['full'] = 'full size';
		
		$settings_fields = array(
			'closify_settings' => array(
				array(
					'name' => 'notify_admin',
					'label' => __( 'Notify site admins', 'closify-press' ),
					'desc' => __( 'Yes', 'closify-press' ),
					'type' => 'checkbox',
					'default' => '',
				),
				array(
					'name' => 'admin_notification_text',
					'label' => __( 'Admin Notification', 'closify-press' ),
					'desc' => __( 'Message that admin will get on new file upload', 'closify-press' ),
					'type' => 'textarea',
					'default' => __( 'Someone uploaded new images', 'closify-press'),
					'sanitize_callback' => 'wp_filter_post_kses'
				),
				array(
					'name' => 'notification_email',
					'label' => __( 'Notification email', 'closify-press' ),
					'desc' => __( 'Leave blank to use site admin email', 'closify-press' ),
					'type' => 'text',
					'default' => '',
					'sanitize_callback' => 'sanitize_email',
				),
                array(
					'name' => 'notification_logo',
					'label' => __( 'Notification Logo', 'closify-press' ),
					'desc' => __( 'Change the logo of the notification', 'closify-press' ),
					'type' => 'file',
					'default' => '',
				),
				array(
					'name' => 'auto_approve_user_files',
					'label' => __( 'Auto-approve registered users files', 'closify-press' ),
					'desc' => __( 'Yes', 'closify-press' ),
					'type' => 'checkbox',
					'default' => '',
				),
				array(
					'name' => 'auto_approve_any_files',
					'label' => __( 'Auto-approve any files', 'closify-press' ),
					'desc' => __( 'Yes', 'closify-press' ),
					'type' => 'checkbox',
					'default' => '',
				),
				array(
					'name' => 'html5_gallery_view',
					'label' => __( 'Switch to collage viewer with Masonry effect', 'closify-press' ),
					'desc' => __( 'This gallery viewer is much more limited, but it is more suitable for small size galleries (<100). If you have any conflicts after switching, make sure to go and update your gallery shortcodes.', 'closify-press' ),
					'type' => 'checkbox',
					'default' => 'yes',
				),
                array(
                    'name' => 'thumb_size',
                    'label' => __( 'Size of thumbnails', 'closify-press' ),
					'desc' => __( 'Select the size of thumbnails for Closify galleries. The lower the better for page load performance.', 'closify-press' ),
					'type' => 'select',
					'options' => $media_sizes,
                    'default' => 'thumbnail'
                )
			),
		);
        
		return $settings_fields;
	}

	/**
	 * Render the UI
	 */
	function plugin_page() {

		echo '<div class="wrap">';
		$this->settings_api->show_navigation();
		$this->settings_api->show_forms();

		echo '</div>';
	}
}

endif;

// Instantiate
$closify_settings_obj = new Closify_Settings;