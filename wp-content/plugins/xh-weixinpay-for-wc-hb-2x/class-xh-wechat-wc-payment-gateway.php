<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once 'abstract-xh-wechat-wc-payment-gateway.php';
class HX_Wechat_WC_Payment_Gateway extends Abstract_HX_Wechat_WC_Payment_Gateway{	
	private $instructions;
	
	public function __construct(){
		$this->id = 'xh_weixinpay_for_wc';
		$this->supports[]='refunds';
		$this->icon = XH_WECHAT_URL. '/images/logo.png';
		$this->method_title=__('Wechat Pay',XH_WECHAT);
		$this->method_description=__('WooCommerce WeChat Pay Plugin helps to add WeChat Pay payment gateway that supports the features including QR code payment, OA native payment, refund to WeChat wallet, exchange rate, automatic login, red envelop promotion and red envelop cashback. ',XH_WECHAT);
		
		$this->init_form_fields ();
		$this->init_settings ();
		$this->wechat_init();
		
		$this->title = $this->get_option ( 'title' );
		$this->description = $this->get_option ( 'description' );
		$this->instructions  = $this->get_option( 'instructions');
		
		$this->enabled =$this->get_option('enabled');
		if('yes'==$this->get_option('disabled_in_mobile_browser')){
			if(XH_Wechat_Url::isWebApp()&&!XH_Wechat_Url::isWeixinClient()){
				$this->enabled='no';
			}
		}
		
		if(!$GLOBALS[XH_Wechat_Api::ID]){
		    $this->enabled = 'no';
		}
		
		$this->section =version_compare(WC()->version, '2.6.0','<')?strtolower( get_class( $this ) ):$this->id;
	}
	
	public function woocommerce_payment_gateways($methods){
	    $methods[]=$this ;
	    return $methods;
	}

