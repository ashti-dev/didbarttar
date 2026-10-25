<?php
/**
 * Add payment method form
 * Overridden for FrontBaz Modern Store Account
 *
 * @package ToranTejarat
 * @version 10.9.0
 */

defined( 'ABSPATH' ) || exit;

$available_gateways = WC()->payment_gateways->get_available_payment_gateways();
?>

<div class="fb-add-payment-method-page-wrap">

	<div class="fb-content-card-header fb-add-payment-header">
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'payment-methods' ) ); ?>" class="fb-btn-link fb-link-arrow">
			<svg class="fb-arrow-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
			<span><?php esc_html_e( 'بازگشت به روش‌های پرداخت', 'torantejarat' ); ?></span>
		</a>
		<div class="fb-header-icon-title" style="margin-top: 12px;">
			<span class="fb-card-icon">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="2" y="4" width="20" height="16" rx="2"></rect>
					<line x1="2" y1="10" x2="22" y2="10"></line>
					<line x1="6" y1="16" x2="10" y2="16"></line>
				</svg>
			</span>
			<div>
				<h3 class="fb-card-title"><?php esc_html_e( 'افزودن کارت یا روش پرداخت جدید', 'torantejarat' ); ?></h3>
				<p class="fb-card-desc"><?php esc_html_e( 'اطلاعات کارت یا روش پرداخت خود را با بالاترین استانداردهای امنیتی ذخیره فرمایید.', 'torantejarat' ); ?></p>
			</div>
		</div>
	</div>

	<?php if ( $available_gateways ) : ?>
		<form id="add_payment_method" method="post" class="fb-add-payment-form">
			<div id="payment" class="woocommerce-Payment fb-payment-section">
				<ul class="woocommerce-PaymentMethods payment_methods methods fb-payment-gateways-list" aria-label="<?php esc_attr_e( 'Payment methods', 'woocommerce' ); ?>">
					<?php
					if ( count( $available_gateways ) ) {
						current( $available_gateways )->set_current();
					}

					foreach ( $available_gateways as $gateway ) {
						?>
						<li class="woocommerce-PaymentMethod woocommerce-PaymentMethod--<?php echo esc_attr( $gateway->id ); ?> payment_method_<?php echo esc_attr( $gateway->id ); ?> fb-gateway-item">
							<div class="fb-gateway-header-row">
								<input id="payment_method_<?php echo esc_attr( $gateway->id ); ?>" type="radio" class="input-radio" name="payment_method" value="<?php echo esc_attr( $gateway->id ); ?>" <?php checked( $gateway->chosen, true ); ?> />
								<label for="payment_method_<?php echo esc_attr( $gateway->id ); ?>" class="fb-gateway-label">
									<span class="fb-gateway-title"><?php echo wp_kses_post( $gateway->get_title() ); ?></span>
									<?php echo wp_kses_post( $gateway->get_icon() ); ?>
								</label>
							</div>
							<?php
							if ( $gateway->has_fields() || $gateway->get_description() ) {
								echo '<div class="woocommerce-PaymentBox woocommerce-PaymentBox--' . esc_attr( $gateway->id ) . ' payment_box payment_method_' . esc_attr( $gateway->id ) . ' fb-gateway-box" style="display: none;">';
								$gateway->payment_fields();
								echo '</div>';
							}
							?>
						</li>
						<?php
					}
					?>
				</ul>

				<?php do_action( 'woocommerce_add_payment_method_form_bottom' ); ?>

				<div class="form-row fb-form-submit-row">
					<?php wp_nonce_field( 'woocommerce-add-payment-method', 'woocommerce-add-payment-method-nonce' ); ?>
					<button type="submit" class="woocommerce-Button button fb-btn-primary fb-add-method-submit" id="place_order" value="<?php esc_attr_e( 'افزودن روش پرداخت', 'torantejarat' ); ?>">
						<span><?php esc_html_e( 'ذخیره روش پرداخت', 'torantejarat' ); ?></span>
					</button>
					<input type="hidden" name="woocommerce_add_payment_method" id="woocommerce_add_payment_method" value="1" />
				</div>
			</div>
		</form>
	<?php else : ?>
		<div class="fb-empty-state">
			<p class="fb-empty-desc"><?php esc_html_e( 'در حال حاضر درگاه فعالی جهت ذخیره مستقیم کارت یافت نشد.', 'torantejarat' ); ?></p>
		</div>
	<?php endif; ?>

</div>
