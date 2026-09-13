<?php
/**
 * WooCommerce Customization — دید برتر توران تجارت
 * تطبیق کامل با Design System نسخه 1.0.0
 *
 * @package DidBarttar
 * @version 1.1.0
 */

if (!defined('ABSPATH')) exit;

/* ============================================================
   ۱. پشتیبانی و Setup
   ============================================================ */
function didbarttar_woocommerce_support() {
    add_theme_support('woocommerce', [
        'thumbnail_image_width'       => 400,
        'gallery_thumbnail_image_width' => 100,
        'single_image_width'          => 800,
        'product_grid'                => [
            'default_rows'    => 3,
            'min_rows'        => 1,
            'max_rows'        => 8,
            'default_columns' => 3,
            'min_columns'     => 1,
            'max_columns'     => 4,
        ],
    ]);
    add_theme_support('wc-product-gallery-zoom');
    add_theme_support('wc-product-gallery-lightbox');
    add_theme_support('wc-product-gallery-slider');
}
add_action('after_setup_theme', 'didbarttar_woocommerce_support', 20);

// حذف استایل پیش‌فرض ووکامرس (ما استایل سفارشی داریم)
add_filter('woocommerce_enqueue_styles', '__return_empty_array');


/* ============================================================
   ۲. تنظیمات فروشگاه
   ============================================================ */

// تعداد محصولات در هر صفحه
add_filter('loop_shop_per_page', function() { return 12; });

// تعداد ستون‌ها
add_filter('loop_shop_columns', function() { return 3; });

// تعداد تصاویر thumbnail در محصول تکی
add_filter('woocommerce_product_thumbnails_columns', function() { return 4; });

// تعداد محصولات مرتبط
add_filter('woocommerce_output_related_products_args', function($args) {
    $args['posts_per_page'] = 3;
    $args['columns']        = 3;
    return $args;
});

// حذف Upsells یا محدود کردن
add_filter('woocommerce_upsell_display_args', function($args) {
    $args['posts_per_page'] = 3;
    $args['columns']        = 3;
    $args['orderby']        = 'rand';
    return $args;
});


/* ============================================================
   ۳. متن‌ها و ترجمه‌ها (فارسی‌سازی)
   ============================================================ */

// دکمه افزودن به سبد
add_filter('woocommerce_product_add_to_cart_text', function() {
    return 'افزودن به سبد';
});
add_filter('woocommerce_product_single_add_to_cart_text', function() {
    return 'افزودن به سبد خرید';
});

// متن Sale Badge با درصد تخفیف واقعی
add_filter('woocommerce_sale_flash', function($html, $post, $product) {
    if ($product->is_type('simple')) {
        $regular = (float) $product->get_regular_price();
        $sale    = (float) $product->get_sale_price();
        if ($regular > 0 && $sale > 0) {
            $percent = round((($regular - $sale) / $regular) * 100);
            return '<span class="onsale">٪' . $percent . ' تخفیف</span>';
        }
    }
    return '<span class="onsale">تخفیف</span>';
}, 10, 3);

// تب‌های صفحه محصول
add_filter('woocommerce_product_tabs', function($tabs) {
    if (isset($tabs['description'])) {
        $tabs['description']['title'] = 'توضیحات محصول';
    }
    if (isset($tabs['additional_information'])) {
        $tabs['additional_information']['title'] = 'مشخصات فنی';
    }
    if (isset($tabs['reviews'])) {
        $count = get_comments_number();
        $tabs['reviews']['title'] = sprintf('نظرات (%d)', $count);
    }
    return $tabs;
});

// حذف heading پیش‌فرض تب توضیحات
add_filter('woocommerce_product_description_heading', '__return_false');
add_filter('woocommerce_product_additional_information_heading', '__return_false');

// placeholder جستجو
add_filter('woocommerce_product_search_placeholder', function() {
    return 'جستجوی محصول...';
});

// نام عنوان Related Products
add_filter('woocommerce_product_related_products_heading', function() {
    return 'محصولات مرتبط';
});
add_filter('woocommerce_product_upsells_products_heading', function() {
    return 'ممکن است بپسندید';
});

// تغییر متن "In stock" و "Out of stock"
add_filter('woocommerce_get_availability', function($availability, $product) {
    if ($product->is_in_stock()) {
        $availability['availability'] = 'موجود در انبار';
        $availability['class']        = 'in-stock';
    } else {
        $availability['availability'] = 'ناموجود';
        $availability['class']        = 'out-of-stock';
    }
    return $availability;
}, 10, 2);


/* ============================================================
   ۴. واحد پول و فرمت قیمت
   ============================================================ */

// نماد واحد پول
add_filter('woocommerce_currency_symbol', function($symbol, $currency) {
    if (in_array($currency, ['IRR', 'IRT', 'IRAN'], true)) {
        return ' <small>تومان</small>';
    }
    return $symbol;
}, 10, 2);

