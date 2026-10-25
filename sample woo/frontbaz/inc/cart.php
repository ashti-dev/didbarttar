<?php
/**
 * FrontBaz WooCommerce Framework
 * Package     : FrontBaz Store Framework
 * Module      : Cart (ماژول سبد خرید)
 * File        : inc/cart.php
 * Author      : Sina Samipour (FrontBaz)
 * Description : Custom logic, quantity updates, and asset loading for Cart.
 *
 * @package HelloElementorChild
 * @subpackage Inc
 * @version 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * عملکرد: بارگذاری فایل‌های استایل و اسکریپت تعاملی صفحه سبد خرید
 * هوک وردپرس: wp_enqueue_scripts (اولویت ۳۰)
 */
function fb_enqueue_cart_assets() {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() || is_checkout() ) {
		return;
	}

	$theme_version  = defined( 'HELLO_ELEMENTOR_CHILD_VERSION' ) ? HELLO_ELEMENTOR_CHILD_VERSION : '1.0.0';
	$theme_dir_uri  = get_stylesheet_directory_uri();
	$theme_dir_path = get_stylesheet_directory();

	// بارگذاری استایل اختصاصی سبد خرید
	$cart_css = function_exists( 'fb_asset_path' ) ? fb_asset_path( 'assets/css/fb-cart.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-cart.css' ) ? $theme_dir_path . '/frontbaz/assets/css/fb-cart.css' : $theme_dir_path . '/assets/css/fb-cart.css' );
	$cart_css_uri = function_exists( 'fb_asset_url' ) ? fb_asset_url( 'assets/css/fb-cart.css' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/css/fb-cart.css' ) ? $theme_dir_uri . '/frontbaz/assets/css/fb-cart.css' : $theme_dir_uri . '/assets/css/fb-cart.css' );
	if ( file_exists( $cart_css ) ) {
		wp_enqueue_style(
			'fb-cart-style',
			$cart_css_uri,
			[ 'fb-base-style' ],
			filemtime( $cart_css )
		);
	}

	// بارگذاری جاوااسکریپت کنترلرهای افزایش/کاهش تعداد و اعمال کوپن تخفیف
	$cart_js = function_exists( 'fb_asset_path' ) ? fb_asset_path( 'assets/js/fb-cart.js' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/js/fb-cart.js' ) ? $theme_dir_path . '/frontbaz/assets/js/fb-cart.js' : $theme_dir_path . '/assets/js/fb-cart.js' );
	$cart_js_uri = function_exists( 'fb_asset_url' ) ? fb_asset_url( 'assets/js/fb-cart.js' ) : ( file_exists( $theme_dir_path . '/frontbaz/assets/js/fb-cart.js' ) ? $theme_dir_uri . '/frontbaz/assets/js/fb-cart.js' : $theme_dir_uri . '/assets/js/fb-cart.js' );
	if ( file_exists( $cart_js ) ) {
		wp_enqueue_script(
			'fb-cart-script',
			$cart_js_uri,
			[ 'jquery', 'woocommerce' ],
			filemtime( $cart_js ),
			true
		);

		// ارسال پارامترها و کدهای نانس امنیتی به اسکریپت fb-cart.js
		wp_localize_script(
			'fb-cart-script',
			'fb_cart_params',
			[
				'ajax_url'            => admin_url( 'admin-ajax.php' ),
				'wc_ajax_url'         => class_exists( 'WC_AJAX' ) ? WC_AJAX::get_endpoint( '%%endpoint%%' ) : '',
				'nonce'               => wp_create_nonce( 'fb_cart_nonce' ),
				'apply_coupon_nonce'  => wp_create_nonce( 'apply-coupon' ),
				'remove_coupon_nonce' => wp_create_nonce( 'remove-coupon' ),
				'cart_url'            => wc_get_cart_url(),
				'checkout_url'        => wc_get_checkout_url(),
			]
		);
	}
}
add_action( 'wp_enqueue_scripts', 'fb_enqueue_cart_assets', 30 );

/**
 * عملکرد: به‌روزرسانی آنی تعداد هر محصول در صفحه سبد خرید با ایجکس
 * هوک وردپرس: wp_ajax و wc_ajax جهت عملکرد سریع بدون رفرش صفحه
 * خروجی: فرمت JSON شامل قیمت جزء جدید سطر و جدول محاسبه کل سبد خرید
 */
function fb_ajax_update_cart_qty() {
	check_ajax_referer( 'fb_cart_nonce', 'security' );

	if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
		wp_send_json_error( [
			'message' => esc_html__( 'سبد خرید در دسترس نیست.', 'hello-elementor-child' ),
		] );
	}

	$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';
	$quantity      = isset( $_POST['quantity'] ) ? intval( wp_unslash( $_POST['quantity'] ) ) : 0;

	if ( empty( $cart_item_key ) || ! isset( WC()->cart->get_cart()[ $cart_item_key ] ) ) {
		wp_send_json_error( [
			'message' => esc_html__( 'آیتم مورد نظر در سبد خرید یافت نشد.', 'hello-elementor-child' ),
		] );
	}

	$cart_item = WC()->cart->get_cart()[ $cart_item_key ];
	$_product  = $cart_item['data'];
	$max_qty   = $_product->get_max_purchase_quantity();

	if ( $quantity > 0 && $max_qty > 0 && $quantity > $max_qty ) {
		wp_send_json_error( [
			'message'     => sprintf(
				/* translators: 1: product name, 2: max available quantity */
				esc_html__( 'متأسفانه موجودی محصول «%1$s» حداکثر %2$s عدد است.', 'hello-elementor-child' ),
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

	// دریافت HTML به‌روزرسانی‌شده بخش صورت‌حساب سبد خرید
	ob_start();
	woocommerce_cart_totals();
	$cart_totals_html = ob_get_clean();

	$item_subtotal = '';
	if ( isset( WC()->cart->get_cart()[ $cart_item_key ] ) ) {
		$cart_item  = WC()->cart->get_cart()[ $cart_item_key ];
		$_product   = $cart_item['data'];
		$is_on_sale = false;
		$reg_price  = 0;
		$sale_price = 0;

		if ( $_product && $_product->is_on_sale() ) {
			$raw_reg  = (float) $_product->get_regular_price();
			$raw_sale = (float) $_product->get_price();
			if ( $raw_reg > $raw_sale ) {
				$is_on_sale = true;
				$reg_price  = wc_get_price_to_display( $_product, array( 'price' => $raw_reg ) );
				$sale_price = wc_get_price_to_display( $_product );
			}
		}

		if ( $is_on_sale ) {
			$reg_subtotal  = $reg_price * $cart_item['quantity'];
			$sale_subtotal = $sale_price * $cart_item['quantity'];

			$item_subtotal = '<div class="fb-cart-item-price-wrap fb-cart-subtotal-wrap">'
				. '<del class="fb-price-old">' . esc_html( number_format_i18n( $reg_subtotal ) ) . '</del>'
				. '<ins class="fb-price-current">' . wc_price( $sale_subtotal ) . '</ins>'
				. '</div>';
		} else {
			$item_subtotal = WC()->cart->get_product_subtotal( $_product, $cart_item['quantity'] );
		}
	}

	wp_send_json_success( [
		'cart_count'       => WC()->cart->get_cart_contents_count(),
		'is_empty'         => WC()->cart->is_empty(),
		'cart_item_key'    => $cart_item_key,
		'item_subtotal'    => $item_subtotal,
		'cart_totals_html' => $cart_totals_html,
	] );

	wp_die();
}
add_action( 'wp_ajax_fb_ajax_update_cart_qty', 'fb_ajax_update_cart_qty' );
add_action( 'wp_ajax_nopriv_fb_ajax_update_cart_qty', 'fb_ajax_update_cart_qty' );
add_action( 'wc_ajax_fb_ajax_update_cart_qty', 'fb_ajax_update_cart_qty' );

/**
 * عملکرد: حذف آنی یک سطر از سبد خرید از طریق ایجکس (بدون بارگذاری مجدد برگه)
 * هوک وردپرس: wp_ajax و wc_ajax
 */
function fb_ajax_remove_cart_item() {
	check_ajax_referer( 'fb_cart_nonce', 'security' );

	if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart ) {
		wp_send_json_error( [
			'message' => esc_html__( 'سبد خرید در دسترس نیست.', 'hello-elementor-child' ),
		] );
	}

	$cart_item_key = isset( $_POST['cart_item_key'] ) ? sanitize_text_field( wp_unslash( $_POST['cart_item_key'] ) ) : '';

	if ( empty( $cart_item_key ) || ! isset( WC()->cart->get_cart()[ $cart_item_key ] ) ) {
		wp_send_json_error( [
			'message' => esc_html__( 'آیتم مورد نظر در سبد خرید یافت نشد.', 'hello-elementor-child' ),
		] );
	}

	WC()->cart->remove_cart_item( $cart_item_key );
	WC()->cart->calculate_totals();
	WC()->cart->maybe_set_cart_cookies();

	ob_start();
	woocommerce_cart_totals();
	$cart_totals_html = ob_get_clean();

	wp_send_json_success( [
		'cart_count'       => WC()->cart->get_cart_contents_count(),
		'is_empty'         => WC()->cart->is_empty(),
		'cart_item_key'    => $cart_item_key,
		'cart_totals_html' => $cart_totals_html,
	] );

	wp_die();
}
add_action( 'wp_ajax_fb_ajax_remove_cart_item', 'fb_ajax_remove_cart_item' );
add_action( 'wp_ajax_nopriv_fb_ajax_remove_cart_item', 'fb_ajax_remove_cart_item' );
add_action( 'wc_ajax_fb_ajax_remove_cart_item', 'fb_ajax_remove_cart_item' );

/**
 * عملکرد: غیرفعال‌سازی محاسبه و نمایش گزینه‌های حمل و نقل در برگه سبد خرید
 * انتخاب روش ارسال به صورت کاملاً دقیق در برگه تسویه حساب (Checkout) و پس از ثبت استان و شهر انجام می‌شود.
 */
function fb_disable_shipping_calc_on_cart( $show_shipping ) {
	if ( is_cart() || ( defined( 'DOING_AJAX' ) && DOING_AJAX && isset( $_POST['action'] ) && in_array( $_POST['action'], [ 'fb_ajax_update_cart_qty', 'fb_ajax_remove_cart_item' ], true ) ) ) {
		return false;
	}
	return $show_shipping;
}
add_filter( 'woocommerce_cart_ready_to_calc_shipping', 'fb_disable_shipping_calc_on_cart', 99 );

/**
 * عملکرد: نمایش دکمه فیکس چسبان سبد خرید در پایین صفحه موبایل (متصل به بالاترین سطح DOM)
 */
function fb_render_mobile_cart_bar() {
	if ( ! function_exists( 'is_cart' ) || ! is_cart() || is_checkout() ) {
		return;
	}
	if ( ! class_exists( 'WooCommerce' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		return;
	}
	?>
	<!-- Mobile Sticky Bottom Cart Bar (Top-level Body Child) -->
	<div class="fb-mobile-cart-bar">
		<div class="fb-mobile-cart-bar-content">
			<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="fb-btn-primary fb-mobile-cart-btn">
				<span class="fb-btn-text"><?php echo esc_html( apply_filters( 'fb_cart_proceed_to_checkout_text', __( 'ثبت سفارش', 'hello-elementor-child' ) ) ); ?></span>
				<span class="fb-btn-loader" aria-hidden="true"></span>
			</a>
			<div class="fb-mobile-cart-price-wrap">
				<span class="fb-mobile-cart-price-label"><?php esc_html_e( 'مبلغ قابل پرداخت:', 'hello-elementor-child' ); ?></span>
				<span class="fb-mobile-cart-price-amount fb-cart-submit-amount"><?php wc_cart_totals_order_total_html(); ?></span>
			</div>
		</div>
	</div>
	<?php
}
add_action( 'wp_footer', 'fb_render_mobile_cart_bar', 25 );

/**
 * عملکرد: دریافت اطلاعات و وضعیت ارسال رایگان ووکامرس جهت نمایش باکس و نوار پیشرفت در سبد خرید
 *
 * @return array|false اطلاعات محاسبه یا false در صورت غیرفعال بودن
 */
function fb_get_free_shipping_info() {
	if ( ! function_exists( 'WC' ) || ! WC()->cart || WC()->cart->is_empty() ) {
		return false;
	}

	$has_free_shipping = false;
	$min_amount        = null;
	$ignore_discounts  = false;

	// 1. بررسی پکیج‌های حمل‌ونقل و مناطق فعال (Shipping Zones)
	$packages = WC()->shipping()->get_packages();
	if ( empty( $packages ) && method_exists( WC()->cart, 'get_shipping_packages' ) ) {
		$packages = WC()->cart->get_shipping_packages();
	}

	if ( ! empty( $packages ) ) {
		foreach ( $packages as $package ) {
			$zone = WC_Shipping_Zones::get_zone_matching_package( $package );
			if ( ! $zone ) {
				$zone = new WC_Shipping_Zone( 0 );
			}
			$methods = $zone->get_shipping_methods( true );
			foreach ( $methods as $method ) {
				if ( 'free_shipping' === $method->id && 'yes' === $method->enabled ) {
					$has_free_shipping = true;
					$req               = $method->get_option( 'requires' );
					$raw_min           = (float) $method->get_option( 'min_amount' );

					if ( in_array( $req, [ 'min_amount', 'either', 'both' ], true ) || ( empty( $req ) && $raw_min > 0 ) ) {
						if ( null === $min_amount || $raw_min < $min_amount ) {
							$min_amount       = $raw_min;
							$ignore_discounts = ( 'yes' === $method->get_option( 'ignore_discounts' ) );
						}
					} elseif ( empty( $req ) || 'coupon' === $req ) {
						if ( null === $min_amount ) {
							$min_amount = 0.0;
						}
					}
				}
			}
		}
	}

	// 2. در صورت عدم تطبیق پکیج (قبل از انتخاب آدرس)، بررسی تمامی مناطق ارسال
	if ( ! $has_free_shipping ) {
		$all_zones   = WC_Shipping_Zones::get_zones();
		$all_zones[] = [ 'shipping_methods' => ( new WC_Shipping_Zone( 0 ) )->get_shipping_methods( true ) ];

		foreach ( $all_zones as $z ) {
			$methods = isset( $z['shipping_methods'] ) ? $z['shipping_methods'] : [];
			foreach ( $methods as $method ) {
				if ( 'free_shipping' === $method->id && 'yes' === $method->enabled ) {
					$has_free_shipping = true;
					$req               = $method->get_option( 'requires' );
					$raw_min           = (float) $method->get_option( 'min_amount' );

					if ( in_array( $req, [ 'min_amount', 'either', 'both' ], true ) || ( empty( $req ) && $raw_min > 0 ) ) {
						if ( null === $min_amount || ( $raw_min > 0 && $raw_min < $min_amount ) ) {
							$min_amount       = $raw_min;
							$ignore_discounts = ( 'yes' === $method->get_option( 'ignore_discounts' ) );
						}
					} elseif ( empty( $req ) || 'coupon' === $req ) {
						if ( null === $min_amount ) {
							$min_amount = 0.0;
						}
					}
				}
			}
		}
	}

	// فیلتر اختصاصی جهت شخصی‌سازی سقف ارسال رایگان
	$min_amount = apply_filters( 'fb_free_shipping_min_amount', $min_amount );

	if ( ! $has_free_shipping && null === $min_amount ) {
		return false;
	}

	if ( null !== $min_amount && $min_amount <= 0 ) {
		return [
			'enabled'    => true,
			'min_amount' => 0,
			'current'    => (float) WC()->cart->get_displayed_subtotal(),
			'remaining'  => 0,
			'percent'    => 100,
			'is_free'    => true,
		];
	}

	// محاسبه مبلغ مبنای سبد خرید بر اساس تنظیم ignore_discounts
	if ( $ignore_discounts ) {
		$current_amount = (float) WC()->cart->get_subtotal();
	} else {
		$current_amount = (float) WC()->cart->get_subtotal() - (float) WC()->cart->get_discount_total();
	}

	$is_free   = ( $current_amount >= $min_amount );
	$remaining = max( 0, $min_amount - $current_amount );
	$percent   = $min_amount > 0 ? min( 100, max( 0, round( ( $current_amount / $min_amount ) * 100 ) ) ) : 100;

	return [
		'enabled'    => true,
		'min_amount' => $min_amount,
		'current'    => $current_amount,
		'remaining'  => $remaining,
		'percent'    => $percent,
		'is_free'    => $is_free,
	];
}


