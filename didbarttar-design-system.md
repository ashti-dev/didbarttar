# سیستم دیزاین — دید برتر توران تجارت
**نسخه:** 1.0.0  
**تاریخ:** ۱۴۰۴  
**تهیه‌کننده:** KhanWP

---

## ۱. معرفی

### موقعیت برند
دید برتر توران تجارت یک شرکت تخصصی در حوزه تجهیزات امنیتی، نظارتی و ردیابی است. برند باید همزمان به مخاطب عام (خریدار دوربین خانگی) و مخاطب سازمانی (شرکت، معدن، ناوگان) اعتماد القا کند.

### ارزش‌های بصری
- **اعتماد** — هویت حقوقی و مجوز رسمی در معرض دید
- **تخصص فنی** — طراحی دقیق، نه تزئینی
- **قدرت** — پالت تیره و جدی، نه رنگارنگ مصرفی
- **وضوح** — اطلاعات سلسله‌مراتبی، بدون شلوغی

---

## ۲. پالت رنگ

### رنگ‌های اصلی

| نام | Hex | RGB | کاربرد |
|---|---|---|---|
| Navy Core | `#1A2E4A` | 26, 46, 74 | هدر، فوتر، دکمه اصلی، پس‌زمینه Hero |
| Navy Mid | `#2A4A72` | 42, 74, 114 | Hover دکمه‌ها، حاشیه‌های تاکیدی |
| Navy Light | `#3D6A9E` | 61, 106, 158 | لینک‌های ثانوی، آیکون‌ها |
| Orange Core | `#E8680A` | 232, 104, 10 | CTA، قیمت، نشان‌گرها |
| Orange Mid | `#F07C28` | 240, 124, 40 | Hover دکمه CTA |

### رنگ‌های پشتیبان

| نام | Hex | کاربرد |
|---|---|---|
| BG Section | `#F4F7FA` | پس‌زمینه بخش‌های متناوب |
| BG White | `#FFFFFF` | پس‌زمینه اصلی، کارت محصول |
| BG Dark | `#0F1E30` | فوتر، موکاپ دستگاه |
| Text Dark | `#0F1E30` | متن اصلی |
| Text Mid | `#4A6077` | متن توضیحی، زیرعنوان |
| Text Light | `#8BA3B8` | کپشن، placeholder |
| Border | `#DDE6EF` | خطوط جداکننده، حاشیه کارت |

### پالت آبی — ۶ درجه

```
#1A2E4A → #2A4A72 → #3D6A9E → #6B9EC4 → #A8C5DC → #D4E5F0
```

### پالت نارنجی — ۵ درجه

```
#E8680A → #F07C28 → #F59A55 → #FAC090 → #FDDCC0
```

### قوانین استفاده از رنگ

- دکمه اصلی (خرید، تماس): `#E8680A` با متن `#FFFFFF`
- دکمه ثانوی (مشاوره، کاتالوگ): `border: #1A2E4A` با متن `#1A2E4A`
- دکمه Ghost (روی پس‌زمینه تیره): `border: rgba(168,197,220,0.35)` با متن `#A8C5DC`
- لینک فعال: `#E8680A`
- لینک غیرفعال: `#4A6077`
- وضعیت موفق: `#4CAF88`
- وضعیت خطا: `#E84040`
- وضعیت هشدار: `#F5A060`

---

## ۳. تایپوگرافی

### فونت‌ها

```
فارسی:  Vazirmatn (Google Fonts)
لاتین:  Inter (Google Fonts)
```

هر دو رایگان و بهینه برای وب. Vazirmatn برای همه متن‌های فارسی استفاده می‌شود. Inter صرفاً برای اعداد (قیمت، آمار) و برچسب‌های لاتین به کار می‌رود.

### تعریف Import

```css
@import url('https://fonts.googleapis.com/css2?family=Vazirmatn:wght@400;500;700;900&family=Inter:wght@400;600;700&display=swap');
```

### سلسله‌مراتب تایپوگرافی

| نوع | فونت | وزن | سایز دسکتاپ | سایز موبایل | Line Height |
|---|---|---|---|---|---|
| H1 — تیتر Hero | Vazirmatn | 900 | 46px | 32px | 1.2 |
| H2 — تیتر بخش | Vazirmatn | 900 | 34px | 26px | 1.25 |
| H3 — تیتر کارت | Vazirmatn | 700 | 18px | 16px | 1.35 |
| H4 — تیتر فرعی | Vazirmatn | 700 | 16px | 15px | 1.4 |
| Body Large | Vazirmatn | 400 | 16px | 15px | 1.8 |
| Body Default | Vazirmatn | 400 | 14px | 14px | 1.7 |
| Caption | Vazirmatn | 400 | 12px | 12px | 1.6 |
| Label | Vazirmatn | 600 | 12px | 12px | 1.4 |
| Price | Inter | 700 | 20–24px | 18px | 1.0 |
| Section Label | Vazirmatn | 700 | 12px | 12px | 1.4 |

