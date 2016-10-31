<?php
/*
 * This file is part of facebook-page-albums.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * DB Manager
 *
 * @package     facebook-page-albums
 */
class FacebookPageAlbumsDBManager {
	protected $api_config = 'facebook_page_albums_api_config';


	/**
	 * Constructor
	 */
	public function __construct() {
	}


	/**
	 * Execute when this plugin is activated
	 */
	public function initialize() {
	}


	/**
	 * Execute when this plugin is deactivated
	 */
	public function destroy() {
		delete_option( $this->api_config );
	}


	/**
	 * get api option
	 */
	public function get_api_option() {
		$options = get_option( $this->api_config );

		return wp_parse_args( $options, array(
			'appId'     => '',
			'secret'  => '',
			'pageId'  => ''
			) );
	}


	/**
	 * set api option
	 *
	 * @param Array  $args {
	 *   @type String appId
	 *   @type String secret
	 *   @type String pageId
	 * }
	 * @return Array
	 */
	public function set_api_option( $args=array() ) {
		$defaults = array(
			'appId'     => '',
			'secret'  => '',
			'pageId'  => ''
		);
		$args = wp_parse_args( $args, $defaults );
		return update_option( $this->api_config, $args );
	}
}