<?php
/**
 * Shared single-row product slider — same engine and cards as home-products.
 * Args: products (WC_Product[]), eyebrow, title, description?, link_url?, link_text?.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_items = isset( $args['products'] ) && is_array( $args['products'] )
	? array_values( array_filter( $args['products'], static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) )
	: array();
if ( ! $torantejarat_items ) { return; }
$torantejarat_eyebrow = isset( $args['eyebrow'] ) ? (string) $args['eyebrow'] : '';
$torantejarat_title   = isset( $args['title'] ) ? (string) $args['title'] : '';
$torantejarat_desc    = isset( $args['description'] ) ? (string) $args['description'] : '';
$torantejarat_link    = isset( $args['link_url'] ) ? (string) $args['link_url'] : '';
$torantejarat_link_tx = isset( $args['link_text'] ) ? (string) $args['link_text'] : '';
$torantejarat_visible = min( 3, count( $torantejarat_items ) );
?>
<section class="torantejarat-section torantejarat-soft-section torantejarat-product-slider-section"><div class="torantejarat-container woocommerce">
	<div class="torantejarat-section-heading">
		<div>
			<?php if ( '' !== $torantejarat_eyebrow ) : ?><span class="torantejarat-eyebrow"><?php echo esc_html( $torantejarat_eyebrow ); ?></span><?php endif; ?>
			<?php if ( '' !== $torantejarat_title ) : ?><h2><?php echo esc_html( $torantejarat_title ); ?></h2><?php endif; ?>
			<?php if ( '' !== $torantejarat_desc ) : ?><p><?php echo esc_html( $torantejarat_desc ); ?></p><?php endif; ?>
		</div>
		<?php if ( '' !== $torantejarat_link && '' !== $torantejarat_link_tx ) : ?><a class="torantejarat-text-link" href="<?php echo esc_url( $torantejarat_link ); ?>"><?php echo esc_html( $torantejarat_link_tx ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
	</div>
	<div class="product-slider-wrap"
		data-slider
		data-slides-desktop="<?php echo esc_attr( $torantejarat_visible ); ?>"
		data-slides-tablet="2"
		data-slides-mobile="1"
		data-autoplay="0"
		data-loop="false">
		<div class="product-slider-viewport">
		<div class="product-slider-track">
			<?php foreach ( $torantejarat_items as $torantejarat_slider_product ) : ?>
				<?php
				$torantejarat_product_id    = $torantejarat_slider_product->get_id();
				$torantejarat_product_url   = get_permalink( $torantejarat_product_id );
				$torantejarat_product_terms = get_the_terms( $torantejarat_product_id, 'product_cat' );
				$torantejarat_product_tag   = ( $torantejarat_product_terms && ! is_wp_error( $torantejarat_product_terms ) ) ? $torantejarat_product_terms[0]->name : '';
				$torantejarat_attributes    = torantejarat_product_attribute_items( $torantejarat_slider_product, 3 );
				?>
				<div class="product-slide">
					<article class="torantejarat-product-card">
						<div class="torantejarat-product-image">
							<a href="<?php echo esc_url( $torantejarat_product_url ); ?>" tabindex="-1" aria-hidden="true">
								<?php if ( $torantejarat_slider_product->get_image_id() ) : ?>
									<?php echo wp_get_attachment_image( $torantejarat_slider_product->get_image_id(), 'medium', false, array( 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<?php torantejarat_icon( 'scan' ); ?>
								<?php endif; ?>
							</a>
							<?php if ( $torantejarat_product_tag ) : ?><span class="torantejarat-product-tag"><?php echo esc_html( $torantejarat_product_tag ); ?></span><?php endif; ?>
						</div>
						<div class="torantejarat-product-info">
							<?php if ( $torantejarat_slider_product->get_sku() ) : ?><span class="torantejarat-product-code" dir="auto"><?php echo esc_html( $torantejarat_slider_product->get_sku() ); ?></span><?php endif; ?>
							<h3><a href="<?php echo esc_url( $torantejarat_product_url ); ?>"><?php echo esc_html( $torantejarat_slider_product->get_name() ); ?></a></h3>
							<?php if ( $torantejarat_attributes ) : ?>
								<div class="torantejarat-spec-chips">
									<?php foreach ( $torantejarat_attributes as $torantejarat_attribute ) : ?><span><?php echo esc_html( $torantejarat_attribute['value'] ); ?></span><?php endforeach; ?>
								</div>
							<?php endif; ?>
							<div class="torantejarat-product-bottom">
								<div>
									<small><?php esc_html_e( 'قیمت', 'torantejarat' ); ?></small>
									<?php echo wp_kses_post( torantejarat_card_price( $torantejarat_slider_product ) ); ?>
								</div>
								<a class="torantejarat-product-open" href="<?php echo esc_url( $torantejarat_product_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده %s', 'torantejarat' ), $torantejarat_slider_product->get_name() ) ); ?>"><?php torantejarat_icon( 'arrow' ); ?></a>
							</div>
						</div>
					</article>
				</div>
			<?php endforeach; ?>
		</div>
		</div>
	</div>
</div></section>
