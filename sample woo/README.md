# 🎨 FrontBaz WooCommerce Framework
### دیزاین‌سیستم و قالب‌ساز ماژولار ووکامرس (نسخه چایلدتم مستقل)

بسته‌بندی رسمی توکن‌ها، تمپلیت‌های ووکامرس، استایل‌ها و توابع هسته فرانت‌باز — کاملاً مستقل، امن، سبک و بدون نیاز به هیچ ابزار یا وابستگی خارجی.

---

## 🚀 راهنمای سریع نصب در ۳ مرحله (کمتر از ۱ دقیقه):

### ۱. انتقال فایل‌ها به قالب فرزند (Child Theme)
شما می‌توانید به یکی از دو روش ساده زیر فایل‌ها را منتقل نمایید:

* ⚡ **روش اول (فوق‌سریع - پیشنهادی برای هاست و سی‌پنل):**  
  فایل فشرده **`frontbaz-upload.zip`** را مستقیماً داخل پوشه چایلدتم فعال سایت خود (مثلاً `wp-content/themes/hello-theme-child-master/`) آپلود کرده و گزینه **Extract** را بزنید تا هر دو پوشه فوراً در جای خود قرار گیرند.
* 📂 **روش دوم (انتقال دستی پوشه‌ها):**  
  دو پوشه زیر را مستقیماً در ریشه چایلدتم کپی نمایید:
  * 📁 **`frontbaz/`** (شامل استایل‌ها، اسکریپت‌ها و کدهای PHP به صورت کاملاً مستقل و ایزوله)
  * 📁 **`woocommerce/`** (تمپلیت‌های بازنویسی‌شده سبد خرید، تسویه‌حساب و پنل کاربری)

> 💡 **چرا پوشه `frontbaz/` کاملاً امن است؟**  
> تمام فایل‌های استایل، جاوااسکریپت و ماژول‌های PHP فرانت‌باز درون پوشه اختصاصی `frontbaz/` کپسوله شده‌اند تا با پوشه‌های پیش‌فرض قالب شما (`assets` یا `inc`) هیچ‌گونه تداخل یا برخوردی نداشته باشند و فایل‌های قبلی قالب شما محفوظ بمانند.

### ۲. اتصال به `functions.php`
فایل `functions.php` چایلدتم خود را باز کنید و کد کوتاه داخل فایل **`functions-snippet.php`** را در انتهای آن قرار دهید:

```php
// فعال‌سازی فریم‌ورک فرانت‌باز
if ( file_exists( get_stylesheet_directory() . '/frontbaz/loader.php' ) ) {
    require_once get_stylesheet_directory() . '/frontbaz/loader.php';
}
```

### ۳. پایان!
اکنون سبد خرید، برگه تسویه‌حساب دو ستونه، صفحه تشکر با فاکتور چاپی رسمی، و داشبورد حساب کاربری من با استایل مدرن و راست‌چین فرانت‌باز فعال شده‌اند.

---

## 📁 ساختار فایل‌های پکیج (Package Architecture):

```text
your-child-theme/
├── 📁 frontbaz/                       <-- تمام متعلقات فرانت‌باز (ایزوله و بدون تداخل)
│   ├── 📁 assets/
│   │   ├── 📁 css/ (fb-base.css, fb-cart.css, fb-checkout.css, fb-myaccount.css, fb-thankyou.css)
│   │   └── 📁 js/  (fb-cart.js, fb-checkout.js, fb-myaccount.js)
│   ├── 📁 inc/     (core.php, cart.php, checkout.php, thankyou.php, myaccount.php)
│   └── 📄 loader.php
├── 📁 woocommerce/                    <-- تمپلیت‌های استاندارد اورراید ووکامرس
│   ├── 📁 cart/
│   ├── 📁 checkout/
│   └── 📁 myaccount/
├── 🗜️ frontbaz-upload.zip             <-- فایل فشرده آماده آپلود و اکسترکت سریع در هاست
├── 📄 functions-snippet.php           <-- کد اتصال سریع به functions.php
└── 📄 README.md                       <-- راهنمای راه‌اندازی
```

---

## 🛠️ شخصی‌سازی و تغییر رنگ‌ها با FrontBaz Studio:
برای ویرایش پالت رنگی، انحنای گوشه‌ها (Border Radius) یا سایه‌ها:
1. وارد پوشه `FrontBaz WooCommerce Builder` شوید.
2. روی فایل **`theme-builder.html`** دو بار کلیک کنید تا در مرورگرتان باز شود.
3. رنگ‌ها و مقادیر دلخواه را تنظیم کرده و پکیج جدید ZIP را با ۱ کلیک دانلود کنید.

---

<div dir="ltr">

## 🇬🇧 English Quick Setup:
1. Copy **`frontbaz/`** and **`woocommerce/`** folders into your WordPress child theme directory.
2. Paste the code from **`functions-snippet.php`** into your child theme's `functions.php`.
3. All FrontBaz assets and PHP handlers are strictly isolated within the `frontbaz/` folder to eliminate any collision with your theme's native `assets/` or `inc/` folders.
</div>
