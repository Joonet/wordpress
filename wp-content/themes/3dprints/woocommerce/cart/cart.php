<?php
/**
 * Cart Page
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart.php.
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
 * @version 2.3.8
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

<?php do_action( 'woocommerce_before_cart_table' ); ?>

<table class="shop_table shop_table_bg shop_table_responsive cart" cellspacing="0">
	<thead>
		<tr>			
			<!--<th class="product-thumbnail" width="85">&nbsp;</th>-->
			<th class="product-name"width="285"><?php _e( 'Products', 'woocommerce' ); ?></th>
			<th class="product-details" width="220"><?php _e( 'Specification', '3dprint' ); ?></th>
			<th class="product-price" width="130"><?php _e( 'Unit Price', '3dprint' ); ?></th>
			<th class="product-quantity" width="100"><?php _e( 'Quantity', 'woocommerce' ); ?></th>
			<th class="product-subtotal" width="100"><?php _e( 'Total', 'woocommerce' ); ?></th>
            <th class="product-remove" width="100"><?php _e( 'Operating', '3dprint' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php do_action( 'woocommerce_before_cart_contents' ); ?>

		<?php
        $timestamp_delivery = 0;
		foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
			$_product   = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
			$product_id = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );
            
            if ( $product_timestamp_delivery = get_post_meta( $product_id, 'timestamp_delivery', 1 ) ) {
                if ( $product_timestamp_delivery > $timestamp_delivery )
                    $timestamp_delivery = $product_timestamp_delivery;
            }
            
			if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">
                    <?php /*
					<td class="product-thumbnail">
						<?php
							// $thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

							// if ( ! $product_permalink ) {
								// echo $thumbnail;
							// } else {
								// printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail );
							// }
						?>                        
                        <div id="viewer-<?php echo $product_id; ?>" class="viewer_product" data-href="<?php echo get_post_meta( $product_id, 'file', 1 ); ?>"></div>
					</td>
                    */ ?>
					<td class="product-name" data-title="<?php _e( 'Products', 'woocommerce' ); ?>">
						<?php
							// if ( ! $product_permalink ) {
								// echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
							// } else {
								// echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_title() ), $cart_item, $cart_item_key );
							// }

							// Meta data
							//echo WC()->cart->get_item_data( $cart_item );
                            
                            // 3D Print Product
                            // Materials: Future 8000 resin
                            // Color: White
                            // Resolution: 100 microns
                            // Lead Time: 48 hours
                            // File: PS4_Camera...

							// Backorder notification
							// if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) ) {
								// echo '<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'woocommerce' ) . '</p>';
							// }
						?>
                        <div class="product-name-inner">
                        <?php 
                        if ( ! $product_permalink ) {
                            echo apply_filters( 'woocommerce_cart_item_name', $_product->get_title(), $cart_item, $cart_item_key ) . '&nbsp;';
                        } else {
                            echo apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $_product->get_title() ), $cart_item, $cart_item_key );
                        }
                        
                        $material_id = get_post_meta( $product_id, 'material_id', 1 );
                        ?>
                        <br/><?php _e( 'Material', '3dprint' ); ?>: <?php echo get_the_title( $material_id ); ?>
                        <br/><?php _e( 'Color', '3dprint' ); ?>: <?php echo get_post_meta( $material_id, 'color', 1  ); ?>
                        <br/><?php _e( 'Resolution', '3dprint' ); ?>: <?php echo get_post_meta( $material_id, 'accuracy', 1  ); ?>
                        <br/><?php _e( 'Lead Time', '3dprint' ); ?>: <?php echo get_post_meta( $material_id, 'estimated_delivery', 1 ); ?> <?php _e( 'hours', '3dprint' ); ?>
                        <br/><?php _e( 'File', '3dprint' ); ?>: <span class="short-text"><?php echo basename( get_post_meta( $product_id, 'file', 1  ) ); ?></span>
                        </div>
					</td>

					<td class="product-details" data-title="<?php _e( 'Specification', '3dprint' ); ?>">
						<?php _e( 'Volume', '3dprint' ); ?>: <?php echo number_format( get_post_meta( $product_id, 'volume', 1 ), 2, '.', '' ); ?> mm<sup>3</sup><br>
                        <?php _e( 'Surface area', '3dprint' ); ?>: <?php echo number_format( get_post_meta( $product_id, 'surface_area', 1 ), 2, '.', '' ); ?> mm<sup>2</sup><br>
                        <?php _e( 'Weight', '3dprint' ); ?>: <?php echo number_format( get_post_meta( $product_id, 'weight', 1 ), 3, '.', '' ); ?> g<br>
                        <?php _e( 'Density', '3dprint' ); ?>: <?php echo number_format( get_post_meta( $product_id, 'density', 1 ), 3, '.', '' ); ?> g/cm<sup>3</sup>
					</td>
                    
                    <td class="product-price" data-title="<?php _e( 'Unit Price', '3dprint' ); ?>">
						<?php
							echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
						?>
					</td>

					<td class="product-quantity" data-title="<?php _e( 'Quantity', 'woocommerce' ); ?>">
						<?php
							if ( $_product->is_sold_individually() ) {
								$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
							} else {
								$product_quantity = woocommerce_quantity_input( array(
									'input_name'  => "cart[{$cart_item_key}][qty]",
									'input_value' => $cart_item['quantity'],
									'max_value'   => $_product->backorders_allowed() ? '' : $_product->get_stock_quantity(),
									'min_value'   => '0'
								), $_product, false );
							}

							//echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key, $cart_item );
						?>
                        <div class="box-material"><?php echo str_replace( array( '<div class="quantity">', '</div>', 'number' ), array( '<div class="quantity"><button class="bt-minus"></button>', '<button class="bt-plus"></button></div>', 'text' ), $product_quantity ); ?>
					</td>

					<td class="product-subtotal" data-title="<?php _e( 'Total', 'woocommerce' ); ?>">
						<?php
							echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
						?>
					</td>
                    
                    <td class="product-remove" data-title="<?php _e( 'Operating', 'woocommerce' ); ?>">
						<?php
							echo apply_filters( 'woocommerce_cart_item_remove_link', sprintf(
								'<a href="%s" class="my-remove" title="%s" data-product_id="%s" data-product_sku="%s">%s</a>',
								esc_url( WC()->cart->get_remove_url( $cart_item_key ) ),
								__( 'Remove this item', 'woocommerce' ),
								esc_attr( $product_id ),
								esc_attr( $_product->get_sku() ),
								__( 'delete', '3dprint' )
							), $cart_item_key );
						?>
					</td>
				</tr>
				<?php
			}
		}

		do_action( 'woocommerce_cart_contents' );
		?>
        
		<tr class="nodisplay">
			<td colspan="7" class="actions">

				<?php if ( wc_coupons_enabled() ) { ?>
					<div class="coupon">

						<label for="coupon_code"><?php _e( 'Coupon:', 'woocommerce' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'woocommerce' ); ?>" /> <input type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply Coupon', 'woocommerce' ); ?>" />

						<?php do_action( 'woocommerce_cart_coupon' ); ?>
					</div>
				<?php } ?>

				<input type="submit" class="button" name="update_cart" value="<?php esc_attr_e( 'Update Cart', 'woocommerce' ); ?>" />

				<?php do_action( 'woocommerce_cart_actions' ); ?>

				<?php wp_nonce_field( 'woocommerce-cart' ); ?>
			</td>
		</tr>
        
		<?php do_action( 'woocommerce_after_cart_contents' ); ?>
	</tbody>
</table>

<?php do_action( 'woocommerce_after_cart_table' ); ?>

</form>

<div class="cart_bottom_text clearfix">
<p><strong><?php _e( 'Estimated delivery time', '3dprint' ); ?></strong>
<?php _e( 'Your product delivery time for the', '3dprint' ); ?> <span class="cart_date_delivery"><?php echo date_i18n( 'F d, Y', $timestamp_delivery ); ?></span></p>

<p><?php _e( '<span class="note">Note:</span> When your order contains multiple items, the delivery time will be shipped the latest products prevail. If you have special needs, please remark in the 
order described, or call 0755-25583221 online customer service contact.', '3dprint' ); ?></p>
</div> 

<div class="cart-collaterals">

	<?php do_action( 'woocommerce_cart_collaterals' ); ?>

</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
