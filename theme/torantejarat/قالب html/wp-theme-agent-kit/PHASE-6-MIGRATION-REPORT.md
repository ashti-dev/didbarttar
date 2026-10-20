# Phase 6 — Exit Record / Migration Freeze

**Owner freeze instruction — 2026-09-22**

`Core Migration: IMPLEMENTATION COMPLETE`

`Runtime QA: BLOCKED / NOT AVAILABLE`

Combined status: **IMPLEMENTATION COMPLETE — RUNTIME QA BLOCKED**. Neither **DONE** nor **READY FOR PRODUCTION**. The first-delivery report below is retained as historical evidence, not the current backlog/status.

## Migration Baseline — frozen

Home; Header/Footer/Navigation; Article/TOC; Blog/Archive/Search/404; Shop/Product; Cart; Checkout; My Account; About; Academy; Contact/Rent/Repair; CSS/JS bridge; Woo presentation integration are frozen at their current implementation. The [final boundary matrix](../theme/torantejarat/README.md#final-implementation-boundary--2026-09-22) describes the existing implementation and its contract-dependent limits; this freeze does not imply missing features were implemented.

No more source-level iterations, cosmetic cleanup, refactoring or dependency/environment installation for Phase 6. A code change requires a **specific demonstrable defect**: record reproduction/evidence, affected frozen files, a minimal fix and regression evidence. An unresolved contract is not permission to invent a schema, handler or business rule. Runtime QA may uncover such defects; the baseline itself remains identifiable.

No production code, Theme asset or test runner was changed in this freeze operation. All prior work is retained.

## Runtime QA Blocker

The workspace has no available PHP/WP/Woo/MySQL/browser fixture. Historical available evidence is source **23/23**, navigation **13/13 DOM double**, mutation **13/13**, JS syntax and CSS source/structural checks. Historical Foundation remains **1/10** under its earlier scope. These results are **not** runtime, browser, database, compatibility or release PASS. No runtime environment is being installed for the freeze.

## Runtime QA Prerequisites

Only dependencies required to execute the next QA phase:

- **WordPress** running on an isolated local/development QA site.
- **WooCommerce**, with native Shop/Cart/Checkout/My Account pages and endpoints configured in that fixture.
- **MySQL** database and permission to inspect fixture state/changes. MySQL-only is Owner-approved; MariaDB is not a substitute.
- **PHP** CLI for lint plus a PHP-capable HTTP runtime/server for WordPress, with WP/Woo-required extensions and diagnostic logging.
- **Browser** with developer tools for real rendering, keyboard, console and network checks.
- **Synthetic test/fixture data**, not production content/customer data: native menus; populated/empty/protected Posts/Pages; long RTL text; products and supported variations; sale/stock/missing-data states; test users/roles and orders/history. Data must be prepared using native WP/Woo administration/APIs, not Theme seeding or a mock cart/account authority.
- **WP-CLI only if running the supplied `wp eval-file` smoke workflow**; it is QA tooling, not a Theme dependency.

Numeric WP/Woo/PHP/MySQL and browser proposals remain **PROPOSED**, not Approved or compatibility evidence. Record actual versions and configured Cart/Checkout mode at QA start. No mode, account policy or gateway is chosen by this record.

## Next Phase — Runtime QA

The next phase is real-runtime QA, not another migration iteration. Entry: identify the frozen commit; verify a clean diff/inventory; provide the isolated prerequisites; record exact PHP/WP/Woo/MySQL/browser versions, locale, viewport, fixture IDs and mode. Theme must not depend on the Gutenberg plugin. Keep an explicit Woo-present and Woo-absent diagnostic scenario.

| Area | Required real-runtime coverage |
|---|---|
| Theme | Activation; PHP lint/fatal/notice/warning/deprecation logs; actual template hierarchy and hooks; asset loading; RTL and responsive rendering |
| WooCommerce | Shop/Product; simple/variable states where applicable (absence is NOT TESTED, not PASS); stock/out-of-stock; sale price; gallery; populated/empty cart, quantity/removal/totals/cross-sells; checkout validation; order confirmation; My Account, login/logout and order history |
| UI states | Empty and missing image/price/attribute/Home video; empty search/archive; mobile navigation; keyboard/focus; long and protected content |
| Security | Escaping with adversarial synthetic inputs; native nonce/capability/order-ownership checks where applicable; form boundaries; no secrets/real PII in code/logs/artifacts; MySQL before/after evidence for unintended writes, separating expected WP/Woo session/order writes from Theme-origin writes |
| Performance | Real network/asset request inventory; console errors; unnecessary third-party requests; obvious render-blocking regressions; no unmeasured CWV claim |

Payment/provider qualification and unresolved policy/mode-specific cases stay blocked by their own decisions. Native order-confirmation UI can use synthetic existing Woo orders, but that does not prove a real payment or gateway lifecycle. Do not simulate success, create a parallel checkout or perform a real charge. Cases lacking required fixtures/decisions remain NOT TESTED/BLOCKED.

Re-evaluate **F-AC-01 through F-AC-12** using the separate [Runtime QA acceptance mapping](ACCEPTANCE.md#runtime-qa--separate-acceptance-record-after-freeze). A run must have real WordPress, WooCommerce, browser and MySQL evidence; each PASS must point to the relevant logs/screenshots/network traces/DB observations and expected-vs-actual result. Source results remain historical evidence in their own column/record; do not relabel them as runtime PASS. Redact tokens, cookies, credentials and personal data; store raw sensitive QA artifacts outside Git.

## Pending Decisions / Contracts — no implementation

Forms → **Pending Handler Decision**; Compare → **Pending Data Contract**; Quiz → **Pending Data/Administration Contract**. Specialized filters; product metadata/schema; payment gateway/PSP; real content; font decision; SEO implementation; Account/Checkout policy and mode decisions remain Pending and untouched.

## Files / Diff Baseline — traceable checkpoint

- Branch: `arena/01a0c314-didbarttar`; original parent: `13cbd745a1fc6bafe70fdad6a1c42f6d87aeada9`.
- Local checkpoint subject: **`Freeze Phase 6 migration baseline; runtime QA blocked`**. The commit containing this exit record is the frozen checkpoint; its Git tree is the authoritative full-file/byte inventory. No other branch, push, deployment or release is involved.
- Cumulative diff to the original parent: **67 added / 24 modified / 0 deleted**. This includes previously uncommitted Owner/architecture/Foundation work, not 91 newly edited files in the freeze turn. This turn edits only six existing documentation files: root README; this report; Migration Map; AGENTS; QA; ACCEPTANCE.
- All **35 original non-document reference files**, historical Review, Foundation runners/fixtures/report and existing migration code are preserved. Scope audit found no changed path outside root README, the existing agent-kit documents, `tests/`, and `theme/torantejarat/`.
- Pre-Phase-6 provenance remains in `tests/fixtures/migration-baseline.json`; it is not overwritten. Original-parent diff and pre-migration diff are different baselines and must not be conflated.
- Frozen Theme inventory: **51 files**, aggregate SHA-256: `b63d1219b2ab658c442b09108ca77117fb187b6c8fbe4554637b19196ec80088`. Computation: sort relative Theme paths; hash UTF-8 concatenation of `path + NUL + file_SHA256_hex + newline`. Includes all Theme files, not only PHP.

Recover the precise checkpoint, inventory and full binary-capable diff locally:

```sh
BASE=$(git log arena/01a0c314-didbarttar --fixed-strings --grep='Freeze Phase 6 migration baseline; runtime QA blocked' -1 --format=%H)
test -n "$BASE"
git show --format=fuller --stat "$BASE"
git ls-tree -r --full-tree "$BASE"
git diff --name-status 13cbd745a1fc6bafe70fdad6a1c42f6d87aeada9 "$BASE"
git diff --binary 13cbd745a1fc6bafe70fdad6a1c42f6d87aeada9 "$BASE"
# Before QA, compare the working tree (including untracked paths) against the checkpoint:
git status --short --untracked-files=all
git diff "$BASE" --
```

<details>
<summary>Cumulative changed-file inventory vs original parent (A = added, M = modified)</summary>

```text
M	README.md
A	tests/README.md
A	tests/fixtures/content.html
A	tests/fixtures/foundation-baseline.json
A	tests/fixtures/migration-baseline.json
A	tests/foundation-smoke.php
A	tests/foundation-static.mjs
A	tests/migration-mutations.mjs
A	tests/migration-navigation.mjs
A	tests/migration-smoke.php
A	tests/migration-static.mjs
A	theme/torantejarat/404.php
A	theme/torantejarat/README.md
A	theme/torantejarat/archive.php
A	theme/torantejarat/assets/css/base.css
A	theme/torantejarat/assets/css/components.css
A	theme/torantejarat/assets/css/editor.css
A	theme/torantejarat/assets/css/layout.css
A	theme/torantejarat/assets/css/native.css
A	theme/torantejarat/assets/css/source/breadcrumbs.css
A	theme/torantejarat/assets/css/source/cart.css
A	theme/torantejarat/assets/css/source/compare.css
A	theme/torantejarat/assets/css/source/product.css
A	theme/torantejarat/assets/css/source/shop.css
A	theme/torantejarat/assets/css/source/theme.css
A	theme/torantejarat/assets/images/device-case.webp
A	theme/torantejarat/assets/images/device-orange.webp
A	theme/torantejarat/assets/images/device-pro.webp
A	theme/torantejarat/assets/images/favicon.svg
A	theme/torantejarat/assets/images/lens-heads.webp
A	theme/torantejarat/assets/js/navigation.js
A	theme/torantejarat/assets/video/tunnel-inspection.mp4
A	theme/torantejarat/footer.php
A	theme/torantejarat/front-page.php
A	theme/torantejarat/functions.php
A	theme/torantejarat/header.php
A	theme/torantejarat/home.php
A	theme/torantejarat/inc/assets.php
A	theme/torantejarat/inc/bootstrap.php
A	theme/torantejarat/inc/patterns.php
A	theme/torantejarat/inc/presentation.php
A	theme/torantejarat/inc/setup.php
A	theme/torantejarat/inc/woocommerce.php
A	theme/torantejarat/index.php
A	theme/torantejarat/page-templates/about.php
A	theme/torantejarat/page-templates/academy.php
A	theme/torantejarat/page-templates/request.php
A	theme/torantejarat/page.php
A	theme/torantejarat/search.php
A	theme/torantejarat/searchform.php
A	theme/torantejarat/single.php
A	theme/torantejarat/style.css
A	theme/torantejarat/template-parts/brand.php
A	theme/torantejarat/template-parts/content-none.php
A	theme/torantejarat/template-parts/content.php
A	theme/torantejarat/template-parts/editorial-card.php
A	theme/torantejarat/template-parts/editorial-list.php
A	theme/torantejarat/template-parts/home-posts.php
A	theme/torantejarat/template-parts/home-products.php
A	theme/torantejarat/template-parts/home-services.php
A	theme/torantejarat/template-parts/page-intro.php
A	theme/torantejarat/woocommerce/content-product.php
M	wp-theme-agent-kit/A11Y.md
M	wp-theme-agent-kit/ACCEPTANCE.md
M	wp-theme-agent-kit/ADMIN.md
M	wp-theme-agent-kit/ADR.md
M	wp-theme-agent-kit/AGENTS.md
A	wp-theme-agent-kit/ARCHITECTURE-CONTRACT.md
A	wp-theme-agent-kit/ARCHITECTURE-REVIEW.md
M	wp-theme-agent-kit/COMPONENTS.md
M	wp-theme-agent-kit/CONSTRAINTS.md
M	wp-theme-agent-kit/CONTENT-MODEL.md
M	wp-theme-agent-kit/COPY.md
M	wp-theme-agent-kit/DESIGN-TOKENS.md
M	wp-theme-agent-kit/DEVELOPMENT-PROTOCOL.md
A	wp-theme-agent-kit/FINAL-DECISION-SHEET.md
M	wp-theme-agent-kit/I18N.md
M	wp-theme-agent-kit/LAYOUT-CONTRACT.md
M	wp-theme-agent-kit/MASTER-PROMPT.md
M	wp-theme-agent-kit/NAVIGATION.md
M	wp-theme-agent-kit/PERF.md
A	wp-theme-agent-kit/PHASE-5-FOUNDATION-REPORT.md
A	wp-theme-agent-kit/PHASE-6-MIGRATION-MAP.md
A	wp-theme-agent-kit/PHASE-6-MIGRATION-REPORT.md
M	wp-theme-agent-kit/PRODUCT.md
M	wp-theme-agent-kit/QA.md
M	wp-theme-agent-kit/SECURITY.md
M	wp-theme-agent-kit/SEO.md
M	wp-theme-agent-kit/SETTINGS.md
M	wp-theme-agent-kit/SITEMAP.md
M	wp-theme-agent-kit/WOOCOMMERCE.md
```

</details>

---

## Historical first-delivery record — retained verbatim below

The following counts, incomplete items and IN PROGRESS status describe the first delivery only. Current implementation/freeze status is the Exit Record above; historical evidence is not retroactively marked PASS.

# Phase 6 — گزارش migration واقعی HTML → WP/Woo

تاریخ: **2026-09-22**  
وضعیت: **IN PROGRESS — کد ایجاد شده، نه Done / release-ready**

**Brand:** توران تجارت  
**Legal Entity:** دید برتر توران تجارت — **National ID:** `14011384991`  
**Author:** یعقوب طیبی  
**Designer:** یعقوب طیبی  
**Website:** https://yaghoubtayebi.ir/

## 1. نتیجه این نوبت

این تحویل صرفاً proposal یا ادامه Foundation نیست: قالب‌های Classic، کپی namespaced استایل‌های HTML، منوی موبایل مستقل، اتصال به داده‌های WordPress و integration بومی WooCommerce در `theme/torantejarat/` نوشته شده‌اند. از محصولات نمونه، سبد localStorage، checkout نمایشی و فرم‌های «پیش‌نویس ارسال نشده» استفاده نشده است.

**تفکیک ضروری:** «کد integration ایجاد شده» به معنی «فروشگاه اجرا و آزموده شده» نیست. هیچ قالبی هر ده معیار DoD را کامل نکرده است؛ PHP lint و WP/Woo/MySQL/browser در این محیط **NOT TESTED** هستند. این کمبود، gate عمومی ادامه migration نیست، ولی پیش از release باید برطرف شود.

## 2. Baseline و حفاظت از کار قبلی

- Branch: `arena/01a0c314-didbarttar`
- HEAD: `13cbd745a1fc6bafe70fdad6a1c42f6d87aeada9`
- baseline قبل از Phase 6: `/tmp/phase6-baseline.json`؛ کپی پایدار همان داده در `tests/fixtures/migration-baseline.json`.
- آمار نسبت به baseline Phase 6، با احتساب همین گزارش: **33 فایل افزوده، 23 فایل تغییرکرده، 0 حذف**. rename انجام نشده است.
- هر **35 فایل مرجع غیرمستند** با hash قبلی برابرند. `ARCHITECTURE-REVIEW.md`، گزارش Phase 5، runnerها و fixtureهای تاریخی Foundation تغییر نکرده‌اند.
- هیچ dependency/package نصب یا اضافه نشد؛ commit، push، deployment یا release انجام نشده است. تغییرات dirty قبلی reset نشده‌اند.

## 3. قالب‌ها و رفتار واقعاً نوشته‌شده

| بخش | اجرای فعلی | محدودیت / وضعیت |
|---|---|---|
| Header / Navigation / Footer | ساختار topbar، brand، search، actions، nav و ستون‌های footer از HTML؛ site identity/custom logo و شش محل منوی WP؛ لینک‌های cart/account از Woo | نیازمند انتساب منو و محتوای واقعی؛ browser QA باز |
| Home | `front-page.php`: hero از Page title/excerpt/featured image، محتوای Page، دسته‌های واقعی Woo، محصولات featured و تازه‌های مجله | بخش‌های marketing/service/video/FAQ کامل نشده؛ hero title ساده است و تأکید دو‌رنگ متن نمونه را از داده حدسی بازسازی نمی‌کند |
| Blog / Index / Archive / Search | `home.php`, `index.php`, `archive.php`, `search.php`؛ grid/card منبع با main query، excerpt، تصویر، taxonomy label و pagination | search مستقل در HTML وجود نداشت؛ از componentهای موجود استفاده شد، نه visual system جدید |
| Page | `page.php`: page-intro و محتوای واقعی با `the_content()` / `wp_link_pages()` | در صفحات commerce، محتوا را Woo تولید می‌کند |
| Article | `single.php`: page-intro، article-layout/prose، author/date/category/tags، featured image و content؛ TOC از anchor واقعی headingهای Gutenberg | برای Classic HTML anchorها یا تیتر بدون anchor، TOC حدسی ساخته نمی‌شود؛ read time و art variant custom نداریم |
| 404 | page-intro، empty-state، native GET search و لینک خانه | component reuse؛ source مستقل 404 نداشتیم |
| About | template اختیاری `page-templates/about.php`: دو ستون about-statement/prose با excerpt/image/content بومی Page | اطلاعات شرکت hardcode نشده است |
| Contact / Rent / Repair | template اختیاری مشترک `page-templates/request.php`: request-layout و guide/body از HTML، با داده واقعی Page | فقط layout محتوایی؛ فیلدها و submission فرم پیاده نشده‌اند، دکمه ارسال جعلی نداریم |
| Shop / Product category / Product tag | archive بومی Woo، breadcrumb، category links، ordering/result count/pagination بومی و کارت source-derived | فیلتر تخصصی، mobile filter dialog و query contract سفارشی پیاده نشده؛ sidebar source هنوز کامل نیست |
| Product Single | hook wrapperهای pdp-top/grid/gallery/summary/purchase؛ title/price/excerpt/gallery/variation/add-to-cart/meta/tabs/attributes/related/upsells بومی Woo | بخش‌های تخصصی specs chips، video، limitations، FAQ و sticky purchase سفارشی کامل نشده‌اند |
| Cart / Checkout / Account / History / Order confirmation | عبور محتوای صفحات موجود و endpointها به Woo؛ `page.php`، Woo support و URL APIs واقعی | **integration code فقط**؛ migration جزئی UI این صفحه‌ها و آزمون حالت‌ها تمام نشده؛ هیچ mode یا gateway انتخاب نشده |
| Compare / Quiz / Academy | Page عمومی قابل render است؛ source مرجع محفوظ | قابلیت اختصاصی Compare/Quiz و curated Academy grid **پیاده نشده‌اند**؛ تصمیم data/rules/IA باز است |

در حالت Settings → Reading = latest posts، `front-page.php` به listing واقعی blog واگذار می‌کند؛ اولین پست به‌اشتباه به hero تبدیل نمی‌شود. queryهای اضافی خانه محدودند: چهار دسته root غیرخالی، چهار محصول featured قابل نمایش در catalog، سه پست منتشرشده با `no_found_rows`. post/loop globals پس از queryها reset می‌شوند. هیچ محتوایی seed یا overwrite نمی‌شود.

## 4. Static → Dynamic و مالکیت فیلدها

| داده source | مرجع جدید / طبقه‌بندی | وضعیت |
|---|---|---|
| عنوان/URL سایت، منوها، logo | WP Core | native APIs؛ بدون settings framework |
| Page/Post title، content، excerpt، author/date، تصویر، دسته/tag | WP Core | templateهای محتوایی و editorial cards |
| product ID / URL | Woo entity ID + WP permalink | ID/slug نمونه منتقل نشده |
| Product title / SKU / price / sale / stock / availability / purchase type | Woo Core | hook/template/getter بومی؛ محاسبه مبلغ، تبدیل تومان/ریال یا validation موازی نداریم |
| Product image/gallery | Woo Core با attachmentهای WP؛ alt از Media/native renderer | gallery/zoom/lightbox/slider Woo؛ تصویر نمونه به محصول نسبت داده نشده |
| توضیح کوتاه/بلند و related/upsells | Woo Core | loop excerpt و single native hooks |
| دسته و tag محصول | Woo Core taxonomy روی WP | term URLs از API؛ کاربردهای نمونه به taxonomy حدسی تبدیل نشده‌اند |
| طول کابل، قطر هد، resolution، waterproof/IP، recording | اولویت با **Woo native attributes** | additional-information بومی، با attributes واقعی موجود؛ slug/unit/unknown-state و mapping اختصاصی ND-R07 باز است. طول کابل با shipping length محصول یکی فرض نشده |
| subtitle انگلیسی / tag بازاریابی | ابتدا بررسی taxonomy/description/i18n بومی؛ فقط اگر واقعاً داده مستقل باشد Theme-specific | schema یا meta key اختراع نشده؛ source `en` خودکار به SKU تبدیل نشده |
| story / video / limitations / FAQ اختصاصی محصول | محتوای آزاد ممکن است در description بومی باشد؛ ساختار مستقل احتمالی Theme-specific و نیازمند قرارداد | ND-R08 باز؛ هیچ meta registry/ACF یا داده تکراری ساخته نشده |
| cart / totals / customer / order / payment status | Woo Core | هیچ business state یا local cart در Theme نیست |

## 5. Woo integration و تنها override

- `inc/woocommerce.php` support و wrapperها، native gallery support، breadcrumb، loop classes، category links و cart fragments را ثبت می‌کند.
- `woocommerce/content-product.php` تنها override است: ساختار image/info/code/description/bottom منبع به Woo loop متصل شده و هر پنج hook عمومی با ترتیب اصلی باقی مانده‌اند. قیمت/دکمه خرید از hookهای Woo می‌آیند؛ visibility نیز کنترل می‌شود.
- دلیل override: جای wrapperهای source card با markup پیش‌فرض یکسان نبود. templateهای single/cart/checkout/account/order کپی نشده‌اند.
- single summary، فرم خرید و validation، gallery، tabs و related به Woo واگذار شده‌اند. wrapperها فقط ارائه‌اند؛ native price برای قرار گرفتن داخل purchase panel جابه‌جا شده، نه بازنویسی.
- CTA بومی Woo در کارت‌ها حفظ می‌شود؛ دکمه صرفاً ظاهری یا خرید جعلی به‌جای حالت‌های simple/variable/external ساخته نشده است. تطبیق نهایی ابعاد/چیدمان آن با source نیازمند visual QA است.
- cart badge از `WC()->cart` می‌خواند؛ refresh به `wc-cart-fragments` ثبت‌شده Woo واگذار می‌شود. همگام‌سازی badge در cache و تغییر quantity در Blocks هنوز آزموده نشده است.
- endpointهای order/account و خطا/اعتبارسنجی به خود Woo تعلق دارند. Theme query string را پرداخت موفق تفسیر نمی‌کند، order ایجاد نمی‌کند و credential/PSP/callback/verification ندارد.

برای provenance ساختار hookها، این دو فایل upstream در tag `10.8.0` خوانده شدند؛ این **Compatibility Test نیست**:

- [content-product.php — template version 9.4.0](https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.0/plugins/woocommerce/templates/content-product.php)
- [content-single-product.php — template version 3.6.0](https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.0/plugins/woocommerce/templates/content-single-product.php)

## 6. Audit سه فایل JavaScript مرجع

| فایل / رفتار | طبقه‌بندی | اقدام واقعی |
|---|---|---|
| `catalog.js`: آرایه محصولات، قیمت‌ها، دسته‌ها، slugها و مشخصات نمونه | Data | منتقل/اجرا نشد؛ WP/Woo منبع واقعی‌اند |
| `core.js`: normalizeDigits/normalize/phone | helper با وابستگی به ورودی/handler و قرارداد locale | کپی نشد؛ native formatting فعلی کافی است، form contract هنوز باز |
| `core.js`: filterProducts، cleanCart، recommend | Business Logic | منتقل نشد؛ cart/visibility/price با Woo؛ filter/Quiz وابسته به قرارداد |
| `app.js`: SVG pathهای مصرف‌شده | Presentation | SVGهای لازم به helper محدود PHP با allowlist ثابت منتقل شدند |
| `app.js`: mobile navigation، Escape، outside/link close و breakpoint | Presentation | استخراج در `assets/js/navigation.js`؛ no-JS fallback و focus-leave handling اضافه شد |
| `app.js`: product cards / shop render / local search/sort/query/filter modal | Presentation + Data + Business Logic | loop و ordering/pagination بومی جایگزین؛ فیلتر تخصصی و dialog بدون مدل داده فعال نشده‌اند |
| `app.js`: localStorage cart/compare، totals، quantity clamp، undo و storage sync | Data + Business Logic | هیچ‌کدام کپی نشد؛ Woo cart/session مسئول commerce است؛ Compare باز |
| `app.js`: Quiz questions / rules / recommendations | Data + Business Logic + Presentation | بدون rule/attribute contract پیاده نشده |
| `app.js`: request draft/phone validation/date/copy و download-order | Business Logic + Presentation نمایشی | وارد Theme نشده؛ نه submission واقعی است نه checkout معتبر |
| `app.js`: PDP lightbox | Presentation | با gallery/lightbox بومی Woo جایگزین، نه handler موازی |
| WP / Woo integration | در سه فایل استاتیک integration واقعی نداشت | اکنون PHP native queries/hooks/URLs/enqueue و Woo-owned scripts؛ هیچ global تاریخی وارد Theme نشده |

## 7. Assets / typography / RTL

- هر شش CSS مرجع به `assets/css/source/` کپی شده‌اند؛ declarationها و breakpointها محفوظ، classها `.torantejarat-*` و token scope روی body سایت است. تست، نتیجه این تبدیل را با منبع کامل مقایسه می‌کند.
- `native.css` bridge برای menu list/submenus، heading/price markup، thumbnail/grid و Woo gallery/summary/form است؛ این فایل بازطراحی مستقل نیست، اما نتیجه بصری آن هنوز با مرورگر تأیید نشده.
- frontend پایه: source theme + breadcrumbs + native bridge؛ shop/product CSS فقط در context مربوط. فایل‌های neutral Foundation در repository محفوظ ولی در frontend enqueue نمی‌شوند. editor CSS جداست.
- `source/cart.css` و `source/compare.css` فقط assetهای staged هستند، **enqueue نمی‌شوند**. CSS کارت استاتیک را روی cart واقعی تحمیل نکردیم.
- چهار WebP، favicon SVG و ویدئوی محلی عیناً کپی شده‌اند؛ hash یکسان. آنها خودکار به محصول/صفحه نسبت داده یا به Media Library import نشده‌اند. favicon واقعی سایت با Site Icon بومی WP قابل تنظیم است.
- منوی JS فقط وقتی primary menu وجود دارد enqueue می‌شود؛ vanilla، بدون dependency جدید. Gallery/cart dependencies همان وابستگی‌های بومی Woo هستند.
- `language_attributes()`، logical properties، breakpointهای source، skip link، hidden/focus states، native labels/links و alt architecture حفظ شده‌اند؛ **browser/a11y PASS نداریم**. Site Language باید در WP متناسب با محتوای فارسی تنظیم شود.
- **فونت:** فایل محلی Vazirmatn در repository نیست. Google Fonts/CDN اضافه نشده؛ stack منبع فعلاً به Tahoma/Arial fallback می‌کند. typography دقیق تا تأمین فونت مجاز/تصمیم ND-R17 قابل تأیید نیست.
- اندازه خام/gzip آفلاین: source theme `53488/9707 B`، breadcrumbs `2063/615 B`، native bridge `14428/2807 B`، shop `11842/2417 B`، product `12034/2676 B`، navigation `1641/598 B`. این‌ها اندازه فایل‌اند، نه HTTP/CWV یا حجم کل WP/Woo.

## 8. QA اجراشده و evolution تست‌ها

محیط اجرای بررسی‌ها: **Node.js v22.22.3** و **Python 3.11.2**. `php`, `wp`, `mysql`, `composer`, `phpcs`, `chromium`, `google-chrome` در PATH موجود نیستند.

| آزمون | نتیجه واقعی |
|---|---|
| `node tests/migration-static.mjs` | **11/11 PASS** — hierarchy/identity، guard/callback، native data/escaping، source CSS/media equality، enqueue، Woo hooks، query bounds، no writes/dependencies و scope/reference audit |
| `node tests/migration-navigation.mjs` | **9/9 PASS** — DOM double؛ missing menu، enhancement، toggle/labels، Escape/focus، inside/outside/link click، breakpoint، focus leave |
| `node --check` برای JS Theme و دو runner جدید | PASS |
| Mutation testing | **7/7 detected**: تغییر رنگ source، حذف Woo loop hook، hardcode cart URL، option write، browser cart، callback ناموجود، شکستن Escape |
| `git diff --check` + کنترل whitespace فایل‌های جدید/تغییرکرده | PASS؛ untrackedها نیز جداگانه بررسی شده‌اند |
| SHA preservation | 35 مرجع، Review و evidence تاریخی محفوظ |
| PHP lint / PHPCS | **NOT TESTED**؛ PHP/Composer/PHPCS موجود نیست |
| `tests/migration-smoke.php` | runner read-only آماده، **NOT EXECUTED** |
| WordPress / Woo / MySQL / browser / transaction / compatibility | **NOT TESTED** |

اولین اجرای migration checker برابر 10/11 بود: checker اشتباهاً home URL را در header انتظار داشت، درحالی‌که داخل brand part بود. اکنون هم فراخوانی part و هم URL escaped همان component بررسی می‌شود؛ حذف escaping برای سبز شدن تست انجام نشده است.

runner تاریخی Foundation دست‌نخورده روی source جدید **2/10** می‌دهد، نه GREEN: F-S01/03/04/05/08/10 فرض‌های no-JS/no-override/no-query/exact-manifest همان مرحله را دارند؛ F-S06 به whitespace منو وابسته است و F-S07 escaping را inline می‌خواهد، نه در page-intro مشترک. F-S02/09 همچنان PASS هستند. این تفاوت مرحله‌ای مستند شده؛ هیچ assertion تاریخی حذف نشده و نتیجه Phase 5 بازنویسی نشده است.

## 9. DoD هر template — چرا هنوز Done نیست

ده معیار ارزیابی: **D1** trace به HTML، **D2** hierarchy صحیح Classic، **D3** داده اصلی dynamic، **D4** بدون demo business data، **D5** enqueue، **D6** RTL، **D7** responsiveness/interaction، **D8** بدون dependency ممنوع، **D9** PHP/JS syntax، **D10** بدون اختراع خارج Contract.

| خانواده template | شواهد source | معیارهای باز |
|---|---|---|
| Shell / Blog / Article / Page / Archive / Search / 404 | mapping و source code برای D1–5/8/10؛ search/404 با reuse component | PHP syntax در D9؛ مشاهده واقعی D6/7؛ runtime hierarchy/escaping/rendering |
| Home | core hero/category/product/editorial واقعی، نه کاتالوگ نمونه | باقی sectionهای source، دو‌رنگ بودن title، font و D6/7/9/runtime |
| About / Request layout | structural mapping و native Page fields | form portion برای Request پیاده نشده؛ font و D6/7/9/runtime |
| Shop / Product | native integration و source wrappers/card/conditional CSS | filter/specs/custom sections، native markup bridge visual diff، D6/7/9/runtime/commerce states |
| Cart / Checkout / Account / Order | native page/endpoints boundary و no fake commerce | source-faithful UI جزئی، انتخاب mode، state matrix، D6/7/9/runtime |
| Compare / Quiz / Academy اختصاصی | فقط reference و generic Page fallback | feature implementation و قراردادهای وابسته؛ هیچ Done/Implemented برای قابلیت ادعا نشده |

حتی معیارهایی که کنترل source دارند، certification اجرا نیستند. **هیچ سطر این جدول DoD کامل ندارد.**

## 10. بخش‌های باقی‌مانده و تصمیم‌های محلی

- **خانه:** benefit strip، finder/Quiz banner، field/video، service cards، FAQ و claims کسب‌وکار با داده واقعی/قرارداد لازم؛ چیزی از demo به‌عنوان واقعیت کسب‌وکار hardcode نشده.
- **ND-R07/08:** attribute conventions و mapping مشخصات، custom video/story/limitations/FAQ و UI جزئی editor محصول. Woo-native attributes/description مستقل قابل استفاده‌اند.
- **ND-R09/14:** filter/query behavior، blog/Academy IA، Compare persistence/share و Quiz rules/weights؛ این اجزا فقط خودشان Pending هستند.
- **ND-R11/18:** forms handler، مقصد/consent/retention/spam/upload و rent date/calendar. layout درخواست حاضر است، submission نیست.
- **ND-R10:** Checkout mode و provider qualification. Page passthrough mode انتخاب نمی‌کند؛ پرداخت متعلق به Payment phase است.
- **ND-R04/05/06:** currency/tax/shipping/guest/identity و محصول نهایی در Woo/configuration معتبر تعیین شوند؛ Theme اکنون تصمیم موازی نمی‌گیرد. عدم کاتالوگ نهایی مانع کدنویسی مستقل نیست.
- **ND-R15/17:** read-time algorithm، font/media rights، محتوای تماس/policies و readiness انتشار؛ read time دستی/حدسی یا فونت خارجی اجباری نداریم.
- **Runtime pending:** baseline PHP/WP/Woo/MySQL، no-Woo fallback، screenshots/keyboard/RTL و حالت‌های تجاری طبق `tests/README.md`. MySQL تنها خانواده مجاز است؛ نسخه‌های پیشنهادی قبلی تأیید compatibility نشده‌اند.

## 11. فایل‌های ایجادشده / تغییرکرده

### Theme — افزوده

- `front-page.php`, `home.php`, `search.php`, `searchform.php`
- `inc/presentation.php`
- `page-templates/about.php`, `page-templates/request.php`
- `template-parts/brand.php`, `editorial-card.php`, `home-posts.php`, `home-products.php`, `page-intro.php`
- `woocommerce/content-product.php`
- `assets/css/native.css`
- `assets/css/source/{theme,breadcrumbs,shop,product,cart,compare}.css`
- `assets/js/navigation.js`
- `assets/images/{device-case,device-orange,device-pro,lens-heads}.webp`, `assets/images/favicon.svg`, `assets/video/tunnel-inspection.mp4`

### Theme — تغییرکرده

- `header.php`, `footer.php`, `index.php`, `page.php`, `single.php`, `archive.php`, `404.php`
- `inc/{bootstrap,setup,assets,woocommerce}.php`
- `template-parts/content-none.php`, `style.css`, `README.md`

Theme اکنون 27 فایل PHP دارد. `functions.php`، content part قدیمی و CSSهای Foundation محفوظ‌اند؛ CSSهای قدیمی frontend مصرف نمی‌شوند.

### QA / Documentation

- افزوده: `tests/migration-static.mjs`, `tests/migration-navigation.mjs`, `tests/migration-smoke.php`, `tests/fixtures/migration-baseline.json`، Migration Map و همین گزارش.
- تغییر: `tests/README.md`، README ریشه، و فقط وضعیت مجوز/مرحله در `ACCEPTANCE.md`, `ADR.md`, `AGENTS.md`, `ARCHITECTURE-CONTRACT.md`, `DEVELOPMENT-PROTOCOL.md`, `FINAL-DECISION-SHEET.md`, `QA.md`.
- تغییر dependency/DB/schema/handler/gateway: **صفر**. Media copies source-derived هستند، نه داده تولیدی جدید.

## 12. گام اجرایی بعدی

ادامه تطبیق جزئی bridge و بخش‌های مستقل source؛ با فراهم شدن fixture واقعی، PHP lint و smoke، سپس screenshot/keyboard و Woo state matrix اجرا شوند. تکمیل filter/Compare/Quiz/forms/Academy فقط قرارداد همان component را لازم دارد، نه مجوز کلی مجدد Phase 6. قبل از ورود به جزئیات Checkout باید mode روشن شود؛ qualification درگاه برای go-live لازم است، نه برای ادامه migration مستقل.

**جمع‌بندی:** کد واقعی migration تحویل شده است؛ UI کامل، runtime acceptance، تراکنش واقعی و release تحویل نشده‌اند. Compatibility Tested = **No**.
