# QA

## Next Phase — Runtime QA (migration frozen)

`Core Migration: IMPLEMENTATION COMPLETE`

`Runtime QA: BLOCKED / NOT AVAILABLE`

No further Phase-6 source iteration or environment installation. The next phase runs on an isolated **real WordPress + WooCommerce + MySQL + PHP + browser** fixture; see [Runtime QA Prerequisites and coverage](PHASE-6-MIGRATION-REPORT.md#runtime-qa-prerequisites) and the [separate F-AC mapping](ACCEPTANCE.md#runtime-qa--separate-acceptance-record-after-freeze).

Before execution, identify the frozen Git commit, diff/inventory all subsequent changes, record versions/configuration/fixtures and provide appropriate database visibility. Numeric/browser proposals are not approvals or compatibility results. Capture actual runtime/browser/DB evidence; no synthetic or source-only PASS. Keep source checks separate, pending cases blocked, and sensitive artifacts out of Git. No new account, checkout, form, schema or gateway decision is authorized by QA. Fix only demonstrable defects; no cleanup refactors.


## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

Historical Foundation evidence: ND-R01 and ND-R02 = OWNER APPROVED; MySQL only, version/browser proposals unchanged, Compatibility Tested = No. Owner has authorized limited Foundation implementation and QA. **Phase 5 = STARTED — QA INCOMPLETE**. [Test instructions](../tests/README.md) and [acceptance evidence](PHASE-5-FOUNDATION-REPORT.md) distinguish passing source checks from NOT TESTED runtime criteria.

For future authorized QA, record exact versions/patches and actual results; database coverage is MySQL only. Owner approval is not a test result.

Define supported:
- browsers
- devices
- viewport classes
- WordPress environments
- WooCommerce flows

Test categories:
- functional
- visual
- responsive
- RTL
- accessibility
- performance
- SEO
- security
- regression

Baseline test names/checks should be preserved unless an intentional change is documented.
