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
use function ToranTejarat\Theme\torantejarat_order_status_badge;

defined( 'ABSPATH' ) || exit;

require_once get_template_directory() . '/inc/presentation.php';

do_action( 'woocommerce_before_thankyou', $order ? $order->get_id() : 0 );

/* ---------- Empty / invalid order ---------- */
if ( ! $order ) :
	?>
	<div class="torantejarat-thx torantejarat-thx--empty">
		<section class="torantejarat-thx__state-card" aria-labelledby="torantejarat-thx-empty-title">
			<span class="torantejarat-thx__state-icon" aria-hidden="true">
				<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M6 2h12v20l-3-2-3 2-3-2-3 2V2Z"/><path d="M9 7h6M9 11h6M9 15h3"/></svg>
			</span>
			<p class="torantejarat-thx__eyebrow"><?php esc_html_e( 'رسید سفارش', 'torantejarat' ); ?></p>
			<h1 id="torantejarat-thx-empty-title"><?php esc_html_e( 'رسید این سفارش در دسترس نیست', 'torantejarat' ); ?></h1>
			<p class="torantejarat-thx__sub"><?php esc_html_e( 'لینک رسید ممکن است ناقص یا منقضی باشد. برای دیدن سفارش های ثبت شده، وارد حساب کاربری شوید.', 'torantejarat' ); ?></p>
			<div class="torantejarat-thx__actions">
				<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( wc_get_page_permalink( 'shop' ) ); ?>"><?php esc_html_e( 'مشاهده فروشگاه', 'torantejarat' ); ?></a>
				<?php if ( wc_get_page_id( 'myaccount' ) > 0 ) : ?>
					<a class="torantejarat-btn torantejarat-btn-outline" href="<?php echo esc_url( is_user_logged_in() ? wc_get_account_endpoint_url( 'orders' ) : wc_get_page_permalink( 'myaccount' ) ); ?>"><?php echo esc_html( is_user_logged_in() ? __( 'سفارش های من', 'torantejarat' ) : __( 'ورود به حساب کاربری', 'torantejarat' ) ); ?></a>
				<?php endif; ?>
			</div>
		</section>
	</div>
	<?php
	return;
endif;

/* ---------- Failed ---------- */
if ( $order->has_status( 'failed' ) ) :
	?>
	<div class="torantejarat-thx torantejarat-thx--failed">
		<section class="torantejarat-thx__state-card" aria-labelledby="torantejarat-thx-failed-title">
			<span class="torantejarat-thx__state-icon" aria-hidden="true">
				<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v6M12 17h.01"/></svg>
			</span>
			<p class="torantejarat-thx__eyebrow"><?php esc_html_e( 'نتیجه پرداخت', 'torantejarat' ); ?></p>
			<h1 id="torantejarat-thx-failed-title"><?php esc_html_e( 'پرداخت این سفارش تکمیل نشد', 'torantejarat' ); ?></h1>
			<p class="torantejarat-thx__sub"><?php esc_html_e( 'نتیجه موفق برای تراکنش ثبت نشده است. می توانید از مسیر امن پرداخت دوباره تلاش کنید یا سفارش را در حساب خود بررسی کنید.', 'torantejarat' ); ?></p>
			<dl class="torantejarat-thx__state-meta">
				<div><dt><?php esc_html_e( 'شماره سفارش', 'torantejarat' ); ?></dt><dd>#<?php echo esc_html( torantejarat_fa_number( $order->get_order_number() ) ); ?></dd></div>
				<div><dt><?php esc_html_e( 'مبلغ سفارش', 'torantejarat' ); ?></dt><dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div>
			</dl>
			<div class="torantejarat-thx__actions">
				<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $order->get_checkout_payment_url() ); ?>"><?php esc_html_e( 'رفتن به صفحه پرداخت', 'torantejarat' ); ?></a>
				<?php if ( is_user_logged_in() ) : ?>
					<a class="torantejarat-btn torantejarat-btn-outline" href="<?php echo esc_url( wc_get_account_endpoint_url( 'orders' ) ); ?>"><?php esc_html_e( 'مشاهده سفارش ها', 'torantejarat' ); ?></a>
				<?php endif; ?>
			</div>
			<div class="torantejarat-thx__extensions">
				<?php
				do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
				do_action( 'woocommerce_thankyou', $order->get_id() );
				?>
			</div>
		</section>
	</div>
	<?php
	return;
