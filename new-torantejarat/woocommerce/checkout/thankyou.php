<?php
/**
 * Order received (thank-you) page.
 *
 * Theme override: torantejarat/woocommerce/checkout/thankyou.php
 * WooCommerce passes: $order (WC_Order|false)
 *
 * @package ToranTejarat
 * @version 11.2.0
 */

use function ToranTejarat\Theme\torantejarat_jalali_ymd;
use function ToranTejarat\Theme\torantejarat_fa_number;
use function ToranTejarat\Theme\torantejarat_tracking_carrier_label;

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/presentation.php';

do_action( 'woocommerce_before_thankyou', $order ? $order->get_id() : 0 );

/* ---------- Empty / invalid order ---------- */
if ( ! $order ) :
	?>
	<div class="torantejarat-thx torantejarat-thx--empty">
		<div class="torantejarat-thx__card">
			<h2><?php esc_html_e( 'سفارشی با این مشخصات پیدا نشد', 'torantejarat' ); ?></h2>
			<p class="torantejarat-thx__sub"><?php esc_html_e( 'ممکن است لینک منقضی شده باشد. سفارش‌های خود را از حساب کاربری ببینید.', 'torantejarat' ); ?></p>
			<div class="torantejarat-thx__actions">
				<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'ادامه خرید', 'torantejarat' ); ?></a>
			</div>
		</div>
	</div>
	<?php
	return;
endif;

/* ---------- Failed ---------- */
if ( $order->has_status( 'failed' ) ) :
	?>
	<div class="torantejarat-thx torantejarat-thx--failed">
		<div class="torantejarat-thx__card">
			<span class="torantejarat-thx__icon torantejarat-thx__icon--failed" aria-hidden="true">
				<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 8v5M12 16.5h.01"/><circle cx="12" cy="12" r="9"/></svg>
			</span>
			<h2><?php esc_html_e( 'پرداخت سفارش شما انجام نشد', 'torantejarat' ); ?></h2>
			<p class="torantejarat-thx__sub"><?php esc_html_e( 'متأسفانه پرداخت سفارش شما توسط بانک رد شد. می‌توانید دوباره تلاش کنید.', 'torantejarat' ); ?></p>
			<div class="torantejarat-thx__actions">
				<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"><?php esc_html_e( 'تلاش مجدد برای پرداخت', 'torantejarat' ); ?></a>
			</div>
		</div>
	</div>
	<?php
	do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
	do_action( 'woocommerce_thankyou', $order->get_id() );
	return;
endif;

/* ---------- Order data ---------- */
$created   = $order->get_date_created();
$status    = $order->get_status();
$is_cod    = 'cod' === $order->get_payment_method();
$is_paid   = $order->is_paid();
$tracking       = $order->get_meta( '_torantejarat_tracking' );
$tracking_label = torantejarat_tracking_carrier_label( $order->get_meta( '_torantejarat_carrier' ) );

$headlines = array(
	'pending'    => __( 'سفارش شما ثبت شد', 'torantejarat' ),
	'on-hold'    => __( 'سفارش شما ثبت شد', 'torantejarat' ),
	'processing' => __( 'سفارش تأیید شد و در حال آماده‌سازی است', 'torantejarat' ),
	'shipped'    => __( 'سفارش شما ارسال شد', 'torantejarat' ),
	'completed'  => __( 'سفارش شما تحویل داده شد', 'torantejarat' ),
	'cancelled'  => __( 'سفارش شما لغو شد', 'torantejarat' ),
	'refunded'   => __( 'مبلغ سفارش شما بازگردانده شد', 'torantejarat' ),
);
$headline  = isset( $headlines[ $status ] ) ? $headlines[ $status ] : __( 'سفارش شما ثبت شد', 'torantejarat' );

