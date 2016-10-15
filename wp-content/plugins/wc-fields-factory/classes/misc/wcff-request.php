<?php 
/**
 * @author 		: Saravana Kumar K
 * @copyright	: sarkware.com
 * @todo		: Wrapper module for all wccpf related Ajax request.
 * 				  All Ajax request target for wccpf will be converted to "wcff_request" object and
 * 				  made available to the context through "wcff()->request".
 * 
 */
if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_request {
	
	function __construct() {
		add_filter( 'wcff/request', array( $this, 'prepare_request' ) );
	}
	
	function prepare_request() {
		if( isset( $_REQUEST["wcff_param"] ) ) {	
			$payload = json_decode( str_replace('\"','"', $_REQUEST["wcff_param"] ), true );			
			return array (				
				"method" 	=> $payload["request"],
				"context" 	=> $payload["context"],
				"post" 		=> $payload["post"],
				"post_type" => $payload["post_type"],
				"payload" 	=> $payload["payload"]
			);
		}
	}
	
}

new wcff_request();

?>