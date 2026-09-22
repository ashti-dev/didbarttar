# Phase 6 — Migration Map

> **FROZEN — IMPLEMENTATION COMPLETE — RUNTIME QA BLOCKED.** This is the initial migration/provenance map, not an instruction to continue source iterations. Current scope, final disposition and next-phase prerequisites are in the [Phase 6 Exit Record](PHASE-6-MIGRATION-REPORT.md) and [final implementation matrix](../theme/torantejarat/README.md#final-implementation-boundary--2026-09-22). Earlier incomplete items below are historical; pending contracts stay unimplemented.


**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

Owner اکنون migration واقعی را مجاز کرده است؛ QA معوق Foundation شرط توقف بخش‌های مستقل نیست. Convert, Don't Redesign. فایل‌های HTML/CSS/JS مرجع دست‌نخورده می‌مانند.

| HTML Source | WP Template | Data Source | Component / Part | JS behavior | Assets |
|---|---|---|---|---|---|
| header/footer مشترک همه HTMLها | header.php / footer.php | Site title/description/logo، WP menus؛ Woo cart/account URLs | brand / navigation / searchform / cart link؛ SVGهای خود source | فقط mobile disclosure از app.js؛ cart refresh متعلق به Woo | theme.css با namespace؛ migration bridge CSS |
| index.html | front-page.php | Front Page title/excerpt/content/featured image؛ Woo featured products و product categories؛ WP posts | hero / product card / editorial card | menu؛ بدون static catalog | theme.css، تصاویر از Media؛ assetهای مرجع محلی |
| blog.html / guide-*.html | home.php / single.php / archive.php / index.php | main WP query، categories/tags، date/author، excerpt/content/featured image | page intro / editorial card / article shell | native links/content | theme.css / breadcrumbs.css |
| صفحات HTML / about.html | page.php / page-templates/about.php | WP Page title/excerpt/content/featured image | page intro / prose / about layout | بدون handler حدسی | theme.css |
| source فاقد search/404 مستقل | search.php / 404.php | native WP search / status 404 | reuse page intro، cards، empty state موجود | native GET search؛ نه live search | theme.css |
| shop.html / product-*.html | Woo native archive + required content-product override؛ native single hooks | WC_Product: price/SKU/stock/images/gallery/categories/attributes/descriptions/related | source shop-card / pdp wrappers + Woo gallery/add-to-cart/tabs | Woo native variations/gallery؛ هیچ catalog.js/core.js | theme.css / shop.css / product.css / bridge |
| cart.html / checkout.html / account missing in source | page.php + existing Woo page content/endpoints | Woo cart/checkout/customer/order | source page shell + native Woo output؛ بدون fake summary | Woo scripts، نه localStorage/draft export | page shell + native Woo CSS؛ cart.css فقط کپی آرشیوی و فعلاً enqueue نمی‌شود؛ no cart/checkout overrides |
| Compare / Quiz / Rent / Repair / Contact / Academy | page-templates/request.php برای layout تماس/خدمات؛ core Page برای Academy؛ feature-specific migration جدا | قرارداد field/rules/handler/taxonomy باز | componentهایی که dependency باز دارند متوقف؛ fake form نساز | static business logic منتقل نمی‌شود | فایل‌های source حفظ؛ compare.css فقط پس از feature واقعی enqueue |

## مرزهای لازم، نه توقف کل migration

- ND-R07/08: attribute mapping تخصصی، video/limitations/story schema نهایی نیست؛ Woo-native data اکنون قابل مصرف است، custom meta حدس زده نمی‌شود.
- ND-R10: انتخاب Cart/Checkout Blocks یا shortcode باز است؛ محتوای Page موجود توسط Woo render می‌شود، Theme mode را تحمیل یا صفحات را خودکار seed نمی‌کند. Provider در Phase پرداخت.
- ND-R11/14/15: Forms handler، Compare/Quiz persistence/rules و read-time باز؛ feature مربوط Pending، core migration ادامه دارد.
- ND-R09/17: slug/URL تجاری، footer business copy، font/media publication rights تعیین نشده؛ لینک‌ها از WP menus/permalinks، نه حدس slug. فایل فونت محلی در source نیست؛ Google Fonts خودکار اضافه نمی‌شود. این اختلاف typography ثبت خواهد شد.
- خانه: core WordPress Page داده hero را می‌دهد؛ demo claims، اعداد، قیمت/کاتالوگ و FAQ/service/video نامصوب hardcode/import نمی‌شوند. sectionهای وابسته فقط با داده معتبر نمایش داده شوند؛ عدم داده با نمونه ساختگی جبران نشود.
