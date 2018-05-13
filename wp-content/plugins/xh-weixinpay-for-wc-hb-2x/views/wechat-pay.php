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
global $HX_Wechat_WC_Payment_Gateway;

$order_id = $_GET['oid'];
$callback = urldecode($_GET['callback']);
if(empty($callback)){
    if(!isset($_SESSION)){
        @session_start();
    }

    $callback =isset($_SESSION['callback'])?$_SESSION['callback']:'';
}
if(empty($order_id)||empty($callback)){
	wp_die(-1);
}

$order = new WC_Order ( $order_id );
$callback_fail =$callback;
$callback_success = $HX_Wechat_WC_Payment_Gateway->get_return_url($order);
if(!$order||!$order->needs_payment ()){
	wp_redirect($callback_success);
	exit;
}


$total = $order->get_total ();
$preTotal = $total;
if (! in_array (  get_woocommerce_currency(), array (
		'RMB',
		'CNY'
) )) {
	$exchange_rate = floatval($HX_Wechat_WC_Payment_Gateway->get_option('exchange_rate'));
	if($exchange_rate<=0){
		$exchange_rate=1;
	}
		
	$total = round ( $total * $exchange_rate, 2 );
}

$total = apply_filters('xh_woocommerce_payment_gateway_exchange_rate', $total,$preTotal,$HX_Wechat_WC_Payment_Gateway);
$totalFee=( int ) ( $total* 100);

$order_ID = $HX_Wechat_WC_Payment_Gateway->guid(); // 订单号
$jsApiParameters=null;

