# معماری و Wireframe بازطراحی پنل بلکوکم

**شناسه:** `ADMIN-UX-001`

**تاریخ:** 2026-10-26

**مبنای Audit:** Commit `9862b90483818fbe132c767135effcdfdde33b82`

**وضعیت:** سند طراحی پایه برای مرحلهٔ بعد؛ **هیچ کد اجرایی، Option یا دادهٔ سایت در این مرحله تغییر نکرده است.**

## ۱. تصمیم اصلی و مرز قلمرو

پنل از نظر **معماری اطلاعات، Presentation و کنترل‌های تعاملی** بازطراحی می‌شود؛ اما بازطراحی ظاهر به‌معنای پاک‌کردن تنظیمات سایت واقعی نیست.

در نخستین مرحلهٔ پیاده‌سازی (`ADMIN-UI-001`):

- هر ۷ Option و هر ۶۱ Field Key فعلی حفظ می‌شوند.
- مقدارهای ذخیره‌شده، Defaults، Sanitizerها، نام Inputها و قرارداد `_tt_scope` حفظ می‌شوند.
- Save همچنان از مسیر بومی `options.php` و Settings API انجام می‌شود؛ Auto-save، AJAX Save یا `localStorage` جایگزین آن نمی‌شود.
- هیچ فیلد تازه‌ای صرفاً برای شبیه‌شدن به تصاویر مرجع ساخته نمی‌شود.
- مدیریت محصول، سفارش، نوشته، منو و درخواست در وردپرس/WooCommerce بومی باقی می‌ماند؛ پنل قالب به آن‌ها لینک می‌دهد و رفتار/ظاهر قالب را مدیریت می‌کند.
- Reset عمومی، Import/Export و تغییر چیدمان واقعی بخش‌ها جزء این مرحله نیستند.

### واژه‌های این سند

- **موجود:** در سورس Commit مبنا دیده شده است؛ ادعای Runtime تازه نیست.
- **طرح فاز ۱:** تصمیم Presentation برای `ADMIN-UI-001`؛ هنوز پیاده‌سازی نشده است.
- **پیشنهاد بعد:** قابلیتی که به Schema، مصرف Frontend یا Migration جدا نیاز دارد و اکنون وجود ندارد.
- **دادهٔ بومی:** محتوا یا وضعیتی که منبعش وردپرس/WooCommerce است و نباید در پنل قالب کپی یا دست‌کاری شود.

## ۲. شاهد Audit ساختار فعلی

### ۲.۱. Schema و ذخیره‌سازی

منبع: `inc/settings/class-theme-settings.php`.

| Option / Tab | شروع Schema | تعداد فیلد | محتوای فعلی |
|---|---:|---:|---|
| `general` | خط ۲۹ | ۳ | نوار بالا و اعلان‌های پیش‌نمایش |
| `home` | خط ۶۰ | ۳۴ | ۳۳ مقدار Scalar + یک Repeater برای FAQ |
| `contact` | خط ۲۷۶ | ۷ | راه‌های تماس، نشانی، ساعت و نقشه |
| `social` | خط ۳۳۱ | ۴ | چهار URL ثابت شبکهٔ اجتماعی |
| `footer` | خط ۳۶۴ | ۲ | کپیرایت و نوار پایین |
| `login` | خط ۳۸۴ | ۷ | عمدتاً صفحهٔ بومی `wp-login.php` |
| `business` | خط ۴۳۹ | ۴ | دادهٔ سازمان و Schema |
| **جمع** | — | **۶۱** | — |

قراردادهای حیاتی که نباید در بازطراحی گم شوند:

- `sanitize_tab()` در خط ۵۷۴ و `_tt_scope`: ذخیرهٔ یک زیرصفحه نباید فیلدهای خارج از همان صفحه را پاک کند.
- `sanitize_value()` در خط ۶۲۷ و `sanitize_repeater()` در خط ۶۸۸: نوع داده و اعتبارسنجی سمت Server مستقل از ظاهر کنترل است.
- `torantejarat_option()` در `inc/settings/helpers.php:22`: مصرف‌کننده‌ها Option را با نام `torantejarat_<tab>` می‌خوانند.
- `torantejarat_home_defaults()` در `inc/presentation.php:260`: منبع واحد ۳۳ Scalar Default صفحهٔ اصلی است.
- FAQ سه Slot اول با Fallback موقعیتی دارد؛ `torantejarat_home_faq_slot()` در `inc/presentation.php:355` و `torantejarat_home_faq_extra()` در خط ۳۷۰. بنابراین Repeater را نمی‌توان بدون توجه به ترتیب و Fallback، یک List معمولی فرض کرد.
- Publish Mode در `inc/settings/publish-mode.php` مسیر مستقل `admin-post.php` دارد و فقط Optionهای بومی `woocommerce_coming_soon` و `blog_public` را می‌نویسد؛ بخشی از `torantejarat_general` نیست.

### ۲.۲. Navigation، Search و Renderer

- `inc/settings/panel-nav.php:61` فقط برای `home` نقشهٔ زیربخش صریح دارد. سایر Tabها در Search با Group عمومی «سایر» Index می‌شوند.
- `torantejarat_panel_nav_check()` در خط ۱۶۹ قرارداد Unplaced/Duplicated/Unknown دارد و باید در ساختار جدید حفظ شود.
- `torantejarat_panel_search_index()` در خط ۲۲۴ Index را از Schema و Nav می‌سازد، نه از مقدارهای ذخیره‌شده.
- Renderer فعلی در `class-theme-settings.php:1017` از Text/URL/Email/Tel/Textarea/Embed/Checkbox/Select/Color و Repeater پشتیبانی می‌کند.
- Media Picker، Dimension Control، Layout Picker و Icon Picker مصرف فعلی در Schema ندارند؛ نمایش آن‌ها بدون مصرف واقعی، قابلیت ساختگی خواهد بود.
- تعاملات Search در `assets/admin/panel-search.js` و Add/Remove/Focus ردیف‌ها در `assets/js/admin-settings.js` قرار دارد.
- Responsive و Focus فعلی در `assets/admin/panel.css` هستند و نباید با Shell جدید از بین بروند.

