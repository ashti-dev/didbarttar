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

/**
 * Preload the variable font so the browser fetches it before first paint.
 */
function torantejarat_font_preload() {
	echo '<link rel="preload" href="' . esc_url( get_template_directory_uri() . '/assets/fonts/Vazirmatn-Variable.woff2' ) . '" as="font" type="font/woff2" crossorigin>' . "\n";
}

function torantejarat_enqueue_assets() {
	wp_enqueue_style( 'torantejarat-fonts', get_template_directory_uri() . '/assets/css/source/fonts.css', array(), torantejarat_asset_version( 'assets/css/source/fonts.css' ) );

	$styles = array(
		'torantejarat-source'      => array( 'assets/css/source/theme.css', array( 'torantejarat-fonts' ) ),
		'torantejarat-breadcrumbs' => array( 'assets/css/source/breadcrumbs.css', array( 'torantejarat-source' ) ),
	);
	if ( torantejarat_woocommerce_available() ) {
		if ( is_shop() || is_product_taxonomy() ) {
			$styles['torantejarat-shop'] = array( 'assets/css/source/shop.css', array( 'torantejarat-source' ) );
		}
		if ( is_product() ) {
			$styles['torantejarat-product'] = array( 'assets/css/source/product.css', array( 'torantejarat-source' ) );
		}
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
	if ( torantejarat_woocommerce_available() && is_product() ) {
		wp_enqueue_script( 'torantejarat-pdp', get_template_directory_uri() . '/assets/js/pdp.js', array(), torantejarat_asset_version( 'assets/js/pdp.js' ), true );
		wp_enqueue_script( 'torantejarat-pdp-tabs', get_template_directory_uri() . '/assets/js/pdp-tabs.js', array(), torantejarat_asset_version( 'assets/js/pdp-tabs.js' ), true );
	}
	if ( torantejarat_woocommerce_available() && ( is_product() || is_shop() || is_product_taxonomy() ) ) {
		wp_enqueue_script( 'torantejarat-ajax-add-to-cart', get_template_directory_uri() . '/assets/js/ajax-add-to-cart.js', array( 'jquery' ), torantejarat_asset_version( 'assets/js/ajax-add-to-cart.js' ), true );
		wp_enqueue_script( 'torantejarat-recently-viewed', get_template_directory_uri() . '/assets/js/recently-viewed.js', array(), torantejarat_asset_version( 'assets/js/recently-viewed.js' ), true );
	}
	if ( torantejarat_woocommerce_available() && is_page_template( 'page-templates/product-finder.php' ) ) {
		wp_enqueue_script( 'torantejarat-product-finder', get_template_directory_uri() . '/assets/js/product-finder.js', array(), torantejarat_asset_version( 'assets/js/product-finder.js' ), true );
	}
	if ( torantejarat_woocommerce_available() && ( is_front_page() || is_product() || is_cart() ) ) {
		wp_enqueue_style( 'torantejarat-product-slider', get_template_directory_uri() . '/assets/css/product-slider.css', array( 'torantejarat-source' ), torantejarat_asset_version( 'assets/css/product-slider.css' ) );
		wp_enqueue_script( 'torantejarat-product-slider', get_template_directory_uri() . '/assets/js/product-slider.js', array(), torantejarat_asset_version( 'assets/js/product-slider.js' ), true );
		wp_enqueue_style( 'torantejarat-hero-slider', get_template_directory_uri() . '/assets/css/hero-slider.css', array( 'torantejarat-source' ), torantejarat_asset_version( 'assets/css/hero-slider.css' ) );
		wp_enqueue_script( 'torantejarat-hero-slider', get_template_directory_uri() . '/assets/js/hero-slider.js', array(), torantejarat_asset_version( 'assets/js/hero-slider.js' ), true );
	}
}
