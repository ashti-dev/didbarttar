/* AJAX add-to-cart with Persian button text states (product form + loops). */
(function ($) {
	'use strict';

	if (typeof $ === 'undefined') {
		return;
	}

	var LOADING_TEXT = 'در حال افزودن ...';
	var ADDED_TEXT = 'اضافه شد';
	var ERROR_TEXT = 'خطا! دوباره تلاش کنید';
	var REVERT_DELAY = 2000;

	function getAjaxUrl() {
		if (typeof wc_add_to_cart_params !== 'undefined' && wc_add_to_cart_params.wc_ajax_url) {
			return wc_add_to_cart_params.wc_ajax_url.toString().replace('%%endpoint%%', 'add_to_cart');
		}
		return window.location.origin + window.location.pathname + '?wc-ajax=add_to_cart';
	}

	function ensureOriginalText($btn) {
		if (!$btn.data('wc-ajax-original-html')) {
			$btn.data('wc-ajax-original-html', $btn.html());
		}
	}

	function revertLater($btn) {
		setTimeout(function () {
			var original = $btn.data('wc-ajax-original-html');
			$btn
				.removeClass('wc-ajax-loading wc-ajax-added wc-ajax-error added')
				.removeAttr('aria-disabled')
				.css('pointer-events', '');
			if (typeof original !== 'undefined') {
				$btn.html(original);
			}
			$btn.siblings('.added_to_cart').remove();
		}, REVERT_DELAY);
	}

	function setLoadingState($btn) {
		if (!$btn || !$btn.length) {
			return;
		}
		ensureOriginalText($btn);
		$btn
			.addClass('wc-ajax-loading')
			.removeClass('wc-ajax-added wc-ajax-error added')
			.attr('aria-disabled', 'true')
			.css('pointer-events', 'none')
			.html(LOADING_TEXT);
	}

	function setAddedState($btn) {
		if (!$btn || !$btn.length) {
			return;
		}
		ensureOriginalText($btn);
		$btn.removeClass('wc-ajax-loading wc-ajax-error').addClass('wc-ajax-added').html(ADDED_TEXT);
		revertLater($btn);
	}

	function setErrorState($btn) {
		if (!$btn || !$btn.length) {
			return;
		}
		ensureOriginalText($btn);
		$btn.removeClass('wc-ajax-loading wc-ajax-added').addClass('wc-ajax-error').html(ERROR_TEXT);
		revertLater($btn);
	}

	function sendAddToCartRequest(formData, $btn) {
		setLoadingState($btn);

		$.ajax({
			url: getAjaxUrl(),
			type: 'POST',
			data: formData,
			processData: false,
			contentType: false,
			dataType: 'json'
		}).done(function (response) {
			if (!response || response.error) {
				if (response && response.product_url) {
					window.location = response.product_url;
					return;
				}
				setErrorState($btn);
				return;
			}
			$(document.body).trigger('added_to_cart', [response.fragments, response.cart_hash, $btn]);
			setAddedState($btn);
		}).fail(function () {
			setErrorState($btn);
		});
	}

	$(document).on('click', 'a.add_to_cart_button.product_type_simple:not(.ajax_add_to_cart)', function (e) {
		var $btn = $(this);
		var productId = $btn.data('product_id');

		if (!productId) {
			return;
		}

		e.preventDefault();

		if ($btn.hasClass('wc-ajax-loading')) {
			return;
		}

		var data = new FormData();
		data.append('product_id', productId);
		data.append('quantity', $btn.data('quantity') || 1);

		sendAddToCartRequest(data, $btn);
	});

	$(document.body).on('adding_to_cart', function (e, $btn) {
		setLoadingState($btn);
	});

	$(document.body).on('added_to_cart', function (e, fragments, cart_hash, $btn) {
		setAddedState($btn);
	});

	$(document).on('submit', 'form.cart', function (e) {
		var $form = $(this);

		if ($form.find('input[name^="quantity["]').length) {
			return;
		}

		var $btn = $form.find('.single_add_to_cart_button');

		if (!$btn.length || $btn.hasClass('wc-ajax-loading')) {
			return;
		}

		e.preventDefault();

		var formData = new FormData(this);

		if (!formData.get('product_id')) {
			var addToCartVal = formData.get('add-to-cart') || $btn.val();
			if (addToCartVal) {
				formData.set('product_id', addToCartVal);
			}
		}

		if (!formData.get('quantity')) {
			formData.set('quantity', 1);
		}

		sendAddToCartRequest(formData, $btn);
	});
})(window.jQuery);
