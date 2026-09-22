<?php
/**
 * ShalangBin Theme Functions
 *
 * @package ShalangBin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SHALANGBIN_VERSION', '1.0.1' );
define( 'SHALANGBIN_DIR', get_template_directory() );
define( 'SHALANGBIN_URI', get_template_directory_uri() );

/**
 * Setup theme.
 */
function shalangbin_setup() {
	load_theme_textdomain( 'shalangbin', SHALANGBIN_DIR . '/languages' );

	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'customize-selective-refresh-widgets' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'align-wide' );
	add_theme_support( 'editor-styles' );
	add_editor_style( 'assets/css/theme.css' );
	add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script', 'navigation-widgets' ) );

	// WooCommerce.
	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width' => 600,
		'single_image_width'    => 900,
		'product_grid'          => array(
			'default_columns' => 3,
			'default_rows'    => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-lightbox' );
	add_theme_support( 'wc-product-gallery-slider' );

	// Custom logo & image sizes.
	add_theme_support( 'custom-logo', array(
		'height'      => 56,
		'width'       => 200,
		'flex-height' => true,
		'flex-width'  => true,
	) );
	add_image_size( 'shalangbin-card', 600, 600, true );
	add_image_size( 'shalangbin-wide', 1200, 520, true );

	register_nav_menus( array(
		'primary'         => __( 'منوی اصلی', 'shalangbin' ),
		'topbar'          => __( 'نوار بالا', 'shalangbin' ),
		'footer'          => __( 'منوی فوتر', 'shalangbin' ),
		'footer_shop'     => __( 'فوتر: انتخاب و خرید', 'shalangbin' ),
		'footer_support'  => __( 'فوتر: همراه شما', 'shalangbin' ),
		'footer_about'    => __( 'فوتر: شلنگ‌بین', 'shalangbin' ),
	) );
}
add_action( 'after_setup_theme', 'shalangbin_setup' );

/**
 * Widgets.
 */
