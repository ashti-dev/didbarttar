import fs from 'node:fs';
import path from 'node:path';
import assert from 'node:assert/strict';
import { chromium } from 'playwright';

const fixtureFile = process.env.FIXTURE_FILE || '/tmp/torantejarat-fixture.json';
const resultsDir = process.env.TEST_RESULTS_DIR || '/tmp/torantejarat-test-results';
const customerPassword = process.env.CI_CUSTOMER_PASSWORD;
const fixture = JSON.parse(fs.readFileSync(fixtureFile, 'utf8'));
const baseHost = new URL(fixture.baseUrl).hostname;
assert.ok(['127.0.0.1', 'localhost'].includes(baseHost), 'Browser acceptance only runs against localhost.');
assert.ok(customerPassword, 'CI_CUSTOMER_PASSWORD is required.');
fs.mkdirSync(resultsDir, { recursive: true });

const results = {
  status: 'running',
  checks: [],
  pageErrors: [],
  consoleErrors: [],
  requestFailures: [],
  serverErrors: [],
};
const writeResults = () => fs.writeFileSync(path.join(resultsDir, 'browser-results.json'), JSON.stringify(results, null, 2));
const passed = (name) => {
  results.checks.push(name);
  console.log(`PASS: ${name}`);
  writeResults();
};

const browser = await chromium.launch({ headless: true, args: ['--no-sandbox'] });
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
  if (reason !== 'net::ERR_ABORTED') results.requestFailures.push(`${request.method()} ${request.url()} — ${reason}`);
});
page.on('response', (response) => {
  if (response.url().startsWith(fixture.baseUrl) && response.status() >= 500) {
    results.serverErrors.push(`${response.status()} ${response.url()}`);
  }
});

async function healthyPage() {
  const body = await page.locator('body').innerText();
  for (const marker of ['Fatal error', 'Parse error', 'Uncaught Error', 'critical error on this website']) {
    assert.ok(!body.includes(marker), `PHP/runtime marker found: ${marker}`);
  }
}

async function goto(url, label) {
  const response = await page.goto(url, { waitUntil: 'domcontentloaded' });
  assert.ok(response, `No response for ${label}`);
  assert.ok(response.status() < 400, `${label}: HTTP ${response.status()}`);
  await healthyPage();
}

async function visible(selector, label) {
  const locator = page.locator(selector);
  await locator.waitFor({ state: 'visible' });
  assert.ok(await locator.isVisible(), `${label} is not visible`);
  return locator;
}

