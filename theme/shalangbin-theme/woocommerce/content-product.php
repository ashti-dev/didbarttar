<?php
/**
 * Shop loop item override — renders the shared theme card inside WC's <li>.
 *
 * @package ShalangBin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Fire standard WC loop hooks (rating/cart buttons) so plugins keep working.
do_action( 'woocommerce_before_shop_loop_item' );
get_template_part( 'template-parts/product-card' );
do_action( 'woocommerce_after_shop_loop_item' );