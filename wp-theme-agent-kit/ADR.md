# ADR — Architecture Decision Records

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

مرجع معماری هدف: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md)، نسخه 0.6، migration با مجوز جدید Owner، تاریخ 2026-09-22.

سابقه Foundation طبق [Final Decision Sheet](FINAL-DECISION-SHEET.md): **ND-R01 = OWNER APPROVED** برای Brand توران تجارت و هویت فنی `torantejarat`؛ **ND-R02 = OWNER APPROVED** با **Database Support = MySQL**؛ MariaDB خارج Support Matrix است. اعداد نسخه‌ها و Browser policy قبلی PROPOSED باقی‌اند و **Compatibility Tested = No**. **Phase 5 = STARTED — QA INCOMPLETE** طبق مجوز مستقیم Owner؛ نتایج در [گزارش اجرا](PHASE-5-FOUNDATION-REPORT.md). جزئیات Product/hosting از ND-R02 همچنان deferred هستند؛ تأیید identity/database به‌تنهایی مجوز نبود؛ آن دستور فقط Foundation را مجاز کرد؛ دستور بعدی Owner اکنون Phase 6 migration را هم مجاز کرده است.

## Status و دامنه پذیرش

- `Accepted`: فقط تصمیم صریح مالک یا تصمیم بعداً تصویب‌شده؛ به معنی implementation یا مجوز Phase 5 نیست.
- `Proposed`: نیازمند Review؛ هیچ انتخاب consequential از آن بدون تأیید اجرا نشود.
- `Rejected` / `Superseded`: سابقه حفظ شود؛ پیشنهاد ردشده دوباره به‌عنوان پیش‌فرض استفاده نشود.
- `EXTRACTED — OWNER_CONFIRMED` منبع تصمیم‌های پذیرفته‌شده این Ratification است؛ `PROPOSED` جزئیات فنی پیشنهادی و `NEEDS_DECISION` موارد بازند.
- تمام ADRهای بعدی باید Dependency Policy و Responsibility Matrix قرارداد را رعایت کنند. ACF، core-feature plugin dependency، catalog تجاری موازی یا PSP processing داخل Theme گزینه اجرایی نیستند.

## تطبیق شناسه‌های Discovery با Registry فعلی

`EXTRACTED` — پیش از این Ratification، این فایل فقط الگوی ADR داشت و هیچ ADR Accepted ثبت نشده بود. گزارش Discovery هشت candidate با شماره‌هایی متفاوت از عناوین ارجاع‌شده در درخواست جدید داشت. برای جلوگیری از ابهام، ارجاع تاریخی گزارش با پیشوند `DISC-ADR` نگهداری می‌شود؛ از این نسخه، شناسه‌های بدون پیشوند canonical هستند و شماره‌های 003 تا 006 با درخواست مالک منطبق‌اند.

| ارجاع تاریخی گزارش | عنوان آن در گزارش | ارجاع canonical / سرنوشت |
|---|---|---|
| DISC-ADR-001 | نوع Theme و تجربه ویرایش | ADR-001؛ پیشنهاد تاریخی بود، اکنون Classic + Gutenberg طبق تصمیم مالک Accepted؛ گزینه FSE کنار گذاشته شد |
| DISC-ADR-002 | مرجع حقیقت تجارت | ADR-003 + ADR-005؛ مرجع Woo Accepted، جزئیات فنی باز |
| DISC-ADR-003 | مرز Theme و قابلیت‌های سایت؛ پیشنهاد plugin-owned features | ADR-002 + ADR-003؛ پیشنهاد plugin مالک Core Features **Superseded / غیرمجاز طبق تصمیم مالک** |
| DISC-ADR-004 | Cart/Checkout و سیاست Override | ADR-005؛ Blocks/Classic هنوز ND، فروش آنلاین قطعی |
| DISC-ADR-005 | مدل محتوا و taxonomy | ADR-003 + ADR-006؛ همان شماره برای Checkout به‌طور پنهانی بازتفسیر نشود |
| DISC-ADR-006 | Asset و Design System | ADR-010؛ فقط Foundation با مجوز اجرا Accepted؛ Design System نهایی deferred |
| DISC-ADR-007 | URL و مالکیت SEO | ADR-007؛ همچنان Proposed |
| DISC-ADR-008 | Localization و واحدهای تجاری | ADR-009؛ جزئیات هنوز باز |

