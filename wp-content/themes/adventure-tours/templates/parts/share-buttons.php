<?php
/**
 * Post sharing buttons rendering template part.
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   2.1.2
 */

$buttons_set = array(
	'googleplus' => 'googlePlus',
	'facebook' => 'facebook',
	'twitter' => 'twitter',
	'stumbleupon' => 'stumbleupon',
	'linkedin' => 'linkedin',
	'pinterest' => 'pinterest',
	'vk' => 'vk'
);

$buttons_html = array();
foreach ( $buttons_set as $type_key => $btn_code ) {
	if ( adventure_tours_get_option( 'social_sharing_' . $type_key ) ) {
		$buttons_html[] = sprintf( '<div class="share-buttons__item share-buttons__item--%s" data-btntype="%s"></div>', $type_key, $btn_code );
	}
}

if ( ! $buttons_html ) {
	return;
}

$sharrePluginConfig = array(
	// 'urlCurl' => admin_url( 'admin-ajax.php?action=sharrre_curl' ),
	'itemsSelector' => '.share-buttons__item[data-btntype]',
);

wp_enqueue_script( 'sharrre' );
TdJsClientScript::addScript( 'sharreInit', 'Theme.initSharrres(' . wp_json_encode( $sharrePluginConfig ) . ');' );
$thumbnail_src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'full' );
$thumbnail_img_link = isset( $thumbnail_src[0] ) ? $thumbnail_src[0] : '';

printf( '<div class="share-buttons" data-urlshare="%s" data-imageshare="%s">%s</div>',
	esc_url( get_permalink() ),
	$thumbnail_img_link ? esc_url( $thumbnail_img_link ) : '',
	join( '', $buttons_html )
);
