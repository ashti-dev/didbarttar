# Architecture Review / Pre-Phase-5 Audit

> **Historical snapshot — نه وضعیت جاری تصمیم‌ها.** یافته‌ها، NDها و ارجاع‌های خط/هش این گزارش مربوط به اسناد هنگام Review قبلی‌اند. Credits این فایل طبق تصمیم جدید Owner اصلاح شده‌اند. وضعیت جاری: Brand توران تجارت؛ ND-R01 و ND-R02 = OWNER APPROVED؛ هویت فنی torantejarat؛ Database Support = MySQL (MariaDB خارج Support Matrix)؛ نسخه‌ها/Browser policy قبلی PROPOSED؛ Compatibility Tested = No؛ Phase 5 = BLOCKED — NOT STARTED. مرجع جاری [Final Decision Sheet](FINAL-DECISION-SHEET.md) و [Architecture Contract §21](ARCHITECTURE-CONTRACT.md) است؛ یافته‌های تاریخی زیر مجوز اجرا یا تصمیم باز جاری نیستند.

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

- تاریخ Review: 2026-09-21
- نوع بررسی: Document architecture audit؛ بدون implementation، نصب یا انتخاب dependency.
- وضعیت نهایی Review: **NOT READY — unresolved architecture gates**.
- Phase 5: **متوقف**؛ این گزارش هیچ مجوز شروع، انتخاب Provider یا ratification جزئیات فنی نیست.
- فایل‌های مبنا: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md) و [ADR.md](ADR.md)، نسخه جاری workspace، نه صرفاً نسخه HEAD یا diff.
- کل Contract، ۵۷۰ خط، و کل ADR، ۱۶۱ خط، خوانده و بررسی شد.
- shorthand ارجاع: `AC:Lx–Ly` یعنی خطوط سند Architecture Contract؛ `ADR:Lx–Ly` یعنی خطوط فایل ADR. ارجاع‌ها برای همین snapshot معتبرند.
- Source SHA-256 قرارداد: `4ee58dc7ebc1f2cbd2c2633e43fe1cee5b272162bcb8084d161f4e13a920cf5e`
- Source SHA-256 رجیستری ADR: `d34334324416b65fedb736ef322f48288c29bca9bec84743c8a20aa89736ec57`

`EXTRACTED`: Project Identity دقیقاً در هر دو سند موجود است؛ اصلاح Identity لازم نبود. اسناد مبنا در این audit تغییر داده نمی‌شوند. تنها خروجی جدید این مرحله همین گزارش است؛ تغییرات ثبت‌نشده قبلی repository متعلق به مراحل قبل‌اند و به این audit نسبت داده نمی‌شوند.

تعریف برچسب‌ها: `EXTRACTED` برای شاهد متن؛ `PROPOSED` برای توصیه reviewer؛ `NEEDS_DECISION` برای تصمیم باز. در جدول وضعیت تصمیم‌ها فقط چهار Status درخواستی استفاده شده‌اند: `OWNER_CONFIRMED`، `PROPOSED`، `NEEDS_DECISION`، `SUPERSEDED`.

---

# A. Architecture Review Findings

## A.1 نتیجه کلی و روش بررسی

### نتیجه

- **تضاد صریح با ممنوعیت‌های اصلی مالک پیدا نشد:** هیچ مجوز فعالی برای ACF، core-feature plugin، Page Builder dependency، Commerce موازی یا PSP processing در Theme در این دو سند وجود ندارد.
- **تفکیک فروش/Provider صحیح است:** حداقل یک درگاه آنلاین ایرانی برای go-live الزام قطعی است؛ Provider و Checkout mode هنوز بازند.
- **Product Data Contract از نظر وجود ستون‌ها کامل است:** هر ۲۰ ردیف، شش خانه Owner/Storage/UI/Validation/Fallback/Migration دارد؛ بااین‌حال برخی خانه‌ها هنوز schema یا رفتار اجرایی دقیق نیستند.
- **Theme-native صرفاً نام نیست:** Settings API، Meta API، Options API، Woo CRUD، admin controls، lifecycle و versioned migration معرفی شده‌اند؛ قرارداد اجرایی fieldها هنوز Proposed است.
- **دو نقص متنی قابل اثبات:** ابهام `processing` در جدول پرداخت و حذف ND-R18 از خلاصه نهایی NDها.
- **مانع واقعی آمادگی:** Phase 5 scope تعریف نشده و تصمیم‌های foundation و قراردادهای فنی مؤثر هنوز تصویب نشده‌اند. نبود تخلف از policy مساوی readiness برای implementation نیست.

### بررسی‌های انجام‌شده

`EXTRACTED`

1. خواندن کامل هر دو سند، از اولین تا آخرین خط؛ نه صرفاً تغییرات اخیر.
2. search حساس به معنی، با نگارش فارسی/انگلیسی و حالت‌های مختلف عبارت‌های درخواستی.
3. کنترل ۲۰ ردیف data contract و وجود شش مؤلفه هر ردیف.
4. کنترل یکتایی ۱۵ FD، ۱۸ ND و ۱۰ ADR و ارجاع NDها؛ ارجاع ND تعریف‌نشده پیدا نشد.
5. تطبیق NDهای اصلی با FINAL DECISION LOG؛ ND-R18 در خلاصه نیامده است.
6. بررسی scope پذیرش ADRها و crosswalk شماره‌گذاری تاریخی.
7. کنترل دقیق نام و URL Credits.
8. کنترل documentation-only بودن خروجی و ثابت ماندن محتوای فایل‌های قبلی؛ `git diff --check`.

