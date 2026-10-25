<?php
/**
 * Review Order Template
 * Overridden for FrontBaz Modern Combined Cart & Checkout Review
 *
 * @version 11.0.0
 */

defined( 'ABSPATH' ) || exit;

$cart = WC()->cart;
$cart_items = $cart ? $cart->get_cart() : [];
?>

<div class="fb-order-summary-card woocommerce-checkout-review-order-table">

	<div class="fb-order-summary-header fb-summary-accordion-toggle" role="button" tabindex="0" aria-expanded="false" title="<?php esc_attr_e( 'نمایش یا بستن اقلام سبد خرید', 'hello-elementor-child' ); ?>">
		<div class="fb-summary-header-main">
			<div class="fb-summary-title-wrap">
				<h3 class="fb-order-summary-title"><?php esc_html_e( 'سبد خرید شما', 'hello-elementor-child' ); ?></h3>
				<span class="fb-order-items-badge"><?php printf( esc_html__( '%s کالا', 'hello-elementor-child' ), esc_html( $cart->get_cart_contents_count() ) ); ?></span>
			</div>
		</div>
		<div class="fb-summary-toggle-action">
			<span class="fb-toggle-label"><?php esc_html_e( 'مشاهده اقلام', 'hello-elementor-child' ); ?></span>
			<svg class="fb-toggle-chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
				<polyline points="6 9 12 15 18 9"></polyline>
			</svg>
		</div>
	</div>

	<?php do_action( 'woocommerce_review_order_before_cart_contents' ); ?>

	<!-- Items List with AJAX Quantity Controls (Collapsible Accordion) -->
	<div class="fb-order-items-list fb-accordion-content">
		<?php
		foreach ( $cart_items as $cart_item_key => $cart_item ) :
			$_product = apply_filters( 'woocommerce_cart_item_product', $cart_item['data'], $cart_item, $cart_item_key );

			if ( ! $_product || ! $_product->exists() || $cart_item['quantity'] <= 0 ) {
				continue;
			}

			if ( ! apply_filters( 'woocommerce_checkout_cart_item_visible', true, $cart_item, $cart_item_key ) ) {
				continue;
			}

			$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $cart_item ) : '', $cart_item, $cart_item_key );
			$thumbnail         = $_product->get_image( 'woocommerce_thumbnail' );
			$product_name      = apply_filters( 'woocommerce_cart_item_name', $_product->get_name(), $cart_item, $cart_item_key );
			$product_subtotal  = apply_filters( 'woocommerce_cart_item_subtotal', $cart->get_product_subtotal( $_product, $cart_item['quantity'] ), $cart_item, $cart_item_key );
			$is_qty_one        = ( 1 === (int) $cart_item['quantity'] );
			?>

			<div class="fb-order-item" data-cart-item-key="<?php echo esc_attr( $cart_item_key ); ?>">
				<div class="fb-order-item-left">
					<div class="fb-order-item-thumb">
						<?php if ( ! empty( $product_permalink ) ) : ?>
							<a href="<?php echo esc_url( $product_permalink ); ?>" target="_blank" rel="noopener">
								<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
							</a>
						<?php else : ?>
							<?php echo $thumbnail; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<?php endif; ?>
					</div>
					<div class="fb-order-item-meta">
						<div class="fb-order-item-title" title="<?php echo esc_attr( wp_strip_all_tags( $product_name ) ); ?>">
							<?php if ( ! empty( $product_permalink ) ) : ?>
								<a href="<?php echo esc_url( $product_permalink ); ?>" target="_blank" rel="noopener">
									<?php echo wp_kses_post( $product_name ); ?>
								</a>
							<?php else : ?>
								<?php echo wp_kses_post( $product_name ); ?>
							<?php endif; ?>
						</div>
						<?php echo wc_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
						<div class="fb-order-item-qty-label">
							<?php printf( esc_html__( 'تعداد: %s', 'hello-elementor-child' ), esc_html( $cart_item['quantity'] ) ); ?>
						</div>
					</div>
				</div>

				<div class="fb-order-item-right">
					<div class="fb-order-item-price">
						<?php echo $product_subtotal; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>

					<!-- Quantity Increment/Decrement Buttons -->
					<?php
					$max_qty = $_product->get_max_purchase_quantity();
					$is_max  = ( $max_qty > 0 && (int) $cart_item['quantity'] >= $max_qty );
					?>
					<div class="fb-qty-control" data-max-qty="<?php echo esc_attr( $max_qty ); ?>" aria-label="<?php esc_attr_e( 'تغییر تعداد محصول', 'hello-elementor-child' ); ?>">
						<button type="button" class="fb-qty-btn fb-qty-plus <?php echo $is_max ? 'is-disabled' : ''; ?>" <?php echo $is_max ? 'disabled="disabled"' : ''; ?> title="<?php echo $is_max ? esc_attr__( 'حداکثر موجودی در انبار', 'hello-elementor-child' ) : esc_attr__( 'افزایش تعداد', 'hello-elementor-child' ); ?>">+</button>
						<input type="text" class="fb-qty-val fb-qty-input" value="<?php echo esc_attr( $cart_item['quantity'] ); ?>" readonly />
						<button type="button" class="fb-qty-btn fb-qty-minus <?php echo $is_qty_one ? 'is-trash' : ''; ?>" title="<?php echo $is_qty_one ? esc_attr__( 'حذف محصول از سبد خرید', 'hello-elementor-child' ) : esc_attr__( 'کاهش تعداد', 'hello-elementor-child' ); ?>">
							<?php if ( $is_qty_one ) : ?>
								<svg class="fb-trash-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
									<polyline points="3 6 5 6 21 6"></polyline>
									<path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path>
									<line x1="10" y1="11" x2="10" y2="17"></line>
									<line x1="14" y1="11" x2="14" y2="17"></line>
								</svg>
							<?php else : ?>
								−
							<?php endif; ?>
						</button>
					</div>
				</div>
			</div>

		<?php endforeach; ?>
	</div>

	<?php do_action( 'woocommerce_review_order_after_cart_contents' ); ?>

	<!-- Totals Breakdown List (Items count on top) -->
	<div class="fb-order-breakdown-list">
		<!-- Return to Cart Action (Replaces redundant item count) -->
		<div class="fb-breakdown-row fb-return-to-cart-row">
			<a href="<?php echo esc_url( wc_get_cart_url() ); ?>" class="fb-return-to-cart-link fb-link-arrow" title="<?php esc_attr_e( 'بازگشت به سبد خرید جهت ویرایش اقلام', 'hello-elementor-child' ); ?>">
				<span><?php esc_html_e( 'بازگشت به سبد خرید', 'hello-elementor-child' ); ?></span>
				<svg class="fb-arrow-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
			</a>
		</div>

		<!-- Coupons -->
		<?php foreach ( $cart->get_coupons() as $code => $coupon ) : ?>
			<div class="fb-breakdown-row fb-breakdown-discount">
				<span class="fb-breakdown-label">
					<?php wc_cart_totals_coupon_label( $coupon ); ?>
					<a href="#" class="fb-remove-coupon fb-coupon-remove-link" data-coupon="<?php echo esc_attr( $code ); ?>" style="color: var(--fb-text-main); font-size: 11px; margin-right: 4px; text-decoration: none;">[حذف]</a>
				</span>
				<span class="fb-breakdown-val"><?php wc_cart_totals_coupon_html( $coupon ); ?></span>
			</div>
		<?php endforeach; ?>

		<!-- Shipping -->
		<?php if ( $cart->needs_shipping() && $cart->show_shipping() ) : ?>
			<?php do_action( 'woocommerce_review_order_before_shipping' ); ?>
			<div class="fb-breakdown-row fb-breakdown-shipping">
				<span class="fb-breakdown-label"><?php esc_html_e( 'روش ارسال', 'hello-elementor-child' ); ?></span>
				<span class="fb-breakdown-val"><?php wc_cart_totals_shipping_html(); ?></span>
			</div>
			<?php do_action( 'woocommerce_review_order_after_shipping' ); ?>
		<?php endif; ?>

		<!-- Fees -->
		<?php foreach ( $cart->get_fees() as $fee ) : ?>
			<div class="fb-breakdown-row">
				<span class="fb-breakdown-label"><?php echo esc_html( $fee->name ); ?></span>
				<span class="fb-breakdown-val"><?php wc_cart_totals_fee_html( $fee ); ?></span>
			</div>
		<?php endforeach; ?>

		<!-- Taxes -->
		<?php if ( wc_tax_enabled() && ! $cart->display_prices_including_tax() ) : ?>
			<div class="fb-breakdown-row">
				<span class="fb-breakdown-label"><?php echo esc_html( WC()->countries->tax_or_vat() ); ?></span>
				<span class="fb-breakdown-val"><?php wc_cart_totals_taxes_total_html(); ?></span>
			</div>
		<?php endif; ?>

		<?php
		// Calculate total savings from product regular vs sale prices
		$total_savings = 0;
		if ( ! empty( $cart_items ) ) {
			foreach ( $cart_items as $cart_item ) {
				$_product = isset( $cart_item['data'] ) ? $cart_item['data'] : null;
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
		}
		?>

		<!-- Order Total (Visible below shipping and breakdown) -->
		<?php do_action( 'woocommerce_review_order_before_order_total' ); ?>
		<div class="fb-breakdown-row fb-breakdown-total order-total">
			<span class="fb-breakdown-label"><?php esc_html_e( 'مبلغ قابل پرداخت', 'hello-elementor-child' ); ?></span>
			<span class="fb-breakdown-val fb-order-total-value fb-order-total-value-main">
				<?php
				$discount_sum = (float) $total_savings + (float) $cart->get_discount_total();
				if ( $discount_sum > 0 ) {
					$total_before_discount = (float) $cart->total + $discount_sum;
					?>
					<div class="fb-cart-total-wrap fb-checkout-total-wrap">
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
		<?php do_action( 'woocommerce_review_order_after_order_total' ); ?>
	</div>
</div>
