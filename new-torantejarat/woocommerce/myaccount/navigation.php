<?php
/**
 * My Account navigation
 * Overridden for FrontBaz Modern Store Account
 *
 * @version 9.3.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();

// Look for phone number from WooCommerce billing, SMS/OTP plugins (Digits, etc.) or user meta
$user_phone = get_user_meta( $current_user->ID, 'billing_phone', true );
if ( empty( $user_phone ) ) {
	$user_phone = get_user_meta( $current_user->ID, 'digits_phone', true );
}
if ( empty( $user_phone ) ) {
	$user_phone = get_user_meta( $current_user->ID, 'digits_phone_no', true );
}
if ( empty( $user_phone ) ) {
	$user_phone = get_user_meta( $current_user->ID, 'phone', true );
}
if ( empty( $user_phone ) ) {
	$user_phone = get_user_meta( $current_user->ID, 'mobile', true );
}
if ( empty( $user_phone ) ) {
	$user_phone = get_user_meta( $current_user->ID, 'phone_number', true );
}

// Clean & normalize phone if found, or detect if user_login itself is a phone number
if ( ! empty( $user_phone ) ) {
	$user_phone = trim( $user_phone );
	if ( preg_match( '/^(\+?98|0098|98)(9\d{9})$/', $user_phone, $matches ) ) {
		$user_phone = '0' . $matches[2];
	}
} elseif ( preg_match( '/^(\+?98|0098|98|0)?(9\d{9})$/', $current_user->user_login, $matches ) ) {
	$user_phone = '0' . $matches[2];
}

// Priority: If phone number exists, display phone; otherwise fallback to username (user_login)
$account_user_name = ! empty( $user_phone ) ? $user_phone : $current_user->user_login;

$user_email   = $current_user->user_email;
$avatar_url   = get_avatar_url( $current_user->ID, [ 'size' => 120 ] );
$first_letter = mb_strtoupper( mb_substr( $account_user_name, 0, 1, 'UTF-8' ), 'UTF-8' );

$orders_stats = function_exists( 'fb_get_customer_orders_stats' ) ? fb_get_customer_orders_stats( $current_user->ID ) : [];
$active_orders = isset( $orders_stats['processing'] ) ? $orders_stats['processing'] : 0;

do_action( 'woocommerce_before_account_navigation' );
?>

<div class="fb-account-nav-card">

	<!-- User Profile Header Card -->
	<div class="fb-user-profile-header">
		<div class="fb-user-avatar-wrap">
			<?php if ( ! empty( $avatar_url ) && ! strpos( $avatar_url, 'gravatar.com/avatar/?s=' ) ) : ?>
				<img src="<?php echo esc_url( $avatar_url ); ?>" alt="<?php echo esc_attr( $account_user_name ); ?>" class="fb-user-avatar-img" />
			<?php else : ?>
				<div class="fb-user-avatar-fallback">
					<?php if ( is_numeric( $first_letter ) ) : ?>
						<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle></svg>
					<?php else : ?>
						<?php echo esc_html( $first_letter ); ?>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
		<div class="fb-user-info-meta">
			<h4 class="fb-user-name" title="<?php echo esc_attr( $account_user_name ); ?>">
				<span class="fb-user-name-text" x-apple-data-detectors="false"><?php echo esc_html( $account_user_name ); ?></span>
			</h4>
			<?php if ( ! empty( $user_email ) ) : ?>
				<span class="fb-user-email"><?php echo esc_html( $user_email ); ?></span>
			<?php endif; ?>
			<div class="fb-user-badge-wrap">
				<span class="fb-user-badge"><?php printf( esc_html__( 'کاربر %s', 'torantejarat' ), esc_html( get_bloginfo( 'name' ) ) ); ?></span>
			</div>
		</div>
	</div>

	<!-- Navigation Menu List -->
	<nav class="woocommerce-MyAccount-navigation fb-myaccount-nav" aria-label="<?php esc_html_e( 'Account pages', 'woocommerce' ); ?>">
		<ul class="fb-nav-list">
			<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : 
				$is_active = wc_is_current_account_menu_item( $endpoint );
				$item_classes = 'fb-nav-item fb-nav-item-' . esc_attr( $endpoint ) . ' ' . wc_get_account_menu_item_classes( $endpoint ) . ( $is_active ? ' fb-active' : '' );
			?>
				<li class="<?php echo esc_attr( $item_classes ); ?>">
					<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo $is_active ? 'aria-current="page"' : ''; ?>>
						<span class="fb-nav-icon">
							<?php
							switch ( $endpoint ) {
								case 'dashboard':
									?>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<rect x="3" y="3" width="7" height="9"></rect>
										<rect x="14" y="3" width="7" height="5"></rect>
										<rect x="14" y="12" width="7" height="9"></rect>
										<rect x="3" y="16" width="7" height="5"></rect>
									</svg>
									<?php
									break;

								case 'orders':
									?>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
										<line x1="3" y1="6" x2="21" y2="6"></line>
										<path d="M16 10a4 4 0 0 1-8 0"></path>
									<?php
									break;

								case 'downloads':
									?>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
										<polyline points="7 10 12 15 17 10"></polyline>
										<line x1="12" y1="15" x2="12" y2="3"></line>
									</svg>
									<?php
									break;

								case 'edit-address':
									?>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
										<circle cx="12" cy="10" r="3"></circle>
									</svg>
									<?php
									break;

								case 'payment-methods':
									?>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<rect x="2" y="4" width="20" height="16" rx="2"></rect>
										<line x1="2" y1="10" x2="22" y2="10"></line>
										<line x1="6" y1="16" x2="10" y2="16"></line>
									</svg>
									<?php
									break;

								case 'edit-account':
									?>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
										<circle cx="12" cy="7" r="4"></circle>
									</svg>
									<?php
									break;

								case 'customer-logout':
									?>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
										<polyline points="16 17 21 12 16 7"></polyline>
										<line x1="21" y1="12" x2="9" y2="12"></line>
									</svg>
									<?php
									break;

								default:
									?>
									<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
										<circle cx="12" cy="12" r="10"></circle>
										<line x1="12" y1="16" x2="12" y2="12"></line>
										<line x1="12" y1="8" x2="12.01" y2="8"></line>
									</svg>
									<?php
									break;
							}
							?>
						</span>
						<span class="fb-nav-label"><?php echo esc_html( $label ); ?></span>

						<?php if ( 'orders' === $endpoint && $active_orders > 0 ) : ?>
							<span class="fb-nav-badge" title="<?php esc_attr_e( 'سفارش‌های در حال انجام', 'torantejarat' ); ?>">
								<?php echo esc_html( $active_orders ); ?>
							</span>
						<?php endif; ?>
					</a>
				</li>
			<?php endforeach; ?>
		</ul>
	</nav>

</div>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