آزمون WordPress/WooCommerce runtime، gateway، DB، مرورگر، accessibility عملی و performance اجرا نشده است. چون چنین runtimeای موضوع این مرحله نیست، ادعای pass اجرایی یا عدم duplication در database واقعی مطرح نمی‌شود.

## A.2 Findings — Location → Problem → Severity → Required Correction

شدت‌ها `PROPOSED` و مربوط به اثر بر تصمیم‌گیری هستند، نه رتبه incident. **P1** یعنی مانع scope مرتبط؛ **P2** یعنی ناهماهنگی/ابهام سند که باید اصلاح یا صریحاً تعیین تکلیف شود. «مشروط» یعنی مانع شروع همان قابلیت، نه لزوماً هر کار Foundation.

| ID | Location | Problem | Severity | Required Correction |
|---|---|---|---|---|
| AR-001 | AC:L519–L523، به‌ویژه L522 | خود سند می‌گوید Phase 5 تعریف نشده است. بدون deliverableها و مرز این فاز نمی‌توان ادعا کرد کدام ND واقعاً قبل از شروع لازم است. این یک **missing decision** است، نه تعارض با اصل توقف. | P1 — مانع gate فعلی | مالک دامنه، فایل‌های مجاز، خروجی، exclusionها و exit criteria فاز را تصویب کند؛ reviewer نباید Phase 5 را خودسرانه برابر Foundation یا کل WooCommerce فرض کند. |
| AR-002 | AC:L87، L496–L498؛ ADR:L42–L50 | نوع Theme، platform versions، editor و technical identity قطعی نیستند. ADR-001 به‌درستی Proposed است ولی هنوز قابل اجرا نیست. | P1 — مانع شروع Foundation | بخش فنی ND-R01، نسخه/API/editorهای مؤثر ND-R02 و Theme/editor contract در ND-R03 بسته شوند؛ یا محدودیت دقیق scope توسط مالک تعیین شود. نام/وب‌سایت طراح به جای Theme slug یا text domain استفاده نشود. |
| AR-003 | AC:L125–L154، L501–L503 | وجود متن در شش ستون با schema قابل پیاده‌سازی یکسان نیست: typed listها، boundها، enum/defaultها، unit convention و storage migration map نهایی نشده‌اند. عدم قطعیت در متن صریح است، اما نباید در downstream به‌عنوان «Data Contract پذیرفته‌شده و کامل» مصرف شود. | P1 مشروط — پیش از custom data/Product/Filter/Quiz implementation | ND-R06/07/08 مرتبط با scope بسته شوند؛ type، cardinality، bounds، default/unknown، parent/variation scope و migration value mapping برای fieldهای انتخاب‌شده ثبت شود. native Woo fields در Theme بازتعریف نشوند. جزئیات جدول A.4 مرجع این finding است. |
| AR-004 | AC:L162–L169 و L476؛ ADR:L70–L74 | ثبت meta و Woo CRUD هر دو ذکر شده و دو writer ممنوع شده است؛ اما هنوز مسیر واحد write/validation/auth برای هر نوع ورودی admin/REST/import و نسخه editor انتخاب نشده. این **ابهام اجرایی** است، نه شاهد دو storage یا دو writer فعلی. | P1 مشروط — پیش از Data Layer writes | برای schemaهای مصوب، owner save pipeline، permission، validator و writer واحد تعیین شود. توضیح داده شود registration به‌تنهایی جای authorization/validation همه مسیرها نیست. هیچ endpoint یا handler در این audit ساخته نشود. |
| AR-005 | AC:L249–L260؛ به‌خصوص L254 در مقابل L256 | ردیف `redirect / processing` به «در حال انتقال، بدون ادعای paid» اشاره می‌کند؛ ردیف موفقیت می‌گوید سفارش موفق می‌تواند `processing` باشد. استفاده یک نام برای UI activity و Woo order status خطر mapping اشتباه وضعیت paid را دارد. متن ردیف موفقیت درست است؛ جدول باید دو namespace را صریح جدا کند. | P1 مشروط — پیش از Payment state mapping | label داخلی پیشنهادی مثل `redirecting` یا `awaiting-verification` برای فعالیت UI از status واقعی Woo مانند `processing` جدا شود. Theme از نام activity وضعیت سفارش نسازد؛ Woo/Gateway مرجع paid state بمانند. این اصلاح نام‌گذاری است، نه انتخاب Provider یا ساخت state machine تجاری جدید. |
| AR-006 | AC:L513 در مقابل L560–L568؛ ADR:L145 | ND-R18 برای زبان/تقویم/timezone/ارقام در registry وجود دارد و ADR-009 به آن ارجاع می‌دهد، ولی FINAL DECISION LOG بخش تصمیم‌های باز آن را حذف کرده است. **ناهماهنگی قطعی خلاصه با registry**؛ به معنی حل‌شدن ND نیست. | P2 — صحت و traceability سند | ND-R18 به خلاصه بازها افزوده شود؛ نیاز به تصمیم معماری جدید ندارد. در این audit source تغییر نکرده است؛ پیشنهاد اصلاح ثبت می‌شود. |
| AR-007 | ADR:L55، L67، L91، L104، L127؛ AC:L125، L247–L249 | Status سطح ADR، Accepted است ولی بعضی جزئیات همان ADR Proposed هستند؛ توضیح دامنه وجود دارد و خلاف تصمیم مالک تأیید نشده است. بااین‌حال استفاده سطحی از Status می‌تواند باعث ratification کاذب شود. عبارت «همگی P0» بالای state table نیز از دامنه Proposed جدا دیده می‌شود. | P2 — ریسک انتقال وضعیت، نه Conflict قطعی | وضعیت هر تصمیم اتمیک جدا گزارش شود؛ `Accepted` فقط مالکیت/مدل تجاری/P0 اعلام‌شده را بپوشاند، نه schema، provider، full state matrix یا روش اجرا. جدول A.7 این تفکیک را انجام می‌دهد؛ برای گسترش P0 پیشنهادی نیاز به ratification است. |
| AR-008 | AC:L175، L496–L513، L520–L522؛ ADR:L99 | NDها چند milestone را در یک ردیف جمع کرده‌اند: ND-R10 شامل mode، انتخاب Provider و qualification است؛ ND-R01 برند و namespace را با هم دارد؛ ND-R16 baseline و migration اجرایی را با هم دارد. اگر همه این‌ها شرط یکسان «قبل از Phase 5» شوند، scope نادرست بسته یا بیش از حد مسدود می‌شود. | P2 — ابهام scheduling؛ همراه AR-001 مانع gate دقیق | هر ND مرکب به sub-decision با owner و milestone تفکیک شود؛ نیاز به انتخاب Provider نهایی برای Foundation از سند استنتاج نشود. mode/compatibility پیش از checkout scope و gateway فعال پیش از انتشار تعیین تکلیف شوند. به‌تعویق‌انداختن نیز فقط با تأیید مالک و exclusion صریح. |

