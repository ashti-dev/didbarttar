# Phase 5 — Foundation Execution Report

**Date:** 2026-09-22

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

## 1. Executive Summary

Owner اجرای Foundation محدود را پس از Review مستندات مجاز کرد. Source نسخه `0.1.0` در [theme/torantejarat](../theme/torantejarat/README.md) ایجاد شد؛ این خروجی Classic Theme است، نه فروشگاه نهایی یا release آزموده‌شده.

**Phase 5 = STARTED — QA INCOMPLETE**. **Compatibility Tested = No**. منطق shell و مرزهای native پیاده شده‌اند، ولی PHP lint و اجرای واقعی WordPress/Woo/MySQL/مرورگر ممکن نبود. Phase 5 کامل/Accepted اعلام نمی‌شود و هیچ فاز بعدی شروع نشده است.

Brand **توران تجارت**؛ Legal Entity **دید برتر توران تجارت**؛ National ID `14011384991`؛ ND-R01 و ND-R02 همچنان OWNER APPROVED. PHP namespace برابر `ToranTejarat\Theme`، slug/text domain برابر `torantejarat`، functions با `torantejarat_`، handles/CSS مطابق بسته مصوب هستند. هیچ global constant/class غیرضروری ساخته نشده؛ prefix مصوب constants برای نیاز واقعی آینده `TORANTEJARAT_` است.

## 2. Phase 5 Scope Compliance

- فقط bootstrap، Classic templates، native content/menu/Reading settings، assets، RTL/a11y/security/performance پایه و minimum QA.
- هیچ معماری جدید، dependency جدید، تغییر نام اجرایی فایل‌های قبلی یا گسترش scope انجام نشد.
- طرح asset/QA موجود در ADR-010 فقط برای Foundation، طبق دستور مستقیم اجرای Owner، Accepted شد؛ design/performance نهایی همچنان deferred است.
- عدم ایجاد JS، settings module/page، generic data layer یا theme.json بی‌مصرف مطابق قاعده «فقط consumer واقعی» است، نه جایگزینی معماری.
- نام‌های تاریخی در source جدید Theme یا executableهای QA استفاده نشده‌اند. legacy HTML/JS فقط reference خارج Theme جدید باقی مانده و import/enqueue نشده است.

## 3. Implemented Foundation

- [functions.php](../theme/torantejarat/functions.php) و [inc/bootstrap.php](../theme/torantejarat/inc/bootstrap.php): require صریح، چهار ماژول کوچک با hooks بومی، بدون framework/autoloader/container.
- [inc/setup.php](../theme/torantejarat/inc/setup.php): text domain، title-tag، HTML5 markup، native primary menu و editor stylesheet.
- تمام ۱۴ فایل PHP Theme namespace مصوب، prefix تابع و guard دسترسی مستقیم دارند.
- Theme header شامل Brand، Author/Designer، Author URI، version و target minimumهاست؛ هیچ Tested-up-to یا compatibility PASS اختراع نشده است.
- هیچ option/meta registration، custom setting یا admin action ایجاد نشده؛ site title و navigation از WordPress خوانده می‌شوند.

## 4. Theme Structure

```text
theme/torantejarat/
  README.md
  style.css
  functions.php
  header.php
  footer.php
  index.php
  page.php
  single.php
  archive.php
  404.php
  inc/
    bootstrap.php
    setup.php
    assets.php
    woocommerce.php
  template-parts/
    content.php
    content-none.php
  assets/css/
    base.css
    layout.css
    components.css
    editor.css
tests/
  README.md
  foundation-static.mjs
  foundation-smoke.php
  fixtures/
    foundation-baseline.json
    content.html
```

۲۰ فایل داخل Theme: ۱۴ PHP، ۵ CSS شامل header metadata، یک README. هیچ JS stub، FSE template، Woo override، languages catalog خالی، package manifest یا archive توزیع وجود ندارد.

## 5. WordPress Boundary

