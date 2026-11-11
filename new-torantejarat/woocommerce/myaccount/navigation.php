<?php
/**
 * My Account navigation — sidebar with avatar and icons.
 *
 * Theme override: torantejarat/woocommerce/myaccount/navigation.php
 *
 * @version 9.3.0
 * @package ToranTejarat
 */

use function ToranTejarat\Account\menu_icons;

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_account_navigation' );

$torantejarat_user        = wp_get_current_user();
$torantejarat_icons       = menu_icons();
$torantejarat_tickets_url = wc_get_account_endpoint_url( 'tickets' );
$torantejarat_create_url  = add_query_arg( 'ticket_view', 'new', $torantejarat_tickets_url );
$torantejarat_ticket_view = isset( $_GET['ticket_view'] ) ? sanitize_key( wp_unslash( $_GET['ticket_view'] ) ) : '';
$torantejarat_is_create   = ( 'new' === $torantejarat_ticket_view );
?>
<nav class="woocommerce-MyAccount-navigation acp__nav" aria-label="<?php esc_attr_e( 'بخش‌های حساب کاربری', 'torantejarat' ); ?>">
	<div class="acp__who">
		<span class="acp__avatar" aria-hidden="true"><?php echo get_avatar( $torantejarat_user->ID, 96 ); ?></span>
		<div>
			<strong><?php echo esc_html( $torantejarat_user->display_name ); ?></strong>
			<span dir="ltr"><?php echo esc_html( $torantejarat_user->user_email ); ?></span>
		</div>
	</div>
	<ul>
		<?php foreach ( wc_get_account_menu_items() as $endpoint => $label ) : ?>
			<?php
			$torantejarat_is_tickets = ( 'tickets' === $endpoint );
			$torantejarat_classes   = wc_get_account_menu_item_classes( $endpoint );
			if ( $torantejarat_is_tickets ) {
				$torantejarat_classes .= ' acp__nav-item--has-sub';
			}
			?>
			<li class="<?php echo esc_attr( $torantejarat_classes ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo ( wc_is_current_account_menu_item( $endpoint ) && ! ( $torantejarat_is_tickets && $torantejarat_is_create ) ) ? 'aria-current="page"' : ''; ?><?php echo $torantejarat_is_tickets ? ' aria-haspopup="true"' : ''; ?>>
					<?php \ToranTejarat\Theme\torantejarat_icon( isset( $torantejarat_icons[ $endpoint ] ) ? $torantejarat_icons[ $endpoint ] : 'chevron' ); ?>
					<span><?php echo esc_html( $label ); ?></span>
					<?php if ( $torantejarat_is_tickets ) : ?>
						<svg class="acp__caret" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false"><path d="m6 9 6 6 6-6"/></svg>
					<?php endif; ?>
				</a>
				<?php if ( $torantejarat_is_tickets ) : ?>
					<ul class="acp__subnav">
						<li>
							<a href="<?php echo esc_url( $torantejarat_tickets_url ); ?>" <?php echo $torantejarat_is_create ? '' : 'aria-current="page"'; ?>>
								<span class="acp__subnav-icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="9" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1" fill="currentColor" stroke="none"/></svg>
								</span>
								<span><?php esc_html_e( 'لیست تیکت ها', 'torantejarat' ); ?></span>
							</a>
						</li>
						<li>
							<a href="<?php echo esc_url( $torantejarat_create_url ); ?>" <?php echo $torantejarat_is_create ? 'aria-current="page"' : ''; ?>>
								<span class="acp__subnav-icon" aria-hidden="true">
									<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
								</span>
								<span><?php esc_html_e( 'ایجاد تیکت', 'torantejarat' ); ?></span>
							</a>
						</li>
					</ul>
				<?php endif; ?>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="acp__foot">
		<a href="<?php echo esc_url( wc_logout_url() ); ?>" data-confirm="<?php esc_attr_e( 'از حساب کاربری خارج می‌شوید و برای ورود دوباره باید نام کاربری و رمز عبور را وارد کنید.', 'torantejarat' ); ?>"><?php esc_html_e( 'خروج از حساب', 'torantejarat' ); ?></a>
	</div>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