`EXTRACTED` — ACF، plugin اختصاصی Features، handler فرم یا پرداخت در checkout فعلی وجود ندارد؛ این اصلاح، قرارداد معماری آینده است، نه ادعای حذف implementation موجود.

## ADR-001 — Classic Theme / Gutenberg Editing

- **Status:** Accepted — تصمیم صریح مالک در Foundation Ratification
- **Date:** 2026-09-21
- **Context / EXTRACTED:** پروژه WooCommerce، Persian-first/RTL و نیازمند کنترل دقیق markup/template است؛ انتخاب Theme type در نسخه پیشین Proposed بود.
- **Decision / OWNER_CONFIRMED:** Foundation یک **Classic WordPress Theme** است؛ FSE/Block Theme در این نسخه وارد نمی‌شود. Gutenberg editor اصلی content است، اما Theme rendering با PHP templates، Commerce با Woo و Theme Features به‌صورت Theme-native اجرا می‌شوند؛ plugin/editor runtime Gutenberg dependency اجرای Core نیست.
- **Rationale:** template hierarchy کلاسیک کنترل مستقیم markup و integration با Woo را فراهم می‌کند؛ Gutenberg نقش ویرایش محتوا را نگه می‌دارد، بدون واگذاری shell به Site Editor یا Page Builder.
- **Alternatives:** گزینه قبلی FSE/Block Theme برای این نسخه کنار گذاشته شد؛ Page Builder dependency از قبل ممنوع است.
- **Consequences:** ND-R03 برای Foundation بسته است. theme.json صرفاً برای editor defaults حداقلی **PROPOSED** است و Theme را به FSE تبدیل نمی‌کند. انتخاب Cart/Checkout Blocks در برابر Classic و Product editor جزئی موضوع جداگانه فاز Commerce است.
- **Current gate:** ND-R01 و ND-R02 = OWNER APPROVED طبق دامنه §21؛ نسخه‌ها/Browser policy قبلی PROPOSED و Compatibility Tested = No. Phase 5 = STARTED — QA INCOMPLETE؛ دستور مستقیم Owner برای Foundation صادر شده است.
- **Related specs:** Architecture Contract §21، CONSTRAINTS.md، ADMIN.md.

## ADR-002 — Dependency Policy / Theme-owned Custom Features

- **Status:** Accepted — مرز dependency طبق دستور مالک؛ روش ماژول‌بندی همچنان Proposed
- **Date:** 2026-09-21
- **Context / EXTRACTED:** پیشنهاد تاریخی جداسازی Core Features در plugin با تصمیم جدید مالک ناسازگار است.
- **Decision / EXTRACTED — OWNER_CONFIRMED:** WordPress و WooCommerce dependencies رسمی‌اند. ACF Free/Pro، field/options frameworks مشابه، plugin اختصاصی مالک Features اصلی Theme و وابستگی Core به Page Builder ممنوع‌اند. Compare، Quiz، recommendation، live search UI، settings و custom presentation/data layer در خود Theme باشند.
- **Optional integrations:** Iranian gateway provider، SEO و form integration در صورت تصمیم و نیاز. Provider خاص optional است؛ وجود یک gateway آنلاین معتبر برای go-live اجباری است.
- **Alternatives:** ACF/feature plugin/page-builder ownership رد می‌شوند؛ ماژول‌های داخلی native Theme مجازند.
- **Consequences:** Theme باید schema، admin UI، validation، migration و پشتیبانی را خود نگه دارد. Theme switch می‌تواند Feature UI را غیرفعال کند؛ داده نباید خودکار حذف شود. export/lifecycle جزئیات Proposed قرارداد است.
- **Current gate:** ND-R01 و ND-R02 = OWNER APPROVED؛ نام‌های فنی `torantejarat` و Database Support = MySQL ثبت شده‌اند، نه compatibility. Theme type با ADR-001 بسته است؛ integrationهای قابلیت‌های آینده همچنان deferred هستند. Phase 5 = STARTED — QA INCOMPLETE.
- **Related specs:** Architecture Contract §2، §3، §5، §18، CONSTRAINTS.md، AGENTS.md.

## ADR-003 — Content Architecture / Native Data Contract

