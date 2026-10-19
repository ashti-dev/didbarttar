<?php
/**
 * Native data and navigation slots consumed by the source layouts.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_setup() {
	load_theme_textdomain( 'torantejarat' );
	add_theme_support( 'title-tag' );
	add_theme_support( 'post-thumbnails' );
	/* Card frame (~380px, up to ~2x DPR): soft bounding box, never crops the product. */
	add_image_size( 'torantejarat-card', 640, 640, false );
	add_theme_support( 'custom-logo' );
	add_theme_support( 'html5', array( 'caption', 'gallery', 'style', 'script', 'search-form' ) );
	add_theme_support( 'editor-styles' );
	add_editor_style( array( 'assets/css/source/fonts.css', 'assets/css/source/theme.css', 'assets/css/native.css', 'assets/css/editor.css' ) );
	add_post_type_support( 'page', 'excerpt' );

	register_sidebar(
		array(
			'id'            => 'torantejarat-blog-sidebar',
			'name'          => __( 'سایدبار بلاگ', 'torantejarat' ),
			'description'   => __( 'اگر ابزارکی نباشد، بلوک‌های پیش‌فرض (جست‌وجو، دسته‌ها، آخرین مطالب) نمایش داده می‌شود.', 'torantejarat' ),
			'before_widget' => '<div class="torantejarat-widget %2$s">',
			'after_widget'  => '</div>',
			'before_title'  => '<h2>',
			'after_title'   => '</h2>',
		)
	);

	register_nav_menus(
		array(
			'torantejarat-primary'        => __( 'منوی اصلی', 'torantejarat' ),
			'torantejarat-utility'        => __( 'پیوند نوار بالای سایت', 'torantejarat' ),
			'torantejarat-home-services'  => __( 'کارت‌های خدمات خانه — پیوند صفحات موجود', 'torantejarat' ),
			'torantejarat-home-actions'   => __( 'پیوندهای اصلی خانه', 'torantejarat' ),
			'torantejarat-footer-shop'    => __( 'ستون اول پیوندهای پابرگ', 'torantejarat' ),
			'torantejarat-footer-help'    => __( 'ستون دوم پیوندهای پابرگ', 'torantejarat' ),
			'torantejarat-footer-company' => __( 'ستون سوم پیوندهای پابرگ', 'torantejarat' ),
		)
	);
}