function shalangbin_widgets_init() {
	register_sidebar( array(
		'name'          => __( 'ستون کناری فروشگاه', 'shalangbin' ),
		'id'            => 'shop-sidebar',
		'description'   => __( 'ابزارک‌های فیلتر صفحه فروشگاه', 'shalangbin' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'ستون کناری وبلاگ', 'shalangbin' ),
		'id'            => 'blog-sidebar',
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
	register_sidebar( array(
		'name'          => __( 'ابزارک‌های فوتر', 'shalangbin' ),
		'id'            => 'footer-1',
		'description'   => __( 'ابزارک‌های ستون فوتر (در کنار «دسترسی سریع»)', 'shalangbin' ),
		'before_widget' => '<section id="%1$s" class="widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="widget-title">',
		'after_title'   => '</h2>',
	) );
}
add_action( 'widgets_init', 'shalangbin_widgets_init' );

/**
 * Enqueue styles & scripts.
 */
function shalangbin_scripts() {
	// فونت وزیرمتن به‌صورت محلی (assets/fonts) — بدون CDN خارجی.
	// @font-face در assets/css/wp.css تعریف شده و با theme.css بارگذاری می‌شود.

	// Main styles: پایه همیشه، بقیه شرطی (کارایی — CSS استفاده‌نشده لود نشود).
	// theme.css + wp.css + compare.css (ترِی مقایسه در هدر همه صفحات است).
	$base = array( 'theme', 'compare', 'breadcrumbs' );
	foreach ( $base as $handle ) {
		$file = SHALANGBIN_DIR . "/assets/css/{$handle}.css";
		if ( file_exists( $file ) ) {
			wp_enqueue_style( "shalangbin-{$handle}", SHALANGBIN_URI . "/assets/css/{$handle}.css", array(), SHALANGBIN_VERSION );
		}
	}

	$cond = array();
	// کارت محصول/گرید در صفحه اصلی هم استفاده می‌شود.
	if ( is_front_page() || ( function_exists( 'is_woocommerce' ) && is_woocommerce() ) ) {
		$cond[] = 'shop';
	}
	if ( function_exists( 'is_product' ) && is_product() ) {
		$cond[] = 'product';
	}
	if ( function_exists( 'is_cart' ) && ( is_cart() || is_checkout() || ( function_exists( 'is_account_page' ) && is_account_page() ) ) ) {
		$cond[] = 'cart';
	}
	foreach ( $cond as $handle ) {
		$file = SHALANGBIN_DIR . "/assets/css/{$handle}.css";
		if ( file_exists( $file ) ) {
			wp_enqueue_style( "shalangbin-{$handle}", SHALANGBIN_URI . "/assets/css/{$handle}.css", array( 'shalangbin-theme' ), SHALANGBIN_VERSION );
		}
	}

	// WordPress integration layer (loaded last so it can override).
	if ( file_exists( SHALANGBIN_DIR . '/assets/css/wp.css' ) ) {
		wp_enqueue_style( 'shalangbin-wp', SHALANGBIN_URI . '/assets/css/wp.css', array(), SHALANGBIN_VERSION );
	}

	// Theme runtime (front-end logic ported to vanilla JS without file:// logic).
	if ( file_exists( SHALANGBIN_DIR . '/assets/js/theme.js' ) ) {
		wp_enqueue_script( 'shalangbin-theme', SHALANGBIN_URI . '/assets/js/theme.js', array(), SHALANGBIN_VERSION, true );
		wp_localize_script( 'shalangbin-theme', 'ShalangBinData', array(
			'ajaxUrl'   => admin_url( 'admin-ajax.php' ),
			'nonce'     => wp_create_nonce( 'shalangbin-nonce' ),
			'cartUrl'   => function_exists( 'wc_get_cart_url' ) ? wc_get_cart_url() : home_url( '/cart/' ),
			'shopUrl'   => function_exists( 'wc_get_page_permalink' ) ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
			'compareMax' => 3,
			'wcAjaxUrl' => function_exists( 'WC' ) && WC()->ajax_url() ? WC()->ajax_url( '%%endpoint%%' ) : '',
		) );
	}

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	// اسکریپت کاتالوگ/فیلتر فقط در آرشیوهای فروشگاه.
	if ( file_exists( SHALANGBIN_DIR . '/assets/js/catalog.js' )
		&& function_exists( 'is_woocommerce' ) && is_woocommerce()
		&& ! is_product() ) {
		wp_enqueue_script( 'shalangbin-catalog', SHALANGBIN_URI . '/assets/js/catalog.js', array( 'shalangbin-theme' ), SHALANGBIN_VERSION, true );
	}
}
add_action( 'wp_enqueue_scripts', 'shalangbin_scripts' );

/**
 * حذف jquery-migrate (قالب به jQuery قدیمی وابسته نیست).
 */
function shalangbin_remove_jquery_migrate( $scripts ) {
	if ( ! is_admin() && isset( $scripts->registered['jquery'] ) ) {
		$script = $scripts->registered['jquery'];
		if ( $script->deps ) {
			$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
		}
	}
}
add_action( 'wp_default_scripts', 'shalangbin_remove_jquery_migrate' );

/**
 * بارگذاری غیرمسدودکننده: theme.js بدون jQuery، پس defer است.
 */
function shalangbin_defer_scripts( $tag, $handle ) {
	$defer = array( 'shalangbin-theme', 'shalangbin-catalog' );
	if ( in_array( $handle, $defer, true ) && false === strpos( $tag, 'defer' ) ) {
		$tag = str_replace( ' src=', ' defer src=', $tag );
	}
	return $tag;
}
add_filter( 'script_loader_tag', 'shalangbin_defer_scripts', 10, 2 );

/**
 * Preload local font (highest-priority asset above the fold).
 */
function shalangbin_preload_font() {
	$font = SHALANGBIN_DIR . '/assets/fonts/Vazirmatn-Variable.woff2';
	if ( file_exists( $font ) ) {
		printf(
			'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin="anonymous">' . "\n",
			esc_url( SHALANGBIN_URI . '/assets/fonts/Vazirmatn-Variable.woff2' )
		);
	}
}
add_action( 'wp_head', 'shalangbin_preload_font', 2 );

/**
 * Security & privacy hardening.
 * - XML-RPC (هدف حملات brute-force) خاموش.
 * - امضا/متادیتای اضافه وردپرس حذف.
 * - هدرهای امنیتی استاندارد.
 * - ویرایشگر فایل‌ها در پیشخوان خاموش (کاهش ریسک پس از نفوذ).
 */
defined( 'DISALLOW_FILE_EDIT' ) || define( 'DISALLOW_FILE_EDIT', true );
add_filter( 'xmlrpc_enabled', '__return_false' );
remove_action( 'wp_head', 'wp_generator' );
remove_action( 'wp_head', 'rsd_link' );
remove_action( 'wp_head', 'wlwmanifest_link' );
remove_action( 'wp_head', 'wp_shortlink_wp_head' );
remove_action( 'wp_head', 'rest_output_link_wp_head' );
remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );
remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
remove_action( 'admin_print_styles', 'print_emoji_styles' );
add_filter( 'emoji_svg_url', '__return_false' );
add_filter( 'the_generator', '__return_empty_string' );

function shalangbin_security_headers() {
	if ( is_admin() ) {
		return;
	}
	header( 'X-Content-Type-Options: nosniff' );
	header( 'X-Frame-Options: SAMEORIGIN' );
	header( 'Referrer-Policy: strict-origin-when-cross-origin' );
	header( 'Permissions-Policy: camera=(), microphone=(), geolocation=()' );
}
add_action( 'send_headers', 'shalangbin_security_headers' );

/**
 * جلوگیری از شمارش کاربران از طریق ?author=N (کشف نام‌کاربری).
 */
function shalangbin_disable_author_enumeration() {
	if ( ! is_admin() && isset( $_GET['author'] ) && ! current_user_can( 'edit_posts' ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		wp_safe_redirect( home_url( '/' ), 301 );
		exit;
	}
}
add_action( 'init', 'shalangbin_disable_author_enumeration' );

/**
 * Preconnect for Google fonts.
 */
/**
 * Body classes for layout context.
 */
function shalangbin_body_classes( $classes ) {
	$classes[] = 'shalangbin-theme';
	if ( is_rtl() ) {
		$classes[] = 'rtl';
	}
	if ( function_exists( 'is_woocommerce' ) ) {
		if ( is_woocommerce() || is_cart() || is_checkout() || is_account_page() ) {
			$classes[] = 'sb-commerce';
		}
	}
	return $classes;
}
add_filter( 'body_class', 'shalangbin_body_classes' );

/**
 * Excerpt length & more string (Persian).
 */
function shalangbin_excerpt_length() {
	return 24;
}
add_filter( 'excerpt_length', 'shalangbin_excerpt_length' );

function shalangbin_excerpt_more() {
	return ' …';
}
add_filter( 'excerpt_more', 'shalangbin_excerpt_more' );

/**
 * WooCommerce: products per row on shop page.
 */
function shalangbin_loop_columns() {
	return 3;
}
add_filter( 'loop_shop_columns', 'shalangbin_loop_columns' );

/**
 * WooCommerce: strip the default .woocommerce divs. Our archive-product.php and
 * single-product.php provide their own .container.page-shell wrappers, and the
 * cart/checkout/account pages are rendered through page.php (which adds the
 * .wc-page branch), so no extra wrappers are needed anywhere.
 */
function shalangbin_woocommerce_wrappers() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}
	remove_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper', 10 );
	remove_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end', 10 );
}
add_action( 'after_setup_theme', 'shalangbin_woocommerce_wrappers' );

