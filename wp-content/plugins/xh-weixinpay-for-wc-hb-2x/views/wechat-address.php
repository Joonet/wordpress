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

if(!WC()->session->has_session()){
	WC()->session->set_customer_session_cookie(true);
}

$callback='';
if(isset($_GET['callback'])){
	$callback=urldecode($_GET['callback']);
	WC()->session->set(XH_WECHAT_SESSION_ADDRESS_CALLBACK, $callback);
	WC()->session->save_data();
}else{
	$callback=WC()->session->get(XH_WECHAT_SESSION_ADDRESS_CALLBACK,get_option('home'));
}
if(empty($callback)){
	$callback = get_option('home');
}

if($_GET['cancel']){
	wp_redirect($callback);
	exit;
}

global $HX_Wechat_WC_Payment_Gateway,$current_user;
if(!$HX_Wechat_WC_Payment_Gateway->is_user_authorized()){
	//注意，这里的callback是来源地址，防止url过长导致异常访问
	wp_redirect(XH_Wechat_Url::user_auth($callback));
	exit;
}

if(isset($_POST['wa'])){
	update_user_meta($current_user->ID, 'billing_first_name', $_POST['ship_to_customer_name']);
	update_user_meta($current_user->ID, 'billing_last_name', '');
	update_user_meta($current_user->ID, 'billing_phone', $_POST['ship_to_customer_mobile']);
	update_user_meta($current_user->ID, 'billing_postcode', $_POST['ship_to_zip']);
	update_user_meta($current_user->ID, 'billing_address_1', $_POST['street_address']);
	update_user_meta($current_user->ID, 'billing_city',  $_POST['city']);
	
	$province = rtrim( $_POST['province'],'省市');
	if(!empty($province)){
		foreach (HX_Wechat_WC_Payment_Gateway::$my_states as $key=>$name){
			if( stristr($province,$name) !=false){
				update_user_meta($current_user->ID, 'billing_state',  $key);
				break;
			}
		}
	}
	
	if($_POST['wa']==1){
		wp_redirect($callback)	;
	}else{
		$msg=array(
				'success'=>true,
				message=>null
		);
		print json_encode($msg);
	}

	exit;
}else{
    if(class_exists('XH_Social')){
       $api =XH_Social::instance()->get_available_addon('wechat_social_add_ons_social_wechat_ext');
        if($api&&$api->enabled){
            if('cross_domain_enabled'==$api->get_option('enabled_cross_domain')){
                $new_authorize_url = $api->get_option('cross_domain_url');
                if(!empty($new_authorize_url)){
                    $params = array(
                        'callback'=>XH_WECHAT_URL.'/views/wechat-address.php'
                    );
                    $params['hash'] = XH_Social_Helper::generate_hash($params, XH_Wx_Pay_Config::$APPSECRET);
    
                    $authorize_url=$new_authorize_url.'?wa=1&'.http_build_query($params);
                    wp_redirect($authorize_url);
                    exit;
                }
            }
        }
    }
    
	//第三方跳转授权
	if('yes'==$HX_Wechat_WC_Payment_Gateway->get_option('auth_enabled')){
		$new_authorize_url = $HX_Wechat_WC_Payment_Gateway->get_option('wechat_remote_action');
		if(!empty($new_authorize_url)){
			$authorize_url=$new_authorize_url.'?wa=1&callback='.urlencode(XH_WECHAT_URL.'/views/wechat-address.php');
			wp_redirect($authorize_url);
			exit;
		}
	}
}

