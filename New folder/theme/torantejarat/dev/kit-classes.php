<?php
/* Dev: extract class="..." attribute values from an HTML file (kit pages are minified single-line).
 * Usage: php dev/kit-classes.php index  */
$root = dirname(__DIR__);
$slug = isset($argv[1]) ? $argv[1] : 'index';
$path = $root . '/قالب html/' . $slug . '.html';
if (!is_file($path)) { fwrite(STDERR, "not found: $path\n"); exit(1); }
$html = file_get_contents($path);
preg_match_all('/class\s*=\s*"([^"]*)"/', $html, $m);
$classes = array();
foreach ($m[1] as $v) {
	foreach (preg_split('/\s+/', trim($v)) as $c) {
		if ('' !== $c) { $classes[$c] = ($classes[$c] ?? 0) + 1; }
	}
}
uksort($classes, 'strcmp');
foreach ($classes as $c => $n) { printf("%-52s x%d\n", $c, $n); }
echo 'total: ', count($classes), "\n";
