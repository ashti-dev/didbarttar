<?php
/**
 * FrontBaz WooCommerce Framework
 * Package     : FrontBaz Store Framework
 * Module      : My Account (ماژول حساب کاربری)
 * File        : inc/myaccount.php
 * Author      : Sina Samipour (FrontBaz)
 * Description : Custom navigation, tab layouts, and asset loading for My Account.
 *
 * @package ToranTejarat
 * @subpackage Inc
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * عملکرد: بارگذاری فایل استایل اختصاصی صفحه پنل کاربری (حساب من)
 * هوک وردپرس: wp_enqueue_scripts (اولویت ۳۰)
 */
function fb_enqueue_myaccount_assets() {
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}

	$theme_dir_uri  = get_stylesheet_directory_uri();
	$theme_dir_path = get_stylesheet_directory();

	$acc_css = function_exists( 'fb_asset_path' ) ? fb_asset_path( 'assets/css/fb-myaccount.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-myaccount.css' ) ? $theme_dir_path . '/frontbaz/assets/css/fb-myaccount.css' : $theme_dir_path . '/assets/css/fb-myaccount.css' );
	$acc_css_uri = function_exists( 'fb_asset_url' ) ? fb_asset_url( 'assets/css/fb-myaccount.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-myaccount.css' ) ? $theme_dir_uri . '/frontbaz/assets/css/fb-myaccount.css' : $theme_dir_uri . '/assets/css/fb-myaccount.css' );
	if ( file_exists( $acc_css ) ) {
		wp_enqueue_style(
			'fb-myaccount-style',
			$acc_css_uri,
			[ 'fb-base-style' ],
			filemtime( $acc_css )
		);
	}

	$acc_js = function_exists( 'fb_asset_path' ) ? fb_asset_path( 'assets/js/fb-myaccount.js' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/js/fb-myaccount.js' ) ? $theme_dir_path . '/frontbaz/assets/js/fb-myaccount.js' : $theme_dir_path . '/assets/js/fb-myaccount.js' );
	$acc_js_uri = function_exists( 'fb_asset_url' ) ? fb_asset_url( 'assets/js/fb-myaccount.js' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/js/fb-myaccount.js' ) ? $theme_dir_uri . '/frontbaz/assets/js/fb-myaccount.js' : $theme_dir_uri . '/assets/js/fb-myaccount.js' );
	if ( file_exists( $acc_js ) ) {
		wp_enqueue_script(
			'fb-myaccount-script',
			$acc_js_uri,
			[ 'jquery' ],
			filemtime( $acc_js ),
			true
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fb_enqueue_myaccount_assets', 30 );

/**
 * عملکرد: دریافت و تفکیک آمار سفارش‌های مشتری برای نمایش در کارت‌های پیشخوان
 *
 * @param int $user_id شناسه کاربری (پیش‌فرض: کاربر جاری)
 * @return array آرایه‌ای شامل تعداد سفارش‌ها در وضعیت‌های گوناگون
 */
function fb_get_customer_orders_stats( $user_id = 0 ) {
	if ( ! $user_id ) {
		$user_id = get_current_user_id();
	}

	if ( ! $user_id || ! class_exists( 'WooCommerce' ) ) {
		return [
			'processing' => 0,
			'completed'  => 0,
			'cancelled'  => 0,
			'total'      => 0,
		];
	}

	// دریافت لیست شناسه‌های سفارش کاربر
	$orders = wc_get_orders( [
		'customer' => $user_id,
		'limit'    => -1,
		'return'   => 'ids',
	] );

	$stats = [
		'processing' => 0,
		'completed'  => 0,
		'cancelled'  => 0,
		'total'      => count( $orders ),
	];

	if ( ! empty( $orders ) ) {
		foreach ( $orders as $order_id ) {
			$order = wc_get_order( $order_id );
			if ( ! $order ) {
				continue;
			}
			$status = $order->get_status();
			if ( in_array( $status, [ 'processing', 'on-hold', 'pending' ], true ) ) {
				$stats['processing']++;
			} elseif ( 'completed' === $status ) {
				$stats['completed']++;
			} elseif ( in_array( $status, [ 'cancelled', 'failed', 'refunded' ], true ) ) {
				$stats['cancelled']++;
			}
		}
	}

	return $stats;
}

/**
 * عملکرد: شخصی‌سازی و اصلاح عناوین منوی حساب کاربری و حذف بخش «دانلودها»
 * هوک وردپرس: woocommerce_account_menu_items (فیلتر تغییر لینک‌ها و تب‌های سایدبار حساب کاربری)
 */
function fb_customize_account_menu_items( $items ) {
	$polished_items = [];

	foreach ( $items as $endpoint => $label ) {
		switch ( $endpoint ) {
			case 'dashboard':
				$polished_items[ $endpoint ] = esc_html__( 'پیشخوان', 'torantejarat' );
				break;
			case 'orders':
				$polished_items[ $endpoint ] = esc_html__( 'سفارش‌های من', 'torantejarat' );
				break;
			case 'downloads':
				$polished_items[ $endpoint ] = esc_html__( 'دانلودها', 'torantejarat' );
				break;
			case 'edit-address':
				$polished_items[ $endpoint ] = esc_html__( 'آدرس‌های من', 'torantejarat' );
				break;
			case 'payment-methods':
				$polished_items[ $endpoint ] = esc_html__( 'روش‌های پرداخت', 'torantejarat' );
				break;
			case 'edit-account':
				$polished_items[ $endpoint ] = esc_html__( 'اطلاعات حساب', 'torantejarat' );
				break;
			case 'customer-logout':
				$polished_items[ $endpoint ] = esc_html__( 'خروج از حساب', 'torantejarat' );
				break;
			default:
				$polished_items[ $endpoint ] = $label;
				break;
		}
	}

	return $polished_items;
}
add_filter( 'woocommerce_account_menu_items', 'fb_customize_account_menu_items' );

/**
 * عملکرد: لغو اتصال جدول پیش‌فرض ووکامرس از اکشن woocommerce_view_order
 * جهت جلوگیری از نمایش جدول تکراری، در عین حفظ اجرای اکشن‌های سایر افزونه‌ها روی woocommerce_view_order
 */
remove_action( 'woocommerce_view_order', 'woocommerce_order_details_table', 10 );

/**
 * ==========================================================================
 * FrontBaz Studio: مدیریت تب‌های منوی حساب کاربری
 * ==========================================================================
 * شخصی‌سازی و غیرفعال‌سازی تب‌های حساب کاربری ووکامرس توسط FrontBaz Studio
 */
if ( ! function_exists( 'fb_studio_custom_account_menu_items' ) ) {
	add_filter( 'woocommerce_account_menu_items', 'fb_studio_custom_account_menu_items', 999 );
	function fb_studio_custom_account_menu_items( $items ) {
		// غیرفعال‌سازی تب‌های انتخاب‌شده در استودیو
		unset( $items['downloads'] );
		return $items;
	}
}
