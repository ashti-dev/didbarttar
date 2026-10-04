<?php
/* Dev: show CSS context around a token occurrence.
 * Usage: php dev/css-context.php <token> [width] */
$root = dirname(__DIR__);
$token = isset($argv[1]) ? $argv[1] : '';
$width = isset($argv[2]) ? (int) $argv[2] : 160;
$files = array_merge( glob( $root . '/قالب html/assets/css/*.css' ) ?: array(), glob( $root . '/assets/css/source/*.css' ) ?: array(), glob( $root . '/assets/css/*.css' ) ?: array() );
foreach ( $files as $f) {
	$css = file_get_contents( $f );
	$pos = 0;
	while ( ( $pos = strpos( $css, $token, $pos ) ) !== false ) {
		$from = max( 0, $pos - $width / 2 );
		printf( "%s @%d\n…%s…\n\n", basename( dirname( $f ) ), $pos, substr( $css, $from, $width ) );
		$pos += strlen( $token );
	}
}