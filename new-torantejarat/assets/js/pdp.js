/* Mobile purchase bar: trigger the native single-product add-to-cart form. */
(function () {
	'use strict';
	document.addEventListener('click', function (event) {
		var button = event.target.closest('[data-torantejarat-mobile-add]');
		if (!button) return;
		var main = document.querySelector('form.cart .single_add_to_cart_button');
		if (main) {
			main.click();
		}
	});
})();
