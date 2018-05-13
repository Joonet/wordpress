<?php 
//if ( isset( $_GET['admin'] ) ) {
	//var_dump( wp_mail('wlada.feok@gmail.com', 'Theme', 'test' ) );
//}

require_once('includes/woo.php');
require_once('includes/CN_cities.php');
require_once('includes/custom_post_types.php');
require_once('includes/acf-options-for-polylang.php');
require_once('includes/woocommerce-cny-integration.php');

/* Customize Admin Panel */
class Wptuts_Simple_Admin {
    function __construct() {
		// Hook onto the action 'admin_menu' for our function to remove menu items
		add_action( 'admin_menu', array( $this, 'remove_menus' ) );
		// Hook Dashboard Widgets
		add_action( 'wp_dashboard_setup', array( $this, 'remove_dashboard_widget' ) );
		// Hook onto the action 'admin_bar'
		add_action( 'wp_before_admin_bar_render', array( $this, 'my_admin_bar_render' ) );
		// CUSTOM ADMIN LOGIN HEADER LOGO
		add_action( 'login_head',  array( $this, 'my_custom_login_logo' ) );	
		// CUSTOM ADMIN LOGIN LOGO LINK		
		add_filter( 'login_headerurl', array( $this, 'change_wp_login_url' ) );
		// CUSTOM ADMIN LOGIN LOGO & ALT TEXT
		add_filter( 'login_headertitle', array ( $this, 'change_wp_login_title' ) );
		// Custom css: Hide ACF menu
		add_action( 'admin_head', array( $this, 'custom_css' ) );
    }
	
    // This function removes each menu item
    function remove_menus() {
		global $submenu;		
		unset($submenu['themes.php'][6]); // Customize
        remove_menu_page('edit-comments.php');		
    }
	
	function remove_dashboard_widget() {
		remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
		remove_action('welcome_panel', 'wp_welcome_panel');				
	}

	function my_admin_bar_render() {
		global $wp_admin_bar;
		$wp_admin_bar->remove_menu('comments');
		$wp_admin_bar->remove_menu('wp-logo');
	}
	
	function custom_css() {
		$css = "<style>";
			//$css .= "th#id_post { width:100px; }";
			if ( ! isset( $_GET{'admin'} ) )
                $css .= "#menu-posts-product {display:none !important;}";
			$css .= "#toplevel_page_acf-options .wp-menu-name{ background: #0022aa; } #toplevel_page_acf-options:hover .wp-menu-name{ background: #111; } #toplevel_page_acf-options.current .wp-menu-name{ background: #0074a2; }";
			$css .= "#adminmenu { transform: translateZ(0); }"; // fix bag Google Chrome
		$css .= "</style>";
		echo $css;
	}
	
	function my_custom_login_logo() {
		echo '<style  type="text/css">h1 a {  background-size: 135px 50px !important; width: 100% !important; height: 50px!important; background:#CD4C27 url('. get_stylesheet_directory_uri() .'/img/logo.svg) no-repeat center center !important; margin-bottom: 0px !important; } .login h1{ padding: 10px 0 !important; margin-bottom: 20px; background: #CD4C27; } </style>';
	}	

	function change_wp_login_url() {
		return home_url();
	}
	
	function change_wp_login_title() {
		return get_option('blogname');
	}
}
$wptuts_simple_admin = new Wptuts_Simple_Admin();

remove_action('wp_head', 'wp_generator');
remove_action('wp_head', 'rsd_link');
remove_action('wp_head', 'feed_links_extra', 3);
remove_action('wp_head', 'feed_links', 2);
remove_action('wp_head', 'wlwmanifest_link');
remove_action('wp_head', 'dns-prefetch', 11);
remove_action('wp_head', 'rest_output_link_wp_head');
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'rest_output_link_header', 11, 0);
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');
add_filter('emoji_svg_url', '__return_false');

/* WP_POST_REVISIONS */
function my_revisions_to_keep( $revisions ) {
    return 5;
}
add_filter( 'wp_revisions_to_keep', 'my_revisions_to_keep' );

