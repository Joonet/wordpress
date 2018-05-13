<?php
/*
 * Plugin Name: WooCommerce微信支付(红包)
 * Plugin URI: https://www.wpweixin.net/product/201.html 
 * Description:WooCommerce微信支付插件为WooCommerce增加微信支付网关，支持微信扫码支付和公众号原生支付功能，退款到微信零钱，汇率，微信自动登录，红包推广，红包返现等促销功能。
 * Version: 2.1.7
 * Author: 迅虎网络 
 * Author URI:http://www.wpweixin.net 
 * Text Domain: wechat weixin Woocommerce 微信 支付
 */
if (! defined ( 'ABSPATH' )) exit (); // Exit if accessed directly
if (! defined ( 'XH_WECHAT' )) define ( 'XH_WECHAT', 'XH_WECHAT' ); else return;
define('XH_WECHAT_FILE',__FILE__);
define('XH_WECHAT_VERSION','2.1.7');
define('XH_WECHAT_DIR',rtrim(plugin_dir_path(XH_WECHAT_FILE),'/'));
define('XH_WECHAT_URL',rtrim(plugin_dir_url(XH_WECHAT_FILE),'/'));

require_once 'class-xh-wechat-api.php';
$api = new XH_Wechat_Api();
add_filter( 'sanitize_user', array($api,'sanitize_user'), 10, 3);
