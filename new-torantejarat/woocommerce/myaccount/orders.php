<?php
/**
 * Orders — table on desktop, cards on mobile, chips filter.
 *
 * Theme override: torantejarat/woocommerce/myaccount/orders.php
 * Detail/stepper lives on view-order.
 *
 * @version 9.5.0
 * @package ToranTejarat
 */

use function ToranTejarat\Account\reorder_url;
use function ToranTejarat\Theme\torantejarat_fa_number;
use function ToranTejarat\Theme\torantejarat_jalali_ymd;

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_orders', $has_orders );

$torantejarat_current = isset( $_GET['status'] ) ? sanitize_key( wp_unslash( $_GET['status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only filter.
$torantejarat_chips = array(
	'all'       => __( 'همه', 'torantejarat' ),
	'active'    => __( 'در جریان', 'torantejarat' ),
	'completed' => __( 'تحویل‌شده', 'torantejarat' ),
	'cancelled' => __( 'لغوشده', 'torantejarat' ),
);

$torantejarat_rows = array();
if ( $has_orders ) {
	foreach ( $customer_orders->orders as $customer_order ) {
		$order = wc_get_order( $customer_order ); // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited
		if ( ! $order instanceof WC_Order ) {
			continue;
		}
		$torantejarat_status  = $order->get_status();
		$torantejarat_created = $order->get_date_created();
		$torantejarat_thumbs  = array();
		foreach ( $order->get_items() as $torantejarat_item ) {
			if ( count( $torantejarat_thumbs ) >= 2 ) {
				break;
			}
			$torantejarat_p = $torantejarat_item->get_product();
			if ( $torantejarat_p && $torantejarat_p->get_image_id() ) {
				$torantejarat_thumbs[] = wp_get_attachment_image( $torantejarat_p->get_image_id(), 'thumbnail' );
			} else {
				$torantejarat_thumbs[] = mb_substr( $torantejarat_item->get_name(), 0, 1 );
			}
		}
		$torantejarat_rows[] = array(
			'order'      => $order,
			'status'     => $torantejarat_status,
			'group'      => in_array( $torantejarat_status, array( 'completed' ), true ) ? 'done' : ( in_array( $torantejarat_status, array( 'cancelled', 'refunded', 'failed' ), true ) ? 'cancel' : 'active' ),
			'items'      => $order->get_item_count() - $order->get_item_count_refunded(),
			'thumbs'     => $torantejarat_thumbs,
			'date'       => $torantejarat_created ? torantejarat_jalali_ymd( $torantejarat_created->date( 'Y' ), $torantejarat_created->date( 'n' ), $torantejarat_created->date( 'j' ) ) : '',
			'reorder'    => in_array( $torantejarat_status, array( 'processing', 'completed', 'shipped', 'on-hold' ), true ),
		);
	}
}
?>
<div class="acp__hello">
	<h2><?php esc_html_e( 'سفارش‌های من', 'torantejarat' ); ?></h2>
	<p><?php esc_html_e( 'وضعیت هر سفارش و جزئیات ارسال.', 'torantejarat' ); ?></p>
</div>

<div class="acp-filter" role="group" aria-label="<?php esc_attr_e( 'فیلتر وضعیت', 'torantejarat' ); ?>">
	<?php foreach ( $torantejarat_chips as $torantejarat_key => $torantejarat_label ) : ?>
		<?php
		$torantejarat_url = 'all' === $torantejarat_key
			? remove_query_arg( 'status', wc_get_account_endpoint_url( 'orders' ) )
			: add_query_arg( 'status', $torantejarat_key, wc_get_account_endpoint_url( 'orders' ) );
		$torantejarat_pressed = ( 'all' === $torantejarat_key && '' === $torantejarat_current ) || $torantejarat_current === $torantejarat_key;
		?>
		<a class="acp-chip" href="<?php echo esc_url( $torantejarat_url ); ?>" aria-pressed="<?php echo $torantejarat_pressed ? 'true' : 'false'; ?>"><?php echo esc_html( $torantejarat_label ); ?></a>
	<?php endforeach; ?>
</div>

<?php if ( $torantejarat_rows ) : ?>

	<table class="acp-orders-table">
		<thead>
			<tr>
				<th scope="col"><?php esc_html_e( 'سفارش', 'torantejarat' ); ?></th>
				<th scope="col"><?php esc_html_e( 'تاریخ', 'torantejarat' ); ?></th>
				<th scope="col"><?php esc_html_e( 'وضعیت', 'torantejarat' ); ?></th>
				<th scope="col"><?php esc_html_e( 'مبلغ', 'torantejarat' ); ?></th>
				<th scope="col"><span class="acp-sr"><?php esc_html_e( 'اقدام', 'torantejarat' ); ?></span></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $torantejarat_rows as $torantejarat_row ) : ?>
				<?php $order = $torantejarat_row['order']; ?>
				<tr>
					<td>
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
							<?php printf( esc_html__( '#%s', 'torantejarat' ), esc_html( torantejarat_fa_number( $order->get_order_number() ) ) ); ?>
						</a>
						<small><?php echo esc_html( sprintf( __( '%s قلم', 'torantejarat' ), torantejarat_fa_number( $torantejarat_row['items'] ) ) ); ?></small>
					</td>
					<td><?php echo esc_html( $torantejarat_row['date'] ); ?></td>
					<td><span class="acp-badge<?php echo 'active' === $torantejarat_row['group'] ? ' warn' : ''; ?>"><?php echo esc_html( wc_get_order_status_name( $torantejarat_row['status'] ) ); ?></span></td>
					<td><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></td>
					<td>
						<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( $order->get_view_order_url() ); ?>"><?php esc_html_e( 'جزئیات', 'torantejarat' ); ?></a>
						<?php if ( $torantejarat_row['reorder'] ) : ?>
							<a class="acp-btn acp-btn-primary" href="<?php echo esc_url( reorder_url( $order->get_id() ) ); ?>"><?php esc_html_e( 'خرید مجدد', 'torantejarat' ); ?></a>
						<?php endif; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<div class="acp-orders" id="order-list">
		<?php foreach ( $torantejarat_rows as $torantejarat_row ) : ?>
			<?php $order = $torantejarat_row['order']; ?>
			<article class="acp-order" data-status="<?php echo esc_attr( $torantejarat_row['group'] ); ?>">
				<div class="acp-wish-thumbs" aria-hidden="true">
					<?php foreach ( $torantejarat_row['thumbs'] as $torantejarat_thumb ) : ?>
						<span><?php echo wp_kses_post( $torantejarat_thumb ); ?></span>
					<?php endforeach; ?>
					<?php if ( $torantejarat_row['items'] > 2 ) : ?>
						<span>+<?php echo esc_html( torantejarat_fa_number( $torantejarat_row['items'] - 2 ) ); ?></span>
					<?php endif; ?>
				</div>
				<div class="acp-order-info">
					<strong>
						<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
							<?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( torantejarat_fa_number( $order->get_order_number() ) ) ); ?>
						</a>
					</strong>
					<small>
						<?php echo esc_html( sprintf( __( '%s قلم', 'torantejarat' ), torantejarat_fa_number( $torantejarat_row['items'] ) ) ); ?>
						·
						<?php echo esc_html( $torantejarat_row['date'] ); ?>
					</small>
				</div>
				<div class="acp-order-side">
					<span class="acp-badge<?php echo 'active' === $torantejarat_row['group'] ? ' warn' : ''; ?>"><?php echo esc_html( wc_get_order_status_name( $torantejarat_row['status'] ) ); ?></span>
					<span class="acp-total"><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></span>
				</div>
				<div class="acp-order-actions">
					<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( $order->get_view_order_url() ); ?>"><?php esc_html_e( 'جزئیات و مراحل', 'torantejarat' ); ?></a>
					<?php if ( $torantejarat_row['reorder'] ) : ?>
						<a class="acp-btn acp-btn-primary" href="<?php echo esc_url( reorder_url( $order->get_id() ) ); ?>"><?php esc_html_e( 'خرید مجدد', 'torantejarat' ); ?></a>
					<?php endif; ?>
				</div>
			</article>
		<?php endforeach; ?>
	</div>

	<div class="acp-empty" id="orders-empty" hidden>
		<h3><?php esc_html_e( 'سفارشی با این فیلتر پیدا نشد', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'فیلتر دیگری را امتحان کنید.', 'torantejarat' ); ?></p>
	</div>

	<?php do_action( 'woocommerce_before_account_orders_pagination' ); ?>

	<?php if ( 1 < $customer_orders->max_num_pages ) : ?>
		<div class="acp-actions" style="margin-top:16px">
			<?php if ( 1 !== $current_page ) : ?>
				<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page - 1 ) ); ?>"><?php esc_html_e( 'قبلی', 'torantejarat' ); ?></a>
			<?php endif; ?>
			<?php if ( intval( $customer_orders->max_num_pages ) !== $current_page ) : ?>
				<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( wc_get_endpoint_url( 'orders', $current_page + 1 ) ); ?>"><?php esc_html_e( 'بعدی', 'torantejarat' ); ?></a>
			<?php endif; ?>
		</div>
	<?php endif; ?>

<?php else : ?>

	<div class="acp-empty">
		<h3><?php esc_html_e( 'هنوز سفارشی ثبت نشده است', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'از فروشگاه شروع کنید.', 'torantejarat' ); ?></p>
		<a class="acp-btn acp-btn-primary" href="<?php echo esc_url( apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) ) ); ?>"><?php esc_html_e( 'مشاهده فروشگاه', 'torantejarat' ); ?></a>
	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_account_orders', $has_orders ); ?>