if ( $is_paid ) {
	$badge_text = __( 'پرداخت شد', 'torantejarat' );
	$badge_cls  = '';
} elseif ( $is_cod ) {
	$badge_text = __( 'پرداخت هنگام تحویل', 'torantejarat' );
	$badge_cls  = ' torantejarat-thx__badge--pending';
} else {
	$badge_text = __( 'در انتظار پرداخت', 'torantejarat' );
	$badge_cls  = ' torantejarat-thx__badge--pending';
}

$pay_label = $is_cod ? __( 'پرداخت نقدی هنگام تحویل', 'torantejarat' ) : $order->get_payment_method_title();

$steps = array(
	1 => array(
		__( 'ثبت سفارش', 'torantejarat' ),
		$created
			? sprintf( /* translators: %s: Jalali date */ __( 'ثبت شد: %s', 'torantejarat' ), torantejarat_jalali_ymd( $created->date( 'Y' ), $created->date( 'n' ), $created->date( 'j' ) ) )
			: __( 'ثبت شد', 'torantejarat' ),
	),
	2 => array(
		__( 'تأیید و آماده‌سازی', 'torantejarat' ),
		__( 'کارشناس سفارش را بررسی و تأیید می‌کند.', 'torantejarat' ),
	),
	3 => array(
		__( 'ارسال', 'torantejarat' ),
		'' !== $tracking
			? ( '' !== $tracking_label
				? sprintf( /* translators: 1: carrier, 2: tracking code */ __( 'کد مرسوله (%1$s): %2$s', 'torantejarat' ), $tracking_label, $tracking )
				: sprintf( /* translators: %s: tracking code */ __( 'کد مرسوله: %s', 'torantejarat' ), $tracking ) )
			: __( 'پس از ارسال، کد مرسوله اینجا نمایش داده می‌شود.', 'torantejarat' ),
	),
	4 => array(
		__( 'تحویل', 'torantejarat' ),
		__( 'پس از تحویل، وضعیت سفارش اینجا ثبت می‌شود.', 'torantejarat' ),
	),
);

$stage_map = array(
	'pending'    => 2,
	'on-hold'    => 2,
	'processing' => 2,
	'shipped'    => 3,
	'completed'  => 4,
);
$stage     = isset( $stage_map[ $status ] ) ? $stage_map[ $status ] : 2;
$effective = ( 'completed' === $status ) ? 5 : $stage;

$is_logged_in = is_user_logged_in();
$orders_url   = wc_get_account_endpoint_url( 'orders' );
$shop_url     = wc_get_page_permalink( 'shop' );
$contact_page = get_page_by_path( 'contact' );
$contact_url  = $contact_page ? get_permalink( $contact_page ) : home_url( '/' );

$primary_url   = $is_logged_in ? $orders_url : $shop_url;
$primary_label = $is_logged_in ? __( 'پیگیری در حساب کاربری', 'torantejarat' ) : __( 'ادامه خرید', 'torantejarat' );
?>

