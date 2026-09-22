<?php
/**
 * Four source product-card slots, using Woo featured products rather than demo IDs.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_products = wc_get_products( array( 'status' => 'publish', 'featured' => true, 'visibility' => 'catalog', 'limit' => 4, 'orderby' => 'date', 'order' => 'DESC' ) );
$torantejarat_products = array_values( array_filter( $torantejarat_products, static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
if ( ! $torantejarat_products ) { return; }
global $product, $post;
$torantejarat_saved_product = $product;
$torantejarat_saved_post    = $post;
?>
<section class="torantejarat-section torantejarat-soft-section"><div class="torantejarat-container woocommerce">
	<div class="torantejarat-section-heading"><h2><?php esc_html_e( 'محصولات منتخب', 'torantejarat' ); ?></h2>
		<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?><a class="torantejarat-text-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'همه محصولات', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
	</div>
	<?php
	wc_setup_loop( array( 'name' => 'torantejarat-home', 'columns' => 4, 'total' => count( $torantejarat_products ), 'is_paginated' => false ) );
	woocommerce_product_loop_start();
	foreach ( $torantejarat_products as $product ) {
		$post = get_post( $product->get_id() );
		setup_postdata( $post );
		wc_get_template_part( 'content', 'product' );
	}
	woocommerce_product_loop_end();
	wc_reset_loop();
	wp_reset_postdata();
	$product = $torantejarat_saved_product;
	$post    = $torantejarat_saved_post;
	?>
</div></section>