### ۲.۳. مرز مهم «ورود» و «حساب کاربری»

این مرز در UI باید صریح باشد:

- `login_logo`، `login_title`، `login_text`، `login_bg`، `login_accent` و `login_hide_lostpassword` در `inc/login.php` برای صفحهٔ بومی `wp-login.php` مصرف می‌شوند.
- `login_hide_register` علاوه بر `wp-login.php`، نمایش ثبت‌نام را در `page-templates/login-register.php:18` نیز کنترل می‌کند.
- این هفت فیلد، ظاهر Dashboard، Orders، Wishlist، Tickets، Notifications یا Security حساب WooCommerce را کنترل نمی‌کنند.

بنابراین نام فاز ۱ این بخش **«ورود و دسترسی»** است، نه «تنظیمات کامل حساب کاربری». گزارش بی‌استایل بودن حساب روی هاست نیز مسیر مستقل دارد و در این سند علت‌یابی یا اصلاح نشده است.

## ۳. اصول طراحی متناسب با بلکوکم

1. **ابزار فنی، نه داشبورد تزئینی:** اطلاعات خوانا، کنترل‌های دقیق، توضیح کوتاه و فاصله‌گذاری منظم؛ بدون Gradient و Motion غیرضروری.
2. **هویت بلکوکم:** Sidebar سرمه‌ای/زغالی، Surface سفید و نارنجی برند برای Action و نشان فعال؛ رنگ بنفش/آبی تصاویر مرجع کپی نمی‌شود.
3. **منبع واقعی داده:** قیمت، موجودی، فروش، سفارش و Review دست‌ساز در پنل ساخته نمی‌شوند.
4. **هر کنترل یک مصرف مشخص:** تا Layout دوم، Font واقعی یا Dimension مصرف‌شده در Frontend وجود ندارد، کنترل مربوط به آن اضافه نمی‌شود.
5. **جدا بودن Type از Control:** مثلاً `type=url` قرارداد داده و Sanitizer است؛ `control=media-url` فقط روش نمایش/انتخاب آن است. تغییر Control نباید نوع ذخیره را تغییر دهد.
6. **ذخیرهٔ محلی و قابل پیش‌بینی:** هر فرم فقط Scope خودش را ذخیره می‌کند و این Scope پیش از Save نوشته می‌شود.
7. **بدون تب خالی:** فروشگاه، خدمات و حساب تا داشتن فیلد واقعی، Tab خالی نمی‌گیرند؛ میان‌بر بومی و نقشهٔ مدیریت در Dashboard ارائه می‌شود.

## ۴. معماری اطلاعات فاز ۱

### ۴.۱. سطح اول Sidebar

| ترتیب | عنوان نمایشی | شناسهٔ مسیر | منبع |
|---:|---|---|---|
| ۱ | داشبورد | `dashboard` | View جدید؛ بدون Option و بدون Save |
| ۲ | عمومی و انتشار | `general` | Option فعلی + Publish Mode بومی |
| ۳ | صفحه اصلی | `home` | Option فعلی |
| ۴ | اطلاعات تماس | `contact` | Option فعلی |
| ۵ | شبکه‌های اجتماعی | `social` | Option فعلی |
| ۶ | فوتر | `footer` | Option فعلی |
| ۷ | ورود و دسترسی | `login` | Option فعلی |
| ۸ | کسب‌وکار و Schema | `business` | Option فعلی |

شناسه‌های Option تغییر نمی‌کنند. `dashboard` فقط یک Route نمایشی است و به Schema اضافه نمی‌شود.

### ۴.۲. زیرصفحه‌ها و مالکیت فیلدها

| Section | زیرصفحهٔ پیشنهادی | فیلدهای فاز ۱ | یادداشت |
|---|---|---:|---|
| `general` | `topbar` — نوار بالای سایت | ۲ | متن و لینک |
| `general` | `preview` — اعلان پیش‌نمایش | ۱ | جدا از عمومی/خصوصی بودن سایت |
| `general` | `publish` — حالت انتشار | ۰ | فرم مستقل `admin-post.php` و Nonce فعلی |
| `home` | `hero` — هیرو | ۸ | فقط Copy فعلی |
| `home` | `trust` — نوار اعتماد | ۴ | چهار Slot ثابت |
| `home` | `category` — دسته‌ها | ۳ | Copy؛ کارت‌ها از WooCommerce می‌آیند |
| `home` | `field` — بخش فنی و ویدیو | ۱۱ | Copy؛ فایل ویدیو فعلاً Asset ثابت است |
| `home` | `blog` — مجله | ۴ | Copy؛ کارت‌ها از نوشته‌ها می‌آیند |
| `home` | `faq` — پرسش‌های متداول | ۴ | سه Copy + Repeater |
| `contact` | `channels` — راه‌های ارتباطی | ۴ | تلفن، واتساپ و ایمیل |
| `contact` | `location` — نشانی و حضور حضوری | ۳ | نشانی، ساعت و Embed نقشه |
| `social` | `profiles` — پروفایل‌ها | ۴ | صفحهٔ واحد؛ Label بهتر برای Search |
| `footer` | `content` — متن‌های فوتر | ۲ | صفحهٔ واحد |
| `login` | `content` — محتوای ورود وردپرس | ۳ | لوگو، عنوان و متن |
| `login` | `appearance` — ظاهر ورود وردپرس | ۲ | دو رنگ |
| `login` | `access` — قواعد دسترسی | ۲ | ثبت‌نام و بازیابی رمز |
| `business` | `organization` — هویت سازمان | ۲ | نام و نوع |
| `business` | `schema-details` — جزئیات Schema | ۲ | شهر و محدودهٔ قیمت |

