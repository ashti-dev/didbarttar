<?php
/**
 * Frontend bloat cleanup — fewer requests, less render-blocking weight.
 * Feeds stay (the blog uses them); everything removed here is unused.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_cleanup() {
	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head' );
}

/**
 * Drop jQuery Migrate on the frontend. Theme scripts declare no jQuery
 * dependency; WooCommerce core keeps working on jQuery core alone.
 *
 * @param \WP_Scripts $scripts Script registry.
 */
function torantejarat_dequeue_jquery_migrate( $scripts ) {
	if ( is_admin() || ! isset( $scripts->registered['jquery'] ) ) {
		return;
	}
	$script = $scripts->registered['jquery'];
	if ( ! empty( $script->deps ) ) {
		$script->deps = array_diff( $script->deps, array( 'jquery-migrate' ) );
	}
}
