<?php
/* Dev: check whether tokens exist in kit CSS (source) or built CSS.
 * Usage: php dev/css-token-check.php token1 token2 ... */
$root = dirname(__DIR__);
$css = '';
foreach (array_merge(glob($root . '/assets/css/source/*.css') ?: array(), glob($root . '/assets/css/*.css') ?: array()) as $f) { $css .= file_get_contents($f); }
foreach (array_slice($argv, 1) as $t) {
	printf("%-24s %s\n", $t, ( false !== strpos( $css, $t ) ? 'FOUND' : 'MISSING' ) );
}