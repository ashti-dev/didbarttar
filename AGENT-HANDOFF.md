# 🤖 AGENT HANDOFF — قالب وردپرس «دید برتر توران تجارت»

> **سخن به Agent:** این سند نقشه کامل پروژه است. قبل از هر تغییری، ابتدا فایل
> `didbarttar-design-system.md` را کامل بخوان (الزام سند: «این سند باید در ابتدای هر
> session کدنویسی مرور شود»). هر تغییر جدید را اول در سند دیزاین ثبت کن، بعد در کد.

---

## ۱. شناسنامه پروژه

| مورد | مقدار |
|---|---|
| نام قالب | Did Barttar (`didbarttar`) |
| نسخه | 1.0.0 |
| برند | دید برتر توران تجارت — تجهیزات امنیتی، نظارتی و ردیابی |
| مخاطب | عام (دوربین خانگی) + سازمانی (معدن، ناوگان، شرکت‌ها) |
| زبان/جهت | فارسی، RTL کامل |
| استک | WordPress 6.4+ / PHP 8.1+ / WooCommerce / بدون ACF / بدون کتابخانه JS خارجی |
| سند دیزاین | `didbarttar-design-system.md` نسخه 1.0.0 (منبع حقیقت tunggal) |
| وضعیت | فاز ۱ و ۲ تمام‌شده؛ **یک باگ باز رندر وجود دارد (بخش ۹)**؛ فاز ۳ شروع‌نشده |

---

## ۲. قوانین سخت (Hard Rules) — نقض ممنوع

1. **ACF ممنوع.** تمام فیلدها با Meta Box بومی وردپرس + Customizer (`get_theme_mod`) پیاده‌سازی شده‌اند. افزونه جدید معرفی نکن.
2. **کتابخانه JS/CSS خارجی ممنوع** (جز Google Fonts). انیمیشن‌ها فقط با Intersection Observer و CSS.
3. **محصولات فقط ووکامرس.** پست‌تایپ `did_product` حذف شده است. هرگز کوئری محصول از غیر `post_type => 'product'` نزن.
4. **RTL و فارسی در همه خروجی‌ها.** اعداد فارسی با `number_format_i18n()`؛ قیمت‌ها و اعداد لاتین با فونت Inter.
5. **توکن‌های دیزاین:** هیچ رنگ/شعاع/سایه hardcoded جدیدی خارج از `:root` مجاز نیست.
6. **Reveal باید JS-safe بماند:** مخفی‌سازی اولیه فقط تحت کلاس `.did-js` (که توسط اسکریپت inline در `header.php` اضافه می‌شود). اگر JS نیاید، محتوا باید دیده شود.
7. **امنیت:** هر خروجی escape (`esc_html/esc_url/esc_attr`)، هر ورودی sanitize، هر فرم/AJAX با nonce (`didbarttar_nonce`).
8. **سایه‌ها ظریف:** blur بالا ممنوع (مطابق بخش ۶ سند دیزاین).
9. **انیمیشن تزئینی ممنوع:** فقط پاسخ به عمل کاربر یا جلب توجه به یک نقطه مهم.

---

## ۳. خلاصه Design System (چکیده اجرایی)

### رنگ
- Navy Core `#1A2E4A` | Navy Mid `#2A4A72` | Navy Light `#3D6A9E`
- Orange Core `#E8680A` (CTA/قیمت/لینک فعال) | Orange Mid `#F07C28` (hover)
- BG Section `#F4F7FA` | BG Dark `#0F1E30` | Border `#DDE6EF`
- Text Dark `#0F1E30` | Text Mid `#4A6077` | Text Light `#8BA3B8` | Text on Navy `#A8C5DC`
- Success `#4CAF88` | Error `#E84040` | Warning `#F5A060`

### تایپوگرافی
- فارسی: Vazirmatn (400/500/700/900) — لاتین/اعداد: Inter (400/600/700)
- H1: 46px/900 (موبایل 32px) | H2: 34px/900 (موبایل 26px) | H3: 18px/700 | Body: 14px/1.7 | Body Large: 16px/1.8
- Section Label: 12px/700 نارنجی، margin-bottom 10px، قبل از هر H2

