# OWNER-DECISION — 2026-10-19

**وضعیت سند:** بند ۲ و بند ۳ معتبر — ثبت‌شده بر پایهٔ تأیید کتبی مالک در سشن 2026-10-19.
**دامنهٔ اعتبار:** بند ۲ (دامنهٔ فریز) مبتنی بر متن موجود قرارداد است و تصمیم جدید محسوب نمی‌شود.
بند ۳ (FD-20-REV) با تأیید مالک معتبر است؛ بازگردانی آن با یک خط «رد شد» در همین سند ممکن است.

**منبع واقعیت:** قالب `new-torantejarat` v1.0.1 در `E:\work item\didbarttar\new-torantejarat`
و نصب زندهٔ `C:\laragon\www\torantejarat-test\wp-content\themes\new-torantejarat` (تنها قالب نصب‌شده).

---

## ۱) چرا این سند وجود دارد

`ARCHITECTURE-CONTRACT.md:40` صریح می‌گوید:

> تغییر الزام پذیرفته‌شده نیازمند تصمیم جدید مالک و ADR دارای سابقه است.

و در `ARCHITECTURE-CONTRACT.md:599` برند تجاری **«توران تجارت»** با وضعیت `OWNER_CONFIRMED` ثبت شده،
در حالی که خروجی تحقق‌یافته (`new-torantejarat/style.css:2`، نام سایت، لوگو، ووکامرس) **بلکوکم / BELLCOCAM** است.
این تعارض باید پیش از هر کار روی هویت برند بسته شود.

## ۲) دامنهٔ فریز — این یک «واقعیتِ سند» است، نه تصمیم جدید

نقل مستقیم از `AGENTS.md`:

> «Only `theme/torantejarat/`, minimal Foundation QA and necessary status documentation are in scope.»

نتیجهٔ قطعی:

- فریز Phase 6/7 به پوشهٔ `theme/torantejarat/` می‌خورد.
- قالب تحویل و توسعه یعنی `new-torantejarat/` **بیرون دامنهٔ فریز** است؛ تغییر در آن با قرارداد فریز تداخل ندارد.
- از این تاریخ، هر ارجاع به «فریز» یعنی `theme/torantejarat/` — نه `new-torantejarat/`.

## ۳) FD-20-REV (پیشنهاد — تا امضای مالک غیرالزام‌آور)

> **FD-20-REV (2026-10-19):** Brand تجاریِ عملیاتی = **بلکوکم / BELLCOCAM**؛
> «دید برتر توران تجارت» (شناسهٔ ملی `14011384991`) به‌عنوان **Legal Entity / مالک** باقی می‌ماند.
> شناسه‌های فنی بدون تغییر: theme slug / text domain `torantejarat`، namespace `ToranTejarat\Theme`،
> نام پوشه، نام دیتابیس. دلیل: تحقق‌یافته در `style.css:2`، نام سایت، `assets/images/logo-bellcocam.png` و ووکامرس.

پیامدهای اجرایی پس از امضا:

- متن‌های درون‌قالب (فرم ورود، ایمیل‌های ووکامرس، فوتر، topbar) باید «بلکوکم» را نشان دهند؛
  `ورود به توران تجارت` یک نقص است، نه یک انتخاب. (`inc/settings/class-theme-settings.php` → `login_title`)
- هیچ شناسهٔ فنی (slug / text domain / namespace) تغییر نمی‌کند ⇒ نیاز به rename دیتابیسی صفر است.

**امضای مالک:** ☑ تأیید شد — 2026-10-19 (تأیید کتبی در سشن؛ مالک: YaghoubYTD)　 ☐ رد شد 　 ☐ اصلاح لازم است: ......

## ۴) الزاماتی که الزام فعال تلقی می‌شوند

فقط موارد `OWNER_CONFIRMED` / `OWNER APPROVED` در `ARCHITECTURE-CONTRACT.md` و `SETTINGS.md`:

| # | الزام | مرجع |
|---|-------|------|
| 1 | ACF Free/Pro و هر فریمورک custom-field/options خارجی ممنوع | `ARCHITECTURE-CONTRACT.md:483` |
| 2 | پلاگین اختصاصی برای compare/quiz/settings/metadata ممنوع | `:485` |
| 3 | Page Builder برای Core Theme ممنوع (Classic، بدون FSE) | `:486`, `:98` |
| 4 | Settings/Meta/Options API بومی؛ schema و UI دادهٔ اختصاصی مال Theme | `:171` |
| 5 | Compare داخلیِ Theme است | `:311` |
| 6 | پیکربندی کوئیز داخل Theme، بدون ACF | `:327` |
| 7 | فرم‌های اختصاصی مال Theme؛ وابستگی به پلاگین فرم فرض نشود | `:365` |
| 8 | SEO plugin اختیاری؛ Core نباید به آن وابسته شود | `:390` |
| 9 | Woo مالک commerce است؛ Theme تنظیماتش را mirror نکند | `:246` |
| 10 | Theme مالک پردازش پرداخت / merchant credentials نیست | `:491` |
| 11 | My Account و Order History از P0 | `:295` |

هر مورد با برچسب `PROPOSED` غیرالزام‌آور است و به آن استناد نمی‌شود.

## ۵) موارد بازِ مالک (مسدودکنندهٔ محتوا)

1. نرخ تبدیل دلار→تومان، یا قیمت دستی برای محصولات ۱۲۰–۱۲۴.
2. تصویر شاخص برای محصولات ۱۲۰، ۱۲۳، ۱۲۴.
3. `merchantcode` زرین‌پال (فعلاً sandbox و خالی).
4. مناطق/روش‌های ارسال (فعلاً ۰ zone / ۰ method) + عنوان COD («پرداخت هنگام دریافت (تست)»).
5. متن سیاست مرجوعی/ضمانت واقعی + تصمیم دربارهٔ صفحهٔ `/terms/` (ID 58) و پیش‌نویس صفحهٔ 12.
6. تعیین تکلیف صفحهٔ Compare (ID 54) و صفحه‌های تکراری #10 `/checkout/` و #11 `/my-account/`.
7. متن و لینک نوار بالای سایت (`topbar_text`, `topbar_link`).
8. ایمیل ادمین سایت (فعلاً `admin@example.com`) و ایمیل تماس اعلامی (فعلاً `hello@example.ir`).
