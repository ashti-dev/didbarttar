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
 * Build deterministic, non-empty FAQ rows for boundary checks.
 *
 * @param int    $count  Number of rows.
 * @param string $prefix Value prefix.
 * @return array<int,array{q:string,a:string}>
 */
function torantejarat_ci_settings_faq_rows( $count, $prefix = 'CI' ) {
	$rows = array();
	for ( $index = 0; $index < $count; ++$index ) {
		$rows[] = array(
			'q' => $prefix . '-Q-' . $index,
			'a' => $prefix . '-A-' . $index,
		);
	}
	return $rows;
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

torantejarat_ci_settings_run(
	'home scalar defaults have one canonical source for panel and frontend',
	static function () {
		torantejarat_ci_settings_register();
		$settings = ToranTejarat\Theme\TT_Settings::get_instance();
		$property = new ReflectionProperty( 'ToranTejarat\\Theme\\TT_Settings', 'schema' );
		$property->setAccessible( true );
		$schema   = $property->getValue( $settings );
		$defaults = ToranTejarat\Theme\torantejarat_home_defaults();
		torantejarat_ci_settings_assert( 34 === count( $schema['home']['fields'] ), 'The home schema must contain 34 fields.' );
		torantejarat_ci_settings_assert( 33 === count( $defaults ), 'The canonical home copy source must contain 33 scalar defaults.' );
		$scalar_keys = array();
		foreach ( $schema['home']['fields'] as $key => $field ) {
			if ( 'repeater' === $field['type'] ) {
				continue;
			}
			$scalar_keys[] = $key;
			torantejarat_ci_settings_assert( array_key_exists( $key, $defaults ), 'Canonical home default missing: ' . $key );
			torantejarat_ci_settings_assert( array_key_exists( 'default', $field ), 'Hydrated schema default missing: ' . $key );
			torantejarat_ci_settings_assert( $defaults[ $key ] === $field['default'], 'Panel/frontend default mismatch: ' . $key );
		}
		torantejarat_ci_settings_assert( array_keys( $defaults ) === $scalar_keys, 'Home default keys or their schema order changed unexpectedly.' );
		$faq = $schema['home']['fields']['faq'];
		torantejarat_ci_settings_assert( array() === $faq['default'], 'FAQ must retain its empty stored default.' );
		torantejarat_ci_settings_assert( ToranTejarat\Theme\torantejarat_home_faq_defaults() === call_user_func( $faq['default_callback'] ), 'FAQ callback is not the canonical frontend fallback.' );
	}
);

torantejarat_ci_settings_run(
	'FAQ sanitizer caps new rows at twelve without deleting legacy overflow',
	static function () {
		torantejarat_ci_settings_register();
		$empty_home_filter = static function () {
			return array();
		};
		add_filter( 'pre_option_torantejarat_home', $empty_home_filter );
		try {
			$clean = sanitize_option(
				'torantejarat_home',
				array(
					'_tt_scope' => array( 'faq' ),
					'faq'       => torantejarat_ci_settings_faq_rows( 13 ),
				)
			);
		} finally {
			remove_filter( 'pre_option_torantejarat_home', $empty_home_filter );
		}
		torantejarat_ci_settings_assert( isset( $clean['faq'] ) && is_array( $clean['faq'] ), 'Sanitized FAQ value is missing.' );
		torantejarat_ci_settings_assert( 12 === count( $clean['faq'] ), 'A new thirteen-row submission was not capped at twelve.' );
		torantejarat_ci_settings_assert( range( 0, 11 ) === array_keys( $clean['faq'] ), 'FAQ rows were not re-indexed cleanly.' );
		torantejarat_ci_settings_assert( 'CI-Q-11' === $clean['faq'][11]['q'], 'The twelfth row was not preserved.' );

		$legacy_rows   = torantejarat_ci_settings_faq_rows( 13, 'LEGACY' );
		$legacy_filter = static function () use ( $legacy_rows ) {
			return array( 'faq' => $legacy_rows );
		};
		add_filter( 'pre_option_torantejarat_home', $legacy_filter );
		try {
			$legacy_input   = $legacy_rows;
			$legacy_input[] = array( 'q' => 'EXTRA-Q', 'a' => 'EXTRA-A' );
			$legacy_clean   = sanitize_option(
				'torantejarat_home',
				array(
					'_tt_scope' => array( 'faq' ),
					'faq'       => $legacy_input,
				)
			);
		} finally {
			remove_filter( 'pre_option_torantejarat_home', $legacy_filter );
		}
		torantejarat_ci_settings_assert( 13 === count( $legacy_clean['faq'] ), 'Saving legacy overflow silently deleted an existing row.' );
		torantejarat_ci_settings_assert( 'LEGACY-Q-12' === $legacy_clean['faq'][12]['q'], 'The existing legacy row was not preserved.' );
	}
);

torantejarat_ci_settings_run(
	'FAQ renderer adds a trailing blank row only below the cap',
	static function () {
		torantejarat_ci_settings_register();
		$settings = ToranTejarat\Theme\TT_Settings::get_instance();
		$property = new ReflectionProperty( 'ToranTejarat\\Theme\\TT_Settings', 'schema' );
		$property->setAccessible( true );
		$schema = $property->getValue( $settings );
		$field  = $schema['home']['fields']['faq'];
		$method = new ReflectionMethod( 'ToranTejarat\\Theme\\TT_Settings', 'render_repeater_field' );
		$method->setAccessible( true );
		$render = static function ( array $rows ) use ( $method, $settings, $field ) {
			ob_start();
			try {
				$method->invoke( $settings, 'home', 'faq', $field, $rows );
				return ob_get_clean();
			} catch ( Throwable $error ) {
				ob_end_clean();
				throw $error;
			}
		};
		$below = $render( torantejarat_ci_settings_faq_rows( 11 ) );
		$at_cap = $render( torantejarat_ci_settings_faq_rows( 12 ) );
		$legacy = $render( torantejarat_ci_settings_faq_rows( 13, 'LEGACY' ) );
		torantejarat_ci_settings_assert( 12 === substr_count( $below, 'class="tt-repeater-row"' ), 'Below-cap form lost its no-JavaScript blank row.' );
		torantejarat_ci_settings_assert( 12 === substr_count( $at_cap, 'class="tt-repeater-row"' ), 'At-cap form rendered an editable thirteenth row.' );
		torantejarat_ci_settings_assert( 13 === substr_count( $legacy, 'class="tt-repeater-row"' ), 'Legacy rows were hidden or a fourteenth blank row was added.' );
	}
);

torantejarat_ci_settings_run(
	'readiness email items resolve to their native corrective pages',
	static function () {
		$values = array(
			'admin_email'                    => 'admin@example.com',
			'woocommerce_email_from_address' => 'admin@example.com',
			'torantejarat_contact'           => array(),
			'torantejarat_general'           => array(),
			'torantejarat_social'            => array(),
			'woocommerce_coming_soon'        => 'no',
			'blog_public'                    => '1',
		);
		$filters = array();
		foreach ( $values as $option => $value ) {
			$filters[ $option ] = static function () use ( $value ) {
				return $value;
			};
			add_filter( 'pre_option_' . $option, $filters[ $option ] );
		}
		try {
			torantejarat_ci_settings_assert( function_exists( 'ToranTejarat\\Theme\\torantejarat_panel_readiness' ), 'Readiness source is not loaded.' );
			$items = ToranTejarat\Theme\torantejarat_panel_readiness();
		} finally {
			foreach ( $filters as $option => $filter ) {
				remove_filter( 'pre_option_' . $option, $filter );
			}
		}
		torantejarat_ci_settings_assert( 2 === count( $items ), 'The isolated readiness fixture should contain only two email items.' );
		$admin_item = null;
		$woo_item   = null;
		foreach ( $items as $item ) {
			if ( false !== strpos( $item['label'], 'admin_email' ) ) {
				$admin_item = $item;
			}
			if ( false !== strpos( $item['label'], 'فرستنده' ) ) {
				$woo_item = $item;
			}
		}
		torantejarat_ci_settings_assert( is_array( $admin_item ) && is_array( $woo_item ), 'Email readiness items were not identified.' );
		torantejarat_ci_settings_assert( function_exists( 'ToranTejarat\\Theme\\torantejarat_panel_readiness_url' ), 'Readiness URL resolver is missing.' );
		torantejarat_ci_settings_assert( admin_url( 'options-general.php' ) === ToranTejarat\Theme\torantejarat_panel_readiness_url( $admin_item ), 'Admin email item does not lead to WordPress general settings.' );
		torantejarat_ci_settings_assert( admin_url( 'admin.php?page=wc-settings&tab=email' ) === ToranTejarat\Theme\torantejarat_panel_readiness_url( $woo_item ), 'WooCommerce sender item does not lead to WooCommerce email settings.' );
		$internal = array( 'section' => 'home', 'sub' => 'faq' );
		torantejarat_ci_settings_assert( admin_url( 'admin.php?page=torantejarat-settings&section=home&sub=faq' ) === ToranTejarat\Theme\torantejarat_panel_readiness_url( $internal ), 'Internal readiness links lost their existing destination.' );
	}
);

$torantejarat_ci_settings_state = $GLOBALS['torantejarat_ci_settings_state'];
if ( $torantejarat_ci_settings_state['failed'] ) {
	WP_CLI::error( 'Settings smoke failed: ' . implode( ' | ', $torantejarat_ci_settings_state['failed'] ) );
}
if ( 6 !== $torantejarat_ci_settings_state['passed'] ) {
	WP_CLI::error( 'Settings smoke expected 6 passing checks, got ' . $torantejarat_ci_settings_state['passed'] . '.' );
}
WP_CLI::success( 'Settings smoke: ' . $torantejarat_ci_settings_state['passed'] . ' checks passed; no database writes.' );
