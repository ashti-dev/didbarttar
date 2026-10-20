<?php
/**
 * Migration read-only HTTP/hook checks; no fixture creation or commerce transaction.
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

$torantejarat_check( 'M-R01-php-proposed-floor', version_compare( PHP_VERSION, '8.3', '>=' ) );
$torantejarat_check( 'M-R02-wp-proposed-floor', version_compare( $GLOBALS['wp_version'], '6.9', '>=' ) );
$torantejarat_check(
	'M-R03-mysql-only-proposed-floor',
	false === stripos( $torantejarat_db_info, 'mariadb' ) && version_compare( $torantejarat_db_version[0] ?? '0', '8.4', '>=' )
);
$torantejarat_check( 'M-R04-classic-theme', ! wp_is_block_theme() );
$torantejarat_check( 'M-R05-identity', 'torantejarat' === $torantejarat_theme->get( 'TextDomain' ) && 'یعقوب طیبی' === $torantejarat_theme->get( 'Author' ) );
$torantejarat_check( 'M-R06-title-support', current_theme_supports( 'title-tag' ) );
$torantejarat_check( 'M-R07-woo-mode', $torantejarat_woo === ( 'woo-present' === $torantejarat_mode ) );
$torantejarat_check( 'M-R08-woo-support', current_theme_supports( 'woocommerce' ) === $torantejarat_woo );

if ( $torantejarat_woo ) {
	$torantejarat_check( 'M-R09-woo-proposed-floor', defined( 'WC_VERSION' ) && version_compare( WC_VERSION, '10.8', '>=' ) );
	$torantejarat_check(
		'M-R10-woo-wrappers',
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
$torantejarat_check( 'M-R11-no-duplicate-enqueue', array( 'torantejarat-source', 'torantejarat-breadcrumbs', 'torantejarat-native' ) === $torantejarat_queue );
$torantejarat_check( 'M-R12-editor-style', in_array( 'assets/css/editor.css', $GLOBALS['editor_styles'] ?? array(), true ) );

$torantejarat_fetch = static function ( $name, $url, $status ) use ( $torantejarat_check ) {
	$response = wp_remote_get( $url, array( 'timeout' => 15, 'redirection' => 0 ) );
	if ( 'checkout' === $name && ! is_wp_error( $response ) && 302 === wp_remote_retrieve_response_code( $response ) ) {
		$torantejarat_check( 'checkout-empty-cart-redirect', wc_get_cart_url() === wp_remote_retrieve_header( $response, 'location' ) );
		fwrite( STDOUT, "NOT TESTED checkout form: this read-only request has no populated Woo session.\n" );
		return null;
	}
	$torantejarat_check( $name . '-http', ! is_wp_error( $response ) && $status === wp_remote_retrieve_response_code( $response ) );
	return is_wp_error( $response ) ? '' : wp_remote_retrieve_body( $response );
};

foreach ( $torantejarat_queue as $torantejarat_handle ) {
	$torantejarat_asset = $torantejarat_styles->registered[ $torantejarat_handle ];
	$torantejarat_check( $torantejarat_handle . '-no-editor-leak', false === strpos( $torantejarat_asset->src, 'editor.css' ) );
	$torantejarat_asset_path = ltrim( substr( $torantejarat_asset->src, strlen( get_template_directory_uri() ) ), '/' );
	$torantejarat_check( $torantejarat_handle . '-version', $torantejarat_asset->ver === \ToranTejarat\Theme\torantejarat_asset_version( $torantejarat_asset_path ) );
	$torantejarat_fetch( $torantejarat_handle, add_query_arg( 'ver', $torantejarat_asset->ver, $torantejarat_asset->src ), 200 );
}

$torantejarat_page = get_page_by_path( 'torantejarat-foundation-page' );
$torantejarat_posts = get_posts(
	array( 'name' => 'torantejarat-foundation-post', 'post_type' => 'post', 'post_status' => 'publish', 'numberposts' => 1 )
);
$torantejarat_check( 'M-R13-fixture-page', $torantejarat_page instanceof \WP_Post && 'publish' === $torantejarat_page->post_status );
$torantejarat_check( 'M-R14-fixture-post', 1 === count( $torantejarat_posts ) );

$torantejarat_routes = array(
	'home' => array( home_url( '/' ), 200, false ),
	'search' => array( add_query_arg( 's', 'TORANTEJARAT', home_url( '/' ) ), 200, false ),
	'404'  => array( add_query_arg( 'p', 2147483647, home_url( '/' ) ), 404, false ),
);
if ( $torantejarat_page instanceof \WP_Post ) {
	$torantejarat_routes['page'] = array( get_permalink( $torantejarat_page ), 200, true );
}
if ( $torantejarat_posts ) {
	$torantejarat_post_id = $torantejarat_posts[0]->ID;
	$torantejarat_routes['single'] = array( get_permalink( $torantejarat_post_id ), 200, true );
	$torantejarat_categories = get_the_category( $torantejarat_post_id );
	$torantejarat_check( 'M-R15-fixture-category', ! empty( $torantejarat_categories ) );
	if ( $torantejarat_categories ) {
		$torantejarat_routes['archive'] = array( get_category_link( $torantejarat_categories[0] ), 200, false );
	}
}
if ( $torantejarat_woo ) {
	$torantejarat_shop = (int) get_option( 'woocommerce_shop_page_id' );
	$torantejarat_check( 'M-R16-native-shop-fixture', $torantejarat_shop > 0 );
	if ( $torantejarat_shop > 0 ) {
		$torantejarat_routes['native-woo-shell'] = array( get_permalink( $torantejarat_shop ), 200, false );
	}
}

if ( $torantejarat_woo ) {
	$torantejarat_fixture_products = wc_get_products( array( 'status' => 'publish', 'visibility' => 'visible', 'limit' => 1 ) );
	$torantejarat_check( 'M-R17-published-product-fixture', ! empty( $torantejarat_fixture_products ) );
	if ( $torantejarat_fixture_products ) {
		$torantejarat_routes['product'] = array( $torantejarat_fixture_products[0]->get_permalink(), 200, false );
	}
	foreach ( array( 'cart', 'checkout', 'myaccount' ) as $torantejarat_page_key ) {
		$torantejarat_check( 'M-R18-' . $torantejarat_page_key . '-assigned', wc_get_page_id( $torantejarat_page_key ) > 0 );
		if ( wc_get_page_id( $torantejarat_page_key ) > 0 ) {
			$torantejarat_routes[ $torantejarat_page_key ] = array( wc_get_page_permalink( $torantejarat_page_key ), 200, false );
		}
	}
	$torantejarat_check( 'M-R19-native-gallery-support', current_theme_supports( 'wc-product-gallery-slider' ) && current_theme_supports( 'wc-product-gallery-lightbox' ) );
	$torantejarat_check( 'M-R20-native-purchase-hook', 30 === has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart' ) );
}

$torantejarat_check( 'M-R21-home-patterns', \WP_Block_Patterns_Registry::get_instance()->is_registered( 'torantejarat/faq' ) && \WP_Block_Patterns_Registry::get_instance()->is_registered( 'torantejarat/field-video' ) );
if ( $torantejarat_woo ) {
	$torantejarat_check( 'M-R22-archive-header-boundary', 99 === has_action( 'woocommerce_shop_loop_header', 'ToranTejarat\\Theme\\torantejarat_archive_intro_end' ) );
	$torantejarat_check( 'M-R23-core-meta-hook', 2 === has_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta' ) );
}

// Read-only render probes, never saved as Page or product content. NOT executed without WP.
if ( is_admin() ) {
	\WP_CLI::warning( 'NOT TESTED empty-slot rendering: use a frontend bootstrap context.' );
} else {
	foreach ( array( 'benefits', 'field-video', 'faq' ) as $torantejarat_pattern_name ) {
		$torantejarat_pattern = \WP_Block_Patterns_Registry::get_instance()->get_registered( 'torantejarat/' . $torantejarat_pattern_name );
		$torantejarat_check( 'M-R24-empty-' . $torantejarat_pattern_name, is_array( $torantejarat_pattern ) && '' === trim( do_blocks( $torantejarat_pattern['content'] ) ) );
	}
	$torantejarat_slot_cases = array(
		'empty-video' => array( 'torantejarat-video-panel', '<figure><video controls></video><figcaption>Fixture caption</figcaption></figure>', false ),
		'real-video-markup' => array( 'torantejarat-video-panel', '<video controls src="/fixture-only.mp4"></video>', true ),
		'blank-question' => array( 'torantejarat-faq-item', '<details><summary></summary><p>Fixture answer</p></details>', false ),
		'blank-answer' => array( 'torantejarat-faq-item', '<details><summary>Fixture question</summary><p></p></details>', false ),
		'complete-question' => array( 'torantejarat-faq-item', '<details><summary>Fixture question</summary><p>Fixture answer</p></details>', true ),
		'zero-text' => array( 'torantejarat-benefit-strip', '<p>0</p>', true ),
		'invisible-text' => array( 'torantejarat-benefit-strip', '<p>&nbsp;&#8204;</p>', false ),
		'unrelated-group' => array( 'unrelated', '<div></div>', true ),
		'invalid-class-shape' => array( array(), '<div></div>', true ),
	);
	foreach ( $torantejarat_slot_cases as $torantejarat_case_name => $torantejarat_case ) {
		$torantejarat_rendered = \ToranTejarat\Theme\torantejarat_render_content_slot( $torantejarat_case[1], array( 'attrs' => array( 'className' => $torantejarat_case[0] ) ) );
		$torantejarat_check( 'M-R25-' . $torantejarat_case_name, $torantejarat_rendered === ( $torantejarat_case[2] ? $torantejarat_case[1] : '' ) );
	}
}

// In-memory TOC fixture only; never stored in the database.
$torantejarat_toc_fixture = <<<'HTML'
<!-- wp:group --><div class="wp-block-group">
<!-- wp:heading {"anchor":"first"} --><h2 id="first">A &amp; B</h2><!-- /wp:heading -->
<!-- wp:heading {"anchor":"first"} --><h2 id="first">Duplicate</h2><!-- /wp:heading -->
<!-- wp:heading {"anchor":"empty"} --><h2 id="empty">&nbsp;</h2><!-- /wp:heading -->
<!-- wp:heading {"anchor":[]} --><h2>Invalid anchor shape</h2><!-- /wp:heading -->
<!-- wp:heading {"anchor":"bad anchor"} --><h2>Invalid anchor whitespace</h2><!-- /wp:heading -->
<!-- wp:heading {"anchor":"zero"} --><h2 id="zero">0</h2><!-- /wp:heading -->
<!-- wp:heading --><h2>No inferred anchor</h2><!-- /wp:heading -->
</div><!-- /wp:group -->
HTML;
$torantejarat_check( 'M-R26-authored-nested-TOC', array( array( 'id' => 'first', 'title' => 'A & B' ), array( 'id' => 'zero', 'title' => '0' ) ) === \ToranTejarat\Theme\torantejarat_article_links( parse_blocks( $torantejarat_toc_fixture ) ) );
if ( $torantejarat_woo ) {
	$torantejarat_check( 'M-R27-native-cart-grid-hooks', 99 === has_action( 'woocommerce_before_cart', 'ToranTejarat\\Theme\\torantejarat_cart_layout_start' ) && 1 === has_action( 'woocommerce_after_cart', 'ToranTejarat\\Theme\\torantejarat_cart_layout_end' ) );
	$torantejarat_check( 'M-R28-native-cart-totals-and-cross-sells', 10 === has_action( 'woocommerce_cart_collaterals', 'woocommerce_cart_totals' ) && false === has_action( 'woocommerce_cart_collaterals', 'woocommerce_cross_sell_display' ) && 20 === has_action( 'woocommerce_after_cart', 'woocommerce_cross_sell_display' ) );
}

foreach ( $torantejarat_routes as $torantejarat_route => $torantejarat_request ) {
	$torantejarat_html = $torantejarat_fetch( $torantejarat_route, $torantejarat_request[0], $torantejarat_request[1] );
	if ( null === $torantejarat_html ) { continue; }
	$torantejarat_check( $torantejarat_route . '-single-main', 1 === preg_match_all( '/<main\b/i', $torantejarat_html ) && str_contains( $torantejarat_html, 'id="torantejarat-main"' ) );
	$torantejarat_check( $torantejarat_route . '-skip-link', str_contains( $torantejarat_html, 'href="#torantejarat-main"' ) );
	$torantejarat_check( $torantejarat_route . '-no-php-diagnostics', ! preg_match( '/(?:Fatal error|Parse error|Warning|Notice|Deprecated):/i', $torantejarat_html ) );
	$torantejarat_check( $torantejarat_route . '-no-editor-css', ! str_contains( $torantejarat_html, 'assets/css/editor.css' ) );
	$torantejarat_check( $torantejarat_route . '-source-css', str_contains( $torantejarat_html, '/assets/css/source/theme.css' ) );
	$torantejarat_check( $torantejarat_route . '-no-competing-foundation-css', ! preg_match( '~assets/css/(?:base|layout|components)\.css~', $torantejarat_html ) );
	if ( $torantejarat_request[2] ) {
		$torantejarat_check( $torantejarat_route . '-block-and-classic-content', str_contains( $torantejarat_html, 'TORANTEJARAT_BLOCK_FIXTURE' ) && str_contains( $torantejarat_html, 'TORANTEJARAT_CLASSIC_FIXTURE' ) );
	}
}

fwrite( STDOUT, "Smoke failures: {$torantejarat_failures}. These checks do not replace browser/security/acceptance QA.\n" );
exit( $torantejarat_failures ? 1 : 0 );