- Classic hierarchy: front page از Reading settings بومی؛ static front page از `page.php` و posts front page از `index.php` استفاده می‌کند. فایل front-page ویژه یا UI نهایی ساخته نشده است.
- `page.php`، `single.php`، `archive.php` و `404.php` shell پایه‌اند؛ `header.php`/`footer.php` hookهای `wp_head`، `wp_body_open`، `wp_footer` و language/body attributes بومی را حفظ می‌کنند.
- [content.php](../theme/torantejarat/template-parts/content.php) از `the_content()` و `wp_link_pages()` استفاده می‌کند؛ عنوان/URL جداگانه escape می‌شوند، نه کل محتوای Gutenberg.
- Gutenberg فقط content editor است؛ `add_editor_style()` مصرف واقعی editor را پوشش می‌دهد. هیچ Gutenberg plugin/frontend runtime dependency یا Site Editor/FSE ایجاد نشده است.
- هیچ option مربوط به Reading/site title/menu به‌صورت خودکار تغییر نمی‌کند. fixture HTML فقط در مسیر tests است، نه محتوای سایت.

## 6. WooCommerce Boundary

[inc/woocommerce.php](../theme/torantejarat/inc/woocommerce.php):

- تشخیص class موجود Woo پس از بارگذاری pluginها، بدون autoload اختصاصی.
- در حضور Woo: `add_theme_support('woocommerce')` و جایگزینی فقط main-content wrapper hooks برای landmark/skip target مشترک. هیچ template override یا business logic وجود ندارد.
- در نبود Woo: setup تجاری انجام نمی‌شود؛ مدیر دارای capability `activate_plugins` یک notice خواندنی می‌بیند. بازدیدکننده handler یا commerce fallback ساختگی دریافت نمی‌کند.
- هیچ Product/Order/Cart write، PSP request، callback، verification یا credential handling وجود ندارد.
- رفتار واقعی با Woo روشن/خاموش هنوز **NOT TESTED** است؛ صرف بررسی source تأیید runtime نیست.

## 7. Asset / CSS / JS Architecture

- [inc/assets.php](../theme/torantejarat/inc/assets.php): `torantejarat-base` → `torantejarat-layout` → `torantejarat-components` با dependency صریح و یک enqueue loop.
- مسیرها از WP APIs؛ version واحد از Theme header؛ mtime فقط در environment نوع `development`. در production/local از header version استفاده می‌شود.
- `style.css` metadata است و به‌صورت asset خالی enqueue نمی‌شود؛ editor.css نیز از frontend enqueue جداست.
- CSS با `.torantejarat-*`، logical properties و neutral shell؛ تنها classهای native تولیدشده توسط WP زیر ریشه Theme scope می‌شوند.
- هیچ Theme JS لازم نبود: navigation و skip link بومی‌اند، منو پنهان/mouse-only نیست. قرارداد vanilla module/IIFE بدون global برای نیاز واقعی آینده در Theme README ثبت شده؛ هیچ business JS یا نام global جدیدی ساخته نشده است.

## 8. Security Foundation

- URL، title، attributes و متن UI با توابع escaping مناسب WordPress نمایش داده می‌شوند؛ `the_content()` در مسیر استاندارد WP باقی می‌ماند.
- تمام PHPهای Theme guard مستقیم `ABSPATH` دارند.
- admin notice capability check دارد؛ هیچ state-changing endpoint/action، form یا input handler وجود ندارد که nonce یا persistence جدید لازم داشته باشد.
- هیچ secret/PII، raw request variable، remote HTTP client، product metadata یا schema write در Theme وجود ندارد.
- شواهد source: F-S02، F-S07، F-S08. آزمون payloadهای مخرب و output واقعی در WP هنوز **NOT TESTED**؛ security certification ادعا نمی‌شود.

## 9. Accessibility Foundation

