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
  // A late-loading script must not hide a link the user already reached without JS.
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
})();
