<?php
define('XH_LOGIN_IGNORE', true);
define('WP_USE_THEMES', false);
$root_path =rtrim($_SERVER['DOCUMENT_ROOT'],'/');
if(file_exists($root_path.'/wp-load.php')){
    require_once $root_path.'/wp-load.php';
}else if(file_exists('../../../../wp-load.php')){
    require_once '../../../../wp-load.php';
}else{
    wp_die('无法加载wp-load.php');
}

if(!$_POST){
	wp_redirect(get_option('home'));
	exit;
}

$auth_key =AUTH_KEY;
if(empty($auth_key)){
	$auth_key =DB_NAME;
}
$auth =isset( $_POST['auth'])? $_POST['auth']:'';
$hash = isset($_POST['hash'])?$_POST['hash']:'';
$openid =isset($_POST['openid'])?$_POST['openid']:'';
$username =isset($_POST['username'])?$_POST['username']:'';
$password =isset($_POST['password'])?$_POST['password']:'';
$headimgurl =isset($_POST['headimgurl'])?$_POST['headimgurl']:'';
$nickname =isset($_POST['nickname'])?$_POST['nickname']:'';

$ohash =md5($openid.$headimgurl.$nickname.$auth_key.$auth);
if($ohash!=$hash){
	wp_redirect(get_option('home'));
	exit;
}

global $HX_Wechat_WC_Payment_Gateway;
//跳过绑定
if($auth==0){
	//原登录方式
	$user = get_user_by('login', $openid);	
	if ($user) {
		$call =$HX_Wechat_WC_Payment_Gateway->wpuser_authorize($user->ID,$openid,$headimgurl,$nickname);
		if(is_wp_error($call)){
			XH_Log::DEBUG ( " XHWxPayApi:" . $call->get_error_message());
			print json_encode(array(
				'success'=>false,
				'message'=>$call->get_error_message()
			));
			exit;
		}
		print json_encode(array(
				'success'=>true,
				'message'=>''
		));
		exit;
	}
	
	//创建用户
	$user_ID = wp_create_user($openid,date_i18n ( 'YmdHis' ));
	if ( is_wp_error($user_ID) ) {
		XH_Log::DEBUG ( " XHWxPayApi:" .$user_ID->get_error_message());
		print json_encode(array(
				'success'=>false,
				'message'=>$user_ID->get_error_message()
		));
		exit;
	}
	
	$call =$HX_Wechat_WC_Payment_Gateway->wpuser_authorize($user_ID,$openid,$headimgurl,$nickname);
	if(is_wp_error($call)){
		XH_Log::DEBUG ( " XHWxPayApi:" . $call->get_error_message());
		print json_encode(array(
				'success'=>false,
				'message'=>$call->get_error_message()
		));
		exit;
	}
	
	print json_encode(array(
			'success'=>true,
			'message'=>''
	));
	exit;
}else{
	if(empty($username)){
		print json_encode(array(
				'success'=>false,
				'message'=>'登录名不能为空'
		));
		exit;
	}
	
	if(empty($password)){
		print json_encode(array(
				'success'=>false,
				'message'=>'登录密码不能为空'
		));
		exit;
	}
	
	$error_times =intval(WC()->session->get(XH_WECHAT_SESSION_AUTH_ERROR_TIMES));
	if($error_times>10){
		print json_encode(array(
				'success'=>false,
				'message'=>'您登录的次数过多，请稍候重试'
		));
		exit;
	}
	
	$user = wp_authenticate($username,$password);
	if(is_wp_error($user)){
		$error_times++;
		WC()->session->set(XH_WECHAT_SESSION_AUTH_ERROR_TIMES,$error_times);
		print json_encode(array(
				'success'=>false,
				'message'=>'用户名或者密码错误'
		));
		exit;
	}
	
	WC()->session->set(XH_WECHAT_SESSION_AUTH_ERROR_TIMES,0);
	
	$call =$HX_Wechat_WC_Payment_Gateway->wpuser_authorize($user->ID,$openid,$headimgurl,$nickname);
	if(is_wp_error($call)){
		XH_Log::DEBUG ( " XHWxPayApi:" . $call->get_error_message());
		print json_encode(array(
				'success'=>false,
				'message'=>$call->get_error_message()
		));
		exit;
	}
	
	print json_encode(array(
			'success'=>true,
			'message'=>''
	));
	exit;
}