<div class="torantejarat-thx">

	<header class="torantejarat-thx__hero">
		<span class="torantejarat-thx__icon" aria-hidden="true">
			<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
		</span>
		<div>
			<p class="torantejarat-thx__eyebrow"><?php esc_html_e( 'متشکریم', 'torantejarat' ); ?></p>
			<h1 class="torantejarat-thx__title"><?php echo esc_html( $headline ); ?></h1>
			<p class="torantejarat-thx__sub">
				<?php
				printf(
					/* translators: %s: order number */
					esc_html__( 'شماره سفارش شما %s است. جزئیات را در ادامه ببینید.', 'torantejarat' ),
					'<strong>#' . esc_html( torantejarat_fa_number( $order->get_order_number() ) ) . '</strong>'
				);
				?>
			</p>
			<span class="torantejarat-thx__badge<?php echo esc_attr( $badge_cls ); ?>"><?php echo esc_html( $badge_text ); ?></span>

			<dl class="torantejarat-thx__meta">
				<div>
					<dt><?php esc_html_e( 'شماره سفارش', 'torantejarat' ); ?></dt>
					<dd>#<?php echo esc_html( torantejarat_fa_number( $order->get_order_number() ) ); ?></dd>
				</div>
				<div>
					<dt><?php esc_html_e( 'تاریخ ثبت', 'torantejarat' ); ?></dt>
					<dd><?php echo esc_html( $created ? torantejarat_jalali_ymd( $created->date( 'Y' ), $created->date( 'n' ), $created->date( 'j' ) ) : '—' ); ?></dd>
				</div>
				<div>
					<dt><?php esc_html_e( 'مبلغ نهایی', 'torantejarat' ); ?></dt>
					<dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd>
				</div>
				<div>
					<dt><?php esc_html_e( 'روش پرداخت', 'torantejarat' ); ?></dt>
					<dd><?php echo esc_html( $pay_label ); ?></dd>
				</div>
			</dl>
		</div>
	</header>

	<div class="torantejarat-thx__grid">

		<div class="torantejarat-thx__main">

			<section class="torantejarat-thx__card" aria-labelledby="torantejarat-thx-progress">
				<h2 id="torantejarat-thx-progress"><?php esc_html_e( 'وضعیت سفارش', 'torantejarat' ); ?></h2>
				<ol class="torantejarat-thx__steps">
					<?php foreach ( $steps as $i => $step ) :
						if ( $i < $effective ) {
							$state = 'is-done';
						} elseif ( $i === $stage && 'completed' !== $status ) {
							$state = 'is-active';
						} else {
							$state = 'is-pending';
						}
						?>
						<li class="torantejarat-thx__step <?php echo esc_attr( $state ); ?>" <?php echo 'is-active' === $state ? 'aria-current="step"' : ''; ?>>
							<span class="torantejarat-thx__dot" aria-hidden="true"></span>
							<span class="torantejarat-thx__step-title"><?php echo esc_html( $step[0] ); ?></span>
							<span class="torantejarat-thx__step-desc"><?php echo esc_html( $step[1] ); ?></span>
						</li>
					<?php endforeach; ?>
				</ol>
			</section>

			<section class="torantejarat-thx__card" aria-labelledby="torantejarat-thx-items">
				<h2 id="torantejarat-thx-items"><?php esc_html_e( 'دستگاه‌های سفارش', 'torantejarat' ); ?></h2>
				<?php foreach ( $order->get_items() as $item ) :
					$product = $item->get_product();
					$link    = ( $product && $product->is_visible() ) ? $product->get_permalink() : '';
					?>
					<div class="torantejarat-thx__item">
						<?php if ( $product ) : ?>
							<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'torantejarat-thx__thumb', 'alt' => '' ) ) ); ?>
						<?php endif; ?>
						<div class="torantejarat-thx__item-main">
							<?php if ( $link ) : ?>
								<a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $item->get_name() ); ?></a>
							<?php else : ?>
								<strong><?php echo esc_html( $item->get_name() ); ?></strong>
							<?php endif; ?>
							<div class="torantejarat-thx__sub"><?php printf( esc_html__( 'تعداد: %s', 'torantejarat' ), esc_html( torantejarat_fa_number( $item->get_quantity() ) ) ); ?></div>
						</div>
						<div class="torantejarat-thx__item-price"><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></div>
					</div>
				<?php endforeach; ?>

				<div class="torantejarat-thx__totals">
					<div class="torantejarat-thx__row">
						<span><?php esc_html_e( 'جمع جزء', 'torantejarat' ); ?></span>
						<span><?php echo wp_kses_post( wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) ) ); ?></span>
					</div>
					<div class="torantejarat-thx__row">
						<span><?php esc_html_e( 'ارسال', 'torantejarat' ); ?></span>
						<span><?php echo (float) $order->get_shipping_total() > 0 ? wp_kses_post( wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ) ) : esc_html__( 'رایگان', 'torantejarat' ); ?></span>
					</div>
					<div class="torantejarat-thx__row torantejarat-thx__row--total">
						<span><?php esc_html_e( 'قابل پرداخت', 'torantejarat' ); ?></span>
						<span><?php echo wp_kses_post( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ); ?></span>
					</div>
				</div>
			</section>

			<div class="torantejarat-thx__actions">
				<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $primary_url ); ?>"><?php echo esc_html( $primary_label ); ?></a>
				<div class="torantejarat-thx__secondary">
					<button class="torantejarat-btn torantejarat-btn-outline" type="button" onclick="window.print()"><?php esc_html_e( 'چاپ فاکتور', 'torantejarat' ); ?></button>
					<a class="torantejarat-btn torantejarat-btn-outline" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'ادامه خرید', 'torantejarat' ); ?></a>
				</div>
			</div>

		</div>

		<aside class="torantejarat-thx__side">

			<section class="torantejarat-thx__card" aria-labelledby="torantejarat-thx-customer">
				<h2 id="torantejarat-thx-customer"><?php esc_html_e( 'تحویل به', 'torantejarat' ); ?></h2>
				<p class="torantejarat-thx__name"><?php echo esc_html( $order->get_formatted_billing_full_name() ); ?></p>
				<?php if ( $order->get_billing_phone() ) : ?>
					<p class="torantejarat-thx__phone" dir="ltr"><?php echo esc_html( $order->get_billing_phone() ); ?></p>
				<?php endif; ?>
				<address class="torantejarat-thx__addr">
					<?php echo wp_kses_post( $order->has_shipping_address() ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address() ); ?>
				</address>
			</section>

			<section class="torantejarat-thx__card torantejarat-thx__help" aria-labelledby="torantejarat-thx-help">
				<h2 id="torantejarat-thx-help"><?php esc_html_e( 'سؤالی دارید؟', 'torantejarat' ); ?></h2>
				<p class="torantejarat-thx__sub"><?php esc_html_e( 'درباره سفارش یا انتخاب دستگاه با ما در تماس باشید.', 'torantejarat' ); ?></p>
				<a class="torantejarat-btn torantejarat-btn-outline torantejarat-btn-block" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'ارتباط با پشتیبانی', 'torantejarat' ); ?></a>
			</section>

		</aside>
	</div>
