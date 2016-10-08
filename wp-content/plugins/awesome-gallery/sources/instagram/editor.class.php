<?php
class ASG_Instagram_Source_Editor extends ASG_Source_Editor {
	var $slug = 'instagram';
	var $name = 'Instagram';

	function __construct(){
		add_action('wp_ajax_asg_instagram_auth', array($this, '_instagram_auth'));
		add_action('wp_ajax_asg_instagram_ping', array($this, '_ping'));
		add_action('wp_ajax_asg_instagram_data_check', array($this, '_data_check'));
		add_action('admin_action_asg_instagram_save_client_data', array($this, '_save_client_data'));
		add_action('admin_menu', array($this, '_admin_menu'));
	}


	function _admin_menu(){
		add_submenu_page(null, __('Instagram Authorization', 'asg'), null, 'install_plugins', 'asg-instagram-auth-success', array($this, '_instagram_auth_success'));
		add_submenu_page(null, __('Instagram Authorization', 'asg'), null, 'install_plugins', 'asg-instagram-auth-failure', array($this, '_instagram_auth_failure'));
	}

	function _instagram_auth_success(){
		require(dirname(__FILE__) . '/auth-success.php');
	}

	function _instagram_auth_failure(){
		require(dirname(__FILE__) . '/auth-failure.php');
	}

	function _save_client_data(){
		session_start();
		$_SESSION['asg_instagram_client_data'] = stripslashes_deep($_GET);
		wp_redirect((stripslashes($_REQUEST['redirect'])));
		exit;
	}

	function _data_check(){
		$source = new ASG_Instagram_Source(stripslashes_deep($_REQUEST['data']));
		$images = $source->fetch_raw_images(1, 10);
		if (is_wp_error($images)){
			_e('Error', 'asg');
			echo $images->get_error_message();
		} else {
			if (isset($images) && count($images)){
				_e('Data is available.', 'asg');
			} else{
				_e('No data available. Please check your authorization', 'asg');
			}
		}
		exit;
	}

	function get_redirect_url($id){
		return admin_url('admin-ajax.php?action=asg_instagram_auth&gallery=' . $id);
	}

	function _instagram_auth(){
		$id = $_REQUEST['gallery'];
		session_start();
		$source = $_SESSION['asg_instagram_client_data'];
		if (!$source) {
			$gallery = new ASG_Gallery($id);
			$source = $gallery->create_source();
			$source = $source->source;
		}
		$auth_params = array('body' => array(
				'client_id' => $source['client_id'],
				'client_secret' => $source['client_secret'],
				'grant_type' => 'authorization_code',
				'redirect_uri' => $this->get_redirect_url($id),
				'code' => $_REQUEST['code']
		)
		);
		$response = asg_remote_post('https://api.instagram.com/oauth/access_token', $auth_params);
		if (is_wp_error($response)){
			wp_redirect(admin_url('admin.php?page=asg-instagram-auth-failure&message=' . urlencode($response->get_error_message())));
			exit;
		}
		if ($response['body']){
			$body = json_decode($response['body']);
			if($body->access_token){
				wp_redirect(admin_url('admin.php?page=asg-instagram-auth-success&insta_token=' . $body->access_token));
				exit;
			}
		}
		if ($response['response']){
			wp_redirect(admin_url('admin.php?page=asg-instagram-auth-failure&message=' . ($response['response']['message'])));
			exit;
		}
		wp_redirect(admin_url('admin.php?page=asg-instagram-auth-failure&message=' . urlencode('Unknown error')));
		exit;
	}

	function _ping(){
		$response = asg_remote_get(add_query_arg('access_token', $_REQUEST['data']['access_token'], 'https://api.instagram.com/v1/users/self/feed'));
		if ($response['response']['code'] == 200)
			echo('OK');
		else
			echo 'FAIL';
		exit;
	}



	function get_defaults(){
		return array(
			'client_id' => '',
			'client_secret' => '',
			'feed_type' => 'my-feed',
			'access_token' => '',
			'other_user_login' => '',
			'hashtag' => '',
			'caption_1' => 'caption',
			'caption_2' => 'login',
			'image' => '',
			'link' => 'lightbox',
			'lightbox_caption_1' => 'caption',
			'lightbox_caption_2' => 'none'
		);
	}

}

global $asg_source_editors;
$asg_source_editors['instagram'] = new ASG_Instagram_Source_Editor;
