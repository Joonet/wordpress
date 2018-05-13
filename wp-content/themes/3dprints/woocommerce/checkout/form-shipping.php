<?php
/**
 * Checkout shipping information form
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/checkout/form-shipping.php.
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
 * @version 2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

    // EXIT
    //return;
?>
<div class="woocommerce-shipping-fields">
	<?php if ( true === WC()->cart->needs_shipping_address() ) : ?>
        <div>
		<h3 id="ship-to-different-address">
			<label for="ship-to-different-address-checkbox" class="checkbox"><?php _e( 'Consignee Info 2', '3dprint' ); ?></label>
			<input id="ship-to-different-address-checkbox" class="input-checkbox" <?php checked( apply_filters( 'woocommerce_ship_to_different_address_checked', 'shipping' === get_option( 'woocommerce_ship_to_destination' ) ? 1 : 0 ), 1 ); ?> type="checkbox" name="ship_to_different_address" value="1" />
            <?php if ( $checkout->get_value('shipping_phone') && $checkout->get_value('shipping_postcode') ) { ?><a href="#" class="show_hide_fields">(<?php _e( 'Change', '3dprint' ); ?>)</a><?php } ?>
		</h3>
        
        <?php 
        $user_id = get_current_user_id(); 
        if ( $checkout->get_value('shipping_phone') && $checkout->get_value('shipping_postcode') ) {
            $account_details = $checkout->get_value('shipping_first_name');
            
            if ( $checkout->get_value('shipping_email') )
                $account_details .= ', '. $checkout->get_value('shipping_email');
            
            if ( $checkout->get_value('shipping_phone') )
                $account_details .= ', '. $checkout->get_value('shipping_phone');
            
            if ( $checkout->get_value('shipping_phone_2') )
                $account_details .= ', '. $checkout->get_value('shipping_phone_2');
            
            $address = WC()->countries->countries[ $checkout->get_value('shipping_country') ];
            
            $wc_countries = new WC_Countries();        
            if ( isset( $wc_countries->states['CN'][ $checkout->get_value('shipping_state') ] ) )
                $address .= ', '. $wc_countries->states['CN'][ $checkout->get_value('shipping_state') ];
            else if ( $checkout->get_value('shipping_state') )
                $address .= ', '. $checkout->get_value('shipping_state');
            
            $address .= ', '. $checkout->get_value('shipping_city') .', '. $checkout->get_value('shipping_address_1') .', '. $checkout->get_value('shipping_postcode');
            
            echo '<div class="clear"><p>'. $account_details .'</p><p>'. $address .'</p></div>';
        }
        ?>

		<?php do_action( 'woocommerce_before_checkout_shipping_form', $checkout ); ?>
        
        <div class="hide_fields clearfix <?php if ( $checkout->get_value('shipping_phone') && $checkout->get_value('shipping_postcode') ) echo 'nodisplay'; ?>">
            <div class="shipping_address">                
                <?php foreach ( $checkout->checkout_fields['shipping'] as $key => $field ) : ?>

                    <?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

                <?php endforeach; ?>                
            </div>
        </div>
        
		<?php do_action( 'woocommerce_after_checkout_shipping_form', $checkout ); ?>
        </div>
	<?php endif; ?>

	<?php do_action( 'woocommerce_before_order_notes', $checkout ); ?>

	<?php if ( apply_filters( 'woocommerce_enable_order_notes_field', get_option( 'woocommerce_enable_order_comments', 'yes' ) === 'yes' ) ) : ?>

		<?php if ( ! WC()->cart->needs_shipping() || wc_ship_to_billing_address_only() ) : ?>

			<h3><?php _e( 'Additional Information', 'woocommerce' ); ?></h3>

		<?php endif; ?>

		<?php foreach ( $checkout->checkout_fields['order'] as $key => $field ) : ?>

			<?php woocommerce_form_field( $key, $field, $checkout->get_value( $key ) ); ?>

		<?php endforeach; ?>

	<?php endif; ?>

	<?php do_action( 'woocommerce_after_order_notes', $checkout ); ?>
</div>
