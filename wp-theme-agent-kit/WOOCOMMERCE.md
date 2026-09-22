# WOOCOMMERCE

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

مرجع canonical: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md)، §4، §7 تا §12، §18 و §19؛ ADR-003، ADR-005، ADR-006 و ADR-008.

## Accepted commerce contract — EXTRACTED / OWNER_CONFIRMED

- WooCommerce dependency رسمی و required است.
- فروش مستقیم آنلاین: Product → Add to Cart → Cart → Checkout → Payment → Order Confirmation.
- Shop/Product/Cart/Checkout/Payment/Confirmation/My Account/Order History/Customer Account و موفقیت/شکست/لغو پرداخت P0 هستند.
- Iranian Online Payment Gateway از WooCommerce Gateway abstraction؛ Provider-agnostic و بدون provider hard-code در Core Theme.
- Lead-only و COD مدل اصلی نیستند؛ مشاوره رایگان/اجاره/تعمیر conversion مکمل‌اند.
- price، SKU، stock، gallery، descriptions، categories و attributes فقط Woo-native؛ custom catalog، cart یا order source موازی در Theme ممنوع.
- Theme صاحب UI و custom metadata/features است؛ payment transaction، verification، callback/webhook processor و merchant credentials در Theme نیست.

## Payment boundary

Theme UI → Woo Checkout → Woo Payment Gateway → Iranian PSP/Gateway → Callback/Webhook → Woo Order Status.

`PROPOSED` — gateway integration مسئول verification، amount/currency/order matching و idempotent completion است؛ Theme status معتبر Woo را نمایش دهد. URL بازگشت یا client state proof of payment نیست. موفقیت می‌تواند processing باشد، نه الزاماً completed. تأخیر callback با failed/cancelled اشتباه نشود.

Provider خاص optional/pluggable است، اما حداقل یک gateway آنلاین فعال و آزموده شرط انتشار است. نبود gateway مجوز fallback خاموش به COD نیست.

## PROPOSED — presentation and flow mapping

- shop.html → Woo archive/category queries با Theme cards و فیلترهای taxonomy/attributes native.
- چهار product HTML → template مشترک و چهار Product، نه چهار data model.
- cart.html → Woo cart/session؛ localStorage demo مرجع تجاری نیست.
- checkout.html → checkout واقعی؛ export متن demo جای پرداخت نیست.
- My Account و order endpoints → Woo native؛ WP identity و WC Customer؛ بدون user/order store موازی.
- Compare/Quiz → Theme-native؛ داده استاندارد Woo و rules/config داخلی Theme.
- native hooks/adapters بر overrides گسترده اولویت داشته باشند؛ هر override مستند و version-tracked.

## P0 state coverage — PROPOSED acceptance

- cart empty/populated/update/remove، stock/price changes، validation و network failure.
- checkout loading، duplicate submit protection، session expiry، billing/shipping/tax validation.
- payment pending/redirect/verification delayed/success/failed/user cancelled/order cancelled/retry و callbackهای تکراری یا دیررس.
- order confirmation با access check و status واقعی؛ no private cache.
- account logged-out/login/reset/error، authenticated dashboard، empty/paginated orders، order detail/forbidden access، addresses/account save states.
- user A نتواند order user B را ببیند؛ guest access با verification استاندارد Woo منتخب.

## NEEDS_DECISION

- ND-R04: currency، tax، shipping و billing requirements.
- ND-R05: guest checkout، registration و identity policy؛ وجود Account باز نیست.
- ND-R06/ND-R07: Product types، داده معتبر و attribute schema.
- ND-R10: provider qualification و Blocks در برابر Classic؛ هیچ‌کدام اکنون انتخاب نشده‌اند.
- ND-R13: gateway sandbox، browser/a11y و performance test environment.

در Ratification هیچ Woo template، gateway، handler یا endpoint ساخته/نصب/متصل نمی‌شود.
