<?php
/**
 * ==========================================================================
 * FrontBaz WooCommerce Framework - Master Autoloader
 * ==========================================================================
 * Package     : FrontBaz Store Framework
 * Author      : Sina Samipour (FrontBaz)
 * Description : Central autoloader, path resolver and module orchestrator.
 * ==========================================================================
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! defined( 'FB_FW_LOADED' ) ) {
	define( 'FB_FW_LOADED', true );

	if ( ! defined( 'FB_THEME_VERSION' ) ) {
		define( 'FB_THEME_VERSION', '2.0.0' );
	}

	/**
	 * Helper: Return absolute filesystem path for FrontBaz assets
	 * Supports both encapsulated (frontbaz/assets/...) and legacy root (assets/...)
	 */
	if ( ! function_exists( 'fb_asset_path' ) ) {
		function fb_asset_path( $relative_path ) {
			$relative_path  = ltrim( $relative_path, '/\\' );
			$theme_dir_path = get_stylesheet_directory();

			// 1. Check encapsulated frontbaz/ folder first
			if ( file_exists( $theme_dir_path . '/frontbaz/' . $relative_path ) ) {
				return $theme_dir_path . '/frontbaz/' . $relative_path;
			}

			// 2. Fallback to theme root
			return $theme_dir_path . '/' . $relative_path;
		}
	}

	/**
	 * Helper: Return URL for FrontBaz assets
	 * Supports both encapsulated (frontbaz/assets/...) and legacy root (assets/...)
	 */
	if ( ! function_exists( 'fb_asset_url' ) ) {
		function fb_asset_url( $relative_path ) {
			$relative_path  = ltrim( $relative_path, '/\\' );
			$theme_dir_path = get_stylesheet_directory();
			$theme_dir_uri  = get_stylesheet_directory_uri();

			// 1. Check encapsulated frontbaz/ folder first
			if ( file_exists( $theme_dir_path . '/frontbaz/' . $relative_path ) ) {
				return $theme_dir_uri . '/frontbaz/' . $relative_path;
			}

			// 2. Fallback to theme root
			return $theme_dir_uri . '/' . $relative_path;
		}
	}

	// Determine inc directory path
	$fb_inc_dir = __DIR__ . '/inc/';
	if ( ! is_dir( $fb_inc_dir ) ) {
		$fb_inc_dir = get_stylesheet_directory() . '/frontbaz/inc/';
		if ( ! is_dir( $fb_inc_dir ) ) {
			$fb_inc_dir = get_stylesheet_directory() . '/inc/';
		}
	}

	// Autoload all PHP modules safely (explicit list guarantees loading even on hosts where glob is disabled)
	if ( is_dir( $fb_inc_dir ) ) {
		$fb_core_modules = [
			'core.php',
			'cart.php',
			'checkout.php',
			'thankyou.php',
			'myaccount.php',
		];

		foreach ( $fb_core_modules as $fb_module_name ) {
			$fb_module_path = $fb_inc_dir . $fb_module_name;
			if ( file_exists( $fb_module_path ) ) {
				require_once $fb_module_path;
			}
		}

		// Fallback for any extra custom PHP modules
		if ( function_exists( 'glob' ) ) {
			$fb_extra_modules = glob( $fb_inc_dir . '*.php' );
			if ( ! empty( $fb_extra_modules ) && is_array( $fb_extra_modules ) ) {
				foreach ( $fb_extra_modules as $fb_extra_file ) {
					if ( file_exists( $fb_extra_file ) ) {
						require_once $fb_extra_file;
					}
				}
			}
		}
	}
}
