<?php
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * Class which responsible for creating and maintaining image field ( for Admin, as well as image fields's meta section )
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_field_image extends wcff_field {
	
	function __construct() {
		$this->name 		= "image";
		$this->label 		= "Image";
		$this->required 	= false;
		$this->valid		= true;
		$this->message 		= "This field can't be Empty";
		$this->params 		= array(
			'filetypes'	=>	''				
		);	

    	parent::__construct();
	}
	
	function render_wcff_setup_fields( $type = "wccaf" ) { ob_start(); ?>
	
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Required', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Is this field Mandatory', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="required">
					<ul class="wcff-field-layout-horizontal">
						<li><label><input type="radio" name="wcff-field-type-meta-required" value="yes" /> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-required" value="no" checked/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
					</ul>						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Message', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Message to display whenever the validation failed', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="text" data-param="message">
					<input type="text" id="wcff-field-type-meta-message" value="<?php echo esc_attr( $this->message ); ?>" />						
				</div>
			</td>
		</tr>	
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Button Text', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Enter the upload button label text', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="text" data-param="upload_btn_label">
					<input type="text" id="wcff-field-type-meta-upload_btn_label" value="" />						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Probe Text', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Enter a description ( eg. You haven\'t added an image )', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="text" data-param="upload_probe_text">
					<input type="text" id="wcff-field-type-meta-upload_probe_text" value="" />						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Media Browser Title', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Give a title for the Media Library Browser', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="text" data-param="media_browser_title">
					<input type="text" id="wcff-field-type-meta-media_browser_title" value="" />						
				</div>
			</td>
		</tr>
		
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Tips', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Whether to show tool tip icon or not', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="radio" data-param="desc_tip">
					<ul class="wcff-field-layout-horizontal">
						<li><label><input type="radio" name="wcff-field-type-meta-desc_tip" value="yes" /> <?php _e( 'Yes', 'wc-fields-factory' ); ?></label></li>
						<li><label><input type="radio" name="wcff-field-type-meta-desc_tip" value="no" checked/> <?php _e( 'No', 'wc-fields-factory' ); ?></label></li>
					</ul>						
				</div>
			</td>
		</tr>
					
		<tr>
			<td class="summary">
				<label for="post_type"><?php _e( 'Description', 'wc-fields-factory' ); ?></label>
				<p class="description"><?php _e( 'Description about this field, if user clicked tool tip icon', 'wc-fields-factory' ); ?></p>
			</td>
			<td>
				<div class="wcff-field-types-meta" data-type="textarea" data-param="description">
					<textarea rows="4" id="wcff-field-type-meta-description"></textarea>	
				</div>
			</td>
		</tr>
		
		<?php 
		
		return ob_get_clean();
	}
	
	function render_product_field( $field ) {		
		ob_start(); ?>	
		<!-- We don't support image field for front end product screen -->
	<?php return ob_get_clean();
	
	}

	function render_admin_field( $field ) {
	
		global $content_width, $_wp_additional_image_sizes;
		
		$thumbnail_html = "";
		$old_content_width = $content_width;
		$content_width = 150;
		$has_image = false;
		$image_wrapper_class = "wccaf-image-field-wrapper";		
		
		$field["upload_btn_label"] = ( ! empty( $field["upload_btn_label"] ) ) ? $field["upload_btn_label"] : "Upload";
		$field["media_browser_title"] = ( ! empty( $field["media_browser_title"] ) ) ? $field["media_browser_title"] : "Choose an Image";
		$field["upload_probe_text"] = ( ! empty( $field["upload_probe_text"] ) ) ? $field["upload_probe_text"] : "You haven't set an image yet"; 
			
		if( isset( $field["value"] ) && ! empty( $field["value"] ) ) {
			if ( ! isset( $_wp_additional_image_sizes['thumbnail'] ) ) {
				$thumbnail_html = wp_get_attachment_image( $field["value"], array( $content_width, $content_width ) );
			} else {
				$thumbnail_html = wp_get_attachment_image( $field["value"], 'thumbnail' );
			}
			if ( ! empty( $thumbnail_html ) ) {
				$has_image = true;
				$image_wrapper_class = "wccaf-image-field-wrapper has_image";
			}
			$content_width = $old_content_width;
		} else {
				
		}
	
		if( $field["location"] != "product_cat_add_form_fields" && $field["location"] != "product_cat_edit_form_fields" ) {
	
			echo '<div class="form-field '. esc_attr( $field['name'] ) ."_field ". $image_wrapper_class .'">';			
			
			echo '<h4>'. esc_html( $field['label'] ) .'</h4>';
			
			if( !empty( $thumbnail_html ) ) {
				echo $thumbnail_html;
			} else {
				echo '<img src="" style="width:'. esc_attr( $content_width ) .'px;height:auto;border:0;display:none;" />';
			}
					
			echo '<a href="#" class="wccaf-image-remove-btn"></a>';
			echo '<input type="hidden" id="'. esc_attr( $field["name"] ) .'" name="'. esc_attr( $field["name"] ) .'" value="' . esc_attr( $image_id ) . '" />';
						
			?>			
			
			<p class="wccaf-img-field-btn-wrapper" style="display:<?php echo $has_image ? "none" : "block"; ?>"><?php echo esc_html( $field["upload_probe_text"] ); ?> <input type="button" class="button wcff_upload_image_button" data-uploader_title="<?php echo esc_attr( $field["media_browser_title"] );?>" value="<?php echo esc_attr( $field["upload_btn_label"] ); ?>" />
			
			<?php 
			if ( !empty( $field['description'] ) ) :
				if ( isset( $field['desc_tip'] ) && "no" != $field['desc_tip'] ) : ?>
					<img class="help_tip" data-tip="<?php echo wp_kses_post( $field['description'] ); ?>" src="<?php echo esc_url( wcff()->info["dir"] ); ?>/assets/images/help.png" height="16" width="16" />
				<?php else : ?>
					<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
				<?php 
				endif;
			endif; ?>	

			</p>
			
			<span class="wccaf-validation-message wccaf-is-valid-<?php echo $this->valid; ?>"><?php echo $field["message"]; ?></span>
			
			<?php		
			
			echo '</div>';
			
		} else if( $field["location"] == "product_cat_add_form_fields" ) { ?>
				
		<div class="form-field <?php echo esc_attr( $field['name'] ); ?>_field <?php echo $image_wrapper_class; ?>">		
			<label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo wp_kses_post( $field['label'] ); ?><?php echo ( isset( $field["required"] ) && $field["required"] == "yes" ) ? ' <span>*</span>' : ''; ?></label>
			
			<?php 
			
			if( !empty( $thumbnail_html ) ) {
				echo $thumbnail_html;
			} else {
				echo '<img src="" style="width:'. esc_attr( $content_width ) .'px;height:auto;border:0;display:none;" />';
			}
					
			echo '<a href="#" class="wccaf-image-remove-btn"></a>';
			echo '<input type="hidden" id="'. esc_attr( $field["name"] ) .'" name="'. esc_attr( $field["name"] ) .'" value="' . esc_attr( $image_id ) . '" />';
						
			?>			
			
			<p style="display:<?php echo $has_image ? "none" : "block"; ?>"><?php echo esc_html( $field["upload_probe_text"] ); ?> <input type="button" class="button wcff_upload_image_button" data-uploader_title="<?php echo esc_attr( $field["media_browser_title"] );?>" value="<?php echo esc_attr( $field["upload_btn_label"] ); ?>" />
			
			<?php 
			if ( !empty( $field['description'] ) ) :
				if ( isset( $field['desc_tip'] ) && "no" != $field['desc_tip'] ) : ?>
					<img class="help_tip" data-tip="<?php echo wp_kses_post( $field['description'] ); ?>" src="<?php echo esc_url( wcff()->info["dir"] ); ?>/assets/images/help.png" height="16" width="16" />
				<?php else : ?>
					<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
				<?php 
				endif;
			endif; ?>	
			
			</p>
			
			<span class="wccaf-validation-message wccaf-is-valid-<?php echo $this->valid; ?>"><?php echo $field["message"]; ?></span>				
		</div>
			
		<?php 
			
		} else if( $field["location"] == "product_cat_edit_form_fields" ) { ?>
		
		<tr class="form-field <?php echo esc_attr( $field['name'] ); ?>_field <?php echo $image_wrapper_class; ?>">
			<th scope="row" valign="top"><label for="<?php echo esc_attr( $field['name'] ); ?>"><?php echo wp_kses_post( $field['label'] ); ?><?php echo ( isset( $field["required"] ) && $field["required"] == "yes" ) ? ' <span>*</span>' : ''; ?></label></th>
			<td>
				<?php 
				
				if( !empty( $thumbnail_html ) ) {
					echo $thumbnail_html;
				} else {
					echo '<img src="" style="width:'. esc_attr( $content_width ) .'px;height:auto;border:0;display:none;" />';
				}
						
				echo '<a href="#" class="wccaf-image-remove-btn"></a>';
				echo '<input type="hidden" id="'. esc_attr( $field["name"] ) .'" name="'. esc_attr( $field["name"] ) .'" value="' . esc_attr( $image_id ) . '" />';
							
				?>			
			
				<p style="display:<?php echo $has_image ? "none" : "block"; ?>"><?php echo esc_html( $field["upload_probe_text"] ); ?> <input type="button" class="button wcff_upload_image_button" data-uploader_title="<?php echo esc_attr( $field["media_browser_title"] );?>" value="<?php echo esc_attr( $field["upload_btn_label"] ); ?>" />			
				
				<?php 
				if ( !empty( $field['description'] ) ) :
					if ( isset( $field['desc_tip'] ) && "no" != $field['desc_tip'] ) : ?>
						<img class="help_tip" data-tip="<?php echo wp_kses_post( $field['description'] ); ?>" src="<?php echo esc_url( wcff()->info["dir"] ); ?>/assets/images/help.png" height="16" width="16" />
					<?php else : ?>
						<span class="description"><?php echo wp_kses_post( $field['description'] ); ?></span>
					<?php 
					endif;
				endif; ?>
				
				</p>
					
				<span class="wccaf-validation-message wccaf-is-valid-<?php echo $this->valid; ?>"><?php echo $field["message"]; ?></span>
			</td>
		</tr>
			
		<?php 
		
		}
		
		return ob_get_clean();
	}
	
	/* Just for compatibility reason, since we use media uploader for file upload */
	function validate( $val ) {
		return true;
	}	
	
}

new wcff_field_image();

?>