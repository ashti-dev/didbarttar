<?php
/**
 * Isolated WP-CLI smoke checks for theme settings.
 *
 * Required by setup-fixture.php after its local/development safety checks. The
 * standalone guard also prevents accidental execution against a public site.
 * These checks do not write options or content to the database.
 */

if ( ! defined( 'WP_CLI' ) || ! WP_CLI ) {
	exit( 1 );
}

$torantejarat_ci_settings_host = wp_parse_url( home_url( '/' ), PHP_URL_HOST );
if ( ! in_array( $torantejarat_ci_settings_host, array( '127.0.0.1', 'localhost' ), true ) || ! in_array( wp_get_environment_type(), array( 'local', 'development' ), true ) ) {
	WP_CLI::error( 'Settings smoke refused: only an isolated local/development WordPress is allowed.' );
}

$GLOBALS['torantejarat_ci_settings_state'] = array(
	'passed' => 0,
	'failed' => array(),
);

/**
 * Register the real Settings API callbacks once for this WP-CLI process.
 */
function torantejarat_ci_settings_register() {
	static $registered = false;
	if ( $registered ) {
		return;
	}
	if ( ! class_exists( 'ToranTejarat\\Theme\\TT_Settings' ) ) {
		throw new RuntimeException( 'TT_Settings is not loaded in WP-CLI.' );
	}
	global $wp_registered_settings;
	if ( ! is_array( $wp_registered_settings ) || empty( $wp_registered_settings['torantejarat_business'] ) ) {
		ToranTejarat\Theme\TT_Settings::get_instance()->register_settings();
	}
	$registered = true;
}

/**
 * Throw a test failure instead of relying on disabled PHP assertions.
 *
 * @param bool   $condition Condition that must be true.
 * @param string $message   Failure message.
 */
function torantejarat_ci_settings_assert( $condition, $message ) {
	if ( ! $condition ) {
		throw new RuntimeException( $message );
	}
}

/**
 * Run one named check and keep executing the remaining independent checks.
 *
 * @param string   $name     Check name.
 * @param callable $callback Test callback.
 */
function torantejarat_ci_settings_run( $name, $callback ) {
	$state =& $GLOBALS['torantejarat_ci_settings_state'];
	try {
		$callback();
		++$state['passed'];
		WP_CLI::log( 'SETTINGS PASS: ' . $name );
	} catch ( Throwable $error ) {
		$state['failed'][] = $name . ': ' . $error->getMessage();
		WP_CLI::log( 'SETTINGS FAIL: ' . end( $state['failed'] ) );
	}
}

torantejarat_ci_settings_run(
	'blank business locality and price range survive sanitization',
	static function () {
		torantejarat_ci_settings_register();
		$clean = sanitize_option(
			'torantejarat_business',
			array(
				'_tt_scope'   => array( 'city', 'price_range' ),
				'city'        => '',
				'price_range' => '',
			)
		);
		torantejarat_ci_settings_assert( is_array( $clean ), 'Sanitized business option is not an array.' );
		torantejarat_ci_settings_assert( array_key_exists( 'city', $clean ) && '' === $clean['city'], 'Blank city was not preserved.' );
		torantejarat_ci_settings_assert( array_key_exists( 'price_range', $clean ) && '' === $clean['price_range'], 'Blank price range was replaced by an assumed value.' );
	}
);

torantejarat_ci_settings_run(
	'organization schema omits unknown locality, address and price range',
	static function () {
		$business_filter = static function () {
			return array(
				'business_name' => 'CI Schema Fixture',
				'business_type' => 'Store',
				'city'          => '',
				'price_range'   => '',
			);
		};
		$contact_filter = static function () {
			return array();
		};
		add_filter( 'pre_option_torantejarat_business', $business_filter );
		add_filter( 'pre_option_torantejarat_contact', $contact_filter );
		try {
			torantejarat_ci_settings_assert( class_exists( 'ToranTejarat\\Theme\\TT_Org_Schema' ), 'TT_Org_Schema is not loaded.' );
			$method = new ReflectionMethod( 'ToranTejarat\\Theme\\TT_Org_Schema', 'build' );
			$method->setAccessible( true );
			$data = $method->invoke( ToranTejarat\Theme\TT_Org_Schema::get_instance() );
		} finally {
			remove_filter( 'pre_option_torantejarat_business', $business_filter );
			remove_filter( 'pre_option_torantejarat_contact', $contact_filter );
		}
		torantejarat_ci_settings_assert( is_array( $data ) && 'CI Schema Fixture' === $data['name'], 'Schema fixture name is missing.' );
		torantejarat_ci_settings_assert( ! isset( $data['address'] ), 'Schema invented an address without real locality or street data.' );
		torantejarat_ci_settings_assert( ! array_key_exists( 'priceRange', $data ), 'Schema invented a priceRange for an unknown setting.' );
	}
);

$torantejarat_ci_settings_state = $GLOBALS['torantejarat_ci_settings_state'];
if ( $torantejarat_ci_settings_state['failed'] ) {
	WP_CLI::error( 'Settings smoke failed: ' . implode( ' | ', $torantejarat_ci_settings_state['failed'] ) );
}
if ( 2 !== $torantejarat_ci_settings_state['passed'] ) {
	WP_CLI::error( 'Settings smoke expected 2 passing checks, got ' . $torantejarat_ci_settings_state['passed'] . '.' );
}
WP_CLI::success( 'Settings smoke: ' . $torantejarat_ci_settings_state['passed'] . ' checks passed; no database writes.' );
