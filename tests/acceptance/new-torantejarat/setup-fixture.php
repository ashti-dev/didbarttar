<?php
/**
 * Build an isolated WooCommerce fixture for GitHub Actions.
 *
 * This file is intentionally WP-CLI-only and refuses non-local hosts. It is
 * not loaded by the theme and must never be run against production data.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 1 );
}

$torantejarat_ci_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
if ( ! in_array( $torantejarat_ci_host, array( '127.0.0.1', 'localhost' ), true ) || ! in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) ) {
	WP_CLI::error( 'Fixture refused: only an isolated local/development WordPress is allowed.' );
}
if ( ! class_exists( 'WooCommerce' ) || ! function_exists( 'wc_get_page_id' ) ) {
	WP_CLI::error( 'WooCommerce must be active before creating the fixture.' );
}
$torantejarat_ci_customer_password = getenv( 'CI_CUSTOMER_PASSWORD' );
if ( ! $torantejarat_ci_customer_password ) {
	WP_CLI::error( 'CI_CUSTOMER_PASSWORD is required for the disposable customer.' );
}

/* Classic templates are the subject of this theme acceptance run. */
if ( wc_get_page_id( 'shop' ) <= 0 || wc_get_page_id( 'cart' ) <= 0 || wc_get_page_id( 'checkout' ) <= 0 || wc_get_page_id( 'myaccount' ) <= 0 ) {
	if ( ! class_exists( 'WC_Install' ) ) {
		require_once WC()->plugin_path() . '/includes/class-wc-install.php';
	}
	WC_Install::create_pages();
}
$torantejarat_ci_classic_pages = array(
	'cart'      => '[woocommerce_cart]',
	'checkout'  => '[woocommerce_checkout]',
	'myaccount' => '[woocommerce_my_account]',
);
foreach ( $torantejarat_ci_classic_pages as $torantejarat_ci_page => $torantejarat_ci_shortcode ) {
	$torantejarat_ci_page_id = wc_get_page_id( $torantejarat_ci_page );
	if ( $torantejarat_ci_page_id <= 0 ) {
		WP_CLI::error( "Missing WooCommerce page: {$torantejarat_ci_page}" );
	}
	wp_update_post(
		array(
			'ID'           => $torantejarat_ci_page_id,
			'post_content' => $torantejarat_ci_shortcode,
		)
	);
}

update_option( 'woocommerce_currency', 'IRR' );
update_option( 'woocommerce_default_country', 'IR:THR' );
update_option( 'woocommerce_default_customer_address', 'base' );
update_option( 'woocommerce_ship_to_destination', 'billing_only' );
update_option( 'woocommerce_enable_guest_checkout', 'yes' );
update_option( 'woocommerce_enable_checkout_login_reminder', 'no' );
update_option( 'woocommerce_enable_signup_and_login_from_checkout', 'no' );
update_option( 'woocommerce_calc_taxes', 'no' );
update_option( 'woocommerce_cod_settings',
	array(
		'enabled'            => 'yes',
		'title'              => 'پرداخت در محل آزمایشی',
		'description'        => 'روش پرداخت فقط برای اجرای CI است.',
		'instructions'       => 'این سفارش فقط در محیط آزمایشی GitHub Actions ثبت شده است.',
		'enable_for_methods' => array(),
		'enable_for_virtual' => 'yes',
	)
);

/* One real shipping rate is required by the physical-product checkout. */
$torantejarat_ci_zone = new WC_Shipping_Zone();
$torantejarat_ci_zone->set_zone_name( 'CI — ایران آزمایشی' );
$torantejarat_ci_zone->set_zone_order( 0 );
$torantejarat_ci_zone->save();
$torantejarat_ci_zone->add_location( 'IR', 'country' );
$torantejarat_ci_rate_id = $torantejarat_ci_zone->add_shipping_method( 'flat_rate' );
$torantejarat_ci_rate_key = 'woocommerce_flat_rate_' . absint( $torantejarat_ci_rate_id ) . '_settings';
$torantejarat_ci_rate_settings = get_option( $torantejarat_ci_rate_key, array() );
$torantejarat_ci_rate_settings['title'] = 'ارسال آزمایشی';
$torantejarat_ci_rate_settings['cost'] = '50000';
$torantejarat_ci_rate_settings['tax_status'] = 'none';
update_option( $torantejarat_ci_rate_key, $torantejarat_ci_rate_settings );