$errmsg = null;
$addressParameters=null;
try {
	$tools = new XH_Wx_Pay_Js_Api();
	$tools->GetOpenid ();
	$addressParameters = $tools->GetEditAddressParameters ();
} catch (Exception $e) {
	XH_Log::ERROR($e->getMessage());
	$errmsg=$e->getMessage();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title>微信收货地址</title>
    <link rel="stylesheet" href="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/lib/weui.css">
	<link rel="stylesheet" href="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/css/jquery-weui.css">
	<script src="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/lib/jquery-2.1.4.js"></script>
	<script src="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/js/jquery-weui.js"></script>
	<script type="text/javascript" src="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/js/htmlhelper.js"></script>
</head>
<body>
	<p style="margin:50px 0 0 0;text-align:center;">
	<img src="data:image/gif;base64,R0lGODlhEAAQAPQAAP///2R1cfb397jAvuzu7o+bmK63tWR1cZqlonqJhs3S0djc23GAfcPKyGd4dIWTkKSuq2R1cWR1cWR1cWR1cWR1cWR1cWR1cWR1cWR1cWR1cWR1cWR1cWR1cWR1cWR1cSH5BAkKAAAAIf4aQ3JlYXRlZCB3aXRoIGFqYXhsb2FkLmluZm8AIf8LTkVUU0NBUEUyLjADAQAAACwAAAAAEAAQAAAFdyAgAgIJIeWoAkRCCMdBkKtIHIngyMKsErPBYbADpkSCwhDmQCBethRB6Vj4kFCkQPG4IlWDgrNRIwnO4UKBXDufzQvDMaoSDBgFb886MiQadgNABAokfCwzBA8LCg0Egl8jAggGAA1kBIA1BAYzlyILczULC2UhACH5BAkKAAAALAAAAAAQABAAAAV2ICACAmlAZTmOREEIyUEQjLKKxPHADhEvqxlgcGgkGI1DYSVAIAWMx+lwSKkICJ0QsHi9RgKBwnVTiRQQgwF4I4UFDQQEwi6/3YSGWRRmjhEETAJfIgMFCnAKM0KDV4EEEAQLiF18TAYNXDaSe3x6mjidN1s3IQAh+QQJCgAAACwAAAAAEAAQAAAFeCAgAgLZDGU5jgRECEUiCI+yioSDwDJyLKsXoHFQxBSHAoAAFBhqtMJg8DgQBgfrEsJAEAg4YhZIEiwgKtHiMBgtpg3wbUZXGO7kOb1MUKRFMysCChAoggJCIg0GC2aNe4gqQldfL4l/Ag1AXySJgn5LcoE3QXI3IQAh+QQJCgAAACwAAAAAEAAQAAAFdiAgAgLZNGU5joQhCEjxIssqEo8bC9BRjy9Ag7GILQ4QEoE0gBAEBcOpcBA0DoxSK/e8LRIHn+i1cK0IyKdg0VAoljYIg+GgnRrwVS/8IAkICyosBIQpBAMoKy9dImxPhS+GKkFrkX+TigtLlIyKXUF+NjagNiEAIfkECQoAAAAsAAAAABAAEAAABWwgIAICaRhlOY4EIgjH8R7LKhKHGwsMvb4AAy3WODBIBBKCsYA9TjuhDNDKEVSERezQEL0WrhXucRUQGuik7bFlngzqVW9LMl9XWvLdjFaJtDFqZ1cEZUB0dUgvL3dgP4WJZn4jkomWNpSTIyEAIfkECQoAAAAsAAAAABAAEAAABX4gIAICuSxlOY6CIgiD8RrEKgqGOwxwUrMlAoSwIzAGpJpgoSDAGifDY5kopBYDlEpAQBwevxfBtRIUGi8xwWkDNBCIwmC9Vq0aiQQDQuK+VgQPDXV9hCJjBwcFYU5pLwwHXQcMKSmNLQcIAExlbH8JBwttaX0ABAcNbWVbKyEAIfkECQoAAAAsAAAAABAAEAAABXkgIAICSRBlOY7CIghN8zbEKsKoIjdFzZaEgUBHKChMJtRwcWpAWoWnifm6ESAMhO8lQK0EEAV3rFopIBCEcGwDKAqPh4HUrY4ICHH1dSoTFgcHUiZjBhAJB2AHDykpKAwHAwdzf19KkASIPl9cDgcnDkdtNwiMJCshACH5BAkKAAAALAAAAAAQABAAAAV3ICACAkkQZTmOAiosiyAoxCq+KPxCNVsSMRgBsiClWrLTSWFoIQZHl6pleBh6suxKMIhlvzbAwkBWfFWrBQTxNLq2RG2yhSUkDs2b63AYDAoJXAcFRwADeAkJDX0AQCsEfAQMDAIPBz0rCgcxky0JRWE1AmwpKyEAIfkECQoAAAAsAAAAABAAEAAABXkgIAICKZzkqJ4nQZxLqZKv4NqNLKK2/Q4Ek4lFXChsg5ypJjs1II3gEDUSRInEGYAw6B6zM4JhrDAtEosVkLUtHA7RHaHAGJQEjsODcEg0FBAFVgkQJQ1pAwcDDw8KcFtSInwJAowCCA6RIwqZAgkPNgVpWndjdyohACH5BAkKAAAALAAAAAAQABAAAAV5ICACAimc5KieLEuUKvm2xAKLqDCfC2GaO9eL0LABWTiBYmA06W6kHgvCqEJiAIJiu3gcvgUsscHUERm+kaCxyxa+zRPk0SgJEgfIvbAdIAQLCAYlCj4DBw0IBQsMCjIqBAcPAooCBg9pKgsJLwUFOhCZKyQDA3YqIQAh+QQJCgAAACwAAAAAEAAQAAAFdSAgAgIpnOSonmxbqiThCrJKEHFbo8JxDDOZYFFb+A41E4H4OhkOipXwBElYITDAckFEOBgMQ3arkMkUBdxIUGZpEb7kaQBRlASPg0FQQHAbEEMGDSVEAA1QBhAED1E0NgwFAooCDWljaQIQCE5qMHcNhCkjIQAh+QQJCgAAACwAAAAAEAAQAAAFeSAgAgIpnOSoLgxxvqgKLEcCC65KEAByKK8cSpA4DAiHQ/DkKhGKh4ZCtCyZGo6F6iYYPAqFgYy02xkSaLEMV34tELyRYNEsCQyHlvWkGCzsPgMCEAY7Cg04Uk48LAsDhRA8MVQPEF0GAgqYYwSRlycNcWskCkApIyEAOw=="  width="16" height="16" alt=""/> 正在加载微信收货地址，请稍候...
	</p>
<script type="text/javascript">
	(function(){
		//调用微信JS api 支付
		window.jsApiCall = function (){
			WeixinJSBridge.invoke(
				'editAddress',
				<?php echo $addressParameters; ?>,
				function(res){
					if (res == null) {
						location.href='<?php print $callback?>';
						return;
					}

					if (res.err_msg === 'edit_address:fail') {
						if(res.err_desc){
							alert(res.err_desc);
						}else{
							htmlhelper.dialog.alert(JSON.stringify(res));
						}
						
						setTimeout(function(){location.href='<?php print $callback?>';},2000);
						return;
					}

					if (res.err_msg === 'edit_address:cancel') {
						location.href='<?php print $callback?>';
						return;
					}

					if(htmlhelper.isNullOrEmpty(res.userName)){
						location.href='<?php print $callback?>';
						return;
					}
					
					var data ={
							'wa':2,
							ship_to_customer_name:res.userName,
							ship_to_customer_mobile:res.telNumber,

							district:res.addressCountiesThirdStageName,
							
							province:res.proviceFirstStageName,
							city:res.addressCitySecondStageName,
							
							ship_to_zip:res.addressPostalCode,
							street_address:res.addressDetailInfo,
							ship_to_street_address: res.proviceFirstStageName + ' ' + res.addressCitySecondStageName + ' ' + res.addressCountiesThirdStageName + ' ' + res.addressDetailInfo 
					};
					$.ajax({
					      url:'<?php print XH_WECHAT_URL.'/views/wechat-address.php'?>',
					      type:'post',
					      timeout: 60 * 1000,
					      async: true,
					      cache: false,
					      data: data,
					      dataType: 'json',
					      beforeSend: function() {
					      },
					      complete: function() {
					        htmlhelper.dialog.loading.hide();
					      },
					      success: function(e) {
						      if(!e.success){
						    	  htmlhelper.dialog.alert(e.message);
							  }
							  
						      setTimeout(function(){ location.href='<?php print $callback?>';},2000);
					      },
					      error: function(e){
					    	  htmlhelper.dialog.alert('<?php print __('Error, please try again later.' ,XH_WECHAT)?>');
					    	  
					    	  setTimeout(function(){ location.href='<?php print $callback?>';},2000);
						  }
					    });
				}
			);
		}

		htmlhelper.dialog.loading.show();
		if (typeof WeixinJSBridge == "undefined"){
		    if( document.addEventListener ){
		        document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
		    }else if (document.attachEvent){
		        document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
		        document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
		    }
		}else{
		    jsApiCall();
		}
	})();

	
	</script>
</body>
</html>