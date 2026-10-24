# Architecture Contract — Ratification Pass

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

- تاریخ به‌روزرسانی اجرا: 2026-09-22؛ تصمیم‌های پیشین و منابع با تاریخ خود حفظ شده‌اند.
- نسخه سند: 0.6 / Phase 6 migration authorization؛ قراردادهای پذیرفته‌شده حفظ شده و runtime acceptance ناقص است.
- Repository baseline: `13cbd745a1fc6bafe70fdad6a1c42f6d87aeada9`
- مرحله جاری: Phase 6 = IN PROGRESS؛ source migration روی Foundation در حال اجراست. وضعیت تاریخی Phase 5 = STARTED — QA INCOMPLETE. جزئیات اجرا و evidence در [PHASE-5-FOUNDATION-REPORT.md](PHASE-5-FOUNDATION-REPORT.md).
- مرجع ADR: [ADR.md](ADR.md)
- دامنه فعلی: migration قالب Classic، integration بومی Woo، QA migration و همگام‌سازی وضعیت؛ legacy HTML/CSS/JS/assets و snapshot Review دست‌نخورده، بدون dependency جدید یا Feature خارج scope.

اطلاعات رسمی جاری — `OWNER_CONFIRMED`: Brand **توران تجارت**؛ Legal Entity **دید برتر توران تجارت**؛ National ID `14011384991`. **ND-R01 = OWNER APPROVED** با Theme slug/text domain `torantejarat` و namespace `ToranTejarat\Theme`؛ نام‌های کامل در §21. **ND-R02 = OWNER APPROVED** با **Database Support = MySQL**؛ MariaDB خارج Support Matrix است. نسخه‌های عددی و Browser policy قبلی همچنان PROPOSED و Compatibility Tested = No هستند. Author و Designer هر دو **یعقوب طیبی**؛ Website بدون تغییر. **Phase 6 = IN PROGRESS** با مجوز جدید Owner؛ این مجوز migration است، نه compatibility approval یا release. مرجع خلاصه: [Final Decision Sheet](FINAL-DECISION-SHEET.md).

## وضعیت و اعتبار قرارداد

- `EXTRACTED`: واقعیت repository یا الزام صریح مالک؛ منبع الزام مالک با `OWNER_CONFIRMED / Accepted` مشخص می‌شود. Accepted بودن یک الزام به معنی پیاده‌سازی‌شدن آن نیست.
- `PROPOSED`: روش پیشنهادی اجرا؛ تا Review و پذیرش، تصمیم نهایی نیست.
- `NEEDS_DECISION`: تصمیم باز؛ با شناسه `ND-Rxx` ثبت می‌شود. این شناسه‌ها مستقل از GAPهای Discovery هستند و آن‌ها را حذف نمی‌کنند.
- تصمیم‌های قطعی این سند، پیشنهادهای ناسازگار Discovery را برای معماری هدف جایگزین می‌کنند. شرح وضعیت HTML فعلی همچنان یک baseline تاریخی است.
- شماره ADRهای گزارش قبلی با شماره‌های مدنظر مالک یکسان نبود. جدول تطبیق در ADR.md این اختلاف را حفظ و حل می‌کند؛ بازشماری پنهان انجام نشده است.
- در صورت تعارض پیشنهاد فنی با `OWNER_CONFIRMED`، پیشنهاد قابل اجرا نیست. تغییر الزام پذیرفته‌شده نیازمند تصمیم جدید مالک و ADR دارای سابقه است.

`EXTRACTED — snapshot پیش از Foundation` — checkout در آن زمان ۲۰ HTML، ۶ CSS، ۳ JS، چهار محصول نمونه و اسناد راهنماست؛ ACF، Theme PHP، WooCommerce runtime یا plugin اختصاصی موجود نیست. فایل ADR قبلی فقط الگو بود؛ ADRهای Discovery در گزارش پیشنهاد شده بودند، نه به‌عنوان Accepted در repository.

---

## 1. Business Model

### تصمیم‌های قطعی

`EXTRACTED — OWNER_CONFIRMED / Accepted`

- سایت **فروش آنلاین واقعی** است، نه Lead Generation Only.
- WooCommerce dependency رسمی Commerce است.
- conversion اصلی:

  `Product → Add to Cart → Cart → Checkout → Payment → Order Confirmation`

- پرداخت اصلی از طریق **Iranian Online Payment Gateway** انجام می‌شود؛ COD جایگزین مدل اصلی نیست.
- conversionهای مکمل: **مشاوره رایگان، درخواست اجاره و خدمات تعمیر**. هیچ‌کدام جای خرید آنلاین را نمی‌گیرند.
- Shop، Product، Cart، Checkout، Payment، Order Confirmation، My Account، Order History، Customer Account و stateهای پرداخت موفق/ناموفق/لغوشده همگی **P0** هستند. P0 در این قرارداد اولویت و مانع پذیرش Core Flow است، نه ادعای وجود incident فعلی.

`PROPOSED` — CTA خرید، قیمت معتبر و وضعیت خریدپذیری باید در PDP و archive روشن باشند؛ CTA مشاوره مسیر مکمل باشد. متن‌های demo فقط در fixture/staging باقی بمانند؛ انتشار به داده و سیاست تجاری تأییدشده وابسته است.

`OWNER_CONFIRMED` — برند توران تجارت و هویت فنی طبق ND-R01 تصویب شده‌اند.

`NEEDS_DECISION` — محصول واقعی، simple/variable، مالیات، ارسال، ارز، guest checkout، قوانین خدمات و KPIها: ND-R04، ND-R05، ND-R06، ND-R11.

## 2. Dependency Policy

### Required platform dependency

`EXTRACTED — OWNER_CONFIRMED / Accepted`

- WordPress: محتوا، کاربر، رسانه و زیرساخت API.
- WooCommerce: همه چرخه Commerce و payment abstraction.

### Optional integrations

`EXTRACTED — OWNER_CONFIRMED / Accepted`

- Provider درگاه ایرانی؛ نام و افزونه خاص hard-code نمی‌شود.
- SEO plugin.
- Contact/form plugin، فقط در صورت نیاز اثبات‌شده و تصمیم جداگانه.

**تفکیک مهم:** انتخاب یک Provider خاص optional/pluggable است، اما **وجود حداقل یک درگاه آنلاین ایرانی فعال و آزموده برای انتشار فروشگاه الزامی است**. Optional بودن integration به معنی optional بودن Payment در Core Flow نیست.

### Forbidden architectural dependency

`EXTRACTED — OWNER_CONFIRMED / Accepted`

- ACF Free، ACF Pro و هر وابستگی مشابه مدیریت custom fields/options.
- plugin اختصاصی برای مالکیت یا اجرای Featureهای اصلی Theme.
- وابستگی Featureهای Core Theme به Page Builder.
- الزام به plugin فرم/SEO برای خواندن یا ویرایش داده اختصاصی Theme.

`PROPOSED` — Theme custom features در ماژول‌های داخلی Theme تفکیک شوند؛ تفکیک ماژول به معنی plugin جدا نیست. هیچ loader یا compatibility shim برای ACF ساخته نشود. در نبود WooCommerce، Theme fatal error ندهد، به مدیر اخطار دهد و خرید را fail-closed کند؛ چنین وضعیت degraded معادل سایت آماده فروش نیست.

`OWNER_CONFIRMED` — ND-R03 برای Foundation بسته شد: Classic WordPress Theme، بدون FSE؛ Gutenberg برای content editing، نه dependency اجرای Core Theme. ND-R02 = OWNER APPROVED با انتخاب MySQL و حذف MariaDB از Support Matrix؛ نسخه‌های عددی WP/Woo/PHP/MySQL و Browser policy همچنان PROPOSED، بدون compatibility approval، در §21 آمده‌اند. Classic Theme به‌تنهایی انتخاب Cart/Checkout Blocks در برابر Classic را تعیین نمی‌کند.

## 3. WordPress / WooCommerce / Theme Responsibility Matrix

`EXTRACTED — OWNER_CONFIRMED / Accepted`

| حوزه | مالک معنایی و مرجع حقیقت | مسئولیت Theme | خارج از اختیار Theme |
|---|---|---|---|
| Posts / Pages | WordPress | presentation، navigation، template | ایجاد CMS موازی |
| Users / login identity | WordPress | account UI سازگار با Woo | user table یا password flow موازی |
| Media / attachments | WordPress | انتخاب و نمایش rendition | کپی catalog مستقل رسانه |
| Taxonomies / core settings | WordPress؛ قواعد product taxonomies با Woo | نمایش/مصرف با API | بازسازی storage موازی |
| Products / descriptions / SKU | WooCommerce | PDP و card سفارشی | داده محصول استاندارد تکراری |
| Price / sale / tax / stock | WooCommerce | نمایش معتبر از API | محاسبه authoritative در JS یا options |
| Product categories / attributes / gallery | WooCommerce با زیرساخت WP | navigation، filters، gallery UI | نسخه مستقل دسته/ویژگی/گالری |
| Cart / Checkout | WooCommerce | layout و integration استاندارد | cart محلی authoritative یا checkout موازی |
| Orders / order history | WooCommerce | نمایش مجاز و status-aware | order table یا status engine اختصاصی |
| Customer commerce profile | WooCommerce؛ identity با WP | account UI | ذخیره موازی آدرس، billing یا مشتری |
| Payment abstraction | WooCommerce | نمایش روش‌ها و status | تراکنش، verification یا callback handler در Theme |
| Provider processing | WooCommerce-compatible Gateway integration | استقلال از Provider | merchant secret، PSP HTTP calls و verification در Theme |
| Design system / custom UI | Theme | مالک کامل | وابستگی به page builder |
| Compare / Quiz / recommendation | Theme | UI، rules، querying و cache مشتق | واگذاری Core Feature به plugin اختصاصی |
| Custom product presentation metadata | Theme با storage native WP/Woo | schema، editor UI، validation و migration | ACF یا تکرار price/stock/attributes |
| Theme settings / article variant / read time | Theme با APIهای WP | مالک کامل | options framework خارجی اجباری |
| Forms / live search / archive UI | Theme | UI و قابلیت‌های اصلی؛ handler تحت قرارداد | وابستگی اجباری به provider فرم |

`PROPOSED` — مرز ownership با محل فیزیکی جدول یکی نیست: برای نمونه، متادیتای اختصاصی محصول می‌تواند در post meta ذخیره شود ولی schema و UI آن را Theme مالک است؛ داده تجاری همان محصول همچنان متعلق به WooCommerce است.

## 4. Product Data Contract

**Foundation boundary — OWNER_CONFIRMED:** مالکیت native و منع duplication قطعی است؛ جدول زیر قرارداد فازهای قابلیت است، نه backlog ساخت Data Layer کامل در Phase 5. هیچ Product Metadata schema/editor، migration، Compare/Quiz rule یا field اجرایی جدید در Foundation ساخته نشود. حداقل data/settings foundation فقط طبق §21 اجرا خواهد شد، پس از مجوز.

### قواعد سراسری

`EXTRACTED — OWNER_CONFIRMED / Accepted`

