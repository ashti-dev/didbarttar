<?php
/**
 * Edit account form
 * Overridden for FrontBaz Modern Store Account
 *
 * @version 8.7.0
 */

defined( 'ABSPATH' ) || exit;

do_action( 'woocommerce_before_edit_account_form' ); ?>

<div class="fb-edit-account-page-wrap">

	<div class="fb-content-card-header">
		<div class="fb-header-icon-title">
			<span class="fb-card-icon">
				<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
					<circle cx="12" cy="7" r="4"></circle>
				</svg>
			</span>
			<div>
				<h3 class="fb-card-title"><?php esc_html_e( 'اطلاعات و امنیت حساب کاربری', 'torantejarat' ); ?></h3>
				<p class="fb-card-desc"><?php esc_html_e( 'ویرایش نام و نام خانوادگی، آدرس ایمیل و کلمه عبور ورود به حساب.', 'torantejarat' ); ?></p>
			</div>
		</div>
	</div>

	<form class="woocommerce-EditAccountForm edit-account fb-edit-account-form" action="" method="post" <?php do_action( 'woocommerce_edit_account_form_tag' ); ?> >

		<?php do_action( 'woocommerce_edit_account_form_start' ); ?>

		<div class="fb-form-section">
			<h4 class="fb-form-section-title"><?php esc_html_e( 'اطلاعات هویتی', 'torantejarat' ); ?></h4>

			<div class="fb-form-row-grid">
				<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first fb-form-group">
					<label for="account_first_name"><?php esc_html_e( 'نام', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text fb-input" name="account_first_name" id="account_first_name" autocomplete="given-name" value="<?php echo esc_attr( $user->first_name ); ?>" placeholder="<?php esc_attr_e( 'نام خود را وارد کنید...', 'torantejarat' ); ?>" />
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last fb-form-group">
					<label for="account_last_name"><?php esc_html_e( 'نام خانوادگی', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text fb-input" name="account_last_name" id="account_last_name" autocomplete="family-name" value="<?php echo esc_attr( $user->last_name ); ?>" placeholder="<?php esc_attr_e( 'نام خانوادگی...', 'torantejarat' ); ?>" />
				</p>
			</div>

			<div class="fb-form-row-grid">
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide fb-form-group">
					<label for="account_display_name"><?php esc_html_e( 'نام نمایشی در سایت', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="text" class="woocommerce-Input woocommerce-Input--text input-text fb-input" name="account_display_name" id="account_display_name" value="<?php echo esc_attr( $user->display_name ); ?>" />
					<span class="fb-field-hint"><?php esc_html_e( 'این نام در پیام‌ها و دیدگاه‌های شما نمایش داده خواهد شد.', 'woocommerce' ); ?></span>
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide fb-form-group">
					<label for="account_email"><?php esc_html_e( 'آدرس ایمیل', 'woocommerce' ); ?>&nbsp;<span class="required">*</span></label>
					<input type="email" class="woocommerce-Input woocommerce-Input--email input-text fb-input" name="account_email" id="account_email" autocomplete="email" value="<?php echo esc_attr( $user->user_email ); ?>" />
				</p>
			</div>
		</div>

		<?php
			/**
			 * Hook where additional fields can be added.
			 *
			 * @since 8.7.0
			 */
			do_action( 'woocommerce_edit_account_form_fields' );
		?>

		<div class="fb-form-section fb-password-change-section">
			<h4 class="fb-form-section-title">
				<svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
					<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
					<path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
				</svg>
				<?php esc_html_e( 'تغییر کلمه عبور (اختیاری)', 'torantejarat' ); ?>
			</h4>
			<p class="fb-form-section-subtitle"><?php esc_html_e( 'تنها در صورتی که تمایل به تغییر رمز عبور دارید این فیلدها را تکمیل فرمایید.', 'torantejarat' ); ?></p>

			<div class="fb-form-row-grid">
				<p class="woocommerce-form-row woocommerce-form-row--wide form-row form-row-wide fb-form-group">
					<label for="password_current"><?php esc_html_e( 'گذرواژه پیشین (در صورت تمایل به تغییر)', 'woocommerce' ); ?></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--password input-text fb-input" name="password_current" id="password_current" autocomplete="current-password" placeholder="••••••••" />
				</p>
			</div>

			<div class="fb-form-row-grid">
				<p class="woocommerce-form-row woocommerce-form-row--first form-row form-row-first fb-form-group">
					<label for="password_1"><?php esc_html_e( 'گذرواژه جدید', 'woocommerce' ); ?></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--password input-text fb-input" name="password_1" id="password_1" autocomplete="new-password" placeholder="••••••••" />
				</p>
				<p class="woocommerce-form-row woocommerce-form-row--last form-row form-row-last fb-form-group">
					<label for="password_2"><?php esc_html_e( 'تأیید گذرواژه جدید', 'woocommerce' ); ?></label>
					<input type="password" class="woocommerce-Input woocommerce-Input--password input-text fb-input" name="password_2" id="password_2" autocomplete="new-password" placeholder="••••••••" />
				</p>
			</div>
		</div>

		<?php do_action( 'woocommerce_edit_account_form' ); ?>

		<div class="fb-form-submit-row">
			<?php wp_nonce_field( 'save_account_details', 'save-account-details-nonce' ); ?>
			<button type="submit" class="woocommerce-Button button fb-btn-primary" name="save_account_details" value="<?php esc_attr_e( 'ذخیره تغییرات', 'woocommerce' ); ?>">
				<?php esc_html_e( 'ذخیره تغییرات حساب', 'torantejarat' ); ?>
			</button>
			<input type="hidden" name="action" value="save_account_details" />
		</div>

		<?php do_action( 'woocommerce_edit_account_form_end' ); ?>
	</form>

</div>

<?php do_action( 'woocommerce_after_edit_account_form' ); ?>
