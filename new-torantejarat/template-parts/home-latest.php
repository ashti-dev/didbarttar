<?php
/**
 * Home bottom slider — latest products ("تازه رسیده‌ها").
 * Ordered by date DESC; reuses the shared product-slider engine.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! torantejarat_woocommerce_available() ) {
	return;
}

/* The bottom slider only earns its place once the catalog is large enough
   to differ from the top (featured) slider — otherwise it just repeats it. */
$torantejarat_catalog_ids = wc_get_products(
	array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => 7,
		'return'     => 'ids',
	)
);
if ( count( $torantejarat_catalog_ids ) <= 6 ) {
	return;
}

$torantejarat_latest = wc_get_products(
	array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => 8,
		'orderby'    => 'date',
		'order'      => 'DESC',
	)
);
$torantejarat_latest = array_values(
	array_filter(
		$torantejarat_latest,
		static fn( $item ) => $item instanceof \WC_Product && $item->is_visible()
	)
);
if ( ! $torantejarat_latest ) {
	return;
}

$torantejarat_shop_url = wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : '';
$torantejarat_latest_url = $torantejarat_shop_url ? add_query_arg( 'orderby', 'date', $torantejarat_shop_url ) : '';

get_template_part(
	'template-parts/product-slider',
	null,
	array(
		'products'    => $torantejarat_latest,
		'eyebrow'     => __( 'جدیدترین‌ها', 'torantejarat' ),
		'title'       => __( 'تازه رسیده‌ها', 'torantejarat' ),
		'description' => __( 'جدیدترین دستگاه‌های اضافه‌شده به فروشگاه — مشخصات کلیدی و قیمت در یک نگاه.', 'torantejarat' ),
		'link_url'    => $torantejarat_latest_url,
		'link_text'   => __( 'همه دستگاه‌ها', 'torantejarat' ),
	)
);
