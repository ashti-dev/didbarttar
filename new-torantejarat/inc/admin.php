<?php
/**
 * Branded wp-admin: color scheme, decluttered dashboard, store widget.
 * Never touches WooCommerce workflows — presentation only.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Brand palette across wp-admin (scheme-independent CSS override).
 */
function torantejarat_admin_css() {
	?>
	<style id="torantejarat-admin">
		#adminmenuback,#adminmenuwrap,#adminmenu{background:#172325}
		#adminmenu a{color:#cbd2ce}
		#adminmenu div.wp-menu-image:before{color:#8fa09a}
		#adminmenu li.menu-top:hover,#adminmenu li.opensub>a.menu-top,#adminmenu li>a.menu-top:focus{background:#232d2e;color:#fff}
		#adminmenu .wp-has-current-submenu>a.menu-top,#adminmenu .wp-menu-open>a.menu-top{background:#232d2e;color:#fff}
		#adminmenu li.current a.menu-top,#adminmenu li.wp-has-current-submenu a.wp-has-current-submenu{color:#f77b3e}
		#adminmenu .wp-submenu{background:#10181a}
		#adminmenu .wp-submenu a{color:#aeb9b4}
		#adminmenu .wp-submenu a:hover,#adminmenu .wp-submenu a:focus{color:#f77b3e}
		#wpadminbar{background:#10181a}
		.wp-core-ui .button-primary{background:#f77b3e;border-color:#f77b3e}
		.wp-core-ui .button-primary:hover{background:#e96a2b;border-color:#e96a2b}
	</style>
	<?php
	$screen = get_current_screen();
	if ( ! $screen || ( 'dashboard' !== $screen->id && false === strpos( $screen->id, 'torantejarat' ) ) ) {
		return;
	}
	?>
	<style id="torantejarat-admin-dash">
		.torantejarat-dash-rows{margin:0;display:grid;gap:4px}
		.torantejarat-dash-rows a{display:flex;align-items:center;gap:12px;padding:10px 6px;text-decoration:none;color:inherit;border-radius:8px}
		.torantejarat-dash-rows a:hover{background:#f4f5f1}
		.torantejarat-dash-rows strong{font-size:22px;min-width:44px;text-align:center;background:#172325;color:#fff;border-radius:10px;padding:4px 8px}
	</style>
	<?php
}

function torantejarat_dashboard_cleanup() {
	remove_action( 'welcome_panel', 'wp_welcome_panel' );
	remove_meta_box( 'dashboard_primary', 'dashboard', 'side' );
	remove_meta_box( 'dashboard_quick_press', 'dashboard', 'side' );
	remove_meta_box( 'woocommerce_dashboard_status', 'dashboard', 'normal' );
}

function torantejarat_store_widget() {
	wp_add_dashboard_widget(
		'torantejarat-store',
		__( 'نمای فروشگاه', 'torantejarat' ),
		__NAMESPACE__ . '\\torantejarat_store_widget_html'
	);
}

function torantejarat_store_widget_html() {
	$pending  = function_exists( 'wc_orders_count' ) ? (int) wc_orders_count( 'pending' ) : 0;
	$products = (int) wp_count_posts( 'product' )->publish;
	$requests = (int) wp_count_posts( 'torantejarat_request' )->private;
	$rows     = array(
		array(
			'label' => __( 'سفارش در انتظار بررسی', 'torantejarat' ),
			'count' => $pending,
			'url'   => admin_url( 'edit.php?post_status=pending&post_type=shop_order' ),
		),
		array(
			'label' => __( 'محصول منتشرشده', 'torantejarat' ),
			'count' => $products,
			'url'   => admin_url( 'edit.php?post_type=product' ),
		),
		array(
			'label' => __( 'درخواست بی‌پاسخ', 'torantejarat' ),
			'count' => $requests,
			'url'   => admin_url( 'edit.php?post_type=torantejarat_request' ),
		),
	);
	echo '<ul class="torantejarat-dash-rows">';
	foreach ( $rows as $row ) {
		echo '<li><a href="' . esc_url( $row['url'] ) . '"><strong>' . esc_html( number_format_i18n( $row['count'] ) ) . '</strong>' . esc_html( $row['label'] ) . '</a></li>';
	}
	echo '</ul>';
}

function torantejarat_admin_footer() {
	/* translators: %s: theme version. */
	return sprintf( __( 'توران‌تجارت — نسخه قالب %s', 'torantejarat' ), esc_html( wp_get_theme( get_template() )->get( 'Version' ) ) );
}