- هیچ standard field مربوط به WooCommerce در options، custom meta یا JSON catalog موازی Theme دوباره ساخته نمی‌شود.
- custom field، limitation، video، story، quiz configuration، compare supplements، art variant و settings بدون ACF و بدون plugin اختصاصی Feature پیاده می‌شوند.
- Read time به‌طور پیش‌فرض محاسباتی است، نه فیلد دستی.

`PROPOSED` — جدول زیر قرارداد اجرایی پیشنهادی است. ستون owner برای موارد تصریح‌شده قطعی است؛ storage key، schema، UI دقیق و قواعد migration نیازمند Review هستند. هویت فنی ND-R01 با `torantejarat` مصوب است؛ `<prefix>` در مثال‌های این جدول placeholder schema پیشنهادی است، نه namespace باز یا نام فنی جایگزین. تصویب function prefix برابر `torantejarat_` به معنی تصویب خودکار meta/option keyها نیست؛ جزئیات داده تا فاز مربوط deferred هستند. R=required برای قابلیت مربوط، O=optional، C=conditional. اجباری‌بودن فروش و انتشار نهایی با سیاست داده محصول تأیید می‌شود، نه با نمونه HTML.

| Field / الزام | Owner | Storage / Access | UI مدیریت | Validation | Fallback | Migration strategy |
|---|---|---|---|---|---|---|
| Product name / R | WooCommerce | محصول native؛ `WC_Product` CRUD | editor محصول Woo | non-empty، escaping هنگام نمایش؛ قواعد Woo | محصول ناقص برای انتشار تجاری تأیید نشود | نام catalog → product name؛ تطبیق legacy ID، نه ساخت field موازی |
| SKU / C | WooCommerce | SKU native با Woo CRUD | فیلد SKU خود Woo | یکتا، trim، validation Woo؛ بدون ساخت SKU فرضی | SKU خالی نمایش داده نشود | `SB-*` فقط پس از تأیید مالک؛ catalog فعلی SKU ندارد |
| Price / R برای خریدپذیری | WooCommerce | regular price native؛ API قیمت برای نمایش | pricing Woo | decimal معتبر، currency مصوب، tax policy؛ عدد JS معتبر تلقی نشود | بدون قیمت معتبر، خریدپذیری از Woo؛ نه قیمت صفر مصنوعی | قیمت نمونه auto-publish نشود؛ import draft و تأیید واحد/مبلغ |
| Sale price / O | WooCommerce | sale price و dateهای native | sale controls Woo | قیمت و بازه زمانی طبق Woo؛ timezone استاندارد | قیمت معمول native | HTML فعلی sale ندارد؛ مقدار invent نشود |
| Stock / R policy | WooCommerce | stock status، quantity، backorders native | Inventory Woo | ظرفیت، مدیریت stock، variation inheritance و Woo checks | Theme از Woo `is_in_stock`/purchasability استفاده کند؛ محصول تأییدنشده از طریق workflow draft بماند | «نیازمند تأیید موجودی» به in-stock تبدیل نشود |
| Gallery / O | WooCommerce | featured image + gallery attachment IDs native | Media/Gallery Woo | attachment موجود و مجاز، MIME تصویر، حقوق رسانه | placeholder بومی Woo؛ بدون URL شکسته | چهار WebP به Media پس از تأیید مجوز؛ rendition و ترتیب حفظ شود |
| Product categories / C | WooCommerce | `product_cat` taxonomy native | taxonomy UI Woo | term معتبر؛ mapping صریح؛ بدون category table Theme | empty category به معنای کاربرد همه‌جانبه نباشد | دسته‌های catalog از طریق ID→term map؛ accessory فراموش نشود |
| Attributes / C | WooCommerce | global attributes / `pa_*` برای filter/compare؛ API Woo | Attributes UI با کمک ورودی/واحد توسط Theme | schema واحد، values allowlisted، unknown/not-applicable متفاوت از no؛ validation نوع عددی در لایه Theme | مقدار نامعلوم «نامشخص»؛ qualification حدس زده نشود | length/diameter/resolution/IP/recording → attribute terms تأییدشده؛ false فعلی head خودکار به «خیر» نگاشت نشود |
| Description / O | WooCommerce | product description native | editor محصول | محتوای مجاز WordPress/Woo و KSES مناسب | بخش خالی حذف؛ copy demo جایگزین نشود | محتوای اصلی محصول → description، بدون تکرار story |
| Short description / O | WooCommerce | short description native | editor کوتاه Woo | متن/HTML مجاز و محدودیت محتوایی مصوب | بدون متن ساختگی؛ حذف بخش خالی | `description` کوتاه catalog → short description |
| Product video / O | Theme | `<prefix>_product_video` product meta؛ attachment ID و caption؛ external URL فقط در صورت تصویب | panel بومی Theme با Media Picker | attachment معتبر، MIME و مجوز؛ برای URL خارجی allowlist/protocol؛ بدون arbitrary embed HTML | نمایش صادقانه نبود ویدیو؛ درخواست نمونه، بدون انتساب demo به مدل | ویدیوی خانه به محصول نسبت داده نشود؛ ویدیوی محصول فعلاً خالی |
| Technical limitation / O | Theme | `<prefix>_product_limitations` product meta، لیست typed و versioned | panel native داخل editor محصول | bounded list، متن مجاز، عدم ادعای safety تأییدنشده | بخش ادعا حذف؛ «اطلاعات تأیید نشده» فقط بر اساس وضعیت واقعی | `limitation` هر محصول به رکورد متن؛ نیازمند review فنی |
| Product story/content blocks / O | Theme | `<prefix>_product_story` product meta، ساختار versioned از sectionهای allowlisted؛ نه کپی description | UI section/repeater بومی Theme؛ editor adapter وابسته به نسخه هدف | section type، heading/text/media ID معتبر؛ no executable markup؛ حدود تعداد/اندازه پس از review | حذف section نامعتبر/خالی؛ استفاده از description native بدون duplicate render | فقط بخش‌های اختصاصی overview/assurance/FAQ در صورت مدل مصوب؛ متن مشترک از تنظیمات/محتوا، نه کپی در همه محصولات |
| Compare data استاندارد / C | WooCommerce | attributes، price، stock و SKU native؛ read-only projection در Theme | UI محصول Woo؛ انتخاب ردیف‌های compare در تنظیمات Theme | row→field mapping allowlisted؛ unit و applicability روشن | «نامشخص/نامرتبط»؛ هیچ مقدار فرضی | mapping ردیف‌های JS به fieldهای native؛ حذف catalog موازی |
| Compare supplemental data / O | Theme | `<prefix>_compare_supplements` product meta برای متن/ارائه‌ای که native معادل ندارد | panel native با schema محدود | هر field باید دلیل عدم استفاده از attribute داشته باشد؛ نه numeric price/stock/spec mirror | حذف supplement؛ native comparison ادامه یابد | تنها field اختصاصی تأییدشده import شود؛ limitation دوباره ذخیره نشود و از field اصلی خوانده شود |
| Quiz recommendation rules / R برای Quiz | Theme | `<prefix>_quiz_config` versioned option؛ rule references به attribute/taxonomy IDs؛ قیمت از Woo | صفحه Settings API + editor قواعد native | schema، operator allowlist، unit، valid terms، finite ranges؛ بدون PHP/SQL قابل ورود توسط مدیر | config نامعتبر: recommendation خاموش با CTA shop/مشاوره؛ محصول مناسب اعلام نشود | چهار سؤال و قواعد فعلی صرفاً seed پیشنهادی؛ عدم import budget/sample assumptions بدون تأیید |
| Theme settings / C | Theme با WordPress Settings API | `<prefix>_settings` options کوچک؛ transient برای cache نه source | Settings API تحت menu بومی Theme | register settings، per-field sanitize/validate، capability، defaults؛ secrets ممنوع | defaultهای طراحی و مقادیر خالی امن؛ تماس/ادعای تجاری invent نشود | فقط config تأییدشده از shell؛ demo contact تولید نشود |
| Article presentation variant / O | Theme | `<prefix>_art_variant` post meta enum | native post sidebar/metabox؛ بدون ACF | enum allowlist؛ schema type؛ نقش مجاز edit post | variant پیش‌فرض طرح | art-0/1/2 فقط variant ارائه؛ taxonomy مقاله تلقی نشود |
| Read time / computed | Theme | بدون فیلد authoritatve؛ cache مشتق اختیاری با content hash و algorithm version | خروجی read-only در preview/frontend | الگوریتم زبان‌آگاه، حذف markup؛ سرعت مطالعه باید تصویب شود | اگر الگوریتم/محتوا معتبر نیست، نمایش نده | از متن Post محاسبه؛ مقدار دستی یا تخمینی import نشود |
| Legacy source identity / migration-only | Theme (ابزار مهاجرت داخلی) | `<prefix>_legacy_id` و migration map نسخه‌دار؛ شناسه فنی، نه SKU | گزارش migration برای مدیر، نه field تجاری | uniqueness در محدوده import، source digest، عدم overwrite ناخواسته | import بدون identity معتبر متوقف شود | `pro/g52/case/head` → WC product ID برای idempotency و redirects |

### Numeric attributes بدون duplication

`PROPOSED` — Woo attribute term canonical value و واحد، مرجع ویژگی فنی باشد؛ برای شرط‌های بازه‌ای quiz/filter، Theme termهای معتبر را به عدد تبدیل و term IDهای واجد شرایط را query کند. نام نمایشی «۱۰ متر» به‌تنهایی parser آزاد و مبهم نداشته باشد؛ term slug/value convention پس از ND-R07 تثبیت شود. برای مقیاس بالا، index/cache فقط **مشتق و بازسازی‌پذیر** است؛ index دائمی یا custom table نیازمند ADR جداگانه است. numeric meta موازی با attribute به‌عنوان مرجع دوم ساخته نشود.

`NEEDS_DECISION` — دسته‌بندی کاربرد، attribute slugs/units، انواع Product، story schema، محدودیت‌های field و video source policy: ND-R06 تا ND-R08. Read-time speed: ND-R15.

## 5. Theme-native Data Layer

`EXTRACTED — OWNER_CONFIRMED / Accepted` — schema، UI و منطق custom data متعلق به خود Theme است؛ Settings API، Meta API، Options API و ساختارهای native، بدون dependency field framework.

`PROPOSED` — معماری داخلی هدف برای فازهای بعد، نه فایل‌های ایجادشده و نه فهرست تحویل Phase 5. محدودیت Foundation در §21 بر این فهرست مقدم است؛ schema registry عمومی، domain serviceها و migration engine در Foundation ساخته نمی‌شوند:

