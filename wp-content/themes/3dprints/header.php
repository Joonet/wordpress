<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=1170">
	<title><?php wp_title(); ?></title>
	<?php wp_head(); ?>
    <!--[if lt IE 10]>
		<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
	<![endif]-->
</head>
<body <?php body_class(); ?>>
<div class="wrapper">
    <header class="header">
        <div class="wrap clearfix">
            <?php if ( ( is_home() || is_front_page() ) && ! is_paged() ) { ?>
                <span class="logo" title="<?php echo bloginfo('name'); ?>"></span>
            <?php } else { ?>
                <a href="<?php echo home_url(); ?>" class="logo" title="<?php echo bloginfo('name'); ?>"></a>
            <?php } ?>                
            <nav>
                <?php if ( has_nav_menu('menu_top') ) wp_nav_menu( array( 'container' => '', 'theme_location' => 'menu_top', 'menu_class' => 'top-menu' ) ); ?>
            </nav>            
            <?php storefront_header_cart(); ?>
            <ul class="login-info">
                <?php if ( is_user_logged_in() ) {
                    $user = wp_get_current_user();
                    $display_name = $user->first_name;
                    if ( ! trim( $display_name ) )
                        $display_name = $user->user_nicename;
                    ?>
                    <li><span><i class="fa fa-user" aria-hidden="true"></i><?php echo $display_name; ?></span>
                        <ul>
                            <?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
                                <li class="<?php echo wc_get_account_menu_item_classes( $endpoint ); ?>">
                                    <a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>"><?php echo esc_html( $label ); ?></a>
                                </li>
                            <?php endforeach; ?>
                            <li><a href="<?php echo esc_url( wc_get_account_endpoint_url( get_option( 'woocommerce_logout_endpoint', 'customer-logout' ) ) ); ?>" title="<?php _e('Logout', '3dprint'); ?>"><?php _e('Logout', '3dprint'); ?></a></li>
                        </ul>
                    </li>
                <?php } else { ?>
                    
                    <li><?php if ( is_account_page() ) echo '<span>'. __('Login | Register', '3dprint') .'</span>'; else { ?><a href="<?php echo wc_get_page_permalink('myaccount'); ?>" title="<?php _e('Login | Register', '3dprint'); ?>"><?php _e('Login | Register', '3dprint'); ?></a><?php } ?></li>
                <?php } ?>
            </ul>
            <div class="user-online"><?php _e('Online:', '3dprint'); ?> <?php my_users_online(); ?></div>
        </div>
    </header><!-- .header-->