<?php
/**
 * View Order - FrontBaz Modern Invoice Style
 * Shows the details of a particular order on the account page styled as a prestigious digital invoice.
 *
 * @package ToranTejarat
 * @version 10.6.0
 */

defined( 'ABSPATH' ) || exit;

if ( ! $order ) {
	return;
}

$order_id      = $order->get_id();
$order_number  = $order->get_order_number();
$status        = $order->get_status();
$status_name   = wc_get_order_status_name( $status );
$order_date    = wc_format_datetime( $order->get_date_created() );
$payment_title = $order->get_payment_method_title();
$payment_method = $order->get_payment_method();

// Dynamic gateway title lookup if method exists
if ( empty( $payment_title ) && ! empty( $payment_method ) ) {
	$gateways = WC()->payment_gateways ? WC()->payment_gateways->payment_gateways() : array();
	if ( isset( $gateways[ $payment_method ] ) ) {
		$payment_title = $gateways[ $payment_method ]->get_title();
	}
}

if ( empty( $payment_title ) ) {
	$payment_title = ( (float) $order->get_total() == 0 ) ? __( 'رایگان', 'torantejarat' ) : __( 'ثبت نشده', 'torantejarat' );
}

$notes = array();
if ( function_exists( 'wc_get_order_notes' ) ) {
	$notes = wc_get_order_notes( array(
		'order_id' => $order->get_id(),
		'type'     => 'customer',
	) );
}
if ( empty( $notes ) ) {
	$notes = $order->get_customer_order_notes();
}
$items         = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$totals        = $order->get_order_item_totals();

$billing_address  = $order->get_formatted_billing_address();
$shipping_address = $order->get_formatted_shipping_address();
$needs_shipping   = ! wc_ship_to_billing_address_only() && $order->needs_shipping_address();
?>

