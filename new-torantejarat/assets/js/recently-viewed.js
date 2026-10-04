/* Recently viewed products: localStorage recorder (PDP) + slot renderer. No backend. */
(function () {
	'use strict';
	var KEY = 'torantejarat-recently-viewed';
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
		var title = metaContent('og:title') || document.title;
		if (title) {
			var url = canonicalUrl();
			var rest = read().filter(function (item) {
				return item.url !== url;
			});
			rest.unshift({ url: url, title: title, image: metaContent('og:image') });
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
					card.appendChild(img);
				}
				var label = document.createElement('span');
				label.textContent = item.title;
				card.appendChild(label);
				grid.appendChild(card);
			});
			section.hidden = false;
		});
	});
})();