/* Adds JS and CSS to Thema */
function adds_js_css_thema(){
    wp_enqueue_style( 'scrollbar',      get_stylesheet_directory_uri() . '/js/perfect-scrollbar/perfect-scrollbar.min.css' );
    wp_enqueue_style( 'font-awesome',   get_stylesheet_directory_uri() . '/css/font-awesome.min.css' );    
    wp_enqueue_style( 'style',          get_stylesheet_directory_uri() . '/style.css?v=11' );
    
    wp_enqueue_script( 'jquery' );
    
    wp_localize_script( 'jquery', 'myajax', array( 'url' => admin_url('admin-ajax.php'), 'theme_url' => get_stylesheet_directory_uri() ));
    if ( is_page_template('page-prints.php') || is_cart() ) {
        wp_enqueue_script( 'form.min',      get_stylesheet_directory_uri() . '/js/jquery.form.min.js', array( 'jquery' ), '', 1 );
        wp_enqueue_script( 'CFInstall',     get_stylesheet_directory_uri() . '/js/CFInstall.min.js', array( 'jquery' ), '', 1 );
        wp_enqueue_script( 'Three',         get_stylesheet_directory_uri() . '/js/Three.js', array( 'jquery' ), '', 1 );
        wp_enqueue_script( 'plane',         get_stylesheet_directory_uri() . '/js/plane.js', array( 'jquery' ), '', 1 );
        wp_enqueue_script( 'thingiview',    get_stylesheet_directory_uri() . '/js/thingiview.js', array( 'jquery' ), '', 1 );
        //wp_enqueue_script( 'thingiview', get_stylesheet_directory_uri() . '/js/thingiview-new.js', array( 'jquery' ), '', 1 );
        
        wp_enqueue_script( 'scrollbar',     get_stylesheet_directory_uri() . '/js/perfect-scrollbar/perfect-scrollbar.min.js', array( 'jquery' ), '', 1 ); 
    }    
    wp_enqueue_script( 'themescript',   get_stylesheet_directory_uri() . '/js/main.js?v=11', array( 'jquery' ), '', 1 ); 
}
add_action( 'wp_enqueue_scripts', 'adds_js_css_thema' );


function mytheme_setup() {
    /*
	 * Make theme available for translation.
	 */
    load_theme_textdomain( '3dprint' );
    
	/* This theme uses post thumbnails */
	add_theme_support( 'post-thumbnails' );

	if ( function_exists( 'add_image_size' ) ) {
		
	}
	
	register_nav_menus(array(  
		'menu_top' => 'Menu Top'
	));
	
	// register_sidebar( array(
		// 'name' => 'Widget Area',
		// 'id' => 'widget-area',
		// 'description' => '',
		// 'before_widget' => '<div class="widget %2$s">',
		// 'after_widget' => '</div>',
		// 'before_title' => '<div class="widget-title">',
		// 'after_title' => '</div>',
	// ));
	
	// valid HTML5.
	add_theme_support( 'html5', array(
		'comment-form', 'comment-list', 'gallery', 'caption', 'widgets'
	) );
	
	// This theme styles the visual editor with editor-style.css to match the theme style.
	add_editor_style();
	
	/* Label Options Page */
	if( function_exists('acf_add_options_page') ) {
		acf_add_options_page(array(
			'page_title' 	=> 'Theme Options',
			'menu_title'	=> 'Theme Options',
			'menu_slug' 	=> 'acf-options',
			'autoload'		=> true
		));		
	}
    
}
add_action( 'after_setup_theme', 'mytheme_setup' );

function link_class_active( $menu ) {
	return str_replace(array('current-menu-item','current-menu-parent'), 'active', $menu);
}
add_filter( 'wp_nav_menu', 'link_class_active' );

function add_footer_scripts(){
	$js_footer = get_field( 'js-code', 'options' );
	if ( $js_footer )
		echo $js_footer;
}
add_action( 'wp_footer', 'add_footer_scripts' );

function my_css_attributes_filter($var) {
    return is_array($var) ? array_intersect($var, array('current-menu-item', 'parent-menu-item', 'menu-item-has-children', 'current-menu-parent', 'sub-menu')) : '';
}
add_filter('nav_menu_css_class', 'my_css_attributes_filter', 100, 1);
add_filter('nav_menu_item_id', 'my_css_attributes_filter', 100, 1);
add_filter('page_css_class', 'my_css_attributes_filter', 100, 1);