### شعاع / سایه / حرکت
- Radius: sm 4 | md 6 | lg 8 | **xl 12 (کارت‌ها)** | 2xl 16 | full 9999
- Shadow card: `0 1px 3px rgba(26,46,74,.06)` | hover: `0 8px 24px rgba(26,46,74,.10)` | product hover: `0 8px 28px rgba(26,46,74,.10)`
- Transition استاندارد: `0.15s ease` | Hover کارت: `translateY(-2px)` (کارت دسته‌بندی: `-3px`)
- Keyframes مجاز: `scan` (اسکن‌لاین Hero)، `blink` (LED)

### لی‌اوت
- Container: max-width 1200px + padding 0 24px | Section padding: 80px 24px
- Grid: cat 4 ستون (gap 16) | prod 3 ستون (gap 20) | srv 2 ستون | hero 1fr 1fr (gap 60) | footer 2fr 1fr 1fr 1fr
- Breakpoints: ≤1024 (cat/prod → 2 ستون) | ≤768 (همه → 1 ستون، hero-visual hidden، منو همبرگری، cat-grid استثناً 2 ستون) | ≤480 (cat → 1 ستون)

### پالت WooCommerce (بخش ۱۲ سند)
- قیمت: Inter Navy | del: `#8BA3B8` | ins: `#E84040` | دکمه سبد: Navy → hover Navy Mid | `single_add_to_cart_button`: Orange | onsale: `#E84040` radius 20px | `woocommerce-message` border-top: Orange

---

## ۴. ساختار فایل‌ها (وضعیت واقعی فعلی)

```
wp-content/themes/didbarttar/
├── style.css                  # هدر تم + کل استایل سایت + reveal (با پیشوند .did-js)
├── functions.php              # ثابت‌ها، لود inc/، enqueue، theme support، sidebars، body classes
├── header.php                 # topbar? (نداریم) + هدر چسبان + جستجوی زنده + <script>did-js</script>
├── footer.php                 # فوتر 4 ستونه + wp_footer
├── front-page.php             # فقط get_template_part به ترتیب: hero, categories, products, services, stats, testimonials, cta
├── index.php / page.php / single.php / archive.php / search.php / 404.php
├── page-contact.php           # Template Name: صفحه تماس (فرم POST با nonce did_contact_form)
├── page-about.php             # Template Name: صفحه درباره ما
├── inc/
│   ├── theme-setup.php        # (placeholder خالی)
│   ├── custom-post-types.php  # did_service, did_testimonial, did_project  ← بدون did_product
│   ├── meta-boxes.php         # فقط متاباکس testimonial (role + rating)
│   ├── theme-options.php      # Customizer: did_phone/email/address/social_* + helper did_get_option()
│   ├── security.php           # headers، حذف generator، limit login، حذف xml-rpc
│   ├── performance.php        # حذف emoji/rsd، preload فونت، lazy images، defer، حذف query string
│   ├── seo.php                # Schema Organization + Open Graph
│   ├── ajax-handlers.php      # action: did_contact (فرم AJAX قدیمی)
│   ├── breadcrumb.php         # didbarttar_breadcrumb() + Schema BreadcrumbList
│   ├── pagination.php         # didbarttar_pagination($query)
│   ├── live-search.php        # action: did_live_search (product + service + post/page)
│   └── woocommerce-hooks.php  # ★ بزرگ‌ترین فایل: همه هوک‌های WC + توابع کمکی
├── assets/
│   ├── css/woocommerce.css    # استایل WC + قیمت داخل .prod-price + a.cat-card
│   └── js/
│       ├── scroll-reveal.js   # کلاس ScrollReveal (IntersectionObserver)
│       ├── main.js            # هدر scroll، منو موبایل، smooth scroll، شمارنده‌ها، تب‌های محصول، فرم تماس
│       └── live-search.js     # debounce 300ms + AbortController + کیبورد + highlight
├── template-parts/
│   ├── hero.php               # موکاپ دستگاه (scan/blink) + آمار + badge-trust
│   ├── categories.php         # get_terms(product_cat) + آیکون چرخشی + fallback
│   ├── products.php           # WP_Query(product) featured→latest + badge + قیمت WC
│   ├── services.php / stats.php / testimonials.php / cta.php
├── woocommerce/               # Override ها
│   ├── woocommerce.php        # Wrapper (breadcrumb + container + woocommerce_content)
│   ├── single-product.php
│   ├── content-product.php
│   ├── archive-product.php
│   ├── cart/cart.php
│   └── checkout/form-checkout.php
└── (فایل‌های حذف‌شده: single-did_product.php، archive-did_product.php، inc/product-query.php)
```