**پوشش:** ۱۸ Group نمایشی + Dashboard؛ هر ۶۱ فیلد دقیقاً یک مالک دارند. Group تک‌فیلدی/تک‌صفحه‌ای لزوماً به چند URL شکسته نمی‌شود؛ می‌تواند فقط Label ساختاری برای Search و Heading باشد.

`dashboard` و `publish` به‌عنوان **Utility Route** از Field Map جدا می‌مانند. افزودن یک Group با `fields=[]` به تابع فعلی کافی نیست: `torantejarat_panel_nav()` Group خالی را حذف می‌کند و `render_fields()` نیز List خالی را به همهٔ فیلدهای Tab برمی‌گرداند. Renderer مرحلهٔ بعد باید این دو Route را صریح و جدا مدیریت کند تا صفحهٔ Publish به‌اشتباه فرم همهٔ فیلدهای General را نشان ندهد.

### ۴.۳. Dashboard

فاز ۱ فقط اطلاعات واقعی و مسیرهای قابل اثبات را نشان می‌دهد:

- Readiness فعلی از `torantejarat_panel_readiness()`؛ بدون تبدیل آن به ادعای کامل «سلامت سایت» یا «آمادگی قطعی Production».
- لینک «مشاهده سایت» و لینک مستقیم به بخش‌های اصلی تنظیمات.
- میان‌بر به مدیریت بومی محصولات، سفارش‌ها، درخواست‌های `torantejarat_request`، نوشته‌ها، منوها و هویت سایت.
- مسیر سفارش‌ها و سایر Deep Linkهای WooCommerce هنگام پیاده‌سازی با نسخهٔ واقعی Woo/HPOS بررسی می‌شود؛ URL حدسی Hardcode نمی‌شود.
- شمارنده‌های واقعی موجود در Widget پیشخوان (`inc/admin.php:81`) در فاز ۱ تکرار نمی‌شوند. اگر بعداً به Dashboard پنل بیایند، ابتدا یک Helper Read-only مشترک ساخته می‌شود؛ عدد نمونه یا Query موازی مستقل مجاز نیست.
- در Dashboard و Overviewهای بدون فرم، دکمهٔ Save نمایش داده نمی‌شود.

### ۴.۴. Deep Link و Redirectهای وابسته

تغییر Groupها باید همراه مقصدهای فعلی باشد؛ در غیر این صورت لینک درست به Overview می‌رود، نه فیلد موردنظر. در `ADMIN-UI-001` این موارد همراه Shell به‌روز می‌شوند:

| مبدأ | مقصد فاز ۱ |
|---|---|
| Readiness تلفن/واتساپ/ایمیل نمونه | `contact/channels` |
| Readiness نشانی نمونه | `contact/location` |
| Readiness لینک محلی نوار بالا | `general/topbar` |
| Readiness وضعیت Preview/غیرعمومی | `general/publish` |
| Readiness شبکهٔ اجتماعی نمونه | `social/profiles` |
| Redirect نتیجهٔ Publish Mode | `general/publish` |
| لینک ایمیل مدیر و فرستندهٔ Woo | مقصد بومی فعلی؛ بدون تغییر |

- الگوی DOM فعلی `tt-<tab>-<field>` و `ttf-<tab>-<field>` و شناسهٔ مرکب `tab/field` حفظ می‌شود.
- URLهای فعلی Home با `section/sub` و Alias ورودی `tab` نباید در بازطراحی شکسته شوند.
- به‌روزرسانی Redirect در `inc/settings/publish-mode.php` فقط مسیر بازگشت را به زیرصفحهٔ جدید می‌برد؛ Nonce، Capability و Optionهای نوشته‌شده تغییر نمی‌کنند.

## ۵. Wireframe

این Wireframe ساختار و سلسله‌مراتب را نشان می‌دهد، نه اندازهٔ نهایی پیکسل. در RTL، Sidebar داخلی سمت راست محتواست.

```text
┌──────────────────────────────────────────────────────────────────────┐
│ بلکوکم / مسیر بخش      جست‌وجوی تنظیمات      مشاهده سایت      ذخیره  │
├───────────────────────────────────────────┬──────────────────────────┤
│                                           │ داشبورد                  │
│ عنوان صفحه                                │ عمومی و انتشار           │
│ توضیح کوتاه + مشاهده در سایت              │   نوار بالای سایت        │
│                                           │   اعلان پیش‌نمایش        │
│ ┌───────────────────────────────────────┐ │   حالت انتشار            │
│ │ کارت گروه فیلد                        │ │ صفحه اصلی               │
│ │ توضیح کوتاه گروه                      │ │   هیرو / اعتماد / ...     │
│ │ Label + راهنما      کنترل             │ │ اطلاعات تماس             │
│ │ Label + راهنما      کنترل             │ │ شبکه‌های اجتماعی         │
│ └───────────────────────────────────────┘ │ فوتر                     │
│ ┌───────────────────────────────────────┐ │ ورود و دسترسی             │
│ │ کارت گروه دوم / Repeater              │ │ کسب‌وکار و Schema         │
│ └───────────────────────────────────────┘ │                          │
│ متن Scope ذخیره                          │                          │
└───────────────────────────────────────────┴──────────────────────────┘
```

### صفحهٔ Overview صفحه اصلی

به‌جای نمایش فقط شش کارت Copy، فهرست همهٔ بخش‌های واقعی `front-page.php` دیده می‌شود. هر کارت یکی از این سه برچسب را دارد:

- **قابل ویرایش در قالب:** لینک به زیرصفحهٔ فیلدها.
- **دادهٔ بومی:** لینک به مدیریت واقعی محصول/دسته/نوشته/منو.
- **بدون کنترل فعلی:** توضیح صریح و ثبت به‌عنوان پیشنهاد بعد؛ نه دکمهٔ بی‌عملکرد.

