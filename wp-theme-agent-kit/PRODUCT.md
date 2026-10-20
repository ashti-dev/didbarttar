# PRODUCT

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

مرجع تصمیم‌های هدف: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md)، §1 و FINAL DECISION LOG، 2026-09-21.

## Current repository — EXTRACTED

پیش‌نمایش استاتیک فارسی/RTL فعلی، چهار محصول نمونه تجهیزات بازرسی، محتوای راهنما، سبد محلی و فرم‌های draft-only دارد. «شلنگ‌بین» صرفاً نام قبلی Project/Concept در این آثار تاریخی است، نه Brand Identity جاری. فایل‌های اجرایی هنوز تغییر نکرده‌اند؛ برند هدف نهایی **توران تجارت** است.

## Business model — EXTRACTED / OWNER_CONFIRMED / Accepted

- برند نهایی **توران تجارت**؛ نام حقوقی **دید برتر توران تجارت**؛ شناسه ملی `14011384991`.
- **ND-R01 = OWNER APPROVED**: هویت فنی `torantejarat` و namespace `ToranTejarat\Theme`؛ سایر نام‌ها طبق [Final Decision Sheet](FINAL-DECISION-SHEET.md).
- **ND-R02 = OWNER APPROVED**: Database Support = MySQL؛ MariaDB خارج Support Matrix، Compatibility Tested = No؛ نسخه‌ها و Browser policy قبلی PROPOSED باقی‌اند.
- فروش آنلاین واقعی با WordPress و WooCommerce.
- Primary conversion: Product → Add to Cart → Cart → Checkout → Payment → Order Confirmation.
- پرداخت آنلاین با Gateway ایرانی استاندارد Woo؛ Provider-agnostic.
- Secondary conversions: مشاوره رایگان، درخواست اجاره و خدمات تعمیر.
- Secondary conversions جایگزین خرید آنلاین نیستند؛ Lead-only و COD مدل اصلی پروژه نیستند.
- My Account، Customer Account، Order History و همه نتایج پرداخت از همین حالا P0 هستند، همراه کل مسیر خرید.
- Theme مالک custom features؛ WordPress مالک content؛ WooCommerce مالک commerce.

## Non-goals / constraints — EXTRACTED / OWNER_CONFIRMED

- ساخت Commerce یا payment processing موازی در Theme.
- ACF یا dependency مشابه custom data؛ feature plugin اختصاصی برای Core Theme؛ Core dependency به Page Builder.
- تبدیل خودکار sample prices/specifications به داده قابل فروش بدون تأیید.
- اجرای هر Feature یا UI نهایی خارج از Foundation مصوب؛ مجوز جدید فقط Phase 5 محدود است.

## Decision status / جزئیات باقی‌مانده

- ND-R01 و ND-R02 = OWNER APPROVED؛ **Phase 5 = STARTED — QA INCOMPLETE** تا تکمیل QA محیط واقعی. Owner اجرای Foundation را مجاز کرده، نه فاز Product یا تولید محتوای واقعی. [Phase 5 Foundation Report](PHASE-5-FOUNDATION-REPORT.md).
- بازار هدف دقیق، سیاست فروش/ارسال/مالیات/ارز: ND-R04 و ND-R17.
- customer identity/guest policy: ND-R05.
- catalog واقعی، product types و تأیید مشخصات/رسانه: ND-R06 و ND-R17.
- اجاره و تعمیر: فرم درخواست یا workflow پیچیده، نحوه delivery/retention: ND-R11.
- نقش مستقل آکادمی و مجله/URLها: ND-R09.
- KPIهای conversion و معیارهای عملیاتی: در Review کسب‌وکار باید با معیارهای QA/انتشار تکمیل شوند؛ عددی از قالب استاتیک قابل استخراج نیست.
