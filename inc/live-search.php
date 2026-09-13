<?php
/**
 * جستجوی زنده AJAX — دید برتر
 * جستجو همزمان در: محصولات ووکامرس، محصولات سفارشی، خدمات، نوشته‌ها
 */

if (!defined('ABSPATH')) exit;

add_action('wp_ajax_did_live_search', 'didbarttar_live_search_handler');
add_action('wp_ajax_nopriv_did_live_search', 'didbarttar_live_search_handler');

function didbarttar_live_search_handler() {
    // امنیت: بررسی nonce
    check_ajax_referer('didbarttar_nonce', 'nonce');

    $term = isset($_GET['term']) ? sanitize_text_field(wp_unslash($_GET['term'])) : '';

    if (mb_strlen($term) < 2) {
        wp_send_json_success(['results' => [], 'count' => 0, 'term' => $term, 'allUrl' => '']);
    }

    $results = [];

    /* ۱) محصولات ووکامرس */
    if (class_exists('WooCommerce')) {
        $q = new WP_Query([
            'post_type'      => 'product',
            'post_status'    => 'publish',
            'posts_per_page' => 4,
            's'              => $term,
            'no_found_rows'  => true,
        ]);
        while ($q->have_posts()) {
            $q->the_post();
            $product = wc_get_product(get_the_ID());
            $price   = $product ? $product->get_price() : '';
            $results[] = [
                'type'  => 'product',
                'label' => 'محصول',
                'title' => get_the_title(),
                'url'   => get_permalink(),
                'image' => get_the_post_thumbnail_url(get_the_ID(), 'woocommerce_thumbnail') ?: '',
                'meta'  => $price ? number_format_i18n($price) . ' تومان' : 'تماس بگیرید',
                'cat'   => wp_strip_all_tags(wc_get_product_category_list(get_the_ID(), ', ')),
            ];
        }
        wp_reset_postdata();
    }

    /* ۲) محصولات سفارشی دید برتر */
    $q = new WP_Query([
        'post_type'      => 'did_product',
        'post_status'    => 'publish',
        'posts_per_page' => 3,
        's'              => $term,
        'no_found_rows'  => true,
    ]);
    while ($q->have_posts()) {
        $q->the_post();
        $price = get_post_meta(get_the_ID(), '_did_price', true);
        $results[] = [
            'type'  => 'product',
            'label' => 'محصول',
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: '',
            'meta'  => $price ? number_format_i18n($price) . ' تومان' : 'تماس بگیرید',
            'cat'   => wp_strip_all_tags(get_the_term_list(get_the_ID(), 'did_product_cat', '', ', ')),
        ];
    }
    wp_reset_postdata();

    /* ۳) خدمات */
    $q = new WP_Query([
        'post_type'      => 'did_service',
        'post_status'    => 'publish',
        'posts_per_page' => 2,
        's'              => $term,
        'no_found_rows'  => true,
    ]);
    while ($q->have_posts()) {
        $q->the_post();
        $results[] = [
            'type'  => 'service',
            'label' => 'خدمات',
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: '',
            'meta'  => '',
            'cat'   => '',
        ];
    }
    wp_reset_postdata();

    /* ۴) نوشته‌ها و صفحات */
    $q = new WP_Query([
        'post_type'      => ['post', 'page'],
        'post_status'    => 'publish',
        'posts_per_page' => 2,
        's'              => $term,
        'no_found_rows'  => true,
    ]);
    while ($q->have_posts()) {
        $q->the_post();
        $results[] = [
            'type'  => get_post_type(),
            'label' => get_post_type() === 'page' ? 'صفحه' : 'مقاله',
            'title' => get_the_title(),
            'url'   => get_permalink(),
            'image' => get_the_post_thumbnail_url(get_the_ID(), 'thumbnail') ?: '',
            'meta'  => '',
            'cat'   => '',
        ];
    }
    wp_reset_postdata();

    $results = array_slice($results, 0, 7);

    wp_send_json_success([
        'results' => $results,
        'count'   => count($results),
        'term'    => $term,
        'allUrl'  => home_url('/?s=' . rawurlencode($term)),
    ]);
}