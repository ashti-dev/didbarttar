<?php
/**
 * Output a single payment method
 * Overridden for FrontBaz Card Selection Style
 *
 * @version 3.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?> fb-payment-gateway-item <?php echo $gateway->chosen ? 'fb-active' : ''; ?>">

	<div class="fb-payment-gateway-header">
		<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="fb-payment-gateway-info">
			<div class="fb-payment-gateway-icon">
				<?php 
				$gateway_icon = $gateway->get_icon();
				if ( ! empty( $gateway_icon ) ) {
					echo $gateway_icon; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
				} else {
					// Fallback SVG icon for payment gateway
					echo '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="1" y="4" width="22" height="16" rx="2" ry="2"></rect><line x1="1" y1="10" x2="23" y2="10"></line></svg>';
				}
				?>
			</div>
			<span class="fb-payment-gateway-title"><?php echo wp_kses_post( $gateway->get_title() ); ?></span>
		</label>

		<div class="fb-payment-gateway-radio-wrap">
			<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>" />
		</div>
	</div>

	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>

</li>
