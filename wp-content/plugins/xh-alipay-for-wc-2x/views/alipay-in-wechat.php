<?php 
define('WP_USE_THEMES', false);
$root_path =rtrim($_SERVER['DOCUMENT_ROOT'],'/');
if(file_exists($root_path.'/wp-load.php')){
    require_once $root_path.'/wp-load.php';
}else if(file_exists('../../../../wp-load.php')){
    require_once     '../../../../wp-load.php';
}else{
    wp_die('无法加载wp-load.php');
}

 global $HX_Alipay_WC_Payment_Gateway;
$order_id = $_GET['order_ID'];
$order = new WC_Order ( $order_id );
if(!$order){
	wp_die('未知的订单信息');
	exit;
}

if(!$order->needs_payment()){
	wp_redirect($HX_Alipay_WC_Payment_Gateway->get_return_url($order));
	exit;
}

$total = $order->get_total ();
$preTotal = $total;
if (! in_array (get_woocommerce_currency(), array (
		'RMB',
		'CNY'
) )) {
	$exchange_rate = floatval($HX_Alipay_WC_Payment_Gateway->get_option('exchange_rate'));
	if($exchange_rate<=0){$exchange_rate=1;}
	$total = round ( $total * $exchange_rate, 2 );
}

$totalFee = apply_filters('xh_woocommerce_payment_gateway_exchange_rate', $total,$preTotal,$HX_Alipay_WC_Payment_Gateway);
$html_text='';
if(XH_Alipay_Url::isWeixinClient()){
?>
<html>
<head>
<meta http-equiv="content-type" content="text/html;charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>支付宝</title>
<body style="padding:0;margin:0;">
<?php 
	if(XH_Alipay_Url::isIOS()){
		?>
		<img alt="支付宝" src="<?php print XH_ALIPAY_URL?>/images/ios.png" style="max-width: 100%;">
		<?php 
	}else{
		?>
		<img alt="支付宝" src="<?php print XH_ALIPAY_URL?>/images/alipayout.jpg" style="max-width: 100%;">
		<?php 
	}
?>
</body>
</html>
<?php 

}else{
	require_once (XH_ALIPAY_DIR . "/alipay/alipaywapdirect/alipay.config.php");
	require_once (XH_ALIPAY_DIR . "/alipay/alipaywapdirect/lib/alipay_submit.class.php");
	// 商户订单号，商户网站订单系统中唯一订单号，必填
	$out_trade_no = $order_id;
		
	// 订单名称，必填
	$subject =$HX_Alipay_WC_Payment_Gateway->get_order_title($order);
		
	// 付款金额，必填
	$total_fee = $totalFee;
		
	// 收银台页面上，商品展示的超链接，必填
	$show_url = $order->get_view_order_url ();
		
	// 商品描述，可空
	$body = '';
		
	/**
	 * *********************************************************
	 */
	global $alipay_config;
	// 构造要请求的参数数组，无需改动
	$parameter = array (
			"service" => $alipay_config ['service'],
			"partner" => $alipay_config ['partner'],
			"seller_id" => $alipay_config ['seller_id'],
			"payment_type" => $alipay_config ['payment_type'],
			"notify_url" => $alipay_config ['notify_url'],
			"return_url" => $HX_Alipay_WC_Payment_Gateway->get_return_url($order),
			"_input_charset" => trim ( strtolower ( $alipay_config ['input_charset'] ) ),
			"out_trade_no" => $out_trade_no,
			"subject" => $subject,
			'app_pay'=>'Y',
			"total_fee" => $total_fee,
			"show_url" => $show_url,
			"body" => $body
			// 其他业务参数根据在线开发文档，添加参数.文档地址:https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.2Z6TSk&treeId=60&articleId=103693&docType=1
			// 如"参数名" => "参数值" 注：上一个参数末尾需要“,”逗号。
	);

		
		
	// 建立请求
	$alipaySubmit = new AlipaySubmit ( $alipay_config );
	$html_text = $alipaySubmit->buildRequestUrl ( $parameter);
	wp_redirect($html_text);exit;
}