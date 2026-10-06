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
	wp_enqueue_style( 'torantejarat-login-font', get_template_directory_uri() . '/assets/css/source/fonts.css', array(), torantejarat_asset_version( 'assets/css/source/fonts.css' ) );
	?>
	<style id="torantejarat-login">
		body.login{font-family:Vazirmatn,Tahoma,Arial,sans-serif;background:<?php echo esc_html( $bg ); ?>;display:flex;align-items:center;justify-content:center;min-height:100vh;position:relative;overflow-x:hidden}
		body.login::before{content:'';position:fixed;inset:0;background:url("<?php echo esc_url( get_template_directory_uri() . '/assets/images/favicon.svg' ); ?>") center 12%/min(560px,90vw) no-repeat,radial-gradient(600px 400px at 50% 0%,#ffffff14,transparent 70%);opacity:.5;pointer-events:none}
		#login{width:min(400px,calc(100% - 40px));padding:24px 0;margin:0;position:relative}
		#login h1 a{background-image:url("<?php echo esc_url( $logo ); ?>");background-size:contain;background-position:center;width:104px;height:104px;margin-bottom:8px;filter:drop-shadow(0 10px 24px #00000050)}
		.torantejarat-login-title{text-align:center;color:#fff;font-size:22px;font-weight:800;margin:0 0 6px}
		.torantejarat-login-text{text-align:center;color:#ffffffb3;font-size:13px;margin:0 0 20px}
		body.login form{background:#fff;border:0;border-radius:20px;box-shadow:0 24px 70px #00000050;padding:28px 26px}
		body.login label{font-size:12px;font-weight:700;color:#3c4a44}
		body.login input[type=text],body.login input[type=password],body.login input[type=email],body.login input[type=number]{background:#f4f6f2;border:1px solid #e2e7e2;border-radius:12px;padding:0 14px;min-height:48px;font-size:14px;margin-top:6px}
		body.login input[type=text]:focus,body.login input[type=password]:focus,body.login input[type=email]:focus,body.login input[type=number]:focus{border-color:<?php echo esc_html( $accent ); ?>;box-shadow:0 0 0 3px <?php echo esc_html( $accent ); ?>33;outline:none}
		body.login .forgetmenot{margin:14px 0 4px;font-weight:400}
		body.login .submit{margin-top:16px}
		body.login .button-primary{background:<?php echo esc_html( $accent ); ?>!important;border-color:<?php echo esc_html( $accent ); ?>!important;width:100%;min-height:50px;font-size:15px;font-weight:800;border-radius:12px;transition:filter .2s}
		body.login .button-primary:hover{filter:brightness(.93)}
		body.login .notice{border:0;border-radius:12px;border-inline-start:4px solid #d63638;box-shadow:0 10px 30px #00000030}
		body.login .notice-success{border-inline-start-color:#2e7d4f}
		body.login #login_error{border-radius:12px}
		body.login #nav,body.login #backtoblog{display:flex;align-items:center;justify-content:center;gap:8px;text-align:center;margin:18px 0 0;padding:0}
		body.login #nav a,body.login #backtoblog a{display:inline-flex;align-items:center;gap:6px;font-size:13px;font-weight:600;background:#ffffff14;border:1px solid #ffffff2e;border-radius:30px;padding:9px 18px;text-decoration:none;transition:background .2s,border-color .2s}
		body.login #nav a,body.login #backtoblog a{color:#ffffffb3!important}
		body.login #nav a:hover,body.login #backtoblog a:hover{color:#fff!important;background:#ffffff26;border-color:#ffffff4d}
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
