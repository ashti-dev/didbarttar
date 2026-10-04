<?php
/**
 * Home product slider — Woo products in the professional product-slider.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_products = wc_get_products( array( 'status' => 'publish', 'featured' => true, 'visibility' => 'catalog', 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC' ) );
if ( ! $torantejarat_products ) {
	$torantejarat_products = wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 8, 'orderby' => 'date', 'order' => 'DESC' ) );
}
$torantejarat_products = array_values( array_filter( $torantejarat_products, static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
if ( ! $torantejarat_products ) { return; }
?>
<section class="torantejarat-section torantejarat-soft-section torantejarat-product-slider-section"><div class="torantejarat-container woocommerce">
	<div class="torantejarat-section-heading">
		<div>
			<span class="torantejarat-eyebrow"><?php esc_html_e( 'کاتالوگ تخصصی', 'torantejarat' ); ?></span>
			<h2><?php esc_html_e( 'هر دستگاه، برای یک مسئله', 'torantejarat' ); ?></h2>
			<p><?php esc_html_e( 'مشخصات کلیدی را ببینید و گزینه‌های نزدیک را مقایسه کنید.', 'torantejarat' ); ?></p>
		</div>
		<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?><a class="torantejarat-text-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'همه دستگاه‌ها', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
	</div>
	<div class="product-slider-wrap"
		data-slider
		data-slides-desktop="3"
		data-slides-tablet="2"
		data-slides-mobile="1"
		data-autoplay="0"
		data-loop="false">
		<div class="product-slider-viewport">
		<div class="product-slider-track">
			<?php foreach ( $torantejarat_products as $torantejarat_slider_product ) : ?>
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