endif;

/* ---------- Order data ---------- */
$created        = $order->get_date_created();
$status         = $order->get_status();
$is_cod         = 'cod' === $order->get_payment_method();
$badge          = torantejarat_order_status_badge( $order );
$tracking       = trim( (string) $order->get_meta( '_torantejarat_tracking' ) );
$tracking_label = torantejarat_tracking_carrier_label( $order->get_meta( '_torantejarat_carrier' ) );
$customer_name  = trim( (string) $order->get_billing_first_name() );

$headlines = array(
	'pending'    => __( 'سفارش شما در فروشگاه ثبت شد', 'torantejarat' ),
	'on-hold'    => __( 'سفارش شما در صف بررسی است', 'torantejarat' ),
	'processing' => __( 'سفارش شما در حال آماده سازی است', 'torantejarat' ),
	'shipped'    => __( 'سفارش شما راهی مقصد شده است', 'torantejarat' ),
	'completed'  => __( 'سفارش شما با موفقیت تکمیل شد', 'torantejarat' ),
	'cancelled'  => __( 'این سفارش لغو شده است', 'torantejarat' ),
	'refunded'   => __( 'بازگشت وجه این سفارش ثبت شد', 'torantejarat' ),
);
$headline = isset( $headlines[ $status ] ) ? $headlines[ $status ] : __( 'رسید سفارش شما آماده است', 'torantejarat' );

$descriptions = array(
	'pending'    => __( 'سفارش در سیستم ثبت شده است. برای اقدام بعدی، وضعیت پرداخت و راهنمای همین صفحه را بررسی کنید.', 'torantejarat' ),
	'on-hold'    => __( 'اطلاعات سفارش در حال بررسی است. هر تغییر وضعیت در همین رسید و در حساب کاربری شما قابل مشاهده خواهد بود.', 'torantejarat' ),
	'processing' => __( 'سفارش وارد مرحله بررسی و آماده سازی شده است. جزئیات اقلام و مسیر پیگیری را در ادامه می بینید.', 'torantejarat' ),
	'shipped'    => __( 'مرسوله تحویل شرکت ارسال شده است. اگر کد رهگیری ثبت شده باشد، در بخش وضعیت سفارش نمایش داده می شود.', 'torantejarat' ),
	'completed'  => __( 'فرایند این سفارش به پایان رسیده است. رسید خرید برای مراجعه بعدی در حساب کاربری شما باقی می ماند.', 'torantejarat' ),
	'cancelled'  => __( 'این سفارش فعال نیست. برای بررسی علت لغو یا ثبت سفارش تازه می توانید با پشتیبانی در تماس باشید.', 'torantejarat' ),
	'refunded'   => __( 'روند بازگشت وجه برای این سفارش ثبت شده است. مبلغ ها و جزئیات مالی در خلاصه فاکتور آمده است.', 'torantejarat' ),
);
$description = isset( $descriptions[ $status ] ) ? $descriptions[ $status ] : __( 'آخرین وضعیت ثبت شده و جزئیات مالی سفارش را در این رسید بررسی کنید.', 'torantejarat' );

$badge_text = $badge['label'];
$badge_cls  = ' torantejarat-thx__badge--' . $badge['state'];
$pay_label  = $is_cod ? __( 'پرداخت نقدی هنگام تحویل', 'torantejarat' ) : $order->get_payment_method_title();
if ( ! $pay_label ) {
	$pay_label = __( 'ثبت نشده', 'torantejarat' );
}

