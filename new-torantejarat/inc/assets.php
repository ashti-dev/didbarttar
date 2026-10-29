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
	$path = get_template_directory() . '/' . $relative_path;

	/* Always filemtime when the file exists: syncs and hotfixes reach
	   browsers immediately instead of hiding behind a cached ?ver=. */
	if ( is_file( $path ) ) {
		$modified = filemtime( $path );
		if ( false !== $modified ) {
			return (string) $modified;
		}
	}

	return wp_get_theme( get_template() )->get( 'Version' );
}

/**
 * Favicon fallback: when the owner has not set a site icon in the
 * Customizer, use the theme's own mark instead of shipping without one.
 * Rank Math does not manage favicons, so there is no conflict.
 */
function torantejarat_fallback_icon() {
	if ( has_site_icon() ) {
		return;
	}
	$file = get_template_directory() . '/assets/images/favicon.svg';
	if ( ! is_file( $file ) ) {
		return;
	}
	echo '<link rel="icon" href="' . esc_url( get_template_directory_uri() . '/assets/images/favicon.svg' ) . '" type="image/svg+xml">' . "\n";
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
		if ( torantejarat_is_catalog() ) {
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
	wp_enqueue_script( 'torantejarat-search-modal', get_template_directory_uri() . '/assets/js/search-modal.js', array(), torantejarat_asset_version( 'assets/js/search-modal.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script(
		'torantejarat-search-modal',
		'torantejaratSearch',
		array(
			'ajaxUrl'    => admin_url( 'admin-ajax.php' ),
			'nonce'      => wp_create_nonce( 'torantejarat-search' ),
			'label'      => __( 'جست‌وجو', 'torantejarat' ),
			'close'      => __( 'بستن', 'torantejarat' ),
			'hint'       => __( 'حداقل ۲ حرف بنویسید تا نتیجه بیاید.', 'torantejarat' ),
			'noResult'   => __( 'چیزی پیدا نشد؛ عبارت دیگری را امتحان کنید.', 'torantejarat' ),
			'inCats'     => __( 'در دسته‌ها', 'torantejarat' ),
			'inProducts' => __( 'در محصولات', 'torantejarat' ),
			'viewAll'     => __( 'مشاهده همه نتایج', 'torantejarat' ),
			'searchUrl'   => home_url( '/search/' ),
			'searchParam' => 'q',
			'shopUrl'     => torantejarat_woocommerce_available() && wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
			'endpoint'   => esc_url_raw( rest_url( 'torantejarat/v1/search' ) ),
			'catalog'    => esc_url_raw( rest_url( 'torantejarat/v1/catalog' ) ),
		)
	);
	wp_enqueue_script( 'torantejarat-mobile-menu', get_template_directory_uri() . '/assets/js/mobile-menu.js', array(), torantejarat_asset_version( 'assets/js/mobile-menu.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script(
		'torantejarat-mobile-menu',
		'torantejaratMenu',
		array(
			'shopUrl' => torantejarat_woocommerce_available() && wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
		)
	);
	wp_enqueue_script( 'torantejarat-reveal', get_template_directory_uri() . '/assets/js/reveal.js', array(), torantejarat_asset_version( 'assets/js/reveal.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	if ( torantejarat_woocommerce_available() && is_product() ) {
		wp_enqueue_script( 'torantejarat-pdp', get_template_directory_uri() . '/assets/js/pdp.js', array(), torantejarat_asset_version( 'assets/js/pdp.js' ), true );
		wp_enqueue_script( 'torantejarat-pdp-tabs', get_template_directory_uri() . '/assets/js/pdp-tabs.js', array(), torantejarat_asset_version( 'assets/js/pdp-tabs.js' ), true );
		if ( is_user_logged_in() ) {
			wp_enqueue_script( 'torantejarat-wishlist', get_template_directory_uri() . '/assets/js/wishlist.js', array(), torantejarat_asset_version( 'assets/js/wishlist.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
			wp_localize_script(
				'torantejarat-wishlist',
				'torantejaratWishlist',
				array(
					'restToggle' => esc_url_raw( rest_url( 'torantejarat/v1/wishlist/toggle' ) ),
					'restNonce'  => wp_create_nonce( 'wp_rest' ),
				)
			);
		}
	}
	if ( torantejarat_woocommerce_available() && torantejarat_is_catalog() ) {
		wp_enqueue_script( 'torantejarat-shop-filters', get_template_directory_uri() . '/assets/js/shop-filters.js', array(), torantejarat_asset_version( 'assets/js/shop-filters.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	}
	if ( torantejarat_woocommerce_available() && ( is_product() || torantejarat_is_catalog() ) ) {
		wp_enqueue_script( 'torantejarat-ajax-add-to-cart', get_template_directory_uri() . '/assets/js/ajax-add-to-cart.js', array( 'jquery' ), torantejarat_asset_version( 'assets/js/ajax-add-to-cart.js' ), true );
		wp_enqueue_script( 'torantejarat-recently-viewed', get_template_directory_uri() . '/assets/js/recently-viewed.js', array(), torantejarat_asset_version( 'assets/js/recently-viewed.js' ), true );
	}
	if ( torantejarat_woocommerce_available() && is_page_template( 'page-templates/product-finder.php' ) ) {
		wp_enqueue_script( 'torantejarat-product-finder', get_template_directory_uri() . '/assets/js/product-finder.js', array(), torantejarat_asset_version( 'assets/js/product-finder.js' ), true );
	}
	if ( torantejarat_woocommerce_available() && is_page_template( 'page-templates/compare.php' ) ) {
		wp_enqueue_script( 'torantejarat-compare', get_template_directory_uri() . '/assets/js/compare.js', array(), torantejarat_asset_version( 'assets/js/compare.js' ), true );
		wp_localize_script(
			'torantejarat-compare',
			'torantejaratCompare',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'torantejarat-compare' ),
			)
		);
	}
	if ( torantejarat_woocommerce_available() && ( is_front_page() || is_product() || is_cart() ) ) {
		wp_enqueue_style( 'torantejarat-product-slider', get_template_directory_uri() . '/assets/css/product-slider.css', array( 'torantejarat-source' ), torantejarat_asset_version( 'assets/css/product-slider.css' ) );
		wp_enqueue_script( 'torantejarat-product-slider', get_template_directory_uri() . '/assets/js/product-slider.js', array(), torantejarat_asset_version( 'assets/js/product-slider.js' ), true );
		wp_enqueue_style( 'torantejarat-hero-slider', get_template_directory_uri() . '/assets/css/hero-slider.css', array( 'torantejarat-source' ), torantejarat_asset_version( 'assets/css/hero-slider.css' ) );
		wp_enqueue_script( 'torantejarat-hero-slider', get_template_directory_uri() . '/assets/js/hero-slider.js', array(), torantejarat_asset_version( 'assets/js/hero-slider.js' ), true );
	}
	if ( torantejarat_woocommerce_available() && is_front_page() ) {
		wp_enqueue_style( 'torantejarat-finder', get_template_directory_uri() . '/assets/css/finder.css', array( 'torantejarat-native' ), torantejarat_asset_version( 'assets/css/finder.css' ) );
		wp_enqueue_script( 'torantejarat-finder', get_template_directory_uri() . '/assets/js/finder.js', array(), torantejarat_asset_version( 'assets/js/finder.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_localize_script(
			'torantejarat-finder',
			'torantejaratFinder',
			array(
				'shopUrl'    => wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : home_url( '/' ),
				'contactUrl' => get_page_by_path( 'contact' ) ? get_permalink( get_page_by_path( 'contact' ) ) : home_url( '/' ),
			)
		);
	}
	if ( torantejarat_woocommerce_available() && function_exists( 'is_order_received_page' ) && is_order_received_page() ) {
		wp_enqueue_style( 'torantejarat-thankyou', get_template_directory_uri() . '/assets/css/thankyou.css', array( 'torantejarat-native' ), torantejarat_asset_version( 'assets/css/thankyou.css' ) );
	}
	if ( torantejarat_woocommerce_available() && function_exists( 'is_checkout' ) && is_checkout() && ! is_order_received_page() ) {
		wp_enqueue_style( 'torantejarat-checkout', get_template_directory_uri() . '/assets/css/checkout.css', array( 'torantejarat-native' ), torantejarat_asset_version( 'assets/css/checkout.css' ) );
	}
	if ( torantejarat_woocommerce_available() && function_exists( 'is_account_page' ) && is_account_page() ) {
		wp_enqueue_script( 'torantejarat-tracking', get_template_directory_uri() . '/assets/js/tracking.js', array(), torantejarat_asset_version( 'assets/js/tracking.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_enqueue_style( 'torantejarat-account', get_template_directory_uri() . '/assets/css/account.css', array( 'torantejarat-native' ), torantejarat_asset_version( 'assets/css/account.css' ) );
		wp_enqueue_script( 'torantejarat-account', get_template_directory_uri() . '/assets/js/account.js', array(), torantejarat_asset_version( 'assets/js/account.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
		wp_localize_script(
			'torantejarat-account',
			'torantejaratAccount',
			array(
				'restToggle' => esc_url_raw( rest_url( 'torantejarat/v1/wishlist/toggle' ) ),
				'restNonce'  => wp_create_nonce( 'wp_rest' ),
			)
		);
	}
}