/**
 * Max content width (used by WP core oEmbed & block alignment).
 */
function shalangbin_content_width() {
	$GLOBALS['content_width'] = 1200;
}
add_action( 'after_setup_theme', 'shalangbin_content_width', 0 );

/**
 * Convert digits to Persian numerals (helper used in templates).
 */
function shalangbin_fa_num( $value ) {
	$en = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
	$fa = array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹' );
	return str_replace( $en, $fa, (string) $value );
}

/**
 * Persian date via WP jalali plugin (if available) fallback to standard date.
 */
function shalangbin_date( $format = 'j F Y', $post_id = null ) {
	if ( function_exists( 'wp_date' ) ) {
		return wp_date( $format, get_post_time( 'U', true, $post_id ) );
	}
	return get_the_date( $format, $post_id );
}

/**
 * Breadcrumb trail (simple, SEO-friendly).
 */
function shalangbin_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}
	$sep = '<svg class="icon" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" aria-hidden="true"><path d="m15 18-6-6 6-6"/></svg>';
	echo '<nav class="sb-breadcrumbs" aria-label="' . esc_attr__( 'مسیر صفحه', 'shalangbin' ) . '">';
	echo '<a href="' . esc_url( home_url( '/' ) ) . '">' . esc_html__( 'خانه', 'shalangbin' ) . '</a>';
	echo $sep; // phpcs:ignore
	if ( function_exists( 'is_woocommerce' ) && ( is_shop() || is_product_taxonomy() || is_product() ) ) {
		if ( is_shop() ) {
			echo '<span>' . esc_html( woocommerce_page_title( false ) ) . '</span>';
		} elseif ( is_product() ) {
			echo '<a href="' . esc_url( get_permalink( wc_get_page_id( 'shop' ) ) ) . '">' . esc_html( get_the_title( wc_get_page_id( 'shop' ) ) ) . '</a>' . $sep . '<span>' . esc_html( get_the_title() ) . '</span>'; // phpcs:ignore
		} else {
			$term = get_queried_object();
			echo '<span>' . esc_html( $term->name ) . '</span>';
		}
	} elseif ( is_singular() ) {
		echo '<span>' . esc_html( get_the_title() ) . '</span>';
	} elseif ( is_archive() ) {
		echo '<span>' . esc_html( get_the_archive_title() ) . '</span>';
	} elseif ( is_search() ) {
		echo '<span>' . esc_html__( 'نتایج جست‌وجو', 'shalangbin' ) . '</span>';
	} elseif ( is_404() ) {
		echo '<span>' . esc_html__( 'صفحه پیدا نشد', 'shalangbin' ) . '</span>';
	}
	echo '</nav>';
}