- header/nav/main/footer، headingهای متناسب با single/list/404، skip link با target focusable، `:focus-visible` و لینک‌های native keyboard-accessible.
- logical spacing/dimensions، wrap شدن navigation/متن بلند، media max-inline-size و pre overflow.
- انیمیشن یا interaction پیچیده ساخته نشده است؛ نیازی به JS/mouse-only نیست.
- بررسی source F-S03 و F-S06 موفق؛ آزمون fa-IR/RTL و Latin در 320/768/1280، keyboard واقعی، browser/AT و contrast هنوز **NOT TESTED**. نه audit کامل accessibility و نه PASS بصری اعلام شده است.

## 10. Performance Foundation

Static asset inventory، byteهای خام و بدون ادعای transfer/runtime:

| Asset | Bytes | مصرف |
|---|---:|---|
| assets/css/base.css | 1062 | Frontend |
| assets/css/layout.css | 675 | Frontend؛ وابسته به base |
| assets/css/components.css | 868 | Frontend؛ وابسته به layout |
| assets/css/editor.css | 260 | Editor only |
| style.css | 328 | Metadata؛ enqueue نمی‌شود |

مجموع CSS تعریف‌شده برای frontend: **2605 bytes** در سه فایل. Theme JS، font/CDN، catalog preload، remote image یا third-party runtime جدید: **صفر در source Theme**. Bootstrap query تجاری یا eager catalog load ندارد.

این inventory **browser network capture نیست**؛ assets تولیدشده توسط WP/Woo/pluginها جدا هستند و اندازه‌گیری نشده‌اند. هیچ CWV/Lighthouse یا Performance PASS اعلام نشده است.

## 11. QA / F-AC-01 → F-AC-12

### محیط و بررسی‌های واقعاً اجراشده

- موجود: Node.js `v22.22.3`، Python standard library و git.
- ناموجود: `php`/`php8.3`/`php8.4`، `wp`، `mysql`/`mysqld`، Composer/PHPCS، Chromium/Chrome/Firefox و Docker؛ هیچ نصب یا provision انجام نشد.
- repository نیز WordPress/Woo runtime یا دیتابیس ندارد. PHP syntax حتی برای smoke runner قابل اجرا نبود.
- `node tests/foundation-static.mjs`: **۱۰/۱۰ source check موفق**؛ exit 0. این‌ها `F-S*` هستند و با F-AC runtime یکی نیستند.
- `node --check tests/foundation-static.mjs` و syntax سه JS مرجع فعلی: موفق. هیچ Theme JS وجود ندارد.
- RED پیش از ساخت Theme: checker با exit 1 فقدان فایل‌ها/hooks/metadata را تشخیص داد. دو false-positive در checker اولیه (تطبیق نام hook با call و اشتباه گرفتن `esc_url()` با CSS `url()`) با محدودکردن regex به syntax درست اصلاح شد؛ الزام‌ها حذف نشدند.
- سپس هفت mutation روی **کپی موقت خارج repository** شناسایی شدند: حذف wp_head (F-S03)، text domain اشتباه (F-S01)، script enqueue اضافی (F-S03)، CSS import خارجی (F-S09)، option write (F-S08)، main target اشتباه Woo (F-S05)، حذف guard (F-S02). همه exit 1؛ کپی‌ها حذف و source سالم دوباره بررسی شد.
- `git diff --check` و کنترل فایل‌های جدید/قدیمی، نام‌گذاری، schema/Feature نبودن source و hashهای baseline انجام شد.
- [foundation-smoke.php](../tests/foundation-smoke.php) برای fixture واقعی نوشته شده ولی **اجرا نشده** است؛ exit 2 برای prerequisites ناموجود، exit 1 برای assertion شکست‌خورده طراحی شده، اما رفتار PHP آن هنوز آزموده نشده است. دستورها و checklist در [tests/README.md](../tests/README.md) هستند.

### نتیجه دقیق Acceptance Criteria

هر ردیف کل معیار را ارزیابی می‌کند؛ شواهد source به‌تنهایی بخش runtime معیار را PASS نمی‌کنند.

