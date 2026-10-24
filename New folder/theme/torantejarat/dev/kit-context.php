<?php
/* Dev: extract a substring of a kit page around a token (minified single-line HTML).
 * Usage: php dev/kit-context.php <slug> <token> [width] */
$root = dirname(__DIR__);
$slug  = isset($argv[1]) ? $argv[1] : 'index';
$token = isset($argv[2]) ? $argv[2] : '';
$width = isset($argv[3]) ? (int) $argv[3] : 600;
$f = $root . '/قالب html/' . $slug . '.html';
if ( ! is_file( $f ) ) { fwrite( STDERR, "not found: $f\n" ); exit( 1 ); }
$html = file_get_contents( $f );
$pos = 0;
while ( ( $pos = strpos( $html, $token, $pos ) ) !== false ) {
	printf( "@%d\n%s\n\n", $pos, substr( $html, max( 0, $pos - $width / 2 ), $width ) );
	$pos += strlen( $token );
}