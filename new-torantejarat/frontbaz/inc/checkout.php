<?php
/**
 * FrontBaz WooCommerce Framework
 * Package     : FrontBaz Store Framework
 * Module      : Checkout (ماژول تسویه حساب)
 * File        : inc/checkout.php
 * Author      : Sina Samipour (FrontBaz)
 * Description : Custom logic, order review enhancements, and asset loading for Checkout.
 *
 * @package ToranTejarat
 * @subpackage Inc
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * عملکرد: بارگذاری فایل‌های CSS و JS اختصاصی صفحه تسویه حساب
 * هوک وردپرس: wp_enqueue_scripts (اولویت ۳۰ جهت بارگذاری پس از استایل‌های پایه)
 */
function fb_enqueue_checkout_assets() {
	if ( ! function_exists( 'is_checkout' ) || ! is_checkout() || is_order_received_page() ) {
		return;
	}

	$theme_version  = defined( 'FB_THEME_VERSION' ) ? FB_THEME_VERSION : '2.0.0';
	$theme_dir_uri  = get_stylesheet_directory_uri();
	$theme_dir_path = get_stylesheet_directory();

	// بارگذاری فایل استایل تسویه حساب (سازگار با frontbaz/assets/ و assets/)
	$chk_css = function_exists( 'fb_asset_path' ) ? fb_asset_path( 'assets/css/fb-checkout.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-checkout.css' ) ? $theme_dir_path . '/frontbaz/assets/css/fb-checkout.css' : $theme_dir_path . '/assets/css/fb-checkout.css' );
	$chk_css_uri = function_exists( 'fb_asset_url' ) ? fb_asset_url( 'assets/css/fb-checkout.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-checkout.css' ) ? $theme_dir_uri . '/frontbaz/assets/css/fb-checkout.css' : $theme_dir_uri . '/assets/css/fb-checkout.css' );
	if ( file_exists( $chk_css ) ) {
		wp_enqueue_style(
			'fb-checkout-style',
			$chk_css_uri,
			[ 'fb-base-style' ],
			filemtime( $chk_css )
		);
	}

	// بارگذاری اسکریپت جاوااسکریپت تسویه حساب
	$chk_js = function_exists( 'fb_asset_path' ) ? fb_asset_path( 'assets/js/fb-checkout.js' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/js/fb-checkout.js' ) ? $theme_dir_path . '/frontbaz/assets/js/fb-checkout.js' : $theme_dir_path . '/assets/js/fb-checkout.js' );
	$chk_js_uri = function_exists( 'fb_asset_url' ) ? fb_asset_url( 'assets/js/fb-checkout.js' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/js/fb-checkout.js' ) ? $theme_dir_uri . '/frontbaz/assets/js/fb-checkout.js' : $theme_dir_uri . '/assets/js/fb-checkout.js' );
	if ( file_exists( $chk_js ) ) {
		wp_enqueue_script(
			'fb-checkout-script',
			$chk_js_uri,
			[ 'jquery', 'woocommerce' ],
			filemtime( $chk_js ),
			true
		);

		// ارسال آدرس ایجکس وردپرس و کدهای امنیتی نانس (Nonce) به اسکریپت fb-checkout.js
		wp_localize_script(
			'fb-checkout-script',
			'fb_checkout_params',
			[
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'wc_ajax_url'         => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
				'nonce'               => wp_create_nonce( 'fb_checkout_nonce' ),
				'apply_coupon_nonce'  => wp_create_nonce( 'apply-coupon' ),
				'remove_coupon_nonce' => wp_create_nonce( 'remove-coupon' ),
			]
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fb_enqueue_checkout_assets', 30 );

/**
 * عملکرد: به‌روزرسانی آنی تعداد محصول در صفحه تسویه حساب از طریق ایجکس (AJAX)
 * هوک وردپرس: wp_ajax و wc_ajax برای کاربران لاگین‌شده و مهمان
 */
function fb_ajax_update_checkout_qty() {
	check_ajax_referer( 'fb_checkout_nonce', 'security' );

	if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
		wp_send_json_error( [
			'message' => esc_html__( 'سبد خرید در دسترس نیست.', 'torantejarat' ),
		] );
	}

	$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
	$quantity      = isset( $_POST['quantity'] ) ? intval( wp_unslash( $_POST['quantity'] ) ) : 0;

	if ( empty( $cart_item_key ) || ! isset( WC()->cart->get_cart()[ $cart_item_key ] ) ) {
		wp_send_json_error( [
			'message' => esc_html__( 'آیتم مورد نظر در سبد خرید یافت نشد.', 'torantejarat' ),
		] );
	}

	$cart_item = WC()->cart->get_cart()[ $cart_item_key ];
	$_product  = $cart_item['data'];
	$max_qty   = $_product->get_max_purchase_quantity();

	if ( $quantity > 0 && $max_qty > 0 && $quantity > $max_qty ) {
		wp_send_json_error( [
			'message'     => sprintf(
				/* translators: 1: product name, 2: max available quantity */
				esc_html__( 'متأسفانه موجودی محصول «%1$s» حداکثر %2$s عدد است.', 'torantejarat' ),
				$_product->get_name(),
				number_format_i18n( $max_qty )
			),
			'max_qty'     => $max_qty,
			'current_qty' => $cart_item['quantity'],
		] );
	}

	if ( $quantity <= 0 ) {
		WC()->cart->remove_cart_item( $cart_item_key );
	} else {
		WC()->cart->set_quantity( $cart_item_key, $quantity, true );
	}

	WC()->cart->calculate_totals();
	WC()->cart->maybe_set_cart_cookies();

	wp_send_json_success( [
		'cart_count' => WC()->cart->get_cart_contents_count(),
		'is_empty'   => WC()->cart->is_empty(),
	] );

	wp_die();
}
add_action( 'wp_ajax_fb_ajax_update_checkout_qty', 'fb_ajax_update_checkout_qty' );
add_action( 'wp_ajax_nopriv_fb_ajax_update_checkout_qty', 'fb_ajax_update_checkout_qty' );
add_action( 'wc_ajax_fb_ajax_update_checkout_qty', 'fb_ajax_update_checkout_qty' );

/**
 * عملکرد: حذف کدهای تزریقی و مداخله‌گر المنتور پرو در ساختار تسویه حساب
 * هوک وردپرس: woocommerce_before_checkout_form و اکشن wp
 * نکته: فقط کالبک‌های متعلق به المنتور حذف می‌شوند تا اکشن‌های سایر افزونه‌ها حفظ گردند.
 */
function fb_adjust_checkout_order_review_hooks() {
	global $wp_filter;

	$hooks_to_clean = [
		'woocommerce_checkout_before_customer_details',
		'woocommerce_checkout_after_customer_details',
		'woocommerce_checkout_before_order_review_heading',
		'woocommerce_checkout_after_order_review',
	];

	foreach ( $hooks_to_clean as $hook_name ) {
		if ( ! isset( $wp_filter[ $hook_name ] ) ) {
			continue;
		}

		$hook = $wp_filter[ $hook_name ];
		if ( ! empty( $hook->callbacks ) && is_array( $hook->callbacks ) ) {
			foreach ( $hook->callbacks as $priority => $callbacks ) {
				foreach ( $callbacks as $cb_key => $cb_data ) {
					$callback     = $cb_data['function'];
					$is_elementor = false;

					if ( is_array( $callback ) ) {
						$obj_or_class = $callback[0];
						$class_name   = is_object( $obj_or_class ) ? get_class( $obj_or_class ) : (string) $obj_or_class;
						if ( false !== stripos( $class_name, 'elementor' ) ) {
							$is_elementor = true;
						}
					} elseif ( is_string( $callback ) && false !== stripos( $callback, 'elementor' ) ) {
						$is_elementor = true;
					}

					if ( $is_elementor ) {
						$hook->remove_filter( $hook_name, $callback, $priority );
					}
				}
			}
		}
	}

	// حذف فیلتر تفکیک‌کننده درگاه‌های پرداخت در اولویت ۱۵ که باعث تکرار بخش پرداخت می‌شد
	if ( isset( $wp_filter['woocommerce_checkout_order_review'] ) ) {
		$hook = $wp_filter['woocommerce_checkout_order_review'];
		if ( isset( $hook->callbacks[15] ) ) {
			foreach ( $hook->callbacks[15] as $cb ) {
				if ( isset( $cb['function'] ) ) {
					$hook->remove_filter( 'woocommerce_checkout_order_review', $cb['function'], 15 );
				}
			}
		}
	}
}
add_action( 'wp', 'fb_adjust_checkout_order_review_hooks' );
add_action( 'woocommerce_before_checkout_form', 'fb_adjust_checkout_order_review_hooks', 1 );

/**
 * عملکرد: تنظیم فیلدهای صورتحساب، متون راهنما (Placeholders) و اجباری‌سازی شماره موبایل
 * هوک وردپرس: woocommerce_billing_fields با اولویت ۹۹۹۹
 */
function fb_customize_billing_fields( $fields ) {
	if ( isset( $fields['billing_phone'] ) ) {
		$fields['billing_phone']['required']    = true;
		$fields['billing_phone']['label']       = esc_html__( 'شماره موبایل', 'torantejarat' );
		$fields['billing_phone']['placeholder'] = esc_html__( '۰۹۱۲۳۴۵۶۷۸۹', 'torantejarat' );
	}
	if ( isset( $fields['billing_first_name'] ) ) {
		$fields['billing_first_name']['placeholder'] = esc_html__( 'نام خود را وارد کنید...', 'torantejarat' );
	}
	if ( isset( $fields['billing_last_name'] ) ) {
		$fields['billing_last_name']['placeholder'] = esc_html__( 'نام خانوادگی...', 'torantejarat' );
	}
	if ( isset( $fields['billing_email'] ) ) {
		$fields['billing_email']['placeholder'] = esc_html__( 'example@email.com', 'torantejarat' );
	}
	if ( isset( $fields['billing_address_1'] ) ) {
		$fields['billing_address_1']['placeholder'] = esc_html__( 'نام خیابان، کوچه، پلاک، واحد...', 'torantejarat' );
	}
	if ( isset( $fields['billing_postcode'] ) ) {
		$fields['billing_postcode']['placeholder'] = esc_html__( 'کد پستی ۱۰ رقمی بدون خط تیره', 'torantejarat' );
	}

	return $fields;
}
add_filter( 'woocommerce_billing_fields', 'fb_customize_billing_fields', 9999 );

/**
 * عملکرد: تنظیم متون راهنما و وضعیت فیلدهای تسویه حساب
 * هوک وردپرس: woocommerce_checkout_fields با اولویت ۹۹۹۹
 */
function fb_customize_checkout_fields( $fields ) {
	if ( isset( $fields['billing']['billing_phone'] ) ) {
		$fields['billing']['billing_phone']['required']    = true;
		$fields['billing']['billing_phone']['label']       = esc_html__( 'شماره موبایل', 'torantejarat' );
		$fields['billing']['billing_phone']['placeholder'] = esc_html__( '۰۹۱۲۳۴۵۶۷۸۹', 'torantejarat' );
		$fields['billing']['billing_phone']['class']       = [ 'form-row-wide' ];
	}
	if ( isset( $fields['billing']['billing_first_name'] ) ) {
		$fields['billing']['billing_first_name']['placeholder'] = esc_html__( 'نام خود را وارد کنید...', 'torantejarat' );
	}
	if ( isset( $fields['billing']['billing_last_name'] ) ) {
		$fields['billing']['billing_last_name']['placeholder'] = esc_html__( 'نام خانوادگی...', 'torantejarat' );
	}
	if ( isset( $fields['billing']['billing_email'] ) ) {
		$fields['billing']['billing_email']['placeholder'] = esc_html__( 'example@email.com', 'torantejarat' );
		$fields['billing']['billing_email']['class']       = [ 'form-row-wide' ];
	}
	if ( isset( $fields['billing']['billing_address_1'] ) ) {
		$fields['billing']['billing_address_1']['placeholder'] = esc_html__( 'نام خیابان، کوچه، پلاک، واحد...', 'torantejarat' );
	}
	if ( isset( $fields['billing']['billing_postcode'] ) ) {
		$fields['billing']['billing_postcode']['placeholder'] = esc_html__( 'کد پستی ۱۰ رقمی بدون خط تیره', 'torantejarat' );
	}

	return $fields;
}
add_filter( 'woocommerce_checkout_fields', 'fb_customize_checkout_fields', 9999 );

/**
 * عملکرد: اطمینان نهایی از اجباری بودن و عدم درج برچسب (اختیاری) در رندر ویجت تسویه حساب المنتور و ووکامرس
 * هوک وردپرس: woocommerce_form_field_args با اولویت ۹۹۹۹
 */
function fb_customize_checkout_form_field_args( $args, $key, $value = null ) {
	if ( 'billing_phone' === $key ) {
		$args['required'] = true;
		if ( empty( $args['label'] ) || 'تلفن' === trim( $args['label'] ) || 'Phone' === trim( $args['label'] ) ) {
			$args['label'] = esc_html__( 'شماره موبایل', 'torantejarat' );
		}
	}
	return $args;
}
add_filter( 'woocommerce_form_field_args', 'fb_customize_checkout_form_field_args', 9999, 3 );

/**
 * عملکرد: تثبیت وضعیت اجباری بودن فیلد شماره موبایل در تنظیمات آپشن ووکامرس و اسکریپت address-i18n
 */
add_filter( 'option_woocommerce_checkout_phone_field', function() {
	return 'required';
}, 9999 );

add_filter( 'woocommerce_default_address_fields', function( $fields ) {
	if ( isset( $fields['phone'] ) ) {
		$fields['phone']['required'] = true;
	}
	return $fields;
}, 9999 );

add_filter( 'woocommerce_get_country_locale_default', function( $fields ) {
	if ( isset( $fields['phone'] ) ) {
		$fields['phone']['required'] = true;
	}
	return $fields;
}, 9999 );

add_filter( 'woocommerce_get_country_locale_base', function( $fields ) {
	if ( isset( $fields['phone'] ) ) {
		$fields['phone']['required'] = true;
	}
	return $fields;
}, 9999 );

/**
 * عملکرد: حذف عنوان فرعی «مرسوله» از بخش روش‌های ارسال در تسویه حساب
 */
add_filter( 'woocommerce_shipping_package_name', '__return_empty_string', 99 );

/**
 * عملکرد: اطمینان از قرارگیری مبلغ کل در دکمه پرداخت در سمت سرور و فرانت
 */
function fb_custom_order_button_html( $button_html ) {
	if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
		return $button_html;
	}

	$order_button_text = apply_filters( 'fb_checkout_place_order_text', apply_filters( 'woocommerce_order_button_text', __( 'پرداخت', 'torantejarat' ) ) );
	$total_html        = WC()->cart->get_total();

	return sprintf(
		'<button type="submit" class="button alt fb-checkout-submit-btn" name="woocommerce_checkout_place_order" id="place_order" value="%1$s" data-value="%1$s"><span class="fb-btn-text">%1$s</span> <span class="fb-btn-amount">(<span class="fb-checkout-submit-amount">%2$s</span>)</span><span class="fb-btn-loader" aria-hidden="true"></span></button>',
		esc_attr( $order_button_text ),
		$total_html
	);
}
add_filter( 'woocommerce_order_button_html', 'fb_custom_order_button_html', 99 );

/**
 * عملکرد: حذف لینک تکراری [حذف] از انتهای مبلغ تخفیف در صفحه تسویه حساب
 */
function fb_remove_duplicate_coupon_remove_link( $coupon_html, $coupon, $discount_amount_html ) {
	if ( is_checkout() || ( function_exists( 'wp_doing_ajax' ) && wp_doing_ajax() ) ) {
		return $discount_amount_html;
	}
	return $coupon_html;
}
add_filter( 'woocommerce_cart_totals_coupon_html', 'fb_remove_duplicate_coupon_remove_link', 20, 3 );

/**
 * ==========================================================================
 * FrontBaz Studio: مدیریت و بهینه‌سازی فیلدهای تسویه حساب
 * ==========================================================================
 * شخصی‌سازی فیلدهای تسویه حساب و وضعیت اجباری/اختیاری بودن توسط FrontBaz Studio
 */
if ( ! function_exists( 'fb_studio_custom_checkout_fields' ) ) {
	add_filter( 'woocommerce_checkout_fields', 'fb_studio_custom_checkout_fields', 9999 );
	function fb_studio_custom_checkout_fields( $fields ) {
	// حذف فیلدهای غیرضروری فرم
	unset( $fields['billing']['billing_company'] );
	unset( $fields['billing']['billing_address_2'] );
	unset( $fields['billing']['billing_district'] );
	unset( $fields['shipping']['shipping_district'] );
	// تنظیم الزام/اختیاری بودن فیلدها
	$fields['billing']['billing_email']['required'] = true;
	$fields['billing']['billing_phone']['required'] = true;
		return $fields;
	}
}

if ( ! function_exists( 'fb_studio_custom_billing_fields' ) ) {
	add_filter( 'woocommerce_billing_fields', 'fb_studio_custom_billing_fields', 9999 );
	function fb_studio_custom_billing_fields( $fields ) {
	// حذف از فیلدهای آدرس صورتحساب
	unset( $fields['billing_company'] );
	unset( $fields['billing_address_2'] );
	unset( $fields['billing_district'] );
	// الزامی یا اختیاری بودن فیلدها
	$fields['billing_email']['required'] = true;
	$fields['billing_phone']['required'] = true;
		return $fields;
	}
}

if ( ! function_exists( 'fb_studio_custom_form_field_args' ) ) {
	add_filter( 'woocommerce_form_field_args', 'fb_studio_custom_form_field_args', 9999, 3 );
	function fb_studio_custom_form_field_args( $args, $key, $value = null ) {
		if ( 'billing_phone' === $key ) {
			$args['required'] = true;
		}
		return $args;
	}
}