$torantejarat_ci_simple_name = 'دوربین آزمایشی CI-100';
$torantejarat_ci_simple_id = wc_get_product_id_by_sku( 'CI-SIMPLE-100' );
if ( ! $torantejarat_ci_simple_id ) {
	$torantejarat_ci_simple = new WC_Product_Simple();
	$torantejarat_ci_simple->set_name( $torantejarat_ci_simple_name );
	$torantejarat_ci_simple->set_slug( 'ci-simple-camera' );
	$torantejarat_ci_simple->set_sku( 'CI-SIMPLE-100' );
	$torantejarat_ci_simple->set_status( 'publish' );
	$torantejarat_ci_simple->set_catalog_visibility( 'visible' );
	$torantejarat_ci_simple->set_regular_price( '2500000' );
	$torantejarat_ci_simple->set_manage_stock( true );
	$torantejarat_ci_simple->set_stock_quantity( 20 );
	$torantejarat_ci_simple->set_stock_status( 'instock' );
	$torantejarat_ci_simple->set_short_description( 'محصول کاملاً آزمایشی برای بررسی قالب؛ برای فروش واقعی نیست.' );
	$torantejarat_ci_simple->set_description( 'این داده فقط برای تست مسیر خرید در GitHub Actions ساخته شده است.' );
	$torantejarat_ci_simple_id = $torantejarat_ci_simple->save();
}

$torantejarat_ci_variable_name = 'دوربین متغیر آزمایشی CI-200';
$torantejarat_ci_variable_id = wc_get_product_id_by_sku( 'CI-VARIABLE-200' );
if ( ! $torantejarat_ci_variable_id ) {
	$torantejarat_ci_attribute = new WC_Product_Attribute();
	$torantejarat_ci_attribute->set_name( 'cable_length' );
	$torantejarat_ci_attribute->set_options( array( '5 m', '10 m' ) );
	$torantejarat_ci_attribute->set_position( 0 );
	$torantejarat_ci_attribute->set_visible( true );
	$torantejarat_ci_attribute->set_variation( true );

	$torantejarat_ci_variable = new WC_Product_Variable();
	$torantejarat_ci_variable->set_name( $torantejarat_ci_variable_name );
	$torantejarat_ci_variable->set_slug( 'ci-variable-camera' );
	$torantejarat_ci_variable->set_sku( 'CI-VARIABLE-200' );
	$torantejarat_ci_variable->set_status( 'publish' );
	$torantejarat_ci_variable->set_catalog_visibility( 'visible' );
	$torantejarat_ci_variable->set_attributes( array( $torantejarat_ci_attribute ) );
	$torantejarat_ci_variable->set_short_description( 'محصول متغیر آزمایشی برای بررسی Attribute و Variation Meta.' );
	$torantejarat_ci_variable_id = $torantejarat_ci_variable->save();

	foreach ( array( '5 m' => '2600000', '10 m' => '3100000' ) as $torantejarat_ci_length => $torantejarat_ci_price ) {
		$torantejarat_ci_variation = new WC_Product_Variation();
		$torantejarat_ci_variation->set_parent_id( $torantejarat_ci_variable_id );
		$torantejarat_ci_variation->set_attributes( array( 'cable_length' => $torantejarat_ci_length ) );
		$torantejarat_ci_variation->set_sku( 'CI-200-' . ( '5 m' === $torantejarat_ci_length ? '5M' : '10M' ) );
		$torantejarat_ci_variation->set_regular_price( $torantejarat_ci_price );
		$torantejarat_ci_variation->set_manage_stock( true );
		$torantejarat_ci_variation->set_stock_quantity( 12 );
		$torantejarat_ci_variation->set_stock_status( 'instock' );
		$torantejarat_ci_variation->set_status( 'publish' );
		$torantejarat_ci_variation->save();
	}
	WC_Product_Variable::sync( $torantejarat_ci_variable_id );
	wc_delete_product_transients( $torantejarat_ci_variable_id );
}

