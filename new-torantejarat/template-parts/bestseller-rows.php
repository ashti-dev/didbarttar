<?php
/**
 * Ranked best-seller rows — rank badge + thumb + name + price.
 * Data only: pass array of WC_Product already ordered by sales.
 * Args: items (WC_Product[]).
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_items = isset( $args['items'] ) && is_array( $args['items'] )
	? array_values( array_filter( $args['items'], static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) )
	: array();
if ( ! $torantejarat_items ) { return; }
?>
<div class="torantejarat-bestseller-rows">
	<?php foreach ( $torantejarat_items as $torantejarat_rank => $torantejarat_item ) : ?>
		<?php $torantejarat_url = get_permalink( $torantejarat_item->get_id() ); ?>
		<a class="torantejarat-bestseller-row" href="<?php echo esc_url( $torantejarat_url ); ?>">
			<span class="torantejarat-bestseller-rank" aria-hidden="true"><?php echo esc_html( number_format_i18n( $torantejarat_rank + 1 ) ); ?></span>
			<span class="torantejarat-bestseller-thumb" aria-hidden="true">
				<?php if ( $torantejarat_item->get_image_id() ) : ?>
					<?php echo wp_get_attachment_image( $torantejarat_item->get_image_id(), 'torantejarat-card', false, array( 'loading' => 'lazy' ) ); ?>
				<?php else : ?>
					<?php torantejarat_icon( 'scan' ); ?>
				<?php endif; ?>
			</span>
			<span class="torantejarat-bestseller-body">
				<strong><?php echo esc_html( $torantejarat_item->get_name() ); ?></strong>
				<span class="torantejarat-bestseller-price"><?php echo wp_kses_post( torantejarat_card_price( $torantejarat_item ) ); ?></span>
			</span>
		</a>
	<?php endforeach; ?>
</div>
