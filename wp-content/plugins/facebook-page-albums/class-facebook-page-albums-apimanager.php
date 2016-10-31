<?php
/*
 * This file is part of facebook-page-albums.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

// Load Facebook SDK
define('FACEBOOK_SDK_V4_SRC_DIR', FACEBOOK_PAGE_ALBUMS_DIR . '/lib/facebook-php-sdk-v4/src/Facebook/');
require_once( FACEBOOK_PAGE_ALBUMS_DIR . '/lib/facebook-php-sdk-v4/autoload.php' );
require_once( FACEBOOK_PAGE_ALBUMS_DIR . '/class-facebook-page-albums-dbmanager.php' );
use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\FacebookRequestException;
use Facebook\FacebookResponse;

/**
 * API Manager
 *
 * @package     facebook-page-albums
 */
class FacebookPageAlbumsAPIManager {
	private $session = null;
	private $page_id = null;
	public $error = array();

	/** @var FacebookPageAlbumsDBManager $db */
	private $db = null;


	/**
	 * Constructor
	 */
	public function __construct() {
		$this->init();
	}


	/**
	 * Create api instance.
	 */
	protected function init() {
		//
		// Get Config
		//
		if (empty($this->db)) {
			//@see class-facebook-page-albums-dbmanager.php
			$this->db = new FacebookPageAlbumsDBManager();
		}
		$config = $this->db->get_api_option();
		if (empty($config['appId']) || empty($config['secret'])) {
			return false;
		}
		$this->page_id = $config['pageId'];


		//
		// Get Session for Facebook usin SDK
		// @see https://developers.facebook.com/docs/php/gettingstarted/4.0.0
		//
		FacebookSession::setDefaultApplication($config['appId'], $config['secret']);
		$this->session = FacebookSession::newAppSession();

		return true;
	}


	/**
	 * Get data by using facebook graph api.
	 *
	 * @param  String $query  Graph API Query
	 * @param  String|Array $params Parameter
	 * @return Object
	 */
	public function get($query=null, $params=array()) {
		if (empty($query) ||
			empty($this->session)) {
			return false;
		}

		//
		// Build query string
		//
		$slug = $query;
		if (!empty($params)) {
			if (is_array($params)) {
				$params = implode('&', $params);
			}
			$slug .= '?' . $params;
		}


		//
		// Send query through Facebook PHP SDK
		//
		try {
			$response = (new FacebookRequest($this->session, 'GET', $slug))->execute();
			$results = $response->getResponse();
		} catch (FacebookRequestException $ex) {
			$this->error = $ex->getMessage();
			$results = false;
			error_log($ex);
		} catch (\Exception $ex) {
			$this->error = $ex->getMessage();
			$results = false;
			error_log($ex);
		}

		/** @var FacebookResponse $results*/
		return $results;
	}


	/**
	 * Get Album list of Facebook Page
	 *
	 * @see  https://developers.facebook.com/docs/reference/api/album/
	 * @param  array  $args    Arguments.
	 * @return array
	 */
	public function get_albums($args=array()) {
		$args = wp_parse_args($args, array(
			'cover_photo' => false,
			'profile' => false,
			'fields' => array(
				'id',
				'name',
				'link',
				'cover_photo',
				'privacy',
				'count',
				'type',
				'created_time',
				'updated_time',
				'can_upload',
				'likes.limit(1).summary(true)',
				'comments.limit(1).summary(true)',
			),
			'after' => null,
			'before' => null,
			'per_page' => 25,
			'paged'    => 1
		));


		//
		// Build page parameters
		//
		$params = array();
		if (!empty($args['per_page'])) {
			$params[] = 'limit=' . $args['per_page'];
			if (!empty($args['paged'])) {
				$params[] = 'offset=' . ($args['paged'] - 1) * $args['per_page'];
			}
		}
		// Fields
		if (!empty($args['fields'])) {
			$params[] = 'fields=' . implode(',', $args['fields']);
		}
		// After
		if (!empty($args['after'])) {
			$params[] = 'after=' . $args['after'];
		}
		// Previous
		if (!empty($args['before'])) {
			$params[] = 'before=' . $args['before'];
		}


		//
		// Get Request
		//
		if (!$albums = $this->get('/' . $this->page_id . '/albums', $params)) {
			return false;
		}


		//
		// Loop
		//
		$data = array();
		foreach ($albums->data as $item) {
			$item = $this->get_album_data($item);

			// Cover Photo Album
			if ($item['type'] == 'cover' && empty($args['cover_photo'])) {
				continue;
			}
			// Profile Album
			if ($item['type'] == 'profile' && empty($args['profile'])) {
				continue;
			}

			$data[] = $item;
		}

		return array(
			'data' => $data,
			'paging' => isset($albums->paging) ? $albums->paging : false
		);
	}


	/**
	 * Convert Data
	 *
	 * @param  Object  $item
	 * @return Array
	 */
	protected function get_album_data($item) {
		$item = (array) $item;

		// Counts
		$item['likes'] = empty($item['likes']->summary->total_count) ? 0 : $item['likes']->summary->total_count;
		$item['comments'] = empty($item['comments']->summary->total_count) ? 0 : $item['comments']->summary->total_count;

		// Get Cover Photo Data through Facebook API
		if ($cover_id = $item['cover_photo']) {
			if ($thumb = $this->get('/' . $cover_id, array(
				'fields=link,picture,source,height,width'
			))) {
				$item['cover_photo_data'] = (array) $thumb;
			}
		}

		return $item;
	}


	/**
	 * Get Album list of Facebook Page
	 *
	 * @param  String  $album_id
	 * @return array
	 */
	public function get_album($album_id) {
		$fields = array(
			'id',
			'name',
			'link',
			'cover_photo',
			'privacy',
			'count',
			'type',
			'created_time',
			'updated_time',
			'can_upload',
			'likes.limit(1).summary(true)',
			'comments.limit(1).summary(true)',
		);

		if (!$data = $this->get( '/' . $album_id,  array(
			'fields=' . implode(',', $fields)
		))) {
			return false;
		}

		return $this->get_album_data($data);
	}


	/**
	 * Get photos
	 *
	 * @param array   $args     limit and offset
	 * @return array
	 */
	public function get_photos($args=null) {
		$defaults = array(
			'album_id' => null,
			'fields' => array(
				'id',
				'height',
				'width',
				'images',
				'link',
				'picture',
				'source',
				'created_time',
				'updated_time',
				'likes.limit(1).summary(true)',
				'comments.limit(1).summary(true)',
			),
			'per_page' => 25,
			'paged'    => 1
		);
		$args = wp_parse_args($args, $defaults);

		if (empty($args['album_id'])) return false;


		//
		// Build pagination parameters
		//
		$params = array();
		if (!empty($args['per_page'])) {
			$params[] = 'limit=' . $args['per_page'];
			if (!empty($args['paged'])) {
				$params[] = 'offset=' . ($args['paged'] - 1) * $args['per_page'];
			}
		}
		// Fields
		if (!empty($args['fields'])) {
			$params[] = 'fields=' . implode(',', $args['fields']);
		}

		// Send
		$photos = $this->get('/' . $args['album_id'] . '/photos', $params);


		//
		// Loop
		//
		$data = array();
		foreach ($photos->data as $item) {
			$item = (array) $item;

			// Counts
			$item['likes'] = empty($item['likes']->summary->total_count) ? 0 : $item['likes']->summary->total_count;
			$item['comments'] = empty($item['comments']->summary->total_count) ? 0 : $item['comments']->summary->total_count;

			$data[] = $item;
		}

		return $data;
	}
}