<div class="fb-view-order-wrap">

	<!-- Top Bar: Navigation & Print Action -->
	<div class="fb-view-order-topbar">
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="fb-btn-link fb-link-arrow">
			<span><?php esc_html_e( 'بازگشت به سفارش‌ها', 'torantejarat' ); ?></span>
			<svg class="fb-arrow-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
		</a>
		<button type="button" class="fb-btn-outline fb-btn-print" onclick="window.print();">
			<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
			<span><?php esc_html_e( 'چاپ فاکتور', 'torantejarat' ); ?></span>
		</button>
	</div>

	<!-- Main Invoice Card -->
	<div class="fb-invoice-card">

		<!-- Invoice Hero Header -->
		<div class="fb-invoice-header">
			<div class="fb-invoice-header-info">
				<div class="fb-invoice-title-row">
					<h2 class="fb-invoice-title">
						<?php printf( esc_html__( 'فاکتور سفارش #%s', 'torantejarat' ), esc_html( $order_number ) ); ?>
					</h2>
					<span class="fb-order-status fb-status-<?php echo esc_attr( $status ); ?>">
						<?php echo esc_html( $status_name ); ?>
					</span>
				</div>
			</div>
		</div>

		<!-- 4-Grid Key Meta Highlights -->
		<div class="fb-invoice-meta-grid">
			<div class="fb-meta-item">
				<span class="fb-meta-label"><?php esc_html_e( 'شماره سفارش', 'torantejarat' ); ?></span>
				<span class="fb-meta-val fb-order-num">#<?php echo esc_html( $order_number ); ?></span>
			</div>
			<div class="fb-meta-item">
				<span class="fb-meta-label"><?php esc_html_e( 'تاریخ و زمان ثبت', 'torantejarat' ); ?></span>
				<span class="fb-meta-val"><?php echo esc_html( $order_date ); ?></span>
			</div>
			<div class="fb-meta-item">
				<span class="fb-meta-label"><?php esc_html_e( 'شیوه پرداخت', 'torantejarat' ); ?></span>
				<span class="fb-meta-val"><?php echo esc_html( $payment_title ); ?></span>
			</div>
			<div class="fb-meta-item">
				<span class="fb-meta-label"><?php esc_html_e( 'مبلغ کل سفارش', 'torantejarat' ); ?></span>
				<span class="fb-meta-val fb-total-num"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
			</div>
		</div>

		<?php if ( ! empty( $notes ) ) : ?>
			<!-- Order Updates Timeline (Postal Tracking & Notes) -->
			<div class="fb-invoice-updates-section">
				<h5 class="fb-updates-title">
					<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
					<span><?php esc_html_e( 'پیام‌ها، وضعیت و کد رهگیری پستی مرسوله', 'torantejarat' ); ?></span>
				</h5>
				<ul class="fb-updates-list">
					<?php foreach ( $notes as $note ) : 
						$content = '';
						if ( ! empty( $note->content ) ) {
							$content = $note->content;
						} elseif ( ! empty( $note->comment_content ) ) {
							$content = $note->comment_content;
						}

						$date_timestamp = 0;
						if ( ! empty( $note->date_created ) && is_a( $note->date_created, 'WC_DateTime' ) ) {
							$date_timestamp = $note->date_created->getTimestamp();
						} elseif ( ! empty( $note->comment_date ) ) {
							$date_timestamp = strtotime( $note->comment_date );
						} elseif ( ! empty( $note->date_created ) ) {
							$date_timestamp = strtotime( (string) $note->date_created );
						}
					?>
						<li class="fb-update-item">
							<?php if ( $date_timestamp ) : ?>
								<span class="fb-update-date"><?php echo esc_html( date_i18n( esc_html__( 'l j F Y - H:i', 'torantejarat' ), $date_timestamp ) ); ?></span>
							<?php endif; ?>
							<div class="fb-update-content"><?php echo wp_kses_post( wpautop( wptexturize( $content ) ) ); ?></div>
						</li>
					<?php endforeach; ?>
				</ul>
			</div>
		<?php endif; ?>

		<!-- Purchased Items Table -->
		<div class="fb-invoice-table-wrap">
			<h4 class="fb-invoice-section-title">
				<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 0 1-8 0"></path></svg>
				<?php esc_html_e( 'اقلام و محصولات سفارش', 'torantejarat' ); ?>
			</h4>
			<table class="fb-invoice-table">
				<thead>
					<tr>
						<th class="fb-th-product"><?php esc_html_e( 'محصول', 'torantejarat' ); ?></th>
						<th class="fb-th-price"><?php esc_html_e( 'قیمت واحد', 'torantejarat' ); ?></th>
						<th class="fb-th-qty"><?php esc_html_e( 'تعداد', 'torantejarat' ); ?></th>
						<th class="fb-th-total"><?php esc_html_e( 'مجموع', 'torantejarat' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $items as $item_id => $item ) :
						$product = $item->get_product();
						$qty = $item->get_quantity();
					?>
						<tr class="fb-invoice-row">
							<td class="fb-td-product">
								<div class="fb-product-cell">
									<?php if ( $product && $product->get_image_id() ) : ?>
										<div class="fb-product-thumb">
											<?php echo wp_kses_post( $product->get_image( 'thumbnail' ) ); ?>
										</div>
									<?php endif; ?>
									<div class="fb-product-info">
										<?php if ( $product && $product->is_visible() ) : ?>
											<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="fb-product-link">
												<?php echo esc_html( $item->get_name() ); ?>
											</a>
										<?php else : ?>
											<span class="fb-product-name"><?php echo esc_html( $item->get_name() ); ?></span>
										<?php endif; ?>
										<?php
										// Item meta (attributes, etc.)
										wc_display_item_meta( $item );
										?>
									</div>
								</div>
							</td>
							<td class="fb-td-price">
								<span class="fb-item-unit-price">
									<?php echo wp_kses_post( wc_price( $order->get_item_subtotal( $item, false, true ) ) ); ?>
								</span>
							</td>
							<td class="fb-td-qty">
								<span class="fb-item-qty-badge"><?php printf( esc_html__( '%s عدد', 'torantejarat' ), esc_html( $qty ) ); ?></span>
							</td>
							<td class="fb-td-total">
								<span class="fb-item-line-total">
									<?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?>
								</span>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<!-- Financial Breakdown Totals (خلاصه مالی فاکتور) -->
		<div class="fb-invoice-totals-section">
			<div class="fb-invoice-totals-box">
				<?php foreach ( $totals as $key => $total ) : 
					$is_total_row = ( 'order_total' === $key );
				?>
					<div class="fb-totals-row <?php echo esc_attr( $is_total_row ? 'fb-totals-row-final' : '' ); ?> fb-total-<?php echo esc_attr( $key ); ?>">
						<span class="fb-total-label"><?php echo esc_html( $total['label'] ); ?></span>
						<span class="fb-total-val"><?php echo wp_kses_post( $total['value'] ); ?></span>
					</div>
				<?php endforeach; ?>
			</div>
		</div>

		<!-- Customer & Delivery Addresses -->
		<div class="fb-invoice-addresses-section">
			<h4 class="fb-invoice-section-title">
				<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
				<?php esc_html_e( 'اطلاعات تحویل و صورتحساب', 'torantejarat' ); ?>
			</h4>
			<div class="fb-addresses-grid <?php echo esc_attr( ( $needs_shipping && ! empty( $shipping_address ) ) ? 'has-two-cols' : 'has-one-col' ); ?>">
				<!-- Billing Card -->
				<div class="fb-address-box">
					<div class="fb-address-box-header">
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
						<h5><?php esc_html_e( 'مشخصات و آدرس صورتحساب', 'torantejarat' ); ?></h5>
					</div>
					<div class="fb-address-box-body">
						<?php if ( ! empty( $billing_address ) && trim( wp_strip_all_tags( $billing_address ) ) !== '' ) : ?>
							<p class="fb-address-text"><?php echo wp_kses_post( $billing_address ); ?></p>
						<?php else : ?>
							<p class="fb-address-text fb-text-muted"><?php esc_html_e( 'آدرس متنی ثبت نشده است.', 'torantejarat' ); ?></p>
						<?php endif; ?>

						<div class="fb-address-contacts">
							<?php if ( $order->get_billing_phone() ) : ?>
								<div class="fb-contact-item">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
									<span><?php echo esc_html( $order->get_billing_phone() ); ?></span>
								</div>
							<?php endif; ?>
							<?php if ( $order->get_billing_email() ) : ?>
								<div class="fb-contact-item">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
									<span class="fb-contact-email"><?php echo esc_html( $order->get_billing_email() ); ?></span>
								</div>
							<?php endif; ?>
						</div>
					</div>
				</div>

				<?php if ( $needs_shipping && ! empty( $shipping_address ) ) : ?>
					<!-- Shipping Card -->
					<div class="fb-address-box">
						<div class="fb-address-box-header">
							<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
							<h5><?php esc_html_e( 'نشانی تحویل سفارش (گیرنده)', 'torantejarat' ); ?></h5>
						</div>
						<div class="fb-address-box-body">
							<p class="fb-address-text"><?php echo wp_kses_post( $shipping_address ); ?></p>
							<?php if ( $order->get_shipping_phone() ) : ?>
								<div class="fb-address-contacts">
									<div class="fb-contact-item">
										<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
										<span><?php echo esc_html( $order->get_shipping_phone() ); ?></span>
									</div>
								</div>
							<?php endif; ?>
						</div>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php if ( $order->get_customer_note() ) : ?>
			<!-- Customer Note -->
			<div class="fb-invoice-note-box">
				<h5 class="fb-note-title">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"></path></svg>
					<?php esc_html_e( 'یادداشت خریدار برای این سفارش:', 'torantejarat' ); ?>
				</h5>
				<p class="fb-note-content"><?php echo wp_kses_post( nl2br( $order->get_customer_note() ) ); ?></p>
			</div>
		<?php endif; ?>

	</div>

	<?php do_action( 'woocommerce_view_order', $order_id ); ?>

</div>