1. **Schema Registry:** تعریف type، default، required/optional، enum، permission، version و consumer هر field در Theme.
2. **Registration:** `register_setting` و `register_post_meta`/Meta API با sanitize و auth callback. `show_in_rest` فقط در صورت نیاز editor و با schema/permission صحیح؛ public exposure خودکار ممنوع.
3. **Storage adapters:** options برای config سراسری؛ post meta برای custom presentation؛ Woo CRUD برای product read/write، از جمله custom product meta در save cycle مناسب. ثبت schema و save به‌صورت دو writer موازی اجرا نشود.
4. **Admin UI:** Settings API و metabox/panel بومی؛ Woo editor hooks/extension APIs متناسب با نسخه مصوب. برای Gutenberg و Product Editor فعال، adapter جدا ولی storage مشترک؛ UI انتخاب‌نشده از قبل پیاده نشود.
5. **Read models:** adapterهای کوچک برای presentation و normalization؛ catalog موازی یا ORM اختصاصی ساخته نشود.
6. **Domain services داخلی:** compare، recommendation، live search، forms و cache با مرزهای مشخص در Theme.
7. **Migrations:** versioned، idempotent، backup-first، قابل گزارش و retry؛ تغییر schema روی هر frontend request انجام نشود.
8. **Cache:** مشتق، دارای version/invalidation؛ stock/price/purchasability لحظه خرید از Woo دوباره تأیید شود.

### Lifecycle و portability

`PROPOSED` — تغییر/غیرفعال‌شدن Theme داده‌های custom meta/options را حذف نکند. theme switch ممکن است UI compare/quiz/story/handler را غیرفعال کند، ولی محصولات، سفارش‌ها، مشتریان و محتوا در WP/Woo می‌مانند. export/import مستند JSON با schema version و attachment/term remapping برای custom data طراحی شود؛ بدون وابستگی به plugin اختصاصی. گزینه purge، اگر لازم شد، explicit، capability-protected و پس از backup باشد؛ نه خودکار در theme switch.

`OWNER_CONFIRMED` — namespace برابر `ToranTejarat\Theme` طبق ND-R01 است.

`NEEDS_DECISION` — Product editor جزئی، export scope، migration triggers و purge: ND-R02 (بخش Product)، ND-R08 و ND-R16؛ این موارد مانع Foundation محدود نیستند. ND-R03 برای Classic + Gutenberg بسته است. بازگشت به plugin-owned Core Features مجاز نیست.

## 6. URL Architecture

`PROPOSED` — مسیرهای زیر الگوی پیشنهادی‌اند، نه slugهای Accepted. Theme URLها را با permalink، term link، Woo page/endpoint URL APIs بسازد و رشته route را در UI hard-code نکند.

| مقصد | نمونه مسیر پیشنهادی | Owner / رفتار |
|---|---|---|
| خانه | `/` | WP front page |
| Shop | `/shop/` | Woo shop page/archive |
| دسته محصول | `/product-category/{term}/` | Woo `product_cat` archive |
| PDP | `/product/{slug}/` | Woo Product |
| Cart | `/cart/` | Woo assigned cart page |
| Checkout | `/checkout/` | Woo assigned checkout page |
| پرداخت سفارش موجود | checkout `order-pay` endpoint | Woo endpoint؛ URL از API، احراز شرایط سفارش |
| Order confirmation | checkout `order-received` endpoint | Woo endpoint؛ نمایش وضعیت authoritative |
| My Account | `/my-account/` | Woo account page |
| Order history/detail | account `orders` / `view-order` | Woo authenticated endpoints |
| آدرس/حساب/رمز | `edit-address` / `edit-account` / `lost-password` | Woo/WP endpoints و permissions |
| Compare / Quiz | `/compare/` / `/quiz/` | Page + Theme feature |
| مجله/مقاله | `/blog/` و permalink مصوب Post | WordPress |
| آکادمی/خدمات/حقوقی | Pages با slug مصوب | WordPress؛ Theme presentation |
| جست‌وجوی محصول | فرم GET بومی product search | Woo-compatible query؛ نه route اختصاصی PSP |

- filter queryها canonical و allowlisted باشند؛ invalid query نباید خطای server یا query دلخواه تولید کند.
- pagination با URL پایدار؛ Back/Forward و shareable filters آزمون شوند.
- callback/webhook URL را gateway integration ثبت می‌کند؛ Theme route یا return verifier نمی‌سازد.
- صفحه order-received proof of payment نیست؛ query parameter موفقیت هم مرجع اعتبار نیست.

`NEEDS_DECISION` — فارسی/لاتین بودن slugها، blog/category architecture، query names و سیاست indexability: ND-R09 و ND-R12.

## 7. Shop / Category / PDP Architecture

`EXTRACTED` — shop.html و چهار product HTML مرجع design هستند؛ چهارتا template مستقل لازم نیست. `.pdp-choice` فعلی فقط label کابل است، نه variation UI.

`PROPOSED`

- SSR با loop/queryهای Woo و progressive enhancement؛ shop/category بدون JS قابل پیمایش و صفحه‌بندی باشد.
- Theme مالک archive card variants، gallery presentation، technical summary، limitation/story/video، compare control و mobile purchase UI است.
- همه قیمت‌ها با Woo price APIs و currency/tax display صحیح؛ sale و variable price range از native API.
- add-to-cart با form/API استاندارد Woo؛ variable selection و quantity constraint با اعتبارسنجی Woo؛ duplicate DOM mobile/desktop یک رفتار تجاری مشترک داشته باشند.
- gallery از attachmentهای native؛ custom video metadata مسیر جداست و gallery mirror نیست.
- product type، catalog visibility، password/private state، purchasability و stock رعایت شوند؛ draftها در search/quiz/compare عمومی نمایش داده نشوند.
- related/upsell/cross-sell از رابطه Woo یا انتخاب مدیر؛ «سه محصول دیگر» HTML الگوریتم نهایی نیست.
- hooks و presentation adapters بر template override گسترده اولویت داشته باشند؛ هر override با نسخه مبدأ و regression coverage ثبت شود.

`NEEDS_DECISION` — Product types/editor جزئی و رفتار stock/backorder: ND-R02 (بخش Product)، ND-R06؛ Checkout Blocks/Classic: ND-R10. Classic Theme و Gutenberg برای content editing قطعی‌اند؛ جزئیات Product/Commerce در scope Foundation نیستند.

## 8. Cart / Checkout / Payment Architecture

### مرز قطعی پرداخت

`EXTRACTED — OWNER_CONFIRMED / Accepted`

`Theme Payment UI integration → WooCommerce Checkout/order lifecycle → WooCommerce Gateway interface → Payment Gateway (Iranian PSP communication / transaction initiation / callback / verification / result) → WooCommerce Order Status`

| مسئول | مرز قطعی OWNER_CONFIRMED |
|---|---|
| Theme | Payment UI integration، checkout presentation و gateway-independent presentation hooks؛ نه transaction processing یا verification |
| WooCommerce | Order/payment lifecycle abstraction و gateway interface |
| Payment Gateway | ارتباط با PSP ایرانی، transaction initiation، callback، verification و transaction result از طریق قرارداد Woo |

این مرز در Foundation فقط compatibility boundary است؛ نه مجوز ساخت Payment UI نهایی، transaction handler یا Gateway.

- Theme transaction، HTTP request به PSP، verify/refund handler، merchant credential storage یا callback processor ندارد.
- provider integration از Woo Gateway abstraction استفاده می‌کند؛ افزودن/تعویض provider نباید Core Theme را تغییر دهد.
- gateway-specific code و SDK در Theme قرار نگیرد. custom gateway احتمالی، فقط با تصمیم جداگانه و به‌صورت integration استاندارد خارج از Theme، نه plugin مالک Core Featureهای Theme.
- Lead-only و COD به‌عنوان مسیر اصلی کنار گذاشته شده‌اند.

### Cart و Checkout

`PROPOSED`

- Woo session/cart منبع حقیقت؛ localStorage فعلی cart مهاجرت تجاری نمی‌شود.
- cart totals، discounts، shipping، tax، stock reservation و order creation همگی Woo-owned.
- Theme noticeها و خطاهای Woo را حذف/دور نزند؛ loading، validation، empty، stale price/stock، repeated submit و retry UI داشته باشد.
- Cart/Checkout Blocks در برابر Classic هنوز انتخاب نشده: معیار انتخاب، سازگاری gatewayهای کاندید، روش ارسال، localization، a11y و نسخه Woo است. انتخاب هر دو مسیر هم‌زمان بدون ADR ممنوع.
- Theme فقط متدهای available و enabled را از Woo بخواند؛ provider ID/URL/logo از string hard-code در core نیاید. logo extension فقط metadata/presentation adapter عمومی.
- gatewayهای ایرانی باید در staging برای واحد پول، redirect، callback، idempotency و checkout mode تأیید شوند. نبود gateway آنلاین معتبر مانع go-live است، نه مجوز فعال‌کردن خاموش COD.

### Payment / Order state contract — تفصیل پیشنهادی برای Core Flowهای P0

`PROPOSED` — «لغو بازگشت کاربر» لزوماً معادل `cancelled` در Woo نیست؛ status فقط از order/gateway معتبر خوانده شود. UI activityهایی مثل `redirecting` یا `awaiting-verification` با Woo order status یکی نیستند؛ وضعیت native `processing` را «در حال انتقال به درگاه» ترجمه یا تفسیر نکنید. اولویت P0 مصوب حفظ شده؛ نام activity جدید، state تجاری جدید نمی‌سازد.

| حالت | مرجع authoritative | UI و اقدام مجاز |
|---|---|---|
| آماده پرداخت / pending | Woo order/payment eligibility | روش پرداخت، submit محافظت‌شده، قیمت/واحد روشن |
| UI activity: redirecting | مسیر redirect مجاز توسط Woo/Gateway؛ مستقل از status سفارش | feedback در حال انتقال؛ بدون ادعای paid؛ `processing` نام این activity نیست |
| callback هنوز نرسیده / verification نامعلوم | order pending/on-hold طبق integration | «پرداخت هنوز تأیید نشده»، refresh محدود/بازدید مجدد؛ retry فقط با اجازه Woo |
| پرداخت موفق | gateway verified → Woo payment completion / paid state | تأیید پرداخت، شماره سفارش، خلاصه مجاز، لینک account؛ الزاماً status `completed` نیست و می‌تواند `processing` باشد |
| پرداخت ناموفق قطعی | gateway/Woo verified failure | پیام روشن و امن، retry از Woo order-pay در صورت eligibility؛ بدون سفارش/charge تکراری |
| کاربر بازگشته/پرداخت را لغو کرده | وضعیت واقعی Woo + داده معتبر gateway | نمایش عدم تکمیل یا وضعیت در انتظار؛ query `cancel` به‌تنهایی status را عوض نکند |
| سفارش cancelled | Woo status معتبر | نمایش cancelled؛ پرداخت مجدد فقط طبق eligibility Woo؛ عدم override دستی |
| callback تکراری / out-of-order / دیررس | Gateway + Woo reconciliation | Theme status آخر معتبر را نمایش دهد؛ fulfillment یا پیام موفقیت دوباره از JS اجرا نشود |
| Session expiry / link invalid / forbidden | Woo/WP permission checks | مسیر امن بازگشت/ورود/بازیابی؛ بدون افشای order |

- مسئول amount/currency/order matching، signature/server verification و idempotent payment completion، **Gateway integration و WooCommerce** هستند. Theme فقط این موارد را در acceptance matrix الزام می‌کند.
- تفاوت ریال/تومان، decimal و واحد درگاه باید پیش از تراکنش واقعی حل شود؛ Theme تبدیل ۱۰برابری پراکنده یا مقایسه مبلغ از client انجام ندهد.
- cached HTML عمومی برای Cart/Checkout/Account/confirmation ممنوع؛ تنظیم caching hosting باید این مسیرها و sessionهای خصوصی را مستثنا کند.
- sandbox gateway و provider-agnostic integration tests لازم‌اند؛ در این مرحله هیچ provider انتخاب، نصب یا متصل نشده است.

