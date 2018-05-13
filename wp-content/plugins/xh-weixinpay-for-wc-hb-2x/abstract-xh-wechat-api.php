<?php
if (! defined('ABSPATH'))
    exit();
if(!class_exists('XH_Log')){
    require_once   'infrastructure/logger/class-xh-log.php';
}
require_once 'class-xh-wechat-url.php';
abstract class Abstract_XH_Wechat_Api{
    public $v,$t,$f,$i,$dir,$key,$u_u,$get;
    const ID='xh_weixinpay_for_wc_hb';
    public function __construct(){
        $this->i = self::ID;
        $this->v=XH_WECHAT_VERSION;
        $this->f=XH_WECHAT_FILE;
        $this->dir=XH_WECHAT_DIR;
        $this->get = $_GET;
        $this->u_u = admin_url("admin.php?page=woo_wechat_license");
       
        define('XH_WECHAT_SESSION_AUTH_CALLBACK', 'XH_WECHAT_SESSION_AUTH_CALLBACK');
        define('XH_WECHAT_SESSION_ADDRESS_CALLBACK', 'XH_WECHAT_SESSION_ADDRESS_CALLBACK');
        define('XH_WECHAT_SESSION_JSAPI_PAY_CALLBACK', 'XH_WECHAT_SESSION_JSAPI_PAY_CALLBACK');
        define('XH_WECHAT_SESSION_AUTH_ERROR', 'XH_WECHAT_SESSION_AUTH_ERROR');
        define('XH_WECHAT_SESSION_AUTH_ERROR_TIMES', 'XH_WECHAT_SESSION_AUTH_ERROR_TIMES');
        
        load_plugin_textdomain( XH_WECHAT, false,dirname( plugin_basename( __FILE__ ) ) . '/lang/'  );
        register_activation_hook ( XH_WECHAT_FILE, array($this,'register_activation_hook') );
        register_deactivation_hook ( XH_WECHAT_FILE, array($this,'register_deactivation_hook') );
        XH_Log::Init ( new XH_Log_File_Handler ( XH_WECHAT_DIR . "/logs/" . date ( 'Y-m-d' ) . '.log' ), 15 );
        add_filter ( 'get_avatar', array($this,'get_avatar'), 99, 6 );
        add_action('admin_menu',array($this,'admin_menu'));
        if(isset($_POST['action'])
            &&isset($_POST['license_key'])
            &&$_POST['action']===md5(self::ID)){
            $license_key = trim($_POST['license_key']);
            update_option(self::ID, $license_key);
        }
        
        add_action('admin_init', array($this,'option_init'));
    }
    public  function sanitize_user( $username, $raw_username, $strict ) {
        if( !$strict )
            return $username;
    
            return sanitize_user(stripslashes($raw_username), false);
    }
    public function option_init(){
        register_setting( 'general', self::ID);
        add_settings_field(self::ID,'【微信支付】Woo授权',array($this,'general_default_callback'),'general','default');
    }
    
    public function general_default_callback() {
        $id=self::ID;
        $hash =esc_attr( get_option( self::ID) );
        echo "<input name=\"{$id}\" type=\"text\" id=\"{$id}\" value=\"{$hash}\" class=\"regular-text code\" style=\"width: 25em;\" />";
        
    }
    
    public function register_deactivation_hook(){
        //清除
        $actions = array(
            'plugin_latest_version'
        );
      
        foreach ($actions as $action){
            $cache_key =md5("xh_updater,id:{$this->i},action:{$action},version:{$this->v}");
            delete_site_transient( $cache_key );
        }
    }
    public function inc($b=null){
        if(is_null($b)){
            return $GLOBALS[self::ID];
        }
        $GLOBALS[self::ID]=$b;
    }
    
    public function after_init(){
        
    }
    
