<?php 
remove_action('woocommerce_before_main_content',    'woocommerce_output_content_wrapper', 10);
remove_action('woocommerce_after_main_content',     'woocommerce_output_content_wrapper_end', 10);
remove_action('woocommerce_sidebar',                'woocommerce_get_sidebar', 10);
remove_action('woocommerce_before_checkout_form',           array( YITH_WooCommerce_Additional_Uploads::get_instance(), 'show_upload_section_on_checkout' ), 10);
remove_action('woocommerce_thankyou_order_received_text',   array( YITH_WooCommerce_Additional_Uploads::get_instance(), 'show_upload_section_on_thankyoupage' ), 10, 2);
remove_action('add_meta_boxes',                             array( YITH_WooCommerce_Additional_Uploads::get_instance(), 'add_metaboxes' ), 10);
//remove_action('woocommerce_view_order',                     array( YITH_WooCommerce_Additional_Uploads::get_instance(), 'show_upload_section_on_view_page' ), 1);


add_action('after_setup_theme',                             'woocommerce_support');
add_action('woocommerce_before_main_content',               'my_theme_wrapper_start', 15);
add_action('woocommerce_after_main_content',                'my_theme_wrapper_end', 15);
add_action('wp_print_scripts',                              'wc_ninja_remove_password_strength', 100);
add_action('woocommerce_admin_order_data_after_billing_address', 'my_custom_checkout_field_display_admin_order_meta', 10, 1 );
add_action('woocommerce_admin_order_data_after_shipping_address','my_shipping_checkout_field_display_admin_order_meta', 10, 1 );
//add_action('woocommerce_after_checkout_billing_form',       'my_custom_checkout_field' );
add_action('woocommerce_before_order_notes',                'my_custom_checkout_field' );
add_action('woocommerce_checkout_update_order_meta',        'my_custom_checkout_field_update_order_meta' );
add_action('woocommerce_save_account_details',              'my_woocommerce_save_account_details' );
add_action('woocommerce_before_order_itemmeta',             'my_woocommerce_before_order_itemmeta', 10, 3 );
add_action('payment_box_bacs',                              array( YITH_WooCommerce_Additional_Uploads::get_instance(), 'show_upload_section_on_checkout' ) );
add_action('woocommerce_thankyou',                          'my_woocommerce_thankyou', 5 );
add_action('add_meta_boxes',                                'my_shop_order_add_metaboxes' ); 
add_action('woocommerce_cart_calculate_fees',               'woo_add_cart_fee', 15);
add_action('woocommerce_get_order_item_totals',             'my_woocommerce_get_order_item_totals', 10, 2);
add_action('woocommerce_checkout_update_order_review',      'my_woocommerce_checkout_update_order_review');
add_action('woocommerce_customer_save_address',             'redirect_woocommerce_customer_save_address');
add_action('woocommerce_checkout_process',                  'my_custom_checkout_field_process');


//add_filter('woocommerce_enqueue_styles',            '__return_empty_array');
add_filter('load_textdomain_mofile',               'load_custom_plugin_translation_file', 99, 2 );
add_filter('woocommerce_add_to_cart_fragments',    'storefront_cart_link_fragment');
add_filter('add_to_cart_fragments',                'storefront_cart_link_fragment');
add_filter('woocommerce_account_menu_items',       'my_woocommerce_account_menu_items');
add_filter('woocommerce_checkout_fields',          'custom_override_checkout_fields', 99);

add_filter('woocommerce_save_account_details_required_fields', 'my_woocommerce_save_account_details_required_fields', 99);
add_filter('woocommerce_default_address_fields',   'my_woocommerce_default_address_fields');
add_filter('woocommerce_states',                   'custom_woocommerce_states' );
add_filter('woocommerce_cart_ready_to_calc_shipping', 'disable_shipping_calc_on_cart', 99);
add_filter('woocommerce_get_cart_page_id',         'filter_woocommerce_get_page_id' );
add_filter('woocommerce_get_checkout_page_id',     'filter_woocommerce_get_page_id' );
add_filter('woocommerce_get_myaccount_page_id',    'filter_woocommerce_get_page_id' );
//add_filter('woocommerce_get_cart_page_permalink',  'filter_woocommerce_get_cart_page_permalink' );
add_filter('woocommerce_gateway_title',            'my_filter_lang_text');
add_filter('woocommerce_gateway_description',      'my_filter_lang_text');
add_filter('woocommerce_order_get_items',          'my_woocommerce_order_get_items');
add_filter('woocommerce_cart_shipping_method_full_label',   'my_woocommerce_cart_shipping_method_full_label', 10, 2);
add_filter('woocommerce_shipping_chosen_method',   'my_woocommerce_shipping_chosen_method' );

