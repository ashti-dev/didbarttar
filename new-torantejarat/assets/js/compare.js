/* Compare refresh without full page reload (progressive enhancement).
   No-JS fallback: the form submits normally and the page renders server-side.
   With JS: select/diff changes and submit fetch only the results fragment
   from admin-ajax; the URL stays shareable via history.pushState. */
(function () {
	'use strict';

	var FA_DIGITS = '۰۱۲۳۴۵۶۷۸۹';

	function faNumber(n) {
		return String(n).replace(/\d/g, function (d) { return FA_DIGITS[+d]; });
	}

	document.addEventListener('DOMContentLoaded', function () {
		var form = document.querySelector('[data-compare-form]');
		var results = document.querySelector('[data-compare-results]');
		if (!form || !results || typeof torantejaratCompare === 'undefined') {
			return;
		}
		if (!('fetch' in window) || !('AbortController' in window)) {
			return;
		}

		var status = document.createElement('p');
		status.className = 'torantejarat-sr-only';
		status.setAttribute('role', 'status');
		results.parentNode.insertBefore(status, results);

		var pending = null;

		function params() {
			return new URLSearchParams(new FormData(form));
		}

		function pageUrl() {
			var q = params().toString();
			return form.action + (q ? '?' + q : '');
		}

		function announce(count) {
			status.textContent = count >= 2
				? faNumber(count) + ' دستگاه با هم مقایسه شد.'
				: 'برای مقایسه، دو دستگاه متفاوت انتخاب کنید.';
		}

		function load(push) {
			if (pending) {
				pending.abort();
			}
			var controller = new AbortController();
			pending = controller;
			results.setAttribute('aria-busy', 'true');
			results.classList.add('is-loading');

			fetch(torantejaratCompare.ajaxUrl + '?action=torantejarat_compare&nonce=' + encodeURIComponent(torantejaratCompare.nonce) + '&' + params().toString(), {
				signal: controller.signal,
				headers: { 'X-Requested-With': 'XMLHttpRequest' },
				credentials: 'same-origin'
			}).then(function (res) {
				if (!res.ok) {
					throw new Error('bad response');
				}
				return res.json();
			}).then(function (json) {
				if (!json || !json.success) {
					throw new Error('bad payload');
				}
				results.innerHTML = json.data.html;
				results.removeAttribute('aria-busy');
				results.classList.remove('is-loading');
				announce(json.data.count);
				if (push !== false) {
					try {
						history.pushState(null, '', pageUrl());
					} catch (e) { /* File previews may restrict history updates. */ }
				}
			}).catch(function (err) {
				if (err && err.name === 'AbortError') {
					return;
				}
				/* Anything unexpected: fall back to a normal page load. */
				form.submit();
			});
		}

		form.addEventListener('submit', function (event) {
			event.preventDefault();
			load(true);
		});

		form.addEventListener('change', function (event) {
			if (event.target.matches('select, input[type="checkbox"]')) {
				load(true);
			}
		});

		window.addEventListener('popstate', function () {
			try {
				var url = new URL(window.location.href);
				url.searchParams.forEach(function (value, key) {
					var field = form.elements.namedItem(key);
					if (!field) {
						return;
					}
					if (field.type === 'checkbox') {
						field.checked = value === '1';
					} else {
						field.value = value;
					}
				});
			} catch (e) { /* Keep current form state. */ }
			load(false);
		});
	});
})();
