<?php
/**
 * Template Name: Page with comments
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   2.3.7
 */

get_header();
?>

<?php if ( have_posts() ) : ?>
	<?php while ( have_posts() ) { the_post(); ?>
		<div class="page-single">
			<main class="page-single__content" role="main">
			<?php
				$thumb = adventure_tours_get_the_post_thumbnail( null, 'thumb_single' );
				if ( $thumb ) {
					printf( '<div class="margin-bottom">%s</div>', $thumb );
				}
				the_content();

				if ( comments_open() ) {
					comments_template();
				}
			?>
			</main>
		</div>
	<?php } ?>
<?php else : ?>
	<?php get_template_part( 'content', 'none' ); ?>
<?php endif; ?>

<?php get_footer(); ?>