### قوانین تایپوگرافی

- Section Label (برچسب بالای تیتر): رنگ `#E8680A`، حروف عادی (نه کپس)، margin-bottom: 10px
- تیتر H2 بخش: رنگ `#1A2E4A`، حداکثر عرض ۵۶۰px در حالت متن کنار تصویر
- متن توضیحی: رنگ `#4A6077`، Line height: 1.8 برای خوانایی
- قیمت محصول: فونت Inter برای ارقام، فارسی برای واحد پولی

---

## ۴. فاصله‌گذاری (Spacing)

### مقیاس پایه: 4px

| نام | مقدار | کاربرد |
|---|---|---|
| xs | 4px | فاصله آیکون با متن |
| sm | 8px | فاصله درونی کوچک، gap بین chip‌ها |
| md | 16px | padding کارت کوچک، gap بین آیتم‌ها |
| lg | 24px | padding افقی صفحه (موبایل) |
| xl | 32px | padding کارت بزرگ |
| 2xl | 48px | فاصله بین المان‌های بخش |
| 3xl | 60px | padding داخلی هر section |
| 4xl | 80px | padding عمودی هر section |

### section padding استاندارد

```css
section {
  padding: 80px 24px;
}

.section-inner {
  max-width: 1200px;
  margin: 0 auto;
}
```

---

## ۵. شعاع گوشه (Border Radius)

| نام | مقدار | کاربرد |
|---|---|---|
| sm | 4px | Badge، chip، تگ |
| md | 6px | دکمه، input |
| lg | 8px | دکمه بزرگ، آیکون‌باکس کوچک |
| xl | 12px | کارت محصول، کارت سرویس |
| 2xl | 16px | کارت Hero، پنل دستگاه |
| full | 9999px | pill badge، آواتار |

---

## ۶. سایه (Shadow)

| نام | مقدار | کاربرد |
|---|---|---|
| card-default | `0 1px 3px rgba(26,46,74,0.06)` | کارت در حالت عادی |
| card-hover | `0 8px 24px rgba(26,46,74,0.10)` | کارت در hover |
| card-product | `0 8px 28px rgba(26,46,74,0.10)` | کارت محصول در hover |
| nav | `0 1px 0 #DDE6EF` | هدر (border-bottom) |

> **قانون:** از box-shadow با blur بالا خودداری کنید. سایه‌ها باید ظریف و اعتمادبرانگیز باشند نه دکوراتیو.

---

## ۷. کامپوننت‌های UI

### ۷.۱ دکمه‌ها

#### Primary (CTA اصلی)
```css
.btn-primary {
  background: #E8680A;
  color: #FFFFFF;
  font-family: 'Vazirmatn', sans-serif;
  font-size: 13px;
  font-weight: 700;
  padding: 9px 20px;
  border-radius: 6px;
  border: none;
  transition: background 0.15s;
}
.btn-primary:hover { background: #F07C28; }
```

#### Secondary (خط‌دار)
```css
.btn-outline {
  background: transparent;
  color: #1A2E4A;
  border: 1.5px solid #1A2E4A;
  font-family: 'Vazirmatn', sans-serif;
  font-size: 13px;
  font-weight: 600;
  padding: 8px 18px;
  border-radius: 6px;
  transition: background 0.15s, color 0.15s;
}
.btn-outline:hover { background: #1A2E4A; color: #fff; }
```

#### Ghost (روی پس‌زمینه تیره)
```css
.btn-ghost {
  background: transparent;
  color: #A8C5DC;
  border: 1.5px solid rgba(168,197,220,0.35);
  font-family: 'Vazirmatn', sans-serif;
  font-size: 15px;
  font-weight: 600;
  padding: 14px 28px;
  border-radius: 8px;
  transition: border-color 0.15s, color 0.15s;
}
.btn-ghost:hover { color: #fff; border-color: #fff; }
```

#### Hero (بزرگ)
```css
.btn-hero {
  font-size: 15px;
  font-weight: 700;
  padding: 14px 28px;
  border-radius: 8px;
}
```

