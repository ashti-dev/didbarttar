<?php
/* Dev: list defined CSS classes filtered by stems. php dev/css-class-stems.php footer,faq,... */
$root = dirname(__DIR__);
$stems = isset($argv[1]) ? array_filter(array_map('trim', explode(',', $argv[1]))) : array();
$cssFiles = array_merge(glob($root.'/assets/css/*.css') ?: array(), glob($root.'/assets/css/source/*.css') ?: array());
$css = '';
foreach ($cssFiles as $f) { $css .= file_get_contents($f); }
preg_match_all('/\.([A-Za-z][A-Za-z0-9_-]*)/', $css, $m);
$known = array_unique($m[1]);
sort($known);
$hits = array();
foreach ($known as $c) {
	if (!$stems) { $hits[] = $c; continue; }
	foreach ($stems as $s) { if (false !== stripos($c, $s)) { $hits[] = $c; break; } }
}
echo implode("\n", $hits), "\n";
echo 'total: ', count($hits), ' / ', count($known), "\n";
