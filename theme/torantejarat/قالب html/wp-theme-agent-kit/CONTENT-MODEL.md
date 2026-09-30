# CONTENT MODEL

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

قرارداد canonical: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md)، §3 تا §5 و §17. جدول §4 برای **هر field** storage، owner، UI، validation، fallback و migration دارد. نام keyها و schemaهای پیشنهادی تا Review قطعی نیستند.

## Accepted ownership — EXTRACTED / OWNER_CONFIRMED

| Entity / field family | Owner | Boundary |
|---|---|---|
| Posts / Pages / Users / Media / core taxonomies/settings | WordPress | مدل core موازی ساخته نشود |
| Product name / SKU / regular and sale price / stock | WooCommerce | Woo CRUD و UI native؛ Theme source of truth دوم نسازد |
| Gallery / product categories / attributes / descriptions | WooCommerce | attachments/taxonomies و product APIs native |
| Cart / Checkout / Orders / Customer commerce data | WooCommerce | نه localStorage یا order/customer store اختصاصی |
| Product video / technical limitations / story presentation | Theme | metadata native و editor UI خود Theme |
| Compare standard rows | WooCommerce data consumed by Theme | attributes/price/stock native؛ بدون duplicate field |
| Compare supplemental presentation | Theme | فقط چیزی که native equivalent ندارد؛ limitation اصلی دوباره ذخیره نشود |
| Quiz rules / recommendation configuration | Theme | versioned native options؛ اجرای قواعد داخل Theme |
| Theme settings | Theme via WP Settings/Options APIs | بدون options framework یا ACF |
| Article presentation variant | Theme via WP post meta | enum native و UI بومی |
| Read time | Theme computed | بدون field دستی مگر ضرورت مصوب |

ACF Free/Pro، وابستگی مشابه مدیریت داده و plugin اختصاصی مالک Features اصلی Theme ممنوع‌اند. انتخاب storage native، مالک معنایی داده را تغییر نمی‌دهد.

## EXTRACTED — current data risks

- `assets/js/catalog.js` چهار محصول نمونه دارد؛ مرجع اطلاعات تجاری تأییدشده نیست.
- legacy IDs برابر pro/g52/case/head هستند و WC product IDs نیستند.
- SKUهای SB-* در HTML دیده می‌شوند، اما field مستقل catalog نیستند.
- همه محصولات sample resolution/IP/diameter دارند؛ این‌ها بدون تأیید publish نشوند.
- recording=false برای head در copy به معنی وابسته/تأییدنشده است؛ importer نباید آن را خودکار «خیر» کند.
- هیچ ACF data، WordPress database یا Woo runtime در checkout فعلی وجود ندارد؛ ACF migration فرضی ساخته نشود.

## PROPOSED — lifecycle and relationships

- Page/Post/Product native؛ CPT اضافی فقط با نیاز محتوایی اثبات‌شده و ADR.
- رابطه product→attributes/categories/gallery native؛ رابطه quiz/compare→product با IDs و visibility validation.
- fields عمومی از PII فرم و customer private data جدا؛ custom meta REST exposure به‌صورت پیش‌فرض عمومی نباشد.
- numeric attribute term conventions، unknown/not-applicable و unit contract قبل از filter/quiz implementation تصویب شوند؛ index مشتق در صورت نیاز، نه source موازی.
- تغییر Theme داده را حذف نکند؛ versioned/idempotent migrations، export/import schema و حفظ محتوا الزامی پیشنهادی‌اند.
- import sample فقط در draft/staging و با migration identity؛ price/stock واقعی نیازمند تأیید است.

## Decision status / جزئیات باقی‌مانده

**ND-R01 = OWNER APPROVED**؛ نام‌ها طبق قرارداد ثابت‌اند. **Phase 5 = STARTED — QA INCOMPLETE** طبق [Phase 5 Foundation Report](PHASE-5-FOUNDATION-REPORT.md)؛ هیچ Product Metadata، option/meta registration، schema framework یا migration ایجاد نشده است. ND-R06/07/08/14/15/16 برای فازهای بعد باقی‌اند؛ scope/ownership تغییر نکرده است.

## Phase 5 boundary — OWNER_CONFIRMED

Data Contract و API ownership فقط در حد Foundation طبق §21 حفظ شود. پیاده‌سازی Product metadata/editor/fields، schema registry عمومی، generic CRUD، migration/export engine، Compare/Quiz rules به فاز مربوط تعلق دارند. Settings foundation فقط برای consumer واقعی همان فاز؛ field یا Option Page آزمایشی/بی‌مصرف تولید نشود.