$torantejarat_ci_customer_id = username_exists( 'ci_customer' );
if ( ! $torantejarat_ci_customer_id ) {
	$torantejarat_ci_customer_id = wp_create_user( 'ci_customer', $torantejarat_ci_customer_password, 'ci-customer@example.test' );
}
if ( is_wp_error( $torantejarat_ci_customer_id ) ) {
	WP_CLI::error( $torantejarat_ci_customer_id->get_error_message() );
}
$torantejarat_ci_customer = new WP_User( $torantejarat_ci_customer_id );
$torantejarat_ci_customer->set_role( 'customer' );
update_user_meta( $torantejarat_ci_customer_id, 'billing_first_name', 'مشتری' );
update_user_meta( $torantejarat_ci_customer_id, 'billing_last_name', 'آزمایشی' );
update_user_meta( $torantejarat_ci_customer_id, 'billing_phone', '09123456789' );
update_user_meta( $torantejarat_ci_customer_id, 'billing_email', 'ci-customer@example.test' );

$torantejarat_ci_contact = get_page_by_path( 'contact' );
if ( ! $torantejarat_ci_contact ) {
	$torantejarat_ci_contact_id = wp_insert_post(
		array(
			'post_type'    => 'page',
			'post_status'  => 'publish',
			'post_title'   => 'تماس آزمایشی',
			'post_name'    => 'contact',
			'post_content' => 'این برگه فقط برای آزمون لینک پشتیبانی قالب است.',
		)
	);
	if ( is_wp_error( $torantejarat_ci_contact_id ) ) {
		WP_CLI::error( $torantejarat_ci_contact_id->get_error_message() );
	}
}

flush_rewrite_rules();
wp_cache_flush();

$torantejarat_ci_simple = wc_get_product( $torantejarat_ci_simple_id );
$torantejarat_ci_variable = wc_get_product( $torantejarat_ci_variable_id );
if ( ! $torantejarat_ci_simple || ! $torantejarat_ci_variable || ! $torantejarat_ci_variable->is_type( 'variable' ) ) {
	WP_CLI::error( 'Product fixture verification failed.' );
}
$torantejarat_ci_fixture = array(
	'baseUrl'       => home_url( '/' ),
	'shopUrl'       => wc_get_page_permalink( 'shop' ),
	'cartUrl'       => wc_get_cart_url(),
	'checkoutUrl'   => wc_get_checkout_url(),
	'accountUrl'    => wc_get_page_permalink( 'myaccount' ),
	'simpleId'      => $torantejarat_ci_simple_id,
	'simpleUrl'     => get_permalink( $torantejarat_ci_simple_id ),
	'simpleName'    => $torantejarat_ci_simple_name,
	'variableId'    => $torantejarat_ci_variable_id,
	'variableUrl'   => get_permalink( $torantejarat_ci_variable_id ),
	'variableName'  => $torantejarat_ci_variable_name,
	'customerLogin' => 'ci_customer',
	'customerEmail' => 'ci-customer@example.test',
);
$torantejarat_ci_fixture_file = getenv( 'FIXTURE_FILE' ) ?: '/tmp/torantejarat-fixture.json';
if ( false === file_put_contents( $torantejarat_ci_fixture_file, wp_json_encode( $torantejarat_ci_fixture, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES ) ) ) {
	WP_CLI::error( 'Could not write fixture metadata.' );
}

global $wp_version;
WP_CLI::log( 'PHP=' . PHP_VERSION . ' WP=' . $wp_version . ' Woo=' . WC()->version );
WP_CLI::log( 'Simple product=' . $torantejarat_ci_simple_id . ' Variable product=' . $torantejarat_ci_variable_id . ' Customer=' . $torantejarat_ci_customer_id );
WP_CLI::success( 'Isolated classic WooCommerce fixture is ready.' );

/* Settings checks share the same disposable fixture and safety boundary. */
require __DIR__ . '/settings-smoke.php';