/**
 * SVG icon helper.
 */
function shalangbin_icon( $name, $echo = true ) {
	$paths = array(
		'arrow'   => 'm14 7-5 5 5 5M9 12h12',
		'search'  => 'm21 21-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0',
		'cart'    => 'M3 3h2l3 12h10l3-9H6M9 21h.01M18 21h.01',
		'menu'    => 'M4 6h16M4 12h16M4 18h16',
		'close'   => 'm6 6 12 12M6 18 18 6',
		'compare' => 'M5 4v16M19 4v16M2 8h6M16 16h6M12 4v16',
		'check'   => 'm5 12 4 4L20 5',
		'shield'  => 'm12 3 8 3v6c0 5-8 9-8 9S4 17 4 12V6zM8 12l3 3 5-6',
		'scan'    => 'M8 3H3v5M16 3h5v5M3 16v5h5M21 16v5h-5M7 12h10M12 7v10',
		'tool'    => 'M14 6a5 5 0 0 0-6 6L3 17a3 3 0 0 0 4 4l6-6a5 5 0 0 0 6-6l-3 3-4-4 2-2z',
		'clock'   => 'M12 8v5l3 2M22 12a10 10 0 1 1-20 0 10 10 0 0 1 20 0',
		'phone'   => 'M3 5h18v14H3zM3 5l9 8 9-8',
		'user'    => 'M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2M12 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8',
		'chevron' => 'm9 6 6 6-6 6',
		'play'    => 'm9 5 11 7-11 7z',
		'pipe'    => 'M3 6h9v12h9M3 3v6M21 15v6M6 6v15h15',
		'car'     => 'm4 10 2-6h12l2 6M3 10h18v8H3zM6 18v3M18 18v3M6 13h2M16 13h2',
		'factory' => 'M3 21V10l6-4v4l6-4v15M15 12h6v9H3M18 12V3h3v9M6 15h1M11 15h1',
		'book'    => 'M12 5c-3-2-6-2-10-1v15c4-1 7-1 10 1m0-15c3-2 6-2 10-1v15c-4-1-7-1-10 1V5',
		'headset' => 'M3 13v-1a9 9 0 0 1 18 0v1M3 12h4v8H3zM17 12h4v8h-4z',
		'plus'    => 'M12 5v14M5 12h14',
		'minus'   => 'M5 12h14',
		'trash'   => 'M3 6h18M9 6V3h6v3M6 6l1 15h10l1-15M10 10v7M14 10v7',
		'mail'    => 'M3 5h18v14H3zM3 5l9 8 9-8',
	);
	$d = isset( $paths[ $name ] ) ? $paths[ $name ] : $paths['scan'];
	$svg = '<svg class="icon" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="' . esc_attr( $d ) . '"/></svg>';
	if ( $echo ) {
		echo $svg; // phpcs:ignore WordPress.Security.EscapeOutput
	}
	return $svg;
}

/**
 * TGM-free simple required plugin notice (WooCommerce).
 */
function shalangbin_woocommerce_notice() {
	if ( ! class_exists( 'WooCommerce' ) && current_user_can( 'activate_plugins' ) ) {
		echo '<div class="notice notice-warning"><p><strong>قالب شلنگ‌بین:</strong> برای فعال شدن فروشگاه، افزونه WooCommerce را نصب و فعال کنید.</p></div>';
	}
}
add_action( 'admin_notices', 'shalangbin_woocommerce_notice' );

