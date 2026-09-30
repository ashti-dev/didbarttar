# AGENTS.md — WordPress Theme Agent Operating Rules

## Current task — Phase 7 Runtime QA Preparation (2026-09-22)

Baseline **`4640054480f7ccf5a29a3ba38f703534fe2bef9e`**, branch **`arena/01a0c314-didbarttar`**. Phase 6 stays closed/frozen. Phase 7 is **preparation only**, not runtime execution or migration: see [the executable matrix and minimal fixture plan](QA.md). Do not alter Theme/templates/CSS/JS/assets or frozen test runners, refactor, create fixtures, install dependencies/environments, or implement pending contracts. Inspect environment read-only. Record a discovered defect; no fix during preparation without separate authorization. Current **RUNTIME QA BLOCKED**; no source result can count as real WP/Woo/browser/MySQL PASS.

## Owner Freeze — effective 2026-09-22

**Phase 6 is FROZEN: IMPLEMENTATION COMPLETE — RUNTIME QA BLOCKED.** No new source iterations, cosmetic fixes/refactors, dependency installation or Phase-6 runtime provisioning. Change frozen code only for a specific demonstrable defect with evidence and a minimal regression-backed fix; do not implement pending contracts. Next phase is **Runtime QA on a real WP/Woo/MySQL/PHP/browser fixture**. Source PASS never becomes runtime PASS. The [Exit Record and baseline inventory](PHASE-6-MIGRATION-REPORT.md) take precedence over earlier “continue migration” instructions below. NOT DONE / NOT READY FOR PRODUCTION.

## وضعیت جاری — Phase 6، مجوز مستقیم Owner / 2026-09-22

**Phase 6 core migration = IMPLEMENTATION COMPLETE — RUNTIME QA BLOCKED (2026-09-22، طبق دستور نهایی Owner).** بررسی نهایی Article/Archive/Cart/Account/empty states انجام شد؛ کار مستقل دیگری در migration هسته شناسایی نشد. این وضعیت DONE یا release-ready نیست. Runtime QA = BLOCKED / NOT AVAILABLE؛ feature contracts، mode/provider/account policies و محتوای واقعی همچنان Pending هستند. مرز فعلی در [README قالب](../theme/torantejarat/README.md) و evidence در [Acceptance](ACCEPTANCE.md) است. دستور جدید Owner جای محدودیت قبلی «Foundation only / هیچ فاز بعدی شروع نشود» را برای migration گرفته است. QA اجرا‌نشده Foundation، مرورگر و runtime موارد **Pending پیش از release** هستند، نه مانع عمومی توسعه. معیارهای پذیرش حذف نشده‌اند؛ هیچ compatibility یا release PASS اعلام نشده است. Payment provider، SEO نهایی و داده نهایی کاتالوگ نیز فقط جزء وابسته را متوقف می‌کنند.

مرجع فعلی: [Migration Map](PHASE-6-MIGRATION-MAP.md) و [گزارش کد و QA](PHASE-6-MIGRATION-REPORT.md). §21 و گزارش Phase 5 سابقه scope/آزمون همان مرحله‌اند؛ تصمیمات هویت، Classic Theme، WP/Woo ownership، MySQL-only و ممنوعیت dependencyهای مشخص همچنان معتبرند. schema، handler یا تصمیم معماری باز به‌صورت ضمنی تصویب نشده است.


## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

## Role
Act as a senior WordPress theme architect, frontend engineer, WooCommerce engineer, QA engineer, and performance/accessibility reviewer.

## Core rule
Do not invent architecture while coding. Read the relevant specification first. If the specification is insufficient, record `NEEDS_DECISION` and stop before making a consequential architectural choice.

## Ratified owner boundaries — 2026-09-21
Before any architecture or implementation task, read [ARCHITECTURE-CONTRACT.md](ARCHITECTURE-CONTRACT.md) and [ADR.md](ADR.md). `OWNER_CONFIRMED / Accepted` requirements bind subsequent decisions; `PROPOSED` details are not approved implementation choices.

- Theme owns its custom features; WooCommerce owns commerce; WordPress owns core content.
- ACF Free/Pro and equivalent custom-field/options framework dependencies are forbidden.
- Do not place core Theme features in a dedicated plugin or make them depend on a Page Builder.
- Use Theme-native Settings/Meta/Options APIs and native admin UI for custom data. Do not duplicate standard Woo product, price, stock, category, attribute, gallery or description data.
- WordPress and WooCommerce are required. Iranian gateway provider, SEO and form integrations are pluggable; a working qualified Iranian online gateway is nevertheless mandatory for commerce go-live.
- Real online sales, Checkout, Payment, Order Confirmation, My Account and Order History are P0. Lead-only and COD are not the primary business model.
- No PSP transaction, verification, credential or callback-processing logic in the Theme; use the WooCommerce Gateway abstraction through a standard gateway integration.
- Forms handler selection is still open; do not assume a form plugin is required.
- Owner has explicitly authorized Phase 6 source-faithful HTML → WP/Woo migration. New unapproved architecture/dependencies and legacy refactors remain outside scope.
- Historical Discovery ADR numbering differs from the current registry; use the crosswalk in ADR.md rather than silently reinterpreting old references.
- Foundation Ratification: Phase 5 is Foundation only; read Architecture Contract §21 for IN/OUT and acceptance. Classic Theme (no FSE) and Gutenberg for content editing are confirmed; frontend/Core rendering must not require the editor or Gutenberg plugin.
- Historical Foundation gate (superseded for development by Phase 6 authorization above): **Phase 5 = STARTED — QA INCOMPLETE**. **ND-R01 = OWNER APPROVED**, **ND-R02 = OWNER APPROVED**. Only `theme/torantejarat/`, minimal Foundation QA and necessary status documentation are in scope. Exact identity follows [FINAL-DECISION-SHEET.md](FINAL-DECISION-SHEET.md); MySQL only, numeric/browser proposals unchanged, Compatibility Tested = No. Read [PHASE-5-FOUNDATION-REPORT.md](PHASE-5-FOUNDATION-REPORT.md): source checks do not substitute for missing PHP/WP/Woo/MySQL/browser runtime checks. Continue authorized migration; do not claim release readiness.
- Do not build a generic data framework, Product metadata/editor, migration engine or feature stubs in Foundation. Only actual consumers justify settings/JS; Phase 6 has a real mobile-navigation consumer. Legacy HTML/CSS/JS/assets and the historical Review are protected.

## Evidence first
Before any change:
1. Inspect the real repository tree.
2. Establish a baseline.
3. Identify protected/frozen/generated files.
4. Identify existing tests and tripwires.
5. Read relevant ADRs.
6. Run authorized checks and record exact environments. Missing-runtime criteria are NOT TESTED, not PASS; do not install dependencies or guess architecture to bypass a blocker.

## Change discipline
- Keep changes small and reviewable.
- Do not weaken or delete an existing test merely to make it pass.
- Do not remove assertions without a documented reason.
- Do not change protected files without explicit approval.
- Do not refactor unrelated code while implementing a scoped task.
- Do not add dependencies without justification and an ADR when architectural.
- Prefer native WordPress/WooCommerce APIs over unnecessary custom abstractions.

## Validation protocol
For implementation tasks, prefer:
RED → Implementation → GREEN → Mutation Testing where applicable → Re-proof if tests changed → Full Regression → Check-name Diff → Diff Audit.

## Completion
A task is not Done until its acceptance criteria pass, relevant regression passes, and the final diff is reviewed.

## Reporting
Every task report must include:
- baseline
- files changed
- behavior changed
- tests/checks run
- regression result
- remaining gaps
- decisions required
