# ADMIN — Native Theme Data UI

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

مرجع canonical: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md)، §3 تا §5 و §19.

## Accepted boundary — EXTRACTED / OWNER_CONFIRMED

WordPress و WooCommerce داده استاندارد خود را مدیریت می‌کنند. Theme مسئول admin UI و schema داده اختصاصی خودش است. ACF، framework مشابه و plugin اختصاصی مالک Core Theme features ممنوع‌اند. هیچ Option Page مبتنی بر ACF ساخته نشود.

## PROPOSED admin design

- WP Posts/Pages/Media/Users native باقی بمانند؛ frontend components عیناً به wp-admin تحمیل نشوند.
- Woo standard pricing، stock، SKU، gallery، descriptions، categories و attributes از UI خود Woo و APIهای آن؛ field موازی در panel Theme نباشد.
- product limitation/video/story و compare supplements از native Theme panel با schema معتبر؛ limitation در compare از source اصلی خوانده شود.
- article art variant از native post meta control؛ read time فقط calculated preview.
- quiz/settings با Settings API و editor محدود و type-safe؛ امکان اجرای code دلخواه وجود نداشته باشد.
- برای save: nonce + capability + object permission؛ autosave/revisions و REST policy مطابق editor منتخب؛ show_in_rest تنها در صورت نیاز و با exposure امن.
- نقش editor محتوا، مدیر محصول و مدیر تنظیمات باید تفکیک شوند؛ capabilityهای دقیق پس از نسخه/editor و سیاست مالک تعیین شوند.
- خطاها field-specific و قابل دسترس؛ invalid config ذخیره/فعال نشود؛ defaultهای امن و admin notices واضح.
- theme switch داده‌ها را حذف نکند؛ export/import، schema version و migration report در ابزار داخلی Theme پس از تصویب.
- gateway credentials/config با Woo gateway integration، نه صفحه settings Theme.

## Decision status / جزئیات باقی‌مانده

ND-R03 با Classic/Gutenberg و ND-R01 با torantejarat قطعی‌اند؛ ND-R02 = OWNER APPROVED با MySQL. نسخه‌ها/Browser policy پیشنهادی و Compatibility Tested = No ثابت‌اند. Product editor/schema/capabilities، rules UI، migration و form retention همچنان deferred هستند. **Phase 5 = STARTED — QA INCOMPLETE**؛ source ایجاد شده ولی هیچ Product panel یا Settings page بدون مصرف واقعی ساخته نشده است. [Phase 5 Foundation Report](PHASE-5-FOUNDATION-REPORT.md).

Phase 5 شامل Product panels، Quiz/dashboard کامل یا فرم‌ها نیست. تنها admin output فعلی یک notice خواندنی برای نبود Woo با capability check است؛ هیچ write/admin action وجود ندارد.
