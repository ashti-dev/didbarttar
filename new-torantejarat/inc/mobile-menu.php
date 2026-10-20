<?php
/**
 * Fixed bottom menu for mobile (≤900px): shop, search, home, cart, account.
 * Home sits raised in the center, icon-only. Hidden on single products
 * where the sticky purchase bar owns the bottom edge, and in print.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'body_class', __NAMESPACE__ . '\\torantejarat_mobile_menu_body_class' );

function torantejarat_mobile_menu_body_class( $classes ) {
	if ( ! is_admin() && torantejarat_mobile_menu_visible() ) {
		$classes[] = 'torantejarat-has-bottommenu';
	}
	return $classes;
}

function torantejarat_mobile_menu_visible() {
	if ( is_admin() ) {
		return false;
	}
	if ( torantejarat_woocommerce_available() && is_product() ) {
		return false;
	}
	return true;
}

function torantejarat_mobile_menu() {
	if ( ! torantejarat_mobile_menu_visible() ) {
		return;
	}
	$shop_url = torantejarat_woocommerce_available() && wc_get_page_id( 'shop' ) > 0
		? wc_get_page_permalink( 'shop' )
		: home_url( '/' );
	$account_url = torantejarat_auth_url();
	$cart_count = ( torantejarat_woocommerce_available() && WC()->cart ) ? (int) WC()->cart->get_cart_contents_count() : 0;
	$shop_active = torantejarat_woocommerce_available() && ( is_shop() || is_product_taxonomy() || torantejarat_is_product_search() );
	?>
	<nav class="torantejarat-bottommenu" aria-label="<?php esc_attr_e( 'منوی دسترسی سریع', 'torantejarat' ); ?>">
		<a href="<?php echo esc_url( $shop_url ); ?>"<?php echo $shop_active ? ' class="is-active" aria-current="page"' : ''; ?>>
			<?php torantejarat_icon( 'shop' ); ?>
			<span><?php esc_html_e( 'فروشگاه', 'torantejarat' ); ?></span>
		</a>
		<button type="button" data-menu-search aria-label="<?php esc_attr_e( 'جست‌وجو', 'torantejarat' ); ?>">
			<?php torantejarat_icon( 'search' ); ?>
			<span><?php esc_html_e( 'جست‌وجو', 'torantejarat' ); ?></span>
		</button>
		<a class="torantejarat-bottommenu-home<?php echo is_front_page() ? ' is-active' : ''; ?>" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'خانه', 'torantejarat' ); ?>"<?php echo is_front_page() ? ' aria-current="page"' : ''; ?>>
			<span class="torantejarat-bottommenu-homebtn"><?php torantejarat_icon( 'home' ); ?></span>
		</a>
		<a href="<?php echo esc_url( torantejarat_woocommerce_available() && wc_get_page_id( 'cart' ) > 0 ? wc_get_cart_url() : home_url( '/' ) ); ?>"<?php echo torantejarat_woocommerce_available() && is_cart() ? ' class="is-active" aria-current="page"' : ''; ?>>
			<?php torantejarat_icon( 'cart' ); ?>
			<span><?php esc_html_e( 'سبد خرید', 'torantejarat' ); ?></span>
			<b data-menu-cart-count<?php echo $cart_count > 0 ? '' : ' hidden'; ?>><?php echo esc_html( torantejarat_fa_number( $cart_count ) ); ?></b>
		</a>
		<a href="<?php echo esc_url( $account_url ); ?>"<?php echo torantejarat_woocommerce_available() && is_account_page() ? ' class="is-active" aria-current="page"' : ''; ?>>
			<?php torantejarat_icon( 'user' ); ?>
			<span><?php esc_html_e( 'حساب کاربری', 'torantejarat' ); ?></span>
		</a>
	</nav>
	<?php
}
