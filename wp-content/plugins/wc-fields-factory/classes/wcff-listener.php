<?php 
/**
 * @author		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com  
 * Wcff core Ajax handler. common hub for all ajax related actions of wcff
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_ajax {
	
	function __construct() {		
		add_action("wp_ajax_wcff_ajax", array( $this, "listen" ) );		
	}
	
	/**
	 * Primary listener
	 * Origin for all wcff related Ajax requests
	 * Mostly comes from wp-admin wcff related screens
	 * All ajax request will have the following properties  
	 * @param 	wcff()->request = {	 * 				
					method		: Could be one of GET, POST, UPDATE or DELETE
					context		: Context of the operation which it belongs. could be Product, Product Cat, Fields, Meta ... 
					post		: ID of the current post
					post_type	: CUrrent post type
					payload		: Data sent by the Client. mostly JSON
	 * 			}
	 * All ajax response will have the following properties  
	 * @param	wcff()->response = {
	 * 				status		: Status of the last operation - either TRUE or FALSE
	 * 				message		: Few words about the last operation, any status message ...  
	 * 				data		: The result of the last operation - could be json, html ...
	 * 			}
	 */
	function listen() {
		/* Parse the incoming request */
		wcff()->request = apply_filters( 'wcff/request', array() );		
		/* Handle the request */
		$this->handleRequest();
		/* Respond the request */
		echo wcff()->response;
		/* end the request - response cycle */
		die();
	}
	
	/**
	 * Called from listen method
	 * Primary handler for all wcff related Ajax request
	 * It drilled down the wcff()->request object and determine what operation has been requested by client
	 * Perform that operation and stores the rersponse on wcff()->response object
	 */
	function handleRequest() {
		
		$data = array();
		$fields = array();
		$status = true;
		$message = "Success";
		
		do_action( 'wcff/update/post/type', wcff()->request["post_type"] );		
				
		if( wcff()->request["context"] == "product" ) {		
			/* Request arrived for Product List */
			if( wcff()->request["method"] == "GET" ) {				
				$data = apply_filters( 'wcff/build/products/list', "wcff_condition_value select" );								
			}			
		} else if( wcff()->request["context"] == "product_cat" ) {
			/* Request arrived for Product Cat List */
			if( wcff()->request["method"] == "GET" ) {				
				$data = apply_filters( 'wcff/build/products/cat/list', "wcff_condition_value select" );				
			}
		} else if( wcff()->request["context"] == "product_tag" ) {			
			if( wcff()->request["method"] == "GET" ) {				
				$data = apply_filters( 'wcff/build/products/tag/list', "wcff_condition_value select" );				
			}
		} else if( wcff()->request["context"] == "product_type" ) {			
			if( wcff()->request["method"] == "GET" ) {
				$data = apply_filters( 'wcff/build/products/type/list', "wcff_condition_value select" );
			}
		} else if( wcff()->request["context"] == "location_product" ||  wcff()->request["context"] == "location_product_cat"  ){
			/* Request arrived for Metabox Context & Priority List */
			if( wcff()->request["method"] == "GET" ) {				
				$data = apply_filters( 'wcff/build/metabox/context/list', "wcff_location_metabox_context_value select" );
				$data .= apply_filters( 'wcff/build/metabox/priority/list', "wcff_location_metabox_priorities_value select" );				
			}
		} else if( wcff()->request["context"] == "location_product_data" ){
			/* Request arrived for Product Tab List */
			if( wcff()->request["method"] == "GET" ) {
				$data = apply_filters( 'wcff/build/products/tabs/list', "wcff_location_product_data_value select" );
			}
		} else if( wcff()->request["context"] == "wcff_meta_fields" ) {
			/* Request arrived for Meta Fields for one of a wcff field */
			if( wcff()->request["method"] == "GET" ) {
				$data = apply_filters( 'wcff/render/setup/fields/type='.wcff()->request["payload"]["type"], wcff()->request["post_type"] );				
			}
		} else if( wcff()->request["context"] == "wcff_field_single" ) {
			
		} else {
			if( wcff()->request["method"] == "GET" ) {				
				$data = apply_filters( 'wcff/load/field', wcff()->request["post"], wcff()->request["payload"]["field_key"] );
				if( !$data ) {					
					$data = array();
					$message = "Failed to load wcff meta";
				}
			} else if( wcff()->request["method"] == "POST" ) {							
				$res = apply_filters( 'wcff/save/field', wcff()->request["post"], wcff()->request["payload"] );				
				if( $res ) {
					$message = "Successfully Inserted";
					$fields = apply_filters( 'wcff/load/fields', wcff()->request["post"] );
					$data = apply_filters( 'wcff/build/fields', $fields );
				} else {
					$status = false;
					$message = "Failed to create custom field";
				}				
			} else if( wcff()->request["method"] == "PUT" ) {				
				$res = apply_filters( 'wcff/update/field', wcff()->request["post"], wcff()->request["payload"] );
				if( $res ) {
					$message = "Successfully Updated";
					$fields = apply_filters( 'wcff/load/fields', wcff()->request["post"] );
					$data = apply_filters( 'wcff/build/fields', $fields );
				} else {
					$status = false;
					$message = "Failed to update the custom field";
				}				
			} else {			
				$res = apply_filters( 'wcff/remove/field', wcff()->request["post"], wcff()->request["payload"]["field_key"] );
				if( $res ) {
					$message = "Successfully removed";
					$fields = apply_filters( 'wcff/load/fields', wcff()->request["post"] );
					$data = apply_filters( 'wcff/build/fields', $fields );
				} else {
					$status = false;					
					$message = "Failed to remove the custom field";
				}				
			}
		}
		/* Store Status, Message and Data, which will be flushed out to client later */
		wcff()->response = apply_filters( 'wcff/response', $status, $message, $data );
		
	}
	
}

/* Init wcff ajax object */
new wcff_ajax();

?>