add_filter('woocommerce_billing_fields',           'custom_woocommerce_billing_fields');
add_filter('woocommerce_shipping_fields',          'custom_woocommerce_shipping_fields');


function woocommerce_support() {
    add_theme_support( 'woocommerce' );
}

function my_theme_wrapper_start() {
    //get_template_part('templates/woo', 'wrapper_start');
}

function my_theme_wrapper_end() {
    //get_template_part('templates/woo', 'wrapper_end');
}

function wc_ninja_remove_password_strength() {
	if ( wp_script_is( 'wc-password-strength-meter', 'enqueued' ) ) {
		wp_dequeue_script( 'wc-password-strength-meter' );
	}
}

if ( ! function_exists( 'storefront_is_woocommerce_activated' ) ) {
	function storefront_is_woocommerce_activated() {
		return class_exists( 'woocommerce' ) ? true : false;
	}
}

if ( ! function_exists( 'storefront_cart_link_fragment' ) ) {
	function storefront_cart_link_fragment( $fragments = array() ) {
		global $woocommerce;
		ob_start();
		storefront_cart_link();
		$fragments['a.cart-contents'] = ob_get_clean();
        ob_end_clean();
        
		return $fragments;
	}
}

if ( ! function_exists( 'storefront_cart_link' ) ) {
	function storefront_cart_link() {
		?>
			<a href="<?php echo wc_get_page_permalink('cart'); ?>" title="<?php esc_attr_e( 'View your cart', '3dprint' ); ?>" class="cart-contents label-cart">
                <i class="fa fa-shopping-cart" aria-hidden="true"></i>
                <?php echo WC()->cart->get_cart_contents_count(); ?> <?php _e( 'Commodity', '3dprint' ); ?> - <?php echo wp_kses_data( WC()->cart->get_cart_subtotal() ); ?> <?php _e( 'Yuan', '3dprint' ); ?>
            </a>            
		<?php
	}
}

if ( ! function_exists( 'storefront_header_cart' ) ) {
	function storefront_header_cart() {
		if ( storefront_is_woocommerce_activated() ) {
			if ( is_cart() ) {
				$class = 'current-menu-item';
			} else {
				$class = '';
			}
		?>
        <div class="site-header-cart cart">
            <?php storefront_cart_link(); ?>            
            <div class="inner-cart"><?php the_widget( 'WC_Widget_Cart', 'title=' ); ?></div>
        </div>
		<?php
		}
	}
}

function my_woocommerce_account_menu_items( $menu_items ) {
	unset( $menu_items['dashboard'] );
	unset( $menu_items['downloads'] );
	unset( $menu_items['customer-logout'] );

	return $menu_items;
}