- **Status:** Accepted — مالکیت و native/no-ACF؛ schemaهای اجرایی Proposed
- **Date:** 2026-09-21
- **Context / EXTRACTED:** catalog چهار محصول نمونه و HTML تکراری دارد. SKU نمایشی در HTML هست، ولی field مستقل catalog نیست. recording=false در head معنای تأییدنشده/وابسته دارد.
- **Decision / EXTRACTED — OWNER_CONFIRMED:** WordPress مالک Posts/Pages/Users/Media/core settings است؛ WooCommerce مالک Products، price/sale/SKU/stock، descriptions، gallery، categories و attributes است. استانداردهای Woo در Theme mirror نشوند. custom video/limitation/story/compare supplements، quiz configuration و art variant با Theme-native metadata/options مدیریت شوند. read time توسط Theme محاسبه شود، نه فیلد دستی بدون ضرورت.
- **Implementation / PROPOSED:** Settings API، Meta API، Options API، Woo CRUD، schema registration و editor controls در Theme؛ جدول §4 برای هر field storage/owner/UI/validation/fallback/migration را مشخص می‌کند. هویت فنی `torantejarat` مصوب است؛ `<prefix>` در مثال‌های storage فقط placeholder schema است و خود meta/option keyها و جزئیات schema هنوز PROPOSED هستند.
- **Alternatives:** استفاده native از Page/Post/Product یا مدل اضافی صرفاً با نیاز اثبات‌شده؛ ACF، framework مشابه یا feature plugin گزینه نیستند.
- **Consequences:** duplicate source of truth ممنوع؛ migrations و lifecycle/export اختصاصی در فاز داده مرتبط طراحی می‌شوند، نه پیشاپیش در Foundation.
- **Foundation scope / OWNER_CONFIRMED:** در Phase 5 فقط حداقل Settings/Data موردنیاز Foundation؛ Product Metadata، schema registry عمومی، data framework، migration engine و editorهای Product ساخته نشوند. API/ownership contract ثابت است، پیاده‌سازی جزئی Product به فاز Product موکول می‌شود.
- **Decision status:** ND-R01 = OWNER APPROVED با هویت `torantejarat`؛ ND-R03 بسته است. ND-R06/07/08/15/16 مربوط به قابلیت‌ها و migration بعدی‌اند و همچنان deferred هستند؛ هیچ Data Layer اجرایی در این مرحله ساخته نمی‌شود.
- **Related specs:** Architecture Contract §3 تا §5 و §17، CONTENT-MODEL.md، SETTINGS.md، ADMIN.md.

## ADR-004 — Forms / Handler and Delivery

- **Status:** Proposed — انتخاب handler هنوز پذیرفته نشده؛ الزامات مالک داخل ADR لازم‌الاجراست
- **Date:** 2026-09-21
- **Context / EXTRACTED:** فرم‌های contact/rent/repairs فقط متن محلی می‌سازند؛ backend ندارند. no-JS GET ریسک PII دارد.
- **Constraint / EXTRACTED — OWNER_CONFIRMED:** UI و Featureهای اختصاصی Theme-owned؛ ضرورت form plugin فرض نشود. مشاوره رایگان، اجاره و تعمیر conversion مکمل خریدند.
- **Decision / PROPOSED:** feasibility handler داخلی با WordPress APIs برای نیاز ساده بررسی شود؛ handler mode نهایی بعد از مشخص‌شدن recipient، persistence، delivery، anti-spam و workflow انتخاب شود. Custom UI/data نباید به plugin فرم وابسته شود.
- **Alternatives:** native POST handler؛ optional integration adapter برای workflow اثبات‌شده؛ draft-only فقط با تصمیم صریح برای همان فرم، نه مدل اصلی سایت.
- **Consequences:** native handler مسئولیت security، spam، mail failure، retention و نگهداری را به Theme اضافه می‌کند؛ integration اختیاری محدود به نقش مصوب باشد. پذیرش wp_mail تضمین delivery نیست.
- **NEEDS_DECISION:** ND-R11؛ تصمیم باز را با نصب/ساخت handler نبندید.
- **Related specs:** Architecture Contract §13 و §19، SECURITY.md، ADMIN.md.

## ADR-005 — Checkout / Iranian Online Payment Gateway

