<?php
/**
 * Product price scheme rendering template part.
 *
 * @var object $product
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   3.7.1
 */

$price = $product ? $product->get_price() : null;
if ( empty( $price ) ) {
	return;
}

$has_on_sale_date = $product->is_on_sale() && $product->get_date_on_sale_to();
$price_valid_until = $has_on_sale_date 
	? date( 'Y-m-d', $product->get_date_on_sale_to()->getTimestamp() )
	: date( 'Y-12-31', current_time( 'timestamp', true ) + YEAR_IN_SECONDS );

?>

<span itemprop="offers" itemscope itemtype="https://schema.org/Offer">
	<meta itemprop="price" content="<?php echo esc_attr( $price ); ?>">
	<meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>">
	<meta itemprop="priceValidUntil" content="<?php echo esc_attr( $price_valid_until ); ?>">
	<meta itemprop="url" content="<?php echo esc_url( get_permalink() ); ?>">
	<link itemprop="availability" href="https://schema.org/<?php printf( '%s', $product->is_in_stock() ? 'InStock' : 'OutOfStock' ); ?>">
</span>