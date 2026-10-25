<?php
/**
 * My Account page
 * Overridden for FrontBaz Modern Store Account
 *
 * @version 3.5.0
 */

defined( 'ABSPATH' ) || exit;
?>

<div class="fb-myaccount-wrapper">
	<div class="fb-myaccount-grid">

		<!-- Sidebar: User Info Card + Navigation -->
		<aside class="fb-myaccount-sidebar">
			<?php do_action( 'woocommerce_account_navigation' ); ?>
		</aside>

		<!-- Main Content Area -->
		<main class="fb-myaccount-main">
			<div class="fb-myaccount-content-card">
				<?php
				/**
				 * My Account content.
				 *
				 * @since 2.6.0
				 */
				do_action( 'woocommerce_account_content' );
				?>
			</div>
		</main>

	</div>
</div>
