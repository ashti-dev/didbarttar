<?php
/**
 * Homepage product finder — one box, three real-data filters (use / length /
 * budget), live AJAX results reusing the native product loop + card override.
 * Read-only endpoint with nonce (same pattern as compare fragment).
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Use-case options: fixed homepage categories with live counts.
 * Empty categories are skipped (never shown).
 */
function torantejarat_finder_cats() {
	$slugs = apply_filters(
		'torantejarat_home_categories',
		array( 'industrial', 'monitor', 'wifi', 'deep-well' )
	);
	$out = array();
	foreach ( $slugs as $slug ) {
		$term = get_term_by( 'slug', $slug, 'product_cat' );
		if ( ! $term || is_wp_error( $term ) || $term->count < 1 ) {
			continue;
		}
		$out[] = array( 'slug' => $term->slug, 'name' => $term->name, 'count' => (int) $term->count );
	}
	return $out;
}

/**
 * Cable lengths with products (real pa_cable-length terms only).
 */
function torantejarat_finder_lengths() {
	$terms = get_terms( array( 'taxonomy' => 'pa_cable-length', 'hide_empty' => true ) );
	if ( is_wp_error( $terms ) || ! $terms ) {
		return array();
	}
	$out = array();
	foreach ( $terms as $term ) {
		$out[] = array( 'id' => (int) $term->term_id, 'name' => $term->name, 'count' => (int) $term->count );
	}
	return $out;
}

/**
 * Budget bands from real numeric prices (tertiles). Products without a
 * numeric price are counted separately and never shape the bands.
 */
function torantejarat_finder_bands() {
	$prices = array();
	$ids = wc_get_products( array( 'status' => 'publish', 'limit' => -1, 'return' => 'ids' ) );
	foreach ( $ids as $id ) {
		$product = wc_get_product( $id );
		if ( ! $product || ! $product->is_visible() ) {
			continue;
		}
		$price = $product->get_price();
		if ( is_numeric( $price ) && (float) $price > 0 ) {
			$prices[] = (float) $price;
		}
	}
	if ( count( $prices ) < 3 ) {
		return array();
	}
	sort( $prices );
	$n     = count( $prices );
	$cut1  = $prices[ (int) floor( $n / 3 ) ];
	$cut2  = $prices[ (int) floor( $n * 2 / 3 ) ];
	return array(
		array( 'min' => 0, 'max' => $cut1, 'label' => sprintf( __( 'تا %s', 'torantejarat' ), torantejarat_fa_number( $cut1 ) . ' ' . __( 'تومان', 'torantejarat' ) ) ),
		array( 'min' => $cut1, 'max' => $cut2, 'label' => sprintf( __( '%1$s تا %2$s', 'torantejarat' ), torantejarat_fa_number( $cut1 ), torantejarat_fa_number( $cut2 ) ) ),
		array( 'min' => $cut2, 'max' => 0, 'label' => sprintf( __( 'بالای %s', 'torantejarat' ), torantejarat_fa_number( $cut2 ) . ' ' . __( 'تومان', 'torantejarat' ) ) ),
	);
}

/**
 * Run the finder query. All inputs validated server-side.
 */
