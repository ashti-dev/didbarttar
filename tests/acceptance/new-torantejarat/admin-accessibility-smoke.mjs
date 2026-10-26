import assert from 'node:assert/strict';
import fs from 'node:fs';
import path from 'node:path';
import AxeBuilder from '@axe-core/playwright';

/**
 * Keyboard and focus acceptance for the existing administrator page.
 *
 * The caller has already authenticated and prepared the twelve-row FAQ fixture.
 * These checks navigate and mutate only the DOM; no form is submitted here.
 */
export async function runAdminSettingsAccessibility({ page, settingsUrl, goto, passed, resultsDir }) {
  await page.setViewportSize({ width: 1440, height: 1000 });

  const assertFocusIndicator = async (locator, label) => {
    await locator.focus();
    const style = await locator.evaluate((element) => {
      const computed = getComputedStyle(element);
      return {
        focused: document.activeElement === element,
        outlineStyle: computed.outlineStyle,
        outlineWidth: Number.parseFloat(computed.outlineWidth) || 0,
        boxShadow: computed.boxShadow,
      };
    });
    assert.equal(style.focused, true, `${label}: element did not receive focus.`);
    const outlined = style.outlineStyle !== 'none' && style.outlineWidth > 0;
    const shadowed = Boolean(style.boxShadow && style.boxShadow !== 'none');
    assert.ok(outlined || shadowed, `${label}: no visible outline or box-shadow: ${JSON.stringify(style)}`);
  };

  await goto(settingsUrl('general'), 'Keyboard search and navigation');
  const search = page.locator('#tt-search-input');
  await assertFocusIndicator(search, 'Settings search');
  await search.fill('متن');
  await search.press('ArrowDown');
  assert.equal(await search.getAttribute('aria-expanded'), 'true');
  assert.ok(await search.getAttribute('aria-activedescendant'), 'Arrow navigation did not select a search result.');
  await search.press('Escape');
  assert.equal(await search.getAttribute('aria-expanded'), 'false');
  assert.equal(await search.getAttribute('aria-activedescendant'), null);
  assert.equal(await page.locator('#tt-search-results').isHidden(), true);
  assert.equal(await search.evaluate((element) => document.activeElement === element), true, 'Escape must leave focus in the combobox.');

  const homeLink = page.locator('.tt-nav-link').filter({ hasText: 'صفحه اصلی' });
  await assertFocusIndicator(homeLink, 'Home navigation link');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    homeLink.press('Enter'),
  ]);
  await page.locator('.tt-overview').waitFor({ state: 'visible' });
  const heroCard = page.locator('.tt-card-link').filter({ hasText: 'هیرو' });
  await assertFocusIndicator(heroCard, 'Hero overview card');
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    heroCard.press('Enter'),
  ]);
  await page.locator('#tt-home-hero_kicker').waitFor({ state: 'visible' });
  assert.equal(await page.locator('.tt-subnav-link[aria-current="page"]').count(), 1);

  await goto(settingsUrl('home', 'faq'), 'Keyboard repeater');
  const rows = page.locator('[data-tt-repeater] tbody > tr');
  assert.equal(await rows.count(), 12, 'Keyboard repeater checks require the twelve-row fixture.');
  const remove = rows.nth(1).locator('[data-tt-repeater-remove]');
  await assertFocusIndicator(remove, 'FAQ remove button');
  await remove.press('Enter');
  assert.equal(await rows.count(), 11, 'Keyboard removal did not remove exactly one row.');
  const followingInput = rows.nth(1).locator('input').first();
  assert.equal(await followingInput.evaluate((element) => document.activeElement === element), true, 'Focus did not move to the following row after removal.');
  assert.equal(await followingInput.inputValue(), 'CI-CAP-Q2', 'Focus moved to the wrong row after removal.');

  const add = page.locator('[data-tt-repeater-add]');
  assert.equal(await add.isDisabled(), false, 'Add must be re-enabled below the cap.');
  await assertFocusIndicator(add, 'FAQ add button');
  await add.press('Enter');
  assert.equal(await rows.count(), 12, 'Keyboard add did not append a row.');
  const addedInput = rows.last().locator('input').first();
  assert.equal(await addedInput.evaluate((element) => document.activeElement === element), true, 'Focus did not move to the added row.');
  assert.equal(await addedInput.inputValue(), '', 'The keyboard-added row must be blank.');
  await goto(settingsUrl('home', 'faq'), 'Restore unsaved keyboard repeater fixture');
  assert.equal(await rows.count(), 12, 'DOM-only keyboard edits must not change the stored fixture.');

  await goto(settingsUrl('general'), 'Keyboard publish mode');
  const publishForm = page.locator('.tt-publish-mode form');
  const preview = publishForm.locator('input[name="mode"][value="preview"]');
  const publicMode = publishForm.locator('input[name="mode"][value="public"]');
  const confirm = publishForm.locator('input[name="confirm"]');
  await assertFocusIndicator(preview, 'Preview radio');
  await preview.press('Space');
  assert.equal(await preview.isChecked(), true, 'Space did not select Preview.');
  await publicMode.press('Space');
  assert.equal(await publicMode.isChecked(), true, 'Space did not select Public.');
  assert.equal(await preview.isChecked(), false, 'The radio group retained two selected modes.');
  await confirm.press('Space');
  assert.equal(await confirm.isChecked(), true, 'Space did not toggle the confirmation checkbox.');
  await assertFocusIndicator(publishForm.locator('button[type="submit"]'), 'Publish mode submit');

  passed('Admin keyboard navigation, visible focus and repeater focus restoration work without form submission');

  const responsiveMatrix = [];
  for (const width of [320, 390, 768, 1440]) {
    await page.setViewportSize({ width, height: width <= 390 ? 844 : 1000 });

    await goto(settingsUrl('general'), `Responsive General ${width}px`);
    const general = await inspectResponsiveView(page, width, 'General');
    const generalScreenshot = `admin-ui-general-${width}.png`;
    await page.screenshot({ path: path.join(resultsDir, generalScreenshot), fullPage: true, animations: 'disabled' });

    await goto(settingsUrl('home', 'faq'), `Responsive FAQ ${width}px`);
    assert.equal(await rows.count(), 12, 'Responsive checks require the twelve-row FAQ fixture.');
    const faq = await inspectResponsiveView(page, width, 'FAQ');
    const repeater = await page.locator('.tt-repeater-scroll').evaluate((element) => {
      const table = element.querySelector('table');
      return {
        clientWidth: element.clientWidth,
        scrollWidth: element.scrollWidth,
        tableWidth: table ? table.scrollWidth : 0,
        overflowX: getComputedStyle(element).overflowX,
      };
    });
    assert.equal(repeater.overflowX, 'auto', `${width}px FAQ: the table is not in a horizontal scroll container.`);
    assert.ok(repeater.scrollWidth >= repeater.tableWidth, `${width}px FAQ: incomplete table geometry: ${JSON.stringify(repeater)}`);
    if (width <= 390) {
      assert.ok(repeater.tableWidth > repeater.clientWidth, `${width}px FAQ: the fixture no longer exercises horizontal overflow.`);
    }
    const faqScreenshot = `admin-ui-faq-${width}.png`;
    await page.screenshot({ path: path.join(resultsDir, faqScreenshot), fullPage: true, animations: 'disabled' });

    responsiveMatrix.push({ width, general, faq, repeater, screenshots: [generalScreenshot, faqScreenshot] });
  }
  fs.writeFileSync(
    path.join(resultsDir, 'admin-responsive-matrix.json'),
    JSON.stringify({ generatedAt: new Date().toISOString(), viewports: [320, 390, 768, 1440], responsiveMatrix }, null, 2)
  );
  passed('Admin responsive matrix passes General, Publish Mode and contained FAQ controls at 320, 390, 768 and 1440px');

  const axeReport = await runAxeAcceptance({ page, settingsUrl, goto, resultsDir });
  const incompleteCount = axeReport.views.reduce((total, view) => total + view.incomplete.length, 0);
  console.log(`AXE REVIEW: ${incompleteCount} incomplete result(s) retained in admin-axe-report.json for manual review.`);
  passed('Axe WCAG A/AA rules report no violations in seven representative, scoped admin views');
}

