<?php
/**
 * Dedicated product search — Digikala-style /search/?q= page.
 *
 * A real catalog view (product grid + smart filters + sortbar + pagination)
 * with its own URL and breadcrumb. It intentionally reuses the shop filter
 * engine, but never the shop slug, title or breadcrumb.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

const TORANTEJARAT_SEARCH_REWRITE_VER = 1;

add_action( 'init', __NAMESPACE__ . '\\torantejarat_product_search_rewrite' );
add_filter( 'query_vars', __NAMESPACE__ . '\\torantejarat_product_search_vars' );
add_action( 'pre_get_posts', __NAMESPACE__ . '\\torantejarat_product_search_unhijack', 20 );
add_filter( 'template_include', __NAMESPACE__ . '\\torantejarat_product_search_template' );
add_action( 'template_redirect', __NAMESPACE__ . '\\torantejarat_product_search_setup', 1 );
add_action( 'template_redirect', __NAMESPACE__ . '\\torantejarat_product_search_empty_redirect', 10 );
add_action( 'after_switch_theme', __NAMESPACE__ . '\\torantejarat_product_search_flush' );
add_action( 'admin_init', __NAMESPACE__ . '\\torantejarat_product_search_maybe_flush' );

function torantejarat_product_search_rewrite() {
	add_rewrite_rule( '^search/?$', 'index.php?torantejarat_product_search=1', 'top' );
	add_rewrite_rule( '^search/page/([0-9]+)/?$', 'index.php?torantejarat_product_search=1&paged=$matches[1]', 'top' );
}

function torantejarat_product_search_vars( $vars ) {
	$vars[] = 'torantejarat_product_search';
	$vars[] = 'torantejarat_q';
	return $vars;
}

/**
 * True only on /search/ (frontend). Safe to call before the main query.
 */
function torantejarat_is_product_search() {
	return ! is_admin() && 1 === (int) get_query_var( 'torantejarat_product_search' );
}

/**
 * Any product-catalog surface: shop, category archive, or product search.
 * Single gate for the shared filter/sort/grid engine.
 */
function torantejarat_is_catalog() {
	return ( torantejarat_woocommerce_available() && ( is_shop() || is_product_taxonomy() ) ) || torantejarat_is_product_search();
}

/**
 * Normalized ?q= text (Persian fixes, collapsed spaces, capped length).
 */
