/**
 * ShalangBin theme runtime.
 * Mobile menu, header scroll state, WooCommerce cart count refresh.
 */
(function () {
	'use strict';

	// ---------- Mobile menu ----------
	var toggle = document.querySelector('.menu-toggle');
	var nav = document.getElementById('main-nav');
	if (toggle && nav) {
		toggle.addEventListener('click', function () {
			var open = nav.classList.toggle('is-open');
			toggle.setAttribute('aria-expanded', open ? 'true' : 'false');
			toggle.setAttribute('aria-label', open ? 'بستن منو' : 'باز کردن منو');
			document.body.classList.toggle('nav-open', open);
		});
		document.addEventListener('keydown', function (e) {
			if (e.key === 'Escape' && nav.classList.contains('is-open')) {
				nav.classList.remove('is-open');
				toggle.setAttribute('aria-expanded', 'false');
				document.body.classList.remove('nav-open');
			}
		});
	}

	// ---------- Sticky header shadow ----------
	var header = document.querySelector('.site-header');
	var lastY = 0;
	if (header) {
		window.addEventListener('scroll', function () {
			var y = window.scrollY;
			if (y > 8 && lastY <= 8) { header.classList.add('is-stuck'); }
			if (y <= 8 && lastY > 8) { header.classList.remove('is-stuck'); }
			lastY = y;
		}, { passive: true });
	}

	// ---------- WooCommerce cart count sync (via fragments) ----------
	if (typeof jQuery !== 'undefined') {
		jQuery(document.body).on('wc_fragments_refreshed wc_fragments_loaded', function () {
			var count = document.querySelector('[data-cart-count]');
			if (!count) { return; }
			var n = parseInt(count.textContent, 10) || 0;
			count.textContent = n.toLocaleString('fa-IR');
		});
	}

	// ---------- [data-add] → WooCommerce AJAX add-to-cart ----------
	// Used by the sticky mobile purchase bar (outside the add-to-cart form).
	document.addEventListener('click', function (e) {
		var btn = e.target.closest('[data-add]');
		if (!btn) { return; }
		e.preventDefault();
		if (btn.classList.contains('is-loading')) { return; }

		var id = parseInt(btn.getAttribute('data-add'), 10);
		if (!id) { return; }

		var cfg = window.ShalangBinData || {};
		var wcAjax = cfg.wcAjaxUrl
			|| (window.wc_add_to_cart_params && window.wc_add_to_cart_params.wc_ajax_url)
			|| (window.location.origin + window.location.pathname + '?wc-ajax=%%endpoint%%');

		var label = btn.innerHTML;
		btn.classList.add('is-loading');
		btn.setAttribute('aria-busy', 'true');

		fetch(wcAjax.replace('%%endpoint%%', 'add_to_cart'), {
			method: 'POST',
			credentials: 'same-origin',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8' },
			body: new URLSearchParams({ product_id: String(id), quantity: '1' }).toString()
		}).then(function (r) { return r.json(); }).then(function (res) {
			btn.classList.remove('is-loading');
			btn.removeAttribute('aria-busy');
			if (!res || res.error) {
				// Variable/unpurchasable products: fall back to the product form.
				window.location.href = btn.getAttribute('data-add-url') || window.location.href;
				return;
			}
			if (res.fragments) {
				Object.keys(res.fragments).forEach(function (sel) {
					document.querySelectorAll(sel).forEach(function (el) { el.innerHTML = res.fragments[sel]; });
				});
			}
			if (res.cart_count !== undefined) {
				var count = document.querySelector('[data-cart-count]');
				if (count) { count.textContent = parseInt(res.cart_count, 10).toLocaleString('fa-IR'); }
			}
			btn.innerHTML = 'به سبد اضافه شد ✓';
			btn.classList.add('is-added');
			window.setTimeout(function () {
				btn.innerHTML = label;
				btn.classList.remove('is-added');
			}, 2200);
			if (typeof jQuery !== 'undefined') { jQuery(document.body).trigger('wc_fragments_refreshed'); }
		}).catch(function () {
			btn.classList.remove('is-loading');
			btn.removeAttribute('aria-busy');
			window.location.href = btn.getAttribute('data-add-url') || window.location.href;
		});
	});

	// ---------- Persian numerals for prices (visual only) ----------
	document.querySelectorAll('.woocommerce-Price-amount').forEach(function (el) {
		el.setAttribute('dir', 'rtl');
	});

	// ---------- Shop filter navigation (radio-style, like reference) ----------
	document.querySelectorAll('#shop-filters input[data-filter-nav]').forEach(function (input) {
		input.addEventListener('change', function () {
			var url = input.getAttribute('data-url');
			if (url) { window.location.href = url; }
		});
	});
})();
