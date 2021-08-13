<?php
/**
 * Shortcode [tours_list] view list.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $title
 * @var boolean $title_underline
 * @var string  $sub_title
 * @var string  $image_size
 * @var string  $image_size_mobile
 * @var string  $btn_more_text           text for more button
 * @var string  $btn_more_link           url address for more button
 * @var string  $description_words_limit limit for words that should be outputed for each item
 * @var string  $price_style             allowed values are: 'default', 'hidden'
 * @var string  $tour_category
 * @var string  $tour_category_ids
 * @var boolean $show_categories
 * @var string  $tour_ids
 * @var string  $show
 * @var int     $number
 * @var string  $css_class
 * @var string  $orderby
 * @var string  $order
 * @var string  $view
 * @var array   $items                   collection of tours that should be rendered.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   4.0.1
 */

if ( ! $items ) {
	return;
}

if ( $image_size_mobile && wp_is_mobile() ) {
	$image_size = $image_size_mobile;
}

$render_ratings = get_option( 'woocommerce_enable_review_rating' ) === 'yes';
?>

<div class="atlist<?php if ( $css_class ) echo ' ' . esc_attr( $css_class ); ?>">
<?php if ( $btn_more_link && ( $title || $sub_title ) ) { ?>
	<div class="title-block-link title-block-link--with-button">
		<div class="title-block-link__text-wrap">
		<?php if ( $title ) { ?>
			<h3 class="title-block-link__title"><?php echo esc_html( $title ); ?></h3>
		<?php } ?>
		<?php if ( $sub_title ) { ?>
			<div class="title-block-link__description"><?php echo esc_html( $sub_title ); ?></div>
		<?php } ?>
		</div>
		<div class="title-block-link__button-wrap">
			<a href="<?php echo esc_url( $btn_more_link ); ?>" class="atbtn atbtn--rounded"><?php echo esc_html( $btn_more_text ); ?><i class="atbtn__icon atbtn__icon--right fa fa-long-arrow-right"></i></a>
		</div>
	</div>
<?php } elseif ( $title || $sub_title ) { ?>
	<?php echo do_shortcode( '[title text="' . $title . '" subtitle="' . $sub_title . '" size="big" position="center" decoration="on" underline="' . $title_underline . '" style="dark"]' ); ?>
<?php } ?>
<?php foreach ( $items as $item_index => $item ) : ?>
	<?php
		$item_post = adventure_tours_get_post_for_product( $item );
		setup_postdata( $GLOBALS['post']=$item_post );// addon for WooCommerce Jetapack plugin

		$item_id = $item->get_id();
		$permalink = get_permalink( $item_id );
		$item_title = $item->get_title();
		$thumb_html = adventure_tours_get_the_post_thumbnail( $item_id, $image_size );
		$price_html = isset($price_style) && 'hidden' == $price_style ? null : $item->get_price_html();

		ob_start();
		adventure_tours_render_product_attributes(array(
			'before' => '<div class="item-attributes item-attributes--style2">',
			'after' => '</div>',
			'before_each' => '<div class="item-attributes__item">',
			'after_each' => '</div>',
			'limit' => 3,
		), $item_id );
		$attributes = ob_get_clean();
	?>
	<div class="atlist__item margin-bottom">
		<div class="atlist__item__image">
		<?php printf('<a class="atlist__item__image-wrap" href="%s">%s</a>',
			esc_url( $permalink ),
			$thumb_html ? $thumb_html : adventure_tours_placeholder_img( $image_size )
		); ?>
		<?php if ( $show_categories ) {
			adventure_tours_render_tour_icons(array(
				'before' => '<div class="atlist__item__icons">',
				'after' => '</div>',
			), $item_id);
		} ?>
		<?php adventure_tours_renders_tour_badge( array(
			'tour_id' => $item_id,
			'wrap_css_class' => 'atlist__item__angle-wrap',
			'css_class' => 'atlist__item__angle',
		) );?>
		</div>
		<div class="atlist__item__content<?php if ( ! $attributes ) { echo ' atlist__item__content--full-height'; }; ?>">
			<div class="atlist__item__content__items">
				<div class="atlist__item__content__item">
					<h2 class="atlist__item__title"><a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $item_title ); ?></a></h2>
					<?php if ( $description_words_limit > 0 ) { ?>
						<div class="atlist__item__description"><?php echo adventure_tours_get_short_description( $item_post, $description_words_limit ); ?></div>
					<?php } ?>
				</div>
				<div class="atlist__item__content__item atlist__item__content__item--alternative">
					<?php if ( $render_ratings && $item->get_rating_count() > 0 ) {
						$review_count = $item->get_review_count();
						$average = $item->get_average_rating();

						adventure_tours_renders_stars_rating( $average, array(
							'before' => '<div class="atlist__item__rating">',
							'after' => '</div>',
						) );
						echo '<div class="atlist__item__rating-value">' . $average . ' / ' . sprintf( _n( '1 review', '%s reviews', $review_count, 'adventure-tours' ), $review_count ) . '</div>';
					} ?>
					<?php if ( $price_html ) {
						printf( '<div class="atlist__item__price%s"><a href="%s">%s</a></div>',
							$item->is_variable_tour() ? ' atlist__item__price--variable' : '',
							esc_url( $permalink ),
							$price_html
						);
					} ?>
					<?php
						$label_text = $price_html ? apply_filters( 'adventure_tours_list_price_decoration_label', esc_html__( 'per person', 'adventure-tours' ), $item ) : null;
						if ( $label_text ) {
							printf('<div class="atlist__item__price-label">%s</div>', esc_html( $label_text ) );
						}
					?>
					<div class="atlist__item__read-more"><a href="<?php echo esc_url( $permalink ); ?>" class="atbtn atbtn--small atbtn--rounded atbtn--light"><?php esc_html_e( 'view tour', 'adventure-tours' ); ?></a></div>
				</div>
			</div>
			<?php if ( $attributes ) {
				printf( '<div class="atlist__item__attributes">%s</div>', $attributes ); 
			} ?>
		</div>
	</div>
<?php endforeach; ?>
<?php wp_reset_postdata();// addon for WooCommerce Jetapack plugin ?>
</div>