<?php
/*
 * Plugin Name: WooCommerce支付宝
 * Plugin URI: http://www.wpweixin.net/product/246.html
 * Description:WooCommerce支付宝插件全平台版是一款基于WooCommerce购物车插件的子插件，让WooCommerce增加支付宝扫码支付和手机网站支付的功能，适合任意WooCommerce主题，此插件对WooCommerce兼容良好，自动适配PC和手机网站。 
 * Version: 1.5.4
 * Author: 迅虎网络 
 * Author URI:http://www.wpweixin.net 
 * Text Domain: xh alipay for Woocommerce
 */
if (! defined ( 'ABSPATH' ))
	exit (); // Exit if accessed directly

if (! defined ( 'XH_ALIPAY' )) define ( 'XH_ALIPAY', 'XH_ALIPAY' ); else return;

define('XH_ALIPAY_FILE',__FILE__);
define('XH_ALIPAY_VERSION','1.5.4');
define('XH_ALIPAY_DIR',rtrim(plugin_dir_path(XH_ALIPAY_FILE),'/'));
define('XH_ALIPAY_URL',rtrim(plugin_dir_url(XH_ALIPAY_FILE),'/'));
require_once 'class-xh-alipay-api.php';
$api = new XH_Alipay_Api();
