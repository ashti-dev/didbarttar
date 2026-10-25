<?php
/**
 * Checkout Payment Section
 * Overridden for FrontBaz Checkout
 *
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_before_payment' );
}
?>

<div id="payment" class="woocommerce-checkout-payment fb-payment-section">

	<div class="fb-checkout-section-header">
		<h3 class="fb-checkout-section-title"><?php esc_html_e( 'روش پرداخت', 'hello-elementor-child' ); ?></h3>
		<p class="fb-checkout-section-desc"><?php esc_html_e( 'درگاه یا روش پرداخت مورد نظر خود را جهت واریز وجه انتخاب نمایید.', 'hello-elementor-child' ); ?></p>
	</div>

	<?php if ( WC()->cart && WC()->cart->needs_payment() ) : ?>
		<ul class="wc_payment_methods payment_methods methods fb-payment-gateways-list" aria-label="<?php esc_attr_e( 'Payment methods', 'woocommerce' ); ?>">
			<?php
			if ( ! empty( $available_gateways ) ) {
				foreach ( $available_gateways as $gateway ) {
					wc_get_template( 'checkout/payment-method.php', array( 'gateway' => $gateway ) );
				}
			} else {
				echo '<li class="fb-no-gateways-notice">';
				wc_print_notice( apply_filters( 'woocommerce_no_available_payment_methods_message', WC()->customer->get_billing_country() ? esc_html__( 'متأسفانه در حال حاضر درگاه یا شیوه پرداختی یافت نشد.', 'hello-elementor-child' ) : esc_html__( 'لطفاً مشخصات و آدرس خود را برای مشاهده روش‌های پرداخت تکمیل فرمایید.', 'hello-elementor-child' ) ), 'notice' );
				echo '</li>';
			}
			?>
		</ul>
	<?php endif; ?>

	<div class="form-row place-order fb-place-order-wrap" style="margin-top: 20px;">
		<noscript>
			<?php
			printf( esc_html__( 'از آنجا که جاوااسکریپت در مرورگر شما غیرفعال است، لطفاً پیش از ثبت سفارش روی دکمه %1$sبه‌روزرسانی مجموع%2$s کلیک کنید.', 'hello-elementor-child' ), '<em>', '</em>' );
			?>
			<br/><button type="submit" class="button alt" name="woocommerce_checkout_update_totals" value="<?php esc_attr_e( 'به‌روزرسانی مجموع', 'hello-elementor-child' ); ?>"><?php esc_html_e( 'به‌روزرسانی مجموع', 'hello-elementor-child' ); ?></button>
		</noscript>

		<?php wc_get_template( 'checkout/terms.php' ); ?>

		<?php do_action( 'woocommerce_review_order_before_submit' ); ?>

		<!-- The Custom Styled Pay Button matching user's reference -->
		<?php
		$btn_text = ! empty( $order_button_text ) ? $order_button_text : apply_filters( 'fb_checkout_place_order_text', __( 'پرداخت', 'hello-elementor-child' ) );
		ob_start();
		wc_cart_totals_order_total_html();
		$order_total_html = ob_get_clean();

		$custom_btn_html = sprintf(
			'<button type="submit" class="button alt fb-checkout-submit-btn" name="woocommerce_checkout_place_order" id="place_order" value="%1$s" data-value="%1$s"><span class="fb-btn-text">%2$s</span> <span class="fb-btn-amount">(<span class="fb-checkout-submit-amount">%3$s</span>)</span><span class="fb-btn-loader" aria-hidden="true"></span></button>',
			esc_attr( $order_button_text ),
			esc_html( $btn_text ),
			$order_total_html
		);
		echo apply_filters( 'woocommerce_order_button_html', $custom_btn_html ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>

		<?php do_action( 'woocommerce_review_order_after_submit' ); ?>

		<?php wp_nonce_field( 'woocommerce-process_checkout', 'woocommerce-process-checkout-nonce' ); ?>
	</div>

</div>

<?php
if ( ! wp_doing_ajax() ) {
	do_action( 'woocommerce_review_order_after_payment' );
}