- **Status:** Accepted — مدل فروش و مرز پرداخت؛ Provider و Checkout mode باز
- **Date:** 2026-09-21
- **Context / EXTRACTED:** checkout.html فقط export متن دارد؛ هدف جدید فروش آنلاین واقعی با چرخه سفارش است.
- **Decision / EXTRACTED — OWNER_CONFIRMED:** conversion اصلی Product → Add to Cart → Cart → Checkout → Payment → Order Confirmation. پرداخت آنلاین ایرانی از WooCommerce Payment Gateway abstraction؛ Provider-agnostic. Theme UI/integration را ارائه می‌کند، نه پردازش PSP.
- **Boundary / OWNER_CONFIRMED:** **Theme:** Payment UI integration، checkout presentation و gateway-independent presentation hooks. **WooCommerce:** order/payment lifecycle abstraction و gateway interface. **Payment Gateway:** ارتباط با PSP ایرانی، transaction initiation، callback، verification و transaction result. verify/transaction/PSP HTTP/credentials/callback processor داخل Theme ممنوع. این بند مجوز ساخت Gateway یا Payment UI نهایی در Foundation نیست.
- **Alternatives:** Lead-only و COD به‌عنوان مدل اصلی رد شده‌اند. Providerهای مختلف ایرانی فقط پس از qualification؛ هیچ Provider فعلاً hard-code یا انتخاب نشده است. Blocks و Classic هر دو candidate هستند، نه انتخاب‌های پذیرفته‌شده.
- **Consequences:** یک درگاه آنلاین فعال و آزموده شرط go-live است. صحت amount/currency/order matching و idempotency در gateway/Woo؛ UI از وضعیت authoritative سفارش پیروی کند. Woo cart/session/order مرجع‌اند، نه localStorage.
- **Implementation / PROPOSED:** state matrix §8 برای فاز Commerce، حداقل overrides، provider compatibility tests و private-cache exclusion. UI activity مانند `redirecting` از Woo status مثل `processing` جداست؛ موفقیت redirect به‌تنهایی paid نیست. `processing` سفارش، label «در حال انتقال» نیست؛ status/paid state از Woo خوانده شود.
- **NEEDS_DECISION:** ND-R04، ND-R05، ND-R06 و ND-R10 به فاز Commerce/Account deferred هستند؛ mode پیش از checkout implementation و gateway qualification پیش از go-live. هیچ‌یک blocker عمومی Foundation نیستند. Online sales و Iranian gateway requirement قطعی‌اند؛ Provider مشخص انتخاب نشده است.
- **Related specs:** Architecture Contract §1، §8، §18، §19، WOOCOMMERCE.md، SECURITY.md.

## ADR-006 — Filters / Woo Native Taxonomies and Attributes

- **Status:** Accepted — مالکیت Woo و Theme UI؛ semantics و query strategy دقیق Proposed
- **Date:** 2026-09-21
- **Context / EXTRACTED:** فیلتر فعلی روی array محلی اجرا می‌شود و query «۱۰» حتی resolution 1080p را match می‌کند؛ SKU نمایشی searchable نیست.
- **Decision / EXTRACTED — OWNER_CONFIRMED:** filters با taxonomy/attributes بومی Woo هماهنگ باشند؛ Theme مالک controls، live search UI و custom behavior است؛ standard product data mirror نشود.
- **Implementation / PROPOSED:** SSR GET، progressive enhancement، allowlisted vars، native product queries، normalization فارسی/SKU، numeric attribute term mapping و pagination. هر cache/index مشتق و rebuildable؛ storage authoritative دوم ممنوع.
- **Alternatives:** دسته کاربرد به‌صورت product_cat یا global attribute طبق content decision؛ search/filter plugin dependency از پیش تصویب نشده است.
- **Consequences:** مدل unit/unknown/applicability و term convention باید قبل از query coding مشخص شود؛ بهینه‌سازی catalog بزرگ نیازمند اندازه‌گیری است.
- **NEEDS_DECISION:** ND-R07، ND-R09، ND-R14.
- **Related specs:** Architecture Contract §4، §10 تا §12، CONTENT-MODEL.md، NAVIGATION.md.

## ADR-007 — URL / SEO Ownership

- **Status:** Proposed
- **Date:** 2026-09-21
- **Context / EXTRACTED:** مسیرهای فعلی .html هستند؛ SEO ownership و redirects تعیین نشده‌اند.
- **Decision / PROPOSED:** native permalink/term/Woo endpoint APIs، redirect manifest و ownership بدون duplicate schema/canonical. SEO integration optional؛ absence نباید Core Theme را بشکند.
- **Alternatives:** SEO plugin منتخب یا native baseline؛ URLهای فارسی/لاتین پس از تصمیم، نه hard-code.
- **Consequences:** cart/account/checkout private و noindex، query index policy و migration link tests لازم‌اند. noindex جای authorization نیست.
- **NEEDS_DECISION:** ND-R09، ND-R12.
- **Related specs:** Architecture Contract §6، §14، §17، SEO.md، SITEMAP.md.

