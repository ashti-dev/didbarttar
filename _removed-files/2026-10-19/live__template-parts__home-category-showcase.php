<?php
/**
 * Home category showcase — tabbed sliders with compact horizontal cards.
 * Placed after the finder banner; fully driven by native categories/products.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_terms = get_terms( array( 'taxonomy' => 'product_cat', 'hide_empty' => true, 'parent' => 0, 'number' => 4 ) );
if ( is_wp_error( $torantejarat_terms ) || ! $torantejarat_terms ) { return; }
$torantejarat_tabs = array();
foreach ( $torantejarat_terms as $torantejarat_term ) {
	$torantejarat_items = wc_get_products(
		array(
			'status'   => 'publish',
			'category' => array( $torantejarat_term->slug ),
			'limit'    => 6,
			'orderby'  => 'date',
			'order'    => 'DESC',
		)
	);
	$torantejarat_items = array_values( array_filter( $torantejarat_items, static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
	if ( ! $torantejarat_items ) { continue; }
	$torantejarat_tabs[] = array( 'term' => $torantejarat_term, 'items' => $torantejarat_items );
}
if ( count( $torantejarat_tabs ) < 1 ) { return; }
?>
<section class="torantejarat-section torantejarat-category-showcase"><div class="torantejarat-container">
	<div class="torantejarat-section-heading">
		<div>
			<span class="torantejarat-eyebrow"><?php esc_html_e( 'بر اساس کاربرد', 'torantejarat' ); ?></span>
			<h2><?php esc_html_e( 'هر کاربرد، دستگاه خودش', 'torantejarat' ); ?></h2>
		</div>
	</div>
	<div class="torantejarat-cat-tabs" role="tablist" aria-label="<?php esc_attr_e( 'دسته‌بندی دستگاه‌ها', 'torantejarat' ); ?>">
		<?php foreach ( $torantejarat_tabs as $torantejarat_index => $torantejarat_tab ) : ?>
			<button type="button" role="tab" data-cat-tab="<?php echo esc_attr( $torantejarat_index ); ?>" aria-selected="<?php echo $torantejarat_index === 0 ? 'true' : 'false'; ?>"><?php echo esc_html( $torantejarat_tab['term']->name ); ?></button>
		<?php endforeach; ?>
	</div>
	<?php foreach ( $torantejarat_tabs as $torantejarat_index => $torantejarat_tab ) : ?>
		<div class="torantejarat-cat-panel" data-cat-panel="<?php echo esc_attr( $torantejarat_index ); ?>" role="tabpanel">
			<div class="product-slider-wrap"
				data-slider
				data-slides-desktop="2"
				data-slides-tablet="2"
				data-slides-mobile="1"
				data-autoplay="0"
				data-loop="false">
				<div class="product-slider-viewport">
				<div class="product-slider-track">
					<?php foreach ( $torantejarat_tab['items'] as $torantejarat_slider_product ) : ?>
						<?php
						$torantejarat_product_url = get_permalink( $torantejarat_slider_product->get_id() );
						$torantejarat_attributes = torantejarat_product_attribute_items( $torantejarat_slider_product, 2 );
						?>
						<div class="product-slide">
							<article class="torantejarat-product-row">
								<a class="torantejarat-product-row-image" href="<?php echo esc_url( $torantejarat_product_url ); ?>" tabindex="-1" aria-hidden="true">
									<?php if ( $torantejarat_slider_product->get_image_id() ) : ?>
										<?php echo wp_get_attachment_image( $torantejarat_slider_product->get_image_id(), 'medium', false, array( 'loading' => 'lazy' ) ); ?>
									<?php else : ?>
										<?php torantejarat_icon( 'scan' ); ?>
									<?php endif; ?>
								</a>
								<div class="torantejarat-product-row-info">
									<h3><a href="<?php echo esc_url( $torantejarat_product_url ); ?>"><?php echo esc_html( $torantejarat_slider_product->get_name() ); ?></a></h3>
									<?php if ( $torantejarat_attributes ) : ?>
										<div class="torantejarat-spec-chips">
											<?php foreach ( $torantejarat_attributes as $torantejarat_attribute ) : ?><span><?php echo esc_html( $torantejarat_attribute['value'] ); ?></span><?php endforeach; ?>
										</div>
									<?php endif; ?>
									<div class="torantejarat-product-row-bottom">
										<?php echo wp_kses_post( torantejarat_card_price( $torantejarat_slider_product ) ); ?>
										<a class="torantejarat-product-open" href="<?php echo esc_url( $torantejarat_product_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده %s', 'torantejarat' ), $torantejarat_slider_product->get_name() ) ); ?>"><?php torantejarat_icon( 'arrow' ); ?></a>
									</div>
								</div>
							</article>
						</div>
					<?php endforeach; ?>
				</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
</div></section>
