<?php
/**
 * Checkout billing information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-billing.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.1.2
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/** @global WC_Checkout $checkout */

?>
<div class="woocommerce-billing-fields clearfix">
	<div>
    <?php if ( wc_ship_to_billing_address_only() && WC()->cart->needs_shipping() ) : ?>
    
		<h3><?php _e( 'Consignee Info', '3dprint' ); ?></h3> <?php if ( $checkout->get_value('billing_phone') && $checkout->get_value('billing_postcode') ) { ?><a href="#" class="show_hide_fields">(<?php _e( 'Change', '3dprint' ); ?>)</a><?php } ?>

	<?php else : ?>

		<h3><?php _e( 'Consignee Info', '3dprint' ); ?></h3> <?php if ( $checkout->get_value('billing_phone') && $checkout->get_value('billing_postcode') ) { ?><a href="#" class="show_hide_fields">(<?php _e( 'Change', '3dprint' ); ?>)</a><?php } ?>

	<?php endif; ?>
    
    <?php 
    $user_id = get_current_user_id(); 
    if ( $checkout->get_value('billing_phone') && $checkout->get_value('billing_postcode') ) {
        $account_details = $checkout->get_value('billing_first_name') .', '. $checkout->get_value('billing_email') .', '. $checkout->get_value('billing_phone');
        if ( $checkout->get_value('billing_phone_2') )
            $account_details .= ', '. $checkout->get_value('billing_phone_2');
        
        $address = WC()->countries->countries[ $checkout->get_value('billing_country') ];
        
        $wc_countries = new WC_Countries();        
        if ( isset( $wc_countries->states['CN'][ $checkout->get_value('billing_state') ] ) )
            $address .= ', '. $wc_countries->states['CN'][ $checkout->get_value('billing_state') ];
        else if ( $checkout->get_value('billing_state') )
            $address .= ', '. $checkout->get_value('billing_state');
        
        $address .= ', '. $checkout->get_value('billing_city') .', '. $checkout->get_value('billing_address_1') .', '. $checkout->get_value('billing_postcode');
        
        echo '<div class="clear"><p>'. $account_details .'</p><p>'. $address .'</p></div>';
    }
    ?>

	<?php do_action( 'woocommerce_before_checkout_billing_form', $checkout ); ?>
    
    <div class="hide_fields clearfix <?php if ( $checkout->get_value('billing_phone') && $checkout->get_value('billing_postcode') ) echo 'nodisplay'; ?>">
        <?php foreach ( $checkout->checkout_fields['billing'] as $key => $field ) : ?>

            <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

        <?php endforeach; ?>
    </div>
    
    </div>

	<?php do_action('woocommerce_after_checkout_billing_form', $checkout ); ?>

	<?php if ( ! is_user_logged_in() && $checkout->enable_signup ) : ?>

		<?php if ( $checkout->enable_guest_checkout ) : ?>

			<p class="form-row form-row-wide create-account nodisplay">
				<input class="input-checkbox" id="createaccount" checked="checked" type="checkbox" name="createaccount" value="1" /> <label for="createaccount" class="checkbox"><?php _e( 'Create an account?', 'woocommerce' ); ?></label>
			</p>

		<?php endif; ?>

		<?php do_action( 'woocommerce_before_checkout_registration_form', $checkout ); ?>

		<?php if ( ! empty( $checkout->checkout_fields['account'] ) ) : ?>

			<div class="create-account">

				<p><?php _e( 'Create an account by entering the information below. If you are a returning customer please login at the top of the page.', 'woocommerce' ); ?></p>

				<?php foreach ( $checkout->checkout_fields['account'] as $key => $field ) : ?>

					<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

				<?php endforeach; ?>

				<div class="clear"></div>

			</div>

		<?php endif; ?>

		<?php do_action( 'woocommerce_after_checkout_registration_form', $checkout ); ?>

	<?php endif; ?>
</div>
