/* Bottom mobile menu: the search item focuses the header search field,
   which opens the live search modal. No-JS fallback is plain navigation. */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var btn = document.querySelector('[data-menu-search]');
		if (!btn) {
			return;
		}
		btn.addEventListener('click', function () {
			var field = document.querySelector('form.torantejarat-header-search input[name="s"], form.torantejarat-header-search input[name="q"]');
			if (field) {
				try {
					window.scrollTo({ top: 0, behavior: 'smooth' });
				} catch (e) { /* older browsers */ }
				field.focus({ preventScroll: true });
				return;
			}
			if (typeof torantejaratMenu !== 'undefined' && torantejaratMenu.shopUrl) {
				window.location.href = torantejaratMenu.shopUrl;
			}
		});
	});
})();