$review_is_pending = ! in_array( $status, array( 'processing', 'shipped', 'completed' ), true );
$steps = array(
	1 => array(
		__( 'ثبت سفارش', 'torantejarat' ),
		$created
			? sprintf( /* translators: %s: Jalali date */ __( 'تاریخ ثبت: %s', 'torantejarat' ), torantejarat_jalali_ymd( $created->date( 'Y' ), $created->date( 'n' ), $created->date( 'j' ) ) )
			: __( 'در فروشگاه ثبت شد', 'torantejarat' ),
	),
	2 => array(
		$review_is_pending ? __( 'بررسی سفارش', 'torantejarat' ) : __( 'آماده سازی', 'torantejarat' ),
		$review_is_pending
			? __( 'پرداخت و اطلاعات سفارش برای ورود به مرحله بعد بررسی می شود.', 'torantejarat' )
			: __( 'اقلام سفارش برای بسته بندی و تحویل به شرکت ارسال آماده می شوند.', 'torantejarat' ),
	),
	3 => array(
		__( 'ارسال مرسوله', 'torantejarat' ),
		'' !== $tracking
			? ( '' !== $tracking_label
				? sprintf( /* translators: 1: carrier, 2: tracking code */ __( '%1$s — کد رهگیری: %2$s', 'torantejarat' ), $tracking_label, $tracking )
				: sprintf( /* translators: %s: tracking code */ __( 'کد رهگیری: %s', 'torantejarat' ), $tracking ) )
			: __( 'کد رهگیری پس از ثبت در همین بخش قرار می گیرد.', 'torantejarat' ),
	),
	4 => array(
		__( 'تحویل به شما', 'torantejarat' ),
		'completed' === $status
			? __( 'وضعیت تکمیل سفارش در رسید ثبت شده است.', 'torantejarat' )
			: __( 'وضعیت نهایی پس از تحویل مرسوله در رسید ثبت می شود.', 'torantejarat' ),
	),
);

$stage_map = array(
	'pending'    => 2,
	'on-hold'    => 2,
	'processing' => 2,
	'shipped'    => 3,
	'completed'  => 4,
);
$stage       = isset( $stage_map[ $status ] ) ? $stage_map[ $status ] : 2;
$effective   = ( 'completed' === $status ) ? 5 : $stage;
$show_steps  = ! in_array( $status, array( 'cancelled', 'refunded' ), true );
$is_logged_in = is_user_logged_in();
$orders_url   = wc_get_account_endpoint_url( 'orders' );
$shop_url     = apply_filters( 'woocommerce_return_to_shop_redirect', wc_get_page_permalink( 'shop' ) );
$contact_page = get_page_by_path( 'contact' );
$contact_url  = $contact_page ? get_permalink( $contact_page ) : home_url( '/' );

$order_items        = $order->get_items( apply_filters( 'woocommerce_purchase_order_item_types', 'line_item' ) );
$order_totals       = $order->get_order_item_totals();
$show_purchase_note = $order->has_status( apply_filters( 'woocommerce_purchase_note_order_statuses', array( 'completed', 'processing' ) ) );
$primary_url        = $is_logged_in ? $orders_url : $shop_url;
$primary_label      = $is_logged_in ? __( 'پیگیری در حساب کاربری', 'torantejarat' ) : __( 'ادامه خرید', 'torantejarat' );
$phone              = trim( (string) $order->get_billing_phone() );
$phone_href         = preg_replace( '/[^0-9+]/', '', $phone );

/* Keep gateway instructions visible, but execute this hook only once. */
ob_start();
do_action( 'woocommerce_thankyou_' . $order->get_payment_method(), $order->get_id() );
$payment_instructions = trim( ob_get_clean() );

/* The custom receipt below already renders items, totals and addresses. */
remove_action( 'woocommerce_thankyou', 'woocommerce_order_details_table', 10 );
?>

