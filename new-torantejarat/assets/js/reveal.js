/* Scroll reveal — dependency-free entrance animation.
   Progressive enhancement: without JS (or IO support) everything stays
   visible; the hidden state only exists under .reveal-init, which JS adds.
   Elements toggle both ways (hide again when scrolled out of view).
   transform/opacity only — compositor-friendly, no layout shift. */
(function () {
	'use strict';

	if (!('IntersectionObserver' in window) || !('querySelectorAll' in document)) {
		return;
	}
	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
		return;
	}

	/* Curated targets: headings, cards, banners. Tables and forms excluded. */
	var SELECTORS = [
		'.torantejarat-section-heading',
		'.torantejarat-hero-copy',
		'.torantejarat-hero-visual',
		'.torantejarat-category-card',
		'.torantejarat-product-card',
		'.torantejarat-service-card',
		'.torantejarat-finder-banner',
		'.torantejarat-field-copy',
		'.torantejarat-video-panel',
		'.torantejarat-editorial-card',
		'.torantejarat-compare-photo',
		'.torantejarat-finder-card',
		'.torantejarat-check-list li',
		'.torantejarat-bestseller-row',
		'.torantejarat-deal-card',
		'.torantejarat-contact-card'
	];

	document.addEventListener('DOMContentLoaded', function () {
		var root = document.querySelector('#torantejarat-main') || document.body;
		var targets = [];
		SELECTORS.forEach(function (sel) {
			root.querySelectorAll(sel).forEach(function (el) {
				if (!el.hasAttribute('data-reveal')) {
					el.setAttribute('data-reveal', '');
					targets.push(el);
				}
			});
		});
		if (!targets.length) {
			return;
		}

		/* Stagger siblings: up to 6 steps of 70ms within the same parent. */
		var groups = new Map();
		targets.forEach(function (el) {
			var count = groups.get(el.parentNode) || 0;
			var step = Math.min(count, 5);
			el.style.setProperty('--reveal-delay', (step * 70) + 'ms');
			groups.set(el.parentNode, count + 1);
		});

		root.classList.add('reveal-init');

		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (entry) {
				entry.target.classList.toggle('is-visible', entry.isIntersecting);
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -6% 0px' });

		targets.forEach(function (el) { io.observe(el); });
	});
})();
