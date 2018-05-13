<?php
class XH_Jsapi_Ticket {
	public $expire_time;
	public $jsapi_ticket;
}
class XH_Access_Token {
	public $expire_time;
	public $access_token;
}
class XH_Wx_Jssdk {
	private $appId;
	private $appSecret;
	public function __construct($appId, $appSecret) {
		$this->appId = $appId;
		$this->appSecret = $appSecret;
	}
	public function getSignPackage() {
		$jsapiTicket = $this->getJsApiTicket ();
		
		// 注意 URL 一定要动态获取，不能 hardcode.
		$protocol = (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
		$url = $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
	
		$timestamp = time ();
		$nonceStr = $this->createNonceStr ();
		
		// 这里参数的顺序要按照 key 值 ASCII 码升序排序
		$string = "jsapi_ticket=$jsapiTicket&noncestr=$nonceStr&timestamp=$timestamp&url=$url";
		
		$signature = sha1 ( $string );
		
		$signPackage = array (
				"appId" => $this->appId,
				"nonceStr" => $nonceStr,
				"timestamp" => $timestamp,
				"url" => $url,
				"signature" => $signature,
				"rawString" => $string 
		);
		return $signPackage;
	}
	private function createNonceStr($length = 16) {
		$chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$str = "";
		for($i = 0; $i < $length; $i ++) {
			$str .= substr ( $chars, mt_rand ( 0, strlen ( $chars ) - 1 ), 1 );
		}
		return $str;
	}
	private function getJsApiTicket() {
	    if(class_exists('XH_Social')&& class_exists('XH_Social_Wechat_Token')){
	        $addon = XH_Social::instance()->get_available_addon('wechat_social_add_ons_social_wechat_ext');
	        if($addon&&$addon->enabled){
    	        $api = XH_Social_Wechat_Token::instance();
    	        if(method_exists($api, 'jsapi_ticket')){
    	            return $api->jsapi_ticket();
    	        }
	        }
	    }
	    
		// jsapi_ticket 应该全局存储与更新，以下代码以写入到文件中做示例
		$data = get_option ( 'xhwechatjsapiticket' );
		if (is_null($data)||!$data) {
		    $data = new XH_Jsapi_Ticket ();
		    $data->expire_time = time () - 1;
		    $data->jsapi_ticket = null;
		}
		
		if(is_array($data)){
		    $data = json_decode(json_encode($data),false);
		}
		
		if(!is_object($data)||!isset($data->expire_time)||!isset($data->jsapi_ticket)){
		    $data = new XH_Access_Token ();
		    $data->expire_time = time () - 1;
		}
		
		if ($data->expire_time > time ()) {
		    return $data->jsapi_ticket;
		}
		
		$accessToken = $this->getAccessToken ();
		// 如果是企业号用以下 URL 获取 ticket
		// $url = "https://qyapi.weixin.qq.com/cgi-bin/get_jsapi_ticket?access_token=$accessToken";
		$url = "https://api.weixin.qq.com/cgi-bin/ticket/getticket?type=jsapi&access_token=$accessToken";
		$res = json_decode ( $this->httpGet ( $url ) );
		$ticket = $res->ticket;
		if ($ticket) {
			$data = new XH_Jsapi_Ticket ();
			$data->expire_time = time () + 7000;
			$data->jsapi_ticket = $ticket;
			
			update_option ( 'xhwechatjsapiticket', $data );
		}
		
		return $ticket;
	}
	
	private function getAccessToken() {
	    if(class_exists('XH_Social')&& class_exists('XH_Social_Wechat_Token')){
	        $addon = XH_Social::instance()->get_available_addon('wechat_social_add_ons_social_wechat_ext');
	        if($addon&&$addon->enabled){
	           return XH_Social_Wechat_Token::instance()->access_token();
	        }
	    }
	    
		$data = get_option ( 'xhwechataccesstoken' );
	   if (is_null($data)||!$data) {
			$data = new XH_Access_Token ();
			$data->expire_time = time () - 1;
		}
		
		if(is_array($data)){
		    $data = json_decode(json_encode($data),false);
		}
		
		if(!is_object($data)||!isset($data->expire_time)||!isset($data->access_token)){
		    $data = new XH_Access_Token ();
		    $data->expire_time = time () - 1;
		}
		// access_token 应该全局存储与更新，以下代码以写入到文件中做示例
		
		if ($data->expire_time > time ()) {
			return $data->access_token;
		}
		
		// 如果是企业号用以下URL获取access_token
		// $url = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=$this->appId&corpsecret=$this->appSecret";
		$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$this->appId&secret=$this->appSecret";
		$res = json_decode ( $this->httpGet ( $url ) );
		$access_token = $res->access_token;
		if ($access_token) {
			$data = new XH_Access_Token ();
			$data->expire_time = time () + 7000;
			$data->access_token = $access_token;
			update_option ( 'xhwechataccesstoken', $data);
		}
		
		return $access_token;
	}
	private function httpGet($url) {
		$curl = curl_init ();
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 500 );
		// 为保证第三方服务器与微信服务器之间数据传输的安全性，所有微信接口采用https方式调用，必须使用下面2行代码打开ssl安全校验。
		// 如果在部署过程中代码在此处验证失败，请到 http://curl.haxx.se/ca/cacert.pem 下载新的证书判别文件。
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, true );
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, 2 );
		curl_setopt ( $curl, CURLOPT_URL, $url );
		
		$res = curl_exec ( $curl );
		curl_close ( $curl );
		
		return $res;
	}
}

