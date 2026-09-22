<?php
/**
 * Sidebar.
 *
 * @package ShalangBin
 */
if ( ! is_active_sidebar( 'blog-sidebar' ) && ! is_active_sidebar( 'shop-sidebar' ) ) {
	return;
}
?>
<aside class="sidebar">
	<?php
	if ( function_exists( 'is_woocommerce' ) && ( is_woocommerce() || is_product_taxonomy() ) && is_active_sidebar( 'shop-sidebar' ) ) {
		dynamic_sidebar( 'shop-sidebar' );
	} elseif ( is_active_sidebar( 'blog-sidebar' ) ) {
		dynamic_sidebar( 'blog-sidebar' );
	}
	?>
</aside>
