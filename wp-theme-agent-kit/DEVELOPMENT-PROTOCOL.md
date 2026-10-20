# DEVELOPMENT PROTOCOL

## وضعیت جاری — Phase 6، مجوز مستقیم Owner / 2026-09-22

**Phase 6 = IN PROGRESS — HTML → WordPress/WooCommerce Migration.** دستور جدید Owner جای محدودیت قبلی «Foundation only / هیچ فاز بعدی شروع نشود» را برای migration گرفته است. QA اجرا‌نشده Foundation، مرورگر و runtime موارد **Pending پیش از release** هستند، نه مانع عمومی توسعه. معیارهای پذیرش حذف نشده‌اند؛ هیچ compatibility یا release PASS اعلام نشده است. Payment provider، SEO نهایی و داده نهایی کاتالوگ نیز فقط جزء وابسته را متوقف می‌کنند.

مرجع فعلی: [Migration Map](PHASE-6-MIGRATION-MAP.md) و [گزارش کد و QA](PHASE-6-MIGRATION-REPORT.md). §21 و گزارش Phase 5 سابقه scope/آزمون همان مرحله‌اند؛ تصمیمات هویت، Classic Theme، WP/Woo ownership، MySQL-only و ممنوعیت dependencyهای مشخص همچنان معتبرند. schema، handler یا تصمیم معماری باز به‌صورت ضمنی تصویب نشده است.


## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

## Historical gate — Foundation authorization, superseded by Phase 6 above

`EXTRACTED — OWNER_CONFIRMED`: Owner has authorized Phase 5 Foundation only, superseding the prior documentation-only stop. **Phase 5 = STARTED — QA INCOMPLETE**; ND-R01 and ND-R02 remain OWNER APPROVED. Brand/identity remain torantejarat and Database Support remains MySQL only. Existing numerical/browser proposals remain unchanged; Compatibility Tested = No. Follow Contract §21, Accepted ADRs and [PHASE-5-FOUNDATION-REPORT.md](PHASE-5-FOUNDATION-REPORT.md). Source is implemented; missing PHP/WP/Woo/MySQL/browser environments prevent runtime acceptance. That authorization was Foundation-only; the current Owner instruction separately authorizes source migration. No unapproved schema, handler, dependency or legacy refactor is authorized.

For this implementation, use the saved pre-change manifest, named source checks, available syntax checks, temporary-copy failure probes and diff review. Run real PHP/WordPress/browser checks only on an available isolated fixture; otherwise mark NOT TESTED. Do not report static checks as runtime/commerce/accessibility/performance PASS.

## Implementation lifecycle — only after approval

Preferred implementation lifecycle:

1. Baseline
2. RED test/spec proof
3. Implementation
4. GREEN
5. Mutation testing where meaningful
6. Re-proof if tests changed
7. Full regression
8. Check-name diff
9. Diff audit
10. Report
11. Commit/push according to project policy

Rules:
- Never weaken a test to obtain GREEN.
- A surviving meaningful mutant requires investigation.
- A reduction in check count requires investigation.
- Compare check names, not only totals.
- Verify behavioral fixtures come from real project markup where applicable.
- Protected files require explicit approval.
- Unrelated scope must remain untouched.