## ۶. قرارداد کنترل‌ها

| کنترل | تصمیم فاز ۱ | مرز و دسترس‌پذیری |
|---|---|---|
| Text / Textarea | Label بالای کنترل یا در ستون مشخص، راهنمای نزدیک | `aria-describedby` برای راهنما؛ بدون Placeholder به‌جای Label |
| URL / Email / Tel | کنترل LTR داخل پنل RTL؛ لینک «بازکردن» فقط برای URL معتبر و غیرخالی | مقدار و نام Input حفظ می‌شود |
| Select | Select بومی برای گزینه‌های چندتایی | Keyboard و Label بومی حفظ می‌شود |
| Enum `yes/no` | نمایش Switch/Segmented فقط با Submit مقدار واقعی `yes/no` | تبدیل به `1/0` ممنوع؛ Sanitizer فعلی حفظ می‌شود |
| Checkbox | نمایش Switch برای `0/1` | Checkbox بومی و Label قابل کلیک؛ متن وضعیت فقط به رنگ وابسته نیست |
| Color | Swatch + مقدار HEX قابل ویرایش | هر دو View یک مقدار دارند؛ Fallback Sanitizer فعلی حفظ می‌شود |
| Media + URL | فقط برای `login_logo`؛ انتخاب از Media Library و همچنان ذخیرهٔ URL | «حذف انتخاب» فیلد را خالی می‌کند و فایل رسانه را حذف نمی‌کند؛ Fallback دستی URL باقی می‌ماند |
| Repeater | کارت‌های جمع‌شونده به‌جای جدول عریض، با Add/Remove و شمارهٔ ردیف | نام‌های فعلی، حداکثر ۱۲ و Focus حفظ می‌شود؛ Collapse فقط با JS و محتوای No-JS باز است؛ حذف تا Save در DB اعمال نمی‌شود |
| Embed | Code Textarea با راهنما و لینک مشاهدهٔ Frontend | Preview خام iframe داخل wp-admin در فاز ۱ اجرا نمی‌شود |
| Link به Frontend | بازکردن صفحهٔ واقعی در Tab جدید | Live Preview ساختگی یا iframe داخل پنل ساخته نمی‌شود |

### قواعد اختصاصی Media

- پیاده‌سازی `media-url` پیش از کدنویسی با سورس/مستندات رسمی WordPress و Capability مربوط بررسی می‌شود.
- انتخاب به تصویر محدود می‌شود و مقدار URL همان فیلد فعلی را می‌سازد؛ Attachment ID جدید یا Migration لازم نیست.
- اگر کاربر Capability لازم برای Media Library را نداشته باشد، کنترل انتخاب غیرفعال/حذف و ورودی URL قابل استفاده باقی می‌ماند.
- حذف انتخاب به‌معنای حذف فایل از کتابخانه نیست و هیچ عملیات مخربی انجام نمی‌دهد.
- بدون JavaScript، ورودی URL و Save بومی همچنان کار می‌کند.

### کنترل‌هایی که فعلاً ساخته نمی‌شوند

- **Typography:** تا Audit متغیرهای CSS و فونت‌های واقعی، فقط یک طرح کاندیدا است.
- **Dimension/Range:** فیلد مصرف‌کننده برای Container یا Spacing وجود ندارد؛ Slider صرفاً تزئینی نمی‌شود.
- **Layout Picker:** تا وجود Layout دوم و قواعد Fallback، گزینهٔ تصویری ساخته نمی‌شود.
- **Icon Picker:** آیکون‌های Benefit و Category فعلاً در PHP ثابت‌اند؛ ابتدا Schema و مصرف Frontend لازم است. در آینده فقط Allowlist از `torantejarat_icon()`، نه SVG دلخواه.
- **Sortable عمومی:** ترتیب سه Slot اول FAQ معنای Fallback دارد؛ Drag/Drop در فاز ۱ فعال نمی‌شود.
- **Reset All / JSON خام:** در مسیر اصلی کاربر قرار نمی‌گیرند.

## ۷. نگاشت کامل ۶۱ فیلد

ستون «کنترل هدف» طرح فاز ۱ است؛ Type فعلی و قرارداد داده تغییر نمی‌کند. مسیر Schema همهٔ ردیف‌ها `inc/settings/class-theme-settings.php` است.

<!-- FIELD-MAP:START -->

### ۷.۱. `general` — ۳ فیلد

مصرف اصلی: `header.php:29-35` و `inc/settings/helpers.php:50`.

| Option | زیرصفحه | Field | Type فعلی | کنترل هدف | مصرف / قرارداد |
|---|---|---|---|---|---|
| `general` | `topbar` | `topbar_text` | `text` | Text | متن نوار؛ خالی = توضیح سایت |
| `general` | `topbar` | `topbar_link` | `url` | URL + بازکردن | لینک کل متن نوار؛ مقصد دلخواه |
| `general` | `preview` | `preview_note` | `select` | Switch/Segmented با `yes/no` | اعلان‌های پیش‌نمایش؛ تغییر Visibility سایت نیست |

### ۷.۲. `home` — ۳۴ فیلد

مصرف Scalarها از `front-page.php` از طریق `torantejarat_home()` است. FAQ از Helperهای `inc/presentation.php:318-371` خوانده می‌شود.

