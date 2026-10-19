<?php
/**
 * My Account Dashboard — bento cards from real data only.
 *
 * Theme override: torantejarat/woocommerce/myaccount/dashboard.php
 *
 * @version 4.4.0
 * @package ToranTejarat
 */

use function ToranTejarat\Account\get_summary;
use function ToranTejarat\Account\reorder_url;
use function ToranTejarat\Theme\torantejarat_fa_number;
use function ToranTejarat\Theme\torantejarat_jalali_ymd;

defined( 'ABSPATH' ) || exit;

$torantejarat_user    = wp_get_current_user();
$torantejarat_summary = get_summary( $torantejarat_user->ID );
$torantejarat_active  = $torantejarat_summary['active'] ? wc_get_order( $torantejarat_summary['active'] ) : false;
$torantejarat_shop    = wc_get_page_permalink( 'shop' );
$torantejarat_orders  = wc_get_account_endpoint_url( 'orders' );
?>
<div class="acp__hello">
	<h2>
		<?php
		printf(
			/* translators: %s: display name */
			esc_html__( 'سلام %s', 'torantejarat' ),
			esc_html( $torantejarat_user->display_name )
		);
		?>
	</h2>
	<p><?php esc_html_e( 'وضعیت سفارش‌ها و علاقه‌مندی‌های شما.', 'torantejarat' ); ?></p>
</div>

<div class="acp__grid">
	<section class="acp-card acp-card--wide acp-hero" aria-labelledby="acp-active-t">
		<div class="acp-card__head">
			<span class="acp-eyebrow"><?php esc_html_e( 'سفارش در جریان', 'torantejarat' ); ?></span>
			<?php if ( $torantejarat_active ) : ?>
				<span class="acp-badge dark"><?php echo esc_html( wc_get_order_status_name( $torantejarat_active->get_status() ) ); ?></span>
			<?php endif; ?>
		</div>
		<?php if ( $torantejarat_active ) : ?>
			<?php $torantejarat_created = $torantejarat_active->get_date_created(); ?>
			<h3 id="acp-active-t">
				<?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( torantejarat_fa_number( $torantejarat_active->get_order_number() ) ) ); ?>
			</h3>
			<p>
				<?php esc_html_e( 'ثبت:', 'torantejarat' ); ?>
				<?php echo $torantejarat_created ? esc_html( torantejarat_jalali_ymd( $torantejarat_created->date( 'Y' ), $torantejarat_created->date( 'n' ), $torantejarat_created->date( 'j' ) ) ) : ''; ?>
				·
				<?php echo wp_kses_post( $torantejarat_active->get_formatted_order_total() ); ?>
			</p>
			<div class="acp-actions">
				<a class="acp-btn acp-btn-primary" href="<?php echo esc_url( $torantejarat_active->get_view_order_url() ); ?>"><?php esc_html_e( 'پیگیری سفارش', 'torantejarat' ); ?></a>
				<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( reorder_url( $torantejarat_active->get_id() ) ); ?>"><?php esc_html_e( 'خرید مجدد', 'torantejarat' ); ?></a>
			</div>
		<?php else : ?>
			<h3 id="acp-active-t"><?php esc_html_e( 'سفارش فعالی ندارید.', 'torantejarat' ); ?></h3>
			<div class="acp-actions">
				<a class="acp-btn acp-btn-primary" href="<?php echo esc_url( $torantejarat_shop ); ?>"><?php esc_html_e( 'شروع خرید', 'torantejarat' ); ?></a>
			</div>
		<?php endif; ?>
	</section>

	<section class="acp-card" aria-labelledby="acp-wish-t">
		<div class="acp-card__head">
			<h3 id="acp-wish-t"><?php esc_html_e( 'علاقه‌مندی‌ها', 'torantejarat' ); ?></h3>
			<span class="acp-eyebrow"><?php echo esc_html( sprintf( __( '%s محصول', 'torantejarat' ), torantejarat_fa_number( $torantejarat_summary['wishlist'] ) ) ); ?></span>
		</div>
		<div class="acp-wish-mini">
			<div>
				<div class="acp-count-big"><?php echo esc_html( torantejarat_fa_number( $torantejarat_summary['wishlist'] ) ); ?></div>
				<small><?php esc_html_e( 'محصول ذخیره‌شده', 'torantejarat' ); ?></small>
			</div>
		</div>
		<div class="acp-actions">
			<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( wc_get_account_endpoint_url( 'wishlist' ) ); ?>"><?php esc_html_e( 'مشاهده علاقه‌مندی‌ها', 'torantejarat' ); ?></a>
		</div>
	</section>

	<section class="acp-card" aria-labelledby="acp-act-t">
		<div class="acp-card__head"><h3 id="acp-act-t"><?php esc_html_e( 'آخرین فعالیت', 'torantejarat' ); ?></h3></div>
		<ul class="acp-activity">
			<?php if ( $torantejarat_summary['recent'] ) : ?>
				<?php foreach ( array_slice( $torantejarat_summary['recent'], 0, 3 ) as $torantejarat_rid ) : ?>
					<?php $torantejarat_ro = wc_get_order( $torantejarat_rid ); ?>
					<?php if ( ! $torantejarat_ro ) { continue; } ?>
					<?php $torantejarat_rdate = $torantejarat_ro->get_date_created(); ?>
					<li>
						<span class="acp-dot"><?php \ToranTejarat\Theme\torantejarat_icon( 'box' ); ?></span>
						<div>
							<strong><?php printf( esc_html__( 'سفارش #%s', 'torantejarat' ), esc_html( torantejarat_fa_number( $torantejarat_ro->get_order_number() ) ) ); ?></strong>
							<br>
							<small><?php echo esc_html( wc_get_order_status_name( $torantejarat_ro->get_status() ) ); ?></small>
						</div>
						<time><?php echo $torantejarat_rdate ? esc_html( torantejarat_jalali_ymd( $torantejarat_rdate->date( 'Y' ), $torantejarat_rdate->date( 'n' ), $torantejarat_rdate->date( 'j' ) ) ) : ''; ?></time>
					</li>
				<?php endforeach; ?>
			<?php else : ?>
				<li>
					<span class="acp-dot"><?php \ToranTejarat\Theme\torantejarat_icon( 'box' ); ?></span>
					<div><?php esc_html_e( 'هنوز سفارشی ثبت نشده؛ از فروشگاه شروع کنید.', 'torantejarat' ); ?></div>
				</li>
			<?php endif; ?>
		</ul>
	</section>

	<?php if ( $torantejarat_summary['recent'] ) : ?>
		<section class="acp-card" aria-labelledby="acp-re-t">
			<div class="acp-card__head"><h3 id="acp-re-t"><?php esc_html_e( 'خرید مجدد', 'torantejarat' ); ?></h3></div>
			<p><?php esc_html_e( 'اقلام سفارش قبلی را در یک کلیک به سبد اضافه کنید. موجودی و قیمت فعلی پیش از افزودن بررسی می‌شود.', 'torantejarat' ); ?></p>
			<div class="acp-actions">
				<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( reorder_url( (int) $torantejarat_summary['recent'][0] ) ); ?>"><?php printf( esc_html__( 'افزودن اقلام سفارش #%s', 'torantejarat' ), esc_html( torantejarat_fa_number( (int) $torantejarat_summary['recent'][0] ) ) ); ?></a>
			</div>
		</section>
	<?php endif; ?>
</div>

<?php
	/**
	 * My Account dashboard.
	 *
	 * @since 2.6.0
	 */
	do_action( 'woocommerce_account_dashboard' );