</div>

<?php
/* ---------- Print-only formal invoice (window.print shows only this) ---------- */
$inv_state_name = $order->get_billing_state();
if ( function_exists( 'WC' ) && WC()->countries ) {
	$inv_states = WC()->countries->get_states( $order->get_billing_country() ? $order->get_billing_country() : 'IR' );
	if ( isset( $inv_states[ $inv_state_name ] ) ) {
		$inv_state_name = $inv_states[ $inv_state_name ];
	}
}
$inv_addr = array_filter(
	array(
		$inv_state_name,
		$order->get_billing_city(),
		$order->get_billing_address_1(),
		$order->get_billing_address_2(),
	)
);
$inv_row = 0;
?>
<section class="torantejarat-print-invoice" aria-label="<?php esc_attr_e( 'فاکتور فروش', 'torantejarat' ); ?>">
	<div class="torantejarat-print-invoice__head">
		<div>
			<strong><?php echo esc_html( get_bloginfo( 'name' ) ); ?></strong>
			<span><?php esc_html_e( 'فاکتور فروش', 'torantejarat' ); ?></span>
		</div>
		<div>
			<span><?php esc_html_e( 'شماره سفارش:', 'torantejarat' ); ?> <?php echo esc_html( torantejarat_fa_number( $order->get_order_number() ) ); ?></span>
			<span><?php esc_html_e( 'تاریخ:', 'torantejarat' ); ?> <?php echo esc_html( $created ? torantejarat_jalali_ymd( $created->date( 'Y' ), $created->date( 'n' ), $created->date( 'j' ) ) : '—' ); ?></span>
		</div>
	</div>
	<div class="torantejarat-print-invoice__parties">
		<div>
			<strong><?php esc_html_e( 'فروشنده', 'torantejarat' ); ?></strong>
			<span><?php echo esc_html( get_bloginfo( 'name' ) ); ?></span>
			<span dir="ltr"><?php echo esc_html( home_url( '/' ) ); ?></span>
		</div>
		<div>
			<strong><?php esc_html_e( 'خریدار', 'torantejarat' ); ?></strong>
			<span><?php echo esc_html( $order->get_formatted_billing_full_name() ); ?></span>
			<?php if ( $order->get_billing_phone() ) : ?><span dir="ltr"><?php echo esc_html( $order->get_billing_phone() ); ?></span><?php endif; ?>
			<?php if ( $inv_addr ) : ?><span><?php echo esc_html( implode( ' — ', $inv_addr ) ); ?></span><?php endif; ?>
			<?php if ( $order->get_billing_postcode() ) : ?><span><?php esc_html_e( 'کد پستی:', 'torantejarat' ); ?> <?php echo esc_html( $order->get_billing_postcode() ); ?></span><?php endif; ?>
		</div>
	</div>
	<table class="torantejarat-print-invoice__table">
		<thead>
			<tr>
				<th><?php esc_html_e( 'ردیف', 'torantejarat' ); ?></th>
				<th><?php esc_html_e( 'شرح کالا', 'torantejarat' ); ?></th>
				<th><?php esc_html_e( 'تعداد', 'torantejarat' ); ?></th>
				<th><?php esc_html_e( 'مبلغ (تومان)', 'torantejarat' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $order->get_items() as $item ) : ?>
				<?php
				++$inv_row;
				$inv_product = $item->get_product();
				$inv_sku     = ( $inv_product && $inv_product->get_sku() ) ? ' (' . $inv_product->get_sku() . ')' : '';
				?>
				<tr>
					<td><?php echo esc_html( torantejarat_fa_number( $inv_row ) ); ?></td>
					<td><?php echo esc_html( $item->get_name() . $inv_sku ); ?></td>
					<td><?php echo esc_html( torantejarat_fa_number( $item->get_quantity() ) ); ?></td>
					<td><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<th colspan="3"><?php esc_html_e( 'جمع جزء', 'torantejarat' ); ?></th>
				<td><?php echo wp_kses_post( wc_price( $order->get_subtotal(), array( 'currency' => $order->get_currency() ) ) ); ?></td>
			</tr>
			<tr>
				<th colspan="3"><?php esc_html_e( 'ارسال', 'torantejarat' ); ?></th>
				<td><?php echo (float) $order->get_shipping_total() > 0 ? wp_kses_post( wc_price( $order->get_shipping_total(), array( 'currency' => $order->get_currency() ) ) ) : esc_html__( 'رایگان', 'torantejarat' ); ?></td>
			</tr>
			<tr>
				<th colspan="3"><?php esc_html_e( 'مبلغ نهایی', 'torantejarat' ); ?></th>
				<td><?php echo wp_kses_post( wc_price( $order->get_total(), array( 'currency' => $order->get_currency() ) ) ); ?></td>
			</tr>
		</tfoot>
	</table>
	<div class="torantejarat-print-invoice__foot">
		<span><?php esc_html_e( 'روش پرداخت:', 'torantejarat' ); ?> <?php echo esc_html( $pay_label ); ?></span>
		<span><?php esc_html_e( 'وضعیت:', 'torantejarat' ); ?> <?php echo esc_html( $badge_text ); ?></span>
		<?php if ( '' !== $tracking ) : ?><span><?php esc_html_e( 'کد مرسوله:', 'torantejarat' ); ?> <?php echo esc_html( $tracking ); ?></span><?php endif; ?>
	</div>
	<p class="torantejarat-print-invoice__thanks"><?php esc_html_e( 'از خرید شما سپاسگزاریم.', 'torantejarat' ); ?></p>
</section>

<?php
/* Our layout renders its own items/totals/addresses; drop Woo's native
   details table but keep gateway/tracking callbacks on this hook. */
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
do_action( 'woocommerce_thankyou', $order->get_id() );
