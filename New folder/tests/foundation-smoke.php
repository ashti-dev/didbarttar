<?php
/**
 * Read-only smoke checks on an isolated, real WordPress fixture via WP-CLI.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme\Tests;

if ( 'cli' !== PHP_SAPI ) {
	http_response_code( 404 );
	exit;
}

if ( ! defined( 'ABSPATH' ) || ! function_exists( 'wp_get_theme' ) ) {
	fwrite( STDERR, "BLOCKED: run through wp eval-file on a WordPress fixture.\n" );
	exit( 2 );
}

if ( ! in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) ) {
	fwrite( STDERR, "BLOCKED: isolated local/development fixture required.\n" );
	exit( 2 );
}

$torantejarat_mode = $args[0] ?? '';
if ( ! in_array( $torantejarat_mode, array( 'woo-present', 'woo-absent' ), true ) || 'torantejarat' !== get_stylesheet() ) {
	fwrite( STDERR, "BLOCKED: activate torantejarat and pass woo-present or woo-absent.\n" );
	exit( 2 );
}

$torantejarat_failures = 0;
$torantejarat_check    = static function ( $name, $condition ) use ( &$torantejarat_failures ) {
	if ( ! $condition ) {
		++$torantejarat_failures;
	}
	fwrite( STDOUT, ( $condition ? 'PASS ' : 'FAIL ' ) . $name . "\n" );
};

$torantejarat_db_info = $GLOBALS['wpdb']->db_server_info();
$torantejarat_theme   = wp_get_theme();
$torantejarat_woo     = class_exists( 'WooCommerce', false );
preg_match( '/^\d+\.\d+(?:\.\d+)?/', $torantejarat_db_info, $torantejarat_db_version );

fwrite(
	STDOUT,
	wp_json_encode(
		array(
			'php'       => PHP_VERSION,
			'wordpress' => $GLOBALS['wp_version'],
			'woo'       => defined( 'WC_VERSION' ) ? WC_VERSION : null,
			'database'  => $torantejarat_db_info,
			'locale'    => get_locale(),
			'theme'     => $torantejarat_theme->get( 'Version' ),
			'mode'      => $torantejarat_mode,
		)
	) . "\n"
);

$torantejarat_check( 'F-R01-php-proposed-floor', version_compare( PHP_VERSION, '8.3', '>=' ) );
$torantejarat_check( 'F-R02-wp-proposed-floor', version_compare( $GLOBALS['wp_version'], '6.9', '>=' ) );
$torantejarat_check(
	'F-R03-mysql-only-proposed-floor',
	false === stripos( $torantejarat_db_info, 'mariadb' ) && version_compare( $torantejarat_db_version[0] ?? '0', '8.4', '>=' )
);
$torantejarat_check( 'F-R04-classic-theme', ! wp_is_block_theme() );
$torantejarat_check( 'F-R05-identity', 'torantejarat' === $torantejarat_theme->get( 'TextDomain' ) && 'یعقوب طیبی' === $torantejarat_theme->get( 'Author' ) );
$torantejarat_check( 'F-R06-title-support', current_theme_supports( 'title-tag' ) );
$torantejarat_check( 'F-R07-woo-mode', $torantejarat_woo === ( 'woo-present' === $torantejarat_mode ) );
$torantejarat_check( 'F-R08-woo-support', current_theme_supports( 'woocommerce' ) === $torantejarat_woo );

if ( $torantejarat_woo ) {
	$torantejarat_check( 'F-R09-woo-proposed-floor', defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '10.8', '>=' ) );
	$torantejarat_check(
		'F-R10-woo-wrappers',
		10 === has_action( 'woocommerce_before_main_content', 'ToranTejarat\\Theme\\torantejarat_woocommerce_wrapper_start' )
		&& 10 === has_action( 'woocommerce_after_main_content', 'ToranTejarat\\Theme\\torantejarat_woocommerce_wrapper_end' )
		&& false === has_action( 'woocommerce_before_main_content', 'woocommerce_output_content_wrapper' )
		&& false === has_action( 'woocommerce_after_main_content', 'woocommerce_output_content_wrapper_end' )
	);
}

\ToranTejarat\Theme\torantejarat_enqueue_assets();
\ToranTejarat\Theme\torantejarat_enqueue_assets();
$torantejarat_styles = wp_styles();
$torantejarat_queue  = array_values(
	array_filter( $torantejarat_styles->queue, static fn( $handle ) => str_starts_with( $handle, 'torantejarat-' ) )
);
$torantejarat_check( 'F-R11-no-duplicate-enqueue', array( 'torantejarat-base', 'torantejarat-layout', 'torantejarat-components' ) === $torantejarat_queue );
$torantejarat_check( 'F-R12-editor-style', in_array( 'assets/css/editor.css', $GLOBALS['editor_styles'] ?? array(), true ) );

$torantejarat_fetch = static function ( $name, $url, $status ) use ( $torantejarat_check ) {
	$response = wp_remote_get( $url, array( 'timeout' => 15, 'redirection' => 0 ) );
	$torantejarat_check( $name . '-http', ! is_wp_error( $response ) && $status === wp_remote_retrieve_response_code( $response ) );
	return is_wp_error( $response ) ? '' : wp_remote_retrieve_body( $response );
};

foreach ( $torantejarat_queue as $torantejarat_handle ) {
	$torantejarat_asset = $torantejarat_styles->registered[ $torantejarat_handle ];
	$torantejarat_check( $torantejarat_handle . '-no-editor-leak', false === strpos( $torantejarat_asset->src, 'editor.css' ) );
	$torantejarat_asset_path = 'assets/css/' . substr( $torantejarat_handle, strlen( 'torantejarat-' ) ) . '.css';
	$torantejarat_check( $torantejarat_handle . '-version', $torantejarat_asset->ver === \ToranTejarat\Theme\torantejarat_asset_version( $torantejarat_asset_path ) );
	$torantejarat_fetch( $torantejarat_handle, add_query_arg( 'ver', $torantejarat_asset->ver, $torantejarat_asset->src ), 200 );
}

$torantejarat_page = get_page_by_path( 'torantejarat-foundation-page' );
$torantejarat_posts = get_posts(
	array( 'name' => 'torantejarat-foundation-post', 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 1 )
);
$torantejarat_check( 'F-R13-fixture-page', $torantejarat_page instanceof \WP_Post && 'publish' === $torantejarat_page->post_status );
$torantejarat_check( 'F-R14-fixture-post', 1 === count( $torantejarat_posts ) );

$torantejarat_routes = array(
	'home' => array( home_url( '/' ), 200, false ),
	'404'  => array( add_query_arg( 'p', 2147483647, home_url( '/' ) ), 404, false ),
);
if ( $torantejarat_page instanceof \WP_Post ) {
	$torantejarat_routes['page'] = array( get_permalink( $torantejarat_page ), 200, true );
}
if ( $torantejarat_posts ) {
	$torantejarat_post_id = $torantejarat_posts[0]->ID;
	$torantejarat_routes['single'] = array( get_permalink( $torantejarat_post_id ), 200, true );
	$torantejarat_categories = get_the_category( $torantejarat_post_id );
	$torantejarat_check( 'F-R15-fixture-category', ! empty( $torantejarat_categories ) );
	if ( $torantejarat_categories ) {
		$torantejarat_routes['archive'] = array( get_category_link( $torantejarat_categories[0] ), 200, true );
	}
}
if ( $torantejarat_woo ) {
	$torantejarat_shop = (int) get_option( 'woocommerce_shop_page_id' );
	$torantejarat_check( 'F-R16-native-shop-fixture', $torantejarat_shop > 0 );
	if ( $torantejarat_shop > 0 ) {
		$torantejarat_routes['native-woo-shell'] = array( get_permalink( $torantejarat_shop ), 200, false );
	}
}

foreach ( $torantejarat_routes as $torantejarat_route => $torantejarat_request ) {
	$torantejarat_html = $torantejarat_fetch( $torantejarat_route, $torantejarat_request[0], $torantejarat_request[1] );
	$torantejarat_check( $torantejarat_route . '-single-main', 1 === preg_match_all( '/<main\b/i', $torantejarat_html ) && str_contains( $torantejarat_html, 'id="torantejarat-main"' ) );
	$torantejarat_check( $torantejarat_route . '-skip-link', str_contains( $torantejarat_html, 'href="#torantejarat-main"' ) );
	$torantejarat_check( $torantejarat_route . '-no-php-diagnostics', ! preg_match( '/(?:Fatal error|Parse error|Warning|Notice|Deprecated):/i', $torantejarat_html ) );
	$torantejarat_check( $torantejarat_route . '-no-editor-css', ! str_contains( $torantejarat_html, 'assets/css/editor.css' ) );
	if ( $torantejarat_request[2] ) {
		$torantejarat_check( $torantejarat_route . '-block-and-classic-content', str_contains( $torantejarat_html, 'TORANTEJARAT_BLOCK_FIXTURE' ) && str_contains( $torantejarat_html, 'TORANTEJARAT_CLASSIC_FIXTURE' ) );
	}
}

fwrite( STDOUT, "Smoke failures: {$torantejarat_failures}. These checks do not replace browser/security/acceptance QA.\n" );
exit( $torantejarat_failures ? 1 : 0 );
