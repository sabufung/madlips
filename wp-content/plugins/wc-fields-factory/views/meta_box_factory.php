<?php 

if ( ! defined( 'ABSPATH' ) ) { exit; }

global $post;

if( $post->post_type != "wccaf" ) {
	$fields = apply_filters( "wccpf/fields/factory/supported/fields", array(
		array( "id" => "text", "title" => __( 'Text', 'wc-fields-factory' ) ),
		array( "id" => "number", "title" => __( 'Number', 'wc-fields-factory' ) ),
		array( "id" => "email", "title" => __( 'Email', 'wc-fields-factory' ) ),
		array( "id" => "hidden", "title" => __( 'Hidden', 'wc-fields-factory' ) ),
		array( "id" => "label", "title" => __( 'Label', 'wc-fields-factory' ) ),
		array( "id" => "textarea", "title" => __( 'Text Area', 'wc-fields-factory' ) ),
		array( "id" => "checkbox", "title" => __( 'Check Box', 'wc-fields-factory' ) ),
		array( "id" => "radio", "title" => __( 'Radio Button', 'wc-fields-factory' ) ),
		array( "id" => "select", "title" => __( 'Select', 'wc-fields-factory' ) ),
		array( "id" => "datepicker", "title" => __( 'Date Picker', 'wc-fields-factory' ) ),
		array( "id" => "colorpicker", "title" => __( 'Color Picker', 'wc-fields-factory' ) ),
		array( "id" => "file", "title" => __( 'File', 'wc-fields-factory' ) )
	));
} else {
	$fields = apply_filters( "wccaf/fields/factory/supported/fields", array(
		array( "id" => "text", "title" => __( 'Text', 'wc-fields-factory' ) ),
		array( "id" => "number", "title" => __( 'Number', 'wc-fields-factory' ) ),
		array( "id" => "email", "title" => __( 'Email', 'wc-fields-factory' ) ),
		array( "id" => "textarea", "title" => __( 'Text Area', 'wc-fields-factory' ) ),
		array( "id" => "checkbox", "title" => __( 'Check Box', 'wc-fields-factory' ) ),
		array( "id" => "radio", "title" => __( 'Radio Button', 'wc-fields-factory' ) ),
		array( "id" => "select", "title" => __( 'Select', 'wc-fields-factory' ) ),
		array( "id" => "datepicker", "title" => __( 'Date Picker', 'wc-fields-factory' ) ),
		array( "id" => "colorpicker", "title" => __( 'Color Picker', 'wc-fields-factory' ) ),
		array( "id" => "image", "title" => __( 'Image', 'wc-fields-factory' ) )
	));
}
	
?>

<div id="wcff_fields_factory" action="POST">

	<table class="wcff_table wcff_fields_factory_header">
		<tr>
			<td>
				<select class="select" id="wcff-field-type-meta-type">
					<?php foreach ( $fields as $field ) : ?>
					<option value="<?php echo $field["id"]; ?>"><?php echo $field["title"]; ?></option>
					<?php endforeach;?>								
				</select>
			</td>
			<td><input type="text" id="wcff-field-type-meta-label" value="" placeholder="Label"/></td>
			<td><input type="text" id="wcff-field-type-meta-name" value="" placeholder="Name" readonly/></td>
			<td><a href="#" class="wcff-add-new-field button button-primary">+ <?php _e( 'Add Field', 'wc-fields-factory' ); ?></a></td>
		</tr>
	</table>

	<div class="wcff-field-types-meta-container">
		<table class="wcff_table">
			<tbody id="wcff-field-types-meta-body">				
				<?php echo apply_filters( 'wcff/render/setup/fields/type=text', $post->post_type ) ?>				
			</tbody>
			<tfoot id="wcff-field-factory-footer" style="display:none">
				<tr>
					<td></td>
					<td style="text-align: right;">
						<a href="#" class="wcff-cancel-update-field-btn button"><?php _e( 'Cancel', 'wc-fields-factory' ); ?></a>
						<a href="#" data-key="" class="button wcff-meta-option-delete"><?php _e( 'Delete', 'wc-fields-factory' ); ?></a>						
					</td>
				</tr>
			</tfoot>
		</table>
	</div>
</div>