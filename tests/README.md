# Phase 6 — migration QA (current)

**Author / Designer:** یعقوب طیبی — https://yaghoubtayebi.ir/

```sh
node tests/migration-static.mjs
node tests/migration-navigation.mjs
node tests/migration-mutations.mjs
node --check theme/torantejarat/assets/js/navigation.js
```

These are source checks and DOM-double unit checks, **not** PHP syntax, browser, WordPress, WooCommerce or MySQL acceptance. Current results: 23/23 source, 13/13 navigation, JS syntax PASS; 13/13 final-iteration mutation regressions detected. PHP/runtime/browser remain NOT TESTED.

## Final core-migration iteration — 2026-09-22

**IMPLEMENTATION COMPLETE — RUNTIME QA BLOCKED**, not DONE. This closes independently implementable core migration, not runtime acceptance or pending feature/commerce contracts.

- **23/23** source, **13/13** navigation DOM-double checks, JS syntax PASS. `migration-mutations.mjs` reproduces **13/13** detections using temporary Theme copies: duplicate TOC/unsafe fragment, empty grid/lost pagination, cart wrapper order/totals removal/business writes, account authentication writes/current-state selector, wrong cart columns/duplicate CSS, and two focus regressions. It removes its scratch copies and never executes mutated PHP or changes production.
- M-S20–23 add Article/TOC, shared main-query/empty states, Cart source geometry/native ownership, and Account boundary checks. M-S16 follows the extracted `editorial-list.php` consumer rather than requiring the obsolete inline Search loop; its empty-state assertion remains. M-S19 checks all CSS structure, duplicate native properties and selected source values: **not** a full CSS parser, cascade/visual test or browser certification. M-S12 remains a branch-shape tripwire, **not PHP lint**.
- Fresh probe: PHP/PHP8.3, WP-CLI, MySQL/mysqld, Composer/PHPCS, Docker and Chromium/Chrome/Firefox unavailable. No WP loader/settings found in the bounded workspace probe, no PHP/MySQL/browser process. Node 22.22.3 and Python 3.11.2 available. **Runtime QA = BLOCKED / NOT AVAILABLE**; no provisioning/dependency installation.
- Prepared M-R26–28 add nested/blank/duplicate/malformed TOC probes and native Cart hook checks. Existing M-R24/25 remain. Fixed the prepared smoke's namespaced `WP_CLI` warning reference. All PHP smoke remains **unexecuted/unlinted**; probes do not imply acceptance.
- Runtime/browser queue: article sidebar anchors/scroll/focus at 320–1440 and admin bar; native multipage links, empty/protected/mixed query listings; Cart table/thumbnail/quantity/disabled controls/notices/totals/cross-sells and real updates; Account dashboard/orders/no-orders/pagination/details/logout/expired sessions/forbidden orders; plus the existing Home/Shop/Product/Checkout matrix below. No authentication or transaction is simulated by this runner.
- Historical Foundation runner remains **1/10** under its former assumptions; historical tests/fixtures/reports are unchanged. All 35 original references remain hash-identical. See the Theme README's final boundary matrix and `ACCEPTANCE.md` for F-AC-01…12.

## Test evolution, not deletion of evidence

`foundation-static.mjs`, `foundation-smoke.php`, original fixtures and Phase 5 report are unchanged. Their no-JS/no-Woo-override/no-query/exact-20-file assumptions belong to Foundation. The first migration iteration gave **2/10** with the unchanged historical runner; this iteration gives **1/10**, not GREEN: F-S01/03/04/05/08/10 assert the historical scope; F-S06 has a whitespace-sensitive menu assertion, and F-S07 expects escaping inline rather than in the migrated page-intro component. F-S02 still passes. F-S09 now also rejects the literal `preload` in the native video pattern even though it is `preload="none"`, not a remote asset or catalog preload; that Foundation-only assertion is preserved. Phase 6 checks preserve namespace/guard/escaping/native-hooks/no-writes/no-dependency/reference guarantees while permitting the newly authorized real consumers. No historical assertion was removed to manufacture PASS.