### ابهام‌های باقیمانده که فعلاً Conflict نیستند

`EXTRACTED`

- **فرم:** AC:L348–L363 و ADR-004 به‌درستی handler را قطعی نمی‌کنند. native handler یک candidate است، نه انتخاب مالک. storage/retention/delivery بدون ND-R11 آماده اجرا نیست.
- **Checkout mode:** Blocks/Classic هنوز باز است؛ هیچ modeی در جدول URL یا Product mapping به‌طور الزام‌آور انتخاب نشده است. generic gateway abstraction به‌تنهایی اثبات سازگاری Blocks نیست؛ compatibility test برای مسیر انتخابی در زمان خودش لازم است.
- **SEO:** optional بودن plugin با ND-R12 سازگار است؛ هیچ SEO plugin مشخصی dependency Core نیست.
- **Lifecycle:** حفظ داده، export، purge و migrationهای دقیق Proposed هستند؛ ownership در Theme پذیرفته شده ولی طراحی maintenance/export هنوز تصمیم اجرایی تصویب‌شده نیست.
- **Media/localization/quality:** مجوز رسانه و فونت، سیاست ارقام/تاریخ، بودجه performance و test matrix بازند؛ هیچ عدد budget یا انتخاب فونت محلی به‌عنوان دستور قطعی مالک ثبت نشده است.
- **Page Builder:** ممنوعیت dependency روشن است. اسناد عمدتاً از «Core Theme» سخن می‌گویند؛ این عبارت مجوز وابسته‌کردن بخش دیگری از Theme نیست. هیچ چنین dependency پیشنهادی در متن پیدا نشد.

## A.3 جست‌وجوی ممنوعیت‌ها و بررسی متن‌های حساس

نتایج search با معنی جمله بررسی شدند؛ وجود کلمه ACF یا Plugin در بند ممنوعیت، Finding خلاف policy نیست. شمارش زیر تعداد **خط دارای match** است، نه تعداد وقوع واژه. جست‌وجوی payment/duplication شامل موارد benign مانند callback مربوط به Meta API یا duplicate-submit نیز بود؛ این‌ها با PSP processing یا data duplication اشتباه گرفته نشدند.

| عبارت/خانواده | Evidence / Location | Problem | Severity | Required Correction |
|---|---|---|---|---|
| ACF / ACF Pro | ۱۳ خط AC: 33,80,85,109,122,146,304,460,461,492,498,527,544؛ ۷ خط ADR: 23,40,48,58,60,67,72 | همگی نبود implementation، منع، یا حفظ منع در تصمیم‌های بعدی‌اند؛ هیچ optional/proposed dependency مثبت پیدا نشد | ندارد | ممنوعیت حفظ شود؛ occurrenceها صرفاً به خاطر search حذف نشوند |
| Custom Plugin / feature-plugin ownership | ۱۱ خط AC: 33,81,108,122,173,175,233,304,462,527,545؛ ۷ خط ADR: 23,33,40,57,58,60,72 | پیشنهاد تاریخی plugin مالک Core در ADR:L33 صریحاً Superseded است؛ AC:L233 integration استاندارد gateway را با شرط تصمیم جدا مطرح می‌کند، نه core-feature plugin | ندارد | crosswalk و قید external standard gateway حفظ شود؛ مجوز ساخت plugin جدید از آن برداشت نشود |
| Page Builder | ۶ خط AC: 82,107,403,463,498,527؛ ۳ خط ADR: 48,58,60 | dependency ممنوع است؛ پیشنهاد فعال وابسته به builder یافت نشد | ندارد | Native architecture بدون این dependency حفظ شود |
| Lead-only / Lead Generation Only | AC:L43، L234، L548؛ ADR:L96 | مدل Lead-only رد شده است؛ draft-only فرم در AC:L350 با شرط تأیید همان فرم است، نه مدل فروش | ندارد | مسیر مکمل فرم را جایگزین خرید نکنید |
| COD as primary payment | AC:L49، L234، L245، L457، L549؛ ADR:L96 | همه موارد منع COD به‌عنوان مدل اصلی یا fallback پنهان‌اند | ندارد | prohibition حفظ شود؛ از این متن ممنوعیت مطلق هر روش ثانویه‌ای که مالک هنوز بررسی نکرده نتیجه نشود |
| Payment processing inside Theme | AC:L105–L106، L201، L227–L266، L457، L468–L487؛ ADR:L23، L94–L98 | PSP processing، verification، callback و credential در Theme ممنوع؛ status mapping ambiguity جداگانه AR-005 است | منع صحیح؛ AR-005 اثر P1 مشروط | مرز Woo/Gateway حفظ و label پردازش UI روشن شود |
| Duplicated product data | AC:L99–L104، L121، L129–L169، L293–L295، L528؛ ADR:L23، L70–L73، L107–L108 | standard data mirror ممنوع؛ projection/cache مشتق مجاز پیشنهادی است. schemaهای هنوز باز، duplication بالفعل نیستند | نقص طراحی اجرایی طبق AR-003/004؛ نه duplication اثبات‌شده | field whitelist و single writer پیش از اجرا؛ DB جدید یا source موازی نسازید |
| Conflicting ADR decisions | ADR:L25–L38، L55–L145 | شماره‌گذاری تاریخی حل شده؛ هیچ Accepted فعال مخالف ownership پیدا نشد. mixed-scope status ریسک AR-007 است | P2 طبق AR-007 | تصمیم‌های مالک از Proposed implementation تفکیک بمانند |
| Conflicting ND decisions | AC:L490–L568؛ ADR:L50–L156 | ND تعریف‌نشده نیست؛ ND-R18 در final summary جا افتاده؛ deadlineهای ترکیبی AR-008 | P2 طبق AR-006/008 | تکمیل summary و تفکیک milestones؛ ND را خودکار Accepted نکنید |