/** Inspect actual geometry rather than inferring responsiveness from CSS. */
async function inspectResponsiveView(page, width, label) {
  const metrics = await page.evaluate(() => {
    const rect = (selector) => {
      const element = document.querySelector(selector);
      if (!element) {
        return null;
      }
      const box = element.getBoundingClientRect();
      return {
        left: box.left,
        right: box.right,
        top: box.top,
        bottom: box.bottom,
        width: box.width,
        height: box.height,
      };
    };
    const fieldLayout = (fieldSelector, labelSelector) => {
      const field = document.querySelector(fieldSelector);
      const fieldLabel = field ? field.querySelector(labelSelector) : null;
      const control = field ? field.querySelector(':scope > .tt-control') : null;
      if (!fieldLabel || !control) {
        return null;
      }
      const labelBox = fieldLabel.getBoundingClientRect();
      const controlBox = control.getBoundingClientRect();
      return {
        stacked: controlBox.top >= labelBox.bottom - 1,
        labelBottom: labelBox.bottom,
        controlTop: controlBox.top,
      };
    };
    const layout = document.querySelector('.tt-layout');
    const header = document.querySelector('.tt-header');
    return {
      documentWidth: document.documentElement.scrollWidth,
      bodyWidth: document.body.scrollWidth,
      headerWrap: header ? getComputedStyle(header).flexWrap : null,
      layoutDirection: layout ? getComputedStyle(layout).flexDirection : null,
      sidebar: rect('.tt-sidebar'),
      main: rect('.tt-main'),
      search: rect('#tt-search-input'),
      save: rect('.tt-form > .tt-footer input[type="submit"]'),
      publishForm: rect('.tt-publish-mode form'),
      standardField: fieldLayout('.tt-fields > .tt-field:not(.tt-field-repeater)', ':scope > .tt-label'),
      repeaterField: fieldLayout('.tt-fields > .tt-field-repeater', '.tt-repeater-head .tt-label'),
      publishField: fieldLayout('.tt-publish-mode > .tt-field', ':scope > .tt-label'),
    };
  });

  const narrow = width <= 600;
  const compact = width <= 960;
  assert.ok(metrics.documentWidth <= width + 1, `${label} ${width}px: document overflow ${JSON.stringify(metrics)}`);
  assert.ok(metrics.bodyWidth <= width + 1, `${label} ${width}px: body overflow ${JSON.stringify(metrics)}`);
  assert.equal(metrics.headerWrap, compact ? 'wrap' : 'nowrap', `${label} ${width}px: unexpected header wrapping.`);
  assert.equal(metrics.layoutDirection, compact ? 'column' : 'row-reverse', `${label} ${width}px: unexpected panel direction.`);

  for (const [name, box] of Object.entries({ search: metrics.search, save: metrics.save })) {
    assert.ok(box && box.width > 0 && box.height > 0, `${label} ${width}px: ${name} is not rendered.`);
    assert.ok(box.left >= -1 && box.right <= width + 1, `${label} ${width}px: ${name} is outside the viewport: ${JSON.stringify(box)}`);
  }
  if (compact) {
    assert.ok(metrics.sidebar && metrics.main, `${label} ${width}px: compact sidebar/main are missing.`);
    assert.ok(Math.abs(metrics.sidebar.width - metrics.main.width) <= 1, `${label} ${width}px: compact columns do not share the available width.`);
  } else {
    assert.ok(metrics.sidebar && Math.abs(metrics.sidebar.width - 250) <= 1, `${label} ${width}px: desktop sidebar width changed.`);
  }
  if (metrics.standardField) {
    assert.equal(metrics.standardField.stacked, narrow, `${label} ${width}px: wrong label/control stacking ${JSON.stringify(metrics.standardField)}`);
  }
  if (metrics.repeaterField) {
    assert.equal(metrics.repeaterField.stacked, true, `${label} ${width}px: repeater heading and control must remain stacked.`);
  }
  if (label === 'General') {
    assert.ok(metrics.publishField && metrics.publishForm, `${label} ${width}px: Publish Mode is missing.`);
    assert.equal(metrics.publishField.stacked, narrow, `${label} ${width}px: wrong Publish Mode stacking.`);
  }

  return metrics;
}