`NEEDS_DECISION` — provider/version qualification، Blocks/Classic، ارز/ارسال/مالیات و billing fields: ND-R04، ND-R10. Online Payment دیگر تصمیم باز نیست.

## 9. My Account Architecture

`EXTRACTED — OWNER_CONFIRMED / Accepted` — My Account، customer account و order history از همین حالا P0 هستند، نه scope اختیاری آینده.

`PROPOSED`

- Woo My Account و endpointهای native، WP identity و `WC_Customer` مرجع باشند؛ user store یا customer profile موازی در Theme ساخته نشود.
- هسته UI: ورود، خروج، dashboard، order history، order detail، addresses، account details و password recovery. register UI تابع سیاست registration باشد؛ وجود account flow قطعی است ولی اجبار ثبت‌نام برای خرید هنوز باز است.
- stateها: logged-out، invalid login، validation error، reset requested/expired، logged-in، no orders، orders pagination، forbidden order، expired session، updated details و save error.
- مالکیت سفارش در تمام endpointها بررسی شود؛ دانستن order ID یا تغییر URL اجازه مشاهده نمی‌دهد.
- guest confirmation/detail از مکانیزم verification استاندارد نسخه Woo منتخب استفاده کند؛ order key به‌تنهایی مبنای طراحی authorization سفارشی نباشد.
- اطلاعات private وارد HTML cache، live search، public REST response یا analytics URL نشود.
- saved payment methods فقط در صورت پشتیبانی provider و انتخاب scope؛ downloads فقط برای محصول دانلودی؛ این دو با اصل P0 بودن Account اشتباه نشوند.

`NEEDS_DECISION` — guest checkout، register timing، احراز با email/phone، OTP احتمالی و account copy: ND-R05. OTP یا plugin خاص پیش‌فرض نیست.

## 10. Compare Architecture

`EXTRACTED — OWNER_CONFIRMED / Accepted` — Compare قابلیت داخلی Theme است؛ داده استاندارد از Woo attributes/product APIs، داده واقعاً اختصاصی از Theme meta.

`PROPOSED`

- انتخاب فعلی حداکثر سه محصول به‌عنوان baseline UX حفظ شود؛ تغییر حد نیازمند تأیید است.
- client فقط WC product IDs و schema version نگهدارد؛ نه price/stock/spec authoritative و نه PII.
- خواندن مقایسه با allowlist IDs و visibility check؛ محصول حذف/مخفی‌شده حذف شود و پیام قابل دسترس بدهد.
- قیمت/stock در زمان نمایش از Woo؛ feature rows از attribute contract؛ limitation از همان meta اصلی، نه فیلد دوم.
- differences-only باید unknown، not-applicable و unit را درست مقایسه کند؛ accessory با دستگاه کامل برابر فرض نشود.
- stateها: empty، 1/2/3 selected، limit reached، removed/unavailable product، refresh error، partial unknown data، loading و no-JS fallback.
- localStorage failure: in-page state و پیام روشن؛ cross-tab sync بدون overwrite داده تجاری.

`NEEDS_DECISION` — persistence/consent، shareable compare URL، variation در برابر parent product و جدول مشخصات نهایی: ND-R07 و ND-R14.

## 11. Quiz Architecture

`EXTRACTED — OWNER_CONFIRMED / Accepted` — سؤال‌ها، configuration و recommendation logic در خود Theme؛ بدون ACF یا feature plugin.

`PROPOSED`

- configuration نسخه‌دار از native options؛ UI مدیریت Theme با rules editor محدود و typed.
- چهار سؤال موجود (کاربرد، طول، قطر، بودجه) baseline پیشنهادی‌اند، نه تضمین صحت قواعد واقعی.
- rules فقط روی fieldهای allowlisted Woo و data contract اجرا شوند؛ query/PHP/JS دلخواه از settings اجرا نشود.
- evaluation از داده published/visible و خریدپذیری/stock policy مصوب؛ همه محصولات برای client دانلود نشوند.
- نتیجه همراه دلیل match و محدودیت‌ها؛ قیمت و availability هنگام add-to-cart دوباره توسط Woo بررسی شود.
- اطلاعات unknown برای شرط سخت، match مثبت محسوب نشود؛ accessory/variation handling صریح باشد.
- fallback config invalid یا no-match، هدایت به shop/مشاوره است؛ نباید گزینه‌ای را به‌اشتباه «مناسب قطعی» معرفی کند.
- پاسخ‌ها پیش‌فرض in-memory؛ ذخیره PII یا ایجاد lead خودکار ممنوع. endpoint عمومی، اگر انتخاب شد، input bounds و rate limit دارد.
- back/restart، focus عنوان مرحله، live result، empty/error/loading از contract باشند.

`NEEDS_DECISION` — قواعد و وزن‌ها، hard/soft constraints، بودجه، applicability، stock policy و endpoint mode: ND-R07 و ND-R14.

## 12. Search / Filter Architecture

`EXTRACTED — OWNER_CONFIRMED / Accepted` — header live search UI، archive controls و رفتار Theme-owned؛ category/attribute و product data Woo-native. ADR-006 این مرز را ثبت می‌کند.

`PROPOSED`

- فرم GET جست‌وجوی محصول SSR baseline؛ live results enhancement باشد، نه تنها راه پیدا کردن محصول.
- schema فیلتر بر پایه `product_cat` و global attributes مصوب؛ برای ویژگی‌هایی مانند طول/قطر قرارداد numeric term mapping بخش 4 استفاده شود.
- query vars، sort keys، term IDs و pagination allowlist و محدود شوند؛ direct SQL/پارامتر آزاد client ممنوع.
- title و SKU پوشش داده شوند؛ جست‌وجوی فارسی ک/ی، ارقام و نیم‌فاصله normalize شود، بدون تخریب SKU لاتین.
- semantics «۱۰ متر» از substring آزاد `1080p` جدا طراحی شود؛ فیلتر عددی authoritative از ویژگی typed بیاید.
- برای live search: debounce، حداقل طول و سقف نتایج مصوب، لغو پاسخ stale، keyboard navigation، accessible combobox/listbox در صورت استفاده از این الگو، Escape و no-result/error states.
- URL فیلترها shareable؛ clear-one/clear-all، result count و history behavior آزمون شوند. counts باید static/global یا contextual بودن را صریح نشان دهند.
- public projection فقط محصول visible و fieldهای عمومی؛ private/draft/PII خارج شوند.
- caching query با invalidation محصول/term؛ pagination و سقف workload برای catalog بزرگ.

`NEEDS_DECISION` — taxonomy کاربرد، naming، sort/filter semantics، facet counts، search weights و catalog scale: ND-R07، ND-R09، ND-R14. هیچ search/filter plugin dependency تصویب نشده است.

## 13. Forms Architecture

`EXTRACTED` — فرم‌های HTML فعلی فقط draft محلی تولید می‌کنند و handler backend ندارند. بدون اجرای JS، فرم‌های فاقد method/action می‌توانند GET شوند؛ این ریسک Discovery همچنان باز است.

`EXTRACTED — OWNER_CONFIRMED / Accepted` — Custom forms/UI متعلق به Theme؛ وابستگی به plugin فرم نباید بدون بررسی ضروری فرض شود. مشاوره رایگان، اجاره و تعمیر مسیرهای مکمل فروش هستند.

### گزینه‌های قابل بررسی، نه انتخاب‌شده

| گزینه `PROPOSED` | قابلیت و مزیت | هزینه/ریسک | وضعیت |
|---|---|---|---|
| Theme-native handler با WP APIs | POST امن با `admin-post` یا REST permission callback مناسب؛ `wp_mail`/مسیر تأییدشده؛ مستقل از plugin فرم | امنیت، spam، محدودیت نرخ، delivery، retry و نگهداری داده مسئولیت Theme می‌شود | candidate اصلی برای نیاز ساده؛ **هنوز Accepted نیست** |
| optional form integration adapter | اتصال به workflow پیچیده، CRM یا سرویس ارسال در صورت نیاز واقعی | وابستگی عملی integration، privacy و license؛ نباید custom product/settings را مالک شود | فقط با ADR تکمیلی و امکان نبود integration |
| draft-only | حفظ رفتار فعلی | درخواست واقعاً به کسب‌وکار نمی‌رسد | تنها اگر مالک برای یک فرم خاص تأیید کند؛ جایگزین آنلاین‌فروشی نیست |

`PROPOSED` — baseline الزامات مشترک handler، مستقل از گزینه نهایی:

- POST بدون PII در query؛ progressive enhancement و PRG؛ no-JS امن.
- validation server-side، نام/شماره/شرح bounded، normalize ارقام، escaping خروجی؛ client validation کافی نیست.
- nonce برای context مناسب ولی nonce عمومی به‌تنهایی anti-spam یا authentication نیست؛ honeypot/rate limit و logging حداقلی باید طراحی شود.
- recipient و mail headers از input کاربر گرفته نشوند؛ جلوگیری از header injection.
- success پس از acceptance واقعی توسط handler؛ قبول‌شدن `wp_mail` به معنی تحویل به inbox نیست و copy نباید آن را تضمین کند.
- تعیین consent، retention، visibility، notification، failure/retry و redaction قبل از ذخیره PII.
- بدون upload در نسخه پایه پیشنهادی؛ upload تنها با تصمیم و MIME/size/access policy مستقل.
- native handler با theme switch غیرفعال می‌شود؛ وابستگی عملیاتی و مسیر انتقال باید مستند باشد.

`NEEDS_DECISION` — مقصد ارسال، email/CRM، ذخیره lead یا عدم ذخیره، SLA، anti-spam، retention، upload و workflow خدمات: ND-R11. تعیین Theme-native در برابر integration در ADR-004 **Proposed** باقی می‌ماند.

## 14. SEO Architecture

`EXTRACTED — OWNER_CONFIRMED / Accepted` — SEO plugin optional integration است؛ نباید Core Featureهای Theme به آن وابسته شوند.

`PROPOSED`

- Theme: semantics، heading، breadcrumb UI، internal links، product/content presentation و compatibility hooks.
- WordPress: document title/canonical پایه و permalinkها از APIs؛ Woo: product semantics/data و structured data native.
- plugin SEO در صورت انتخاب: metadata/social/sitemap/schema ownership به‌صورت feature-specific تعیین شود؛ Theme تولید موازی schema/canonical نکند.
- بدون plugin نیز صفحات SSR، title و لینک‌های بومی معتبر باشند؛ fallback به معنای اختراع SEO engine کامل در Theme نیست.
- Cart/Checkout/Account و صفحات private/transactional index نشوند؛ noindex جای authorization نیست.
- filter/search queryها index policy صریح داشته باشند؛ category archive قابل‌خزش و pagination پایدار.
- redirect map از HTMLها؛ اعتبار تصاویر و ادعاهای قیمت/stock واقعی؛ ویدیوی demo به Product schema نسبت داده نشود.

