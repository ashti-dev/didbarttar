# CONSTRAINTS

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

مرجع الزام‌آور: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md)، نسخه 0.5، اجرای محدود Foundation با مجوز Owner، 2026-09-22؛ هیچ compatibility approval یا تغییر scope ایجاد نشده است.

## Accepted owner constraints — EXTRACTED / OWNER_CONFIRMED

- WordPress و WooCommerce required platform/commerce dependencies هستند.
- سایت فروش آنلاین واقعی است؛ Lead-only و COD مدل اصلی نیستند.
- پرداخت آنلاین ایرانی از WooCommerce Gateway abstraction، به‌صورت Provider-agnostic.
- Theme نباید PSP HTTP/verify/transaction/callback یا merchant credential handling داشته باشد.
- ACF Free/Pro و هر وابستگی مشابه مدیریت custom fields/options ممنوع است.
- plugin اختصاصی مالک Features اصلی Theme و Core dependency به Page Builder ممنوع است.
- Theme owns custom features؛ WooCommerce owns commerce؛ WordPress owns core content.
- Standard Woo data شامل price، SKU، stock، attributes، categories، gallery و descriptions در Theme mirror نشوند.
- Theme custom data با native Settings/Meta/Options APIs و Woo APIs مرتبط مدیریت شود؛ schema و UI داخل خود Theme.
- Compare، Quiz، recommendation، live search UI، custom components و settings در Theme بمانند.
- Cart/Checkout/Payment/Order Confirmation/Account/Order History و success/failed/cancelled payment flows، همراه Shop/Product، P0 هستند.
- مشاوره رایگان، درخواست اجاره و تعمیرات conversion مکمل‌اند.
- SEO و form integration optional؛ form plugin لازم فرض نشود.
- Provider خاص درگاه optional/pluggable است؛ یک gateway آنلاین فعال و آزموده برای انتشار فروشگاه الزامی است.
- Persian-first و RTL از brief پروژه الزامی است؛ سیاست تاریخ/عدد/زبان دوم هنوز باید تعیین شود.

## Decision status — platform and delivery

- **ND-R02 = OWNER APPROVED**؛ Database Support = **MySQL**. MariaDB خارج Support Matrix است، مگر با تصمیم جدید Owner. نسخه‌های WP/Woo/PHP/MySQL و Browser policy قبلی بدون تغییر PROPOSED هستند؛ Compatibility Tested = No. Hosting vendor بعداً انتخاب می‌شود.
- Theme type و editor contract برای Foundation بسته: Classic، بدون FSE؛ Gutenberg برای content editing نه Core rendering dependency. Product editor جزئی به فاز Product deferred است.
- **ND-R01 = OWNER APPROVED**؛ Brand = **توران تجارت** و Technical Identity = `torantejarat`؛ namespace `ToranTejarat\Theme` و سایر نام‌های نهایی فقط طبق [Final Decision Sheet](FINAL-DECISION-SHEET.md) و ADR-011. نام‌های قبلی صرفاً تاریخی‌اند و هویت فنی جایگزین مجاز نیست.
- currency/tax/shipping: ND-R04؛ checkout mode/provider qualification: ND-R10.
- product types/data/attribute schema: ND-R06 تا ND-R08.
- form handler/delivery/retention: ND-R11.
- SEO ownership: ND-R12؛ quality budgets و QA: ND-R13.
- باقی NDها در §20 قرارداد canonical نگهداری می‌شوند؛ این فهرست جای آن‌ها نیست.

## Phase gate

**Phase 5 = STARTED — QA INCOMPLETE**؛ مجوز مستقیم Owner برای Foundation صادر شده و source ایجاد شده است. محدوده تغییر: Theme Foundation، QA حداقلی و مستندات وضعیت. legacy assets دست‌نخورده‌اند؛ rename، dependency جدید، Product migration، payment implementation و قابلیت‌های خارج scope مجاز نیستند. PHP/WP/Woo/MySQL/browser در این محیط موجود نیستند؛ پذیرش runtime و هر فاز بعد تا رفع کمبود محیط/Review متوقف است. مرجع: [Phase 5 Foundation Report](PHASE-5-FOUNDATION-REPORT.md).

## Phase 5 scope — OWNER_CONFIRMED

Phase 5 فقط Foundation است؛ scope و F-ACها طبق §21 قرارداد و نتایج اجرا طبق [Phase 5 Foundation Report](PHASE-5-FOUNDATION-REPORT.md). Compare/Quiz، migration، UI نهایی Shop/PDP/Cart/Checkout/Account، Forms/Search/SEO، Gateway، Design System نهایی و محتوای واقعی خارج این فازند. نبود مصرف‌کننده واقعی یعنی نبود Settings/Data framework یا stub.