// موقعیت واحد پول (بعد از قیمت با فاصله)
add_filter('woocommerce_price_trim_zeros', '__return_true');
add_filter('wc_price_args', function($args) {
    $args['price_format'] = '%1$s%2$s'; // عدد + نماد
    return $args;
});


/* ============================================================
   ۵. Breadcrumb ووکامرس (هماهنگ با Breadcrumb قالب)
   ============================================================ */
add_filter('woocommerce_breadcrumb_defaults', function($defaults) {
    return [
        'delimiter'   => ' <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8"><polyline points="15 18 9 12 15 6"/></svg> ',
        'wrap_before' => '<nav class="breadcrumb woocommerce-breadcrumb" aria-label="مسیر"><div class="container"><ol class="bc-list" itemscope itemtype="https://schema.org/BreadcrumbList">',
        'wrap_after'  => '</ol></div></nav>',
        'before'      => '<li class="bc-item" itemprop="itemListElement" itemscope itemtype="https://schema.org/ListItem">',
        'after'       => '<meta itemprop="position" content="%position%"></li>',
        'home'        => 'خانه',
    ];
});


/* ============================================================
   ۶. فیلدهای سفارشی محصول (پنل "دید برتر")
   ============================================================ */

// افزودن تب جدید در صفحه ویرایش محصول
add_filter('woocommerce_product_data_tabs', function($tabs) {
    $tabs['didbarttar'] = [
        'label'    => 'دید برتر',
        'target'   => 'didbarttar_product_data',
        'class'    => [],
        'priority' => 80,
    ];
    return $tabs;
});

// محتوای پنل
add_action('woocommerce_product_data_panels', function() {
    global $post;
    ?>
    <div id="didbarttar_product_data" class="panel woocommerce_options_panel">
        <div class="options_group">
            <?php
            woocommerce_wp_text_input([
                'id'          => '_did_custom_sku',
                'label'       => 'کد سفارشی محصول',
                'placeholder' => 'DB-4000',
                'desc_tip'    => true,
                'description' => 'کد یکتای محصول در سیستم دید برتر',
            ]);
            woocommerce_wp_text_input([
                'id'          => '_did_warranty',
                'label'       => 'مدت گارانتی',
                'placeholder' => '۲۴ ماه گارانتی شرکتی',
                'desc_tip'    => true,
                'description' => 'مثال: ۱۲ ماه گارانتی',
            ]);
            woocommerce_wp_text_input([
                'id'          => '_did_manufacturer',
                'label'       => 'برند / سازنده',
                'placeholder' => 'Dahua, Hikvision...',
            ]);
            woocommerce_wp_text_input([
                'id'          => '_did_delivery_time',
                'label'       => 'زمان ارسال',
                'placeholder' => '۱ تا ۳ روز کاری',
            ]);
            ?>
        </div>
    </div>
    <?php
});

// ذخیره فیلدها
add_action('woocommerce_process_product_meta', function($post_id) {
    $fields = ['_did_custom_sku', '_did_warranty', '_did_manufacturer', '_did_delivery_time'];
    foreach ($fields as $f) {
        if (isset($_POST[$f])) {
            update_post_meta($post_id, $f, sanitize_text_field(wp_unslash($_POST[$f])));
        }
    }
});


/* ============================================================
   ۷. Trust Badges در صفحه محصول تکی
   ============================================================ */
add_action('woocommerce_single_product_summary', 'didbarttar_product_trust_badges', 35);

function didbarttar_product_trust_badges() {
    $badges = [
        [
            'icon'  => '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>',
            'title' => 'ضمانت اصالت کالا',
        ],
        [
            'icon'  => '<rect x="1" y="3" width="15" height="13"/><polygon points="16 8 20 8 23 11 23 16 16 16 16 8"/><circle cx="5.5" cy="18.5" r="2.5"/><circle cx="18.5" cy="18.5" r="2.5"/>',
            'title' => 'ارسال سراسری ایران',
        ],
        [
            'icon'  => '<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>',
            'title' => 'پشتیبانی ۲۴/۷',
        ],
        [
            'icon'  => '<path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/>',
            'title' => 'گارانتی معتبر',
        ],
    ];
    echo '<div class="product-trust-badges">';
    foreach ($badges as $b) {
        echo '<div class="trust-badge">';
        echo '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $b['icon'] . '</svg>';
        echo '<span>' . esc_html($b['title']) . '</span>';
        echo '</div>';
    }
    echo '</div>';
}


/* ============================================================
   ۸. نمایش اطلاعات سفارشی در صفحه محصول
   ============================================================ */
add_action('woocommerce_single_product_summary', 'didbarttar_custom_product_info', 22);