| Option | زیرصفحه | Field | Type فعلی | کنترل هدف | مصرف / قرارداد |
|---|---|---|---|---|---|
| `home` | `hero` | `hero_kicker` | `text` | Text | پیش‌تیتر هیرو |
| `home` | `hero` | `hero_title_1` | `text` | Text | خط اول H1 |
| `home` | `hero` | `hero_title_2` | `text` | Text | خط رنگی H1 |
| `home` | `hero` | `hero_lead` | `textarea` | Textarea | توضیح هیرو |
| `home` | `hero` | `hero_cta_primary` | `text` | Text | فقط Label؛ مقصد فعلی Shop است |
| `home` | `hero` | `hero_cta_secondary` | `text` | Text | فقط Label؛ مقصد فعلی برگهٔ `quiz` است |
| `home` | `hero` | `hero_foot_1` | `text` | Text | نشانهٔ پایین هیرو ۱ |
| `home` | `hero` | `hero_foot_2` | `text` | Text | نشانهٔ پایین هیرو ۲ |
| `home` | `trust` | `benefit_1` | `text` | Text | Slot ثابت ۱؛ آیکون فعلی ثابت است |
| `home` | `trust` | `benefit_2` | `text` | Text | Slot ثابت ۲؛ آیکون فعلی ثابت است |
| `home` | `trust` | `benefit_3` | `text` | Text | Slot ثابت ۳؛ آیکون فعلی ثابت است |
| `home` | `trust` | `benefit_4` | `text` | Text | Slot ثابت ۴؛ آیکون فعلی ثابت است |
| `home` | `category` | `cat_eyebrow` | `text` | Text | پیش‌تیتر؛ کارت‌ها در این فیلد نیستند |
| `home` | `category` | `cat_title` | `text` | Text | تیتر بخش دسته‌ها |
| `home` | `category` | `cat_text` | `textarea` | Textarea | توضیح بخش دسته‌ها |
| `home` | `field` | `field_eyebrow` | `text` | Text | پیش‌تیتر بخش فنی |
| `home` | `field` | `field_title` | `text` | Text | تیتر بخش فنی |
| `home` | `field` | `field_text` | `textarea` | Textarea | توضیح بخش فنی |
| `home` | `field` | `field_check_1` | `text` | Text | چک‌لیست ثابت ۱ |
| `home` | `field` | `field_check_2` | `text` | Text | چک‌لیست ثابت ۲ |
| `home` | `field` | `field_check_3` | `text` | Text | چک‌لیست ثابت ۳ |
| `home` | `field` | `field_link_label` | `text` | Text | فقط Label؛ مقصد Contact با `topic=video` |
| `home` | `field` | `video_label_1` | `text` | Text/LTR | برچسب لاتین بالای ویدیو |
| `home` | `field` | `video_label_2` | `text` | Text/LTR | برچسب لاتین شماره/وضعیت |
| `home` | `field` | `video_aria` | `text` | Text | نام دسترس‌پذیر ویدیو؛ حفظ راهنمای ARIA |
| `home` | `field` | `video_caption` | `text` | Text | زیرنویس؛ منبع MP4 فعلاً Asset ثابت است |
| `home` | `blog` | `blog_eyebrow` | `text` | Text | پیش‌تیتر مجله |
| `home` | `blog` | `blog_title` | `text` | Text | تیتر مجله |
| `home` | `blog` | `blog_text` | `textarea` | Textarea | توضیح مجله |
| `home` | `blog` | `blog_link` | `text` | Text | فقط Label لینک به مجله |
| `home` | `faq` | `faq_eyebrow` | `text` | Text | پیش‌تیتر FAQ |
| `home` | `faq` | `faq_title` | `text` | Text | تیتر FAQ |
| `home` | `faq` | `faq_link` | `text` | Text | فقط Label لینک به Contact |
| `home` | `faq` | `faq` | `repeater` | Card Repeater | زیرفیلدهای `q/a`؛ حداکثر ۱۲ و Fallback موقعیتی |

### ۷.۳. `contact` — ۷ فیلد

مصرف: `template-parts/contact-cards.php`، `template-parts/footer-brand.php`، `page-templates/request.php` و `inc/schema/class-org-schema.php` بر اساس جدول زیر.

| Option | زیرصفحه | Field | Type فعلی | کنترل هدف | مصرف / قرارداد |
|---|---|---|---|---|---|
| `contact` | `channels` | `phone` | `tel` | Tel/LTR | لینک تماس، فوتر و Schema |
| `contact` | `channels` | `phone_display` | `text` | Text با `dir=auto` | متن نمایشی تلفن در کارت و فوتر |
| `contact` | `channels` | `whatsapp` | `text` | Text/LTR | ساخت لینک `wa.me` در کارت تماس |
| `contact` | `channels` | `email` | `email` | Email/LTR | لینک `mailto` در کارت تماس |
| `contact` | `location` | `address` | `textarea` | Textarea | کارت نشانی و `streetAddress` در Schema |
| `contact` | `location` | `working_hours` | `text` | Text | ابزارک ساعت کاری صفحهٔ تماس |
| `contact` | `location` | `map_embed` | `embed` | Code Textarea/LTR | Embed صفحهٔ تماس؛ Sanitizer فعلی حفظ می‌شود |

### ۷.۴. `social` — ۴ فیلد

هر چهار URL در `template-parts/footer-brand.php`، `page-templates/request.php` و `sameAs` در `inc/schema/class-org-schema.php` مصرف می‌شوند. چهار Network ثابت‌اند و در فاز ۱ به Repeater تبدیل نمی‌شوند.

| Option | زیرصفحه | Field | Type فعلی | کنترل هدف | مصرف / قرارداد |
|---|---|---|---|---|---|
| `social` | `profiles` | `instagram` | `url` | URL/LTR + آیکون | پروفایل اینستاگرام |
| `social` | `profiles` | `telegram` | `url` | URL/LTR + آیکون | پروفایل تلگرام |
| `social` | `profiles` | `aparat` | `url` | URL/LTR + آیکون | پروفایل آپارات |
| `social` | `profiles` | `youtube` | `url` | URL/LTR + آیکون | پروفایل یوتیوب |

### ۷.۵. `footer` — ۲ فیلد

