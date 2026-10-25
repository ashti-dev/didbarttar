<?php
/**
 * Edit address form
 * Overridden for FrontBaz Modern Store Account
 *
 * @version 9.3.0
 */

defined( 'ABSPATH' ) || exit;

$page_title = ( 'billing' === $load_address ) ? esc_html__( 'آدرس صورتحساب و فاکتور', 'torantejarat' ) : esc_html__( 'آدرس تحویل سفارش (حمل و نقل)', 'torantejarat' );

do_action( 'woocommerce_before_edit_account_address_form' ); ?>

<?php if ( ! $load_address ) : ?>
	<?php wc_get_template( 'myaccount/my-address.php' ); ?>
<?php else : ?>

	<div class="fb-edit-address-wrap">

		<div class="fb-content-card-header">
			<div class="fb-header-icon-title">
				<span class="fb-card-icon">
					<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
						<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path>
						<circle cx="12" cy="10" r="3"></circle>
					</svg>
				</span>
				<div>
					<h3 class="fb-card-title"><?php echo esc_html( $page_title ); ?></h3>
					<p class="fb-card-desc"><?php esc_html_e( 'نشانی دقیق و شماره تماس جهت هماهنگی تحویل مرسوله را وارد فرمایید.', 'torantejarat' ); ?></p>
				</div>
			</div>
		</div>

		<form method="post" class="woocommerce-EditAddressForm fb-edit-address-form" novalidate>

			<div class="woocommerce-address-fields fb-address-fields-wrap">
				<?php do_action( "woocommerce_before_edit_address_form_{$load_address}" ); ?>

				<div class="woocommerce-address-fields__field-wrapper fb-fields-grid">
					<?php
					foreach ( $address as $key => $field ) {
						woocommerce_form_field( $key, $field, wc_get_post_data_by_key( $key, $field['value'] ) );
					}
					?>
				</div>

				<?php do_action( "woocommerce_after_edit_address_form_{$load_address}" ); ?>

				<div class="fb-form-submit-row">
					<button type="submit" class="woocommerce-Button button fb-btn-primary" name="save_address" value="<?php esc_attr_e( 'ذخیره آدرس', 'torantejarat' ); ?>">
						<?php esc_html_e( 'ذخیره آدرس', 'torantejarat' ); ?>
					</button>
					<?php wp_nonce_field( 'woocommerce-edit_address', 'woocommerce-edit-address-nonce' ); ?>
					<input type="hidden" name="action" value="edit_address" />
				</div>
			</div>

		</form>

	</div>

<?php endif; ?>

<?php do_action( 'woocommerce_after_edit_account_address_form' ); ?>
