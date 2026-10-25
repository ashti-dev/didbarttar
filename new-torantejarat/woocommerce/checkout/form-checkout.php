<?php
/**
 * Checkout Form Template
 * Overridden for FrontBaz RTL Modern Combined Cart & Checkout
 *
 * @version 9.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

do_action( 'woocommerce_before_checkout_form', $checkout );

// If checkout registration is disabled and not logged in, user cannot checkout.
if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'You must be logged in to checkout.', 'woocommerce' ) ) );
	return;
}
?>

<form name="checkout" method="post" class="checkout woocommerce-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'Checkout', 'woocommerce' ); ?>">

	<div class="fb-checkout-wrapper">


		<!-- Two-Column Grid -->
		<div class="fb-checkout-grid">

			<!-- Column 1 (Right in RTL): Customer Details Only -->
			<div class="fb-checkout-col-main">

				<!-- Section: Customer Details -->
				<div class="fb-customer-details-box">
					<div class="fb-checkout-section-header">
						<h2 class="fb-checkout-section-title"><?php esc_html_e( 'مشخصات و آدرس تحویل', 'torantejarat' ); ?></h2>
						<p class="fb-checkout-section-desc"><?php esc_html_e( 'جهت ارسال دقیق و سریع سفارش، اطلاعات فردی و آدرس پستی خود را تکمیل نمایید.', 'torantejarat' ); ?></p>
					</div>

					<?php if ( $checkout->get_checkout_fields() ) : ?>
						<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

						<div id="customer_details">
							<div class="fb-billing-fields-wrap">
								<?php do_action( 'woocommerce_checkout_billing' ); ?>
							</div>

							<div class="fb-shipping-fields-wrap">
								<?php do_action( 'woocommerce_checkout_shipping' ); ?>
							</div>
						</div>

						<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>
					<?php endif; ?>
				</div>

			</div>

			<!-- Column 2 (Left in RTL): Current Order, Payment Gateways & Pay Button -->
			<div class="fb-checkout-col-sidebar">
				<div id="order_review" class="woocommerce-checkout-review-order">
					<?php do_action( 'woocommerce_checkout_order_review' ); ?>
				</div>
			</div>

		</div>

	<!-- Mobile Sticky Bottom Bar (Fixed to bottom on mobile) -->
	<div class="fb-mobile-checkout-bar">
		<div class="fb-mobile-bar-content">
			<button type="button" class="fb-checkout-submit-btn fb-mobile-bar-btn" id="fb_mobile_place_order_btn">
				<span class="fb-btn-text"><?php echo esc_html( apply_filters( 'fb_checkout_place_order_text', __( 'پرداخت', 'torantejarat' ) ) ); ?></span>
				<span class="fb-btn-amount">(<span class="fb-checkout-submit-amount fb-mobile-place-order-amount"><?php wc_cart_totals_order_total_html(); ?></span>)</span>
				<span class="fb-btn-loader" aria-hidden="true"></span>
			</button>
		</div>
	</div>

	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
