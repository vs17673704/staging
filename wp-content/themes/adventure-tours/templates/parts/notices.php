<?php
/**
 * Notices view.
 *
 * @var array $messages
 * @var string $type
 *
 * @author    Themedelight
 * @package   Themedelight/AdventureTours
 * @version   4.1.2
 */

if ( ! $messages ){
	return;
}

// type success default
$types = array(
	'error' => 'notices-box--error',
	'notice' => 'notices-box--notice',
);

$type = isset( $type ) ? $type : '';
$type_class = isset( $types[$type] ) ? ' ' . $types[$type] : '';

$icon_class = ' fa-check-circle';
switch( $type ) {
	case 'error':
		$icon_class = ' fa-exclamation-triangle';
		break;
	case 'notice':
		$icon_class = ' fa-info-circle';
		break;
}
?>

<div class="notices-box block-after-indent<?php echo esc_attr( $type_class ); ?>">
	<ul class="notices-box__list">
		<?php foreach( $messages as $message ) { ?>
		<?php
			$message_text = is_string($message) ? $message : (
					is_array($message) && isset($message['notice']) ? $message['notice'] : null
				);

			if (empty($message_text)) {
				continue;
			}
		?>
			<li class="notices-box__item"><i class="notices-box__item__icon fa<?php echo esc_attr( $icon_class ); ?>"></i><?php echo wp_kses_post( $message_text ); ?></li>
		<?php } ?>
	</ul>
</div>