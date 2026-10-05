<?php
/**
 * Compare results fragment — table or empty state.
 * Shared by the compare page and its AJAX refresh.
 * Args: items (WC_Product[]), maps, labels.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_items  = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
$torantejarat_maps   = isset( $args['maps'] ) && is_array( $args['maps'] ) ? $args['maps'] : array();
$torantejarat_labels = isset( $args['labels'] ) && is_array( $args['labels'] ) ? $args['labels'] : array();
?>
<?php if ( count( $torantejarat_items ) < 2 ) : ?>
	<div class="torantejarat-empty-state">
		<?php torantejarat_icon( 'compare' ); ?>
		<p><?php esc_html_e( 'برای مقایسه، دو دستگاه متفاوت انتخاب کنید.', 'torantejarat' ); ?></p>
	</div>
<?php else : ?>
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
			<tr>
				<th scope="row"><?php esc_html_e( 'وضعیت', 'torantejarat' ); ?></th>
				<?php foreach ( $torantejarat_items as $torantejarat_item ) : ?><td><?php echo $torantejarat_item->is_in_stock() ? esc_html__( 'موجود', 'torantejarat' ) : esc_html__( 'ناموجود', 'torantejarat' ); ?></td><?php endforeach; ?>
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
<?php endif; ?>