<div class="torantejarat-thx torantejarat-thx--status-<?php echo esc_attr( sanitize_html_class( $status ) ); ?>">
	<header class="torantejarat-thx__hero">
		<div class="torantejarat-thx__hero-top">
			<span class="torantejarat-thx__icon" aria-hidden="true">
				<?php if ( 'shipped' === $status ) : ?>
					<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M3 6h11v11H3zM14 10h4l3 3v4h-7z"/><circle cx="7" cy="19" r="2"/><circle cx="18" cy="19" r="2"/></svg>
				<?php elseif ( in_array( $status, array( 'cancelled', 'refunded' ), true ) ) : ?>
					<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="9"/><path d="M12 11v6M12 7h.01"/></svg>
				<?php else : ?>
					<svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="m5 12 4 4L19 6"/></svg>
				<?php endif; ?>
			</span>
			<div class="torantejarat-thx__hero-copy">
				<p class="torantejarat-thx__eyebrow"><?php printf( esc_html__( 'رسید سفارش در %s', 'torantejarat' ), esc_html( get_bloginfo( 'name' ) ) ); ?></p>
				<h1 class="torantejarat-thx__title"><?php echo esc_html( $headline ); ?></h1>
				<p class="torantejarat-thx__sub">
					<?php if ( '' !== $customer_name ) : ?><strong><?php echo esc_html( $customer_name ); ?> عزیز، </strong><?php endif; ?>
					<?php echo esc_html( $description ); ?>
				</p>
				<span class="torantejarat-thx__badge<?php echo esc_attr( $badge_cls ); ?>"><?php echo esc_html( $badge_text ); ?></span>
			</div>
			<div class="torantejarat-thx__hero-order">
				<span><?php esc_html_e( 'شماره سفارش', 'torantejarat' ); ?></span>
				<strong>#<?php echo esc_html( torantejarat_fa_number( $order->get_order_number() ) ); ?></strong>
				<button class="torantejarat-thx__print" type="button" onclick="window.print()">
					<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M6 9V2h12v7M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"/><path d="M6 14h12v8H6z"/></svg>
					<?php esc_html_e( 'چاپ رسید', 'torantejarat' ); ?>
				</button>
			</div>
		</div>

		<dl class="torantejarat-thx__meta">
			<div><dt><?php esc_html_e( 'تاریخ سفارش', 'torantejarat' ); ?></dt><dd><?php echo esc_html( $created ? torantejarat_jalali_ymd( $created->date( 'Y' ), $created->date( 'n' ), $created->date( 'j' ) ) : '—' ); ?></dd></div>
			<div><dt><?php esc_html_e( 'مبلغ نهایی', 'torantejarat' ); ?></dt><dd><?php echo wp_kses_post( $order->get_formatted_order_total() ); ?></dd></div>
			<div><dt><?php esc_html_e( 'شیوه پرداخت', 'torantejarat' ); ?></dt><dd><?php echo esc_html( $pay_label ); ?></dd></div>
			<div><dt><?php esc_html_e( 'تعداد اقلام', 'torantejarat' ); ?></dt><dd><?php echo esc_html( torantejarat_fa_number( $order->get_item_count() ) ); ?></dd></div>
		</dl>
	</header>

	<div class="torantejarat-thx__grid">
		<div class="torantejarat-thx__main">
			<section class="torantejarat-thx__card" aria-labelledby="torantejarat-thx-progress">
				<div class="torantejarat-thx__section-head">
					<div><span class="torantejarat-thx__kicker"><?php esc_html_e( 'پیگیری سفارش', 'torantejarat' ); ?></span><h2 id="torantejarat-thx-progress"><?php esc_html_e( 'مسیر از ثبت تا تحویل', 'torantejarat' ); ?></h2></div>
				</div>
				<?php if ( $show_steps ) : ?>
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
				<?php else : ?>
					<div class="torantejarat-thx__status-note"><strong><?php echo esc_html( $headline ); ?></strong><p><?php echo esc_html( $description ); ?></p></div>
				<?php endif; ?>
			</section>

			<?php if ( '' !== $payment_instructions ) : ?>
				<section class="torantejarat-thx__card torantejarat-thx__instructions" aria-labelledby="torantejarat-thx-instructions">
					<div class="torantejarat-thx__section-head"><div><span class="torantejarat-thx__kicker"><?php esc_html_e( 'اقدام بعدی', 'torantejarat' ); ?></span><h2 id="torantejarat-thx-instructions"><?php esc_html_e( 'راهنمای پرداخت و تحویل', 'torantejarat' ); ?></h2></div></div>
					<div class="torantejarat-thx__instructions-content"><?php echo $payment_instructions; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Trusted WooCommerce gateway hook, matching core thank-you behavior. ?></div>
				</section>
			<?php endif; ?>

			<section class="torantejarat-thx__card" aria-labelledby="torantejarat-thx-items">
				<div class="torantejarat-thx__section-head"><div><span class="torantejarat-thx__kicker"><?php esc_html_e( 'رسید خرید', 'torantejarat' ); ?></span><h2 id="torantejarat-thx-items"><?php esc_html_e( 'اقلام این سفارش', 'torantejarat' ); ?></h2></div></div>
				<div class="torantejarat-thx__items">
					<?php foreach ( $order_items as $item_id => $item ) :
						if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
							continue;
						}
						$product       = $item->get_product();
						$is_visible    = $product && $product->is_visible();
						$link          = apply_filters( 'woocommerce_order_item_permalink', $is_visible ? $product->get_permalink( $item ) : '', $item, $order );
						$purchase_note = $product ? $product->get_purchase_note() : '';
						$refunded_qty  = $order->get_qty_refunded_for_item( $item_id );
						?>
						<article class="torantejarat-thx__item">
							<div class="torantejarat-thx__thumb-wrap">
								<?php if ( $product ) : ?>
									<?php if ( $link ) : ?><a href="<?php echo esc_url( $link ); ?>" tabindex="-1" aria-hidden="true"><?php endif; ?>
									<?php echo wp_kses_post( $product->get_image( 'woocommerce_thumbnail', array( 'class' => 'torantejarat-thx__thumb', 'alt' => '' ) ) ); ?>
									<?php if ( $link ) : ?></a><?php endif; ?>
								<?php endif; ?>
							</div>
							<div class="torantejarat-thx__item-main">
								<?php if ( $link ) : ?><a class="torantejarat-thx__item-title" href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $item->get_name() ); ?></a><?php else : ?><strong class="torantejarat-thx__item-title"><?php echo esc_html( $item->get_name() ); ?></strong><?php endif; ?>
								<div class="torantejarat-thx__quantity">
									<span><?php esc_html_e( 'تعداد', 'torantejarat' ); ?></span>
									<strong><?php if ( $refunded_qty ) : ?><del><?php echo esc_html( torantejarat_fa_number( $item->get_quantity() ) ); ?></del> <ins><?php echo esc_html( torantejarat_fa_number( $item->get_quantity() - ( $refunded_qty * -1 ) ) ); ?></ins><?php else : ?><?php echo esc_html( torantejarat_fa_number( $item->get_quantity() ) ); ?><?php endif; ?></strong>
								</div>
								<?php
								do_action( 'woocommerce_order_item_meta_start', $item_id, $item, $order, false );
								wc_display_item_meta( $item );
								do_action( 'woocommerce_order_item_meta_end', $item_id, $item, $order, false );
								?>
								<?php if ( $show_purchase_note && $purchase_note ) : ?><div class="torantejarat-thx__purchase-note"><?php echo wpautop( do_shortcode( wp_kses_post( $purchase_note ) ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Matches WooCommerce order item template. ?></div><?php endif; ?>
							</div>
							<div class="torantejarat-thx__item-price"><span><?php esc_html_e( 'جمع ردیف', 'torantejarat' ); ?></span><strong><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></strong></div>
						</article>
					<?php endforeach; ?>
				</div>
				<?php if ( $order->get_customer_note() ) : ?>
					<div class="torantejarat-thx__customer-note"><strong><?php esc_html_e( 'یادداشت شما برای سفارش', 'torantejarat' ); ?></strong><p><?php echo wp_kses( nl2br( wc_wptexturize_order_note( $order->get_customer_note() ) ), array( 'br' => array() ) ); ?></p></div>
				<?php endif; ?>
			</section>

			<?php
			$show_downloads = apply_filters( 'woocommerce_order_downloads_table_show_downloads', $order->has_downloadable_item() && $order->is_download_permitted(), $order );
			if ( $show_downloads ) :
				?>
				<section class="torantejarat-thx__card torantejarat-thx__downloads">
					<?php wc_get_template( 'order/order-downloads.php', array( 'downloads' => $order->get_downloadable_items(), 'show_title' => true ) ); ?>
				</section>
			<?php endif; ?>

			<div class="torantejarat-thx__actions">
				<a class="torantejarat-btn torantejarat-btn-primary" href="<?php echo esc_url( $primary_url ); ?>"><?php echo esc_html( $primary_label ); ?></a>
				<?php if ( $primary_url !== $shop_url ) : ?><a class="torantejarat-btn torantejarat-btn-outline" href="<?php echo esc_url( $shop_url ); ?>"><?php esc_html_e( 'ادامه خرید در فروشگاه', 'torantejarat' ); ?></a><?php endif; ?>
			</div>
		</div>

		<aside class="torantejarat-thx__side">
			<section class="torantejarat-thx__card torantejarat-thx__totals-card" aria-labelledby="torantejarat-thx-totals">
				<div class="torantejarat-thx__section-head"><div><span class="torantejarat-thx__kicker"><?php esc_html_e( 'فاکتور', 'torantejarat' ); ?></span><h2 id="torantejarat-thx-totals"><?php esc_html_e( 'خلاصه مالی', 'torantejarat' ); ?></h2></div></div>
				<div class="torantejarat-thx__totals">
					<?php foreach ( $order_totals as $total_key => $total ) : ?>
						<div class="torantejarat-thx__row<?php echo 'order_total' === $total_key ? ' torantejarat-thx__row--total' : ''; ?>"><span><?php echo esc_html( $total['label'] ); ?></span><span><?php echo wp_kses_post( $total['value'] ); ?></span></div>
					<?php endforeach; ?>
				</div>
			</section>

			<section class="torantejarat-thx__card" aria-labelledby="torantejarat-thx-customer">
				<div class="torantejarat-thx__section-head"><div><span class="torantejarat-thx__kicker"><?php esc_html_e( 'گیرنده', 'torantejarat' ); ?></span><h2 id="torantejarat-thx-customer"><?php esc_html_e( 'اطلاعات تحویل', 'torantejarat' ); ?></h2></div></div>
				<?php if ( $order->get_formatted_billing_full_name() ) : ?><p class="torantejarat-thx__name"><?php echo esc_html( $order->get_formatted_billing_full_name() ); ?></p><?php endif; ?>
				<?php if ( '' !== $phone ) : ?><p class="torantejarat-thx__phone"><?php if ( '' !== $phone_href ) : ?><a href="tel:<?php echo esc_attr( $phone_href ); ?>" dir="ltr"><?php echo esc_html( $phone ); ?></a><?php else : ?><span dir="ltr"><?php echo esc_html( $phone ); ?></span><?php endif; ?></p><?php endif; ?>
				<?php if ( $order->get_billing_email() ) : ?><p class="torantejarat-thx__email" dir="ltr"><?php echo esc_html( $order->get_billing_email() ); ?></p><?php endif; ?>
				<?php $delivery_address = $order->has_shipping_address() ? $order->get_formatted_shipping_address() : $order->get_formatted_billing_address(); ?>
				<?php if ( $delivery_address ) : ?><address class="torantejarat-thx__addr"><?php echo wp_kses_post( $delivery_address ); ?></address><?php endif; ?>
				<?php if ( '' !== $tracking ) : ?><div class="torantejarat-thx__tracking"><span><?php echo esc_html( $tracking_label ? $tracking_label : __( 'کد رهگیری مرسوله', 'torantejarat' ) ); ?></span><strong dir="ltr"><?php echo esc_html( $tracking ); ?></strong></div><?php endif; ?>
			</section>

			<section class="torantejarat-thx__card torantejarat-thx__help" aria-labelledby="torantejarat-thx-help">
				<span class="torantejarat-thx__kicker"><?php esc_html_e( 'همراه شما هستیم', 'torantejarat' ); ?></span>
				<h2 id="torantejarat-thx-help"><?php esc_html_e( 'درباره این سفارش سؤال دارید؟', 'torantejarat' ); ?></h2>
				<p class="torantejarat-thx__sub"><?php esc_html_e( 'شماره سفارش را همراه پیام خود ارسال کنید تا بررسی دقیق تر انجام شود.', 'torantejarat' ); ?></p>
				<a class="torantejarat-btn torantejarat-btn-outline torantejarat-btn-block" href="<?php echo esc_url( $contact_url ); ?>"><?php esc_html_e( 'ارسال پیام به پشتیبانی', 'torantejarat' ); ?></a>
			</section>
		</aside>
	</div>

	<div class="torantejarat-thx__extensions"><?php do_action( 'woocommerce_thankyou', $order->get_id() ); ?></div>
</div>

<?php
/* ---------- Quick ticket for this order ---------- */
/* بخش ثبت تیکت سریع برای سفارش فعلی — کاربر بتواند مستقیماً از رسید خرید تیکت پشتیبانی مرتبطة با همین سفارش ثبت کند. */
$ticket_url = wc_get_account_endpoint_url( 'tickets' );
$ticket_url = add_query_arg( 'order_id', $order->get_id(), $ticket_url );
?>
<div class="torantejarat-thx torantejarat-thx--ticket-quick">
	<section class="torantejarat-thx__card torantejarat-thx__ticket-quick">
		<div class="torantejarat-thx__section-head">
			<div>
				<span class="torantejarat-thx__kicker"><?php esc_html_e( 'پشتیبانی سفارش', 'torantejarat' ); ?></span>
				<h2><?php esc_html_e( 'سوال درباره این سفارش دارید؟', 'torantejarat' ); ?></h2>
			</div>
		</div>
		<p class="torantejarat-thx__sub">
			<?php
			printf(
				/* translators: %s: order number */
				esc_html__( 'اگر مشکلی در سفارش شماره %s دارید، می‌توانید مستقیماً تیکت پشتیبانی ثبت کنید.', 'torantejarat' ),
				'<strong>#' . esc_html( torantejarat_fa_number( $order->get_order_number() ) ) . '</strong>'
			);
			?>
		</p>
		<a class="torantejarat-btn torantejarat-btn-primary torantejarat-btn-block" href="<?php echo esc_url( $ticket_url ); ?>">
			<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" style="vertical-align: middle; margin-inline-end: 6px;"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>
			<?php esc_html_e( 'ثبت تیکت برای این سفارش', 'torantejarat' ); ?>
		</a>
	</section>
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
				<th><?php esc_html_e( 'مبلغ', 'torantejarat' ); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ( $order_items as $item_id => $item ) : ?>
				<?php
				if ( ! apply_filters( 'woocommerce_order_item_visible', true, $item ) ) {
					continue;
				}
				++$inv_row;
				$inv_product      = $item->get_product();
				$inv_sku          = ( $inv_product && $inv_product->get_sku() ) ? ' (' . $inv_product->get_sku() . ')' : '';
				$inv_refunded_qty = $order->get_qty_refunded_for_item( $item_id );
				?>
				<tr>
					<td><?php echo esc_html( torantejarat_fa_number( $inv_row ) ); ?></td>
					<td>
						<?php echo esc_html( $item->get_name() . $inv_sku ); ?>
						<?php wc_display_item_meta( $item ); ?>
					</td>
					<td>
						<?php if ( $inv_refunded_qty ) : ?>
							<del><?php echo esc_html( torantejarat_fa_number( $item->get_quantity() ) ); ?></del>
							<ins><?php echo esc_html( torantejarat_fa_number( $item->get_quantity() - ( $inv_refunded_qty * -1 ) ) ); ?></ins>
						<?php else : ?>
							<?php echo esc_html( torantejarat_fa_number( $item->get_quantity() ) ); ?>
						<?php endif; ?>
					</td>
					<td><?php echo wp_kses_post( $order->get_formatted_line_subtotal( $item ) ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<?php foreach ( $order_totals as $total ) : ?>
				<tr>
					<th colspan="3"><?php echo esc_html( $total['label'] ); ?></th>
					<td><?php echo wp_kses_post( $total['value'] ); ?></td>
				</tr>
			<?php endforeach; ?>
		</tfoot>
	</table>
	<div class="torantejarat-print-invoice__foot">
		<span><?php esc_html_e( 'روش پرداخت:', 'torantejarat' ); ?> <?php echo esc_html( $pay_label ); ?></span>
		<span><?php esc_html_e( 'وضعیت:', 'torantejarat' ); ?> <?php echo esc_html( $badge_text ); ?></span>
		<?php if ( '' !== $tracking ) : ?><span><?php esc_html_e( 'کد مرسوله:', 'torantejarat' ); ?> <?php echo esc_html( $tracking ); ?></span><?php endif; ?>
	</div>
	<p class="torantejarat-print-invoice__thanks"><?php esc_html_e( 'از خرید شما سپاسگزاریم.', 'torantejarat' ); ?></p>
</section>
