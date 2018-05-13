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
ini_set ( 'date.timezone', 'Asia/Shanghai' );
error_reporting ( E_ERROR );
global $HX_Wechat_WC_Payment_Gateway,$callback;

if(!WC()->session->has_session()){
	WC()->session->set_customer_session_cookie(true);
}

$callback='';
if(isset($_GET['callback'])){
	$callback=urldecode($_GET['callback']);
	WC()->session->set(XH_WECHAT_SESSION_AUTH_CALLBACK, $callback);
	WC()->session->save_data();
}else{
	$callback=WC()->session->get(XH_WECHAT_SESSION_AUTH_CALLBACK,get_option('home'));
}

if(empty($callback)){
	$callback = get_option('home');
}

global $current_user;
if($HX_Wechat_WC_Payment_Gateway->is_user_authorized()){
	WC()->session->__unset(XH_WECHAT_SESSION_AUTH_CALLBACK);
	wp_redirect($callback);
	exit;
}

if(class_exists('XH_Social')){
    $api =XH_Social::instance()->channel->get_social_channel('social_wechat');
    if($api&&$api->enabled){
        XH_Social::instance()->session->set('social_login_location_uri', $callback);
        $login_uri = $api->process_generate_authorization_uri($callback);
        wp_redirect($login_uri);
        exit;
    }
}
//new api callback
$weixin_user=null;
if(isset($_POST['hash'])){
	$openid = $_POST['openid'];
	$nickname = $_POST['nickname'];
	$headimgurl = $_POST['headimgurl'];
	$hash = $_POST['hash'];
	
	$ohash =md5($openid.$headimgurl.$nickname.XH_Wx_Pay_Config::$APPID.XH_Wx_Pay_Config::$APPSECRET.XH_WECHAT_URL.'/views/wechat-authorize.php');
	if($hash!=$ohash){
		XH_Log::ERROR('系统验证登录返回的消息出错了;msg:'.$openid.';'.$headimgurl.';'.$nickname);
		wp_die('我也不知道为什么，报错了(： ');
		exit;
	}
	$weixin_user=array();
	$weixin_user['openid']=$openid;
	$weixin_user['headimgurl']=$headimgurl;
	$weixin_user['nickname']=$nickname;
}else{
	//第三方跳转授权
	if('yes'==$HX_Wechat_WC_Payment_Gateway->get_option('auth_enabled')){
		$new_authorize_url = $HX_Wechat_WC_Payment_Gateway->get_option('wechat_remote_action');
		if(!empty($new_authorize_url)){
			$authorize_url=$new_authorize_url.'?callback='.urlencode(XH_WECHAT_URL.'/views/wechat-authorize.php');
			wp_redirect($authorize_url);
			exit;
		}
	}
}

if(is_null($weixin_user)||!$weixin_user){
	try {
		$tools = new XH_Wx_Pay_Js_Api();
		$weixin_user = $tools->GetUserInfo(function(){
			global $HX_Wechat_WC_Payment_Gateway,$callback;
			if($HX_Wechat_WC_Payment_Gateway->is_user_authorized()){
				WC()->session->__unset(XH_WECHAT_SESSION_AUTH_CALLBACK);
				wp_redirect($callback);
				exit;
			}
		});	
		
		if(!$weixin_user||empty($weixin_user['openid'])){
			throw new Exception( __('User information loading fails' ,XH_WECHAT));
		}
	} 
	catch (Exception $e) {
		if($e->getCode()==40029){
			$error_code = isset($_GET['_error_code'])?intval($_GET['_error_code']):0;
			if($error_code<2){
				$error_code++;
				$protocol = (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
		
				$url= explode('?',$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);
				$authorize_url=$url[0].'?_error_code='.$error_code.'&callback='.urlencode($callback);
				wp_redirect($authorize_url);
				exit;	
			}
		}
		
		XH_Log::DEBUG ( " XHWxPayApi:" . $e->getMessage());
		WC()->session->set(XH_WECHAT_SESSION_AUTH_ERROR, $e->getMessage());
		wp_redirect(XH_WECHAT_URL.'/views/wechat-authorize-fail.php');
		exit;
	}
}

//原登录方式
global $wpdb;
$meta_user =$wpdb->get_row(
    "select m.user_id as ID 
    from {$wpdb->prefix}usermeta m
    inner join {$wpdb->prefix}users u on u.ID = m.user_id 
    where m.meta_key='openid' 
          and m.meta_value='{$weixin_user['openid']}' 
    limit 1;");
$user_ID = $meta_user?$meta_user->ID:0;

if($user_ID==0){
    $user = get_user_by('login', $weixin_user['openid']);
    if($user){
        $user_ID=$user->ID;
    }
}

if ($user_ID>0) {
	$call=$HX_Wechat_WC_Payment_Gateway->wpuser_authorize($user_ID,$weixin_user['openid'],$weixin_user['headimgurl'],$weixin_user['nickname']);
	wp_redirect($callback);
	exit;
}

//创建用户
$user_ID = wp_insert_user(array(
    'user_login'=>$HX_Wechat_WC_Payment_Gateway->generate_user_login($weixin_user['nickname']),
    'user_nicename'=>$HX_Wechat_WC_Payment_Gateway->guid(),
    'first_name'=>$HX_Wechat_WC_Payment_Gateway->remove_emoji($weixin_user['nickname']),
    'nickname'=>$HX_Wechat_WC_Payment_Gateway->remove_emoji($weixin_user['nickname']),
    'display_name'=>$HX_Wechat_WC_Payment_Gateway->remove_emoji($weixin_user['nickname']),
));

if ( is_wp_error($user_ID) ) {
	XH_Log::DEBUG ( " XHWxPayApi:" .$user_ID->get_error_message());
	WC()->session->set(XH_WECHAT_SESSION_AUTH_ERROR,$user_ID->get_error_message());
	wp_redirect(XH_WECHAT_URL.'/views/wechat-authorize-fail.php');
	exit;
}

$HX_Wechat_WC_Payment_Gateway->wpuser_authorize($user_ID,$weixin_user['openid'],$weixin_user['headimgurl'],$weixin_user['nickname']);

WC()->session->__unset(XH_WECHAT_SESSION_AUTH_CALLBACK);
wp_redirect($callback);
exit;