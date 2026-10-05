<?php
/**
 * Compare layout — pick up to 3 catalog products, full spec table.
 * Selection via ?p1=&p2=&p3=, ?compare=1,2,3 or ?add= (prefill). Native data only.
 *
 * @package ToranTejarat
 */

/* Template Name: مقایسه — جدول مشخصات */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$torantejarat_catalog = torantejarat_woocommerce_available()
	? wc_get_products( array( 'status' => 'publish', 'visibility' => 'catalog', 'limit' => 50, 'orderby' => 'name', 'order' => 'ASC' ) )
	: array();
$torantejarat_catalog = array_values( array_filter( $torantejarat_catalog, static fn( $item ) => $item instanceof \WC_Product && $item->is_visible() ) );
$torantejarat_by_id   = array();
foreach ( $torantejarat_catalog as $torantejarat_item ) {
	$torantejarat_by_id[ $torantejarat_item->get_id() ] = $torantejarat_item;
}

$torantejarat_wanted = array();
if ( isset( $_GET['compare'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Public read-only filter.
	$torantejarat_wanted = array_merge( $torantejarat_wanted, explode( ',', sanitize_text_field( wp_unslash( $_GET['compare'] ) ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
foreach ( array( 'p1', 'p2', 'p3' ) as $torantejarat_key ) {
	if ( isset( $_GET[ $torantejarat_key ] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$torantejarat_wanted[] = sanitize_text_field( wp_unslash( $_GET[ $torantejarat_key ] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	}
}
if ( isset( $_GET['add'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification.Recommended
	array_unshift( $torantejarat_wanted, sanitize_text_field( wp_unslash( $_GET['add'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended
}
$torantejarat_ids = array();
foreach ( $torantejarat_wanted as $torantejarat_raw ) {
	$torantejarat_id = absint( $torantejarat_raw );
	if ( $torantejarat_id > 0 && isset( $torantejarat_by_id[ $torantejarat_id ] ) && ! in_array( $torantejarat_id, $torantejarat_ids, true ) ) {
		$torantejarat_ids[] = $torantejarat_id;
	}
	if ( count( $torantejarat_ids ) >= 3 ) { break; }
}
$torantejarat_only_diff = isset( $_GET['diff'] ) && '1' === sanitize_text_field( wp_unslash( $_GET['diff'] ) ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

$torantejarat_items = array();
foreach ( $torantejarat_ids as $torantejarat_id ) {
	$torantejarat_items[] = $torantejarat_by_id[ $torantejarat_id ];
}
$torantejarat_maps   = array();
$torantejarat_labels = array();
foreach ( $torantejarat_items as $torantejarat_item ) {
	$torantejarat_map = array();
	foreach ( torantejarat_product_attribute_items( $torantejarat_item, 10 ) as $torantejarat_attr ) {
		$torantejarat_map[ $torantejarat_attr['label'] ] = $torantejarat_attr['value'];
		if ( ! in_array( $torantejarat_attr['label'], $torantejarat_labels, true ) && count( $torantejarat_labels ) < 8 ) {
			$torantejarat_labels[] = $torantejarat_attr['label'];
		}
	}
	$torantejarat_maps[] = $torantejarat_map;
}
if ( $torantejarat_only_diff && count( $torantejarat_items ) > 1 ) {
	$torantejarat_labels = array_values(
		array_filter(
			$torantejarat_labels,
			static function ( $torantejarat_label ) use ( $torantejarat_maps ) {
				$torantejarat_seen = array();
				foreach ( $torantejarat_maps as $torantejarat_map ) {
					$torantejarat_seen[] = $torantejarat_map[ $torantejarat_label ] ?? null;
				}
				return count( array_unique( $torantejarat_seen ) ) > 1;
			}
		)
	);
}

get_header();
?>
<main id="torantejarat-main" class="torantejarat-main" tabindex="-1">
	<?php while ( have_posts() ) : the_post(); ?>
		<?php get_template_part( 'template-parts/page-intro', null, array( 'title' => get_the_title(), 'description' => ! post_password_required() && has_excerpt() ? get_the_excerpt() : '' ) ); ?>
		<section class="torantejarat-section"><div class="torantejarat-container">
			<form class="torantejarat-compare-pickers" method="get" action="<?php echo esc_url( get_permalink() ); ?>">
				<?php for ( $torantejarat_slot = 1; $torantejarat_slot <= 3; $torantejarat_slot++ ) : ?>
					<?php $torantejarat_current = $torantejarat_ids[ $torantejarat_slot - 1 ] ?? 0; ?>
					<label>
						<span><?php echo esc_html( sprintf( __( 'دستگاه %s', 'torantejarat' ), number_format_i18n( $torantejarat_slot ) ) ); ?></span>
						<select name="<?php echo esc_attr( 'p' . $torantejarat_slot ); ?>">
							<option value="0"><?php esc_html_e( 'انتخاب کنید', 'torantejarat' ); ?></option>
							<?php foreach ( $torantejarat_catalog as $torantejarat_option ) : ?>
								<option value="<?php echo esc_attr( $torantejarat_option->get_id() ); ?>"<?php selected( $torantejarat_current, $torantejarat_option->get_id() ); ?>><?php echo esc_html( $torantejarat_option->get_name() ); ?></option>
							<?php endforeach; ?>
						</select>
					</label>
				<?php endfor; ?>
				<label class="torantejarat-compare-diff">
					<input type="checkbox" name="diff" value="1"<?php checked( $torantejarat_only_diff ); ?>>
					<span><?php esc_html_e( 'فقط تفاوت‌ها', 'torantejarat' ); ?></span>
				</label>
				<button type="submit" class="torantejarat-btn torantejarat-btn-primary"><?php esc_html_e( 'مقایسه کن', 'torantejarat' ); ?></button>
			</form>
			<?php if ( count( $torantejarat_items ) < 2 ) : ?>
				<div class="torantejarat-empty-state">
					<?php torantejarat_icon( 'compare' ); ?>
					<p><?php esc_html_e( 'برای مقایسه، دست‌کم دو دستگاه انتخاب کنید.', 'torantejarat' ); ?></p>
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
		</div></section>
	<?php endwhile; ?>
</main>
<?php get_footer(); ?>
