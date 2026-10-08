/* Live search modal (Digikala-style): dimmed page, focused modal, instant
   results. Progressive enhancement: header forms submit normally without JS.
   With JS, focusing any theme search field opens the modal instead. */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var forms = Array.prototype.slice.call(document.querySelectorAll('form.torantejarat-header-search'));
		if (!forms.length || !('fetch' in window) || !('AbortController' in window) || typeof torantejaratSearch === 'undefined') {
			return;
		}

		var overlay = document.createElement('div');
		overlay.className = 'torantejarat-search-modal';
		overlay.hidden = true;
		overlay.innerHTML = '<div class="torantejarat-search-box" role="dialog" aria-modal="true" aria-label="' + torantejaratSearch.label + '">'
			+ '<div class="torantejarat-search-bar">'
			+ '<button type="button" data-search-close aria-label="' + torantejaratSearch.close + '">×</button>'
			+ '<input type="search" autocomplete="off" />'
			+ '</div>'
			+ '<div class="torantejarat-search-live" data-search-live aria-live="polite"></div>'
			+ '</div>';
		document.body.appendChild(overlay);

		var input = overlay.querySelector('input');
		var live = overlay.querySelector('[data-search-live]');
		var pending = null;
		var timer = null;
		var lastInput = null;

		function openModal(prefill) {
			if (prefill) {
				input.value = prefill;
			}
			overlay.hidden = false;
			document.body.classList.add('search-open');
			input.focus();
			if (input.value.trim().length >= 2) {
				search(input.value);
			} else {
				renderEmpty();
			}
		}

		function closeModal() {
			overlay.hidden = true;
			document.body.classList.remove('search-open');
			if (lastInput) {
				lastInput.focus({ preventScroll: true });
			}
		}

		function esc(text) {
			return String(text).replace(/[&<>"']/g, function (c) {
				return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
			});
		}

		function renderEmpty() {
			live.innerHTML = '<p class="torantejarat-search-hintline">' + esc(torantejaratSearch.hint) + '</p>';
		}

		function renderLoading() {
			live.innerHTML = '<p class="torantejarat-search-hintline"><span class="torantejarat-spinner" aria-hidden="true"></span></p>';
		}

		function search(q) {
			if (pending) {
				pending.abort();
			}
			var controller = new AbortController();
			pending = controller;
			renderLoading();
			fetch(torantejaratSearch.ajaxUrl + '?action=torantejarat_live_search&nonce=' + encodeURIComponent(torantejaratSearch.nonce) + '&q=' + encodeURIComponent(q), {
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
				renderResults(q, json.data);
			}).catch(function (err) {
				if (err && err.name === 'AbortError') {
					return;
				}
				live.innerHTML = '';
			});
		}

		function renderResults(q, data) {
			var html = '';
			if (data.cats && data.cats.length) {
				html += '<p class="torantejarat-search-group">' + esc(torantejaratSearch.inCats) + '</p><div class="torantejarat-search-cats">';
				data.cats.forEach(function (c) {
					html += '<a href="' + esc(c.url) + '">' + esc(c.name) + '</a>';
				});
				html += '</div>';
			}
			if (data.products && data.products.length) {
				html += '<p class="torantejarat-search-group">' + esc(torantejaratSearch.inProducts) + '</p><div class="torantejarat-search-items">';
				data.products.forEach(function (p) {
					html += '<a class="torantejarat-search-item" href="' + esc(p.url) + '">';
					if (p.image) {
						html += '<img src="' + esc(p.image) + '" alt="" loading="lazy" width="56" height="56">';
					}
					html += '<span><strong>' + esc(p.name) + '</strong>';
					if (p.price) {
						html += '<small>' + esc(p.price) + '</small>';
					}
					html += '</span></a>';
				});
				html += '</div>';
			}
			if (!html) {
				html = '<p class="torantejarat-search-hintline">' + esc(torantejaratSearch.noResult) + '</p>';
			}
			var allUrl = torantejaratSearch.shopUrl + (torantejaratSearch.shopUrl.indexOf('?') >= 0 ? '&' : '?') + 's=' + encodeURIComponent(q);
			html += '<a class="torantejarat-search-all" href="' + esc(allUrl) + '">' + esc(torantejaratSearch.viewAll) + '</a>';
			live.innerHTML = html;
		}

		forms.forEach(function (form) {
			var field = form.querySelector('input[name="s"]');
			if (!field) {
				return;
			}
			field.addEventListener('focus', function () {
				lastInput = field;
				openModal(field.value);
			});
			field.addEventListener('click', function () {
				lastInput = field;
				openModal(field.value);
			});
			form.addEventListener('submit', function (event) {
				if (field.value.trim() === '') {
					return;
				}
				event.preventDefault();
				openModal(field.value);
			});
		});

		input.addEventListener('input', function () {
			var q = input.value.trim();
			if (timer) {
				clearTimeout(timer);
			}
			if (q.length < 2) {
				if (pending) {
					pending.abort();
				}
				renderEmpty();
				return;
			}
			timer = setTimeout(function () {
				search(q);
			}, 280);
		});

		input.addEventListener('keydown', function (event) {
			if (event.key === 'Enter') {
				event.preventDefault();
				var q = input.value.trim();
				if (q === '') {
					return;
				}
				window.location.href = torantejaratSearch.shopUrl + (torantejaratSearch.shopUrl.indexOf('?') >= 0 ? '&' : '?') + 's=' + encodeURIComponent(q);
			}
		});

		overlay.addEventListener('click', function (event) {
			if (event.target === overlay || event.target.closest('[data-search-close]')) {
				closeModal();
			}
		});

		document.addEventListener('keydown', function (event) {
			if (event.key === 'Escape' && !overlay.hidden) {
				closeModal();
			}
		});
	});
})();