#### Service (خط‌دار نارنجی روی تیره)
```css
.btn-service {
  color: #E8680A;
  border: 1.5px solid rgba(232,104,10,0.4);
  background: transparent;
  padding: 9px 18px;
  border-radius: 6px;
}
.btn-service:hover {
  background: rgba(232,104,10,0.12);
  border-color: #E8680A;
}
```

---

### ۷.۲ کارت محصول

```
┌─────────────────────────────┐
│  [تصویر محصول — 190px]      │
│  [Badge پرفروش / جدید]      │
├─────────────────────────────┤
│  دسته‌بندی (13px، آبی)     │
│  نام محصول (15px، Bold)     │
│  توضیحات (13px، خاکستری)   │
├─────────────────────────────┤
│  قیمت (Inter Bold)  [دکمه] │
└─────────────────────────────┘
```

```css
.product-card {
  background: #FFFFFF;
  border: 1px solid #DDE6EF;
  border-radius: 12px;
  overflow: hidden;
  transition: box-shadow 0.15s, transform 0.15s;
}
.product-card:hover {
  box-shadow: 0 8px 28px rgba(26,46,74,0.10);
  transform: translateY(-2px);
}
```

---

### ۷.۳ کارت دسته‌بندی

```
┌─────────────────────────────┐
│  [آیکون ۵۲×۵۲ — Navy]      │
│                             │
│  عنوان (15px، Bold، Navy)  │
│  توضیح (13px، خاکستری)     │
│                             │
│  مشاهده محصولات ←           │
└─────────────────────────────┘
```

Hover: `border-color: #3D6A9E` + `translateY(-3px)`

---

### ۷.۴ کارت سرویس (روی پس‌زمینه Navy)

```
┌─────────────────────────────┐
│  [آیکون ۵۲×۵۲ — Orange]   │
│                             │
│  عنوان (18px، Bold، سفید)  │
│  توضیح (14px، آبی کمرنگ)  │
│                             │
│  • ویژگی یک               │
│  • ویژگی دو               │
│                             │
│  [دکمه Service]             │
└─────────────────────────────┘
```

```css
.service-card {
  background: rgba(255,255,255,0.06);
  border: 1px solid rgba(168,197,220,0.15);
  border-radius: 12px;
  padding: 32px;
}
.service-card:hover {
  background: rgba(255,255,255,0.10);
  border-color: rgba(168,197,220,0.30);
}
```

---

### ۷.۵ Badge / برچسب

```css
/* پرفروش */
.badge-hot {
  background: #E8680A;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 20px;
}

/* جدید */
.badge-new {
  background: #1A2E4A;
  color: #A8C5DC;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 20px;
}

/* تخفیف */
.badge-sale {
  background: #E84040;
  color: #FFFFFF;
  font-size: 11px;
  font-weight: 700;
  padding: 3px 9px;
  border-radius: 20px;
}

/* مجوز / اعتماد */
.badge-trust {
  background: rgba(232,104,10,0.15);
  border: 1px solid rgba(232,104,10,0.35);
  color: #F5A060;
  font-size: 12px;
  font-weight: 600;
  padding: 5px 12px;
  border-radius: 20px;
}
```

---

### ۷.۶ آیکون‌باکس

```css
/* روی پس‌زمینه روشن */
.icon-box-navy {
  width: 52px;
  height: 52px;
  background: #1A2E4A;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
}
/* آیکون داخل: stroke: #A8C5DC */

/* روی پس‌زمینه تیره */
.icon-box-orange {
  background: rgba(232,104,10,0.15);
  border: 1px solid rgba(232,104,10,0.25);
  border-radius: 12px;
}
/* آیکون داخل: stroke: #E8680A */
```

---

### ۷.۷ فرم استعلام قیمت

```
┌─────────────────────────────────────────┐
│  نام و نام خانوادگی                     │
│  ┌───────────────────────────────────┐  │
│  │                                   │  │
│  └───────────────────────────────────┘  │
│  شماره تماس              ایمیل          │
│  ┌────────────┐  ┌────────────────┐    │
│  │            │  │                │    │
│  └────────────┘  └────────────────┘    │
│  نوع نیاز                               │
│  ┌───────────────────────────────────┐  │
│  │ انتخاب کنید ▾                     │  │
│  └───────────────────────────────────┘  │
│  توضیح بیشتر                            │
│  ┌───────────────────────────────────┐  │
│  │                                   │  │
│  │                                   │  │
│  └───────────────────────────────────┘  │
│           [ ارسال درخواست مشاوره ]      │
└─────────────────────────────────────────┘
```

