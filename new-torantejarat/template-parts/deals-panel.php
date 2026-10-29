<?php
/**
 * Amazing-deals panel — orange promo side + product SLIDER with distinct
 * centered deal cards. Regular price on top, sale price below with spacing.
 * Data only: pass deals as array of array('product'=>WC_Product,'percent'=>int).
 * Percent badges are computed, never hand-written.
 * Args: deals (array).
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_deals = isset( $args['deals'] ) && is_array( $args['deals'] ) ? $args['deals'] : array();
$torantejarat_deals = array_values(
	array_filter(
		$torantejarat_deals,
		static fn( $deal ) => is_array( $deal ) && isset( $deal['product'] ) && $deal['product'] instanceof \WC_Product && $deal['product']->is_visible() && (int) ( $deal['percent'] ?? 0 ) > 0
	)
);
if ( count( $torantejarat_deals ) < 2 ) { return; }
$torantejarat_shop_url = wc_get_page_id( 'shop' ) > 0 ? wc_get_page_permalink( 'shop' ) : '';
$torantejarat_visible  = min( 3, count( $torantejarat_deals ) );
?>
<section class="torantejarat-section"><div class="torantejarat-container">
	<div class="torantejarat-deals-panel">
		<div class="torantejarat-deals-promo">
			<span class="torantejarat-deals-percent" aria-hidden="true">٪</span>
			<h2><?php esc_html_e( 'پیشنهاد شگفت‌انگیز!', 'torantejarat' ); ?></h2>
			<p><?php esc_html_e( 'تخفیف‌های واقعی روی مدل‌های منتخب — تا موجودی هست.', 'torantejarat' ); ?></p>
			<?php if ( $torantejarat_shop_url ) : ?><a class="torantejarat-btn torantejarat-btn-dark" href="<?php echo esc_url( $torantejarat_shop_url ); ?>"><?php esc_html_e( 'مشاهده همه', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
		</div>
		<div class="product-slider-wrap torantejarat-deals-slider"
			data-slider
			data-slides-desktop="<?php echo esc_attr( $torantejarat_visible ); ?>"
			data-slides-tablet="2"
			data-slides-mobile="1"
			data-autoplay="0"
			data-loop="false">
			<div class="product-slider-viewport">
			<div class="product-slider-track">
				<?php foreach ( $torantejarat_deals as $torantejarat_deal ) : ?>
					<?php
					$torantejarat_item    = $torantejarat_deal['product'];
					$torantejarat_url     = get_permalink( $torantejarat_item->get_id() );
					$torantejarat_regular = $torantejarat_item->get_regular_price();
					$torantejarat_price   = $torantejarat_item->get_price();
					?>
					<div class="product-slide">
						<article class="torantejarat-deal-card">
							<span class="torantejarat-deal-badge"><?php echo esc_html( sprintf( __( '٪%s تخفیف', 'torantejarat' ), torantejarat_fa_number( $torantejarat_deal['percent'] ) ) ); ?></span>
							<a class="torantejarat-deal-image" href="<?php echo esc_url( $torantejarat_url ); ?>" tabindex="-1" aria-hidden="true">
								<?php if ( $torantejarat_item->get_image_id() ) : ?>
									<?php echo wp_get_attachment_image( $torantejarat_item->get_image_id(), 'torantejarat-card', false, array( 'loading' => 'lazy' ) ); ?>
								<?php else : ?>
									<?php torantejarat_icon( 'scan' ); ?>
								<?php endif; ?>
							</a>
							<h3><a href="<?php echo esc_url( $torantejarat_url ); ?>"><?php echo esc_html( $torantejarat_item->get_name() ); ?></a></h3>
							<p class="torantejarat-deal-regular"><del><?php echo esc_html( torantejarat_fa_number( $torantejarat_regular ) ); ?> <?php esc_html_e( 'تومان', 'torantejarat' ); ?></del></p>
							<p class="torantejarat-deal-price"><strong><?php echo esc_html( torantejarat_fa_number( $torantejarat_price ) ); ?> <?php esc_html_e( 'تومان', 'torantejarat' ); ?></strong></p>
							<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $torantejarat_url ); ?>"><?php esc_html_e( 'مشاهده و خرید', 'torantejarat' ); ?></a>
						</article>
					</div>
				<?php endforeach; ?>
			</div>
			</div>
		</div>
	</div>
</div></section>
