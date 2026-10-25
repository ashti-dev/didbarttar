/**
 * FrontBaz WooCommerce Cart Interactive Logic
 * Handles real-time AJAX Quantity Updates, Item Removal, Coupon Toggle, and Fragment Sync
 */
(function ($) {
	'use strict';

	$(document).ready(function () {
		var isUpdating = false;

		// 1. Quantity Plus & Minus Handlers
		$(document).on('click', '.fb-qty-plus', function (e) {
			e.preventDefault();
			var $btn = $(this);
			$btn.blur();
			if ($btn.hasClass('is-disabled') || $btn.prop('disabled')) {
				return;
			}
			var $container = $btn.closest('.fb-qty-control');
			var $input = $container.find('.fb-qty-input');
			var currentVal = parseInt($input.val(), 10) || 1;
			var maxVal = parseInt($container.attr('data-max-qty') || $container.data('max-qty') || $input.attr('max'), 10);

			if (!isNaN(maxVal) && maxVal > 0 && currentVal >= maxVal) {
				return;
			}

			var newVal = currentVal + 1;
			$input.val(newVal);
			updateCartQuantity($container.data('cart_item_key'), newVal, $btn.closest('.fb-cart-item'));
		});

		var trashSvg = '<svg class="fb-trash-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';
		var minusSvg = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="5" y1="12" x2="19" y2="12"></line></svg>';

		function updateCartTrashIcons() {
			$('.fb-qty-control').each(function () {
				var $ctl = $(this);
				var qty = parseInt($ctl.find('.fb-qty-input').val(), 10) || 1;
				var maxQty = parseInt($ctl.attr('data-max-qty') || $ctl.data('max-qty') || $ctl.find('.fb-qty-input').attr('max'), 10) || 0;
				var $minus = $ctl.find('.fb-qty-minus');
				var $plus = $ctl.find('.fb-qty-plus');

				if (qty <= 1) {
					$minus.addClass('is-trash').attr('aria-label', 'حذف محصول').html(trashSvg);
				} else {
					$minus.removeClass('is-trash').attr('aria-label', 'کاهش تعداد').html(minusSvg);
				}

				if (maxQty > 0 && qty >= maxQty) {
					$plus.addClass('is-disabled').prop('disabled', true).attr('title', 'حداکثر موجودی در انبار (' + maxQty + ' عدد)');
				} else {
					$plus.removeClass('is-disabled').prop('disabled', false).attr('title', 'افزایش تعداد');
				}
			});
		}

		function syncMobileCartTotal() {
			var $current = $('.fb-total-current');
			if ($current.length) {
				$('.fb-mobile-cart-price-amount').html($current.html());
			} else {
				var $total = $('.fb-total-val, .order-total .woocommerce-Price-amount').first();
				if ($total.length) {
					$('.fb-mobile-cart-price-amount').html($total.html());
				}
			}
		}

		updateCartTrashIcons();
		syncMobileCartTotal();

		$(document.body).on('updated_cart_totals updated_wc_div', function () {
			updateCartTrashIcons();
			syncMobileCartTotal();
		});

		$(document).on('click', '.fb-qty-minus', function (e) {
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			var $btn = $(this);
			$btn.blur();
			var $container = $btn.closest('.fb-qty-control');
			var $input = $container.find('.fb-qty-input');
			var currentVal = parseInt($input.val(), 10) || 1;

			if (currentVal <= 1) {
				if (confirm('آیا از حذف این محصول از سبد خرید اطمینان دارید؟')) {
					var $removeBtn = $btn.closest('.fb-cart-item').find('.fb-cart-remove-btn');
					if ($removeBtn.length) {
						$removeBtn.trigger('click', [true]);
					} else {
						updateCartQuantity($container.data('cart_item_key'), 0, $btn.closest('.fb-cart-item'));
					}
				}
				return false;
			}

			var newVal = currentVal - 1;
			$input.val(newVal);
			updateCartQuantity($container.data('cart_item_key'), newVal, $btn.closest('.fb-cart-item'));
		});

		/**
		 * Send AJAX request to update cart quantity
		 */
		function updateCartQuantity(cartItemKey, quantity, $itemRow) {
			if (isUpdating || !cartItemKey) {
				return;
			}
			isUpdating = true;
			$itemRow.addClass('fb-loading');

			$.ajax({
				url: fb_cart_params.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'fb_ajax_update_cart_qty',
					security: fb_cart_params.nonce,
					cart_item_key: cartItemKey,
					quantity: quantity
				},
				success: function (res) {
					if (res && res.success) {
						if (res.data.is_empty) {
							window.location.reload();
							return;
						}

						// Update row subtotal
						if (res.data.item_subtotal) {
							$itemRow.find('.fb-subtotal-amount').html(res.data.item_subtotal);
						}

						// Update count badge
						if (res.data.cart_count !== undefined) {
							$('.fb-cart-count-badge').text(res.data.cart_count + ' قلم کالا');
						}

						// Update Totals sidebar card
						if (res.data.cart_totals_html) {
							$('.fb-cart-collaterals').html(res.data.cart_totals_html);
						}

						// Sync Header Mini-Cart
						$(document.body).trigger('wc_fragment_refresh');

						// Update trash icons state and mobile bar price
						updateCartTrashIcons();
						syncMobileCartTotal();
					} else {
						if (res && res.data && res.data.message) {
							alert(res.data.message);
						}
						if (res && res.data && res.data.current_qty !== undefined) {
							$itemRow.find('.fb-qty-input').val(res.data.current_qty);
							updateCartTrashIcons();
						}
					}
				},
				error: function () {
					// Fallback: submit standard form if AJAX fails
					$('.woocommerce-cart-form').submit();
				},
				complete: function () {
					$itemRow.removeClass('fb-loading');
					isUpdating = false;
				}
			});
		}

		// Disable WooCommerce core cart remove click listener so it doesn't conflict or bypass our confirmation
		$(document).off('click', '.woocommerce-cart-form .product-remove > a');

		// 2. AJAX Remove Cart Item
		$(document).on('click', '.fb-cart-remove-btn', function (e, skipConfirm) {
			e.preventDefault();
			e.stopPropagation();
			e.stopImmediatePropagation();
			var $btn = $(this);
			var cartItemKey = $btn.data('cart_item_key');
			var $itemRow = $btn.closest('.fb-cart-item');

			if (isUpdating || !cartItemKey) {
				return false;
			}

			// Confirm before removing product
			if (!skipConfirm && !confirm('آیا از حذف این محصول از سبد خرید اطمینان دارید؟')) {
				return false;
			}

			isUpdating = true;
			$itemRow.addClass('fb-loading');

			$.ajax({
				url: fb_cart_params.ajax_url,
				type: 'POST',
				dataType: 'json',
				data: {
					action: 'fb_ajax_remove_cart_item',
					security: fb_cart_params.nonce,
					cart_item_key: cartItemKey
				},
				success: function (res) {
					if (res && res.success) {
						if (res.data.is_empty) {
							window.location.reload();
							return;
						}

						// Fade out and remove row
						$itemRow.fadeOut(300, function () {
							$(this).remove();
						});

						// Update count badge
						if (res.data.cart_count !== undefined) {
							$('.fb-cart-count-badge').text(res.data.cart_count + ' قلم کالا');
						}

						// Update Totals sidebar card
						if (res.data.cart_totals_html) {
							$('.fb-cart-collaterals').html(res.data.cart_totals_html);
						}

						// Sync Header Mini-Cart
						$(document.body).trigger('wc_fragment_refresh');

						// Update trash icons state and mobile bar price
						updateCartTrashIcons();
						syncMobileCartTotal();
						$(document.body).trigger('updated_cart_totals');
					} else {
						window.location.href = $btn.attr('href');
					}
				},
				error: function () {
					window.location.href = $btn.attr('href');
				},
				complete: function () {
					isUpdating = false;
				}
			});
		});

		// 3. Coupon Code Box Toggle
		$(document).on('click', '#fb_cart_coupon_toggle, .fb-coupon-toggle-btn', function (e) {
			e.preventDefault();
			var $form = $('#fb_cart_coupon_box');
			if (!$form.length) {
				$form = $('.fb-coupon-form');
			}
			$form.slideToggle(200, function () {
				if ($form.is(':visible')) {
					$form.find('.fb-coupon-input').focus();
				}
			});
		});

		// 4. Coupon Code Submission
		$(document).on('keypress', '#fb_cart_coupon_code', function (e) {
			if (e.which === 13) {
				e.preventDefault();
				$('#fb_cart_apply_coupon_btn').trigger('click');
			}
		});

		$(document).on('click', '#fb_cart_apply_coupon_btn', function (e) {
			e.preventDefault();
			var $input = $('#fb_cart_coupon_code');
			var couponCode = $.trim($input.val());
			var $msg = $('#fb_cart_coupon_msg');

			if (!couponCode) {
				$msg.css({
					'color': '#ef4444',
					'margin-top': '8px',
					'font-size': '12.5px',
					'display': 'block',
					'font-weight': '600'
				}).text('لطفاً کد تخفیف را وارد کنید.');
				$input.focus();
				return;
			}

			var $btn = $(this);
			$btn.prop('disabled', true).text('در حال بررسی...');
			$msg.text('').hide();

			var endpointUrl = fb_cart_params.wc_ajax_url
				? fb_cart_params.wc_ajax_url.replace('%%endpoint%%', 'apply_coupon')
				: '?wc-ajax=apply_coupon';

			$.ajax({
				url: endpointUrl,
				type: 'POST',
				data: {
					security: fb_cart_params.apply_coupon_nonce,
					coupon_code: couponCode
				},
				dataType: 'html',
				success: function (response) {
					var $temp = $('<div>').html(response);
					var $error = $temp.find('.woocommerce-error');
					var hasError = $error.length > 0 || (typeof response === 'string' && response.indexOf('woocommerce-error') !== -1);

					if (hasError) {
						var errorMsg = $error.text().trim();
						if (!errorMsg || errorMsg.indexOf('وجود ندارد') !== -1 || errorMsg.indexOf('does not exist') !== -1) {
							errorMsg = 'این کد تخفیف معتبر نیست';
						} else {
							errorMsg = errorMsg.replace(/محدودیت استفاده/g, 'مهلت استفاده')
							                   .replace(/محدودیت/g, 'مهلت')
							                   .replace(/کوپن/g, 'کد تخفیف');
						}
						$msg.css({
							'color': '#ef4444',
							'margin-top': '8px',
							'font-size': '12.5px',
							'display': 'block',
							'font-weight': '600'
						}).text(errorMsg);
						$btn.prop('disabled', false).text('اعمال کد');
						$input.focus();
					} else {
						var $success = $temp.find('.woocommerce-message');
						var successMsg = $success.length ? $success.text().trim() : 'کد تخفیف با موفقیت اعمال شد.';
						successMsg = successMsg.replace(/کوپن/g, 'کد تخفیف');
						$msg.css({
							'color': '#10b981',
							'margin-top': '8px',
							'font-size': '12.5px',
							'display': 'block',
							'font-weight': '600'
						}).text(successMsg);
						setTimeout(function () {
							window.location.reload();
						}, 600);
					}
				},
				error: function () {
					$msg.css({
						'color': '#ef4444',
						'margin-top': '8px',
						'font-size': '12.5px',
						'display': 'block',
						'font-weight': '600'
					}).text('این کد تخفیف معتبر نیست');
					$btn.prop('disabled', false).text('اعمال کد');
				}
			});
		});

		// 4. Proceed to Checkout button loading trigger & BFCache Reset
		function resetCheckoutButtons() {
			$('.fb-proceed-checkout-btn, .checkout-button, .fb-mobile-cart-btn, .wc-proceed-to-checkout a').removeClass('fb-loading');
		}

		// Reset loading state when navigating back via browser history (BFCache)
		$(window).on('pageshow', function (event) {
			resetCheckoutButtons();
		});

		window.addEventListener('pageshow', function (event) {
			resetCheckoutButtons();
		});

		window.addEventListener('pagehide', function () {
			resetCheckoutButtons();
		});

		$(document).on('click', '.fb-proceed-checkout-btn, .checkout-button, .fb-mobile-cart-btn, .wc-proceed-to-checkout a', function () {
			var $btn = $(this);
			$btn.addClass('fb-loading');
			if (!$btn.find('.fb-btn-loader').length) {
				$btn.append('<span class="fb-btn-loader" aria-hidden="true"></span>');
			}
		});

	});

})(jQuery);
