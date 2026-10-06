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
require_once __DIR__ . '/requests.php';
require_once __DIR__ . '/cleanup.php';
require_once __DIR__ . '/login.php';
require_once __DIR__ . '/settings/helpers.php';

// Settings panel class loads in admin only (plus WP-CLI so settings stay
// manageable from the command line); helpers serve both sides.
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once __DIR__ . '/settings/class-theme-settings.php';
}

// Organization schema: frontend only, homepage only (guarded inside).
if ( ! is_admin() ) {
	require_once __DIR__ . '/schema/class-org-schema.php';
}

add_action( 'init', __NAMESPACE__ . '\\torantejarat_cleanup' );
add_action( 'wp_default_scripts', __NAMESPACE__ . '\\torantejarat_dequeue_jquery_migrate' );
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\torantejarat_login_styles' );
add_filter( 'login_message', __NAMESPACE__ . '\\torantejarat_login_intro' );
add_filter( 'login_headerurl', static fn() => home_url( '/' ) );
add_filter( 'login_headertext', static fn() => get_bloginfo( 'name' ) );

add_action( 'after_setup_theme', __NAMESPACE__ . '\\torantejarat_setup' );
add_action( 'after_setup_theme', __NAMESPACE__ . '\\torantejarat_woocommerce_setup' );
add_action( 'init', __NAMESPACE__ . '\\torantejarat_register_request_type' );
add_action( 'admin_post_nopriv_torantejarat_request', __NAMESPACE__ . '\\torantejarat_handle_request' );
add_action( 'admin_post_torantejarat_request', __NAMESPACE__ . '\\torantejarat_handle_request' );
add_filter( 'manage_torantejarat_request_posts_columns', __NAMESPACE__ . '\\torantejarat_request_columns' );
add_action( 'manage_torantejarat_request_posts_custom_column', __NAMESPACE__ . '\\torantejarat_request_column_content', 10, 2 );
add_action( 'add_meta_boxes_torantejarat_request', __NAMESPACE__ . '\\torantejarat_request_metabox' );
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\torantejarat_enqueue_assets' );
add_action( 'wp_head', __NAMESPACE__ . '\\torantejarat_font_preload', 1 );
add_action( 'wp_head', __NAMESPACE__ . '\\torantejarat_fallback_icon', 99 );
add_action( 'admin_notices', __NAMESPACE__ . '\\torantejarat_woocommerce_notice' );

add_action( 'init', __NAMESPACE__ . '\\torantejarat_register_patterns' );

add_filter( 'render_block_core/group', __NAMESPACE__ . '\\torantejarat_render_content_slot', 10, 2 );
add_filter( 'render_block_core/details', __NAMESPACE__ . '\\torantejarat_render_content_slot', 10, 2 );