`NEEDS_DECISION` — provider اختیاری SEO، مالک breadcrumbs/schema، query indexability، article metadata و routeها: ND-R09 و ND-R12.

## 15. Accessibility Architecture

`EXTRACTED` — baseline دارای skip link، focus-visible، label، native details/dialog و reduced-motion است؛ بعضی رنگ‌های متن کوچک contrast کمتر از 4.5:1 دارند. مرورگر/screen reader در Discovery اجرا نشده است.

`PROPOSED`

- هدف قابل آزمون WCAG 2.2 AA برای Core Flow، از جمله Checkout/Payment/Account.
- native semantics، heading hierarchy، keyboard-first navigation، focus return و dialog behavior حفظ/تکمیل شوند.
- Woo error notices و field associations، error summary، status announcements و focus پس از submit شکست‌خورده الزامی شوند.
- وضعیت پرداخت فقط با رنگ نمایش داده نشود؛ pending و success متن متمایز داشته باشند.
- live search و compare table قابل استفاده با keyboard/screen reader؛ fixed purchase bar focus/content را نپوشاند.
- RTL، mixed Latin identifiers، text zoom، 320px reflow، contrast و reduced-motion در QA.
- checkout mode/provider انتخابی از نظر مسیر redirect و بازگشت آزمون شود؛ نارسایی صفحه PSP خارج از Theme پنهان یا به‌عنوان pass ثبت نشود.

`NEEDS_DECISION` — browser/AT matrix، remediation scope و معیار تصویری: ND-R02 و ND-R13.

## 16. Performance Architecture

`EXTRACTED` — baseline محلی: 74,919 بایت CSS، 34,860 بایت JS؛ همه شش CSS و سه JS در هر ۲۰ صفحه لود می‌شوند؛ Google Fonts خارجی و تصاویر بدون srcset. این اعداد CWV یا transfer واقعی سایت Woo نیستند.

`PROPOSED`

- SSR، progressive enhancement، conditional assets، بدون SPA/page-builder dependency.
- media responsive از WordPress attachment API؛ dimensions صحیح و lazy loading با استثنای تصویر LCP.
- فونت محلی پس از تأیید license؛ عدم الزام به provider فونت خارجی.
- cache public archive/query/projections با invalidation؛ عدم cache عمومی cart/account/order/checkout.
- quiz و live search bounded و paginated؛ بدون دانلود کل catalog یا scan همه محصولات روی هر keystroke.
- register options بزرگ با autoload نامناسب انجام نشود؛ متادیتا lazy/read-on-demand؛ جلوگیری از N+1 queries.
- budgets پیشنهادی: LCP p75 ≤2.5s، INP p75 ≤200ms، CLS p75 ≤0.1؛ Theme CSS/page ≤30 KiB gzip، Theme JS/page ≤15 KiB gzip. بودجه کل Woo/gateway جداگانه پس از انتخاب stack اندازه‌گیری شود.
- scripts ضروری checkout/gateway به بهانه performance بی‌قید delay/dequeue نشوند.

`NEEDS_DECISION` — hosting/cache، حجم catalog، font/license، synthetic profile، field data و بودجه کامل: ND-R02، ND-R13، ND-R14، ND-R17.

## 17. Migration Strategy — HTML → WordPress

`PROPOSED` — migration باید native و Theme-owned باشد؛ ابزار اجرایی فعلاً ساخته نمی‌شود.

1. حفظ HTML/CSS/JS/assets فعلی به‌عنوان reference read-only؛ ثبت checksum و baseline visual/behavioral. generator/testهای معرفی‌شده در README هنوز غایب‌اند.
2. تأیید واقعی بودن داده، مجوز رسانه، ارز، stock و SKU؛ داده نمونه ابتدا draft/staging، نه published/purchasable.
3. ساخت manifest نگاشت legacy ID/file → WP/Woo ID و permalink؛ ثبت schema version/source digest؛ اجرای dry-run و گزارش خطا.
4. import رسانه و taxonomies با APIs native؛ mapping attachment/term IDs و alt/crop صحیح؛ عدم نسبت‌دادن ویدیوی عمومی به محصول خاص.
5. import Product با Woo CRUD؛ standard fields فقط native؛ custom presentation meta طبق بخش 4.
6. import سه راهنما به Posts و صفحات معرفی/خدمات/حقوقی به Pages؛ art variants به Theme post meta و read time محاسباتی.
7. جایگزینی چهار PDP با template مشترک؛ shop/category از Woo queries. custom data editorهای Theme باید بدون plugin field framework کار کنند.
8. ایجاد/اختصاص Cart، Checkout، Account و endpointهای Woo در راه‌اندازی مجاز؛ نه در هر frontend request. محتوا/شناسه صفحات موجود overwrite نشوند.
9. cart/comparison demo: `pro/g52/case/head` cart تجاری نیست؛ cart قدیمی به Order تبدیل نشود. compare selection فقط با نگاشت صریح ID، اجازه انتقال و visibility check قابل انتقال است.
10. gateway integration در محیط sandbox و سپس qualification؛ UI پرداخت با مرجع status واقعی Woo. no real charge در migration dry-run.
11. rewrite/redirect `.html` به permalink نهایی و تبدیل legacy queryها پس از approve map؛ بدون redirect URL خصوصی callback با قواعد عمومی.
12. اجرای مجدد import نباید محصول، سفارش، رسانه یا term تکراری بسازد؛ محتوای ویرایش‌شده مدیر بدون تأیید overwrite نشود.
13. backup DB/media و log بدون PII؛ rollback داده importشده با ownership map. پس از go-live، rollback Theme نباید سفارش/مشتری تازه را حذف یا DB تجاری را به عقب برگرداند.
14. migration نسخه‌های بعدی custom meta/options forward-compatible؛ تغییر Theme داده را حفظ کند؛ export برای portability.

### Mapping صفحه‌ها

| HTML موجود `EXTRACTED` | مقصد `PROPOSED` |
|---|---|
| index.html | WP front page + Theme sections |
| shop.html | Woo Shop + category archives |
| product.html / product-g52.html / product-case.html / product-head.html | چهار Product، یک PDP contract |
| cart.html | Woo Cart واقعی |
| checkout.html | Woo Checkout واقعی؛ دانلود demo جایگزین checkout نشود |
| compare.html / quiz.html | WP Pages + Theme-native capabilities |
| contact.html / rent.html / repairs.html | WP Pages + Theme form UI/handler مصوب |
| blog.html / academy.html | Posts index / curated landing طبق IA مصوب |
| guide-selection.html / guide-cable.html / guide-care.html | WP Posts |
| about.html / terms.html | WP Pages؛ متن حقوقی واقعی جداگانه تصویب شود |
| مرجع HTML ندارد | My Account، order endpoints، paid/failed/cancelled/pending views، search/404/pagination؛ design تکمیلی لازم است |

`NEEDS_DECISION` — baseline generator، import approvals، deployment topology، rollback و final URLs: ND-R09، ND-R16، ND-R17.

## 18. Plugin Dependency Matrix

| جزء | دسته | مالکیت داده/رفتار | وضعیت در نبود آن | وضعیت تصمیم |
|---|---|---|---|---|
| WordPress | Required platform | content/users/media/settings/APIs | runtime سایت وجود ندارد | `OWNER_CONFIRMED / Accepted` |
| WooCommerce | Required commerce plugin | products/cart/checkout/orders/customers/payment abstraction | Theme fatal ندهد؛ خرید غیرممکن و go-live مسدود | الزام `Accepted`؛ degraded behavior `PROPOSED` |
| Iranian Gateway provider integration | انتخاب Provider optional/pluggable؛ یک integration فعال برای go-live الزامی | verification/transaction/callback از طریق Woo gateway | هیچ پرداخت آنلاین معتبر؛ فروشگاه آماده انتشار نیست؛ fallback خاموش به COD ممنوع | معماری `Accepted`؛ Provider `NEEDS_DECISION` |
| SEO plugin | Optional integration | طبق ownership map SEO، نه Core Theme data | native WP/Woo + Theme semantics باقی بماند | optional بودن `Accepted`؛ انتخاب `NEEDS_DECISION` |
| Contact/form plugin | Optional integration | transport/workflow فقط در صورت نیاز مصوب | Core Theme UI/data باقی؛ delivery وابسته به mode منتخب باید صریح اعلام شود | ضرورت و provider `NEEDS_DECISION` |
| ACF Free / Pro | Forbidden | هیچ | هیچ مسیر runtime/import/admin به آن متکی نباشد | `OWNER_CONFIRMED / Accepted` |
| مشابه ACF / options/custom-field framework خارجی | Forbidden architectural dependency | هیچ custom data ownership | native Theme UI جایگزین | `OWNER_CONFIRMED / Accepted` |
| plugin اختصاصی compare/quiz/settings/metadata | Forbidden core-feature dependency | این قابلیت‌ها داخل Theme | چنین وابستگی ایجاد نشود | `OWNER_CONFIRMED / Accepted` |
| Page Builder برای Core Theme | Forbidden architectural dependency | هیچ Core Feature | layout/content baseline native باقی بماند | `OWNER_CONFIRMED / Accepted` |
| pluginهای ارسال/مالیات/OTP/cache و غیره | در فهرست مجاز فعلی تثبیت نشده | وابسته به نیاز | هیچ‌کدام خودکار ضروری فرض نشوند | `NEEDS_DECISION`؛ بررسی جداگانه dependency/ADR |

## 19. Security Boundaries

`EXTRACTED — OWNER_CONFIRMED / Accepted` — Theme صاحب پردازش مستقیم پرداخت یا credential درگاه نیست. Woo commerce APIs و WP identity مرز قطعی‌اند.

`PROPOSED`

| مرز اعتماد | الزامات |
|---|---|
| Browser → Theme endpoints | input typed/bounded، validation server-side، public/private projection مجزا، rate-limit برای abuse؛ nonce جای authentication نیست |
| Browser → Woo cart/checkout | قیمت/stock/totals از server؛ quantity/product/variation مجاز؛ endpoint standards حفظ شوند |
| Admin → Theme settings/meta | capability + per-object authorization + nonce، sanitize و schema validation؛ nonce به‌تنهایی مجوز نیست؛ autosave/revision handling مطابق editor |
| Theme → WP/Woo data | APIs native؛ output escaping مناسب HTML/attr/URL/JSON؛ بدون raw SQL/custom order table |
| Gateway → Woo callback | gateway verification، signature یا server verification متناسب provider، amount/currency/order matching، idempotency؛ Theme خارج از این trust boundary |
| Account → order/private data | owner/role checks native Woo/WP؛ جلوگیری از IDOR، private cache و leakage |
| Media/external video | MIME و دسترسی مجاز، allowlist embed/URL، no arbitrary server-side fetch/SSRF؛ upload طبق policy |
| Forms → delivery/storage | POST، no PII URL، recipient ثابت، spam protection، retention، redacted logs و failure handling |
| Theme lifecycle/migration | backup-first، idempotence، explicit privileges؛ عدم حذف خودکار داده یا rollback سفارش‌های live |

