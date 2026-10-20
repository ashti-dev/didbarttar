# SECURITY

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

WordPress security requirements:
- escape output
- sanitize input
- validate input
- nonce verification
- capability checks
- secure AJAX/REST endpoints
- upload validation
- safe redirects
- admin permissions
- no secrets in theme files
- safe settings persistence
- WooCommerce data handling

Never treat client-side validation as sufficient security.

## Ratification security boundaries — 2026-09-21

Canonical contract: [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md), sections 8, 9, 13 and 19.

`EXTRACTED — OWNER_CONFIRMED / Accepted`:
- WooCommerce owns commerce and payment abstraction. Theme must not implement PSP HTTP calls, transaction verification, callback/webhook processing or gateway credential storage.
- Iranian gateway integrations own provider-specific processing through WooCommerce standards; no provider hard-coding in Core Theme.
- Theme-native custom data and UI must not depend on ACF, equivalent field frameworks or a dedicated core-feature plugin.

`EXTRACTED`:
- Existing request forms lack method/action and rely on JS preventDefault. In no-JS/initialization failure, normal form submission can place PII in a GET URL. This issue is documented, not fixed in this architecture pass.

`PROPOSED` requirements for implementation acceptance:
- Public form POST, server validation, bounded inputs, no PII in URLs/logs, rate limiting/anti-spam and explicit retention/delivery policy. A public nonce is not authentication or sufficient spam protection.
- Meta/options writes require capabilities and object permissions as well as nonce checks; typed schema and context-appropriate output escaping. REST exposure must be intentional and permission-correct.
- Theme must read authoritative Woo payment/order state; redirect success parameters or localStorage cannot mark an order paid. Gateway/Woo must verify amount/currency/order and handle duplicate/late callbacks idempotently.
- Account/order routes require ownership checks and Woo-native guest verification; prevent IDOR. Private cart/checkout/account/order responses must not enter shared public caches.
- Use Woo order APIs, not assumptions about postmeta order storage; maintain HPOS compatibility for the selected platform.
- No payment card data or OTP collection/logging in Theme. Credential management belongs to the gateway integration.
- Migrations must be idempotent, backup-first and privilege-protected; Theme switch must not purge custom data or commerce records.

`NEEDS_DECISION`: ND-R04, ND-R05, ND-R10, ND-R11 and ND-R16 define unresolved payment units, customer policy, integration qualification, forms/privacy and migration details. No handler or gateway is implemented by this document.
