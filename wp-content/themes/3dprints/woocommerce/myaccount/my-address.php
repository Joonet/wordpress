<?php
/**
 * My Addresses
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/myaccount/my-address.php.
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
 * @version 2.6.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' => __( 'Consignee Info', '3dprint' ),
		'shipping' => __( 'Consignee Info 2', '3dprint' )
	), $customer_id );
} else {
	$get_addresses = apply_filters( 'woocommerce_my_account_get_addresses', array(
		'billing' =>  __( 'Consignee Info', '3dprint' )
	), $customer_id );
}

$oldcol = 1;
$col    = 1;
?>

<p>
	<?php echo apply_filters( 'woocommerce_my_account_my_address_description', __( 'The following addresses will be used on the checkout page by default.', 'woocommerce' ) ); ?>
</p>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) echo '<div class="u-columns woocommerce-Addresses col2-set addresses">'; ?>

<?php foreach ( $get_addresses as $name => $title ) : ?>

	<div class="u-column<?php echo ( ( $col = $col * -1 ) < 0 ) ? 1 : 2; ?> col-<?php echo ( ( $oldcol = $oldcol * -1 ) < 0 ) ? 1 : 2; ?> woocommerce-Address">
		<header class="woocommerce-Address-title title">
			<h3><?php echo $title; ?></h3>
			<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="edit"><?php _e( 'Edit', 'woocommerce' ); ?></a>
		</header>
		<address>
			<?php
				$address = apply_filters( 'woocommerce_my_account_my_address_formatted_address', array(
					'first_name'  => get_user_meta( $customer_id, $name . '_first_name', true ),
					'email'       => get_user_meta( $customer_id, $name . '_email', true ),
					'phone'       => get_user_meta( $customer_id, $name . '_phone', true ),
					'phone_2'     => get_user_meta( $customer_id, $name . '_phone_2', true ),
					'address_1'   => get_user_meta( $customer_id, $name . '_address_1', true ),
					'address_2'   => get_user_meta( $customer_id, $name . '_address_2', true ),
					'city'        => get_user_meta( $customer_id, $name . '_city', true ),
					'state'       => get_user_meta( $customer_id, $name . '_state', true ),
					'postcode'    => get_user_meta( $customer_id, $name . '_postcode', true ),
					'country'     => get_user_meta( $customer_id, $name . '_country', true )
				), $customer_id, $name );

				//$formatted_address = WC()->countries->get_formatted_address( $address );

				// if ( ! $formatted_address )
					// _e( 'You have not set up this type of address yet.', 'woocommerce' );
				// else
					// echo $formatted_address;
			?>
		</address>
        <?php
        $account_details = $address['first_name'] .', '. $address['email'] .', '. $address['phone'];
        if ( $address['phone_2'] )
            $account_details .= ', '. $address['phone_2'];
        
        $address_details = WC()->countries->countries[ $address['country'] ];
        
        $wc_countries = new WC_Countries();        
        if ( isset( $wc_countries->states['CN'][ $address['state'] ] ) )
            $address_details .= ', '. $wc_countries->states['CN'][ $address['state'] ];
        else if ( $address['state'] )
            $address_details .= ', '. $address['state'];
        
        $address_details .= ', '. $address['city'] .', '. $address['address_1'] .', '. $address['postcode'];
        
        echo '<div class="clear"><p>'. $account_details .'</p><p>'. $address_details .'</p></div>';
        ?>
	</div>

<?php endforeach; ?>

<?php if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) echo '</div>'; ?>