The first migration run was 10/11: M-S03 incorrectly expected the home URL inside header.php rather than its brand component. The checker was corrected to assert the caller and escaped URL in that component; no unsafe link was accepted.

## Runtime commands — prepared, NOT executed

On isolated local/development WordPress + Woo + **MySQL** (same proposed version floor as the historical fixture):

```sh
find theme/torantejarat tests -name '*.php' -print0 | xargs -0 -n1 php -l
wp --path=/path/to/isolated-wp eval-file /path/to/repo/tests/migration-smoke.php woo-present
# On a second isolated fixture without Woo:
wp --path=/path/to/isolated-wp eval-file /path/to/repo/tests/migration-smoke.php woo-absent
```

Use the synthetic page/post fixture named in the historical instructions below. Also prepare a public product and native Shop/Cart/My Account pages, plus the site menus and static homepage. The smoke runner creates no content, writes no settings, submits no form and creates no order. It checks identity, hooks, assets and read-only HTTP routes; it is not a transaction test. Product fixture must be publicly accessible, not password-protected. Missing fixtures fail, rather than pretending there is coverage.

### Required browser/commerce matrix (all NOT TESTED here)

- Source-vs-WP screenshots: 1440, 1100, 900, 600, 390, 320; RTL and LTR resilience; local font pending; no JS and keyboard menus; dropdowns, escape, focus return, zoom/reflow, skip link, alt/heading/landmark checks.
- WP: empty and populated home/blog/search/archive; pagination; no image/excerpt/menu; protected article/page; long Persian text; authored blocks and classic content; explicit anchored TOC.
- Woo: native Shop/category/tag, ordering/pagination, hidden/private products, no image, sale/variable/grouped/external products, stock/backorders, unavailable variations, quantity validation, gallery/lightbox, tabs/attributes/related/upsells.
- Cart/Checkout (eventual approved mode): empty/cart changes/removal/restoration, coupon/shipping/tax totals, validation, stale stock, cached badge and Blocks quantity synchronization. Do not force Blocks or shortcode while ND-R10 is open.
- Account: guest login, authenticated dashboard/history, expired session, order authorization. Order-received/order-pay/statuses remain Woo-owned; test pending/failed/cancelled/paid and invalid keys without accepting client query strings as payment authority.
- Payment sandbox/provider qualification belongs to Payment phase; no real charge and no Theme PSP logic. Missing gateway is a launch blocker, not a general migration gate.

See `wp-theme-agent-kit/PHASE-6-MIGRATION-REPORT.md` for template scope, remaining decisions and provenance. Compatibility Tested = No.

---

## Historical Phase 5 instructions — retained evidence, not the current runner

# Foundation QA — torantejarat

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

No test framework or package installation is required by these scripts. Source checks are not WordPress execution or compatibility evidence. Do not run a destructive setup on production.

## Checks available in this workspace

```sh
node --check tests/foundation-static.mjs
node tests/foundation-static.mjs
node --check assets/js/app.js
node --check assets/js/core.js
node --check assets/js/catalog.js
git diff --check
```

The three legacy JS files are syntax-checked only, not imported/enqueued by the Theme or changed. The static runner has ten named `F-S*` source checks. Any failed check yields exit 1. `foundation-baseline.json` captures all 62 pre-Phase-5 file hashes, HEAD and dirty status; source checks preserve all files outside the explicit documentation allowlist. It is a baseline record, not a new application dependency.

A second positional path can target a temporary copy of the Theme to verify failure detection, without changing source or tests:

```sh
node tests/foundation-static.mjs /absolute/path/to/temporary/theme-copy
```

## Real PHP / WordPress fixture — NOT RUN here

Required externally provisioned test environment: isolated local/development WordPress, PHP CLI, MySQL (not MariaDB), and WooCommerce for the active-Woo run. Use the proposed floor versions and a separately recorded updated combination; never infer broad future-version compatibility. WP-CLI is one possible external fixture tool, not a new Theme or installed project dependency. Record exact versions, locale, browser and test date. Set WP_DEBUG and log diagnostics in the fixture, not in Theme code.

