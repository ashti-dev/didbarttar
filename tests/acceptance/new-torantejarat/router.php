<?php
/* PHP built-in-server router for the disposable WordPress acceptance fixture. */
$torantejarat_ci_root = rtrim( (string) getenv( 'WP_ROOT' ), '/' );
if ( '' === $torantejarat_ci_root || ! is_file( $torantejarat_ci_root . '/index.php' ) ) {
	http_response_code( 500 );
	echo 'WP_ROOT is missing or invalid.';
	return;
}
$torantejarat_ci_path = (string) parse_url( $_SERVER['REQUEST_URI'], PHP_URL_PATH );
$torantejarat_ci_file = $torantejarat_ci_root . $torantejarat_ci_path;
if ( '/' !== $torantejarat_ci_path && is_file( $torantejarat_ci_file ) ) {
	return false;
}
require $torantejarat_ci_root . '/index.php';
