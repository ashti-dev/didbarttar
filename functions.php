<?php
/**
 * Did Barttar Theme Functions
 * 
 * توابع اصلی قالب دید برتر توران تجارت
 *
 * @package DidBarttar
 * @version 1.0.0
 */

if (!defined('ABSPATH')) exit;

/* ============================================
   ۱. تعریف ثابت‌ها
   ============================================ */
define('DIDBARTTAR_VERSION', '1.0.0');
define('DIDBARTTAR_DIR', get_template_directory());
define('DIDBARTTAR_URI', get_template_directory_uri());

/* ============================================
   ۲. بارگذاری فایل‌های کمکی (inc/)
   ============================================ */
$inc_files = [
    'theme-setup.php',       // تنظیمات پایه قالب
    'custom-post-types.php', // CPT های سفارشی
    'meta-boxes.php',        // متا باکس‌های سفارشی
    'theme-options.php',     // تنظیمات Customizer
    'security.php',          // لایه امنیتی
    'performance.php',       // بهینه‌سازی سرعت
    'seo.php',               // Schema و Open Graph
    'ajax-handlers.php',     // هندلرهای AJAX
    'breadcrumb.php',        // Breadcrumb Navigation
    'pagination.php',        // Pagination استایل‌دار
    'product-query.php',     // مرتب‌سازی محصولات
    'live-search.php',       // جستجوی زنده AJAX
];

foreach ($inc_files as $file) {
    $path = DIDBARTTAR_DIR . '/inc/' . $file;
    if (file_exists($path)) {
        require_once $path;
    }
}

/* ============================================
   ۳. بارگذاری CSS و JavaScript
   ============================================ */
function didbarttar_enqueue_assets() {
    
    // --- فونت‌ها ---
    wp_enqueue_style(
        'didbarttar-fonts',
        'https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;600;700;900&family=Inter:wght@400;600;700&display=swap',
        [],
        null
    );
    
    // --- استایل اصلی قالب ---
    wp_enqueue_style(
        'didbarttar-style',
        get_stylesheet_uri(),
        ['didbarttar-fonts'],
        DIDBARTTAR_VERSION
    );
    
    // --- استایل ووکامرس (فقط در صورت فعال بودن ووکامرس) ---
    if (class_exists('WooCommerce')) {
        wp_enqueue_style(
            'didbarttar-woocommerce',
            DIDBARTTAR_URI . '/assets/css/woocommerce.css',
            ['didbarttar-style'],
            DIDBARTTAR_VERSION
        );
    }
    
    // --- اسکریپت‌ها ---
    wp_enqueue_script(
        'didbarttar-scroll',
        DIDBARTTAR_URI . '/assets/js/scroll-reveal.js',
        [],
        DIDBARTTAR_VERSION,
        true
    );
    
    wp_enqueue_script(
        'didbarttar-main',
        DIDBARTTAR_URI . '/assets/js/main.js',
        ['didbarttar-scroll'],
        DIDBARTTAR_VERSION,
        true
    );
    
    // انتقال داده به اسکریپت اصلی
    wp_localize_script('didbarttar-main', 'didbarttar', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce'   => wp_create_nonce('didbarttar_nonce'),
        'siteUrl' => home_url('/'),
    ]);
    
    // --- اسکریپت جستجوی زنده ---
    wp_enqueue_script(
        'didbarttar-search',
        DIDBARTTAR_URI . '/assets/js/live-search.js',
        [],
        DIDBARTTAR_VERSION,
        true
    );
    
    wp_localize_script('didbarttar-search', 'didSearch', [
        'ajaxUrl'  => admin_url('admin-ajax.php'),
        'nonce'    => wp_create_nonce('didbarttar_nonce'),
        'minChars' => 2,
    ]);
}
add_action('wp_enqueue_scripts', 'didbarttar_enqueue_assets');

/* ============================================
   ۴. پشتیبانی از ویژگی‌های قالب
   ============================================ */
function didbarttar_theme_support() {
    // ویژگی‌های پایه وردپرس
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', [
        'search-form',
        'comment-form',
        'comment-list',
        'gallery',
        'caption',
    ]);
    add_theme_support('custom-logo', [
        'height'      => 60,
        'width'       => 200,
        'flex-height' => true,
        'flex-width'  => true,
    ]);
    
    // پشتیبانی از ووکامرس
    add_theme_support('woocommerce');
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
    
    // ثبت منوها
    register_nav_menus([
        'primary' => 'منوی اصلی',
        'footer'  => 'منوی فوتر',
    ]);
}
add_action('after_setup_theme', 'didbarttar_theme_support');

/* ============================================
   ۵. ثبت سایدبارها (ویجت‌ها)
   ============================================ */
function didbarttar_widgets_init() {
    
    // سایدبار اصلی (صفحات و وبلاگ)
    register_sidebar([
        'name'          => 'سایدبار اصلی',
        'id'            => 'sidebar-1',
        'description'   => 'سایدبار پیش‌فرض برای صفحات و نوشته‌های وبلاگ',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
    
    // سایدبار فروشگاه (ووکامرس)
    register_sidebar([
        'name'          => 'سایدبار فروشگاه',
        'id'            => 'woocommerce-sidebar',
        'description'   => 'ویجت‌های نمایش داده شده در صفحات فروشگاه، دسته‌بندی و محصول تکی',
        'before_widget' => '<div id="%1$s" class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h3 class="widget-title">',
        'after_title'   => '</h3>',
    ]);
}
add_action('widgets_init', 'didbarttar_widgets_init');

/* ============================================
   ۶. هوک‌های کمکی پایانی
   ============================================ */

// اضافه کردن کلاس body برای استایل‌های اختصاصی
function didbarttar_body_classes($classes) {
    if (class_exists('WooCommerce')) {
        if (is_shop()) $classes[] = 'is-shop';
        if (is_product()) $classes[] = 'is-single-product';
        if (is_cart()) $classes[] = 'is-cart';
        if (is_checkout()) $classes[] = 'is-checkout';
    }
    return $classes;
}
add_filter('body_class', 'didbarttar_body_classes');

// فعال کردن excerpt برای محصولات سفارشی
add_filter('excerpt_length', function() { return 25; });
add_filter('excerpt_more', function() { return '...'; });