| Criterion | Status | Evidence / علت |
|---|---|---|
| F-AC-01 | NOT TESTED | metadata و فایل‌ها در F-S01 بررسی شدند؛ install/activation و صفر fatal/notice به PHP/WP واقعی نیاز دارد که موجود نیست. |
| F-AC-02 | NOT TESTED | F-S01/F-S03 نبود FSE/ACF/Page Builder/editor-runtime dependency را در source تأیید می‌کنند؛ شناسایی واقعی Classic و frontend بدون Gutenberg plugin اجرا نشد. |
| F-AC-03 | NOT TESTED | hierarchy/hooks/the_content و fixture ترکیبی در source موجودند؛ rendering واقعی routeها، empty/long-title و filter chain اجرا نشد. |
| F-AC-04 | NOT TESTED | F-S02/F-S04 نام‌ها، prefixها، text domain، مسیر WP API و یکتایی declarations را بررسی کردند؛ collision/registration واقعی در WP آزمایش نشده است. |
| F-AC-05 | NOT TESTED | enqueue graph/version source/editor isolation در F-S04 بررسی شد؛ HTTP 200، console، duplicate network requests و cache-key واقعی نیازمند runtime/browser هستند. |
| F-AC-06 | NOT TESTED | F-S05/F-S08 و بازبینی source، detection/wrappers/capability و نبود Theme commerce writes/PSP calls را نشان می‌دهند؛ Woo روشن/خاموش و نبود fatal اجرا نشده است. |
| F-AC-07 | NOT TESTED | F-S06 و CSS/HTML review شواهد پایه RTL/focus/landmarks دارند؛ viewport و keyboard/browser واقعی در دسترس نبود. |
| F-AC-08 | NOT TESTED | F-S07/F-S08 escaping و نبود secret/PII persistence/handlers را در source بررسی کردند؛ fixtureهای ورودی مخرب و output واقعی اجرا نشدند. |
| F-AC-09 | PASS | F-S08 + بازبینی همه PHPهای Theme: صفر option/meta/admin page/schema/product-field registration و صفر settings/data stub؛ تنها consumerهای native WP خوانده می‌شوند. |
| F-AC-10 | NOT TESTED | Node syntax و ۱۰ source assertion اجرا شدند؛ PHP lint و smoke assertions روی WP، و اثبات failure exit خود runner PHP به محیط ناموجود نیاز دارند. |
| F-AC-11 | NOT TESTED | inventory static بالا و F-S09 نبود منابع ثالث/catalog در source Theme را نشان می‌دهند؛ browser network inventory اجرا نشده و CWV ادعا نشده است. |
| F-AC-12 | PASS | F-S10، hashهای pre-change و بازبینی diff: تمام ۳۵ فایل غیرمستند مرجع و گزارش تاریخی Review بدون تغییر؛ فقط Theme/QA/status docs اضافه یا اصلاح شدند، بدون Feature/migration/Provider/handler خارج scope. |

**۲ PASS، ۱۰ NOT TESTED، ۰ FAIL ثبت‌شده، ۰ BLOCKED در ردیف‌ها.** نبود FAIL در تست‌های اجراشده به معنی نبود عیب runtime نیست. کمبود محیط، blocker پذیرش نهایی است. **Compatibility Tested = No**.

## 12. Git / Change Summary

### Baseline قبل از تغییر

- Branch: `arena/01a0c314-didbarttar`؛ HEAD: `13cbd745a1fc6bafe70fdad6a1c42f6d87aeada9`.
- Working tree از ابتدا dirty بود: **۲۴ Markdown tracked modified + ۳ Markdown untracked**. هیچ‌کدام به‌عنوان تغییر جدید این اجرا جا زده نشده‌اند.
- ۶۲ فایل موجود: ۲۷ Markdown + ۳۵ HTML/CSS/JS/media. وضعیت دقیق و hash تک‌تک فایل‌ها در [foundation-baseline.json](../tests/fixtures/foundation-baseline.json) ذخیره شده است.
- SHA-256 mapping مرتب‌شده hashها: `0fb3914c17b4f9a72a4d25086fd821ee7fd2c8aec978dd14ba291e79f0c63f3a`.
- مقایسه این مرحله نسبت به baseline فوق است، نه صرفاً diff تجمعی نسبت به HEAD. Commit/push انجام نشده است.