function didbarttar_custom_product_info() {
    global $product;
    $id = $product->get_id();

    $warranty     = get_post_meta($id, '_did_warranty', true);
    $manufacturer = get_post_meta($id, '_did_manufacturer', true);
    $delivery     = get_post_meta($id, '_did_delivery_time', true);
    $sku_custom   = get_post_meta($id, '_did_custom_sku', true);

    if (!$warranty && !$manufacturer && !$delivery && !$sku_custom) return;

    echo '<div class="did-product-meta">';
    if ($sku_custom) {
        echo '<div class="did-meta-row"><span class="did-meta-label">کد محصول:</span><strong>' . esc_html($sku_custom) . '</strong></div>';
    }
    if ($manufacturer) {
        echo '<div class="did-meta-row"><span class="did-meta-label">برند:</span><strong>' . esc_html($manufacturer) . '</strong></div>';
    }
    if ($warranty) {
        echo '<div class="did-meta-row"><span class="did-meta-label">گارانتی:</span><strong>' . esc_html($warranty) . '</strong></div>';
    }
    if ($delivery) {
        echo '<div class="did-meta-row"><span class="did-meta-label">ارسال:</span><strong>' . esc_html($delivery) . '</strong></div>';
    }
    echo '</div>';
}


/* ============================================================
   ۹. AJAX Add to Cart (افزودن به سبد بدون رفرش)
   ============================================================ */
add_filter('woocommerce_loop_add_to_cart_link', function($button, $product) {
    if ($product->is_type('simple') && $product->is_purchasable() && $product->is_in_stock()) {
        $button = sprintf(
            '<a href="%s" data-quantity="1" class="button product_type_simple add_to_cart_button ajax_add_to_cart" data-product_id="%d" data-product_sku="%s" aria-label="%s" rel="nofollow">%s</a>',
            esc_url($product->add_to_cart_url()),
            esc_attr($product->get_id()),
            esc_attr($product->get_sku()),
            esc_attr('افزودن «' . $product->get_name() . '» به سبد'),
            esc_html('افزودن به سبد')
        );
    }
    return $button;
}, 10, 2);

// فعال‌سازی redirect به cart بعد از AJAX (اختیاری)
// add_filter('woocommerce_add_to_cart_fragments', ...);


/* ============================================================
   ۱۰. Mini Cart Fragment (به‌روزرسانی سبد در هدر)
   ============================================================ */
add_filter('woocommerce_add_to_cart_fragments', function($fragments) {
    ob_start();
    ?>
    <span class="cart-count" data-count="<?php echo WC()->cart->get_cart_contents_count(); ?>">
        <?php echo WC()->cart->get_cart_contents_count(); ?>
    </span>
    <?php
    $fragments['span.cart-count'] = ob_get_clean();
    return $fragments;
});


/* ============================================================
   ۱۱. Body Classes برای استایل‌های اختصاصی
   ============================================================ */
add_filter('body_class', function($classes) {
    if (class_exists('WooCommerce')) {
        if (is_shop())                      $classes[] = 'is-shop';
        if (is_product_category())          $classes[] = 'is-product-category';
        if (is_product_tag())               $classes[] = 'is-product-tag';
        if (is_singular('product'))         $classes[] = 'is-single-product';
        if (is_cart())                      $classes[] = 'is-cart';
        if (is_checkout())                  $classes[] = 'is-checkout';
        if (is_account_page())              $classes[] = 'is-account';
        if (wc_post_content_has_shortcode('products')) $classes[] = 'has-products-shortcode';
    }
    return $classes;
});


/* ============================================================
   ۱۲. توابع کمکی برای استفاده در قالب
   ============================================================ */

/**
 * لینک هوشمند فروشگاه
 */
if (!function_exists('did_shop_url')) {
    function did_shop_url() {
        if (function_exists('wc_get_page_id')) {
            $id = wc_get_page_id('shop');
            if ($id && $id > 0) return get_permalink($id);
        }
        return home_url('/');
    }
}

/**
 * لینک هوشمند سبد خرید
 */
if (!function_exists('did_cart_url')) {
    function did_cart_url() {
        return function_exists('wc_get_cart_url') ? wc_get_cart_url() : home_url('/');
    }
}

/**
 * لینک هوشمند Checkout
 */
if (!function_exists('did_checkout_url')) {
    function did_checkout_url() {
        return function_exists('wc_get_checkout_url') ? wc_get_checkout_url() : home_url('/');
    }
}

/**
 * لینک حساب کاربری
 */
if (!function_exists('did_account_url')) {
    function did_account_url() {
        return function_exists('get_permalink') && function_exists('wc_get_page_id')
            ? get_permalink(wc_get_page_id('myaccount'))
            : home_url('/');
    }
}

/**
 * تعداد آیتم‌های سبد خرید
 */
