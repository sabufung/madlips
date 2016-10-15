<?php 
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * Class which responsible for creating and maintaining label field ( for Product as well as label fields's meta section )
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_field_label extends wcff_field {
	
	function __construct() {
		$this->name 		= 'label';
		$this->label 		= "Label";
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
				<label for="post_type"><?php _e( 'Message', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Any text which has to be displayed.', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="text" data-param="message">
					<textarea id="wcff-field-type-meta-message" row="4"></textarea>						
				</div>
			</td>
		</tr>	
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Position', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Where this message has to be displayed ( before all the fields or after the all fields or along with other fields )', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="position">
					<ul class="wcff-field-layout-horizontal">
						<li><label><input type="radio" name="wcff-field-type-meta-position" value="normal" checked /> <?php _e( 'Normal', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-position" value="beginning"/> <?php _e( 'At the  Beginning', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-position" value="end"/> <?php _e( 'At the End', 'wc-fields-factory' ); ?></label></li>										
					</ul>						
				</div>
			</td>
		</tr>	
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Type', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Type of the message that is about to display', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="message_type">
					<ul class="wcff-field-layout-horizontal">
						<li><label><input type="radio" name="wcff-field-type-meta-message_type" value="info" checked /> <?php _e( 'Info', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-message_type" value="success"/> <?php _e( 'Success', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-message_type" value="warning"/> <?php _e( 'Warning', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-message_type" value="danger"/> <?php _e( 'Danger', 'wc-fields-factory' ); ?></label></li>
					</ul>						
				</div>
			</td>
		</tr>			
						
		<?php	
		return ob_get_clean();
	}
	
	function render_product_field( $field ) { ob_start(); ?>		
		
		<div class="wcff-label wcff-label-<?php echo $field["message_type"]; ?>"><?php echo $field["message"]; ?></div>		
				
	<?php return ob_get_clean();
	}

	function render_admin_field( $field ) { ob_start(); ?>
	
		<!-- Label field not supported for wccaf -->
	
	<?php return ob_end_clean();
	}
	
	function validate( $val ) {
		return true;
	}
	
}

new wcff_field_label();

?>