- Theme نه PAN/CVV/OTP پرداخت می‌گیرد و نه آن‌ها را log می‌کند؛ اطلاعات پرداخت حساس به مسیر gateway تعلق دارد.
- Tokens/credentials در options Theme، source، HTML، query string یا log قرار نگیرند؛ مدیریت gateway settings با خود integration.
- وضعیت paid از localStorage، redirect parameter یا پاسخ client-side ساخته نشود.
- HPOS/order-storage compatibility از طریق Woo order APIs؛ Theme نباید ساختار postmeta سفارش را فرض کند.
- رفتار بدون JS، network error، plugin غیرفعال و session expiry fail-safe باشد؛ هیچ‌کدام به معنی خرید/ارسال موفق مصنوعی نباشد.

## 20. Decision Registry / Remaining NDها و Gate ورود به Phase 5

تصمیمات قطعی مالک (عدم ACF، online sales، Iranian gateway abstraction، Theme ownership و P0 Account) باز نمی‌شوند. ND-R01 و ND-R02 اکنون OWNER APPROVED و ND-R03 برای Foundation بسته‌اند؛ شناسه‌ها برای traceability حفظ می‌شوند. دامنه تأیید ND-R02 انتخاب MySQL است؛ نسخه‌ها/Browser policy پیشنهادی بدون تغییر مانده‌اند. سایر NDهای مرتبط با قابلیت‌ها deferred هستند. Owner پس از Review، اجرای Foundation را صراحتاً مجاز کرده است؛ Gate تحویل §21 هنوز به evidence آزمون‌های runtime وابسته است.

| ID | تصمیم مشخص موردنیاز | وابستگی / زمان بستن |
|---|---|---|
| ND-R01 | OWNER APPROVED؛ Brand: توران تجارت؛ Technical Identity: torantejarat؛ جزئیات کامل §21 | ADR-011 به‌روز؛ هویت ثبت‌شده مجوز rename یا implementation نیست |
| ND-R02 | OWNER APPROVED؛ Database Support = MySQL؛ MariaDB خارج Support Matrix | نسخه‌های WP/Woo/PHP/MySQL و Browser policy قبلی PROPOSED؛ Compatibility Tested = No؛ product editor جزئی/hosting provider بعداً |
| ND-R03 | OWNER_CONFIRMED / بسته برای Foundation: Classic Theme، بدون FSE؛ Gutenberg برای content editing؛ rendering با PHP templates | دیگر blocker نیست؛ theme.json حداقلی فقط پیشنهاد implementation در §21، نه FSE |
| ND-R04 | ارز ذخیره/نمایش/درگاه، decimal، tax/shipping، billing/address requirements | پیش از commerce contract اجرایی؛ هیچ فرض تومان/ریال |
| ND-R05 | guest checkout، registration timing، email/phone identity، ضرورت OTP | پیش از Account/Checkout UI؛ Account P0 ثابت |
| ND-R06 | داده تأییدشده Product، simple/variable، stock/backorder، SKU، price approval | پیش از import و Product flows |
| ND-R07 | taxonomy کاربرد، attribute slugs/units/unknown states، numeric term convention، filter semantics | پیش از Product/Compare/Quiz data implementation |
| ND-R08 | schema نهایی story/video/limitations، bounds، editor controls، external video policy | پیش از custom metadata implementation |
| ND-R09 | URLها، query contract، redirects و blog/academy IA | پیش از routing و migration |
| ND-R10 | انتخاب/qualification provider، Checkout Blocks یا Classic بر اساس سازگاری واقعی | deferred به Commerce: mode قبل از checkout implementation، qualification قبل از go-live؛ blocker Foundation نیست |
| ND-R11 | مقصد، handler mode، retention، consent، email/CRM، spam/upload و workflow هر فرم | پیش از Forms implementation؛ plugin لازم فرض نمی‌شود |
| ND-R12 | SEO integration و ownership schema/canonical/breadcrumb/indexability | پیش از SEO implementation/انتشار |
| ND-R13 | QA Foundation در §21 برای Review نهایی پیشنهاد شده؛ visual/AT/performance نهایی بعداً | تأیید بسته QA §21 همراه Review نهایی؛ budget/UI نهایی blocker Foundation نیست |
| ND-R14 | catalog scale، compare persistence/share، quiz rules/weights، search tuning | پیش از این Featureها؛ owner همواره Theme |
| ND-R15 | الگوریتم/سرعت read time فارسی و شروط نمایش | پیش از read-time implementation؛ فیلد دستی بدون ضرورت تصویب نشود |
| ND-R16 | generator/migration/import/export/rollback و schema triggers | deferred به migration؛ Foundation از HTML فعلی فقط reference read-only می‌گیرد و generator غایب را اجرا نمی‌کند؛ blocker Foundation نیست |
| ND-R17 | حقوق رسانه و فونت، اطلاعات تماس، policies واقعی، deploy/caching و operational readiness | پیش از داده و انتشار مرتبط |
| ND-R18 | Persian-only یا چندزبانه، تقویم ورودی/نمایش اجاره، timezone و سیاست ارقام/ترجمه | پیش از I18N و فرم تاریخ‌دار؛ Persian-first/RTL قطعی است |

### Ratification gate

`OWNER_CONFIRMED` — اجرای Phase 5 Foundation اکنون مجاز است؛ پذیرش/تحویل نهایی بدون ارزیابی F-ACها مجاز نیست. وضعیت: STARTED — QA INCOMPLETE.

- این سند برای Review است؛ مالک هنوز جزئیات `PROPOSED` را تصویب نکرده است.
- مجوز فعلی Owner فقط scope Foundation و طرح اجرایی حداقلی این قرارداد را پوشش می‌دهد؛ هر تصمیم معماری جدید یا گسترش scope نیازمند توقف و Decision Request است.
- NDهای deferred باید owner، milestone و اثر آن‌ها ثبت شود؛ deferred بودن اجازه حدس‌زدن implementation نیست.
- تعریف Phase 5 اکنون طبق تصمیم مالک در §21 تثبیت شده: Foundation فقط. محدودیت تاریخی این مرحله، با دستور Phase 6 مانع ادامه migration نیست؛ پذیرش نهایی همچنان به evidence وابسته است.
- قبل از go-live، همه P0 flowها و provider فعال باید acceptance واقعی داشته باشند؛ موفقیت syntax یا docs check جای آزمون سفارش/پرداخت را نمی‌گیرد.

### آزمون‌های پذیرش معماری پیشنهادی — فازهای قابلیت، نه شرط تحویل Foundation

- Theme custom field/settings/compare/quiz بدون ACF، feature plugin و page builder قابل اجرا/مدیریت باشد.
- standard Product values در Theme mirror نشوند؛ تغییر قیمت/stock در Woo در همه UIها اثر کند.
- فعال‌سازی gateway سازگار دوم فقط از Woo integration/settings، بدون patch Core Theme.
- fake success URL، callback تکراری/دیررس، payment failed/cancelled/pending، retry و paid state صحیح آزمون شوند؛ آزمون verification متعلق به gateway integration است.
- کاربر A سفارش کاربر B را با تغییر ID نبیند؛ empty history و login/reset states پوشش داده شوند.
- فرم no-JS هیچ PII در URL نگذارد؛ پیام success مطابق نتیجه واقعی باشد.
- import دوم duplicate نسازد؛ theme switch داده را حذف نکند؛ rollback سفارش live را از بین نبرد.
- WC dependency missing و optional integration missing به‌صورت امن و بدون موفقیت کاذب مدیریت شود.

---

## 21. Foundation Ratification — قرارداد محدود Phase 5

### A. Foundation Ratification

`EXTRACTED / OWNER_CONFIRMED` — Phase 5 فقط Foundation قابل اتکا برای فازهای بعد است. Classic WordPress Theme، بدون FSE؛ content editing با Gutenberg ولی rendering/Core Theme به plugin یا editor runtime گوتنبرگ وابسته نباشد. جداسازی WP content / Woo commerce / Theme custom features و scope/responsibility قبلی ثابت است؛ Brand/Technical Identity، Database Support و Credits فقط طبق تصمیم جدید Owner در همین نسخه به‌روز شده‌اند.

**Conflict check:** نسخه 0.2 صریحاً می‌گفت Phase 5 تعریف نشده است؛ تعریف مصوب متفاوتی پیدا نشد. بنابراین Conflict با scope قبلی وجود ندارد. گزینه قبلی Block Theme صرفاً Proposed بود و اکنون با تصمیم مالک برای این نسخه کنار گذاشته شده است؛ یافته‌های Review Report قبلی به‌عنوان snapshot تاریخی حفظ شده‌اند؛ فقط Credits آن طبق تصمیم جدید اصلاح و ارجاع وضعیت جاری افزوده شده است. ND-R18 فقط به Summary اضافه شد؛ معنای تصمیم تغییر نکرد.

### B. Owner Decisions — ND-R01 / ND-R02

#### ND-R02 / Platform Baseline

**ND-R02 = OWNER APPROVED**؛ **Database Support = MySQL** طبق تصمیم صریح Owner. MariaDB خارج Support Matrix پروژه است، مگر با تصمیم جدید Owner.

**دامنه تأیید:** وضعیت بسته طبق دستور Owner نهایی است؛ انتخاب قطعی جدید خانواده Database است. نسخه‌های پیشنهادی قبلی و Browser policy بدون تغییر PROPOSED می‌مانند؛ این ثبت نه تصویب خودکار عدد نسخه‌هاست و نه Compatibility Approval.

| Platform / مورد | تصمیم یا پیشنهاد جاری | وضعیت | Compatibility Tested |
|---|---|---|---|
| Database Support | MySQL | OWNER APPROVED | No |
| WordPress minimum | 6.9؛ patch به‌روز در QA آینده | PROPOSED — بدون تغییر | No |
| WooCommerce minimum | 10.8؛ patch به‌روز در QA آینده | PROPOSED — بدون تغییر | No |
| PHP minimum | 8.3؛ patch به‌روز، syntax مطابق floor | PROPOSED — بدون تغییر | No |
| MySQL minimum | 8.4 LTS؛ patch به‌روز | PROPOSED — عدد نسخه بدون تغییر | No |
| Browser support policy | دو نسخه stable اخیر Chrome/Edge/Firefox؛ Safari macOS و iOS در دو major اخیر پشتیبانی‌شده؛ Android Chrome در دو stable اخیر؛ IE و WebViewهای قدیمی خارج پشتیبانی تضمین‌شده | PROPOSED — بدون تغییر | No |

**منابع از پیش ثبت‌شده، نه ادعای compatibility پروژه:**