function custom_override_checkout_fields( $fields ) {    
    if ( isset( $fields['order']['order_comments'] ) )
        unset( $fields['order']['order_comments'] );
    
    if ( isset( $fields['billing']['billing_address_2'] ) )
        unset( $fields['billing']['billing_address_2'] );
    
    if ( isset( $fields['billing']['billing_last_name'] ) )
        unset( $fields['billing']['billing_last_name'] );
    
    if ( isset( $fields['billing']['billing_company'] ) )
        unset( $fields['billing']['billing_company'] );    
    
    $fields['billing']['billing_first_name']['placeholder'] = __( 'Name', '3dprint' );
    $fields['billing']['billing_first_name']['label'] = __( 'Name', '3dprint' );
    $fields['billing']['billing_first_name']['class'] = array('input-one');
    
    $fields['billing']['billing_email']['class']   = array('input-two input-last');
    $fields['billing']['billing_country']['class'] = array('input-one');
    
    $fields['billing']['billing_state']['placeholder'] = __( 'Province', '3dprint' );
    $fields['billing']['billing_state']['label'] = __( 'Province', '3dprint' );
    $fields['billing']['billing_state']['class'] = array('input-two input-last');
    
    $fields['billing']['billing_postcode']['placeholder'] = __( 'Postal Code', '3dprint' );
    $fields['billing']['billing_postcode']['label'] = __( 'Postal Code', '3dprint' );
    $fields['billing']['billing_postcode']['class'] = array('input-one-theree');    
    
    $fields['billing']['billing_city']['class'] = array('input-two-theree');    
    $fields['billing']['billing_address_1']['class'] = array('input-theree-theree');    
    
    $fields['billing']['billing_phone']['placeholder'] = __( 'Phone No.1', '3dprint' );
    $fields['billing']['billing_phone']['label'] = __( 'Phone No.1', '3dprint' );
    $fields['billing']['billing_phone']['class'] = array('input-one');
    $fields['billing']['billing_phone']['clear'] = false;
    
    
    $fields['billing']['billing_phone_2'] = array(
        'label'         => __('Phone No.2', '3dprint'),
        'placeholder'   => _x('Phone No.2', 'placeholder', '3dprint'),
        'required'      => false,        
        'clear'         => false,
        'class'         => array('input-two input-last'),
        'validate'      => array( 'phone' ),
    );
    
    $order = array(
        'billing_first_name', 
        'billing_email', 
        'billing_phone',
        'billing_phone_2',
        'billing_country', 
        'billing_state', 
        'billing_city', 
        'billing_address_1', 
        'billing_postcode',
    );
    
    $ordered_fields = array();
    foreach( $order as $field ) {
        $ordered_fields[$field] = $fields["billing"][$field];
    }    
    $fields["billing"] = $ordered_fields;
    
    /* ------------- */
    if ( isset( $fields['shipping']['shipping_address_2'] ) )
        unset( $fields['shipping']['shipping_address_2'] );
    
    if ( isset( $fields['shipping']['shipping_last_name'] ) )
        unset( $fields['shipping']['shipping_last_name'] );
    
    if ( isset( $fields['shipping']['shipping_company'] ) )
        unset( $fields['shipping']['shipping_company'] );    
    
    $fields['shipping']['shipping_first_name']['placeholder'] = __( 'Name', '3dprint' );
    $fields['shipping']['shipping_first_name']['label'] = __( 'Name', '3dprint' );
    $fields['shipping']['shipping_first_name']['class'] = array('input-one');
    
    $fields['shipping']['shipping_email']['class']   = array('input-two input-last');
    $fields['shipping']['shipping_country']['class'] = array('input-one');
    
    $fields['shipping']['shipping_state']['placeholder'] = __( 'Province', '3dprint' );
    $fields['shipping']['shipping_state']['label'] = __( 'Province', '3dprint' );
    $fields['shipping']['shipping_state']['class'] = array('input-two input-last');
    
    $fields['shipping']['shipping_postcode']['placeholder'] = __( 'Postal Code', '3dprint' );
    $fields['shipping']['shipping_postcode']['label'] = __( 'Postal Code', '3dprint' );
    $fields['shipping']['shipping_postcode']['class'] = array('input-one-theree');  
    
    $fields['shipping']['shipping_city']['class'] = array('input-two-theree');        
    $fields['shipping']['shipping_address_1']['class'] = array('input-theree-theree');  
    
    $fields['shipping']['shipping_phone']['placeholder'] = __( 'Phone No.1', '3dprint' );
    $fields['shipping']['shipping_phone']['label'] = __( 'Phone No.1', '3dprint' );
    $fields['shipping']['shipping_phone']['class'] = array('input-one');
    $fields['shipping']['shipping_phone']['clear'] = false;
    $fields['shipping']['shipping_phone']['required'] = 1;
    
    $fields['shipping']['shipping_email'] = array(
        'label'         => __('Email Address', '3dprint'),
        'placeholder'   => _x('Email Address', 'placeholder', '3dprint'),
        'required'      => false,        
        'clear'         => false,
        'class'         => array('input-two input-last'),
        'validate'      => array( 'email' ),
    );
    
    $fields['shipping']['shipping_phone_2'] = array(
        'label'         => __('Phone No.2', '3dprint'),
        'placeholder'   => _x('Phone No.2', 'placeholder', '3dprint'),
        'required'      => false,        
        'clear'         => false,
        'class'         => array('input-two input-last'),
        'validate'      => array( 'phone' ),
    );   
    
    
    $order = array(
        'shipping_first_name', 
        'shipping_email', 
        'shipping_phone',
        'shipping_phone_2',
        'shipping_country', 
        'shipping_state', 
        'shipping_city', 
        'shipping_address_1', 
        'shipping_postcode',
    );
    
    $ordered_fields = array();
    foreach( $order as $field ) {
        $ordered_fields[$field] = $fields["shipping"][$field];
    }    
    $fields["shipping"] = $ordered_fields;

    return $fields;
}


