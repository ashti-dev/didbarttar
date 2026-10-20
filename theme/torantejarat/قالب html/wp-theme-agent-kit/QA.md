# Phase 7 — Runtime QA Preparation

**Date:** 2026-09-22 · **Preparation only; execution has NOT started.**

**Runtime status: RUNTIME QA BLOCKED — BLOCKED / NOT AVAILABLE.** No WordPress activation, Woo rendering, Cart/Checkout/account interaction, browser or database test has run in this phase. No fixture, environment or dependency has been created/installed.

## 1. Immutable baseline and scope

- Runtime QA reference commit: **`4640054480f7ccf5a29a3ba38f703534fe2bef9e`**.
- Working branch: **`arena/01a0c314-didbarttar`**. Preparation began at that HEAD with a clean worktree.
- Phase 6 remains **FROZEN — IMPLEMENTATION COMPLETE — RUNTIME QA BLOCKED**, not DONE or READY FOR PRODUCTION. [Exit Record](PHASE-6-MIGRATION-REPORT.md) remains unchanged.
- Frozen Theme aggregate SHA-256: `b63d1219b2ab658c442b09108ca77117fb187b6c8fbe4554637b19196ec80088`; algorithm and complete inventory are in the Exit Record.
- This phase permits QA documentation/preparation only. No Theme/PHP/template/CSS/JS/asset/test-runner changes, refactor, migration, schema or contract implementation. A discovered code defect is recorded with reproduction/evidence; its fix requires separate authorization, not an automatic Phase-6 reopening.
- No environment installation, fixture creation or actual test execution is authorized by this plan. The steps below are **future execution instructions**, gated by prerequisites and subsequent authorization.

## 2. Read-only environment inspection

| Inspected dependency | Observation in this workspace | Consequence |
|---|---|---|
| PHP CLI/web runtime | `php`, `php8.3`, `php-fpm` not in PATH; no matching PHP process observed | PHP lint/activation/server execution BLOCKED |
| WordPress | No `wp-load.php` or `wp-settings.php` found under `/home/user` (max depth 6); no usable WP URL supplied | Activation/hierarchy/content tests BLOCKED |
| WooCommerce | No `wp-content/plugins/woocommerce/woocommerce.php` found in the same bounded search | Product/cart/checkout/account tests BLOCKED |
| MySQL | `mysql`/`mysqld` not in PATH; no MySQL process observed; no QA DB connection supplied | Database observations and writes audit BLOCKED |
| Browser | Chromium/Chrome/Firefox commands absent; no executable candidate in bounded `/usr/bin`, `/usr/local/bin`, `/opt`, `/home/user` search (max depth 5), no matching process observed | Actual rendering/keyboard/network tests BLOCKED |
| QA CLI | `wp` not in PATH | Existing WP-CLI smoke workflow BLOCKED |
| Fixtures | Only repository source fixtures/baseline files exist; no populated runtime fixture discovered | Source fixture text is not runtime data or evidence |

These are bounded availability observations, not a claim about every possible remote host. No environment was provisioned or connection credentials searched/requested. Fetching upstream source, a static HTML preview or a DOM double cannot substitute for this missing runtime.

### Runtime QA Prerequisites

| ID | Required dependency/access for future execution | Approval boundary |
|---|---|---|
| E0 | Frozen baseline checkout/deployed Theme hash match; isolated local/development QA site and evidence storage; explicit authorization to execute/prepare fixtures | No deployment to production; no Theme editing |
| E1 | PHP CLI **and** PHP-capable HTTP server with WP/Woo-required extensions, real WordPress, accessible debug/error logs | Record CLI and web PHP versions separately; no install now |
| E2 | Real WooCommerce, native assigned Shop/Cart/Checkout/My Account pages; recorded existing page mode and configuration | Do not choose Blocks/Classic, registration/guest policy, currency/shipping/tax rules or a gateway on the Owner's behalf |
| E3 | Real **MySQL**, read access to version/schema/state observations and authorized native fixture administration | MySQL family only is approved; not MariaDB. No hand-written schema or direct fixture SQL writes |
| E4 | Real browser/devtools, independent admin/customer/anonymous contexts, viewport/keyboard/network/console capture | Observed browser/version is not an approved compatibility matrix |
| E5 | Minimal synthetic fixture manifest from §4; per-case restore points; access to native WP/Woo administration | Test-only data, no real content/customer data; no Theme seeding |
| E6 | WP-CLI **if** using the supplied `wp eval-file` runner; otherwise an explicitly documented real-runtime procedure | QA tooling only, not a Theme dependency; do not install it now |

