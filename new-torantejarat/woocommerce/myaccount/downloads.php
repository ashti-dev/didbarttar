<?php
/**
 * Downloads
 * Overridden for FrontBaz Modern Store Account
 *
 * @version 7.8.0
 */

defined( 'ABSPATH' ) || exit;

$downloads     = WC()->customer ? WC()->customer->get_downloadable_products() : [];
$has_downloads = (bool) $downloads;

do_action( 'woocommerce_before_account_downloads', $has_downloads ); ?>

<div class="fb-orders-page-wrap fb-downloads-page-wrap">

	<div class="fb-content-card-header">
		<div class="fb-header-icon-title">
			<span class="fb-card-icon">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
					<polyline points="7 10 12 15 17 10"></polyline>
					<line x1="12" y1="15" x2="12" y2="3"></line>
				</svg>
			</span>
			<div>
				<h3 class="fb-card-title"><?php esc_html_e( 'فایل‌های دانلودی من', 'torantejarat' ); ?></h3>
				<p class="fb-card-desc"><?php esc_html_e( 'لیست کلیه فایل‌های دانلودی سفارش داده شده به همراه وضعیت دسترسی و لینک دانلود مستقیم.', 'torantejarat' ); ?></p>
			</div>
		</div>
	</div>

	<?php if ( $has_downloads ) : ?>

		<div class="fb-orders-cards-container fb-downloads-cards-container">
			<?php foreach ( $downloads as $download ) : 
				$product   = ! empty( $download['product_id'] ) ? wc_get_product( $download['product_id'] ) : null;
				$remaining = is_numeric( $download['downloads_remaining'] ) ? sprintf( esc_html__( '%s دانلود باقی‌مانده', 'torantejarat' ), esc_html( $download['downloads_remaining'] ) ) : esc_html__( 'دانلود نامحدود', 'torantejarat' );
				$expires   = ! empty( $download['access_expires'] ) ? date_i18n( get_option( 'date_format' ), strtotime( $download['access_expires'] ) ) : esc_html__( 'همیشگی (بدون انقضا)', 'torantejarat' );
			?>

				<div class="fb-order-full-card fb-download-card">
					<div class="fb-order-full-top">
						<div class="fb-order-id-meta">
							<span class="fb-order-badge-num">
								<?php echo esc_html( $download['download_name'] ); ?>
							</span>
							<?php if ( ! empty( $download['order_id'] ) ) : ?>
								<span class="fb-order-date-text">
									<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
										<line x1="3" y1="6" x2="21" y2="6"></line>
										<path d="M16 10a4 4 0 0 1-8 0"></path>
									</svg>
									<?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( $download['order_id'] ) ); ?>
								</span>
							<?php endif; ?>
						</div>
						<div class="fb-order-status fb-status-completed">
							<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
							<span><?php echo esc_html( $remaining ); ?></span>
						</div>
					</div>

					<div class="fb-order-items-preview">
						<div class="fb-order-preview-row">
							<div class="fb-preview-product-col">
								<div class="fb-preview-thumb">
									<?php if ( $product && $product->get_image_id() ) : ?>
										<?php echo wp_kses_post( $product->get_image( 'thumbnail' ) ); ?>
									<?php else : ?>
										<div class="fb-preview-thumb-placeholder">
											<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path><polyline points="7 10 12 15 17 10"></polyline><line x1="12" y1="15" x2="12" y2="3"></line></svg>
										</div>
									<?php endif; ?>
								</div>
								<div class="fb-preview-meta">
									<?php if ( ! empty( $download['product_url'] ) ) : ?>
										<a href="<?php echo esc_url( $download['product_url'] ); ?>" class="fb-preview-name">
											<?php echo esc_html( $download['product_name'] ); ?>
										</a>
									<?php else : ?>
										<span class="fb-preview-name"><?php echo esc_html( $download['product_name'] ); ?></span>
									<?php endif; ?>
									<span class="fb-download-file-title" style="font-size: 12px; color: var(--fb-text-muted); font-weight: 500;">
										<?php echo esc_html( $download['download_name'] ); ?>
									</span>
								</div>
							</div>
						</div>
					</div>

					<div class="fb-order-full-bottom">
						<div class="fb-order-total-summary">
							<span class="fb-meta-label"><?php esc_html_e( 'مهلت دسترسی:', 'torantejarat' ); ?></span>
							<span class="fb-meta-total" style="font-size: 13px; font-weight: 600;"><?php echo esc_html( $expires ); ?></span>
						</div>

						<div class="fb-order-actions-wrap">
							<a href="<?php echo esc_url( $download['download_url'] ); ?>" class="fb-btn-primary fb-btn-sm" download>
								<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
									<polyline points="7 10 12 15 17 10"></polyline>
									<line x1="12" y1="15" x2="12" y2="3"></line>
								</svg>
								<span><?php esc_html_e( 'دانلود فایل', 'torantejarat' ); ?></span>
							</a>
						</div>
					</div>
				</div>

			<?php endforeach; ?>
		</div>

	<?php else : ?>

		<div class="fb-empty-state">
			<div class="fb-empty-icon">
				<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
					<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
					<polyline points="7 10 12 15 17 10"></polyline>
					<line x1="12" y1="15" x2="12" y2="3"></line>
				</svg>
			</div>
			<h4 class="fb-empty-title"><?php esc_html_e( 'هیچ فایل دانلودی یافت نشد', 'torantejarat' ); ?></h4>
			<p class="fb-empty-desc"><?php esc_html_e( 'شما تاکنون محصول دانلودی خریداری نکرده‌اید. با خرید محصولات دانلودی، فایل‌های شما در این بخش برای دریافت مستقیم قرار می‌گیرند.', 'torantejarat' ); ?></p>
			<a href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>" class="fb-btn-primary fb-btn-sm">
				<?php esc_html_e( 'رفتن به فروشگاه', 'torantejarat' ); ?>
			</a>
		</div>

	<?php endif; ?>

</div>

<?php do_action( 'woocommerce_after_account_downloads', $has_downloads ); ?>
