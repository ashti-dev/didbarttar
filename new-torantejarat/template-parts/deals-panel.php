<?php
/**
 * Amazing-deals panel — orange promo side + on-sale product cards.
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
?>
<section class="torantejarat-section"><div class="torantejarat-container">
	<div class="torantejarat-deals-panel">
		<div class="torantejarat-deals-promo">
			<span class="torantejarat-deals-percent" aria-hidden="true">٪</span>
			<h2><?php esc_html_e( 'پیشنهاد شگفت‌انگیز!', 'torantejarat' ); ?></h2>
			<p><?php esc_html_e( 'تخفیف‌های واقعی روی مدل‌های منتخب — تا موجودی هست.', 'torantejarat' ); ?></p>
			<?php if ( $torantejarat_shop_url ) : ?><a class="torantejarat-btn torantejarat-btn-dark" href="<?php echo esc_url( $torantejarat_shop_url ); ?>"><?php esc_html_e( 'مشاهده همه', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
		</div>
		<div class="torantejarat-deals-cards woocommerce">
			<?php foreach ( $torantejarat_deals as $torantejarat_deal ) : ?>
				<?php
				$torantejarat_item = $torantejarat_deal['product'];
				$torantejarat_url  = get_permalink( $torantejarat_item->get_id() );
				?>
				<article class="torantejarat-product-card torantejarat-deal-card">
					<div class="torantejarat-product-image">
						<a href="<?php echo esc_url( $torantejarat_url ); ?>" tabindex="-1" aria-hidden="true">
							<?php if ( $torantejarat_item->get_image_id() ) : ?>
								<?php echo wp_get_attachment_image( $torantejarat_item->get_image_id(), 'torantejarat-card', false, array( 'loading' => 'lazy' ) ); ?>
							<?php else : ?>
								<?php torantejarat_icon( 'scan' ); ?>
							<?php endif; ?>
						</a>
						<span class="torantejarat-deal-badge"><?php echo esc_html( sprintf( __( '٪%s تخفیف', 'torantejarat' ), number_format_i18n( $torantejarat_deal['percent'] ) ) ); ?></span>
					</div>
					<div class="torantejarat-product-info">
						<h3><a href="<?php echo esc_url( $torantejarat_url ); ?>"><?php echo esc_html( $torantejarat_item->get_name() ); ?></a></h3>
						<div class="torantejarat-product-bottom">
							<div><?php echo wp_kses_post( torantejarat_card_price( $torantejarat_item ) ); ?></div>
							<a class="torantejarat-product-open" href="<?php echo esc_url( $torantejarat_url ); ?>" aria-label="<?php echo esc_attr( sprintf( __( 'مشاهده %s', 'torantejarat' ), $torantejarat_item->get_name() ) ); ?>"><?php torantejarat_icon( 'arrow' ); ?></a>
						</div>
					</div>
				</article>
			<?php endforeach; ?>
		</div>
	</div>
</div></section>