مصرف هر دو در `footer.php:32-39` است. منوهای سه‌ستونهٔ فوتر از `wp_nav_menu()` می‌آیند و جزء این دو Option نیستند.

| Option | زیرصفحه | Field | Type فعلی | کنترل هدف | مصرف / قرارداد |
|---|---|---|---|---|---|
| `footer` | `content` | `copyright_text` | `textarea` | Textarea | خالی = نام و توضیح سایت |
| `footer` | `content` | `bar_text` | `text` | Text | متن نوار پایین فوتر |

### ۷.۶. `login` — ۷ فیلد

مصرف اصلی `inc/login.php` است؛ استثنای ثبت‌نام در ردیف خودش آمده است. عنوان صفحه صریحاً «ورود وردپرس» خواهد بود تا با Account سفارشی مخلوط نشود.

| Option | زیرصفحه | Field | Type فعلی | کنترل هدف | مصرف / قرارداد |
|---|---|---|---|---|---|
| `login` | `content` | `login_logo` | `url` | Media + URL | لوگوی `wp-login.php`؛ Fallback Custom Logo و Asset |
| `login` | `content` | `login_title` | `text` | Text | تیتر `wp-login.php`؛ خالی = نام سایت |
| `login` | `content` | `login_text` | `textarea` | Textarea | متن زیر تیتر `wp-login.php` |
| `login` | `appearance` | `login_bg` | `color` | Color + HEX | پس‌زمینهٔ `wp-login.php` |
| `login` | `appearance` | `login_accent` | `color` | Color + HEX | دکمه/Focus صفحهٔ `wp-login.php` |
| `login` | `access` | `login_hide_register` | `checkbox` | Switch با `0/1` | ورود Core و صفحهٔ سفارشی ثبت‌نام |
| `login` | `access` | `login_hide_lostpassword` | `checkbox` | Switch با `0/1` + هشدار | فقط لینک بازیابی در `wp-login.php` |

### ۷.۷. `business` — ۴ فیلد

هر چهار فیلد در `inc/schema/class-org-schema.php:48-90` مصرف می‌شوند. مقدارهای خالی نباید با دادهٔ ساختگی پر شوند.

| Option | زیرصفحه | Field | Type فعلی | کنترل هدف | مصرف / قرارداد |
|---|---|---|---|---|---|
| `business` | `organization` | `business_name` | `text` | Text | نام سازمان؛ خالی = نام سایت |
| `business` | `organization` | `business_type` | `select` | Select | فقط چهار Type مجاز Schema |
| `business` | `schema-details` | `city` | `text` | Text | `addressLocality` فقط با شهر واقعی |
| `business` | `schema-details` | `price_range` | `select` | Select با مقدار LTR | خالی = حذف `priceRange` از خروجی |

<!-- FIELD-MAP:END -->

## ۸. نقشهٔ مدیریت صفحه اصلی؛ فراتر از ۳۴ فیلد فعلی

ترتیب زیر از `front-page.php` خوانده شده است. این جدول قابلیت موجود را از پیشنهاد آینده جدا می‌کند.

| بخش | منبع فعلی | محل مدیریت فاز ۱ | پیشنهاد بعد؛ پیاده‌سازی‌نشده |
|---|---|---|---|
| Hero Copy | ۸ فیلد `home/hero` | زیرصفحهٔ هیرو | URL مستقل CTA با Link Picker |
| Hero Visual | محصولات واقعی با SKUهای اولویت‌دار و Fallback به تازه‌ترین‌ها؛ در نبود Slide، تصویر شاخص برگه؛ `inc/woocommerce.php:404` و `front-page.php:47-70` | لینک مدیریت محصولات/برگهٔ خانه | Product Picker اختصاصی، بدون کپی قیمت/موجودی |
| Benefit Strip | چهار Slot متنی و آیکون ثابت | زیرصفحهٔ نوار اعتماد | Repeater/Icon Picker فقط با Migration و مصرف Frontend |
| Category Grid | چهار Slug فیلترشدنی + Term واقعی؛ `inc/woocommerce.php:353` | Copy در قالب + لینک دسته‌های Woo | Term Picker برای انتخاب/ترتیب دسته‌ها |
| Deals | حداقل دو محصول On-sale واقعی؛ `template-parts/home-deals.php` | لینک محصولات/تخفیف‌های Woo | عنوان و Visibility اختیاری؛ بدون فروش یا تخفیف ساختگی |
| Finder | دسته، `pa_cable-length` و قیمت واقعی؛ `inc/finder.php` | لینک دادهٔ بومی | تنظیم Copy و رفتار پس از Scope مستقل |
| Featured/Product Slider | محصولات Featured، سپس Fallback به تازه‌ترین‌ها | لینک محصولات | انتخاب منبع/عنوان با حفظ Fallback صادقانه |
| Field/Video | ۱۱ فیلد Copy + MP4 ثابت در `front-page.php:125-127` | زیرصفحهٔ بخش فنی | Media Field برای ویدیو و Poster |
| Services | `torantejarat_home_services()` و URL صفحه بر اساس Slugهای `rent/repairs/blog` | لینک برگه‌های بومی مربوط | ویرایش Copy و Link با مقادیر پیش‌فرض فعلی |
| Bestsellers | `total_sales` واقعی و حداقل دو محصول واجد شرایط | لینک محصولات/سفارش‌ها | فقط Copy/Visibility؛ فروش هرگز دستی وارد نمی‌شود |
| Latest Products | تاریخ واقعی محصولات و شرط حداقل Catalog | لینک محصولات | فقط Copy/Visibility |
| Blog | سه نوشتهٔ واقعی منتشرشده | Copy در قالب + لینک نوشته‌ها | تعداد/منبع نمایش با Query مشخص |
| FAQ | سه Slot با Fallback + ردیف‌های اضافی | Card Repeater | مرتب‌سازی فقط پس از حفظ معنای Slot و Preview Notice |

