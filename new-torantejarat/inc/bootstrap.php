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
require_once __DIR__ . '/tickets.php';
require_once __DIR__ . '/tickets-meta.php';
require_once __DIR__ . '/cleanup.php';
require_once __DIR__ . '/login.php';
require_once __DIR__ . '/admin.php';
require_once __DIR__ . '/shop-filters.php';
require_once __DIR__ . '/product-search.php';
require_once __DIR__ . '/mobile-menu.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/finder.php';
require_once __DIR__ . '/account/account.php';
require_once __DIR__ . '/../template-parts/footer-note.php';
require_once __DIR__ . '/settings/helpers.php';

// Settings panel class loads in admin only (plus WP-CLI so settings stay
// manageable from the command line); helpers serve both sides.
if ( is_admin() || ( defined( 'WP_CLI' ) && WP_CLI ) ) {
	require_once __DIR__ . '/settings/panel-nav.php';
	require_once __DIR__ . '/settings/publish-mode.php';
	require_once __DIR__ . '/settings/class-theme-settings.php';
}

// Organization schema: frontend only, homepage only (guarded inside).
if ( ! is_admin() ) {
	require_once __DIR__ . '/schema/class-org-schema.php';
}

add_action( 'init', __NAMESPACE__ . '\\torantejarat_cleanup' );
add_action( 'wp_default_scripts', __NAMESPACE__ . '\\torantejarat_dequeue_jquery_migrate' );
add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\torantejarat_dashboard_cleanup', 20 );
add_action( 'wp_dashboard_setup', __NAMESPACE__ . '\\torantejarat_store_widget', 30 );
add_action( 'admin_enqueue_scripts', __NAMESPACE__ . '\\torantejarat_admin_assets' );
add_action( 'admin_head', __NAMESPACE__ . '\\torantejarat_admin_css', 99 );
add_filter( 'admin_footer_text', __NAMESPACE__ . '\\torantejarat_admin_footer' );
add_action( 'login_enqueue_scripts', __NAMESPACE__ . '\\torantejarat_login_styles' );
add_filter( 'login_message', __NAMESPACE__ . '\\torantejarat_login_intro' );
add_filter( 'login_headerurl', static fn() => home_url( '/' ) );
add_filter( 'login_headertext', static fn() => get_bloginfo( 'name' ) );
add_filter( 'login_display_language_dropdown', '__return_false' );
add_action( 'wp_ajax_torantejarat_live_search', __NAMESPACE__ . '\\torantejarat_live_search' );
add_action( 'wp_ajax_nopriv_torantejarat_live_search', __NAMESPACE__ . '\\torantejarat_live_search' );

add_action( 'after_setup_theme', __NAMESPACE__ . '\\torantejarat_setup' );
add_action( 'after_setup_theme', __NAMESPACE__ . '\\torantejarat_woocommerce_setup' );
add_action( 'after_setup_theme', __NAMESPACE__ . '\\torantejarat_shop_filters_setup' );
add_action( 'init', __NAMESPACE__ . '\\torantejarat_register_request_type' );
add_action( 'admin_post_nopriv_torantejarat_request', __NAMESPACE__ . '\\torantejarat_handle_request' );
add_action( 'admin_post_torantejarat_request', __NAMESPACE__ . '\\torantejarat_handle_request' );
add_action( 'admin_post_torantejarat_ticket', __NAMESPACE__ . '\\torantejarat_handle_ticket' );
add_action( 'admin_post_torantejarat_ticket_create_frontend', __NAMESPACE__ . '\\torantejarat_tickets_handle_create_frontend' );
add_action( 'admin_post_torantejarat_ticket_reply_frontend', __NAMESPACE__ . '\\torantejarat_tickets_handle_reply_frontend' );
add_action( 'admin_post_torantejarat_ticket_close_frontend', __NAMESPACE__ . '\\torantejarat_tickets_handle_close_frontend' );
add_action( 'add_meta_boxes_torantejarat_request', __NAMESPACE__ . '\\torantejarat_ticket_admin_metabox' );
add_action( 'save_post_torantejarat_request', __NAMESPACE__ . '\\torantejarat_ticket_admin_save' );
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
