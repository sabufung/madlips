<?php
require_once(ABSPATH . WPINC . '/feed.php');

class ASG_RSS_Source extends ASG_Http_Source {
	function __construct($options) {
		$this->slug = 'rss';
		$this->name = 'RSS';
		parent::__construct($options);
	}

	function fetch_raw_images($page, $per_page, $state, $options){
		$feed = fetch_feed(explode(',', $this->source['url']));
		if (is_wp_error($feed))
			return array($feed, null);
		$items = $feed->get_items(($page - 1) * $per_page, $page * $per_page);
		return array($items, null);
	}

	function _hack(){
		return 1;
	}

	function get_permalink($item) {
		return $item->get_permalink();
	}

	function get_lightbox_url($item, $options) {
		return $this->get_image_url($item);
	}

	function get_slug($data, $options = array()){
		return $data->get_id();
	}

	function get_caption($name, $item) {
		$source = $this->source[$name];
		if (!$source)
			return null;
		switch ($source) {
			case 'title':
				return stripslashes($item->get_title());
			case 'author':
				$author = $item->get_author();
				if ($author)
					return $author->name;
				return '';
			case 'tags':
				return implode(', ', $this->get_tags($item));
			case 'description':
				return $this->sanitize_html($item->get_description());
			case 'excerpt':
				return $this->truncate($this->sanitize_html($item->get_description), 200);
			default:
				return null;
		};
	}

	function get_tags($item) {
		$categories = $item->get_categories();
		$tags = array();
		if (count($categories))
			foreach ($categories as $cat) {
				if (trim($cat->term))
					$tags [] = trim($cat->term);
			}
		return $tags;
	}

	function get_image_url($item) {
		$url = $this->find_image_url($item);
		if (!$url){
			return $url;
			echo 'no image';
		}

		$parsed = parse_url($url);
		if (empty($parsed['host'])) {
			$feed_url = parse_url($item->get_feed()->subscribe_url());
			$url = "{$feed_url['scheme']}://{$feed_url['host']}{$url}";
		}
		return $url;
	}

	function find_image_url($item) {
		$links = $item->get_enclosures();
		if ($links)
			foreach ($links as $link) {
				$link = $link->get_link();
				if (!trim($link))
					continue;
				if (preg_match('/\.gravatar\.com\/.+/', $link)/* ||
					!preg_match('/\.(jpg|jpeg|png|gif)$/', $link)*/)
					continue;
				return $link;
			}
		$thumbnail = $item->get_item_tags(SIMPLEPIE_NAMESPACE_MEDIARSS, 'thumbnail');
		if (isset($thumbnail[0]['attribs']['']['url']) && trim($thumbnail[0]['attribs']['']['url']))
			return isset($thumbnail[0]['attribs']['']['url']);
		$text = html_entity_decode($item->get_content(), ENT_QUOTES, 'UTF-8');
		preg_match("/<img[^>]+\>/i", $text, $matches);
		if (count($matches)) {
			preg_match('/src=[\'"]?([^\'" >]+)[\'" >]/', $matches[0], $link);
			return urldecode($link[1]);
		}
		return null;
	}

}

global $asg_sources;
$asg_sources['rss'] = 'ASG_RSS_Source';