function custom_woocommerce_billing_fields( $fields_billing ) {
	if ( isset( $fields_billing['billing_address_2'] ) )
        unset( $fields_billing['billing_address_2'] );
    
    if ( isset( $fields_billing['billing_last_name'] ) )
        unset( $fields_billing['billing_last_name'] );
    
    if ( isset( $fields_billing['billing_company'] ) )
        unset( $fields_billing['billing_company'] ); 
    
    
    $fields_billing['billing_first_name']['placeholder'] = __( 'Name', '3dprint' );
    $fields_billing['billing_first_name']['label'] = __( 'Name', '3dprint' );
    $fields_billing['billing_first_name']['class'] = array('input-one');
    
    $fields_billing['billing_email']['class']   = array('input-two input-last');
    $fields_billing['billing_country']['class'] = array('input-one');
    
    $fields_billing['billing_state']['placeholder'] = __( 'Province', '3dprint' );
    $fields_billing['billing_state']['label'] = __( 'Province', '3dprint' );
    $fields_billing['billing_state']['class'] = array('input-two input-last');
    
    $fields_billing['billing_postcode']['placeholder'] = __( 'Postal Code', '3dprint' );
    $fields_billing['billing_postcode']['label'] = __( 'Postal Code', '3dprint' );
    $fields_billing['billing_postcode']['class'] = array('input-one-theree');    
    
    $fields_billing['billing_city']['class'] = array('input-two-theree');    
    $fields_billing['billing_address_1']['class'] = array('input-theree-theree');    
    
    $fields_billing['billing_phone']['placeholder'] = __( 'Phone No.1', '3dprint' );
    $fields_billing['billing_phone']['label'] = __( 'Phone No.1', '3dprint' );
    $fields_billing['billing_phone']['class'] = array('input-one');
    $fields_billing['billing_phone']['clear'] = false;
    
    
    $fields_billing['billing_phone_2'] = array(
        'label'         => __('Phone No.2', '3dprint'),
        'placeholder'   => _x('Phone No.2', 'placeholder', '3dprint'),
        'required'      => false,        
        'clear'         => false,
        'class'         => array('input-two input-last'),
    );
    
    $order = array(
        'billing_first_name', 
        'billing_email', 
        'billing_phone',
        'billing_phone_2',
        'billing_country', 
        'billing_state', 
        'billing_city', 
        'billing_address_1', 
        'billing_postcode',
    );
    
    foreach( $order as $field ) {
        $ordered_fields[ $field ] = $fields_billing[ $field ];
    }    
    $fields_billing = $ordered_fields;

    return $fields_billing;
}


function custom_woocommerce_shipping_fields( $fields_shipping ) {
	if ( isset( $fields_shipping['shipping_address_2'] ) )
        unset( $fields_shipping['shipping_address_2'] );
    
    if ( isset( $fields_shipping['shipping_last_name'] ) )
        unset( $fields_shipping['shipping_last_name'] );
    
    if ( isset( $fields_shipping['shipping_company'] ) )
        unset( $fields_shipping['shipping_company'] ); 
    
    
    $fields_shipping['shipping_first_name']['placeholder'] = __( 'Name', '3dprint' );
    $fields_shipping['shipping_first_name']['label'] = __( 'Name', '3dprint' );
    $fields_shipping['shipping_first_name']['class'] = array('input-one');
    
    $fields_shipping['shipping_email']['placeholder'] = __( 'Email Address', '3dprint' );
    $fields_shipping['shipping_email']['label']   = __( 'Email Address', '3dprint' );
    $fields_shipping['shipping_email']['class']   = array('input-two input-last');
    $fields_shipping['shipping_country']['class'] = array('input-one');
    
    $fields_shipping['shipping_state']['placeholder'] = __( 'Province', '3dprint' );
    $fields_shipping['shipping_state']['label'] = __( 'Province', '3dprint' );
    $fields_shipping['shipping_state']['class'] = array('input-two input-last');
    
    $fields_shipping['shipping_postcode']['placeholder'] = __( 'Postal Code', '3dprint' );
    $fields_shipping['shipping_postcode']['label'] = __( 'Postal Code', '3dprint' );
    $fields_shipping['shipping_postcode']['class'] = array('input-one-theree');    
    
    $fields_shipping['shipping_city']['class'] = array('input-two-theree');    
    $fields_shipping['shipping_address_1']['class'] = array('input-theree-theree');    
    
    $fields_shipping['shipping_phone']['placeholder'] = __( 'Phone No.1', '3dprint' );
    $fields_shipping['shipping_phone']['label'] = __( 'Phone No.1', '3dprint' );
    $fields_shipping['shipping_phone']['class'] = array('input-one');
    $fields_shipping['shipping_phone']['clear'] = false;
    
    
    $fields_shipping['shipping_phone_2'] = array(
        'label'         => __('Phone No.2', '3dprint'),
        'placeholder'   => _x('Phone No.2', 'placeholder', '3dprint'),
        'required'      => false,        
        'clear'         => false,
        'class'         => array('input-two input-last'),
    );
    
    $order = array(
        'shipping_first_name', 
        'shipping_email', 
        'shipping_phone',
        'shipping_phone_2',
        'shipping_country', 
        'shipping_state', 
        'shipping_city', 
        'shipping_address_1', 
        'shipping_postcode',
    );
    
    foreach( $order as $field ) {
        $ordered_fields[ $field ] = $fields_shipping[ $field ];
    }    
    $fields_shipping = $ordered_fields;

    return $fields_shipping;
}


