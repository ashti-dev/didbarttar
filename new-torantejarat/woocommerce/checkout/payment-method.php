<?php
/**
 * Single payment method option (card style).
 *
 * Theme override: torantejarat/woocommerce/checkout/payment-method.php
 * Keeps the class names and input names WooCommerce JS relies on (payment_box, input-radio, payment_method).
 *
 * @version 3.5.0
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;
?>
<li class="wc_payment_method payment_method_<?php echo esc_attr( $gateway->id ); ?>">
	<input
		id="payment_method_<?php echo esc_attr( $gateway->id ); ?>"
		type="radio"
		class="input-radio"
		name="payment_method"
		value="<?php echo esc_attr( $gateway->id ); ?>"
		<?php checked( $gateway->chosen, true ); ?>
		data-order_button_text="<?php echo esc_attr( $gateway->order_button_text ); ?>"
	/>

	<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="torantejarat-pay__label">
		<span class="torantejarat-pay__title"><?php echo wp_kses_post( $gateway->get_title() ); ?></span>
		<?php if ( $gateway->get_icon() ) : ?>
			<span class="torantejarat-pay__icon"><?php echo wp_kses_post( $gateway->get_icon() ); ?></span>
		<?php endif; ?>
	</label>

	<?php if ( $gateway->has_fields() || $gateway->get_description() ) : ?>
		<div class="payment_box payment_method_<?php echo esc_attr( $gateway->id ); ?>" <?php if ( ! $gateway->chosen ) : /* Hidden until selected; WC JS toggles it. */ ?>style="display:none;"<?php endif; ?>>
			<?php $gateway->payment_fields(); ?>
		</div>
	<?php endif; ?>
</li>
