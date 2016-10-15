<?php
/**
 * @author		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com  
 * Wcff option page renderer
 */

wp_register_style( 'wcff-style', plugin_dir_url( __FILE__ ) . '../assets/css/wcff.css' );
wp_enqueue_style('wcff-style');

if( is_admin() ) {
	add_action( 'admin_init', 'wccpf_register_options' );
}

function wccpf_register_options() {	
	register_setting( 'wccpf_options', 'wccpf_options' );		
}

function wccpf_render_option_page() {	
	
	$wccpf_options = get_option( 'wccpf_options' );
	$wccpf_options =  is_array( $wccpf_options ) ? $wccpf_options : array();		
	$show_custom_data = isset( $wccpf_options["show_custom_data"] ) ? $wccpf_options["show_custom_data"] : "yes";	
	$fields_location = isset( $wccpf_options["field_location"] ) ? $wccpf_options["field_location"] : "woocommerce_before_add_to_cart_button";
	
	$ptab_title = isset( $wccpf_options["product_tab_title"] ) ? $wccpf_options["product_tab_title"] : "";
	$ptab_priority = isset( $wccpf_options["product_tab_priority"] ) ? $wccpf_options["product_tab_priority"] : 30;
	
	$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
	$group_title =  isset( $wccpf_options["fields_group_title"] ) ? $wccpf_options["fields_group_title"] : "";
	$show_field_group_title =  isset( $wccpf_options["show_group_title"] ) ? $wccpf_options["show_group_title"] : "no";
	$group_meta_on_cart = isset( $wccpf_options["group_meta_on_cart"] ) ? $wccpf_options["group_meta_on_cart"] : "no"; 
	$group_fields_on_cart = isset( $wccpf_options["group_fields_on_cart"] ) ? $wccpf_options["group_fields_on_cart"] : "no";
	$client_side_validation = isset( $wccpf_options["client_side_validation"] ) ? $wccpf_options["client_side_validation"] : "no";
-	$client_side_validation_type = isset( $wccpf_options["client_side_validation_type"] ) ? $wccpf_options["client_side_validation_type"] : "submit"; ?>

	<?php if( isset( $_GET["settings-updated"] ) ) :?>
	<div id="message" class="updated fade"><p><strong>Your settings have been saved.</strong></p></div>
	<?php endif; ?>

	<div class="wrap wcff-options-wrapper">		
		<h2><?php _e( 'WC Fields Factory Options', 'wc-fields-factory' ); ?></h2>
		<form action='options.php' method='post' class='wcff-options-form'>		
			<?php settings_fields('wccpf_options'); ?>
					
			<table class="wcff-option-field-row wcff_table">			
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Display on Cart & Checkout', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'Display custom meta data on Cart & Checkout page.!', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">
							<ul class="wcff-field-layout-horizontal">
								<li><label><input type="radio" name="wccpf_options[show_custom_data]" value="yes" <?php echo ( $show_custom_data == "yes" ) ? "checked" : ""; ?>/> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" name="wccpf_options[show_custom_data]" value="no" <?php echo ( $show_custom_data == "no" ) ? "checked" : ""; ?>/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
							</ul>						
						</div>
					</td>
				</tr>			
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Fields Location', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'Choose where the fields should be displayed on product page', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">
							<ul class="wcff-field-layout-horizontal">
								<li><label><input type="radio" class="wcff-fields-location-radio" name="wccpf_options[field_location]" value="woocommerce_before_add_to_cart_button" <?php echo ( $fields_location == "woocommerce_before_add_to_cart_button" ) ? "checked" : ""; ?>/> <?php _e( 'Before Add To Cart Button', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" class="wcff-fields-location-radio" name="wccpf_options[field_location]" value="woocommerce_after_add_to_cart_button" <?php echo ( $fields_location == "woocommerce_after_add_to_cart_button" ) ? "checked" : ""; ?>/> <?php _e( 'After Add To Cart Button', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" class="wcff-fields-location-radio" name="wccpf_options[field_location]" value="woocommerce_before_add_to_cart_form" <?php echo ( $fields_location == "woocommerce_before_add_to_cart_form" ) ? "checked" : ""; ?>/> <?php _e( 'Before Add To Cart Form', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" class="wcff-fields-location-radio" name="wccpf_options[field_location]" value="woocommerce_after_add_to_cart_form" <?php echo ( $fields_location == "woocommerce_after_add_to_cart_form" ) ? "checked" : ""; ?>/> <?php _e( 'After Add To Cart Form', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" class="wcff-fields-location-radio" name="wccpf_options[field_location]" value="woocommerce_before_single_product_summary" <?php echo ( $fields_location == "woocommerce_before_single_product_summary" ) ? "checked" : ""; ?>/> <?php _e( 'Before Product Summary', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" class="wcff-fields-location-radio" name="wccpf_options[field_location]" value="woocommerce_after_single_product_summary" <?php echo ( $fields_location == "woocommerce_after_single_product_summary" ) ? "checked" : ""; ?>/> <?php _e( 'After Product Summary', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" class="wcff-fields-location-radio" name="wccpf_options[field_location]" value="woocommerce_single_product_summary" <?php echo ( $fields_location == "woocommerce_single_product_summary" ) ? "checked" : ""; ?>/> <?php _e( 'Product Summary', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" class="wcff-fields-location-radio" name="wccpf_options[field_location]" value="woocommerce_single_product_tab" <?php echo ( $fields_location == "woocommerce_single_product_tab" ) ? "checked" : ""; ?>/> <?php _e( 'Product Tab', 'wc-fields-factory' ); ?></label></li>
							</ul>						
						</div>
					</td>
				</tr>	
				<tr id="wcff-product-tab-config" style="display:<?php echo ( $fields_location == "woocommerce_single_product_tab" ) ? "table-row" : "none"; ?>">
					<td class="summary">
						<label for="post_type"><?php _e( 'Product Tab Config', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'New tab will be inserted on the Product Tab, and all the custom fields will be injected on it.<br/> Enter a title for that product tab and the priority ( 10,20 30... Enter 0 if you want this tab at first )', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">							
							<label>Tab Title</label>
							<input type="text" name="wccpf_options[product_tab_title]" placeholder="eg. Customize This Product" value="<?php echo esc_attr( $ptab_title ); ?>" />								
							<label>Tab Priority</label>
							<input type="number" name="wccpf_options[product_tab_priority]" value="<?php echo esc_attr( $ptab_priority ); ?>" />													
						</div>
					</td>
				</tr>			
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Fields Cloning', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'Display custom fields per product count. Whenever user increases the product quantity, all custom fields will be cloned.!, the', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">
							<ul class="wcff-field-layout-horizontal">
								<li><label><input type="radio" name="wccpf_options[fields_cloning]" value="yes" <?php echo ( $fields_cloning == "yes" ) ? "checked" : ""; ?>/> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" name="wccpf_options[fields_cloning]" value="no" <?php echo ( $fields_cloning == "no" ) ? "checked" : ""; ?>/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
							</ul>						
						</div>
					</td>
				</tr>	
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Cloning Group Title', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'If "Fields Cloning" enabled, then you can assign a title for fields group.!', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">
							<input type="text" name="wccpf_options[fields_group_title]" value="<?php echo esc_attr( $group_title ); ?>" placeholder="eg. Addiotnal Options : "/>						
						</div>
					</td>
				</tr>			
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Group Meta', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'Custom meta data will be grouped and displayed in cart & checkout. won\'t work if group fields option choosed.', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">
							<ul class="wcff-field-layout-horizontal">
								<li><label><input type="radio" name="wccpf_options[group_meta_on_cart]" value="yes" <?php echo ( $group_meta_on_cart == "yes" ) ? "checked" : ""; ?>/> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" name="wccpf_options[group_meta_on_cart]" value="no" <?php echo ( $group_meta_on_cart == "no" ) ? "checked" : ""; ?>/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
							</ul>						
						</div>
					</td>
				</tr>
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Group Fields', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'Custom fields will be grouped ( within each line item, per count ) and displayed in cart & checkout.', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">
							<ul class="wcff-field-layout-horizontal">
								<li><label><input type="radio" name="wccpf_options[group_fields_on_cart]" value="yes" <?php echo ( $group_fields_on_cart == "yes" ) ? "checked" : ""; ?>/> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" name="wccpf_options[group_fields_on_cart]" value="no" <?php echo ( $group_fields_on_cart == "no" ) ? "checked" : ""; ?>/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
							</ul>						
						</div>
					</td>
				</tr>	
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Show Group Title', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'Whether to show the group title for each fields group.', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">
							<ul class="wcff-field-layout-horizontal">
								<li><label><input type="radio" name="wccpf_options[show_group_title]" value="yes" <?php echo ( $show_field_group_title == "yes" ) ? "checked" : ""; ?>/> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" name="wccpf_options[show_group_title]" value="no" <?php echo ( $show_field_group_title == "no" ) ? "checked" : ""; ?>/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
							</ul>						
						</div>
					</td>
				</tr>					
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Client Side Validation', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'Whether the validation should be done on Client Side.?', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">							
							<ul class="wcff-field-layout-horizontal">
								<li><label><input type="radio" name="wccpf_options[client_side_validation]" value="yes" <?php echo ( $client_side_validation == "yes" ) ? "checked" : ""; ?>/> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" name="wccpf_options[client_side_validation]" value="no" <?php echo ( $client_side_validation == "no" ) ? "checked" : ""; ?>/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
							</ul>						
						</div>
					</td>
				</tr>		
				<tr>
					<td class="summary">
						<label for="post_type"><?php _e( 'Client Side Validation Type', 'wc-fields-factory' ); ?></label>
						<p class="description"><?php _e( 'Choose whether the validation done on field level ( on blur ) or while form submit', 'wc-fields-factory' ); ?></p>
					</td>
					<td>
						<div class="wcff-field-types-meta">							
							<ul class="wcff-field-layout-horizontal">
								<li><label><input type="radio" name="wccpf_options[client_side_validation_type]" value="submit" <?php echo ( $client_side_validation_type == "submit" ) ? "checked" : ""; ?>/> <?php _e( 'On Product Submit', 'wc-fields-factory' ); ?></label></li>
								<li><label><input type="radio" name="wccpf_options[client_side_validation_type]" value="blur" <?php echo ( $client_side_validation_type == "blur" ) ? "checked" : ""; ?>/> <?php _e( 'On Blur [ + Product Submit ]', 'wc-fields-factory' ); ?></label></li>
							</ul>						
						</div>
					</td>
				</tr>								
			</table>			
			<p class="submit">
				<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e('Save Changes'); ?>" />
			</p>
		</form>
	</div>
	
	<script type="text/javascript">
		(function($){
			$( document ).ready(function(){
				$( ".wcff-fields-location-radio" ).on( "change", function() {
					if( $( this ).is(":checked") && $( this ).val() == "woocommerce_single_product_tab" ) {
						$( "#wcff-product-tab-config" ).fadeIn("normal");
					} else {
						$( "#wcff-product-tab-config" ).fadeOut("normal");
					}
				});
			});
		})(jQuery);
	</script>
	
<?php 

}

/* Wrapper class for getting wcff options */
class WcffOptions {

	public function __construct() {}

	public function get_options() {

		$options = get_option( 'wccpf_options' );
		$options =  is_array( $options ) ? $options : array();
		return apply_filters( 'wcff_options', $options );

	}
}

?>