<?php
require_once(dirname(__FILE__) . "/500pxoauth.class.php");

class ASG_500px_Source_Editor extends ASG_Source_Editor{
	var $slug = '500px';
	var $name = '500px';
	
	function __construct(){
		add_action('wp_ajax_asg-500px-check-keys', array($this, '_check_keys'));
		add_action('wp_ajax_asg-500px-get-collections', array($this, '_get_collections'));
		add_action('wp_ajax_asg-500px-check-token', array($this, '_check_token'));
		add_action('admin_action_asg-500px-oauth-get-token', array($this, '_oauth_get_token'));
		add_action('admin_action_asg-500px-oauth-receive-token', array($this, '_oauth_receive_token'));

		add_action('admin_menu', array($this, '_admin_menu'));
	}

	function _admin_menu(){
		add_submenu_page(null, __('500px Authorization', 'asg'), null, 'install_plugins', 'asg-500px-auth-success', array($this, '_oauth_success'));
	}

	function _check_token(){
		$source = new ASG_500px_Source(stripslashes_deep($_REQUEST['data']));
		if ($source->ping_token()){
			_e('Token is OK', 'asg', 'asg');
		} else {
			_e('The token is invalid', 'asg');
		}
		exit;
	}
	function _check_keys(){
		$source = new ASG_500px_Source(stripslashes_deep($_REQUEST));
		if ($source->ping()){
			_e('Keys are OK', 'asg');
		} else {
			_e('Keys are invalid', 'asg');
		}
		exit;
	}
	
	function _get_collections(){
		header('Content-type: text/json');
		$source = new ASG_500px_Source(stripslashes_deep($_REQUEST['data']));
		echo json_encode(array('success' => true, 'data' => $source->get_collections()));
		exit;
	}

	function _oauth_success(){
		session_start();

		$data = $_SESSION['asg_500px_data'];

		$connection = new ASG_500px_OAuth($data['consumer_key'], $data['consumer_secret'], $_SESSION['asg_500px_oauth_token'], $_SESSION['asg_500px_oauth_token_secret']);
		$access_token = $connection->getAccessToken($_REQUEST['oauth_verifier']);
		require(dirname(__FILE__) . "/auth-success.php");
		exit;
	}

	function _oauth_get_token(){
		$connection = new ASG_500px_OAuth($_REQUEST['consumer_key'], $_REQUEST['consumer_secret']);
		/* Get temporary credentials. */
		$request_token = $connection->getRequestToken(admin_url('admin.php?page=asg-500px-auth-success'));
		if (!isset($request_token['oauth_token']) || !isset($request_token['oauth_token_secret'])){

		} else {
			session_start();
			$_SESSION['asg_500px_data'] = stripslashes_deep($_REQUEST);
			$_SESSION['asg_500px_oauth_token'] = $token = $request_token['oauth_token'];
			$_SESSION['asg_500px_oauth_token_secret'] = $request_token['oauth_token_secret'];
			wp_redirect($connection->getAuthorizeURL($token));
			exit;
		}
		exit;
	}
	
	function get_defaults(){
		return array(
			'consumer_key' => '',
			'consumer_secret' => '',
			'access_token' => '',
			'access_token_secret' => '',
			'source_type' => '',
			'username' => '',
			'category' => '',
			'collection' => '',
			'collection_name' => '',
			'sorting' => '',
			'link' => 'lightbox',
			'caption_1' => 'name',
			'caption_2' => 'rating',
			'lightbox_caption_1' => 'name',
			'lightbox_caption_2' => 'rating',
		);
	}
	
}

global $asg_source_editors;
$asg_source_editors['500px']= new ASG_500px_Source_Editor;