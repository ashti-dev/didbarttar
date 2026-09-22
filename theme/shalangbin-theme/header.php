<?php
/**
 * Theme header.
 *
 * @package ShalangBin
 */
?><!doctype html>
<html <?php language_attributes(); ?>>
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>">
<meta name="viewport" content="width=device-width,initial-scale=1">
<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip" href="#main"><?php esc_html_e( 'رفتن به محتوای اصلی', 'shalangbin' ); ?></a>

<div class="topbar">
	<div class="container">
		<span><?php echo esc_html( get_theme_mod( 'shalangbin_topbar_text', 'تجهیزات تخصصی بازرسی تصویری' ) ); ?></span>
		<a class="topbar-cta" href="<?php echo esc_url( shalangbin_quiz_url() ); ?>">انتخاب درست، قبل از خرید <?php shalangbin_icon( 'arrow' ); ?></a>
		<div class="topbar-side">
			<?php if ( $phone = get_theme_mod( 'shalangbin_phone', '' ) ) : ?>
				<a class="topbar-phone" href="tel:<?php echo esc_attr( preg_replace( '/\s+/', '', $phone ) ); ?>" dir="ltr"><?php echo esc_html( $phone ); ?></a>
			<?php endif; ?>
			<?php
			if ( has_nav_menu( 'topbar' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'topbar',
					'container'      => false,
					'menu_class'     => 'topbar-menu',
					'depth'          => 1,
				) );
			}
			?>
		</div>
	</div>
</div>

<header class="site-header">
	<div class="container header-main">
		<?php if ( has_custom_logo() ) : ?>
			<?php the_custom_logo(); ?>
		<?php else : ?>
		<a class="brand" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php bloginfo( 'name' ); ?>">
			<span class="brand-mark"><?php shalangbin_icon( 'scan' ); ?></span>
			<span><?php
				$name = trim( (string) get_bloginfo( 'name' ) );
				if ( $name !== '' && function_exists( 'mb_substr' ) && mb_substr( $name, -4 ) === '‌بین' ) {
					// «شلنگ‌بین» → «شلنگ» + <span class="brand-accent">‌بین</span> (عین مرجع).
					echo esc_html( mb_substr( $name, 0, -4 ) ) . '<span class="brand-accent">‌بین</span>';
				} else {
					echo esc_html( $name );
				}
				?><small><?php echo esc_html( get_theme_mod( 'shalangbin_tagline', 'تخصص در دیدنِ نادیدنی‌ها' ) ); ?></small></span>
		</a>
		<?php endif; ?>

		<form class="header-search" role="search" method="get" action="<?php echo esc_url( home_url( '/' ) ); ?>">
			<label class="sr-only" for="header-q"><?php esc_html_e( 'جست‌وجو', 'shalangbin' ); ?></label>
			<?php shalangbin_icon( 'search' ); ?>
			<input id="header-q" type="search" name="s" placeholder="<?php esc_attr_e( 'نام دستگاه، مدل یا کاربرد...', 'shalangbin' ); ?>" value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
			<?php if ( function_exists( 'is_woocommerce' ) ) : ?>
				<input type="hidden" name="post_type" value="product">
			<?php endif; ?>
			<button aria-label="<?php esc_attr_e( 'جست‌وجو', 'shalangbin' ); ?>" type="submit"><?php shalangbin_icon( 'arrow' ); ?></button>
		</form>

		<div class="header-actions">
			<a class="icon-link compare-link" href="<?php echo esc_url( shalangbin_compare_url() ); ?>" aria-label="<?php esc_attr_e( 'مقایسه دستگاه‌ها', 'shalangbin' ); ?>">
				<?php shalangbin_icon( 'compare' ); ?><span><?php esc_html_e( 'مقایسه', 'shalangbin' ); ?></span>
			</a>
			<?php if ( function_exists( 'WC' ) && WC()->cart ) : ?>
			<a class="cart-link" href="<?php echo esc_url( wc_get_cart_url() ); ?>" aria-label="<?php esc_attr_e( 'سبد خرید', 'shalangbin' ); ?>">
				<?php shalangbin_icon( 'cart' ); ?>
				<span class="cart-label"><?php esc_html_e( 'سبد خرید', 'shalangbin' ); ?></span>
				<b data-cart-count><?php echo esc_html( shalangbin_fa_num( WC()->cart->get_cart_contents_count() ) ); ?></b>
			</a>
			<?php endif; ?>
			<?php if ( is_user_logged_in() ) : ?>
			<a class="icon-link" href="<?php echo esc_url( get_permalink( get_option( 'woocommerce_myaccount_page_id' ) ) ); ?>" aria-label="<?php esc_attr_e( 'حساب کاربری', 'shalangbin' ); ?>">
				<?php shalangbin_icon( 'user' ); ?><span><?php esc_html_e( 'حساب', 'shalangbin' ); ?></span>
			</a>
			<?php endif; ?>
			<button class="icon-button menu-toggle" aria-label="<?php esc_attr_e( 'باز کردن منو', 'shalangbin' ); ?>" aria-expanded="false" aria-controls="main-nav">
				<?php shalangbin_icon( 'menu' ); ?>
			</button>
		</div>
	</div>

	<div class="nav-wrap">
		<nav class="container main-nav" id="main-nav" aria-label="<?php esc_attr_e( 'منوی اصلی', 'shalangbin' ); ?>">
			<?php
			// عین طرح اصلی (build.mjs): لینک‌های تخت، بدون ul/li و بدون ساب‌منو.
			// آیتم‌ها از فهرست وردپرس می‌آیند اما walker تخت، فقط <a> خروجی می‌دهد؛
			// بدون فهرست، fallbackِ تختِ همین طرح رندر می‌شود.
			if ( has_nav_menu( 'primary' ) ) {
				wp_nav_menu( array(
					'theme_location' => 'primary',
					'container'      => false,
					'menu_class'     => '',
					'items_wrap'     => '%3$s',
					'depth'          => 1,
					'fallback_cb'    => false,
					'walker'         => new ShalangBin_Flat_Nav_Walker(),
				) );
			} else {
				shalangbin_menu_fallback();
			}
			?>
		</nav>
	</div>
</header>

<main id="main" class="site-main">