### قاعدهٔ Visibility و Order آینده

اگر نمایش/چیدمان بخش‌ها اضافه شود:

- Default ساختار باید دقیقاً ترتیب فعلی را بازتولید کند.
- شرط‌های صادقانهٔ Auto-hide (مثل نبود محصول تخفیف‌دار) حذف نمی‌شوند؛ روشن‌بودن Toggle نباید بخش خالی یا دادهٔ جعلی بسازد.
- Migration و مقدار پیش‌فرض پیش از افزودن UI نوشته و آزمون می‌شود.
- محتوای بومی و ترتیب آن در Option قالب کپی نمی‌شود؛ فقط ارجاع/انتخاب ذخیره می‌شود.

## ۹. Design System، Responsive و Accessibility

### ۹.۱. توکن‌های پیشنهادی

| نقش | مقدار پیشنهادی | کاربرد |
|---|---|---|
| Sidebar | `#172325` | پس‌زمینهٔ ناوبری |
| Sidebar hover/active | `#232d2e` | سطح فعال؛ همراه نشان غیررنگی |
| متن اصلی | `#1d2327` | عنوان و متن روی Surface روشن |
| متن فرعی | `#50575e` | راهنما و Metadata |
| متن Sidebar | `#cbd2ce` / `#f0f3f1` | متن عادی/فعال |
| Brand | `#f77b3e` | Fill دکمه با متن تیره و Accent |
| State روی روشن | `#b34700` | لینک/وضعیت فعال با Contrast مناسب |
| Surface | `#ffffff` | کارت و فرم |
| Border | `#dcdcde` | مرز کارت و کنترل |

محاسبهٔ ایستای جفت‌های اصلی: متن تیره روی Brand عادی **5.94:1** و Hover **4.96:1**؛ متن فرعی روی سفید **7.33:1**؛ `#cbd2ce` روی Sidebar **10.47:1**؛ State روی `#fff5ef` **5.12:1**. این فقط محاسبهٔ جفت‌رنگ است، نه گواهی WCAG یا جایگزین axe و بازبینی واقعی.

### ۹.۲. ابعاد و چیدمان

- Sidebar دسکتاپ حدود ۲۷۲px و Sticky داخل Shell؛ `position:fixed` سراسری که با Sidebar خود وردپرس تداخل کند استفاده نمی‌شود.
- محتوای خوانا با حداکثر عرض کنترل‌شده؛ فرم از تمام عرض صفحهٔ عریض کشیده نمی‌شود.
- ریتم فاصلهٔ ۸px، Padding کارت ۲۰–۲۴px و حداقل ارتفاع ۴۴px برای کنترل‌های لمسی.
- Labelها حداقل ۱۴px و راهنماها خوانا؛ متن ۱۰–۱۱px برخی تصاویر مرجع الگو نیست.
- ماشین‌خوان‌ها (`URL`، `Email`، `Tel`، `HEX`) LTR و متن فارسی RTL می‌مانند.

### ۹.۳. موبایل و تبلت

- در Viewport کوچک، Sidebar به Drawer قابل دسترس تبدیل می‌شود: دکمهٔ باز/بسته با `aria-expanded`، بستن با Escape، بازگشت Focus به Trigger و مدیریت Focus در حالت Modal.
- بدون JavaScript، ناوبری در جریان صفحه و فرم بومی قابل استفاده باقی می‌ماند.
- ذخیره در نوار پایین موبایل به همان Form واقعی اشاره می‌کند؛ Save جداگانه یا Auto-save نداریم.
- Repeater کارتی دیگر به Scroll افقی جدول نیاز ندارد؛ نام‌ها و ترتیب Submit حفظ می‌شود.
- ماتریس حداقل ۳۲۰، ۳۹۰، ۷۶۸ و ۱۴۴۰ پیکسل با Sidebar وردپرس باز و جمع‌شده بررسی می‌شود.

### ۹.۴. Keyboard و Screen Reader

- فقط لینک صفحهٔ واقعی `aria-current="page"` می‌گیرد؛ والد باز، Current دوم نمی‌سازد.
- Search با ArrowUp/ArrowDown، Enter و Escape و هماهنگی `aria-activedescendant`/`aria-selected` حفظ می‌شود.
- Focus پس از Add/Remove در Repeater از دست نمی‌رود؛ حذف ردیف Focus را به `body` نمی‌اندازد.
- Focus Visible حداقل ۲px و واضح روی Surface روشن و Sidebar تیره است.
- Groupهای مرتبط با Heading و در صورت نیاز `fieldset/legend` معنا می‌گیرند؛ راهنما با `aria-describedby` متصل می‌شود.
- Motion ضروری نیست و `prefers-reduced-motion` محترم شمرده می‌شود.
- رنگ، تنها حامل وضعیت خطا/موفقیت/فعال نیست.

## ۱۰. Save، خروج و وضعیت تغییرات

1. دکمهٔ Save فقط روی صفحهٔ دارای یک فرم Settings API ظاهر می‌شود و همان فرم را Submit می‌کند.
2. Publish Mode همچنان دکمه و فرم مستقل خودش را دارد؛ Save عمومی آن را اجرا نمی‌کند.
3. تغییر Input فقط وضعیت «ذخیره‌نشده» را نشان می‌دهد و هرگز پیام موفقیت ذخیره تولید نمی‌کند.
4. خروج از صفحه یا Search هنگام تغییر ذخیره‌نشده، هشدار بومی و قابل Keyboard می‌دهد؛ Submit واقعی نباید هشدار خروج اشتباه بسازد.
5. پیام موفقیت بر اساس چرخهٔ واقعی Save/Notice وردپرس است، نه صرفاً کلیک یا Response ساختگی.
6. متن Scope کنار Save می‌ماند: «ذخیره این فرم فقط فیلدهای همین صفحه را تغییر می‌دهد.»
7. هیچ مقدار فرم در `localStorage` ذخیره نمی‌شود و Reset خودکار پس از خطا انجام نمی‌شود.

