<?php

/**
 * 
 * JSAPI支付实现类
 * 该类实现了从微信公众平台获取code、通过code获取openid和access_token、
 * 生成jsapi支付js接口所需的参数、生成获取共享收货地址所需的参数
 * 
 * 该类是微信支付提供的样例程序，商户可根据自己的需求修改，或者使用lib中的api自行开发
 * 
 * @author widy
 *
 */
class XH_Wx_Pay_Js_Api
{
	/**
	 * 
	 * 网页授权接口微信服务器返回的数据，返回样例如下
	 * {
	 *  "access_token":"ACCESS_TOKEN",
	 *  "expires_in":7200,
	 *  "refresh_token":"REFRESH_TOKEN",
	 *  "openid":"OPENID",
	 *  "scope":"SCOPE",
	 *  "unionid": "o6_bmasdasdsad6_2sgVt7hMZOPfL"
	 * }
	 * 其中access_token可用于获取共享收货地址
	 * openid是微信支付jsapi支付接口必须的参数
	 * @var array
	 */
	public $data = null;
	
	/**
	 * 
	 * 通过跳转获取用户的openid，跳转流程如下：
	 * 1、设置自己需要调回的url及其其他参数，跳转到微信服务器https://open.weixin.qq.com/connect/oauth2/authorize
	 * 2、微信服务处理完成之后会跳转回用户redirect_uri地址，此时会带上一些参数，如：code
	 * 
	 * @return 用户的openid
	 */
	public function GetOpenid()
	{
		//通过code获得openid
		if (!isset($_GET['code'])){
			//触发微信返回code码
		    $protocol = (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
		    $callback= $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			$baseUrl = urlencode($callback);
		
			$url = $this->__CreateOauthUrlForCode($baseUrl);
			Header("Location: $url");
			exit();
		} else {
			//获取code码，以获取openid
		    $code = $_GET['code'];
			$openid = $this->getOpenidFromMp($code);
			return $openid;
		}
	}
	
	
	public function GetUserInfo()
	{
		//通过code获得openid
		if (!isset($_GET['code'])){
			$protocol = (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
		    $callback= $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
			//触发微信返回code码
			$baseUrl = urlencode($callback);
		
			$url = $this->__CreateSnsapiUserinfoOauthUrlForCode($baseUrl);
			Header("Location: $url");
			exit();
		} else {
			//获取code码，以获取openid
			$code = $_GET['code'];
			$userInfo = $this->GetUserInfoFromMp($code);
			return $userInfo;
		}
	}
	
	/**
	 * 
	 * 获取jsapi支付的参数
	 * @param array $UnifiedOrderResult 统一支付接口返回的数据
	 * @throws XH_Wx_Pay_Exception
	 * 
	 * @return json数据，可直接填入js函数作为参数
	 */
	public function GetJsApiParameters($UnifiedOrderResult)
	{
		if(!array_key_exists("appid", $UnifiedOrderResult)
		|| !array_key_exists("prepay_id", $UnifiedOrderResult)
		|| $UnifiedOrderResult['prepay_id'] == "")
		{
			throw new XH_Wx_Pay_Exception("参数错误");
		}
		$jsapi = new WxPayXH_Wx_Pay_Js_Api();
		$jsapi->SetAppid($UnifiedOrderResult["appid"]);
		$timeStamp = time();
		$jsapi->SetTimeStamp("$timeStamp");
		$jsapi->SetNonceStr(XH_Wx_Pay_Api::getNonceStr());
		$jsapi->SetPackage("prepay_id=" . $UnifiedOrderResult['prepay_id']);
		$jsapi->SetSignType("MD5");
		$jsapi->SetPaySign($jsapi->MakeSign());
		$parameters = json_encode($jsapi->GetValues());
		return $parameters;
	}

	/**
	 * 
	 * 通过code从工作平台获取openid机器access_token
	 * @param string $code 微信跳转回来带上的code
	 * 
	 * @return openid
	 */
	public function GetOpenidFromMp($code)
	{
		$url = $this->__CreateOauthUrlForOpenid($code);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		
		if(XH_Wx_Pay_Config::CURL_PROXY_HOST != "0.0.0.0" && XH_Wx_Pay_Config::CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, XH_Wx_Pay_Config::CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, XH_Wx_Pay_Config::CURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		if(!$data||(isset($data['errcode'])&&$data['errcode']!=0)){
			throw new Exception($res,$data&&isset($data['errcode'])?$data['errcode']:-1);
		}
		$this->data = $data;
		return isset($data['openid'])?$data['openid']:'';
	}
	
	public function GetUserInfoAccessToken($code)
	{
		$url = $this->__CreateOauthUrlForOpenid($code);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(XH_Wx_Pay_Config::CURL_PROXY_HOST != "0.0.0.0"
				&& XH_Wx_Pay_Config::CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, XH_Wx_Pay_Config::CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, XH_Wx_Pay_Config::CURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
	
		$data = json_decode($res,true);
		if(!$data||(isset($data['errcode'])&&$data['errcode']!=0)){
			throw new Exception($res,$data&&isset($data['errcode'])?$data['errcode']:-1);
		}
		$this->data = $data;
		return $data;
	}
	
	private function GetUserInfoFromMp($code)
	{
		$access_token =$this->GetUserInfoAccessToken($code);
		
		$url = $this->__CreateOauthUrlForUserinfo($access_token);
		//初始化curl
		$ch = curl_init();
		//设置超时
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,FALSE);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,FALSE);
		curl_setopt($ch, CURLOPT_HEADER, FALSE);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
		if(XH_Wx_Pay_Config::CURL_PROXY_HOST != "0.0.0.0"
				&& XH_Wx_Pay_Config::CURL_PROXY_PORT != 0){
			curl_setopt($ch,CURLOPT_PROXY, XH_Wx_Pay_Config::CURL_PROXY_HOST);
			curl_setopt($ch,CURLOPT_PROXYPORT, XH_Wx_Pay_Config::CURL_PROXY_PORT);
		}
		//运行curl，结果以jason形式返回
		$res = curl_exec($ch);
		curl_close($ch);
		//取出openid
		$data = json_decode($res,true);
		if(!$data||(isset($data['errcode'])&&$data['errcode']!=0)){
			throw new Exception($res,$data&&isset($data['errcode'])?$data['errcode']:-1);
		}
		$this->data = $data;
	
		return $data;
	}
	
	/**
	 * 
	 * 拼接签名字符串
	 * @param array $urlObj
	 * 
	 * @return 返回已经拼接好的字符串
	 */
	private function ToUrlParams($urlObj)
	{
		$buff = "";
		foreach ($urlObj as $k => $v)
		{
			if($k != "sign"){
				$buff .= $k . "=" . $v . "&";
			}
		}
		
		$buff = trim($buff, "&");
		return $buff;
	}
	
	/**
	 * 
	 * 获取地址js参数
	 * 
	 * @return 获取共享收货地址js函数需要的参数，json格式可以直接做参数使用
	 */
	public function GetEditAddressParameters()
	{	
		$getData = $this->data;
		$data = array();
		$data["appid"] = XH_Wx_Pay_Config::$APPID;
		$protocol = (! empty ( $_SERVER ['HTTPS'] ) && $_SERVER ['HTTPS'] !== 'off' || $_SERVER ['SERVER_PORT'] == 443) ? "https://" : "http://";
		    $callback= $protocol.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
		$data["url"] = $callback;
		
		$time = time();
		$data["timestamp"] = "$time";
		$data["noncestr"] = substr(str_shuffle($time), 0,8);
		$data["accesstoken"] = $getData["access_token"];
		ksort($data);
		$params = $this->ToUrlParams($data);
		$addrSign = sha1($params);
		
		$afterData = array(
			"addrSign" => $addrSign,
			"signType" => "sha1",
			"scope" => "jsapi_address",
			"appId" => XH_Wx_Pay_Config::$APPID,
			"timeStamp" => $data["timestamp"],
			"nonceStr" => $data["noncestr"]
		);
		$parameters = json_encode($afterData);
		return $parameters;
	}
	
	/**
	 * 
	 * 构造获取code的url连接
	 * @param string $redirectUrl 微信服务器回跳的url，需要url编码
	 * 
	 * @return 返回构造好的url
	 */
	private function __CreateOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = XH_Wx_Pay_Config::$APPID;
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_base";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->ToUrlParams($urlObj);
	
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}
	
	private function __CreateSnsapiUserinfoOauthUrlForCode($redirectUrl)
	{
		$urlObj["appid"] = XH_Wx_Pay_Config::$APPID;
		$urlObj["redirect_uri"] = "$redirectUrl";
		$urlObj["response_type"] = "code";
		$urlObj["scope"] = "snsapi_userinfo";
		$urlObj["state"] = "STATE"."#wechat_redirect";
		$bizString = $this->ToUrlParams($urlObj);
	
		return "https://open.weixin.qq.com/connect/oauth2/authorize?".$bizString;
	}
	
	/**
	 * 
	 * 构造获取open和access_toke的url地址
	 * @param string $code，微信跳转带回的code
	 * 
	 * @return 请求的url
	 */
	private function __CreateOauthUrlForOpenid($code)
	{
		$urlObj["appid"] = XH_Wx_Pay_Config::$APPID;
		$urlObj["secret"] = XH_Wx_Pay_Config::$APPSECRET;
		$urlObj["code"] = $code;
		$urlObj["grant_type"] = "authorization_code";
		$bizString = $this->ToUrlParams($urlObj);
		return "https://api.weixin.qq.com/sns/oauth2/access_token?".$bizString;
	}
	
	private function __CreateOauthUrlForUserinfo($data)
	{
		return "https://api.weixin.qq.com/sns/userinfo?access_token=".$data['access_token']."&openid=".$data['openid']."&lang=zh_CN";
	}
}