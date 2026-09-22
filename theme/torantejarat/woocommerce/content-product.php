<?php
/**
 * Source shop.html card using WooCommerce public loop hooks. Upstream content-product.php 9.4.0 (Woo 10.8.0) reviewed.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* @version 9.4.0 */
global $product;
if ( ! $product instanceof \WC_Product || ! $product->is_visible() ) { return; }
$torantejarat_card_class = 'torantejarat-product-card';
if ( is_shop() || is_product_taxonomy() ) { $torantejarat_card_class .= ' torantejarat-shop-card'; }
?>
<li <?php wc_product_class( $torantejarat_card_class, $product ); ?>>
	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>
	<div class="torantejarat-product-image"><?php do_action( 'woocommerce_before_shop_loop_item_title' ); ?></div>
	<div class="torantejarat-product-info">
		<?php if ( $product->get_sku() ) : ?><span class="torantejarat-product-code" dir="auto"><?php echo esc_html( $product->get_sku() ); ?></span><?php endif; ?>
		<?php do_action( 'woocommerce_shop_loop_item_title' ); ?>
		<?php if ( ! post_password_required( $product->get_id() ) && $product->get_short_description() ) : ?><p class="torantejarat-shop-card-description"><?php echo esc_html( wp_trim_words( wp_strip_all_tags( $product->get_short_description() ), 24 ) ); ?></p><?php endif; ?>
		<?php $torantejarat_attributes = torantejarat_product_attribute_items( $product, 3 ); ?>
		<?php if ( $torantejarat_attributes ) : ?>
			<?php if ( is_shop() || is_product_taxonomy() ) : ?>
				<dl class="torantejarat-shop-card-specs"><?php foreach ( $torantejarat_attributes as $torantejarat_attribute ) : ?><div><dt><?php echo esc_html( $torantejarat_attribute['label'] ); ?></dt><dd><?php echo esc_html( $torantejarat_attribute['value'] ); ?></dd></div><?php endforeach; ?></dl>
			<?php else : ?>
				<div class="torantejarat-spec-chips"><?php foreach ( $torantejarat_attributes as $torantejarat_attribute ) : ?><span><?php echo esc_html( $torantejarat_attribute['label'] . ': ' . $torantejarat_attribute['value'] ); ?></span><?php endforeach; ?></div>
			<?php endif; ?>
		<?php endif; ?>
		<?php echo wp_kses_post( wc_get_stock_html( $product ) ); ?>
		<div class="torantejarat-product-bottom"><?php do_action( 'woocommerce_after_shop_loop_item_title' ); ?></div>
	</div>
	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>
</li>