Synthetic product price/stock variants below are QA inputs only, not approval of merchant stock/backorder/currency policy. Numeric platform/browser proposals remain **PROPOSED**, not Approved. The existing smoke runner encodes proposed floors (PHP 8.3, WP 6.9, Woo 10.8, MySQL 8.4); a runner assertion is neither Owner approval nor broad compatibility evidence. Record a mismatch; do not silently rewrite the frozen runner or platform policy. A later environment-provisioning instruction must explicitly authorize any installation.

## 3. Result rules and evidence contract

**Current result for F-AC-01…12: BLOCKED / NOT AVAILABLE; executed = No.** Source-era PASS for F-AC-09/12 and source/DOM/mutation counts remain separate historical evidence in [ACCEPTANCE](ACCEPTANCE.md). They are not runtime results.

Apply these rules to each case, then aggregate to its criterion:

- **PASS:** case actually executed against the recorded real fixture; every expected assertion met and linked evidence complete. The run must establish actual WP, Woo, browser and MySQL availability; relevant layers must support the individual assertion. CLI success alone cannot pass a browser/commerce case.
- **FAIL:** a valid executed case violates an expected result (e.g. fatal, incorrect total, unauthorized data exposure). Keep the failure even if other cases are blocked. Record reproducible steps, actual output, baseline and evidence; no code fix in preparation.
- **BLOCKED:** execution/verification cannot proceed because a prerequisite, fixture, approval, policy/mode/provider decision or required evidence access is missing. Record the precise blocker; missing prerequisites are not application failures.
- **NOT TESTED:** eligible case has not been executed, or an explicitly conditional case is not applicable to the available fixture (e.g. variable product absent). Give the reason; never turn a skip into PASS.
- **Criterion aggregation:** any demonstrated failure ⇒ FAIL; otherwise any required blocked case ⇒ BLOCKED; otherwise any required unexecuted/unverified case ⇒ NOT TESTED; PASS only when every required case passes. Conditional exclusions must be explicit, justified and excluded from any claim of coverage. No successful child case masks a missing critical flow.

Every future run record must contain: run/case IDs; F-AC mapping; baseline commit + deployed Theme digest; date; actual PHP/WP/Woo/MySQL/browser versions; locale/viewport; observed Cart/Checkout mode and applicable approval references; fixture manifest revision/IDs; reproduction steps; expected vs actual; status/blocker; redacted evidence paths. Record DB storage mode (including Woo order storage) rather than assuming orders live in `wp_posts`.

Acceptable evidence: WP/PHP diagnostic logs, actual HTTP/DOM output, screenshots plus keyboard observation/video, browser network/HAR and console, native hook/asset registry output, and scoped MySQL before/after observations with timestamps. A screenshot alone does not prove authorization, nonce checks, database behavior or successful commerce. Keep raw cookies/nonces/passwords/order keys/tokens and personal data out of Git/chat; evidence shared for review must be redacted. No evidence bundle is created by this preparation.

## 4. Minimal Test Fixture Plan — NOT CREATED

Fixture IDs below are **plan identifiers**, not database IDs. Create/reuse only after explicit authorization, using native WP/Woo UI/APIs on the isolated fixture. Record assigned real IDs privately in the manifest. Reset between variants; do not create a separate permanent record for every state. Do not import the legacy mock catalog or production content.

