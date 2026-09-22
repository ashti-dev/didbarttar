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
	<?php wp_head(); ?>
</head>
<body <?php body_class( 'torantejarat-site' ); ?>>
<?php wp_body_open(); ?>
<a class="torantejarat-skip" href="#torantejarat-main"><?php esc_html_e( 'رفتن به محتوای اصلی', 'torantejarat' ); ?></a>
<div class="torantejarat-topbar">
	<div class="torantejarat-container">
		<span><?php echo esc_html( get_bloginfo( 'description' ) ); ?></span>
		<?php
		wp_nav_menu( array( 'theme_location' => 'torantejarat-utility', 'container' => false, 'menu_class' => 'torantejarat-utility-menu', 'fallback_cb' => false, 'depth' => 1 ) );
		?>
	</div>
</div>
<header class="torantejarat-site-header">
	<div class="torantejarat-container torantejarat-header-main">
		<?php get_template_part( 'template-parts/brand' ); ?>
		<?php get_search_form(); ?>
		<div class="torantejarat-header-actions">
			<?php
			if ( torantejarat_woocommerce_available() ) {
				if ( wc_get_page_id( 'myaccount' ) > 0 ) {
					echo '<a class="torantejarat-icon-link" href="' . esc_url( wc_get_page_permalink( 'myaccount' ) ) . '">' . esc_html__( 'حساب کاربری', 'torantejarat' ) . '</a>';
				}
				torantejarat_cart_link();
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
			</nav>
		</div>
	<?php endif; ?>
</header>
