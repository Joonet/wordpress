<?php
if (! defined('ABSPATH')) {
    exit();
}

abstract  class Abstract_HX_WC_Payment_Gateway extends WC_Payment_Gateway{
    public $section;
    public static $my_states = array(
        'CN1' => '云南',
        'CN2' => '北京',
        'CN3' => '天津',
        'CN4' => '河北',
        'CN5' => '山西',
        'CN6' => '内蒙古',
        'CN7' => '辽宁',
        'CN8' => '吉林',
        'CN9' => '黑龙江',
        'CN10' => '上海',
        'CN11' => '江苏',
        'CN12' => '浙江',
        'CN13' => '安徽',
        'CN14' => '福建',
        'CN15' => '江西',
        'CN16' => '山东',
        'CN17' => '河南',
        'CN18' => '河北',
        'CN19' => '湖南',
        'CN20' => '广东',
        'CN21' => '广西',
        'CN22' => '海南',
        'CN23' => '重庆',
        'CN24' => '四川',
        'CN25' => '贵州',
        'CN26' => '陕西',
        'CN27' => '甘肃',
        'CN28' => '青海',
        'CN29' => '宁夏',
        'CN30' => '澳门',
        'CN31' => '台北',
        'CN32' => '新疆'
    );
    
    public function guid()
    {
        $guid = '';
        if (function_exists('com_create_guid')) {
            $guid = com_create_guid();
        } else {
            mt_srand((double) microtime() * 10000); // optional for php 4.2.0 and up.
            $charid = strtoupper(md5(uniqid(rand(), true)));
            $hyphen = chr(45); // "-"
            $uuid = chr(123) . // "{"
            substr($charid, 0, 8) . $hyphen . substr($charid, 8, 4) . $hyphen . substr($charid, 12, 4) . $hyphen . substr($charid, 16, 4) . $hyphen . substr($charid, 20, 12) . chr(125); // "}"
            $guid = $uuid;
        }
    
        return str_replace('-', '', trim($guid, '{}'));
    }
    public  function remove_emoji($source) {
        // Match Emoticons
        $regexEmoticons = '/[\x{1F600}-\x{1F64F}]/u';
        $clean_text = preg_replace($regexEmoticons, '', $source);
    
        // Match Miscellaneous Symbols and Pictographs
        $regexSymbols = '/[\x{1F300}-\x{1F5FF}]/u';
        $clean_text = preg_replace($regexSymbols, '', $clean_text);
    
        // Match Transport And Map Symbols
        $regexTransport = '/[\x{1F680}-\x{1F6FF}]/u';
        $clean_text = preg_replace($regexTransport, '', $clean_text);
    
        // Match Miscellaneous Symbols
        $regexMisc = '/[\x{2600}-\x{26FF}]/u';
        $clean_text = preg_replace($regexMisc, '', $clean_text);
    
        // Match Dingbats
        $regexDingbats = '/[\x{2700}-\x{27BF}]/u';
        $clean_text = preg_replace($regexDingbats, '', $clean_text);
    
        return $clean_text;
    }
    public function generate_user_login($nickname){
        $nickname = $this->remove_emoji($nickname);
        if(empty($nickname)){
            $nickname = $this->guid();
        }
    
        $index=0;
        while (username_exists($nickname)){
            if($index++==0){
                $nickname=$nickname.'_'.date('yz');//年+一年中的第N天
                continue;
            }
    
            if($index==1){
                $nickname=$nickname.'_'.date('ymdHis');//年+一年中的第N天
                continue;
            }
    
            //加随机数
            $nickname.=mt_rand(1000, 9999);
            if(strlen($nickname)>40){
                $nickname =$this->guid();
            }
    
            //尝试次数过多
            if($index>5){
                return $this->guid();
            }
        }
    
        return $nickname;
    }
    public function plugin_action_links($links) {
        if($GLOBALS[Abstract_XH_Wechat_Api::ID])
        return array_merge ( array (
            'settings' => '<a href="' . admin_url ( 'admin.php?page=wc-settings&tab=checkout&section='.$this->section ) . '">设置</a>'
        ), $links );
        return array_merge ( array (
            'settings' => '<a href="' . admin_url ( "admin.php?page=woo_wechat_license") . '">设置</a>'
        ), $links );
    }
    