> ⚠️ اگر فایلی از لیست بالا در نصب فعلی موجود نبود، یعنی کاربر آن را نساخته؛ قبل از ارجاع، وجودش را چک کن و در غیر این صورت بساز.

---

## ۵. مدل داده

### Custom Post Types
| CPT | کاربرد | وضعیت |
|---|---|---|
| `did_service` | خدمات | فعال |
| `did_testimonial` | نظرات (public=false, show_ui=true) | فعال |
| `did_project` | پروژه‌ها | فعال |
| ~~`did_product`~~ | ❌ حذف‌شده — محصولات فقط ووکامرس |

### Meta Keys
| کلید | نوع | محل |
|---|---|---|
| `_did_testimonial_role` / `_did_testimonial_rating` | testimonial | meta-box |
| `_did_custom_sku` / `_did_warranty` / `_did_manufacturer` / `_did_delivery_time` | محصول ووکامرس | تب «دید برتر» در ویرایش محصول |

### Customizer (theme_mod با پیشوند `did_`)
`did_phone`، `did_email`، `did_address`، `did_social_instagram`، `did_social_telegram`، `did_social_whatsapp`
→ دسترسی: `did_get_option('phone')`

### Sidebars
`sidebar-1` (اصلی) | `woocommerce-sidebar` (فروشگاه)

---

## ۶. رفتارهای فرانت‌اند (قراردادهای JS/CSS)

### Scroll Reveal
- Markup: `data-reveal="up|down|left|right|scale"` + اختیاری `data-delay="100"`
- CSS: مخفی‌سازی **فقط** تحت `.did-js`؛ کلاس `.revealed` یعنی نمایش.
- JS: `scroll-reveal.js` → observer با threshold .12 و rootMargin `0px 0px -60px 0px`؛ یک‌بار مصرف.
- **قانون:** هر کامپوننت جدید که reveal می‌خواهد باید بدون JS هم خوانا بماند.

### جستجوی زنده
- UI: دکمه `#search-toggle` → پنل `#search-panel` → input `#live-search-input` → نتایج `#search-results`
- Endpoint: `admin-ajax.php?action=did_live_search&nonce=…&term=…`
- آبجکت لوکال: `didSearch = {ajaxUrl, nonce, minChars:2}`
- خروجی JSON: `{results:[{type,label,title,url,image,meta,cat}], count, term, allUrl}`

### آبجکت لوکال دیگر
`didbarttar = {ajaxUrl, nonce, siteUrl}` (برای main.js)

### سایر
- شمارنده آمار: `.stat-number[data-target]` با IntersectionObserver و اعداد فارسی
- تب‌های محصول تکی: `.ps-tab-btn[data-tab]` ↔ `#tab-desc / #tab-specs`
- منوی موبایل: کلاس `.nav-open` روی `#site-header`
- هدر: کلاس `.scrolled` بعد از 40px اسکرول

---

## ۷. یکپارچگی WooCommerce (خلاصه woocommerce-hooks.php)

