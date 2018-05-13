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
$error=WC()->session->get(XH_WECHAT_SESSION_AUTH_ERROR);
WC()->session->__unset(XH_WECHAT_SESSION_AUTH_ERROR);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1,user-scalable=0">
    <title><?php print __('WeChat loading fails' ,XH_WECHAT)?></title>
     <link rel="stylesheet" href="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/lib/weui.css">
	<link rel="stylesheet" href="<?php print XH_WECHAT_URL?>/assets/jquery-weui/dist/css/jquery-weui.css">
</head>

    <body>
	<div class="page">
	    <div class="weui_msg">
	        <div class="weui_icon_area"><i class="weui_icon_warn weui_icon_msg"></i></div>
	        <div class="weui_text_area">
	            <h2 class="weui_msg_title"><?php print __('WeChat loading fails' ,XH_WECHAT)?></h2>
           	 <p class="weui_msg_desc" ><?php print $error;?></p>
	        </div>
	    </div>
	</div>
    </body>
</html>
