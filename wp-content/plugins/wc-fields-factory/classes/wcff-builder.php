<?php 
/**
 * @author 		: Saravana Kumar K
 * @author url  : iamsark.com
 * @copyright	: sarkware.com
 * HTML generator module, which wil uses "wccpf_dao" module to get data and render HTML skeletons.
 *
 */

if ( ! defined( 'ABSPATH' ) ) { exit; }

class wcff_builder {
	
	function __construct() {
		add_filter( 'wcff/build/products/list', array( $this, 'build_wcff_products_list' ), 10, 2 );
		add_filter( 'wcff/build/products/cat/list', array( $this, 'build_wcff_products_cat_list' ), 10, 2 );		
		add_filter( 'wcff/build/products/tag/list', array( $this, 'build_wcff_products_tag_list' ), 10, 2 );
		add_filter( 'wcff/build/products/type/list', array( $this, 'build_wcff_products_type_list' ), 10, 2 );		
		add_filter( 'wcff/build/products/tabs/list', array( $this, 'build_wcff_products_tabs_list' ), 10, 2 );
		add_filter( 'wcff/build/metabox/context/list', array( $this, 'build_wcff_metabox_context_list' ), 10, 2 );
		add_filter( 'wcff/build/metabox/priority/list', array( $this, 'build_wcff_metabox_priority_list' ), 10, 2 );
		add_filter( 'wcff/build/fields', array( $this, 'build_wcff_fields' ) );
	}
	
	function build_wcff_products_list( $class, $active = "") {
		$html = '<select class="'. esc_attr( $class ) .' select">';
		$products = apply_filters( "wcff/load/products", array() );
		$html .= '<option value="-1">All Products</option>';
		
		if( count( $products ) > 0 ) {			
			foreach ( $products as $product ) {
				$selected = ( $product["id"] == $active ) ? 'selected="selected"' : '';
				$html .= '<option value="'. esc_attr( $product["id"] ) .'" '. $selected .'>'. esc_html( $product["title"] ) .'</option>';
			}			
		}
		
		$html .= '</select>';
		return $html;
	}
	
	function build_wcff_products_cat_list( $class, $active = "" ) {
		$html = '<select class="'. esc_attr( $class ) .' select">';
		$pcats = apply_filters( "wcff/load/products/cat", array() );
		$html .= '<option value="-1">All Categories</option>';
		
		if( count( $pcats ) > 0 ) {
			foreach ( $pcats as $pcat ) {
				$selected = ( $pcat["id"] == $active ) ? 'selected="selected"' : '';
				$html .= '<option value="'. esc_attr( $pcat["id"] ) .'" '. $selected .'>'. esc_html( $pcat["title"] ) .'</option>';
			}
		}

		$html .= '</select>';
		return $html;
	}
	
	function build_wcff_products_tag_list( $class, $active = "" ) {		
		$html = '<select class="'. esc_attr( $class ) .' select">';
		$ptags = apply_filters( "wcff/load/products/tag", array() );
		$html .= '<option value="-1">All Tags</option>';
		
		if( count( $ptags ) > 0 ) {
			foreach ( $ptags as $ptag ) {
				$selected = ( $ptag["id"] == $active ) ? 'selected="selected"' : '';
				$html .= '<option value="'. esc_attr( $ptag["id"] ) .'" '. $selected .'>'. esc_html( $ptag["title"] ) .'</option>';
			}
		}
		
		$html .= '</select>';
		return $html;
	}
	
	function build_wcff_products_type_list( $class, $active = "" ) {
		$html = '<select class="'. esc_attr( $class ) .' select">';
		$ptypes = apply_filters( "wcff/load/products/type", array() );
		$html .= '<option value="-1">All Types</option>';
	
		if( count( $ptypes ) > 0 ) {
			foreach ( $ptypes as $ptype ) {
				$selected = ( $ptype["id"] == $active ) ? 'selected="selected"' : '';
				$html .= '<option value="'. esc_attr( $ptype["id"] ) .'" '. $selected .'>'. esc_html( $ptype["title"] ) .'</option>';
			}
		}
	
		$html .= '</select>';
		return $html;
	}
	
	function build_wcff_products_tabs_list( $class, $active = "" ) {
		$html = '<select class="'. esc_attr( $class ) .' select">';
		$ptabs = apply_filters( "wcff/load/products/tabs", array() );	
		
		if( count( $ptabs ) > 0 ) {
			foreach ( $ptabs as $pttitle => $ptvalue ) {
				$selected = ( $ptvalue == $active ) ? 'selected="selected"' : '';
				$html .= '<option value="'. esc_attr( $ptvalue ) .'" '. $selected .'>'. esc_html( $pttitle ) .'</option>';
			}
		}
		
		$html .= '</select>';
		return $html;
	}
	
	function build_wcff_metabox_context_list( $class, $active = "" ) {
		$html = '<select class="'. esc_attr( $class ) .' select">';
		$mcontexts = apply_filters( "wcff/load/metabox/contexts", array() );		
	
		if( count( $mcontexts ) > 0 ) {
			foreach ( $mcontexts as $mckey => $mcvalue ) {
				$selected = ( $mckey == $active ) ? 'selected="selected"' : '';
				$html .= '<option value="'. esc_attr( $mckey ) .'" '. $selected .'>'. esc_html( $mcvalue ) .'</option>';
			}
		}
	
		$html .= '</select>';
		return $html;
	}
	
	function build_wcff_metabox_priority_list( $class, $active = "" ) {
		$html = '<select class="'. esc_attr( $class ) .' select">';
		$mpriorities = apply_filters( "wcff/load/metabox/priorities", array() );
	
		if( count( $mpriorities ) > 0 ) {
			foreach ( $mpriorities as $mpkey => $mpvalue ) {
				$selected = ( $mpkey == $active ) ? 'selected="selected"' : '';
				$html .= '<option value="'. esc_attr( $mpkey ) .'" '. $selected .'>'. esc_html( $mpvalue ) .'</option>';
			}
		}
	
		$html .= '</select>';
		return $html;
	}	
	
	function build_wcff_fields( $fields ) {
		$html = "";
		foreach ( $fields as $key => $field ) {				
			$html .= '<div class="wcff-meta-row" data-key="'. esc_attr( $key ) .'">
						<table class="wcff_table">
							<tbody>		
								<tr>
									<td class="wcff-sortable">
										<span class="wcff-field-order"></span>
									</td>
									<td>
										<label class="wcff-field-label">'. esc_html( $field["label"] ) .'</label>
										<div class="wcff-meta-option">
											<a href="#" data-key="'. esc_attr( $key ) .'" class="wcff-meta-option-edit">Edit</a> | 
											<a href="#" data-key="'. esc_attr( $key ) .'" class="wcff-meta-option-delete">Delete</a>
										</div>
									</td>
									<td>
										<label class="wcff-field-name">'. $field["name"] .'</label>
									</td>
									<td>
										<label class="wcff-field-type">'. $field["type"] .'</label>
									</td>
								</tr>
							</tbody>
						</table>
						<input type="hidden" name="'. esc_attr( $key ) .'_order" class="wcff-field-order-index" value="'. $field["order"] .'" />
					</div>';
		}
	 	return $html;		
	}
	
}

new wcff_builder();

?>