## ۱۱. مرحله‌بندی بعد از این سند

### `ADMIN-UI-001` — Shell و کنترل‌های فاز ۱

- Sidebar و Header جدید، Dashboard ساده، همهٔ Groupهای این سند و Search به‌روزشده.
- کنترل‌های Media + URL، Color + HEX، Switch و Card Repeater فقط برای فیلدهای نگاشت‌شده.
- حفظ Optionها، Defaults، `_tt_scope`، Sanitizer، Search/Focus و No-JS fallback.
- هیچ Option یا قابلیت Frontend جدیدی در این مرحله تبلیغ نمی‌شود.

### `ADMIN-FEAT-BRAND-001` — هویت و ظاهر واقعی

- Audit متغیرهای CSS، Custom Logo، فونت و Menuها.
- فقط کنترل‌هایی اضافه می‌شوند که مصرف Frontend و Sanitizer مشخص دارند.
- لینک به تنظیمات بومی برای داده‌هایی که نباید در قالب تکرار شوند.

### `ADMIN-FEAT-HOME-001` — مدیریت کامل‌تر صفحه اصلی

- URL دکمه‌ها، ویدیو، Services و انتخاب دسته‌ها در کارهای کوچک و جدا.
- Visibility/Order فقط با Migration، Default برابر وضعیت فعلی و تست Auto-hide.

### `ADMIN-FEAT-SHOP-001` و `ADMIN-FEAT-ACCOUNT-001`

- ابتدا Audit مصرف و نیاز واقعی Shop/PDP/Checkout/Account.
- لینک مدیریت بومی WooCommerce از تغییر ظاهر قالب جدا می‌ماند.
- گزارش فعلی بی‌استایل بودن حساب پیش از طراحی قابلیت جدید، با URL/Screenshot و شاهد مرورگر جدا بررسی می‌شود.

### `ADMIN-SYSTEM-001` — Export/Import

- Export فقط Optionهای Allowlist‌شدهٔ قالب، نه Backup کامل سایت یا دادهٔ محصول/سفارش.
- Version و Schema مشخص، Nonce/Capability، اعتبارسنجی سمت Server و Preview تفاوت‌ها پیش از Confirm.
- هیچ Import یا Reset روی Production بدون تأیید صریح و Backup قابل بازیابی انجام نمی‌شود.

## ۱۲. Acceptance مورد نیاز برای `ADMIN-UI-001`

این‌ها برنامهٔ تست مرحلهٔ بعد‌اند، نه نتیجهٔ اجراشده در این مرحله:

### قرارداد و ذخیره‌سازی

- شمارش دقیق ۷ Option و ۶۱ Field؛ نبود Field جدید/حذف‌شده در Shell.
- هر Field دقیقاً یک Group و یک Search Result دارد؛ Unknown/Duplicated/Unplaced صفر است.
- Save هر زیرصفحه با Sentinel روی Option خام: مقدار هدف تغییر می‌کند و همهٔ کلیدهای خارج از Scope دست‌نخورده می‌مانند.
- `preview_note` همچنان `yes/no` و Checkboxها `0/1` ذخیره می‌کنند.
- FAQ با Add/Remove، سقف ۱۲، Legacy Overflow، سه Slot و No-JS trailing blank بررسی می‌شود.
- Media انتخاب/پاک‌کردن را با URL و بدون حذف Attachment تست می‌کند؛ URL دستی و No-JS نیز Save می‌شود.
- Publish Mode فقط دو Option بومی خودش را تغییر می‌دهد و Redirect به `general/publish` می‌رسد.
- هر لینک Readiness و Search به Group و Fragment فیلد واقعی می‌رسد؛ URLهای قبلی Home و Alias `tab` نیز بررسی می‌شوند.

### مرورگر و دسترس‌پذیری

- Search و همهٔ مسیرهای Save/Drawer/Repeater با Keyboard.
- Focus پس از Add/Remove و بازگشت Focus از Drawer.
- ماتریس ۳۲۰/۳۹۰/۷۶۸/۱۴۴۰ در RTL، بدون Document Overflow و با Sidebar وردپرس باز/بسته.
- axe روی نمای دسکتاپ/موبایل هر نوع صفحه؛ موارد `incomplete` دستی بازبینی می‌شوند و PASS اعلام نمی‌شوند.
- Visual Review واقعی Screenshotها و بازبینی Contrast در حالت‌های Default/Hover/Focus/Disabled/Error.
- Chromium و در صورت آماده‌سازی Harness، Firefox/WebKit؛ فقط نتیجهٔ واقعاً اجراشده گزارش می‌شود.

### محیط و داده

- اجرای Runtime فقط در WordPress ایزوله یا Staging؛ Option خام قبل/بعد ثبت می‌شود.
- هیچ تست Save/Import روی Production انجام نمی‌شود.
- نبود Harness یا اجرا نشدن هر Check صریح گزارش می‌شود؛ PASS تاریخی Commit قبلی جای تست نسخهٔ جدید را نمی‌گیرد.

## ۱۳. نتیجهٔ `ADMIN-UX-001`

- تصمیم معماری، Wireframe، Map همهٔ ۶۱ فیلد، مرز دادهٔ بومی و معیارهای پذیرش مشخص شد.
- در این مرحله فقط سند طراحی و لاگ توسعه ایجاد/به‌روز می‌شوند.
- هیچ PHP، CSS، JS، Schema، Option یا دادهٔ سایت تغییر نکرده است.
- هیچ Runtime Test، Browser Test یا ادعای Production Readiness برای این مرحله وجود ندارد.
- مرحلهٔ اجرایی بعدی `ADMIN-UI-001` است و باید در Commitهای کوچک و قابل بازبینی انجام شود.
