<?php
/**
 * My Account page — sidebar layout.
 *
 * Theme override: torantejarat/woocommerce/myaccount/my-account.php
 *
 * @version 3.5.0
 * @package ToranTejarat
 */

defined( 'ABSPATH' ) || exit;

$torantejarat_tabs = array(
	''             => array( 'label' => __( 'داشبورد', 'torantejarat' ), 'icon' => 'home' ),
	'orders'       => array( 'label' => __( 'سفارش‌ها', 'torantejarat' ), 'icon' => 'box' ),
	'wishlist'     => array( 'label' => __( 'علاقه‌مندی‌ها', 'torantejarat' ), 'icon' => 'heart' ),
	'edit-account' => array( 'label' => __( 'حساب', 'torantejarat' ), 'icon' => 'user' ),
);
$torantejarat_more = array();
foreach ( wc_get_account_menu_items() as $torantejarat_endpoint => $torantejarat_label ) {
	if ( in_array( $torantejarat_endpoint, array( 'dashboard', 'orders', 'wishlist', 'edit-account', 'customer-logout' ), true ) ) {
		continue;
	}
	$torantejarat_more[ $torantejarat_endpoint ] = $torantejarat_label;
}
$torantejarat_current_endpoint = WC()->query->get_current_endpoint();
$torantejarat_tickets_url      = wc_get_account_endpoint_url( 'tickets' );
$torantejarat_create_url       = add_query_arg( 'ticket_view', 'new', $torantejarat_tickets_url );
$torantejarat_ticket_view      = isset( $_GET['ticket_view'] ) ? sanitize_key( wp_unslash( $_GET['ticket_view'] ) ) : '';
$torantejarat_is_create        = ( 'new' === $torantejarat_ticket_view );
?>
<div class="acp" id="acp-panel">
	<?php
		/**
		 * My Account navigation.
		 *
		 * @since 2.6.0
		 */
		do_action( 'woocommerce_account_navigation' );
	?>

	<div class="woocommerce-MyAccount-content acp__content" id="acp-content">
		<?php
			/**
			 * My Account content.
			 *
			 * @since 2.6.0
			 */
			do_action( 'woocommerce_account_content' );
		?>
	</div>
</div>
<nav class="acp-tabs" aria-label="<?php esc_attr_e( 'ناوبری حساب کاربری', 'torantejarat' ); ?>">
	<?php foreach ( $torantejarat_tabs as $torantejarat_endpoint => $torantejarat_tab ) : ?>
		<?php
		$torantejarat_url = '' === $torantejarat_endpoint
			? wc_get_page_permalink( 'myaccount' )
			: wc_get_account_endpoint_url( $torantejarat_endpoint );
		$torantejarat_active = $torantejarat_current_endpoint === $torantejarat_endpoint;
		?>
		<a href="<?php echo esc_url( $torantejarat_url ); ?>" <?php echo $torantejarat_active ? 'aria-current="page"' : ''; ?>>
			<?php \ToranTejarat\Theme\torantejarat_icon( $torantejarat_tab['icon'] ); ?>
			<span><?php echo esc_html( $torantejarat_tab['label'] ); ?></span>
		</a>
	<?php endforeach; ?>
	<button type="button" data-sheet-open aria-haspopup="dialog">
		<?php \ToranTejarat\Theme\torantejarat_icon( 'menu' ); ?>
		<span><?php esc_html_e( 'بیشتر', 'torantejarat' ); ?></span>
	</button>
</nav>
<button type="button" class="acp-scrim" data-sheet-close hidden aria-label="<?php esc_attr_e( 'بستن', 'torantejarat' ); ?>"></button>
<div class="acp-sheet" id="acp-sheet" role="dialog" aria-modal="true" aria-label="<?php esc_attr_e( 'بخش‌های بیشتر', 'torantejarat' ); ?>" hidden>
	<div class="acp-sheet__handle" aria-hidden="true"></div>
	<?php foreach ( $torantejarat_more as $torantejarat_endpoint => $torantejarat_label ) : ?>
		<?php $torantejarat_is_tickets_item = ( 'tickets' === $torantejarat_endpoint ); ?>
		<a href="<?php echo esc_url( ( 'dashboard' === $torantejarat_endpoint ) ? wc_get_page_permalink( 'myaccount' ) : wc_get_account_endpoint_url( $torantejarat_endpoint ) ); ?>" <?php echo $torantejarat_current_endpoint === $torantejarat_endpoint ? 'aria-current="page"' : ''; ?>>
			<?php \ToranTejarat\Theme\torantejarat_icon( \ToranTejarat\Account\menu_icons()[ $torantejarat_endpoint ] ?? 'chevron' ); ?>
			<span><?php echo esc_html( $torantejarat_label ); ?></span>
		</a>
		<?php if ( $torantejarat_is_tickets_item ) : ?>
			<a class="acp-sheet__sublink" href="<?php echo esc_url( $torantejarat_tickets_url ); ?>" <?php echo $torantejarat_is_create ? '' : 'aria-current="page"'; ?>>
				<span class="acp-sheet__subicon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="9" y1="6" x2="21" y2="6"/><line x1="9" y1="12" x2="21" y2="12"/><line x1="9" y1="18" x2="21" y2="18"/><circle cx="4" cy="6" r="1" fill="currentColor" stroke="none"/><circle cx="4" cy="12" r="1" fill="currentColor" stroke="none"/><circle cx="4" cy="18" r="1" fill="currentColor" stroke="none"/></svg>
				</span>
				<span><?php esc_html_e( 'لیست تیکت ها', 'torantejarat' ); ?></span>
			</a>
			<a class="acp-sheet__sublink" href="<?php echo esc_url( $torantejarat_create_url ); ?>" <?php echo $torantejarat_is_create ? 'aria-current="page"' : ''; ?>>
				<span class="acp-sheet__subicon" aria-hidden="true">
					<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
				</span>
				<span><?php esc_html_e( 'ایجاد تیکت', 'torantejarat' ); ?></span>
			</a>
		<?php endif; ?>
	<?php endforeach; ?>
	<a href="<?php echo esc_url( wc_logout_url() ); ?>" data-confirm="<?php esc_attr_e( 'از حساب کاربری خارج می‌شوید و برای ورود دوباره باید نام کاربری و رمز عبور را وارد کنید.', 'torantejarat' ); ?>">
		<?php \ToranTejarat\Theme\torantejarat_icon( 'logout' ); ?>
		<span><?php esc_html_e( 'خروج از حساب', 'torantejarat' ); ?></span>
	</a>
</div>
<div class="acp-confirm" id="acp-confirm" role="alertdialog" aria-modal="true" aria-labelledby="acp-confirm-title" hidden>
	<h3 id="acp-confirm-title"></h3>
	<p id="acp-confirm-text"></p>
	<div class="acp-actions">
		<button type="button" class="acp-btn acp-btn-outline" data-confirm-no><?php esc_html_e( 'انصراف', 'torantejarat' ); ?></button>
		<a class="acp-btn acp-btn-primary" id="acp-confirm-yes" href="#"><?php esc_html_e( 'تأیید', 'torantejarat' ); ?></a>
	</div>
</div>
<div class="acp-toast" id="acp-toast" role="status" aria-live="polite"></div>
