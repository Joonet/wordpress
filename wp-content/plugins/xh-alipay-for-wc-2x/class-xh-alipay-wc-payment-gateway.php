<?php
if (! defined ( 'ABSPATH' ))
	exit (); // Exit if accessed directly
class HX_Alipay_WC_Payment_Gateway extends WC_Payment_Gateway {
	private $instructions;
	
	public function __construct() {
		$this->id = 'xh_alipay_for_wc';
		array_push($this->supports,'refunds');
		$this->icon = XH_ALIPAY_URL . '/images/logo.png';
		$this->has_fields = false;
		$this->method_title ='支付宝';
		$this->method_description='WooCommerce支付宝插件全平台版是一款基于WooCommerce购物车插件的子插件，让WooCommerce增加支付宝扫码支付和手机网站支付的功能，适合任意WooCommerce主题，此插件对WooCommerce兼容良好，自适应PC网站和手机网站，自动选择相应的支付方式，后期我们会在此插件上陆续开发基于口碑网的支付宝应用。';
		
		$this->init_form_fields ();
		$this->init_settings ();
		
		$this->title = $this->get_option ( 'title' );
		$this->description = $this->get_option ( 'description' );
		$this->instructions  = $this->get_option( 'instructions');
		$this->enabled =$this->get_option('enabled');
		//在微信中判断是否禁用
		if(XH_Alipay_Url::isWeixinClient()){
			$disabled = $this->get_option('xh_alipay_for_wc_disabled_in_wechat');
			if($disabled=='yes'){
				$this->enabled = 'no';
			}
		}
		
		if(!$GLOBALS[XH_Alipay_Api::ID]){
		    $this->enabled = 'no';
		}
	}
	
	public function thankyou_page() {
		if ( $this->instructions ) {
			echo wpautop( wptexturize( $this->instructions ) );
		}
	}

	public function email_instructions( $order, $sent_to_admin, $plain_text = false ) {
		if ( $this->instructions && ! $sent_to_admin && $this->id === $order->payment_method ) {
			echo wpautop( wptexturize( $this->instructions ) ) . PHP_EOL;
		}
	}
	
