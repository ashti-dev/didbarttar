/* Product Finder: step-by-step wizard (progressive enhancement).
   Without JS the form submits normally and results render server-side.
   With JS: one question at a time, progress label, back button, and the
   submit appears only once the last step is reached. */
(function () {
	'use strict';

	var FA_DIGITS = '۰۱۲۳۴۵۶۷۸۹';

	function faNumber(n) {
		return String(n).replace(/\d/g, function (d) { return FA_DIGITS[+d]; });
	}

	document.addEventListener('DOMContentLoaded', function () {
		var root = document.querySelector('[data-finder]');
		if (!root) {
			return;
		}
		var form = root.querySelector('[data-finder-form]');
		var steps = Array.prototype.slice.call(root.querySelectorAll('[data-step]'));
		var progress = root.querySelector('[data-finder-progress]');
		var backBtn = root.querySelector('[data-finder-back]');
		if (!form || steps.length < 2) {
			return;
		}

		root.classList.add('js-finder');

		var editBtn = root.querySelector('[data-finder-edit]');
		if (editBtn && root.classList.contains('is-submitted')) {
			editBtn.hidden = false;
			editBtn.addEventListener('click', function () {
				root.classList.remove('is-submitted');
				editBtn.hidden = true;
				showStep(0);
				var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
				try {
					form.scrollIntoView({ block: 'start', behavior: reduced ? 'auto' : 'smooth' });
				} catch (e) { /* Headless: skip scrolling. */ }
			});
		}

		function currentIndex() {
			for (var i = 0; i < steps.length; i++) {
				if (!steps[i].classList.contains('is-done')) {
					return i;
				}
			}
			return steps.length;
		}

		function showStep(index) {
			index = Math.max(0, Math.min(index, steps.length));
			steps.forEach(function (step, i) {
				if (i < index) {
					step.classList.add('is-done');
					step.classList.remove('is-active');
				} else if (i === index) {
					step.classList.add('is-active');
					step.classList.remove('is-done');
				} else {
					step.classList.remove('is-active', 'is-done');
				}
			});
			var complete = index >= steps.length;
			root.classList.toggle('is-complete', complete);
			if (progress) {
				progress.hidden = complete;
				if (!complete) {
					progress.textContent = 'مرحله ' + faNumber(index + 1) + ' از ' + faNumber(steps.length);
				}
			}
			if (backBtn) {
				backBtn.hidden = complete || index === 0;
			}
			var focusTarget = steps[Math.min(index, steps.length - 1)];
			if (focusTarget) {
				var legend = focusTarget.querySelector('legend');
				if (legend) {
					legend.setAttribute('tabindex', '-1');
					legend.focus({ preventScroll: true });
				}
			}
		}

		steps.forEach(function (step, index) {
			step.addEventListener('change', function (event) {
				if (event.target.matches('input[type="radio"]')) {
					if (index === steps.length - 1) {
						finishWizard();
					} else {
						showStep(index + 1);
					}
				}
			});
		});

		var finishTimer = null;

		function finishWizard() {
			showStep(steps.length);
			root.classList.add('is-loading');
			var loader = document.createElement('div');
			loader.className = 'torantejarat-finder-loading';
			loader.setAttribute('role', 'status');
			loader.innerHTML = '<span class="torantejarat-spinner" aria-hidden="true"></span><p>در حال بررسی مدل‌ها با پاسخ‌های شما…</p>';
			form.appendChild(loader);
			if (finishTimer) {
				clearTimeout(finishTimer);
			}
			finishTimer = setTimeout(function () {
				form.submit();
			}, 1800);
		}

		if (backBtn) {
			backBtn.addEventListener('click', function () {
				if (finishTimer) {
					clearTimeout(finishTimer);
					finishTimer = null;
				}
				root.classList.remove('is-loading');
				var loader = root.querySelector('.torantejarat-finder-loading');
				if (loader) {
					loader.remove();
				}
				showStep(currentIndex() - 1);
			});
		}

		function firstUnanswered() {
			for (var i = 0; i < steps.length; i++) {
				if (!steps[i].querySelector('input[type="radio"]:checked')) {
					return i;
				}
			}
			return steps.length;
		}

		showStep(firstUnanswered());

		var resultsBox = root.querySelector('[data-finder-results]');
		if (resultsBox) {
			var reduced = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;
			try {
				resultsBox.scrollIntoView({ block: 'start', behavior: reduced ? 'auto' : 'smooth' });
			} catch (e) { /* Headless: skip scrolling. */ }
		}
	});
})();
