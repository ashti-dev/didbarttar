<?php
/**
 * Shared header migrated from index.html; no guessed business routes.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<style id="torantejarat-critical">.torantejarat-menu-toggle{display:none}.torantejarat-main-nav{display:flex}@media(max-width:900px){.torantejarat-menu-toggle{display:grid}.torantejarat-main-nav:not(.torantejarat-is-open){display:none}}</style>
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'torantejarat-site' ); ?>>
<?php wp_body_open(); ?>
<a class="torantejarat-skip" href="#torantejarat-main"><?php esc_html_e( 'رفتن به محتوای اصلی', 'torantejarat' ); ?></a>
<div class="torantejarat-topbar">
	<div class="torantejarat-container">
		<?php
		$torantejarat_topbar_text = torantejarat_option( 'general', 'topbar_text', get_bloginfo( 'description' ) );
		$torantejarat_topbar_link = torantejarat_option( 'general', 'topbar_link' );
		if ( $torantejarat_topbar_link ) {
			echo '<a href="' . esc_url( $torantejarat_topbar_link ) . '">' . esc_html( $torantejarat_topbar_text ) . '</a>';
		} else {
			echo '<span>' . esc_html( $torantejarat_topbar_text ) . '</span>';
		}
		?>
		<?php
		wp_nav_menu( array( 'theme_location' => 'torantejarat-utility', 'container' => false, 'menu_class' => 'torantejarat-utility-menu', 'fallback_cb' => false, 'depth' => 1 ) );
		?>
	</div>
</div>
<header class="torantejarat-site-header">
	<div class="torantejarat-container torantejarat-header-main">
		<?php get_template_part( 'template-parts/brand' ); ?>
		<?php get_search_form( array( 'torantejarat_product_search' => true ) ); ?>
		<div class="torantejarat-header-actions">
			<?php
			if ( torantejarat_woocommerce_available() ) {
				$torantejarat_account_url = torantejarat_auth_url();
				if ( is_user_logged_in() ) {
					echo '<a class="torantejarat-icon-link" href="' . esc_url( $torantejarat_account_url ) . '">' . esc_html__( 'حساب کاربری', 'torantejarat' ) . '</a>';
				} else {
					echo '<a class="torantejarat-icon-link" href="' . esc_url( $torantejarat_account_url ) . '">' . esc_html__( 'ورود', 'torantejarat' ) . '<span aria-hidden="true"> | </span>' . esc_html__( 'ثبت نام', 'torantejarat' ) . '</a>';
				}
				echo '<div class="torantejarat-cart-wrap">';
			torantejarat_cart_link();
			torantejarat_cart_dropdown();
			echo '</div>';
			}
			?>
			<?php if ( has_nav_menu( 'torantejarat-primary' ) ) : ?>
				<button type="button" class="torantejarat-icon-button torantejarat-menu-toggle" aria-label="<?php esc_attr_e( 'باز کردن منو', 'torantejarat' ); ?>" data-open-label="<?php esc_attr_e( 'باز کردن منو', 'torantejarat' ); ?>" data-close-label="<?php esc_attr_e( 'بستن منو', 'torantejarat' ); ?>" aria-expanded="false" aria-controls="torantejarat-main-nav" hidden><?php torantejarat_icon( 'menu' ); ?></button>
			<?php endif; ?>
		</div>
	</div>
	<?php if ( has_nav_menu( 'torantejarat-primary' ) ) : ?>
		<div class="torantejarat-nav-wrap">
			<nav class="torantejarat-container torantejarat-main-nav" id="torantejarat-main-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'torantejarat' ); ?>">
				<?php wp_nav_menu( array( 'theme_location' => 'torantejarat-primary', 'container' => false, 'menu_class' => 'torantejarat-nav-list', 'fallback_cb' => false ) ); ?>
				<?php
				$torantejarat_support_page = get_page_by_path( 'contact' );
				$torantejarat_support_url  = $torantejarat_support_page ? get_permalink( $torantejarat_support_page ) : home_url( '/' );
				$torantejarat_deals_url    = torantejarat_woocommerce_available() && wc_get_page_id( 'shop' ) > 0
					? add_query_arg( 'orderby', 'discount', wc_get_page_permalink( 'shop' ) )
					: home_url( '/' );
				?>
				<div class="torantejarat-nav-actions">
					<a class="torantejarat-nav-btn" href="<?php echo esc_url( $torantejarat_support_url ); ?>"><?php torantejarat_icon( 'chat' ); ?><span><?php esc_html_e( 'پشتیبانی', 'torantejarat' ); ?></span></a>
					<a class="torantejarat-nav-btn is-deals" href="<?php echo esc_url( $torantejarat_deals_url ); ?>"><?php torantejarat_icon( 'percent' ); ?><span><?php esc_html_e( 'شگفت‌انگیزها', 'torantejarat' ); ?></span></a>
				</div>
			</nav>
		</div>
	<?php endif; ?>
</header>