```css
input, select, textarea {
  font-family: 'Vazirmatn', sans-serif;
  font-size: 14px;
  color: #0F1E30;
  background: #F4F7FA;
  border: 1px solid #DDE6EF;
  border-radius: 6px;
  padding: 11px 14px;
  width: 100%;
  transition: border-color 0.15s, box-shadow 0.15s;
  direction: rtl;
}
input:focus, select:focus, textarea:focus {
  outline: none;
  border-color: #3D6A9E;
  box-shadow: 0 0 0 3px rgba(61,106,158,0.12);
}
```

---

## ۸. آیکون‌ها

### کتابخانه پیشنهادی
**Lucide Icons** — سبک outline، استروک ۱.۸px

### استایل پایه
```css
.icon {
  fill: none;
  stroke-width: 1.8;
  stroke-linecap: round;
  stroke-linejoin: round;
}
```

### رنگ‌بندی آیکون بر اساس موقعیت

| موقعیت | رنگ استروک |
|---|---|
| روی آیکون‌باکس Navy | `#A8C5DC` |
| روی آیکون‌باکس Orange | `#E8680A` |
| آیکون‌های منو | `#4A6077` |
| آیکون Trust Bar | `#A8C5DC` |
| آیکون فوتر | `#5A7A94` |

### آیکون‌های مورد استفاده در سایت

| بخش | آیکون |
|---|---|
| دوربین مداربسته | eye, circle |
| دوربین خورشیدی | sun, clock |
| ردیابی | map-pin, navigation |
| فلزیاب | align-justify |
| امنیت | shield |
| تعمیرات | tool, wrench |
| ارسال | truck |
| گارانتی | clock |
| تماس | phone |
| ایمیل | mail |
| موقعیت | map-pin |

---

## ۹. لایه‌بندی (Layout)

### Grid اصلی

```css
.section-inner {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 24px;
}
```

### Grid های پرکاربرد

```css
/* دسته‌بندی‌ها — ۴ ستونی */
.cat-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 16px;
}

/* محصولات — ۳ ستونی */
.prod-grid {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
}

/* سرویس‌ها — ۲ ستونی */
.srv-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

/* Hero — ۲ ستونی مساوی */
.hero-inner {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 60px;
  align-items: flex-end;
}

/* فوتر — ۴ ستونی نامساوی */
.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: 40px;
}
```

---

## ۱۰. ریسپانسیو (Breakpoints)

| نام | عرض | تغییرات |
|---|---|---|
| Desktop | ≥ 1200px | حالت پیش‌فرض |
| Laptop | ≥ 1024px | همان Desktop |
| Tablet | ≥ 768px | گرید ۴ ستونی → ۲ ستونی |
| Mobile | < 768px | همه گریدها → ۱ ستونی، منو → Hamburger |

### نکات موبایل

```css
@media (max-width: 768px) {
  .hero-inner { grid-template-columns: 1fr; }
  .hero-visual { display: none; } /* موکاپ دستگاه پنهان */
  .hero h1 { font-size: 32px; }
  .cat-grid { grid-template-columns: 1fr 1fr; }
  .prod-grid { grid-template-columns: 1fr; }
  .srv-grid { grid-template-columns: 1fr; }
  .footer-grid { grid-template-columns: 1fr; }
  .nav-links { display: none; } /* → Hamburger Menu */
}
```

---

## ۱۱. انیمیشن و تعامل

### اصل پایه
انیمیشن فقط برای پاسخ به عمل کاربر یا جذب توجه به یک نقطه مهم. هیچ انیمیشن تزئینی.

### Transition استاندارد

```css
/* همه تعاملات کاربر */
transition: [property] 0.15s ease;

/* موارد مناسب */
transition: background 0.15s ease;
transition: color 0.15s ease;
transition: border-color 0.15s ease;
transition: box-shadow 0.15s ease, transform 0.15s ease;
```

### Hover استاندارد کارت

```css
.card:hover {
  transform: translateY(-2px);       /* بالا آمدن کوچک */
  box-shadow: 0 8px 24px rgba(26,46,74,0.10);
}
```

### انیمیشن‌های تعریف‌شده

```css
/* اسکن‌لاین دوربین (Hero) */
@keyframes scan {
  0%   { top: 10%; opacity: 0; }
  20%  { opacity: 1; }
  80%  { opacity: 1; }
  100% { top: 90%; opacity: 0; }
}

/* چشمک LED */
@keyframes blink {
  0%, 100% { opacity: 1; }
  50%       { opacity: 0.3; }
}
```

