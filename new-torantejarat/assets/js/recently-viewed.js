/* Recently viewed products: localStorage recorder (PDP) + slot renderer. No backend. */
(function () {
	'use strict';
	var KEY = 'torantejarat-recently-viewed-v2';
	var MAX = 8;

	function read() {
		try {
			var raw = window.localStorage.getItem(KEY);
			var list = raw ? JSON.parse(raw) : [];
			return Array.isArray(list) ? list : [];
		} catch (error) {
			return [];
		}
	}

	function write(list) {
		try {
			window.localStorage.setItem(KEY, JSON.stringify(list.slice(0, MAX)));
		} catch (error) {
			/* Private mode or disabled storage: skip silently. */
		}
	}

	function metaContent(property) {
		var el = document.querySelector('meta[property="' + property + '"]');
		return el ? el.getAttribute('content') : '';
	}

	function canonicalUrl() {
		var link = document.querySelector('link[rel="canonical"]');
		var href = link ? link.getAttribute('href') : window.location.href;
		return href.split('?')[0];
	}

	if (document.body && document.body.classList.contains('single-product')) {
		var recorded = null;
		var source = document.getElementById('torantejarat-recent-source');
		if (source) {
			try {
				var data = JSON.parse(source.textContent);
				if (data && data.url) {
					recorded = { url: String(data.url).split('?')[0], title: String(data.title || ''), image: String(data.image || ''), price: String(data.price || '') };
				}
			} catch (error) { /* Fall through to meta scraping. */ }
		}
		if (!recorded) {
			var title = metaContent('og:title') || document.title;
			if (title) {
				recorded = { url: canonicalUrl(), title: title, image: metaContent('og:image'), price: '' };
			}
		}
		if (recorded && recorded.title) {
			var rest = read().filter(function (item) {
				return item.url !== recorded.url;
			});
			rest.unshift(recorded);
			write(rest);
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		var current = canonicalUrl();
		var items = read().filter(function (item) {
			return item.url && item.url !== current;
		}).slice(0, 6);
		if (!items.length) {
			return;
		}
		var sections = document.querySelectorAll('.torantejarat-recent-section');
		Array.prototype.forEach.call(sections, function (section) {
			var grid = section.querySelector('.torantejarat-recent-grid');
			if (!grid) {
				return;
			}
			items.forEach(function (item) {
				var card = document.createElement('a');
				card.className = 'torantejarat-recent-card';
				card.href = item.url;
				if (item.image) {
					var img = document.createElement('img');
					img.src = item.image;
					img.alt = item.title;
					img.loading = 'lazy';
					img.width = 72;
					img.height = 72;
					card.appendChild(img);
				}
				var body = document.createElement('span');
				body.className = 'torantejarat-recent-body';
				var label = document.createElement('strong');
				label.textContent = item.title;
				body.appendChild(label);
				if (item.price) {
					var price = document.createElement('span');
					price.className = 'torantejarat-recent-price';
					price.textContent = item.price;
					body.appendChild(price);
				}
				card.appendChild(body);
				grid.appendChild(card);
			});
			section.hidden = false;
		});
	});
})();
