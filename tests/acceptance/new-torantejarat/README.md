# ToranTejarat WooCommerce acceptance fixture

این پوشه فقط برای اجرای تست واقعی روی یک وردپرس موقت در GitHub Actions است و هیچ Dependencyای به Runtime قالب اضافه نمی‌کند.

## مرز امنیتی

- `setup-fixture.php` فقط با WP-CLI، روی `localhost/127.0.0.1` و Environment نوع `local/development` اجرا می‌شود.
- محصولات، مشتری، سفارش، ارسال و پرداخت همه مصنوعی و مخصوص Runner موقت هستند.
- هیچ Credential یا دادهٔ Production لازم نیست و نباید وارد این پوشه شود.
- صفحات Cart/Checkout عمداً Shortcode کلاسیک دارند تا Overrideهای قالب، نه Woo Blocks، آزمون شوند.

## نسخه‌ها و اجرا

Workflow `.github/workflows/woocommerce-acceptance.yml` روی PHP 8.3، WordPress 7.1.3 و WooCommerce 11.2.1 اجرا می‌شود. مرورگر با Playwright 1.64.1 و Chromium نصب‌شده در Runner اجرا می‌شود. آزمون دسترس‌پذیری با DevDependency ثابت `@axe-core/playwright` و `axe-core` نسخهٔ 4.14.0 انجام می‌شود؛ هیچ‌کدام Dependencyای به Runtime قالب نیستند.

پوشش فعلی:

- Lint واقعی PHP و Syntax تمام JS قالب؛
- هفت Settings Check با WP-CLI برای حذف Fallback فرضی Schema، یکسان‌بودن ۳۳ Default متنی Home در Panel/Frontend، قرارداد مرکب و یکتای `tab/field` و وجود Wrapper هر ۶۱ فیلد و Input فیلدهای غیر Repeater در مقصد Search، سقف Sanitizer/Renderer پرسش‌های متداول و مقصد لینک‌های Readiness؛ Optionها با `pre_option` موقت تزریق می‌شوند و این Checkها هیچ نوشتن DBای انجام نمی‌دهند؛
- ساخت Simple/Variable product و مشتری آزمایشی؛
- Shop و PDP، افزودن Simple با AJAX و Variable با Submit بومی؛
- Cart و Checkout با نشانی ایرانی، ارسال Flat Rate و COD؛
- صفحهٔ تشکر با دو قلم، Variation Meta، Totals و دستورالعمل درگاه؛
- حالت Desktop/Mobile، Overflow و CSS چاپ فاکتور؛
- Login مشتری و Add/Remove واقعی Wishlist با REST و Confirm/Cancel؛
- نه Admin Browser Check نخست در `admin-settings-smoke.mjs`: ورود ادمین و هر ۷ بخش/۶۱ فیلد با `aria-current` صفحهٔ واقعی و راهنمای Scope ذخیره، ذخیرهٔ ۶ تب مسطح و هر ۶ زیرصفحهٔ Home از مسیر واقعی `options.php` با مقایسهٔ Option خام، حفظ مقادیر خارج از Scope، Add/Remove و سقف UI پرسش‌های متداول، جست‌وجوی Combobox با `aria-selected` و Keyboard Navigation، مقصد واقعی لینک‌های Readiness، Preview/Public با `admin-post.php` و در 390px بررسی هندسی Header/Sidebar/Fields، نبود Overflow سند و ScrollContainer جدول FAQ؛
- سه Admin Check دیگر در `admin-accessibility-smoke.mjs`: پیمایش Keyboard و Focus Visible برای Search/Sidebar/Cards/Repeater/Publish با بازیابی Focus منطقی پس از حذف و بدون Submit فرم؛ ماتریس هندسی 320/390/768/1440 برای General، Publish Mode و FAQ با کنترل Search/Save و Scroll جدول؛ و اجرای واقعی axe روی هفت View نماینده در Scope `.tt-wrap` با Tagهای `wcag2a/wcag2aa/wcag21a/wcag21aa` و بدون Disable کردن Rule؛
- ثبت Console/Page errors و اسکرین‌شات‌های فروشگاه و پنل Desktop/Mobile، از جمله `admin-settings-mobile-faq-390.png` و هشت Screenshot ماتریس `admin-ui-{general,faq}-{width}.png`، به‌عنوان Artifact. هندسهٔ ماتریس در `admin-responsive-matrix.json` و نتیجهٔ کامل Violation/Incomplete/Passها در `admin-axe-report.json` ثبت می‌شود. در مجموع ۲۴ Browser Check اجرا می‌شود: ۱۱ سناریوی فروشگاه، ۱۲ سناریوی Admin و یک بررسی مشترک خطاها.

نوشتن Optionها و تغییر Publish Mode فقط در همین وردپرس موقت انجام می‌شود. Optionهای بومی دست‌کاری‌شده در پایان سناریوی Admin بازیابی می‌شوند و هیچ ارتباطی با سایت عملیاتی برقرار نمی‌شود.

نتیجهٔ axe فقط دربارهٔ همان هفت View و Ruleهای انتخاب‌شده است؛ موارد `incomplete` برای بازبینی دستی باقی می‌مانند و هیچ PASS خودکاری گواهی کامل WCAG نیست. این تست همچنین جای ماتریس کامل Gateway Sandbox، همهٔ وضعیت‌ها، سایر مرورگرها و CWV را نمی‌گیرد. ساخت Screenshot نیز به‌تنهایی Visual Review دستی محسوب نمی‌شود.
