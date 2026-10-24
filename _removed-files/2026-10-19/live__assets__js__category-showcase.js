/* Home category showcase: tab switching between per-category sliders.
   Progressive enhancement — without JS every panel stays visible. */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var section = document.querySelector('.torantejarat-category-showcase');
		if (!section) {
			return;
		}
		var tabs = Array.prototype.slice.call(section.querySelectorAll('[data-cat-tab]'));
		var panels = Array.prototype.slice.call(section.querySelectorAll('[data-cat-panel]'));
		if (tabs.length < 2 || panels.length < 2) {
			return;
		}

		function refreshSliders() {
			try {
				window.dispatchEvent(new Event('resize'));
			} catch (error) {
				/* Older engines: sliders re-measure on real resizes. */
			}
		}

		function activate(index) {
			tabs.forEach(function (tab, i) {
				tab.setAttribute('aria-selected', i === index ? 'true' : 'false');
			});
			panels.forEach(function (panel, i) {
				panel.hidden = i !== index;
			});
			refreshSliders();
		}

		section.classList.add('js-cat-tabs');

		tabs.forEach(function (tab, index) {
			tab.addEventListener('click', function () {
				activate(index);
			});
		});

		activate(0);
	});
})();
