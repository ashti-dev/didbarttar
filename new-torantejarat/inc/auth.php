<?php
/**
 * Auth page: login/register AJAX endpoint + asset wiring.
 *
 * Handlers use WP core wp_signon / wp_create_user, nonce verification,
 * server-side re-validation (Persian digits normalized before checks).
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_ajax_nopriv_torantejarat_login', __NAMESPACE__ . '\\torantejarat_auth_login' );
add_action( 'wp_ajax_nopriv_torantejarat_register', __NAMESPACE__ . '\\torantejarat_auth_register' );

function torantejarat_auth_enqueue() {
	if ( ! is_page_template( 'page-templates/login-register.php' ) ) {
		return;
	}
	wp_enqueue_style( 'torantejarat-auth', get_template_directory_uri() . '/assets/css/auth.css', array( 'torantejarat-fonts' ), torantejarat_asset_version( 'assets/css/auth.css' ) );
	wp_enqueue_script( 'torantejarat-auth', get_template_directory_uri() . '/assets/js/auth.js', array(), torantejarat_asset_version( 'assets/js/auth.js' ), array( 'in_footer' => true, 'strategy' => 'defer' ) );
	wp_localize_script(
		'torantejarat-auth',
		'torantejaratAuth',
		array(
			'ajaxUrl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => wp_create_nonce( 'torantejarat_auth' ),
		)
	);
}
add_action( 'wp_enqueue_scripts', __NAMESPACE__ . '\\torantejarat_auth_enqueue', 20 );

function torantejarat_auth_get_page_url() {
	$page = get_page_by_path( 'login-register' );
	if ( ! $page ) {
		$page = get_page_by_path( 'auth' );
	}
	return $page ? get_permalink( $page->ID ) : wp_login_url();
}

/**
 * Link guests to the auth page, logged-in users to their account.
 */
function torantejarat_auth_url() {
	if ( is_user_logged_in() && wc_get_page_id( 'myaccount' ) > 0 ) {
		return wc_get_page_permalink( 'myaccount' );
	}
	return torantejarat_auth_get_page_url();
}

/* The auth page is the single login entry: logged-out account visits go there. */
function torantejarat_auth_account_redirect() {
	if ( is_admin() || is_user_logged_in() ) {
		return;
	}
	if ( ! function_exists( 'is_account_page' ) || ! is_account_page() ) {
		return;
	}
	if ( function_exists( 'is_wc_endpoint_url' ) && is_wc_endpoint_url() ) {
		return;
	}
	if ( 'GET' !== ( isset( $_SERVER['REQUEST_METHOD'] ) ? $_SERVER['REQUEST_METHOD'] : 'GET' ) ) { // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- Method check only.
		return;
	}
	wp_safe_redirect( torantejarat_auth_get_page_url() );
	exit;
}
add_action( 'template_redirect', __NAMESPACE__ . '\\torantejarat_auth_account_redirect', 5 );

/* After logout, land on the auth page instead of my-account. */
function torantejarat_auth_logout_redirect( $redirect ) {
	return torantejarat_auth_get_page_url();
}
add_filter( 'woocommerce_logout_default_redirect_url', __NAMESPACE__ . '\\torantejarat_auth_logout_redirect' );
add_filter( 'logout_redirect', __NAMESPACE__ . '\\torantejarat_auth_logout_redirect' );

