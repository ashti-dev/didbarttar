<?php
/* Dev: scan kit HTML pages for art-N variant usage inside editorial-art blocks.
 * Usage: php dev/kit-art-scan.php */
$root = dirname(__DIR__);
$counts = array();
foreach ( glob( $root . '/قالب html/*.html' ) ?: array() as $f ) {
	$html = file_get_contents( $f );
	if ( false === strpos( $html, 'editorial-art' ) ) { continue; }
	preg_match_all( '/editorial-art\s+([a-z0-9-]*art-\d+)/', $html, $m );
	foreach ( $m[1] as $c ) { $counts[ $c ] = ( $counts[ $c ] ?? 0 ) + 1; }
	printf( "%-22s editorial-art=%d  variants: %s\n", basename( $f ), substr_count( $html, 'editorial-art' ), implode( ',', $m[1] ) ?: '-' );
}
echo 'variant totals: ', json_encode( $counts ), "\n";