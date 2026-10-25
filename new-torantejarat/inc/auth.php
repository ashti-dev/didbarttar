<?php
/**
 * Auth page: login/register AJAX endpoint + asset wiring.
 *
 * Handlers use wp_signon and WooCommerce customer registration when available,
 * nonce verification and POST-only native/AJAX form fallbacks.
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

function torantejarat_auth_account_url() {
	if ( function_exists( 'wc_get_page_id' ) && wc_get_page_id( 'myaccount' ) > 0 ) {
		return wc_get_page_permalink( 'myaccount' );
	}
	return home_url( '/' );
}

/**
 * Link guests to the auth page, logged-in users to their account.
 */
function torantejarat_auth_url() {
	if ( is_user_logged_in() ) {
		return torantejarat_auth_account_url();
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

function torantejarat_auth_normalize_mobile( $raw ) {
	$value = torantejarat_normalize_digits( trim( (string) $raw ) );
	$value = preg_replace( '/[\s\-()]+/', '', $value );

	if ( str_starts_with( $value, '+98' ) ) {
		$value = '0' . substr( $value, 3 );
	} elseif ( str_starts_with( $value, '0098' ) ) {
		$value = '0' . substr( $value, 4 );
	} elseif ( str_starts_with( $value, '98' ) && 12 === strlen( $value ) ) {
		$value = '0' . substr( $value, 2 );
	}

	return preg_match( '/^09\d{9}$/', $value ) ? $value : '';
}

function torantejarat_send_json( $success, $message, $redirect = '' ) {
	$is_ajax = isset( $_POST['auth_via'] ) && 'ajax' === $_POST['auth_via']; // phpcs:ignore WordPress.Security.NonceVerification.Missing -- Nonce is verified by each handler before responding.

	if ( ! $is_ajax ) {
		if ( $success ) {
			wp_safe_redirect( $redirect ? $redirect : torantejarat_auth_account_url() );
			exit;
		}

		$form = isset( $_POST['action'] ) && 'torantejarat_register' === $_POST['action'] ? 'register' : 'login'; // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$url  = add_query_arg(
			array(
				'auth_error' => $message,
				'auth_form'  => $form,
			),
			torantejarat_auth_get_page_url()
		);
		wp_safe_redirect( $url . '#' . $form );
		exit;
	}

	wp_send_json(
		array(
			'success'  => $success,
			'message'  => $message,
			'redirect' => $redirect,
		)
	);
}

function torantejarat_auth_verify_nonce() {
	$nonce = isset( $_POST['nonce'] ) ? sanitize_text_field( wp_unslash( $_POST['nonce'] ) ) : '';
	if ( ! wp_verify_nonce( $nonce, 'torantejarat_auth' ) ) {
		torantejarat_send_json( false, __( 'نشست فرم منقضی شده است. صفحه را تازه‌سازی و دوباره تلاش کنید.', 'torantejarat' ) );
	}
}

/**
 * Login via wp_signon. Accepts mobile or email as login.
 */
function torantejarat_auth_login() {
	torantejarat_auth_verify_nonce();

	$login = isset( $_POST['log'] ) ? sanitize_text_field( wp_unslash( $_POST['log'] ) ) : '';
	$pass  = isset( $_POST['pwd'] ) ? (string) wp_unslash( $_POST['pwd'] ) : ''; // phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized -- wp_signon hashes it.

	if ( '' === $login || '' === $pass ) {
		torantejarat_send_json( false, __( 'نام کاربری و رمز عبور را وارد کنید.', 'torantejarat' ) );
	}

	$login = torantejarat_normalize_digits( $login );

	$creds = array(
		'user_login'    => $login,
		'user_password' => $pass,
		'rememberme'     => isset( $_POST['rememberme'] ) && 'forever' === $_POST['rememberme'],
	);

	$user = wp_signon( $creds, is_ssl() );

	if ( is_wp_error( $user ) ) {
		torantejarat_send_json( false, __( 'نام کاربری یا رمز عبور اشتباه است.', 'torantejarat' ) );
	}

	torantejarat_send_json( true, __( 'خوش آمدید.', 'torantejarat' ), torantejarat_auth_account_url() );
}

/**
 * Register a customer through WooCommerce hooks when available.
 * Email is required for account recovery; mobile is stored as billing phone.
 */
function torantejarat_auth_register() {
	torantejarat_auth_verify_nonce();

	if ( ! get_option( 'users_can_register' ) ) {
		torantejarat_send_json( false, __( 'ثبت‌نام در این سایت غیرفعال است.', 'torantejarat' ) );
	}

	$name   = isset( $_POST['display_name'] ) ? sanitize_text_field( wp_unslash( $_POST['display_name'] ) ) : '';
	$mobile = isset( $_POST['mobile'] ) ? torantejarat_auth_normalize_mobile( sanitize_text_field( wp_unslash( $_POST['mobile'] ) ) ) : '';
	$email  = isset( $_POST['email'] ) ? sanitize_email( wp_unslash( $_POST['email'] ) ) : '';
	$pass1  = isset( $_POST['pass1'] ) ? (string) wp_unslash( $_POST['pass1'] ) : ''; // phpcs:ignore -- The user-creation API hashes it.
	$pass2  = isset( $_POST['pass2'] ) ? (string) wp_unslash( $_POST['pass2'] ) : ''; // phpcs:ignore -- Checked against pass1 only.
	$terms  = isset( $_POST['terms'] ) && 'yes' === $_POST['terms'];

	if ( '' === $name || mb_strlen( $name ) < 3 ) {
		torantejarat_send_json( false, __( 'نام و نام خانوادگی را کامل وارد کنید.', 'torantejarat' ) );
	}

	if ( '' === $mobile ) {
		torantejarat_send_json( false, __( 'شماره موبایل معتبر نیست (مثال: 09123456789).', 'torantejarat' ) );
	}

	if ( '' === $email || ! is_email( $email ) ) {
		torantejarat_send_json( false, __( 'ایمیل معتبر برای ساخت حساب و بازیابی رمز لازم است.', 'torantejarat' ) );
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

	if ( email_exists( $email ) ) {
		torantejarat_send_json( false, __( 'حساب کاربری با این اطلاعات وجود دارد.', 'torantejarat' ) );
	}

	if ( function_exists( 'wc_create_new_customer' ) ) {
		$user_id = wc_create_new_customer(
			$email,
			'',
			$pass1,
			array(
				'display_name' => $name,
				'source'       => 'auth-page',
			)
		);
	} else {
		$user_id = wp_insert_user(
			array(
				'user_login'   => $email,
				'user_email'   => $email,
				'user_pass'    => $pass1,
				'display_name' => $name,
				'role'         => 'subscriber',
			)
		);
	}

	if ( is_wp_error( $user_id ) ) {
		torantejarat_send_json( false, __( 'خطا در ثبت‌نام. دوباره تلاش کنید.', 'torantejarat' ) );
	}

	update_user_meta( $user_id, 'billing_phone', $mobile );

	wp_set_current_user( $user_id );
	wp_set_auth_cookie( $user_id, true );

	torantejarat_send_json( true, __( 'ثبت‌نام انجام شد. خوش آمدید.', 'torantejarat' ), torantejarat_auth_account_url() );
}
