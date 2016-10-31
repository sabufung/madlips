<?php
/*
 Plugin Name: Facebook Page Albums
 Plugin URI: http://wordpress.org/extend/plugins/facebook-page-albums/
 Description: Get the all albums/photos from your Facebook Page.
 Version: 3.0.0
 Author: Daiki Suganuma
 Author URI: http://se-suganuma.blogspot.com/
 */

/**
 *  Copyright 2015 Daiki Suganuma  (email : daiki.suganuma@gmail.com)
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may
 * not use this file except in compliance with the License. You may obtain
 * a copy of the License at
 *
 *     http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations
 * under the License.
 */

/***** Define Part *****/
define('FACEBOOK_PAGE_ALBUMS_DIR', dirname(__FILE__));
define('FACEBOOK_PAGE_ALBUMS_CACHE_GROUP', 'facebook_page_albums');
define('FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT', 60 * 60 ); //60 minutes

if ( is_admin() ) {
	require_once( FACEBOOK_PAGE_ALBUMS_DIR . '/facebook-page-albums-admin.php' );
}


/**
 * Main Class
 *
 * @package     facebook-page-albums
 */
class FacebookPageAlbums {
	public $paging = null;

	/** @var FacebookPageAlbumsAPIManager $api */
	private $api = null;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->load_api();
	}


	/**
	 * Generate API Instance
	 */
	protected function load_api() {
		if ( empty($this->api) ) {
			require_once( FACEBOOK_PAGE_ALBUMS_DIR . '/class-facebook-page-albums-apimanager.php' );
			$this->api = new FacebookPageAlbumsAPIManager();
		}
	}


	/**
	 * Get Album List
	 *
	 * @param Array $args {
	 *   @type Integer page_id
	 *   @type Boolean cover_photo
	 *   @type Integer per_page
	 *   @type String after
	 *   @type String previous
	 * }
	 * @return Array
	 */
	public function get_album_list( $args=array() ) {
		return $this->api->get_albums( $args );
	}


	/**
	 * Get Information of album
	 *
	 * @param Integer $album_id
	 * @return Array|Boolean
	 */
	public function get_album_info( $album_id ) {
		return $this->api->get_album( $album_id );
	}


	/**
	 * Get
	 *
	 * @param Array $args {
	 *   @type String url
	 * }
	 * @return Array|Boolean
	 */
	public function get_paging_params( $args=array() ) {
		$args = wp_parse_args($args, array(
			'url' => false // "false" means use "$_SERVER['REQUEST_URI']" in add_query_arg
		));

		$result = array();

		// Previous
		if ($next = $this->parsing_paging_url( 'previous', $args )) {
			$result['previous'] = add_query_arg($next, $args['url']);
		}

		// Next
		if ($next = $this->parsing_paging_url( 'next', $args )) {
			$result['next'] = add_query_arg($next, $args['url']);
		}

		return $result;
	}


	/**
	 * Parse
	 *
	 * @param String $slug
	 * @param Array $args
	 * @return Array|Boolean
	 */
	protected function parsing_paging_url( $slug, $args=array() ) {
		$args = wp_parse_args($args, array(
			'access_token',
			'fields'
		));

		if (!isset($this->paging->{$slug})) {
			return false;
		}

		if (!$query = parse_url(urldecode($this->paging->{$slug}), PHP_URL_QUERY)) {
			return false;
		}

		parse_str($query, $params);

		// Remove
		foreach ($args as $item) {
			if (isset($params[$item])) {
				unset($params[$item]);
			}
		}

		return $params;
	}


	/**
	 * Photo List
	 *
	 * @param Array $args {
	 *   @type Integer page_id
	 *   @type Boolean cover_photo
	 *   @type Integer per_page
	 *   @type Integer paged
	 * }
	 * @return Array|Boolean
	 */
	public function get_photo_list( $args=array() ) {
		return $this->api->get_photos( $args );
	}
}


/**
 * Get album list.
 *
 * @param Array $args
 * @return Array
 */