**نتیجه Dependency Matrix:** AC:L455–L464 با policy مالک سازگار است. gateway Provider-specific integration از dependencyهای ممنوع field/features جدا شده و شرط go-live روشن است. هیچ متن ACF را optional معرفی نمی‌کند.

## A.4 Product Data Contract — بررسی هر field

### تعریف نتیجه

- `✓`: مؤلفه در سطح قرارداد معماری مشخص است؛ به معنی Accepted بودن جزئیات فنی یا پیاده‌سازی نیست.
- `△`: عنوان/متن وجود دارد، ولی یک بخش مهم همان مؤلفه هنوز دقیق نیست.
- fieldهای Woo نباید برای «دقیق‌تر شدن storage» به کلیدهای داخلی DB یا meta موازی Theme تبدیل شوند؛ نام native field و Woo CRUD برای مرز معماری کافی است.

| Field / Location | Storage | Owner | UI | Validation | Fallback | Migration | نکته audit |
|---|---|---|---|---|---|---|---|
| Product name — AC:L129 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Native Woo؛ validation و publish policy ذکر شده |
| SKU — L130 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | SB-* نیاز به تأیید دارد؛ نبود SKU در source پنهان نشده |
| Price — L131 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Native Woo؛ ارز/مبلغ واقعی ND-R04/06، نه نقص owner |
| Sale price — L132 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | نبود sale در HTML به field نمونه ساختگی تبدیل نشده |
| Stock — L133 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | draft/purchasability و Woo validation مشخص؛ product/backorder policy باز |
| Gallery — L134 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | attachment/native gallery؛ Theme gallery mirror ندارد |
| Product categories — L135 | ✓ | ✓ | ✓ | ✓ | △ | ✓ | fallback فقط می‌گوید empty category به معنی همه‌کاربرد نباشد؛ rendering/رفتار دقیق missing term باید در component contract مشخص شود |
| Attributes — L136 | ✓ | ✓ | ✓ | △ | ✓ | ✓ | canonical unit/value و unknown/not-applicable enum قطعی نیست؛ ND-R07 و L152 |
| Description — L137 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | Woo native؛ دستور منع تکرار story موجود |
| Short description — L138 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | شرح کوتاه catalog به native field منتقل می‌شود |
| Product video — L139 | ✓ | ✓ | ✓ | △ | ✓ | ✓ | attachment ID/caption مسیر اصلی است؛ MIME allowlist، external policy و schema نیازمند ND-R08 |
| Technical limitation — L140 | ✓ | ✓ | ✓ | △ | ✓ | ✓ | bounded list گفته شده ولی item schema و bounds نهایی ثبت نیست |
| Product story/content blocks — L141 | ✓ | ✓ | △ | △ | ✓ | ✓ | content types/sections، field bounds و editor adapter هنوز مصوب نیست |
| Compare standard data — L142 | ✓ | ✓ | ✓ | ✓ | ✓ | ✓ | read-only projection از native data؛ row mapping جزئی بعداً تصویب شود |
| Compare supplemental data — L143 | ✓ | ✓ | ✓ | △ | ✓ | ✓ | field whitelist نهایی ندارد؛ اصل عدم native equivalent و عدم تکرار limitation درست است |
| Quiz rules — L144 | ✓ | ✓ | ✓ | △ | ✓ | ✓ | operator/range/term validation ذکر شده؛ rule schema/semantics و مقادیر معتبر باید تصویب شوند |
| Theme settings — L145 | ✓ | ✓ | ✓ | △ | △ | ✓ | family-level settings تعریف شده، نه registry تک‌تک key/default/capability؛ پیش از coding نیازمند تفصیل |
| Article presentation variant — L146 | ✓ | ✓ | ✓ | △ | △ | ✓ | enum/default اشاره شده ولی enum مصوب و default مشخص نشده؛ art-0/1/2 صرفاً source migration است |
| Read time — L147 | ✓ | ✓ | ✓ | △ | ✓ | ✓ | عدم storage authoritative صحیح؛ سرعت و الگوریتم ND-R15 است؛ ورودی متن و cache version مطرح‌اند |
| Legacy source identity — L148 | △ | ✓ | ✓ | ✓ | ✓ | ✓ | `<prefix>_legacy_id` و map نسخه‌دار گفته شده؛ storage دقیق map و granularity/schema آن روشن نیست؛ ND-R16 |

