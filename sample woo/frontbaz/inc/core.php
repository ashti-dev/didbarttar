<?php
/**
 * FrontBaz WooCommerce Framework
 * Package     : FrontBaz Store Framework
 * Module      : Core Base & Architecture (هسته پایه و معماری فریم‌ورک)
 * File        : inc/core.php
 * Author      : Sina Samipour (FrontBaz)
 * Description : Central enqueuing and management for base design tokens and theme presets.
 *
 * @package HelloElementorChild
 * @subpackage Inc
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Helper: Return absolute filesystem path for FrontBaz assets (supports frontbaz/assets/ and assets/)
 */
if ( ! function_exists( 'fb_asset_path' ) ) {
	function fb_asset_path( $relative_path ) {
		$relative_path  = ltrim( $relative_path, '/\\' );
		$theme_dir_path = get_stylesheet_directory();

		if ( file_exists( $theme_dir_path . '/frontbaz/' . $relative_path ) ) {
			return $theme_dir_path . '/frontbaz/' . $relative_path;
		}

		return $theme_dir_path . '/' . $relative_path;
	}
}

/**
 * Helper: Return URL for FrontBaz assets (supports frontbaz/assets/ and assets/)
 */
if ( ! function_exists( 'fb_asset_url' ) ) {
	function fb_asset_url( $relative_path ) {
		$relative_path  = ltrim( $relative_path, '/\\' );
		$theme_dir_path = get_stylesheet_directory();
		$theme_dir_uri  = get_stylesheet_directory_uri();

		if ( file_exists( $theme_dir_path . '/frontbaz/' . $relative_path ) ) {
			return $theme_dir_uri . '/frontbaz/' . $relative_path;
		}

		return $theme_dir_uri . '/' . $relative_path;
	}
}


/**
 * عملکرد: بارگذاری فایل استایل هسته دیزاین‌سیستم (fb-base.css)
 * هوک وردپرس: wp_enqueue_scripts با اولویت ۱۵ (قبل از استایل برگه‌ها)
 */
function fb_enqueue_core_assets() {
	if ( ! function_exists( 'is_woocommerce' ) ) {
		return;
	}

	// بررسی لود تنها در صفحات فعال دیزاین‌سیستم فرانت‌باز (سبد خرید، تسویه حساب و حساب کاربری)
	$should_enqueue = is_cart() || is_checkout() || is_account_page();
	if ( apply_filters( 'fb_should_enqueue_core_assets', $should_enqueue ) ) {
		$theme_version  = defined( 'HELLO_ELEMENTOR_CHILD_VERSION' ) ? HELLO_ELEMENTOR_CHILD_VERSION : '2.0.0';
		$theme_dir_uri  = get_stylesheet_directory_uri();
		$theme_dir_path = get_stylesheet_directory();

		$core_css = fb_asset_path( 'assets/css/fb-base.css' );
		if ( file_exists( $core_css ) ) {
			wp_enqueue_style(
				'fb-base-style',
				fb_asset_url( 'assets/css/fb-base.css' ),
				[],
				filemtime( $core_css )
			);
		}
	}
}
add_action( 'wp_enqueue_scripts', 'fb_enqueue_core_assets', 15 );

/**
 * عملکرد: تزریق ویژگی‌های پریست تم و سیاست هاور به تگ بادی
 * هوک وردپرس: body_class
 */
function fb_apply_core_theme_body_classes( $classes ) {
	// بررسی ثابت یا فیلتر پریست فعال
	$theme_preset = defined( 'FB_THEME_PRESET' ) ? FB_THEME_PRESET : 'modern-teal';
	$theme_preset = apply_filters( 'fb_active_theme_preset', $theme_preset );

	// بررسی سیاست هاور (flat یا lift)
	$hover_policy = defined( 'FB_HOVER_POLICY' ) ? FB_HOVER_POLICY : 'flat';
	$hover_policy = apply_filters( 'fb_active_hover_policy', $hover_policy );

	$classes[] = 'fb-preset-' . sanitize_html_class( $theme_preset );
	$classes[] = 'fb-hover-' . sanitize_html_class( $hover_policy );

	return $classes;
}
add_filter( 'body_class', 'fb_apply_core_theme_body_classes' );

/**
 * عملکرد: افزودن data-attribute به تگ بادی جهت سوییچ آنی CSS Variables
 */
function fb_output_core_theme_data_attributes() {
	$theme_preset = defined( 'FB_THEME_PRESET' ) ? FB_THEME_PRESET : 'modern-teal';
	$theme_preset = apply_filters( 'fb_active_theme_preset', $theme_preset );

	$hover_policy = defined( 'FB_HOVER_POLICY' ) ? FB_HOVER_POLICY : 'flat';
	$hover_policy = apply_filters( 'fb_active_hover_policy', $hover_policy );

	?>
	<script type="text/javascript" id="fb-core-theme-init">
		(function() {
			document.documentElement.setAttribute('data-fb-theme', <?php echo wp_json_encode( $theme_preset ); ?>);
			document.documentElement.setAttribute('data-fb-hover', <?php echo wp_json_encode( $hover_policy ); ?>);
		})();
	</script>
	<?php
}
add_action( 'wp_head', 'fb_output_core_theme_data_attributes', 1 );

