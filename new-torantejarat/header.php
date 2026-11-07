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

$torantejarat_has_utility = has_nav_menu( 'torantejarat-utility' );
$torantejarat_can_manage  = current_user_can( 'manage_options' ) || current_user_can( 'manage_woocommerce' );
$torantejarat_topbar_text = torantejarat_option( 'general', 'topbar_text', get_bloginfo( 'description' ) );
$torantejarat_topbar_link = torantejarat_option( 'general', 'topbar_link' );

$torantejarat_admin_links = array();
if ( $torantejarat_can_manage ) {
	$torantejarat_admin_links = array(
		array(
			'label' => __( 'داشبورد', 'torantejarat' ),
			'url'   => admin_url(),
			'icon'  => 'home',
		),
		array(
			'label' => __( 'محصولات', 'torantejarat' ),
			'url'   => admin_url( 'edit.php?post_type=product' ),
			'icon'  => 'box',
		),
		array(
			'label' => __( 'سفارشات', 'torantejarat' ),
			'url'   => admin_url( 'edit.php?post_type=shop_order' ),
			'icon'  => 'cart',
		),
		array(
			'label' => __( 'تیکت‌ها', 'torantejarat' ),
			'url'   => torantejarat_woocommerce_available() ? wc_get_account_endpoint_url( 'tickets' ) : admin_url(),
			'icon'  => 'ticket',
		),
	);
}

$torantejarat_contact_items = array();
$torantejarat_phone         = torantejarat_option( 'contact', 'phone' );
if ( $torantejarat_phone ) {
	$torantejarat_contact_items[] = array(
		'icon'  => 'phone',
		'label' => __( 'تلفن تماس', 'torantejarat' ),
		'url'   => 'tel:' . $torantejarat_phone,
		'text'  => torantejarat_option( 'contact', 'phone_display', $torantejarat_phone ),
	);
}
$torantejarat_email = torantejarat_option( 'contact', 'email' );
if ( $torantejarat_email ) {
	$torantejarat_contact_items[] = array(
		'icon'  => 'mail',
		'label' => __( 'ایمیل', 'torantejarat' ),
		'url'   => 'mailto:' . $torantejarat_email,
		'text'  => $torantejarat_email,
	);
}
$torantejarat_address = torantejarat_option( 'contact', 'address' );
if ( $torantejarat_address ) {
	$torantejarat_contact_items[] = array(
		'icon'  => 'pin',
		'label' => __( 'نشانی', 'torantejarat' ),
		'url'   => '',
		'text'  => $torantejarat_address,
	);
}

$torantejarat_topbar_socials = array(
	'instagram' => __( 'اینستاگرام', 'torantejarat' ),
	'telegram'  => __( 'تلگرام', 'torantejarat' ),
	'aparat'    => __( 'آپارات', 'torantejarat' ),
	'youtube'   => __( 'یوتیوب', 'torantejarat' ),
);
$torantejarat_social_links  = array();
foreach ( $torantejarat_topbar_socials as $torantejarat_key => $torantejarat_label ) {
	$torantejarat_url = torantejarat_option( 'social', $torantejarat_key );
	if ( $torantejarat_url ) {
		$torantejarat_social_links[ $torantejarat_key ] = array(
			'label' => $torantejarat_label,
			'url'   => $torantejarat_url,
			'icon'  => $torantejarat_key,
		);
	}
}

