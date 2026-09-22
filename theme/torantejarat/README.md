# توران تجارت — Classic Theme migration

**Author / Designer:** یعقوب طیبی  
**Website:** https://yaghoubtayebi.ir/

Phase 6 core migration: **IMPLEMENTATION COMPLETE — RUNTIME QA BLOCKED**, not DONE or release-ready. Source version 0.1.3 also changes the asset cache key; this is not a release certification. WordPress/WooCommerce own content and commerce. PHP/WP/Woo/MySQL/browser execution has **NOT BEEN TESTED** in this workspace. MySQL is the only approved database family; numeric platform/browser proposals remain unverified. Do not treat the metadata headers as compatibility certification.

## Native content setup on an isolated QA site

After PHP lint and fixture setup, install this directory as `wp-content/themes/torantejarat` (not the repository root). Do not run the historical HTML server as a Theme preview.

- Site title/tagline/custom logo/site icon: native WordPress identity. No brand option framework.
- Assign Primary, Utility, Home Actions and up to three Footer menus. No guessed business slugs or sample links. Footer column titles use the assigned menu names.
- A static Front Page uses its title/excerpt/featured image as the source hero and its content as the authored body. Settings → Reading with “latest posts” uses the blog listing instead.
- Native Page patterns under **توران تجارت — چیدمان‌های منبع** provide benefits, field/video and FAQ. Insert them in the static Front Page and author real content/select real media; they contain no sample claims, questions, URLs or video. Core/group, paragraph, heading, video and details are used, not custom blocks or metadata. The original top-level classes place benefits after the hero and FAQ after the editorial section; other Page content stays in the middle. All regions render in the main loop through native content filters. No Gutenberg plugin is needed. Theme-owned empty slots are pruned only at render time: video requires a source, FAQ requires a question and an answer (text or media). Saved blocks remain editable. Unfilled slots/menu assignments are **CONTENT PENDING**, never seeded with business copy.
- Assign **کارت‌های خدمات خانه** to an ordinary WP menu containing published Page/Post links. Card titles/excerpts come from those records, CTA labels from menu labels; no guessed service slugs. Protected/nonpublic content is skipped.
- Optional **آکادمی — منابع موجود** template uses the **منابع آکادمی** menu to curate existing public WP Posts/Pages in the source editorial grid, plus the Academy Page's own content. This does not define a course/CPT/taxonomy model.
- Home shows up to four nonempty root Woo categories (native name ordering), up to four most recent catalog-visible featured Woo products, further checked with Woo’s own `is_visible()`, and three latest published WP posts. No demo fallback. Configure real content; this Theme does not seed anything.
- Blog/index/archive/search share one main-query listing and native pagination; a grid is emitted only after the first visible card. Empty results use the source empty-state with native navigation, never sample results. Editorial cards use real images, taxonomy labels, titles and excerpts. Home/Academy section cards use h3 beneath h2; unsectioned listings retain h2. Woo section loops likewise use h3, without changing product selection.
- Article TOC uses only explicitly authored Gutenberg heading anchors. No generated anchor mutation, taxonomy convention, read-time algorithm or article variant meta. Nested headings are supported; empty labels, malformed and duplicate anchors are skipped, and fragment URLs are encoded without rewriting IDs. The sidebar uses source geometry, semantic list navigation and the assigned Posts page link when public. Classic content renders normally but does not get an inferred TOC.
- Optional **درباره ما — چیدمان منبع** template uses Page excerpt/featured image/content in the source About columns.
- Optional **تماس و خدمات — چیدمان منبع** template uses those same native fields in the source Contact/Rent/Repair layout. The content area is NOT a fake form. Submission/retention/consent/upload workflows require the unresolved handler contract.
- Woo Shop/Product Taxonomy/Single use native Woo templates and hooks. The only override is `woocommerce/content-product.php` (upstream template version 9.4.0, reviewed in Woo 10.8.0). Keep all public loop hooks when updating it.
- Existing Cart/Checkout/My Account Pages and Woo order/account endpoints render through native page content. No checkout mode, gateway, guest policy, currency, tax, shipping or order status is selected/overwritten by the Theme. Native cart.php presentation hooks place the unchanged form/totals in the source 350px sidebar grid and relocate the unchanged Woo cross-sell renderer below it. This neither assigns shortcode mode nor adds a Blocks implementation. Account uses native templates/endpoints/nonce/logout and source-derived CSS only; it has no dedicated reference HTML.

## Assets and known gaps