/* ==========================================================================
   شخصی‌سازی و استانداردسازی پیام‌ها و خطاهای کد تخفیف در ووکامرس
   ۱. جایگزینی «کوپن» با «کد تخفیف» در تمامی پیام‌ها
   ۲. تغییر پیام عدم وجود کوپن به «این کد تخفیف معتبر نیست»
   ۳. جایگزینی «محدودیت» یا «محدودیت استفاده» با «مهلت» یا «مهلت استفاده»
   ========================================================================== */

/**
 * تغییر متن خطاهای کد تخفیف
 */
function fb_customize_coupon_error_message( $err, $err_code = null, $coupon = null ) {
	if ( ! is_string( $err ) ) {
		return $err;
	}

	// خطای عدم وجود کوپن / کد تخفیف نامعتبر
	if ( 105 === $err_code || ( class_exists( 'WC_Coupon' ) && $err_code === WC_Coupon::E_WC_COUPON_NOT_EXIST ) ) {
		return __( 'این کد تخفیف معتبر نیست', 'hello-elementor-child' );
	}

	if ( false !== strpos( $err, 'وجود ندارد' ) || false !== stripos( $err, 'does not exist' ) ) {
		return __( 'این کد تخفیف معتبر نیست', 'hello-elementor-child' );
	}

	// جایگزینی «محدودیت استفاده» یا «محدودیت» با «مهلت»
	$err = str_replace( 'محدودیت استفاده', 'مهلت استفاده', $err );
	$err = str_replace( 'محدودیت', 'مهلت', $err );

	// جایگزینی سراسری «کوپن» با «کد تخفیف»
	$err = str_replace( 'کوپن', 'کد تخفیف', $err );

	return $err;
}
add_filter( 'woocommerce_coupon_error', 'fb_customize_coupon_error_message', 20, 3 );

/**
 * شخصی‌سازی پیام‌های موفقیت‌آمیز کد تخفیف
 */
function fb_customize_coupon_success_message( $msg, $msg_code = null, $coupon = null ) {
	if ( ! is_string( $msg ) ) {
		return $msg;
	}
	return str_replace( 'کوپن', 'کد تخفیف', $msg );
}
add_filter( 'woocommerce_coupon_message', 'fb_customize_coupon_success_message', 20, 3 );

/**
 * فیلتر عمومی اعلانات ووکامرس جهت اعمال یکپارچه «کد تخفیف» و «مهلت»
 */
function fb_filter_woocommerce_coupon_notices( $message ) {
	if ( ! is_string( $message ) ) {
		return $message;
	}

	// بررسی پیام عدم وجود کوپن
	if ( ( false !== strpos( $message, 'کوپن' ) || false !== strpos( $message, 'کد تخفیف' ) ) && ( false !== strpos( $message, 'وجود ندارد' ) || false !== stripos( $message, 'does not exist' ) ) ) {
		return __( 'این کد تخفیف معتبر نیست', 'hello-elementor-child' );
	}

	// جایگزینی محدودیت با مهلت در پیام‌های مربوط به کوپن یا کد تخفیف
	if ( false !== strpos( $message, 'کوپن' ) || false !== strpos( $message, 'کد تخفیف' ) ) {
		$message = str_replace( 'محدودیت استفاده', 'مهلت استفاده', $message );
		$message = str_replace( 'محدودیت', 'مهلت', $message );
	}

	// جایگزینی کوپن با کد تخفیف
	$message = str_replace( 'کوپن', 'کد تخفیف', $message );

	return $message;
}
add_filter( 'woocommerce_add_error', 'fb_filter_woocommerce_coupon_notices', 20 );
add_filter( 'woocommerce_add_notice', 'fb_filter_woocommerce_coupon_notices', 20 );
add_filter( 'woocommerce_add_message', 'fb_filter_woocommerce_coupon_notices', 20 );

/**
 * اصلاح عنوان سطر کد تخفیف در جدول سبد خرید و تسویه حساب
 */
function fb_customize_coupon_cart_label( $label, $coupon = null ) {
	return str_replace( 'کوپن', 'کد تخفیف', $label );
}
add_filter( 'woocommerce_cart_totals_coupon_label', 'fb_customize_coupon_cart_label', 20, 2 );

/**
 * فیلتر ترجمه‌های ووکامرس برای جایگزینی کوپن و محدودیت
 */
function fb_filter_coupon_translations( $translation, $text, $domain ) {
	if ( 'woocommerce' === $domain ) {
		if ( false !== strpos( $translation, 'کوپن' ) ) {
			$translation = str_replace( 'کوپن', 'کد تخفیف', $translation );
		}
		if ( false !== strpos( $translation, 'محدودیت استفاده' ) ) {
			$translation = str_replace( 'محدودیت استفاده', 'مهلت استفاده', $translation );
		}
	}
	return $translation;
}
add_filter( 'gettext', 'fb_filter_coupon_translations', 20, 3 );

/**
 * جلوگیری از لینک‌شدن خودکار شماره تلفن‌ها توسط مرورگرهای وب‌کیت و موبایل
 */
add_action( 'wp_head', function () {
	echo '<meta name="format-detection" content="telephone=no">' . "\n";
}, 1 );