    public function admin_menu(){
        if(!isset($_GET['page'])||$_GET['page']!='woo_wechat_license'){
            if($GLOBALS[self::ID]){
                return;
            }
        }
        
        add_menu_page( '【微信支付】授权', '【微信支付】授权', 'administrator', 'woo_wechat_license',array($this,'woo_wechat_license'));
          
    }
    public function woo_wechat_license(){
        global $HX_Wechat_WC_Payment_Gateway;
      
        $id=md5(self::ID);
        $license_key = get_option(self::ID);
        ob_start();
        ?>
        <div class="wrap about-wrap gform_installation_progress_step_wrap">
			<h2>许可证密钥</h2>
			<form action="" method="POST">	
				<input type="hidden" name="action" value="<?php print $id;?>"/>		
				<div class="about-text">
    				<p>感谢支持！请在下面输入您的<a href="https://www.wpweixin.net/product/201.html" target="_blank">Woo微信支付</a>许可证密钥（已随订单邮件发给您了）！如有任何疑问，请访问我们的官网<a href="https://www.wpweixin.net" target="_blank">迅虎网络</a>或直接咨询<a href="http://wpa.qq.com/msgrd?v=3&uin=6347007&site=qq&menu=yes" target="_blank">售前客服</a>。</p>
            		<div>
            			<input type="text" class="regular-text" value="<?php print esc_attr($license_key )?>" name="license_key" placeholder="输入您的许可证密钥">
            		</div>
    			</div>
		
				<div>
				<input class="button button-primary" type="submit" value="提交" name="_next">
				<?php 
				if(isset($_POST['action'])&&!$GLOBALS[self::ID]){
				    ?><span style="color:red;">许可证密钥验证失败！</span><?php 
				}else if(isset($_POST['action'])&&$GLOBALS[self::ID]){
				    ?><span style="color:green;">许可证密钥验证成功！</span><?php
				} 
				?>
				</div>
			</form>
		</div>
		<?php 
		if($GLOBALS[self::ID]&&$HX_Wechat_WC_Payment_Gateway){
		    ?>
		    <script type="text/javascript">
				location.href='<?php print admin_url('admin.php?page=wc-settings&tab=checkout&section='.$HX_Wechat_WC_Payment_Gateway->section);?>';
		    </script>
		    <?php 
		}
        print ob_get_clean();
    }
    
    public function init(){
        require_once  $this->dir . '/class-xh-wechat-wc-payment-gateway.php';
        global $HX_Wechat_WC_Payment_Gateway;
        $HX_Wechat_WC_Payment_Gateway= new HX_Wechat_WC_Payment_Gateway ();
        add_action ( 'wp_ajax_'.HX_Wechat_WC_Payment_Gateway::HX_Wechat_LoopOrderStatus, array ($HX_Wechat_WC_Payment_Gateway,'Loop_Order_Status'),10);
        add_action ( 'wp_ajax_nopriv_'.HX_Wechat_WC_Payment_Gateway::HX_Wechat_LoopOrderStatus, array ($HX_Wechat_WC_Payment_Gateway,"Loop_Order_Status"),10);
        
        return $HX_Wechat_WC_Payment_Gateway;
    }
  
    public function get_avatar($avatar, $id_or_email=null, $size=null, $default=null, $alt=null, $args=null) {
        if (is_object ( $id_or_email ) && isset ( $id_or_email->comment_ID )) {
            $comment = get_comment ( $id_or_email );
            if ($comment) {
                return $this->get_user_avatar ( ( int ) $comment->user_id, $avatar, $args );
            }
            	
            return $avatar;
        }
    
        if (is_numeric ( $id_or_email )) {
            return $this->get_user_avatar ( $id_or_email, $avatar, $args );
        }
    
        if (is_string ( $id_or_email )) {
            $user = get_user_by_email ( $id_or_email );
            if ($user) {
                return $this->get_user_avatar ( ( int ) $user->ID, $avatar, $args );
            }
            	
            return $avatar;
            ;
        }
    
        if ($id_or_email instanceof WP_Post) {
            return $this->get_user_avatar ( ( int ) $id_or_email->post_author, $avatar, $args );
        }
    
        if ($id_or_email instanceof WP_Comment) {
            return $this->get_user_avatar ( ( int ) $id_or_email->user_id, $avatar, $args );
        }
    
        return $avatar;
    }
    