### Duplication audit

`EXTRACTED`

- **Woo standard fields:** name/SKU/regular-sale price/stock/descriptions/gallery/categories/attributes فقط native معرفی شده‌اند.
- **WP و Woo ownership:** اینکه محصول از media/term زیرساخت WordPress استفاده می‌کند دو نسخه داده ایجاد نمی‌کند؛ تفاوت physical storage و semantic ownership در AC:L113 صریح است.
- **Compare:** استانداردها read-only projection هستند و limitation از field اصلی خوانده می‌شود؛ AC:L142–L143 و L295.
- **Quiz:** rules به product attributes/taxonomies ارجاع می‌دهند و قیمت از Woo خوانده می‌شود، نه config mirror؛ L144 و L308–L314.
- **Numeric attributes:** source دوم numeric meta ممنوع است؛ index/cache فقط مشتق و بازسازی‌پذیر، آن هم در صورت نیاز؛ L152.
- **Story:** تکرار description و duplicate render منع شده؛ L137 و L141. مرز محتوا هنگام schema approval نیازمند fixture است، نه مجوز duplicate storage.
- **Read time:** computed است؛ cache مشتق با field دستی authoritatve فرق دارد.

**نتیجه:** در قرارداد target، duplication عمدی یا مجازشده standard Product data پیدا نشد. بدون runtime/DB، نمی‌توان pass اجرایی عدم duplication اعلام کرد. ۲۰×۶ خانه پر، به معنی ۲۰ schema نهایی و پذیرفته‌شده نیست.

## A.5 Theme-native / Woo / Payment boundary audit

### Theme-native

`EXTRACTED` — AC:L156–L175 روش‌های واقعی زیر را نام می‌برد:

- `register_setting` / Settings API؛
- Options API برای config؛
- `register_post_meta` / Meta API برای custom presentation؛
- Woo CRUD برای product access/save؛
- native metabox/panel و editor extensions؛
- schema registry، migrations نسخه‌دار و derived cache.

این طراحی بدون ACF قابل تصور و مشخص‌تر از یک عنوان عمومی است. اما API naming جای تصمیم version/editor/write pipeline را نمی‌گیرد؛ AR-002/003/004 پیش از اجرای scope مربوط بسته شوند.

### WooCommerce

`EXTRACTED` — AC:L99–L106، L240–L241، L276 و L487 مالکیت Product/Cart/Checkout/Order/Customer/Payment abstraction را به Woo می‌دهند. account identity با WordPress است. Theme مالک presentation و ویژگی‌های اختصاصی است، نه Commerce backend. Order APIs به‌جای فرض postmeta و HPOS لحاظ شده است.

### Payment

`EXTRACTED` — دو تصمیم کاملاً جدا ثبت شده‌اند:

1. **قطعی:** فروش آنلاین و حداقل یک درگاه ایرانی فعال و آزموده برای انتشار — AC:L43–L51، L74، L245، L457؛ ADR:L94–L99.
2. **باز:** Provider مشخص و Checkout Blocks/Classic — AC:L243، L268، L505؛ ADR:L91، L96، L99.

پردازش مستقیم در Theme منع شده است. در متن هیچ نام Provider مشخص، credential، HTTP integration یا callback implementation تأیید نشده است. AR-005 تنها ابهام مهم در جدول state است؛ توصیه اصلاح آن ownership پرداخت را تغییر نمی‌دهد.

## A.6 Project Identity

`EXTRACTED — OWNER_CONFIRMED`

- Contract، L9 در snapshot: Credits رسمی زمان Review ثبت شده بود؛ آن مقدار قدیمی با تصمیم جدید Owner اصلاح شده است. مقدار جاری برای **Author** و **Designer** هر دو **یعقوب طیبی** است؛ Project Metadata بالا مرجع جاری است.
- Contract، L11: **Website: https://yaghoubtayebi.ir/** با URL دقیق و trailing slash.
- Contract، L13: الزام حفظ اطلاعات در اسناد نهایی.
- Contract، FD-15 در L558: ثبت رسمی در FINAL DECISION LOG.
- ADR، L9–L13: همان اطلاعات و الزام حفظ.

**نتیجه تاریخی Review:** Credits در اسناد آن snapshot یکسان بود؛ این نتیجه به مقادیر قدیمی مربوط بود. اکنون Credits طبق تصمیم جدید Owner اصلاح شده است. metadata به‌تنهایی برند/slug را تعیین نمی‌کند؛ هویت جاری با تصمیم جداگانه Owner در ADR-011 ثبت شده و هیچ امضایی به UI اضافه نشده است.

## A.7 جدول نهایی وضعیت تصمیم‌ها

این جدول **وضعیت audit** است و چیزی را به‌جای مالک ratify نمی‌کند. برای ADRهای mixed-scope، بخش Owner در ردیف FD و روش اجرای آن در ردیف Proposed آمده است؛ کل ADR با یک status ساده اشتباه برچسب نمی‌خورد.

