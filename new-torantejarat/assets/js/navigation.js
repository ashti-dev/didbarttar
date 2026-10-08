/* Presentation-only extraction of the mobile disclosure in source app.js. */
(() => {
  'use strict';
  const button = document.querySelector('.torantejarat-menu-toggle');
  const nav = document.querySelector('#torantejarat-main-nav');
  const header = document.querySelector('.torantejarat-site-header');
  if (!button || !nav || !header) return;

  const setOpen = (open, returnFocus = false) => {
    nav.classList.toggle('torantejarat-is-open', open);
    button.setAttribute('aria-expanded', String(open));
    button.setAttribute('aria-label', open ? button.dataset.closeLabel : button.dataset.openLabel);
    if (returnFocus) button.focus();
  };
  const close = (returnFocus = false) => setOpen(false, returnFocus);
  setOpen(nav.contains(document.activeElement));
  document.body.classList.add('torantejarat-js');
  button.hidden = false;
  button.addEventListener('click', () => {
    setOpen(!nav.classList.contains('torantejarat-is-open'));
  });
  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && nav.classList.contains('torantejarat-is-open')) close(true);
  });
  document.addEventListener('click', (event) => {
    if (!header.contains(event.target)) close(nav.contains(document.activeElement));
  });
  header.addEventListener('focusout', () => {
    setTimeout(() => { if (!header.contains(document.activeElement)) close(); }, 0);
  });
  nav.addEventListener('click', (event) => { if (event.target.closest('a')) close(nav.contains(document.activeElement)); });
  matchMedia('(min-width:901px)').addEventListener('change', (event) => {
    if (event.matches) close();
    else if (nav.contains(document.activeElement)) close(true);
  });

  /* Empty search guard: inline hint + focus instead of a meaningless submit. */
  document.querySelectorAll('form.torantejarat-header-search').forEach((form) => {
    const input = form.querySelector('input[name="s"]');
    if (!input) return;
    let hint = null;
    form.addEventListener('submit', (event) => {
      if (input.value.trim() !== '') {
        if (hint) hint.remove();
        form.classList.remove('is-empty');
        return;
      }
      event.preventDefault();
      form.classList.add('is-empty');
      input.setAttribute('aria-invalid', 'true');
      if (!hint) {
        hint = document.createElement('p');
        hint.className = 'torantejarat-search-hint';
        hint.textContent = form.dataset.emptyText || 'عبارت جست‌وجو را وارد کنید.';
        hint.setAttribute('role', 'alert');
        form.appendChild(hint);
      }
      input.focus({ preventScroll: true });
    });
    input.addEventListener('input', () => {
      if (input.value.trim() !== '') {
        form.classList.remove('is-empty');
        input.removeAttribute('aria-invalid');
        if (hint) hint.remove();
      }
    });
  });
})();
