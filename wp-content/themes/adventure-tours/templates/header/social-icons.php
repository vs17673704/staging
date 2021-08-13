<?php
/**
 * Social icons rendering template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   4.1.5
 */

$social_icons = array(
	'facebook' => 'facebook',
	'twitter' => 'twitter',
	'googleplus' => 'google-plus',
	'pinterest' => 'pinterest',
	'linkedin' => 'linkedin',
	'instagram' => 'instagram',
	'dribbble' => 'dribbble',
	'tumblr' => 'tumblr',
	'vk' => 'vk',
);

$links_set = array();
foreach ( $social_icons as $key => $icon_class ) {
	$url = adventure_tours_get_option( 'social_link_' . $key );
	if ( $url ) {
		$links_set[] = array(
			'icon_class' => 'fa fa-' . $icon_class,
			'url' => $url
		);
	}
}
for( $i = 1; $i <= 5; $i++ ) {
	$url = adventure_tours_get_option( "social_link_{$i}_is_active" ) ? adventure_tours_get_option( "social_link_{$i}_url" ) : null;
	if ( ! $url ) {
		continue;
	}
	$icon_class = adventure_tours_get_option( "social_link_{$i}_icon" );
	if ( $icon_class ) {
		$links_set[] = array(
			'icon_class' => 'fa ' . $icon_class,
			'url' => $url
		);
	}
}

if ( $links_set ){
	$open_links_in_new_tab = adventure_tours_get_option( 'open_social_link_in_new_tab' );
	$link_template = '<a href="%s"' . ( $open_links_in_new_tab ? ' target="_blank"' : '' ) . '><i class="%s"></i></a>';
	$social_icons_html = '';
	foreach ( $links_set as $link_info ) {
		$social_icons_html .= sprintf(
			$link_template,
			esc_url( $link_info['url'] ),
			esc_attr( $link_info['icon_class'] )
		);
	}
	printf( '<div class="header__info__item header__info__item--delimiter header__info__item--social-icons">%s</div>',
		$social_icons_html
	);
}

