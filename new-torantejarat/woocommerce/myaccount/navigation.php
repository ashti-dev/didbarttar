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

$torantejarat_user  = wp_get_current_user();
$torantejarat_icons = menu_icons();
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
			<li class="<?php echo esc_attr( wc_get_account_menu_item_classes( $endpoint ) ); ?>">
				<a href="<?php echo esc_url( wc_get_account_endpoint_url( $endpoint ) ); ?>" <?php echo wc_is_current_account_menu_item( $endpoint ) ? 'aria-current="page"' : ''; ?>>
					<?php \ToranTejarat\Theme\torantejarat_icon( isset( $torantejarat_icons[ $endpoint ] ) ? $torantejarat_icons[ $endpoint ] : 'chevron' ); ?>
					<span><?php echo esc_html( $label ); ?></span>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<div class="acp__foot">
		<a href="<?php echo esc_url( wc_logout_url() ); ?>" data-confirm="<?php esc_attr_e( 'از حساب کاربری خارج می‌شوید و برای ورود دوباره باید نام کاربری و رمز عبور را وارد کنید.', 'torantejarat' ); ?>"><?php esc_html_e( 'خروج از حساب', 'torantejarat' ); ?></a>
	</div>
</nav>

<?php do_action( 'woocommerce_after_account_navigation' ); ?>
