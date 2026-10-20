<?php
/**
 * Security endpoint — active sessions from WP core.
 *
 * Rendered via woocommerce_account_security_endpoint.
 * Expects: $sessions (array from WP_Session_Tokens::get_all()).
 *
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;

$torantejarat_manager = \WP_Session_Tokens::get_instance( get_current_user_id() );
$torantejarat_current = wp_get_session_token();
?>
<div class="acp__hello">
	<h2><?php esc_html_e( 'امنیت و نشست‌ها', 'torantejarat' ); ?></h2>
	<p><?php esc_html_e( 'دستگاه‌هایی که اکنون وارد حساب شما هستند.', 'torantejarat' ); ?></p>
</div>

<div class="acp-card">
	<?php if ( $sessions ) : ?>
		<ul class="acp-activity">
			<?php foreach ( $sessions as $token => $session ) : ?>
				<?php
				$torantejarat_is_current = hash_equals( (string) $token, (string) $torantejarat_current );
				$torantejarat_ua = isset( $session['ua'] ) ? $session['ua'] : '';
				$torantejarat_ip = isset( $session['ip'] ) ? $session['ip'] : '';
				?>
				<li>
					<span class="acp-dot"><?php \ToranTejarat\Theme\torantejarat_icon( 'shield' ); ?></span>
					<div>
						<strong><?php echo $torantejarat_is_current ? esc_html__( 'این دستگاه', 'torantejarat' ) : esc_html( wp_is_mobile() ? __( 'دستگاه دیگر', 'torantejarat' ) : __( 'نشست دیگر', 'torantejarat' ) ); ?></strong>
						<br>
						<small><?php echo esc_html( $torantejarat_ua ); ?><?php echo '' !== $torantejarat_ip ? ' · ' . esc_html( $torantejarat_ip ) : ''; ?></small>
						<?php if ( ! empty( $session['login'] ) ) : ?>
							<br>
							<small><?php echo esc_html( sprintf( __( 'ورود: %s', 'torantejarat' ), date_i18n( 'Y/m/d H:i', (int) $session['login'] ) ) ); ?></small>
						<?php endif; ?>
					</div>
					<?php if ( $torantejarat_is_current ) : ?>
						<span class="acp-badge"><?php esc_html_e( 'فعال', 'torantejarat' ); ?></span>
					<?php endif; ?>
				</li>
			<?php endforeach; ?>
		</ul>
		<?php if ( count( $sessions ) > 1 ) : ?>
			<form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>" style="margin-top:16px">
				<input type="hidden" name="action" value="torantejarat_logout_others">
				<?php wp_nonce_field( 'torantejarat-security', 'torantejarat_security_nonce' ); ?>
				<button type="submit" class="acp-btn acp-btn-outline" data-confirm="<?php esc_attr_e( 'همه نشست‌های دیگر بسته می‌شوند و باید دوباره وارد شوید. نشست فعلی باز می‌ماند.', 'torantejarat' ); ?>"><?php esc_html_e( 'خروج از همه دستگاه‌های دیگر', 'torantejarat' ); ?></button>
			</form>
		<?php endif; ?>
	<?php else : ?>
		<div class="acp-empty">
			<h3><?php esc_html_e( 'نشست فعالی یافت نشد.', 'torantejarat' ); ?></h3>
		</div>
	<?php endif; ?>
</div>
