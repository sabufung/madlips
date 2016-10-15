<?php 
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * @todo		: One of the core class which generates all WCCPF related meta boxs in Admin Screen
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_post_form {
	
	function __construct() {		
		add_action( 'admin_head-post.php', array( $this, 'wcff_post_single_view' ) );
		add_action( 'admin_head-post-new.php',  array( $this, 'wcff_post_single_view' ) );
		add_action( 'wcff/admin/head', array( $this, 'wcff_admin_head' ) );
		add_filter( 'manage_edit-wccpf_columns', array( $this, 'wcff_columns' ) ) ;
		add_action( 'manage_wccpf_posts_custom_column', array( $this, 'wcff_post_listing' ), 10, 2 );
		add_filter( 'manage_edit-wccaf_columns', array( $this, 'wcff_columns' ) ) ;
		add_action( 'manage_wccaf_posts_custom_column', array( $this, 'wcff_post_listing' ), 10, 2 );
		add_action( 'admin_head-edit.php', array( $this, 'wcff_post_admin_listing' ) );
	}

	function wcff_post_single_view() {		
		if( $this->wcff_check_screen( "wccpf" ) || $this->wcff_check_screen( "wccaf" ) ) {			
			add_meta_box( 'wcff_fields', "Fields", array($this, 'inject_fields_meta_box'), get_current_screen() -> id, 'normal', 'high');
			add_meta_box( 'wcff_factory', "Fields Factory", array($this, 'inject_factory_meta_box'), get_current_screen() -> id, 'normal', 'high');
			add_meta_box( 'wcff_conditions', "Conditions", array($this, 'inject_logics_meta_box'), get_current_screen() -> id, 'normal', 'high');
			
			if( $this->wcff_check_screen( "wccaf" ) ) {
				add_meta_box( 'wcff_locations', "Locations", array($this, 'inject_locations_meta_box'), get_current_screen() -> id, 'normal', 'high');
			}
			
			do_action( 'wcff/admin/head' );
			$this->wcff_admin_enqueue_scripts();
		}
	}
	
	function wcff_columns( $columns ) {	
		$columns = array(
			'cb' => '<input type="checkbox" />',
			'title' => __( 'Title' ),
			'fields' => __( 'Fields' )			
		);	
		return $columns;
	}
	
	function wcff_post_listing( $column, $post_id ) {
		global $post;
		
		switch( $column ) {
			case 'fields' : 
				$count =0;
				$keys = get_post_custom_keys( $post_id );
				
				if($keys) {
					foreach($keys as $key) {
						if( ( strpos($key, 'wccpf_') !== false || strpos($key, 'wccaf_') !== false ) && ( strpos($key, 'group_rules') === false && strpos($key, 'condition_rules') === false && strpos($key, 'location_rules') === false ) ) {
							$count++;
						}						
					}
				}					
				echo $count;				
			break;
		}
	}
	
	function inject_fields_meta_box() {
		if( $this->wcff_check_screen( "wccpf" ) || $this->wcff_check_screen( "wccaf" ) ) {
			include( wcff()->info['path'] . 'views/meta_box_fields.php' );
		}		
	}
	
	function inject_factory_meta_box() {
		if( $this->wcff_check_screen( "wccpf" ) || $this->wcff_check_screen( "wccaf" ) ) {
			include( wcff()->info['path'] . 'views/meta_box_factory.php' );
		}	
	}
	
	function inject_logics_meta_box() {
		if( $this->wcff_check_screen( "wccpf" ) || $this->wcff_check_screen( "wccaf" ) ) {
			include( wcff()->info['path'] . 'views/meta_box_conditions.php' );
		}		
	}

	function inject_locations_meta_box() {
		if( $this->wcff_check_screen( "wccaf" ) ) {
			include( wcff()->info['path'] . 'views/meta_box_locations.php' );
		}
	}	
	
	function wcff_admin_enqueue_scripts() {
		if( $this->wcff_check_screen( "wccpf" ) || $this->wcff_check_screen( "wccaf" ) ) {
			wp_enqueue_script(array(
				'jquery',
				'jquery-ui-core',
				'jquery-ui-tabs',
				'jquery-ui-sortable',
				'wp-color-picker',
				'wcff-script'		
			));				
			wp_enqueue_style(array(
				'thickbox',
				'wp-color-picker',
				'wcff-style'
			));
		}
	}
	
	function wcff_check_screen( $scr_id ) {
		if( $scr_id == "wccpf-options" ) {
			return ( ( get_current_screen() -> id == "wccpf" ) || ( get_current_screen() -> id == "wccaf" ) || get_current_screen() -> id == "wccpf-options" );
		}
		return get_current_screen() -> id == $scr_id;
	}
	
	function wcff_admin_head() {
		global $post; ?>
<script type="text/javascript">
var wcff_var = {
	post_id : <?php echo $post->ID; ?>,
	post_type : "<?php echo $post->post_type; ?>",
	nonce  : "<?php echo wp_create_nonce( get_current_screen() -> id .'_nonce' ); ?>",
	admin_url : "<?php echo admin_url(); ?>",
	ajaxurl : "<?php echo admin_url( 'admin-ajax.php' ); ?>",
	version : "<?php echo wcff()->info["version"]; ?>"	 
};		
</script>
<?php
	}
	
	function wcff_post_admin_listing( $hook_suffix ) {
		global $post_type;
		if( $post_type == "wccpf" || $post_type == "wccaf" ) { ?>
					
<script type="text/javascript">

(function($) {	
	
	$(document).ready(function(){		
		var wrapper = $('<div class="wcff-post-listing-column"></div>');
		wrapper.append( $('<div class="wcff-left-column"></div>') );
		$("#posts-filter, .subsubsub").wrapAll( wrapper );
		
		var wcff_message_box = '<div class="wcff-message-box">';
		wcff_message_box += '<div class="wcff-msg-header"><h3>WC Fields Factory <span><?php echo wcff()->info["version"]; ?></span></h3></div>';
		wcff_message_box += '<div class="wcff-msg-content">';
		wcff_message_box += '<h5>Documentations</h5>';
		wcff_message_box += '<a href="https://sarkware.com/wc-fields-factory-a-wordpress-plugin-to-add-custom-fields-to-woocommerce-product-page/" title="Product Fields" target="_blank">Product Fields</a>';
		wcff_message_box += '<a href="https://sarkware.com/add-custom-fields-woocommerce-admin-products-admin-product-category-admin-product-tabs-using-wc-fields-factory/" title="Admin Fields" target="_blank">Admin Fields</a>';
		wcff_message_box += '<a href="https://sarkware.com/wc-fields-factory-api/" title="WC Fields Factory APIs" target="_blank">WC Fields Factory APIs</a>';
		wcff_message_box += '<a href="https://sarkware.com/woocommerce-change-product-price-dynamically-while-adding-to-cart-without-using-plugins#override-price-wc-fields-factory" title="Override Product Prices" target="_blank">Override Product Prices</a>';
		wcff_message_box += '<a href="https://sarkware.com/how-to-change-wc-fields-factory-custom-product-fields-rendering-behavior/" title="Rendering Behaviour" target="_blank">Rendering Behaviour</a>';		
		wcff_message_box += '</div>';
		wcff_message_box += '<div class="wcff-msg-footer">';
		wcff_message_box += '<a href="https://sarkware.com" title="Sarkware" target="_blank">';
		wcff_message_box += '<img src="<?php echo wcff()->info["dir"]; ?>/assets/images/sarkware.png" alt="Sarkware" /> by Sarkware';
		wcff_message_box += '</a>';
		wcff_message_box += '</div>';		
		
		$(".wcff-post-listing-column").append( $('<div class="wcff-right-column">'+ wcff_message_box +'</div>') );
	});
	
})(jQuery);

</script>

<style type="text/css">
	#posts-filter p.search-box { display:none; }
</style>
							
		<?php
		
		}
	}
}

new wcff_post_form();

?>