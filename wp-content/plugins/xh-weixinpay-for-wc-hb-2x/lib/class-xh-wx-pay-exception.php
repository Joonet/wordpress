<?php
/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */

class XH_Wx_Pay_Exception extends Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