    public function get_user_avatar($user_ID, $default=null, $args=null) {
        $url = get_user_meta ( $user_ID, 'Headimgurl', true );
        if (empty ( $url )) {
            return $default;
        }
    
        return '<img src="' . $url . '" style="max-width:50px;max-height:50px; ' . ($args ['width'] ? ('width:' . ( int ) $args ['width'] . 'px') : '') . ';' . ($args ['height'] ? ('height:' . ( int ) $args ['height'] . 'px') : '') . '" />';
    }
    
    public function register_activation_hook(){
        global $wpdb;
    
        $table_red_envelope = $wpdb->prefix . "xh_wechat_red_envelope_history";
        $table_user_preference = $wpdb->prefix . "xh_wechat_user_preference";
       // $table_user_wechat = $wpdb->prefix . "xh_wechat_user";
    
        if ($wpdb->get_var ( "SHOW TABLES LIKE '$table_red_envelope'" ) != $table_red_envelope) {
            $sql = "CREATE TABLE `$table_red_envelope` (
            `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `user_id` BIGINT(20) NOT NULL,
            `total_amount` DECIMAL(18,2) NOT NULL,
            `type` VARCHAR(64) NOT NULL,
            `description` LONGTEXT NULL,
            `success` TINYINT(4) NOT NULL DEFAULT '1',
            `order_id` BIGINT(20) NULL DEFAULT NULL,
            `created_time` DATETIME NOT NULL,
            PRIMARY KEY (`id`),
            INDEX `type` (`type`)
            );";
            $wpdb->query ( $sql );
        }
    
//         if ($wpdb->get_var ( "SHOW TABLES LIKE '$table_user_wechat'" ) != $table_user_wechat) {
//             $sql = "CREATE TABLE `$table_user_wechat` (
//             `user_id` INT(11) NOT NULL,
//             `openid` VARCHAR(64) NOT NULL,
//             `created_time` DATETIME NOT NULL,
//             PRIMARY KEY (`user_id`),
//             INDEX `openid` (`openid`)
//             );";
//             $wpdb->query ( $sql );
//         }
    
        if ($wpdb->get_var ( "SHOW TABLES LIKE '$table_user_preference'" ) != $table_user_preference) {
            $sql = "CREATE TABLE `$table_user_preference` (
            `id` BIGINT(20) NOT NULL AUTO_INCREMENT,
            `user_id` BIGINT(20) NOT NULL,
            `ref_user_id` BIGINT(20) NOT NULL DEFAULT '0',
            `ref_open_id` VARCHAR(128) NOT NULL,/*推荐人的openid*/
            `post_id` BIGINT(20) NOT NULL,
            `created_time` DATETIME NOT NULL,
            `used` TINYINT(4) NOT NULL DEFAULT '0',
            PRIMARY KEY (`id`)
            );";
            $wpdb->query ( $sql );
        }else{
            /*v2.0.8*/
            $DB_NAME =DB_NAME;
            if($wpdb->get_var ("SELECT TABLE_NAME
                FROM INFORMATION_SCHEMA.COLUMNS
                WHERE TABLE_NAME = '$table_user_preference'
                AND TABLE_SCHEMA='$DB_NAME'
                AND COLUMN_NAME = 'ref_user_id'" ) != $table_user_preference){
    
                $wpdb->query ( "ALTER TABLE `$table_user_preference` ADD COLUMN `ref_user_id` BIGINT(20) NOT NULL DEFAULT '0';" );
                	
            }
            //v2.0.8 去掉 user_id  index
            if($wpdb->get_var("show index from `$table_user_preference` where Column_name='user_id';")==$table_user_preference){
                $wpdb->query ( "ALTER TABLE `$table_user_preference` DROP INDEX `user_id`;" );
            }
            	
            /*v2.0.8 end*/
        }
    
        $current = get_site_transient('update_plugins');
        if ( $current ) {
            set_site_transient( 'update_plugins', $current );
        }
    }
}