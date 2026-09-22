# MASTER PROMPT — HTML TEMPLATE → PROFESSIONAL PERSIAN WORDPRESS/WOOCommerce THEME

## Project Metadata

### Project Identity / Credits

`EXTRACTED — OWNER_CONFIRMED / Accepted`

**Author:** یعقوب طیبی

**Designer:** یعقوب طیبی

**Website:** [https://yaghoubtayebi.ir/](https://yaghoubtayebi.ir/)

این اطلاعات رسمی پروژه است و باید با همین نام، عنوان و Website در مستندات نهایی پروژه حفظ شود.

You are starting from an existing HTML/CSS/JS design.

Your mission is NOT to immediately convert it to PHP.

Your mission is to reverse-engineer the existing design and produce an approved documentation/architecture baseline for a professional, fast, Persian-first, RTL WordPress theme with WooCommerce support.

## HARD RULE: NO PRODUCTION CODE IN THIS PHASE

Do not:
- create PHP theme files
- convert HTML to PHP
- create functions.php/header.php/footer.php
- create WooCommerce templates
- refactor the existing frontend
- change CSS/JS/HTML
- add dependencies
- delete files

First analyze, document, identify gaps and decisions.

## 1. Establish baseline

Inspect the complete repository.

Record:
- tree
- files
- dependencies
- existing tests
- scripts
- build tools
- generated/protected files
- current test status

If tests exist, run the relevant baseline. Do not modify tests.

## 2. Evidence protocol

Use the real HTML/CSS/JS/assets as the source of truth for current behavior.

For every extracted fact distinguish:
- EXTRACTED
- PROPOSED
- NEEDS_DECISION

Never present an inference as an existing fact.

## 3. Generate the core documents

Create/update:

AGENTS.md
PRODUCT.md
SITEMAP.md
CONTENT-MODEL.md
COPY.md
DESIGN-TOKENS.md
LAYOUT-CONTRACT.md
COMPONENTS.md
NAVIGATION.md
CONSTRAINTS.md
ADR.md
ACCEPTANCE.md

Then create, only where evidence/requirements justify them:

SETTINGS.md
I18N.md
A11Y.md
PERF.md
SECURITY.md
SEO.md
ADMIN.md
QA.md
WOOCOMMERCE.md
DEVELOPMENT-PROTOCOL.md

Do not create documentation merely to increase file count.

## 4. HTML reverse engineering

Inventory every:
- page
- section
- component
- asset
- font
- icon
- library
- breakpoint
- repeated pattern
- state
- interaction

Identify:
- reusable components
- static versus dynamic content
- missing states
- duplicated UI
- inconsistent tokens
- responsive problems
- accessibility problems
- performance risks

## 5. Design System extraction

Extract actual:
- colors
- typography
- spacing
- radii
- shadows
- breakpoints
- container rules
- grid/flex rules
- component states

Do not invent a new design system when the existing CSS already defines one.

Where the current system is inconsistent, record the inconsistency as a GAP and propose a normalization strategy.

## 6. Content model

Before WordPress architecture, identify the content entities required by the UI.

For each:
- fields
- source of truth
- relationships
- admin ownership
- dynamic consumers
- WooCommerce relation

Do not create custom post types/custom fields without a content reason.

## 7. Layout contract

Document the actual layout behavior across:
- mobile
- tablet
- desktop
- large desktop

Document wrapping, stacking, hiding, scrolling, sticky behavior and container geometry.

This contract must exist before implementation.

## 8. Component contract

For every major component document:
- markup role
- variants
- states
- responsive behavior
- accessibility
- dynamic data
- WordPress/WooCommerce mapping

## 9. Persian/RTL

The target is Persian-first.

Analyze:
- RTL
- typography
- numbers
- dates
- currency
- direction-aware icons
- logical CSS
- forms
- navigation
- tables
- pagination
- translation strategy

## 10. WooCommerce

Map the existing design to:
- shop
- product archive
- product card
- single product
- gallery
- variations
- pricing
- stock
- cart
- checkout
- account
- filters
- search
- related/upsell

Identify missing states such as:
- loading
- empty
- out of stock
- sale
- invalid variation
- cart error
- checkout error

## 11. Performance

Analyze:
- CSS
- JS
- fonts
- images
- third-party resources
- requests
- DOM complexity
- animations
- loading strategy

Produce measurable budgets where possible.

Target fast-by-architecture, including Core Web Vitals.

## 12. Accessibility

Audit:
- semantics
- headings
- keyboard
- focus
- forms
- dialogs/drawers
- contrast
- reduced motion
- images
- ARIA

## 13. SEO

Define ownership of:
- titles/meta
- canonical
- schema
- breadcrumbs
- archives
- pagination
- product/category SEO
- Open Graph

Separate theme responsibility from SEO-plugin responsibility.

## 14. Security

Define requirements for:
- escaping
- sanitization
- validation
- nonce
- capabilities
- AJAX/REST
- uploads
- settings
- user input

## 15. Conversion UX

Audit:
- CTA hierarchy
- trust signals
- product discovery
- search
- filters
- comparison
- social proof
- mobile conversion
- checkout friction

The target is not merely attractive UI. It must support business conversion.

## 16. GAP system

Every issue becomes a traceable GAP.

Format:

GAP-XXX
- Type: Design / Technical / UX / Performance / A11Y / SEO / Security / Content
- Severity: P0/P1/P2/P3
- Evidence:
- Current behavior:
- Problem:
- Impact:
- Recommendation:
- Needs Decision:
- Related document:

Do not silently fix a GAP in this phase.

## 17. ADR system

Architectural choices must be recorded as:
- Proposed
- Accepted
- Rejected
- Superseded

Do not bury architecture decisions inside implementation notes.

## 18. Acceptance

Define measurable Definition of Done for:
- components
- pages
- WooCommerce flows
- responsive behavior
- RTL
- accessibility
- performance
- SEO
- security

## 19. Final readiness report

Return:

1. Executive Summary
2. Repository Baseline
3. Current Architecture
4. Sitemap
5. Product Definition
6. Content Model
7. Copy Inventory
8. Design Tokens
9. Layout Contract
10. Components
11. Navigation
12. WordPress Mapping
13. WooCommerce Mapping
14. RTL/I18N
15. Accessibility
16. Performance
17. SEO
18. Security
19. Conversion UX
20. Design Gaps
21. Technical Gaps
22. ADR Candidates
23. Acceptance Criteria
24. Documentation Readiness
25. Agent Backlog
26. Recommended Development Order

## 20. Final restriction

At the end of this phase:
- no production code should have changed
- no theme skeleton should have been generated
- no dependency should have been added

Stop and wait for approval.

Only after approval may the next phase begin:

Discovery → Documentation Approval → WordPress Architecture → Theme Foundation → Components → Pages → WooCommerce → RTL/I18N → SEO → A11Y → Performance → Security → Testing → QA → Release.