| ID | Decision | Status | Evidence / Location | Action |
|---|---|---|---|---|
| FD-01 | ممنوعیت ACF/ACF Pro و framework مشابه custom data | OWNER_CONFIRMED | AC:L80، L460–L461، L544؛ ADR:L58 | حفظ؛ هیچ dependency یا optional adapter به ACF |
| FD-02 | custom features در Theme، بدون plugin اختصاصی مالک Core | OWNER_CONFIRMED | AC:L81، L108، L545؛ ADR:L58 | حفظ؛ ماژول داخلی با plugin اشتباه نشود |
| FD-03 | WordPress مالک Core Content | OWNER_CONFIRMED | AC:L95–L98، L546؛ ADR:L70 | استفاده از مدل native |
| FD-04 | Woo مالک Commerce؛ standard data بدون mirror | OWNER_CONFIRMED | AC:L99–L106، L121، L547 | حفظ مالکیت؛ schema سفارشی محدود |
| FD-05 | فروش آنلاین واقعی؛ conversionهای خدماتی مکمل | OWNER_CONFIRMED | AC:L43–L50، L548؛ ADR:L94–L96 | Lead-only جایگزین نشود |
| FD-06 | پرداخت ایرانی با Woo Gateway و Provider-agnostic | OWNER_CONFIRMED | AC:L229–L234، L549؛ ADR:L94–L96 | بدون Provider hard-code |
| FD-07 | عدم PSP processing در Theme | OWNER_CONFIRMED | AC:L231، L468، L550؛ ADR:L95 | حفظ؛ status rendering با verification فرق دارد |
| FD-08 | WP/Woo required؛ integrationهای نام‌برده قابل انتخاب | OWNER_CONFIRMED | AC:L63–L83، L455–L459، L551 | یک gateway فعال برای go-live اجباری؛ Provider خاص باز |
| FD-09 | Core checkout/payment/confirmation/account/history و نتایج پرداخت P0 | OWNER_CONFIRMED | AC:L51، L272، L552؛ ADR:L130 | هیچ‌کدام scope اختیاری اعلام نشود |
| FD-10 | custom presentation data/rules/settings/variant Theme-native | OWNER_CONFIRMED | AC:L122، L158، L553؛ ADR:L70 | owner قطعی؛ schemaهای Proposed جدا بمانند |
| FD-11 | read time محاسباتی، نه field دستی پیش‌فرض | OWNER_CONFIRMED | AC:L123، L554؛ ADR:L70 | الگوریتم بعداً تصویب شود |
| FD-12 | handler فرم هنوز انتخاب نشده؛ plugin لازم فرض نشود | OWNER_CONFIRMED | AC:L348–L363، L555؛ ADR:L79–L86 | اصل بازبودن قطعی؛ انتخاب handler هنوز ND-R11 |
| FD-13 | filters روی taxonomy/attributes Woo | OWNER_CONFIRMED | AC:L322، L556؛ ADR:L107 | native source حفظ شود |
| FD-14 | توقف Phase 5 | OWNER_CONFIRMED | AC:L18، L557؛ درخواست فعلی مالک | هیچ کدنویسی یا dependency |
| FD-15 | نام و Website رسمی Credits | OWNER_CONFIRMED | AC:L9–L13، L558؛ ADR:L9–L13 | حفظ دقیق؛ بدون تغییر اجرایی |
| POLICY-PB | Theme نباید به Page Builder وابسته باشد | OWNER_CONFIRMED | AC:L82، L107، L463؛ ADR:L58 | منع dependency در تمام پیشنهادهای بعدی حفظ شود |
| DISC-ADR-003 / old feature-plugin ownership | پیشنهاد تاریخی جداسازی Core Features در plugin | SUPERSEDED | ADR:L27–L35، خصوصاً L33 | صرفاً سابقه؛ به معماری فعال برنگردد |
| ADR-001 / implementation choice | Classic+theme.json و editor کنترل‌شده | PROPOSED | ADR:L44–L50؛ AC:L87 | منتظر ND-R02/03؛ Block Theme نیز هنوز candidate |
| ADR-002 / technical organization | ماژول‌بندی، degraded mode، lifecycle اجرایی | PROPOSED | AC:L85، L160–L175؛ ADR:L55، L61 | فقط مالکیت/dependency قطعی است |
| ADR-003 / schema details | keyها، schema، UI و migration تفصیلی | PROPOSED | AC:L125–L175؛ ADR:L67، L71 | AR-003/004 قبل از scope مربوط |
| ADR-004 / native handler candidate | handler داخلی یا optional delivery integration | PROPOSED | ADR:L79–L86؛ AC:L346–L363 | تصمیم جدید اتخاذ نشود |
| ADR-005 / implementation details | full state matrix، overrides، mode/tests | PROPOSED | ADR:L91، L98؛ AC:L238–L268 | مدل فروش قطعی است، روش اجرا نه؛ AR-005 |
| ADR-006 / query strategy | SSR GET، numeric mapping، live-search tuning | PROPOSED | ADR:L104، L108–L111؛ AC:L324–L336 | source ownership قطعی؛ query details نیازمند review |
| ADR-007 | URL/SEO ownership جزئی و redirect strategy | PROPOSED | ADR:L116–L123؛ AC:L179–L204، L369–L379 | plugin/route نهایی انتخاب نشده |
| ADR-008 / account implementation | endpoint UI، guest access، additional states | PROPOSED | ADR:L127، L131–L134؛ AC:L274–L284 | P0 بودن Account قطعی؛ policy جزئی نه |
| ADR-009 | الگوریتم localization و currency presentation | PROPOSED | ADR:L139–L146 | مبلغ/ارز native؛ details منتظر تصمیم |
| ADR-010 | assets، font hosting، QA/performance budgets | PROPOSED | ADR:L150–L157؛ AC:L401–L412 | metric/روش پیشنهادی را Owner Decision ننامید |
| ND-R01 | brand/slug/namespace/text domain | NEEDS_DECISION | AC:L496 | technical subset پیش از نام‌گذاری؛ Credits این ND را نمی‌بندد |
| ND-R02 | versions/editor/hosting/browser/HPOS | NEEDS_DECISION | AC:L497 | subset مؤثر بر foundation تعیین شود |
| ND-R03 | Theme type و editor scope | NEEDS_DECISION | AC:L498 | gate Foundation |
| ND-R04 | currency/tax/shipping/billing | NEEDS_DECISION | AC:L499 | پیش از commerce implementation مرتبط |
| ND-R05 | guest/registration/identity/OTP | NEEDS_DECISION | AC:L500 | پیش از Account/Checkout UI؛ اصل Account بسته |
| ND-R06 | product types/data/stock/SKU/price | NEEDS_DECISION | AC:L501 | type/scope پیش از طراحی؛ داده واقعی پیش از import/publish |
| ND-R07 | taxonomy/units/unknown/filter semantics | NEEDS_DECISION | AC:L502 | پیش از product data/filter/quiz implementation |
| ND-R08 | story/video/limitations schema/editor bounds | NEEDS_DECISION | AC:L503 | پیش از custom metadata writes |
| ND-R09 | URL/query/redirect/blog-academy IA | NEEDS_DECISION | AC:L504 | پیش از routing/migration scope |
| ND-R10 | Provider qualification و Blocks/Classic | NEEDS_DECISION | AC:L505؛ ADR:L99 | deadlines تفکیک شوند؛ انتخاب Provider نهایی خودکار به gate Foundation تبدیل نشود |
| ND-R11 | forms handler/delivery/privacy/workflow | NEEDS_DECISION | AC:L506؛ ADR:L86 | قبل از forms implementation؛ handler فعلاً هیچ |
| ND-R12 | SEO integration و schema/canonical policy | NEEDS_DECISION | AC:L507 | قبل از SEO scope/انتشار |
| ND-R13 | QA/visual/a11y/performance acceptance | NEEDS_DECISION | AC:L508 | baseline مربوط به scope پیش از تغییر UI |
| ND-R14 | scale/compare/quiz/search tuning | NEEDS_DECISION | AC:L509 | پیش از Featureهای مربوط؛ ownership Theme باز نیست |
| ND-R15 | read-time algorithm/speed | NEEDS_DECISION | AC:L510 | قبل از read time؛ نه gate عمومی Foundation |
| ND-R16 | generator/baseline/import/export/rollback | NEEDS_DECISION | AC:L511 | baseline پیش از اتکا/تولید؛ migration details در milestone مربوط |
| ND-R17 | licenses/contact/policies/deploy/cache | NEEDS_DECISION | AC:L512 | قبل از مصرف/انتشار مرتبط؛ همه موارد شرط یکسان روز اول نیستند |
| ND-R18 | language/calendar/timezone/number policy | NEEDS_DECISION | AC:L513؛ ADR:L145 | AR-006: افزودن به final summary؛ بسته تلقی نشود |
| REVIEW-GATE / phase scope | تعریف دقیق deliverable و exclusionهای Phase 5 | NEEDS_DECISION | AC:L520–L522؛ AR-001 این گزارش | تصمیم جدید با مالک؛ هیچ تعریف فاز توسط reviewer ایجاد نشده |

