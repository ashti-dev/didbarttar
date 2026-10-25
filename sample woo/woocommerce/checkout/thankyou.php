<?php
/**
 * Custom FrontBaz Thank You & Order Received Page Template
 * Prestigious Invoice Layout with Celebratory Purchase Experience
 *
 * @package HelloElementorChild
 * @version 9.4.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="fb-thankyou-wrapper woocommerce-order">

	<?php
	if ( ! $order ) {
		global $wp;
		$order_id = isset( $wp->query_vars['order-received'] ) ? absint( $wp->query_vars['order-received'] ) : 0;
		if ( ! $order_id && isset( $_GET['order_id'] ) ) {
			$order_id = absint( $_GET['order_id'] );
		}
		if ( $order_id ) {
			$potential_order = wc_get_order( $order_id );
			if ( $potential_order && ( current_user_can( 'manage_woocommerce' ) || $potential_order->get_customer_id() === get_current_user_id() ) ) {
				$order = $potential_order;
			}
		}
	}

	$store_name = get_bloginfo( 'name' );

	if ( $order ) :

		do_action( 'woocommerce_before_thankyou', $order->get_id() );
		?>

		<?php if ( $order->has_status( 'failed' ) ) : ?>

			<!-- Failed Order Notice -->
			<div class="fb-order-failed-card">
				<div class="fb-failed-icon-wrap">
					<svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<circle cx="12" cy="12" r="10"></circle>
						<line x1="15" y1="9" x2="9" y2="15"></line>
						<line x1="9" y1="9" x2="15" y2="15"></line>
					</svg>
				</div>
				<h2 class="fb-failed-title"><?php esc_html_e( 'متأسفانه پرداخت سفارش ناموفق بود', 'hello-elementor-child' ); ?></h2>
				<p class="fb-failed-desc"><?php esc_html_e( 'تراکنش از سوی بانک یا درگاه پرداخت تکمیل نگردید. می‌توانید مجدداً جهت پرداخت اقدام فرمایید یا روش پرداخت دیگری را انتخاب کنید.', 'hello-elementor-child' ); ?></p>
				<div class="fb-failed-actions">
					<a href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>" class="fb-checkout-submit-btn fb-failed-btn-retry">
						<span class="fb-btn-text"><?php esc_html_e( 'تلاش مجدد برای پرداخت', 'hello-elementor-child' ); ?></span>
					</a>
					<?php if ( is_user_logged_in() ) : ?>
						<a href="<?php echo esc_url( wc_get_page_permalink( 'myaccount' ) ); ?>" class="fb-btn-secondary">
							<span><?php esc_html_e( 'بازگشت به حساب کاربری', 'hello-elementor-child' ); ?></span>
						</a>
					<?php endif; ?>
				</div>
			</div>

		<?php else : ?>

			<!-- 1. Hero Celebration Banner -->
			<div class="fb-thankyou-hero">
				<div class="fb-hero-icon-wrap">
					<div class="fb-checkmark-circle-glow"></div>
					<svg class="fb-checkmark-svg" viewBox="0 0 52 52">
						<circle class="fb-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
						<path class="fb-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
					</svg>
				</div>

				<h1 class="fb-hero-title"><?php esc_html_e( 'سفارش شما با موفقیت ثبت شد!', 'hello-elementor-child' ); ?></h1>

				<p class="fb-hero-desc">
					<?php
					$customer_name = $order->get_billing_first_name() ? $order->get_billing_first_name() : $order->get_formatted_billing_full_name();
					if ( ! empty( $customer_name ) ) {
						printf(
							/* translators: 1: Store name, 2: Customer name */
							esc_html__( 'از خرید و اعتمادتان به %1$s صمیمانه سپاسگزاریم، %2$s عزیز. سفارش شما تایید گردیده و مراحل بسته‌بندی و ارسال آن در حال انجام است.', 'hello-elementor-child' ),
							esc_html( $store_name ),
							esc_html( $customer_name )
						);
					} else {
						printf(
							/* translators: %s: Store name */
							esc_html__( 'از خرید و اعتمادتان به %s صمیمانه سپاسگزاریم. سفارش شما با موفقیت تایید گردیده و مراحل بسته‌بندی و ارسال آن در حال انجام است.', 'hello-elementor-child' ),
							esc_html( $store_name )
						);
					}
					?>
				</p>

				<!-- Order Fulfillment Stepper / Timeline -->
				<div class="fb-order-timeline">
					<div class="fb-step fb-step-completed">
						<div class="fb-step-dot">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
						</div>
						<span class="fb-step-label"><?php esc_html_e( 'ثبت موفق', 'hello-elementor-child' ); ?></span>
					</div>
					<div class="fb-step-line fb-line-active"></div>
					<div class="fb-step fb-step-active">
						<div class="fb-step-dot">۲</div>
						<span class="fb-step-label"><?php esc_html_e( 'آماده‌سازی محصول', 'hello-elementor-child' ); ?></span>
					</div>
					<div class="fb-step-line"></div>
					<div class="fb-step">
						<div class="fb-step-dot">۳</div>
						<span class="fb-step-label"><?php esc_html_e( 'ارسال مرسوله', 'hello-elementor-child' ); ?></span>
					</div>
					<div class="fb-step-line"></div>
					<div class="fb-step">
						<div class="fb-step-dot">۴</div>
						<span class="fb-step-label"><?php esc_html_e( 'تحویل به شما', 'hello-elementor-child' ); ?></span>
					</div>
				</div>
			</div>

			<!-- 2. Prestigious Digital Invoice Card -->
			<div class="fb-invoice-card" id="fb_printable_invoice">

				<!-- Invoice Top Header -->
				<div class="fb-invoice-header">
					<div class="fb-invoice-brand">
						<h2 class="fb-invoice-brand-title"><?php echo esc_html( $store_name ); ?></h2>
						<span class="fb-invoice-badge"><?php esc_html_e( 'رسید پرداخت الکترونیکی و فاکتور فروشگاهی', 'hello-elementor-child' ); ?></span>
					</div>
					<div class="fb-invoice-header-actions">
						<button type="button" class="fb-btn-print" onclick="window.print();" title="<?php esc_attr_e( 'چاپ فاکتور رسمی', 'hello-elementor-child' ); ?>">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
							<span><?php esc_html_e( 'چاپ فاکتور', 'hello-elementor-child' ); ?></span>
						</button>
					</div>
				</div>

				<!-- 4 Key Meta Data Cards -->
				<div class="fb-invoice-meta-grid">
					<div class="fb-meta-item">
						<span class="fb-meta-label"><?php esc_html_e( 'شماره سفارش:', 'hello-elementor-child' ); ?></span>
						<strong class="fb-meta-val fb-order-num">#<?php echo esc_html( $order->get_order_number() ); ?></strong>
					</div>
					<div class="fb-meta-item">
						<span class="fb-meta-label"><?php esc_html_e( 'تاریخ و زمان ثبت:', 'hello-elementor-child' ); ?></span>
						<strong class="fb-meta-val"><?php echo esc_html( wc_format_datetime( $order->get_date_created() ) ); ?></strong>
					</div>
					<div class="fb-meta-item">
						<span class="fb-meta-label"><?php esc_html_e( 'شیوه پرداخت:', 'hello-elementor-child' ); ?></span>
						<strong class="fb-meta-val"><?php echo wp_kses_post( $order->get_payment_method_title() ? $order->get_payment_method_title() : esc_html__( 'پرداخت آنلاین', 'hello-elementor-child' ) ); ?></strong>
					</div>
					<div class="fb-meta-item">
						<span class="fb-meta-label"><?php esc_html_e( 'وضعیت پرداخت:', 'hello-elementor-child' ); ?></span>
						<strong class="fb-meta-val fb-status-success">
							<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
							<?php echo esc_html( wc_get_order_status_name( $order->get_status() ) ); ?>
						</strong>
					</div>
				</div>

				<!-- Invoice Purchased Items Table -->
				<div class="fb-invoice-table-wrap">
					<table class="fb-invoice-table" cellspacing="0">
						<thead>
							<tr>
								<th class="fb-th-thumb"><?php esc_html_e( 'تصویر', 'hello-elementor-child' ); ?></th>
								<th class="fb-th-item"><?php esc_html_e( 'شرح کالا و مشخصات', 'hello-elementor-child' ); ?></th>
								<th class="fb-th-price"><?php esc_html_e( 'قیمت واحد', 'hello-elementor-child' ); ?></th>
								<th class="fb-th-qty"><?php esc_html_e( 'تعداد', 'hello-elementor-child' ); ?></th>
								<th class="fb-th-subtotal"><?php esc_html_e( 'مجموع', 'hello-elementor-child' ); ?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							foreach ( $order->get_items() as $item_id => $item ) {
								$product   = $item->get_product();
								$thumbnail = $product ? $product->get_image( 'woocommerce_thumbnail' ) : '';
								$permalink = $product && $product->is_visible() ? $product->get_permalink() : '';
								?>
								<tr class="fb-invoice-row">
									<td class="fb-td-thumb">
										<div class="fb-invoice-thumb">
											<?php if ( $permalink ) : ?>
												<a href="<?php echo esc_url( $permalink ); ?>"><?php echo $thumbnail; ?></a>
											<?php else : ?>
												<?php echo $thumbnail; ?>
											<?php endif; ?>
										</div>
									</td>
									<td class="fb-td-info">
										<h4 class="fb-invoice-item-name">
											<?php if ( $permalink ) : ?>
												<a href="<?php echo esc_url( $permalink ); ?>"><?php echo esc_html( $item->get_name() ); ?></a>
											<?php else : ?>
												<?php echo esc_html( $item->get_name() ); ?>
											<?php endif; ?>
										</h4>
										<?php wc_display_item_meta( $item ); ?>
									</td>
									<td class="fb-td-price" data-label="<?php esc_attr_e( 'قیمت واحد:', 'hello-elementor-child' ); ?>">
										<?php echo wc_price( $order->get_item_subtotal( $item, false, true ) ); ?>
									</td>
									<td class="fb-td-qty" data-label="<?php esc_attr_e( 'تعداد:', 'hello-elementor-child' ); ?>">
										<span class="fb-qty-badge"><?php echo esc_html( $item->get_quantity() ); ?></span>
									</td>
									<td class="fb-td-subtotal" data-label="<?php esc_attr_e( 'مجموع:', 'hello-elementor-child' ); ?>">
										<?php echo $order->get_formatted_line_subtotal( $item ); ?>
									</td>
								</tr>
								<?php
							}
							?>
						</tbody>
					</table>
				</div>

				<!-- Invoice Bottom Grid: Delivery & Financial Breakdown -->
				<div class="fb-invoice-bottom-grid">

					<!-- Customer & Shipping Delivery Info Card -->
					<div class="fb-invoice-customer-card">
						<h3 class="fb-card-title">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
							<span><?php esc_html_e( 'مشخصات و آدرس تحویل‌گیرنده', 'hello-elementor-child' ); ?></span>
						</h3>
						<div class="fb-customer-meta">
							<div class="fb-cm-row">
								<span class="fb-cm-label"><?php esc_html_e( 'نام و نام‌خانوادگی:', 'hello-elementor-child' ); ?></span>
								<span class="fb-cm-val"><?php echo esc_html( $order->get_formatted_billing_full_name() ); ?></span>
							</div>

							<?php if ( $order->get_billing_phone() ) : ?>
								<div class="fb-cm-row">
									<span class="fb-cm-label"><?php esc_html_e( 'شماره تماس:', 'hello-elementor-child' ); ?></span>
									<span class="fb-cm-val" dir="ltr"><?php echo esc_html( $order->get_billing_phone() ); ?></span>
								</div>
							<?php endif; ?>

							<div class="fb-cm-row">
								<span class="fb-cm-label"><?php esc_html_e( 'نشانی پستی:', 'hello-elementor-child' ); ?></span>
								<span class="fb-cm-val">
									<?php
									$address_str = $order->get_billing_address_1();
									if ( $order->get_billing_address_2() ) {
										$address_str .= ' - ' . $order->get_billing_address_2();
									}
									if ( $order->get_billing_city() ) {
										$address_str .= '، ' . $order->get_billing_city();
									}
									if ( $order->get_billing_state() ) {
										$country_code = $order->get_billing_country();
										$state_code   = $order->get_billing_state();
										$states       = WC()->countries->get_states( $country_code );
										$state_name   = ( is_array( $states ) && isset( $states[ $state_code ] ) ) ? $states[ $state_code ] : $state_code;
										if ( ! empty( $state_name ) ) {
											$address_str = $state_name . '، ' . $address_str;
										}
									}
									echo esc_html( $address_str );
									?>
								</span>
							</div>

							<?php if ( $order->get_billing_postcode() ) : ?>
								<div class="fb-cm-row">
									<span class="fb-cm-label"><?php esc_html_e( 'کد پستی:', 'hello-elementor-child' ); ?></span>
									<span class="fb-cm-val" dir="ltr"><?php echo esc_html( $order->get_billing_postcode() ); ?></span>
								</div>
							<?php endif; ?>

							<?php if ( $order->get_customer_note() ) : ?>
								<div class="fb-cm-row fb-cm-note">
									<span class="fb-cm-label"><?php esc_html_e( 'توضیحات خریدار:', 'hello-elementor-child' ); ?></span>
									<span class="fb-cm-val"><?php echo esc_html( $order->get_customer_note() ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>

					<!-- Financial Calculations Card -->
					<div class="fb-invoice-totals-card">
						<h3 class="fb-card-title">
							<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"></rect><line x1="6" y1="12" x2="18" y2="12"></line></svg>
							<span><?php esc_html_e( 'خلاصه محاسبات مالی فاکتور', 'hello-elementor-child' ); ?></span>
						</h3>
						<div class="fb-totals-rows">
							<?php
							foreach ( $order->get_order_item_totals() as $key => $total ) :
								$is_grand_total = ( 'order_total' === $key );
								$is_discount    = ( 'discount' === $key );
								?>
								<div class="fb-tot-row <?php echo $is_grand_total ? 'fb-tot-grand' : ''; ?> <?php echo $is_discount ? 'fb-tot-discount' : ''; ?>">
									<span class="fb-tot-label"><?php echo esc_html( $total['label'] ); ?></span>
									<span class="fb-tot-val"><?php echo wp_kses_post( $total['value'] ); ?></span>
								</div>
							<?php endforeach; ?>
						</div>
					</div>

				</div>

				<!-- Official Invoice Stamp / Guarantee Note -->
				<div class="fb-invoice-footer-note">
					<div class="fb-footer-note-text">
						<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path></svg>
						<span>
							<?php
							printf(
								/* translators: %s: Store name */
								esc_html__( 'تمامی اقلام این فاکتور دارای ضمانت اصالت و سلامت فیزیکی طبق قوانین %s می‌باشند.', 'hello-elementor-child' ),
								esc_html( $store_name )
							);
							?>
						</span>
					</div>
					<?php
					$is_paid = $order->is_paid() && 'cod' !== $order->get_payment_method() && ! $order->has_status( array( 'on-hold', 'pending', 'failed', 'cancelled' ) );
					if ( $is_paid ) :
					?>
						<div class="fb-footer-stamp">
							<span class="fb-stamp-badge is-paid"><?php esc_html_e( '✓ پرداخت شده و معتبر', 'hello-elementor-child' ); ?></span>
						</div>
					<?php endif; ?>
				</div>

				<?php
				// Capture payment gateway instructions (e.g. COD / Bank Transfer / Gateway notices)
				ob_start();
				do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
				$payment_method_instructions = trim( ob_get_clean() );
				if ( ! empty( $payment_method_instructions ) ) :
				?>
					<div class="fb-invoice-instructions-wrap">
						<div class="fb-invoice-instructions-box">
							<div class="fb-instructions-icon">
								<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
							</div>
							<div class="fb-instructions-body">
								<h4 class="fb-instructions-title"><?php esc_html_e( 'راهنمای پرداخت و تحویل سفارش:', 'hello-elementor-child' ); ?></h4>
								<div class="fb-instructions-content">
									<?php echo $payment_method_instructions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								</div>
							</div>
						</div>
					</div>
				<?php endif; ?>

			</div>

			<?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?>

			<!-- 3. Bottom Action Buttons Bar -->
			<div class="fb-thankyou-actions">
				<a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="fb-checkout-submit-btn fb-thankyou-btn-shop">
					<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
					<span class="fb-btn-text"><?php esc_html_e( 'بازگشت به فروشگاه و ادامه خرید', 'hello-elementor-child' ); ?></span>
				</a>

				<?php if ( is_user_logged_in() ) : ?>
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="fb-btn-secondary">
						<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect></svg>
						<span><?php esc_html_e( 'مشاهده سفارش در پنل کاربری', 'hello-elementor-child' ); ?></span>
					</a>
				<?php endif; ?>
			</div>

		<?php endif; ?>

	<?php else : ?>

		<div class="fb-thankyou-hero">
			<div class="fb-hero-icon-wrap">
				<svg class="fb-checkmark-svg" viewBox="0 0 52 52">
					<circle class="fb-checkmark-circle" cx="26" cy="26" r="25" fill="none"/>
					<path class="fb-checkmark-check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
				</svg>
			</div>
			<h1 class="fb-hero-title"><?php esc_html_e( 'از خرید شما صمیمانه متشکریم!', 'hello-elementor-child' ); ?></h1>
			<p class="fb-hero-desc"><?php esc_html_e( 'سفارش شما با موفقیت در سیستم ثبت گردید.', 'hello-elementor-child' ); ?></p>
			<div class="fb-thankyou-actions">
				<a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="fb-checkout-submit-btn fb-thankyou-btn-shop">
					<span class="fb-btn-text"><?php esc_html_e( 'مشاهده فروشگاه', 'hello-elementor-child' ); ?></span>
				</a>
			</div>
		</div>

	<?php endif; ?>

</div>
