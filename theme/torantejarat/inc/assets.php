<?php
/**
 * Versioned, local, dependency-ordered stylesheets.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_asset_version( $relative_path ) {
	$version = wp_get_theme( get_template() )->get( 'Version' );
	$path    = get_template_directory() . '/' . $relative_path;

	if ( 'development' === wp_get_environment_type() && is_file( $path ) ) {
		$modified = filemtime( $path );
		if ( false !== $modified ) {
			return (string) $modified;
		}
	}

	return $version;
}

function torantejarat_enqueue_assets() {
	$styles = array(
		'torantejarat-source'      => array( 'assets/css/source/theme.css', array() ),
		'torantejarat-breadcrumbs' => array( 'assets/css/source/breadcrumbs.css', array( 'torantejarat-source' ) ),
	);
	if ( torantejarat_woocommerce_available() ) {
		if ( is_shop() || is_product_taxonomy() ) {
			$styles['torantejarat-shop'] = array( 'assets/css/source/shop.css', array( 'torantejarat-source' ) );
		}
		if ( is_product() ) {
			$styles['torantejarat-product'] = array( 'assets/css/source/product.css', array( 'torantejarat-source' ) );
		}
		// Woo owns AJAX/session refresh; there is no Theme cart store or cart API.
		if ( wc_get_page_id( 'cart' ) > 0 && wp_script_is( 'wc-cart-fragments', 'registered' ) ) {
			wp_enqueue_script( 'wc-cart-fragments' );
		}
	}
	$styles['torantejarat-native'] = array( 'assets/css/native.css', array_keys( $styles ) );
	foreach ( $styles as $handle => $style ) {
		wp_enqueue_style( $handle, get_template_directory_uri() . '/' . $style[0], $style[1], torantejarat_asset_version( $style[0] ) );
	}
	if ( has_nav_menu( 'torantejarat-primary' ) ) {
		wp_enqueue_script( 'torantejarat-navigation', get_template_directory_uri() . '/assets/js/navigation.js', array(), torantejarat_asset_version( 'assets/js/navigation.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
}
