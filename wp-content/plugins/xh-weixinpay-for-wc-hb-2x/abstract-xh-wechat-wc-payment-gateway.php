<?php
if (! defined('ABSPATH')) {
    exit();
}

if(!class_exists('Abstract_HX_WC_Payment_Gateway')){
    require_once 'abstract-xh-wc-payment-gateway.php';
}
class Abstract_HX_Wechat_WC_Payment_Gateway extends Abstract_HX_WC_Payment_Gateway{
    const HX_Wechat_LoopOrderStatus = 'HX_Wechat_LoopOrderStatus';
    const HX_Wechat_LoopOrderID = 'HX_Wechat_LoopOrderID';
    
    public function post_type_link($post_link='', $post=null, $leavename='', $sample=''){
        if(!XH_Wechat_Url::isWeixinClient()){
            return $post_link;
        }
    
        if(!$post){
            return $post_link;
        }
    
        if($post->post_type!='product'){
            return $post_link;
        }
    
        if('yes'!=$this->get_option('hb_eabled_share')){
            return $post_link;
        }
    
        return $this->generate_share_link($post_link,false);
    }
   public function wc_get_order_statuses() {
        $order_statuses = array(
            'wc-pending'    => _x( 'Pending Payment', 'Order status', 'woocommerce' ),
            'wc-processing' => _x( 'Processing', 'Order status', 'woocommerce' ),
            'wc-on-hold'    => _x( 'On Hold', 'Order status', 'woocommerce' ),
            'wc-completed'  => _x( 'Completed', 'Order status', 'woocommerce' ),
            'wc-cancelled'  => _x( 'Cancelled', 'Order status', 'woocommerce' ),
            'wc-refunded'   => _x( 'Refunded', 'Order status', 'woocommerce' ),
            'wc-failed'     => _x( 'Failed', 'Order status', 'woocommerce' ),
        );
        return apply_filters( 'wc_order_statuses', $order_statuses );
    }
    public function Loop_Order_Status(){
        $order_id = $_GET ['orderId'];
        $order = new WC_Order ( $order_id );
    
        echo json_encode ( array (
            'status' =>!$order|| $order->needs_payment ()?'nPaid':'paid',
            'message' => $this->get_return_url ( $order )
        ) );
        exit;
    }
    
