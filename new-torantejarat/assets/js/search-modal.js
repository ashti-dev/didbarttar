/* Live search modal (Digikala-style): dimmed page, focused modal, instant
   results. Progressive enhancement: header forms submit normally without JS.
   With JS, focusing any theme search field opens the modal instead.
   Instant path: the whole catalog is prefetched once (REST, transient-cached)
   and normalized into an in-memory index, so keystrokes render with zero
   network. Server search stays as the fallback until the catalog arrives. */
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
			+ '<input type="search" role="combobox" aria-expanded="true" aria-controls="torantejarat-search-live" aria-autocomplete="list" autocomplete="off" maxlength="60" spellcheck="false" />'
			+ '</div>'
			+ '<div class="torantejarat-search-live" id="torantejarat-search-live" data-search-live aria-live="polite"></div>'
			+ '</div>';
		document.body.appendChild(overlay);

		var input = overlay.querySelector('input');
		var live = overlay.querySelector('[data-search-live]');
		var pending = null;
		var timer = null;
		var lastInput = null;
		var lastKey = '';
		var active = -1;
		var uid = 0;
		var pushed = false;
		var suppressUntil = 0;
		var cache = new Map();
		var index = null;
		var catalogLoading = false;
		var baseUrl = torantejaratSearch.searchUrl || torantejaratSearch.shopUrl;

		function searchUrl(q) {
			var param = torantejaratSearch.searchParam || 's';
			return baseUrl + (baseUrl.indexOf('?') >= 0 ? '&' : '?') + param + '=' + encodeURIComponent(q);
		}

		function fix(s) {
			return String(s).replace(/ي/g, 'ی').replace(/ك/g, 'ک').replace(/\s+/g, ' ').trim().slice(0, 60);
		}

		/* Normalize for matching and cache keys: ي/ك unified, Persian and
		   Arabic digits folded to Latin, lowercased. */
		function norm(s) {
			return fix(s).toLowerCase()
				.replace(/[۰-۹]/g, function (c) { return '۰۱۲۳۴۵۶۷۸۹'.indexOf(c); })
				.replace(/[٠-٩]/g, function (c) { return '٠١٢٣٤٥٦٧٨٩'.indexOf(c); });
		}

		function esc(text) {
			return String(text).replace(/[&<>"']/g, function (c) {
				return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[c];
			});
		}

		/* Whole catalog in memory, normalized once: per-keystroke cost is a
		   few indexOf checks, never normalization or network. */
		function loadCatalog() {
			if (index || catalogLoading || !torantejaratSearch.catalog || !('fetch' in window)) {
				return;
			}
			catalogLoading = true;
			fetch(torantejaratSearch.catalog, { headers: { 'Accept': 'application/json' }, credentials: 'same-origin' })
				.then(function (res) { return res.ok ? res.json() : null; })
				.then(function (json) {
					if (!json || !Array.isArray(json.products)) {
						return;
					}
					index = {
						products: json.products.map(function (p) {
							return {
								data: p,
								hay: norm((p.name || '') + ' ' + (p.sku || '') + ' ' + (p.cat || '')),
								sku: norm(p.sku || '')
							};
						}),
						cats: (json.cats || []).map(function (c) {
							return { data: c, hay: norm(c.name || '') };
						})
					};
				})
				.catch(function () { /* server path stays as fallback */ })
				.then(function () { catalogLoading = false; });
		}

		function localSearch(q) {
			if (!index) {
				return null;
			}
			var nq = norm(q);
			var toks = nq.split(' ').filter(Boolean);
			if (!toks.length) {
				return null;
			}
			var exact = [];
			var rest = [];
			index.products.forEach(function (entry) {
				var ok = toks.every(function (t) { return entry.hay.indexOf(t) >= 0; });
				if (!ok) {
					return;
				}
				if (entry.sku && entry.sku === nq) {
					exact.push(entry.data);
				} else {
					rest.push(entry.data);
				}
			});
			var cats = [];
			index.cats.forEach(function (entry) {
				if (entry.hay.indexOf(nq) >= 0) {
					cats.push(entry.data);
				}
			});
			return { products: exact.concat(rest).slice(0, 6), cats: cats.slice(0, 3) };
		}

		/* Prefetch on idle so the index is ready before the user types. */
		if ('requestIdleCallback' in window) {
			requestIdleCallback(function () { loadCatalog(); }, { timeout: 3000 });
		} else {
			setTimeout(loadCatalog, 1500);
		}

		function endpoint(q) {
			if (torantejaratSearch.endpoint) {
				return torantejaratSearch.endpoint + (torantejaratSearch.endpoint.indexOf('?') >= 0 ? '&' : '?') + 'q=' + encodeURIComponent(q);
			}
			return torantejaratSearch.ajaxUrl + '?action=torantejarat_live_search&nonce=' + encodeURIComponent(torantejaratSearch.nonce) + '&q=' + encodeURIComponent(q);
		}

		function openModal(prefill) {
			if (!overlay.hidden) {
				input.focus();
				return;
			}
			if (prefill) {
				input.value = prefill;
			}
			overlay.hidden = false;
			document.body.classList.add('search-open');
			if (!pushed) {
				try { history.pushState({ torantejaratSearch: 1 }, ''); pushed = true; } catch (e) { /* private mode */ }
			}
			input.focus();
			loadCatalog();
			if (fix(input.value).length >= 2) {
				search(input.value);
			} else {
				renderEmpty();
			}
		}

		function hideModal() {
			overlay.hidden = true;
			document.body.classList.remove('search-open');
			setActive(-1);
			/* Restoring focus fires the trigger's focus listener — suppress
			   the reopen window so close really means close. */
			suppressUntil = Date.now() + 600;
			if (lastInput && document.contains(lastInput)) {
				lastInput.focus({ preventScroll: true });
			}
		}

		function closeModal() {
			if (overlay.hidden) {
				return;
			}
			/* Route the close through history so the browser Back button
			   and the ✕/backdrop/Esc all behave the same standard way. */
			if (pushed) {
				pushed = false;
				try { history.back(); return; } catch (e) { /* fall through */ }
			}
			hideModal();
		}

		window.addEventListener('popstate', function () {
			if (!overlay.hidden) {
				pushed = false;
				hideModal();
			}
		});

		function items() {
			return Array.prototype.slice.call(live.querySelectorAll('[data-search-item]'));
		}

		function setActive(i) {
			var list = items();
			list.forEach(function (el, n) {
				var on = n === i;
				el.classList.toggle('is-active', on);
				if (on) {
					el.setAttribute('aria-selected', 'true');
				} else {
					el.removeAttribute('aria-selected');
				}
			});
			active = i;
			if (i > -1 && list[i]) {
				input.setAttribute('aria-activedescendant', list[i].id);
				list[i].scrollIntoView({ block: 'nearest' });
			} else {
				input.removeAttribute('aria-activedescendant');
			}
		}

		function renderEmpty() {
			setActive(-1);
			live.innerHTML = '<p class="torantejarat-search-hintline">' + esc(torantejaratSearch.hint) + '</p>';
		}

		function renderLoading() {
			/* Keep old results on screen while the next query runs (dimmed);
			   spinner only on the very first load — no wipe flicker. */
			if (live.querySelector('[data-search-live-list]')) {
				live.classList.add('is-loading');
				return;
			}
			live.classList.remove('is-loading');
			live.innerHTML = '<p class="torantejarat-search-hintline"><span class="torantejarat-spinner" aria-hidden="true"></span></p>';
		}

		function search(raw) {
			var q = fix(raw);
			var key = norm(q);
			if (key === lastKey && live.querySelector('[data-search-live-list], [data-search-empty]')) {
				return;
			}
			lastKey = key;
			/* Instant path: in-memory index, zero network. */
			var local = localSearch(q);
			if (local) {
				if (pending) {
					pending.abort();
					pending = null;
				}
				live.classList.remove('is-loading');
				renderResults(q, local);
				return;
			}
			var hit = cache.get(key);
			if (hit) {
				if (pending) {
					pending.abort();
					pending = null;
				}
				live.classList.remove('is-loading');
				renderResults(q, hit);
				return;
			}
			if (pending) {
				pending.abort();
			}
			var controller = new AbortController();
			pending = controller;
			renderLoading();
			fetch(endpoint(q), {
				signal: controller.signal,
				headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
				credentials: 'same-origin'
			}).then(function (res) {
				if (!res.ok) {
					throw new Error('bad response');
				}
				return res.json();
			}).then(function (json) {
				var data = json && json.success !== undefined ? (json.success ? json.data : null) : json;
				if (!data || json.success === false) {
					throw new Error('bad payload');
				}
				data.cats = data.cats || data.categories || [];
				cache.set(key, data);
				if (cache.size > 40) {
					cache.delete(cache.keys().next().value);
				}
				if (norm(input.value) !== key) {
					return;
				}
				live.classList.remove('is-loading');
				renderResults(q, data);
			}).catch(function (err) {
				if (err && err.name === 'AbortError') {
					return;
				}
				/* Keep stale results visible on error; only clear the dim. */
				live.classList.remove('is-loading');
				if (!live.querySelector('[data-search-live-list], [data-search-empty]')) {
					live.innerHTML = '';
				}
			});
		}

		function renderResults(q, data) {
			uid = 0;
			var html = '<div data-search-live-list role="listbox" aria-label="' + esc(torantejaratSearch.label) + '">';
			if (data.cats && data.cats.length) {
				html += '<p class="torantejarat-search-group">' + esc(torantejaratSearch.inCats) + '</p><div class="torantejarat-search-cats">';
				data.cats.forEach(function (c) {
					html += '<a role="option" aria-selected="false" id="torantejarat-search-' + (++uid) + '" data-search-item href="' + esc(c.url) + '">' + esc(c.name) + '</a>';
				});
				html += '</div>';
			}
			if (data.products && data.products.length) {
				html += '<p class="torantejarat-search-group">' + esc(torantejaratSearch.inProducts) + '</p><div class="torantejarat-search-items">';
				data.products.forEach(function (p) {
					html += '<a class="torantejarat-search-item" role="option" aria-selected="false" id="torantejarat-search-' + (++uid) + '" data-search-item href="' + esc(p.url) + '">';
					if (p.image) {
						html += '<img src="' + esc(p.image) + '" alt="" loading="lazy" decoding="async" width="56" height="56">';
					}
					html += '<span><strong>' + esc(p.name) + '</strong>';
					if (p.price) {
						html += '<small>' + esc(p.price) + '</small>';
					}
					html += '</span></a>';
				});
				html += '</div>';
			}
			if (uid === 0) {
				html = '<p class="torantejarat-search-hintline" data-search-empty>' + esc(torantejaratSearch.noResult) + '</p>';
			} else {
				html += '</div>';
			}
			html += '<a class="torantejarat-search-all" href="' + esc(searchUrl(q)) + '">' + esc(torantejaratSearch.viewAll) + '</a>';
			live.innerHTML = html;
			setActive(-1);
		}

		forms.forEach(function (form) {
			var field = form.querySelector('input[name="s"], input[name="q"]');
			if (!field) {
				return;
			}
			field.addEventListener('focus', function () {
				if (Date.now() < suppressUntil) {
					return;
				}
				lastInput = field;
				openModal(field.value);
			});
			field.addEventListener('click', function () {
				if (Date.now() < suppressUntil) {
					return;
				}
				lastInput = field;
				openModal(field.value);
			});
			form.addEventListener('submit', function (event) {
				if (fix(field.value) === '') {
					return;
				}
				event.preventDefault();
				openModal(field.value);
			});
		});

		input.addEventListener('input', function () {
			var q = fix(input.value);
			if (timer) {
				clearTimeout(timer);
			}
			if (q.length < 2) {
				if (pending) {
					pending.abort();
				}
				lastKey = '';
				live.classList.remove('is-loading');
				renderEmpty();
				return;
			}
			/* Index in memory: answer synchronously, no debounce wait. */
			if (index) {
				search(q);
				return;
			}
			timer = setTimeout(function () {
				search(q);
			}, 150);
		});

		input.addEventListener('keydown', function (event) {
			if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
				var list = items();
				if (!list.length) {
					return;
				}
				event.preventDefault();
				var d = event.key === 'ArrowDown' ? 1 : -1;
				setActive(active === -1 ? (d > 0 ? 0 : list.length - 1) : (active + d + list.length) % list.length);
			} else if (event.key === 'Enter') {
				event.preventDefault();
				var current = items()[active];
				if (active > -1 && current) {
					current.click();
					return;
				}
				var q = fix(input.value);
				if (q === '') {
					return;
				}
				window.location.href = searchUrl(q);
			}
		});

		live.addEventListener('mousemove', function (event) {
			var el = event.target.closest ? event.target.closest('[data-search-item]') : null;
			if (!el) {
				return;
			}
			var i = items().indexOf(el);
			if (i !== -1 && i !== active) {
				setActive(i);
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
