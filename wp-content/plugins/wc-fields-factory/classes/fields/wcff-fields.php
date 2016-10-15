<?php 
/**
 * @author 		: Saravana Kumar K
 * @copyright	: sarkware.com
 * Base class for all field classes, Provides common properties and hooks for fields related operations
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_field {
	
	var $label, 
	$name,	
	$required,
	$valid,
	$message,
	$params;	

	function __construct() {
		add_filter( 'wcff/render/setup/fields/type='.$this->name, array($this, 'render_wcff_setup_fields') );
		add_filter( 'wcff/render/product/field/type='.$this->name, array($this, 'render_product_field') );
		add_filter( 'wcff/render/admin/field/type='.$this->name, array($this, 'render_admin_field') );
		add_filter( 'wcff/get/params/type='.$this->name, array($this, 'get_params') );
		add_filter( 'wcff/validate/type='.$this->name, array( $this, 'validate' ) );
	}
	
	function add_filter( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		if( is_callable( $function_to_add ) ) {
			add_filter( $tag, $function_to_add, $priority, $accepted_args );
		}
	}
	
	function add_action( $tag, $function_to_add, $priority = 10, $accepted_args = 1 ) {
		if( is_callable( $function_to_add ) ) {
			add_action( $tag, $function_to_add, $priority, $accepted_args );
		}
	}	

	function get_params() {
		return $this->params;
	}
	
}

?>