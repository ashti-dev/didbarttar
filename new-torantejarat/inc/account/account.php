<?php
/**
 * Advanced account panel — endpoints, wishlist ledger, reorder, REST, cache.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Account;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const WISHLIST_VERSION = '1.0.0';

/* ---------- Endpoints ---------- */

function register_endpoints() {
	add_rewrite_endpoint( 'wishlist', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'security', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'notifications', EP_ROOT | EP_PAGES );
	add_rewrite_endpoint( 'tickets', EP_ROOT | EP_PAGES );
}
add_action( 'init', __NAMESPACE__ . '\\register_endpoints' );

function flush_on_theme_switch() {
	register_endpoints();
	flush_rewrite_rules();
}
add_action( 'after_switch_theme', __NAMESPACE__ . '\\flush_on_theme_switch' );

function menu_items( $items ) {
	$ordered = array();
	foreach ( $items as $endpoint => $label ) {
		$ordered[ $endpoint ] = $label;
		if ( 'orders' === $endpoint ) {
			$ordered['wishlist'] = __( 'علاقه‌مندی‌ها', 'torantejarat' );
			$ordered['notifications'] = __( 'اعلان‌ها', 'torantejarat' );
			$ordered['tickets'] = __( 'پشتیبانی', 'torantejarat' );
		}
		if ( 'edit-account' === $endpoint ) {
			$ordered['security'] = __( 'امنیت', 'torantejarat' );
		}
	}
	if ( ! isset( $ordered['wishlist'] ) ) {
		$ordered['wishlist'] = __( 'علاقه‌مندی‌ها', 'torantejarat' );
	}
	if ( isset( $ordered['downloads'] ) && ! wc_get_customer_available_downloads( get_current_user_id() ) ) {
		unset( $ordered['downloads'] );
	}
	return $ordered;
}
add_filter( 'woocommerce_account_menu_items', __NAMESPACE__ . '\\menu_items' );

function menu_icons() {
	return array(
		'dashboard'       => 'home',
		'orders'          => 'box',
		'wishlist'        => 'heart',
		'notifications'   => 'bell',
		'tickets'         => 'ticket',
		'downloads'       => 'scan',
		'edit-address'    => 'pin',
		'payment-methods' => 'check',
		'edit-account'    => 'user',
		'security'        => 'shield',
		'customer-logout' => 'logout',
	);
}

/* ---------- Wishlist table ---------- */

function table_name() {
	global $wpdb;
	return $wpdb->prefix . 'torantejarat_wishlist';
}