---

# B. Confirmed Architecture

فقط موارد زیر از تصمیم‌های قطعی مالک استخراج می‌شوند؛ این خلاصه، کل پیشنهادهای فنی قرارداد را Accepted اعلام نمی‌کند.

## B.1 Ownership

- **WordPress:** Posts، Pages، Users، Media، taxonomies زیرساخت و core settings.
- **WooCommerce:** Products، standard descriptions، SKU، prices/sale، stock، categories/attributes/gallery، cart، checkout، orders، customer commerce data و payment abstraction.
- **Theme:** design system، UI، PDP/archive presentation، Compare، Quiz، recommendation، Theme settings، custom product metadata واقعاً اختصاصی، custom forms/UI و live-search UI.
- standard data فقط در source native خود باقی می‌ماند. استفاده Theme از WP/Woo APIs به معنی ساخت source دوم نیست.

## B.2 Dependencies

- WordPress و WooCommerce dependencies رسمی‌اند.
- ACF Free/Pro، framework مشابه داده اختصاصی، plugin اختصاصی مالک Core Features و Page Builder dependency ممنوع‌اند.
- نام مشخص Provider درگاه، SEO plugin و form integration هنوز انتخاب نشده است.
- درگاه آنلاین ایرانی برای انتشار لازم است؛ optional بودن انتخاب Provider خاص، الزام Payment را حذف نمی‌کند.
- handler فرم هنوز تصمیم باز است. هیچ plugin یا handler در این audit انتخاب/ایجاد نشده است.

## B.3 Commerce و Payment

`Product → Add to Cart → Cart → Checkout → Payment → Order Confirmation`

مسیر فنی:

`Theme UI → WooCommerce Checkout → WooCommerce Payment Gateway → Iranian PSP/Gateway → Callback/Webhook → WooCommerce Order Status`

