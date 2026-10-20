import assert from 'node:assert/strict';
import { createHash } from 'node:crypto';
import { existsSync, readFileSync, readdirSync } from 'node:fs';
import { dirname, join, relative, resolve } from 'node:path';
import { fileURLToPath } from 'node:url';

// Source checks only: this runner never executes PHP or claims WP compatibility.
const root = resolve(dirname(fileURLToPath(import.meta.url)), '..');
const theme = resolve(process.argv[2] || join(root, 'theme/torantejarat'));
const read = (name) => readFileSync(join(theme, name), 'utf8');
const walk = (dir) => existsSync(dir) ? readdirSync(dir, { withFileTypes: true }).flatMap(
  (entry) => entry.name === '.git' ? [] : entry.isDirectory() ? walk(join(dir, entry.name)) : [join(dir, entry.name)],
) : [];
const files = walk(theme);
const phpFiles = files.filter((file) => file.endsWith('.php'));
const source = phpFiles.map((file) => readFileSync(file, 'utf8')).join('\n');
const checks = [];
function check(name, run) {
  try { run(); console.log(`PASS ${name}`); checks.push(true); }
  catch (error) { console.error(`FAIL ${name}: ${error.message}`); checks.push(false); }
}

check('F-S01 classic-files-and-metadata', () => {
  for (const name of ['style.css', 'functions.php', 'index.php', 'header.php', 'footer.php',
    'page.php', 'single.php', 'archive.php', '404.php', 'inc/bootstrap.php',
    'inc/setup.php', 'inc/assets.php', 'inc/woocommerce.php',
    'template-parts/content.php', 'template-parts/content-none.php']) assert.ok(existsSync(join(theme, name)), name);
  const header = read('style.css');
  for (const line of ['Theme Name: توران تجارت', 'Author: یعقوب طیبی', 'Designer: یعقوب طیبی',
    'Author URI: https://yaghoubtayebi.ir/', 'Version: 0.1.0', 'Text Domain: torantejarat',
    'Requires at least: 6.9', 'Requires PHP: 8.3', 'WC requires at least: 10.8']) assert.ok(header.includes(line), line);
  assert.ok(!/Tested up to:/i.test(header), 'No untested compatibility claim');
  for (const path of ['templates', 'parts', 'woocommerce', 'node_modules', 'vendor']) assert.ok(!existsSync(join(theme, path)), path);
});
check('F-S02-namespace-prefix-and-direct-access', () => {
  assert.ok(phpFiles.length > 0);
  const names = [];
  for (const file of phpFiles) {
    const code = readFileSync(file, 'utf8');
    assert.match(code, /namespace ToranTejarat\\Theme;/, file);
    assert.match(code, /if \( ! defined\( 'ABSPATH' \) \) \{\s*exit;/, file);
    for (const [, name] of code.matchAll(/function\s+(\w+)\s*\(/g)) {
      assert.ok(name.startsWith('torantejarat_'), name); names.push(name);
    }
  }
  assert.equal(new Set(names).size, names.length, 'Unique declared functions');
  assert.ok(!/\b(?:define|class)\s*\(/.test(source), 'No unnecessary globals/classes');
  const translations = [...source.matchAll(/\b(?:__|_e|esc_html__|esc_html_e|esc_attr__|esc_attr_e)\(\s*'[^']*'\s*,\s*'([^']+)'/g)];
  assert.ok(translations.length > 0);
  for (const [, domain] of translations) assert.equal(domain, 'torantejarat');
});
check('F-S03-native-rendering-and-hooks', () => {
  const header = read('header.php'); const footer = read('footer.php');
  for (const hook of ['language_attributes();', 'wp_head();', 'wp_body_open();']) assert.ok(header.includes(hook), hook);
  assert.ok(footer.includes('wp_footer();'));
  assert.ok(read('template-parts/content.php').includes('the_content();'));
  assert.ok(read('inc/setup.php').includes("add_theme_support( 'title-tag' )"));
  for (const file of ['index.php', 'page.php', 'single.php', 'archive.php', '404.php']) {
    const code = read(file);
    for (const text of ['get_header();', 'get_footer();', 'id="torantejarat-main"']) assert.ok(code.includes(text), `${file}: ${text}`);
  }
  assert.ok(!/\b(?:wp_enqueue_script|register_block_type)\s*\(|gutenberg_|acf_|elementor/i.test(source));
});
check('F-S04-assets-and-version-source', () => {
  const assets = read('inc/assets.php');
  for (const text of ['get_template_directory_uri()', 'get_template_directory()', 'wp_get_theme( get_template() )', "get( 'Version' )", "'development' === wp_get_environment_type()", 'filemtime(', 'wp_enqueue_style(']) assert.ok(assets.includes(text), text);
  for (const name of ['base', 'layout', 'components']) {
    assert.ok(assets.includes(`'torantejarat-${name}'`)); assert.ok(existsSync(join(theme, `assets/css/${name}.css`)));
  }
  const handles = [...assets.matchAll(/^\s*'(torantejarat-[a-z-]+)'\s*=>/gm)].map((m) => m[1]);
  assert.deepEqual(handles, ['torantejarat-base', 'torantejarat-layout', 'torantejarat-components']);
  assert.equal((assets.match(/wp_enqueue_style\(/g) || []).length, 1, 'One enqueue loop');
  assert.ok(assets.includes("array( 'torantejarat-base' )"));
  assert.ok(assets.includes("array( 'torantejarat-layout' )"));
  assert.ok(!assets.includes('editor.css'), 'Editor CSS not front-end enqueued');
  assert.ok(read('inc/setup.php').includes("add_editor_style( 'assets/css/editor.css' )"));
  assert.ok(!files.some((file) => file.endsWith('.js')), 'No unused JS file');
});
check('F-S05-woocommerce-boundary-source', () => {
  const woo = read('inc/woocommerce.php');
  for (const text of ["class_exists( 'WooCommerce', false )", "current_user_can( 'activate_plugins' )",
    "add_theme_support( 'woocommerce' )", "'woocommerce_before_main_content'", "'woocommerce_after_main_content'",
    "'woocommerce_output_content_wrapper'", "'woocommerce_output_content_wrapper_end'", 'id="torantejarat-main"']) assert.ok(woo.includes(text), text);
  assert.ok(!/\b(?:WC|wc_get_product|wc_create_order|woocommerce_content)\s*\(/.test(source));
});
check('F-S06-rtl-focus-and-semantic-shell-source', () => {
  const css = ['base', 'layout', 'components'].map((name) => read(`assets/css/${name}.css`)).join('\n');
  for (const text of ['.torantejarat-site', ':focus-visible', 'overflow-wrap', 'max-inline-size', 'padding-inline', 'inset-inline-start']) assert.ok(css.includes(text), text);
  assert.ok(!/\b(?:margin|padding|border)-(?:left|right)\s*:|\b(?:left|right)\s*:/.test(css), 'Logical properties');
  assert.match(read('header.php'), /href="#torantejarat-main"/);
  assert.ok(read('header.php').includes('has_nav_menu('));
  assert.ok(read('header.php').includes("'fallback_cb'    => false"));
  for (const file of ['index.php', 'archive.php', '404.php']) assert.ok(read(file).includes('<h1'));
  for (const file of files.filter((path) => path.endsWith('.css'))) {
    const text = readFileSync(file, 'utf8').replace(/\/\*[\s\S]*?\*\//g, '');
    assert.equal((text.match(/\{/g) || []).length, (text.match(/\}/g) || []).length, file);
    for (const [, name] of text.matchAll(/\.([a-z][\w-]*)/g)) assert.ok(name.startsWith('torantejarat-') || name === 'screen-reader-text', name);
  }
});
check('F-S07-escaping-and-no-input-handlers-source', () => {
  for (const name of ['esc_html(', 'esc_url(', 'esc_attr(']) assert.ok(source.includes(name), name);
  assert.ok(read('archive.php').includes('esc_html( wp_strip_all_tags( get_the_archive_title() ) )'));
  assert.ok(!/\$_(?:GET|POST|REQUEST|COOKIE|SESSION)|\b(?:eval|unserialize|file_put_contents|setcookie)\s*\(/.test(source));
  assert.ok(!/<form\b|wp_ajax_|admin_post_|register_rest_route/.test(source));
});
check('F-S08-no-data-or-commerce-writes', () => {
  assert.ok(!/\b(?:register_setting|register_(?:post_)?meta|add_(?:options|menu|submenu)_page|(?:add|update|delete)_(?:option|post_meta|user_meta)|wp_insert_post|dbDelta|wp_remote_\w+|curl_\w+)\s*\(/.test(source));
  assert.ok(!/\$wpdb|\bWP_Query\b|new\s+WC_|payment_complete|process_payment|register_post_type|register_taxonomy/.test(source));
  assert.ok(!existsSync(join(theme, 'inc/settings.php')), 'No unused settings stub');
});
check('F-S09-no-third-party-or-catalog-assets', () => {
  const executable = files.filter((name) => /\.(php|css|js)$/.test(name)).map((file) => readFileSync(file, 'utf8')).join('\n');
  assert.ok(!/https?:\/\//.test(executable.replace('https://yaghoubtayebi.ir/', '')), 'No remote asset URL');
  assert.ok(!/preload|prefetch|<script\b|localStorage|sessionStorage|catalog\.js/.test(executable));
  const css = files.filter((file) => file.endsWith('.css'));
  assert.ok(!/@import|url\s*\(/.test(css.map((file) => readFileSync(file, 'utf8')).join('\n')));
  console.log(`INVENTORY ${css.map((file) => `${relative(theme, file)}=${readFileSync(file).length}B`).join(', ')}`);
});
check('F-S10-legacy-and-scope-preservation', () => {
  const baseline = JSON.parse(readFileSync(join(root, 'tests/fixtures/foundation-baseline.json'), 'utf8'));
  const allowedDocs = new Set(['README.md', ...['ARCHITECTURE-CONTRACT', 'FINAL-DECISION-SHEET', 'ADR', 'AGENTS',
    'CONSTRAINTS', 'DEVELOPMENT-PROTOCOL', 'ACCEPTANCE', 'ADMIN', 'CONTENT-MODEL', 'SETTINGS', 'PRODUCT', 'QA'].map((name) => `wp-theme-agent-kit/${name}.md`)]);
  for (const [name, hash] of Object.entries(baseline.files)) {
    assert.ok(existsSync(join(root, name)), name);
    if (allowedDocs.has(name)) continue;
    assert.equal(createHash('sha256').update(readFileSync(join(root, name))).digest('hex'), hash, name);
  }
  const expected = new Set(['README.md', 'style.css', 'functions.php', 'index.php', 'header.php', 'footer.php',
    'page.php', 'single.php', 'archive.php', '404.php', 'inc/bootstrap.php', 'inc/setup.php', 'inc/assets.php',
    'inc/woocommerce.php', 'template-parts/content.php', 'template-parts/content-none.php',
    ...['base', 'layout', 'components', 'editor'].map((name) => `assets/css/${name}.css`)]);
  for (const file of files) assert.ok(expected.has(relative(theme, file)), relative(theme, file));
  const additions = new Set([
    ...[...expected].map((name) => `theme/torantejarat/${name}`),
    'tests/README.md', 'tests/foundation-static.mjs', 'tests/foundation-smoke.php',
    'tests/fixtures/content.html', 'tests/fixtures/foundation-baseline.json',
    'wp-theme-agent-kit/PHASE-5-FOUNDATION-REPORT.md',
  ]);
  for (const file of walk(root)) {
    const name = relative(root, file);
    assert.ok(Object.hasOwn(baseline.files, name) || additions.has(name), `Outside scope: ${name}`);
  }
});

console.log(`SOURCE CHECKS: ${checks.filter(Boolean).length}/${checks.length}; not runtime/compatibility acceptance.`);
process.exitCode = checks.every(Boolean) ? 0 : 1;
