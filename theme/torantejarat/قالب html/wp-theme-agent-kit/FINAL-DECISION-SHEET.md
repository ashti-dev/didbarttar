# Final Decision Sheet — Owner Decisions

## وضعیت جاری — Phase 6، مجوز مستقیم Owner / 2026-09-22

**Phase 6 = IN PROGRESS — HTML → WordPress/WooCommerce Migration.** دستور جدید Owner جای محدودیت قبلی «Foundation only / هیچ فاز بعدی شروع نشود» را برای migration گرفته است. QA اجرا‌نشده Foundation، مرورگر و runtime موارد **Pending پیش از release** هستند، نه مانع عمومی توسعه. معیارهای پذیرش حذف نشده‌اند؛ هیچ compatibility یا release PASS اعلام نشده است. Payment provider، SEO نهایی و داده نهایی کاتالوگ نیز فقط جزء وابسته را متوقف می‌کنند.

مرجع فعلی: [Migration Map](PHASE-6-MIGRATION-MAP.md) و [گزارش کد و QA](PHASE-6-MIGRATION-REPORT.md). §21 و گزارش Phase 5 سابقه scope/آزمون همان مرحله‌اند؛ تصمیمات هویت، Classic Theme، WP/Woo ownership، MySQL-only و ممنوعیت dependencyهای مشخص همچنان معتبرند. schema، handler یا تصمیم معماری باز به‌صورت ضمنی تصویب نشده است.


## Project Metadata / Credits — OWNER_CONFIRMED

- Brand: **توران تجارت**
- Legal Entity: **دید برتر توران تجارت**
- National ID: `14011384991`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

## ND-R01 — Technical Identity

**ND-R01 = OWNER APPROVED**

| مورد | هویت فنی نهایی — OWNER APPROVED |
|---|---|
| Theme slug | `torantejarat` |
| PHP namespace | `ToranTejarat\Theme` |
| Text domain | `torantejarat` |
| Function prefix | `torantejarat_` |
| Constant prefix | `TORANTEJARAT_` |
| Asset handles | `torantejarat-*` |
| CSS namespace | `.torantejarat-*` |

`*` نشان‌دهنده suffix است، نه نام literal. Owner اکنون اجرای ساختار Foundation مستند را مجاز کرده است؛ source نسخه `0.1.0` در `theme/torantejarat/` ایجاد شد. هیچ هویت فنی دیگری، rename فایل‌های قبلی یا بسته توزیع/release ساخته نشده است.