    public function get_field_key( $key ) {
        return $this->plugin_id . $this->id . '_' . $key;
    }
    public function generate_text_html( $key, $data ) {
    
        $field    = $this->get_field_key( $key );
        $defaults = array(
            'title'             => '',
            'disabled'          => false,
            'class'             => '',
            'css'               => '',
            'placeholder'       => '',
            'type'              => 'text',
            'desc_tip'          => false,
            'description'       => '',
            'custom_attributes' => array()
        );
    
        $data = wp_parse_args( $data, $defaults );
    
        ob_start();
        ?>
		<tr valign="top" style="<?php print (isset($data['hidden'])?'display:none;':'')?>">
			<th scope="row" class="titledesc">
				<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
				<?php echo $this->get_tooltip_html( $data ); ?>
			</th>
			<td class="forminp">
				<fieldset>
					<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
					<input class="input-text regular-input <?php echo esc_attr( $data['class'] ); ?>" type="<?php echo esc_attr( $data['type'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="<?php echo esc_attr( $this->get_option( $key ) ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> />
					<?php echo $this->get_description_html( $data ); ?>
				</fieldset>
			</td>
		</tr>
		<?php

		return ob_get_clean();
	}
	public function generate_select_html( $key, $data ) {
	
		$field    = $this->get_field_key( $key );
		$defaults = array(
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => array(),
				'options'           => array()
		);
	
		$data = wp_parse_args( $data, $defaults );
	
		ob_start();
		?>
			<tr valign="top" style="<?php print (isset($data['hidden'])?'display:none;':'')?>">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
					<?php echo $this->get_tooltip_html( $data ); ?>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<select class="select <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>>
							<?php foreach ( (array) $data['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, esc_attr( $this->get_option( $key ) ) ); ?>><?php echo esc_attr( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php echo $this->get_description_html( $data ); ?>
					</fieldset>
				</td>
			</tr>
			<?php
	
			return ob_get_clean();
		}
		
	public function generate_checkbox_html( $key, $data ) {
	
		$field    = $this->get_field_key( $key );
		$defaults = array(
				'title'             => '',
				'label'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => array()
		);
	
		$data = wp_parse_args( $data, $defaults );
	
		if ( ! $data['label'] ) {
			$data['label'] = $data['title'];
		}
	
		ob_start();
		?>
			<tr valign="top" style="<?php print (isset($data['hidden'])?'display:none;':'')?>">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
					<?php echo $this->get_tooltip_html( $data ); ?>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<label for="<?php echo esc_attr( $field ); ?>">
						<input <?php disabled( $data['disabled'], true ); ?> class="<?php echo esc_attr( $data['class'] ); ?>" type="checkbox" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" value="1" <?php checked( $this->get_option( $key ), 'yes' ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?> /> <?php echo wp_kses_post( $data['label'] ); ?></label><br/>
						<?php echo $this->get_description_html( $data ); ?>
					</fieldset>
				</td>
			</tr>
			<?php
	
			return ob_get_clean();
		}
	
	public function generate_textarea_html( $key, $data ) {
	
		$field    = $this->get_field_key( $key );
		$defaults = array(
				'title'             => '',
				'disabled'          => false,
				'class'             => '',
				'css'               => '',
				'placeholder'       => '',
				'type'              => 'text',
				'desc_tip'          => false,
				'description'       => '',
				'custom_attributes' => array()
		);
	
		$data = wp_parse_args( $data, $defaults );
	
		ob_start();
		?>
			<tr valign="top"  style="<?php print (isset($data['hidden'])?'display:none;':'')?>">
				<th scope="row" class="titledesc">
					<label for="<?php echo esc_attr( $field ); ?>"><?php echo wp_kses_post( $data['title'] ); ?></label>
					<?php echo $this->get_tooltip_html( $data ); ?>
				</th>
				<td class="forminp">
					<fieldset>
						<legend class="screen-reader-text"><span><?php echo wp_kses_post( $data['title'] ); ?></span></legend>
						<textarea rows="3" cols="20" class="input-text wide-input <?php echo esc_attr( $data['class'] ); ?>" name="<?php echo esc_attr( $field ); ?>" id="<?php echo esc_attr( $field ); ?>" style="<?php echo esc_attr( $data['css'] ); ?>" placeholder="<?php echo esc_attr( $data['placeholder'] ); ?>" <?php disabled( $data['disabled'], true ); ?> <?php echo $this->get_custom_attribute_html( $data ); ?>><?php echo esc_textarea( $this->get_option( $key ) ); ?></textarea>
						<?php echo $this->get_description_html( $data ); ?>
					</fieldset>
				</td>
			</tr>
			<?php
	
			return ob_get_clean();
		}
}