    public function get_order_title($order,$limit=32,$trimmarker='...'){
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
    
    public  function woocommerce_process_product_meta_fx($post_id) {
        if (isset ( $_POST ['__xh_fx_hongbao_per'] )) {
            update_post_meta ( $post_id, '__xh_fx_hongbao_per',  $_POST ['__xh_fx_hongbao_per'] );
        }
        if (isset ( $_POST ['__xh_fx_hongbao_inventory'] )) {
            update_post_meta ( $post_id, '__xh_fx_hongbao_inventory', intval ( $_POST ['__xh_fx_hongbao_inventory'] ) );
        }
    }
    public  function woocommerce_process_product_meta_by($post_id) {
        if (isset ( $_POST ['__xh_by_hongbao_per'] )) {
            update_post_meta ( $post_id, '__xh_by_hongbao_per',  $_POST ['__xh_by_hongbao_per'] );
        }
    
        if (isset ( $_POST ['__xh_by_hongbao_inventory'] )) {
            update_post_meta ( $post_id, '__xh_by_hongbao_inventory', intval ( $_POST ['__xh_by_hongbao_inventory'] ) );
        }
    }
    public  function woocommerce_product_options_general_product_data_fx() {
        global $woocommerce, $post;
        echo '<div class="options_group">
					<h5 style="margin-left:10px;">'.__('Red Envelop Promotion Setting',XH_WECHAT).'</h5>';
    
        woocommerce_wp_text_input ( array (
            'id' => '__xh_fx_hongbao_per',
            'label' => __('Unit Price Per Red Envelop',XH_WECHAT),
            'description' => __('Option 1: Enter the price (from 1 to 100), such as 3.5. Option 2: Enter the price range (from 1 to 200), such as 3 to 10 (the red envelop is rounded and delivered at random)',XH_WECHAT),
            'type' => 'text', // 如果没有这个参数，那么就只是普通的input输入框了
            'custom_attributes' => array (
                'step' => 'any', // 数字输入框的步进，any表示1
                'min' => '0', // 数字输入框的最小值
                'max' => '200',  // 数字输入框的最大值,
                'placeholder'=>'3.5或3-10(RMB)'
            )
        ) );
    
        woocommerce_wp_text_input ( array (
            'id' => '__xh_fx_hongbao_inventory',
            'label' => __('No. of the remaining red envelop',XH_WECHAT),
            'type' => 'number', // 如果没有这个参数，那么就只是普通的input输入框了
            'custom_attributes' => array (
                'step' => 'any', // 数字输入框的步进，any表示1
                'min' => '0', // 数字输入框的最小值
                'max' => '999999'  // 数字输入框的最大值
            )
        ) );
    
        woocommerce_wp_text_input ( array (
            'id' => '__xh_fx_hongbao_sended_qty',
            'label' => __('No. of the red envelop sent',XH_WECHAT),
            'type' => 'number', // 如果没有这个参数，那么就只是普通的input输入框了
            'custom_attributes' => array (
                'step' => 'any', // 数字输入框的步进，any表示1
                'readonly' => 'readonly'
            )
        ) );
        woocommerce_wp_text_input ( array (
            'id' => '__xh_fx_hongbao_sended_amount',
            'label' => __('The amount of the red envelop sent',XH_WECHAT),
            'type' => 'number', // 如果没有这个参数，那么就只是普通的input输入框了
            'custom_attributes' => array (
                'step' => 'any', // 数字输入框的步进，any表示1
                'readonly' => 'readonly'
            )
        ) );
        echo '</div>';
    }
    public  function woocommerce_product_options_general_product_data_by() {
        global $woocommerce, $post;
        echo '<div class="options_group">
					<h5 style="margin-left:10px;">'.__('Red Envelop Upon Order Setting',XH_WECHAT).'</h5>';
    
        woocommerce_wp_text_input ( array (
            'id' => '__xh_by_hongbao_per',
            'label' => __('Unit Price Per Red Envelop',XH_WECHAT),
            'description' => __('Option 1: Enter the price (from 1 to 100), such as 3.5. Option 2: Enter the price range (from 1 to 200), such as 3 to 10 (the red envelop is rounded and delivered at random)',XH_WECHAT),
            'type' => 'text', // 如果没有这个参数，那么就只是普通的input输入框了
            'custom_attributes' => array (
                'step' => 'any', // 数字输入框的步进，any表示1
                'min' => '0', // 数字输入框的最小值
                'max' => '200',  // 数字输入框的最大值,
                'placeholder'=>'3.5或3-10(RMB)'
            )
        ) );
    
        woocommerce_wp_text_input ( array (
            'id' => '__xh_by_hongbao_inventory',
            'label' => __('No. of the remaining red envelop',XH_WECHAT),
            'type' => 'number', // 如果没有这个参数，那么就只是普通的input输入框了
            'custom_attributes' => array (
                'step' => 'any', // 数字输入框的步进，any表示1
                'min' => '0', // 数字输入框的最小值
                'max' => '999999'  // 数字输入框的最大值
            )
        ) );
    
        woocommerce_wp_text_input ( array (
            'id' => '__xh_by_hongbao_sended_qty',
            'label' => __('No. of the red envelop sent',XH_WECHAT),
            'type' => 'number', // 如果没有这个参数，那么就只是普通的input输入框了
            'custom_attributes' => array (
                'step' => 'any', // 数字输入框的步进，any表示1
                'readonly' => 'readonly'
            )
        ) );
    
        woocommerce_wp_text_input ( array (
            'id' => '__xh_by_hongbao_sended_amount',
            'label' => __('The amount of the red envelop sent',XH_WECHAT),
            'type' => 'number', // 如果没有这个参数，那么就只是普通的input输入框了
            'custom_attributes' => array (
                'step' => 'any', // 数字输入框的步进，any表示1
                'readonly' => 'readonly'
            )
        ) );
        echo '</div>';
    }
    
    public function wpuser_authorize($user_ID,$openid,$headimgurl,$nickname){
        wp_cache_delete($user_ID,  'user_meta');
        update_user_meta($user_ID,'openid', $openid);
        update_user_meta($user_ID,'appid', XH_Wx_Pay_Config::$APPID);
        update_user_meta($user_ID, 'Headimgurl', $headimgurl);
    
        wp_set_auth_cookie($user_ID, false);
        return true;
    }
    
    public static function get_HB_price($price_txt){
        $price=0;
        if(!empty($price_txt)){
            $price=0;
            if(strpos($price_txt, '-')!=false){
                //范围价格
                $priceses = explode('-', trim($price_txt));
    
                if(count($priceses)==2){
                    $min = round(floatval(trim($priceses[0])));
                    $max = round(floatval(trim($priceses[1])));
                    if($min>$max){
                        $temp = $max;
                        $min = $max;
                        $max = $temp;
                    }
                    	
                    $price = rand ($min, $max );
                }
            }else{
                //单价格
                $price = floatval($price_txt);
            }
        }
    
        if($price>200){
            $price=200;
        }
    
        if($price<1){
            $price =0;
        }
    
        return $price;
    }
    
    protected function send_hb($order_id,$amount,$openid,$mch_name,$wishing,$act_name,$remark){
        $tools = new XH_Wx_Pay_Js_Api();
        if(empty($order_id)){
            throw new Exception(__('Red envelop upon order is required',XH_WECHAT));
        }
    
        if($amount<1||$amount>200){
            throw new Exception(sprintf(__('The price of red envelop is not legal (￥%s)',XH_WECHAT),$amount));
        }
    
        if(empty($openid)){
            throw new Exception(__('The open ID of the recipient is required',XH_WECHAT));
        }
    
        if(empty($mch_name)){
            throw new Exception(__('Merchant name is required',XH_WECHAT));
        }
        if(empty($wishing)){
            throw new Exception(__('Greetings are required',XH_WECHAT));
        }
    
        if(empty($act_name)){
            throw new Exception(__('Activity name is required',XH_WECHAT));
        }
        if(empty($remark)){
            throw new Exception(__('Remark is required',XH_WECHAT));
        }
    
        $inputObj = array ();
        $inputObj ['order_id'] = $order_id;
        $inputObj ['mch_name'] =mb_strimwidth ( $mch_name, 0, 12, '...' );
        $inputObj ['openid'] = $openid;
        $inputObj ['total_amount'] = intval ( $amount * 100 ); // 分
        $inputObj ['total_num'] = 1;
        $inputObj ['wishing'] =  mb_strimwidth ( $wishing, 0, 30, '...' );
        $inputObj ['act_name'] = mb_strimwidth($act_name, 0,12,'...');
        $inputObj ['remark'] =mb_strimwidth($remark,0, 60,'...');
    
        $result = XH_Wx_Pay_Api::hongbao_single ( $inputObj );
    
        if ($result ['return_code'] != 'SUCCESS' || $result ['result_code'] != 'SUCCESS') {
            throw new Exception('return_msg:'. $result ['return_msg'].';err_code_des:'.$result ['err_code_des']);
        }
    }
    
    public function woocommerce_payment_complete($order_id){
        $order = new WC_Order($order_id);
        if(!$order){
            return;
        }
    
        //未开启分享模式，那么取消操作
        if('yes'!=$this->get_option('hb_eabled_share')){
            return;
        }
    
        $user_id =$order->get_user_id();
        if(!$user_id||$user_id<=0){
            //订单用户不存在，所以无法追寻订单信息
            return;
        }
    
        //willbe null
        $current_order_user = get_user_by('id', $user_id);
        if(!$current_order_user){
            return;
        }
    
        global $wpdb;
        $table_red_envelope = $wpdb->prefix . "xh_wechat_red_envelope_history";
        $table_user_preference = $wpdb->prefix . "xh_wechat_user_preference";
    
        $order_items = $order->get_items ();
    
        //订单内容为空
        if (!$order_items||count($order_items)==0) {
            XH_Log::DEBUG('订单结算时发生异常，订单内容为空。订单ID:'.$order->id);
            return;
        }
    
        foreach ( $order_items as $order_item_id => $order_item ) {
            $product_ID = $order_item ['item_meta'] ['_product_id'] [0];
            //红包价格
            $price_txt = get_post_meta ( $product_ID, '__xh_fx_hongbao_per', true );
            $price = self::get_HB_price($price_txt);
            if ($price < 1) {
                //非法价格或未设置价格，那么取消发红包
                continue;
            }
    
            $inventory = intval ( get_post_meta ( $product_ID, '__xh_fx_hongbao_inventory', true ) );
            if ($inventory <= 0) {
                //红包库存以为空，那么不发红包
                continue;
            }
    
            //获取未使用的邀请函
            $preferrence =$wpdb->get_row("select id,ref_open_id as openid,ref_user_id from $table_user_preference where post_id =$product_ID and user_id=$user_id and used=0 limit 1;");
            if(empty($preferrence)||$preferrence->id<=0){
                continue;
            }
            	
            $sended_qty = intval ( get_post_meta ( $product_ID, '__xh_fx_hongbao_sended_qty', true ) );
            $sended_amount = floatval( get_post_meta ($product_ID, '__xh_fx_hongbao_sended_amount', true ) );
            $HB_product = array(
                'id'=>$product_ID,
                'ref_user_id'=>$preferrence->ref_user_id,
                'openid'=>$preferrence->openid,
                'inventory'=>$inventory,
                'preferrence_id'=>$preferrence->id,
                'amount'=>$price,
                'send_qty'=>$sended_qty,
                'send_amount'=>$sended_amount
            );
    
            $amount = $price;
            if($HB_product==null||$amount<=0){
                continue;
            }
            	
            try {
                $this->send_hb(
                    'ORDER_SHARE_'.$order->id,
                    $amount,
                    $HB_product['openid'],
                    $this->get_option('hb_fx_mch_name'),
                    $this->get_option('hb_fx_wishing'),
                    $this->get_option('hb_fx_act_name'),
                    __('Red envelop promotion',XH_WECHAT));
            } catch (Exception $e) {
                $wpdb->insert($table_red_envelope, array(
                    'user_id'=>$HB_product['ref_user_id'],
                    'total_amount'=>$amount,
                    'type'=>'ORDER-SHARE',
                    'success'=>false,
                    'description'=>$e->getMessage(),
                    'order_id'=>$order->id,
                    'created_time'=> date_i18n ( 'Y-m-d H:i:s' )
                ));
                continue;
            }
            	
            	
            $from =$current_order_user->display_name."(ID:".$current_order_user->ID.")";
            	
            $msg =$from.' 的订单';
            $wpdb->insert($table_red_envelope, array(
                'user_id'=>$HB_product['ref_user_id'],
                'total_amount'=>$amount,
                'type'=>'ORDER-SHARE',
                'success'=>true,
                'description'=>$from,
                'order_id'=>$order->id,
                'created_time'=> date_i18n ( 'Y-m-d H:i:s' )
            ));
            	
            //更新数据库内容
            $wpdb->update($table_user_preference, array(
                'used'=>1
            ), array(
                'id'=>$HB_product['preferrence_id']
            ));
            	
            update_post_meta ( $HB_product['id'], '__xh_fx_hongbao_inventory', $HB_product['inventory'] - 1 );
            update_post_meta ( $HB_product['id'], '__xh_fx_hongbao_sended_qty', $HB_product['send_qty'] + 1 );
            update_post_meta ( $HB_product['id'], '__xh_fx_hongbao_sended_amount', $HB_product['send_amount']+$HB_product['amount'] );
        }
    }
    
    //订单成功时，发红包
    public function woocommerce_order_status_changed($order_id, $old_status, $new_status ){
        $order = new WC_Order($order_id);
    
        if(!$order){
            return;
        }
    
        if($order->payment_method!=$this->id){
            return;
        }
    
        //订单支付成功后
        $order_status =$this->get_option('hb_by_status');
        if(empty($order_status)){$order_status='wc-processing';}
    
        $status   = 'wc-' === substr( $order_status, 0, 3 ) ? substr( $order_status, 3 ) : $order_status;
        if(strcasecmp($status,$new_status)!=0){
            return;
        }
    
        if('yes'!=$this->get_option('hb_eabled_ordered')){
            return;
        }
    
        global $wpdb;
        $table_red_envelope = $wpdb->prefix . "xh_wechat_red_envelope_history";
        $table_user_preference = $wpdb->prefix . "xh_wechat_user_preference";
    
        //针对订单红包，只能发一次
        $history =$wpdb->get_row($wpdb->prepare("select count(id) as qty from $table_red_envelope where order_id=%s and type='ORDER-ORDERED' and user_id=%s", $order->id,$order->get_user_id()));
        if(!empty($history)&&$history->qty>0){
            XH_Log::DEBUG("再次触发订单红包，退出红包流程。订单ID：".$order_id);
            return;
        }
    
        $order_items = $order->get_items ();
        //订单内容为空
        if (!$order_items||count($order_items)==0) {
            XH_Log::DEBUG('订单结算时发生异常，订单内容为空。订单ID:'.$order->id);
            return;
        }
    
        $amount = 0;
        $HB_products = array ();
    
        foreach ( $order_items as $order_item_id => $order_item ) {
            $product_ID = $order_item ['item_meta'] ['_product_id'] [0];
            $price = self::get_HB_price(get_post_meta ( $product_ID, '__xh_by_hongbao_per', true ));
            if ($price < 1) {
                continue;
            }
            	
            $inventory = intval ( get_post_meta ( $product_ID, '__xh_by_hongbao_inventory', true ) );
            if ($inventory <= 0) {
                continue;
            }
            	
            $sended_qty = intval ( get_post_meta ( $product_ID, '__xh_by_hongbao_sended_qty', true ) );
            $sended_amount = floatval( get_post_meta ($product_ID, '__xh_by_hongbao_sended_amount', true ) );
            	
            $HB_products [$product_ID] = array(
                'inventory'=>$inventory,
                'amount'=>$price,
                'sended_qty'=>$sended_qty,
                'sended_amount'=>$sended_amount
            );
    
            $amount += $price;
        }
        if(count($HB_products)==0||$amount<=0){return;}
    
        //订单支付成功时，返回的openid
        $openid = get_post_meta ( $order->id, 'openid', true );
        if (empty ( $openid )) {
    
            $wpdb->insert($table_red_envelope, array(
                'user_id'=>$order->get_user_id(),
                'total_amount'=>$amount,
                'type'=>'ORDER-ORDERED',
                'success'=>false,
                'description'=>__('Unpaid or not via WeChat. Not able to get the open ID of the user',XH_WECHAT),
                'order_id'=> $order->id,
                'created_time'=> date_i18n ( 'Y-m-d H:i:s' )
            ));
            return;
        }
    
        try {
            $this->send_hb(
                'ORDER_ORDERED_'.$order->id,
                $amount,
                $openid,
                $this->get_option('hb_by_mch_name'),
                $this->get_option('hb_by_wishing'),
                $this->get_option('hb_by_act_name'),
                __('Red envelop upon order',XH_WECHAT));
        } catch (Exception $e) {
            $wpdb->insert($table_red_envelope, array(
                'user_id'=>$order->get_user_id(),
                'total_amount'=>$amount,
                'type'=>'ORDER-ORDERED',
                'success'=>false,
                'description'=>$e->getMessage(),
                'order_id'=> $order->id,
                'created_time'=> date_i18n ( 'Y-m-d H:i:s' )
            ));
            return;
        }
    
        $wpdb->insert($table_red_envelope, array(
            'user_id'=>$order->get_user_id(),
            'total_amount'=>$amount,
            'type'=>'ORDER-ORDERED',
            'success'=>true,
            'description'=>__('Sent Successfully',XH_WECHAT),
            'order_id'=> $order->id,
            'created_time'=> date_i18n ( 'Y-m-d H:i:s' )
        ));
    
        foreach ( $HB_products as $product_ID => $info ) {
            update_post_meta ( $product_ID, '__xh_by_hongbao_inventory', $info['inventory'] - 1 );
            update_post_meta ( $product_ID, '__xh_by_hongbao_sended_qty', $info['sended_qty'] + 1 );
            update_post_meta ( $product_ID, '__xh_by_hongbao_sended_amount', $info['send_amount'] + $info['amount'] );
        }
    }
    
    public  function wechat_init() {
        if(!class_exists('XH_Wx_Pay_Config')){
            require_once XH_WECHAT_DIR.'/lib/class-xh-wx-pay-exception.php';
            require_once XH_WECHAT_DIR.'/lib/class-xh-wx-pay-config.php';
            require_once XH_WECHAT_DIR.'/lib/class-xh-wx-pay-data.php';
            require_once XH_WECHAT_DIR.'/lib/class-xh-wx-pay-api.php';
            require_once XH_WECHAT_DIR.'/lib/class-xh-wx-pay-js-api.php';
            require_once XH_WECHAT_DIR.'/lib/class-xh-wx-pay-notify.php';
        }
        XH_Wx_Pay_Config::$APPID=$this->get_option('xh_weixinpay_for_wc_appID');
        XH_Wx_Pay_Config::$MCHID=$this->get_option('xh_weixinpay_for_wc_mchId');
        XH_Wx_Pay_Config::$KEY=$this->get_option('xh_weixinpay_for_wc_key');
        XH_Wx_Pay_Config::$APPSECRET=$this->get_option('xh_weixinpay_for_wc_appSecret');
    }
    public function admin_options() { ?>
		<h3><?php echo ( ! empty( $this->method_title ) ) ? $this->method_title : __( 'Settings', 'woocommerce' ) ; ?></h3>

		<?php echo ( ! empty( $this->method_description ) ) ? wpautop( $this->method_description ) : ''; ?>
		
		<?php 
			$xh_section = trim(isset($_GET['xh-section'])?$_GET['xh-section']:'');
			if(empty($xh_section)||!in_array($xh_section, array('hb','auth','promotion'))){
				$xh_section='default';
			}
			
		?>
		<ul class="subsubsub">
			<li><a href="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&section='.$this->section)?>" class="<?php print empty($xh_section)||$xh_section=='default'?'current':''?>">基本设置</a> | </li>
			<li><a href="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&xh-section=auth&section='.$this->section)?>" class="<?php print $xh_section=='auth'?'current':''?>">登录设置</a> | </li>
			<li><a href="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&xh-section=hb&section='.$this->section)?>" class="<?php print $xh_section=='hb'?'current':''?>">微信红包</a> </li>
		</ul>
		<?php 
	
			switch($xh_section){
				default:
					break;
				case 'default':
				case 'promotion':
					$default_setting = array();
					$form_fields =$this->get_form_fields();
					if($form_fields){
						foreach ($form_fields as $key=>$field){
							if(!isset($field['section'])||$field['section']!=$xh_section){
								$field['hidden']=true;
							}
							$default_setting[$key]=$field;
						}
					}
					
					if(!empty($default_setting)){
						?>
						<table class="form-table">
							<?php $this->generate_settings_html($default_setting); ?>
						</table>
						<?php
					}
					break;
                case 'auth':
                    $xh_sub_section = trim(isset($_GET['xh-sub-section'])?$_GET['xh-sub-section']:'');
                    if(empty($xh_sub_section)||!in_array($xh_sub_section, array('a','b'))){
                        $xh_sub_section='a';
                    }
                    
                    $enabled = 'yes'==$this->get_option('auth_enabled');
                    ?>
                    <div class="clear"></div>
					<br/>
					<ul class="subsubsub">
						<li><a href="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&xh-section=auth&xh-sub-section=a&section='.$this->section )?>" class="<?php print $xh_sub_section=='a'?'current':''?>">微信登录</a> </li>
						
						<li style="<?php echo $enabled?"":"display:none;" ?>">| <a href="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&xh-section=auth&xh-sub-section=b&section='.$this->section )?>" class="<?php print $xh_sub_section=='b'?'current':''?>">多域名授权</a> </li>
					</ul>
					<div class="clear"></div>
                    <?php 
                    $default_setting = array();
                    $form_fields =$this->get_form_fields();
                    if($form_fields){
                        foreach ($form_fields as $key=>$field){
                            if(!isset($field['section'])||!isset($field['sub_section'])||$field['section']!=$xh_section||$field['sub_section']!=$xh_sub_section){
                                $field['hidden']=true;
                            }
                            $default_setting[$key]=$field;
                        }
                    }
                    
                    if(!empty($default_setting)){
                        switch ($xh_sub_section){
                            case 'b':
                                ?><div style="color:red;">多网站共用微信公众号时启用，单网站时不要启用此项</div><?php 
                                break;
                        }
                        ?>
						<table class="form-table">
							<?php $this->generate_settings_html($default_setting); ?>
						</table>
						<hr/>
						<div class="description">一个公众号支付跨多域名，微信(qq、微博等)用户整合，绑定/解绑wp网站用户等功能，请使用<a target="_blank" href="http://www.weixinsocial.com">wechat social</a>。</div>
						<?php
					}
					break;
				case 'hb':
				    $xh_sub_section = trim(isset($_GET['xh-sub-section'])?$_GET['xh-sub-section']:'');
				    if(empty($xh_sub_section)||!in_array($xh_sub_section, array('s','o','l'))){
				        $xh_sub_section='s';
				    }
					?>
					<div class="clear"></div>
					<br/>
					<ul class="subsubsub">
						<li><a href="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&xh-section=hb&xh-sub-section=s&section='.$this->section )?>" class="<?php print $xh_sub_section=='s'?'current':''?>">红包推广</a> | </li>
						<li><a href="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&xh-section=hb&xh-sub-section=o&section='.$this->section )?>" class="<?php print $xh_sub_section=='o'?'current':''?>">订单红包</a> | </li>
						<li><a href="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&xh-section=hb&xh-sub-section=l&section='.$this->section )?>" class="<?php print $xh_sub_section=='l'?'current':''?>">红包记录</a></li>
					</ul>
					<div class="clear"></div>
					<?php 
					switch($xh_sub_section){
						case 's':
						case 'o':
						    if($xh_sub_section=='s'){
						        ?>
    						    <div class="description">提示:1.用户在微信客户端内操作，才能触发红包。2.当开启红包功能，微信内会自动开启微信登录</div>
    						    <?php 
						    }else if($xh_sub_section=='o'){
						        ?>
    						    <div class="description">提示:微信支付才能触发订单</div>
    						    <?php 
						    }
						   
							$default_setting = array();
							$form_fields =$this->get_form_fields();
							if($form_fields){
								foreach ($form_fields as $key=>$field){
									if(!isset($field['section'])||!isset($field['sub_section'])||$field['section']!=$xh_section||$field['sub_section']!=$xh_sub_section){
										$field['hidden']=true;
									}
									$default_setting[$key]=$field;
								}
							}
								
							if(!empty($default_setting)){
								?>
								<table class="form-table">
									<?php $this->generate_settings_html($default_setting); ?>
								</table>
								<?php
							}
							break;
						case 'l':
							$pageSize =20;
							$pageIndex = intval($_GET['pageIndex']);
							if($pageIndex<1){$pageIndex=1;}
								
							$search_order_id = intval(trim($_GET['order_id']));
							
								
							global $wpdb;
							$table_red_envelope = $wpdb->prefix . "xh_wechat_red_envelope_history";
							$table_user = $wpdb->prefix . "users";
							$table_post = $wpdb->prefix . "posts";
								
							$query =$wpdb->get_row(
						      $wpdb->prepare("select count(t.id) as qty
								from $table_red_envelope t
								where (%d=0 or %d=t.order_id)", $search_order_id, $search_order_id));
				
								$total_qty =$query?$query->qty:0;
									
									
								$start = ($pageIndex-1)*$pageSize;
								$items =$wpdb->get_results(
										$wpdb->prepare(
												"select r.id,
												r.order_id,
												r.total_amount,
												r.type,
												r.description,
												r.success,
												r.created_time,
												u.display_name
												from $table_red_envelope r
												left join $table_user u on u.ID = r.user_id
												where (%d=0 or %d=r.order_id)
												order by r.id desc
												limit %d,%d", $search_order_id , $search_order_id,$start,$pageSize));
								
								if(!class_exists('XH_Paging_Model')){
								    require_once   'infrastructure/paging/class-xh-paging-model.php';
								}
								
								$paging = new XH_Paging_Model($pageIndex,$pageSize,$total_qty,function($i){
									return 'javascript:window.XHWECHAT.search('.$i.');';
								});
								?>
						</form>				
						<form id="form-search" method="get" action="<?php print admin_url ( 'admin.php?page=wc-settings&tab=checkout&xh-section=hb&xh-sub-section=l&section='.$this->id )?>">	
							<input type="hidden" name="page" value="wc-settings"/>	
							<input type="hidden" name="tab" value="checkout"/>	
							<input type="hidden" id="form-pageIndex" name="pageIndex" value=""/>
							<input type="hidden" name="xh-section" value="hb"/>	
							<input type="hidden" name="xh-sub-section" value="l"/>	
							<input type="hidden" name="section" value="<?php print $this->id?>"/>	
							<input type="hidden" name="order_id" id="search-order-id" value="<?php print esc_attr($search_order_id==0?'':$search_order_id)?>"/>
						</form>	
						<form>
						<p class="search-box">
							<input type="search" style="min-width: 100px;" id="post-search-order-id" maxlength="10" value="<?php print esc_attr($search_order_id==0?'':$search_order_id)?>" placeholder="<?php print __('Order ID' ,XH_WECHAT)?>"/>
							<input type="button" class="button" onclick="window.XHWECHAT.search();" value="<?php print __('Search' ,XH_WECHAT)?>"/>
						</p>
					
						<div class="tablenav top">
							<div class="alignleft actions bulkactions">
									<select name="action" id="bulk-action-selector-top">
										<option value="-1"><?php print __('Bulk Operations ' ,XH_WECHAT)?></option>
									</select>
								
									<input type="button" class="button action btn-batch-operation" value="<?php print __('Apply' ,XH_WECHAT)?>">
								</div>
						<?php print $paging->wp();?>
						
							<br class="clear"/>
						</div>
							
						<table class="wp-list-table widefat fixed striped posts">
							<thead>
							<tr>
								<td id="cb" class="manage-column column-cb check-column">
									<label class="screen-reader-text" for="cb-select-all-1"><?php print __('Check all' ,XH_WECHAT)?></label>
									<input id="cb-select-all-1" type="checkbox">
								</td>
								
								<th scope="col" id="order_title" class="manage-column column-order_title column-primary">
									<?php print __('Order ID' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('User' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Amount' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Type' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Status' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Detail' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Time' ,XH_WECHAT)?>
								</th>
							</tr>
							</thead>
						
							<tbody id="the-list">
								<?php 
									if($items){
										foreach ($items as $item){
											?>
											<tr id="xhshop-order-<?php print esc_attr($item->order_id)?>" class="iedit author-other level-0 post-<?php print esc_attr($item->order_id)?> type-shop_order status-wc-pending post-password-required hentry">
												<th scope="row" class="check-column">	
													<input type="checkbox" name="order_id[]" value="<?php print  esc_attr($item->order_id)?>">
													<div class="locked-indicator"></div>
												</th>
												<td ><abbr><?php print $item->order_id?></abbr></td>
												<td ><abbr><?php print (empty($item->display_name)?'--':$item->display_name)?></abbr></td>
												<td ><abbr><?php print $item->total_amount?></abbr></td>
												<td ><abbr><?php switch( $item->type){
													default:
														print '--';
														break;
													case 'ORDER-SHARE':
														print '<span style="color:red">'.__('Promotional Red Envelop' ,XH_WECHAT).'</span>';
													break;
													case 'ORDER-ORDERED':
														print '<span style="color:green">'.__('Red envelop upon order' ,XH_WECHAT).'</span>';
														break;
												}?></abbr></td>
												<td ><abbr><?php print ($item->success?'<span style="color:green">'.__('Success' ,XH_WECHAT).'</span>':'<span style="color:red">'.__('Fail' ,XH_WECHAT).'</span>')?></abbr></td>
												<td ><abbr><?php print ($item->success?('<span style="color:green">'.$item->description.'</span>'):('<span style="color:red">'.$item->description.'</span>'))?></abbr></td>
												<td ><abbr><?php print date('Y-m-d H:i',strtotime($item->created_time))?></abbr></td>
												</tr>
											<?php 
										}
									}
								?>
								
								</tbody>
						
							<tfoot>
							<tr>
								<td id="cb" class="manage-column column-cb check-column">
									<label class="screen-reader-text" for="cb-select-all-1"><?php print __('Check all' ,XH_WECHAT)?></label>
									<input id="cb-select-all-1" type="checkbox">
								</td>
								
								<th scope="col" id="order_title" class="manage-column column-order_title column-primary">
									<?php print __('Order ID' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('User' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Amount' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Type' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Status' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Detail' ,XH_WECHAT)?>
								</th>
								
								<th scope="col" id="order_status" class="manage-column">
									<?php print __('Time' ,XH_WECHAT)?>
								</th>
							</tr>
							</tfoot>
						
						</table>
							<div class="tablenav bottom">
						
								<div class="alignleft actions bulkactions">
									<select name="action" id="bulk-action-selector-top">
										<option value="-1"><?php print __('Bulk Operations ' ,XH_WECHAT)?></option>
									</select>
								
									<input type="button" class="button action btn-batch-operation" value="<?php print __('Apply' ,XH_WECHAT)?>">
								</div>
								
								<div class="alignleft actions"></div>
								
								<?php print $paging->wp();?>
								<br class="clear">
							</div>
							
							<script type="text/javascript">
							(function($){
								$('.current-page').keyup(function(e){
									if(e&&e.which==13){
										window.XHWECHAT.search($(this).val());
									}
								});
								if(!window.XHWECHAT){window.XHWECHAT={};}
								window.XHWECHAT.search =function(pageIndex=1){
									$('#search-order-id').val($('#post-search-order-id').val());
									$('#form-pageIndex').val(pageIndex);
									$('#form-search').submit();
								} 
							})(jQuery);
						</script>
									
						<script type="text/javascript">
							(function($){
								$(function(){
									$('p.submit').css('display','none');
								});
								
							})(jQuery);
						</script>
						<?php 
				break;
			}
			break;
		}
	}
    protected function generate_share_link($post_link =null,$redirect=true){
        if(!XH_Wechat_url::isWeixinClient()){
            return $post_link;
        }
    
        //必须获得用户信息
        global $current_user;
        if(!$this->is_user_authorized()){
            if($redirect){
                wp_redirect(XH_Wechat_Url::user_auth());
                exit;
            }else{
                return $post_link;
            }
        }
    
        $openid =$current_user->openid;
        $params ="_uid=$current_user->ID&_ukey=".substr(md5($current_user->ID.$current_user->openid), 5,4);
    
        if(!$post_link){
            
            $protocol = (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
            $post_link= $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
        }
    
        $links =explode('?', $post_link);
        if(count($links)==2){
            $post_link=$links[0];
    
            $index=0;
            foreach (explode('&', $links[1]) as $param){
                $v =explode('=', $param);
                	
                if(count($v)==2){
                    if($v[0]=='_uid'||$v[0]=='_ukey'){
                        continue;
                    }
    
                    if($index++==0){
                        $post_link.='?'.$param;
                    }else{
                        $post_link.='&'.$param;
                    }
                }
            }
        }
        	
        if(strpos($post_link, '?')==false){
            $post_link.='?'.$params;
        }else{
            $post_link.='&'.$params;
        }
    
        if($redirect){
            wp_redirect($post_link);
            exit;
        }
        else{
            return $post_link;
        }
    }
    

    function is_user_authorized(){
        global $current_user;
        if (!is_user_logged_in ()) {
            return false;
        }
    
        if(!$current_user->openid){
            $openid =get_user_meta($current_user->ID, 'openid', true);
            $current_user->openid = $openid;
        }
  
        if(!$current_user->openid){
            if(class_exists('XH_Social')){
                $api =XH_Social::instance()->channel->get_social_channel('social_wechat');
                if($api){
                    $ext_user_info =$api->get_ext_user_info_by_wp($current_user->ID);
                    if($ext_user_info){
                        $current_user->openid = isset($ext_user_info['mp_openid'])?$ext_user_info['mp_openid']:'';
                    }
                }
            }
        }
        
        if(!$current_user->openid){
            return false;
        }
    
        return true;
    }
    
    
    
    //商品详细页面，分享
    function single_product_content_share($post){
        if(is_admin()&&!is_single($post)){return;}
        //非woo产品详细页面，不启用
        if(empty($post)||$post->post_type!='product'){
            return;
        }
    
        //未开启分享，那么直接不启用
        $share =$this->get_option('hb_eabled_share');
        if(empty($share)||$share!='yes'){
            return;
        }
    
        if(
            !isset($_GET['_uid'])||empty($_GET['_uid'])
            ||!isset($_GET['_ukey'])||empty($_GET['_ukey'])
            ){
                //在非微信客户端，就不需要生成分享参数
                $this->generate_share_link();
                return;
        }
    
        //必须获得用户信息
        global $current_user;
        //这里不需要获取用户的openid
        if(!is_user_logged_in()){
            wp_redirect(XH_Wechat_Url::user_auth('',true));
            exit;
        }
    
        //邀请者进入了当前
        $uid = intval(trim($_GET['_uid']));
        if($current_user->ID==$uid){
            return;
        }
    
        //分享者openid获取失败，此处是异常
        $sharer_openid = get_user_meta($uid,'openid',true);
        if(empty($sharer_openid)){
            XH_Log::WARN('受邀者进入商品详情页面时，获取邀请者的openid发生错误！,邀请者ID:'.$uid);
            return;
        }
        	
        //非法篡改的链接
        if(substr(md5($uid.$sharer_openid), 5,4)!=$_GET['_ukey']){
            //直接跳过这种错误
            return;
        }
    
        global $wpdb;
        $table_user_preference = $wpdb->prefix . "xh_wechat_user_preference";
        //查询当前进入的人，是否被他人邀请过
        $query =$wpdb->get_row("select count(id) as qty
            from $table_user_preference
            where user_id=".$current_user->ID." and post_id= ".$post->ID.";");
    
        if(empty($query)||$query->qty==0){
            //插入关联数据
            $wpdb->insert($table_user_preference, array(
                'user_id'=>$current_user->ID,
                'ref_user_id'=>$uid,
                'ref_open_id'=>$sharer_openid,
                'post_id'=>$post->ID,
                'created_time'=>date_i18n("Y-m-d H:i:s")
            ));
        }
    
        //在微信客户端，那么继续生成自己的分享链接
        $this->generate_share_link();
    }
    
    public function auto_login_in_wechat() {
        global $post;
        if(!$post){
            return;
        }
    
        if(is_admin()){return;}
    
        $this->single_product_content_share($post);
    
        // 当前页面就是登录页面
        if (defined ( 'XH_LOGIN_IGNORE' ) && XH_LOGIN_IGNORE) {
            return;
        }

        if($this->is_user_authorized()){
            return;
        }
        
        //若启用分享发红包，那么产品详细页面必须要微信登录
        $need_auth = false;
        if(!$need_auth){
            // 微信发红包
            $auth = $this->get_option('xh_weixinpay_for_wc_weixin_auth');
            if (empty($auth)) {$auth=0;}
            	
            switch ($auth) {
                default :
                case 0 : // 不启用
                    return;
                case 1 : // 仅在微信客户端启用(非微信浏览时，不强制微信登录)
                    // 判断是否是微信客户端
                    if (! XH_Wechat_Url::isWeixinClient()) {
                        return;
                    }
                    break;
                case 2 : // 所有客户端启用
    
                    break;
            }
        }
        	
        
    
        if(is_search()
            ||is_front_page()
            ||is_home()
            ||is_post_type_archive()
            ||is_tax()
            //||is_attachment()
            ||is_single()
            ||is_page()//设置页面需要登录有风险
            ||is_singular()
            ||is_category()
            ||is_tag()
            ||is_author()
            //||is_date()
            ||is_archive() ){
                //排除page页面
                if(!is_front_page()){
                    if(is_page()){
                        return;
                    }
                    global $post;
                    if($post&&$post->post_type=='page'){
                        return;
                    }
                }
                
                wp_redirect(XH_Wechat_Url::user_auth());
                exit ();
        }
    }
    
}