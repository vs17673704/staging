<?php
/**
 * Cart Page
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author   WooThemes
 * @package  WooCommerce/Templates
 * @version  4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$is_wc_older_than_330 = version_compare( WC_VERSION, '3.3.0', '<');
$is_wc_older_than_30 = $is_wc_older_than_330 && version_compare( WC_VERSION, '3.0.0', '<');

// wc_print_notices();

do_action( 'woocommerce_before_cart' ); ?>

<div class="product-box padding-all margin-bottom">
	<form action="<?php echo esc_url( wc_get_cart_url() ); ?>" method="post">

	<?php do_action( 'woocommerce_before_cart_table' ); ?>

	<table class="shop_table cart" cellspacing="0">
		<thead>
			<tr>
				<th class="product-remove">&nbsp;</th>
				<th class="product-thumbnail">&nbsp;</th>
				<th class="product-name"><?php esc_html_e( 'Product', 'adventure-tours' ); ?></th>
				<th class="product-price"><?php esc_html_e( 'Price', 'adventure-tours' ); ?></th>
				<th class="product-quantity"><?php esc_html_e( 'Quantity', 'adventure-tours' ); ?></th>
				<th class="product-subtotal"><?php esc_html_e( 'Subtotal', 'adventure-tours' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php do_action( 'woocommerce_before_cart_contents' ); ?>

			<?php
			foreach ( WC()->cart->get_cart() as $cart_item_key => $cart_item ) {
				$_product     = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );
				$product_id   = apply_filters( 'woocommerce_cart_item_product_id', $cart_item['product_id'], $cart_item, $cart_item_key );

				if ( $_product && $_product->exists() && $cart_item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
					$product_permalink = apply_filters(
						'woocommerce_cart_item_permalink',
						$_product->is_visible() ? $_product->get_permalink( $cart_item ) : '',
						$cart_item,
						$cart_item_key
					);
					?>
					<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'cart_item', $cart_item, $cart_item_key ) ); ?>">

						<td class="product-remove">
							<span class="cart-responsive-title"><?php esc_html_e( 'Delete', 'adventure-tours' ); ?></span>
							<?php
								echo apply_filters( 'woocommerce_cart_item_remove_link',
									sprintf(
										'<a href="%s" class="remove" title="%s">&times;</a>',
										esc_url( $is_wc_older_than_330 ? WC()->cart->get_remove_url( $cart_item_key ) : wc_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove this item', 'adventure-tours' )
									),
									$cart_item_key
								);
							?>
						</td>

						<td class="product-thumbnail">
							<?php
								$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $cart_item, $cart_item_key );

								if ( ! $product_permalink )
									print $thumbnail; // PHPCS: XSS ok.
								else
									printf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // PHPCS: XSS ok.
							?>
						</td>

						<td class="product-name">
							<span class="cart-responsive-title"><?php esc_html_e( 'Product', 'adventure-tours' ); ?></span>
							<?php
								$_prod_name = $is_wc_older_than_30 ? $_product->get_title() : $_product->get_name();

								if ( ! $product_permalink )
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', $_prod_name, $cart_item, $cart_item_key ) . '&nbsp;' );
								else
									echo wp_kses_post( apply_filters( 'woocommerce_cart_item_name', sprintf( '<a href="%s">%s </a>', $_product->get_permalink( $cart_item ), $_prod_name ), $cart_item, $cart_item_key ) );

								do_action( 'woocommerce_after_cart_item_name', $cart_item, $cart_item_key );

								// Meta data
								if ( $is_wc_older_than_330 ) {
									echo WC()->cart->get_item_data( $cart_item );
								} else {
									echo wc_get_formatted_cart_item_data( $cart_item );
								}

								// Backorder notification
								if ( $_product->backorders_require_notification() && $_product->is_on_backorder( $cart_item['quantity'] ) )
									echo wp_kses_post( 
										apply_filters(
											'woocommerce_cart_item_backorder_notification',
											'<p class="backorder_notification">' . esc_html__( 'Available on backorder', 'adventure-tours' ) . '</p>',
											$product_id
										)
									);
							?>
						</td>

						<td class="product-price">
							<span class="cart-responsive-title"><?php esc_html_e( 'Price', 'adventure-tours' ); ?></span>
							<?php
								echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $cart_item, $cart_item_key );
							?>
						</td>

						<td class="product-quantity">
							<span class="cart-responsive-title"><?php esc_html_e( 'Quantity', 'adventure-tours' ); ?></span>
							<?php
								if ( $_product->is_sold_individually() ) {
									$product_quantity = sprintf( '1 <input type="hidden" name="cart[%s][qty]" value="1" />', $cart_item_key );
								} else {
									$is_wc_older_than_30 = version_compare( WC_VERSION, '3.0.0', '<');
									if ( $is_wc_older_than_30 ) {
										$max_quantity = $_product->backorders_allowed() ? '' : $_product->get_stock_quantity();
									} else {
										$max_quantity = $_product->get_max_purchase_quantity();
									}
									$product_quantity = woocommerce_quantity_input( array(
										'input_name'  => "cart[{$cart_item_key}][qty]",
										'input_value' => $cart_item['quantity'],
										'max_value'   => $max_quantity,
										'min_value'   => '0'
									), $_product, false );
								}

								echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $cart_item_key );
							?>
						</td>

						<td class="product-subtotal">
							<span class="cart-responsive-title"><?php esc_html_e( 'Subtotal', 'adventure-tours' ); ?></span>
							<?php
								echo apply_filters( 'woocommerce_cart_item_subtotal', WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
							?>
						</td>
					</tr>
					<?php
				}
			}

			do_action( 'woocommerce_cart_contents' );
			?>
			<tr>
				<td colspan="6" class="actions">

					<?php if ( wc_coupons_enabled() ) { ?>
						<div class="coupon">

							<label for="coupon_code"><?php esc_html_e( 'Coupon:', 'adventure-tours' ); ?></label> <input type="text" name="coupon_code" class="input-text" id="coupon_code" value="" placeholder="<?php esc_attr_e( 'Coupon code', 'adventure-tours' ); ?>" /> <button type="submit" class="button" name="apply_coupon" value="<?php esc_attr_e( 'Apply coupon', 'adventure-tours' ); ?>"><?php esc_attr_e( 'Apply coupon', 'adventure-tours' ); ?></button>

							<?php do_action( 'woocommerce_cart_coupon' ); ?>

						</div>
					<?php } ?>

					<input type="submit" class="button cart-button cart-update-button" name="update_cart" value="<?php esc_attr_e( 'Update Cart', 'adventure-tours' ); ?>" />

					<?php do_action( 'woocommerce_cart_actions' ); ?>

					<?php wp_nonce_field( 'woocommerce-cart', 'woocommerce-cart-nonce' ); ?>
				</td>
			</tr>

			<?php do_action( 'woocommerce_after_cart_contents' ); ?>
		</tbody>
	</table>

	<?php do_action( 'woocommerce_after_cart_table' ); ?>

	</form>
</div>

<?php do_action( 'woocommerce_before_cart_collaterals' ); ?>

<div class="cart-collaterals">
<?php
	/**
	 * woocommerce_cart_collaterals hook.
	 *
	 * @hooked woocommerce_cross_sell_display
	 * @hooked woocommerce_cart_totals - 10
	 */
	do_action( 'woocommerce_cart_collaterals' );
?>
</div>

<?php do_action( 'woocommerce_after_cart' ); ?>
