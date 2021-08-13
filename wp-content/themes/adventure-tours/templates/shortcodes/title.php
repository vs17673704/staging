<?php
/**
 * Shortcode [title] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string  $text
 * @var string  $subtitle
 * @var string  $size
 * @var string  $position
 * @var boolean $decoration
 * @var boolean $underline
 * @var string  $style
 * @var string  $css_class
 * @var string  $view
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   1.2.3
 */
$size_class = ( 'big' == $size ) ? ' title--big' : '';
$position_class = ( 'center' == $position ) ? ' title--center' : '';
$underline_class = $underline ? ' title--underline' : '';
$style_class = ( 'light' == $style ) ? ' title--light title--underline-light' : '';

$decoration_class = '';
if ( $decoration ) {
	switch ( $position ) {
		case 'center':
			$decoration_class = ' title--decoration-bottom-center';
			break;
		case 'left':
			$decoration_class = ' title--decoration-bottom-left';
			break;
	}
}

$title_class = $size_class . $position_class . $underline_class . $style_class . $decoration_class;
if ( ! empty( $css_class ) ) {
	$title_class .= ' ' . $css_class;
}
?>
<div class="title<?php echo esc_attr( $title_class ); ?>">
	<?php if ( $subtitle ) { ?>
		<div class="title__subtitle"><?php echo esc_html( $subtitle ); ?></div>
	<?php } ?>
	<h3 class="title__primary"><?php echo esc_html( $text ); ?></h3>
</div>
