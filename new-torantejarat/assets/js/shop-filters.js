/* Smart filters: PJAX-lite refresh of the catalog results subtree.
   The form submits normally without JS. With JS, the same filtered URL is
   fetched and only .torantejarat-catalog-results is swapped (single code
   path). Sheet open/close + Escape handling for the mobile bottom sheet. */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var results = document.querySelector('[data-catalog-results]');
		var form = document.querySelector('[data-filters-form]');
		if (!results || !form || !('fetch' in window)) {
			return;
		}
		if (window.matchMedia && window.matchMedia('(max-width:900px)').matches) {
			document.body.classList.add('js-filters');
		}
		window.addEventListener('resize', function () {
			if (window.matchMedia && window.matchMedia('(max-width:900px)').matches) {
				document.body.classList.add('js-filters');
			} else {
				document.body.classList.remove('js-filters', 'filter-open');
			}
		});

		var pending = null;

		function pageUrl(extra) {
			var params = new URLSearchParams(new FormData(form));
			/* Collect first: deleting during forEach skips entries. */
			var empty = [];
			params.forEach(function (value, key) {
				if ((value === '' || value === null) && empty.indexOf(key) === -1) {
					empty.push(key);
				}
			});
			empty.forEach(function (key) { params.delete(key); });
			if (extra) {
				Object.keys(extra).forEach(function (k) { params.set(k, extra[k]); });
			}
			var q = params.toString();
			return form.action + (q ? '?' + q : '');
		}

		function load(url, push) {
			if (pending) {
				pending.abort();
			}
			var controller = new AbortController();
			pending = controller;
			results.setAttribute('aria-busy', 'true');
			results.classList.add('is-loading');
			fetch(url, { signal: controller.signal, headers: { 'X-Requested-With': 'XMLHttpRequest' }, credentials: 'same-origin' })
				.then(function (res) {
					if (!res.ok) {
						throw new Error('bad response');
					}
					return res.text();
				})
				.then(function (html) {
					var doc = new DOMParser().parseFromString(html, 'text/html');
					var fresh = doc.querySelector('[data-catalog-results]');
					if (!fresh) {
						throw new Error('no results');
					}
					results.innerHTML = fresh.innerHTML;
					results.removeAttribute('aria-busy');
					results.classList.remove('is-loading');
					document.body.classList.remove('filter-open');
					if (push !== false) {
						try {
							history.pushState(null, '', url);
						} catch (e) { /* File previews may restrict history updates. */ }
					}
					results.scrollIntoView({ block: 'start', behavior: 'smooth' });
				})
				.catch(function (err) {
					if (err && err.name === 'AbortError') {
						return;
					}
					window.location.href = url;
				});
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			load(pageUrl(), true);
		});

		document.addEventListener('click', function (event) {
			var openBtn = event.target.closest('[data-filter-open]');
			if (openBtn) {
				document.body.classList.add('filter-open');
				return;
			}
			if (event.target.closest('[data-filter-close]')) {
				document.body.classList.remove('filter-open');
				return;
			}
			var chip = event.target.closest('[data-chips] a');
			if (chip) {
				event.preventDefault();
				load(chip.href, true);
				return;
			}
			var page = event.target.closest('[data-catalog-results] .page-numbers');
			if (page) {
				event.preventDefault();
				load(page.href, true);
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape') {
				document.body.classList.remove('filter-open');
			}
		});

		document.addEventListener('change', function (event) {
			if (event.target.matches && event.target.matches('[data-catalog-results] .woocommerce-ordering select')) {
				load(pageUrl({ orderby: event.target.value }), true);
			}
		});

		window.addEventListener('popstate', function () {
			load(window.location.href, false);
		});
	});
})();