## ADR-008 — P0 Customer / Account / Order Flows

- **Status:** Accepted — scope P0؛ account policies و UI جزئی باز
- **Date:** 2026-09-21
- **Context / EXTRACTED:** account و سفارش واقعی در HTML وجود ندارند؛ کاربر آن‌ها را به‌طور قطعی جزو Core Flow تعیین کرده است.
- **Decision / EXTRACTED — OWNER_CONFIRMED:** Shop/Product/Cart/Checkout/Payment/Order Confirmation/My Account/Order History/Customer Account و موفقیت/شکست/لغو پرداخت P0 هستند. My Account قابل حذف یا تعویق نامعلوم نیست.
- **Implementation / PROPOSED:** Woo native account endpoints و WP identity؛ order ownership validation، logged-out/empty/error states، pending/expired-session handling و private-cache exclusion. guest verification مطابق نسخه Woo.
- **Alternatives:** guest در برابر required registration هنوز باز؛ این گزینه‌ها اصل وجود My Account را تغییر نمی‌دهند. OTP/ذخیره روش پرداخت نیاز مستقل دارد.
- **Consequences:** طراحی و آزمون‌های جدید لازم است؛ clone کردن checkout demo کافی نیست. هزینه Account در scope اصلی لحاظ شود.
- **NEEDS_DECISION:** ND-R05، ND-R10، ND-R13.
- **Related specs:** Architecture Contract §8، §9، §19، WOOCOMMERCE.md، ACCEPTANCE.md.

## ADR-009 — Persian Localization / Currency and Dates

- **Status:** Proposed
- **Date:** 2026-09-21
- **Context / EXTRACTED:** HTML فارسی/RTL و قیمت نمونه تومان دارد؛ currency storage/gateway و تقویم قراردادی ندارند.
- **Decision / PROPOSED:** عدد و identifier canonical مستقل از presentation؛ Woo مالک مبلغ/ارز و gateway مالک تبدیل موردنیاز integration. تبدیل پراکنده مبلغ در Theme ممنوع. display/input formatting و ترجمه در Theme با WP APIs.
- **Alternatives:** ریال/تومان و تقویم/چندزبانه طبق تصمیم مالک؛ هیچ فرضی از copy demo نتیجه نشود.
- **Consequences:** آزمون decimal، unit mismatch، bidi، ارقام و export لازم است؛ date policy باید قبل از فرم اجاره مشخص شود.
- **NEEDS_DECISION:** ND-R04، ND-R11، ND-R18؛ Persian-first/RTL قطعی است ولی تقویم، timezone، زبان دوم و سیاست ارقام بازند. ND-R01 = OWNER APPROVED و text domain برابر `torantejarat` است.
- **Related specs:** Architecture Contract §4، §8، §15، I18N.md، WOOCOMMERCE.md.

## ADR-010 — Assets / Quality Baseline

- **Status:** Accepted — native assets و QA؛ اکنون migration CSS/JS presentation از HTML با دستور Phase 6 مجاز است، نه redesign یا compatibility approval
- **Date:** 2026-09-21
- **Context / EXTRACTED:** assets در همه صفحات global لود می‌شوند؛ تست‌های معرفی‌شده در README غایب‌اند. در Foundation این فایل‌های مرجع read-only هستند؛ بازسازی generator یا audit کامل دوباره شرط شروع نیست.
- **Decision / OWNER_AUTHORIZED:** Foundation: native enqueue، vanilla JS و CSS لایه‌ای حداقلی، logical RTL، semantic shell و حداقل QA شامل PHP lint/JS syntax/smoke و browser checklist طبق §21. بدون runtime/build framework جدید، catalog preload یا design نهایی. فونت/رسانه واقعی، CWV کامل فروشگاه و optimization gateway به فاز مربوط موکول‌اند؛ scripts ضروری gateway بعداً با optimization کور شکسته نشوند.
- **Alternatives:** حفظ فونت خارجی با سیاست مصوب؛ روش bundling فقط پس از نیاز و ADR dependency.
- **Consequences:** محیط QA و بودجه Theme در برابر کل Woo/gateway باید تفکیک شوند؛ docs check به معنی pass تجارت نیست.
- **Review status:** ND-R02 = OWNER APPROVED با انتخاب MySQL؛ نسخه‌ها/Browser policy قبلی همچنان PROPOSED و Compatibility Tested = No. بسته QA §21 پیاده شده، اما اجرای runtime نیازمند محیط واقعی است؛ باقی ND-R13/14/16/17 مربوط به UI/scale/migration/release نهایی‌اند. Phase 5 = STARTED — QA INCOMPLETE.
- **Related specs:** Architecture Contract §15 تا §17، QA.md، PERF.md، A11Y.md.

