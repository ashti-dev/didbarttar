<?php
/**
 * Login / Register — بِلکوکم
 *
 * Magazine-style customer access with tabs, native POST fallback and the
 * same admin-ajax handlers. Core recovery remains on branded wp-login.php.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Template Name: ورود و ثبت‌نام */
$can_register    = (bool) get_option( 'users_can_register' ) && ! torantejarat_option( 'login', 'login_hide_register' );
$show_recovery   = ! torantejarat_option( 'login', 'login_hide_lostpassword' );
$auth_error      = isset( $_GET['auth_error'] ) ? sanitize_text_field( wp_unslash( $_GET['auth_error'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only server-generated form error.
$auth_error_form = isset( $_GET['auth_form'] ) && 'register' === $_GET['auth_form'] ? 'register' : 'login'; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
$initial_form    = $can_register && 'register' === $auth_error_form ? 'register' : 'login';
$redirect_to     = torantejarat_auth_requested_redirect( '' );
$reauth          = ! empty( $_GET['reauth'] ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Core reauthentication flag only.
$login_title     = torantejarat_option( 'login', 'login_title', get_bloginfo( 'name' ) );
$login_text      = torantejarat_option( 'login', 'login_text', __( 'برای پیگیری سفارش، مشاهده فاکتور و مدیریت درخواست‌ها وارد حساب مشتریان شوید.', 'torantejarat' ) );
$login_bg        = sanitize_hex_color( torantejarat_option( 'login', 'login_bg', '#172325' ) );
$login_accent    = sanitize_hex_color( torantejarat_option( 'login', 'login_accent', '#f77b3e' ) );
$login_bg        = $login_bg ? $login_bg : '#172325';
$login_accent    = $login_accent ? $login_accent : '#f77b3e';
$logo_url        = torantejarat_login_logo_url();
$story_image     = get_template_directory_uri() . '/assets/images/device-orange.webp';
$ajax_url        = admin_url( 'admin-ajax.php' );
$auth_nonce      = wp_create_nonce( 'torantejarat_auth' );
$terms_page      = get_page_by_path( 'terms' );
$terms_url       = $terms_page ? get_permalink( $terms_page ) : get_privacy_policy_url();
?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php echo esc_attr( get_bloginfo( 'charset' ) ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<?php wp_head(); ?>
	<noscript><style>.auth__tabs,.auth__switch-btn,.auth__pass-toggle,.auth__meter{display:none!important}</style></noscript>
</head>
<body <?php body_class( 'torantejarat-site torantejarat-auth-page' ); ?>>
<?php wp_body_open(); ?>
<a class="torantejarat-skip" href="#torantejarat-main"><?php esc_html_e( 'رفتن به فرم ورود', 'torantejarat' ); ?></a>
<main id="torantejarat-main" class="torantejarat-main torantejarat-main--auth" tabindex="-1">
	<div class="auth" style="--auth-bg:<?php echo esc_attr( $login_bg ); ?>;--auth-accent:<?php echo esc_attr( $login_accent ); ?>;">
		<div class="auth__layout">

			<aside class="auth__panel" aria-label="<?php esc_attr_e( 'معرفی حساب مشتریان', 'torantejarat' ); ?>">
				<div class="auth__brand-row">
					<a class="auth__brand-card" href="<?php echo esc_url( home_url( '/' ) ); ?>" aria-label="<?php esc_attr_e( 'بازگشت به صفحه اصلی', 'torantejarat' ); ?>">
						<img src="<?php echo esc_url( $logo_url ); ?>" alt="<?php echo esc_attr( get_bloginfo( 'name' ) ); ?>">
					</a>
					<div class="auth__brand-copy">
						<strong><?php esc_html_e( 'پرتال مشتریان', 'torantejarat' ); ?></strong>
						<span><?php esc_html_e( 'فروشگاه تجهیزات بازرسی', 'torantejarat' ); ?></span>
					</div>
				</div>

				<div class="auth__editorial">
					<p class="auth__eyebrow"><?php esc_html_e( 'حساب شما، مسیر پیگیری خرید', 'torantejarat' ); ?></p>
					<h1 class="auth__headline"><?php echo esc_html( $login_title ); ?></h1>
					<p class="auth__intro"><?php echo esc_html( $login_text ); ?></p>
				</div>

				<figure class="auth__story">
					<img src="<?php echo esc_url( $story_image ); ?>" width="956" height="1020" alt="<?php esc_attr_e( 'دوربین بازرسی صنعتی همراه با کابل و نمایشگر', 'torantejarat' ); ?>" loading="eager" fetchpriority="high" decoding="async">
					<figcaption>
						<span><?php esc_html_e( 'تجهیزات میدانی', 'torantejarat' ); ?></span>
						<strong><?php esc_html_e( 'برای بررسی نقاطی که دید مستقیم ندارند.', 'torantejarat' ); ?></strong>
					</figcaption>
				</figure>

				<ul class="auth__perks">
					<li>
						<span class="auth__perk-number" aria-hidden="true">01</span>
						<strong><?php esc_html_e( 'سفارش و فاکتور', 'torantejarat' ); ?></strong>
						<small><?php esc_html_e( 'جزئیات خرید و وضعیت آن در یک جا', 'torantejarat' ); ?></small>
					</li>
					<li>
						<span class="auth__perk-number" aria-hidden="true">02</span>
						<strong><?php esc_html_e( 'مشخصات خرید', 'torantejarat' ); ?></strong>
						<small><?php esc_html_e( 'ذخیره اطلاعات برای سفارش‌های بعدی', 'torantejarat' ); ?></small>
					</li>
					<li>
						<span class="auth__perk-number" aria-hidden="true">03</span>
						<strong><?php esc_html_e( 'درخواست و تیکت', 'torantejarat' ); ?></strong>
						<small><?php esc_html_e( 'پیگیری درخواست‌های ثبت‌شده در حساب', 'torantejarat' ); ?></small>
					</li>
				</ul>
			</aside>

			<section class="auth__forms" aria-label="<?php esc_attr_e( 'ورود و ثبت‌نام مشتری', 'torantejarat' ); ?>">
				<div class="auth__utility">
					<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
						<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M19 12H5"/><path d="m12 19-7-7 7-7"/></svg>
						<?php esc_html_e( 'بازگشت به فروشگاه', 'torantejarat' ); ?>
					</a>
					<span><i aria-hidden="true"></i><?php esc_html_e( 'دسترسی به سفارش‌ها و فاکتورها', 'torantejarat' ); ?></span>
				</div>

				<div class="auth__form-card">
					<div class="auth__form-meta">
						<span><?php esc_html_e( 'حساب مشتری', 'torantejarat' ); ?></span>
						<span aria-hidden="true">ACCOUNT / 01</span>
					</div>

					<div class="auth__tabs" role="tablist" aria-label="<?php esc_attr_e( 'ورود یا ثبت‌نام', 'torantejarat' ); ?>">
						<button type="button" class="auth__tab<?php echo 'login' === $initial_form ? ' is-active' : ''; ?>" id="auth-tab-login" role="tab" aria-selected="<?php echo 'login' === $initial_form ? 'true' : 'false'; ?>" aria-controls="auth-panel-login" data-tab="login">
							<?php esc_html_e( 'ورود', 'torantejarat' ); ?>
						</button>
						<?php if ( $can_register ) : ?>
							<button type="button" class="auth__tab<?php echo 'register' === $initial_form ? ' is-active' : ''; ?>" id="auth-tab-register" role="tab" aria-selected="<?php echo 'register' === $initial_form ? 'true' : 'false'; ?>" aria-controls="auth-panel-register" data-tab="register">
								<?php esc_html_e( 'ثبت‌نام', 'torantejarat' ); ?>
							</button>
						<?php endif; ?>
					</div>

					<div class="auth__pane<?php echo 'login' === $initial_form ? ' is-active' : ''; ?>" id="auth-panel-login" role="tabpanel" aria-labelledby="auth-tab-login"<?php echo 'login' !== $initial_form ? ' hidden' : ''; ?>>
						<h2 class="auth__heading"><?php echo $reauth ? esc_html__( 'تأیید هویت دوباره', 'torantejarat' ) : esc_html__( 'خوش برگشتید', 'torantejarat' ); ?></h2>
						<p class="auth__sub"><?php esc_html_e( 'با موبایل، ایمیل یا نام کاربری خود وارد شوید.', 'torantejarat' ); ?></p>

						<form class="auth__form" id="auth-login-form" method="post" action="<?php echo esc_url( $ajax_url ); ?>" data-endpoint="<?php echo esc_url( $ajax_url ); ?>" novalidate>
							<input type="hidden" name="action" value="torantejarat_login">
							<input type="hidden" name="nonce" value="<?php echo esc_attr( $auth_nonce ); ?>">
							<?php if ( '' !== $redirect_to ) : ?>
								<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
							<?php endif; ?>
							<?php if ( $reauth ) : ?>
								<input type="hidden" name="reauth" value="1">
							<?php endif; ?>

							<?php if ( $reauth && ! $auth_error ) : ?>
								<p class="auth__message is-info" role="status"><?php esc_html_e( 'برای ادامه، رمز حساب را دوباره وارد کنید.', 'torantejarat' ); ?></p>
							<?php endif; ?>
							<?php if ( $auth_error && 'login' === $auth_error_form ) : ?>
								<p class="auth__message is-error" role="alert"><?php echo esc_html( $auth_error ); ?></p>
							<?php endif; ?>

							<div class="auth__field">
								<label for="login-user"><?php esc_html_e( 'موبایل، ایمیل یا نام کاربری', 'torantejarat' ); ?></label>
								<div class="auth__control">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
									<input type="text" id="login-user" name="log" autocomplete="username" autocapitalize="off" spellcheck="false" required>
								</div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<div class="auth__field">
								<label for="login-pass"><?php esc_html_e( 'رمز عبور', 'torantejarat' ); ?></label>
								<div class="auth__control auth__control--password">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
									<input type="password" id="login-pass" name="pwd" autocomplete="current-password" required>
									<button type="button" class="auth__pass-toggle" aria-label="<?php esc_attr_e( 'نمایش رمز عبور', 'torantejarat' ); ?>" aria-pressed="false"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
								</div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<div class="auth__options">
								<label class="auth__remember">
									<input type="checkbox" name="rememberme" value="forever">
									<span><?php esc_html_e( 'مرا به خاطر بسپار', 'torantejarat' ); ?></span>
								</label>
								<?php if ( $show_recovery ) : ?>
									<a class="auth__forgot" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'فراموشی رمز عبور؟', 'torantejarat' ); ?></a>
								<?php endif; ?>
							</div>

							<button type="submit" class="torantejarat-btn torantejarat-btn-primary torantejarat-btn-block"><?php esc_html_e( 'ورود به حساب', 'torantejarat' ); ?></button>
							<p class="auth__message" aria-live="polite"></p>

							<?php if ( $can_register ) : ?>
								<p class="auth__switch">
									<?php esc_html_e( 'حساب کاربری ندارید؟', 'torantejarat' ); ?>
									<button type="button" class="auth__switch-btn" data-goto="register"><?php esc_html_e( 'ثبت‌نام کنید', 'torantejarat' ); ?></button>
								</p>
								<noscript><p class="auth__switch"><a href="<?php echo esc_url( torantejarat_auth_register_page_url( $redirect_to ) ); ?>"><?php esc_html_e( 'ثبت‌نام در همین صفحه', 'torantejarat' ); ?></a></p></noscript>
							<?php endif; ?>
						</form>
					</div>

					<?php if ( $can_register ) : ?>
						<div class="auth__pane<?php echo 'register' === $initial_form ? ' is-active' : ''; ?>" id="auth-panel-register" role="tabpanel" aria-labelledby="auth-tab-register"<?php echo 'register' !== $initial_form ? ' hidden' : ''; ?>>
							<h2 class="auth__heading"><?php esc_html_e( 'حساب کاربری بسازید', 'torantejarat' ); ?></h2>
							<p class="auth__sub"><?php esc_html_e( 'برای پیگیری سفارش و ذخیره مشخصات خرید.', 'torantejarat' ); ?></p>

							<form class="auth__form" id="auth-register-form" method="post" action="<?php echo esc_url( $ajax_url ); ?>" data-endpoint="<?php echo esc_url( $ajax_url ); ?>" novalidate>
								<input type="hidden" name="action" value="torantejarat_register">
								<input type="hidden" name="nonce" value="<?php echo esc_attr( $auth_nonce ); ?>">
								<?php if ( '' !== $redirect_to ) : ?>
									<input type="hidden" name="redirect_to" value="<?php echo esc_attr( $redirect_to ); ?>">
								<?php endif; ?>
								<?php if ( $reauth ) : ?>
									<input type="hidden" name="reauth" value="1">
								<?php endif; ?>
								<?php if ( $auth_error && 'register' === $auth_error_form ) : ?>
									<p class="auth__message is-error" role="alert"><?php echo esc_html( $auth_error ); ?></p>
								<?php endif; ?>

								<div class="auth__field">
									<label for="reg-name"><?php esc_html_e( 'نام و نام خانوادگی', 'torantejarat' ); ?></label>
									<div class="auth__control">
										<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
										<input type="text" id="reg-name" name="display_name" autocomplete="name" required>
									</div>
									<p class="auth__error" aria-live="polite"></p>
								</div>

								<div class="auth__field">
									<label for="reg-mobile"><?php esc_html_e( 'شماره موبایل', 'torantejarat' ); ?></label>
									<div class="auth__control">
										<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg></span>
										<input type="tel" id="reg-mobile" name="mobile" autocomplete="tel" inputmode="tel" placeholder="09xxxxxxxxx" dir="ltr" required>
									</div>
									<p class="auth__error" aria-live="polite"></p>
								</div>

								<div class="auth__field">
									<label for="reg-email"><?php esc_html_e( 'ایمیل', 'torantejarat' ); ?></label>
									<div class="auth__control">
										<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg></span>
										<input type="email" id="reg-email" name="email" autocomplete="email" inputmode="email" autocapitalize="off" spellcheck="false" dir="ltr" required>
									</div>
									<p class="auth__error" aria-live="polite"></p>
								</div>

								<div class="auth__field">
									<label for="reg-pass"><?php esc_html_e( 'رمز عبور', 'torantejarat' ); ?></label>
									<div class="auth__control auth__control--password">
										<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
										<input type="password" id="reg-pass" name="pass1" autocomplete="new-password" required>
										<button type="button" class="auth__pass-toggle" aria-label="<?php esc_attr_e( 'نمایش رمز عبور', 'torantejarat' ); ?>" aria-pressed="false"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
									</div>
									<div class="auth__meter" aria-hidden="true"><span data-strength="0"></span></div>
									<p class="auth__error" aria-live="polite"></p>
								</div>

								<div class="auth__field">
									<label for="reg-pass2"><?php esc_html_e( 'تکرار رمز عبور', 'torantejarat' ); ?></label>
									<div class="auth__control">
										<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
										<input type="password" id="reg-pass2" name="pass2" autocomplete="new-password" required>
									</div>
									<p class="auth__error" aria-live="polite"></p>
								</div>

								<div class="auth__remember auth__terms">
									<input type="checkbox" id="reg-terms" name="terms" value="yes" required>
									<label for="reg-terms"><?php esc_html_e( 'قوانین و', 'torantejarat' ); ?></label>
									<?php if ( $terms_url ) : ?>
										<a href="<?php echo esc_url( $terms_url ); ?>"><?php esc_html_e( 'حریم خصوصی', 'torantejarat' ); ?></a>
									<?php else : ?>
										<span><?php esc_html_e( 'حریم خصوصی', 'torantejarat' ); ?></span>
									<?php endif; ?>
									<span><?php esc_html_e( 'را خوانده‌ام و می‌پذیرم.', 'torantejarat' ); ?></span>
								</div>
								<p class="auth__error" data-error-for="terms" aria-live="polite"></p>

								<button type="submit" class="torantejarat-btn torantejarat-btn-primary torantejarat-btn-block"><?php esc_html_e( 'ساخت حساب', 'torantejarat' ); ?></button>
								<p class="auth__message" aria-live="polite"></p>

								<p class="auth__switch">
									<?php esc_html_e( 'قبلاً ثبت‌نام کرده‌اید؟', 'torantejarat' ); ?>
									<button type="button" class="auth__switch-btn" data-goto="login"><?php esc_html_e( 'وارد شوید', 'torantejarat' ); ?></button>
								</p>
								<noscript><p class="auth__switch"><a href="<?php echo esc_url( torantejarat_auth_login_page_url( $redirect_to, $reauth ) ); ?>"><?php esc_html_e( 'بازگشت به فرم ورود', 'torantejarat' ); ?></a></p></noscript>
							</form>
						</div>
					<?php endif; ?>
				</div>

				<?php if ( $show_recovery ) : ?>
					<p class="auth__footnote"><?php esc_html_e( 'برای بازیابی رمز، ایمیل ثبت‌شده در حساب لازم است.', 'torantejarat' ); ?></p>
				<?php endif; ?>
			</section>
		</div>
	</div>
</main>
<?php
wp_footer();
?>
</body>
</html>
