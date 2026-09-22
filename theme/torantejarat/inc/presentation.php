<?php
/**
 * Source-derived, read-only presentation helpers.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_icon( $name ) {
	$paths = array(
		'arrow'   => 'm14 7-5 5 5 5M9 12h12',
		'cart'    => 'M3 3h2l3 12h10l3-9H6M9 21h.01M18 21h.01',
		'scan'    => 'M8 3H3v5M16 3h5v5M3 16v5h5M21 16v5h-5M7 12h10M12 7v10',
		'search'  => 'm21 21-5-5M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0',
		'menu'    => 'M4 6h16M4 12h16M4 18h16',
		'home'    => 'm3 10 9-7 9 7M5 9v12h5v-7h4v7h5V9',
		'chevron' => 'm14 6-6 6 6 6',
	);
	$path = $paths[ $name ] ?? $paths['scan'];
	$size = 'chevron' === $name ? 14 : 24;
	$class = 'chevron' === $name ? 'torantejarat-icon torantejarat-crumb-chevron' : 'torantejarat-icon';
	echo '<svg class="' . esc_attr( $class ) . '" width="' . esc_attr( $size ) . '" height="' . esc_attr( $size ) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="' . esc_attr( $path ) . '"/></svg>';
}

function torantejarat_menu_title( $location ) {
	$locations = get_nav_menu_locations();
	$menu      = isset( $locations[ $location ] ) ? wp_get_nav_menu_object( $locations[ $location ] ) : false;
	return $menu ? $menu->name : '';
}

function torantejarat_post_categories() {
	$categories = get_the_category();
	if ( $categories ) {
		$category = $categories[0];
		echo '<a href="' . esc_url( get_category_link( $category ) ) . '">' . esc_html( $category->name ) . '</a>';
	}
}

// Only existing Gutenberg anchors are used; content/IDs are never rewritten.
function torantejarat_article_links( $blocks ) {
	$links = array();
	foreach ( $blocks as $block ) {
		$anchor = $block['attrs']['anchor'] ?? '';
		if ( 'core/heading' === $block['blockName'] && is_string( $anchor ) && '' !== $anchor && ! preg_match( '/\s/u', $anchor ) ) {
			$title = trim( html_entity_decode( wp_strip_all_tags( $block['innerHTML'] ), ENT_QUOTES | ENT_HTML5, 'UTF-8' ) );
			if ( preg_match( '/[^\s\x{00a0}\x{200b}\x{200c}\x{feff}]/u', $title ) ) {
				$links[] = array( 'id' => $anchor, 'title' => $title );
			}
		}
		if ( ! empty( $block['innerBlocks'] ) ) {
			$links = array_merge( $links, torantejarat_article_links( $block['innerBlocks'] ) );
		}
	}
	// A duplicate authored anchor targets the first heading; never invent or rewrite IDs.
	$unique = array();
	foreach ( $links as $link ) {
		if ( ! isset( $unique[ $link['id'] ] ) ) { $unique[ $link['id'] ] = $link; }
	}
	return array_values( $unique );
}

/** Existing, publicly viewable WP content explicitly selected in a native navigation menu. */
function torantejarat_content_menu_items( $location ) {
	$locations = get_nav_menu_locations();
	if ( empty( $locations[ $location ] ) ) { return array(); }
	$items = wp_get_nav_menu_items( $locations[ $location ] );
	$cards = array();
	foreach ( $items ?: array() as $item ) {
		if ( 'post_type' !== $item->type || ! in_array( $item->object, array( 'post', 'page' ), true ) ) { continue; }
		$content = get_post( $item->object_id );
		if ( ! $content || ! is_post_publicly_viewable( $content ) || post_password_required( $content ) ) { continue; }
		$cards[] = array( 'post' => $content, 'label' => $item->title );
	}
	return $cards;
}

/** Home's source section positions, using only authored core/group classes in post_content. */
function torantejarat_home_content_regions() {
	$regions = array( 'benefits' => array(), 'body' => array(), 'faq' => array() );
	if ( post_password_required() ) { return $regions; }
	foreach ( parse_blocks( get_the_content() ) as $block ) {
		$region  = 'body';
		$class_name = $block['attrs']['className'] ?? '';
		$classes = is_string( $class_name ) ? preg_split( '/\s+/', $class_name ) : array();
		if ( 'core/group' === $block['blockName'] ) {
			if ( in_array( 'torantejarat-benefit-strip', $classes, true ) ) { $region = 'benefits'; }
			if ( in_array( 'torantejarat-home-faq', $classes, true ) ) { $region = 'faq'; }
		}
		$regions[ $region ][] = $block;
	}
	return $regions;
}

function torantejarat_home_content_region( $blocks ) {
	$content = serialize_blocks( $blocks );
	if ( '' === trim( $content ) ) { return; }
	// Keep native block/shortcode/auto-paragraph filters; no custom editor runtime.
	$html = apply_filters( 'the_content', $content ); // Native WP content pipeline, not plain-text escaping.
	if ( '' === trim( $html ) ) { return; }
	echo '<div class="torantejarat-authored-sections">';
	echo $html; // The core render filters also suppress empty source slots.
	echo '</div>';
}
