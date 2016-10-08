<?php
class ASG_Facebook_Source_Editor extends ASG_Source_Editor {
	var $slug = 'facebook';
	var $name = 'Facebook';

	function __construct(){
		add_action('wp_ajax_asg-facebook-ping-user', array($this, '_ping_user'));
		add_action('wp_ajax_asg-facebook-generate-token', array($this, '_generate_access_token'));
		add_action('wp_ajax_asg-facebook-check-access-token', array($this, '_check_access_token'));
		add_action('wp_ajax_asg-facebook-get-albums', array($this, '_get_albums'));
		add_action('admin_action_asg-facebook-auth', array($this, '_auth'));
		add_action('admin_action_asg-facebook-auth-show', array($this, '_auth_show'));
	}


	function _auth(){
		$state = stripslashes($_REQUEST['state']);
		list($app_id, $app_secret) = explode('|', $state);
		$redirect_url = admin_url('admin.php?action=asg-facebook-auth');
		$response = asg_remote_get("https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=$redirect_url&client_secret=$app_secret&code=" . stripslashes($_REQUEST['code']));
		if (is_wp_error($response)){
			exit;
		}
		$data = wp_parse_args($response['body']);
		?>
		<html><head><script>
			window.opener.jQuery('#facebook-access-token').val('<?php echo $data['access_token']?>');
			window.opener.jQuery('#facebook-token-expires').val('<?php echo $data['expires']?>');
			window.close()
		</script></head><body></body></html><?php
		exit;
	}

	function _auth_show(){
		$state = stripslashes($_REQUEST['state']);
		list($app_id, $app_secret) = explode('|', $state);
		$redirect_url = admin_url('admin.php?action=asg-facebook-auth-show');
		$response = asg_remote_get("https://graph.facebook.com/oauth/access_token?client_id=$app_id&redirect_uri=$redirect_url&client_secret=$app_secret&code=" . stripslashes($_REQUEST['code']));
		if (is_wp_error($response)){
			var_dump($response);
			exit;
		}
		if ($response['response']['code'] == 400){
			echo "Token expired. Please re-copy the URL and try again.";
		} else {
			$data = wp_parse_args($response['body']);
			?>
			<!doctype html>
			<html>
			<body>
			<strong>Please copy the next parameters:</strong><br>
			<br>
			<strong>ACCESS TOKEN: </strong><?php echo $data['access_token'] ?><br>
			<strong>EXPIRES: </strong><?php echo $data['expires'] ?><br>
			<?php $user_data = wp_remote_get('https://graph.facebook.com/v2.0/me?access_token=' . $data['access_token']) ?>
			<?php $user_data = json_decode($user_data['body'], true); ?>
			<strong>USER / PAGE ID: </strong><?php echo $user_data['id'] ?>
			</body><?php
		}

	}
	function _generate_access_token(){
		$id = (int)$_REQUEST['id'];
		$source = new ASG_Facebook_Source($id, stripslashes_deep($_REQUEST));
		$token = $source->generate_access_token();
		header('Content-type: text/json');
		if (is_wp_error($token)){
			echo json_encode(array('success' => false, 'error' => $token->get_error_message()));
		} else {
			echo json_encode(array('success' => true, 'token' => $token));
		}
		exit;
	}

	function _check_access_token(){
		$id = (int)$_REQUEST['id'];
		$source = new ASG_Facebook_Source(stripslashes_deep($_REQUEST));
		if ($source->check_access_token()){
			_e('Token is valid', 'asg');
		} else {
			_e('Token is invalid', 'asg');
		}
		exit;

	}

	function _get_albums(){
		$id = (int)$_REQUEST['data']['id'];
		$source = new ASG_Facebook_Source(stripslashes_deep($_REQUEST['data']));
		header('Content-type: text/json');
		$data = $source->get_albums();
		echo json_encode(array('success' => true, 'data' => $data));
		exit;
	}

	function _ping_user(){
		$id = (int)$_REQUEST['id'];
		$source = new ASG_Facebook_Source(wp_parse_args($_REQUEST, array('id' => $id)));
		$response = $source->find_user_or_page();
		if (is_wp_error($response)){
			if ($response->get_error_code() == 400)
				echo __('Error: ', 'asg') . $response->get_error_message();
			if ($response->get_error_code() == 404)
				echo ('Sorry, no such user or page');
		} else {
			echo __('User / page valid', 'asg');
		}
		exit;
	}

	function get_defaults(){
		return array(
			'app_id' => '',
			'app_secret' => '',
			'username' => 'me',
			'access_token' => '',
			'token_expires' => '',
			'source_type' => 'photos',
			'source' => '',
			'source_name' => '',
			'link' => 'lightbox',
			'caption_1' => 'name',
			'caption_2' => 'rating',
			'lightbox_caption_1' => 'name',
			'lightbox_caption_2' => 'rating'
		);
	}
}

global $asg_source_editors;
$asg_source_editors['facebook']= new ASG_Facebook_Source_Editor;