function torantejarat_product_search_text() {
	if ( ! isset( $_GET['q'] ) || is_array( $_GET['q'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only search.
		return '';
	}
	return torantejarat_search_normalize( wp_unslash( $_GET['q'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}

function torantejarat_product_search_url( $q = null ) {
	$url = home_url( '/search/' );
	if ( null === $q ) {
		return $url;
	}
	$q = torantejarat_search_normalize( $q );
	if ( '' === $q ) {
		return $url;
	}
	return add_query_arg( 'q', rawurlencode( $q ), $url );
}

/**
 * Search term preserved across filter/sort/page URLs.
 * Product search uses ?q=, legacy shop search uses ?s=.
 */
function torantejarat_catalog_search_params() {
	if ( torantejarat_is_product_search() ) {
		$q = torantejarat_product_search_text();
		return '' !== $q ? array( 'q' => $q ) : array();
	}
	if ( isset( $_GET['s'] ) && '' !== $_GET['s'] && ! is_array( $_GET['s'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only link builder.
		return array( 's' => sanitize_text_field( wp_unslash( $_GET['s'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
	return array();
}

/**
 * Undo WooCommerce's static-front-page hijack (WC_Query::pre_get_posts).
 * On static-front sites Woo mistakes ANY ?orderby= on a home-flagged main
 * query for shop-on-frontpage and forces page_id=<front> onto it — core
 * redirect_canonical then 301s /search/?q=&orderby= to the homepage.
 * Runs after Woo (priority 20) and only on the product search page.
 */
function torantejarat_product_search_unhijack( $q ) {
	if ( ! $q instanceof \WP_Query || ! $q->is_main_query() || is_admin() ) {
		return;
	}
	if ( 1 !== (int) $q->get( 'torantejarat_product_search' ) ) {
		return;
	}
	$q->set( 'page_id', '' );
	$q->is_page     = false;
	$q->is_singular = false;
	$q->is_home     = true;
}

function torantejarat_product_search_template( $template ) {
	if ( torantejarat_is_product_search() ) {
		$file = get_template_directory() . '/search-products.php';
		if ( is_file( $file ) ) {
			return $file;
		}
	}
	return $template;
}

/**
 * /search/ matches no post — clear the 404 flags so caches, SEO plugins
 * and the status header treat it as a normal 200 page.
 */
function torantejarat_product_search_setup() {
	if ( ! torantejarat_is_product_search() ) {
		return;
	}
	global $wp_query;
	$wp_query->is_404 = false;
	status_header( 200 );
}

/**
 * Empty ?q= matches everything — bounce to the shop like empty ?s= does.
 */
function torantejarat_product_search_empty_redirect() {
	if ( ! torantejarat_is_product_search() ) {
		return;
	}
	if ( '' === torantejarat_product_search_text() ) {
		$shop = torantejarat_woocommerce_available() && wc_get_page_id( 'shop' ) > 0
			? wc_get_page_permalink( 'shop' )
			: home_url( '/' );
		wp_safe_redirect( esc_url_raw( $shop ), 302 );
		exit;
	}
}

function torantejarat_product_search_flush() {
	torantejarat_product_search_rewrite();
	flush_rewrite_rules( false );
}

function torantejarat_product_search_maybe_flush() {
	if ( (int) get_option( 'torantejarat_rewrite_ver', 0 ) < TORANTEJARAT_SEARCH_REWRITE_VER ) {
		torantejarat_product_search_flush();
		update_option( 'torantejarat_rewrite_ver', TORANTEJARAT_SEARCH_REWRITE_VER, false );
	}
}

/**
 * Current orderby key, validated against the shared pill list.
 */
function torantejarat_catalog_orderby() {
	$allowed = array( 'date', 'popularity', 'price', 'price-desc', 'discount' );
	$current = isset( $_GET['orderby'] ) && is_string( $_GET['orderby'] ) ? sanitize_key( $_GET['orderby'] ) : 'date'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	return in_array( $current, $allowed, true ) ? $current : 'date';
}

/**
 * WP_Query args for the product-search page. Mirrors the shop filter engine
 * (torantejarat_filters_apply) plus native price range and ordering, so both
 * surfaces always agree on what "filtered" means.
 */
function torantejarat_product_search_query_args() {
	$input = torantejarat_filters_input();

	$tax_query = array(
		array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => array( 'exclude-from-search' ),
			'operator' => 'NOT IN',
		),
	);
	if ( $input['pcat'] ) {
		$tax_query[] = array(
			'taxonomy' => 'product_cat',
			'field'    => 'slug',
			'terms'    => $input['pcat'],
			'operator' => 'IN',
		);
	}
	foreach ( $input['attr'] as $taxonomy => $slugs ) {
		$tax_query[] = array(
			'taxonomy' => $taxonomy,
			'field'    => 'slug',
			'terms'    => $slugs,
			'operator' => 'IN',
		);
	}
	if ( $input['instock'] ) {
		$tax_query[] = array(
			'taxonomy' => 'product_visibility',
			'field'    => 'name',
			'terms'    => array( 'outofstock' ),
			'operator' => 'NOT IN',
		);
	}
	if ( count( $tax_query ) > 1 ) {
		$tax_query['relation'] = 'AND';
	}

	$post__in = null;
	if ( $input['onsale'] ) {
		$post__in = wc_get_product_ids_on_sale();
	}

	$orderby = torantejarat_catalog_orderby();
	$args    = array(
		'post_type'           => 'product',
		'post_status'         => 'publish',
		's'                   => torantejarat_product_search_text(),
		'posts_per_page'      => torantejarat_per_page(),
		'paged'               => max( 1, (int) get_query_var( 'paged' ), (int) get_query_var( 'page' ) ),
		'ignore_sticky_posts' => true,
		'tax_query'           => $tax_query, // phpcs:ignore WordPress.DB.SlowDBQuery -- Bounded catalog query, same shape as the shop engine.
	);

	if ( 'discount' === $orderby ) {
		$deal_ids = array();
		foreach ( torantejarat_deals( 100 ) as $deal ) {
			$deal_ids[] = $deal['product']->get_id();
		}
		$args['post__in'] = $post__in ? array_values( array_intersect( $deal_ids, $post__in ) ) : $deal_ids;
		$args['orderby']  = 'post__in';
		$args['order']    = 'ASC';
	} else {
		if ( $post__in ) {
			$args['post__in'] = array_values( array_map( 'absint', $post__in ) );
		}
		switch ( $orderby ) {
			case 'popularity':
				$args['meta_key'] = 'total_sales';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'price':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'ASC';
				break;
			case 'price-desc':
				$args['meta_key'] = '_price';
				$args['orderby']  = 'meta_value_num';
				$args['order']    = 'DESC';
				break;
			case 'date':
			default:
				$args['orderby'] = 'date';
				$args['order']   = 'DESC';
				break;
		}
	}

	$meta_query = array();
	if ( '' !== $input['min_price'] && is_numeric( $input['min_price'] ) ) {
		$meta_query[] = array(
			'key'     => '_price',
			'value'   => (float) $input['min_price'],
			'compare' => '>=',
			'type'    => 'NUMERIC',
		);
	}
	if ( '' !== $input['max_price'] && is_numeric( $input['max_price'] ) ) {
		$meta_query[] = array(
			'key'     => '_price',
			'value'   => (float) $input['max_price'],
			'compare' => '<=',
			'type'    => 'NUMERIC',
		);
	}
	if ( $meta_query ) {
		$args['meta_query'] = $meta_query;
	}

	return $args;
}
