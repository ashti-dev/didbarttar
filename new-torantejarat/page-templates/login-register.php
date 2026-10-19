<?php
/**
 * Login / Register — بِلکوکم
 *
 * Tabs: login (mobile/email + password) | register (name, mobile, email, password).
 * Server-ready: add REST/admin-ajax endpoint in data-endpoint per form.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/* Template Name: ورود و ثبت‌نام */
get_header();
?>
<main id="torantejarat-main" class="torantejarat-main torantejarat-main--auth" tabindex="-1">
	<section class="torantejarat-section torantejarat-section--auth"><div class="torantejarat-container torantejarat-page-content">

		<div class="auth">
			<div class="auth__layout">

				<aside class="auth__panel">
					<div class="auth__panel-top">
						<div class="auth__lens" aria-hidden="true">
							<span class="auth__lens-ring"></span>
							<span class="auth__lens-dot"></span>
						</div>
						<div class="auth__brand-block">
							<h2 class="auth__brand"><?php esc_html_e( 'بلکوکم', 'torantejarat' ); ?></h2>
							<p class="auth__tagline"><?php esc_html_e( 'دوربین بازرسی شلنگی؛ انتخاب، خرید و پیگیری سفارش', 'torantejarat' ); ?></p>
						</div>
					</div>
					<ul class="auth__perks">
						<li><?php esc_html_e( 'سابقه سفارش و فاکتورها در یک جا', 'torantejarat' ); ?></li>
						<li><?php esc_html_e( 'پیگیری وضعیت ارسال و پرداخت', 'torantejarat' ); ?></li>
						<li><?php esc_html_e( 'ذخیره مشخصات برای خریدهای بعدی', 'torantejarat' ); ?></li>
					</ul>
				</aside>

				<div class="auth__forms">

					<div class="auth__tabs" role="tablist" aria-label="<?php esc_attr_e( 'ورود یا ثبت‌نام', 'torantejarat' ); ?>">
						<button type="button" class="auth__tab is-active" id="auth-tab-login" role="tab" aria-selected="true" aria-controls="auth-panel-login" data-tab="login">
							<?php esc_html_e( 'ورود', 'torantejarat' ); ?>
						</button>
						<button type="button" class="auth__tab" id="auth-tab-register" role="tab" aria-selected="false" aria-controls="auth-panel-register" data-tab="register">
							<?php esc_html_e( 'ثبت‌نام', 'torantejarat' ); ?>
						</button>
					</div>

					<!-- Login -->
					<div class="auth__pane is-active" id="auth-panel-login" role="tabpanel" aria-labelledby="auth-tab-login">
						<h3 class="auth__heading"><?php esc_html_e( 'خوش برگشتید', 'torantejarat' ); ?></h3>
						<p class="auth__sub"><?php esc_html_e( 'با شماره موبایل یا ایمیل خود وارد شوید.', 'torantejarat' ); ?></p>

						<form class="auth__form" id="auth-login-form" data-endpoint="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate>
							<div class="auth__field">
								<label for="login-user"><?php esc_html_e( 'موبایل یا ایمیل', 'torantejarat' ); ?></label>
								<div class="auth__control">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
									<input type="text" id="login-user" name="log" autocomplete="username" inputmode="tel">
								</div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<div class="auth__field">
								<label for="login-pass"><?php esc_html_e( 'رمز عبور', 'torantejarat' ); ?></label>
								<div class="auth__control">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
									<input type="password" id="login-pass" name="pwd" autocomplete="current-password">
									<button type="button" class="auth__pass-toggle" aria-label="<?php esc_attr_e( 'نمایش رمز عبور', 'torantejarat' ); ?>" aria-pressed="false"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
								</div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<label class="auth__remember">
								<input type="checkbox" name="rememberme" value="forever">
								<span><?php esc_html_e( 'مرا به خاطر بسپار', 'torantejarat' ); ?></span>
								<a class="auth__forgot" href="<?php echo esc_url( wp_lostpassword_url() ); ?>"><?php esc_html_e( 'فراموشی رمز عبور؟', 'torantejarat' ); ?></a>
							</label>

							<button type="submit" class="torantejarat-btn torantejarat-btn-primary torantejarat-btn-block">
								<?php esc_html_e( 'ورود', 'torantejarat' ); ?>
							</button>
							<p class="auth__message" aria-live="polite"></p>

							<p class="auth__switch">
								<?php esc_html_e( 'حساب کاربری ندارید؟', 'torantejarat' ); ?>
								<button type="button" class="auth__switch-btn" data-goto="register"><?php esc_html_e( 'ثبت‌نام کنید', 'torantejarat' ); ?></button>
							</p>
						</form>
					</div>

					<!-- Register -->
					<div class="auth__pane" id="auth-panel-register" role="tabpanel" aria-labelledby="auth-tab-register" hidden>
						<h3 class="auth__heading"><?php esc_html_e( 'حساب کاربری بسازید', 'torantejarat' ); ?></h3>
						<p class="auth__sub"><?php esc_html_e( 'برای پیگیری سفارش و ذخیره مشخصات دستگاه‌ها.', 'torantejarat' ); ?></p>

						<form class="auth__form" id="auth-register-form" data-endpoint="<?php echo esc_url( admin_url( 'admin-ajax.php' ) ); ?>" novalidate>
							<div class="auth__field">
								<label for="reg-name"><?php esc_html_e( 'نام و نام خانوادگی', 'torantejarat' ); ?></label>
								<div class="auth__control">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg></span>
									<input type="text" id="reg-name" name="display_name" autocomplete="name">
								</div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<div class="auth__field">
								<label for="reg-mobile"><?php esc_html_e( 'شماره موبایل', 'torantejarat' ); ?></label>
								<div class="auth__control">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="2" width="14" height="20" rx="2"/><path d="M12 18h.01"/></svg></span>
									<input type="tel" id="reg-mobile" name="mobile" autocomplete="tel" inputmode="tel" placeholder="09xxxxxxxxx" dir="ltr">
								</div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<div class="auth__field">
								<label for="reg-email"><?php esc_html_e( 'ایمیل (اختیاری)', 'torantejarat' ); ?></label>
								<div class="auth__control">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="4" width="20" height="16" rx="2"/><path d="m22 7-10 5L2 7"/></svg></span>
									<input type="email" id="reg-email" name="email" autocomplete="email" inputmode="email">
								</div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<div class="auth__field">
								<label for="reg-pass"><?php esc_html_e( 'رمز عبور', 'torantejarat' ); ?></label>
								<div class="auth__control">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
									<input type="password" id="reg-pass" name="pass1" autocomplete="new-password">
									<button type="button" class="auth__pass-toggle" aria-label="<?php esc_attr_e( 'نمایش رمز عبور', 'torantejarat' ); ?>" aria-pressed="false"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg></button>
								</div>
								<div class="auth__meter" aria-hidden="true"><span data-strength="0"></span></div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<div class="auth__field">
								<label for="reg-pass2"><?php esc_html_e( 'تکرار رمز عبور', 'torantejarat' ); ?></label>
								<div class="auth__control">
									<span class="auth__icon" aria-hidden="true"><svg width="19" height="19" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg></span>
									<input type="password" id="reg-pass2" name="pass2" autocomplete="new-password">
								</div>
								<p class="auth__error" aria-live="polite"></p>
							</div>

							<label class="auth__remember">
								<input type="checkbox" name="terms" value="yes">
								<span><?php esc_html_e( 'قوانین و', 'torantejarat' ); ?></span>
								<a href="<?php echo esc_url( get_permalink( get_page_by_path( 'terms' ) ) ); ?>"><?php esc_html_e( 'حریم خصوصی', 'torantejarat' ); ?></a>
								<span><?php esc_html_e( 'را خوانده‌ام و می‌پذیرم.', 'torantejarat' ); ?></span>
							</label>
							<p class="auth__error" data-error-for="terms" aria-live="polite"></p>

							<button type="submit" class="torantejarat-btn torantejarat-btn-primary torantejarat-btn-block">
								<?php esc_html_e( 'ثبت‌نام', 'torantejarat' ); ?>
							</button>
							<p class="auth__message" aria-live="polite"></p>

							<p class="auth__switch">
								<?php esc_html_e( 'قبلاً ثبت‌نام کرده‌اید؟', 'torantejarat' ); ?>
								<button type="button" class="auth__switch-btn" data-goto="login"><?php esc_html_e( 'وارد شوید', 'torantejarat' ); ?></button>
							</p>
						</form>
					</div>

				</div>
			</div>
		</div>

	</div></section>
</main>
<?php
get_footer();
