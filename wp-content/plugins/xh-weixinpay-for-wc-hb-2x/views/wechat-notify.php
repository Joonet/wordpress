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
class PayNotifyCallBack extends XH_Wx_Pay_Notify {
	// 查询订单
	public function Queryorder($transaction_id) {
		$input = new XH_WECHAT_WxPayOrderQuery ();
		$input->SetTransaction_id ( $transaction_id );
		$result = XH_Wx_Pay_Api::orderQuery ( $input );	
		if (array_key_exists ( "return_code", $result ) && array_key_exists ( "result_code", $result ) && $result ["return_code"] == "SUCCESS" && $result ["result_code"] == "SUCCESS") {
		    if(isset($result['trade_state'])&& $result['trade_state']=='SUCCESS'){
    		    $order_id = $result ['attach']; // 获取本地订单号
    			
    			$order = new WC_Order ( $order_id );
    			if($order->needs_payment()){
    				//XH_Log::DEBUG(json_encode($result));
    				if(!empty($result['openid'])){
    					update_post_meta($order_id, 'openid', $result['openid']);
    					$user_id=method_exists($order, 'get_user_id')?$order->get_user_id():($order->customer_user ? intval( $order->customer_user ) : 0);
    				
    					if($user_id&&$user_id>0){
    						update_user_meta($user_id,'openid', $result['openid']);
    						update_user_meta($user_id,'appid', XH_Wx_Pay_Config::$APPID);
    					}
    				}
    				
    				$order->payment_complete ($result ['transaction_id']);
    			}
    		    
    			return true;
		    }
		}
		return false;
	}
	 
	// 重写回调处理函数
	public function NotifyProcess($data, &$msg) {
		$notfiyOutput = array ();
		
		if (! array_key_exists ( "transaction_id", $data )) {
			throw new XH_Wx_Pay_Exception("输入参数不正确");
			return false;
		}
		// 查询订单，判断订单真实性
		if (! $this->Queryorder ( $data ["transaction_id"] )) {
			throw new XH_Wx_Pay_Exception("订单查询失败");
			return false;
		}
		return true;
	}
}

try {
	$notify = new PayNotifyCallBack ();
	$notify->Handle ( false );
} catch (Exception $e) {
	XH_Log::ERROR($e->getMessage());
	return false;
}

