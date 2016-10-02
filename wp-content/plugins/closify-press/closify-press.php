<?php
/**
 * Closify Press
 *
 * @package   closify_press
 * @author    Abdulrhman Elbuni
 * @link      http://www.itechflare.com/
 * @copyright 2015-2016 iTechFlare
 *
 * @wordpress-plugin
 *
 * Plugin Name: Closify Press
 * Plugin URI:  http://www.itechflare.com/
 * Description: Professional image uploading and processing plugin
 * Version:     1.9.3.5
 * Author:      MindSquare
 * Author URI:  http://www.itechflare.com/
 * Text Domain: closify-press
 * Domain Path: /languages
 * 
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

global $wpdb;

define('CLOSIFY_ITECH_PLUGIN_URI',plugin_dir_path( __FILE__ ));
define('CLOSIFY_ITECH_PLUGIN_URL',plugins_url( '',__FILE__));
define('CLOSIFY_PRESS_VERSION', '1.9.3.5');
define('CLOSIFY_DB_VERSION', '1.0');

define('CLOSIFY_POST_EXTRA_INFO', 'closify_post_info');
define('CLOSIFY_SETTING_SLUG', 'closify_settings');
define('CLOSIFY_PREFIX', 'clsfy_');
define('CLOSIFY_UPLOAD_TABLE_NAME', $wpdb->prefix . CLOSIFY_PREFIX . "uploads");
define('CLOSIFY_POST_STATUS', "closify-press");
define('CLOSIFY_POST_TYPE', 'closify');
define('CLOSIFY_TEXT_DOMAIN', 'closify-press');

define('CLOSIFY_PRESS_LOGO_URL',CLOSIFY_ITECH_PLUGIN_URL.'/assets/images/closify-press-logo.svg');
define('CLOSIFY_PRESS_URL_PNG',CLOSIFY_ITECH_PLUGIN_URL.'/assets/images/closify-press-logo.png');
define('CLOSIFY_PRESS_TMP_FOLDER', CLOSIFY_ITECH_PLUGIN_URI.'uploads');
define('CLOSIFY_NONCE','itech_closify_submission');
define('CLOSIFY_TINYMCE_NONCE','closify-tinymce');

// Advance meta boxes
require CLOSIFY_ITECH_PLUGIN_URI . 'includes/cmb/closify-meta.php';

require CLOSIFY_ITECH_PLUGIN_URI . 'includes/settings-api/class.settings-api.php';
require CLOSIFY_ITECH_PLUGIN_URI . 'includes/settings-api/closify-settings.php';

// Required files for registering the post type and taxonomies.
require CLOSIFY_ITECH_PLUGIN_URI . 'includes/closify-type.php';
require CLOSIFY_ITECH_PLUGIN_URI . 'includes/closify-post-type-registrations.php';
require CLOSIFY_ITECH_PLUGIN_URI . 'includes/closify-process-images.php';
require CLOSIFY_ITECH_PLUGIN_URI . 'includes/closify-upload-list-table.php';
require CLOSIFY_ITECH_PLUGIN_URI . 'includes/closify-gallery-util.php';
require CLOSIFY_ITECH_PLUGIN_URI . 'database.php';


// Instantiate registration class, so we can add it as a dependency to main plugin class.
$closify_post_type_registrations = new Closify_Post_Type_Registrations;

// You must register post_status to avoid capabilities error message in Edit Media
register_post_status( CLOSIFY_POST_STATUS, array(
		'label'                     => _x( 'Closify Press', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Closify <span class="count">(%s)</span>', 'Closify <span class="count">(%s)</span>' ),
	) );

// Initialise registrations for post-activation requests.
$closify_post_type_registrations->init();

// Register callback that is fired when the plugin is activated.
register_activation_hook( __FILE__, 'install_closify_database' );
// register_deactivation_hook( __FILE__, 'uninstall_closify_database' );

if(!wp_next_scheduled('closify_clean_temp'))
{
  wp_schedule_event(time(),'twicedaily' , 'closify_clean_temp');
}

function closify_clean_temp()
{
  $files = glob(CLOSIFY_ITECH_PLUGIN_URI.'uploads'.DIRECTORY_SEPARATOR.'*'); // get all file names
  
  foreach($files as $file){ // iterate files
    if(is_file($file))
      unlink($file); // delete file
  }
  do_action('closify_temp_images_after_cleaning');
}
    
load_plugin_textdomain( CLOSIFY_POST_STATUS, false, CLOSIFY_ITECH_PLUGIN_URI . '/languages/' );

  add_action( 'plugins_loaded', array( 'Closify_Press_Uploader', 'get_instance' ), 100 );
  
  class Closify_Press_Uploader
  {
    protected static $instance = null;
    private $closify_settings;
    private $closify_processor;

    function __construct() {
      // Load service property
      $this->closify_processor = new Closify_Press_Image_Processing();
      
      // Initialize plugin
      $this->init();
    }
  
    function init()
    {
        // ========== GLOBALS =======================
        $this->closify_settings = array_merge( $this->closify_settings_defaults(), (array) get_option( CLOSIFY_SETTING_SLUG , $this->closify_settings_defaults() ) );
        // ==========================================
        
        /*==========================================*/
        /*==========================================*/
        /**
        * EVENTs, ACTIONS, FILTERS & HOOKS
         * closify_image_pre_process (img_src)
         * closify_image_post_process (img_src)
         * closify_image_pre_resize (img_src)
         * closify_image_post_resize (img_src)
         * closify_image_pre_cropping (img_src)
         * closify_image_post_resize (img_src)
         * closify_image_oriented (angle, img_obj, currWidth, currHeight)
         * closify_gallery_pre_render ($img_array)
         * closify_temp_images_after_cleaning ()
        */
        /*==========================================*/
        /*==========================================*/
        // Schedule a cron job to remove temp files from upload folder

          /*==========================================*/
          //               Custom actions
          /*==========================================*/
            // This should fire when an image been approved
            add_action('closify_media_approved', array($this, 'closify_handle_approved_image'), 10, 1);
            // This should fire when an image gets uploaded successfully
            add_action('closify_file_uploaded', array($this, 'closify_handle_uploaded_image'), 10, 4);
            // This hook will be called once an image is been uploaded
            add_action('closify_new_image_uploaded', array($this, 'closify_handle_new_image_uploaded'), 10, 2);
          /*==========================================*/

          add_action('manage_users_custom_column',  array($this, 'rd_user_id_column_content'), 10, 3);
          add_filter('upload_mimes', array($this, 'custom_upload_mimes'));
          add_action( 'admin_enqueue_scripts', array($this, 'closify_admin_enqueue' ));
          add_filter('manage_users_columns', array($this, 'rd_user_id_column'));
          
          /*==========================================*/
          //               AJAX Handlers
          /*==========================================*/
          add_action('wp_ajax_itech_closify_submission', array($this,'itech_submit_closify'));
          add_action('wp_ajax_nopriv_itech_closify_submission', array($this,'itech_submit_closify'));
          add_action('wp_ajax_itech_arfaly_submission', array($this,'itech_submit_multi_closify'));
          add_action('wp_ajax_nopriv_itech_arfaly_submission', array($this,'itech_submit_multi_closify'));
          add_action('wp_ajax_approve_closify', array($this,'closify_approve_media') );
          add_action('wp_ajax_delete_closify', array($this,'closify_delete_post') );
          add_filter('posts_where', array($this,'closify_filter_posts_where') );
        /*==========================================*/

        add_shortcode( 'closify', array($this, 'closify_func'));
        add_shortcode( 'closify-collage', array($this, 'closify_collage_func'));
        add_action( 'wp_enqueue_scripts', array($this, 'load_closify_libraries' ));
        add_filter( 'manage_edit-'.CLOSIFY_POST_TYPE.'_columns', array($this, 'set_custom_edit_closify_columns' ));
        add_action( 'manage_'.CLOSIFY_POST_TYPE.'_posts_custom_column' , array($this,'custom_closify_column'), 10, 2 );
        add_action('admin_menu', array($this, 'closify_add_menu_items'));
        //add_filter('closify_gallery_pre_render', array($this, 'test_gallery'), 10, 1);
        
        // Tinmce plugin hooks
        if ( current_user_can( 'edit_posts' ) && current_user_can( 'edit_pages' ) ) {
          add_filter('query_vars', array($this, 'closify_tinymce_plugin_js_trigger'));
          add_action('template_redirect', array($this, 'closify_tinymce_plugin_load'));
          
          add_filter( 'mce_buttons', array($this, 'myplugin_register_buttons') );
          add_filter( 'mce_external_plugins', array($this, 'closify_register_tinymce_javascript') );
          
          // Pass PHP variables to the (plugin.js.php) files
          foreach ( array('post.php','post-new.php') as $hook ) {
            add_action( "admin_head-$hook", array($this, 'my_admin_head') );
          }
        }
        /*==========================================*/
        /*==========================================*/
    }

    /**
    * Localize Script
    */
    function my_admin_head()
    {
          ?>
      <!-- TinyMCE Shortcode Plugin -->
      <script type='text/javascript'>
      var my_plugin = {
          'url': '<?php echo get_site_url().'?closify_tinymce_trigger=1&_wpnonce='.wp_create_nonce( CLOSIFY_TINYMCE_NONCE ); ?>'
      };
      </script>
      <!-- TinyMCE Shortcode Plugin -->
          <?php
    }
    
    function closify_tinymce_plugin_js_trigger($vars) {
        $vars[] = 'closify_tinymce_trigger';
        return $vars;
    }
    
    function closify_tinymce_plugin_load() {
      $nonce = isset($_GET['_wpnonce'])?sanitize_text_field($_GET['_wpnonce']):'';
        
        if(intval(get_query_var('closify_tinymce_trigger')) == 1 && wp_verify_nonce($nonce, CLOSIFY_TINYMCE_NONCE)) {
          
          include CLOSIFY_ITECH_PLUGIN_URI.'includes/widgets/table-list-custom-post.php';
          include CLOSIFY_ITECH_PLUGIN_URI.'includes/widgets/table-list-users.php';
          include CLOSIFY_ITECH_PLUGIN_URI.'includes/widgets/table-list-users-roles.php';
          
          // Check what gallery option has been selected and load js/css libraries accordingly
          if(!empty($this->closify_settings['html5_gallery_view']) && $this->closify_settings['html5_gallery_view']!=''){
            if($this->closify_settings['html5_gallery_view']=="on")
            {
              include CLOSIFY_ITECH_PLUGIN_URI.'includes/get-custom-post-list-collage.php';
              die();
            }
          }
          
          include CLOSIFY_ITECH_PLUGIN_URI.'includes/get-custom-post-list-photo-swipe.php';
          die();
        }
    }
    
    // add new buttons
    function myplugin_register_buttons($buttons) {
      array_push($buttons, 'separator', 'closify');
      return $buttons;
    }

    // Load the TinyMCE plugin : editor_plugin.js
    function closify_register_tinymce_javascript($plugin_array) {
      $plugin_array['closify'] = CLOSIFY_ITECH_PLUGIN_URL.'/includes/js/plugins/closify/plugin.js';
      return $plugin_array;
    }
    
    function custom_upload_mimes ( $existing_mimes=array() ) {
      // add PSD extension to the array
      $existing_mimes['psd'] = 'application/octet-stream';
      // add AI extension to the array
      $existing_mimes['tiff'] = 'image/x-tiff';
      $existing_mimes['tif'] = 'image/tiff';
      $existing_mimes['tif'] = 'image/x-tiff';
      $existing_mimes['eps'] = 'application/postscript';
      $existing_mimes['svg'] = 'image/svg+xml';
      $existing_mimes['ai'] = 'application/postscript';
      // removing existing file types
      // unset( $existing_mimes['exe'] );
      // add as many as you like
      // and return the new full result
      return $existing_mimes;
    }

    function closify_settings_defaults() {
        $defaults = array();
        $settings = Closify_Settings::get_settings_fields();
        foreach ( $settings[CLOSIFY_SETTING_SLUG] as $setting ) {
            $defaults[ $setting['name'] ] = $setting['default'];
        }
        return $defaults;
    }

    function filter_filename($filename)
    {
      $filename = mb_ereg_replace("([^\w\s\d\-_~,;\[\]\(\).])", '', $filename);
      // Remove any runs of periods (thanks falstro!)
      $filename = mb_ereg_replace("([\.]{2,})", '', $filename);
      // Remove white spaces
      $filename = str_replace(' ', '', $filename);;

      return $filename;
    }
  
    /**
      * Notify site administrator by email
      */
    function _notify_admin( ) {

        // Email notifications are disabled, or upload has failed, bailing
        if ( ! ( 'on' == $this->closify_settings['notify_admin'] ) )
            return;

        $closify_press_email_link = admin_url( 'edit.php?post_type='.CLOSIFY_POST_TYPE.'&page=closify_manage_list' );
        $closify_press_email_content = $this->closify_settings['admin_notification_text'];
        $closify_press_email_logo = $this->closify_settings['notification_logo'];

        // TODO: It'd be nice to add the list of upload files
        $to = !empty( $this->closify_settings['notification_email'] ) && filter_var( $this->closify_settings['notification_email'], FILTER_VALIDATE_EMAIL ) ? $this->closify_settings['notification_email'] : get_option( 'admin_email' );
        $subj = __( 'New Closify images have been uploaded', 'closify-uploader' );

        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        ob_start();

        require_once CLOSIFY_ITECH_PLUGIN_URI.'includes/template/notification.php';

        $content  = ob_get_contents();

        ob_end_clean();

        wp_mail( $to, $subj, $content, $headers );
    }

    // Hook to catch single image upload event
    function closify_handle_new_image_uploaded($att_id, $img_src)
    {
        // Notify the admin via email
        $this->_notify_admin();
        // Do something
    }


    /**
    * Loads the color picker javascript
    */
    function closify_admin_enqueue() {
        global $typenow;
        
        wp_enqueue_style( 'closify_meta_box_styles', plugin_dir_url( __FILE__ ) . 'assets/css/closify-admin.css' );
        if( $typenow == CLOSIFY_POST_TYPE ) {
            wp_enqueue_script( 'meta-box-closify-js', plugin_dir_url( __FILE__ ) . 'assets/js/closify-admin-min.js' );
        }
    }

    // Enqueue scripts
    function load_closify_libraries() {
      
      wp_enqueue_script('jquery-ui-draggable');
      wp_enqueue_script('jquery-touch-punch');
      wp_enqueue_script('thickbox');

      wp_enqueue_script(
              'closify-multi-script',
              plugins_url( 'assets/js/closify-multi-min.js' , __FILE__ ), array('jquery'), CLOSIFY_PRESS_VERSION, true
          );

      wp_enqueue_script(
              'closify-script',
              plugins_url( 'assets/js/closify-min.js' , __FILE__ ),
              array( 'jquery', 'jquery-ui-draggable', 'jquery-touch-punch' ), CLOSIFY_PRESS_VERSION, true
          );

      // ************ CSS Section ****************
      //******************************************
      wp_enqueue_style( 'closify-default',
              plugins_url( 'assets/css/style.css' , __FILE__ ), array(), CLOSIFY_PRESS_VERSION);

      wp_enqueue_style( 'arfaly-default',
            plugins_url( 'assets/css/arfaly.css' , __FILE__), array('closify-default'), CLOSIFY_PRESS_VERSION );
            
      // Check what gallery option has been selected and load js/css libraries accordingly
      if(!empty($this->closify_settings['html5_gallery_view']) && $this->closify_settings['html5_gallery_view']!=''){
        if($this->closify_settings['html5_gallery_view']=="on")
        {
          
          wp_enqueue_style( 'lightbox-default',
              plugins_url( 'assets/css/lightbox.css' , __FILE__ ), array(), CLOSIFY_PRESS_VERSION);
              
          // Closify Collage style
          wp_enqueue_style( 'closify-collage',
              plugins_url( 'assets/css/transitions.css' , __FILE__ ), array('lightbox-default'), CLOSIFY_PRESS_VERSION);
              
          wp_enqueue_script(
              'closify-lightbox-script',
              plugins_url( 'assets/js/lightbox-min.js' , __FILE__ ),
              array( 'jquery' ), CLOSIFY_PRESS_VERSION, true
          );

          wp_enqueue_script(
              'closify-collage-script',
              plugins_url( 'assets/js/jquery.collagePlus.min.js' , __FILE__ ),
              array( 'jquery' ), CLOSIFY_PRESS_VERSION, true
          );
              
          return;
        }
      }
      
      $this->include_photswipe_libraries();
              
    }

    function include_photswipe_libraries()
    {
          wp_enqueue_style( 'closify-animate',
            plugins_url( 'assets/css/animate.min.css' , __FILE__ ), array(), CLOSIFY_PRESS_VERSION);
          
          // ================== Photo Swipe Dependencies ============
          wp_enqueue_style( 'photoswipe-css',
              plugins_url( 'assets/css/photoswipe/photoswipe.css' , __FILE__ ), array(), CLOSIFY_PRESS_VERSION);
            
          wp_enqueue_style( 'photoswipe-skin-default',
              plugins_url( 'assets/css/photoswipe/default-skin.css' , __FILE__ ), array('photoswipe-css'), CLOSIFY_PRESS_VERSION);
          
          wp_enqueue_script(
                  'photoswipe-js',
                  plugins_url( 'assets/js/photoswipe.min.js' , __FILE__ ),
                  array(), CLOSIFY_PRESS_VERSION, true
              );
          
          wp_enqueue_script(
                  'photoswipe-ui-default',
                  plugins_url( 'assets/js/photoswipe-ui-default.min.js' , __FILE__ ),
                  array('photoswipe-js'), CLOSIFY_PRESS_VERSION, true
              );
              
          wp_enqueue_script(
                  'photoswipe-gallery-vanilla-code',
                  plugins_url( 'assets/js/photoswipe-gallery.js' , __FILE__ ),
                  array('photoswipe-js'), CLOSIFY_PRESS_VERSION, true
              );
              
              // ================== jPages + LazyLoad ============
          wp_enqueue_script(
                  'closify-jpages-js',
                  plugins_url( 'assets/js/jPages.min.js' , __FILE__ ),
                  array('jquery'), CLOSIFY_PRESS_VERSION, true
              );
              
          /*wp_enqueue_script(
                  'closify-lazyload-js',
                  plugins_url( 'assets/js/jquery.lazyload.min.js' , __FILE__ ),
                  array('jquery'), CLOSIFY_PRESS_VERSION, true
              );*/
              
          wp_enqueue_script(
                  'closify-masonry-js',
                  plugins_url( 'assets/js/masonry.pkgd.min.js' , __FILE__ ),
                  array('jquery'), CLOSIFY_PRESS_VERSION, true
              );
    }

    // [closify id="<id>"]
    function closify_func( $atts ) {
      extract( shortcode_atts( array(
              'id' => 0,
              'disable_menu' => 'false',
              'user_id' => -1
              ),
              $atts ) );

      $id = intval($id);
      $disable_menu = sanitize_text_field($disable_menu);
      $user_id = intval($user_id);
      
      return $this->Building_Closify_Container($id, $disable_menu, $user_id);
    }

    // Shortcode for building collage
    function closify_collage_func( $atts ) {
      
      // Check what gallery option has been selected and load shortcode accordingly
      if(!empty($this->closify_settings['html5_gallery_view']) && $this->closify_settings['html5_gallery_view']!=''){
        if($this->closify_settings['html5_gallery_view']=="on"){
            extract( shortcode_atts( array(
              'user_ids' => -1,
              'roles' => '',
              'closify_ids' => '',
              'effect' => '',
              'disable_caption' => 'false',
              'all_checked' => 'true',
              ),
              $atts ) );
        
            $roles = sanitize_text_field($roles);
            $user_ids = sanitize_text_field($user_ids);
            $closify_ids = sanitize_text_field($closify_ids);
            $effect = sanitize_text_field($effect);
            $disable_caption = sanitize_text_field($disable_caption);
            $all_checked = sanitize_text_field($all_checked);
          
            return $this->Building_Closify_Collage($user_ids, $roles, $closify_ids, $effect, $disable_caption, $all_checked);  
        }
      }
        // If you reach here then its mean the user chosen the new gallery viewer (PhotoSwipe)
        extract( shortcode_atts( array(
              'user_ids' => -1,
              'roles' => '',
              'closify_ids' => '',
              'effect' => '',
              'disable_caption' => 'false',
              'disable_title' => 'false',
              'all_checked' => 'true',
              'thumb_size' => '',
              'img_per_page' => '',
              'image_width' => '',
              'pagination' => ''
              ),
              $atts ) );
        
        $roles = sanitize_text_field($roles);
        $user_ids = sanitize_text_field($user_ids);
        $closify_ids = sanitize_text_field($closify_ids);
        $effect = sanitize_text_field($effect);
        $disable_caption = sanitize_text_field($disable_caption);
        $pagination = sanitize_text_field($pagination);
        $disable_title = sanitize_text_field($disable_title);
        $all_checked = sanitize_text_field($all_checked);
        $thumb_size = sanitize_text_field($thumb_size);
        $img_per_page = sanitize_text_field($img_per_page);
        $image_width = sanitize_text_field($image_width);

        
        
        return $this->Building_Closify_Photoswipe($user_ids, $roles, $closify_ids, $effect, $disable_caption, $all_checked, $img_per_page, $thumb_size, $image_width, $disable_title, $pagination);
    }

    function Building_Closify_Collage($user_ids, $user_roles, $closify_ids, $effect, $disable_caption, $all_checked)
    {
      $user_id_list = array();
      $closify_id_list = array();

      if($closify_ids != '')
      {
        $closify_id_list = explode(',', $closify_ids);
      }else{
        return '<b>Error:</b> No Closify ID Selected for the gallery';
      }

      $roles = array();
      
      if($all_checked != "true"){
        if($user_roles==''){
          // If there was no user roles set
          if($user_ids == -1)
          {
            $blogusers = get_users( 'blog_id=1&orderby=nicename&role=' );

            foreach ( $blogusers as $user ){
              array_push($user_id_list, $user->ID);
            }
          }else{
            // Get all user ids
            $user_id_list = explode(',', $user_ids);
          }
        }else{
          // Override user_ids if user_roles was set
          if(strpos($user_roles, ',')){
            // Roles has comma seperator
            $roles = explode(',', $user_roles);
          }
          else{
            $roles[] = $user_roles;
          }

          // Loop through users and update an array with user_ids
          $users = array();
          foreach($roles as $role){
              $args = array(
                          'blog_id' => 1,
                          'orderby' => 'nicename',
                          'role' => $role
                          );
              $current_role_users = get_users($args);
              $users = array_merge($current_role_users, $users);
          }

          foreach ( $users as $user ){
            array_push($user_id_list, $user->ID);
          }
          if(empty($user_id_list))
          {
            // Add guest
            array_push($user_id_list, -1);
          }
        }
      }

      if(empty($user_id_list)) $user_id_list = "";
      
      $images = $this->get_closify_images_for_user($closify_id_list, $user_id_list);

      // Check removed images
      $images = $this->filter_images($images);

      $closify_id = rand(10,10000);
      // Start building the HTML
      $container = '<div class="Collage effect-parent closify-collage collage-'.$closify_id.'">';

      // Default thumbnail size
      $thumbnail_size = array(300,300);

      // Update the thumbnail size with the setting value if there is any
      if(!empty($this->closify_settings['thumb_size']) && $this->closify_settings['thumb_size']!=''){
        $thumbnail_size = $this->closify_settings['thumb_size'];
      }

      $gallery_img_array = array();
      $gallery_img_array['full'] = array();
      $gallery_img_array['thumb'] = array();
      $gallery_img_array['img_title'] = array();
      
      // Loop for updating the  gallery's filter array of Full/Thumb images
      foreach($images as $img)
      {
        $closify_post = get_post($img['att_id']);

        if($closify_post->post_status!=CLOSIFY_POST_STATUS)
        {
          $gallery_img_array['img_title'][] = $closify_post->post_title;
          $img_obj = wp_get_attachment_image_src( $img['att_id'], 'full');
          $gallery_img_array['full'][] = $img_obj;
          $img_obj_small = wp_get_attachment_image_src( $img['att_id'], $thumbnail_size);
          $gallery_img_array['thumb'][] = $img_obj_small;
        }
      }

      // Closify_gallery_pre_render: Allows user to edit gallery before rendering
      $gallery_img_array = apply_filters('closify_gallery_pre_render', $gallery_img_array);
      
      for($i=0; $i<count($gallery_img_array['full']);$i++)
      {
          $container = $container.'<div class="Closify_Wrapper" data-caption="'.$gallery_img_array['img_title'][$i].'"><a href="'.$gallery_img_array['full'][$i][0].'" data-lightbox="closify-gallery-'.$closify_id.'"><img src="'.$gallery_img_array['thumb'][$i][0].'" /></a></div>';
      }

      $gallery_description = ($disable_caption=='on')?'':'jQuery(".collage-'.$closify_id.'").collageCaption();';

      $output = '<script type="text/javascript">
                jQuery(window).load(function () {
                    
                    jQuery(document).ready(function(){
                        collage'.$closify_id.'();
                        '.$gallery_description.'
                    });

                    function collage'.$closify_id.'() {
                        jQuery(".collage-'.$closify_id.'").removeWhitespace().collagePlus(
                        {
                            "fadeSpeed"     : 2000,
                            "targetHeight"  : 200,
                            "effect" : "'.$effect.'",
                            "allowPartialLastRow" : true
                        });
                    }

                    var resizeTimer = null;
                    jQuery(window).bind("resize", function() {
                        // hide all the images until we resize them
                        jQuery(".Collage .Closify_Wrapper").css("opacity", 0);
                        if (resizeTimer) clearTimeout(resizeTimer);
                        resizeTimer = setTimeout(collage'.$closify_id.', 200);
                    });
                });

          </script>';

      $container = $container.'</div>';  
      $container = $container . $output;

      return $container;
    }
    
    function Building_Closify_Photoswipe($user_ids, $user_roles, $closify_ids, $effect, $disable_caption, $all_checked, $img_per_gallery, $media_size, $image_width, $disable_title, $pagination)
    {
      $user_id_list = array();
      $closify_id_list = array();

      if($closify_ids != '')
      {
        $closify_id_list = explode(',', $closify_ids);
      }else{
        return '<b>Error:</b> No Closify ID Selected for the gallery';
      }

      $roles = array();
      
      if($all_checked != "true"){
        if($user_roles==''){
          // If there was no user roles set
          if($user_ids == -1)
          {
            $blogusers = get_users( 'blog_id=1&orderby=nicename&role=' );

            foreach ( $blogusers as $user ){
              array_push($user_id_list, $user->ID);
            }
          }else{
            // Get all user ids
            $user_id_list = explode(',', $user_ids);
          }
        }else{
          // Override user_ids if user_roles was set
          if(strpos($user_roles, ',')){
            // Roles has comma seperator
            $roles = explode(',', $user_roles);
          }
          else{
            $roles[] = $user_roles;
          }

          // Loop through users and update an array with user_ids
          $users = array();
          foreach($roles as $role){
              $args = array(
                          'blog_id' => 1,
                          'orderby' => 'nicename',
                          'role' => $role
                          );
              $current_role_users = get_users($args);
              $users = array_merge($current_role_users, $users);
          }

          foreach ( $users as $user ){
            array_push($user_id_list, $user->ID);
          }
          if(empty($user_id_list))
          {
            // Add guest
            array_push($user_id_list, -1);
          }
        }
      }

      if(empty($user_id_list)) $user_id_list = "";
      
      $images = $this->get_closify_images_for_user($closify_id_list, $user_id_list);

      // Check removed images
      $images = $this->filter_images($images);

      $random_id = rand(10,10000);

      // Default thumbnail size
      $thumbnail_size = array(300,300);

      // Update the thumbnail size with the setting value if there is any
      if(!empty($media_size) && $media_size!=''){
        $thumbnail_size = $media_size;
      }

      $gallery_img_array = array();
      $gallery_img_array['full'] = array();
      $gallery_img_array['thumb'] = array();
      $gallery_img_array['img_title'] = array();
      $gallery_img_array['img_desc'] = array();
      $gallery_img_array['img_date'] = array();
      
      $ifMultiPage = (count($images) <= intval($img_per_gallery))?false:true; 
      
      if($img_per_gallery == '' || $pagination!='on'){ 
        $img_per_gallery=3000;
        $ifMultiPage = false;
      }
      
      // If it is multipage force thumbnail
      if($ifMultiPage && $pagination == 'on'){
        $thumbnail_size = array(150,150);
      }
      
      // Loop for updating the  gallery's filter array of Full/Thumb images
      foreach($images as $img)
      {
        $closify_post = get_post($img['att_id']);

        if($closify_post->post_status!=CLOSIFY_POST_STATUS)
        {
          $gallery_img_array['img_title'][] = $closify_post->post_title;
          $gallery_img_array['img_desc'][] = $closify_post->post_content;
          $gallery_img_array['img_date'][] = $closify_post->post_date;
          $img_obj = wp_get_attachment_image_src( $img['att_id'], 'full');
          $gallery_img_array['full'][] = $img_obj;
          $img_obj_small = wp_get_attachment_image_src( $img['att_id'], $thumbnail_size);
          $gallery_img_array['thumb'][] = $img_obj_small;
        }
      }

      // Closify_gallery_pre_render: Allows user to edit gallery before rendering
      $gallery_img_array = apply_filters('closify_gallery_pre_render', $gallery_img_array);
      
      // Check if gallery is empty
      if(empty($gallery_img_array['thumb'])) return "<i>Gallery is empty</i>";
      
      if($image_width == '') $image_width=150;
      
      // Build gallery HTML template
      $gallery_div_content = Closify_Translate_Images_to_Photoswipe_HTML($gallery_img_array, $effect, $random_id, $img_per_gallery, $disable_caption, $image_width, $disable_title, $ifMultiPage);
      
      $multiPageScript = '';
      if($ifMultiPage && $pagination=='on')
      {
        $multiPageScript = '                        
                         
                        jQuery("div.closify-holder-'.$random_id.'").closifyPages({
                            containerID : "itemContainer-'.$random_id.'",
                            uid : '.$random_id.',
                            perPage       : '.$img_per_gallery.'
                        });
                        
                        /* on select change */
                        jQuery("#closify-select-'.$random_id.'").change(function(){
                          /* get new nº of items per page */
                          var newPerPage = parseInt( jQuery(this).val() );
                          
                          /* destroy jPages and initiate plugin again */
                          jQuery("div.closify-holder-'.$random_id.'").closifyPages("destroy").closifyPages({
                                containerID   : "itemContainer-'.$random_id.'",
                                uid : '.$random_id.',
                                perPage       : newPerPage
                            });
                        });';
      }else{
        $multiPageScript = '                        
                        
                        jQuery(window).load(function () {
                          var closifyMasonry = jQuery("#itemContainer-'.$random_id.'").masonry();
                        });
                        ';
      }
      
      $script = '<script type="text/javascript">
                  
                  jQuery(window).load(function () {
                      var myElem = document.getElementById("closify-photo-swipe-template");
                      if (myElem === null){
                        jQuery("body").append(\''.preg_replace("/\r?\n/", "\\n", addslashes(Closify_Photoswipe_Footer_Template())).'\');
                      }
                    });
                  
                  jQuery(function () {
                      '.$multiPageScript.'
                      // execute above function
                      initPhotoSwipeFromDOM(".closify-gallery");
                  });
                </script>';

      $gallery_div_content = $gallery_div_content . $script;

      return $gallery_div_content;
    }

    function Building_Closify_Container($id, $disableMenu, $user_id)
    {
      $prefix = '_closify_';

      $post_closify = get_post($id); 
      if($post_closify->post_type != "closify")
      {
        return "Wrong shortcode ID (".$id."), no closify associated";
      }

      $meta = get_post_meta( $id );

      if(isset($meta[$prefix.'mode_select']) && $meta[$prefix.'mode_select'][0] == 'multi'){
        return $this->Building_Arfaly($id);
      }else{
        return $this->Building_Closify($id, $disableMenu, $user_id);
      }
    }

    function Building_Closify($id, $disableMenu, $user_id)
    {
      $prefix = '_closify_';

      // Get closify meta information
      $meta = get_post_meta( $id );
      $allowGuests = false;
      global $itech_globals;

      if(isset($meta['_closify_allow_guests']))
      {
        $allowGuests = true;
      }

      if(!is_user_logged_in() && !$allowGuests)
      {
        return;
      }

      wp_get_current_user();

      static $count = 0;
      static $previous_post_id = 0;

      // This will fix blog page counter reset issue
      if($previous_post_id != $id)
      {
        $count = 0; 
      }

      $closify_info = array();

      if(isset($meta[$prefix.'width']))
        $width = $meta[$prefix.'width'][0];
      else
        $width = 200;

      if(isset($meta[$prefix.'height']))
        $height = $meta[$prefix.'height'][0];
      else
        $height = 200;

      if($width<40) $width=100;
      if($height<40) $height=100;

      if(isset($meta[$prefix.'max_file_size'])){
        $closify_info['allowedFileSize'] = intval($meta[$prefix.'max_file_size'][0]);
        $closify_info['allowedFileSize'] = $closify_info['allowedFileSize'] * 1048576;
      }
  
      if(isset($meta[$prefix.'progress']))
        $closify_info['progress'] = ($meta[$prefix.'progress'][0]=='on')?'true':'false';

      if(isset($meta[$prefix.'enforce_info']))
          $closify_info['enforceInfo'] = ($meta[$prefix.'enforce_info'][0]=='on')?'true':'false';

      if(isset($meta[$prefix.'desc_placeholder']))
        $closify_info['enterDescLbl'] = $meta[$prefix.'desc_placeholder'][0];

      if(isset($meta[$prefix.'title_placeholder']))
        $closify_info['enterTitleLbl'] = $meta[$prefix.'title_placeholder'][0];

      if($disableMenu == 'true')
        $closify_info['disableMenu'] = 'true';

      if(isset($meta[$prefix.'quality'])){
        $closify_info['quality'] = $meta[$prefix.'quality'][0];
        $pos = strpos($closify_info['quality'], '1');

        if ($pos === false) {
          $pos = strpos($closify_info['quality'], '2');
          if ($pos === false) {
            $closify_info['quality'] = '3';
          }else{
            $closify_info['quality'] = '2';
          }
        }else{
          $closify_info['quality'] = '1';
        }
      }

      if(isset($meta[$prefix.'menu_background_color']))
        $closify_info['menuBackgroundColor'] = $meta[$prefix.'menu_background_color'][0];

      if(isset($meta[$prefix.'menu_text_color']))
        $closify_info['menuTextColor'] = $meta[$prefix.'menu_text_color'][0];

      if(isset($meta[$prefix.'target_output']))
      {
        if($meta[$prefix.'target_output']!="")
          $closify_info['targetOutput'] = $meta[$prefix.'target_output'][0];
      }

      if(isset($meta[$prefix.'shadow_background_color']))
        $closify_info['shadowBackgroundColor'] = $meta[$prefix.'shadow_background_color'][0];

      if(isset($meta[$prefix.'corner_multi_selection']))
      {
        $multiselection = unserialize($meta[$prefix.'corner_multi_selection'][0]);

        foreach($multiselection as $corner)
        {
          if($corner == 'tl') $closify_info['topLeftCorner'] = false;
          if($corner == 'tr') $closify_info['topRightCorner'] = false;
          if($corner == 'br') $closify_info['bottomRightCorner'] = false;
          if($corner == 'bl') $closify_info['bottomLeftCorner'] = false;
        }
      }

      if(isset($meta[$prefix.'shape']))
        $closify_info['circularCanvas'] = 'true';

      $closify_info['url'] = admin_url( 'admin-ajax.php' );
      $closify_info['loadingImageUrl'] = plugins_url( '/assets/images/ajax-loader.gif', __FILE__ );
      $closify_info['backgroundImageUrl'] = plugins_url('/assets/images/arrow-upload-icon2.png', __FILE__ );
      $closify_info['nonce'] = wp_create_nonce( CLOSIFY_NONCE );;

      if(!isset($width) || !isset($height))
      {
        $output = 'Error: No width or height is defined';
        return $output;
      }

      $closify_options = json_encode($closify_info);
      // Removing double quotation from the keys
      $closify_options = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',$closify_options);

      // *** Pass loading gif and background photo dynamically here
      $randomVar = rand(0,9999);
      $closifyId = 'closify-'.$id.'-'.$count.'-'.$randomVar;

      add_thickbox();
      // to pass it into the custom javascript script
      $output = '<div id="'.$closifyId.'" closify-idx="'.$count.'" closify-id="'.$id.'" height="'.$height.'" width="'.$width.'"></div>';
      $output = $output . '<script type="text/javascript">
            jQuery(document).ready(function(){
              jQuery("#'.$closifyId.'").closify('.$closify_options.');
            });
          </script>';

      $count++;
      $previous_post_id = $id;

      return $output;
    }

    function Building_Arfaly($id)
    {
      $prefix = '_multi_';

      // Get closify meta information
      $meta = get_post_meta( $id );
      $allowGuests = false;

      if(isset($meta[$prefix.'allow_guests']))
      {
        $allowGuests = true;
      }

      if(!is_user_logged_in() && !$allowGuests)
      {
        return;
      }
  
      global $current_user;
      wp_get_current_user();

      static $count = 0;
      static $previous_post_id = 0;

      // This will fix blog page counter reset issue
      if($previous_post_id != $id)
      {
        $count = 0; 
      }

      $closify_info = array();

      // Check user's meta for any pre-stored info
      $existingImg = get_user_meta( $current_user->ID, 'closify_img_'.$id, true ); 

      if(isset($existingImg) && !empty($existingImg) && isset($existingImg["closify-".$id."-".$count]))
      {
        $img = wp_get_attachment_url($existingImg["closify-".$id."-".$count]);

        if($img == "")
        {
          delete_user_meta($current_user->ID, 'closify_img_'.$id );
        }

        $closify_info['startWithThisImg'] = $img;
      }

      if(isset($meta[$prefix.'max_file_size'])){
        $closify_info['allowedFileSize'] = intval($meta[$prefix.'max_file_size'][0]);
        $closify_info['allowedFileSize'] = $closify_info['allowedFileSize'] * 1048576;
      }

      if(isset($meta[$prefix.'debug']))
        $closify_info['debug'] = ($meta[$prefix.'debug'][0]=='on')?'true':'false';

      if(isset($meta[$prefix.'enforce_info']))
        $closify_info['enforceInfo'] = ($meta[$prefix.'enforce_info'][0]=='on')?'true':'false';
        
      if(isset($meta[$prefix.'disable_drag_drop']))
        $closify_info['dragDrop'] = ($meta[$prefix.'disable_drag_drop'][0]=='on')?'false':'true';

      if(isset($meta[$prefix.'desc_placeholder']))
        $closify_info['enterDescLbl'] = $meta[$prefix.'desc_placeholder'][0];

      if(isset($meta[$prefix.'title_placeholder']))
        $closify_info['enterTitleLbl'] = $meta[$prefix.'title_placeholder'][0];

      if(isset($meta[$prefix.'image_preview']))
        $closify_info['disablePreview'] = ($meta[$prefix.'image_preview'][0]=='on')?'true':'false';

      if(isset($meta[$prefix.'uploader_theme'])){
        $closify_info['theme'] = $meta[$prefix.'uploader_theme'][0];
      }
      
      if(isset($meta[$prefix.'file_upload_limit']))
        $closify_info['limitNumberofFiles'] = $meta[$prefix.'file_upload_limit'][0];
      
      if(isset($meta[$prefix.'logo_color']))
        $closify_info['logoColor'] = $meta[$prefix.'logo_color'][0];

      if(isset($meta[$prefix.'border_color']))
        $closify_info['borderColor'] = $meta[$prefix.'border_color'][0];

      if(isset($meta[$prefix.'label_color']))
        $closify_info['labelColor'] = $meta[$prefix.'label_color'][0];

      if(isset($meta[$prefix.'text_color']))
        $closify_info['textColor'] = $meta[$prefix.'text_color'][0];

      if(isset($meta[$prefix.'target_debug']))
      {
        if($meta[$prefix.'target_debug']!="")
          $closify_info['targetOutput'] = $meta[$prefix.'target_debug'][0];
      }

      if(isset($meta[$prefix.'label']))
      {
          $closify_info['label'] = $meta[$prefix.'label'][0];
      }

      $closify_info['url'] = admin_url( 'admin-ajax.php' );
      $closify_info['nonce'] = wp_create_nonce( CLOSIFY_NONCE );;

      $closify_options = json_encode($closify_info);

      // Removing double quotation from the keys
      $closify_options = preg_replace('/"([a-zA-Z]+[a-zA-Z0-9_]*)":/','$1:',$closify_options);

      // *** Pass loading gif and background photo dynamically here

      $closifyId = 'multi-'.$id.'-'.$count;
      // to pass it into the custom javascript script
      $output = '<div id="'.$closifyId.'" closify-idx="'.$count.'" closify-id="'.$id.'"></div>';
      $output = $output . '<script type="text/javascript">
            jQuery(document).ready(function(){
              jQuery("#'.$closifyId.'").arfaly('.$closify_options.');
            });
          </script>';

      $count++;
      $previous_post_id = $id;

      return $output;
    }

    function set_custom_edit_closify_columns($columns) {

        unset( 
          $columns['taxonomy-closify_category'],
          $columns['taxonomy-closify_tag'],
          $columns['comments']
        );

        $columns['shape'] = __( 'Shape', CLOSIFY_TEXT_DOMAIN );
        $columns['author'] = __( 'Author', CLOSIFY_TEXT_DOMAIN );
        $columns['shortcode'] = __( 'Shortcode', CLOSIFY_TEXT_DOMAIN );
        $columns['width'] = __( 'Width', CLOSIFY_TEXT_DOMAIN );
        $columns['height'] = __( 'Height', CLOSIFY_TEXT_DOMAIN );
        $columns['quality'] = __( 'Quality', CLOSIFY_TEXT_DOMAIN );

        return $columns;
    }

    /*
    * Adding the column
    */
    function rd_user_id_column( $columns ) {
        $columns['user_id'] = 'ID';
        return $columns;
    }

    /*
    * Column content
    */
    function rd_user_id_column_content($value, $column_name, $user_id) {
        if ( 'user_id' == $column_name )
            return $user_id;
        return $value;
    }
    
    public function custom_closify_column( $column, $post_id ) {

      $post = get_post($post_id);

        switch ( $column ) {
            case 'shape' :
                $shapeIndx = get_post_meta( $post_id, '_closify_shape', true);
                $shape = '';
                if($shapeIndx == 0) $shape = 'Rectangular';
                else $shape = 'Circular';

                echo $shape;

                break;
            case 'width' :
                $width = get_post_meta( $post_id, '_closify_width', true);
                if ( is_string( $width ) )
                    echo $width;
                else
                    _e( 'Unable to get width', CLOSIFY_TEXT_DOMAIN );
                break;
            case 'height' :
                $height = get_post_meta( $post_id, '_closify_height', true);
                if ( is_string( $height ) )
                    echo $height;
                else
                    _e( 'Unable to get height', CLOSIFY_TEXT_DOMAIN );
                break;
            case 'quality' :
                $quality = get_post_meta( $post_id, '_closify_quality', true);
                if ( is_string( $quality ) )
                    echo $quality;
                else
                    _e( 'Unable to get quality', CLOSIFY_TEXT_DOMAIN );
                break;
            case 'shortcode' :
                  echo '<strong>[closify id="'.$post->ID.'"]</strong>';
                break;

        }
    }

    // Process multi-images
    public function itech_submit_multi_closify()
    {
        // Sanitize the whole input
        $_POST  = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);

        $prefix = '_multi_';
        global $itech_globals;
        $nonceValidation = false;
        $post_id = "";
        $allowGuests = false;
        $allowedExts = array();
        $extReference = array(
            "gif" => array("image/gif"),
            "jpeg" => array("image/jpeg", "image/pjpeg"),
            "jpg" => array("image/jpeg", "image/pjpeg"),
            "png"=> array("image/png", "image/x-png"),
            "bmp" => array("image/bmp", "image/x-windows-bmp"),
            "tiff" => array("image/x-tiff", "image/tiff"),
            "tif" => array("image/x-tiff", "image/tiff"),
            "svg" => array("image/svg+xml"),
            "psd" => array("application/octet-stream"),
            "ai" => array("application/postscript"),
            "eps" => array("application/postscript")
        );

        if(isset($_POST['closify-id']))
        {
          $post_id = intval($_POST['closify-id']);
          // Get closify meta information
          $meta = get_post_meta( $post_id );

          if(isset($meta[$prefix.'allow_guests']))
          {
            $allowGuests = true;
          }
        }else{
          $allowGuests = true;
        }

        if(!is_user_logged_in() && !$allowGuests)
        {
          $this->Report_Error("You do not have permission!");
        }

        global $current_user;
        wp_get_current_user();

        // Nonce security validation
        if(isset($_POST['nonce']))
        {
          $nonceValidation = wp_verify_nonce( $_POST['nonce'], CLOSIFY_NONCE );
          if(!$nonceValidation)
          {
            $this->Report_Error("You violated a security check!");
          }
        }else{
          $this->Report_Error("Security parameter is missing!");
        }

        if(isset($meta[$prefix.'image_formats']))
        {
          $allowedExts = unserialize($meta[$prefix.'image_formats'][0]);
        }else{
          $allowedExts = array();
        }

        // Default max file size
        $maxFileSize = 1024 * 1024 * 1; // Max 10MB

        if(isset($meta[$prefix.'max_file_size']))
        {
          $maxFileSize = intval($meta[$prefix.'max_file_size'][0]);
          $maxFileSize = $maxFileSize * 1048576;
        }

        $localFileDestination     = CLOSIFY_ITECH_PLUGIN_URI.$itech_globals['destination_folder'].DIRECTORY_SEPARATOR; // From server side, define the uploads folder url 

        ########################################

        // Check if it is a delete command
        if(isset($_POST['command']) && $_POST['command']=='delete')
        {
            if(!isset($_POST['raqmkh']))
            {
                $json = array();
                $json['data'] = "Oops. Something wrong with deletion!";
                $json['status'] = 'false';

                $this->Report_Error($json['data']);
            }

            $att_del_id = intval(base64_decode($_POST['raqmkh']));

            // Handle file deletion here
            $result = wp_delete_post( $att_del_id, true );

            if($result == "false"){
              $json['data'] = "The object couldn't be deleted!";
              $json['status'] = 'false';

              $this->Report_Error($json['data']);
            }else
            {
              echo base64_decode($_POST['arfalyfn']).' Has been deleted!';
              die();
            }
        }

        // Create uploads folder if doesn't exist
        if (!file_exists($localFileDestination)) {
            mkdir($localFileDestination, 0766, true);
        }

        // Enforce extensions to be converted into lower case
        $allowedExts = array_map('strtolower', $allowedExts);

        // Business Logic
        $allowedM = false;
        $allowedS = false;
        $json = array(
            "status" => 'false',
        );

        // Check mime and apply to-lower case comparison to avoid case sensitive comparing
        foreach($allowedExts as $value)
        {
          if(array_key_exists($value,$extReference))
          {
            foreach($extReference[$value] as $mime)
            {
              if(strtolower($_FILES["SelectedFile"]["type"]) == strtolower($mime))
              {
                  $allowedM = true;
              }
            }
          }
        }

        if(!$allowedM) $this->Report_Error("Unsupported file type!");

        if($_FILES["SelectedFile"]["size"] < $maxFileSize)
        {
            $allowedS = true;
        }else{
            $json['data'] = "File size has exceeded the limit (".$maxFileSize.")!";
            $this->Report_Error($json['data']);
        }

        if( $allowedM && $allowedS)
        {
          if ($_FILES["SelectedFile"]["error"] > 0) {
            $this->Report_Error("Return Code: " . $_FILES["SelectedFile"]["error"]);
          } else {

            /*==================================*/
            // Increase execution time limit
            set_time_limit(0);

            // Save image to library and attach it to the post
            // OLD Method::media_sideload_image($targetImgURLPath, $post_id, 'Arfaly ['.$title.'] Uploaded by: '.$current_user->display_name );
            $post_data = array('post_status'=>CLOSIFY_POST_STATUS);
            $post_data['post_title'] = '';
            $post_data['post_content'] = '';

            if(isset($_POST['title']))
              $post_data['post_title'] = sanitize_text_field( $_POST['title'] );

            if(isset($_POST['desc']))
              $post_data['post_content'] = sanitize_text_field ( $_POST['desc'] );

            // Stage 1: Filter the original filename
            $filtered_file_name = $this->filter_filename($_FILES["SelectedFile"]["name"]);
            
            //Add unique timestamp to avoid duplication
            $fileNameInfo = pathinfo($filtered_file_name);
            $filtered_file_name = $fileNameInfo['filename'].'-'.time().'.'.$fileNameInfo['extension'];
            
            // Stage 2: Prepare the temp destination for the new filtered filename
            $filtered_file_destination = CLOSIFY_PRESS_TMP_FOLDER.DIRECTORY_SEPARATOR.$filtered_file_name;
            
            // Stage 3: Copy the new file to temp folder with the filtered name
            // $att_id = media_handle_upload( "SelectedFile", $post_id, $post_data );
            move_uploaded_file ( $_FILES["SelectedFile"]["tmp_name"] , $filtered_file_destination);
            
            // Stage 4: Pass the new filtered temp file to be uploaded to Wordpress Media
            $att_id = $this->closify_press_add_file_to_media_uploader( $post_id, $filtered_file_name ,$post_data['post_title'], $post_data['post_content'] );

            // Delete original file after adding to Media
            @unlink($filtered_file_destination);

            // Check if this is the last submitted file ?
            $finished = false;
            if(isset($_POST['patch-finished']) && $_POST['patch-finished']=='true')
            {
              $finished = true;
            }
            // Check if this is the first submitted file ?
            $started = false;
            if(isset($_POST['patch-started']) && $_POST['patch-started']=='true')
            {
              $started = true;
            }

            // Reset execution time limit
            set_time_limit(120);

            if ( is_wp_error( $att_id ) ) {
              $this->Report_Error($att_id->get_error_message());
            }

            $image_attributes = wp_get_attachment_url( $att_id ); // returns an array
            if( $image_attributes ) {
              $targetImgURLPath = $image_attributes;
            }else
            {
              // ???? Needs attention: What is this function
              $this->Report_Error('Error fetching image url!');
            }

            // Add custom meta for tagging the post as multi
            add_post_meta($att_id, CLOSIFY_POST_EXTRA_INFO, array('is_multi'=>true));

            if(isset($_POST['fileIndx']) && $_POST['fileIndx']=='0')
            {
              // Trigger batch upload email notification
              do_action('closify_new_image_uploaded', $att_id, $targetImgURLPath);
            }

            $json = array(
                "status" => 'true',
                "data" => $_FILES["SelectedFile"]["name"].' Has been successfully uploaded!',
                "attid" => $att_id,
                "newFileName" => $_FILES["SelectedFile"]["name"],
                "fullPath" => $targetImgURLPath
            );
          }
        }

        //====================== Approval management ===============*/
        // Change attachment status to arfaly-press
        global $wpdb;
        $wpdb->query( 
            $wpdb->prepare( 
                "UPDATE $wpdb->posts SET post_status = '".CLOSIFY_POST_STATUS."' WHERE ID = %d", 
                $att_id 
            )
        );

        // Check if auto approve is been checked and generate post_data 
        if($this->closify_settings['auto_approve_user_files'] == 'on')
        {
          if(is_user_logged_in()){
            $this->approve_wordpress_attachment($att_id);
          }
        }

        if($this->closify_settings['auto_approve_any_files'] == 'on')
        {
          $this->approve_wordpress_attachment($att_id);
        }
        /*==========================================================*/

        ###########attId# Put your custom code here ##############
        // Here you will receive the trimmed image that is generated from enabling "hardTrim" option.
        // Put your SQL/PHP code here to link the saved, processed and trimmed image $json["imgSrc"] to any specific user or page.

        // Print out results
        echo json_encode($json);

        die();
    }
  
    // Ajax post sumbission handler
    public function itech_submit_closify()
    {
        $nonceValidation = false;
        $post_id = "";
        $allowGuests = false;
        
        if(isset($_POST['closify-id']))
        {
          $post_id = intval($_POST['closify-id']);
          // Get closify meta information
          $meta = get_post_meta( $post_id );

          if(isset($meta['_closify_allow_guests']))
          {
            $allowGuests = true;
          }
        }else{
          $allowGuests = true;
        }

        if(!is_user_logged_in() && !$allowGuests)
        {
          $this->Report_Error("You do not have permission!");
        }

        // Nonce security validation
        if(isset($_POST['nonce']))
        {
          $nonceValidation = wp_verify_nonce( $_POST['nonce'], CLOSIFY_NONCE );
          if(!$nonceValidation)
          {
            $this->Report_Error("You violated a security check!");
          }
        }else{
          $this->Report_Error("Security parameter is missing!");
        }

        global $current_user;
        wp_get_current_user();

        ############ Edit settings ##############
        $ThumbSquareSize 		= 200; //Thumbnail will be 200x200
        $thumbnails				= false; // Disable generating thumbnails
        $ThumbPrefix			= "thumb_"; //Normal thumb Prefix
        $uploadFolder			= "uploads";
        $DestinationDirectory	= CLOSIFY_PRESS_TMP_FOLDER.DIRECTORY_SEPARATOR; //specify upload directory ends with / (slash)
        $Quality 				= 90; //jpeg quality
        ##########################################

        if(isset($_POST) && isset($_POST["w"]) && isset($_POST["h"]) && isset($_POST["id"]) && isset($_POST["ImageName"]))
        {
            ############ Dynamic settings ##############

            $imageQuality			= intval($_POST['quality']);
            $imageName				= sanitize_text_field($_POST["ImageName"]);
            $container_width 		= intval($_POST["w"]);
            $container_height 		= intval($_POST["h"]);
            $dynamicStorage			= isset($_POST["dynamicStorage"])?sanitize_text_field($_POST["dynamicStorage"]):false;
            ##########################################

            // Create uploads folder if doesn't exist
            if (!file_exists($DestinationDirectory)) {
                mkdir($DestinationDirectory, 0777, true);
            }

            // check if $_FILES['ImageFile'] not empty
            if(!isset($_FILES[$imageName]) || !is_uploaded_file($_FILES[$imageName]['tmp_name']))
            {
                $this->Report_Error('Something wrong with uploaded file, something missing! Please sanitize your filename before uploading');	// output error when above checks fail.
            }

            // Random number will be added after image name
            $RandomNumber 	= rand(0, 9999999999); 

            $ImageName 		= str_replace(' ','-',strtolower($_FILES[$imageName]['name'])); //get image name
            $TempSrc	 	= $_FILES[$imageName]['tmp_name']; // Temp name of image file stored in PHP tmp folder
            $ImageType	 	= $_FILES[$imageName]['type']; //get file type, returns "image/png", image/jpeg, text/plain etc.

            list($CurWidth,$CurHeight)=getimagesize($TempSrc);

            //Let's check allowed $ImageType, we use PHP SWITCH statement here
            switch(strtolower($ImageType))
            {
                case 'image/png':
                    //Create a new image from file 
                    $CreatedImage =  imagecreatefrompng($_FILES[$imageName]['tmp_name']);
                    break;
                case 'image/gif':
                    $CreatedImage =  imagecreatefromgif($_FILES[$imageName]['tmp_name']);
                    break;			
                case 'image/jpeg':
                case 'image/pjpeg':
                    $CreatedImage = imagecreatefromjpeg($_FILES[$imageName]['tmp_name']);
                    break;
                default:
                    $this->Report_Error('Unsupported File!'); //output error and exit
            }
            
            // Check if the image is flipped, or reoriented and restore it
            $exif = @exif_read_data($_FILES[$imageName]['tmp_name']);
            if(!empty($exif['Orientation'])) {
                switch($exif['Orientation']) {
                    case 8:
                        $CreatedImage = imagerotate($CreatedImage,90,0);
                        $temp = $CurWidth;
                        $CurWidth = $CurHeight;
                        $CurHeight = $temp;
                        do_action('closify_image_oriented', 90, $CreatedImage, $CurWidth, $CurHeight);
                        break;
                    case 3:
                        $CreatedImage = imagerotate($CreatedImage,180,0);
                        $temp = $CurWidth;
                        $CurWidth = $CurHeight;
                        $CurHeight = $temp;
                        do_action('closify_image_oriented', 180, $CreatedImage, $CurWidth, $CurHeight);
                        break;
                    case 6:
                        $CreatedImage = imagerotate($CreatedImage,-90,0);
                        $temp = $CurWidth;
                        $CurWidth = $CurHeight;
                        $CurHeight = $temp;
                        do_action('closify_image_oriented', -90, $CreatedImage, $CurWidth, $CurHeight);
                        break;
                }
            }

            $image_ratio = $CurWidth / $CurHeight;

            $src_width = $CurWidth;
            $src_height = $CurHeight;
            // Resize image proportionally according to the size of container
            if($CurWidth > $container_width)
            {
                $processed = true;
                $CurWidth = $container_width;
                $CurHeight = $CurWidth / $image_ratio;
            }
            if($CurHeight > $container_height)
            {
                $CurHeight = $container_height;
                $CurWidth = $CurHeight * $image_ratio;
            }

            if($CurWidth < $container_width)
            {
                $processed = true;
                $CurWidth = $container_width;
                $CurHeight = $CurWidth / $image_ratio;
            }
            if($CurHeight < $container_height){
                $CurHeight = $container_height;
                $CurWidth = $CurHeight * $image_ratio;
            }

            $supposedWidth = $CurWidth;
            $supposedHeight = $CurHeight;

            switch($imageQuality)
            {
                case 2:
                    if($container_width * 2 <= $src_width && $container_height * 2 <= $src_height)
                    {
                        $CurHeight = $CurHeight * 2;
                        $CurWidth = $CurWidth * 2;
                    }
                    break;
                case 3:
                    if($container_width * 3 <= $src_width && $container_height * 3 <= $src_height)
                    {
                        $CurHeight = $CurHeight * 3;
                        $CurWidth = $CurWidth * 3;
                    }
                    break;
                default:
                    break;
            }

            //Get file extension from Image name, this will be added after random name
            $ImageExt = substr($ImageName, strrpos($ImageName, '.'));
            $ImageExt = str_replace('.','',$ImageExt);

            //remove extension from filename
            $ImageName 		= preg_replace("/\\.[^.\\s]{3,4}$/", "", $ImageName); 

            //Construct a new name with random number and extension.
            if($dynamicStorage == "true"){
                $NewImageName = intval($_POST["id"]).'.jpg';
            }else{
                $NewImageName = $ImageName.'-'.$RandomNumber.'.'.$ImageExt;
            }

            //set the Destination Image
            $thumb_DestRandImageName 	= $DestinationDirectory.$ThumbPrefix.$NewImageName; //Thumbnail name with destination directory
            $DestRandImageName 			= $DestinationDirectory.$NewImageName; // Image with destination directory

            do_action('closify_image_pre_process', $DestRandImageName);
            do_action('closify_image_pre_resize', $DestRandImageName);
            //Resize image to Specified Size by calling $this->resizeImage function.
            if($this->closify_processor->resizeImage($CurWidth,$CurHeight,$DestRandImageName,$CreatedImage,$Quality,$ImageType, $src_width, $src_height))
            {
                do_action('closify_image_post_resize', $DestRandImageName);
                do_action('closify_image_pre_cropping', $DestRandImageName);
                    if($thumbnails){
                        //Create a square Thumbnail right after, this time we are using cropImage() function
                        if(!$this->closify_processor->cropImage($CurWidth,$CurHeight,$ThumbSquareSize,$thumb_DestRandImageName,$CreatedImage,$Quality,$ImageType))
                        {
                            $this->Report_Error('Error Creating thumbnail');
                        }
                        do_action('closify_image_post_cropping', $DestRandImageName);
                        do_action('closify_image_post_process', $DestRandImageName);
                        /*
                        We have succesfully resized and created thumbnail image
                        We can now output image to user's browser or store information in the database
                        */
                        $json = array(
                            "imgSrc" => (CLOSIFY_ITECH_PLUGIN_URL.'/'.$uploadFolder.'/'.$NewImageName),
                            "thumbSrc" => ($imageURLFromClient.$ThumbPrefix.$NewImageName),
                            "width" => $supposedWidth,
                            "height" => $supposedHeight
                            );

                        echo json_encode($json);
                        die();
                    }else{
                        $json = array(
                            "imgSrc"=> (CLOSIFY_ITECH_PLUGIN_URL.'/'.$uploadFolder.'/'.$NewImageName),
                            "thumbSrc"=> 'none',
                            "width" => $supposedWidth,
                            "height" => $supposedHeight
                        );

                        echo json_encode($json);
                        die();
                    }

            }else{
                $this->Report_Error('Resize Error');
            }
        }

        // Handling position save command
        if(isset($_POST["top"]) || isset($_POST["left"]))
        {
            ############ Put your custom code here ##############
            // When somebody saves a picture you can read "top" and "left" and save them, so it become stored
            // play with "left / top" image position

            if(!isset($_POST['hardTrim']))
            {
                $json = array(
                    "msg" => 'true'
                );

                echo json_encode($json);
                die();
            }

            $TempSrc = sanitize_text_field($_POST['src']);
            $quality = intval($_POST['quality']);
            $pathInfo = pathinfo($TempSrc);
            $y = intval($_POST["top"]);
            $x = intval($_POST["left"]);
            $cWidth = intval($_POST["width"]);
            $cHeight = intval($_POST["height"]);

            // Fixed ? character availablility
            if(strpos($pathInfo['basename'],'?') !== false)
            {
                $positiona = strpos($pathInfo['basename'], '?');
                $pathInfo['basename'] = substr($pathInfo['basename'],0,$positiona);
            }
            if(strpos($pathInfo['extension'],'?') !== false)
            {
                $positionb = strpos($pathInfo['extension'], '?');
                $pathInfo['extension'] = substr($pathInfo['extension'],0,$positionb);
            }

            list($CurWidth,$CurHeight) = getimagesize($DestinationDirectory.$pathInfo['basename']);

            // The image has already been refined
            if($quality == 2)
            {
                // If image size greater than container's size
                if(($CurWidth-10)>$cWidth  && ($CurHeight-10)>($cHeight))
                {
                    $y = $y*2;
                    $x = $x*2;
                    $cWidth = $cWidth*2;
                    $cHeight = $cHeight*2;
                }
            }elseif($quality == 3)
            {
                // If image size greater than container's size
                if(($CurWidth-10)>($cWidth*2) && ($CurHeight-10)>($cHeight*2))
                {
                    $y = $y*3;
                    $x = $x*3;
                    $cWidth = $cWidth*3;
                    $cHeight = $cHeight*3;
                }
            }

            if($_POST['hardTrim'] == true)
            {
                try{
                    $width = -1;
                    $height = -1;
                    $filename = $DestinationDirectory.$pathInfo['basename'];
                    $imgTempName = '';

                    list($width, $height) = getimagesize($filename);

                    // Resample
                    $image_p = imagecreatetruecolor($cWidth, $cHeight);

                    switch (strtolower($pathInfo['extension'])) { 
                        case "gif" : 
                            $image = imageCreateFromGif($filename); 
                            $background = imagecolorallocate($image_p, 0, 0, 0);
                            imagecolortransparent($image_p, $background);
                        break; 
                        case "jpeg": 
                        case "jpg": 
                            $image = imageCreateFromJpeg($filename); 
                        break; 
                        case "png": 
                            $image = imageCreateFromPng($filename); 
                            imagealphablending( $image_p, false );
                            imagesavealpha( $image_p, true );
                        break; 
                        case "bmp": 
                            $image = imageCreateFromBmp($filename); 
                        break; 
                    }  
                    if(!imagecopyresampled($image_p, $image, 0, 0, abs($x),abs($y), $cWidth, $cHeight, $cWidth, $cHeight))
                    {
                        $this->Report_Error('Image could not be processed! Only [png,jpg,gif] are allowed');
                    }

                    switch(strtolower($pathInfo['extension']))
                    {
                        case 'png':
                            // Output
                            $imgTempName = time().'image.png';
                            // Replace invalid media letters
                            $invalidLetters = array(" ","(",")","{","}","[","]");
                            $imgTempName = str_replace($invalidLetters, "", $imgTempName);
                            imagepng($image_p,$DestinationDirectory.$imgTempName);
                            break;
                        case 'gif':
                            $imgTempName = time().'image.gif';
                            // Replace invalid media letters
                            $invalidLetters = array(" ","(",")","{","}","[","]");
                            $imgTempName = str_replace($invalidLetters, "", $imgTempName);
                            imagegif($image_p,$DestinationDirectory.$imgTempName);
                            break;			
                        case 'jpeg':
                        case 'jpg':
                            $imgTempName = time().'image.jpg';
                            // Replace invalid media letters
                            $invalidLetters = array(" ","(",")","{","}","[","]");
                            $imgTempName = str_replace($invalidLetters, "", $imgTempName);
                            imagejpeg($image_p,$DestinationDirectory.$imgTempName,90);
                            break;
                        default:
                            $this->Report_Error('Image could not be processed! Only [png,jpg,gif] are allowed');
                            return false;
                    }

                    /*==================================*/
                    // Increase execution time limit
                    // set_time_limit(0);

                    //Destroy image, frees memory	
                    if(is_resource($image)) {imagedestroy($image);} 

                    $title = get_the_title($post_id);
                    $targetImg = $pathInfo['dirname'].'/'.$imgTempName;

                    // Save image to library and attach it to the post
                    //$wp_error = media_sideload_image($targetImg, $post_id, 'Closify ['.$title.'] Uploaded by: '.$current_user->display_name );
                    $wp_error = $this->closify_press_add_file_to_media_uploader($post_id, $imgTempName, '', 'Closify ['.$title.'] Uploaded by: '.$current_user->display_name);

                    if ( is_wp_error( $wp_error ) ) {
                      $this->Report_Error($wp_error->get_error_message());
                    }

                    $att_id = $this->closify_processor->itech_get_attachment_id_from_url($pathInfo['dirname'].'/'.$imgTempName);

                    /*==================================*/
                    // Reset execution time limit
                    set_time_limit(120);

                    $closify_post = get_post($att_id);
                    $closify_post->post_status = CLOSIFY_POST_STATUS;

                    if(isset($_POST['title']))
                      $closify_post->post_title = sanitize_text_field($_POST['title']);

                    if(isset($_POST['desc']))
                      $closify_post->post_content = sanitize_text_field ($_POST['desc']);

                    wp_update_post($closify_post);

                    // Add custom meta for tagging the post as multi
                    add_post_meta($att_id, CLOSIFY_POST_EXTRA_INFO, array('is_multi'=>false));

                    // Trigger image upload action for email notification
                    do_action('closify_new_image_uploaded', $att_id, $pathInfo['dirname'].'/'.$imgTempName);

                    $json = array(
                        "msg" => 'true',
                        "height" => intval($_POST["height"]),
                        "width" => intval($_POST["width"]),
                        "imgSrc" => $pathInfo['dirname'].'/'.$imgTempName,
                        "attid" => $att_id
                    );

                    //====================== Approval management ===============*/
                    // Change attachment status to closify-press

                    global $wpdb;
                    $wpdb->query( 
                        $wpdb->prepare( 
                            "UPDATE $wpdb->posts SET post_status = '".CLOSIFY_POST_STATUS."' WHERE ID = %d", 
                            $att_id 
                        )
                    );
                    
                    // Check if auto approve is been checked and generate post_data 
                    if($this->closify_settings['auto_approve_user_files'] == 'on')
                    {
                      if(is_user_logged_in()){
                        $this->approve_wordpress_attachment($att_id);
                      }
                    }

                    if($this->closify_settings['auto_approve_any_files'] == 'on')
                    {
                      $this->approve_wordpress_attachment($att_id);
                    }
                    /*==========================================================*/
                    
                    echo json_encode($json);

                }catch(Exception $e)
                {
                    $this->Report_Error($e->getMessage());
                    return;
                }
            }
        }

        // Handling image delete command
        // POST
        // command: delete
        // id: The ID name of the specific image container that has been deleted
        if(isset($_POST["command"]) && isset($_POST["id"]) && $_POST["command"]=="delete")
        {
            if(!isset($_POST['raqmkh']))
            {
                $json['data'] = "Oops. Something went wrong with deletion!";
                $json['status'] = 'false';

                $this->Report_Error($json['data']);
            }

            $att_del_id = base64_decode($_POST['raqmkh']);

            // Handle file deletion here
            $result = wp_delete_post( $att_del_id, true );        

            if($result == "false"){
              $json['data'] = "The image couldn't be deleted!";
              $json['status'] = 'false';

              $this->Report_Error($json['data']);
            }else
            {
              echo 'Image has been deleted!';
              die();
            }
        }

      die();
    }

    function Report_Error($error)
    {
        $json = array(
            "msg" => 'false',
            "error" => $error
        );

        echo json_encode($json);
        die();
    }

    /**
    * Copies a file from the a subdirectory of the root of the WordPress installation
    * into the uploads directory, attaches it to the given post ID, and adds it to
    * the Media Library.
    *
    * @param    int      $post_id    The ID of the post to which the image is attached.
    * @param    string   $filename   The name of the file to copy and to add to the Media Library
    */
    function closify_press_add_file_to_media_uploader( $post_id, $filename, $description, $title) {
        // Locate the file in a subdirectory of the root of the installation
        $file = CLOSIFY_PRESS_TMP_FOLDER . DIRECTORY_SEPARATOR . $filename;
        // If the file doesn't exist, then write to the error log and duck out
        if ( ! file_exists( $file ) || 0 === strlen( trim( $filename ) ) ) {
            return false;
        }
        /* Read the contents of the upload directory. We need the
        * path to copy the file and the URL for uploading the file.
        */
        $uploads = wp_upload_dir();

        $uploads_dir = $uploads['path'];
        $uploads_url = $uploads['url'];
        // Copy the file from the root directory to the uploads directory
        copy( $file, trailingslashit( $uploads_dir ) . $filename );
        /* Get the URL to the file and grab the file and load 
        * it into WordPress (and the Media Library)
        */
        $file_path = $uploads_dir . '/' . $filename;
        $file_url = $uploads_url. '/' . $filename;

        $id = $this->closify_press_media_sideload_image( $file_path, $file_url, $post_id, $title, $description );
        // If there's an error, then we'll write it to the error log.
        if ( is_wp_error( $id ) ) {
            return false;
        }

        return $id;
    }


    function closify_press_media_sideload_image( $file_path, $file_url, $post_id, $title, $desc = null, $return = 'html' ) {
      if ( ! empty( $file_path ) ) {

          // Check the type of file. We'll use this as the 'post_mime_type'.
          $filetype = wp_check_filetype( basename( $file_path ), null );
          $attachment = array(
              'guid'           => $file_url, 
              'post_parent' => $post_id,
              'post_mime_type' => $filetype['type'],
              'post_title'     => preg_replace( '/\.[^.]+$/', '', basename( $file_path ) ),
              'post_content'   => ''
          );

          if($title!='' && $desc!='')
          {
            $attachment['post_title'] = $title;
            $attachment['post_content'] = $desc;
          }

          $id = wp_insert_attachment( $attachment, $file_path, $post_id );
          wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $file_path ) );

          // If error storing permanently, unlink.
          if ( is_wp_error( $id ) ) {
              @unlink( $file_path );
              return $id;
          }

          $src = wp_get_attachment_url( $id );
      }

      // Finally, check to make sure the file has been saved, then return the HTML.
      if ( ! empty( $src ) ) {
          if ( $return === 'src' ) {
              return $src;
          }

          return $id;
      } else {
          return new WP_Error( 'wp_insert_attachment_failed', __('Wordpress failed to upload attachment', ARFALY_TEXT_DOMAIN) );
      }
    }
    
    function closify_add_menu_items(){
        add_submenu_page('edit.php?post_type=closify', 'Manage Uploads', 'Closify Manage Uploads', 'edit_posts','closify_manage_list', array($this, 'closify_render_menu'));
    }

    function closify_render_menu(){
    
      require_once (ABSPATH . '/wp-admin/admin.php' );
      
      $title = __( 'Manage Closify Uploads', 'closify-uploader' );
      set_current_screen( 'upload' );
      if ( ! current_user_can( 'upload_files' ) )
          wp_die( __( 'You do not have permission to upload files.', 'closify-uploader' ) );

      ?>
      <div class="wrap">
      <?php screen_icon(); ?>
      <h2><?php echo esc_html( $title ); ?> <?php
      if ( isset( $_REQUEST['s'] ) && $_REQUEST['s'] )
          printf( '<span class="subtitle">' . __( 'Search results for &#8220;%s&#8221;', 'closify-uploader' ) . '</span>', get_search_query() ); ?>
      </h2>

      <?php
      $message = '';
      $closify_media_list = new Closify_Media_List_Table();
      $pagenum = $closify_media_list->get_pagenum();
      $doaction = $closify_media_list->current_action();
      $message = $this->closify_process_bulk_action($closify_media_list);
      $closify_media_list->prepare_items();

      if ( isset( $_GET['posted'] ) && (int) $_GET['posted'] ) {
          $message = __( 'Media attachment updated.', 'closify-uploader' );
          $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'posted' ), $_SERVER['REQUEST_URI'] ));
      }

      if ( isset( $_GET['attached'] ) && (int) $_GET['attached'] ) {
          $attached = (int) $_GET['attached'];
          $message = sprintf( _n( 'Reattached %d attachment.', 'Reattached %d attachments.', $attached ), $attached );
          $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'attached' ), $_SERVER['REQUEST_URI'] ));
      }

      if ( isset( $_GET['deleted'] ) && (int) $_GET['deleted'] ) {
          $message = sprintf( _n( 'Media attachment permanently deleted.', '%d media attachments permanently deleted.', $_GET['deleted'] ), number_format_i18n( $_GET['deleted'] ) );
          $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'deleted' ), $_SERVER['REQUEST_URI'] ));
      }

      if ( isset( $_GET['trashed'] ) && (int) $_GET['trashed'] ) {
          $message = sprintf( _n( 'Media attachment moved to the trash.', '%d media attachments moved to the trash.', $_GET['trashed'] ), number_format_i18n( $_GET['trashed'] ) );
          $message .= ' <a href="' . esc_url( wp_nonce_url( 'edit.php?post_type=closify?doaction=undo&action=untrash&ids='.( isset( $_GET['ids'] ) ? $_GET['ids'] : '' ), "bulk-media" ) ) . '">' . __( 'Undo', 'closify-uploader' ) . '</a>';
          $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'trashed' ), $_SERVER['REQUEST_URI'] ));
      }

      if ( isset( $_GET['untrashed'] ) && (int) $_GET['untrashed'] ) {
          $message = sprintf( _n( 'Media attachment restored from the trash.', '%d media attachments restored from the trash.', $_GET['untrashed'] ), number_format_i18n( $_GET['untrashed'] ) );
          $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'untrashed' ), $_SERVER['REQUEST_URI'] ));
      }

      if ( isset( $_GET['approved'] ) ) {
          $message = 'The photo was approved';
      }

      $messages[1] = __( 'Media attachment updated.', 'closify-uploader' );
      $messages[2] = __( 'Media permanently deleted.', 'closify-uploader' );
      $messages[3] = __( 'Error saving media attachment.', 'closify-uploader' );
      $messages[4] = __( 'Media moved to the trash.', 'closify-uploader' ) . ' <a href="' . esc_url( wp_nonce_url( 'edit.php?post_type=closify?doaction=undo&action=untrash&ids='.( isset( $_GET['ids'] ) ? $_GET['ids'] : '' ), "bulk-media" ) ) . '">' . __( 'Undo', 'closify-uploader' ) . '</a>';
      $messages[5] = __( 'Media restored from the trash.', 'closify-uploader' );

      if ( isset( $_GET['message'] ) && (int) $_GET['message'] ) {
          $message = $messages[$_GET['message']];
          $_SERVER['REQUEST_URI'] = esc_url(remove_query_arg( array( 'message' ), $_SERVER['REQUEST_URI'] ));
      }

      if ( !empty( $message ) ) { ?>
      <div id="message" class="updated"><p><?php echo $message; ?></p></div>
      <?php } ?>

      <form id="posts-filter" action="" method="get">
        <input type="hidden" name="page" value="closify_manage_list" />
        <input type="hidden" name="post_type" value="<?php echo CLOSIFY_POST_TYPE;?>" />
      <?php $closify_media_list->search_box( __( 'Search Media', 'closify-uploader' ), 'media' ); ?>

      <?php $closify_media_list->display(); ?>

      <div id="ajax-response"></div>
      <?php find_posts_div(); ?>
      <br class="clear" />

      </form>
      </div>
      <?php

    }
    
    /**
	 * Since WP 3.5-beta-1 WP Media interface shows private attachments as well
	 * We don't want that, so we force WHERE statement to post_status = 'inherit'
	 *
	 * @since 0.3
	 *
	 * @param string $where WHERE statement
	 * @return string WHERE statement
	 */
	function closify_filter_posts_where( $where ) {
		if ( !is_admin() || !function_exists( 'get_current_screen' ) )
			return $where;

		$screen = get_current_screen();
		if ( ! defined( 'DOING_AJAX' ) && $screen && isset( $screen->base ) && $screen->base == 'upload' && ( !isset( $_GET['page'] ) || $_GET['page'] != 'closify_manage_list' ) ) {
			$where = str_replace( "post_status = '".CLOSIFY_POST_STATUS."'", "post_status = 'inherit'", $where );
		}
		return $where;
	}
    
    /**
	 * Approve a media file
	 *
	 * TODO: refactor in 0.6
	 *
	 * @return [type] [description]
	 */
	function closify_approve_media() {
		// Check permissions, attachment ID, and nonce

		if ( false === $this->closify_check_perms_and_nonce() || 0 === (int) $_GET['id'] ) {
			wp_safe_redirect( get_admin_url( null, 'edit.php?post_type=closify&page=closify_manage_list&error=id_or_perm' ) );
		}

		$post = get_post( intval($_GET['id']) );

		if ( is_object( $post ) && $post->post_status == CLOSIFY_POST_STATUS ) {
			$post->post_status = 'inherit';
			wp_update_post( $post );

			do_action( 'closify_media_approved', $post );
			wp_safe_redirect( get_admin_url( null, 'edit.php?post_type=closify&page=closify_manage_list&approved=1' ) );
		}
        
        die();
	}
    
    function closify_check_perms_and_nonce() {
		return current_user_can( 'edit_posts' ) && wp_verify_nonce( $_REQUEST['closify_nonce'], CLOSIFY_NONCE );
	}

	/**
	 * Delete post and redirect to referrer
	 *
	 * @return [type] [description]
	 */
	function closify_delete_post() {
		if ( $this->closify_check_perms_and_nonce() && 0 !== (int) $_GET['id'] ) {
			if ( wp_delete_post( (int) $_GET['id'], true ) )
				$args['deleted'] = 1;
		}

		wp_safe_redirect( esc_url_raw(add_query_arg( $args, wp_get_referer() )) );
		exit;
	}
    
    function closify_process_bulk_action($wp_media_list_table) {

        // security check!
        if ( isset( $_POST['_wpnonce'] ) && ! empty( $_POST['_wpnonce'] ) ) {

            $nonce  = filter_input( INPUT_POST, '_wpnonce', FILTER_SANITIZE_STRING );
            $action = 'bulk-' . $this->_args['plural'];

            if ( ! wp_verify_nonce( $nonce, $action ) )
                wp_die( 'Nope! Security check failed!' );

        }

        $action = $wp_media_list_table->current_action();

        switch ( $action ) {

          case 'delete':
            foreach ( (array) $_REQUEST['media'] as $post_id_delete ) {
                if ( !current_user_can( 'edit_post', $post_id_delete ) )
                    wp_die( __( 'You are not allowed to approve this file upload.' ) );

                $post = get_post( $post_id_delete );

                if ( is_object( $post ) ) {
                    wp_delete_post( $post_id_delete, true );

                    do_action( 'closify_media_deleted', $post );
                }else{
                  return 'No file object found';
                }
            }
            return 'Selected files has been deleted';
            break;
          case 'approve':
              
            foreach ( (array) $_REQUEST['media'] as $post_id_approve ) {
                if ( !current_user_can( 'edit_post', $post_id_approve ) )
                    wp_die( __( 'You are not allowed to approve this file upload.' ) );

                $post = get_post( $post_id_approve );

                if ( is_object( $post ) && $post->post_status == CLOSIFY_POST_STATUS ) {
                    
                  $post->post_status = 'inherit';
                    
                  wp_update_post( $post );

                  do_action( 'closify_media_approved', $post );
                }else{
                  return 'No file object found';
                }
            }
            return 'Selected files has been approved';
            break;

          default:
              // do nothing or something else
              return;
              break;
        }

        return;
    }

    // This function will be called whenever there is an approved file
    function closify_handle_approved_image($att)
    {
      $get_meta_is_multi = get_post_meta($att->ID, CLOSIFY_POST_EXTRA_INFO, true);

      if(!empty($get_meta_is_multi))
      {
        if($get_meta_is_multi['is_multi']){
          $this->closify_save_images_for_user(true, $att->ID, $att->post_author);
        }else{
          $this->closify_save_images_for_user(false, $att->ID, $att->post_author);
        }
      }
    }

    // This function will be called when new file is been uploaded successfully
    function closify_handle_uploaded_image($att_id, $closify_id, $user_id, $att_size)
    {
      global $wpdb;

      try{
        $wpdb->replace( 
          CLOSIFY_UPLOAD_TABLE_NAME, 
          array( 
              'user_id' => intval($user_id), 
              'att_id' => intval($att_id),
              'closify_id' => intval($closify_id),
              'att_size' => sanitize_text_field($att_size),
              'time' => date('Y-m-d H:i:s')
          ), 
          array( 
              '%d', 
              '%d',
              '%d',
              '%s', 
              '%s' 
          ) 
        );
      }catch(Exception $ex)
      {
        $this->Report_Error("Database insert error, check your error log file");
        error_log("Closify Press: ".$ex);
      }
    }

    function delete_closify_attachment($att_id)
    {
      global $wpdb;

      $wpdb->delete( CLOSIFY_UPLOAD_TABLE_NAME, array( 'att_id' => intval($att_id) ) );
    }

    // Load images for a specific user
    function get_closify_images_for_user($closify_ids, $user_ids)
    {
      // Closify return image object structure
      // stdClass Object
      /*(
          [id] => 1
          [user_id] => 1
          [att_id] => 2134
          [closify_id] => 1932
          [att_size] => 396195
          [time] => 2015-10-04 17:51:56
          [dropbox_sent] => 
          [drive_sent] => 
          [ftp_sent] => 
          [dropbox_cdn] => 
          [drive_cdn] => 
          [short_url] => 
      )*/
      global $wpdb;
      $where_closify_id = "(";
      
      if($user_ids!=""){
        $where_userid = "(";  
        /*======================================================*/
        /*=================Generate Where======================*/
        // Filter user_ids
        for($i=0; $i < count($user_ids);$i++)
        {
          // Guest users have ID of 0, so make sure to replace it with -100 for successful storage
          if($user_ids[$i] == 0){
            $user_ids[$i] = -100;
          }

          if($i<(count($user_ids)-1))
            $where_userid = $where_userid . "user_id=".strip_tags($user_ids[$i])." OR ";
          else
            $where_userid = $where_userid . "user_id=".strip_tags($user_ids[$i]).")";
        }
      }
    
      if($closify_ids != ""){
        for($i=0; $i < count($closify_ids);$i++)
        {

          if($i<(count($closify_ids)-1))
            $where_closify_id = $where_closify_id . "closify_id=".strip_tags($closify_ids[$i])." OR ";
          else
            $where_closify_id = $where_closify_id . "closify_id=".strip_tags($closify_ids[$i]).")";
        }
      }

      /*=================Generate Where END===================*/
      /*======================================================*/

      // If there was no closify ID, return the whole images for a single user
      if($closify_ids == "")
      {
        $select_query = (sprintf("SELECT * FROM %s WHERE %s;",CLOSIFY_UPLOAD_TABLE_NAME, $where_userid ) );
        $existingImg = $wpdb->get_results( $select_query, ARRAY_A  );
        return $existingImg;
      }else if($user_ids == ""){
        $select_query = (sprintf("SELECT * FROM %s WHERE %s;",CLOSIFY_UPLOAD_TABLE_NAME, $where_closify_id ) );
        $existingImg = $wpdb->get_results( $select_query, ARRAY_A  );
        return $existingImg;
      }else{

        $select_query = sprintf("SELECT * FROM %s WHERE %s AND %s;",CLOSIFY_UPLOAD_TABLE_NAME, $where_closify_id, $where_userid ) ;
        $existingImg = $wpdb->get_results( $select_query, ARRAY_A  );
        // print_r($select_query);
        return $existingImg;
      }

      return false;

    }

    // This function will check if the images still exist as an attachment and if not it will clear the
    // non-existing images from database and return valid images only
    function filter_images($current_user_images)
    {
      global $wpdb;

      foreach($current_user_images as $key => $img)
      {
        $img_obj = wp_get_attachment_image_src( $img['att_id']);
        // If the attachment is not available, remove it
        if(!$img_obj)
        {
          // Delete record if the attachment is no longer exist
          $wpdb->delete( CLOSIFY_UPLOAD_TABLE_NAME, array( 'att_id' => intval($img['att_id']) ), array( '%d' ) );
          unset($current_user_images[$key]);
        }
      }

      return $current_user_images;
    }

    function approve_wordpress_attachment($post_id)
    {
      global $wpdb;
      $post = get_post( $post_id );

      if ( is_object( $post ) ) {

        $wpdb->query( 
            $wpdb->prepare( 
                "UPDATE $wpdb->posts SET post_status = 'inherit' WHERE ID = %d", 
                intval($post_id) 
            )
        );

        do_action( 'closify_media_approved', $post );
        return true;
      }else{
        return false;
      }
    }

    // Save images for a specific user
    function closify_save_images_for_user($is_multi, $att_id, $user_id)
    {

      // Guest users have ID of 0, so make sure to replace it with -100 for successful storage
      if($user_id == 0)
      {
        $user_id = -100;
      }

      $att = get_post( $att_id );
      $parsed_url = parse_url($att->guid);

      // path will give you the path right after http://[domain] -> /path/to/whatever
      $size = filesize( $_SERVER['DOCUMENT_ROOT'].$parsed_url['path'] );

      // Trigger upload action ($att_id, $closify_id, $user_id, $att_size)
      do_action('closify_file_uploaded', intval($att_id), intval($att->post_parent), intval($user_id), $size);

      return;

    }
  
    /**
    * Return an instance of this class.
    *
    *
    * @return    object    A single instance of this class.
    */
    public static function get_instance() {

        // If the single instance hasn't been set, set it now.
        if ( null == self::$instance ) {
            self::$instance = new self;
        }

        return self::$instance;
    }
  
  }