import fs from 'node:fs';
import path from 'node:path';
import assert from 'node:assert/strict';
import { execFileSync } from 'node:child_process';

/**
 * Real administrator/browser checks for the theme settings panel.
 *
 * All writes target the disposable WordPress fixture through its own admin
 * forms or WP-CLI. This module never runs against a public host and must only
 * be called after browser-smoke.mjs has established the localhost boundary.
 */
export async function runAdminSettingsSmoke({ browser, fixture, resultsDir, results, passed }) {
  const adminPassword = process.env.CI_ADMIN_PASSWORD;
  const wpRoot = process.env.WP_ROOT;
  assert.ok(adminPassword, 'CI_ADMIN_PASSWORD is required for admin settings acceptance.');
  assert.ok(wpRoot && fs.existsSync(path.join(wpRoot, 'wp-load.php')), 'WP_ROOT is required for admin settings acceptance.');
  assert.ok(['127.0.0.1', 'localhost'].includes(new URL(fixture.baseUrl).hostname), 'Admin settings acceptance only runs against localhost.');

  const wp = (args) => execFileSync('wp', [...args, `--path=${wpRoot}`], {
    encoding: 'utf8',
    stdio: ['ignore', 'pipe', 'pipe'],
  }).trim();
  const getScalar = (name) => {
    try {
      return wp(['option', 'get', name]);
    } catch {
      return null;
    }
  };
  const setScalar = (name, value) => wp(['option', 'update', name, String(value)]);
  const getThemeOption = (tab) => JSON.parse(wp(['option', 'get', `torantejarat_${tab}`, '--format=json']));
  const setThemeOption = (tab, value) => wp(['option', 'update', `torantejarat_${tab}`, JSON.stringify(value), '--format=json']);
  const nativeBefore = {
    admin_email: getScalar('admin_email'),
    woocommerce_email_from_address: getScalar('woocommerce_email_from_address'),
    woocommerce_coming_soon: getScalar('woocommerce_coming_soon'),
    blog_public: getScalar('blog_public'),
  };

  const context = await browser.newContext({
    viewport: { width: 1440, height: 1000 },
    locale: 'fa-IR',
    ignoreHTTPSErrors: true,
  });
  const page = await context.newPage();
  page.setDefaultTimeout(30_000);
  page.on('pageerror', (error) => results.pageErrors.push(error.message));
  page.on('console', (message) => {
    if (message.type() === 'error') results.consoleErrors.push(message.text());
  });
  page.on('requestfailed', (request) => {
    const reason = request.failure()?.errorText || 'unknown';
    if (request.url().startsWith(fixture.baseUrl) && reason !== 'net::ERR_ABORTED') {
      results.requestFailures.push(`${request.method()} ${request.url()} — ${reason}`);
    }
  });
  page.on('response', (response) => {
    if (response.url().startsWith(fixture.baseUrl) && response.status() >= 500) {
      results.serverErrors.push(`${response.status()} ${response.url()}`);
    }
  });

  const settingsUrl = (section, sub = '') => {
    const url = new URL('wp-admin/admin.php', fixture.baseUrl);
    url.searchParams.set('page', 'torantejarat-settings');
    url.searchParams.set('section', section);
    if (sub) url.searchParams.set('sub', sub);
    return url.href;
  };
  const healthyPage = async () => {
    const body = await page.locator('body').innerText();
    for (const marker of ['Fatal error', 'Parse error', 'Uncaught Error', 'critical error on this website']) {
      assert.ok(!body.includes(marker), `Admin PHP/runtime marker found: ${marker}`);
    }
  };
  const goto = async (url, label) => {
    const response = await page.goto(url, { waitUntil: 'domcontentloaded' });
    assert.ok(response, `No response for ${label}`);
    assert.ok(response.status() < 400, `${label}: HTTP ${response.status()}`);
    await healthyPage();
  };
  const submitSettings = async () => {
    const form = page.locator('form[action="options.php"]');
    await form.locator('input[type="submit"]').waitFor({ state: 'visible' });
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      form.locator('input[type="submit"]').click(),
    ]);
    await healthyPage();
    // Core's wp_admin_canonical_url() removes the single-use
    // settings-updated query argument with history.replaceState(). The raw
    // option comparisons in saveCase/FAQ checks prove the actual write.
  };
  const saveCase = async ({ tab, sub = '', field, value }) => {
    const before = getThemeOption(tab);
    await goto(settingsUrl(tab, sub), `Settings ${tab}/${sub || 'main'}`);
    const input = page.locator(`#tt-${tab}-${field}`);
    await input.waitFor({ state: 'visible' });
    await input.fill(value);
    await submitSettings();
    const after = getThemeOption(tab);
    assert.deepEqual(after[field], value, `${tab}/${field} was not saved through options.php`);
    for (const [key, previousValue] of Object.entries(before)) {
      if (key === field) continue;
      assert.deepEqual(after[key], previousValue, `${tab}/${key} changed unexpectedly while saving ${field}`);
    }
  };
  const assertMobilePanel = async (label) => {
    const metrics = await page.evaluate(() => {
      const root = document.documentElement;
      const header = document.querySelector('.tt-header');
      const layout = document.querySelector('.tt-layout');
      const sidebar = document.querySelector('.tt-sidebar');
      const main = document.querySelector('.tt-main');
      const field = document.querySelector('.tt-fields .tt-field:not(.tt-field-repeater)');
      const label = field?.querySelector('.tt-label');
      const control = field?.querySelector('.tt-control');
      const labelRect = label?.getBoundingClientRect();
      const controlRect = control?.getBoundingClientRect();
      return {
        viewport: root.clientWidth,
        documentWidth: root.scrollWidth,
        headerWrap: header ? getComputedStyle(header).flexWrap : null,
        layoutDirection: layout ? getComputedStyle(layout).flexDirection : null,
        sidebarWidth: sidebar?.getBoundingClientRect().width || 0,
        mainWidth: main?.getBoundingClientRect().width || 0,
        stacked: labelRect && controlRect ? controlRect.top >= labelRect.bottom - 1 : null,
      };
    });
    const detail = JSON.stringify(metrics);
    assert.ok(metrics.documentWidth <= metrics.viewport + 1, `${label}: document overflows horizontally: ${detail}`);
    assert.equal(metrics.headerWrap, 'wrap', `${label}: header did not wrap: ${detail}`);
    assert.equal(metrics.layoutDirection, 'column', `${label}: panel did not stack: ${detail}`);
    assert.ok(Math.abs(metrics.sidebarWidth - metrics.mainWidth) <= 2, `${label}: sidebar/main widths diverge: ${detail}`);
    assert.equal(metrics.stacked, true, `${label}: field label/control did not stack: ${detail}`);
  };

  try {
    // Deterministic fixture values cover every tab without using production data.
    setThemeOption('general', {
      topbar_text: 'CI-SEED-TOPBAR',
      topbar_link: 'https://torantejarat.test/contact',
      preview_note: 'no',
    });
    setThemeOption('home', {
      hero_kicker: 'CI-SEED-HERO',
      hero_foot_2: 'CI-SEED-HERO-FOOT',
      benefit_1: 'CI-SEED-BENEFIT',
      cat_text: 'CI-SEED-CATEGORY',
      field_text: 'CI-SEED-FIELD',
      blog_text: 'CI-SEED-BLOG',
      faq_eyebrow: 'CI-SEED-FAQ-EYEBROW',
      faq: [
        { q: 'CI-SEED-Q1', a: 'CI-SEED-A1' },
        { q: 'CI-SEED-Q2', a: 'CI-SEED-A2' },
      ],
    });
    setThemeOption('contact', {
      phone: '+989121234567',
      phone_display: 'CI-SEED-PHONE',
      email: 'seed@torantejarat.test',
      address: 'CI-SEED-ADDRESS',
    });
    setThemeOption('social', {
      instagram: 'https://instagram.com/torantejarat_ci',
      telegram: 'https://t.me/torantejarat_ci',
    });
    setThemeOption('footer', { copyright_text: 'CI-SEED-COPYRIGHT', bar_text: 'CI-SEED-BAR' });
    setThemeOption('login', { login_title: 'CI-SEED-LOGIN', login_hide_register: 0, login_hide_lostpassword: 0 });
    setThemeOption('business', { business_name: 'CI-SEED-BUSINESS', business_type: 'Store', city: 'CI-SEED-CITY', price_range: '' });

    // The fixture router maps directory-only /wp-admin/ to the front page;
    // request the real PHP file so core can authenticate and redirect normally.
    await goto(new URL('wp-admin/index.php', fixture.baseUrl).href, 'Admin dashboard');
    if (page.url().includes('wp-login.php')) {
      await page.locator('#user_login').fill('ci_admin');
      await page.locator('#user_pass').fill(adminPassword);
      await Promise.all([
        page.waitForURL((url) => !url.pathname.includes('wp-login.php')),
        page.locator('#wp-submit').click(),
      ]);
    }
    await page.locator('#wpadminbar').waitFor({ state: 'visible' });
    await goto(settingsUrl('general'), 'General settings');
    assert.equal(await page.locator('.tt-nav .tt-nav-link').count(), 7, 'The settings panel must expose all seven sections.');
    assert.equal(await page.evaluate(() => window.ttPanelSearch?.index?.length), 61, 'The search index must contain all 61 fields.');
    const currentSection = page.locator('.tt-nav-link[aria-current="page"]');
    assert.equal(await currentSection.count(), 1, 'The flat section must expose one current page to assistive technology.');
    assert.equal(await currentSection.getAttribute('href'), settingsUrl('general'));
    assert.equal(
      (await page.locator('.tt-saved-hint').innerText()).trim(),
      'ذخیره این فرم فقط فیلدهای همین صفحه را تغییر می‌دهد.',
      'The save hint must describe scope rather than claiming every blank field has a text default.',
    );
    await page.locator('form[action="options.php"]').waitFor({ state: 'visible' });
    await page.screenshot({ path: path.join(resultsDir, 'admin-settings-desktop.png'), fullPage: true, animations: 'disabled' });
    passed('Admin login exposes seven settings sections and the 61-field schema index');

    for (const testCase of [
      { tab: 'general', field: 'topbar_text', value: 'CI-GENERAL-UPDATED' },
      { tab: 'contact', field: 'phone_display', value: 'CI-CONTACT-UPDATED' },
      { tab: 'social', field: 'instagram', value: 'https://instagram.com/ci_updated' },
      { tab: 'footer', field: 'bar_text', value: 'CI-FOOTER-UPDATED' },
      { tab: 'login', field: 'login_title', value: 'CI-LOGIN-UPDATED' },
      { tab: 'business', field: 'city', value: 'CI-BUSINESS-UPDATED' },
    ]) {
      await saveCase(testCase);
    }
    passed('All six flat settings tabs save through options.php and preserve previous values');

    for (const testCase of [
      { tab: 'home', sub: 'hero', field: 'hero_kicker', value: 'CI-HERO-UPDATED' },
      { tab: 'home', sub: 'trust', field: 'benefit_1', value: 'CI-TRUST-UPDATED' },
      { tab: 'home', sub: 'category', field: 'cat_text', value: 'CI-CATEGORY-UPDATED' },
      { tab: 'home', sub: 'field', field: 'field_text', value: 'CI-FIELD-UPDATED' },
      { tab: 'home', sub: 'blog', field: 'blog_text', value: 'CI-BLOG-UPDATED' },
      { tab: 'home', sub: 'faq', field: 'faq_eyebrow', value: 'CI-FAQ-UPDATED' },
    ]) {
      await saveCase(testCase);
    }
    passed('All six home subpages save only their scope and preserve out-of-scope options');

    await goto(settingsUrl('home', 'faq'), 'FAQ settings');
    const rows = page.locator('[data-tt-repeater] tbody > tr');
    assert.equal(await rows.count(), 3, 'Two saved FAQs plus one trailing blank row were expected.');
    await page.locator('[data-tt-repeater-add]').click();
    assert.equal(await rows.count(), 4, 'FAQ add did not append a row.');
    await rows.last().locator('input').fill('CI-FAQ-ADD-Q');
    await rows.last().locator('textarea').fill('CI-FAQ-ADD-A');
    await submitSettings();
    let home = getThemeOption('home');
    assert.equal(home.faq.length, 3, 'The added FAQ was not stored.');
    assert.equal(home.faq[2].q, 'CI-FAQ-ADD-Q');
    await rows.nth(1).locator('[data-tt-repeater-remove]').click();
    assert.equal(await rows.count(), 3, 'FAQ remove did not remove the selected row.');
    await submitSettings();
    home = getThemeOption('home');
    assert.deepEqual(home.faq.map((row) => row.q), ['CI-SEED-Q1', 'CI-FAQ-ADD-Q'], 'FAQ removal stored the wrong rows.');
    passed('FAQ browser add, save, remove and renumbering update the real option correctly');

    home.faq = Array.from({ length: 12 }, (_, index) => ({ q: `CI-CAP-Q${index}`, a: `CI-CAP-A${index}` }));
    setThemeOption('home', home);
    await goto(settingsUrl('home', 'faq'), 'FAQ cap settings');
    assert.equal(await rows.count(), 12, 'The at-cap form must not render a thirteenth editable row.');
    assert.equal(await page.locator('[data-tt-repeater-add]').isDisabled(), true, 'FAQ add must be disabled at the cap.');
    assert.ok((await page.locator('[data-tt-repeater-note]').getAttribute('class')).includes('is-limit'));
    passed('FAQ at-cap UI has twelve rows, no blank thirteenth row and a disabled add control');

    await goto(settingsUrl('general'), 'Settings search');
    const search = page.locator('#tt-search-input');
    await search.fill('متن');
    const options = page.locator('#tt-search-results .tt-search-item');
    await options.first().waitFor({ state: 'visible' });
    assert.ok(await options.count() > 1, 'A broad query is required to verify option selection state.');
    await search.press('ArrowDown');
    assert.equal(await options.nth(0).getAttribute('aria-selected'), 'true');
    assert.equal(await search.getAttribute('aria-activedescendant'), await options.nth(0).getAttribute('id'));
    await search.press('ArrowDown');
    assert.equal(await options.nth(0).getAttribute('aria-selected'), 'false');
    assert.equal(await options.nth(1).getAttribute('aria-selected'), 'true');
    assert.equal(await search.getAttribute('aria-activedescendant'), await options.nth(1).getAttribute('id'));

    await search.fill('متن توضیح');
    const result = page.locator('#tt-search-results .tt-search-item');
    await result.waitFor({ state: 'visible' });
    assert.equal(await result.count(), 1, 'Expected one hero_lead search result.');
    assert.ok((await result.innerText()).includes('صفحه اصلی'));
    await search.press('ArrowDown');
    assert.equal(await result.getAttribute('aria-selected'), 'true');
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      search.press('Enter'),
    ]);
    await healthyPage();
    await page.locator('#ttf-home-hero_lead').waitFor({ state: 'visible' });
    assert.ok(page.url().includes('section=home&sub=hero'));
    assert.ok(page.url().endsWith('#ttf-home-hero_lead'));
    assert.equal(await page.locator('.tt-nav-item.is-active > .tt-nav-link').getAttribute('aria-current'), null);
    assert.equal(await page.locator('.tt-sidebar [aria-current="page"]').count(), 1, 'Only the actual subpage may be current.');
    const currentSubpage = page.locator('.tt-subnav-link[aria-current="page"]');
    assert.equal(await currentSubpage.count(), 1, 'The home subsection must expose one current page.');
    assert.ok((await currentSubpage.innerText()).includes('هیرو'));
    passed('Settings combobox navigates to the correct home/hero field and fragment');

    setScalar('admin_email', 'admin@example.com');
    setScalar('woocommerce_email_from_address', 'admin@example.com');
    await goto(settingsUrl('general'), 'Readiness settings');
    const adminLink = page.locator('.tt-ready-list a').filter({ hasText: 'admin_email' });
    const expectedAdmin = new URL('wp-admin/options-general.php', fixture.baseUrl).href;
    assert.equal(await adminLink.getAttribute('href'), expectedAdmin);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      adminLink.click(),
    ]);
    await healthyPage();
    await page.locator('#new_admin_email').waitFor({ state: 'visible' });
    await goto(settingsUrl('general'), 'Readiness settings for WooCommerce');
    const wooLink = page.locator('.tt-ready-list a').filter({ hasText: 'فرستنده' });
    const expectedWoo = new URL('wp-admin/admin.php?page=wc-settings&tab=email', fixture.baseUrl).href;
    assert.equal(await wooLink.getAttribute('href'), expectedWoo);
    await Promise.all([
      page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
      wooLink.click(),
    ]);
    await healthyPage();
    await page.locator('input[name="woocommerce_email_from_address"]').waitFor({ state: 'visible' });
    passed('Readiness email links open the real WordPress and WooCommerce corrective fields');

    setScalar('admin_email', 'ci-admin@torantejarat.test');
    setScalar('woocommerce_email_from_address', 'ci-sender@torantejarat.test');
    await goto(settingsUrl('general'), 'Publish mode settings');
    const publishForm = page.locator('.tt-publish-mode form');
    await publishForm.locator('input[name="mode"][value="preview"]').check();
    await Promise.all([
      page.waitForURL(/tt_publish=preview/),
      publishForm.locator('button[type="submit"]').click(),
    ]);
    await healthyPage();
    assert.equal(getScalar('woocommerce_coming_soon'), 'yes');
    assert.equal(getScalar('blog_public'), '0');
    await publishForm.locator('input[name="mode"][value="public"]').check();
    await publishForm.locator('input[name="confirm"]').check();
    await Promise.all([
      page.waitForURL(/tt_publish=public/),
      publishForm.locator('button[type="submit"]').click(),
    ]);
    await healthyPage();
    assert.equal(getScalar('woocommerce_coming_soon'), 'no');
    assert.equal(getScalar('blog_public'), '1');
    passed('Publish mode writes preview/public native options with nonce and explicit confirmation');

    await page.setViewportSize({ width: 390, height: 844 });
    await goto(settingsUrl('general'), 'General settings at 390px');
    await page.locator('#tt-search-input').waitFor({ state: 'visible' });
    await page.locator('form[action="options.php"] input[type="submit"]').waitFor({ state: 'visible' });
    await assertMobilePanel('General settings at 390px');
    await page.screenshot({ path: path.join(resultsDir, 'admin-settings-mobile-390.png'), fullPage: true, animations: 'disabled' });

    await goto(settingsUrl('home', 'faq'), 'FAQ settings at 390px');
    assert.equal(await rows.count(), 12, 'The mobile FAQ fixture must retain all twelve rows.');
    await assertMobilePanel('FAQ settings at 390px');
    const repeaterOverflow = await page.locator('.tt-repeater-scroll').evaluate((container) => {
      const table = container.querySelector('table');
      return {
        overflowX: getComputedStyle(container).overflowX,
        clientWidth: container.clientWidth,
        scrollWidth: container.scrollWidth,
        tableWidth: table?.getBoundingClientRect().width || 0,
      };
    });
    assert.equal(repeaterOverflow.overflowX, 'auto');
    assert.ok(repeaterOverflow.tableWidth > repeaterOverflow.clientWidth, `FAQ table should overflow only its own container: ${JSON.stringify(repeaterOverflow)}`);
    assert.ok(repeaterOverflow.scrollWidth >= repeaterOverflow.tableWidth, `FAQ table is not reachable by horizontal scrolling: ${JSON.stringify(repeaterOverflow)}`);
    await page.screenshot({ path: path.join(resultsDir, 'admin-settings-mobile-faq-390.png'), fullPage: true, animations: 'disabled' });
    passed('Admin settings responsive layout, accessible controls and contained FAQ scrolling work at 390px');
  } catch (error) {
    await page.screenshot({ path: path.join(resultsDir, 'admin-settings-failure.png'), fullPage: true, animations: 'disabled' }).catch(() => {});
    throw error;
  } finally {
    // Restore native options even after a failure; theme sentinels remain only
    // in this disposable fixture for diagnostics and are never production data.
    for (const [name, value] of Object.entries(nativeBefore)) {
      try {
        if (value === null) {
          wp(['option', 'delete', name]);
        } else {
          setScalar(name, value);
        }
      } catch (restoreError) {
        console.warn(`Could not restore ${name}: ${restoreError.message}`);
      }
    }
    await context.close();
  }
}