function my_body_class( $classes ) {
    if ( function_exists('pll_current_language') ) {
        $classes[] = 'lang-'. pll_current_language();
    }
    
    return $classes;
}
add_filter('body_class', 'my_body_class');

/**
 * http://wp-kama.ru/id_31/obrezka-teksta-zamenyaem-the-excerpt.html
 */
if ( ! function_exists( 'kama_excerpt' ) ) {
	function kama_excerpt($args=''){
		global $post;
			parse_str($args, $i);
			$maxchar 	 = isset($i['maxchar']) ?  (int)trim($i['maxchar'])		: 350;
			$text 		 = isset($i['text']) ? 			trim($i['text'])		: '';
			$save_format = isset($i['save_format']) ?	trim($i['save_format'])			: false;
			$echo		 = isset($i['echo']) ? 			false		 			: true;
		
		$out = '';
		
		if (!$text){
			$out = $post->post_excerpt ? $post->post_excerpt : $post->post_content;
		}
		else
			$out = $text;
		
		$out = preg_replace ("!\[/?.*\]!U", '', $out );
		// for <!--more-->
		if( !$post->post_excerpt && strpos($out, '<!--more-->') ){
			preg_match ('/(.*)<!--more-->/s', $out, $match);
			$out = str_replace("\r", '', trim($match[1], "\n"));
			$out = preg_replace( "!\n\n+!s", "</p><p>", $out );
			$out = str_replace( "\n", "<br />", $out );
			if ($echo)
				return print $out;
			return $out;
		}

		if ( isset( $post ) && !$post->post_excerpt)
			$out = strip_tags($out, $save_format);

		if ( iconv_strlen($out, 'utf-8') > $maxchar ){
			$out = iconv_substr( $out, 0, $maxchar, 'utf-8' );
			$out = preg_replace('@(.*)\s[^\s]*$@s', '\\1 ...', $out);
		}

		if($save_format){
			$out = str_replace( "\r", '', $out );
			$out = preg_replace( "!\n\n+!", "</p><p>", $out );
			$out = "<p>". str_replace ( "\n", "<br />", trim($out) ) ."</p>";
		}

		if($echo) return print $out;
		return $out;
	}
}


/* AJAX - UPDATE Page Prints from session_files */
function ajax_update_from_session_files() {
    $result = array(
        'files' => array(),
        'error' => '',
    );
    
    $upload_dir = wp_upload_dir();
    $uploads_dir_temp = $upload_dir['basedir'] . '/stl_files_uploads';
    
    if ( isset( $_COOKIE['session_files'] ) ) {
        $session_files = unserialize( stripcslashes( $_COOKIE['session_files'] ) );
        if ( ! empty( $session_files ) ) {
            
            $result['files'] = $session_files;
            
        }
    }
    
    echo json_encode( $result );
    exit;
}
add_action('wp_ajax_update_from_session_files', 'ajax_update_from_session_files');
add_action('wp_ajax_nopriv_update_from_session_files', 'ajax_update_from_session_files');