/** Run real axe-core scans and preserve both violations and manual-review items. */
async function runAxeAcceptance({ page, settingsUrl, goto, resultsDir }) {
  const tags = ['wcag2a', 'wcag2aa', 'wcag21a', 'wcag21aa'];
  const destinations = [
    { name: 'general-desktop', section: 'general', group: null, width: 1440 },
    { name: 'faq-desktop', section: 'home', group: 'faq', width: 1440 },
    { name: 'contact-desktop', section: 'contact', group: null, width: 1440 },
    { name: 'business-desktop', section: 'business', group: null, width: 1440 },
    { name: 'login-desktop', section: 'login', group: null, width: 1440 },
    { name: 'general-mobile', section: 'general', group: null, width: 390 },
    { name: 'faq-mobile', section: 'home', group: 'faq', width: 390 },
  ];
  const report = {
    generatedAt: new Date().toISOString(),
    scope: '.tt-wrap',
    tags,
    note: 'Automated axe results are not a WCAG conformance certification. Incomplete results require manual review.',
    views: [],
  };
  const reportPath = path.join(resultsDir, 'admin-axe-report.json');

  for (const destination of destinations) {
    await page.setViewportSize({ width: destination.width, height: 1000 });
    await goto(settingsUrl(destination.section, destination.group), `Axe ${destination.name}`);
    const results = await new AxeBuilder({ page })
      .include('.tt-wrap')
      .withTags(tags)
      .analyze();
    const view = {
      name: destination.name,
      url: page.url(),
      viewport: { width: destination.width, height: 1000 },
      engine: results.testEngine,
      environment: results.testEnvironment,
      violations: results.violations,
      incomplete: results.incomplete,
      passes: results.passes.map((rule) => ({ id: rule.id, impact: rule.impact, nodes: rule.nodes.length })),
      inapplicable: results.inapplicable.map((rule) => rule.id),
    };
    report.views.push(view);
    fs.writeFileSync(reportPath, JSON.stringify(report, null, 2));
    console.log(`AXE ${destination.name}: violations=${view.violations.length}, incomplete=${view.incomplete.length}, passed-rules=${view.passes.length}`);
  }

  const failures = report.views.flatMap((view) => view.violations.map((rule) => ({
    view: view.name,
    id: rule.id,
    impact: rule.impact,
    help: rule.help,
    nodes: rule.nodes.map((node) => ({ target: node.target, failureSummary: node.failureSummary })),
  })));
  if (failures.length) {
    console.error(`AXE VIOLATIONS:\n${JSON.stringify(failures, null, 2)}`);
  }
  const manualReview = report.views.flatMap((view) => view.incomplete.map((rule) => ({
    view: view.name,
    id: rule.id,
    impact: rule.impact,
    nodes: rule.nodes.map((node) => ({ target: node.target, failureSummary: node.failureSummary })),
  })));
  console.log(`AXE INCOMPLETE (manual review, not violations):\n${JSON.stringify(manualReview, null, 2)}`);
  assert.equal(failures.length, 0, 'Confirmed axe violations require remediation; see admin-axe-report.json.');
  return report;
}