try {
	$tools = new XH_Wx_Pay_Js_Api();	
	$openid=null;
	global $current_user;
	if($HX_Wechat_WC_Payment_Gateway->is_user_authorized()){
		$openid=$current_user->openid;
	}
	
	if(empty($openid)){
	   //若启用了跨域，必须要创建用户了
	    if('yes'==$HX_Wechat_WC_Payment_Gateway->get_option('auth_enabled')){
    	    $new_authorize_url = $HX_Wechat_WC_Payment_Gateway->get_option('wechat_remote_action');
    	    if(!empty($new_authorize_url)){
    	        wp_redirect(XH_Wechat_Url::user_auth());
    	        exit;
    	    }
	    }
	    if(class_exists('XH_Social')){
	        $api =XH_Social::instance()->get_available_addon('wechat_social_add_ons_social_wechat_ext');
	        if($api&&$api->enabled){
	            //是否启用了跨域支付
	            if('cross_domain_enabled'==$api->get_option('enabled_cross_domain')){
	                $new_authorize_url = $api->get_option('cross_domain_url');
	                if(!empty($new_authorize_url)){
	                    wp_redirect(XH_Wechat_Url::user_auth());
	                    exit;
	                }
	            }
	        }
	   }
	    
	   $tools = new XH_Wx_Pay_Js_Api();
	   $openid = $tools->GetOpenid();
	}
	
	$date = new DateTime ();
	$date->setTimezone ( new DateTimeZone ( 'Asia/Shanghai' ) );
	$startTime = $date->format ( 'YmdHis' );
	//$expiredTime = $startTime + 60*2*1000;
	
	// ②、统一下单
	$input = new XH_WECHAT_WxPayUnifiedOrder ();
	$input->SetBody ( $HX_Wechat_WC_Payment_Gateway->get_order_title($order));
	$input->SetOut_trade_no ( $order_ID );
	$input->SetAttach($order_id);
	$input->SetTotal_fee ( $totalFee );
	$input->SetTime_start ( $startTime );
	//$input->SetTime_expire ( $expiredTime );
	
	$input->SetNotify_url (XH_Wechat_Url::weixin_pay_notify());
	$input->SetTrade_type ( "JSAPI" );

	$input->SetOpenid ( $openid );
	$weixinorder = XH_Wx_Pay_Api::unifiedOrder ( $input );
	
	if($weixinorder['return_code']=='FAIL'||$weixinorder['result_code']=='FAIL'){
		throw new XH_Wx_Pay_Exception("return_msg:".$weixinorder['return_msg']." ;err_code_des: ".$weixinorder['err_code_des']);
	}
	
	$jsApiParameters = $tools->GetJsApiParameters ( $weixinorder );		
    if(class_exists('XH_Social')){
      $api =XH_Social::instance()->get_available_addon('wechat_social_add_ons_social_wechat_ext');
        if($api&&$api->enabled){
            //是否启用了跨域支付
            if('cross_domain_enabled'==$api->get_option('enabled_cross_domain')){
                $new_authorize_url = $api->get_option('cross_domain_url');
                if(!empty($new_authorize_url)){
                    $hash = XH_Social_Helper::generate_hash(array(
                        'jsapi_params'=>$jsApiParameters
                    ), XH_Wx_Pay_Config::$APPSECRET);
                    ?>
            			<!DOCTYPE html>
            			<html>
            			<head>
            			<meta http-equiv="content-type" content="text/html;charset=utf-8" />
            			<meta name="viewport" content="width=device-width, initial-scale=1" />
            			<title>微信支付</title>
            			</head>
            			<body>
            			<p style="margin: 50px 0 0 0; text-align: center;">
            				<img src="data:image/gif;base64,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" width="16" height="16" alt="" /> 微信支付加载中，请稍候...
            			</p>
            			
            			<form id="form-xh-wechat-jspapi-pay" action="<?php print $new_authorize_url;?>" method="post">
            				<input type="hidden" name="hash" value="<?php print $hash?>"/>
            				<input type="hidden" name="callback_success" value="<?php print base64_encode($callback_success)?>"/>
            				<input type="hidden" name="callback_fail" value="<?php print base64_encode($callback_fail)?>"/>
            				<input type="hidden" name="jsapi_params" value="<?php print base64_encode($jsApiParameters)?>"/>
            			</form>
            			<script type="text/javascript">
            			document.getElementById('form-xh-wechat-jspapi-pay').submit();
            			</script>
            			</body>
            			</html>
            			<?php 
            			exit;
            		}
            }
        }
       
    }
	
	if(!empty($jsApiParameters)&&'yes'==$HX_Wechat_WC_Payment_Gateway->get_option('auth_enabled')){
		$new_authorize_url = $HX_Wechat_WC_Payment_Gateway->get_option('wechat_remote_action');
		
		if(!empty($new_authorize_url)){
			$hash = md5($jsApiParameters.$callback_success.$callback_fail.XH_Wx_Pay_Config::$APPID.XH_Wx_Pay_Config::$APPSECRET);	
			?>
			<!DOCTYPE html>
			<html>
			<head>
			<meta http-equiv="content-type" content="text/html;charset=utf-8" />
			<meta name="viewport" content="width=device-width, initial-scale=1" />
			<title>微信支付</title>
			</head>
			<body>
			<p style="margin: 50px 0 0 0; text-align: center;">
				<img src="data:image/gif;base64,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" width="16" height="16" alt="" /> 微信支付加载中，请稍候...
			</p>
			
			<form id="form-xh-wechat-jspapi-pay" action="<?php print $new_authorize_url;?>" method="post">
				<input type="hidden" name="hash" value="<?php print urlencode($hash)?>"/>
				<input type="hidden" name="callback_success" value="<?php print urlencode($callback_success)?>"/>
				<input type="hidden" name="callback_fail" value="<?php print urlencode($callback_fail)?>"/>
				<input type="hidden" name="jsapi_params" value="<?php print urlencode($jsApiParameters)?>"/>
			</form>
			<script type="text/javascript">
			document.getElementById('form-xh-wechat-jspapi-pay').submit();
			</script>
			</body>
			</html>
			<?php 
			exit;
		}
	}
	
	?>
	<!DOCTYPE html>
	<html>
	<head>
	<meta http-equiv="content-type" content="text/html;charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1" />
	<title>微信支付</title>
	</head>
	<body>
	<p style="margin: 50px 0 0 0; text-align: center;">
		<img src="data:image/gif;base64,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" width="16" height="16" alt="" /> 微信支付加载中，请稍候...
	</p>
	<script type="text/javascript">
		setTimeout(function (){
	
			//调用微信JS api 支付
			window.jsApiCall=function (){
				WeixinJSBridge.invoke(
					'getBrandWCPayRequest',
					<?php echo $jsApiParameters; ?>,
					function(res){
						if (res.err_msg === "get_brand_wcpay_request:ok") {
							 location.href='<?php print $callback_success;?>'
						}else{
							if(res.err_desc){
								alert(res.err_desc);
							}
							location.href='<?php print $callback_fail;?>';
						}
					}
				);
			}
	
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
		},300);
	</script>
	</body>
	</html>
	<?php 
	exit;
} 
 catch (Exception $e) {
	if($e->getCode()==40029){
		$error_code = isset($_GET['_error_code'])?intval($_GET['_error_code']):0;
		if($error_code<2){
			$error_code++;
			$protocol = (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
			$url= explode('?',$protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']);	
	
			$authorize_url=$url[0].'?_error_code='.$error_code.'&oid='.$order_id.'&callback='.urlencode($callback);
			wp_redirect($authorize_url);
			exit;
		}
	}
 	XH_Log::ERROR ( " XHWxPayApi:" . $e->getMessage());
 	?>
 	<!DOCTYPE html>
 	<html>
 	<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
	<title>微信支付</title>
	<link rel="stylesheet" href="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/lib/weui.css">
	<link rel="stylesheet" href="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/css/jquery-weui.css">
	</head>
    <body>
	<div class="page">
	    <div class="weui_msg">
	        <div class="weui_icon_area"><i class="weui_icon_warn weui_icon_msg"></i></div>
	        <div class="weui_text_area">
	            <h2 class="weui_msg_title">微信支付失败</h2>
           	 	<p class="weui_msg_desc" ><a href="<?php print $callback_fail?>"><?php print $e->getMessage()?></a></p>
	        </div>
	    </div>
	</div>
    </body>
 	</html>
 	<?php 
 	exit;
}