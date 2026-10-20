<?php
/* Dev: list defined CSS classes (built + source) matching a keyword.
 * Usage: php dev/css-classes-list.php <keyword> */
$root = dirname(__DIR__);
$kw = isset($argv[1]) ? $argv[1] : '';
$css = '';
foreach (array_merge(glob($root . '/assets/css/*.css') ?: array(), glob($root . '/assets/css/source/*.css') ?: array()) as $f) { $css .= file_get_contents($f); }
preg_match_all('/\.([A-Za-z][A-Za-z0-9_-]*)/', $css, $m);
$u = array_values(array_unique($m[1]));
sort($u);
if ('' !== $kw) { $u = preg_grep('/' . preg_quote($kw, '/') . '/i', $u) ?: array(); }
foreach ($u as $c) { echo $c, "\n"; }
echo 'total: ', count($u), "\n";