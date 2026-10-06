<?php
/**
 * Custom WordPress login screen driven by theme settings (login tab).
 * No plugin, no template override — login_enqueue_scripts + core filters.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function torantejarat_login_logo_url() {
	$custom = torantejarat_option( 'login', 'login_logo' );
	if ( $custom ) {
		return $custom;
	}
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$url = wp_get_attachment_image_url( $logo_id, 'medium' );
		if ( $url ) {
			return $url;
		}
	}
	return get_template_directory_uri() . '/assets/images/favicon.svg';
}

function torantejarat_login_styles() {
	$bg     = torantejarat_option( 'login', 'login_bg', '#172325' );
	$accent = torantejarat_option( 'login', 'login_accent', '#f77b3e' );
	$logo   = torantejarat_login_logo_url();
	?>
	<style id="torantejarat-login">
		body.login{background:<?php echo esc_html( $bg ); ?>;display:flex;align-items:center;justify-content:center;min-height:100vh}
		#login{width:min(400px,calc(100% - 40px));padding:24px 0;margin:0}
		#login h1 a{background-image:url("<?php echo esc_url( $logo ); ?>");background-size:contain;background-position:center;width:120px;height:120px;margin-bottom:8px}
		.torantejarat-login-title{text-align:center;color:#fff;font-size:22px;font-weight:800;margin:0 0 6px}
		.torantejarat-login-text{text-align:center;color:#ffffffb3;font-size:13px;margin:0 0 20px}
		body.login form{background:#fff;border:0;border-radius:16px;box-shadow:0 20px 60px #00000040;padding:26px}
		body.login .button-primary{background:<?php echo esc_html( $accent ); ?>!important;border-color:<?php echo esc_html( $accent ); ?>!important;width:100%;min-height:46px;font-size:14px;font-weight:700}
		body.login #nav a,body.login #backtoblog a{color:#ffffffb3!important}
		body.login #nav a:hover,body.login #backtoblog a:hover{color:#fff!important}
		.privacy-policy-page-link{display:none}
		<?php if ( torantejarat_option( 'login', 'login_hide_register' ) ) : ?>#nav a[href*="action=register"]{display:none}<?php endif; ?>
		<?php if ( torantejarat_option( 'login', 'login_hide_lostpassword' ) ) : ?>#nav a[href*="lostpassword"],.wp-login-lost-password{display:none}<?php endif; ?>
	</style>
	<?php
}

function torantejarat_login_intro( $message ) {
	$title = torantejarat_option( 'login', 'login_title', get_bloginfo( 'name' ) );
	$text  = torantejarat_option( 'login', 'login_text' );
	$intro = '';
	if ( '' !== $title ) {
		$intro .= '<p class="torantejarat-login-title">' . esc_html( $title ) . '</p>';
	}
	if ( '' !== $text ) {
		$intro .= '<p class="torantejarat-login-text">' . esc_html( $text ) . '</p>';
	}
	return $intro . $message;
}
