<?php
/**
 * Payment methods
 * Overridden for FrontBaz Modern Store Account
 *
 * @package ToranTejarat
 * @version 8.9.0
 */

defined( 'ABSPATH' ) || exit;

$saved_methods = wc_get_customer_saved_methods_list( get_current_user_id() );
$has_methods   = (bool) $saved_methods;
$types         = wc_get_account_payment_methods_types();

do_action( 'woocommerce_before_account_payment_methods', $has_methods );
?>

<div class="fb-payment-methods-page-wrap">

	<div class="fb-content-card-header">
		<div class="fb-header-icon-title">
			<span class="fb-card-icon">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="2" y="4" width="20" height="16" rx="2"></rect>
					<line x1="2" y1="10" x2="22" y2="10"></line>
					<line x1="6" y1="16" x2="10" y2="16"></line>
				</svg>
			</span>
			<div>
				<h3 class="fb-card-title"><?php esc_html_e( 'روش‌های پرداخت من', 'torantejarat' ); ?></h3>
				<p class="fb-card-desc"><?php esc_html_e( 'مدیریت کارت‌های اعتباری و روش‌های پرداخت ذخیره شده جهت تسویه حساب سریع‌تر.', 'torantejarat' ); ?></p>
			</div>
		</div>

		<?php if ( WC()->payment_gateways->get_available_payment_gateways() ) : ?>
			<a class="fb-btn-primary fb-btn-sm fb-add-payment-method-btn" href="<?php echo esc_url( wc_get_endpoint_url( 'add-payment-method' ) ); ?>">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
				<span><?php esc_html_e( 'افزودن روش پرداخت جدید', 'torantejarat' ); ?></span>
			</a>
		<?php endif; ?>
	</div>

	<?php if ( $has_methods ) : ?>

		<div class="fb-saved-cards-grid">
			<?php
			foreach ( $saved_methods as $type => $methods ) :
				foreach ( $methods as $method ) :
					$is_default  = ! empty( $method['is_default'] );
					$brand_slug  = ! empty( $method['method']['brand'] ) ? strtolower( sanitize_title( $method['method']['brand'] ) ) : 'generic';
					$brand_label = ! empty( $method['method']['brand'] ) ? wc_get_credit_card_type_label( $method['method']['brand'] ) : esc_html__( 'کارت اعتباری', 'torantejarat' );
					$last4       = ! empty( $method['method']['last4'] ) ? $method['method']['last4'] : '';
					$expires     = ! empty( $method['expires'] ) ? $method['expires'] : '';
					$actions     = ! empty( $method['actions'] ) ? $method['actions'] : [];
					?>

					<div class="fb-saved-card-item <?php echo $is_default ? 'is-default' : ''; ?> fb-card-brand-<?php echo esc_attr( $brand_slug ); ?>">
						<div class="fb-saved-card-inner">
							<div class="fb-card-chip-row">
								<div class="fb-card-chip">
									<svg width="28" height="22" viewBox="0 0 28 22" fill="none">
										<rect width="28" height="22" rx="4" fill="currentColor" fill-opacity="0.18"/>
										<rect x="2" y="2" width="24" height="18" rx="2" stroke="currentColor" stroke-opacity="0.35" stroke-width="1.2"/>
										<line x1="2" y1="7" x2="26" y2="7" stroke="currentColor" stroke-opacity="0.25"/>
										<line x1="2" y1="15" x2="26" y2="15" stroke="currentColor" stroke-opacity="0.25"/>
										<line x1="10" y1="2" x2="10" y2="20" stroke="currentColor" stroke-opacity="0.25"/>
										<line x1="18" y1="2" x2="18" y2="20" stroke="currentColor" stroke-opacity="0.25"/>
									</svg>
								</div>
								<div class="fb-card-brand-wrap">
									<?php if ( $is_default ) : ?>
										<span class="fb-default-badge"><?php esc_html_e( 'پیش‌فرض', 'torantejarat' ); ?></span>
									<?php endif; ?>
									<span class="fb-brand-name"><?php echo esc_html( $brand_label ); ?></span>
								</div>
							</div>

							<div class="fb-card-number-row" dir="ltr">
								<span class="fb-card-dots">•••• •••• ••••</span>
								<span class="fb-card-last4"><?php echo esc_html( $last4 ); ?></span>
							</div>

							<div class="fb-card-footer-row">
								<div class="fb-card-expiry-wrap">
									<span class="fb-expiry-label"><?php esc_html_e( 'انقضا:', 'torantejarat' ); ?></span>
									<span class="fb-expiry-val" dir="ltr"><?php echo esc_html( $expires ? $expires : '—' ); ?></span>
								</div>

								<?php if ( ! empty( $actions ) ) : ?>
									<div class="fb-card-actions">
										<?php
										foreach ( $actions as $key => $action ) :
											$is_del    = 'delete' === $key;
											$btn_class = $is_del ? 'fb-btn-card-del' : 'fb-btn-card-default';
											?>
											<a href="<?php echo esc_url( $action['url'] ); ?>" class="<?php echo esc_attr( $btn_class ); ?>" title="<?php echo esc_attr( $action['name'] ); ?>">
												<?php if ( $is_del ) : ?>
													<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg>
												<?php else : ?>
													<span><?php echo esc_html( $action['name'] ); ?></span>
												<?php endif; ?>
											</a>
										<?php endforeach; ?>
									</div>
								<?php endif; ?>
							</div>
						</div>
					</div>

				<?php endforeach; ?>
			<?php endforeach; ?>
		</div>

	<?php else : ?>

		<div class="fb-empty-state fb-payment-methods-empty">
			<div class="fb-empty-icon">
				<svg width="44" height="44" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
					<rect x="2" y="4" width="20" height="16" rx="2"></rect>
					<line x1="2" y1="10" x2="22" y2="10"></line>
					<line x1="6" y1="16" x2="10" y2="16"></line>
				</svg>
			</div>
			<h4 class="fb-empty-title"><?php esc_html_e( 'روش پرداخت ذخیره شده‌ای یافت نشد', 'torantejarat' ); ?></h4>
			<p class="fb-empty-desc"><?php esc_html_e( 'شما تاکنون هیچ کارت اعتباری ذخیره نکرده‌اید. با افزودن کارت بانکی می‌توانید سفارش‌های خود را سریع‌تر و بدون نیاز به ورود مجدد اطلاعات ثبت فرمایید.', 'torantejarat' ); ?></p>
			<?php if ( WC()->payment_gateways->get_available_payment_gateways() ) : ?>
				<a class="fb-btn-primary fb-btn-sm" href="<?php echo esc_url( wc_get_endpoint_url( 'add-payment-method' ) ); ?>">
					<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"></line><line x1="5" y1="12" x2="19" y2="12"></line></svg>
					<span><?php esc_html_e( 'افزودن روش پرداخت جدید', 'torantejarat' ); ?></span>
				</a>
			<?php endif; ?>
		</div>

	<?php endif; ?>

</div>

<?php do_action( 'woocommerce_after_account_payment_methods', $has_methods ); ?>
