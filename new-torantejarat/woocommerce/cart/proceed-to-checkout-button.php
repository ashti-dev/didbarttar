<?php
/**
 * Proceed to checkout button
 *
 * Contains the markup for the proceed to checkout button on the cart.
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/proceed-to-checkout-button.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 7.0.1
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}
?>

<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="checkout-button button alt wc-forward fb-proceed-checkout-btn">
	<span class="fb-btn-text"><?php echo esc_html( apply_filters( 'fb_cart_proceed_to_checkout_text', __( 'ثبت سفارش', 'torantejarat' ) ) ); ?></span>
	<span class="fb-btn-loader" aria-hidden="true"></span>
</a>