/**
 * Customizer: phone, social, topbar text.
 */
function shalangbin_customize_register( $wp_customize ) {
	$wp_customize->add_section( 'shalangbin_options', array(
		'title'    => __( 'تنظیمات شلنگ‌بین', 'shalangbin' ),
		'priority' => 30,
	) );

	$wp_customize->add_setting( 'shalangbin_topbar_text', array(
		'default'           => 'تجهیزات تخصصی بازرسی تصویری',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'shalangbin_topbar_text', array(
		'label'   => __( 'متن نوار بالا', 'shalangbin' ),
		'section' => 'shalangbin_options',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'shalangbin_phone', array(
		'default'           => '',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'shalangbin_phone', array(
		'label'   => __( 'شماره تماس (نوار بالا)', 'shalangbin' ),
		'section' => 'shalangbin_options',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'shalangbin_tagline', array(
		'default'           => 'تخصص در دیدنِ نادیدنی‌ها',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'shalangbin_tagline', array(
		'label'   => __( 'شعار زیر لوگو', 'shalangbin' ),
		'section' => 'shalangbin_options',
		'type'    => 'text',
	) );

	$wp_customize->add_setting( 'shalangbin_footer_about', array(
		'default'           => 'ابزار مناسب برای هر نقطه دور از دسترس. انتخاب، مقایسه و شناخت دوربین‌های بازرسی.',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'shalangbin_footer_about', array(
		'label'   => __( 'متن معرفی فوتر', 'shalangbin' ),
		'section' => 'shalangbin_options',
		'type'    => 'textarea',
	) );

	$wp_customize->add_setting( 'shalangbin_footer_note', array(
		'default'           => 'قیمت‌ها و موجودی پس از تأیید نهایی اعلام می‌شود.',
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'shalangbin_footer_note', array(
		'label'   => __( 'یادداشت فوتر', 'shalangbin' ),
		'section' => 'shalangbin_options',
		'type'    => 'textarea',
	) );
}
add_action( 'customize_register', 'shalangbin_customize_register' );

/**
 * URL of a page created from one of the bundled page-templates (by slug).
 */
function shalangbin_page_url( $slug ) {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/' . sanitize_file_name( $slug ) . '.php',
		'number'     => 1,
	) );
	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}
	return home_url( '/' );
}

/**
 * URL of the comparison page (page template "مقایسه دستگاه‌ها").
 */
function shalangbin_compare_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/compare.php',
		'number'     => 1,
	) );
	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}
	return home_url( '/' );
}

/**
 * URL of the contact/consultation page (page template "ارتباط با ما").
 */
function shalangbin_contact_url() {
	$contact_id = get_option( 'shalangbin_contact_page' );
	if ( $contact_id && get_post( $contact_id ) ) {
		return get_permalink( $contact_id );
	}
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/contact.php',
		'number'     => 1,
	) );
	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}
	return home_url( '/' );
}

/**
 * URL of the buying-guide page (page template «راهنمای انتخاب دستگاه»).
 */
function shalangbin_quiz_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/quiz.php',
		'number'     => 1,
	) );
	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}
	return home_url( '/' );
}

/**
 * URL of the shop archive (WooCommerce shop page).
 */
function shalangbin_shop_url() {
	return function_exists( 'wc_get_page_id' ) ? get_permalink( wc_get_page_id( 'shop' ) ) : home_url( '/' );
}

/**
 * URL of the rental page (page template «اجاره دستگاه»).
 */
function shalangbin_rent_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/rent.php',
		'number'     => 1,
	) );
	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}
	return home_url( '/' );
}

/**
 * URL of the repairs page (page template «تعمیرات»).
 */
function shalangbin_repairs_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/repairs.php',
		'number'     => 1,
	) );
	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}
	return home_url( '/' );
}

/**
 * URL of the academy page (page template «آکادمی»).
 */
function shalangbin_academy_url() {
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/academy.php',
		'number'     => 1,
	) );
	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}
	return home_url( '/' );
}

/**
 * URL of the blog/magazine (posts page or blog template).
 */
function shalangbin_blog_url() {
	$posts_page = get_option( 'page_for_posts' );
	if ( $posts_page ) {
		return get_permalink( $posts_page );
	}
	$pages = get_pages( array(
		'meta_key'   => '_wp_page_template',
		'meta_value' => 'page-templates/blog.php',
		'number'     => 1,
	) );
	if ( $pages ) {
		return get_permalink( $pages[0]->ID );
	}
	return home_url( '/' );
}

