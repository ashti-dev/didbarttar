<?php
/**
 * Custom Post Types — بدون تداخل با ووکامرس
 * محصولات کاملاً به ووکامرس سپرده شده‌اند.
 */

function didbarttar_register_cpts() {

    // خدمات
    register_post_type('did_service', [
        'labels' => ['name' => 'خدمات', 'singular_name' => 'خدمت'],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-admin-tools',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'rewrite' => ['slug' => 'services'],
    ]);

    // نظرات مشتریان
    register_post_type('did_testimonial', [
        'labels' => ['name' => 'نظرات مشتریان', 'singular_name' => 'نظر مشتری'],
        'public' => false,
        'show_ui' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-format-quote',
        'supports' => ['title', 'editor'],
    ]);

    // پروژه‌ها
    register_post_type('did_project', [
        'labels' => ['name' => 'پروژه‌ها', 'singular_name' => 'پروژه'],
        'public' => true,
        'has_archive' => true,
        'show_in_rest' => true,
        'menu_icon' => 'dashicons-portfolio',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'rewrite' => ['slug' => 'projects'],
    ]);
}
add_action('init', 'didbarttar_register_cpts');