function torantejarat_normalize_digits( $value ) {
	return str_replace( array( '۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹', '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' ), array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9', '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' ), $value );
}

function torantejarat_send_json( $success, $message, $redirect = '' ) {
	wp_send_json(
		array(
			'success'  => $success,
			'message'  => $message,
			'redirect' => $redirect,
		)
	);
}

/**
 * Login via wp_signon. Accepts mobile or email as login.
 */
function torantejarat_auth_login() {
	check_ajax_referer( 'torantejarat_auth', 'nonce' );

	$login = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
	$pass  = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_signon hashes it.

	if ( '' === $login || '' === $pass ) {
		torantejarat_send_json( false, __( 'نام کاربری و رمز عبور را وارد کنید.', 'torantejarat' ) );
	}

	$login = torantejarat_normalize_digits( $login );
	$login = str_replace( '.', '', $login );

	$creds = array(
		'user_login'    => $login,
		'user_password' => $pass,
		'rememberme'     => isset( $_POST['rememberme'] ) && 'forever' === $_POST['rememberme'],
	);

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		torantejarat_send_json( false, __( 'نام کاربری یا رمز عبور اشتباه است.', 'torantejarat' ) );
	}

	torantejarat_send_json( true, __( 'خوش آمدید.', 'torantejarat' ), wc_get_page_permalink( 'myaccount' ) );
}

/**
 * Register via wp_create_user. Mobile or email required.
 */
function torantejarat_auth_register() {
	check_ajax_referer( 'torantejarat_auth', 'nonce' );

	$name    = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$mobile  = isset( $_POST['mobile'] ) ? torantejarat_normalize_digits( sanitize_text_field( wp_unslash( $_POST['mobile'] ) ) ) : '';
	$email   = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$pass1   = isset( $_POST['pass1'] ) ? (string) wp_unslash( $_POST['pass1'] ) : ''; // phpcs:ignore -- wp_create_user hashes it.
	$pass2   = isset( $_POST['pass2'] ) ? (string) wp_unslash( $_POST['pass2'] ) : ''; // phpcs:ignore -- checked against pass1 only.
	$terms   = isset( $_POST['terms'] ) && 'yes' === $_POST['terms'];

	if ( '' === $name || mb_strlen( $name ) < 3 ) {
		torantejarat_send_json( false, __( 'نام و نام خانوادگی را کامل وارد کنید.', 'torantejarat' ) );
	}

	$mobile = ltrim( $mobile, '0' );
	$mobile = '0' . ltrim( $mobile, '0' );

	if ( '' === $email && ! preg_match( '/^09\d{9}$/', $mobile ) ) {
		torantejarat_send_json( false, __( 'شماره موبایل معتبر نیست (مثال: 09123456789).', 'torantejarat' ) );
	}

	if ( '' !== $email && ! is_email( $email ) ) {
		torantejarat_send_json( false, __( 'ایمیل معتبر نیست.', 'torantejarat' ) );
	}

	if ( strlen( $pass1 ) < 8 ) {
		torantejarat_send_json( false, __( 'رمز عبور حداقل ۸ کاراکتر باشد.', 'torantejarat' ) );
	}

	if ( $pass1 !== $pass2 ) {
		torantejarat_send_json( false, __( 'تکرار رمز عبور مطابقت ندارد.', 'torantejarat' ) );
	}

	if ( ! $terms ) {
		torantejarat_send_json( false, __( 'پذیرش قوانین الزامی است.', 'torantejarat' ) );
	}

	if ( '' !== $email && email_exists( $email ) ) {
		torantejarat_send_json( false, __( 'ایمیل قبلاً ثبت شده است.', 'torantejarat' ) );
	}

	$username = '' !== $email ? $email : 'user_' . wp_generate_password( 8, false );
	if ( username_exists( $username ) ) {
		torantejarat_send_json( false, __( 'حساب کاربری با این اطلاعات وجود دارد.', 'torantejarat' ) );
	}

	$user_id = wp_create_user( $username, $pass1, $email );
	if ( is_wp_error( $user_id ) ) {
		torantejarat_send_json( false, __( 'خطا در ثبت‌نام. دوباره تلاش کنید.', 'torantejarat' ) );
	}

	wp_update_user(
		array(
			'ID'           => $user_id,
			'display_name' => $name,
		)
	);

	if ( preg_match( '/^09\d{9}$/', $mobile ) ) {
		update_user_meta( $user_id, 'billing_phone', $mobile );
	}

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true );

	torantejarat_send_json( true, __( 'ثبت‌نام انجام شد. خوش آمدید.', 'torantejarat' ), wc_get_page_permalink( 'myaccount' ) );
}