/**
 * URL of the first published product (used by hero CTA on the front page).
 */
function shalangbin_first_product_url() {
	if ( ! function_exists( 'wc_get_products' ) ) {
		return home_url( '/shop/' );
	}
	$products = wc_get_products( array(
		'limit'   => 1,
		'status'  => 'publish',
		'orderby' => 'date',
		'order'   => 'ASC',
	) );
	if ( $products ) {
		return get_permalink( $products[0]->get_id() );
	}
	return home_url( '/shop/' );
}

/**
 * URL for shop filter navigation (all products or a product_cat term archive).
 */
function shalangbin_shop_filter_url( $slug = '' ) {
	if ( $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( $term && ! is_wp_error( $term ) ) {
			return get_term_link( $term );
		}
	}
	return get_post_type_archive_link( 'product' );
}

/**
 * Native shop sidebar filters (category radios), matching the reference design.
 */
function shalangbin_shop_filters() {
	$current      = get_queried_object();
	$current_slug = ( $current instanceof WP_Term && 'product_cat' === $current->taxonomy ) ? $current->slug : '';

	$uncategorized = get_term_by( 'slug', 'uncategorized', 'product_cat' );
	$terms         = get_terms( array(
		'taxonomy'   => 'product_cat',
		'hide_empty' => false,
		'exclude'    => $uncategorized ? array( $uncategorized->term_id ) : array(),
	) );

	$total = (int) wp_count_posts( 'product' )->publish;
	?>
	<fieldset>
		<legend><?php esc_html_e( 'کاربرد دستگاه', 'shalangbin' ); ?></legend>
		<label>
			<input type="radio" name="category" value="" <?php checked( '', $current_slug ); ?> data-filter-nav data-url="<?php echo esc_url( shalangbin_shop_filter_url( '' ) ); ?>">
			<?php esc_html_e( 'همه تجهیزات', 'shalangbin' ); ?>
			<small><?php echo esc_html( shalangbin_fa_num( $total ) ); ?></small>
		</label>
		<?php foreach ( $terms as $term ) : ?>
			<label>
				<input type="radio" name="category" value="<?php echo esc_attr( $term->slug ); ?>" <?php checked( $term->slug, $current_slug ); ?> data-filter-nav data-url="<?php echo esc_url( shalangbin_shop_filter_url( $term->slug ) ); ?>">
				<?php echo esc_html( $term->name ); ?>
				<small><?php echo esc_html( shalangbin_fa_num( (int) $term->count ) ); ?></small>
			</label>
		<?php endforeach; ?>
	</fieldset>
	<?php
}

/**
 * منوی اصلی — عین طرح اصلی (build.mjs): لینک‌های تختِ <a>، بدون ul/li و بدون ساب‌منو.
 * Walker فقط <a> خروجی می‌دهد؛ آیتم‌های سطح اول فهرست وردپرس را چاپ می‌کند.
 */
class ShalangBin_Flat_Nav_Walker extends Walker_Nav_Menu {
	public function start_lvl( &$output, $depth = 0, $args = null ) {}
	public function end_lvl( &$output, $depth = 0, $args = null ) {}

	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$extra   = in_array( 'current-menu-item', $classes, true ) ? ' aria-current="page"' : '';
		$title   = apply_filters( 'the_title', $item->title, $item->ID );
		$output .= '<a href="' . esc_url( $item->url ) . '"' . $extra . '>' . esc_html( $title ) . '</a>';
	}

	public function end_el( &$output, $item, $depth = 0, $args = null ) {}
}

/**
 * Fallback منوی اصلی — ساختار تختِ طرح اصلی با لینک‌های واقعی وردپرس.
 */
function shalangbin_menu_fallback() {
	$links = array(
		array( shalangbin_shop_url(), 'فروشگاه' ),
		array( shalangbin_quiz_url(), 'راهنمای انتخاب' ),
		array( shalangbin_rent_url(), 'اجاره دستگاه' ),
		array( shalangbin_repairs_url(), 'تعمیرات' ),
		array( shalangbin_academy_url(), 'آکادمی' ),
		array( shalangbin_contact_url(), 'ارتباط با ما' ),
		array( shalangbin_blog_url(), 'مجله شلنگ‌بین' ),
	);
	foreach ( $links as $link ) {
		echo '<a href="' . esc_url( $link[0] ) . '">' . esc_html( $link[1] ) . '</a>';
	}
}