function my_custom_checkout_field_display_admin_order_meta($order){
    echo '<div class="address">';
        echo '<p><strong>'. __( 'Phone No.2', '3dprint' ) .':</strong> ' . get_post_meta( $order->id, '_billing_phone_2', true ) . '</p>';
        //echo '<p><strong>'. __( 'Sunfeng', '3dprint' ) .':</strong> ' . get_post_meta( $order->id, 'sunfeng', true ) . '</p>';
        echo '<p><strong>'. __( 'Tax', '3dprint' ) .':</strong> ' . get_post_meta( $order->id, 'vat_invoice', true ) . '</p>';    
        echo '<p><strong>'. __( 'Company Name', '3dprint' ) .':</strong> ' . get_post_meta( $order->id, 'company_name', true ) . '</p>';    
    echo '</div>';
}


function my_shipping_checkout_field_display_admin_order_meta($order){
    echo '<div class="address">';
        echo '<p><strong>'. __( 'Email', '3dprint' ) .':</strong> ' . get_post_meta( $order->id, '_shipping_email', true ) . '</p>';  
        echo '<p><strong>'. __( 'Phone', '3dprint' ) .':</strong> ' . get_post_meta( $order->id, '_shipping_phone', true ) . '</p>';  
        echo '<p><strong>'. __( 'Phone No.2', '3dprint' ) .':</strong> ' . get_post_meta( $order->id, '_shipping_phone_2', true ) . '</p>';  
    echo '</div>';
}


