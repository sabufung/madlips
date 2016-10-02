<?php
/**
 * Portfolio Post Type
 *
 * @package   Closify_Post_Type
 * @author    Abdulrhman Elbuni
 * @license   GPL-2.0+
 * @copyright 2013-2014
 */

/**
 * Register closify types and taxonomies.
 *
 * @package Closify_Post_Type
 */
class Closify_Post_Type_Registrations {

	public $post_type = CLOSIFY_POST_TYPE;

	public $taxonomies;

	public function init() {
		// Add the portfolio post type and taxonomies
		add_action( 'init', array( $this, 'register' ) );
        $this->taxonomies = array( CLOSIFY_POST_TYPE.'_category', CLOSIFY_POST_TYPE.'_tag' );
	}

	/**
	 * Initiate registrations of post type and taxonomies.
	 *
	 * @uses Portfolio_Post_Type_Registrations::register_post_type()
	 * @uses Portfolio_Post_Type_Registrations::register_taxonomy_tag()
	 * @uses Portfolio_Post_Type_Registrations::register_taxonomy_category()
	 */
	public function register() {
		$this->register_post_type();
		$this->register_taxonomy_category();
		//$this->register_taxonomy_tag();
	}

	/**
	 * Register the custom post type.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_post_type
	 */
	protected function register_post_type() {
		$labels = array(
			'name'               => __( 'Closify', 'closify-post-type' ),
			'singular_name'      => __( 'Closify Uploader', 'closify-post-type' ),
			'add_new'            => __( 'Add New Uploader', 'closify-post-type' ),
			'add_new_item'       => __( 'Add New Closify Uploader', 'closify-post-type' ),
			'edit_item'          => __( 'Edit Closify Uploader', 'closify-post-type' ),
			'new_item'           => __( 'Add New Closify Uploader', 'closify-post-type' ),
			'view_item'          => __( 'View Uploader', 'closify-post-type' ),
			'search_items'       => __( 'Search Closify', 'closify-post-type' ),
			'not_found'          => __( 'No Closify uploader found', 'closify-post-type' ),
			'not_found_in_trash' => __( 'No Closify items uploader in trash', 'closify-post-type' ),
		);

		$supports = array(
			'title',
//	'editor',
//	'excerpt',
//			'thumbnail',
//			'comments',
			'author',
//		'custom-fields',
//			'revisions',
		);

		$args = array(
			'labels'          => $labels,
			'supports'        => $supports,
			'public'          => true,
			'capability_type' => 'post',
			'rewrite'         => array( 'slug' => 'closify', ), // Permalinks format
			'menu_position'   => 5,
			'menu_icon'       => ( version_compare( $GLOBALS['wp_version'], '3.8', '>=' ) ) ? 'dashicons-format-gallery' : '',
			'has_archive'     => true,
		);

		$args = apply_filters( 'closifyposttype_args', $args );

		register_post_type( $this->post_type, $args );
	}

	/**
	 * Register a taxonomy for Closify Categories.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	protected function register_taxonomy_category() {
		$labels = array(
			'name'                       => __( 'Closify Categories', 'closify-post-type' ),
			'singular_name'              => __( 'Closify Category', 'closify-post-type' ),
			'menu_name'                  => __( 'Closify Categories', 'closify-post-type' ),
			'edit_item'                  => __( 'Edit Closify Category', 'closify-post-type' ),
			'update_item'                => __( 'Update Closify Category', 'closify-post-type' ),
			'add_new_item'               => __( 'Add New Closify Category', 'closify-post-type' ),
			'new_item_name'              => __( 'New Closify Category Name', 'closify-post-type' ),
			'parent_item'                => __( 'Parent Closify Category', 'closify-post-type' ),
			'parent_item_colon'          => __( 'Parent Closify Category:', 'closify-post-type' ),
			'all_items'                  => __( 'All Closify Categories', 'closify-post-type' ),
			'search_items'               => __( 'Search Closify Categories', 'closify-post-type' ),
			'popular_items'              => __( 'Popular Closify Categories', 'closify-post-type' ),
			'separate_items_with_commas' => __( 'Separate closify categories with commas', 'closify-post-type' ),
			'add_or_remove_items'        => __( 'Add or remove closify categories', 'closify-post-type' ),
			'choose_from_most_used'      => __( 'Choose from the most used closify categories', 'closify-post-type' ),
			'not_found'                  => __( 'No closify categories found.', 'closify-post-type' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => true,
			'rewrite'           => array( 'slug' => 'closify_category' ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		$args = apply_filters( 'closifyposttype_category_args', $args );

		register_taxonomy( $this->taxonomies[0], $this->post_type, $args );
	}

	/**
	 * Register a taxonomy for Closify Tags.
	 *
	 * @link http://codex.wordpress.org/Function_Reference/register_taxonomy
	 */
	protected function register_taxonomy_tag() {
		$labels = array(
			'name'                       => __( 'Closify Tags', 'closify-post-type' ),
			'singular_name'              => __( 'Closify Tag', 'closify-post-type' ),
			'menu_name'                  => __( 'Closify Tags', 'closify-post-type' ),
			'edit_item'                  => __( 'Edit Closify Tag', 'closify-post-type' ),
			'update_item'                => __( 'Update Closify Tag', 'closify-post-type' ),
			'add_new_item'               => __( 'Add New Closify Tag', 'closify-post-type' ),
			'new_item_name'              => __( 'New Closify Tag Name', 'closify-post-type' ),
			'parent_item'                => __( 'Parent Closify Tag', 'closify-post-type' ),
			'parent_item_colon'          => __( 'Parent Closify Tag:', 'closify-post-type' ),
			'all_items'                  => __( 'All Closify Tags', 'closify-post-type' ),
			'search_items'               => __( 'Search Closify Tags', 'closify-post-type' ),
			'popular_items'              => __( 'Popular Closify Tags', 'closify-post-type' ),
			'separate_items_with_commas' => __( 'Separate closify tags with commas', 'closify-post-type' ),
			'add_or_remove_items'        => __( 'Add or remove closify tags', 'closify-post-type' ),
			'choose_from_most_used'      => __( 'Choose from the most used closify tags', 'closify-post-type' ),
			'not_found'                  => __( 'No closify tags found.', 'closify-post-type' ),
		);

		$args = array(
			'labels'            => $labels,
			'public'            => true,
			'show_in_nav_menus' => true,
			'show_ui'           => true,
			'show_tagcloud'     => true,
			'hierarchical'      => false,
			'rewrite'           => array( 'slug' => CLOSIFY_POST_TYPE.'_tag' ),
			'show_admin_column' => true,
			'query_var'         => true,
		);

		$args = apply_filters( 'closifyposttype_tag_args', $args );

		register_taxonomy( $this->taxonomies[1], $this->post_type, $args );

	}
}
