<?php
/**
 * Publish-mode handler — preview vs public.
 *
 * Writes only the two native WordPress/WooCommerce options:
 * - woocommerce_coming_soon: yes (preview) / no (public)
 * - blog_public:             0 (noindex) / 1 (index)
 *
 * It is intentionally separate from the Settings API: torantejarat_general
 * stores none of these values, and no $schema field or sanitize rule changes.
 *
 * @package ToranTejarat
 */

namespace ToranTejarat\Theme;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'admin_post_torantejarat_publish_mode', __NAMESPACE__ . '\\torantejarat_handle_publish_mode' );

/**
 * Redirect back to the dedicated publish utility page with a short status code.
 *
 * @param string $notice Notice key.
 */
function torantejarat_publish_mode_redirect( $notice ) {
	$url = add_query_arg(
		array(
			'page'       => 'torantejarat-settings',
			'section'    => 'general',
			'sub'        => 'publish',
			'tt_publish' => sanitize_key( $notice ),
		),
		admin_url( 'admin.php' )
	);

	wp_safe_redirect( $url );
	exit;
}

/**
 * Handle a preview/public switch.
 *
 * Public mode requires an explicit confirmation and non-sample sender emails.
 * Preview mode is always allowed. Nothing else is written.
 */
function torantejarat_handle_publish_mode() {
	if ( ! current_user_can( 'manage_options' ) ) {
		wp_die(
			esc_html__( 'شما اجازهٔ تغییر حالت انتشار را ندارید.', 'torantejarat' ),
			'',
			array( 'response' => 403 )
		);
	}

	check_admin_referer( 'torantejarat_publish_mode' );

	$mode = isset( $_POST['mode'] ) ? sanitize_key( wp_unslash( $_POST['mode'] ) ) : '';
	if ( ! in_array( $mode, array( 'preview', 'public' ), true ) ) {
		torantejarat_publish_mode_redirect( 'invalid' );
	}

	if ( 'public' === $mode ) {
		$confirm = isset( $_POST['confirm'] ) ? sanitize_key( wp_unslash( $_POST['confirm'] ) ) : '';
		if ( 'yes' !== $confirm ) {
			torantejarat_publish_mode_redirect( 'confirm' );
		}

		$admin_email = (string) get_option( 'admin_email', '' );
		$woo_email   = (string) get_option( 'woocommerce_email_from_address', '' );
		if ( false !== strpos( $admin_email, 'example.' ) || false !== strpos( $woo_email, 'example.' ) ) {
			torantejarat_publish_mode_redirect( 'email' );
		}

		update_option( 'woocommerce_coming_soon', 'no' );
		update_option( 'blog_public', 1 );
		torantejarat_publish_mode_redirect( 'public' );
	}

	update_option( 'woocommerce_coming_soon', 'yes' );
	update_option( 'blog_public', 0 );
	torantejarat_publish_mode_redirect( 'preview' );
}
