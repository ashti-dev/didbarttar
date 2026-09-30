# SETTINGS — Theme-native

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

مرجع canonical: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md)، §4 و §5؛ ADR-002 و ADR-003.

## Accepted constraints — EXTRACTED / OWNER_CONFIRMED

- تمام settings و custom configuration موردنیاز Theme با کدنویسی داخل خود Theme و native WordPress APIs مدیریت شوند.
- ACF Options Page، ACF Free/Pro و framework مشابه custom fields/options ممنوع‌اند.
- plugin اختصاصی برای اجرای settings/quiz/compare metadata لازم نباشد.
- Woo settings شامل price/stock/shipping/tax/payment متعلق به Woo باقی بمانند؛ Theme آن‌ها را mirror نکند.
- credential درگاه در Theme settings ذخیره نشود؛ تنظیمات provider با integration استاندارد Woo.

## PROPOSED registry

| خانواده | Native storage پیشنهادی | UI / permission | Validation / fallback |
|---|---|---|---|
| هویت/راه‌های ارتباطی/نمایش محدود | `<prefix>_settings` option | Settings API؛ capability مصوب مدیریت Theme | sanitize per-field؛ مقادیر واقعی و معتبر؛ نبود تماس به معنی ساخت تماس فرضی نیست |
| quiz questions/rules | `<prefix>_quiz_config` versioned option | native Theme rules editor؛ capability مصوب | typed schema/operators/terms/units؛ config invalid → recommendation غیرفعال و fallback shop |
| compare row presentation | بخش مستقل config Theme | native controls؛ عدم اجازه تغییر native product facts | mapping allowlist به Woo fields؛ unknown صریح |
| product presentation | product meta طبق قرارداد | native product panels و مجوز edit محصول | schema و bounded content؛ حذف section خالی |
| article art variant | post meta enum | native sidebar/metabox و edit post | enum allowlist؛ default variant |
| migration/schema version | versioned option فنی | فقط عملیات مدیر مجاز | idempotent؛ migration در request عمومی اجرا نشود |

این keyها پیشنهادی‌اند؛ `<prefix>` نام نهایی نیست. برای هر setting پیش از اجرا باید key، label، type، default، validation، sanitization، permission، consumer و migration مشخص باشد. همه رشته‌ها و styleها به option تبدیل نشوند.

## Decision status / جزئیات باقی‌مانده

هویت فنی `torantejarat` و namespace `ToranTejarat\Theme` طبق ND-R01 مصوب است. در Foundation هیچ setting اختصاصی مصرف واقعی نداشت؛ در نتیجه `inc/settings.php`، Options page، Meta registration یا registry ساخته نشده است. از site title و menu/Reading settings بومی WP استفاده می‌شود. **Phase 5 = STARTED — QA INCOMPLETE** طبق [Phase 5 Foundation Report](PHASE-5-FOUNDATION-REPORT.md)؛ جزئیات Product/editor/business settings/rules/lifecycle همچنان به فاز مربوط deferred هستند.
