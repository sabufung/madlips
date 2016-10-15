<?php 
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * @todo		: One of the core module, which renders the actual wccpf fields to the product page.
 * 
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wccpf_product_form {
	
	function __construct() {		
		$wccpf_options = wcff()->option->get_options();
		$field_location = isset( $wccpf_options["field_location"] ) ? $wccpf_options["field_location"] : "woocommerce_before_add_to_cart_button";
		$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
		$group_fields_on_cart = isset( $wccpf_options["group_fields_on_cart"] ) ? $wccpf_options["group_fields_on_cart"] : "no";
		
		if( $field_location != "woocommerce_single_product_tab" ) {
			add_action( $field_location, array( $this, 'inject_wccpf' ) );
		} else {
			add_filter( 'woocommerce_product_tabs', array( $this, 'wccpf_product_tab' ) );
		}		
		
		add_filter( 'woocommerce_add_to_cart_validation', array( $this, 'validate_wccpf' ), 99, 2 );
		add_filter( 'woocommerce_add_cart_item_data', array( $this, 'save_wccpf_data' ), 10, 2 );				
		add_action( 'woocommerce_add_order_item_meta', array( $this, 'save_wccpf_order_meta' ), 1, 3 );
		
		if( $group_fields_on_cart == "yes" && $fields_cloning == "yes" ) {			
			add_filter( 'woocommerce_cart_item_name', array( $this, 'render_wccpf_data_on_cart' ), 1, 3 );			
			add_filter( 'woocommerce_checkout_cart_item_quantity', array( $this, 'render_wccpf_data_on_checkout' ), 1, 3 );									
		} else {
			add_filter( 'woocommerce_get_item_data', array( $this, 'render_wccpf_data' ), 1, 2 );
		}
	}
	
	function wccpf_product_tab( $tabs = array() ) {
		$wccpf_options = wcff()->option->get_options();
		$tabs['wccpf_fields_tab'] = array(
			'title'    => $wccpf_options[ "product_tab_title" ],
			'priority' => $wccpf_options[ "product_tab_priority" ],
			'callback' => array( $this, 'inject_wccpf' )
		);
		return $tabs;
	}
	
	function inject_wccpf() {
		Global $product;
		
		$group_index = 0;
		$is_datepicker_there = false;
		$is_colorpicker_there = false;
		
		$fields_group_title = "";
		$wccpf_options = wcff()->option->get_options();
		$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
		$show_field_group_title =  isset( $wccpf_options["show_group_title"] ) ? $wccpf_options["show_group_title"] : "no";
		
		if( isset( $wccpf_options["fields_group_title"] ) && $wccpf_options["fields_group_title"] != "" ) {
			$fields_group_title = $wccpf_options["fields_group_title"];
		} else {
			$fields_group_title = "Additional Options : ";
		}
			
		$all_fields = apply_filters( 'wcff/load/all_fields', $product->id, 'wccpf' );
		$admin_fields = apply_filters( 'wcff/load/all_fields', $product->id, 'wccaf', 'any' ); 
		
		foreach ( $all_fields as $title => $fields ) {
			if( count( $fields ) > 0 ) {
				foreach ( $fields as $field ) {
					if( $field["type"] == "label" && $field["position"] == "beginning" ) {
						$html = apply_filters( 'wcff/render/product/field/type='.$field["type"], $field );
						/* Allow third party apps logic to render wccpf fields with their own wish */
						if( has_filter( 'wccpf/before/fields/rendering' ) ) {
							$html = apply_filters( 'wccpf/before/fields/rendering', $field, $html );
						}
							
						do_action( 'wccpf/before/field/start', $field );
							
						echo $html;
							
						do_action( 'wccpf/after/field/end', $field );
					}
				}
			}
		}
		
		do_action( 'wccpf/before/fields/start' );
		
		if( $fields_cloning == "yes" ) {
			if( count( $all_fields ) > 0 || count( $admin_fields ) > 0 ) {
				echo '<div id="wccpf-fields-container">';
				echo '<input type="hidden" id="wccpf_fields_clone_count" value="1" />';
				echo '<div class="wccpf-fields-group">';
				echo '<h4 class="wccpf-cloning-group-title">'. $fields_group_title .' <span class="wccpf-fields-group-title-index">1</span></h4>';
			}
		}
		
		foreach ( $all_fields as $title => $fields ) {
			if( count( $fields ) > 0 ) {		
				echo '<div class="wccpf-fields-group-'. ++$group_index .'">';
				
				if( $show_field_group_title == "yes" ) {
					echo '<h3 class="wccpf-fields-group-title">'. esc_html( $title ) .'</h3>';
				}
				
				foreach ( $fields as $key => $field ) {
					if( $field["type"] == "label" && $field["position"] != "normal" ) {
						continue;	
					}					
					/* generate html for wccpf fields */
					$html = apply_filters( 'wcff/render/product/field/type='.$field["type"], $field );
					/* Allow third party apps logic to render wccpf fields with their own wish */
					if( has_filter( 'wccpf/before/fields/rendering' ) ) {
						$html = apply_filters( 'wccpf/before/fields/rendering', $field, $html );
					}
					
					do_action( 'wccpf/before/field/start', $field );
					
					echo $html;
					
					do_action( 'wccpf/after/field/end', $field );
					
					if( $field["type"] == "datepicker" ) {
						$is_datepicker_there = true;
					}
					
					if( $field["type"] == "colorpicker" ) {
						$is_colorpicker_there = true;
					}				
				}
				echo '</div>';
			}
		}
		
		if( count( $admin_fields ) > 0 ) {
			foreach ( $admin_fields as $title => $afields ) {
				if( count( $afields ) > 0 ) {
					foreach ( $afields as $key => $afield ) {
						$afield["show_on_product_page"] = isset( $afield["show_on_product_page"] ) ? $afield["show_on_product_page"] : "no";
						if( $afield["show_on_product_page"] == "yes" ) {										
							$mval = get_post_meta( $product->id, "wccaf_". $afield["name"], true );							
							if( !$mval || $mval == "" ) {
								if( isset( $afield["default_value"] ) && $afield["type"] != "radio" ) {
									$mval = $afield["default_value"];
								} else {
									$mval = "";
								}
							}
							if( $afield["type"] != "radio" ) {
								$afield["default_value"] = $mval;
							}							
							$afield["is_admin_field"] = true;							
							/* generate html for wccpf fields */
							$html = apply_filters( 'wcff/render/product/field/type='.$afield["type"], $afield );
							/* Allow third party apps logic to render wccpf fields with their own wish */
							if( has_filter( 'wccpf/before/fields/rendering' ) ) {
								$html = apply_filters( 'wccpf/before/fields/rendering', $afield, $html );
							}
								
							do_action( 'wccpf/before/field/start', $afield );
								
							echo $html;
								
							do_action( 'wccpf/after/field/end', $afield );
								
							if( $afield["type"] == "datepicker" ) {
								$is_datepicker_there = true;
							}
								
							if( $afield["type"] == "colorpicker" ) {
								$is_colorpicker_there = true;
							}
						}
					}
				}
			}
		}
		
		if( $fields_cloning == "yes" ) {
			if( count( $all_fields ) > 0 || count( $admin_fields ) > 0 ) {
				echo '</div>';
				echo '</div>';
			}
		}
		
		do_action( 'wccpf/after/fields/end' );
		
		foreach ( $all_fields as $title => $fields ) {
			if( count( $fields ) > 0 ) {
				foreach ( $fields as $field ) {
					if( $field["type"] == "label" && $field["position"] == "end" ) {
						$html = apply_filters( 'wcff/render/product/field/type='.$field["type"], $field );
						/* Allow third party apps logic to render wccpf fields with their own wish */
						if( has_filter( 'wccpf/before/fields/rendering' ) ) {
							$html = apply_filters( 'wccpf/before/fields/rendering', $field, $html );
						}
							
						do_action( 'wccpf/before/field/start', $field );
							
						echo $html;
							
						do_action( 'wccpf/after/field/end', $field );
					}
				}
			}
		}
		
		?>
		
		<script type="text/javascript">	
		    var wccpf_opt = {
				cloning : "<?php echo isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no"; ?>",
				location : "<?php echo isset( $wccpf_options["field_location"] ) ? $wccpf_options["field_location"] : "woocommerce_before_add_to_cart_button"; ?>",
				validation : "<?php echo isset( $wccpf_options["client_side_validation"] ) ? $wccpf_options["client_side_validation"] : "no"; ?>",
				validation_type : "<?php echo isset( $wccpf_options["client_side_validation_type"] ) ? $wccpf_options["client_side_validation_type"] : "submit"; ?>"				
			};			
		</script>
		
		<?php 
		
		$loc = isset( $wccpf_options["field_location"] ) ? $wccpf_options["field_location"] : "woocommerce_before_add_to_cart_button";
		
		if( $loc != "woocommerce_before_add_to_cart_button" && $loc != "woocommerce_after_add_to_cart_button" ) :
		?>
		<!-- Added by WCFF, to fix jQuery clone issue for Select Fields -->
		<script type="text/javascript">
			(function (original) {
			  jQuery.fn.clone = function () {
			    var result           = original.apply(this, arguments),
			        my_selects       = this.find('select').add(this.filter('select')),
			        result_selects   = result.find('select').add(result.filter('select'));
		        			    
			    for ( var i = 0, l = my_selects.length; i < l; ++i ) result_selects[i].selectedIndex = my_selects[i].selectedIndex;

			    return result;
			  };
			}) (jQuery.fn.clone);
		</script>
		
		<?php	
		endif;

		$this->wccpf_front_end_enqueue_scripts( $is_datepicker_there, $is_colorpicker_there );

		if( $is_colorpicker_there ) {
			$this->wccpf_inject_color_picker_script();
		}
		
	}
	
	/**	 
	 * @param 	BOOLEAN	 $unknown
	 * @param 	INT		 $pid
	 * @param 	INT		 $quantity
	 * @todo	There is an unsolved issue, when grouped products are validated ( There won't be $pid ).
	 */
	function validate_wccpf( $passed, $pid = null ) {		
		if( isset( $pid ) ) {
			$is_passed = $passed;
			
			$wccpf_options = wcff()->option->get_options();
			$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
			
			$all_fields = apply_filters( 'wcff/load/all_fields', $pid, 'wccpf' );
			$admin_fields = apply_filters( 'wcff/load/all_fields', $pid, 'wccaf', 'any' );
			
			if( $fields_cloning == "no" ) {
				foreach ( $all_fields as $title => $fields ) {
					foreach ( $fields as $field ) {						
						$res = true;				
						$is_multi_file = isset( $field["multi_file"] ) ? $field["multi_file"] : "no";
						if( $field["required"] == "yes" || $field["type"] == "file" ) {
							if( $field["type"] != "file" ) {								
								$res = apply_filters( 'wccpf/validate/type='.$field["type"], $_REQUEST[ $field["name"] ] );								
							} else {								
								if( $is_multi_file == "yes" ) {
									$files = $_FILES[ $field["name"] ];
									foreach ( $files['name'] as $key => $value ) {
										if ( $files['name'][$key] ) {
											$file = array(
												'name'     => $files['name'][$key],
												'type'     => $files['type'][$key],
												'tmp_name' => $files['tmp_name'][$key],
												'error'    => $files['error'][$key],
												'size'     => $files['size'][$key]
											);
											$res = apply_filters( 'wccpf/upload/validate', $file, $field['filetypes'], $field["required"] );
											if( !$res ) {
												break;
											}
										}
									}
								} else {
									$res = apply_filters( 'wccpf/upload/validate', $_FILES[ $field["name"] ], $field['filetypes'], $field["required"] );
								}								
							}
						}						
						if( !$res ) {						
							$is_passed = false;
							wc_add_notice( $field["message"], 'error' );
						}
					}
				}	
				if( count( $admin_fields ) > 0 ) {
					foreach ( $admin_fields as $title => $afields ) {
						if( count( $afields ) > 0 ) {
							foreach ( $afields as $key => $afield ) {
								$res = true;
								$afield["show_on_product_page"] = isset( $afield["show_on_product_page"] ) ? $afield["show_on_product_page"] : "no";
								if( $afield["show_on_product_page"] == "yes" && $afield["required"] == "yes" ) {									
									$res = apply_filters( 'wccpf/validate/type='.$afield["type"], $_REQUEST[ $afield["name"] ] );
								}
								if( !$res ) {
									$is_passed = false;
									wc_add_notice( $afield["message"], 'error' );
								}
							}
						}
					}
				}
			} else  {
				if( isset( $_REQUEST["quantity"] ) ) {
					$pcount = intval( $_REQUEST["quantity"] );
					foreach ( $all_fields as $title => $fields ) {
						foreach ( $fields as $field ) {
							$is_multi_file = isset( $field["multi_file"] ) ? $field["multi_file"] : "no";
							if( $field["required"] == "yes" || $field["type"] == "file" ) {
								for( $i = 1; $i <= $pcount; $i++ ) {
									$res = true;																
									if( $field["type"] != "file" ) {										
										$res = apply_filters( 'wccpf/validate/type='.$field["type"], $_REQUEST[ $field["name"] . "_" . $i ] );										
									} else {
										if( $is_multi_file == "yes" ) {
										$files = $_FILES[ $field["name"] . "_" . $i ];
										foreach ( $files['name'] as $key => $value ) {
											if ( $files['name'][$key] ) {
												$file = array(
													'name'     => $files['name'][$key],
													'type'     => $files['type'][$key],
													'tmp_name' => $files['tmp_name'][$key],
													'error'    => $files['error'][$key],
													'size'     => $files['size'][$key]
												);
												$res = apply_filters( 'wccpf/upload/validate', $file, $field['filetypes'], $field["required"] );
												if( !$res ) {
													break;
												}
											}
										}
										} else {
											$res = apply_filters( 'wccpf/upload/validate', $_FILES[ $field["name"] . "_" . $i ], $field['filetypes'], $field["required"] );
										}
									}
									if( !$res ) {										
										$is_passed = false;
										wc_add_notice( $field["message"], 'error' );
									}	
								}
							}
						}
					}
					
					if( count( $admin_fields ) > 0 ) {
						foreach ( $admin_fields as $title => $afields ) {
							if( count( $afields ) > 0 ) {
								foreach ( $afields as $key => $afield ) {
									$res = true;
									$afield["show_on_product_page"] = isset( $afield["show_on_product_page"] ) ? $afield["show_on_product_page"] : "no";
									if( $afield["show_on_product_page"] == "yes" && $afield["required"] == "yes" ) {
										for( $i = 1; $i <= $pcount; $i++ ) {
											$res = apply_filters( 'wccpf/validate/type='.$afield["type"], $_REQUEST[ $afield["name"] . "_" . $i ] );
											if( !$res ) {
												$is_passed = false;
												wc_add_notice( $afield["message"], 'error' );
											}
										}										
									}							
								}
							}
						}
					}
					
				}
			}			
			
			return $is_passed;			
		}
		
		return $passed;		
	}
	
	function save_wccpf_data( $cart_item_data, $product_id ) {
		$unique_cart_item_key = md5( microtime().rand() );
		//$cart_item_data['wccpf_unique_key'] = $unique_cart_item_key;
		if( $product_id ) {
			$val = "";
			$wccpf_options = wcff()->option->get_options();
			$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
			
			$all_fields = apply_filters( 'wcff/load/all_fields', $product_id, 'wccpf' );
			$admin_fields = apply_filters( 'wcff/load/all_fields', $product_id, 'wccaf', 'any' );
			
			if( $fields_cloning == "no" ) {
				foreach ( $all_fields as $title => $fields ) {
					foreach ( $fields as $field ) {	
						$is_multi_file = isset( $field["multi_file"] ) ? $field["multi_file"] : "no";
						if( isset( $_REQUEST[ $field["name"] ] ) || isset( $_FILES[ $field["name"] ] ) ) {
							if( $field["type"] != "checkbox" && $field["type"] != "file" ) {								
								$cart_item_data[ "wccpf_" . $field["name"] ] = $_REQUEST[ $field["name"] ];								
							} else if( $field["type"] == "checkbox" ) {
								$cart_item_data[ "wccpf_" . $field["name"] ] = implode( ", ", $_REQUEST[ $field["name"] ] );								
							} else {								
								$res = array();
								/* Handle the file upload */
								if( $is_multi_file == "yes" ) {
									$files = $_FILES[ $field["name"] ];
									foreach ( $files['name'] as $key => $value ) {									
										if ( $files['name'][$key] ) {									
											$file = array(
												'name'     => $files['name'][$key],
												'type'     => $files['type'][$key],
												'tmp_name' => $files['tmp_name'][$key],
												'error'    => $files['error'][$key],
												'size'     => $files['size'][$key]
											);
											$temp_res = apply_filters( 'wccpf/upload/type=file', $file );
											if( isset( $temp_res['error'] ) ) {
												$res = $temp_res;
												break;
											} else {
												$res[] = $temp_res;
											}
										}
									}									
								} else {
									$res = apply_filters( 'wccpf/upload/type=file', $_FILES[ $field["name"] ] );
								}								
								if( !isset( $res['error'] ) ) {
									$cart_item_data[ "wccpf_" . $field["name"] ] = json_encode( $res );									
									do_action( 'wccpf/uploaded/file', $res );
								} else {
									wc_add_wp_error_notices( $field["message"], 'error' );
								}
							}
						}						
					}
				}	
				if( count( $admin_fields ) > 0 ) {
					foreach ( $admin_fields as $title => $afields ) {
						if( count( $afields ) > 0 ) {
							foreach ( $afields as $key => $afield ) {
								$afield["show_on_product_page"] = isset( $afield["show_on_product_page"] ) ? $afield["show_on_product_page"] : "no";								
								if( $afield["show_on_product_page"] == "yes" && isset( $_REQUEST[ $afield["name"] ] ) ) {									
									if( $afield["type"] != "checkbox" ) {
										$cart_item_data[ "wccpf_" . $afield["name"] ] = $_REQUEST[ $afield["name"] ];										
									} else {
										$cart_item_data[ "wccpf_" . $afield["name"] ] = implode( ", ", $_REQUEST[ $afield["name"] ] );
									}								
								}
							}
						}
					}
				}
			} else {
				if( isset( $_REQUEST["quantity"] ) ) {
					$pcount = intval( $_REQUEST["quantity"] );
					foreach ( $all_fields as $title => $fields ) {
						foreach ( $fields as $field ) {
							$is_multi_file = isset( $field["multi_file"] ) ? $field["multi_file"] : "no";
							for( $i = 1; $i <= $pcount; $i++ ) {
								if( isset( $_REQUEST[ $field["name"] . "_" . $i ] ) || isset( $_REQUEST[ $field["name"] . "_" . $i . "[]" ] ) || isset( $_FILES[ $field["name"] . "_" . $i ] ) ) {
									if( $field["type"] != "checkbox" && $field["type"] != "file" ) {
										$cart_item_data[ "wccpf_" . $field["name"] . "_" . $i ] = $_REQUEST[ $field["name"] . "_" . $i ];									
									} else if( $field["type"] == "checkbox" ) {
										$cart_item_data[ "wccpf_" . $field["name"] . "_" . $i ] = implode( ", ", $_REQUEST[ $field["name"] . "_" . $i ] );										
									} else {
										$res = array();
										/* Handle the file upload */
										if( $is_multi_file == "yes" ) {
											$files = $_FILES[ $field["name"] . "_" . $i ];
											foreach ( $files['name'] as $key => $value ) {
												if ( $files['name'][$key] ) {
													$file = array(
															'name'     => $files['name'][$key],
															'type'     => $files['type'][$key],
															'tmp_name' => $files['tmp_name'][$key],
															'error'    => $files['error'][$key],
															'size'     => $files['size'][$key]
													);
													$temp_res = apply_filters( 'wccpf/upload/type=file', $file );
													if( isset( $temp_res['error'] ) ) {
														$res = $temp_res;
														break;
													} else {
														$res[] = $temp_res;
													}
												}
											}											
										} else {
											$res = apply_filters( 'wccpf/upload/type=file', $_FILES[ $field["name"] . "_" . $i ] );
										}										
										if( !isset( $res['error'] ) ) {
											$cart_item_data[ "wccpf_" . $field["name"] . "_" . $i ] = json_encode( $res );											
											do_action( 'wccpf/uploaded/file', $res );
										} else {
											wc_add_wp_error_notices( $field["message"], 'error' );
										}
									}
								}
							}
						}
					}
					
					if( count( $admin_fields ) > 0 ) {
						foreach ( $admin_fields as $title => $afields ) {
							if( count( $afields ) > 0 ) {
								foreach ( $afields as $key => $afield ) {	
									$afield["show_on_product_page"] = isset( $afield["show_on_product_page"] ) ? $afield["show_on_product_page"] : "no";
									if( $afield["show_on_product_page"] == "yes" ) {
										for( $i = 1; $i <= $pcount; $i++ ) {
											if( isset( $_REQUEST[ $afield["name"] . "_" . $i ] ) ) {
												if( $afield["type"] != "checkbox" ) {
													$cart_item_data[ "wccpf_" . $afield["name"] . "_" . $i ] = $_REQUEST[ $afield["name"] . "_" . $i ];													
												} else {
													$cart_item_data[ "wccpf_" . $afield["name"] . "_" . $i ] = implode( ", ", $_REQUEST[ $afield["name"] . "_" . $i ] );
												}
												
											}									
										}
									}
								}
							}
						}
					}					
				}
			}			
		}		
		return $cart_item_data;
	}

	function render_wccpf_data( $cart_data, $cart_item = null ) {	
		$wccpf_items = array();
		/* Woo 2.4.2 updates */
		if( !empty( $cart_data ) ) {
			$wccpf_items = $cart_data;
		}
		if( isset( $cart_item['product_id'] ) ) {
			
			$wccpf_options = wcff()->option->get_options();
			$show_custom_data = isset( $wccpf_options["show_custom_data"] ) ? $wccpf_options["show_custom_data"] : "yes";
			$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
			$group_meta_on_cart = isset( $wccpf_options["group_meta_on_cart"] ) ? $wccpf_options["group_meta_on_cart"] : "no";
									
			$all_fields = apply_filters( 'wcff/load/all_fields', $cart_item['product_id'], 'wccpf' );
			$admin_fields = apply_filters( 'wcff/load/all_fields', $cart_item['product_id'], 'wccaf', 'any' );
			
			if( $show_custom_data == "yes" ) {
				if( $fields_cloning == "no" ) {
					foreach ( $all_fields as $title => $fields ) {
						foreach ( $fields as $field ) {	
							$is_multi_file = isset( $field["multi_file"] ) ? $field["multi_file"] : "no";
							$field["visibility"] = isset( $field["visibility"] ) ? $field["visibility"] : "yes";
							if( $field["visibility"] == "yes" ) {
								if( isset( $cart_item['wccpf_'. $field["name"] ] ) && trim( $cart_item['wccpf_'. $field["name"] ] ) ) {
									if( $field["type"] == "file" ) {
										if( $is_multi_file == "yes" ) {
											$fnames = array();											
											$farray = json_decode( $cart_item['wccpf_'. $field["name"] ], true );
											foreach ( $farray as $fobj ) {
												$path_parts = pathinfo( $fobj['file'] );
												$fnames[] = $path_parts["basename"];	
											}
											$wccpf_items[] = array( "name" => $field["label"], "value" => implode( ", ", $fnames ) );
										} else {
											$fobj = json_decode( $cart_item['wccpf_'. $field["name"] ], true );										
											$path_parts = pathinfo( $fobj['file'] );
											$wccpf_items[] = array( "name" => $field["label"], "value" => $path_parts["basename"] );
										}
									} else {
										$wccpf_items[] = array( "name" => $field["label"], "value" => $cart_item['wccpf_'. $field["name"] ] );
									}
								}	
							}							
						}
					}
					if( count( $admin_fields ) > 0 ) {
						foreach ( $admin_fields as $title => $afields ) {
							if( count( $afields ) > 0 ) {
								foreach ( $afields as $key => $afield ) {
									$afield["visibility"] = isset( $afield["visibility"] ) ? $afield["visibility"] : "yes";
									if( isset( $cart_item['wccpf_'. $afield["name"] ] ) && trim( $cart_item['wccpf_'. $afield["name"] ] )  && $afield["visibility"] == "yes" ) {										
										$wccpf_items[] = array( "name" => $afield["label"], "value" => $cart_item['wccpf_'. $afield["name"] ] );
									}
								}
							}
						}
					}					
				} else {
					if( isset( $cart_item["quantity"] ) ) {
						$pcount = intval( $cart_item["quantity"] );
						foreach ( $all_fields as $title => $fields ) {							
							if( $group_meta_on_cart == "yes" ) {								
								foreach ( $fields as $field ) {
									$is_multi_file = isset( $field["multi_file"] ) ? $field["multi_file"] : "no";
									for( $i = 1; $i <= $pcount; $i++ ) {
										$field["visibility"] = isset( $field["visibility"] ) ? $field["visibility"] : "yes";
										if( $field["visibility"] == "yes" ) {
											if( isset( $cart_item['wccpf_'. $field["name"] . "_" . $i ] ) && trim( $cart_item['wccpf_'. $field["name"] . "_" . $i ] ) ) {
												if( $field["type"] == "file" ) {
													if( $is_multi_file == "yes" ) {
														$fnames = array();
														$farray = json_decode( $cart_item['wccpf_'. $field["name"] . "_" . $i ], true );
														foreach ( $farray as $fobj ) {
															$path_parts = pathinfo( $fobj['file'] );
															$fnames[] = $path_parts["basename"];
														}
														$wccpf_items[] = array( "name" => $field["label"] . "_" . $i, "value" => implode( ", ", $fnames ) );
													} else {
														$fobj = json_decode( $cart_item['wccpf_'. $field["name"] . "_" . $i ], true );
														$path_parts = pathinfo( $fobj['file'] );
														$wccpf_items[] = array( "name" => $field["label"] . " - " . $i, "value" => $path_parts["basename"] );
													}
												} else {
													$wccpf_items[] = array( "name" => $field["label"] . " - " . $i, "value" => $cart_item['wccpf_'. $field["name"] . "_" . $i ] );
												}
											}
										}																		
									}
								}
							} else {
								for( $i = 1; $i <= $pcount; $i++ ) {
									foreach ( $fields as $field ) {
										$is_multi_file = isset( $field["multi_file"] ) ? $field["multi_file"] : "no";
										$field["visibility"] = isset( $field["visibility"] ) ? $field["visibility"] : "yes";
										if( $field["visibility"] == "yes" ) {
											if( isset( $cart_item['wccpf_'. $field["name"] . "_" . $i ] ) && trim( $cart_item['wccpf_'. $field["name"] . "_" . $i ] ) ) {
												if( $field["type"] == "file" ) {
													if( $is_multi_file == "yes" ) {
														$fnames = array();
														$farray = json_decode( $cart_item['wccpf_'. $field["name"] . "_" . $i ], true );
														foreach ( $farray as $fobj ) {
															$path_parts = pathinfo( $fobj['file'] );
															$fnames[] = $path_parts["basename"];
														}
														$wccpf_items[] = array( "name" => $field["label"] . "_" . $i, "value" => implode( ", ", $fnames ) );
													} else {
														$fobj = json_decode( $cart_item['wccpf_'. $field["name"] . "_" . $i ], true );
														$path_parts = pathinfo( $fobj['file'] );
														$wccpf_items[] = array( "name" => $field["label"] . " - " . $i, "value" => $path_parts["basename"] );
													}
												} else {
													$wccpf_items[] = array( "name" => $field["label"] . " - " . $i, "value" => $cart_item['wccpf_'. $field["name"] . "_" . $i ] );
												}
											}
										}
									}															
								}	
							}						
						}	
						
						if( count( $admin_fields ) > 0 ) {
							foreach ( $admin_fields as $title => $afields ) {
								if( count( $afields ) > 0 ) {
									if( $group_meta_on_cart == "yes" ) {
										foreach ( $afields as $key => $afield ) {
											for( $i = 1; $i <= $pcount; $i++ ) {
												$afield["visibility"] = isset( $afield["visibility"] ) ? $afield["visibility"] : "yes";
												if( isset( $cart_item['wccpf_'. $afield["name"] . "_" . $i ] ) && trim( $cart_item['wccpf_'. $afield["name"] . "_" . $i ] )  && $afield["visibility"] == "yes" ) {
													$wccpf_items[] = array( "name" => $afield["label"] . "_" . $i, "value" => $cart_item['wccpf_'. $afield["name"] . "_" . $i ] );
												}
											}
										}
									} else {
										for( $i = 1; $i <= $pcount; $i++ ) {
											foreach ( $afields as $key => $afield ) {											
												$afield["visibility"] = isset( $afield["visibility"] ) ? $afield["visibility"] : "yes";
												if( isset( $cart_item['wccpf_'. $afield["name"] . "_" . $i ] ) && trim( $cart_item['wccpf_'. $afield["name"] . "_" . $i ] )  && $afield["visibility"] == "yes" ) {
													$wccpf_items[] = array( "name" => $afield["label"] . "_" . $i, "value" => $cart_item['wccpf_'. $afield["name"] . "_" . $i ] );
												}
											}
										}
									}									
								}
							}
						}
						
					}
				}
			}						
		}		
		return $wccpf_items;
	}
	
	function render_wccpf_data_on_cart( $title = null, $cart_item = null, $cart_item_key = null ) {
		if( is_cart() ) {
			return $this->render_wccpf_cloning_fields_data( $title, $cart_item, false );
		}
		return $title;
	}
	
	function render_wccpf_data_on_checkout( $quantity = null, $cart_item = null, $cart_item_key = null ) {		
		return $this->render_wccpf_cloning_fields_data( $quantity, $cart_item, true );				
	}
	
	function render_wccpf_cloning_fields_data( $html = "", $cart_item = null, $is_review_table ) {
		$meta_html = "";
		$wccpf_options = wcff()->option->get_options();
		$show_custom_data = isset( $wccpf_options["show_custom_data"] ) ? $wccpf_options["show_custom_data"] : "yes";
		$group_meta_on_cart = isset( $wccpf_options["group_meta_on_cart"] ) ? $wccpf_options["group_meta_on_cart"] : "no";		
		
		if( $show_custom_data == "no" ) {			
			return $html;
		}
		
		$fields_group_title = "";	
		if( isset( $wccpf_options["fields_group_title"] ) && $wccpf_options["fields_group_title"] != "" ) {
			$fields_group_title = $wccpf_options["fields_group_title"];
		} else {
			$fields_group_title = "Additional Options : ";
		}
		
		if( isset( $cart_item['product_id'] ) ) {	
			
			$all_fields = apply_filters( 'wcff/load/all_fields', $cart_item['product_id'], 'wccpf' );
			$admin_fields = apply_filters( 'wcff/load/all_fields', $cart_item['product_id'], 'wccaf', 'any' );
			
			if( isset( $cart_item["quantity"] ) ) {
				
				$meta_html .= '<div class="wccpf-fields-group-on-cart">';
				
				$pcount = intval( $cart_item["quantity"] );
				foreach ( $all_fields as $title => $fields ) {					
					for( $i = 1; $i <= $pcount; $i++ ) {
						$meta_there = false;
						/* Make sure cart_item does contains some custom meta to display */
						foreach ( $fields as $field ) {
							$field["visibility"] = isset( $field["visibility"] ) ? $field["visibility"] : "yes";
							if( $field["visibility"] == "yes" ) {
								if( $cart_item['wccpf_'. $field["name"] . "_" . $i ] && trim( $cart_item['wccpf_'. $field["name"] . "_" . $i ] ) ) {
									$meta_there = true;
									break;
								}
							}
						}
						
						$title_index = ( $pcount == 1 ) ? "" : $i;
								
						$meta_html .= '<fieldset>';	
						
						if( $meta_there ) {
							$meta_html .= '<h5>'. esc_html( $fields_group_title ) . $title_index .'</h5>';
						}
						
						foreach ( $fields as $field ) {
							$is_multi_file = isset( $field["multi_file"] ) ? $field["multi_file"] : "no";
							$field["visibility"] = isset( $field["visibility"] ) ? $field["visibility"] : "yes";
							if( $field["visibility"] == "yes" ) {
								if( isset( $cart_item['wccpf_'. $field["name"] . "_" . $i ] ) && trim( $cart_item['wccpf_'. $field["name"] . "_" . $i ] ) ) {
									$meta_html .= '<ul>';
									$meta_html .= '<li>'. $field["label"] .' : </li>';
									
									if( $field["type"] == "file" ) {
										if( $is_multi_file == "yes" ) {
											$fnames = array();
											$farray = json_decode( $cart_item['wccpf_'. $field["name"] . "_" . $i ], true );
											foreach ( $farray as $fobj ) {
												$path_parts = pathinfo( $fobj['file'] );
												$fnames[] = $path_parts["basename"];
											}											
											$meta_html .= '<li>'. wp_kses_post( implode( ", ", $fnames ) ) .'</li>';
										} else {
											$fobj = json_decode( $cart_item['wccpf_'. $field["name"] . "_" . $i ], true );
											$path_parts = pathinfo( $fobj['file'] );																				
											$meta_html .= '<li>'. wp_kses_post( $path_parts["basename"] ) .'</li>';
										}
										
									} else {
										$meta_html .= '<li>'. wp_kses_post( wpautop( $cart_item['wccpf_'. $field["name"] . "_" . $i ] ) ) .'</li>';										
									}
									
									$meta_html .= '</ul>';
									
								}
							}
						}											
						$meta_html .= '</fieldset>';
					}
				}	
				
				if( count( $admin_fields ) > 0 ) {
					$meta_html .= '<fieldset>';
					foreach ( $admin_fields as $title => $afields ) {
						if( count( $afields ) > 0 ) {
							for( $i = 1; $i <= $pcount; $i++ ) {
								foreach ( $afields as $key => $afield ) {
									$afield["visibility"] = isset( $afield["visibility"] ) ? $afield["visibility"] : "yes";
									if( isset( $cart_item['wccpf_'. $afield["name"] . "_" . $i ] ) && trim( $cart_item['wccpf_'. $afield["name"] . "_" . $i ] )  && $afield["visibility"] == "yes" ) {
										$meta_html .= '<ul>';
										$meta_html .= '<li>'. $afield["label"] .' : </li>';
										$meta_html .= '<li>'. wp_kses_post( wpautop( $cart_item['wccpf_'. $afield["name"] . "_" . $i ] ) ) .'</li>';
										$meta_html .= '</ul>';
									}
								}
							}
						}
					}
					$meta_html .= '</fieldset>';
				}
				
				$meta_html .= '</div>';				
			}
		}
		
		$this->wccpf_front_end_enqueue_scripts( false, false );
		
		if( $is_review_table ) {
			$html = $html . $meta_html;
		} else {
			$html = $html . $meta_html ;
		}
		
		return $html;
	}
	
	function save_wccpf_order_meta( $item_id, $values, $cart_item_key ) {
		if( isset($values["product_id"] ) ) {			
			$wccpf_options = wcff()->option->get_options();
			$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
			
			$all_fields = apply_filters( 'wcff/load/all_fields', $values["product_id"], 'wccpf' );
			$admin_fields = apply_filters( 'wcff/load/all_fields', $values['product_id'], 'wccaf', 'any' );
			
			if( $fields_cloning == "no" ) {
				foreach ( $all_fields as $title => $fields ) {
					foreach ( $fields as $field ) {
						$add_as_meta = isset( $field["order_meta"] ) ? $field["order_meta"] : "yes";
						if( isset( $values[ 'wccpf_' . $field["name"] ] ) && trim( $values['wccpf_'. $field["name"] ] ) && $add_as_meta == "yes" ) {
							if( $field["type"] == "file" ) {								
								if( $field["multi_file"] == "yes" ) {
									$furls = array();
									$farray = json_decode( $values[ 'wccpf_'. $field["name"] ], true );
									foreach ( $farray as $fobj ) {										
										$furls[] = $fobj["url"];
									}								
									wc_add_order_item_meta( $item_id, $field["label"], implode( ", ", $furls ) );
								} else {
									$fobj = json_decode( $values[ 'wccpf_' . $field["name"] ], true );
									wc_add_order_item_meta( $item_id, $field["label"], $fobj["url"] );
								}
							} else {
								wc_add_order_item_meta( $item_id, $field["label"], $values[ 'wccpf_' . $field["name"] ] );
							}
						}						
					}
				}				
				if( count( $admin_fields ) > 0 ) {
					foreach ( $admin_fields as $title => $afields ) {
						if( count( $afields ) > 0 ) {
							foreach ( $afields as $key => $afield ) {
								$add_as_meta = isset( $afield["order_meta"] ) ? $afield["order_meta"] : "no";
								if( isset( $values['wccpf_'. $afield["name"] ] ) && $add_as_meta == "yes" ) {
									wc_add_order_item_meta( $item_id, $afield["label"], $values['wccpf_'. $afield["name"] ] );									
								}
							}
						}
					}
				}				
			} else {
				if( isset( $values["quantity"] ) ) {
					$pcount = intval( $values["quantity"] );
					foreach ( $all_fields as $title => $fields ) {						
						for( $i = 1; $i <= $pcount; $i++ ) {
							foreach ( $fields as $field ) {
								$add_as_meta = isset( $field["order_meta"] ) ? $field["order_meta"] : "yes";
								if( isset( $values[ 'wccpf_' . $field["name"] . "_" . $i ] ) && trim( $values['wccpf_'. $field["name"] . "_" . $i ] ) && $add_as_meta == "yes" ) {
									if( $field["type"] == "file" ) {
										if( $field["multi_file"] == "yes" ) {
											$furls = array();
											$farray = json_decode( $values['wccpf_'. $field["name"] . "_" . $i ], true );
											foreach ( $farray as $fobj ) {
												$furls[] = $fobj["url"];
											}
											wc_add_order_item_meta( $item_id, $field["label"] . " - " . $i, implode( ", ", $furls ) );
										} else {
											$fobj = json_decode( $values[ 'wccpf_' . $field["name"] . "_" . $i ], true );
											wc_add_order_item_meta( $item_id, $field["label"] . " - " . $i, $fobj["url"] );
										}
									} else {
										wc_add_order_item_meta( $item_id, $field["label"] . " - " . $i, $values[ 'wccpf_' . $field["name"] . "_" . $i ] );
									}
								}
							}												
						}
					}
					
					if( count( $admin_fields ) > 0 ) {
						foreach ( $admin_fields as $title => $afields ) {
							if( count( $afields ) > 0 ) {
								for( $i = 1; $i <= $pcount; $i++ ) {
									foreach ( $afields as $key => $afield ) {
										$add_as_meta = isset( $afield["order_meta"] ) ? $afield["order_meta"] : "no";
										if( isset( $values['wccpf_'. $afield["name"] . "_" . $i ] ) && $add_as_meta == "yes" ) {
											wc_add_order_item_meta( $item_id, $afield["label"] . " - " . $i, $values['wccpf_'. $afield["name"] . "_" . $i ] );
										}
									}
								}
							}
						}
					}
				}
			}			
		}			
	}
	
	function wccpf_front_end_enqueue_scripts( $is_datepicker_there, $is_colorpicker_there ) {
		if( is_shop() || is_product() || is_cart() || is_checkout() ) {	
			
			$wccpf_options = wcff()->option->get_options();
			$fields_cloning = isset( $wccpf_options["fields_cloning"] ) ? $wccpf_options["fields_cloning"] : "no";
			
			wp_register_style( 'wccpf-font-end-style', wcff()->info['dir'] . 'assets/css/wccpf-front-end.css' );
			wp_enqueue_style( 'wccpf-font-end-style' );			
			
			if( $is_datepicker_there ) {
				wp_enqueue_style( 'wccpf-jquery-ui-css','//ajax.googleapis.com/ajax/libs/jqueryui/1.9.0/themes/base/jquery-ui.css',false,"1.9.0",false);
				wp_enqueue_style( 'time-picker-addon', wcff()->info['dir'] . 'assets/css/jquery-ui-timepicker-addon.css', array(), null );
				wp_enqueue_script( 'jquery-ui-core' );
				wp_enqueue_script( 'jquery-ui-datepicker' );				
				wp_register_script( 'wccpf-datepicker-i18n', wcff()->info['dir'] . 'assets/js/jquery-ui-i18n.min.js' );
				wp_register_script( 'wccpf-datetime-picker', wcff()->info['dir'] . 'assets/js/jquery-ui-timepicker-addon.min.js' );				
				wp_enqueue_script( 'wccpf-datetime-picker' );
				wp_enqueue_script( 'wccpf-datepicker-i18n' );
			}
			
			if( $is_colorpicker_there ) {
				wp_register_style( 'spectrum-css', wcff()->info['dir'] . 'assets/css/spectrum.css' );
				wp_register_script( 'wccpf-color-picker', wcff()->info['dir'] . 'assets/js/spectrum.js' );
				wp_enqueue_style( 'spectrum-css' );
				wp_enqueue_script( 'wccpf-color-picker' );				
			}		
						
			wp_register_script( 'wccpf-front-end', wcff()->info['dir'] . 'assets/js/wccpf-front-end.js' );
			wp_enqueue_script( 'wccpf-front-end' );
			
		}
	}
	
	function wccpf_inject_color_picker_script() {
		Global $product;
		$all_fields = apply_filters( 'wcff/load/all_fields', $product->id, 'wccpf' );
		$admin_fields = apply_filters( 'wcff/load/all_fields', $product->id, 'wccaf', 'any' );
	
		echo '<script type="text/javascript">
				var $ = jQuery;
				function wccpf_init_color_pickers() {';
	
		foreach ( $all_fields as $title => $fields ) {
			foreach ( $fields as $key => $field ) {
				if( $field["type"] == "colorpicker" ) {
					$palettes = null;
					$colorformat = isset( $field["color_format"] ) ? $field["color_format"] : "hex";
					$defaultcolor = isset( $field["default_value"] ) ? $field["default_value"] : "#000";
					
					if( isset( $field["palettes"] ) && $field["palettes"] != "" ) {
						$palettes = explode( ";", $field["palettes"] );
					} ?>
											
					$( ".wccpf-color-<?php echo esc_attr( $field["name"] ); ?>").spectrum({
						 color: "<?php echo $defaultcolor; ?>", 
						 preferredFormat: "<?php echo $colorformat; ?>",					
						<?php 
						$comma = "";
						$indexX = 0;
						$indexY = 0;
						if( is_array( $palettes ) && count( $palettes ) > 0 ) {
							if( $field["show_palette_only"] == "yes" ) {
								echo "showPaletteOnly: true,";
							}
							echo "showPalette: true,";
							echo "palette : [";						
							foreach ( $palettes as $palette ) {		
								$indexX = 0;								
								$comma = ( $indexY == 0 ) ? "" : ",";
								echo $comma."[";
								$colors = explode( ",", $palette );
							 	foreach ( $colors as $color ) {							 		
							 		$comma = ( $indexX == 0 ) ? "" : ","; 
							 		echo $comma ."'". $color ."'";	
							 		$indexX++;
								}
								echo "]";
								$indexY++;
							} 
							echo "]";						
						}
						?>
					});		
				<?php 
				}
			}
		}		
		
		if( count( $admin_fields ) > 0 ) {
			foreach ( $admin_fields as $title => $afields ) {
				if( count( $afields ) > 0 ) {
					foreach ( $afields as $key => $afield ) {
						if( $afield["type"] == "colorpicker" ) {
							$palettes = null;
							$colorformat = isset( $afield["color_format"] ) ? $afield["color_format"] : "hex";
							$defaultcolor = isset( $afield["default_value"] ) ? $afield["default_value"] : "#000";
							
							$mval = get_post_meta( $product->id, "wccaf_". $afield["name"], true );
							if( !$mval || $mval == "" ) {								
								$mval = $defaultcolor;								
							}
								
							if( isset( $afield["palettes"] ) && $afield["palettes"] != "" ) {
								$palettes = explode( ";", $afield["palettes"] );
							} ?>
							
							$( ".wccpf-color-<?php echo esc_attr( $afield["name"] ); ?>").spectrum({
								 color: "<?php echo $mval; ?>", 
								 preferredFormat: "<?php echo $colorformat; ?>",					
								<?php 
								$comma = "";
								$indexX = 0;
								$indexY = 0;
								if( is_array( $palettes ) && count( $palettes ) > 0 ) {
									if( $afield["show_palette_only"] == "yes" ) {
										echo "showPaletteOnly: true,";
									}
									echo "showPalette: true,";
									echo "palette : [";						
									foreach ( $palettes as $palette ) {		
										$indexX = 0;								
										$comma = ( $indexY == 0 ) ? "" : ",";
										echo $comma."[";
										$colors = explode( ",", $palette );
									 	foreach ( $colors as $color ) {							 		
									 		$comma = ( $indexX == 0 ) ? "" : ","; 
									 		echo $comma ."'". $color ."'";	
									 		$indexX++;
										}
										echo "]";
										$indexY++;
									} 
									echo "]";						
								}
								?>
							});	
							
							<?php 
						}
					}	
				}				
			}
		}
		
		echo '}				
			$( document ).ready(function() {			
				wccpf_init_color_pickers();
			});
		</script>';
	}
} 

new wccpf_product_form();

?>