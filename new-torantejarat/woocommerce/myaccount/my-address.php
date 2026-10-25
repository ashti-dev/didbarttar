<?php
/**
 * My Addresses
 * Overridden for FrontBaz Modern Store Account
 *
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

$customer_id = get_current_user_id();

if ( ! wc_ship_to_billing_address_only() && wc_shipping_enabled() ) {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing'  => __( 'Billing address', 'woocommerce' ),
			'shipping' => __( 'Shipping address', 'woocommerce' ),
		),
		$customer_id
	);
} else {
	$get_addresses = apply_filters(
		'woocommerce_my_account_get_addresses',
		array(
			'billing' => __( 'Billing address', 'woocommerce' ),
		),
		$customer_id
	);
}
?>

<div class="fb-addresses-page-wrap">

	<div class="fb-content-card-header">
		<div class="fb-header-icon-title">
			<span class="fb-card-icon">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
					<circle cx="12" cy="10" r="3"></circle>
				</svg>
			</span>
			<div>
				<h3 class="fb-card-title"><?php esc_html_e( 'آدرس‌های من', 'torantejarat' ); ?></h3>
				<p class="fb-card-desc"><?php esc_html_e( 'نشانی‌های پیش‌فرض جهت صدور فاکتور و تحویل سریع بسته‌های پستی.', 'torantejarat' ); ?></p>
			</div>
		</div>
	</div>

	<div class="fb-addresses-grid">
		<?php foreach ( $get_addresses as $name => $address_title ) : 
			$address = wc_get_account_formatted_address( $name );
			$is_shipping = 'shipping' === $name;
			$card_title = $is_shipping ? esc_html__( 'آدرس تحویل سفارش (حمل و نقل)', 'torantejarat' ) : esc_html__( 'آدرس صورتحساب و فاکتور', 'torantejarat' );
		?>

			<div class="fb-address-card">
				<div class="fb-address-card-header">
					<div class="fb-address-card-title-wrap">
						<span class="fb-address-icon">
							<?php if ( $is_shipping ) : ?>
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<rect x="1" y="3" width="15" height="13"></rect>
									<polygon points="16 8 20 8 23 11 23 16 16 16 16 8"></polygon>
									<circle cx="5.5" cy="18.5" r="2.5"></circle>
									<circle cx="18.5" cy="18.5" r="2.5"></circle>
								</svg>
							<?php else : ?>
								<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
									<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
									<polyline points="14 2 14 8 20 8"></polyline>
									<line x1="16" y1="13" x2="8" y2="13"></line>
									<line x1="16" y1="17" x2="8" y2="17"></line>
									<polyline points="10 9 9 9 8 9"></polyline>
								</svg>
							<?php endif; ?>
						</span>
						<h4 class="fb-address-title"><?php echo esc_html( $card_title ); ?></h4>
					</div>
					<a href="<?php echo esc_url( wc_get_endpoint_url( 'edit-address', $name ) ); ?>" class="fb-address-edit-link">
						<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
							<path d="M11 4H4a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-7"></path>
							<path d="M18.5 2.5a2.121 2.121 0 0 1 3 3L12 15l-4 1 1-4 9.5-9.5z"></path>
						</svg>
						<?php echo $address ? esc_html__( 'ویرایش', 'torantejarat' ) : esc_html__( 'ثبت آدرس', 'torantejarat' ); ?>
					</a>
				</div>

				<div class="fb-address-card-body">
					<address class="fb-address-content">
						<?php
						echo $address ? wp_kses_post( $address ) : esc_html__( 'شما هنوز این آدرس را ثبت نکرده‌اید.', 'torantejarat' );
						?>
					</address>
				</div>
			</div>

		<?php endforeach; ?>
	</div>

</div>
