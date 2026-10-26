# ToranTejarat WooCommerce acceptance fixture

این پوشه فقط برای اجرای تست واقعی روی یک وردپرس موقت در GitHub Actions است و هیچ Dependencyای به Runtime قالب اضافه نمی‌کند.

## مرز امنیتی

- `setup-fixture.php` فقط با WP-CLI، روی `localhost/127.0.0.1` و Environment نوع `local/development` اجرا می‌شود.
- محصولات، مشتری، سفارش، ارسال و پرداخت همه مصنوعی و مخصوص Runner موقت هستند.
- هیچ Credential یا دادهٔ Production لازم نیست و نباید وارد این پوشه شود.
- صفحات Cart/Checkout عمداً Shortcode کلاسیک دارند تا Overrideهای قالب، نه Woo Blocks، آزمون شوند.

## نسخه‌ها و اجرا

Workflow `.github/workflows/woocommerce-acceptance.yml` روی PHP 8.3، WordPress 7.1.3 و WooCommerce 11.2.1 اجرا می‌شود. مرورگر با Playwright 1.64.1 و Chromium نصب‌شده در Runner اجرا می‌شود.

پوشش فعلی:

- Lint واقعی PHP و Syntax تمام JS قالب؛
- پنج Settings Check با WP-CLI برای حذف Fallback فرضی Schema، سقف Sanitizer/Renderer پرسش‌های متداول و مقصد لینک‌های Readiness؛ Optionها با `pre_option` موقت تزریق می‌شوند و این Checkها هیچ نوشتن DBای انجام نمی‌دهند؛
- ساخت Simple/Variable product و مشتری آزمایشی؛
- Shop و PDP، افزودن Simple با AJAX و Variable با Submit بومی؛
- Cart و Checkout با نشانی ایرانی، ارسال Flat Rate و COD؛
- صفحهٔ تشکر با دو قلم، Variation Meta، Totals و دستورالعمل درگاه؛
- حالت Desktop/Mobile، Overflow و CSS چاپ فاکتور؛
- Login مشتری و Add/Remove واقعی Wishlist با REST و Confirm/Cancel؛
- ثبت Console/Page errors و اسکرین‌شات‌ها به‌عنوان Artifact.

این تست هنوز جای ماتریس کامل Gateway Sandbox، همهٔ وضعیت‌ها، axe و CWV را نمی‌گیرد. هیچ ارتباطی با سایت عملیاتی برقرار نمی‌شود.
