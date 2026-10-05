/* Product Finder: step-by-step wizard (progressive enhancement).
   Without JS the form submits normally and results render server-side. */
(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		var root = document.querySelector('[data-finder]');
		if (!root) {
			return;
		}
		var form = root.querySelector('[data-finder-form]');
		var steps = Array.prototype.slice.call(root.querySelectorAll('[data-step]'));
		if (!form || steps.length < 2) {
			return;
		}

		root.classList.add('js-finder');

		function currentIndex() {
			for (var i = 0; i < steps.length; i++) {
				if (!steps[i].classList.contains('is-done')) {
					return i;
				}
			}
			return steps.length;
		}

		function showStep(index) {
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
		}

		steps.forEach(function (step, index) {
			step.addEventListener('change', function (event) {
				if (event.target.matches('input[type="radio"]')) {
					showStep(index + 1);
				}
			});
		});

		showStep(0);
	});
})();