$torantejarat_has_contacts = (bool) $torantejarat_contact_items || (bool) $torantejarat_social_links;
$torantejarat_show_topbar  = $torantejarat_has_utility || $torantejarat_has_contacts || $torantejarat_admin_links || $torantejarat_topbar_text;
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
<?php if ( $torantejarat_show_topbar ) : ?>
	<div class="torantejarat-topbar<?php echo $torantejarat_has_utility ? '' : ' torantejarat-topbar-hide-mobile'; ?>">
		<div class="torantejarat-container">
			<div class="torantejarat-topbar-admin">
				<?php if ( $torantejarat_admin_links ) : ?>
					<?php foreach ( $torantejarat_admin_links as $torantejarat_admin_link ) : ?>
						<a href="<?php echo esc_url( $torantejarat_admin_link['url'] ); ?>"><?php torantejarat_icon( $torantejarat_admin_link['icon'] ); ?><span><?php echo esc_html( $torantejarat_admin_link['label'] ); ?></span></a>
					<?php endforeach; ?>
				<?php elseif ( $torantejarat_topbar_link ) : ?>
					<a href="<?php echo esc_url( $torantejarat_topbar_link ); ?>"><?php torantejarat_icon( 'bell' ); ?><span><?php echo esc_html( $torantejarat_topbar_text ); ?></span></a>
				<?php else : ?>
					<span class="torantejarat-topbar-note"><?php torantejarat_icon( 'bell' ); ?><span><?php echo esc_html( $torantejarat_topbar_text ); ?></span></span>
				<?php endif; ?>
			</div>
			<?php if ( $torantejarat_has_utility ) : ?>
				<nav class="torantejarat-topbar-utility" aria-label="<?php esc_attr_e( 'منوی دسترسی سریع', 'torantejarat' ); ?>">
					<?php wp_nav_menu( array( 'theme_location' => 'torantejarat-utility', 'container' => false, 'menu_class' => 'torantejarat-utility-menu', 'fallback_cb' => false, 'depth' => 1 ) ); ?>
				</nav>
			<?php endif; ?>
			<?php if ( $torantejarat_has_contacts ) : ?>
				<div class="torantejarat-topbar-contact">
					<?php foreach ( $torantejarat_contact_items as $torantejarat_contact_item ) : ?>
						<?php if ( $torantejarat_contact_item['url'] ) : ?>
							<a href="<?php echo esc_url( $torantejarat_contact_item['url'] ); ?>" aria-label="<?php echo esc_attr( $torantejarat_contact_item['label'] ); ?>"><?php torantejarat_icon( $torantejarat_contact_item['icon'] ); ?><span><?php echo esc_html( $torantejarat_contact_item['text'] ); ?></span></a>
						<?php else : ?>
							<span class="torantejarat-topbar-item" aria-label="<?php echo esc_attr( $torantejarat_contact_item['label'] ); ?>"><?php torantejarat_icon( $torantejarat_contact_item['icon'] ); ?><span><?php echo esc_html( $torantejarat_contact_item['text'] ); ?></span></span>
						<?php endif; ?>
					<?php endforeach; ?>
					<?php if ( $torantejarat_social_links ) : ?>
						<div class="torantejarat-topbar-socials">
							<?php foreach ( $torantejarat_social_links as $torantejarat_social ) : ?>
								<a href="<?php echo esc_url( $torantejarat_social['url'] ); ?>" target="_blank" rel="noopener noreferrer" aria-label="<?php echo esc_attr( $torantejarat_social['label'] ); ?>"><?php torantejarat_icon( $torantejarat_social['icon'] ); ?></a>
							<?php endforeach; ?>
						</div>
					<?php endif; ?>
				</div>
			<?php endif; ?>
		</div>
	</div>
<?php endif; ?>
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
					<div class="torantejarat-support-wrap">
\t\t\t\t\t\t<button class="torantejarat-nav-btn torantejarat-support-btn" aria-haspopup="true" aria-expanded="false"><?php torantejarat_icon( 'chat' ); ?><span><?php esc_html_e( 'پشتیبانی', 'torantejarat' ); ?></span></button>
\t\t\t\t\t\t<ul class="torantejarat-support-submenu">
\t\t\t\t\t\t\t<li><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'tickets' ) ); ?>"><?php esc_html_e( 'لیست تیکت‌ها', 'torantejarat' ); ?></a></li>
\t\t\t\t\t\t\t<li><a href="<?php echo esc_url( wc_get_account_endpoint_url( 'tickets' ) ); ?>#torantejarat-new-ticket"><?php esc_html_e( 'ایجاد تیکت', 'torantejarat' ); ?></a></li>
\t\t\t\t\t\t</ul>
\t\t\t\t\t</div>
					<a class="torantejarat-nav-btn is-deals" href="<?php echo esc_url( $torantejarat_deals_url ); ?>"><?php torantejarat_icon( 'percent' ); ?><span><?php esc_html_e( 'شگفت‌انگیزها', 'torantejarat' ); ?></span></a>
				</div>
			</nav>
		</div>
	<?php endif; ?>
</header>
