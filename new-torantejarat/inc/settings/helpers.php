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

/**
 * Whether "preview mode" is on (general tab → preview_note).
 *
 * Single source of truth for every sample/preview notice: the homepage FAQ,
 * the product page purchase note, the product FAQ and the footer note.
 * Off by default, so a live store never publishes preview claims by accident.
 *
 * @return bool
 */
function torantejarat_is_preview() {
	return ( 'yes' === torantejarat_option( 'general', 'preview_note', 'no' ) );
}