/* AJAX - Upload STL-file */
function ajax_upload_stl_file() {
    $size_limit = 64000000;
    
    $result = array(
        'files' => array(),
        'error' => '',
    );
    
    if ( isset( $_COOKIE['session_files'] ) ) {
        $session_files = unserialize( stripcslashes( $_COOKIE['session_files'] ) );
        if ( ! empty( $session_files ) ) {
            $result['files'] = $session_files;
        }
    }
    
    if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'upload_stl_file' ) ) {
        $result['error'] = __( 'Error. Nonce field. Refresh the page.', '3dprint' );
    }
    else {
        set_time_limit(600);
        
        $upload_dir = wp_upload_dir();
        $uploads_dir_temp = $upload_dir['basedir'] . '/stl_files_uploads';
        
        /*
        if ( ! empty( $_POST['uploaded_files'] ) ) {
            // DELETE temp file
            $uploaded_files = explode( '|', $_POST['uploaded_files'] );
            foreach ( $uploaded_files as $file_name ) {
                if ( $file_name ) {
                    $file_link = $uploads_dir_temp . '/' . $file_name;
                    if ( file_exists( $file_link ) )
                        unlink( $file_link );
                }
            }
        }
        */

        if ( ! empty( $_FILES['files_stl'] ) ) {
            require_once('includes/STLStats.php');
            require_once('includes/STLArea.php');
            
            if ( isset( $_COOKIE['session_files'] ) ) {
                $session_files = unserialize( stripcslashes( $_COOKIE['session_files'] ) );
            }
            else {
                $session_files = array();
            }
            
            $unit = "cm";
            
            foreach ( $_FILES["files_stl"]["error"] as $key => $error ) {
                if ($error == UPLOAD_ERR_OK) {
                    
                    if ( $size_limit && $_FILES["files_stl"]['size'][ $key ] > $size_limit ) {
                        continue;
                    }
                    
                    /* Firefox Bug - not detect file type */
                    // if ( strpos( $_FILES["files_stl"]['type'][ $key ], '.stl' ) === false ) {
                        // continue;
                    // }
                    
                    if ( stripos( $_FILES["files_stl"]['name'][ $key ], '.stl' ) === false ) {
                        continue;
                    }
                    
                    
                    $tmp_name = $_FILES["files_stl"]["tmp_name"][ $key ];
                    $name     = wp_unique_filename( $uploads_dir_temp, basename( $_FILES["files_stl"]["name"][ $key ] ) );
                    $upl      = move_uploaded_file( $tmp_name, "$uploads_dir_temp/$name" );
  
                    if ( $upl ) {
                        $obj = new STLStats( "$uploads_dir_temp/$name" );
                        $obj_area = new STLArea( "$uploads_dir_temp/$name" );
                        $result['files'][] = array( $upload_dir['baseurl'] .'/stl_files_uploads/'. $name, $name, $obj->getVolume($unit)*1000, $obj->getWeight(), $obj->getDensity(), $obj_area->getArea() );
                        
                        $session_files[]   = array( $upload_dir['baseurl'] .'/stl_files_uploads/'. $name, $name, $obj->getVolume($unit)*1000, $obj->getWeight(), $obj->getDensity(), $obj_area->getArea() );
                    }
                    
                }
            }
            
            setcookie( 'session_files', serialize( $session_files ), current_time('timestamp')+60*60*24*180, '/' );
            
            if ( empty( $result['files'] ) ) {
                $result['error'] = __( 'Error loading file.', '3dprint' );
            }
        }
        else {
            $result['error'] = __( 'Error. File not found.', '3dprint' );
        }
    }
    
    echo json_encode( $result );
    exit;
}
add_action('wp_ajax_upload_stl_file', 'ajax_upload_stl_file');
add_action('wp_ajax_nopriv_upload_stl_file', 'ajax_upload_stl_file');


/* AJAX - DELETE STL-file */
function ajax_delete_stl_file() {
    if ( isset( $_COOKIE['session_files'] ) ) {
        $session_files = unserialize( stripcslashes( $_COOKIE['session_files'] ) );
    }
    else {
        $session_files = array();
    }
    
    $upload_dir = wp_upload_dir();
    $uploads_dir_temp = $upload_dir['basedir'] . '/stl_files_uploads';    

    $file_name = $_POST['file_name'];    
    if ( $file_name ) {
        if ( ! is_array( $file_name ) ) {
            $files = array( $file_name );
        }
        else {
            $files = $file_name;
        }
        
        foreach ( $files as $file_name ) {
            foreach( $session_files as $key => $file_item ) {                
                if ( $file_item[1] == $file_name ) {
                    unset( $session_files[ $key ] );
                }
            }
            
            // delete STL file
            if ( WC()->cart->get_cart_contents_count() == 0 ) {
                $file_link = $uploads_dir_temp . '/' . $file_name;
                if ( file_exists( $file_link ) ) {
                    unlink( $file_link ); 
                }
            }
        }
        
        setcookie( 'session_files', serialize( $session_files ), current_time('timestamp')+60*60*24*180, '/' );
    }
    
    exit;
}
add_action('wp_ajax_delete_stl_file', 'ajax_delete_stl_file');
add_action('wp_ajax_nopriv_delete_stl_file', 'ajax_delete_stl_file');


