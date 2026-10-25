<?php
/**
 * FrontBaz WooCommerce Framework
 * Package     : FrontBaz Store Framework
 * Module      : Thank You & Invoice (ماژول صفحه تشکر و دریافت سفارش)
 * File        : inc/thankyou.php
 * Author      : Sina Samipour (FrontBaz)
 * Description : Custom invoice presentation, asset loading, and shortcode for Thank You page.
 *
 * @package HelloElementorChild
 * @subpackage Inc
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Enqueue dedicated Thank You page & printable invoice CSS
 * بارگذاری فایل‌های استایل اختصاصی صفحه تشکر و فاکتور چاپی
 * Hook: wp_enqueue_scripts (Priority 30)
 */
function fb_enqueue_thankyou_assets() {
	if ( ! function_exists( 'is_order_received_page' ) || ! is_order_received_page() ) {
		return;
	}

	$theme_dir_uri  = get_stylesheet_directory_uri();
	$theme_dir_path = get_stylesheet_directory();

	$ty_css = function_exists( 'fb_asset_path' ) ? fb_asset_path( 'assets/css/fb-thankyou.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-thankyou.css' ) ? $theme_dir_path . '/frontbaz/assets/css/fb-thankyou.css' : $theme_dir_path . '/assets/css/fb-thankyou.css' );
	$ty_css_uri = function_exists( 'fb_asset_url' ) ? fb_asset_url( 'assets/css/fb-thankyou.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-thankyou.css' ) ? $theme_dir_uri . '/frontbaz/assets/css/fb-thankyou.css' : $theme_dir_uri . '/assets/css/fb-thankyou.css' );
	if ( file_exists( $ty_css ) ) {
		wp_enqueue_style(
			'fb-thankyou-style',
			$ty_css_uri,
			[ 'fb-base-style' ],
			filemtime( $ty_css )
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fb_enqueue_thankyou_assets', 30 );

/**
 * Allow shop managers and admins to preview order received invoice without needing an order key
 * قابلیت پیش‌نمایش فاکتور سفارش برای مدیران فروشگاه بدون نیاز به order key
 * Hook: woocommerce_thankyou_order_key
 */
function fb_allow_admin_thankyou_preview( $order_key, $order_id = 0 ) {
	if ( empty( $order_key ) && current_user_can( 'manage_woocommerce' ) && ! empty( $order_id ) ) {
		$order = wc_get_order( $order_id );
		if ( $order ) {
			return $order->get_key();
		}
	}
	return $order_key;
}
add_filter( 'woocommerce_thankyou_order_key', 'fb_allow_admin_thankyou_preview', 10, 2 );

/**
 * Shortcode to render Thank You & Invoice in Elementor or other custom pages
 * شرت‌کد اختصاصی جهت رندر و نمایش صفحه تشکر و فاکتور در المنتور یا صفحات دلخواه
 * Usage: [fb_order_received] or [fb_thankyou]
 * Optional ID usage: [fb_order_received order_id="10153"]
 */
function fb_thankyou_shortcode( $atts ) {
	$atts = shortcode_atts( [
		'order_id' => 0,
	], $atts, 'fb_order_received' );

	$order_id = absint( $atts['order_id'] );

	// If ID not specified in shortcode, read from query vars
	if ( ! $order_id ) {
		global $wp;
		if ( isset( $wp->query_vars['order-received'] ) ) {
			$order_id = absint( $wp->query_vars['order-received'] );
		} elseif ( isset( $_GET['order_id'] ) ) {
			$order_id = absint( $_GET['order_id'] );
		}
	}

	$order = $order_id ? wc_get_order( $order_id ) : false;

	// Ensure stylesheet is loaded when using shortcode inside Elementor
	$theme_dir_uri  = get_stylesheet_directory_uri();
	$theme_dir_path = get_stylesheet_directory();
	$ty_css = function_exists( 'fb_asset_path' ) ? fb_asset_path( 'assets/css/fb-thankyou.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-thankyou.css' ) ? $theme_dir_path . '/frontbaz/assets/css/fb-thankyou.css' : $theme_dir_path . '/assets/css/fb-thankyou.css' );
	$ty_css_uri = function_exists( 'fb_asset_url' ) ? fb_asset_url( 'assets/css/fb-thankyou.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-thankyou.css' ) ? $theme_dir_uri . '/frontbaz/assets/css/fb-thankyou.css' : $theme_dir_uri . '/assets/css/fb-thankyou.css' );
	if ( file_exists( $ty_css ) ) {
		wp_enqueue_style(
			'fb-thankyou-style',
			$ty_css_uri,
			[ 'fb-base-style' ],
			filemtime( $ty_css )
		);
	}

	// Output invoice and thank you template
	ob_start();
	wc_get_template( 'checkout/thankyou.php', [ 'order' => $order ] );
	return ob_get_clean();
}
add_shortcode( 'fb_order_received', 'fb_thankyou_shortcode' );
add_shortcode( 'fb_thankyou', 'fb_thankyou_shortcode' );

/**
 * عملکرد: لغو اتصال جدول و مشخصات پیش‌فرض ووکامرس از اکشن woocommerce_thankyou
 * از آنجا که فرانت‌باز فاکتور اختصاصی و مدرن خود را رندر می‌کند، این جدول پیش‌فرض تکراری برداشته می‌شود
 * در حالی که اکشن‌های ردیابی، پیامک، گوگل آنالیتیکس و سایر افزونه‌ها روی woocommerce_thankyou کاملاً فعال می‌مانند.
 */
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );

