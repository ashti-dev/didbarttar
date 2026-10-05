<?php
/**
 * Settings helpers — static cache, one DB read per tab per request.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Read one theme setting value.
 *
 * @param string $tab     Tab name: general | contact | social.
 * @param string $key     Field name.
 * @param mixed  $default Fallback when empty.
 * @return mixed
 */
function torantejarat_option( $tab, $key, $default = '' ) {
	static $cache = array();
	if ( ! array_key_exists( $tab, $cache ) ) {
		$cache[ $tab ] = get_option( 'torantejarat_' . $tab, array() );
	}
	$value = isset( $cache[ $tab ][ $key ] ) ? $cache[ $tab ][ $key ] : '';
	return ( '' !== $value && null !== $value ) ? $value : $default;
}

/**
 * Whether a social network URL is set (for conditional footer icons).
 *
 * @param string $network Network key: instagram | telegram | aparat | youtube.
 * @return bool
 */
function torantejarat_has_social( $network ) {
	return (bool) torantejarat_option( 'social', $network );
}