if (!function_exists('did_cart_count')) {
    function did_cart_count() {
        if (class_exists('WooCommerce') && WC()->cart) {
            return WC()->cart->get_cart_contents_count();
        }
        return 0;
    }
}

/**
 * جمع کل سبد خرید
 */
if (!function_exists('did_cart_total')) {
    function did_cart_total() {
        if (class_exists('WooCommerce') && WC()->cart) {
            return WC()->cart->get_cart_total();
        }
        return '۰ تومان';
    }
}


/* ============================================================
   ۱۳. Query بهبودیافته برای صفحه فروشگاه
   ============================================================ */
add_action('woocommerce_product_query', function($q) {
    // فقط محصولات موجود را در صفحه فروشگاه نمایش بده (اختیاری)
    // $q->set('meta_query', [[
    //     'key'     => '_stock_status',
    //     'value'   => 'instock',
    //     'compare' => '=',
    // ]]);

    // محصولات Out of Stock را آخر لیست نمایش بده
    $q->set('orderby', [
        'menu_order' => 'ASC',
        'date'       => 'DESC',
    ]);
});


/* ============================================================
   ۱۴. بهینه‌سازی تصاویر ووکامرس
   ============================================================ */
add_filter('wp_generate_attachment_metadata', function($metadata, $attachment_id) {
    // WebP conversion trigger در صورت وجود
    return $metadata;
}, 10, 2);

// افزودن loading="lazy" به تصاویر محصول
add_filter('woocommerce_get_product_thumbnail', function($image, $size = 'woocommerce_thumbnail') {
    global $product;
    if (!$product) return $image;

    $image_id = $product->get_image_id();
    if ($image_id) {
        $image = wp_get_attachment_image($image_id, $size, false, [
            'loading' => 'lazy',
            'decoding' => 'async',
        ]);
    }
    return $image;
}, 10, 2);


/* ============================================================
   ۱۵. حذف هوک‌های غیرضروری برای سرعت بیشتر
   ============================================================ */
add_action('init', function() {
    // حذف WooCommerce Generator Meta
    remove_action('wp_head', 'wc_generator_tag');

    // حذف WooCommerce styles از صفحات غیرفروشگاه (اختیاری)
    if (!class_exists('WooCommerce')) return;
    if (!is_woocommerce() && !is_cart() && !is_checkout() && !is_account_page()) {
        // استایل‌های ووکامرس فقط در صفحات لازم لود می‌شوند
        add_action('wp_enqueue_scripts', function() {
            wp_dequeue_style('woocommerce-general');
            wp_dequeue_style('woocommerce-layout');
            wp_dequeue_style('woocommerce-smallscreen');
        }, 99);
    }
});


/* ============================================================
   ۱۶. Schema Markup برای محصولات (SEO)
   ============================================================ */
add_action('wp_head', function() {
    if (!class_exists('WooCommerce') || !is_singular('product')) return;

    global $product;
    if (!is_a($product, 'WC_Product')) return;

    $schema = [
        '@context'    => 'https://schema.org',
        '@type'       => 'Product',
        'name'        => get_the_title(),
        'description' => wp_strip_all_tags($product->get_short_description()),
        'image'       => wp_get_attachment_url($product->get_image_id()),
        'sku'         => $product->get_sku(),
        'brand'       => [
            '@type' => 'Brand',
            'name'  => get_post_meta($product->get_id(), '_did_manufacturer', true) ?: 'دید برتر',
        ],
        'offers'      => [
            '@type'         => 'Offer',
            'priceCurrency' => get_woocommerce_currency(),
            'price'         => $product->get_price(),
            'availability'  => $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock',
            'url'           => get_permalink(),
            'seller'        => [
                '@type' => 'Organization',
                'name'  => 'دید برتر توران تجارت',
            ],
        ],
    ];

    // امتیاز محصول
    if ($product->get_average_rating() > 0) {
        $schema['aggregateRating'] = [
            '@type'       => 'AggregateRating',
            'ratingValue' => $product->get_average_rating(),
            'reviewCount' => $product->get_review_count(),
        ];
    }

    echo '<script type="application/ld+json">' . wp_json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
});


/* ============================================================
   ۱۷. پیام خوشامدگویی بعد از اولین خرید (اختیاری)
   ============================================================ */
add_action('woocommerce_thankyou', function($order_id) {
    if (!$order_id) return;
    $order = wc_get_order($order_id);
    if (!$order) return;

    echo '<div class="woocommerce-message" style="border-top-color:var(--color-orange);">';
    echo '<strong>سفارش شما با موفقیت ثبت شد! 🎉</strong><br>';
    echo 'کارشناسان ما به زودی با شما تماس می‌گیرند.';
    echo '</div>';
});


/* ============================================================
   پایان فایل — همه هوک‌ها بارگذاری شدند
   ============================================================ */