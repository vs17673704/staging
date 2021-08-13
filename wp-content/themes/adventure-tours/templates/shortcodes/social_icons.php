<?php
/**
 * Shortcode [social_icons] view.
 * For more detailed list see list of shortcode attributes.
 *
 * @var string $title
 * @var bool   $open_url_in_new_tab
 * @var string $facebook_url
 * @var string $twitter_url
 * @var string $googleplus_url
 * @var string $youtube_url
 * @var string $pinterest_url
 * @var string $linkedin_url
 * @var string $instagram_url
 * @var string $dribbble_url
 * @var string $tumblr_url
 * @var string $vk_url
 * @var string $tripadvisor_url
 * @var string $css_class
 * @var string $view
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   4.1.4
 */


$elements_full_set = array(
	'facebook' => $facebook_url,
	'twitter' => $twitter_url,
	'google' => $googleplus_url,
	'youtube' => $youtube_url,
	'pinterest' => $pinterest_url,
	'linkedin' => $linkedin_url,
	'instagram' => $instagram_url,
	'dribbble' => $dribbble_url,
	'tumblr' => $tumblr_url,
	'vk' => $vk_url,
	'tripadvisor' => $tripadvisor_url,
);

$new_tab_attribute = !empty($open_url_in_new_tab) ? ' target="_blank"' : '';
?>
<div class="social-icons social-icons--square<?php if ( ! empty( $css_class ) ) { echo ' ' . esc_attr( $css_class ); }; ?>">
<?php if ( $title ) { ?>
	<div class="social-icons__title"><?php echo esc_html( $title ); ?></div>
<?php } ?>
	<div class="social-icons__icons">
	<?php foreach($elements_full_set as $el_code => $el_url_address){
		if (!empty($el_url_address)){
			printf(
				'<a href="%s" class="social-icons__icon social-icons__icon--%s"%s><i class="fa fa-%s"></i></a>',
				esc_url($el_url_address),
				$el_code,
				$new_tab_attribute,
				$el_code
			);
		}
	} ?>
	</div>
</div>
