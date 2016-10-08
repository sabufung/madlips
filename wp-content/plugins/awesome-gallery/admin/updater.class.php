<?php
if (!class_exists('Karevn_Updater_2_0')){
	class Karevn_Updater_2_0{
		var $api_url;
		var $slug;
		var $basename;
		public function __construct($api_url, $slug, $basename){
			$this->api_url = $api_url;
			$this->slug = $slug;
			$this->basename = $basename;
			add_filter('pre_set_site_transient_update_plugins', array($this, 'check_for_updates'));
			// Take over the Plugin info screen
			add_filter('plugins_api', array($this, 'my_plugin_api_call'), 10, 3);
		}

		// Take over the update check
		public function check_for_updates($checked_data) {
			if (empty($checked_data->checked))
				return $checked_data;
			$response = wp_remote_get($this->get_plugin_info_url(), $this->get_remote_params());
			if (!is_wp_error($response) && !empty($response) && $response['response']['code'] == 200){
				$response = json_decode($response['body'], false);
				if (is_object($response) && !empty($response) &&
					version_compare($checked_data->checked[$this->basename], $response->version) == -1){
						// Feed the update data into WP updater
						$response->new_version = $response->version;
					$checked_data->response[$this->basename] = $response;
				}
			}
			return $checked_data;
		}

		public function my_plugin_api_call($def, $action, $args) {
			if ($action == 'query_plugins')
				return $def;
			if (!isset($args->slug) || $args->slug != $this->slug)
				return false;
			// Get the current version
			$response = wp_remote_get($this->get_plugin_info_url(), $this->get_remote_params());
			if (is_wp_error($response)) {
				$res = new WP_Error('plugins_api_failed', __('An Unexpected HTTP Error occurred during the API request.</p> <p><a href="?" onclick="document.location.reload(); return false;">Try again</a>'), $request->get_error_message());
			} else {
				$res = json_decode($response['body'], false);
				if ($res === false)
					$res = new WP_Error('plugins_api_failed', __('An unknown error occurred'), $response['body']);
			}
			return $res;
		}

		function get_plugin_info_url(){
			return trailingslashit($this->api_url) . $this->slug;
		}

		function get_remote_params(){
			global $wp_version;
			return array('timeout' => 15, 'user-agent' =>
				'WordPress/' . $wp_version . '; ' . get_bloginfo( 'url' ));
		}
	}
}
