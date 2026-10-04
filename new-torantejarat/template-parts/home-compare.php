<?php
/**
 * Home mini-compare — latest catalog products side by side on real data.
 * Price + shared attribute rows + per-column CTA. No invented values.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_items = wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 3, 'orderby' => 'date', 'order' => 'DESC' ) );
$torantejarat_items = array_values( array_filter( $torantejarat_items, static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
if ( count( $torantejarat_items ) < 2 ) { return; }
$torantejarat_maps  = array();
$torantejarat_labels = array();
foreach ( $torantejarat_items as $torantejarat_item ) {
	$torantejarat_map = array();
	foreach ( torantejarat_product_attribute_items( $torantejarat_item, 6 ) as $torantejarat_attr ) {
		$torantejarat_map[ $torantejarat_attr['label'] ] = $torantejarat_attr['value'];
		if ( ! in_array( $torantejarat_attr['label'], $torantejarat_labels, true ) && count( $torantejarat_labels ) < 3 ) {
			$torantejarat_labels[] = $torantejarat_attr['label'];
		}
	}
	$torantejarat_maps[] = $torantejarat_map;
}
?>
<section class="torantejarat-section torantejarat-compare-section"><div class="torantejarat-container">
	<div class="torantejarat-section-heading">
		<div>
			<span class="torantejarat-eyebrow"><?php esc_html_e( 'مقایسه سریع', 'torantejarat' ); ?></span>
			<h2><?php esc_html_e( 'کدام برای شماست؟', 'torantejarat' ); ?></h2>
			<p><?php esc_html_e( 'قیمت و مشخصات کلیدی مدل‌ها را کنار هم ببینید.', 'torantejarat' ); ?></p>
		</div>
		<?php if ( wc_get_page_id( 'shop' ) > 0 ) : ?><a class="torantejarat-text-link" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'همه دستگاه‌ها', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a><?php endif; ?>
	</div>
	<div class="torantejarat-compare-scroll">
	<table class="torantejarat-compare-table">
		<thead><tr>
			<th scope="col"><span class="torantejarat-sr-only"><?php esc_html_e( 'مشخصه', 'torantejarat' ); ?></span></th>
			<?php foreach ( $torantejarat_items as $torantejarat_item ) : ?>
				<th scope="col">
					<a class="torantejarat-compare-photo" href="<?php echo esc_url( get_permalink( $torantejarat_item->get_id() ) ); ?>" tabindex="-1" aria-hidden="true">
						<?php if ( $torantejarat_item->get_image_id() ) : ?>
							<?php echo wp_get_attachment_image( $torantejarat_item->get_image_id(), 'medium', false, array( 'loading' => 'lazy' ) ); ?>
						<?php else : ?>
							<?php torantejarat_icon( 'scan' ); ?>
						<?php endif; ?>
					</a>
					<span class="torantejarat-compare-name"><a href="<?php echo esc_url( get_permalink( $torantejarat_item->get_id() ) ); ?>"><?php echo esc_html( $torantejarat_item->get_name() ); ?></a></span>
				</th>
			<?php endforeach; ?>
		</tr></thead>
		<tbody>
			<tr>
				<th scope="row"><?php esc_html_e( 'قیمت', 'torantejarat' ); ?></th>
				<?php foreach ( $torantejarat_items as $torantejarat_item ) : ?><td><?php echo wp_kses_post( torantejarat_card_price( $torantejarat_item ) ); ?></td><?php endforeach; ?>
			</tr>
			<?php foreach ( $torantejarat_labels as $torantejarat_label ) : ?>
				<tr>
					<th scope="row"><?php echo esc_html( $torantejarat_label ); ?></th>
					<?php foreach ( $torantejarat_maps as $torantejarat_map ) : ?><td><?php echo isset( $torantejarat_map[ $torantejarat_label ] ) ? esc_html( $torantejarat_map[ $torantejarat_label ] ) : '—'; ?></td><?php endforeach; ?>
				</tr>
			<?php endforeach; ?>
			<tr class="torantejarat-compare-cta">
				<td></td>
				<?php foreach ( $torantejarat_items as $torantejarat_item ) : ?>
					<td><a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( get_permalink( $torantejarat_item->get_id() ) ); ?>"><?php esc_html_e( 'بررسی دستگاه', 'torantejarat' ); ?> <?php torantejarat_icon( 'arrow' ); ?></a></td>
				<?php endforeach; ?>
			</tr>
		</tbody>
	</table>
	</div>
</div></section>