PHP syntax gate after PHP is available, with failure propagation using Python’s standard library (a missing PHP executable also fails):

```sh
python3 - <<'PY'
from pathlib import Path
import subprocess
for folder in ('theme/torantejarat', 'tests'):
    for file in sorted(Path(folder).rglob('*.php')):
        subprocess.run(['php', '-l', str(file)], check=True)
PY
```

Install/activate the Theme only on the isolated fixture when provisioned. Use WordPress native tools to create the following **test** page/post from `tests/fixtures/content.html` (not real content or product migration):

- Published page slug `torantejarat-foundation-page`.
- Published post slug `torantejarat-foundation-post`, assigned a normal category.
- Use a long Persian/Latin title. Also test empty content separately. Configure a native primary navigation with keyboard-accessible nested links.
- Woo-present fixture: use Woo's own empty shop page. No product/catalog import, gateway, credentials, checkout customization or customer data is needed.
- Keep the Gutenberg plugin absent; test both block content and classic HTML via the native content renderer.
- Use `WP_ENVIRONMENT_TYPE=local` or `development`, WordPress debug logging and a reachable fixture URL. The runner refuses production/staging and never activates plugins/themes, writes data, or creates fixtures itself.

Run from the fixture using the absolute path to this repository's runner:

```sh
wp eval-file /absolute/path/to/tests/foundation-smoke.php woo-absent
wp eval-file /absolute/path/to/tests/foundation-smoke.php woo-present
```

Each command must match the fixture's actual Woo activation state; use isolated snapshots or native fixture administration between runs. Exit 2 means prerequisites are missing, exit 1 means a failed assertion, exit 0 means only this smoke subset passed. It does **not** mark all F-AC criteria PASS.

The runner records actual PHP/WP/Woo/DB/locale versions; checks Classic detection, metadata, native supports, Woo wrappers, duplicate enqueue, asset HTTP responses, page/post/archive/home/404 content and landmarks. It reads fixture data and makes HTTP GET requests to the fixture only. No request is made to a PSP. Test static-front-page and posts-front-page configurations separately using WordPress Reading settings.

## Manual/browser checks still required

- F-AC-01/02: install/activation, WP_DEBUG log, Woo present/absent, no Gutenberg plugin, no forbidden dependencies.
- F-AC-03/05: all routes, content filters, asset status/duplicate requests, console errors, editor CSS isolation; verify release cache key changes with the header version on a disposable copy in `local` environment, and mtime only in `development`.
- F-AC-04: verify actual registered handles/text domain and collision-free activation on the fixture; source declarations alone are insufficient.
- F-AC-06: missing-Woo administrator warning only for the proper capability; no fatal to visitors. Inspect Theme code separately from native Woo writes/requests.
- F-AC-07: fa-IR/RTL and Latin fixtures at 320/768/1280; no unintended horizontal shell overflow, keyboard navigation including nested menus and skip link, visible focus, readable zoom and landmarks. No final UI/complete accessibility certification claimed.
- F-AC-08: malicious title/attribute/URL fixtures must not execute; validate escaping in the actual output. Keep ordinary permitted content HTML rendered via `the_content()`, not blanket-escaped. Never use production PII/secrets in fixtures.
- F-AC-09/12: no new option/meta/settings registrations, data framework or feature code; inspect diff against the saved baseline and preserve historical reference files.
- F-AC-10: PHP lint + JS syntax + named smoke assertions must all succeed in the real fixture; prove smoke's nonzero exit with missing/wrong fixtures. Not tested here.
- F-AC-11: capture browser network inventory, distinguish WP/Woo assets from Theme assets, check no catalog/third-party fetch by Theme. Do not claim final-store CWV/performance PASS.

The acceptance statuses and available evidence are in [the Foundation report](../wp-theme-agent-kit/PHASE-5-FOUNDATION-REPORT.md).