	public function thankyou_page($order_id) {
		if ( $this->instructions ) {
			echo wpautop( wptexturize( $this->instructions ) );
		}
	}

	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method ) {
			echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
		}
	}
	
	public function is_available() {
		return $this->enabled;
	}

	function init_form_fields() {
		$order_status =$this->wc_get_order_statuses();
		$statuses =array();
		if($order_status){
			foreach ($order_status as $key=>$status){
				if($key=='wc-pending'){continue;}
				$statuses[$key]=$status;
			}
		}
		
		$this->form_fields =array (
				'enabled' => array (
						'title' => __('enable',XH_WECHAT),
						'type' => 'checkbox',
						'label' =>__('enable wechat payment',XH_WECHAT),
						'default' => 'no',
						'section'=>'default'
				),
				'title' => array (
						'title' => __('Payment gateway name',XH_WECHAT),
						'type' => 'text',
						'default' =>  __('Wechat Pay',XH_WECHAT),
						'desc_tip' => true,
						'css' => 'width:400px',
						'section'=>'default'
				),
				'description' => array (
						'title' => __('Payment gateway description',XH_WECHAT),
						'type' => 'textarea',
						'default' => __('QR code payment or OA native payment, credit card',XH_WECHAT),
						'desc_tip' => true,
						'css' => 'width:400px',
						'section'=>'default'
				),
				'instructions' => array(
					'title'       => __( 'Instructions', 'woocommerce' ),
					'type'        => 'textarea',
					'css' => 'width:400px',
					'description' => __( 'Instructions that will be added to the thank you page.', 'woocommerce' ),
					'default'     => '',
					'section'=>'default'
				),
				'xh_weixinpay_for_wc_appID' => array (
						'title' => __('App ID (Application ID)',XH_WECHAT),
						'type' => 'text',
						'description' =>__('Application ID can be found at the "developer center" column of WeChat public platform',XH_WECHAT),
						'css' => 'width:400px',
						'section'=>'default'
				),
				'xh_weixinpay_for_wc_appSecret' => array (
						'title' => __('App Key',XH_WECHAT),
						'type' => 'text',
						'description' => __('Key can be found at the "developer center" column of WeChat public platform',XH_WECHAT),
						'css' => 'width:400px',
						'section'=>'default'
				),
	
				'xh_weixinpay_for_wc_mchId' => array (
						'title' => __( 'WeChat Pay Merchant ID',XH_WECHAT),
						'type' => 'text',
						'description' => __('Please enter your merchant ID. If you do not have one, please click <a target="_blank" href="https://pay.weixin.qq.com/"> to get</a>',XH_WECHAT),
						'css' => 'width:400px',
						'section'=>'default'
				),
				'xh_weixinpay_for_wc_key' => array (
						'title' =>  __( 'WeChat Pay Merchant Key',XH_WECHAT),
						'type' => 'text',
						'description' =>__(  'The "account setting"--"account security"--"set API key" at WeChat merchant\'s platform. Editable but not viewable ',XH_WECHAT),
						'css' => 'width:400px',
						'desc_tip' => false,
						'section'=>'default'
				),
				'xh_weixinpay_for_wc_wx_address' => array (
						'title' => __( 'enable',XH_WECHAT),
						'label'=> __('Enable WeChat shipping address',XH_WECHAT),
						'type' => 'checkbox',
						'default' => 'no',
						'description' => __( 'Add the modifiable default button style of <code>.btn-wechat-pay{ }</code> at theme style',XH_WECHAT),
						'section'=>'default'
				),
				'disabled_in_mobile_browser' => array (
						'title' =>  __( 'enable',XH_WECHAT),
						'label'=>'在手机浏览器中禁用微信支付',
						'type' => 'checkbox',
						'default' => 'no',
						'section'=>'default'
				),
				'exchange_rate' => array (
					'title' => __( 'Exchange rate setting',XH_WECHAT),
					'type' => 'text',
					'default'=>1,
					'description' => __(  'Set the exchange rate to RMB. When it is RMB, the default is 1',XH_WECHAT),
					'css' => 'width:400px;',
					'section'=>'default'
				)
				//===============红包设置======================
				
				,
				'hb_eabled_share' => array (
						'title' => __('enable',XH_WECHAT),
						'type' => 'checkbox',
						'label' => __('Red envelop promotion: To share the product information to the circle of friends. Once the product is ordered, the sharer will get red envelop. The more share, the more possible red envelop.',XH_WECHAT),
						'default' => 'no',
						'section'=>'hb',
						'sub_section'=>'s',
				),
				'hb_fx_mch_name' => array (
						'title' =>   __('Merchant Name',XH_WECHAT),
						'type' => 'text',
						'default'=>get_option('blogname'),
						'description' =>  __('【Required】The merchant name is required while sending red envelop. Your can enter your website name (6 Chinese characters at most)',XH_WECHAT),
						'css' => 'width:400px',
						'desc_tip' => false,
						'section'=>'hb',
						'sub_section'=>'s',
				),
				'hb_fx_act_name' => array (
						'title' =>  __('Activity Name',XH_WECHAT) ,
						'type' => 'textarea',
						'default'=> __('Share and get rewarded',XH_WECHAT),
						'description' => __( '【Required】The activity name is required while sending red envelop (6 Chinese characters at most).',XH_WECHAT),
						'css' => 'width:400px',
						'desc_tip' => false,
						'section'=>'hb',
						'sub_section'=>'s',
				),
				'hb_fx_wishing' => array (
						'title' =>  __('Greetings',XH_WECHAT) , 
						'type' => 'textarea',
						'default'=> __('Thanks for your time',XH_WECHAT) ,
						'description' =>  __('【Required】The greetings are required while sending red envelop (20 Chinese characters at most).',XH_WECHAT) ,
						'css' => 'width:400px',
						'desc_tip' => false,
						'section'=>'hb',
						'sub_section'=>'s',
				),
				//===============红包设置======================
				
				'hb_eabled_ordered' => array (
						'title' =>  __('enable',XH_WECHAT),
						'type' => 'checkbox',
						'default' => 'no',
						'label' => __('Red Envelop Cashback: the cashback of red envelop will be available once the order is done successfully for the promotion and encouragement',XH_WECHAT),
						'section'=>'hb',
						'sub_section'=>'o',
				),
				'hb_by_status' => array (
						'title' => __('Order Status',XH_WECHAT),
						'type' => 'select',
						'default'=>'wc-processing',
						'description' => __('The red envelop is triggered when the order is selected',XH_WECHAT),
						'css' => 'min-width:400px;',
						'options'=>$statuses,
						'section'=>'hb',
						'sub_section'=>'o',
				),
				'hb_by_mch_name' => array (
						'title' =>   __('Merchant Name',XH_WECHAT),
						'type' => 'text',
						'default'=>get_option('blogname'),
						'description' =>  __('【Required】The merchant name is required while sending red envelop. Your can enter your website name (6 Chinese characters at most)',XH_WECHAT),
						'css' => 'width:400px',
						'desc_tip' => false,
						'section'=>'hb',
						'sub_section'=>'o',
				),
				
				'hb_by_act_name' => array (
						'title' =>   __('Activity Name',XH_WECHAT),
						'type' => 'textarea',
						'default'=> __('Order Cashback',XH_WECHAT),
						'description' =>  __('【Required】The activity name is required while sending red envelop (6 Chinese characters at most).',XH_WECHAT),
						'css' => 'width:400px',
						'desc_tip' => false,
						'section'=>'hb',
						'sub_section'=>'o',
				),
				'hb_by_wishing' => array (
						'title' =>   __('Greetings',XH_WECHAT),
						'type' => 'textarea',
						'default'=> __('Thanks for your time',XH_WECHAT),
						'description' => __( '【Required】The greetings are required while sending red envelop (20 Chinese characters at most).',XH_WECHAT),
						'css' => 'width:400px',
						'desc_tip' => false,
						'section'=>'hb',
						'sub_section'=>'o',
				),
				
				//-=-=-=-=-=-=-=-=-=-=-微信OAuth2.0网页授权=-=-=-=-=-=-=-=-=-=-=-=
				'xh_weixinpay_for_wc_weixin_auth' => array (
						'title' => '微信登录',
						'type' => 'select',
						'description' =>__('仅对(home、category(tag)、author、single post)页面进行登录检查(避免不可预测的错误)。',XH_WECHAT),
						'options'=>array(
								0=> __('Disabled',XH_WECHAT),
                               1=> __('Enable wechat auto login in wechat client',XH_WECHAT),
                                2=> __('Enable wechat auto login in wechat client,desktop forbidden.',XH_WECHAT),
						),
						'css' => 'width:400px',
						'section'=>'auth',
						'sub_section'=>'a',
				        
				),
				'auth_enabled' => array (
						'title' => __('enable',XH_WECHAT),
						'type' => 'checkbox',
						'label' =>'微信OAuth2.0网页授权多域名模式',
						'default' => 'no',
						'section'=>'auth',
						'sub_section'=>'b',
				),
				'wechat_remote_action' => array (
						'title' =>'公用回调页面',
						'type' => 'textarea',
						'css' => 'width:400px',
						'description'=>'详情设置查看<a target="_blank" href="http://www.wpweixin.net/blog/192.html#auth2">帮助文档</a>',
						'section'=>'auth',
						'sub_section'=>'b',
				)
			);
	}
	
	public function process_refund( $order_id, $amount = null, $reason = ''){
		$order = new WC_Order ($order_id );
		if(!$order){
			return new WP_Error( 'invalid_order', __('Wrong Order',XH_WECHAT) );
		}
	
		$trade_no =$order->get_transaction_id();
		if (empty ( $trade_no )) {
			return new WP_Error( 'invalid_order', __('The WeChat transaction ID is not found or the order is unpaid' ,XH_WECHAT) );
		}
	
		$total = $order->get_total ();
			
		$preTotal = $total;
		$preAmount = $amount;
		
		if (! in_array (  get_woocommerce_currency(), array (
				'RMB',
				'CNY'
		) )) {
			$exchange_rate = floatval($this->get_option('exchange_rate'));
			if($exchange_rate<=0){
				$exchange_rate=1;
			}
			
			$total = round ( $total * $exchange_rate, 2 );
			$amount = round ( $amount * $exchange_rate, 2 );
		}
		
		$total = apply_filters('xh_woocommerce_payment_gateway_exchange_rate', $total,$preTotal,$this);
		$amount = apply_filters('xh_woocommerce_payment_gateway_exchange_rate', $amount,$preAmount,$this);
		
		$amount=(int)($amount*100);
		$total=(int)($total*100);
		
		if($amount<=0||$amount>$total){
			return new WP_Error( 'invalid_order',__('Invalid Amount ' ,XH_WECHAT) );
		}
	
		$transaction_id = $trade_no;
		$total_fee = $total;
		$refund_fee = $amount;
	
		$input = new XH_WECHAT_WxPayRefund ();
		$input->SetTransaction_id ( $transaction_id );
		$input->SetTotal_fee ( $total_fee );
		$input->SetRefund_fee ( $refund_fee );
	
		$input->SetOut_refund_no ( md5($order->id.time()));
		$input->SetOp_user_id ( XH_Wx_Pay_Config::$MCHID );
	
		try {
			$result = XH_Wx_Pay_Api::refund ( $input );
			if ($result ['result_code'] == 'FAIL' || $result ['return_code'] == 'FAIL') {
				XH_Log::DEBUG ( " XHWxPayApi::orderQuery:" . json_encode ( $result ) );
				throw new Exception ("return_msg:". $result ['return_msg'].';err_code_des:'. $result ['err_code_des'] );
			}
				
		} catch ( Exception $e ) {
			return new WP_Error( 'invalid_order',$e->getMessage ());
		}
	
		return true;
	}
	
	public function process_payment($order_id){
		$order = new WC_Order($order_id);
		if(!$order||!$order->needs_payment()){
			return array(
				'result' => 'success',
				'redirect' => $this->get_return_url($order)
			);
		}
		
		if(!XH_Wechat_Url::isWeixinClient()){
			return array(
					'result' => 'success',
					'redirect' => $order->get_checkout_payment_url(true)
			);
		}else{
			return array(
					'result' => 'success',
					'redirect' => XH_Wechat_Url::pay_weixin($order_id,wc_get_checkout_url())
			);
		}
	}
	
	
	
	public function woocommerce_receipt($order_id){
		$order = new WC_Order ( $order_id );
		if(!$order||!$order->needs_payment()){
			wp_redirect($this->get_return_url($order));
			exit;
		}
		
		if(XH_Wechat_Url::isWeixinClient()){
		    wp_redirect(XH_Wechat_Url::pay_weixin($order_id,wc_get_checkout_url()));
		    exit;
		}
		
		$input = new XH_WECHAT_WxPayUnifiedOrder ();
		$input->SetBody ($this->get_order_title($order) );
		$input->SetAttach($order_id);
		$input->SetOut_trade_no (  $this->guid());
		
		$total =  $order->get_total ();
		$preTotal = $total;
		
		if (! in_array (  get_woocommerce_currency(), array (
				'RMB',
				'CNY'
		) )) {
			$exchange_rate = floatval($this->get_option('exchange_rate'));
			if($exchange_rate<=0){
				$exchange_rate=1;
			}
			
			$total = round ( $total * $exchange_rate, 2 );
		}
		
		$totalFee = apply_filters('xh_woocommerce_payment_gateway_exchange_rate', $total,$preTotal,$this);			
		$totalFee=( int )($totalFee*100);
		
		$input->SetTotal_fee ( $totalFee );			
		$date = new DateTime ();
		$date->setTimezone ( new DateTimeZone ( 'Asia/Shanghai' ) );
		$startTime = $date->format ( 'YmdHis' );
		$expiredTime = $startTime + 600;
		
		$input->SetTime_start ( $startTime );
		//$input->SetTime_expire ( $expiredTime );
		
		$input->SetNotify_url ( XH_Wechat_Url::weixin_pay_notify() );
		$input->SetTrade_type ( "NATIVE" );
		$input->SetProduct_id ( $order_id );
		
		$error_msg=null;
		try {
			$result = XH_Wx_Pay_Api::unifiedOrder ( $input, 60 );
		}catch (Exception $e) {
			XH_Log::DEBUG ( " XHWxPayApi::orderQuery:" . $e->getMessage() );
			$error_msg=$e->getMessage();
		}
		
		if(!$error_msg){
			if($result['result_code']=='FAIL'||$result['return_code']=='FAIL'){
				XH_Log::DEBUG ( " XHWxPayApi::orderQuery:" . json_encode ( $result ) );
				$error_msg="return_msg:".$result['return_msg']." ;err_code_des: ".$result['err_code_des'];
			}	
		}
		
		$qrUrl=$result&&isset($result ["code_url"])?$result ["code_url"]:'';
		
		?><p><?php print __('Please use WeChat QR code to do the payment ' ,XH_WECHAT)?></p>
		<script src="<?php print XH_WECHAT_URL?>/assets/qrcode.js"></script>		
		<div id="WxQRCode" style="width:200px;height:200px" ></div>
		<span style="display:none;"><?php print $error_msg?></span>
		<script type="text/javascript">
				(function () {
				    function queryOrderStatus() {
					    var $=jQuery;
					    if(!$){return;}
				        $.ajax({
				            type: "GET",
				            url: '<?php print admin_url( 'admin-ajax.php' )?>',
				            data: {
				                orderId: '<?php print $order_id;?>',
				                action: '<?php print self::HX_Wechat_LoopOrderStatus; ?>'
				            },
				            timeout:6000,
				            cache:false,
				            dataType:'json',
				            async:true,
				            success:function(data){
				                if (data && data.status === "paid") {
				                    location.href = data.message;
				                    return;
				                }
				                
				                setTimeout(queryOrderStatus, 2000);
				            },
				            error:function(){
				            	setTimeout(queryOrderStatus, 2000);
				            }
				        });
				    }

				    var qrcode = new QRCode(document.getElementById("WxQRCode"), {
	    	            width : 282,
	    	            height : 282
	    	        });

	    	        <?php if(!empty($qrUrl)){
	    	            ?>
	    	            qrcode.makeCode("<?php print $qrUrl?>");
	    	            
	    	            setTimeout(function(){
	    	            	queryOrderStatus();
		    	        },3000);
			          <?php 
	    	        }?>
				})();
		</script>
		<?php 
	}
	
	public function woocommerce_checkout_billing() {
		// 判断是否是微信客户端
		if (! XH_Wechat_Url::isWeixinClient()) {
			return;
		}
		
		if ( 'yes'==$this->get_option('xh_weixinpay_for_wc_wx_address')) {
			?>
			<p class="form-row form-row form-row-first" style="width:100%;margin-bottom:10px;">
				<label><?php print __('WeChat Shipping Address' ,XH_WECHAT)?></label>
				<a style="text-align:center;"  class="button" href="<?php print XH_Wechat_Url::weixin_address()?>"><?php print __('Synchronize Now' ,XH_WECHAT)?></a>
			</p>
			<?php
		}
	}
}