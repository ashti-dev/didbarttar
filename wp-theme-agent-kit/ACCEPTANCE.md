# ACCEPTANCE

## Phase 7 — Runtime QA Preparation: current ledger

Reference **`4640054480f7ccf5a29a3ba38f703534fe2bef9e`** on `arena/01a0c314-didbarttar`. Phase 6 remains frozen. [QA.md](QA.md#5-executable-runtime-qa-matrix--f-ac-0112) contains execution steps, prerequisites, fixtures, expected outcomes, evidence and result rules for every criterion. No runtime test or fixture creation has occurred; environment inspection only.

| Criterion | Runtime result | Executed | Current blocker |
|---|---|---|---|
| F-AC-01 | BLOCKED / NOT AVAILABLE | No | PHP/WP/MySQL activation and diagnostics environment absent |
| F-AC-02 | BLOCKED / NOT AVAILABLE | No | Real WP/editor/frontend/browser fixture absent |
| F-AC-03 | BLOCKED / NOT AVAILABLE | No | Actual template/content/route fixture and browser absent |
| F-AC-04 | BLOCKED / NOT AVAILABLE | No | Runtime registration/hooks/diagnostics unavailable |
| F-AC-05 | BLOCKED / NOT AVAILABLE | No | HTTP runtime and actual browser network/console unavailable |
| F-AC-06 | BLOCKED / NOT AVAILABLE | No | Real Woo/customer/cart/order fixture absent; mode/policy/provider-dependent cases also need their own prerequisites |
| F-AC-07 | BLOCKED / NOT AVAILABLE | No | Real RTL/responsive/keyboard/focus browser execution unavailable |
| F-AC-08 | BLOCKED / NOT AVAILABLE | No | Real users/roles/nonces/orders, browser/server and DB observations unavailable |
| F-AC-09 | BLOCKED / NOT AVAILABLE | No | MySQL before/after observations and write attribution unavailable |
| F-AC-10 | BLOCKED / NOT AVAILABLE | No | PHP lint/WP smoke and browser regression execution unavailable |
| F-AC-11 | BLOCKED / NOT AVAILABLE | No | Actual browser request/console/waterfall evidence unavailable |
| F-AC-12 | BLOCKED / NOT AVAILABLE | No | No deployed runtime/browser/DB artifact to match against the source baseline |

Historical **source-only** F-AC-09/12 PASS remains unchanged below. It is not a Runtime PASS. Case and criterion aggregation follow [QA result rules](QA.md#3-result-rules-and-evidence-contract); partial, conditional and contract-blocked cases cannot be silently promoted. No activation, Woo rendering, Cart, Checkout, order placement/payment, login/logout or browser/database behavior is certified by preparation.

## Historical Phase 6 acceptance context — not current execution status

## وضعیت جاری — Phase 6، مجوز مستقیم Owner / 2026-09-22

**Phase 6 = IN PROGRESS — HTML → WordPress/WooCommerce Migration.** دستور جدید Owner جای محدودیت قبلی «Foundation only / هیچ فاز بعدی شروع نشود» را برای migration گرفته است. QA اجرا‌نشده Foundation، مرورگر و runtime موارد **Pending پیش از release** هستند، نه مانع عمومی توسعه. معیارهای پذیرش حذف نشده‌اند؛ هیچ compatibility یا release PASS اعلام نشده است. Payment provider، SEO نهایی و داده نهایی کاتالوگ نیز فقط جزء وابسته را متوقف می‌کنند.

مرجع فعلی: [Migration Map](PHASE-6-MIGRATION-MAP.md) و [گزارش کد و QA](PHASE-6-MIGRATION-REPORT.md). §21 و گزارش Phase 5 سابقه scope/آزمون همان مرحله‌اند؛ تصمیمات هویت، Classic Theme، WP/Woo ownership، MySQL-only و ممنوعیت dependencyهای مشخص همچنان معتبرند. schema، handler یا تصمیم معماری باز به‌صورت ضمنی تصویب نشده است.


## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

Definition of Done for the theme.

## ارزیابی جاری — Phase 6 / final core implementation — 2026-09-22

**Runtime QA = BLOCKED / NOT AVAILABLE.** پس از implementation، probe واقعی انجام شد: PHP/PHP8.3، WP-CLI، MySQL/mysqld، Composer/PHPCS، Docker و Chromium/Chrome در PATH نبودند؛ `wp-load.php`/`wp-settings.php` در جست‌وجوی workspace پیدا نشد و process مربوط به PHP/MySQL مشاهده نشد. هیچ محیط یا dependency ایجاد نشد. Node v22.22.3 و Python 3.11.2 موجودند.

| معیار | وضعیت جاری | evidence / علت |
|---|---|---|
| F-AC-01 | BLOCKED | activation و WP_DEBUG به runtime غایب نیاز دارند؛ metadata به‌تنهایی کافی نیست |
| F-AC-02 | NOT TESTED | Classic hierarchy/no forbidden dependency در M-S01/09/11 کنترل شد؛ اجرای frontend بدون Gutenberg plugin آزموده نشده |
| F-AC-03 | BLOCKED | renderer واقعی Page/Post/Archive/Search/404/Home در دسترس نیست |
| F-AC-04 | NOT TESTED | namespace/guard/callback/handles در M-S02/05 PASS؛ collision runtime مشاهده‌پذیر نیست |
| F-AC-05 | BLOCKED | HTTP 200، enqueue runtime و console آزموده نشد؛ cache key از version بومی 0.1.3 می‌آید |
| F-AC-06 | BLOCKED | no-Woo/with-Woo activation و frontend آزموده نشد؛ source boundary در M-S07/09/14/16/22/23 PASS |
| F-AC-07 | NOT TESTED | مرورگر/RTL/reflow/keyboard واقعی نداریم؛ 13 unit check منو فقط DOM double هستند |
| F-AC-08 | NOT TESTED | escaping و password/public visibility source checks وجود دارند؛ adversarial fixture واقعی اجرا نشده |
| F-AC-09 | PASS | M-S09/14/15/22/23: بدون custom meta/options/admin schema؛ attributeها Woo-native، الگوها core blocks در Page content، انتخاب لینک‌ها WP menus؛ هیچ business data seed/write |
| F-AC-10 | BLOCKED | 23/23 source checks، 13/13 navigation، JS syntax PASS؛ PHP lint و runtime smoke اجرا نشده‌اند |
| F-AC-11 | NOT TESTED | source asset inventory/local-only بررسی شده؛ network inventory واقعی نداریم؛ CWV PASS اعلام نمی‌شود |
| F-AC-12 | PASS | M-S04/10/11 و diff/hash audit: source اصلی و evidence تاریخی محفوظ، تغییرات در دامنه migration جدید Owner، بدون dependency/PSP/handler/CPT/schema جدید |

دامنه F-AC-12 مطابق مجوز فعلی Phase 6 است؛ IN/OUT تاریخی Foundation عطف‌به‌ماسبق بازنویسی نشده. ۱۳ mutation قابل تکرار در `tests/migration-mutations.mjs` شناسایی شدند: TOC، fragment، empty listing/pagination، ترتیب wrapper و مالکیت totals/cart، مرز authentication/account، CSS و focus. M-S20…23 و source/CSS/DOM checks evidence اجرایی همین scope هستند؛ هیچ PHP/browser execution انجام نشده. هیچ‌یک از source/unit PASSها certification WP/Woo یا DoD کامل نیست.

**Core Migration Status: IMPLEMENTATION COMPLETE — RUNTIME QA BLOCKED.** طبق دستور نهایی Owner، بررسی دوبارهٔ دامنه انجام شد: کار مستقل دیگری برای migration هسته شناسایی نشد. این وضعیت **DONE، Template DoD یا release-ready نیست**؛ regression واقعی ممکن است اصلاح لازم کند. جدول مرز اجرا در README قالب ثبت است. Runtime QA همچنان **BLOCKED / NOT AVAILABLE** می‌ماند. محتوای واقعی/منوها **CONTENT PENDING**؛ ND-R10 و سیاست‌های Account/Payment باز هستند. Cart hooks فقط presentation قالب native را تغییر می‌دهند، mode انتخاب نمی‌کنند؛ Blocks/Checkout mode-specific parity تأیید نشده است. My Account/Search/404 مرجع HTML مستقل ندارند و از اجزای مشترک موجود استفاده می‌کنند.

**وضعیت قابلیت‌های وابسته:** Forms → **Pending Handler Decision**؛ Compare → **Pending Data Contract**؛ Quiz → **Pending Data/Administration Contract**. Academy فقط پیوندهای گزینش‌شده به Post/Page موجود دارد؛ CPT/course/enrollment model ساخته نشده. درگاه و lifecycle سفارش همچنان بیرون از Theme هستند.

## Runtime QA — separate acceptance record after freeze

**Initial runtime status for every criterion: BLOCKED / NOT AVAILABLE — NOT RUN.** The preceding table is frozen source-era evidence, including source-only PASS for F-AC-09/12. Do not overwrite/reclassify those PASS results as runtime results. Neither implementation completion nor this mapping is DONE/READY FOR PRODUCTION.

A runtime PASS requires a recorded actual WP/Woo/browser/MySQL run (versions, mode, locale, fixtures) plus criterion-specific evidence below. Missing applicable environments, fixtures, security observations or logs cannot be replaced with source assertions. No runtime test was executed to create this table.

| Criterion | Required runtime evidence | Runtime result |
|---|---|---|
| F-AC-01 | Real activation with PHP/WP_DEBUG fatal/notice/warning/deprecation logs; platform and DB versions | BLOCKED / NOT AVAILABLE |
| F-AC-02 | Classic frontend/editor separation in real WP; no Gutenberg-plugin or forbidden-framework dependency; loaded plugins recorded | BLOCKED / NOT AVAILABLE |
| F-AC-03 | Actual hierarchy/rendering for Home/Page/Article/Blog/Category/Search/404 and empty/protected/missing-data fixtures | BLOCKED / NOT AVAILABLE |
| F-AC-04 | Registered handles/textdomain/callbacks and hook order at runtime; collision/error logs in the active fixture | BLOCKED / NOT AVAILABLE |
| F-AC-05 | HTTP asset results, cache version, browser network/console and duplicate-load evidence | BLOCKED / NOT AVAILABLE |
| F-AC-06 | Woo-present/absent scenarios; native Shop/Product/stock/sale/gallery/cart/empty cart/checkout/confirmation/account/login/logout/history; no Theme-owned commerce or account logic | BLOCKED / NOT AVAILABLE |
| F-AC-07 | Real RTL/responsive viewport screenshots, mobile navigation, keyboard/focus/skip link and zoom/reflow observations | BLOCKED / NOT AVAILABLE |
| F-AC-08 | Adversarial escaping and native nonce/capability/order-ownership cases; form boundaries; redacted diagnostics with no secrets/real PII | BLOCKED / NOT AVAILABLE |
| F-AC-09 | MySQL before/after observations and runtime hook/data ownership audit; attribute legitimate WP/Woo writes separately, explain every unexpected write | BLOCKED / NOT AVAILABLE |
| F-AC-10 | Actual PHP lint, prepared smoke execution on the fixture, intentional failure-detection checks and browser regression results; source runs recorded separately | BLOCKED / NOT AVAILABLE |
| F-AC-11 | Browser request/console inventory; unnecessary external requests and obvious render-blocking regressions; no inferred CWV result | BLOCKED / NOT AVAILABLE |
| F-AC-12 | Frozen commit/diff/file inventory plus deployed Theme/assets/DB observation matched to that commit; deviations and defect fixes explicitly traced | BLOCKED / NOT AVAILABLE |

Use the [Exit Record](PHASE-6-MIGRATION-REPORT.md) for coverage, prerequisites and pending contracts. Test cases without a required product type, policy/mode decision or payment provider remain NOT TESTED/BLOCKED; successful native order UI is not evidence of gateway payment success. Raw HAR/DB/log artifacts may contain tokens/PII and must not be committed unredacted.

## Phase 5 — Foundation only

`OWNER_CONFIRMED`: دامنه IN/OUT در [Architecture Contract §21](ARCHITECTURE-CONTRACT.md) تثبیت شده است. Classic Theme بدون FSE؛ Gutenberg برای ویرایش محتوا و نه dependency Core rendering.

`OWNER_AUTHORIZED`: Owner اجرای Foundation و ارزیابی F-AC-01 تا F-AC-12 را مجاز کرده است؛ معیارها تغییر نکرده‌اند. **Phase 5 = STARTED — QA INCOMPLETE**؛ جدول نتیجه و evidence هر معیار در [Phase 5 Foundation Report](PHASE-5-FOUNDATION-REPORT.md) است. تنها F-AC-09 و F-AC-12 با بررسی source/diff PASS؛ F-AC-01 تا F-AC-08، F-AC-10 و F-AC-11 به علت نبود runtime/مرورگر NOT TESTED هستند. Source checks مکمل‌اند، نه جایگزین runtime. MySQL تنها Database Support است؛ نسخه‌ها و Browser policy قبلی PROPOSED و Compatibility Tested = No می‌مانند. هیچ معیار UI/Commerce نهایی در این مرحله PASS اعلام نشده است.

## General
- [ ] Spec requirements implemented
- [ ] Semantic HTML
- [ ] RTL verified
- [ ] Responsive behavior verified
- [ ] Keyboard/focus verified
- [ ] Accessibility checks pass
- [ ] No unexpected console errors
- [ ] Performance requirements pass
- [ ] Relevant SEO requirements pass
- [ ] Security requirements pass
- [ ] Relevant automated tests pass
- [ ] Regression passes
- [ ] Diff audit completed

## Component acceptance
Each component must have explicit acceptance criteria before it is considered Done.

## Page acceptance
Each page must pass its page-specific criteria.

## WooCommerce acceptance
Shop, product, cart, checkout, payment, order confirmation, My Account, customer account, order history and successful/failed/cancelled payment flows are confirmed P0 for the store release. Their full UI and integration tests belong to the corresponding commerce phases, not Phase 5 Foundation acceptance.
