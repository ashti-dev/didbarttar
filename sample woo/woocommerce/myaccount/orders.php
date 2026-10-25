<?php
/**
 * Orders
 * Overridden for FrontBaz Modern Store Account
 *
 * @version 9.5.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders ); ?>

<div class="fb-orders-page-wrap">

	<div class="fb-content-card-header">
		<div class="fb-header-icon-title">
			<span class="fb-card-icon">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
					<line x1="3" y1="6" x2="21" y2="6"></line>
					<path d="M16 10a4 4 0 0 1-8 0"></path>
				</svg>
			</span>
			<div>
				<h3 class="fb-card-title"><?php esc_html_e( 'سفارش‌های من', 'hello-elementor-child' ); ?></h3>
				<p class="fb-card-desc"><?php esc_html_e( 'لیست کلیه سفارش‌های ثبت شده به همراه وضعیت، مبالغ و فاکتور خرید.', 'hello-elementor-child' ); ?></p>
			</div>
		</div>
	</div>

	<?php if ( $has_orders ) : ?>

		<div class="fb-orders-cards-container">
			<?php
			foreach ( $customer_orders->orders as $customer_order ) :
				$order      = wc_get_order( $customer_order );
				if ( ! $order ) {
					continue;
				}
				$item_count = $order->get_item_count() - $order->get_item_count_refunded();
				$status     = $order->get_status();
				$status_name = wc_get_order_status_name( $status );
				$order_date = wc_format_datetime( $order->get_date_created() );
			?>

				<div class="fb-order-full-card">
					<div class="fb-order-full-top">
						<div class="fb-order-id-meta">
							<span class="fb-order-badge-num">
								<?php printf( esc_html__( 'سفارش #%s', 'hello-elementor-child' ), esc_html( $order->get_order_number() ) ); ?>
							</span>
							<span class="fb-order-date-text">
								<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
									<line x1="16" y1="2" x2="16" y2="6"></line>
									<line x1="8" y1="2" x2="8" y2="6"></line>
									<line x1="3" y1="10" x2="21" y2="10"></line>
								</svg>
								<?php echo esc_html( $order_date ); ?>
							</span>
						</div>
						<div class="fb-order-status fb-status-<?php echo esc_attr( $status ); ?>">
							<?php echo esc_html( $status_name ); ?>
						</div>
					</div>

					<?php 
					$order_items = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
					if ( ! empty( $order_items ) ) : ?>
						<div class="fb-order-items-preview">
							<?php foreach ( $order_items as $item_id => $item ) : 
								$product = $item->get_product();
								$qty = $item->get_quantity();
								$item_subtotal = $order->get_formatted_line_subtotal( $item );
							?>
								<div class="fb-order-preview-row">
									<div class="fb-preview-product-col">
										<div class="fb-preview-thumb">
											<?php if ( $product && $product->get_image_id() ) : ?>
												<?php echo wp_kses_post( $product->get_image( 'thumbnail' ) ); ?>
											<?php else : ?>
												<div class="fb-preview-thumb-placeholder">
													<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg>
												</div>
											<?php endif; ?>
										</div>
										<div class="fb-preview-meta">
											<?php if ( $product && $product->is_visible() ) : ?>
												<a href="<?php echo esc_url( $product->get_permalink() ); ?>" class="fb-preview-name">
													<?php echo esc_html( $item->get_name() ); ?>
												</a>
											<?php else : ?>
												<span class="fb-preview-name"><?php echo esc_html( $item->get_name() ); ?></span>
											<?php endif; ?>
											<?php wc_display_item_meta( $item ); ?>
										</div>
									</div>

									<div class="fb-preview-qty-col">
										<span class="fb-item-qty-badge"><?php printf( esc_html__( '%s عدد', 'hello-elementor-child' ), esc_html( $qty ) ); ?></span>
									</div>

									<div class="fb-preview-price-col">
										<span class="fb-preview-price"><?php echo wp_kses_post( $item_subtotal ); ?></span>
									</div>
								</div>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>

					<div class="fb-order-full-bottom">
						<div class="fb-order-total-summary">
							<span class="fb-meta-label"><?php esc_html_e( 'مبلغ کل سفارش:', 'hello-elementor-child' ); ?></span>
							<span class="fb-meta-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
						</div>

						<div class="fb-order-actions-wrap">
							<?php
							$actions = wc_get_account_orders_actions( $order );
							if ( ! empty( $actions ) ) {
								foreach ( $actions as $key => $action ) {
									$label = ( 'view' === $key ) ? __( 'مشاهده جزئیات سفارش', 'hello-elementor-child' ) : $action['name'];
									$btn_class = 'view' === $key ? 'fb-btn-primary fb-btn-sm' : 'fb-btn-outline fb-btn-sm';
									echo '<a href="' . esc_url( $action['url'] ) . '" class="' . esc_attr( $btn_class ) . ' ' . sanitize_html_class( $key ) . '">' . esc_html( $label ) . '</a>';
								}
							} else {
								echo '<a href="' . esc_url( $order->get_view_order_url() ) . '" class="fb-btn-primary fb-btn-sm">' . esc_html__( 'مشاهده جزئیات سفارش', 'hello-elementor-child' ) . '</a>';
							}
							?>
						</div>
					</div>
				</div>

			<?php endforeach; ?>
		</div>

		<?php do_action( 'woocommerce_before_customer_orders_pagination' ); ?>

		<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
			<div class="fb-pagination-wrap">
				<?php if ( 1 !== $current_page ) : ?>
					<a class="fb-pagination-btn fb-prev" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>">
						<?php esc_html_e( 'صفحه قبلی', 'hello-elementor-child' ); ?>
					</a>
				<?php endif; ?>

				<span class="fb-pagination-current">
					<?php printf( esc_html__( 'صفحه %1$s از %2$s', 'hello-elementor-child' ), esc_html( $current_page ), esc_html( $customer_orders->max_num_pages ) ); ?>
				</span>

				<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
					<a class="fb-pagination-btn fb-next" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>">
						<?php esc_html_e( 'صفحه بعدی', 'hello-elementor-child' ); ?>
					</a>
				<?php endif; ?>
			</div>
		<?php endif; ?>

	<?php else : ?>

		<div class="fb-empty-state">
			<div class="fb-empty-icon">
				<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
					<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
					<line x1="3" y1="6" x2="21" y2="6"></line>
					<path d="M16 10a4 4 0 0 1-8 0"></path>
				</svg>
			</div>
			<h4 class="fb-empty-title"><?php esc_html_e( 'هیچ سفارشی یافت نشد', 'hello-elementor-child' ); ?></h4>
			<p class="fb-empty-desc"><?php esc_html_e( 'شما تاکنون سفارشی ثبت نکرده‌اید. با مراجعه به بخش فروشگاه می‌توانید کالاهای مورد نظر خود را سفارش دهید.', 'hello-elementor-child' ); ?></p>
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="fb-btn-primary fb-btn-sm">
				<?php esc_html_e( 'رفتن به فروشگاه', 'hello-elementor-child' ); ?>
			</a>
		</div>

	<?php endif; ?>

</div>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