function my_custom_checkout_field( $checkout ) {
    $user_id = get_current_user_id(); 
    
    $class = '';
    $sunfeng_label = '';
    if ( $user_id ) 
        $sunfeng  = get_user_meta( $user_id, 'sunfeng', true ); 
    else 
        $sunfeng  = WC()->session->get('sunfeng');
    
    $packages = WC()->shipping->get_packages();
    foreach ( $packages[0]['rates'] as $i => $rate ) {
        if ( $i == $sunfeng )
            $sunfeng_label = my_filter_lang_text( $rate->label );
    } 
    
    echo '<div id="my_delivery"><h3>'. __( 'Delivery Means', '3dprint' ) .'</h3>'; if ( $sunfeng_label ) echo ' <a href="#" class="show_hide_fields">('. __( 'Change', '3dprint' ) .')</a>';

        if ( $sunfeng_label ) {
            $class = 'nodisplay';            
            echo '<div class="clear"><p>'. $sunfeng_label .'</p></div>';
        }
        
        $options = array();
        foreach ( $packages[0]['rates'] as $i => $rate ) {
            $options[ $i ] = my_filter_lang_text( $rate->label );
        }  
        
        echo '<div class="hide_fields clearfix '. $class .'">';
        woocommerce_form_field( 'sunfeng', array(
            'type'          => 'select',
            'options'       => $options,
            'class'         => array(''),
            'label'         => false,
            'required'      => true,
            'placeholder'   => false,
            ), $sunfeng );
        echo '</div>';

    echo '</div>';
    
    $class = $vat_invoice_label = '';   
    
    if ( $user_id )
        $vat_invoice  = get_user_meta( $user_id, 'vat_invoice', true );
    else 
        $vat_invoice = absint( WC()->session->get('vat_invoice') );
    
    if ( $vat_invoice ) 
        $vat_invoice_label = $vat_invoice . '%';
    else if ( $vat_invoice == '0' )
        $vat_invoice_label = _x('None', 'form', '3dprint');
    
    $company_name = get_user_meta( $user_id, 'company_name', true );    
    
    echo '<div id="my_invoice_info"><h3>'. __( 'Invoice Info', '3dprint' ) .'</h3> '; if ( $vat_invoice_label && $company_name ) echo ' <a href="#" class="show_hide_fields">('. __( 'Change', '3dprint' ) .')</a>';

        if ( $vat_invoice_label && $company_name ) {
            $class = 'nodisplay';            
            echo '<div class="clear"><p>'. $vat_invoice_label .', '.  $company_name .'</p></div>';
        }
    
        echo '<div class="hide_fields clearfix '. $class .'">';
        woocommerce_form_field( 'vat_invoice', array(
            'type'          => 'select',
            'options'       => array(
                '0'   => _x('None', 'form', '3dprint'),
                '3'   => '3%',
                '17'  => '17%',
            ),
            'class'         => array('input-one'),
            'label'         => __( 'VAT invoice', '3dprint' ),
            'required'      => false,
            'placeholder'   => __( 'VAT invoice', '3dprint' ),
            ), $vat_invoice );
            
        woocommerce_form_field( 'company_name', array(
            'type'          => 'text',
            'class'         => array('input-two'),
            'label'         => __( 'Customer Company Name', '3dprint' ),
            'required'      => false,
            'placeholder'   => __( 'Customer Company Name', '3dprint' ),
            ), $company_name );
        echo '</div>';
        
    echo '</div>';
}


function my_custom_checkout_field_update_order_meta( $order_id ) {
    $user_id = get_current_user_id(); 
    
    if ( ! empty( $_POST['sunfeng'] ) ) {
        update_post_meta( $order_id, 'sunfeng', sanitize_text_field( $_POST['sunfeng'] ) );
        if ( $user_id )
            update_user_meta( $user_id,  'sunfeng', sanitize_text_field( $_POST['sunfeng'] ) );
    }
    if ( ! empty( $_POST['vat_invoice'] ) ) {
        update_post_meta( $order_id, 'vat_invoice', absint( sanitize_text_field( $_POST['vat_invoice'] ) ) );
        if ( $user_id )
            update_user_meta( $user_id,  'vat_invoice', absint( sanitize_text_field( $_POST['vat_invoice'] ) ) );
    }
    if ( ! empty( $_POST['company_name'] ) ) {
        update_post_meta( $order_id, 'company_name', sanitize_text_field( $_POST['company_name'] ) );
        if ( $user_id )
            update_user_meta( $user_id,  'company_name', sanitize_text_field( $_POST['company_name'] ) );
    }
}


function my_woocommerce_save_account_details_required_fields( $data ) {
    //unset( $data['account_last_name'] );
    
    return $data;
}


function my_woocommerce_save_account_details( $user_id ) {
    $user = get_user_by( 'ID', $user_id );
    update_user_meta( $user_id, 'billing_first_name', $user->first_name );
}


function my_woocommerce_default_address_fields( $fields ) {
    unset( $fields['last_name'] );
    
    return $fields;
}


function my_woocommerce_before_order_itemmeta( $item_id, $item, $_product ) {
    $link_file = get_post_meta( $_product->id, 'file', 1 );
    echo '<br/><strong>File:</strong> <a href="'. $link_file .'">'. basename( $link_file ) .'</a>';
}


function my_woocommerce_thankyou( $order_id ) { 
    if ( class_exists ( 'YITH_WooCommerce_Additional_Uploads' ) ) {
        $uploaded_file = WC ()->session->get ( "ywau_order_file_uploaded" );
        if ( $uploaded_file != null ) {
            //  change file folder, using the real order id
            $relative_path = sprintf ( "%s/%s", $order_id, basename ( $uploaded_file ) );

            $starting_path = sprintf ( "%s/%s",
                YITH_YWAU_SAVE_DIR,
                untrailingslashit ( $uploaded_file ) );

            $destination_path = sprintf ( "%s/%s",
                YITH_YWAU_SAVE_DIR,
                untrailingslashit ( $relative_path ) );

            $new_dir = sprintf ( "%s/%s",
                YITH_YWAU_SAVE_DIR,
                $order_id );

            wp_mkdir_p ( $new_dir );

            //  move file to new folder
            rename ( $starting_path, $destination_path );

            update_post_meta ( $order_id, YWAU_METAKEY_ORDER_FILE_UPLOADED, $relative_path );

            WC ()->session->__unset ( "ywau_order_file_uploaded" );
        }  
    }
}; 