function torantejarat_finder_query( $cats, $lengths, $band ) {
	$tax_query = array( 'relation' => 'AND' );
	if ( $cats ) {
		$valid = array();
		foreach ( (array) $cats as $slug ) {
			$slug = sanitize_title( $slug );
			if ( term_exists( $slug, 'product_cat' ) ) {
				$valid[] = $slug;
			}
		}
		if ( $valid ) {
			$tax_query[] = array(
				'taxonomy' => 'product_cat',
				'field'    => 'slug',
				'terms'    => $valid,
				'operator' => 'IN',
			);
		}
	}
	if ( $lengths ) {
		$valid = array();
		foreach ( (array) $lengths as $term_id ) {
			$term_id = absint( $term_id );
			$term = $term_id > 0 ? get_term( $term_id, 'pa_cable-length' ) : null;
			if ( $term && ! is_wp_error( $term ) ) {
				$valid[] = $term->slug;
			}
		}
		if ( $valid ) {
			$tax_query[] = array(
				'taxonomy' => 'pa_cable-length',
				'field'    => 'slug',
				'terms'    => $valid,
				'operator' => 'IN',
			);
		}
	}
	$args = array(
		'status'     => 'publish',
		'visibility' => 'catalog',
		'limit'      => -1,
		'orderby'    => 'price',
		'order'      => 'ASC',
	);
	if ( count( $tax_query ) > 1 ) {
		$args['tax_query'] = $tax_query; // phpcs:ignore WordPress.DB.SlowDBQuery.slow_db_query_tax_query -- Small catalog, homepage widget.
	}
	$band = absint( $band );
	$band_min = 0;
	$band_max = 0;
	if ( $band > 0 ) {
		$bands = torantejarat_finder_bands();
		if ( isset( $bands[ $band - 1 ] ) ) {
			$band_min = (float) $bands[ $band - 1 ]['min'];
			$band_max = (float) $bands[ $band - 1 ]['max'];
		} else {
			$band = 0;
		}
	}
	/* Price is filtered in PHP: WC_Product_Query has no min/max price args,
	 * and the catalog is small. Bands still come from real prices. */
	$products = wc_get_products( $args );
	$out = array();
	foreach ( $products as $item ) {
		if ( ! $item instanceof \WC_Product || ! $item->is_visible() ) {
			continue;
		}
		if ( $band > 0 ) {
			$price = $item->get_price();
			if ( ! is_numeric( $price ) ) {
				continue;
			}
			$price = (float) $price;
			if ( ( $band_min > 0 && $price < $band_min ) || ( $band_max > 0 && $price > $band_max ) ) {
				continue;
			}
		}
		$out[] = $item;
	}
	return array_values( $out );
}

/**
 * AJAX fragment: rendered product cards + count. Reuses the native loop
 * and the content-product override — no duplicate card markup.
 */
function torantejarat_finder_fragment() {
	check_ajax_referer( 'torantejarat-finder', 'nonce' );
	$cats    = isset( $_GET['cats'] ) ? array_map( 'sanitize_title', explode( ',', sanitize_text_field( wp_unslash( $_GET['cats'] ) ) ) ) : array();
	$lengths = isset( $_GET['lengths'] ) ? array_map( 'absint', explode( ',', sanitize_text_field( wp_unslash( $_GET['lengths'] ) ) ) ) : array();
	$band    = isset( $_GET['band'] ) ? absint( $_GET['band'] ) : 0;
	$items   = torantejarat_finder_query( $cats, $lengths, $band );
	$total   = count( $items );
	$items   = array_slice( $items, 0, 4 ); // One compact row on desktop; "see all" links to the shop.
	ob_start();
	if ( $items ) {
		wc_setup_loop( array( 'name' => 'torantejarat-finder', 'columns' => 4, 'total' => count( $items ), 'is_paginated' => false ) );
		/* Scoped modifier: the same native loop/card, compact finder styling only. */
		$torantejarat_grid_filter = static function ( $html ) {
			return str_replace( 'class="products', 'class="products torantejarat-finder-grid', $html );
		};
		add_filter( 'woocommerce_product_loop_start', $torantejarat_grid_filter );
		woocommerce_product_loop_start();
		remove_filter( 'woocommerce_product_loop_start', $torantejarat_grid_filter );
		global $post;
		foreach ( $items as $product ) {
			$post = get_post( $product->get_id() );
			setup_postdata( $post );
			wc_get_template_part( 'content', 'product' );
		}
		woocommerce_product_loop_end();
		wc_reset_loop();
		wp_reset_postdata();
	}
	$html = ob_get_clean();
	wp_send_json(
		array(
			'count' => $total,
			'html'  => $html,
		)
	);
}

/**
 * Finder query strings on the homepage must not spawn indexable URLs.
 */
function torantejarat_finder_robots( $robots ) {
	if ( function_exists( 'is_front_page' ) && is_front_page() && ( isset( $_GET['fcat'] ) || isset( $_GET['flen'] ) || isset( $_GET['fband'] ) ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only display check.
		$robots['noindex'] = true;
	}
	return $robots;
}
add_filter( 'wp_robots', __NAMESPACE__ . '\\torantejarat_finder_robots' );