function facebook_page_albums_get_album_list( $args=array() ) {
	/** @var FacebookPageAlbums $facebook_page_albums */
	global $facebook_page_albums;

	if ( empty($facebook_page_albums) ) {
		$facebook_page_albums = new FacebookPageAlbums();
	}

	// Get Object Cache
	$cache_name = 'album_list' . implode('', $args);
	$result = wp_cache_get( $cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP );

	if ( empty($result) ) {
		// Get from Facebook API
		$result = $facebook_page_albums->get_album_list( $args );
		if ( !empty($result) ) {
			// Save Object Cache
			wp_cache_set( $cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT );
		}
	}

	// Paging
	if (!empty($result['paging'])) {
		$facebook_page_albums->paging = $result['paging'];
	}

	return empty($result['data']) ? false : $result['data'];
}


/**
 * Get Paging
 *
 * @param Array $args {
 *   @type String url
 * }
 * @return Array
 */
function facebook_page_albums_get_paging($args=array()) {
	/** @var FacebookPageAlbums $facebook_page_albums */
	global $facebook_page_albums;

	if (!$facebook_page_albums) {
		return false;
	}

	return $facebook_page_albums->get_paging_params($args);
}


/**
 * Get album information.
 *
 * @param  Integer $album_id
 * @return Array
 */
function facebook_page_albums_get_album( $album_id ) {
	/** @var FacebookPageAlbums $facebook_page_albums */
	global $facebook_page_albums;

	if ( empty($facebook_page_albums) ) {
		$facebook_page_albums = new FacebookPageAlbums();
	}

	// Get Object Cache
	$cache_name = 'album_info' . $album_id;
	$result = wp_cache_get( $cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP );

	if ( empty($result) ) {
		// Get from Facebook API
		$result = $facebook_page_albums->get_album_info( $album_id );
		if ( !empty($result) ) {
			// Save Object Cache
			wp_cache_set( $cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT );
		}
	}

	return $result;
}


/**
 * Get photo list.
 *
 * @param  integer $album_id album id
 * @param  array $args arguments
 * @return array             photo list
 */
function facebook_page_albums_get_photo_list( $album_id, $args=array() ) {
	/** @var FacebookPageAlbums $facebook_page_albums */
	global $facebook_page_albums;

	if ( empty($facebook_page_albums) ) {
		$facebook_page_albums = new FacebookPageAlbums();
	}

	// Get Object Cache
	$cache_name = 'photo_list' . $album_id . implode('', $args);
	$result = wp_cache_get( $cache_name, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP );

	if ( empty($result) ) {
		// Get from Facebook API
		$args['album_id'] = $album_id;
		$result = $facebook_page_albums->get_photo_list( $args );
		if ( !empty($result) ) {
			// Save Object Cache
			wp_cache_set( $cache_name, $result, FACEBOOK_PAGE_ALBUMS_CACHE_GROUP, FACEBOOK_PAGE_ALBUMS_CACHE_TIMEOUT );
		}
	}

	return $result;
}


//
// Debug Functions
//
if ( !function_exists('alog') ) {
	/**
	 * Describe variable as HTML Table
	 */
	function alog() {
		if ( !WP_DEBUG ) {return;}

		if ( !class_exists('dBug') ) {
			require_once (FACEBOOK_PAGE_ALBUMS_DIR . '/lib/dBug.php');
		}
		foreach ( func_get_args() as $v ) new dBug($v);
	}
}

if ( !function_exists('dlog') ) {
	/**
	 * Dump variable
	 */
	function dlog() {
		if ( !WP_DEBUG ) {return;}

		if ( !class_exists('dBug') ) {
			require_once (FACEBOOK_PAGE_ALBUMS_DIR . '/lib/dBug.php');
		}

		// buffering
		ob_start();
		foreach ( func_get_args() as $v ) new dBug($v);
		$html = ob_get_contents();
		ob_end_clean();

		// write down to html file.
		$html .= '<br/><br/>';
		$upload_dir = wp_upload_dir();
		$file = $upload_dir['basedir'] . '/debug.html';
		if ($handle = fopen($file, 'a')) {
			@chmod($file, 0777);
			fwrite($handle, $html);
			fclose($handle);
		}
	}
}
