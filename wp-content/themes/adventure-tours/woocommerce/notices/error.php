<?php
/**
 * Show error messages
 *
 * @author      WooThemes
 * @package     WooCommerce/Templates
 * @version     3.9.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( empty( $notices ) ){
	// For WooCommerce < 3.9.0
	if ( ! empty( $messages ) ) {
		$notices = array();
		foreach ( $messages as $_notice_text ) {
			$notices[] = array(
				'notice' => $_notice_text
			);
		}
	} else {
		return;
	}
}

?>
<ul class="woocommerce-error" role="alert">
	<?php foreach ( $notices as $notice ) : ?>
		<li<?php echo wc_get_notice_data_attr( $notice ); ?>><i class="fa fa-exclamation-triangle woocommerce-error-icon"></i><?php echo wc_kses_notice( $notice['notice'] ); ?></li>
	<?php endforeach; ?>
</ul>