---

## ۱۲. پالت رنگ WooCommerce

تطبیق رنگ‌های پیش‌فرض WooCommerce با سیستم دیزاین:

```css
/* قیمت اصلی */
.woocommerce .price { color: #1A2E4A; font-family: 'Inter', sans-serif; }

/* قیمت خط‌خورده */
.woocommerce del .amount { color: #8BA3B8; }

/* قیمت تخفیف‌خورده */
.woocommerce ins .amount { color: #E84040; font-weight: 700; }

/* دکمه افزودن به سبد */
.woocommerce .button.add_to_cart_button,
.woocommerce #respond input#submit,
.woocommerce a.button,
.woocommerce button.button,
.woocommerce input.button {
  background: #1A2E4A;
  color: #FFFFFF;
  font-family: 'Vazirmatn', sans-serif;
  font-weight: 700;
  border-radius: 6px;
}
.woocommerce .button:hover { background: #2A4A72; }

/* دکمه خرید اصلی */
.woocommerce .single_add_to_cart_button {
  background: #E8680A;
}
.woocommerce .single_add_to_cart_button:hover {
  background: #F07C28;
}

/* ستاره امتیاز */
.woocommerce .star-rating span::before { color: #E8680A; }

/* نشان حراج */
.woocommerce span.onsale {
  background: #E84040;
  border-radius: 20px;
  font-family: 'Vazirmatn', sans-serif;
  font-weight: 700;
}

/* پیام افزودن به سبد */
.woocommerce-message {
  border-top-color: #E8680A;
}
```

---

## ۱۳. CSS Variables — فایل مرکزی

```css
:root {
  /* رنگ‌های اصلی */
  --color-navy:         #1A2E4A;
  --color-navy-mid:     #2A4A72;
  --color-navy-light:   #3D6A9E;
  --color-orange:       #E8680A;
  --color-orange-mid:   #F07C28;

  /* پس‌زمینه */
  --bg-white:           #FFFFFF;
  --bg-section:         #F4F7FA;
  --bg-dark:            #0F1E30;

  /* متن */
  --text-dark:          #0F1E30;
  --text-mid:           #4A6077;
  --text-light:         #8BA3B8;
  --text-on-navy:       #A8C5DC;

  /* حاشیه */
  --border:             #DDE6EF;
  --border-strong:      #C5D5E5;

  /* وضعیت */
  --status-success:     #4CAF88;
  --status-error:       #E84040;
  --status-warning:     #F5A060;

  /* فونت */
  --font-fa:            'Vazirmatn', sans-serif;
  --font-en:            'Inter', sans-serif;

  /* شعاع */
  --radius-sm:          4px;
  --radius-md:          6px;
  --radius-lg:          8px;
  --radius-xl:          12px;
  --radius-2xl:         16px;

  /* سایه */
  --shadow-card:        0 1px 3px rgba(26,46,74,0.06);
  --shadow-hover:       0 8px 24px rgba(26,46,74,0.10);

  /* انیمیشن */
  --transition:         0.15s ease;
}
```

---

## ۱۴. چک‌لیست اجرایی

### قبل از شروع کدنویسی
- [ ] فایل `variables.css` ساخته و import شده
- [ ] فونت‌ها لود می‌شوند (تست در مرورگر)
- [ ] Child Theme فعال است

### طراحی هر صفحه
- [ ] پس‌زمینه بخش‌ها متناوب است (سفید / آبی‌روشن / Navy)
- [ ] Section Label قبل از H2 وجود دارد
- [ ] CTA اصلی هر بخش با رنگ نارنجی است
- [ ] همه آیکون‌ها outline و stroke 1.8 هستند
- [ ] Border Radius کارت‌ها ۱۲px است

### تست پیش از تحویل
- [ ] تست موبایل (320px، 375px، 768px)
- [ ] بررسی خوانایی فونت فارسی
- [ ] بررسی RTL بودن همه بخش‌ها
- [ ] تست hover روی همه دکمه‌ها و کارت‌ها
- [ ] بررسی رنگ دکمه‌های WooCommerce
- [ ] سرعت لود فونت‌ها (display=swap تنظیم است)

---

*این سند باید در ابتدای هر session کدنویسی مرور شود.*  
*تغییرات باید ابتدا اینجا ثبت شوند، سپس در کد اعمال گردند.*