	public function order_item_add_line_buttons(){
	    global $post;
	    if(!$post||$post->post_type!='shop_order'){
	        return;
	    }
	
	    $order = new WC_Order($post);
	    if($order->payment_method!=$this->id){
	        return;
	    }
	
	    $protocol = (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
	    $callback= $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	    ?>
			<form id="form-onAlipay-refund" action="<?php print XH_Alipay_Url::refund()?>" method="post">
				<input type="hidden" name="order_id" value="<?php print $order->id?>"/>
				<input type="hidden" name="callback" value="<?php print esc_attr(urlencode($callback))?>"/>
				<input type="hidden" name="amount"  id="form-onAlipay-amount"/>
				<input type="hidden" name="reason" id="form-onAlipay-reason"/>
			</form>
			<script type="text/javascript">
				(function($){
					var $btn_redund=$('.refund-actions button.do-api-refund');
					if($btn_redund.length>0){
						var html = $btn_redund.html();
						$btn_redund.remove();
						$('.refund-actions').prepend('<button type="button" onclick="window.onAlipay.refund(<?php print $order->id;?>);" class="button button-primary">'+html+'</button>');
					}
	
					if(typeof window.onAlipay=='undefined'){window.onAlipay={};}
	
					window.onAlipay.block=function(){
						$( '#woocommerce-order-items' ).block({
							message: null,
							overlayCSS: {
								background: '#fff',
								opacity: 0.6
							}
						});
					}
	
					window.onAlipay.unblock=function(){
						$( '#woocommerce-order-items' ).unblock();
					}

					window.onAlipay.refund=function(order_id){
						if ( window.confirm( woocommerce_admin_meta_boxes.i18n_do_refund ) ) {
							var refund_amount = $( 'input#refund_amount' ).val();
							var refund_reason = $( 'input#refund_reason' ).val();

							// Get line item refunds
							var line_item_qtys       = {};
							var line_item_totals     = {};
							var line_item_tax_totals = {};

							$( '.refund input.refund_order_item_qty' ).each(function( index, item ) {
								if ( $( item ).closest( 'tr' ).data( 'order_item_id' ) ) {
									if ( item.value ) {
										line_item_qtys[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ] = item.value;
									}
								}
							});

							$( '.refund input.refund_line_total' ).each(function( index, item ) {
								if ( $( item ).closest( 'tr' ).data( 'order_item_id' ) ) {
									line_item_totals[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ] = accounting.unformat( item.value, woocommerce_admin.mon_decimal_point );
								}
							});

							$( '.refund input.refund_line_tax' ).each(function( index, item ) {
								if ( $( item ).closest( 'tr' ).data( 'order_item_id' ) ) {
									var tax_id = $( item ).data( 'tax_id' );

									if ( ! line_item_tax_totals[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ] ) {
										line_item_tax_totals[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ] = {};
									}

									line_item_tax_totals[ $( item ).closest( 'tr' ).data( 'order_item_id' ) ][ tax_id ] = accounting.unformat( item.value, woocommerce_admin.mon_decimal_point );
								}
							});
							window.onAlipay.block();
							var data = {
								action:                 'woocommerce_refund_line_items',
								order_id:               woocommerce_admin_meta_boxes.post_id,
								refund_amount:          refund_amount,
								refund_reason:          refund_reason,
								line_item_qtys:         JSON.stringify( line_item_qtys, null, '' ),
								line_item_totals:       JSON.stringify( line_item_totals, null, '' ),
								line_item_tax_totals:   JSON.stringify( line_item_tax_totals, null, '' ),
								api_refund:             $( this ).is( '.do-api-refund' ),
								restock_refunded_items: $( '#restock_refunded_items:checked' ).size() ? 'true' : 'false',
								security:               woocommerce_admin_meta_boxes.order_item_nonce
							};

							$.post( woocommerce_admin_meta_boxes.ajax_url, data, function( response ) {
								if ( true === response.success ) {
									
									var refund_amount = $( 'input#refund_amount' ).val();
									var refund_reason = $( 'input#refund_reason' ).val();
			
									$('#form-onAlipay-amount').val(refund_amount);
									$('#form-onAlipay-reason').val(refund_reason);
									//window.onAlipay.block();
									$('#form-onAlipay-refund').submit();
									
// 									if ( 'fully_refunded' === response.data.status ) {
// 										// Redirect to same page for show the refunded status
// 										window.location.href = window.location.href;
// 									}
								} else {
									window.alert( response.data.error );
									window.onAlipay.unblock();
								}
							});
						} else {
							window.onAlipay.unblock();
						}
					};
				})(jQuery);
			</script>
			<?php 
		}

	/**
	 * Add the gateway to WooCommerce
	 *
	 * @access public
	 * @param array $methods
	 * @package WooCommerce/Classes/Payment
	 * @return array
	 */
	public function woocommerce_payment_gateways($methods) {
		$methods [] = $this;
		return $methods;
	}
	
	public function plugin_action_links($links) {
	    if($GLOBALS[Abstract_XH_Alipay_Api::ID])
		return array_merge ( array (
				'settings' => '<a href="' . admin_url ( 'admin.php?page=wc-settings&tab=checkout&section='.$this->id ) . '">设置</a>'
		), $links );
		return array_merge ( array (
		    'settings' => '<a href="' . admin_url ( 'admin.php?page=woo_alipay_license') . '">设置</a>'
		), $links );
	}
	
	public function get_order_title($order, $limit = 32, $trimmarker = '...') {
		$title ="#{$order->id}";
		
		$order_items = $order->get_items();
		if($order_items){
		    $qty = count($order_items);
		    foreach ($order_items as $item_id =>$item){
		        $title.="|{$item['name']}";
		        break;
		    }
		    if($qty>1){
		        $title.='...';
		    }
		}
		
		$title = mb_strimwidth($title, 0, $limit);
		return apply_filters('xh-payment-get-order-title', $title,$order);
	}
	
	public function process_payment($order_id) {
		$order = new WC_Order ( $order_id );
		if(!$order||!$order->needs_payment()){
			return array (
					'result' => 'success',
					'redirect' => $this->get_return_url($order)
			);
		}
		
		if(XH_Alipay_Url::isWeixinClient()){
			return array (
				'result' => 'success',
				'redirect' => XH_ALIPAY_URL.'/views/alipay-in-wechat.php?order_ID='.$order_id
			);
		}
		
		$total = $order->get_total ();
		$preTotal = $total;
		
		if (! in_array (get_woocommerce_currency(), array (
				'RMB',
				'CNY' 
		) )) {
			$exchange_rate = floatval($this->get_option('exchange_rate'));
			if($exchange_rate<=0){$exchange_rate=1;}
			$total = round ( $total * $exchange_rate, 2 );
		}

		$totalFee = apply_filters('xh_woocommerce_payment_gateway_exchange_rate', $total,$preTotal,$this);
		$html_text='';
		
		if (XH_Alipay_Url::isWebApp ()) {
			require_once (XH_ALIPAY_DIR . "/alipay/alipaywapdirect/alipay.config.php");
			require_once (XH_ALIPAY_DIR . "/alipay/alipaywapdirect/lib/alipay_submit.class.php");
			// 商户订单号，商户网站订单系统中唯一订单号，必填
			$out_trade_no = $order_id;
			$subject =$this->get_order_title($order);
			$total_fee = $totalFee;
			$show_url = $order->get_view_order_url ();
			$body = '';
			global $alipay_config;
			// 构造要请求的参数数组，无需改动
			$parameter = array (
					"service" => $alipay_config ['service'],
					"partner" => $alipay_config ['partner'],
					"seller_id" => $alipay_config ['seller_id'],
					"payment_type" => $alipay_config ['payment_type'],
					"notify_url" => $alipay_config ['notify_url'],
					"return_url" => $this->get_return_url($order),
					"_input_charset" => trim ( strtolower ( $alipay_config ['input_charset'] ) ),
					"out_trade_no" => $out_trade_no,
					"subject" => $subject,
					"total_fee" => $total_fee,
					'app_pay'=>'Y',
					"show_url" => $show_url,
					"body" => $body 
			);
			$alipaySubmit = new AlipaySubmit ( $alipay_config );
			$html_text = $alipaySubmit->buildRequestUrl ( $parameter);
		} else {
			// pc
			require_once (XH_ALIPAY_DIR."/alipay/alipaydirect/alipay.config.php");
			require_once (XH_ALIPAY_DIR."/alipay/alipaydirect/lib/alipay_submit.class.php");
			$out_trade_no = $order_id;
			$subject =$this->get_order_title($order);
			$total_fee = $totalFee;
			$show_url = $order->get_view_order_url ();
			$body = '';
			$parameter = array (
					"service" => $alipay_config ['service'],
					"partner" => $alipay_config ['partner'],
					"seller_id" => $alipay_config ['seller_id'],
					"payment_type" => $alipay_config ['payment_type'],
					"notify_url" => $alipay_config ['notify_url'],
					"return_url" => $this->get_return_url($order),
					
					"anti_phishing_key" => $alipay_config ['anti_phishing_key'],
					"exter_invoke_ip" => $alipay_config ['exter_invoke_ip'],
					"out_trade_no" => $out_trade_no,
					"subject" => $subject,
					"total_fee" => $total_fee,
					"body" => $body,
					"_input_charset" => trim ( strtolower ( $alipay_config ['input_charset'] ) ) 
			);
			
			$alipaySubmit = new AlipaySubmit ( $alipay_config );
			$html_text = $alipaySubmit->buildRequestUrl ( $parameter);		
		}
		return array (
				'result' => 'success',
				'redirect' => $html_text
		);
	}
	
	public function process_refund( $order_id, $amount = null, $reason = ''){
		return new WP_Error('notice',__('Refresh the page and try again!',XH_ALIPAY));
	}
	
	function is_available() {
		return $this->enabled;
	}
	
	/**
	 * Initialise Gateway Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields() {
		$this->form_fields = array (
				'enabled' => array (
						'title' => __ ( 'Enable/Disable', XH_ALIPAY ),
						'type' => 'checkbox',
						'label' => __ ( 'Enable Alipay', XH_ALIPAY ),
						'default' => 'no' 
				),
				'title' => array (
						'title' => __ ( 'Title', XH_ALIPAY ),
						'type' => 'text',
						'description' => __ ( 'Payment gateway will be shown at the checkout.', XH_ALIPAY ),
						'default' => '支付宝',
						'required' => true,
						'desc_tip' => false ,
						'css' => 'width:400px'
				),
				'description' => array (
						'title' => __ ( 'Description', XH_ALIPAY ),
						'type' => 'textarea',
						'description' => __ ( 'Payment gateway will be shown at the checkout.', XH_ALIPAY ),
						'default' =>'熟悉的支付宝，安全的保证，你懂的',
						'desc_tip' => false ,
						'css' => 'width:400px',
				),
				'instructions' => array(
					'title'       => __( 'Instructions', 'woocommerce' ),
					'type'        => 'textarea',
					'css' => 'width:400px',
					'description' => __( 'Instructions that will be added to the thank you page.', 'woocommerce' ),
					'default'     => ''
				),
				'xh_alipay_for_wc_appID' => array (
						'title' => __ ( 'Merchant ID', XH_ALIPAY ),
						'type' => 'text',
						'description' => '<a href="https://b.alipay.com/order/pidAndKey.htm" target="_blank">' . __ ( 'Click here to get', XH_ALIPAY ) . '</a>',
						'required' => true,
						'css' => 'width:400px' 
				),
				'xh_alipay_for_wc_key' => array (
						'title' => __ ( 'KEY', XH_ALIPAY ),
						'type' => 'text',
						'description' => '<a href="https://b.alipay.com/order/pidAndKey.htm" target="_blank">' . __ ( 'Click here to get', XH_ALIPAY ) . '</a>',
						'css' => 'width:400px',
						'required' => true,
						'desc_tip' => false 
				),
				'xh_alipay_for_wc_disabled_in_wechat' => array (
						'title' => __ ( 'Disabled in WeChat', XH_ALIPAY ),
						'type' => 'checkbox',
						'label' => __ ( 'Disabled in WeChat', XH_ALIPAY ),
						'default' => 'no',
						'description' => '' 
				),
				'exchange_rate' => array (
						'title' => __ ( 'Exchange Rate', XH_ALIPAY ),
						'type' => 'text',
						'default'=>'1',
						'description' => '请设置对中国人民币汇率(默认为1)。如果你的货币是美元,那么你应该输入6.19',
						'css' => 'width:80px;',
						'required' => true,
						'desc_tip' => false
				)  
		);
	}
}

?>
