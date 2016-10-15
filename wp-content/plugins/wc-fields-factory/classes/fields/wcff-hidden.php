<?php 
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * Class which responsible for creating and maintaining hidden field ( for Product as well as hidden fields's meta section )
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_field_hidden extends wcff_field {
	
	function __construct() {
		$this->name 		= 'hidden';
		$this->label 		= "Hidden";
		$this->required 	= false;
		$this->message 		= "This field can't be Empty";
		$this->params 		= array(			
			'default_value'	=>	''
		);
	
		parent::__construct();
	}
	
	function render_wcff_setup_fields( $type = "wccpf" ) { ob_start(); ?>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Visibility', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Whether to show this custom field on Cart & Checkout page.', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="visibility">
					<ul class="wcff-field-layout-vertical">
						<li><label><input type="radio" name="wcff-field-type-meta-visibility" value="yes" <?php echo ( $type == "wccpf" ) ? "checked" : ""; ?> /> <?php _e( 'Show', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-visibility" value="no" <?php echo ( $type == "wccaf" ) ? "checked" : ""; ?> /> <?php _e( 'Hide', 'wc-fields-factory' ); ?></label></li>							
					</ul>						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Order Item Meta', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Whether to add this custom field to Order & Email.', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="order_meta">
					<ul class="wcff-field-layout-vertical">
						<li><label><input type="radio" name="wcff-field-type-meta-order_meta" value="yes" <?php echo ( $type == "wccpf" ) ? "checked" : ""; ?> /> <?php _e( 'Add', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-order_meta" value="no" <?php echo ( $type == "wccaf" ) ? "checked" : ""; ?> /> <?php _e( 'Do not add', 'wc-fields-factory' ); ?></label></li>							
					</ul>						
				</div>
			</td>
		</tr>		
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Hidden Value', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Value for this hidden field.', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="text" data-param="hidden_value">
					<input type="text" id="wcff-field-type-meta-hidden_value" value="" />						
				</div>
			</td>
		</tr>	
						
		<?php	
		return ob_get_clean();
	}
	
	function render_product_field( $field ) { 
		
		$wccpf_options = wcff()->option->get_options();
		$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
		$name_index = $fields_cloning == "yes" ? "_1" : "";
		
		ob_start(); ?>		
		
		<input type="hidden" id="<?php echo esc_attr( $field["name"] . $name_index ); ?>" name="<?php echo esc_attr( $field["name"] . $name_index ); ?>" value="<?php echo esc_attr( $field["hidden_value"] ); ?>" />
				
	<?php return ob_get_clean();
	
	}

	function render_admin_field( $field ) { ob_start(); ?>
	
		<input type="hidden" id="<?php echo esc_attr( $field["name"] ); ?>" name="<?php echo esc_attr( $field["name"] ); ?>" value="<?php echo esc_attr( $field["hidden_value"] ); ?>" />
	
	<?php return ob_get_clean();
	}
	
	function validate( $val ) {
		return true;
	}
	
}

new wcff_field_hidden();

?>