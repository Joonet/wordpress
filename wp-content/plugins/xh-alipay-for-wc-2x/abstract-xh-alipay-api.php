<?php
if (! defined('ABSPATH'))
    exit();
if(!class_exists('XH_Log')){
    require_once   'infrastructure/logger/class-xh-log.php';
}
require_once 'class-xh-alipay-url.php';
abstract class Abstract_XH_Alipay_Api{
    public $v,$t,$f,$i,$dir,$key,$u_u,$get;
    const ID='xh_alipay_for_wc';
    
    public function __construct(){
        $this->i = self::ID;
        $this->v=XH_ALIPAY_VERSION;
        $this->f=XH_ALIPAY_FILE;
        $this->dir=XH_ALIPAY_DIR;
        $this->get = $_GET;
        $this->u_u = admin_url("admin.php?page=woo_alipay_license");
       
        load_plugin_textdomain( XH_ALIPAY, false,dirname( plugin_basename( __FILE__ ) ) . '/lang/'  );
        register_activation_hook ( XH_ALIPAY_FILE, array($this,'register_activation_hook') );
        register_deactivation_hook ( XH_ALIPAY_FILE, array($this,'register_deactivation_hook') );
        XH_Log::Init ( new XH_Log_File_Handler ( XH_ALIPAY_DIR . "/logs/" . date ( 'Y-m-d' ) . '.log' ), 15 );
      
        add_action('admin_menu',array($this,'admin_menu') );
       
        if(isset($_POST['action'])
            &&isset($_POST['license_key'])
            &&$_POST['action']===md5(self::ID)){
            $license_key = trim($_POST['license_key']);
            update_option(self::ID, $license_key);
        }
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
        if(!isset($_GET['page'])||$_GET['page']!='woo_alipay_license'){
            if($GLOBALS[self::ID]){
                return;
            }
        }
        
        add_menu_page( '【支付宝】授权', '【支付宝】授权', 'administrator', 'woo_alipay_license',array($this,'woo_alipay_license'));
    }
    public function woo_alipay_license(){
      
        global $HX_Alipay_WC_Payment_Gateway;
        if($GLOBALS[self::ID]&&$HX_Alipay_WC_Payment_Gateway){
            wp_redirect(admin_url('admin.php?page=wc-settings&tab=checkout&section='.$HX_Alipay_WC_Payment_Gateway->id));
            return ;
        }
        $id=md5(self::ID);
        $license_key = get_option(self::ID);
        ob_start();
		?>
        <div class="wrap about-wrap gform_installation_progress_step_wrap">
			<h2>许可证密钥</h2>
			
			<form action="" method="POST">	
				<input type="hidden" name="action" value="<?php print $id;?>"/>		
				<div class="about-text">
    				<p>感谢支持！请在下面输入您的<a href="https://www.wpweixin.net/product/246.html" target="_blank">Woo支付宝</a>许可证密钥（已随订单邮件发给您了）！如有任何疑问，请访问我们的官网<a href="https://www.wpweixin.net" target="_blank">迅虎网络</a>或直接咨询<a href="http://wpa.qq.com/msgrd?v=3&uin=6347007&site=qq&menu=yes" target="_blank">售前客服</a>。</p>
            		<div>
            			<input type="text" class="regular-text" value="<?php print esc_attr($license_key )?>" name="license_key" placeholder="输入您的许可证密钥">
            		</div>
    			</div>
		
				<div><input class="button button-primary" type="submit" value="提交">
				<?php 
				if(isset($_POST['action'])&&!$GLOBALS[self::ID]){
				    ?><span style="color:red;">许可证密钥验证失败！</span><?php 
				}
				?>
				</div>
			</form>
		</div>
        <?php 
        print ob_get_clean();
    }
    
    public function init(){
        require_once  $this->dir . '/class-xh-alipay-wc-payment-gateway.php';
        global $HX_Alipay_WC_Payment_Gateway;
        $HX_Alipay_WC_Payment_Gateway= new HX_Alipay_WC_Payment_Gateway ();
        return $HX_Alipay_WC_Payment_Gateway;
    }
  
    public function register_activation_hook(){
        $current = get_site_transient('update_plugins');
        if ( $current ) {
            set_site_transient( 'update_plugins', $current );
        }
    }
}