| Fixture | Minimum data / state needed | Consumed by; why needed |
|---|---|---|
| U-ADMIN | One dedicated synthetic WP administrator, solely for activation/native fixture setup. Credentials remain in the QA environment | T01/T08; activation and capability-positive control |
| U-A / U-B | Two synthetic WP users with native Woo customer role; their `WC_Customer` identities are the same WP users, **not a second user store**. A owns O-A; B initially has no orders. Synthetic names, reserved-domain email, only native required fields | T06/T08/T09; login/details/history, empty history and cross-customer order denial. Reuse B as the non-admin capability control; no extra editor/subscriber is required |
| U-GUEST | Separate anonymous browser context, not another user record | Guest menus/login/checkout-policy observation and private endpoint denial |
| D-PAGE | One published native Page with the exact runner slug `torantejarat-foundation-page`; use existing `tests/fixtures/content.html` markers `TORANTEJARAT_BLOCK_FIXTURE` and `TORANTEJARAT_CLASSIC_FIXTURE`. Reuse via recorded, reversible states: generic Page; static Home; blank Home video/patterns; protected/empty content; About/Academy/Request template assignments one at a time | T02/T03/T07; native content pipeline, templates, absent Home video and no guessed business copy. Assigning test content is not real content migration |
| D-POST / D-POST-2 | One published Post with slug `torantejarat-foundation-post`, same marker fixture, long Persian/Latin title, nested authored heading anchors, entity text, one duplicate/blank anchor, `<!--nextpage-->` and native excerpt/image. **One** short second Post only for archive pagination; set fixture posts-per-page to 1 temporarily and restore it | T02/T07; article/TOC and actual multi-page archives. Reuse protected/empty-title/excerpt/no-image states serially rather than creating extra Posts |
| D-CATS | One native WP blog category assigned to the two Posts, and one native Woo product category for P-A/P-B; reuse suitable fixture terms. They are existing core taxonomy types, not new registrations. Temporarily unpublish both Posts for the existing category's empty archive, then restore | T02/T03; real category routes and 200 empty archive without fake cards |
| D-MENUS | One native primary menu containing existing fixture links with a parent/child item. Reuse existing fixture Page/Post links in the optional Home-actions/services/Academy/Footer locations as required; no business URLs or new content records just to populate menus | T02/T07; dropdown/focus, empty/unassigned menus and source content curation |
| D-MEDIA | Two small synthetic/local licensed images with recorded attachment IDs and meaningful alt: one featured product image + one additional gallery image. Reuse in Page/Post. Temporarily unset attachments for missing-image cases; restore afterward | T03/T07; real thumbnail/gallery/zoom and missing-image behavior. No Home video upload is needed to test **missing** video |
| P-A | One published catalog-visible **simple product** in D-CATS. Illustrative QA-only regular price `100`, active unscheduled sale price `80`, native managed stock `3` and backorders disabled **for this synthetic stock-boundary fixture only**; use the fixture's existing currency/tax/shipping configuration without choosing production policy. D-MEDIA images; one merchant-entered **local native Woo attribute**, e.g. `QA characteristic: value A`, visible. Can be featured for populated Home | T03/T04/T05; actual Woo price/stock/attribute APIs. Values are synthetic test inputs, not migrated catalog data or a new meta/schema |
| P-A variants | Serial restore-point variants of **the same** product: sale cleared → regular price; stock `0` → out-of-stock; price emptied → missing price; images removed; attribute hidden/removed → missing attribute. Restore before the next case. Currency/tax-inclusive formatting expected from actual native settings; do not hard-code displayed totals | T03/T04/T07; covers requested sale/OOS/missing states with no redundant product records. Attempting quantity 4 against recorded stock 3 supplies stock validation input |
| P-B | One second published simple in-stock product, QA-only price `50`, linked from P-A through Woo's **native cross-sells** association | T04; native cross-sell and two-line cart selection/quantity/removal. Necessary second record; no Theme recommendation rules |
| P-V (conditional) | **Only if variable-product coverage is applicable to the provided fixture:** one native variable product with two native variation states: option A priced `120`, managed stock `2`; option B priced `140`, stock `0`, backorders disabled for this synthetic fixture. Use native attributes/price/stock. Reuse an existing synthetic variable product if supplied; do not introduce a custom taxonomy or product type | T03; variation selection/unavailable state. If absent/not authorized, report NOT TESTED with reason, not PASS; do not create unrelated grouped/external types |
| W-PAGES | Native assigned Woo Shop, Cart, Checkout, My Account Pages and their core endpoints; record IDs/permalinks and actual stored shortcode/block mode | T03…T06. No guessed slugs, fake checkout, Theme page seeding or parallel endpoint |
| S-SEARCH / S-404 | A unique search query verified to match no fixture data; valid existing empty category state above; one confirmed nonexistent route (and verify the smoke runner's large post ID is absent) | T02/T07; no extra Post/category is needed just for no-results or 404 |
| C0 / C1 / C2 | Browser session states, not custom DB models: empty cart; P-A quantity 1 then 2; P-A + P-B then native removal back to empty. Also test quantity exceeding available stock, missing-price/OOS refusal and a native invalid quantity input where the control permits | T04; real Woo session persistence, validation, totals, badge and empty recovery. No localStorage/cart fixture store |
| K0 / K1 / K2 | Checkout with empty cart; same-session populated cart; invalid required input using **the fields actually rendered** by Woo, with correction afterward. Guest/registered behavior follows recorded fixture policy, not an assumed registration choice | T05/T08; redirect vs real form, native validation. A 302 for K0 is not coverage of K1/K2 or placement. Submission/payment-dependent cases require their own approvals/configuration |
| O-A | One synthetic Woo order owned by U-A, native line items from P-A/P-B and required synthetic addresses only. Observe existing order-received/view-order/history UI. Use native administration on reversible snapshots for pending/failed/cancelled/processing/completed display cases if needed, rather than extra orders | T06/T08; genuine Woo order record and ownership controls. Manually selected statuses are **UI fixtures, never proof of payment or order-creation success** |
| A0…A4 | Reuse users/order: anonymous login view; wrong then correct login; A dashboard/details/history/O-A; B empty history and denial for A's order; logout and expired-session access | T06/T08. No separate mock account, fabricated orders list or authentication handler |

No coupon, special shipping method, PSP, payment credentials, real content migration, form submission handler, Compare/Quiz data, font package or SEO fixture is required/created by this preparation. Cases needing those decisions remain contract-blocked. Likewise, no extra order set is planned merely for order pagination: if the supplied fixture lacks multiple pages, record that subcase NOT TESTED; archive pagination is covered by D-POST-2. Variation coverage is explicitly conditional, not a blanket exclusion.

## 5. Executable Runtime QA Matrix — F-AC-01…12

All rows below are **planned**, with current result **BLOCKED / NOT AVAILABLE — NOT RUN**. PASS/FAIL/BLOCKED/NOT TESTED use §3; each row adds its specific success/failure oracle. The common baseline/evidence manifest is mandatory for every row.

| Criterion | What to execute (future) | Prerequisites | Fixture/data | Expected result / PASS oracle | Acceptable evidence | FAIL / BLOCKED / NOT TESTED determination |
|---|---|---|---|---|---|---|
| F-AC-01 | PHP lint all frozen Theme/test PHP; activate unchanged Theme in real WP; load frontend/admin; repeat with Woo present and absent using separate restored fixture snapshots | E0/E1/E3/E4; E2 for Woo-present | U-ADMIN, D-PAGE, W-PAGES | Activation succeeds; no Theme fatal, parse error, warning, notice or deprecation; actual runtime/DB identified | CLI lint exit/output, activation screen/HTTP, timestamped PHP/WP logs, MySQL version | Executed fatal/diagnostic ⇒ FAIL. Missing PHP/WP/admin/logs/DB ⇒ BLOCKED. A not-run eligible variant ⇒ NOT TESTED |
| F-AC-02 | Inspect active Theme type/supports and plugin list; view content with Gutenberg plugin absent; test Core block + classic content and native editor/frontend separation | E0…E4 | D-PAGE/D-POST | Classic Theme, frontend renders without Gutenberg plugin/ACF/Page Builder dependency; no editor-only asset leakage | Real WP support/plugin output, DOM/screenshots, frontend/editor network traces | Required forbidden dependency or broken native content ⇒ FAIL. Missing runtime/inspection ⇒ BLOCKED; unexecuted configuration ⇒ NOT TESTED |
| F-AC-03 | Run T02: static/latest-post Home, generic/specialized Page assignments, single/TOC, Blog/category/archive/search/404 and pagination; observe actual templates/hooks/content filters | E0…E5 | D-PAGE, D-POST/2, D-CATS/MENUS, S-SEARCH/404 | Correct native hierarchy, one main/H1 for Theme chrome, safe TOC links/no empty TOC, actual content and pagination; no fake empty results | Actual template/hook diagnostics via runtime tooling outside Theme, rendered DOM/HTTP, viewport captures | Wrong template, missing content, broken wrapper/anchor or fake data ⇒ FAIL. Fixture/route/debug access absent ⇒ BLOCKED; not visited ⇒ NOT TESTED |
| F-AC-04 | Observe namespace/textdomain/supports, registered style/script handles and Woo callback order while real requests run; repeat activation/request without duplicate registration | E0…E4 | U-ADMIN, W-PAGES, P-A | Correct Theme identity/prefix, resolvable callbacks, intended Woo wrappers/order, no redeclarations/collisions | WP registry/hook output, actual notices/logs, representative route DOM | Wrong/missing callback, collision or redeclaration ⇒ FAIL; inspection unavailable ⇒ BLOCKED; omitted route ⇒ NOT TESTED |
| F-AC-05 | Load representative Home/Article/Shop/Product/Cart/Account pages cold and warm; inspect asset status/type/order/version; compare frontend and native editor requests | E0…E5 | D-PAGE/POST, P-A, W-PAGES | Required assets load; no duplicate/competing Theme bundles or editor CSS frontend leak; cache query matches frozen version `0.1.3` or actual development mtime as designed; no Theme console error | Redacted HAR/network table, asset registry/cache headers, browser console and DOM | Missing/wrong asset, duplicate Theme enqueue/cache mismatch ⇒ FAIL; browser/HTTP access absent ⇒ BLOCKED; skipped view ⇒ NOT TESTED. Never edit Theme version to test this |
| F-AC-06 | Run T03…T06: Woo present/absent; Shop/Product/types/stock/sale/gallery, real populated/empty cart, checkout, order confirmation and native account/login/logout/history | E0…E5; relevant existing mode/policy/provider authorization for dependent cases | P-A/B and conditional P-V, C0…2, K0…2, O-A, U-A/B/GUEST, W-PAGES | Data/validation/totals/order/account states match Woo; no missing essential actions/notices or Theme business authority; missing Woo degrades without Theme fatal and warning respects capability | Real-session screenshots/HTTP/network, native Woo API/DB comparisons, logs and authorization observations | Wrong amount/state, lost session/form or unauthorized data ⇒ FAIL. Required mode/policy/provider/session fixture absent ⇒ BLOCKED. Unexecuted type/flow ⇒ NOT TESTED, never inferred from read-only GET |
| F-AC-07 | Run T07 in actual RTL browser and LTR resilience context; resize, keyboard-only traversal, dropdown/skip link/mobile nav/Escape/outside/link close/focus return, gallery/quantity/form focus and zoom | E0…E5 | Long D-PAGE/POST, nested D-MENUS, P-A/gallery, W-PAGES | No unintended viewport overflow/traps/hidden focus; usable native controls; correct source-derived responsive structure; focus visible and returned appropriately | Screenshots at recorded sizes, keyboard sequence/video, actual focus/DOM observations and console | Reproducible reflow/focus/control failure ⇒ FAIL; browser/device/content absent ⇒ BLOCKED; unrun browser/size ⇒ NOT TESTED. DOM-double success is not PASS |
| F-AC-08 | Run T08: adversarial native text/URL/query fixtures; native missing/invalid nonce and capability tests; guest/B access to A's order; inspect page/source/requests/log exposure and request-form boundaries | E0…E5; isolated permitted security testing | U-ADMIN/A/B/GUEST, D-PAGE/POST, P-A, O-A | Escaping prevents injection in escaped fields; native WP/Woo rejects unauthorized operations; only authorized customer data visible; no Theme handler bypass or secrets/real PII | Redacted browser/HTTP traces, role/nonce case manifest, actual DB no-write observations, server logs | Script execution, unauthorized write/read or secret exposure ⇒ FAIL. Missing roles/order/nonce/DB evidence ⇒ BLOCKED; unattempted boundary ⇒ NOT TESTED. Allowed native content HTML is not blanket-escaped |
| F-AC-09 | Run T09: after setup stabilizes, take scoped DB observations before/after read-only routes and authorized native session/cart/account actions; inventory runtime registered models/settings and trace writes to owner | E0…E5, read access/diagnostics for actual MySQL | Same fixtures; no new records solely for audit | No Theme schema/meta/settings duplication or unapproved writes. Explain legitimate WP/Woo sessions/transients/account/order changes separately; no parallel state store | Version/storage manifest, redacted schema/state diffs/checksums, native hook/call attribution and request timestamps | Demonstrated unapproved Theme write/schema ⇒ FAIL. Missing DB view/attribution ⇒ BLOCKED. No executed before/after comparison ⇒ NOT TESTED; source grep cannot pass this |
| F-AC-10 | Execute real PHP lint and existing migration smoke on isolated WP, both Woo modes; prove missing-prerequisite guard nonzero exit in a separate invocation; correlate browser cases rather than treating smoke as full coverage | E0…E6 for supplied runner | Exact D-PAGE/POST slugs/markers, D-CATS, public accessible P-A, native assigned pages | Lint/smoke outcomes recorded honestly; guard refuses invalid invocation; all required runtime/browser cases have evidence; no CHECKOUT redirect/skip promoted to form/order success | Raw exit codes and redacted output with versions, WP/browser/DB run IDs | Genuine assertion failure ⇒ FAIL. Missing tool/fixture or unresolved proposal mismatch ⇒ BLOCKED pending classification. Eligible unexecuted portion ⇒ NOT TESTED; prepared PHP is not tested PHP |
| F-AC-11 | Run T10: cold/warm representative route requests; record initiators, asset count/bytes, console and timing/waterfall; inspect duplicate, unnecessary external or obvious blocking loads | E0…E5 | Reuse content/products/cart/account states | No unexplained Theme console error, remote font/catalog/mock layer, duplicate assets or unjustified blocking regression; distinguish necessary WP/Woo assets from Theme | Redacted HAR/waterfall/console, initiator/owner inventory with request reasons | Proven unnecessary Theme request/duplicate/blocking regression ⇒ FAIL. Missing network/console/attribution ⇒ BLOCKED. Unprofiled route ⇒ NOT TESTED. No arbitrary budget or CWV certification |
| F-AC-12 | Before/after QA compare deployed Theme bytes and repository diff/inventory to baseline; preserve original references and pending boundaries; match runtime outputs/DB observations to the same tested artifact | E0…E5 and Git/artifact access | Frozen snapshot plus the actual fixture/run manifest | No unapproved code/asset/dependency or scope changes; any external fixture configuration/write is accounted for; runtime evidence refers to exact frozen Theme | Git diff/name-status, hashes/deployment manifest, WP loaded Theme version/paths, browser asset and DB run records | Proven unexplained deviation ⇒ FAIL. Artifact/DB visibility unavailable ⇒ BLOCKED. Comparison not performed ⇒ NOT TESTED; clean source worktree alone is insufficient |

## 6. Route/state execution cards

Future execution order is T01 → T02 → T03 → T04 → T05/T06 → T07/T08 → T09/T10, with DB snapshots begun **before** the actions under audit. Reset fixture variants between cards. All are unexecuted now.

| Card / F-AC | Concrete steps using the fixture plan | Observable expected outcome |
|---|---|---|
| T01 — 01/02/04 | Lint; activate frozen Theme with Woo present; load admin + one public page; use a separate restored Woo-absent state and repeat. Record supports/plugins/hooks, errors and DB platform | No Theme fatal/diagnostics; correct Classic/native supports; Woo-absent administrator notice only for suitable capability, no fabricated storefront |
| T02 — 03 | Visit generic D-PAGE and D-POST; follow each authored TOC link and both post pages. Visit Blog/category/search hit + no-hit/confirmed 404. With two Posts and page-size 1 follow archive page 2. Temporarily unpublish both for empty category. Use D-PAGE snapshots for static/latest-post Home and optional About/Academy/Request templates; restore assignments | Expected native content, URL/status (valid empty search/archive 200; nonexistent route 404), hierarchy/pagination, no empty grids/TOC or fake records. Named slugs are fixture-only; no guessed production routes |
| T03 — 03/06/07 | Visit Shop/category and P-A; compare displayed price/stock/category/attribute with actual native values. Exercise regular/sale, out-of-stock, missing price/image/attribute variants and gallery keyboard/click/zoom; run variation selection only if P-V exists | Sale/regular markup and native availability correct; Woo controls purchasing eligibility; no made-up price/attribute; safe missing-image state, working native gallery. Empty price is not zero-price/free |
| T04 — 06/07/09 | In one browser session visit empty Cart; add P-A via native button, update 1→2, reload and compare badge/quantities/totals to Woo; inspect P-B cross-sell and add it; remove lines to empty. Separately attempt unavailable/excess/invalid quantities under native constraints | Same native session persists; authoritative Woo amounts/stock validation/notices; cross-sell uses native association; real empty state/CTA. Native WP/Woo writes logged separately; no Theme local cart authority |
| T05 — 06/08/09 | First K0 GET, then K1 **same populated session**: inspect actual checkout form/fields/notices. Attempt K2 invalid required data only when this fixture/mode test is authorized; correct fields and observe native validation. Observe existing O-A confirmation separately | Empty-cart redirect does not pass populated checkout. Native validation and required policy honored; no fabricated order/success. Successful placement/payment remains BLOCKED until all mode/policy/shipping/provider prerequisites and a separate authorization exist |
| T06 — 06/08 | Guest visits My Account; one wrong login then correct U-A login; inspect dashboard/details/orders/O-A and make an authorized reversible change to a synthetic non-policy field such as first name. U-B sees empty history and cannot read A's order. Logout, revisit private endpoint and repeat with expired session | Native login/logout/validation and ownership, accurate history/details, no leaked other-customer fields. Existing order display/status fixtures do not certify order placement or payment |
| T07 — 03/07 | Observe route cards at 320/390/600/900/1100/1440 widths and around used breakpoints; these are test probes, not approved browser support policy. Check RTL, long Latin text, 200% zoom, no-JS and actual mobile/desktop transitions. Tab/Shift-Tab through skip/menu/submenu/search/cart/account; Escape/selection/outside click and late-loaded navigation focus. Inspect blank Home video and protected/empty content | No unintended horizontal shell overflow, hidden focus, keyboard trap, unlabeled empty accordion/player or sample fallback. Distinguish dedicated source pages from reused components for Search/404/Account; compare against protected HTML where available |
| T08 — 08 | Use inert recognizable text such as `<strong>QA</strong>` and quote/angle characters in native title/alt/query fields; authorized isolated script probes only for escaped fields. Try invalid/missing nonce on native account update and non-admin access to restricted administration. Test guest/U-B order denial and inspect Contact/Rent/Repair boundaries | Escaped fields cannot execute markup/scripts; allowed Page content HTML still renders per WP permissions. Invalid nonce/insufficient capability cannot write; order data remains private; no Theme submission handler or invented success state. HTTP 200 alone does not prove a rejected/accepted mutation—inspect state and notice |
| T09 — 08/09/12 | Exclude documented fixture setup from baseline; capture state before/after targeted route reads and each native mutation. Inspect order storage mode, actual writes and request ownership. Diff repository/deployed Theme bytes before and after | No unexplained Theme writes or source changes; legitimate WP/Woo option/transient/session/account/order activity is identified, not mislabeled as a Theme defect. Unattributed writes prevent PASS |
| T10 — 05/11 | For representative public/product/cart/account pages capture cold + warm browser requests, initiators, console and blocking resources; inspect frontend vs editor inventory, loaded source/bridge version and unnecessary third-party traffic | Accurate owner-attributed request table, no duplicate or unwanted Theme bundles. Necessary CSS/JS is not automatically a failure because it blocks rendering. No measured baseline/evidence means no performance certification |

## 7. Existing runner: scope, commands and limitations

**Planned only — do not run now or provision dependencies to make these commands work.** The runner is frozen; inspect its prerequisite failures rather than changing it for a green result. It requires local/development environment and the exact fixture slugs/markers above. `woo-present`/`woo-absent` must match the real plugin state.

```sh
# Future authorized isolated fixture; use actual absolute paths, not production.
find theme/torantejarat tests -name '*.php' -print0 | xargs -0 -n1 php -l
wp --path=/path/to/isolated-wp eval-file /path/to/repo/tests/migration-smoke.php woo-present
# On a separate restored fixture snapshot without Woo:
wp --path=/path/to/isolated-wp eval-file /path/to/repo/tests/migration-smoke.php woo-absent
```

- Exit 2 indicates prerequisite guard, exit 1 failed assertions, exit 0 only the smoke subset. Check individual outputs/versions; neither an exit code nor a version-floor assertion passes the full matrix.
- Smoke uses read-only HTTP requests without the interactive populated customer session. It accepts Woo's exact empty-cart Checkout redirect while explicitly reporting **NOT TESTED checkout form**. It does not add/update cart items, place/pay an order, authenticate a customer or test private account history.
- Hook inspection and in-memory block/TOC probes supplement real rendering; they do not prove browser behavior, account authorization or database safety. A native Woo GET may cause native session/transient activity; “read-only runner” does not certify zero DB writes.
- Supplied fixture text is not installed data. A copied HTML preview, PHP-shaped strings, static source assertions, mocked DOM or manually marked-paid order cannot pass activation/rendering/Cart/Checkout/payment/auth tests.
- Existing source/DOM/mutation reports remain a **separate evidence stream**. This preparation neither reruns them as runtime tests nor changes their code/results.

## 8. Entry/exit controls and pending contracts

Preparation deliverable: this matrix, minimal fixture plan, read-only environment findings, separate unexecuted acceptance ledger and immutable baseline reference. **Preparation is not Runtime QA completion.**

Before future execution: obtain explicit environment/fixture/execution authorization, make E0…E5 available, verify frozen Theme hashes, capture configuration/storage and synthetic manifest, then execute only eligible cases. Record blockers for the rest; no universal PASS. Log defects for separately authorized fixes without modifying the frozen code in this preparation.

Keep untouched: Forms → **Pending Handler Decision**; Compare → **Pending Data Contract**; Quiz → **Pending Data/Administration Contract**; specialized filters; product metadata/schema; payment gateway/PSP; real content; font decision; SEO implementation; Account/Checkout policy decisions. Missing approval is a blocker for dependent cases, not permission to implement or select a policy.

---

## Historical QA notes — retained context, not current execution status

# QA

## Next Phase — Runtime QA (migration frozen)

`Core Migration: IMPLEMENTATION COMPLETE`

`Runtime QA: BLOCKED / NOT AVAILABLE`

No further Phase-6 source iteration or environment installation. The next phase runs on an isolated **real WordPress + WooCommerce + MySQL + PHP + browser** fixture; see [Runtime QA Prerequisites and coverage](PHASE-6-MIGRATION-REPORT.md#runtime-qa-prerequisites) and the [separate F-AC mapping](ACCEPTANCE.md#runtime-qa--separate-acceptance-record-after-freeze).

Before execution, identify the frozen Git commit, diff/inventory all subsequent changes, record versions/configuration/fixtures and provide appropriate database visibility. Numeric/browser proposals are not approvals or compatibility results. Capture actual runtime/browser/DB evidence; no synthetic or source-only PASS. Keep source checks separate, pending cases blocked, and sensitive artifacts out of Git. No new account, checkout, form, schema or gateway decision is authorized by QA. Fix only demonstrable defects; no cleanup refactors.


## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

Historical Foundation evidence: ND-R01 and ND-R02 = OWNER APPROVED; MySQL only, version/browser proposals unchanged, Compatibility Tested = No. Owner has authorized limited Foundation implementation and QA. **Phase 5 = STARTED — QA INCOMPLETE**. [Test instructions](../tests/README.md) and [acceptance evidence](PHASE-5-FOUNDATION-REPORT.md) distinguish passing source checks from NOT TESTED runtime criteria.

For future authorized QA, record exact versions/patches and actual results; database coverage is MySQL only. Owner approval is not a test result.

Define supported:
- browsers
- devices
- viewport classes
- WordPress environments
- WooCommerce flows

Test categories:
- functional
- visual
- responsive
- RTL
- accessibility
- performance
- SEO
- security
- regression

Baseline test names/checks should be preserved unless an intentional change is documented.
