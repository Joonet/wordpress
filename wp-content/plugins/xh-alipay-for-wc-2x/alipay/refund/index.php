<?php 
define('XH_LOGIN_IGNORE', true);
define('WP_USE_THEMES', false);
$root_path =rtrim($_SERVER['DOCUMENT_ROOT'],'/');
if(file_exists($root_path.'/wp-load.php')){
    require_once $root_path.'/wp-load.php';
}else if(file_exists('../../../../../wp-load.php')){
    require_once     '../../../../../wp-load.php';
}else{
    wp_die('无法加载wp-load.php');
}
?>
<!DOCTYPE html>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<title>支付宝即时到账批量退款有密接口接口</title>
</head>
<?php
/*
 * 功能：即时到账批量退款有密接口接入页 版本：3.4 修改日期：2016-03-08 说明： 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。 ************************注意************************* 如果您在接口集成过程中遇到问题，可以按照下面的途径来解决 1、开发文档中心（https://doc.open.alipay.com/doc2/detail.htm?spm=a219a.7629140.0.0.oxen1k&treeId=66&articleId=103600&docType=1） 2、商户帮助中心（https://cshall.alipay.com/enterprise/help_detail.htm?help_id=473888） 3、支持中心（https://support.open.alipay.com/alipay/support/index.htm） 如果不想使用扩展功能请把扩展功能参数赋空值。
 */
require_once (XH_ALIPAY_DIR . "/alipay/refund/alipay.config.php");
require_once (XH_ALIPAY_DIR . "/alipay/refund/lib/alipay_submit.class.php");

/**
 * ************************请求参数*************************
 */

$order_id = $_POST['order_id'];
$amount = floatval($_POST['amount']);
$reason = trim($_POST['reason']);

global $alipay_config;

$order = new WC_Order($order_id);
if(!$order){
	wp_die( __ ( 'Invalid order!', XH_ALIPAY ),'invalid_order' );
	exit;
}


$total = $order->get_total();
if($amount<=0||$amount>$total){
	wp_die( __ ( 'Refund amount is invalid!', XH_ALIPAY ),'invalid_order' );
	exit;
}

if (! in_array (get_woocommerce_currency(), array (
		'RMB',
		'CNY'
) )) {
	$alipayOptions = get_option ( 'woocommerce_xh_alipay_for_wc_settings' );
	if(!isset($alipayOptions['exchange_rate'])){
		$alipayOptions['exchange_rate']=1;
	}
	$exchange_rate = floatval($alipayOptions['exchange_rate']);
	if($exchange_rate<=0){$exchange_rate=1;}
	
	$amount = round ( $amount * $exchange_rate, 2 );
	$total = round ( $total * $exchange_rate, 2 );
}

$trade_no=$order->get_transaction_id(); 
if(empty($trade_no)){
	$trade_no=get_post_meta ( $order->id, 'XH_ALIPAY_FOR_WC_NO', true );
	if(empty($trade_no)){
		wp_die( __ ( 'Alipay NO is empty,can\'t refund order!', XH_WC_APLPAY_C ) ,'invalid_order' );
		exit;
	}
}
// 批次号，必填，格式：当天日期[8位]+序列号[3至24位]，如：201603081000001

$batch_no =date('YmdHis').(strlen($order->id)>4?substr($order->id, 0,4):$order->id);
// 退款笔数，必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）

$batch_num = 1;
$reason = str_replace('^', '', $reason);
$reason = mb_strimwidth($reason, 0, 100);
// 退款详细数据，必填，格式（支付宝交易号^退款金额^备注），多笔请用#隔开
$detail_data = "$trade_no^$amount^$reason";

/**
 * *********************************************************
 */

// 构造要请求的参数数组，无需改动
$parameter = array (
		"service" => trim ( $alipay_config ['service'] ),
		"partner" => trim ( $alipay_config ['partner'] ),
		"notify_url" => trim ( $alipay_config ['notify_url'] ),
		"seller_user_id" => trim ( $alipay_config ['seller_user_id'] ),
		"refund_date" => trim ( $alipay_config ['refund_date'] ),
		"batch_no" => $batch_no,
		"batch_num" => $batch_num,
		"detail_data" => $detail_data,
		"_input_charset" => trim ( strtolower ( $alipay_config ['input_charset'] ) ) 
)
;

// 建立请求
$alipaySubmit = new AlipaySubmit ( $alipay_config );
$html_text = $alipaySubmit->buildRequestForm ( $parameter, "get", "确认" );

?>
<p id="xh_hd_loading" style="margin: 50px 0 0 0; text-align: center;">
	<img src="<?php print XH_ALIPAY_URL?>/images/loading.gif"
		width="16" height="16" alt="" /> <?php print __('Redirecting to Alipay, please wait...',XH_ALIPAY)?>
</p>
<?php
echo $html_text;

?>
</body>
</html>