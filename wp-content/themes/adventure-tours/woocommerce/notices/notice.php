<?php
/**
 * Show messages
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

<?php foreach ( $notices as $notice ) : ?>
	<div class="woocommerce-info"<?php echo wc_get_notice_data_attr( $notice ); ?>><i class="fa fa-info-circle woocommerce-info-icon"></i><?php echo wc_kses_notice( $notice['notice'] ); ?></div>
<?php endforeach; ?>
