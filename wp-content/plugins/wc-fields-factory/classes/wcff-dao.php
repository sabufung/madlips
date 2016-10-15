<?php 
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * This is the core Data Access Object for the entire wccpf related CRUD operations. 
 * 
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_dao {
	/* Namespace for WCFF related post meta
	 * "wccpf_" for Custom product page Fields ( Front end product page )
	 * "wccaf_" for Custom admin page fields ( for Products & Product Categories )
	 *  */
	var $wcff_key_prefix = "wccpf_";
	
	function __construct() {
				
		add_action( 'save_post', array( $this, 'save_wcff_rules' ), 1, 3 );
		add_action( 'wcff/update/post/type', array( $this, 'update_wcff_post_type' ) );
		add_filter( 'wcff/load/condition/rules', array( $this, 'load_wcff_condition_rules' ) );
		add_filter( 'wcff/load/location/rules', array( $this, 'load_wcff_location_rules' ) );
		add_filter( 'wcff/load/all/location/rules', array( $this, 'load_wcff_add_location_rules' ) );		
		
		add_filter( 'wcff/load/products', array( $this, 'load_products' ) );
		add_filter( 'wcff/load/products/cat', array( $this, 'load_products_cat' ) );
		add_filter( 'wcff/load/products/tag', array( $this, 'load_products_tag' ) );
		add_filter( 'wcff/load/products/type', array( $this, 'load_products_type' ) );
		add_filter( 'wcff/load/products/tabs', array( $this, 'load_products_tabs' ) );
		add_filter( 'wcff/load/metabox/contexts', array( $this, 'load_metabox_contexts' ) );
		add_filter( 'wcff/load/metabox/priorities', array( $this, 'load_metabox_priorities' ) );
		
		add_filter( 'wcff/load/all_fields', array( $this, 'load_all_wcff_fields' ), 10, 3 );
		add_filter( 'wcff/load/fields', array( $this, 'load_wcff_fields' ), 5, 2 );
		add_filter( 'wcff/load/field', array( $this, 'load_wcff_field' ), 5, 2 );
		add_filter( 'wcff/save/field', array( $this, 'save_wcff_field' ), 5, 3 );
		add_filter( 'wcff/update/field', array( $this, 'update_wcff_field' ), 5, 2 );
		add_filter( 'wcff/remove/field', array( $this, 'remove_wcff_field' ), 5, 2 );	
		
	}
	
	function update_wcff_post_type( $type ) {		
		$this->wcff_key_prefix = $type . "_";
	}
	
	/**
	 * @return 	ARRAY
	 * @todo	Used to load all woocommerce products
	 * 			Used in "Conditions" Widget 
	 */
	function load_products() {
		$args = array( 'post_type' => 'product', 'order' => 'ASC', 'posts_per_page' => -1 );
		$products = get_posts( $args );
		$productsList = array();
		
		if( count( $products ) > 0 ) {
			foreach( $products as $product ) {				
				$productsList[] = array( "id" => $product->ID, "title" => $product->post_title );
			}
		}
		
		return $productsList;
	}
	
	/**
	 * @return 	ARRAY
	 * @todo	Used to load all woocommerce products category
	 * 			Used in "Conditions" Widget
	 */
	function load_products_cat() {
		$product_cat = array();
		$pcat_terms = get_terms( 'product_cat', 'orderby=count&hide_empty=0' );
		
		foreach( $pcat_terms as $pterm ) {
			$product_cat[] = array( "id" => $pterm->slug, "title" => $pterm->name );
		}
		
		return $product_cat;
	}	
	
	function load_products_tag() {
		$product_tag = array();
		$ptag_terms = get_terms( 'product_tag', 'orderby=count&hide_empty=0' );
		
		foreach( $ptag_terms as $pterm ) {
			$product_tag[] = array( "id" => $pterm->slug, "title" => $pterm->name );
		}
		
		return $product_tag;
	}
	
	function load_products_type() {
		$product_type = array();
		$default_type = apply_filters( 'default_product_type', 'simple' );
		$all_types = apply_filters( 'product_type_selector', array(
			'simple'   => __( 'Simple product', 'woocommerce' ),
			'grouped'  => __( 'Grouped product', 'woocommerce' ),
			'external' => __( 'External/Affiliate product', 'woocommerce' ),
			'variable' => __( 'Variable product', 'woocommerce' )
		), $default_type );
		
		foreach ( $all_types as $key => $value ) {
			$product_type[] = array( "id" => $key, "title" => $value );
		}
		
		return $product_type;
	}
	
	/**
	 * @return 	ARRAY
	 * @todo 	Used to load location values
	 * 			Used by wccaf location rules
	 */
	function load_products_tabs() {
		return apply_filters( 'wcff/product/tabs', array (
			"General Tab" => "woocommerce_product_options_general_product_data",
			"Inventory Tab" => "woocommerce_product_options_inventory_product_data",
			"Shipping Tab" => "woocommerce_product_options_shipping",
			"Attributes Tab" => "woocommerce_product_options_attributes",
			"Related Tab" => "woocommerce_product_options_related",
			"Advanced Tab" => "woocommerce_product_options_advanced",
			"Variable Tab" => "woocommerce_product_after_variable_attributes"
		));
	}
	
	function load_metabox_contexts() {
		return apply_filters( 'wcff/metabox/contexts', array( "normal" => "Normal", "advanced" => "Advanced", "side" => "Side" ));
	}
	
	function load_metabox_priorities() {
		return apply_filters( 'wcff/metabox/priorities', array( "high" => "High", "core" => "Core", "default" => "Default", "low" => "Low" ));
	}
	
	/**
	 * 
	 * @param 	INT 		$pid	- WCFF Post Id
	 * @param   BOOLEAN 	$sort   - Whether returning fields should be sorted
	 * @param   STRING  	$type   - Type of fields ( wccpf, wccaf ... )
	 * @return 	ARRAY
	 * @todo	This function is used to load all wcff fields for a single WCFF post
	 * 			mostly used in editing wccpf fields in admin screen 
	 */
	function load_wcff_fields( $pid, $sort = true ) {
		$fields = array();
		$meta = get_post_meta( $pid );		
		foreach ( $meta as $key => $val ) {
		 	if( preg_match('/'. $this->wcff_key_prefix .'/', $key) ) {
		 		if( $key != $this->wcff_key_prefix.'condition_rules' && $key != $this->wcff_key_prefix.'location_rules' && $key != $this->wcff_key_prefix.'group_rules' ) {		 			
					$fields[ $key ] = json_decode( $val[0], true );
				}	
		 	}
		 }
		 
		 if( $sort ) {
		 	$this->usort_by_column( $fields, "order" );
		 }
		 
		 return $fields;
	}
	
	/**
	 * 
	 * @param 	INT 		$pid	- Product Id
	 * @param   STRING  	$type   - Type of fields ( wccpf, wccaf ... )
	 * @return 	ARRAY 		( Two Dimentional )
	 * @todo	This function is used to Load all WCCPF groups. which is used by "wccpf_product_form" module
	 * 			to render actual wccpf fields on the Product Page.
	 */
	function load_all_wcff_fields( $pid, $type = "wccpf", $location = "product-page" ) {	
		$fields = array();
		$all_fields = array();		
		$this->wcff_key_prefix = $type ."_";		
		$args = array( 'post_type' => $type, 'order' => 'ASC', 'posts_per_page' => -1 );
		$wcffs = get_posts( $args );	
		
		if( count( $wcffs ) > 0 ) {
			foreach ( $wcffs as $wcff ) {				
				$fields = array();		
				$crules_applicable = false;
				$lrules_applicable = true;
						
				$meta = get_post_meta( $wcff->ID );
				$condition_rules = $this->load_wcff_condition_rules( $wcff->ID );
				$condition_rules = json_decode( $condition_rules, true );
				
				if( is_array( $condition_rules ) ) {
					$crules_applicable = $this->check_wcff_for_product( $pid, $condition_rules );
				} else {
					$crules_applicable = true;
				}
				
				if( $type == "wccaf" ) {
					$location_rules = get_post_meta( $wcff->ID, $this->wcff_key_prefix.'location_rules', true );
					$location_rules = json_decode( $location_rules, true );
					
					if( is_array( $location_rules ) && $location != "any" ) {						
						$lrules_applicable = $this->check_wcff_for_location( $pid, $location_rules, $location );						
					} else {
						$lrules_applicable = true;
					}
				}
				
				if( $crules_applicable && $lrules_applicable ) {
					foreach ( $meta as $key => $val ) {						
						if( preg_match('/'. $this->wcff_key_prefix .'/', $key) ) {
							if( $key != $this->wcff_key_prefix.'condition_rules' && $key != $this->wcff_key_prefix.'location_rules' && $key != $this->wcff_key_prefix.'group_rules' ) {
								$fields[ $key ] = json_decode( $val[0], true );
							}
						}
					}
					$this->usort_by_column( $fields, "order" );
					/* Updated from V 1.3.5 - Added fields group title as a key */
					$all_fields[ $wcff->post_title ] = $fields;
				}				
			}
		}
		
		return $all_fields;
	}
	
	/**
	 * @param 	INT 		$pid	- Product Id
	 * @param 	ARRAY 		$groups
	 * @return 	boolean
	 * @todo	WCFF Condition Rules Engine, This is function used to determine whether or not to include 
	 * 			a particular wccpf group to a particular Product  	
	 */
	function check_wcff_for_product( $pid, $groups ) {
		$matches = array();
		$final_matches = array();
		foreach ( $groups as $rules ) {
			$ands = array();
			foreach ( $rules as $rule ) {
				if( $rule["context"] == "product" ) {
					if( $rule["endpoint"] == -1 ) {
						$ands[] = ( $rule["logic"] == "==" );						
					} else {
						if( $rule["logic"] == "==") {							
							$ands[] = ( $pid == $rule["endpoint"] );
						} else {
							$ands[] = ( $pid != $rule["endpoint"] );
						}	
					}				
				} else if( $rule["context"] == "product_cat" ) {
					if( $rule["endpoint"] == -1 ) {						
						$ands[] = ( $rule["logic"] == "==" );
					} else {
						if( $rule["logic"] == "==") {						
							$ands[] = has_term( $rule["endpoint"], 'product_cat', $pid );
						} else {
							$ands[] = !has_term( $rule["endpoint"], 'product_cat', $pid );
						}
					}
				}  else if( $rule["context"] == "product_tag" ) {
					if( $rule["endpoint"] == -1 ) {						
						$ands[] = ( $rule["logic"] == "==" );
					} else {
						if( $rule["logic"] == "==") {						
							$ands[] = has_term( $rule["endpoint"], 'product_tag', $pid );
						} else {
							$ands[] = !has_term( $rule["endpoint"], 'product_tag', $pid );
						}
					}
				}  else if( $rule["context"] == "product_type" ) {
					if( $rule["endpoint"] == -1 ) {
						$ands[] = ( $rule["logic"] == "==" );						
					} else {
						$ptype = wp_get_object_terms( $pid, 'product_type' );
						$ands[] = ( $ptype[0]->slug == $rule["endpoint"] );						
					}
				}
			}
			$matches[] = $ands;
		}
		
		foreach ( $matches as $match ) {
			$final_matches[] = !in_array( false, $match );
		}
		
		return in_array( true, $final_matches );
	}
	
	/**	 
	 * @param INT 		$pid
	 * @param ARRAY		$groups
	 * @param STRING	$location
	 * @todo			WCFF Location Rules Engine, This is function used to determine where doesa  particular wccaf fields group 
	 * 					to be placed. in the product view, product cat view or one of any product data sections ( Tabs )
	 * 					applicable only for wccaf post_type	  	
	 */
	function check_wcff_for_location( $pid, $groups, $location ) {		
		foreach ( $groups as $rules ) {
			foreach ( $rules as $rule ) {
				if( $rule["context"] == "location_product_data" ) {
					if( $rule["endpoint"] == $location && $rule["logic"] == "==" ) {
						return true;
					}				
				} 
				if( $rule["context"] == "location_product" && $location == "admin_head-post.php" ) {
					return true;
				}
				if( $rule["context"] == "location_product_cat" && ( $location == "product_cat_add_form_fields" || $location == "product_cat_edit_form_fields" ) )  {
					return true;
				}
			}
		}				
		return false;
	}
	
	function load_wcff_condition_rules( $pid ) {
		/* Since we have renamed 'group_rules' meta as 'condition_rules' we need to make sure it is upto date
		 * and we remove the old 'group_rules' meta as well
		 *  */
		$rules = get_post_meta( $pid, $this->wcff_key_prefix.'group_rules', true ); 
		if( $rules && $rules != "" ) {
			delete_post_meta( $pid, $this->wcff_key_prefix.'group_rules' );
			update_post_meta( $pid, $this->wcff_key_prefix.'condition_rules', $rules );
		}
	 	return get_post_meta( $pid, $this->wcff_key_prefix.'condition_rules', true );	 	
	}
	
	function load_wcff_location_rules( $pid ) {
		return get_post_meta( $pid, $this->wcff_key_prefix.'location_rules', true );
	}
	
	function load_wcff_add_location_rules() {
		$location_rules = array();
		$args = array( 'post_type' => "wccaf", 'order' => 'ASC', 'posts_per_page' => -1 );
		$wcffs = get_posts( $args );		
		if( count( $wcffs ) > 0 ) {
			foreach ( $wcffs as $wcff ) {
				$temp_rules = get_post_meta( $wcff->ID, 'wccaf_location_rules', true );
				$temp_rules = json_decode( $temp_rules, true );
				$location_rules = array_merge( $location_rules, $temp_rules );
			}
		}		
		return $location_rules;
	}

	function save_wcff_rules( $post_id, $post, $update ) {		
		if( $post->post_type != "wccpf" && $post->post_type != "wccaf" ) {
			return;
		}			
		$this->wcff_key_prefix = $post->post_type . "_";		
		if( isset( $_REQUEST["wcff_condition_rules"] ) ) {
			delete_post_meta( $post_id, $this->wcff_key_prefix.'condition_rules' );
			add_post_meta( $post_id, $this->wcff_key_prefix.'condition_rules', $_REQUEST["wcff_condition_rules"] );
		}
		if( isset( $_REQUEST["wcff_location_rules"] ) ) {
			delete_post_meta( $post_id, $this->wcff_key_prefix.'location_rules' );
			add_post_meta( $post_id, $this->wcff_key_prefix.'location_rules', $_REQUEST["wcff_location_rules"] );
		}						
		$this->update_wcff_fields_order( $post_id );
		return true;
	}
	
	function update_wcff_fields_order( $pid ) {
		$fields = $this->load_wcff_fields( $pid, false );
		foreach ( $fields as $key => $field ) {
			$field["order"] = $_REQUEST[ $key."_order" ];
			update_post_meta( $pid, $key, wp_slash( json_encode( $field ) ) );
		}
		return true;
	}
	
	function load_wcff_field( $pid, $mkey ) {
		return get_post_meta( $pid, $mkey, true );
	}
	
	function save_wcff_field( $pid, $payload ) {		
		if( !isset( $payload["name"] ) || $payload["name"] == "_" || $payload["name"] == "" ) {
			$payload["name"] = $this->url_slug( $payload["label"], array( 'delimiter' => '_' ) );
		}
		return add_post_meta( $pid, $this->wcff_key_prefix.$payload["name"], wp_slash( json_encode( $payload ) ) );
	}
	
	function update_wcff_field( $pid, $payload ) {
		if( !isset( $payload["key"] ) || $payload["name"] == "_" || $payload["key"] == "" ) {
			$payload["key"] = $this->url_slug( $payload["label"], array( 'delimiter' => '_' ) );
		}
		return update_post_meta( $pid, $payload["key"], wp_slash( json_encode( $payload ) ) );
	}
	
	function remove_wcff_field( $pid, $mkey ) {
		return delete_post_meta( $pid, $mkey );
	}

	function usort_by_column( &$arr, $col, $dir = SORT_ASC) {
		$sort_col = array();
		foreach ($arr as $key=> $row) {
			$sort_col[$key] = $row[$col];
		}	
		array_multisort( $sort_col, $dir, $arr);
	}
	
	/**
	 * Create a web friendly URL slug from a string.
	 * 
	 * @author Sean Murphy <sean@iamseanmurphy.com>
	 * @copyright Copyright 2012 Sean Murphy. All rights reserved.
	 * @license http://creativecommons.org/publicdomain/zero/1.0/
	 *
	 * @param string $str
	 * @param array $options
	 * @return string
	 */
	function url_slug( $str, $options = array( )) {
		// Make sure string is in UTF-8 and strip invalid UTF-8 characters
		$str = mb_convert_encoding((string)$str, 'UTF-8', mb_list_encodings());
	
		$defaults = array(
				'delimiter' => '-',
				'limit' => null,
				'lowercase' => true,
				'replacements' => array(),
				'transliterate' => false,
		);
	
		// Merge options
		$options = array_merge($defaults, $options);
	
		$char_map = array(
				// Latin
				'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'AE', 'Ç' => 'C',
				'È' => 'E', 'É' => 'E', 'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I',
				'Ð' => 'D', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O', 'Õ' => 'O', 'Ö' => 'O', 'Ő' => 'O',
				'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ű' => 'U', 'Ý' => 'Y', 'Þ' => 'TH',
				'ß' => 'ss',
				'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'ae', 'ç' => 'c',
				'è' => 'e', 'é' => 'e', 'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i',
				'ð' => 'd', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o', 'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ő' => 'o',
				'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ü' => 'u', 'ű' => 'u', 'ý' => 'y', 'þ' => 'th',
				'ÿ' => 'y',
				// Latin symbols
				'©' => '(c)',
				// Greek
				'Α' => 'A', 'Β' => 'B', 'Γ' => 'G', 'Δ' => 'D', 'Ε' => 'E', 'Ζ' => 'Z', 'Η' => 'H', 'Θ' => '8',
				'Ι' => 'I', 'Κ' => 'K', 'Λ' => 'L', 'Μ' => 'M', 'Ν' => 'N', 'Ξ' => '3', 'Ο' => 'O', 'Π' => 'P',
				'Ρ' => 'R', 'Σ' => 'S', 'Τ' => 'T', 'Υ' => 'Y', 'Φ' => 'F', 'Χ' => 'X', 'Ψ' => 'PS', 'Ω' => 'W',
				'Ά' => 'A', 'Έ' => 'E', 'Ί' => 'I', 'Ό' => 'O', 'Ύ' => 'Y', 'Ή' => 'H', 'Ώ' => 'W', 'Ϊ' => 'I',
				'Ϋ' => 'Y',
				'α' => 'a', 'β' => 'b', 'γ' => 'g', 'δ' => 'd', 'ε' => 'e', 'ζ' => 'z', 'η' => 'h', 'θ' => '8',
				'ι' => 'i', 'κ' => 'k', 'λ' => 'l', 'μ' => 'm', 'ν' => 'n', 'ξ' => '3', 'ο' => 'o', 'π' => 'p',
				'ρ' => 'r', 'σ' => 's', 'τ' => 't', 'υ' => 'y', 'φ' => 'f', 'χ' => 'x', 'ψ' => 'ps', 'ω' => 'w',
				'ά' => 'a', 'έ' => 'e', 'ί' => 'i', 'ό' => 'o', 'ύ' => 'y', 'ή' => 'h', 'ώ' => 'w', 'ς' => 's',
				'ϊ' => 'i', 'ΰ' => 'y', 'ϋ' => 'y', 'ΐ' => 'i',
				// Turkish
				'Ş' => 'S', 'İ' => 'I', 'Ç' => 'C', 'Ü' => 'U', 'Ö' => 'O', 'Ğ' => 'G',
				'ş' => 's', 'ı' => 'i', 'ç' => 'c', 'ü' => 'u', 'ö' => 'o', 'ğ' => 'g',
				// Russian
				'А' => 'A', 'Б' => 'B', 'В' => 'V', 'Г' => 'G', 'Д' => 'D', 'Е' => 'E', 'Ё' => 'Yo', 'Ж' => 'Zh',
				'З' => 'Z', 'И' => 'I', 'Й' => 'J', 'К' => 'K', 'Л' => 'L', 'М' => 'M', 'Н' => 'N', 'О' => 'O',
				'П' => 'P', 'Р' => 'R', 'С' => 'S', 'Т' => 'T', 'У' => 'U', 'Ф' => 'F', 'Х' => 'H', 'Ц' => 'C',
				'Ч' => 'Ch', 'Ш' => 'Sh', 'Щ' => 'Sh', 'Ъ' => '', 'Ы' => 'Y', 'Ь' => '', 'Э' => 'E', 'Ю' => 'Yu',
				'Я' => 'Ya',
				'а' => 'a', 'б' => 'b', 'в' => 'v', 'г' => 'g', 'д' => 'd', 'е' => 'e', 'ё' => 'yo', 'ж' => 'zh',
				'з' => 'z', 'и' => 'i', 'й' => 'j', 'к' => 'k', 'л' => 'l', 'м' => 'm', 'н' => 'n', 'о' => 'o',
				'п' => 'p', 'р' => 'r', 'с' => 's', 'т' => 't', 'у' => 'u', 'ф' => 'f', 'х' => 'h', 'ц' => 'c',
				'ч' => 'ch', 'ш' => 'sh', 'щ' => 'sh', 'ъ' => '', 'ы' => 'y', 'ь' => '', 'э' => 'e', 'ю' => 'yu',
				'я' => 'ya',
				// Ukrainian
				'Є' => 'Ye', 'І' => 'I', 'Ї' => 'Yi', 'Ґ' => 'G',
				'є' => 'ye', 'і' => 'i', 'ї' => 'yi', 'ґ' => 'g',
				// Czech
				'Č' => 'C', 'Ď' => 'D', 'Ě' => 'E', 'Ň' => 'N', 'Ř' => 'R', 'Š' => 'S', 'Ť' => 'T', 'Ů' => 'U',
				'Ž' => 'Z',
				'č' => 'c', 'ď' => 'd', 'ě' => 'e', 'ň' => 'n', 'ř' => 'r', 'š' => 's', 'ť' => 't', 'ů' => 'u',
				'ž' => 'z',
				// Polish
				'Ą' => 'A', 'Ć' => 'C', 'Ę' => 'e', 'Ł' => 'L', 'Ń' => 'N', 'Ó' => 'o', 'Ś' => 'S', 'Ź' => 'Z',
				'Ż' => 'Z',
				'ą' => 'a', 'ć' => 'c', 'ę' => 'e', 'ł' => 'l', 'ń' => 'n', 'ó' => 'o', 'ś' => 's', 'ź' => 'z',
				'ż' => 'z',
				// Latvian
				'Ā' => 'A', 'Č' => 'C', 'Ē' => 'E', 'Ģ' => 'G', 'Ī' => 'i', 'Ķ' => 'k', 'Ļ' => 'L', 'Ņ' => 'N',
				'Š' => 'S', 'Ū' => 'u', 'Ž' => 'Z',
				'ā' => 'a', 'č' => 'c', 'ē' => 'e', 'ģ' => 'g', 'ī' => 'i', 'ķ' => 'k', 'ļ' => 'l', 'ņ' => 'n',
				'š' => 's', 'ū' => 'u', 'ž' => 'z'
		);
	
		// Make custom replacements
		$str = preg_replace(array_keys($options['replacements']), $options['replacements'], $str);
	
		// Transliterate characters to ASCII
		if ($options['transliterate']) {
			$str = str_replace(array_keys($char_map), $char_map, $str);
		}
	
		// Replace non-alphanumeric characters with our delimiter
		$str = preg_replace('/[^\p{L}\p{Nd}]+/u', $options['delimiter'], $str);
	
		// Remove duplicate delimiters
		$str = preg_replace('/(' . preg_quote($options['delimiter'], '/') . '){2,}/', '$1', $str);
	
		// Truncate slug to max. characters
		$str = mb_substr($str, 0, ($options['limit'] ? $options['limit'] : mb_strlen($str, 'UTF-8')), 'UTF-8');
	
		// Remove delimiter from ends
		$str = trim($str, $options['delimiter']);
	
		return $options['lowercase'] ? mb_strtolower($str, 'UTF-8') : $str;
	}
}

new wcff_dao();

?>