- `add_theme_support('woocommerce' + galleries)`؛ حذف استایل‌های پیش‌فرض WC
- 12 محصول/صفحه، 3 ستون؛ related/upsells = 3
- فارسی‌سازی: دکمه‌ها، تب‌ها («توضیحات محصول/مشخصات فنی/نظرات (n)»)، heading ها، موجودی/ناموجود
- Sale flash با **درصد تخفیف محاسبه‌شده** (`٪N تخفیف`)
- نماد پول: `تومان` داخل `<small>` برای IRR/IRT
- تب «دید برتر» در ادمین محصول + ذخیره 4 متا + نمایش‌شان زیر قیمت (`didbarttar_custom_product_info` در priority 22)
- Trust badges (4 بج) در priority 35 خلاصه محصول
- AJAX add-to-cart برای محصول simple + fragment برای `span.cart-count`
- Body classes: is-shop / is-single-product / is-cart / is-checkout / is-account
- توابع کمکی عمومی: `did_shop_url()`، `did_cart_url()`، `did_checkout_url()`، `did_account_url()`، `did_cart_count()`، `did_cart_total()`
- Schema Product (با aggregateRating) در wp_head برای single product
- پیام تشکر اختصاصی در `woocommerce_thankyou`

---

## ۸. امنیت / عملکرد / SEO — آنچه پیاده شده

- Security headers (nosniff, SAMEORIGIN, Referrer-Policy)؛ حذف generator/RSD/WLW؛ xml-rpc off؛ محدودیت 5 تلاش لاگین/15 دقیقه
- Nonce + sanitize در همه فرم‌ها و AJAX؛ escape در همه خروجی‌ها
- Preload/preconnect فونت‌ها؛ lazy+decoding برای تصاویر؛ defer برای jquery؛ حذف query string؛ حذف emoji
- Schema: Organization (front)، BreadcrumbList، Product؛ Open Graph برای singular
- Breadcrumb و Pagination اختصاصی و استایل‌دار

---

## ۹. 🐞 باگ باز (اولویت اول Agent)

**شرح:** پس از نصب قالب، صفحه اصلی فقط یک باکس تیره (Hero بدون محتوا) در یک ستون باریک نشان می‌دهد؛ بقیه بخش‌ها خالی‌اند؛ footer در اسکرین‌شات دیده نمی‌شود. نوار ادمین نشان می‌دهد WooCommerce در حالت **«Store coming soon»** است.

**فرضیه‌ها به ترتیب احتمال:**
1. خروجی PHP وسط رندر قطع شده (fatal) → `wp_footer()` اجرا نشده → JS ها هرگز چاپ نشده‌اند → همه `[data-reveal]` با opacity:0 مانده‌اند.
2. خطای JS (فایل جاوا اسکریپت 404 یا syntax) → همان اثر نامرئی‌شدن.
3. مشکل لی‌اوت (Hero داخل یک container) ناشی از قالب صفحه اشتباه یا markup ناقص پس از fatal.

**اقدامات اصلاحی که قبلاً داده شده (چک کن اعمال شده‌اند یا نه):**
- [ ] `<script>document.documentElement.classList.add('did-js');</script>` در `header.php`
- [ ] CSS reveal فقط تحت `.did-js [data-reveal]{…}`
- [ ] `front-page.php` با حلقه امن `file_exists` روی template-parts
- [ ] نسخه‌های ضدخطای `categories.php` و `products.php` (guard روی `taxonomy_exists` / `wc_get_product`)
- [ ] فعال‌سازی `WP_DEBUG + WP_DEBUG_LOG + WP_DEBUG_DISPLAY` و خواندن خطا
- [ ] غیرفعال‌سازی حالت coming soon: WooCommerce → Settings → Site visibility → Live

**روش دیباگ استاندارد پروژه:**
1. کنسول مرورگر (خطای JS/404)
2. `debug.log` در `wp-content/`
3. View Source: بررسی اینکه آیا `</footer>` و اسکریپت‌های footer چاپ شده‌اند یا نه (تشخیص fatal)

---

## ۱۰. نقشه راه باقی‌مانده (فاز ۳)

1. **مستندات:** README.md، CHANGELOG.md، LICENSE (GPL-2.0)، screenshot.png (1200×900)، پوشه docs/ (نصب، سفارشی‌سازی، عیب‌یابی)، Child Theme آماده
2. **تست:** Lighthouse >90 موبایل / >95 دسکتاپ؛ Core Web Vitals سبز؛ Cross-browser (Chrome/Firefox/Safari/Edge + iOS/Android)؛ ریسپانسیو 320/375/768/1024
3. **SEO تکمیلی:** Schema برای About/Contact/Article/CollectionPage؛ sitemap سفارشی؛ robots بهینه
4. **دسترس‌پذیری WCAG 2.1 AA:** کنتراست، focus visible، skip-link (وجود دارد)، alt/aria کامل، تست کیبورد
5. **اختیاری:** PWA (manifest + service worker + offline)

