<?php
/**
 * Notifications endpoint — in-site order notifications.
 *
 * Rendered via woocommerce_account_notifications_endpoint.
 * Expects: $items (array of notification rows).
 *
 * @package ToranTejarat
 */

use function ToranTejarat\Account\notification_read_url;

defined( 'ABSPATH' ) || exit;
?>
<div class="acp__hello">
	<h2><?php esc_html_e( 'اعلان‌ها', 'torantejarat' ); ?></h2>
	<p><?php esc_html_e( 'تغییرات سفارش‌ها و کدهای مرسوله، همین‌جا.', 'torantejarat' ); ?></p>
</div>

<?php if ( $items ) : ?>
	<ul class="acp-activity">
		<?php foreach ( $items as $item ) : ?>
			<li<?php echo $item->is_read ? '' : ' class="acp-activity--unread"'; ?>>
				<span class="acp-dot"><?php \ToranTejarat\Theme\torantejarat_icon( 'bell' ); ?></span>
				<div>
					<strong><?php echo esc_html( $item->title ); ?></strong>
					<br>
					<small><?php echo esc_html( $item->text ); ?></small>
					<br>
					<small><?php echo esc_html( mysql2date( 'Y/m/d H:i', $item->created_at ) ); ?></small>
				</div>
				<?php if ( $item->link ) : ?>
					<a class="acp-btn acp-btn-outline" href="<?php echo esc_url( $item->link ); ?>"><?php esc_html_e( 'مشاهده', 'torantejarat' ); ?></a>
				<?php elseif ( ! $item->is_read ) : ?>
					<a class="acp-btn acp-btn-ghost" href="<?php echo esc_url( notification_read_url( $item->id ) ); ?>"><?php esc_html_e( 'خوانده شد', 'torantejarat' ); ?></a>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
<?php else : ?>
	<div class="acp-empty">
		<h3><?php esc_html_e( 'اعلان جدیدی ندارید.', 'torantejarat' ); ?></h3>
		<p><?php esc_html_e( 'تغییر وضعیت سفارش‌ها و کدهای مرسوله اینجا نمایش داده می‌شود.', 'torantejarat' ); ?></p>
	</div>
<?php endif; ?>
