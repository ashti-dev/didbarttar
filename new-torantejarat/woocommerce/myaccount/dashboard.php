<?php
/**
 * My Account Dashboard
 * Overridden for FrontBaz Modern Store Account
 *
 * @version 4.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_user = wp_get_current_user();
$display_name = $current_user->display_name ? $current_user->display_name : $current_user->user_login;
$user_id      = $current_user->ID;

$stats = function_exists( 'fb_get_customer_orders_stats' ) ? fb_get_customer_orders_stats( $user_id ) : [
	'processing' => 0,
	'completed'  => 0,
	'cancelled'  => 0,
	'total'      => 0,
];

// Fetch recent 3 orders for quick overview
$recent_orders = wc_get_orders( [
	'customer' => $user_id,
	'limit'    => 3,
	'orderby'  => 'date',
	'order'    => 'DESC',
] );

// Fetch default shipping or billing address summary
$billing_address = WC()->countries->get_formatted_address( [
	'first_name' => get_user_meta( $user_id, 'billing_first_name', true ),
	'last_name'  => get_user_meta( $user_id, 'billing_last_name', true ),
	'address_1'  => get_user_meta( $user_id, 'billing_address_1', true ),
	'city'       => get_user_meta( $user_id, 'billing_city', true ),
	'state'      => get_user_meta( $user_id, 'billing_state', true ),
	'postcode'   => get_user_meta( $user_id, 'billing_postcode', true ),
	'country'    => get_user_meta( $user_id, 'billing_country', true ),
] );
?>

<div class="fb-dashboard-wrap">

	<!-- Welcome Greeting Banner -->
	<div class="fb-welcome-banner">
		<div class="fb-welcome-text">
			<h2 class="fb-welcome-title">
				<?php printf( esc_html__( 'سلام، %s عزیز 👋', 'torantejarat' ), esc_html( $display_name ) ); ?>
			</h2>
			<p class="fb-welcome-desc">
				<?php printf( esc_html__( 'به حساب کاربری %s خوش آمدید. از این بخش می‌توانید سفارش‌ها، آدرس‌های تحویل و مشخصات حساب خود را مدیریت فرمایید.', 'torantejarat' ), esc_html( get_bloginfo( 'name' ) ) ); ?>
			</p>
		</div>
		<div class="fb-welcome-action">
			<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="fb-btn-primary fb-btn-sm">
				<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="9" cy="21" r="1"></circle>
					<circle cx="20" cy="21" r="1"></circle>
					<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
				</svg>
				<span><?php esc_html_e( 'فروشگاه و محصولات', 'torantejarat' ); ?></span>
			</a>
		</div>
	</div>

	<!-- Order Status Counters (4 Quick Stats) -->
	<div class="fb-stats-grid">
		<!-- Processing -->
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="fb-stat-card fb-stat-processing">
			<div class="fb-stat-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="10"></circle>
					<polyline points="12 6 12 12 16 14"></polyline>
				</svg>
			</div>
			<div class="fb-stat-info">
				<span class="fb-stat-num"><?php echo esc_html( $stats['processing'] ); ?></span>
				<span class="fb-stat-label"><?php esc_html_e( 'در حال ارسال', 'torantejarat' ); ?></span>
			</div>
		</a>

		<!-- Completed -->
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="fb-stat-card fb-stat-completed">
			<div class="fb-stat-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
					<polyline points="22 4 12 14.01 9 11.01"></polyline>
				</svg>
			</div>
			<div class="fb-stat-info">
				<span class="fb-stat-num"><?php echo esc_html( $stats['completed'] ); ?></span>
				<span class="fb-stat-label"><?php esc_html_e( 'تحویل داده شده', 'torantejarat' ); ?></span>
			</div>
		</a>

		<!-- Cancelled -->
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="fb-stat-card fb-stat-cancelled">
			<div class="fb-stat-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<circle cx="12" cy="12" r="10"></circle>
					<line x1="15" y1="9" x2="9" y2="15"></line>
					<line x1="9" y1="9" x2="15" y2="15"></line>
				</svg>
			</div>
			<div class="fb-stat-info">
				<span class="fb-stat-num"><?php echo esc_html( $stats['cancelled'] ); ?></span>
				<span class="fb-stat-label"><?php esc_html_e( 'لغو شده / مرجوعی', 'torantejarat' ); ?></span>
			</div>
		</a>

		<!-- Total -->
		<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="fb-stat-card fb-stat-total">
			<div class="fb-stat-icon">
				<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
					<line x1="3" y1="6" x2="21" y2="6"></line>
					<path d="M16 10a4 4 0 0 1-8 0"></path>
				</svg>
			</div>
			<div class="fb-stat-info">
				<span class="fb-stat-num"><?php echo esc_html( $stats['total'] ); ?></span>
				<span class="fb-stat-label"><?php esc_html_e( 'کل سفارش‌ها', 'torantejarat' ); ?></span>
			</div>
		</a>
	</div>

	<!-- Recent Orders Section -->
	<div class="fb-dashboard-section">
		<div class="fb-section-header">
			<h3 class="fb-section-title">
				<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M6 2L3 6v14a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2V6l-3-4z"></path>
					<line x1="3" y1="6" x2="21" y2="6"></line>
				</svg>
				<?php esc_html_e( 'سفارش‌های اخیر شما', 'torantejarat' ); ?>
			</h3>
			<?php if ( ! empty( $recent_orders ) ) : ?>
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>" class="fb-btn-link fb-link-arrow fb-section-more">
					<span><?php esc_html_e( 'مشاهده همه سفارش‌ها', 'torantejarat' ); ?></span>
					<svg class="fb-arrow-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
				</a>
			<?php endif; ?>
		</div>

		<?php if ( ! empty( $recent_orders ) ) : ?>
			<div class="fb-orders-list">
				<?php foreach ( $recent_orders as $order ) : 
					$status = $order->get_status();
					$status_name = wc_get_order_status_name( $status );
					$order_date = wc_format_datetime( $order->get_date_created() );
					$items_count = $order->get_item_count();
				?>
					<div class="fb-order-card">
						<div class="fb-order-card-header">
							<div class="fb-order-meta-info">
								<span class="fb-order-id">
									<?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( $order->get_order_number() ) ); ?>
								</span>
								<span class="fb-order-dot">•</span>
								<span class="fb-order-date"><?php echo esc_html( $order_date ); ?></span>
								<span class="fb-order-dot">•</span>
								<span class="fb-order-count"><?php printf( esc_html__( '%s کالا', 'torantejarat' ), esc_html( $items_count ) ); ?></span>
							</div>
							<div class="fb-order-status fb-status-<?php echo esc_attr( $status ); ?>">
								<?php echo esc_html( $status_name ); ?>
							</div>
						</div>

						<div class="fb-order-card-body">
							<div class="fb-order-total-wrap">
								<span class="fb-order-total-label"><?php esc_html_e( 'مبلغ سفارش:', 'torantejarat' ); ?></span>
								<span class="fb-order-total-val"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
							</div>
							<div class="fb-order-actions">
								<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>" class="fb-btn-outline fb-btn-sm">
									<?php esc_html_e( 'مشاهده جزئیات سفارش', 'torantejarat' ); ?>
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
						<circle cx="9" cy="21" r="1"></circle>
						<circle cx="20" cy="21" r="1"></circle>
						<path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"></path>
					</svg>
				</div>
				<h4 class="fb-empty-title"><?php esc_html_e( 'هنوز هیچ سفارشی ثبت نکرده‌اید', 'torantejarat' ); ?></h4>
				<p class="fb-empty-desc"><?php esc_html_e( 'می‌توانید از بخش فروشگاه محصولات مورد نظر خود را انتخاب نموده و اولین سفارش خود را ثبت فرمایید.', 'torantejarat' ); ?></p>
				<a href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>" class="fb-btn-primary fb-btn-sm">
					<?php esc_html_e( 'مشاهده فروشگاه و خرید', 'torantejarat' ); ?>
				</a>
			</div>
		<?php endif; ?>
	</div>

	<!-- Quick Shortcuts (Addresses & Account Details) -->
	<div class="fb-shortcuts-grid">
		<!-- Address Shortcut -->
		<div class="fb-shortcut-card">
			<div class="fb-shortcut-header">
				<div class="fb-shortcut-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
						<circle cx="12" cy="10" r="3"></circle>
					</svg>
				</div>
				<h4 class="fb-shortcut-title"><?php esc_html_e( 'نشانی تحویل سفارش', 'torantejarat' ); ?></h4>
			</div>
			<div class="fb-shortcut-body">
				<?php if ( ! empty( $billing_address ) ) : ?>
					<p class="fb-shortcut-desc"><?php echo wp_kses_post( $billing_address ); ?></p>
				<?php else : ?>
					<p class="fb-shortcut-desc fb-text-muted"><?php esc_html_e( 'هنوز هیچ آدرسی ثبت نکرده‌اید. برای ارسال سریع‌تر سفارش‌ها نشانی خود را تکمیل نمایید.', 'torantejarat' ); ?></p>
				<?php endif; ?>
			</div>
			<div class="fb-shortcut-footer">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-address' ) ); ?>" class="fb-btn-link fb-link-arrow">
					<span><?php esc_html_e( 'مدیریت و ویرایش آدرس‌ها', 'torantejarat' ); ?></span>
					<svg class="fb-arrow-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
				</a>
			</div>
		</div>

		<!-- Account Info Shortcut -->
		<div class="fb-shortcut-card">
			<div class="fb-shortcut-header">
				<div class="fb-shortcut-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
						<circle cx="12" cy="7" r="4"></circle>
					</svg>
				</div>
				<h4 class="fb-shortcut-title"><?php esc_html_e( 'اطلاعات و امنیت حساب', 'torantejarat' ); ?></h4>
			</div>
			<div class="fb-shortcut-body">
				<p class="fb-shortcut-desc">
					<?php esc_html_e( 'می‌توانید اطلاعات هویتی، آدرس ایمیل و کلمه عبور حساب کاربری خود را در این بخش به‌روزرسانی کنید.', 'torantejarat' ); ?>
				</p>
			</div>
			<div class="fb-shortcut-footer">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( 'edit-account' ) ); ?>" class="fb-btn-link fb-link-arrow">
					<span><?php esc_html_e( 'ویرایش اطلاعات و کلمه عبور', 'torantejarat' ); ?></span>
					<svg class="fb-arrow-icon" width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M19 12H5M12 19l-7-7 7-7"/></svg>
				</a>
			</div>
		</div>
	</div>

</div>

<?php
/**
 * My Account dashboard hooks
 */
do_action( 'woocommerce_account_dashboard' );
do_action( 'woocommerce_before_my_account' );
do_action( 'woocommerce_after_my_account' );
