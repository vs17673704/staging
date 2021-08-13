<?php
/**
 * Template for pages with empty content.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   2.4.1
 */

?>
<div class="page-404">
<?php if ( is_home() && current_user_can( 'publish_posts' ) ) : ?>
	<div class="page-404__box padding-all">
		<?php
			printf( esc_html__( 'Ready to publish your first post?', 'adventure-tours' ) );
			printf( ' <a href="%1$s">' . esc_html__( 'Get started here', 'adventure-tours' ) . '</a>.', admin_url( 'post-new.php' ) );
		?>
	</div>
<?php elseif ( is_search() ) : ?>
	<div class="page-404__box padding-top padding-bottom">
		<div class="page-404__notice padding-left padding-right"><?php esc_html_e( 'Sorry, but nothing matched your search terms. Please try again with different keywords.', 'adventure-tours' ); ?></div>
		<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form page-404__form page-404__form--style2 padding-left padding-right">
		<?php if ( adventure_tours_check( 'is_wpml_in_use' ) ) { ?>
			<input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>">
		<?php } ?>
			<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Type in your request...', 'adventure-tours' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
			<div class="page-404__form--style2__button-wrap">
				<button type="submit" class="search-submit page-404__form--style2__button atbtn"><i class="atbtn__icon fa fa-search"></i><?php esc_attr_e( 'Search', 'adventure-tours' ); ?></button>
			</div>
		</form>
	</div>
<?php else : ?>
	<div class="page-404__container stick-to-top stick-to-bottom">
		<div class="page-404__content">
			<div class="page-404__image"></div>
			<div class="page-404__map"></div>
			<p class="page-404__description"><?php esc_html_e( 'Oops! The page you are looking for is not found!', 'adventure-tours' ); ?></p>
			<form role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>" class="search-form page-404__form page-404__form--style1">
			<?php if ( adventure_tours_check( 'is_wpml_in_use' ) ) { ?>
				<input type="hidden" name="lang" value="<?php echo esc_attr( ICL_LANGUAGE_CODE ); ?>">
			<?php } ?>
				<input type="search" class="search-field" placeholder="<?php esc_attr_e( 'Type in your request...', 'adventure-tours' ); ?>" value="<?php echo get_search_query(); ?>" name="s">
				<i class="fa fa-search"></i>
				<input type="submit" class="search-submit" value="<?php esc_attr_e( 'Search', 'adventure-tours' ); ?>">
			</form>
		</div>
	</div>
<?php endif; ?>
</div>