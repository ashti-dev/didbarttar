<?php
/**
 * One-off setup: replace shop sidebar widgets with product categories block.
 * Delete after running.
 */
define( 'ABSPATH', 'C:/laragon/www/shalangbin/' );
define( 'WPINC', 'wp-includes' );
require 'C:/laragon/www/shalangbin/wp-load.php';

// 1) Find/create product cats block.
$found = null;
foreach ( get_posts( array( 'post_type' => 'wp_block', 'post_status' => 'publish', 'numberposts' => 20 ) ) as $b ) {
	if ( false !== strpos( $b->post_content, 'product-categories' ) ) {
		$found = $b;
		break;
	}
}
if ( ! $found ) {
	$found_id = wp_insert_post( array(
		'post_title'   => 'دسته‌های تجهیزات',
		'post_content' => '<!-- wp:woocommerce/product-categories {"className":"shalangbin-filter-cats"} /-->',
		'post_status'  => 'publish',
		'post_type'    => 'wp_block',
	) );
	echo 'Created reusable block id=' . $found_id . PHP_EOL;
} else {
	$found_id = $found->ID;
	echo 'Found reusable block id=' . $found_id . PHP_EOL;
}

// 2) Assign to shop-sidebar.
$sidebars           = (array) get_option( 'sidebars_widgets', array() );
$sidebars['shop-sidebar'] = array( 'block-' . $found_id );
update_option( 'sidebars_widgets', $sidebars );
echo 'shop-sidebar now: ' . implode( ',', $sidebars['shop-sidebar'] ) . PHP_EOL;

// 3) Render check.
ob_start();
dynamic_sidebar( 'shop-sidebar' );
$html = ob_get_clean();
preg_match_all( '/<(h2|label|span|li|a)[^>]*>([^<]{1,40})</', $html, $t );
foreach ( array_slice( $t[2], 0, 15 ) as $txt ) { echo 'txt: ' . trim( $txt ) . PHP_EOL; }
echo 'has product-cat links: ' . count( preg_match_all( '/product_cat|product-category/', $html, $x ) ? $x[0] : array() ) . PHP_EOL;