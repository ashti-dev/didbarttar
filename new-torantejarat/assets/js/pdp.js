/* Mobile purchase bar: trigger the native single-product add-to-cart form. */
/* Click-to-play video facade: load the player only on user click. */
(function () {
	'use strict';
	document.body.classList.add('js');
	document.addEventListener('click', function (event) {
		var button = event.target.closest('[data-torantejarat-mobile-add]');
		if (button) {
			var main = document.querySelector('form.cart .single_add_to_cart_button');
			if (main) {
				main.click();
			}
			return;
		}
		var cover = event.target.closest('[data-video-cover]');
		if (!cover) {
			return;
		}
		var box = cover.closest('[data-video-facade]');
		var tpl = box ? box.querySelector('template') : null;
		if (!tpl) {
			return;
		}
		box.innerHTML = '';
		box.appendChild(tpl.content.cloneNode(true));
		var video = box.querySelector('video');
		if (video && video.play) {
			try {
				var pending = video.play();
				if (pending && pending.catch) {
					pending.catch(function () {});
				}
			} catch (ignore) {}
		}
	});
})();