/* AJAX - Add new Produt */
function ajax_create_product() {
    global $woocommerce;
    
    wc_clear_notices();
    
    @set_time_limit(300);
    
    $result = array(
        'products'  => array(),
        'products_add_to_cart'  => array(),
        'html_cart' => '',
        'error'     => '',
    );
    
    // if ( empty( $_POST['_wpnonce'] ) || ! wp_verify_nonce( $_POST['_wpnonce'], 'upload_stl_file' ) ) {
        // $result['error'] = 'Error. Nonce field. Refresh the page.';
    // }
    // else {   

        $upload_dir = wp_upload_dir();            
        $uploads_dir_temp = $upload_dir['basedir'] . '/stl_files_uploads';

        if ( ! empty( $_POST['files'] ) ) {
            require_once('includes/STLStats.php');
            require_once('includes/STLArea.php');
            
            $unit = "cm";
           
            $cart_id        = WC()->cart->generate_cart_id( 233 );
            $cart_item_key  = WC()->cart->find_product_in_cart( $cart_id );
            if ( $cart_item_key )
                WC()->cart->remove_cart_item( $cart_item_key );
            
            foreach ( $_POST['files'] as $key => $file ) {
                $file['price'] = str_replace( ',', '.', $file['price'] );
                
                if ( file_exists( "$uploads_dir_temp/" . $file['file_name'] ) ) {
                    
                    $args_product = array(
                        'post_type'   => 'product',
                        'post_status' => 'publish',
                        'post_title'  => wp_strip_all_tags( $file['file_name'] .' | '. get_post_field( 'post_title', $file['material_id'] ) ),
                    );
                    $post_id = wp_insert_post( $args_product );
                    if ( $post_id ) {
                        $price_product = get_post_meta( (int)$file['material_id'], 'price', 1 );
                        if ( isset( $file['is_painting'] ) && $file['is_painting'] == 'true' ) {
                            $price_product = $price_product + get_post_meta( (int)$file['material_id'], 'price_painting', 1 );
                        }
                        if ( isset( $file['is_screen'] ) && $file['is_screen'] == 'true' ) {
                            $price_product = $price_product + get_post_meta( (int)$file['material_id'], 'price_silk_screen', 1 );
                        }
                        
                        update_post_meta( $post_id, '_visibility',      'visible' );                        
                        update_post_meta( $post_id, '_stock_status',    'instock' );
                        update_post_meta( $post_id, 'total_sales',      '0' );
                        update_post_meta( $post_id, '_downloadable',    'no' );
                        update_post_meta( $post_id, '_virtual',         'no' );
                        update_post_meta( $post_id, '_regular_price',   $price_product );
                        update_post_meta( $post_id, '_price',           $price_product );
                        update_post_meta( $post_id, '_featured',        'no' );                        
                        //update_post_meta( $post_id, '_product_version', '2.6.14' ); 
                        
                        //update_post_meta( $post_id, '_manage_stock',    'no' );
                        //update_post_meta( $post_id, '_backorders',      'no' );
                        
                        $obj      = new STLStats( "$uploads_dir_temp/" . $file['file_name'] );
                        $obj_area = new STLArea(  "$uploads_dir_temp/" . $file['file_name'] );
                        $estimated_delivery = get_post_meta( (int)$file['material_id'], 'estimated_delivery', 1 );                        
                        $date_delivery      = current_time('timestamp') + $estimated_delivery * 3600;
                        
                        update_post_meta( $post_id, 'volume',       $obj->getVolume($unit)*1000 );
                        update_post_meta( $post_id, 'surface_area', $obj_area->getArea() );
                        update_post_meta( $post_id, 'weight',       $obj->getWeight() );
                        update_post_meta( $post_id, 'density',      $obj->getDensity() ); 
                        
                        update_post_meta( $post_id, 'material_id',  $file['material_id'] );                        
                        update_post_meta( $post_id, 'file',         $upload_dir['baseurl'] . '/stl_files_uploads/' . $file['file_name'] );

                        update_post_meta( $post_id, 'estimated_delivery', $estimated_delivery );
                        update_post_meta( $post_id, 'timestamp_delivery', $date_delivery );
                        
                        /*
                        $found = false;                
                        //check if product already in cart
                        if ( sizeof( WC()->cart->get_cart() ) > 0 ) {
                            foreach ( WC()->cart->get_cart() as $cart_item_key => $values ) {
                                $_product = $values['data'];
                                if ( $_product->id == $post_id )
                                    $found = true;
                            }
                            // if product not found, add it
                            if ( ! $found )
                                WC()->cart->add_to_cart( $post_id, (int)$file['quantity'] );
                        } else {
                            // if no products in cart, add it
                            WC()->cart->add_to_cart( $post_id, (int)$file['quantity'] );
                        }
                        */
                        
                        WC()->cart->add_to_cart( $post_id, (int)$file['quantity'] );
                        
                        $result['products'][] = $post_id;
                        //$result['products_add_to_cart'][] = array( $post_id, (int)$file['quantity'] );
                    }
                    else {
                        $result['error'] = __( 'Error. Failed create item.', '3dprint' );
                    }
                    
                }
                else {
                    $result['error'] = __( 'Error. File not found in dir.', '3dprint' );
                }
            }
            $result['html_cart'] = storefront_cart_link_fragment();            
        }
        else {
            $result['error'] = __( 'Error. File not found.', '3dprint' );
        }
    //}

    echo json_encode( $result );
    exit;
   
}
add_action('wp_ajax_prints_create_product', 'ajax_create_product');
add_action('wp_ajax_nopriv_prints_create_product', 'ajax_create_product');


