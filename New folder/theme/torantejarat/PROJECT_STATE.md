# وضعیت پروژه — ادامه کار در چت جدید

> این فایل را به دستیار بده و بگو: «این را بخوان و ادامه بده»

## پروژه
- قالب وردپرس: `torantejarat` (فارسی، RTL، ووکامرس)
- مسیر ریشه قالب: `e:\work item\didbarttar\theme\torantejarat`
- مرجع طراحی: پوشه `قالب html` (۲۱ صفحه HTML + کیت اسناد در `قالب html/wp-theme-agent-kit/`)
- تصمیم مصوب کاربر: **بازسازی کامل قالب وردپرس از صفر مطابق کیت HTML** (CSS/JS قالب از کیت جدیدتر است و نگه داشته شد)

## وضعیت فعلی (آخرین گزارش)
- ساختار قالب از پایه بازسازی شده و سالم است:
  - namespace صحیح: `ToranTejarat`، توابع `torantejarat_*`
  - `header.php` مطابق کیت (topbar, brand, search, actions, nav)
  - دارایی‌ها: CSS کامل (`assets/css/...` ۶ فایل)، تصاویر webp، ویدیو `tunnel-inspection.mp4`
- صفحات کیت: index, shop, product (۴ نمونه), cart, checkout, compare, blog, quiz, rent, repairs, about, academy, contact, terms, guide (۳ نمونه)
- قالب فعلی: front-page, archive, single, page, 404, search + page-templates (about, academy, request) + template-parts (home-*)
- ⚠️ **نکته مهم کشف‌شده**: تایپوی سیستماتیک در PHP‌ها — کلاس‌های CSS با املای اشتباه نوشته شده‌اند و با namespace CSS قالب (`torantejarat-` در `assets/css/theme.css`) مطابقت ندارند → بخش‌ها بدون استایل رندر می‌شوند. باید یک تصحیح سیستماتیک (search/replace) روی فایل‌های PHP انجام شود. **قبل از هر کاری دامنه دقیق را با grep نسبت به CSS مرجع بشمار و درست کن.**

## اولویت کار (به ترتیب مصوب کاربر)
1. **صفحه اصلی** — باید دقیقاً مطابق `قالب html/index.html` شود (همه بخش‌ها: topbar, header, hero video, services, products, editorial/blog, brand strip, footer)
2. بعد بقیه صفحات یکی‌یکی: shop → product → cart/checkout → compare → blog → بقیه

## محیط QA (ایزوله)
- PHP: `C:\laragon\bin\php\php-8.3.33\php.exe` (در PATH نیست؛ با مسیر کامل صدا بزن)
- MySQL: پورت **3306**، datadir: `C:\laragon\bin\mysql\mysql-8.4\...` (سرویس دسکتاپ گاهی خاموش می‌شود؛ دوباره بالا بیاور)
- دیتابیس QA: `wpqa` با پیشوند `wpqa_` (دیتابیس دیگر `shalangbin` با پیشوند `wp_` به QA ربط ندارد)
- آدرس سایت QA: `http://127.0.0.1:8098`
- اجرای سرور: `php -S 127.0.0.1:8098` با **router وردپرس** (بدون router صفحه اصلی 404 می‌شود) — router را در پوشه QA بساز
- wp-cli: فایل phar دانلود شده در TEMP؛ اگر نبود دوباره دانلود کن
- داده‌های seed قبلی: ۴ محصول، منوی اصلی متصل، صفحه خانه (ولی پوشه QA یک بار خالی شد؛ در آخرین وضعیت سرور 200 OK با خروجی 42KB بود — اگر خالی بود دوباره seed کن)

## سرنخ‌های مهم از اسناد کیت (مصوب)
- `PHASE-6-MIGRATION-MAP.md` = نقشه مصوب بخش‌ها (کدام بخش قالب = کدام فایل)
- `COPY.md` = متن‌های فارسی نهایی (هر متن از اینجا)
- `DESIGN-TOKENS.md` = رنگ/فونت/فاصله‌ها
- `CONTENT-MODEL.md` و `WOOCOMMERCE.md` = مدل داده محصولات
- `ACCEPTANCE.md` = معیار پذیرش هر صفحه
- CSS کیت = مرجع namespace واقعی کلاس‌ها

## یادآوری‌های ابزاری (این محیط)
- برای فایل جدید از insert_edit_into_file استفاده کن
- ترمینال: فقط دستورات اجرایی (php, mysql, wp); خواندن/نوشتن فایل پروژه با ترمینال ممنوع
- ابزار memory در این محیط خطای param می‌دهد — استفاده نشود
- فایل‌های کیت HTML تک‌خطی/minified هستند؛ با grep برای استخراج ساختار section ها استفاده کن

## گام بعدی فوری
1. شمارش دقیق تایپوهای کلاس CSS در PHP و تصحیح سیستماتیک (تطبیق با theme.css)
2. بالا آوردن MySQL + سرور QA با router وردپرس
3. مقایسه رندر واقعی صفحه اصلی با `قالب html/index.html` بخش‌به‌بخش و اصلاح
4. تکمیل داده نمونه (منو کامل مطابق NAVIGATION.md، ۶ محصول مطابق CONTENT-MODEL، نوشته‌های بلاگ)
