<?php
/**
 * Checkout form — torantejarat layout.
 *
 * Theme override: torantejarat/woocommerce/checkout/form-checkout.php
 * Layout: main column (billing + notes) | sticky review column (summary + payment + trust).
 * All standard WooCommerce hooks are preserved so gateways, plugins and fragments keep working.
 *
 * @version 9.4.0
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;

/* Fired before the guard, same as core: login/coupon/shipping-calculator output lives here. */
do_action( 'woocommerce_before_checkout_form', $checkout );

if ( ! $checkout->is_registration_enabled() && $checkout->is_registration_required() && ! is_user_logged_in() ) {
	echo esc_html( apply_filters( 'woocommerce_checkout_must_be_logged_in_message', __( 'برای ادامه باید وارد حساب کاربری شوید.', 'torantejarat' ) ) );
	return;
}

/**
 * Trust statements shown under the payment button.
 * Only add claims that match your real policy. Override via filter.
 */
$torantejarat_trust_items = apply_filters(
	'torantejarat_checkout_trust_items',
	array(
		__( 'پرداخت امن از طریق درگاه بانکی', 'torantejarat' ),
		__( 'اطلاعات شما فقط برای پردازش سفارش استفاده می‌شود', 'torantejarat' ),
	)
);

/* Steps: cart (done) → checkout (current) → confirmation (next). */
?>

<nav class="torantejarat-steps" aria-label="<?php esc_attr_e( 'مراحل خرید', 'torantejarat' ); ?>">
	<ol class="torantejarat-steps__list">
		<li class="torantejarat-steps__item is-done"><span class="torantejarat-steps__dot" aria-hidden="true"></span><?php esc_html_e( 'سبد خرید', 'torantejarat' ); ?></li>
		<li class="torantejarat-steps__item is-current" aria-current="step"><span class="torantejarat-steps__dot" aria-hidden="true"></span><?php esc_html_e( 'تسویه‌حساب', 'torantejarat' ); ?></li>
		<li class="torantejarat-steps__item"><span class="torantejarat-steps__dot" aria-hidden="true"></span><?php esc_html_e( 'تأیید سفارش', 'torantejarat' ); ?></li>
	</ol>
</nav>

<form name="checkout" method="post" class="checkout woocommerce-checkout torantejarat-checkout" action="<?php echo esc_url( wc_get_checkout_url() ); ?>" enctype="multipart/form-data" aria-label="<?php echo esc_attr__( 'تسویه‌حساب', 'torantejarat' ); ?>">

	<div class="torantejarat-checkout__layout">

		<div class="torantejarat-checkout__main">

			<?php if ( $checkout->get_checkout_fields() ) : ?>

				<?php do_action( 'woocommerce_checkout_before_customer_details' ); ?>

				<section class="torantejarat-card torantejarat-card--billing" id="customer_details" aria-label="<?php esc_attr_e( 'اطلاعات تحویل', 'torantejarat' ); ?>">
					<?php do_action( 'woocommerce_checkout_billing' ); ?>
				</section>

				<section class="torantejarat-card torantejarat-card--shipping">
					<?php do_action( 'woocommerce_checkout_shipping' ); ?>
				</section>

				<?php do_action( 'woocommerce_checkout_after_customer_details' ); ?>

			<?php endif; ?>

		</div>

		<aside class="torantejarat-checkout__side" aria-label="<?php esc_attr_e( 'خلاصه سفارش و پرداخت', 'torantejarat' ); ?>">

			<?php do_action( 'woocommerce_checkout_before_order_review_heading' ); ?>

			<h3 id="order_review_heading" class="torantejarat-card__heading"><?php esc_html_e( 'خلاصه سفارش', 'torantejarat' ); ?></h3>

			<?php do_action( 'woocommerce_checkout_before_order_review' ); ?>

			<div id="order_review" class="woocommerce-checkout-review-order torantejarat-card torantejarat-card--review">
				<?php do_action( 'woocommerce_checkout_order_review' ); ?>
			</div>

			<?php do_action( 'woocommerce_checkout_after_order_review' ); ?>

			<ul class="torantejarat-trust" aria-label="<?php esc_attr_e( 'اطمینان خرید', 'torantejarat' ); ?>">
				<?php foreach ( $torantejarat_trust_items as $torantejarat_trust_text ) : ?>
					<li><?php echo esc_html( $torantejarat_trust_text ); ?></li>
				<?php endforeach; ?>
			</ul>

		</aside>

	</div>

</form>

<?php do_action( 'woocommerce_after_checkout_form', $checkout ); ?>