---

## ۱۱. قراردادهای کدنویسی

- پیشوند توابع/هوک‌ها: `didbarttar_` ؛ پیشوند متا/تنظیمات: `did_` ؛ پیشوند متغیرهای لوکال模板: `did_` (جلوگیری از برخورد)
- کلاس‌های CSS موجود (تغییر نام ممنوع): `prod-card, prod-image, prod-body, prod-footer, prod-price, prod-badge, badge-hot/new/sale, cat-card, cat-grid, srv-card, srv-grid, test-card, stat-card, section, section-alt, section-dark, section-label, section-title, section-subtitle, btn/btn-primary/btn-outline/btn-ghost/btn-hero/btn-service, breadcrumb/bc-*, pg-*, ps-* (صفحه محصول سفارشی قدیمی), wc-* (ووکامرس), search-* (جستجو), form-* (تماس)`
- فایل‌های PHP با `if (!defined('ABSPATH')) exit;` شروع شوند
- همه کوئری‌ها با `no_found_rows` وقتی pagination لازم نیست
- قیمت فقط از API ووکامرس (`get_price_html`) — هرگز عدد خام چاپ نکن
- اعداد فارسی: `number_format_i18n()`؛ اعداد فنی/قیمت لاتین: کلاس با `font-family: var(--font-en)`

---

## ۱۲. چک‌لیست تحویل هر تغییر (Definition of Done)

- [ ] مطابق توکن‌های دیزاین (رنگ/شعاع/سایه/transition)
- [ ] RTL سالم در 320 تا 1920 پیکسل
- [ ] بدون JS هم محتوا خوانا است
- [ ] escape/sanitize/nonce رعایت شده
- [ ] کنسول بدون خطا؛ `debug.log` خالی
- [ ] کش پاک‌شده تست شده (Incognito)
- [ ] در صورت تغییر بصری: سند دیزاین اول آپدیت شده

---

## ۱۳. Change Logセッション‌ها

| نسخه | تاریخ | خلاصه |
|---|---|---|
| 0.1 | — | ساختار پایه، CPT ها، meta boxes، Customizer، security/performance/seo |
| 0.2 | — | فاز ۱: breadcrumb، pagination، single محصول سفارشی، archive با فیلتر، صفحه تماس |
| 0.3 | — | فاز ۲: WooCommerce کامل (overrides + hooks + css)، درباره ما، cart/checkout، جستجوی زنده |
| 0.4 | — | حذف `did_product`؛ اتصال صفحه اصلی به ووکامرس؛ باگ رندر باز (بخش ۹) |
| 1.0 | — | **هدف:** رفع باگ رندر + فاز ۳ |

---

## ۱۴. پیوست: اسنیپت‌های مرجع سریع

```php
// لینک فروشگاه / سبد / چک‌اوت
did_shop_url(); did_cart_url(); did_checkout_url();

// تنظیمات قالب
did_get_option('phone', '۰۲-۸۸۰۰۰۰۰۰');

// Breadcrumb و Pagination
didbarttar_breadcrumb();
didbarttar_pagination($custom_query);
```

```html
<!-- الگوی reveal -->
<div data-reveal="up" data-delay="100"> … </div>
```

```css
/* الگوی کارت مطابق سند */
.card { border:1px solid var(--border); border-radius:var(--radius-xl);
        box-shadow:var(--shadow-card); transition:var(--transition); }
.card:hover { transform:translateY(-2px); box-shadow:var(--shadow-hover); }
```

---

> **پایان سند.** Agent گرامی: اول باگ بخش ۹ را ببند، بعد فاز ۳ را به ترتیب بخش ۱۰ پیش ببر.
> موفق باشی. 🎯