function my_shop_order_add_metaboxes(){
    if ( class_exists('YITH_WooCommerce_Additional_Uploads') ) {    
        add_meta_box ( 'ywau_order_metabox', 'Check of Bank Transfer', 'my_display_order_metabox', 'shop_order', 'side', 'high' );
    }
};


function my_display_order_metabox() {
    if ( ! isset( $_GET[ "post" ] ) ) {
        return;
    }

    $order_id = intval ( $_GET[ "post" ] );

    echo '<div id="ywau_uploaded_file">';

    if ( YITH_WooCommerce_Additional_Uploads::get_instance()->order_has_file_uploaded ( $order_id ) ) {
        echo '<span class="file-uploaded">' . __ ( "The customer has sent a file.", 'yith-woocommerce-additional-uploads' ) . '</span>';
        $file_path = YITH_WooCommerce_Additional_Uploads::get_instance()->order_has_file_uploaded ( $order_id );
        $file_url = YITH_YWAU_SAVE_URL . $file_path;
        if ( stripos( $file_url, '.jpg' ) !== false || stripos( $file_url, '.jpeg' ) !== false || stripos( $file_url, '.png' ) !== false ) {
            echo '<a class="download-uploaded-file" href="' . admin_url ( "admin.php?action=" . YWAU_ACTION_DOWNLOAD_FILE . "&order_id=$order_id" ) . '"><img style="max-width: 100%; max-height:150px;" src="' . $file_url . '" alt=""></a>';
        }
        
        echo '<a class="download-uploaded-file" href="' . admin_url ( "admin.php?action=" . YWAU_ACTION_DOWNLOAD_FILE . "&order_id=$order_id" ) . '">' . __ ( "Download", 'yith-woocommerce-additional-uploads' ) . '</a>';

    } else {
        echo '<span class="file-not-uploaded">' . __ ( "There are no files attached to the order.", 'yith-woocommerce-additional-uploads' ) . '</span>';
    }
    echo "</div>";
}


function woo_add_cart_fee() {
    global $woocommerce;
  
    if ( is_admin() && ! defined( 'DOING_AJAX' ) )
        return;
    
    if ( is_checkout() || defined('WOOCOMMERCE_CHECKOUT') ) {
        
        $user_id = get_current_user_id();
        
        if ( isset( $_POST['post_data'] ) ) {
            parse_str($_POST['post_data'], $post_data);
            if ( $post_data['vat_invoice'] ) {   
                if ( $user_id ) {
                    update_user_meta( $user_id, 'vat_invoice', absint( $post_data['vat_invoice'] ) );
                }
                else {
                    WC()->session->set('vat_invoice', absint( $post_data['vat_invoice'] ) );
                }
            }
        }
        else {
            if ( $user_id ) {
                $post_data['vat_invoice'] = get_user_meta( $user_id, 'vat_invoice', 1 );
            }
            else {
                if ( WC()->session->get('vat_invoice') ) {
                    $post_data['vat_invoice'] = WC()->session->get('vat_invoice');
                }
            }
        }
        
        $vat_tax = 0;    
        if ( $post_data['vat_invoice'] ) {              
            $vat_tax = ($woocommerce->cart->cart_contents_total + $woocommerce->cart->tax_total + $woocommerce->cart->shipping_tax_total + $woocommerce->cart->shipping_total) * (int)$post_data['vat_invoice']/100;            
        }    
        
        $name_fee = __('VAT Tax', '3dprint');
        $fee_id   = sanitize_title( $name_fee );
        foreach ( $woocommerce->cart->fees as $key_fee => $fee ) {
            if ( $fee->id == $fee_id ) {
                unset( $woocommerce->cart->fees[ $key_fee ] );
            }
        }
        
        $woocommerce->cart->add_fee( $name_fee, $vat_tax );	
    }
}


