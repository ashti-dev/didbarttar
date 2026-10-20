<?php
/**
 * Load the Classic Theme presentation and native platform integrations.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once __DIR__ . '/presentation.php';
require_once __DIR__ . '/patterns.php';
require_once __DIR__ . '/setup.php';
require_once __DIR__ . '/assets.php';
require_once __DIR__ . '/woocommerce.php';

add_action( 'after_setup_theme', __NAMESPACE__ . '\\torantejarat_setup' );
add_action( 'after_setup_theme', __NAMESPACE__ . '\\torantejarat_woocommerce_setup' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\torantejarat_enqueue_assets' );
add_action( 'admin_notices', __NAMESPACE__ . '\\torantejarat_woocommerce_notice' );

add_action( 'init', __NAMESPACE__ . '\\torantejarat_register_patterns' );

add_filter( 'render_block_core/group', __NAMESPACE__ . '\\torantejarat_render_content_slot', 10, 2 );
add_filter( 'render_block_core/details', __NAMESPACE__ . '\\torantejarat_render_content_slot', 10, 2 );
