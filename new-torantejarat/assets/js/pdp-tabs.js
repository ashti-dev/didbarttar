/* PDP anchor tabs: one open panel at a time with a soft entrance.
   Progressive enhancement — without JS every section stays visible. */
(function () {
	'use strict';

	function panelContainer(section) {
		if (!section) {
			return null;
		}
		return section.closest('.torantejarat-container') || section;
	}

	function activate(nav, links, panels, link, updateHash) {
		links.forEach(function (other) {
			if (other === link) {
				other.setAttribute('aria-current', 'true');
			} else {
				other.removeAttribute('aria-current');
			}
		});

		panels.forEach(function (entry) {
			var isActive = entry.link === link;
			entry.box.hidden = !isActive;
			entry.box.classList.toggle('torantejarat-tab-hidden', !isActive);
			entry.section.classList.remove('torantejarat-tab-open');
			if (isActive) {
				void entry.section.offsetWidth;
				entry.section.classList.add('torantejarat-tab-open');
			}
		});

		if (updateHash && link.hash) {
			try {
				history.replaceState(null, '', link.hash);
			} catch (error) {
				/* Ignore private-mode history failures. */
			}
		}
	}

	document.addEventListener('DOMContentLoaded', function () {
		var nav = document.querySelector('.torantejarat-pdp-tabs');
		if (!nav) {
			return;
		}

		var links = Array.prototype.slice.call(nav.querySelectorAll('a[href^="#"]'));
		var panels = [];

		links.forEach(function (link) {
			var section = document.getElementById(link.hash.slice(1));
			var box = panelContainer(section);
			if (!section || !box) {
				return;
			}
			panels.push({ link: link, section: section, box: box });
		});

		if (panels.length < 2) {
			return;
		}

		var details = nav.closest('.torantejarat-product-details');
		if (details) {
			details.classList.add('js-tabs');
		}

		links = panels.map(function (entry) {
			return entry.link;
		});

		links.forEach(function (link) {
			link.addEventListener('click', function (event) {
				event.preventDefault();
				activate(nav, links, panels, link, true);
				nav.scrollIntoView({ behavior: 'smooth', block: 'start' });
			});
		});

		var initial = panels[0].link;
		if (window.location.hash) {
			panels.forEach(function (entry) {
				if ('#' + entry.section.id === window.location.hash) {
					initial = entry.link;
				}
			});
		}
		activate(nav, links, panels, initial, false);
	});
})();