function my_woocommerce_get_order_item_totals( $total_rows, $obj ) {
    $new_total_rows = array();
    foreach ( $total_rows as $key => $val ) {
        
        //if ( $key == 'shipping' ) continue;        
        
        $new_total_rows[ $key ] = $val;
    }
    
    return $new_total_rows;
}


function custom_woocommerce_states( $states ) {
    $states_CN = $states['CN'];
    
    $states_CN['CN33'] = __( 'Taiwan / 台湾', '3dprint' );
    $states_CN['CN34'] = __( 'Hong Kong Special Administrative Region / 香港特别行政区', '3dprint' );
    
    $states['CN'] = $states_CN;

    return $states;
}


function disable_shipping_calc_on_cart( $show_shipping ) {
    if( is_cart() ) {
        return false;
    }
    return $show_shipping;
}


function filter_woocommerce_get_page_id( $page_id ) {
    if ( function_exists('pll_get_post') ) {
        $page_id = pll_get_post( $page_id );
    }
    
    return $page_id;
}


function filter_woocommerce_get_cart_page_permalink( $link ) {
    if ( function_exists('pll_get_post') && strpos( $link, 'cart' ) ) {
        $link = get_permalink( pll_get_post( 5 ) );
    }
    
    return $link;
}


function load_custom_plugin_translation_file( $mofile, $domain ) {
  if ( 'woocommerce' === $domain ) {
    $mofile = WP_LANG_DIR . '/loco/plugins/woocommerce-' . get_locale() . '.mo';
  }
  return $mofile;
}


function my_filter_lang_text( $text ) {
    if ( strpos( $text, '[:zh]' ) !== false ) {
        $text_arr = explode( '[:zh]', $text );
        if ( function_exists('pll_current_language') && pll_current_language() == 'zh' ) {        
            if ( count( $text_arr ) > 1 && $text_arr[1] ) {
                $text = $text_arr[1];
            }
        }
        else {
            $text = $text_arr[0];
        }
    }
    
    return $text;
}


function my_woocommerce_order_get_items( $item ) {
    if ( is_array( $item ) ) {
        foreach ( $item as $key => $item_ ) {
            $item[ $key ]['name'] = my_filter_lang_text( $item[ $key ]['name'] );
        }
    }
    
    return $item;
}


function my_woocommerce_cart_shipping_method_full_label( $label, $method ) {
    $label_arrr = explode( ' ', my_filter_lang_text( $method->label ) );
    $label = current( $label_arrr );
    
    if ( $method->cost > 0 ) {
        if ( WC()->cart->tax_display_cart == 'excl' ) {
            $label .= ': ' . wc_price( $method->cost );
            if ( $method->get_shipping_tax() > 0 && WC()->cart->prices_include_tax ) {
                $label .= ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
            }
        } else {
            $label .= ': ' . wc_price( $method->cost + $method->get_shipping_tax() );
            if ( $method->get_shipping_tax() > 0 && ! WC()->cart->prices_include_tax ) {
                $label .= ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
            }
        }
    }

    return $label;
}


function my_woocommerce_checkout_update_order_review() {
    $user_id = get_current_user_id();
    
    $shipping_method = isset( $_POST['shipping_method'] ) ? current( $_POST['shipping_method'] ) : '';
    if ( $shipping_method ) {
        if ( $user_id )
            update_user_meta( $user_id, 'sunfeng', $shipping_method );
        else 
            WC()->session->set('sunfeng', $shipping_method);
    }
}


function my_woocommerce_shipping_chosen_method( $method_id ) {
    //error_log( print_r( $method_id, true ) );
	$user_id = get_current_user_id(); 

    if ( $user_id ) 
        $sunfeng  = get_user_meta( $user_id, 'sunfeng', true ); 
    else 
        $sunfeng  = WC()->session->get('sunfeng');
    
    if ( $sunfeng )
        return $sunfeng;

	return $method_id;
}


function redirect_woocommerce_customer_save_address() {
    if ( isset( $_POST['_wp_http_referer'] ) && $_POST['_wp_http_referer'] ) {
        wp_safe_redirect( $_POST['_wp_http_referer'] );
        exit;
    }    
}


function my_custom_checkout_field_process() {
    if ( $_POST['vat_invoice'] != '0' ) {
        if ( ! $_POST['company_name'] )
            wc_add_notice( __( 'Customer Company Name is a required field.', '3dprint' ), 'error' );
    }
}