اصل [ADR-011](ADR.md#adr-011--technical-identity--brand-separation) حفظ می‌شود: هویت فنی تا حد امکان از برند جداست تا تغییر برند باعث بازطراحی غیرضروری ساختار نشود. این بار نام فنی صراحتاً توسط Owner انتخاب شده، نه از نام شرکت یا repository استنتاج شده است.

**تاریخچه، نه هویت جاری:** «شلنگ‌بین» نام قبلی Project/Concept بود و دیگر Brand Identity نیست. `didbarttar` فقط در سابقه Temporary Repository/Project Identity قابل اشاره است و هویت فنی Theme نیست؛ `shalangbin` انتخاب نشده است. آثار کانسپت قبلی در HTML/JS صرفاً فایل‌های مرجع دست‌نخورده‌اند؛ legacy files اصلی خارج از Theme جدید دست‌نخورده‌اند؛ در Phase 6 فقط کپی namespaced CSS/media و رفتار presentation مجاز منتقل شده، نه demo catalog یا mock commerce.

## ND-R02 — Platform / Database

**ND-R02 = OWNER APPROVED**

**Database Support = MySQL** — انتخاب قطعی Owner. **MariaDB خارج از Support Matrix پروژه است**، مگر با تصمیم جدید Owner.

**تفکیک دامنه تأیید:** وضعیت ND-R02 طبق دستور مالک OWNER APPROVED است؛ انتخاب قطعی جدید، خانواده Database یعنی MySQL است. سایر نسخه‌ها و Browser policy طبق دستور مالک بدون تغییر در سطح **PROPOSED** حفظ می‌شوند. انتخاب MySQL نه تصویب خودکار عدد نسخه آن است و نه نتیجه Compatibility Test.

| مورد | تصمیم / پیشنهاد جاری | وضعیت تأیید | Compatibility Tested |
|---|---|---|---|
| Database Support | MySQL | OWNER APPROVED | No |
| WordPress minimum | 6.9 | PROPOSED — بدون تغییر | No |
| WooCommerce minimum | 10.8 | PROPOSED — بدون تغییر | No |
| PHP minimum | 8.3 | PROPOSED — بدون تغییر | No |
| MySQL minimum | 8.4 LTS | PROPOSED — عدد نسخه بدون تغییر | No |
| Browser policy | سیاست زیر | PROPOSED — بدون تغییر | No |

**Browser policy پیشنهادی:** دو نسخه stable اخیر Chrome/Edge/Firefox و Android Chrome؛ دو major اخیر پشتیبانی‌شده Safari در macOS/iOS؛ IE و WebView قدیمی خارج پشتیبانی تضمین‌شده. patchهای پلتفرم باید به‌روز و نسخه‌های دقیق محیط QA در زمان اجرای مجاز ثبت شوند.

### Official-source evidence already recorded

منابع قبلاً در [Architecture Contract §21](ARCHITECTURE-CONTRACT.md) با تاریخ بررسی 2026-09-21 ثبت شده‌اند؛ در این مرحله بررسی آنلاین یا تست جدیدی انجام نشده است:

- [WordPress requirements](https://wordpress.org/about/requirements/): توصیه PHP 8.3+ و MySQL 8.0+؛ اشاره upstream به MariaDB به معنی پشتیبانی پروژه از آن نیست.
- [WooCommerce server recommendations](https://woocommerce.com/document/server-requirements/): راهنمای WooCommerce 10.8+؛ توصیه WP 6.9+، PHP 8.3+ و MySQL 8.0+.
- [MySQL release model](https://dev.mysql.com/doc/refman/8.4/en/mysql-releases.html): شاخه 8.4 از نوع LTS؛ floor پیشنهادی 8.4، پیشنهاد پروژه است، نه minimum اجباری WordPress/WooCommerce.
- Browser policy پیشنهاد پروژه است، نه تضمین upstream. هیچ‌یک از منابع یا تصمیم‌های Owner اثبات compatibility این repository نیست.

### QA / Execution Gate

ماتریس Database برای اجرای QA آینده فقط **MySQL** را شامل می‌شود؛ ماتریس دوخانواده‌ای قبلی دیگر جاری نیست. نسخه دقیق WP/Woo/PHP/MySQL و مرورگرها و نتایج آزمون باید در اجرای مجاز آینده ثبت شوند. **Compatibility Tested = No**؛ هیچ PASS یا پشتیبانی آزموده‌شده‌ای ادعا نمی‌شود.

**Phase 5 = STARTED — QA INCOMPLETE**؛ مجوز مستقیم Owner برای اجرای Foundation محدود صادر شد و source ایجاد شده است. توقف قبلی NOT STARTED تاریخی است و دیگر وضعیت جاری نیست. پذیرش نهایی به علت نبود PHP/WP/Woo/MySQL/browser و QA اجرا‌نشده ناقص است؛ هیچ compatibility PASS یا release readiness ثبت نشده است. [Phase 5 Foundation Report](PHASE-5-FOUNDATION-REPORT.md) وضعیت F-ACها و تغییرات را ثبت می‌کند. با مجوز جدید، migration مستقل ادامه می‌یابد؛ QA runtime پیش از release لازم است.

## Final Status

| Item | Status |
|---|---|
| Brand | توران تجارت |
| Legal Entity | دید برتر توران تجارت |
| National ID | 14011384991 |
| Technical Identity | torantejarat |
| ND-R01 | OWNER APPROVED |
| Database Support | MySQL |
| ND-R02 | OWNER APPROVED |
| Compatibility Tested | No |
| Author | یعقوب طیبی |
| Designer | یعقوب طیبی |
| Phase 5 | STARTED — QA INCOMPLETE |
| Phase 6 | IN PROGRESS — source migration, runtime NOT TESTED |
| Code Changes | Classic templates, source assets, native WP/Woo integrations |
| Production Changes | Theme source added; not deployed |

Phase 6 با دستور Owner در حال اجراست؛ بخش‌های وابسته به تصمیم فقط به‌صورت محلی Pending هستند. Compatibility Tested = No.
