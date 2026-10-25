<?php
/**
 * Cart item data (variations)
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/cart/cart-item-data.php.
 *
 * @see     https://woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates
 * @version 2.4.0
 */

defined( 'ABSPATH' ) || exit;
?>
<dl class="variation">
	<?php foreach ( $item_data as $data ) : ?>
		<div class="fb-variation-item <?php echo sanitize_html_class( 'variation-' . $data['key'] ); ?>">
			<dt class="<?php echo sanitize_html_class( 'variation-' . $data['key'] ); ?>"><?php echo wp_kses_post( $data['key'] ); ?>:</dt>
			<dd class="<?php echo sanitize_html_class( 'variation-' . $data['key'] ); ?>"><?php echo wp_kses_post( wpautop( $data['display'] ) ); ?></dd>
		</div>
	<?php endforeach; ?>
</dl>
