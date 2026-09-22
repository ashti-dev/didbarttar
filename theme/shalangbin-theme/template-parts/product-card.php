<?php
/**
 * Shared product card — used by front-page featured grid and shop loop override.
 *
 * Expected context: global $product (WooCommerce product object) or the loop.
 *
 * @package ShalangBin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product;

if ( ! $product instanceof WC_Product && function_exists( 'wc_get_product' ) ) {
	$product = wc_get_product( get_the_ID() );
}
if ( ! $product ) {
	return;
}
?>
<?php
// Reference card() markup from build.mjs:
// article.product-card > .product-image(a>img + .product-tag + button.compare-button)
//                     + .product-info(.product-code[en] + h3>a + .spec-chips + .product-bottom)
?>
<article <?php post_class( 'product-card' ); ?> data-product-card="<?php echo esc_attr( $product->get_id() ); ?>">
	<div class="product-image">
		<a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
			<?php
			if ( has_post_thumbnail() ) {
				the_post_thumbnail( 'shalangbin-card' );
			} else {
				wc_placeholder_img( 'woocommerce_thumbnail' );
			}
			?>
		</a>
		<?php if ( $product->is_on_sale() ) : ?>
			<span class="product-tag"><?php esc_html_e( 'تخفیف', 'shalangbin' ); ?></span>
		<?php endif; ?>
		<?php if ( shortcode_exists( 'shalangbin_compare' ) || defined( 'SHALANGBIN_VERSION' ) ) : ?>
			<button class="compare-button"
				data-compare="<?php echo esc_attr( $product->get_id() ); ?>"
				aria-label="<?php echo esc_attr( sprintf( __( 'افزودن %s به مقایسه', 'shalangbin' ), $product->get_name() ) ); ?>"
				aria-pressed="false"><?php shalangbin_icon( 'compare' ); ?></button>
		<?php endif; ?>
	</div>
	<div class="product-info">
		<span class="product-code" dir="ltr"><?php echo esc_html( $product->get_sku() ? $product->get_sku() : $product->get_id() ); ?></span>
		<h3><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h3>
		<?php
		// Spec chips: length / head diameter / resolution when attributes exist.
		$attrs = array(
			array(
				'label' => __( 'کابل', 'shalangbin' ),
				'value' => $product->get_attribute( 'pa_length' ) ? $product->get_attribute( 'pa_length' ) : $product->get_attribute( 'length' ),
				'unit'  => __( 'متر', 'shalangbin' ),
			),
			array(
				'label' => __( 'هد', 'shalangbin' ),
				'value' => $product->get_attribute( 'pa_diameter' ) ? $product->get_attribute( 'pa_diameter' ) : $product->get_attribute( 'diameter' ),
				'unit'  => __( 'میلی‌متر', 'shalangbin' ),
			),
			array(
				'label' => '',
				'value' => $product->get_attribute( 'pa_resolution' ) ? $product->get_attribute( 'pa_resolution' ) : $product->get_attribute( 'resolution' ),
				'unit'  => '',
			),
		);
		?>
		<?php if ( array_filter( wp_list_pluck( $attrs, 'value' ) ) ) : ?>
			<div class="spec-chips">
				<?php
				foreach ( $attrs as $attr ) :
					if ( ! $attr['value'] ) {
						continue;
					}
					?>
					<span><?php echo esc_html( trim( $attr['label'] . ' ' . $attr['value'] . ( $attr['unit'] ? ' ' . $attr['unit'] : '' ) ) ); ?></span>
				<?php endforeach; ?>
			</div>
		<?php endif; ?>
		<div class="product-bottom">
			<div>
				<small><?php esc_html_e( 'قیمت', 'shalangbin' ); ?></small>
				<strong><?php echo wp_kses_post( $product->get_price_html() ); ?></strong>
			</div>
			<a class="product-open" href="<?php the_permalink(); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده %s', 'shalangbin' ), $product->get_name() ) ); ?>"><?php shalangbin_icon( 'arrow' ); ?></a>
		</div>
	</div>
</article>