- Theme پردازش مستقیم تراکنش، verification، PSP HTTP client، credential storage و callback processor ندارد.
- Provider باید بدون patch Core Theme قابل جایگزینی/افزودن باشد.
- Order Confirmation، My Account، Order History و customer account همراه Checkout/Payment و موفقیت/شکست/لغو پرداخت P0 هستند.
- Lead-only و COD مدل اصلی نیستند؛ مشاوره رایگان، درخواست اجاره و تعمیر مکمل فروش‌اند.

## B.4 Data و Identity

- custom data/UI باید Theme-native و بدون ACF/feature plugin باشد؛ استفاده از WordPress Settings/Meta/Options APIs و Woo APIs مرتبط در این چارچوب مجاز است.
- روش دقیق schema/editor/storage key/migration هنوز یک تصمیم فنی پیشنهادی است، نه دستور تفصیلی مالک.
- read time محاسباتی است؛ field دستی تنها با ضرورت و تصمیم جداگانه.
- Credits رسمی، اصلاح‌شده با تصمیم جدید Owner: **Author: یعقوب طیبی** — **Designer: یعقوب طیبی** — **Website: https://yaghoubtayebi.ir/**؛ مقادیر جاری در مستندات نهایی حفظ شوند.

---

# C. Remaining Decisions Before Phase 5

این بخش عمداً همه ۱۸ ND را به‌عنوان blocker روز اول تکرار نمی‌کند. deadline هر ND در جدول A.7 ثبت شده است. چون Phase 5 هنوز تعریف نشده، فهرست وابسته به قابلیت را نمی‌توان بدون تصمیم مالک «حتماً قبل از شروع» اعلام کرد.

## C.1 تصمیم‌های واقعاً لازم برای بازشدن gate فعلی

| تصمیم موردنیاز | مرجع | خروجی مشخص برای مالک |
|---|---|---|
| دامنه دقیق Phase 5 و مجوز ادامه | AR-001؛ AC:L519–L522 | deliverableها، فایل‌های مجاز، inclusion/exclusion، test/exit criteria؛ اکنون همچنان هیچ کد مجاز نیست |
| انتخاب Theme type و editor contract | ND-R03؛ ADR-001 | Classic+theme.json یا Block Theme، سطح ویرایش layout/content؛ بدون ACF و Page Builder dependency |
| baseline پلتفرم و APIs | بخش مؤثر ND-R02 | WP/PHP/Woo versions و editor/APIهای مجاز برای scope انتخابی؛ سازگاری HPOS و مرورگرهای هدف به اندازه نیاز همان scope |
| هویت فنی پایدار | بخش فنی ND-R01 | Theme slug، namespace و text domain؛ الزام به نهایی‌کردن تمام branding بازاریابی از این تصمیم نتیجه نمی‌شود |
| منبع baseline و معیار اثبات همان فاز | بخش مرتبط ND-R16 و ND-R13 | بازیابی generator یا پذیرش HTML فعلی به‌عنوان reference؛ روش QA/visual/functional متناسب با scope؛ baseline tests غایب با pass ساختگی جایگزین نشوند |
| تصویب یا defer صریح جزئیات مؤثر | AR-003/004/008؛ AC:L520–L521 | هر Proposed/ND مؤثر بر deliverable یا تصویب شود یا از scope خارج و با owner/milestone مشخص defer شود؛ اجازه حدس وجود ندارد |

**این جدول تصمیم‌ها را نمی‌بندد.** اگر Phase 5 شامل custom data، checkout، account یا forms باشد، تصمیم‌های همان قابلیت از جدول A.7 نیز به gate شروع همان scope تبدیل می‌شوند. reviewer بدون تعریف فاز آن‌ها را الزام عمومی روز اول اعلام نمی‌کند.

## C.2 اصلاحات مستنداتی لازم، بدون تصمیم تجاری جدید

- **AR-006:** بازگرداندن ND-R18 به خلاصه FINAL DECISION LOG.
- **AR-005:** تفکیک اصطلاح UI activity از Woo `processing`، قبل از مصرف جدول پرداخت در طراحی/کد.
- **AR-007:** حفظ وضعیت اتمیک Owner/Proposed در readout بعدی ADRها؛ label کلی Accepted مجوز schema/provider/handler نیست.
- **AR-008:** تفکیک deadlineهای NDهای مرکب بر اساس scope مصوب؛ Provider خاص برای Foundation بی‌دلیل اجباری نشود، ولی gateway فعال برای go-live همچنان شرط قطعی است.

این موارد فقط به‌عنوان Required Correction گزارش شدند؛ source contract/ADR در این audit تغییر نکرده است.

## C.3 Verdict و توقف

**اعلام آمادگی معماری در این مرحله قابل صدور نیست.** policyهای اصلی با تصمیم مالک سازگارند، اما تعریف فاز، Theme type، platform baseline و ratification جزئیات مؤثر هنوز بازند؛ ابهام‌های مستنداتی فوق نیز ثبت شده‌اند.

- وضعیت: **NOT READY — pending scope and architecture ratification**.
- هیچ تصمیم مالک تغییر نکرده و هیچ پیشنهاد reviewer به‌عنوان Owner Decision ثبت نشده است.
- فایل‌های مبنا و تمام فایل‌های اجرایی بدون تغییر مانده‌اند؛ فقط این Review Report اضافه شده است.
- هیچ PHP، JS، CSS، Gateway، Handler، Plugin، Feature یا Dependency ساخته، نصب یا تغییر داده نشده است.
- **Phase 5 متوقف می‌ماند. گزارش برای Review مالک ارائه می‌شود.**
