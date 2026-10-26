import assert from 'node:assert/strict';

/**
 * Keyboard and focus acceptance for the existing administrator page.
 *
 * The caller has already authenticated and prepared the twelve-row FAQ fixture.
 * These checks navigate and mutate only the DOM; no form is submitted here.
 */
export async function runAdminSettingsAccessibility({ page, settingsUrl, goto, passed }) {
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
}