/* Redirects */
function my_template_redirect() {
    global $woocommerce, $wp_query;
    
    if ( is_woocommerce() ) {
        wp_redirect( home_url() );
        exit;
    }
    
    if ( is_account_page() && ! is_wc_endpoint_url() && ! isset( $wp_query->query['subscriptions'] ) ) {
        wp_redirect( wc_get_account_endpoint_url( 'orders' ) );
        exit;
    }
    
    if ( is_page_template('page-prints.php') ) {
        if ( isset( $_COOKIE['session_files'] ) ) {
            $session_files = unserialize( stripcslashes( $_COOKIE['session_files'] ) );
            if ( ! empty( $session_files ) ) {
                $upload_dir = wp_upload_dir();
                $uploads_dir_temp = $upload_dir['basedir'] . '/stl_files_uploads';
                
                foreach ( $session_files as $key => $file_item ) {
                    $file_link = $uploads_dir_temp . '/' . $file_item[1];
                    if ( ! file_exists( $file_link ) ) {
                        unset( $session_files[ $key ] );
                    }
                }
                setcookie( 'session_files', serialize( $session_files ), current_time('timestamp')+60*60*24*180, '/' );
            }
        }
    }
    
    // DELETE $_COOKIE['session_files']
    if ( is_checkout() && ! empty( $wp_query->query_vars['order-received'] ) ) {
		if ( isset( $_COOKIE['session_files'] ) )
            setcookie( 'session_files', null, -1, '/' );
	}
}
add_action('template_redirect', 'my_template_redirect', 15);


function my_admin_footer() {
    global $post;
    if ( $post && $post->post_type == 'shop_order' ) {
    ?>
        <script>
        jQuery(document).ready(function($){
            $('a.wc-order-item-name').each(function(){
                var block = $(this).closest('.name');
                var text = $(this).text();
                $(this).remove();
                
                block.prepend('<strong>'+ text +'</strong>');
            });
        });
        </script>
    <?php
    }
}
add_action('admin_footer', 'my_admin_footer');


function my_users_online() {
    ob_start();
    users_online();
   // shownInSite(users_online());
    $html = ob_get_contents();
    ob_get_clean();
    
    if ( function_exists('pll_current_language') && pll_current_language() == 'zh' ) {
        $html = str_replace( 'People', '人', $html );
    }
    
    echo $html;
}




function change_text_english( $text ) { 
    if ( function_exists('pll_current_language') ) {
        $lang = pll_current_language();
        
        if ( $lang == 'en' ) {
            $text = str_replace(
                array( '发票税', '转账', '支付宝', '支付宝', '顺丰 20元', '圆通 13元' ),
                array( 'VAT Tax', 'Bank Account Transfer', 'Alipay Payment', 'Alipay', 'Sunfeng RMB 20', 'Yuantong RMB 13' ),
                $text
            );
        }
        else {
            $text = str_replace(
                array( 'VAT Tax', 'Bank Account Transfer', 'Alipay Payment', 'Alipay', 'Sunfeng RMB 20', 'Yuantong RMB 13' ),
                array( '发票税', '转账', '支付宝', '支付宝', '顺丰 20元', '圆通 13元' ),      
                $text
            );
        }
    }
    
    return $text;
}