try {
  await goto(fixture.shopUrl, 'Shop');
  await page.getByText(fixture.simpleName, { exact: true }).first().waitFor();
  await page.getByText(fixture.variableName, { exact: true }).first().waitFor();
  passed('Shop renders both real WooCommerce fixture products');

  await goto(fixture.simpleUrl, 'Simple product');
  await visible('form.cart', 'Simple product form');
  const simpleAjax = page.waitForResponse((response) => response.url().includes('wc-ajax=add_to_cart'));
  await page.locator('.single_add_to_cart_button').click();
  const simpleResponse = await simpleAjax;
  assert.equal(simpleResponse.status(), 200);
  const simpleJson = await simpleResponse.json();
  assert.ok(!simpleJson.error, 'Simple AJAX add-to-cart returned an error');
  passed('Simple product is added through the theme AJAX flow');

  await goto(fixture.variableUrl, 'Variable product');
  await visible('form.variations_form', 'Variation form');
  await page.locator('select[name="attribute_cable_length"]').selectOption({ label: '10 m' });
  await page.waitForFunction(() => {
    const input = document.querySelector('input[name="variation_id"]');
    return input && Number(input.value) > 0;
  });
  await Promise.all([
    page.waitForNavigation({ waitUntil: 'domcontentloaded' }),
    page.locator('.single_add_to_cart_button').click(),
  ]);
  await healthyPage();
  passed('Variable product keeps native WooCommerce submission and resolves its variation');

  await goto(fixture.cartUrl, 'Cart');
  const cartText = await page.locator('body').innerText();
  assert.ok(cartText.includes(fixture.simpleName));
  assert.ok(cartText.includes(fixture.variableName));
  assert.ok(cartText.includes('10 m'));
  passed('Cart contains the simple item and the selected variation metadata');

  await goto(fixture.checkoutUrl, 'Checkout');
  await visible('form.checkout', 'Classic checkout form');
  await page.locator('#billing_first_name').fill('خریدار');
  await page.locator('#billing_last_name').fill('آزمایشی');
  await page.locator('#billing_state').selectOption('THR');
  await page.locator('#billing_city').fill('تهران');
  await page.locator('#billing_address_1').fill('خیابان آزمایشی، پلاک ۱۰');
  await page.locator('#billing_postcode').fill('1234567890');
  await page.locator('#billing_phone').fill('09123456789');
  await page.locator('#billing_email').fill('ci-buyer@example.test');
  await page.locator('#billing_email').press('Tab');
  await page.waitForLoadState('networkidle');
  const cod = page.locator('#payment_method_cod');
  await cod.waitFor({ state: 'attached' });
  if (!(await cod.isChecked())) {
    await page.locator('label[for="payment_method_cod"]').click();
  }
  assert.ok(await cod.isChecked(), 'COD payment method is not selected');
  await page.screenshot({ path: path.join(resultsDir, 'checkout-desktop.png'), fullPage: true, animations: 'disabled' });
  await Promise.all([
    page.waitForURL(/order-received/, { timeout: 45_000 }),
    page.locator('#place_order').click(),
  ]);
  await healthyPage();
  results.orderReceivedUrl = page.url();
  passed('Real guest checkout places a COD order and reaches order-received');

  await visible('.torantejarat-thx__hero', 'Redesigned thank-you hero');
  await visible('.torantejarat-thx__steps', 'Order progress');
  await visible('.torantejarat-thx__totals-card', 'Financial summary');
  const thankyouText = await page.locator('.torantejarat-thx').innerText();
  assert.ok(thankyouText.includes(fixture.simpleName));
  assert.ok(thankyouText.includes(fixture.variableName));
  assert.ok(thankyouText.includes('cable_length'));
  assert.ok(thankyouText.includes('10 m'));
  assert.ok(thankyouText.includes('ci-buyer@example.test'));
  assert.ok(thankyouText.includes('این سفارش فقط در محیط آزمایشی GitHub Actions ثبت شده است.'));
  assert.ok((await page.locator('.torantejarat-thx__row').count()) >= 3);
  const badge = await page.locator('.torantejarat-thx__badge').innerText();
  assert.ok(badge.includes('پرداخت هنگام تحویل'), `Unexpected COD badge: ${badge}`);
  assert.ok(!badge.includes('پرداخت شد —'), 'COD must not be labelled as already paid');
  assert.equal(await page.locator('.torantejarat-print-invoice').count(), 1);
  assert.equal(await page.locator('.torantejarat-print-invoice').isHidden(), true);
  await page.screenshot({ path: path.join(resultsDir, 'thankyou-desktop.png'), fullPage: true, animations: 'disabled' });
  passed('Thank-you renders items, variation meta, totals, gateway instructions and honest COD status');

  await page.setViewportSize({ width: 390, height: 844 });
  const dimensions = await page.evaluate(() => ({
    viewport: window.innerWidth,
    content: Math.max(document.documentElement.scrollWidth, document.body.scrollWidth),
  }));
  assert.ok(dimensions.content <= dimensions.viewport + 1, `Horizontal overflow at 390px: ${JSON.stringify(dimensions)}`);
  await page.screenshot({ path: path.join(resultsDir, 'thankyou-mobile-390.png'), fullPage: true, animations: 'disabled' });
  passed('Thank-you remains within a 390px mobile viewport');

  await page.emulateMedia({ media: 'print' });
  assert.equal(await page.locator('.torantejarat-thx').isHidden(), true);
  assert.equal(await page.locator('.torantejarat-print-invoice').isVisible(), true);
  await page.pdf({ path: path.join(resultsDir, 'thankyou-print.pdf'), format: 'A4', printBackground: true });
  assert.ok(fs.statSync(path.join(resultsDir, 'thankyou-print.pdf')).size > 1000);
  await page.emulateMedia({ media: 'screen' });
  passed('Print media hides the screen receipt and produces the formal invoice PDF');

  await page.setViewportSize({ width: 1440, height: 1000 });
  const loginUrl = new URL('wp-login.php', fixture.baseUrl);
  loginUrl.searchParams.set('redirect_to', fixture.simpleUrl);
  await goto(loginUrl.href, 'Customer login');
  await page.locator('#user_login').fill(fixture.customerLogin);
  await page.locator('#user_pass').fill(customerPassword);
  await Promise.all([
    page.waitForURL((url) => !url.pathname.includes('wp-login.php')),
    page.locator('#wp-submit').click(),
  ]);
  await goto(fixture.simpleUrl, 'Logged-in simple product');
  const wishlistButton = await visible('[data-wishlist-toggle]', 'PDP wishlist button');
  const addWishlistResponse = page.waitForResponse((response) => response.url().includes('/wp-json/torantejarat/v1/wishlist/toggle'));
  await wishlistButton.click();
  const added = await addWishlistResponse;
  assert.equal(added.status(), 200);
  assert.equal((await added.json()).wishlisted, true);
  assert.equal(await wishlistButton.getAttribute('aria-pressed'), 'true');
  passed('Logged-in PDP wishlist addition succeeds with real cookie/nonce REST authentication');

  const accountBase = fixture.accountUrl.endsWith('/') ? fixture.accountUrl : `${fixture.accountUrl}/`;
  await goto(new URL('wishlist/', accountBase).href, 'Account wishlist');
  await page.getByText(fixture.simpleName, { exact: true }).first().waitFor();
  await page.locator('[data-remove-wish]').click();
  await visible('#acp-confirm', 'Wishlist confirmation');
  await page.locator('[data-confirm-no]').click();
  assert.equal(await page.locator('#acp-confirm').isHidden(), true);
  assert.equal(await page.locator('[data-remove-wish]').count(), 1);
  passed('Wishlist cancellation preserves the item and closes the confirmation');

  await page.locator('[data-remove-wish]').click();
  await visible('#acp-confirm', 'Wishlist confirmation retry');
  const removeWishlistResponse = page.waitForResponse((response) => response.url().includes('/wp-json/torantejarat/v1/wishlist/toggle'));
  await page.locator('#acp-confirm-yes').click();
  const removed = await removeWishlistResponse;
  assert.equal(removed.status(), 200);
  assert.equal((await removed.json()).wishlisted, false);
  await visible('#wish-empty', 'Wishlist empty state');
  passed('Wishlist confirmation removes the real database row and reveals the empty state');

  assert.deepEqual(results.pageErrors, [], `Browser page errors: ${results.pageErrors.join('\n')}`);
  assert.deepEqual(results.consoleErrors, [], `Browser console errors: ${results.consoleErrors.join('\n')}`);
  assert.deepEqual(results.requestFailures, [], `Failed browser requests: ${results.requestFailures.join('\n')}`);
  assert.deepEqual(results.serverErrors, [], `Local HTTP 5xx responses: ${results.serverErrors.join('\n')}`);
  passed('No browser exceptions, console errors, failed requests or local HTTP 5xx responses');
  results.status = 'passed';
  writeResults();
} catch (error) {
  results.status = 'failed';
  results.error = error.stack || String(error);
  writeResults();
  await page.screenshot({ path: path.join(resultsDir, 'failure.png'), fullPage: true, animations: 'disabled' }).catch(() => {});
  throw error;
} finally {
  await browser.close();
}
