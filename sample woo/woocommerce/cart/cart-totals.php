<?php
/**
 * Custom FrontBaz Cart Totals Template
 * Matches Checkout Order Summary Box
 *
 * @package HelloElementorChild
 */

defined( 'ABSPATH' ) || exit;

$cart_count = WC()->cart->get_cart_contents_count();
?>
<div class="cart_totals fb-order-summary-card fb-cart-totals-box <?php echo ( WC()->customer->has_calculated_shipping() ) ? 'calculated_shipping' : ''; ?>">

	<?php do_action( 'woocommerce_before_cart_totals' ); ?>

	<div class="fb-summary-header">
		<h3 class="fb-summary-title"><?php esc_html_e( 'خلاصه سفارش', 'hello-elementor-child' ); ?></h3>
		<span class="fb-summary-badge">
			<?php echo esc_html( sprintf( _n( '%s قلم کالا', '%s قلم کالا', $cart_count, 'hello-elementor-child' ), number_format_i18n( $cart_count ) ) ); ?>
		</span>
	</div>

	<div class="fb-summary-body">

<?php
// Calculate total savings from product regular vs sale prices
$total_savings = 0;
foreach ( WC()->cart->get_cart() as $cart_item ) {
	$_product = $cart_item['data'];
	if ( $_product && $_product->is_on_sale() ) {
		$raw_reg  = (float) $_product->get_regular_price();
		$raw_sale = (float) $_product->get_price();
		if ( $raw_reg > $raw_sale ) {
			$reg_display  = (float) wc_get_price_to_display( $_product, array( 'price' => $raw_reg ) );
			$sale_display = (float) wc_get_price_to_display( $_product );
			$total_savings += ( $reg_display - $sale_display ) * $cart_item['quantity'];
		}
	}
}
?>
		<!-- Subtotal -->
		<div class="fb-summary-row cart-subtotal">
			<span class="fb-row-label"><?php esc_html_e( 'قیمت کالاها', 'hello-elementor-child' ); ?></span>
			<span class="fb-row-val" data-title="<?php esc_attr_e( 'Subtotal', 'woocommerce' ); ?>"><?php wc_cart_totals_subtotal_html(); ?></span>
		</div>

		<!-- Total Savings / سود شما از خرید -->
		<?php if ( $total_savings > 0 ) : ?>
			<div class="fb-summary-row fb-savings-row">
				<span class="fb-row-label fb-savings-label">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
					<?php echo esc_html( apply_filters( 'fb_cart_savings_label_text', __( 'سود شما از خرید', 'hello-elementor-child' ) ) ); ?>
				</span>
				<span class="fb-row-val fb-savings-val" data-title="<?php esc_attr_e( 'سود شما از خرید', 'hello-elementor-child' ); ?>">
					<?php echo wc_price( $total_savings ); ?>
				</span>
			</div>
		<?php endif; ?>

		<!-- Applied Coupons -->
		<?php foreach ( WC()->cart->get_coupons() as $code => $coupon ) : ?>
			<div class="fb-summary-row fb-discount-row cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
				<span class="fb-row-label">
					<?php wc_cart_totals_coupon_label( $coupon ); ?>
					<a href="<?php echo esc_url( add_query_arg( 'remove_coupon', rawurlencode( $coupon->get_code() ), wc_get_cart_url() ) ); ?>" class="woocommerce-remove-coupon fb-coupon-remove-link" data-coupon="<?php echo esc_attr( $coupon->get_code() ); ?>" title="<?php esc_attr_e( 'حذف کد تخفیف', 'hello-elementor-child' ); ?>">[حذف]</a>
				</span>
				<span class="fb-row-val fb-discount-val" data-title="<?php echo esc_attr( wc_cart_totals_coupon_label( $coupon, false ) ); ?>"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<!-- Shipping Notice & Free Shipping Progress -->
		<?php
		$fb_free_shipping = function_exists( 'fb_get_free_shipping_info' ) ? fb_get_free_shipping_info() : false;
		$fb_is_free       = ( $fb_free_shipping && ! empty( $fb_free_shipping['is_free'] ) );
		?>
		<div class="fb-shipping-wrapper">
			<div class="fb-summary-row fb-shipping-row fb-shipping-notice-row">
				<span class="fb-row-label"><?php esc_html_e( 'هزینه ارسال', 'hello-elementor-child' ); ?></span>
				<span class="fb-row-val fb-shipping-notice-val <?php echo $fb_is_free ? 'fb-is-free' : ''; ?>">
					<?php
					if ( $fb_is_free ) {
						echo esc_html( apply_filters( 'fb_cart_free_shipping_text', __( 'ارسال رایگان', 'hello-elementor-child' ) ) );
					} else {
						echo esc_html( apply_filters( 'fb_cart_shipping_notice_text', __( 'محاسبه در مرحله ثبت سفارش', 'hello-elementor-child' ) ) );
					}
					?>
				</span>
			</div>

			<?php if ( $fb_free_shipping && ! $fb_is_free ) : ?>
				<div class="fb-free-shipping-box" id="fb_free_shipping_box" data-threshold="<?php echo esc_attr( $fb_free_shipping['min_amount'] ); ?>">
					<div class="fb-free-shipping-text">
						<?php
						$diff_html = '<span class="fb-free-shipping-diff">' . wc_price( $fb_free_shipping['remaining'] ) . '</span>';
						echo wp_kses_post(
							sprintf(
								/* translators: %s: remaining amount formatted */
								__( 'تنها با افزودن %s دیگر به سبد خرید، ارسال سفارش شما رایگان میشود!', 'hello-elementor-child' ),
								$diff_html
							)
						);
						?>
					</div>
					<div class="fb-free-shipping-bar-wrap">
						<div class="fb-free-shipping-bar" style="width: <?php echo esc_attr( $fb_free_shipping['percent'] ); ?>%;"></div>
					</div>
				</div>
			<?php endif; ?>
		</div>

		<!-- Fees -->
		<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
			<div class="fb-summary-row fee">
				<span class="fb-row-label"><?php echo esc_html( $fee->name ); ?></span>
				<span class="fb-row-val" data-title="<?php echo esc_attr( $fee->name ); ?>"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<!-- Tax -->
		<?php
		if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) {
			$taxable_address = WC()->customer->get_taxable_address();
			$estimated_text  = '';

			if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
				$estimated_text = sprintf( ' <small>' . esc_html__( '(تخمینی برای %s)', 'hello-elementor-child' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
			}

			if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) {
				foreach ( WC()->cart->get_tax_totals() as $code => $tax ) {
					?>
					<div class="fb-summary-row tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
						<span class="fb-row-label"><?php echo esc_html( $tax->label ) . $estimated_text; ?></span>
						<span class="fb-row-val" data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></span>
					</div>
					<?php
				}
			} else {
				?>
				<div class="fb-summary-row tax-total">
					<span class="fb-row-label"><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; ?></span>
					<span class="fb-row-val" data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></span>
				</div>
				<?php
			}
		}
		?>

		<!-- Coupon Toggle & Box -->
		<?php if ( wc_coupons_enabled() ) : ?>
			<div class="fb-coupon-wrapper">
				<button type="button" class="fb-coupon-toggle-btn" id="fb_cart_coupon_toggle">
					<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"></path><line x1="7" y1="7" x2="7.01" y2="7"></line></svg>
					<span>کد تخفیف دارید؟</span>
				</button>
				<div class="fb-coupon-form" id="fb_cart_coupon_box">
					<div class="fb-coupon-input-group">
						<input type="text" name="fb_cart_coupon_code" class="fb-coupon-input" id="fb_cart_coupon_code" placeholder="کد تخفیف را وارد کنید..." />
						<button type="button" class="fb-coupon-btn" id="fb_cart_apply_coupon_btn">اعمال کد</button>
					</div>
					<div class="fb-coupon-message" id="fb_cart_coupon_msg"></div>
				</div>
			</div>
		<?php endif; ?>

		<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>

		<!-- Total Payable -->
		<div class="fb-summary-row fb-total-row order-total">
			<span class="fb-total-label"><?php esc_html_e( 'مبلغ قابل پرداخت', 'hello-elementor-child' ); ?></span>
			<span class="fb-total-val" data-title="<?php esc_attr_e( 'Total', 'woocommerce' ); ?>">
				<?php
				$discount_sum = (float) $total_savings + (float) WC()->cart->get_discount_total();
				if ( $discount_sum > 0 ) {
					$total_before_discount = (float) WC()->cart->total + $discount_sum;
					?>
					<div class="fb-cart-total-wrap">
						<del class="fb-total-old"><?php echo esc_html( number_format_i18n( $total_before_discount ) ); ?></del>
						<div class="fb-total-current"><?php wc_cart_totals_order_total_html(); ?></div>
					</div>
					<?php
				} else {
					wc_cart_totals_order_total_html();
				}
				?>
			</span>
		</div>

		<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>

	</div>

	<!-- Proceed To Checkout Button (Figma Matched) -->
	<div class="wc-proceed-to-checkout fb-proceed-wrapper">
		<?php do_action( 'woocommerce_proceed_to_checkout' ); ?>
	</div>

	<!-- Trust & Service Badges -->
	<div class="fb-cart-guarantees">
		<div class="fb-guarantee-item">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
			<span>ضمانت اصالت و سلامت فیزیکی کالا</span>
		</div>
		<div class="fb-guarantee-item">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="1" y="3" width="15" height="13"></rect><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon><circle cx="5.5" cy="18.5" r="2.5"></circle><circle cx="18.5" cy="18.5" r="2.5"></circle></svg>
			<span>ارسال سریع و پیگیری لحظه‌ای سفارش</span>
		</div>
	</div>

	<?php do_action( 'woocommerce_after_cart_totals' ); ?>

</div>