## ADR-011 — Technical Identity / Brand Separation

- **Status:** Accepted — به‌روزرسانی با تصمیم صریح Owner؛ ND-R01 = OWNER APPROVED. اصل تفکیک هویت فنی از برند حفظ می‌شود؛ دستور جداگانه Owner اکنون اجرای Foundation را مجاز کرده است.
- **Date:** 2026-09-21
- **Context / OWNER_CONFIRMED:** Brand نهایی **توران تجارت**؛ Legal Entity **دید برتر توران تجارت**؛ National ID `14011384991`. Author و Designer هر دو **یعقوب طیبی**؛ Website رسمی بدون تغییر.
- **Decision / OWNER_CONFIRMED:** Theme slug و text domain = `torantejarat`؛ PHP namespace = `ToranTejarat\Theme`؛ function prefix = `torantejarat_`؛ constant prefix = `TORANTEJARAT_`؛ asset handles = `torantejarat-*`؛ CSS namespace = `.torantejarat-*`. هیچ هویت فنی دیگری تعریف نشود. `*` الگوی suffix است.
- **Architectural principle / unchanged:** هویت فنی تا حد امکان از برند جدا بماند تا تغییر برند موجب بازطراحی غیرضروری ساختار نشود. تطبیق فعلی نام‌ها تصمیم صریح Owner است، نه استنتاج Agent از نام حقوقی یا repository.
- **History / Supersedes:** وضعیت قبلی «Brand Identity Pending» و تعلیق ND-R01 با تأیید فعلی Owner جایگزین شد. «شلنگ‌بین» فقط نام قبلی Project/Concept است، نه Brand Identity جاری. `didbarttar` صرفاً سابقه Temporary Repository/Project Identity است، نه هویت فنی Theme؛ پیشنهاد قبلی و مشتقات آن کنار گذاشته شدند. `shalangbin` انتخاب نشده است. Credits قبلی با مقادیر جاری مالک اصلاح شدند.
- **Related owner decision — ND-R02:** **OWNER APPROVED**؛ **Database Support = MySQL**. MariaDB خارج Support Matrix مگر با تصمیم جدید Owner. نسخه‌های WP 6.9، Woo 10.8، PHP 8.3، MySQL 8.4 LTS و Browser policy قبلی بدون تغییر **PROPOSED** می‌مانند؛ تأیید خانواده Database به معنی تأیید خودکار عدد نسخه یا نتیجه تست نیست. **Compatibility Tested = No**.
- **Alternatives:** برند/نام‌های قبلی گزینه جاری نیستند؛ هویت جایگزین جدیدی پیشنهاد نمی‌شود. اصل تفکیک برند و نام فنی حفظ شده است.
- **Consequences:** نام‌های مصوب در source Foundation و Documentation استفاده شده‌اند؛ هیچ هویت جدیدی انتخاب نشد. Phase 5 = STARTED — QA INCOMPLETE؛ توقف قبلی با دستور مستقیم Owner برای Foundation محدود جایگزین شده است. هیچ rename فایل‌های مرجع، dependency نصب‌شده، migration، payment implementation یا release وجود ندارد؛ Compatibility Tested = No.
- **Unchanged:** Classic/Gutenberg، scope/responsibility، P0های Commerce و سایر تصمیم‌های قبلی ثابت‌اند. source از Foundation وارد Phase 6 migration شده؛ deployment و Payment qualification اجرا نشده‌اند.
- **Related specs:** [Architecture Contract §21](ARCHITECTURE-CONTRACT.md)، [Final Decision Sheet](FINAL-DECISION-SHEET.md)؛ ND-R01 و ND-R02.

## ثبت تصمیم‌های بعدی

هر ADR جدید باید Status، Date، Context، Decision، Alternatives، Consequences، Related specs و NDهای مؤثر داشته باشد. تعارض با FDهای Architecture Contract باید قبل از implementation به مالک گزارش شود. به‌روزرسانی این اسناد مجوز شروع Phase 5 نیست.