function maybe_create_table() {
	if ( '1.0.0' === get_option( 'torantejarat_wishlist_db_version' ) ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$sql     = 'CREATE TABLE ' . table_name() . " (\n"
		. "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n"
		. "user_id BIGINT UNSIGNED NOT NULL,\n"
		. "product_id BIGINT UNSIGNED NOT NULL,\n"
		. "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n"
		. "PRIMARY KEY (id),\n"
		. "UNIQUE KEY user_product (user_id, product_id),\n"
		. "KEY user_created (user_id, created_at)\n"
		. ") $charset;";
	dbDelta( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- dbDelta takes raw schema.
	update_option( 'torantejarat_wishlist_db_version', '1.0.0' );
}
add_action( 'init', __NAMESPACE__ . '\\maybe_create_table' );

function get_wishlist_ids( $user_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom ledger table by design.
	return array_map( 'absint', $wpdb->get_col( $wpdb->prepare( 'SELECT product_id FROM ' . table_name() . ' WHERE user_id = %d ORDER BY created_at DESC', $user_id ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function is_wishlisted( $user_id, $product_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery -- Custom ledger table by design.
	return (bool) $wpdb->get_var( $wpdb->prepare( 'SELECT id FROM ' . table_name() . ' WHERE user_id = %d AND product_id = %d', $user_id, $product_id ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function toggle_wishlist( $user_id, $product_id ) {
	global $wpdb;
	$product_id = absint( $product_id );
	$product    = wc_get_product( $product_id );
	if ( ! $product || ! $product->is_visible() ) {
		return new \WP_Error( 'invalid_product', __( 'محصول معتبر نیست.', 'torantejarat' ), array( 'status' => 400 ) );
	}
	if ( is_wishlisted( $user_id, $product_id ) ) {
		$result = $wpdb->delete( table_name(), array( 'user_id' => $user_id, 'product_id' => $product_id ), array( '%d', '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		if ( false === $result ) {
			return new \WP_Error( 'wishlist_delete_failed', __( 'حذف از علاقه‌مندی‌ها انجام نشد. دوباره تلاش کنید.', 'torantejarat' ), array( 'status' => 500 ) );
		}
		delete_transient( summary_cache_key( $user_id ) );
		return false;
	}
	$result = $wpdb->insert( table_name(), array( 'user_id' => $user_id, 'product_id' => $product_id ), array( '%d', '%d' ) ); // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
	if ( false === $result ) {
		return new \WP_Error( 'wishlist_insert_failed', __( 'افزودن به علاقه‌مندی‌ها انجام نشد. دوباره تلاش کنید.', 'torantejarat' ), array( 'status' => 500 ) );
	}
	delete_transient( summary_cache_key( $user_id ) );
	return true;
}

/* ---------- REST ---------- */

function register_rest_routes() {
	register_rest_route(
		'torantejarat/v1',
		'/wishlist/toggle',
		array(
			'methods'             => 'POST',
			'callback'            => __NAMESPACE__ . '\\rest_toggle',
			'permission_callback' => 'is_user_logged_in',
			'args'                => array(
				'product_id' => array( 'required' => true, 'type' => 'integer', 'minimum' => 1 ),
			),
		)
	);
}
add_action( 'rest_api_init', __NAMESPACE__ . '\\register_rest_routes' );

function rest_toggle( \WP_REST_Request $request ) {
	// Cookie authentication is authorized by WordPress with X-WP-Nonce (wp_rest).
	$result = toggle_wishlist( get_current_user_id(), $request->get_param( 'product_id' ) );
	if ( is_wp_error( $result ) ) {
		return $result;
	}
	return array(
		'wishlisted' => $result,
		'count'      => count( get_wishlist_ids( get_current_user_id() ) ),
	);
}

/* ---------- Native + PDP wishlist toggle ---------- */

function wishlist_native_toggle() {
	$product_id = isset( $_POST['product_id'] ) ? absint( $_POST['product_id'] ) : 0; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified below.
	if ( ! is_user_logged_in() ) {
		$auth_url = function_exists( 'ToranTejarat\\Theme\\torantejarat_auth_get_page_url' ) ? \ToranTejarat\Theme\torantejarat_auth_get_page_url() : wp_login_url();
		wp_safe_redirect( $auth_url );
		exit;
	}

	$redirect = wp_validate_redirect( wp_get_referer() ?: '', wc_get_account_endpoint_url( 'wishlist' ) );
	$nonce = isset( $_POST['torantejarat_wishlist_nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['torantejarat_wishlist_nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'torantejarat_wishlist_toggle_' . $product_id ) ) {
		wc_add_notice( __( 'نشست فرم منقضی شده است. دوباره تلاش کنید.', 'torantejarat' ), 'error' );
		wp_safe_redirect( $redirect );
		exit;
	}

	$result = toggle_wishlist( get_current_user_id(), $product_id );
	if ( is_wp_error( $result ) ) {
		wc_add_notice( $result->get_error_message(), 'error' );
	} else {
		wc_add_notice( $result ? __( 'به علاقه‌مندی‌ها اضافه شد.', 'torantejarat' ) : __( 'از علاقه‌مندی‌ها حذف شد.', 'torantejarat' ), 'success' );
	}
	wp_safe_redirect( $redirect );
	exit;
}
add_action( 'admin_post_torantejarat_wishlist_toggle', __NAMESPACE__ . '\\wishlist_native_toggle' );
add_action( 'admin_post_nopriv_torantejarat_wishlist_toggle', __NAMESPACE__ . '\\wishlist_native_toggle' );

function wishlist_button() {
	global $product;
	if ( ! is_user_logged_in() || ! $product instanceof \WC_Product || ! $product->is_visible() ) {
		return;
	}

	$product_id = $product->get_id();
	$active     = is_wishlisted( get_current_user_id(), $product_id );
	$label      = $active ? __( 'حذف از علاقه‌مندی‌ها', 'torantejarat' ) : __( 'افزودن به علاقه‌مندی‌ها', 'torantejarat' );
	?>
	<form class="torantejarat-wishlist" method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" data-wishlist-form data-wishlisted="<?php echo esc_attr( $active ? 'true' : 'false' ); ?>">
		<input type="hidden" name="action" value="torantejarat_wishlist_toggle">
		<input type="hidden" name="product_id" value="<?php echo esc_attr( $product_id ); ?>">
		<input type="hidden" name="torantejarat_wishlist_nonce" value="<?php echo esc_attr( wp_create_nonce( 'torantejarat_wishlist_toggle_' . $product_id ) ); ?>">
		<button type="submit" class="torantejarat-btn torantejarat-btn-outline torantejarat-btn-block<?php echo $active ? ' is-active' : ''; ?>" data-wishlist-toggle="<?php echo esc_attr( $product_id ); ?>" aria-pressed="<?php echo esc_attr( $active ? 'true' : 'false' ); ?>">
			<span data-wishlist-label><?php echo esc_html( $label ); ?></span>
		</button>
		<span class="torantejarat-wishlist__message" data-wishlist-message aria-live="polite"></span>
	</form>
	<?php
}
add_action( 'woocommerce_single_product_summary', __NAMESPACE__ . '\\wishlist_button', 31 );

/* ---------- Reorder ---------- */

function reorder_action() {
	if ( ! isset( $_GET['torantejarat_reorder'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Verified below.
		return;
	}
	$order_id = absint( $_GET['torantejarat_reorder'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	if ( ! isset( $_GET['_wpnonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'torantejarat_reorder_' . $order_id ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wc_add_notice( __( 'درخواست نامعتبر است.', 'torantejarat' ), 'error' );
		return;
	}
	$order = wc_get_order( $order_id );
	if ( ! $order instanceof \WC_Order || (int) $order->get_customer_id() !== get_current_user_id() ) {
		wc_add_notice( __( 'سفارش یافت نشد.', 'torantejarat' ), 'error' );
		return;
	}
	$added   = 0;
	$skipped = 0;
	foreach ( $order->get_items() as $item ) {
		$product = $item->get_product();
		if ( ! $product || ! $product->is_purchasable() || ! $product->is_in_stock() ) {
			++$skipped;
			continue;
		}
		$qty = max( 1, (int) $item->get_quantity() );
		if ( $product->managing_stock() && ! $product->backorders_allowed() ) {
			$stock = (int) $product->get_stock_quantity();
			if ( $stock <= 0 ) {
				++$skipped;
				continue;
			}
			$qty = min( $qty, $stock );
		}
		if ( $product->is_type( 'variation' ) ) {
			WC()->cart->add_to_cart( $product->get_parent_id(), $qty, $product->get_id() );
		} else {
			WC()->cart->add_to_cart( $product->get_id(), $qty );
		}
		++$added;
	}
	if ( $added > 0 ) {
		wc_add_notice( sprintf( __( '%s قلم به سبد اضافه شد.', 'torantejarat' ), \ToranTejarat\Theme\torantejarat_fa_number( $added ) ) );
		if ( $skipped > 0 ) {
			wc_add_notice( sprintf( __( '%s قلم ناموجود بود و اضافه نشد.', 'torantejarat' ), \ToranTejarat\Theme\torantejarat_fa_number( $skipped ) ), 'notice' );
		}
		wp_safe_redirect( wc_get_cart_url() );
		exit;
	}
	wc_add_notice( __( 'هیچ‌یک از اقلام این سفارش هم‌اکنون موجود نیست.', 'torantejarat' ), 'error' );
	wp_safe_redirect( $order->get_view_order_url() );
	exit;
}
add_action( 'template_redirect', __NAMESPACE__ . '\\reorder_action', 5 );

function reorder_url( $order_id ) {
	return wp_nonce_url( add_query_arg( 'torantejarat_reorder', absint( $order_id ), home_url( '/' ) ), 'torantejarat_reorder_' . absint( $order_id ) );
}

function order_actions( $actions, $order ) {
	if ( $order instanceof \WC_Order && in_array( $order->get_status(), array( 'processing', 'completed', 'shipped', 'on-hold' ), true ) ) {
		$actions['torantejarat-reorder'] = array(
			'url'  => reorder_url( $order->get_id() ),
			'name' => __( 'خرید مجدد', 'torantejarat' ),
		);
	}
	return $actions;
}
add_filter( 'woocommerce_my_account_my_orders_actions', __NAMESPACE__ . '\\order_actions', 10, 2 );

/* ---------- Dashboard summary (transient, per user) ---------- */

function summary_cache_key( $user_id ) {
	return 'torantejarat_account_' . absint( $user_id );
}

function clear_summary_cache( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( $order instanceof \WC_Order && $order->get_customer_id() ) {
		delete_transient( summary_cache_key( $order->get_customer_id() ) );
	}
}
add_action( 'woocommerce_new_order', __NAMESPACE__ . '\\clear_summary_cache' );
add_action( 'woocommerce_order_status_changed', __NAMESPACE__ . '\\clear_summary_cache', 10, 1 );

function get_summary( $user_id ) {
	$key     = summary_cache_key( $user_id );
	$cached  = get_transient( $key );
	if ( is_array( $cached ) ) {
		return $cached;
	}
	$orders = wc_get_orders(
		array(
			'customer_id' => $user_id,
			'limit'       => 4,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'return'      => 'ids',
		)
	);
	$active = null;
	foreach ( $orders as $id ) {
		$o = wc_get_order( $id );
		if ( $o && ! in_array( $o->get_status(), array( 'completed', 'cancelled', 'refunded', 'failed' ), true ) ) {
			$active = $o;
			break;
		}
	}
	$summary = array(
		'active'   => $active instanceof \WC_Order ? $active->get_id() : 0,
		'recent'   => array_values( array_filter( array_map( 'absint', $orders ) ) ),
		'wishlist' => count( get_wishlist_ids( $user_id ) ),
	);
	set_transient( $key, $summary, 10 * MINUTE_IN_SECONDS );
	return $summary;
}

/* ---------- Notifications (in-site, order events) ---------- */

function notifications_table() {
	global $wpdb;
	return $wpdb->prefix . 'torantejarat_notifications';
}

function maybe_create_notifications_table() {
	if ( '1.0.0' === get_option( 'torantejarat_notifications_db_version' ) ) {
		return;
	}
	global $wpdb;
	require_once ABSPATH . 'wp-admin/includes/upgrade.php';
	$charset = $wpdb->get_charset_collate();
	$sql     = 'CREATE TABLE ' . notifications_table() . " (\n"
		. "id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,\n"
		. "user_id BIGINT UNSIGNED NOT NULL,\n"
		. "type VARCHAR(40) NOT NULL DEFAULT '',\n"
		. "title VARCHAR(190) NOT NULL DEFAULT '',\n"
		. "text TEXT NOT NULL,\n"
		. "link VARCHAR(190) NOT NULL DEFAULT '',\n"
		. "is_read TINYINT(1) NOT NULL DEFAULT 0,\n"
		. "created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,\n"
		. "PRIMARY KEY (id),\n"
		. "KEY user_read (user_id, is_read, created_at)\n"
		. ") $charset;";
	dbDelta( $sql ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared -- dbDelta takes raw schema.
	update_option( 'torantejarat_notifications_db_version', '1.0.0' );
}
add_action( 'init', __NAMESPACE__ . '\\maybe_create_notifications_table' );

function add_notification( $user_id, $type, $title, $text, $link = '' ) {
	global $wpdb;
	$user_id = absint( $user_id );
	if ( $user_id <= 0 || '' === $title ) {
		return 0;
	}
	$wpdb->insert( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		notifications_table(),
		array(
			'user_id' => $user_id,
			'type'    => sanitize_key( $type ),
			'title'   => sanitize_text_field( $title ),
			'text'    => sanitize_textarea_field( $text ),
			'link'    => esc_url_raw( $link ),
		),
		array( '%d', '%s', '%s', '%s', '%s' )
	);
	return (int) $wpdb->insert_id;
}

function get_notifications( $user_id, $limit = 20, $unread_only = false ) {
	global $wpdb;
	$where = 'WHERE user_id = %d';
	$params = array( absint( $user_id ) );
	if ( $unread_only ) {
		$where .= ' AND is_read = 0';
	}
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom ledger table by design.
	return $wpdb->get_results( $wpdb->prepare( 'SELECT * FROM ' . notifications_table() . " $where ORDER BY created_at DESC LIMIT %d", array_merge( $params, array( absint( $limit ) ) ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function count_unread_notifications( $user_id ) {
	global $wpdb;
	// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching -- Custom ledger table by design.
	return (int) $wpdb->get_var( $wpdb->prepare( 'SELECT COUNT(*) FROM ' . notifications_table() . ' WHERE user_id = %d AND is_read = 0', absint( $user_id ) ) ); // phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
}

function mark_notification_read( $user_id, $id ) {
	global $wpdb;
	$wpdb->update( // phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery,WordPress.DB.DirectDatabaseQuery.NoCaching
		notifications_table(),
		array( 'is_read' => 1 ),
		array( 'id' => absint( $id ), 'user_id' => absint( $user_id ) ),
		array( '%d' ),
		array( '%d', '%d' )
	);
}

/* Auto-notify on order lifecycle events (owner actions only, never faked). */
function notify_order_created( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order instanceof \WC_Order || ! $order->get_customer_id() ) {
		return;
	}
	add_notification(
		$order->get_customer_id(),
		'order_created',
		sprintf( __( 'سفارش #%s ثبت شد', 'torantejarat' ), $order->get_order_number() ),
		__( 'سفارش شما با موفقیت ثبت شد و در انتظار بررسی است.', 'torantejarat' ),
		$order->get_view_order_url()
	);
}
add_action( 'woocommerce_new_order', __NAMESPACE__ . '\\notify_order_created' );

function notify_order_status( $order_id, $old_status, $new_status ) {
	$order = wc_get_order( $order_id );
	if ( ! $order instanceof \WC_Order || ! $order->get_customer_id() ) {
		return;
	}
	$messages = array(
		'processing' => __( 'سفارش شما تأیید شد و در حال آماده‌سازی است.', 'torantejarat' ),
		'shipped'    => __( 'سفارش شما ارسال شد. کد مرسوله در جزئیات سفارش قابل مشاهده است.', 'torantejarat' ),
		'completed'  => __( 'سفارش شما تحویل داده شد. ممنون از خریدتان.', 'torantejarat' ),
		'cancelled'  => __( 'سفارش شما لغو شد. برای جزئیات با پشتیبانی در تماس باشید.', 'torantejarat' ),
	);
	if ( ! isset( $messages[ $new_status ] ) ) {
		return;
	}
	add_notification(
		$order->get_customer_id(),
		'order_' . $new_status,
		sprintf( __( 'سفارش #%s: %s', 'torantejarat' ), $order->get_order_number(), wc_get_order_status_name( $new_status ) ),
		$messages[ $new_status ],
		$order->get_view_order_url()
	);
}
add_action( 'woocommerce_order_status_changed', __NAMESPACE__ . '\\notify_order_status', 10, 3 );

function notify_tracking_added( $order_id ) {
	$order = wc_get_order( $order_id );
	if ( ! $order instanceof \WC_Order || ! $order->get_customer_id() ) {
		return;
	}
	$code = $order->get_meta( '_torantejarat_tracking' );
	if ( '' === $code ) {
		return;
	}
	add_notification(
		$order->get_customer_id(),
		'tracking',
		sprintf( __( 'کد مرسوله سفارش #%s', 'torantejarat' ), $order->get_order_number() ),
		sprintf( __( 'کد رهگیری شما: %s', 'torantejarat' ), $code ),
		$order->get_view_order_url()
	);
}

function notifications_endpoint_content() {
	$user_id = get_current_user_id();
	if ( isset( $_GET['mark_read'] ) && isset( $_GET['_wpnonce'] ) && wp_verify_nonce( sanitize_key( wp_unslash( $_GET['_wpnonce'] ) ), 'torantejarat_notif_read' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		mark_notification_read( $user_id, absint( $_GET['mark_read'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	$items = get_notifications( $user_id, 30 );
	wc_get_template( 'myaccount/notifications.php', array( 'items' => $items ), '', trailingslashit( get_template_directory() ) . 'woocommerce/' );
}
add_action( 'woocommerce_account_notifications_endpoint', __NAMESPACE__ . '\\notifications_endpoint_content' );

function notification_read_url( $id ) {
	return wp_nonce_url( add_query_arg( 'mark_read', absint( $id ), wc_get_account_endpoint_url( 'notifications' ) ), 'torantejarat_notif_read' );
}

function wishlist_endpoint_content() {
	$ids      = get_wishlist_ids( get_current_user_id() );
	$products = array();
	foreach ( $ids as $id ) {
		$p = wc_get_product( $id );
		if ( $p && $p->is_visible() ) {
			$products[] = $p;
		}
	}
	wc_get_template( 'myaccount/wishlist.php', array( 'products' => $products ), '', trailingslashit( get_template_directory() ) . 'woocommerce/' );
}
add_action( 'woocommerce_account_wishlist_endpoint', __NAMESPACE__ . '\\wishlist_endpoint_content' );

/* ---------- Tickets endpoint content ---------- */

function tickets_endpoint_content() {
	$user_id = get_current_user_id();
	$tickets = \ToranTejarat\Theme\torantejarat_user_tickets( $user_id );
	$orders  = wc_get_orders(
		array(
			'customer_id' => $user_id,
			'limit'       => 10,
			'orderby'     => 'date',
			'order'       => 'DESC',
			'return'      => 'ids',
		)
	);
	wc_get_template( 'myaccount/tickets.php', array( 'tickets' => $tickets, 'orders' => $orders ), '', trailingslashit( get_template_directory() ) . 'woocommerce/' );
}
add_action( 'woocommerce_account_tickets_endpoint', __NAMESPACE__ . '\\tickets_endpoint_content' );

/* ---------- Security: sessions ---------- */

function security_endpoint_content() {
	$sessions = \WP_Session_Tokens::get_instance( get_current_user_id() )->get_all();
	wc_get_template( 'myaccount/security.php', array( 'sessions' => $sessions ), '', trailingslashit( get_template_directory() ) . 'woocommerce/' );
}
add_action( 'woocommerce_account_security_endpoint', __NAMESPACE__ . '\\security_endpoint_content' );

function logout_other_sessions() {
	if ( ! isset( $_POST['torantejarat_security_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['torantejarat_security_nonce'] ) ), 'torantejarat-security' ) ) {
		wc_add_notice( __( 'درخواست نامعتبر است.', 'torantejarat' ), 'error' );
		wp_safe_redirect( wc_get_account_endpoint_url( 'security' ) );
		exit;
	}
	\WP_Session_Tokens::get_instance( get_current_user_id() )->destroy_all_except_current();
	wc_add_notice( __( 'همه نشست‌های دیگر بسته شد.', 'torantejarat' ) );
	wp_safe_redirect( wc_get_account_endpoint_url( 'security' ) );
	exit;
}
add_action( 'admin_post_torantejarat_logout_others', __NAMESPACE__ . '\\logout_other_sessions' );

/* ---------- No cache on account pages (personal data) ---------- */

function nocache_account_pages() {
	if ( function_exists( 'is_account_page' ) && is_account_page() ) {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( 'DONOTCACHEPAGE', true );
		}
		nocache_headers();
	}
}
add_action( 'template_redirect', __NAMESPACE__ . '\\nocache_account_pages', 1 );

/* ---------- Redirects for retired category slugs ---------- */

function retired_category_redirect() {
	if ( ! is_404() ) {
		return;
	}
	$path = isset( $_SERVER['REQUEST_URI'] ) ? sanitize_text_field( wp_unslash( $_SERVER['REQUEST_URI'] ) ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Path check only.
	if ( false !== strpos( $path, '/imported' ) ) {
		$target = get_term_link( 'industrial', 'product_cat' );
		if ( ! is_wp_error( $target ) ) {
			wp_safe_redirect( $target, 301 );
			exit;
		}
	}
}
add_action( 'template_redirect', __NAMESPACE__ . '\\retired_category_redirect', 2 );

/* ---------- Orders query: status filter + number search ---------- */

function orders_query_args( $args ) {
	if ( ! is_account_page() ) {
		return $args;
	}
	if ( isset( $_GET['status'] ) && '' !== $_GET['status'] ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter.
		$status = sanitize_key( wp_unslash( $_GET['status'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$allowed = array( 'pending', 'processing', 'on-hold', 'shipped', 'completed', 'cancelled', 'refunded', 'failed' );
		if ( in_array( $status, $allowed, true ) ) {
			$args['status'] = array( 'wc-' . $status );
		}
	}
	return $args;
}
add_filter( 'woocommerce_my_account_my_orders_query', __NAMESPACE__ . '\\orders_query_args' );