`assets/css/source/` is the existing design with class names prefixed and tokens scoped to the site body. `native.css` adapts WordPress menus/headings and Woo markup. Neutral Foundation frontend styles remain as historical files but are no longer enqueued. Source `cart.css`/`compare.css` are staged, not loaded for fake commerce/features. All images/video are byte-preserving source copies, not imported product records or automatically displayed sample media.

Only mobile navigation is extracted into Theme JS; late enhancement preserves an already-focused menu link. FAQ uses native details/summary behavior. Woo owns galleries/variations/add-to-cart and cart fragments. Test badge refresh on the eventual chosen checkout mode, including Blocks quantity changes and cached pages. No custom cart store/API.

No local Vazirmatn font exists in the reference. No Google Fonts/CDN dependency is introduced; the source font stack currently falls back to Tahoma/Arial. Final font/media rights and locally licensed font delivery remain pending. Home sections and service/Academy navigation now have native content consumers, but require authored content/menu assignments; no placeholder business copy is published. Finder/Quiz behavior, custom product video/story/limitations, advanced filters, Forms handlers, Compare and Quiz remain pending their respective decisions. Visible Woo attributes, stock, sale price, gallery, meta and related products use native Woo data; no new product metadata exists.

See [current acceptance](../../wp-theme-agent-kit/ACCEPTANCE.md), [first-iteration migration report](../../wp-theme-agent-kit/PHASE-6-MIGRATION-REPORT.md) and [QA instructions](../../tests/README.md). No dependency installation, payment integration, deployment or release has been performed.

## Final implementation boundary — 2026-09-22

| Core surface | Implementation disposition | Remaining gate |
|---|---|---|
| Header/footer/Home | Native menus/content, source structure and editable empty slots | CONTENT PENDING; rendering/browser QA |
| Blog/Category/Archive/Search/404 | Main query, shared visible-result/empty-state, native pagination/recovery | Query fixtures, screenshots, escaping and keyboard QA |
| Article | Source sidebar/TOC, authored anchors, native body/metadata and multipage links | Classic/block/protected/multipage/long-TOC fixtures |
| About/Academy/Contact/Rent/Repair | Native content layouts; Academy uses existing public Posts/Pages | CONTENT PENDING; forms handler remains undecided |
| Shop/Product | Native Woo data/hooks, source card override, gallery/summary/attributes/related | Product/state/extension compatibility and visual QA |
| Cart | Conditional native-template layout/CSS; native empty state, quantity, totals, cross-sells and CTA retained | Real updates/coupons/shipping/removal; ND-R10 mode qualification |
| My Account | Native navigation/current state/dashboard/orders/details/logout; shared source tokens | Authenticated/guest/empty/error/authorization fixtures; account policy decisions |
| Checkout/order endpoints | Native Page/endpoint passthrough; no fake flow, gateway or order-state logic | Mode/provider/policy qualification and actual lifecycle QA |

No further **independent core migration implementation** was identified in the final review. This is a bounded implementation status, **not** visual parity certification, full feature completion, Template DoD or launch readiness. Runtime regressions may require fixes. ND-R10 still gates mode-specific checkout work; Blocks content remains Woo-owned and is not claimed styled/tested to parity. No separate My Account/Search/404 HTML exists (Contract §16); the implementation reuses the existing article-nav, form, button, page-intro and empty-state language, not an invented dashboard.

Forms → **Pending Handler Decision**; Compare → **Pending Data Contract**; Quiz → **Pending Data/Administration Contract**. Specialized filters, new product metadata/schema, PSP, real content entry, font decision and SEO implementation remain untouched/pending. Product-specific video/story/limitations/read-time conventions are not inferred from source copy.

### Source inspection, not runtime evidence

Reviewed Woo 10.8.0 source: [cart.php](https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.0/plugins/woocommerce/templates/cart/cart.php) (template 10.8.0), [cart-totals.php](https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.0/plugins/woocommerce/templates/cart/cart-totals.php) (2.3.6), [account navigation](https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.0/plugins/woocommerce/templates/myaccount/navigation.php) (9.3.0), [orders](https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.0/plugins/woocommerce/templates/myaccount/orders.php) (9.5.0), [account details](https://raw.githubusercontent.com/woocommerce/woocommerce/10.8.0/plugins/woocommerce/templates/myaccount/form-edit-account.php) (10.5.0). The `my-account.php` fetch failed; no successful inspection of that file is claimed. No additional Woo template was copied/overridden. WP 6.9 [post-template.php](https://raw.githubusercontent.com/WordPress/wordpress-develop/6.9/src/wp-includes/post-template.php) confirmed `post-nav-links`/`post-page-numbers`; the dead `page-links` bridge was replaced. Existing catalog/related selectors have actual PHP class producers and were retained; repeated price selector blocks were consolidated without removing responsive declarations.