- [WordPress requirements](https://wordpress.org/about/requirements/) هنگام بررسی PHP 8.3+ و MySQL 8.0+ را توصیه می‌کرد؛ اشاره upstream به MariaDB 10.11+ مجوز ورود آن به Support Matrix پروژه نیست.
- [WooCommerce server recommendations](https://woocommerce.com/document/server-requirements/) راهنمای WooCommerce 10.8+ با توصیه WP 6.9+، PHP 8.3+، MySQL 8.0+، HTTPS و memory limit حداقل 256MB بود؛ توصیه upstream به MariaDB 10.6+ نیز scope پروژه را تغییر نمی‌دهد.
- [MySQL 8.4 LTS release model](https://dev.mysql.com/doc/refman/8.4/en/mysql-releases.html) شاخه 8.4 را LTS معرفی می‌کند. floor برابر 8.4 پیشنهاد قبلی پروژه است؛ نه minimum اجباری WordPress/Woo.
- منابع قبلاً در 2026-09-21 خوانده شدند؛ این مرحله بررسی آنلاین یا تست تازه ندارد. اعداد نه ادعای آخرین release هستند و نه certification نسخه نصب‌شده. repository هنوز runtime یا compatibility evidence برای این baseline ندارد.

`PROPOSED` — اجرای QA آینده فقط روی MySQL، با ثبت WP/Woo/PHP/MySQL و patch/browserهای دقیق؛ ترکیب floor و ترکیب به‌روز پشتیبانی‌شده طبق پیشنهاد قبلی. آزمون دوخانواده‌ای MySQL/MariaDB دیگر در scope نیست. نسخه‌های future خودکار supported اعلام نشوند. HTTPS، UTF-8/utf8mb4 و حافظه مناسب Woo در fixture آینده لحاظ شود. در اجرای فعلی هیچ runtime/dependency نصب نشده و هیچ compatibility test اجرا نشده است؛ PHP/WP/Woo/MySQL/browser موجود نیستند. Source checks جای آن‌ها را نمی‌گیرند.

#### ND-R01 / Technical Identity

**ND-R01 = OWNER APPROVED**

`OWNER_CONFIRMED` — Brand **توران تجارت**؛ Legal Entity **دید برتر توران تجارت**؛ National ID `14011384991`؛ Author و Designer هر دو **یعقوب طیبی**.

| مورد | مقدار نهایی — OWNER APPROVED |
|---|---|
| Theme slug | `torantejarat` |
| PHP namespace | `ToranTejarat\Theme` |
| Text domain | `torantejarat` |
| Function prefix | `torantejarat_` |
| Constant prefix | `TORANTEJARAT_` |
| Asset handles | `torantejarat-*` |
| CSS namespace | `.torantejarat-*` |

اصل تفکیک هویت فنی از Brand Identity در ADR-011 حفظ می‌شود؛ نام‌های بالا صراحتاً توسط Owner انتخاب شده‌اند. `*` الگوی suffix است، نه نام literal. هیچ custom-hook/global-JS identity جدیدی تصویب یا اختراع نمی‌شود. طرح اجرایی بخش C اکنون طبق مجوز Owner در حد مصرف واقعی Foundation اجرا شده است: source نسخه `0.1.0`، بدون rename فایل‌های قبلی یا تولید بسته توزیع. نسخه‌های platform و Browser policy همچنان PROPOSED می‌مانند.

**تاریخچه — نه هویت جاری:** «شلنگ‌بین» فقط نام قبلی Project/Concept بود؛ `didbarttar` فقط سابقه Temporary Repository/Project Identity است، نه هویت فنی Theme. `shalangbin` انتخاب نشده است. وضعیت تعلیق برند و بسته نام‌گذاری قبلی با تصمیم جدید Owner جایگزین شد. فایل‌های مرجع HTML/JS قدیمی در این مرحله دست‌نخورده‌اند.

**سابقه Phase 5 (نه gate جاری migration):** اجرای Foundation طبق دستور مستقیم Owner انجام شد؛ source محدود و QA حداقلی ایجاد شده‌اند. ND-R01 و ND-R02 = OWNER APPROVED؛ Phase 5 = STARTED — QA INCOMPLETE. معیارهای runtime به علت نبود محیط واقعی NOT TESTED هستند؛ در آن مرحله فاز بعد مجاز نبود؛ اکنون فقط دامنه migration طبق دستور Phase 6 توسعه یافته است.

Provider مشخص، handler فرم، read time، Compare/Quiz، Product Metadata schema، Product editor خاص، UI نهایی و محتوای واقعی همچنان blocker عمومی Foundation نیستند؛ در فاز مربوط تعیین می‌شوند. scope/responsibility و P0ها تغییر نکرده‌اند.

### C. Phase 5 Scope

#### IN — OWNER_CONFIRMED

- ایجاد ساختار نهایی Theme و bootstrap.
- namespace، text domain و قرارداد version/asset naming.
- asset loading، CSS و JS architecture پایه.
- template hierarchy پایه Classic.
- WordPress/WooCommerce integration boundaries.
- Theme-native Settings/Data foundation فقط به اندازه نیاز Foundation.
- پایه RTL، accessibility، security و performance.
- development conventions، coding standards و minimum QA infrastructure.

#### OUT — OWNER_CONFIRMED

- migration کامل Product یا داده واقعی.
- Shop UI و PDP نهایی.
- Compare و Quiz.
- Cart UI، Checkout UI و My Account UI نهایی.
- Payment Gateway implementation یا نصب/اتصال Provider برای فروش واقعی.
- Forms و Search implementation.
- SEO implementation.
- Blog/Academy UI.
- نهایی‌سازی Design System و تولید محتوای واقعی.

#### Implemented Foundation structure — OWNER AUTHORIZED؛ runtime هنوز آزموده نشده

```text
theme/torantejarat/
  README.md
  style.css                 # metadata؛ source version 0.1.0
  functions.php
  index.php
  header.php
  footer.php
  page.php
  single.php
  archive.php
  404.php
  inc/
    bootstrap.php
    setup.php
    assets.php
    woocommerce.php         # detection/support/main wrappers/admin notice only
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
  foundation-static.mjs      # Node built-ins؛ source checks نه runtime
  foundation-smoke.php       # روی WP fixture واقعی؛ هنوز اجرا نشده
  fixtures/
    foundation-baseline.json # وضعیت و hashes قبل از اجرا
    content.html             # fixture مصنوعی QA، نه محتوای تجاری
```

فقط فایل با مصرف واقعی ایجاد شد: JS، Settings file/page، languages catalog خالی و theme.json بدون نیاز editor-specific ساخته نشدند. Gutenberg با native rendering و `add_editor_style()` کار می‌کند؛ FSE یا Woo template override وجود ندارد. Header/footer shell فاقد UI نهایی است. Source کامل و دستورهای آزمون در Theme README و tests/README مستند شده‌اند.

| محور | قرارداد اجرایی حداقلی مورد مجوز Owner |
|---|---|
| Bootstrap | functions.php entry کوتاه؛ فایل‌های داخلی namespaced با require صریح؛ WP hooks در زمان صحیح؛ بدون service container/autoloader dependency |
| Editor / rendering | Gutenberg بومی برای محتوا؛ render از PHP templates و `the_content()`؛ plugin Gutenberg یا JS editor frontend requirement نباشد؛ Theme با محتوای block و classic HTML fixture کار کند |
| Assets | native enqueue؛ مسیرها از WP APIs؛ version واحد؛ conditional editor/frontend assets؛ بدون hard-code localhost/CDN یا بارگذاری app.js/catalog.js نمونه در Theme |
| CSS | layers/folders روشن base/layout/components/editor؛ logical properties و RTL-first؛ neutral shell، نه بازتعریف نهایی رنگ/spacing؛ فایل minified source بدون نیاز build pipeline خارجی اجباری نشود |
| JS | vanilla، progressive enhancement، محدود به shell؛ no jQuery/page-builder dependency خودساخته؛ هیچ compare/quiz/search/cart logic حتی stub اجرا نشود |
| Woo boundary | اعلام support با API native و feature detection؛ نبود Woo باعث fatal نشود و اطلاع مدیر داشته باشد؛ هیچ product/order/cart recreation، custom checkout route یا template override در Phase 5 |
| Data/settings | API boundary و naming/default/validation conventions؛ در نبود setting واقعی Foundation، option/meta registration یا admin page ساخته نشود. برای core identity از WP native APIs. schema registry عمومی، migrations، exporters، product panels و generic CRUD ممنوع در این فاز |
| RTL / A11Y | `language_attributes()`، semantics/landmarks، skip link، keyboard focus، خوانایی پایه، logical spacing و reduced-motion برای حرکت احتمالی؛ محتوای placeholder fixture فقط برای QA |
| Security | context escaping، URL validation، guard مستقیم فایل‌های داخلی، عدم secret/PII persistence؛ هیچ public endpoint، callback، form handler یا payment HTTP client |
| Performance | assetهای ضروری shell فقط؛ بدون third-party runtime جدید؛ editor CSS در frontend نشت نکند؛ هیچ catalog preload یا heavy init در هر request |
| Coding conventions | WordPress Coding Standards برای PHP/HTML/CSS/JS به اندازه scope؛ i18n همه رشته‌های UI با text domain مصوب؛ namespaced PHP و prefix برای globals/hooks؛ feature code در functions.php انباشته نشود |
| QA infrastructure | PHP CLI lint، syntax check برای JS در صورت وجود، smoke runner سبک WP با assertions نام‌دار و exit code؛ fixture خارج production و قابل تکرار؛ browser checklist با viewport/نسخه ثبت‌شده؛ بدون dependency test framework جدید در این Ratification |

#### Acceptance Criteria — F-ACهای مبنای اجرای مجاز؛ نتایج در گزارش Phase 5

| ID | شرط قابل اثبات |
|---|---|
| F-AC-01 | package Theme با metadata صحیح (نام فنی و min versions مصوب، Author و Author URI رسمی) روی fixture پلتفرم مصوب نصب و فعال شود؛ صفر fatal/notice ناشی از Theme در WP_DEBUG |
| F-AC-02 | Classic Theme شناسایی شود؛ هیچ FSE template یا Page Builder/ACF/feature-plugin requirement وجود نداشته باشد؛ frontend بدون plugin Gutenberg اجرا شود |
| F-AC-03 | index/page/single/archive/404 روی content fixture استاندارد render شوند؛ wp_head/wp_footer/body hooks و the_content حذف نشده باشند؛ حالت empty و عنوان بلند صفحه از shell خارج نشوند |
| F-AC-04 | همه handleها، functions/globals و text domain با بسته identity مصوب سازگار؛ collision عمومی مشاهده نشود؛ asset URL از WP APIs باشد |
| F-AC-05 | assets محلی 200 بدهند، duplicate enqueue و console error Theme صفر؛ editor stylesheet در frontend لود نشود؛ تغییر version release cache key را عوض کند |
| F-AC-06 | نبود Woo، optional pluginها و محتوای Gutenberg باعث fatal نشود؛ Woo موجود support را ببیند؛ کد Theme هیچ data write به price/stock/order/cart یا HTTP request به PSP انجام ندهد؛ فعالیت native Woo در محیط تست به‌اشتباه write اختصاصی Theme شمرده نشود |
| F-AC-07 | fa-IR/RTL و fixture لاتین در 320/768/1280px بدون overflow ناخواسته shell؛ keyboard skip/focus قابل مشاهده؛ modal/interaction پیچیده‌ای برای فاز بعد زودتر ساخته نشده باشد |
| F-AC-08 | title/attribute/URL و سایر ورودی‌های پویا در context مناسب escape/validate شوند؛ fixture ورودی نامعتبر در این contextها execution ایجاد نکند. `the_content()` از مسیر استاندارد فیلتر/مجوز WP عبور کند، نه plain-text escaping کل محتوای Gutenberg؛ هیچ secret/PII/demo cart توسط Theme ذخیره نشود |
| F-AC-09 | option/meta/admin page و schema جدید بدون consumer واقعی Foundation صفر؛ در صورت نیاز واقعی، key/type/default/capability/validation قبل از همان implementation مستند و تست شود؛ Product metadata صفر |
| F-AC-10 | `php -l` همه PHPها، JS syntax checks برای فایل‌های موجود و smoke assertions نام‌دار همگی موفق؛ scripts تست exit غیرصفر هنگام خطای واقعی بدهند؛ test framework جدید بدون تأیید dependency اضافه نشود |
| F-AC-11 | network/asset inventory پایه ثبت شود؛ third-party font/script و catalog download جدید توسط Theme صفر؛ CWV فروشگاه نهایی در این فاز pass اعلام نشود |
| F-AC-12 | diff allowlist: Theme Foundation و QA حداقلی مجاز پس از approval؛ HTML/CSS/JS/assets مرجع فعلی و snapshot Review حفظ؛ هیچ Feature یا migration/Provider/handler خارج IN ایجاد نشده باشد |

متن معیارها تغییر نکرده است؛ هر PASS نیازمند evidence است. نتیجه اجرا در [PHASE-5-FOUNDATION-REPORT.md](PHASE-5-FOUNDATION-REPORT.md): F-AC-09 و F-AC-12 با source/diff checks PASS؛ سایر معیارها NOT TESTED به علت نبود PHP/WordPress/Woo/MySQL/browser. Node source/syntax checks اجرا شده‌اند، نه compatibility یا PHP lint. WPCS به‌صورت خودکار اجرا نشده و هیچ test framework، runtime یا dependency جدیدی نصب نشده است.

### D. Updated ADR Status

- **ADR-001: Accepted** — Classic Theme + Gutenberg editing، بدون FSE و بدون dependency editor در Core rendering؛ rationale کوتاه ثبت شد. theme.json حداقلی implementation proposal است، نه پذیرش FSE.
- **ADR-002: Accepted، مرز قبلی ثابت** — Theme type دیگر ND نیست؛ integrationهای آینده مانع Foundation نیستند.
- **ADR-003: Accepted، ownership ثابت** — اجرای کامل Data Layer/Product metadata به فاز مربوط deferred؛ فقط حداقل Foundation در §21.
- **ADR-005: Accepted، مرز قبلی دقیق‌تر** — Theme presentation / Woo lifecycle / Gateway transaction؛ اصلاح activity label، بدون انتخاب Provider یا Checkout mode.
- **ADR-010: Accepted برای اجرای Foundation** — native assets و QA حداقلی طبق مجوز Owner؛ performance/design نهایی و compatibility همچنان تأیید نشده‌اند.
- **ADR-011: Accepted، به‌روز طبق Owner** — Brand توران تجارت، Technical Identity torantejarat؛ اصل تفکیک هویت حفظ شد؛ ND-R02 با MySQL مصوب، اجرای Foundation مجاز و انجام شده؛ compatibility آزموده نشده است.
- سایر ADRها از نظر تصمیم و scope خود تغییر نکرده‌اند؛ ADR-004 Forms همچنان Proposed و خارج Phase 5 است.

### E. Final Gate

**Phase 5 = STARTED — QA INCOMPLETE**. مجوز مستقیم Owner برای Foundation جای توقف قبلی NOT STARTED را گرفته است. Source Theme و QA حداقلی ایجاد شده‌اند؛ **Compatibility Tested = No**. PHP/WordPress/WooCommerce/MySQL/browser در محیط فعلی نیستند، بنابراین runtime acceptance و release readiness اعلام نمی‌شود.

scope فقط Foundation است. هیچ dependency نصب، rename legacy، Product migration، payment implementation، UI نهایی یا فاز بعد اجرا نشده است. اجرای موارد مبهم/خارج scope همچنان نیازمند توقف و Decision Request است. [گزارش اجرا](PHASE-5-FOUNDATION-REPORT.md) فهرست فایل‌ها، evidence و F-AC-01 تا F-AC-12 را ثبت می‌کند.

---

# FINAL DECISION LOG

## A. تصمیم‌های قطعی مالک — EXTRACTED / OWNER_CONFIRMED / Accepted

| ID | تصمیم قطعی | اثر الزام‌آور | ADR |
|---|---|---|---|
| FD-01 | ACF Free/Pro و وابستگی مشابه مدیریت custom data ممنوع | هیچ field/options/editor/import متکی به این ابزارها طراحی نشود | ADR-002، ADR-003 |
| FD-02 | تمام Featureهای اختصاصی Theme در خود Theme | compare، quiz، recommendation، settings، custom metadata/UI با native APIs؛ بدون feature plugin اختصاصی | ADR-002، ADR-003 |
| FD-03 | WordPress مالک محتوای هسته است | Posts/Pages/Users/Media/core taxonomies/settings native | ADR-003 |
| FD-04 | WooCommerce مالک Commerce است | Product/price/stock/cart/checkout/order/customer/attributes/gallery native؛ بدون duplication | ADR-003، ADR-005 |
| FD-05 | فروش آنلاین واقعی و conversion اصلی خرید | Lead-only رد؛ مسیر مکمل مشاوره/اجاره/تعمیر جای خرید نیست | ADR-005 |
| FD-06 | پرداخت آنلاین ایرانی از Woo Gateway abstraction | Provider-agnostic؛ بدون provider hard-code؛ COD مدل اصلی نیست | ADR-005 |
| FD-07 | Theme پردازش مستقیم payment ندارد | بدون PSP HTTP/verify/callback/credential logic در Core Theme | ADR-005 |
| FD-08 | WP و Woo required؛ gateway/SEO/form integrations قابل انتخاب | provider خاص اجباری نیست؛ حداقل یک gateway آنلاین آزموده برای go-live لازم | ADR-002، ADR-005 |
| FD-09 | Checkout/Payment/Confirmation/Account/History و سه نتیجه پرداخت P0 | اختیاری یا Phase بعد نامعلوم تلقی نشوند | ADR-008 |
| FD-10 | Product Data Contract native | custom video/limitation/story در Theme؛ compare ترجیحاً attributes؛ rules/settings/variant Theme-native | ADR-003، ADR-006 |
| FD-11 | read time محاسبه خودکار Theme | field دستی تنها با ضرورت و تصمیم جدید | ADR-003 |
| FD-12 | ضرورت plugin فرم هنوز قطعی نیست | feasibility native handler بررسی شود؛ انتخاب نهایی باز | ADR-004 |
| FD-13 | filters با taxonomy/attributes Woo هماهنگ | dataset اختصاصی فیلتر/قیمت/ویژگی به‌عنوان مرجع دوم ممنوع | ADR-006 |
| FD-14 | مجوز مستقیم Owner برای اجرای Foundation محدود | توقف قبلی پس از Review با دستور اجرا جایگزین شد؛ هیچ مجوز Feature یا فاز بعد نیست | gate این سند؛ گزارش Phase 5 |
| FD-15 | اطلاعات رسمی Project Identity / Credits | **Author:** یعقوب طیبی؛ **Designer:** یعقوب طیبی؛ **Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)؛ حفظ همین اطلاعات در مستندات نهایی | بخش Project Metadata این قرارداد؛ تصمیم metadata، نه ADR معماری |
| FD-16 | Phase 5 فقط Foundation | IN/OUT طبق §21؛ UI نهایی و Featureها خارج scope؛ اجرای محدود اکنون مجاز است | §21 |
| FD-17 | Classic WordPress Theme؛ بدون FSE در این نسخه | template hierarchy کلاسیک؛ Block Theme گزینه Foundation نیست | ADR-001 |
| FD-18 | Gutenberg editor اصلی محتوا، نه dependency اجرای Core | content editing با Gutenberg؛ Theme rendering با templates؛ commerce با Woo؛ features Theme-native | ADR-001 |
| FD-19 | Data Layer Foundation حداقلی | مالکیت/API contract حفظ؛ Product Metadata و data/migration framework کامل در Phase 5 ساخته نشود | ADR-003، §21 |
| FD-20 | Brand و Technical Identity نهایی | توران تجارت؛ torantejarat و نام‌های مصوب §21؛ ND-R01 = OWNER APPROVED؛ بدون rename اجرایی | ADR-011 |
| FD-21 | Database Support نهایی | فقط MySQL؛ MariaDB خارج Support Matrix؛ ND-R02 = OWNER APPROVED؛ Compatibility Tested = No و نسخه‌ها/policy همچنان PROPOSED | §21، ADR-011 |

## B. وضعیت باقی‌مانده — جزئیات پیشنهادی و تصمیم‌های فازهای بعد

| گروه | شناسه‌ها | وضعیت / جزئیات باقی‌مانده |
|---|---|---|
| هویت فنی/پلتفرم | ND-R01، ND-R02 | هر دو OWNER APPROVED؛ torantejarat و MySQL مصوب؛ نسخه‌ها/Browser policy قبلی PROPOSED؛ Compatibility Tested = No؛ ND-R03 بسته است |
| Commerce و customer | ND-R04 تا ND-R06، ND-R10 | ارز/ارسال/مالیات، account policy، محصول واقعی، provider و checkout mode |
| مدل custom و query | ND-R07، ND-R08، ND-R14، ND-R15 | schema/keyهای نهایی، rules، persistence، search و read-time algorithm |
| URL/فرم/SEO/localization | ND-R09، ND-R11، ND-R12، ND-R18 | routeها، handler/delivery/retention، SEO ownership و سیاست زبان/تاریخ/ارقام؛ deferred به فاز مرتبط |
| کیفیت/مهاجرت/انتشار | ND-R13، ND-R16، ND-R17 | test/budget، generator/import/rollback، license و operational policies |

**وضعیت جاری: Phase 6 migration با دستور جدید Owner شروع شده؛ QA runtime ناقص و Compatibility Tested = No است. فعال‌سازی/qualification پرداخت و release هنوز انجام نشده‌اند.**

## 22. Phase 6 — مجوز migration، بدون تصویب schema جدید

Owner: Convert, Don't Redesign؛ ترتیب shell/assets → core templates → Woo presentation → custom pages. کد فعلی و وضعیت واقعی هر template در گزارش Phase 6 آمده است. فایل‌های source اصلی و Review تاریخی immutable می‌مانند؛ CSS/media در Theme کپی و classها namespace می‌شوند. mode یا صفحه Woo به‌صورت خودکار ساخته/تغییر داده نمی‌شود. theme تنها `the_content()` صفحات موجود و endpointهای Woo را ارائه می‌کند؛ این به معنی انتخاب هم‌زمان دو checkout mode نیست.

فیلدهای Woo/WP native اکنون مصرف می‌شوند؛ ND-R07/08/09/10/11/14/15/17/18 فقط قسمت وابسته را محدود می‌کنند. QA قبلی محفوظ است و QA مرحله migration معیارهای جدید دارد؛ شکست assertionهای Foundation-only در محصول توسعه‌یافته به معنی پاک‌کردن تاریخچه یا PASS بودن runtime نیست.
