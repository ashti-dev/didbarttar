<?php
/**
 * Dev tool: audit CSS classes used in PHP templates vs classes defined in built CSS.
 * Usage: php dev/css-class-audit.php   (read-only, prints compact report)
 */
$root = dirname(__DIR__);

$cssFiles = array_merge(
	glob($root . '/assets/css/*.css') ?: array(),
	glob($root . '/assets/css/source/*.css') ?: array()
);
$css = '';
foreach ($cssFiles as $f) { $css .= file_get_contents($f); }
preg_match_all('/\.([A-Za-z][A-Za-z0-9_-]*)/', $css, $m);
$known = array_fill_keys($m[1], true);
printf("CSS files: %d | defined classes: %d\n", count($cssFiles), count($known));

$it = new RegexIterator(
	new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root, FilesystemIterator::SKIP_DOTS)),
	'/\.php$/i'
);
$unknown = array(); $fileCount = 0;

$check = function ($token, $file) use (&$unknown, $known) {
	$token = trim($token);
	if ('' === $token) { return; }
	if (preg_match('/[\$<>()=]/', $token)) { return; } /* PHP remnants / dynamic */
	if (!preg_match('/^[A-Za-z][A-Za-z0-9_%-]*$/', $token)) { return; }
	/* dynamic prefix like torantejarat-art-%d -> check literal prefix against known family */
	if (false !== strpos($token, '%')) {
		$prefix = substr($token, 0, strpos($token, '%'));
		if ('' === $prefix) { return; }
		foreach ($known as $k => $_) {
			if (0 === strpos($k, $prefix)) { return; } /* family exists: ok */
		}
		$key = $prefix . '#';
	} else {
		$key = $token;
		if (isset($known[$key])) { return; }
	}
	if (!isset($unknown[$key])) { $unknown[$key] = array('n' => 0, 'files' => array()); }
	$unknown[$key]['n']++;
	$unknown[$key]['files'][basename($file)] = true;
};

foreach ($it as $f) {
	if (false !== strpos($f->getPathname(), DIRECTORY_SEPARATOR . 'dev' . DIRECTORY_SEPARATOR)) { continue; } /* skip dev tools */
	$code = file_get_contents($f->getPathname());
	$fileCount++;
	$tokens = array();

	/* class="..." attributes: drop inline <?php ... ?> code, keep literals */
	if (preg_match_all('/class\s*=\s*"([^"]*)"/', $code, $mm)) {
		foreach ($mm[1] as $attr) {
			$attr = preg_replace('/<\?php.*?\?>/s', ' ', $attr);
			foreach (preg_split('/\s+/', $attr) as $t) { $tokens[] = $t; }
		}
	}
	/* quoted strings referencing theme classes (helpers, arrays) */
	if (preg_match_all('/[\'"]([^\'"]*torantejarat-[A-Za-z0-9_%-]+[^\'"]*)[\'"]/', $code, $tm)) {
		foreach ($tm[1] as $s) { foreach (preg_split('/\s+/', $s) as $t) { $tokens[] = $t; } }
	}
	foreach ($tokens as $t) { $check($t, $f->getPathname()); }
}
uksort($unknown, 'strcmp');
printf("PHP files: %d | unknown classes: %d\n\n", $fileCount, count($unknown));
foreach ($unknown as $cls => $info) {
	printf("%s x%d [%s]\n", $cls, $info['n'], implode(',', array_keys($info['files'])));
}