| Change category | Result |
|---|---|
| Files Added | 26: بیست فایل Theme + پنج فایل QA/fixture + همین گزارش |
| Files Modified | 13 سند موجود؛ فهرست زیر |
| Files Deleted | 0 |
| Files Renamed | 0 |
| Dependencies Added | 0 |
| Dependencies Removed | 0 |

**Files Added:** تمام ۲۰ فایل Theme و ۵ فایل tests در tree بخش ۴، به‌اضافه `wp-theme-agent-kit/PHASE-5-FOUNDATION-REPORT.md`. در Theme، ۱۹ فایل PHP/CSS جدید production source هستند؛ deploy نشده‌اند. Fixtureها/runnerها خارج package Theme هستند.

**Files Modified:** `README.md` و زیر `wp-theme-agent-kit/`: `ARCHITECTURE-CONTRACT.md`، `FINAL-DECISION-SHEET.md`، `ADR.md`، `AGENTS.md`، `CONSTRAINTS.md`، `DEVELOPMENT-PROTOCOL.md`، `ACCEPTANCE.md`، `ADMIN.md`، `CONTENT-MODEL.md`، `SETTINGS.md`، `PRODUCT.md`، `QA.md`.

تغییر سندها ثبت مجوز جدید اجرا، manifest واقعی و وضعیت QA است؛ نه تغییر هویت/نسخه‌های پیشنهادی یا تصمیم تازه. ۱۴ سند دیگر و تمام ۳۵ فایل غیرمستند قبلی byte-identical باقی مانده‌اند. سایر assets/styles/scripts قبلی صرفاً مرجعند و در Theme جدید enqueue نشده‌اند.

## 13. Out-of-Scope Verification

بررسی source/diff هیچ‌یک را نشان نداد: Product migration/data model/metadata، ACF، Page Builder، UI نهایی Shop/PDP/Cart/Checkout/Account، Gateway/PSP/callback/verification، order workflow، Compare/Quiz/Search/Filters/Recommendation، Forms/Rent/Repair، Blog/Academy UI، SEO implementation، Design System نهایی، real content یا production catalog.

هیچ endpoint، form handler، credential، custom database/table/schema، option write یا package dependency اضافه نشد. Native loop/menu/empty state و Woo main wrapper فقط Foundation هستند. `index.php` fallback استاندارد WP است، نه Search یا Blog feature جدید.

## 14. Remaining Blockers

1. **Environment Required:** PHP CLI 8.3 target و WordPress/Woo/MySQL fixture برای PHP lint، activation، routes، debug log، enqueue/cache و Woo present/absent. PHP source و runner هنوز از نظر syntax/runtime تأیید نشده‌اند.
2. **Environment Required:** مرورگرهای policy پیشنهادی ثبت‌شده برای RTL/viewport/keyboard، console/network، security payload و editor isolation؛ QA matrix واقعی باید نسخه دقیق محیط و نتیجه را ثبت کند.
3. **Acceptance incomplete:** ده F-AC هنوز NOT TESTED هستند؛ آماده انتشار یا اتمام Phase 5 اعلام نمی‌شود.

در subset اجراشده **Decision Required معماری جدیدی پیدا نشد**؛ موارد بی‌مصرف یا خارج scope ساخته نشدند. تأمین محیط آزمون نباید به‌صورت خودکار با نصب dependency یا گسترش معماری انجام شود. در صورت بروز ambiguity واقعی در ادامه، باید توقف و Decision Request ثبت شود.

## 15. Recommended Next Phase

**فعلاً فاز محصول/تجارت را شروع نکنید.** اقدام بعدی، تکمیل QA همین Foundation روی محیط واقعی، رفع خطاهای اثبات‌شده در همین scope و Review نتیجه توسط Owner است. فقط پس از بستن F-ACها و مجوز بعدی، scope فاز بعد از مستندات موجود تعیین و اجرا شود.

**Final: Phase 5 STARTED — QA INCOMPLETE; Compatibility Tested = No; no deployment or next-phase execution.**
