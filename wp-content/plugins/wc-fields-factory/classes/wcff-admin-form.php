<?php
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * One of the core module, which renders the actual wccaf fields to the admin pages ( products & Product Categories pages ).
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_admin_form {
	
	var $location;
	
	function __construct() {
		
		$wccaf_locations = apply_filters( 'wccaf/locations', array (
			"woocommerce_product_options_general_product_data" => array( $this, 'inject_wccaf_on_product_options_general_product_data' ),
			"woocommerce_product_options_inventory_product_data" => array( $this, 'inject_wccaf_on_product_options_inventory_product_data' ),
			"woocommerce_product_options_shipping" => array( $this, 'inject_wccaf_on_product_options_shipping' ),
			"woocommerce_product_options_attributes" => array( $this, 'inject_wccaf_on_product_options_attributes' ),
			"woocommerce_product_options_related" => array( $this, 'inject_wccaf_on_product_options_related' ),
			"woocommerce_product_options_advanced" => array( $this, 'inject_wccaf_on_product_options_advanced' )						
		));
		
		foreach ( $wccaf_locations as $location => $callback ) {
			if( is_callable( $callback ) ) {
				add_action( $location, $callback );
			}			
		}		
		
		add_action( 'admin_head-post.php', array( $this, 'inject_wccaf_on_product_page' ) );
		
		add_action( 'product_cat_add_form_fields', array( $this, 'inject_wccaf_on_product_cat_page_add' ) );
		add_action( 'product_cat_edit_form_fields', array( $this, 'inject_wccaf_on_product_cat_page_edit' ) );
		
		add_action( 'save_post', array( $this, 'save_wccaf_product_fields' ), 1, 3 );		
		
		add_action( 'edited_product_cat', array( $this, 'save_wccaf_product_cat_fields' ) );
		add_action( 'create_product_cat', array( $this, 'save_wccaf_product_cat_fields' ) );
		
		add_action( 'woocommerce_product_after_variable_attributes', array( $this, 'inject_wccaf_on_product_variable_section' ), 10, 3 );
		add_action( 'woocommerce_save_product_variation', array( $this, 'save_wccaf_product_variable_fields' ), 99, 2 );
		
	}
	
	function inject_wccaf_on_product_page() {
		global $post;
		if( $post->post_type == "product" ) {
			$this->location = "admin_head-post.php";
			$this->inject_wccaf();
		}		
	}
	
	function inject_wccaf_on_product_cat_page_add() {
		$this->location = "product_cat_add_form_fields";
		$this->inject_wccaf();
	}
	
	function inject_wccaf_on_product_cat_page_edit( $term ) {
		$this->location = "product_cat_edit_form_fields";
		$this->inject_wccaf( $term );
	}
	
	function inject_wccaf_on_product_options_general_product_data() {
		$this->location = "woocommerce_product_options_general_product_data";
		$this->inject_wccaf();
	}
	
	function inject_wccaf_on_product_options_inventory_product_data() {
		$this->location = "woocommerce_product_options_inventory_product_data";
		$this->inject_wccaf();
	}
	
	function inject_wccaf_on_product_options_shipping() {
		$this->location = "woocommerce_product_options_shipping";
		$this->inject_wccaf();
	}
	
	function inject_wccaf_on_product_options_attributes() {
		$this->location = "woocommerce_product_options_attributes";
		$this->inject_wccaf();
	}
	
	function inject_wccaf_on_product_options_related() {
		$this->location = "woocommerce_product_options_related";
		$this->inject_wccaf();
	}
	
	function inject_wccaf_on_product_options_advanced() {
		$this->location = "woocommerce_product_options_advanced";
		$this->inject_wccaf();
	}
	
	function inject_wccaf( $term = null ) {
		
		global $post;		
		$is_colorpicker_there = false;
		$is_image_field_there = false;
		$all_fields = apply_filters( 'wcff/load/all_fields', $post->ID, 'wccaf', $this->location );
		
		if( count( $all_fields ) > 0 ) {
			
			do_action( 'wccaf/before/fields/start' );
			
			if( $this->location != "admin_head-post.php" ) {
				foreach ( $all_fields as $title => $fields ) {
					if( count( $fields ) > 0 ) {		
						foreach ( $fields as $key => $field ) {
							
							$mval = "";							
							if( $this->location != "product_cat_edit_form_fields" ) {
								$mval = get_post_meta( $post->ID, "wccaf_". $field["name"], true );
							} else {
								if( $term ) {
									$mval = get_option( "taxonomy_product_cat_". $term->term_id . "_wccaf_" . $field["name"] );
								}
							}
							
							if( !$mval ) {
								if( isset( $field["default_value"] ) && $field["type"] != "radio" && $field["type"] != "select" ) {
									$mval = $field["default_value"];
								} else {
									$mval = "";
								}
							}						
							
							$field["value"] = $mval;
							$field["location"] = $this->location;
							
							do_action( 'wccaf/before/field/start', $field );
							
							/* generate html for wccaf fields */
							echo apply_filters( 'wcff/render/admin/field/type='.$field["type"], $field );
								
							do_action( 'wccaf/after/field/end', $field );
							
							if( $field["type"] == "colorpicker" ) {
								$is_colorpicker_there = true;
							}
							
							if( $field["type"] == "image" ) {
								$is_image_field_there = true;
							}
							
						}
					}
				}
				if( $is_colorpicker_there || $is_image_field_there ) {
					$this->wccaf_back_end_enqueue_scripts( $is_colorpicker_there, $is_image_field_there );
				}				
				$this->wccaf_fields_validation();
			} else {
				$added = false;
				$location_group = apply_filters( 'wcff/load/all/location/rules', array() );				
				foreach ( $location_group as $lrules ) {
					foreach ( $lrules as $lrule ) {						
						if( $lrule["context"] == "location_product" || $lrule["context"] == "location_product_cat" ) {
							add_meta_box( 'wccaf_meta_box', "Additional Options", array( $this, "inject_wccaf_meta_box" ), get_current_screen() -> id, $lrule["endpoint"]["context"], $lrule["endpoint"]["priority"], array( 'fields' => $all_fields, 'location' => $this->location ) );
							$added = true;
							break;
						}
					}
					if( $added ) {
						break;
					}
				}				
			}
			
			do_action( 'wccaf/after/fields/end' );
		
		}		
	}	
	
	function inject_wccaf_on_product_variable_section( $loop, $variation_data, $variation ) {
		global $post;
		$this->location = "woocommerce_product_after_variable_attributes";
		$is_colorpicker_there = false;
		$all_fields = apply_filters( 'wcff/load/all_fields', $post->ID, 'wccaf', $this->location );
		
		if( count( $all_fields ) > 0 ) {
				
			do_action( 'wccaf/before/fields/start' );				
			
			foreach ( $all_fields as $title => $fields ) {
				if( count( $fields ) > 0 ) {
					foreach ( $fields as $key => $field ) {
						
						$mval = "";						
						$mval = get_post_meta( $variation->ID, "wccaf_".$field["name"], true );					
							
						if( !$mval ) {
							if( isset( $field["default_value"] ) && $field["type"] != "radio" && $field["type"] != "select" ) {
								$mval = $field["default_value"];
							} else {
								$mval = "";
							}
						}
							
						$field["value"] = $mval;		
						$field["location"] = $this->location;
						$field["name"] = $field["name"] ."[". $loop ."]"; 
							
						do_action( 'wccaf/before/field/start', $field );
							
						/* generate html for wccaf fields */
						echo apply_filters( 'wcff/render/admin/field/type='.$field["type"], $field );
	
						do_action( 'wccaf/after/field/end', $field );
							
						if( $field["type"] == "colorpicker" ) {
							$is_colorpicker_there = true;
						}
							
					}
				}
			}
			if( $is_colorpicker_there ) {
				$this->wccaf_front_end_enqueue_scripts();
			}			
			$this->wccaf_fields_validation();
				
			do_action( 'wccaf/after/fields/end' );
		
		}
	}
	
	function inject_wccaf_meta_box( $post, $margs ) {		
		$is_colorpicker_there = false;		
		if( isset( $margs["args"]["fields"] ) ) {
			foreach ( $margs["args"]["fields"] as $title => $fields ) {
				if( count( $fields ) > 0 ) {
					foreach ( $fields as $key => $field ) {
						
						$mval = "";						
						$mval = get_post_meta( $post->ID, "wccaf_". $field["name"], true );
							
						if( !$mval ) {
							if( isset( $field["default_value"] ) && $field["type"] != "radio" && $field["type"] != "select" ) {
								$mval = $field["default_value"];
							} else {
								$mval = "";
							}
						}
							
						$field["location"] = $this->location;
						$field["value"] = $mval;
			
						do_action( 'wccaf/before/field/start', $field );
			
						/* generate html for wccaf fields */
						echo apply_filters( 'wcff/render/admin/field/type='.$field["type"], $field );
			
						do_action( 'wccaf/after/field/end', $field );
			
						if( $field["type"] == "colorpicker" ) {
							$is_colorpicker_there = true;
						}
			
					}
				}
			}	
		}		
		if( $is_colorpicker_there ) {
			$this->wccaf_front_end_enqueue_scripts();
		}
		$this->wccaf_fields_validation();		
	}
	
	function save_wccaf_product_fields( $post_id, $post, $update ) {
		$all_fields = apply_filters( 'wcff/load/all_fields', $post_id, 'wccaf', "any" );		
		if( count( $all_fields ) > 0 ) {			
			foreach ( $all_fields as $title => $fields ) {
				if( count( $fields ) > 0 ) {
					foreach ( $fields as $key => $field ) {
						if( isset( $_REQUEST[ $field["name"] ] ) ) {							
							update_post_meta( $post_id, "wccaf_" . $field["name"], $_REQUEST[ $field["name"] ] );
						}
					}
				}
			}			
		}
	}
	
	function save_wccaf_product_cat_fields( $term_id ) {
		$this->location = "product_cat_edit_form_fields";
		$all_fields = apply_filters( 'wcff/load/all_fields', $term_id, 'wccaf', $this->location );
		if( count( $all_fields ) > 0 ) {
			foreach ( $all_fields as $title => $fields ) {
				if( count( $fields ) > 0 ) {
					foreach ( $fields as $key => $field ) {
						if( isset( $_REQUEST[ $field["name"] ] ) ) {
							update_option( "taxonomy_product_cat_". $term_id ."_wccaf_". $field["name"], $_REQUEST[ $field["name"] ] );							
						}
					}
				}
			}
		}		
	}
	
	function save_wccaf_product_variable_fields( $variant_id, $i ) {
		global $post;
		$parent_post_id = -1;
	
		if( !$post ) {
			$parent_post_id = wp_get_post_parent_id( $variant_id );
		} else {
			$parent_post_id = $post->ID;
		}
	
		$this->location = "woocommerce_product_after_variable_attributes";
		$all_fields = apply_filters( 'wcff/load/all_fields', $parent_post_id, 'wccaf', $this->location );
		if( count( $all_fields ) > 0 ) {
			foreach ( $all_fields as $title => $fields ) {
				if( count( $fields ) > 0 ) {
					foreach ( $fields as $key => $field ) {
						if( isset( $_REQUEST[ $field["name"] ][$i] ) ) {
							update_post_meta( $variant_id, "wccaf_". $field["name"], $_REQUEST[ $field["name"] ][$i] );
						}
					}
				}
			}
		}
	}

	function wccaf_back_end_enqueue_scripts( $is_color_picker_there, $is_image_field_there ) {
		if( $is_color_picker_there ) {
			wp_enqueue_style( 'spectrum-css', wcff()->info['dir'] . 'assets/css/spectrum.css', array(), null );
			wp_register_script( 'wccpf-color-picker', wcff()->info['dir'] . 'assets/js/spectrum.js' );
			wp_enqueue_script( 'wccpf-color-picker' );
		}
		if( $is_image_field_there ) { ?>
		
		<script type="text/javascript">
			var $ = jQuery;
			var media_uploader = null;
			$( document ).ready( function() {

				$( document ).on( "click", ".wcff_upload_image_button", function() {
					
					var btn = $( this );
					var ifield = btn.parent().prev().prev().prev();
					var ufield = btn.parent().prev();
					var pfield = btn.parent().parent();
					
					if ( media_uploader ) {
						media_uploader.open();
					  	return;
					}

					media_uploader = wp.media.frames.file_frame = wp.media({
					  	title: btn.data( 'uploader_title' ),					  	
					  	multiple: false
					});

					media_uploader.on( 'select', function() {
						var attachment = media_uploader.state().get('selection').first().toJSON();						
						ufield.val( attachment.id );
						if( attachment.sizes["thumbnail"].url != "" ) {
							ifield.attr( 'src',attachment.sizes["thumbnail"].url );
						} else {
							ifield.attr( 'src',attachment.url );
						}
						btn.parent().hide();						
						ifield.show();						
						pfield.removeClass( "has_image" ).addClass( "has_image" );
					});

					media_uploader.open();					
					
				});		

				$( document ).on( 'click', 'a.wccaf-image-remove-btn', function(e) {
					
					$( this ).next().val( '' );
					$( this ).prev().attr( 'src', '' );
					$( this ).prev().hide();
					$( this ).next().next().show();
					$( this ).parent().removeClass( "has_image" );					

					e.preventDefault();
				});
			});
		</script>
			
		<?php 
		}
	}
	
	function wccaf_fields_validation() { ?>
	<script type="text/javascript">
		var wccaf_is_valid = true;
		(function($) {
			$(document).ready(function(){
				$( document ).on( "blur", ".wccaf-field", function(e) {										
					doValidate( $(this) );
					$("input[name=save]").removeClass("disabled");
					$("input[name=save]").parent().find(".spinner").hide();
				});	
				$(document).on("submit", "#post", function(){			 
					wccaf_is_valid = true;
					$( ".wccaf-field" ).each(function(){
						doValidate( $(this) );
					});
					setTimeout(function(){
						$("input[name=save]").removeClass("disabled");
						$("input[name=save]").parent().find(".spinner").hide();
					}, 1000)				
					return wccaf_is_valid;				
				});
				function doValidate( field ) {
					if( field.attr("wccaf-type") != "radio" && field.attr("wccaf-type") != "checkbox" ) {					
						if( field.attr("wccaf-mandatory") == "yes" ) {						
							if( doPatterns( field.attr("wccaf-pattern"), field.val() ) ) {
								field.parent().find("span.wccaf-validation-message").hide();
							} else {		
								wccaf_is_valid = false;
								field.parent().find("span.wccaf-validation-message").css("display", "block");
							}
						}
					} else {
						if( field.attr("wccaf-mandatory") == "yes" ) {	
							if( $("input[name="+ field.attr("name") +"]").is(':checked') ) {
								field.parent().find("span.wccaf-validation-message").css("display", "block");
							} else {
								wccaf_is_valid = false;
								field.parent().find("span.wccaf-validation-message").hide();
							}	 
						}
					}
				}				
				function doPatterns( patt, val ){
					var pattern = {
						mandatory	: /\S/, 
						number		: /^\d*$/,
						email		: /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i,	      	
					};			    
				    return pattern[ patt ].test(val);	
				}
			});	
		})(jQuery);
	</script>		
	<?php 
	}
	
}

new wcff_admin_form();

?>