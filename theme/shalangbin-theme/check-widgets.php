<?php
define( 'ABSPATH', 'C:/laragon/www/shalangbin/' );
define( 'WPINC', 'wp-includes' );
require 'C:/laragon/www/shalangbin/wp-load.php';

// Verify the login flow programmatically (nonce is not required for wp_signon).
$user = wp_signon( array(
	'user_login'    => 'customer1',
	'user_password' => 'Test1234!',
	'remember'      => true,
), '' );
if ( is_wp_error( $user ) ) {
	echo 'LOGIN ERROR: ' . $user->get_error_message() . PHP_EOL;
} else {
	echo 'LOGIN OK: ' . $user->user_login . ' (id ' . $user->ID . ')' . PHP_EOL;
	wp_set_current_user( $user->ID );
	// Check my-account endpoint access.
	$account_page = get_option( 'woocommerce_myaccount_page_id' );
	echo 'myaccount page id: ' . $account_page . PHP_EOL;
	echo 'permalink: ' . get_permalink( $account_page ) . PHP_EOL;
	$endpoints = wc_get_account_menu_items();
	echo 'endpoints: ' . implode( ' | ', array_keys( $endpoints ) ) . PHP_EOL;
}