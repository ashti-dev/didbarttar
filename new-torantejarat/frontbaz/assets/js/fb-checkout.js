/**
 * FrontBaz Checkout & Interactive Cart Scripts
 * Handled within an IIFE and modular scope.
 */
(function ($) {
	'use strict';

	var fbCheckout = {
		isUpdating: false,

		init: function () {
			this.initItemsAccordion();
			this.initQtyControls();
			this.initPaymentSelection();
			this.initShippingSelection();
			this.initCouponToggle();
			this.initSubmitSync();
			this.bindWooCommerceEvents();
			this.updateTrashIcons();
			this.formatPlaceOrderButton();
		},

		/**
		 * Collapsible Order Items Accordion
		 */
		initItemsAccordion: function () {
			var self = this;
			var isOpen = sessionStorage.getItem('fb_checkout_items_open') === 'true';

			self.applyAccordionState(isOpen);

			$(document).on('click', '.fb-summary-accordion-toggle', function (e) {
				e.preventDefault();
				var $header = $(this);
				var $list = $('.fb-order-items-list.fb-accordion-content');
				var currentlyOpen = $header.hasClass('is-open');
				var nextState = !currentlyOpen;

				sessionStorage.setItem('fb_checkout_items_open', nextState ? 'true' : 'false');

				if (nextState) {
					$header.addClass('is-open').attr('aria-expanded', 'true');
					$header.find('.fb-toggle-label').text('بستن اقلام');
					$list.slideDown(220).addClass('is-open');
				} else {
					$header.removeClass('is-open').attr('aria-expanded', 'false');
					$header.find('.fb-toggle-label').text('مشاهده اقلام');
					$list.slideUp(220).removeClass('is-open');
				}
			});
		},

		applyAccordionState: function (isOpen) {
			var $header = $('.fb-summary-accordion-toggle');
			var $list = $('.fb-order-items-list.fb-accordion-content');

			if (isOpen) {
				$header.addClass('is-open').attr('aria-expanded', 'true');
				$header.find('.fb-toggle-label').text('بستن اقلام');
				$list.show().addClass('is-open');
			} else {
				$header.removeClass('is-open').attr('aria-expanded', 'false');
				$header.find('.fb-toggle-label').text('مشاهده اقلام');
				$list.hide().removeClass('is-open');
			}
		},

		/**
		 * Dynamically update trash icon for quantity 1 and disable plus button if stock limit reached
		 */
		updateTrashIcons: function () {
			var trashSvg = '<svg class="fb-trash-icon" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path><line x1="10" y1="11" x2="10" y2="17"></line><line x1="14" y1="11" x2="14" y2="17"></line></svg>';

			$('.fb-order-item').each(function () {
				var $card = $(this);
				var $control = $card.find('.fb-qty-control');
				var maxQty = parseInt($control.attr('data-max-qty') || $control.data('max-qty'), 10) || 0;
				var qty = parseInt($card.find('.fb-qty-val').val(), 10) || 1;
				var $minusBtn = $card.find('.fb-qty-minus');
				var $plusBtn = $card.find('.fb-qty-plus');

				// Minus button: trash vs minus
				if (qty <= 1) {
					$minusBtn.addClass('is-trash').attr('title', 'حذف محصول از سبد خرید').html(trashSvg);
				} else {
					$minusBtn.removeClass('is-trash').attr('title', 'کاهش تعداد').text('−');
				}

				// Plus button: stock limit check
				if (maxQty > 0 && qty >= maxQty) {
					$plusBtn.addClass('is-disabled').prop('disabled', true).attr('title', 'حداکثر موجودی در انبار (' + maxQty + ' عدد)');
				} else {
					$plusBtn.removeClass('is-disabled').prop('disabled', false).attr('title', 'افزایش تعداد');
				}
			});
		},

		/**
		 * Quantity increment/decrement module
		 */
		initQtyControls: function () {
			var self = this;

			$(document).on('click', '.fb-qty-btn', function (e) {
				e.preventDefault();

				if (self.isUpdating) {
					return;
				}

				var $button = $(this);
				$button.blur();
				if ($button.hasClass('is-disabled') || $button.prop('disabled')) {
					return;
				}

				var $card = $button.closest('.fb-order-item');
				var $control = $button.closest('.fb-qty-control');
				var maxQty = parseInt($control.attr('data-max-qty') || $control.data('max-qty'), 10) || 0;
				var cartItemKey = $card.data('cart-item-key');
				var $valInput = $card.find('.fb-qty-val');
				var currentQty = parseInt($valInput.val(), 10) || 1;
				var isPlus = $button.hasClass('fb-qty-plus');

				if (isPlus && maxQty > 0 && currentQty >= maxQty) {
					return;
				}

				var newQty = isPlus ? currentQty + 1 : currentQty - 1;

				if (newQty < 0) {
					newQty = 0;
				}

				// If decreasing to 0, confirm with user
				if (newQty === 0 && !confirm('آیا از حذف این محصول از سبد خرید اطمینان دارید؟')) {
					return;
				}

				self.updateCartQty(cartItemKey, newQty, $card);
			});
		},

		/**
		 * Send AJAX request to update cart quantity
		 */
		updateCartQty: function (cartItemKey, newQty, $card) {
			var self = this;
			self.isUpdating = true;

			if ($card && $card.length) {
				$card.addClass('fb-loading');
			}
			$('.fb-order-summary-card').addClass('fb-loading');

			$.ajax({
				type: 'POST',
				url: fb_checkout_params.ajax_url,
				data: {
					action: 'fb_ajax_update_checkout_qty',
					security: fb_checkout_params.nonce,
					cart_item_key: cartItemKey,
					quantity: newQty
				},
				success: function (response) {
					if (response && response.success) {
						if (response.data && response.data.is_empty) {
							window.location.reload();
							return;
						}
						// Trigger WooCommerce standard checkout update
						$(document.body).trigger('update_checkout');
						// Trigger fragment refresh so header mini-cart and badge stay 100% in sync
						$(document.body).trigger('wc_fragment_refresh');
					} else {
						var errorMsg = (response && response.data && response.data.message) 
							? response.data.message 
							: 'خطایی در تغییر تعداد رخ داد.';
						alert(errorMsg);
						if (response && response.data && response.data.current_qty) {
							$card.find('.fb-qty-val').val(response.data.current_qty);
						}
						self.isUpdating = false;
						$('.fb-loading').removeClass('fb-loading');
						self.updateTrashIcons();
					}
				},
				error: function () {
					alert('خطای اتصال به سرور. لطفاً مجدداً تلاش فرمایید.');
					self.isUpdating = false;
					$('.fb-loading').removeClass('fb-loading');
				}
			});
		},

		/**
		 * Payment method selection cards
		 */
		initPaymentSelection: function () {
			$(document).on('click', '.fb-payment-gateway-item', function (e) {
				// Don't trigger if clicked directly on input or inside active payment_box or label
				if ($(e.target).is('input[type="radio"]') || $(e.target).closest('label').length || $(e.target).closest('.payment_box').length) {
					return;
				}

				var $radio = $(this).find('input[type="radio"]');
				if ($radio.length && !$radio.is(':checked')) {
					$radio.prop('checked', true).trigger('click').trigger('change');
				}
			});

			$(document).on('change', 'input[name="payment_method"]', function () {
				$('.fb-payment-gateway-item').removeClass('fb-active');
				$(this).closest('.fb-payment-gateway-item').addClass('fb-active');
			});
		},

		/**
		 * Sync shipping method selection state (active class)
		 */
		syncShippingSelection: function () {
			$('ul.woocommerce-shipping-methods, ul#shipping_method').each(function () {
				var $ul = $(this);
				var $lis = $ul.find('> li');
				$lis.removeClass('fb-active fb-single-shipping');

				var $checkedRadio = $lis.find('input[type="radio"].shipping_method:checked');
				if ($checkedRadio.length) {
					$checkedRadio.closest('li').addClass('fb-active');
				} else if ($lis.length === 1) {
					// Single shipping method (inherently chosen by WooCommerce)
					$lis.first().addClass('fb-active fb-single-shipping');
				} else {
					// Fallback when WooCommerce renders input[type="hidden"]
					var $hidden = $lis.find('input[type="hidden"].shipping_method');
					if ($hidden.length) {
						$hidden.closest('li').addClass('fb-active fb-single-shipping');
					}
				}
			});
		},

		/**
		 * Shipping method selection cards (matching payment cards)
		 */
		initShippingSelection: function () {
			var self = this;

			$(document).on('click', 'ul.woocommerce-shipping-methods li, ul#shipping_method li', function (e) {
				if ($(e.target).is('input[type="radio"]') || $(e.target).closest('.shipping_method_description').length) {
					return;
				}

				var $radio = $(this).find('input[type="radio"].shipping_method');
				if ($radio.length && !$radio.is(':checked')) {
					$radio.prop('checked', true).trigger('change');
				}
			});

			$(document).on('change', 'input.shipping_method', function () {
				self.syncShippingSelection();
			});

			// Initial state
			self.syncShippingSelection();
		},

		/**
		 * Coupon toggle & AJAX application
		 */
		initCouponToggle: function () {
			$(document).on('click', '.fb-coupon-toggle', function (e) {
				e.preventDefault();
				var $form = $('.fb-coupon-form');
				$form.slideToggle(200, function () {
					if ($form.is(':visible')) {
						$form.find('.fb-coupon-input').focus();
					}
				});
			});

			$(document).on('keypress', '.fb-coupon-input', function (e) {
				if (e.which === 13) {
					e.preventDefault();
					$(this).closest('.fb-coupon-form').find('.fb-coupon-btn').trigger('click');
				}
			});

			$(document).on('click', '.fb-coupon-btn', function (e) {
				e.preventDefault();
				var $btn = $(this);
				var $form = $btn.closest('.fb-coupon-form');
				var $input = $form.find('.fb-coupon-input');
				var $msg = $form.find('.fb-coupon-message');
				if (!$msg.length) {
					$msg = $('<div class="fb-coupon-message"></div>').appendTo($form);
				}
				var couponCode = $.trim($input.val());

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

				$btn.prop('disabled', true).addClass('fb-loading');
				$msg.text('').hide();

				$.ajax({
					type: 'POST',
					url: fb_checkout_params.wc_ajax_url.replace('%%endpoint%%', 'apply_coupon'),
					data: {
						security: fb_checkout_params.apply_coupon_nonce,
						coupon_code: couponCode
					},
					success: function (response) {
						$btn.prop('disabled', false).removeClass('fb-loading');
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
								$form.slideUp();
								$input.val('');
								$msg.text('').hide();
								$(document.body).trigger('update_checkout');
							}, 600);
						}
					},
					error: function () {
						$btn.prop('disabled', false).removeClass('fb-loading');
						$msg.css({
							'color': '#ef4444',
							'margin-top': '8px',
							'font-size': '12.5px',
							'display': 'block',
							'font-weight': '600'
						}).text('این کد تخفیف معتبر نیست');
						$input.focus();
					}
				});
			});

			// Remove coupon via AJAX
			$(document).on('click', '.fb-remove-coupon', function (e) {
				e.preventDefault();
				var couponCode = $(this).data('coupon');

				$.ajax({
					type: 'POST',
					url: fb_checkout_params.wc_ajax_url.replace('%%endpoint%%', 'remove_coupon'),
					data: {
						security: fb_checkout_params.remove_coupon_nonce,
						coupon: couponCode
					},
					success: function () {
						$(document.body).trigger('update_checkout');
					}
				});
			});
		},

		/**
		 * Forward click from mobile fixed bar button directly to WooCommerce #place_order button
		 */
		initSubmitSync: function () {
			// When mobile fixed button is clicked, trigger native click on #place_order
			$(document).on('click', '#fb_mobile_place_order_btn', function (e) {
				e.preventDefault();
				e.stopPropagation();

				var $mobileBtn = $(this);
				if ($mobileBtn.hasClass('fb-loading') || $mobileBtn.prop('disabled')) {
					return false;
				}

				var placeOrderEl = document.getElementById('place_order');
				if (placeOrderEl) {
					// Use native DOM click so the browser and WooCommerce process it exactly as if #place_order was tapped
					placeOrderEl.click();
				} else {
					$('form.checkout').trigger('submit');
				}
			});

			// Prevent duplicate clicks on #place_order when already loading
			$(document).on('click', '#place_order', function (e) {
				var $btn = $(this);
				if ($btn.hasClass('fb-loading') || $btn.prop('disabled')) {
					e.preventDefault();
					return false;
				}
			});
		},

		/**
		 * Listen for WooCommerce checkout update events
		 */
		bindWooCommerceEvents: function () {
			var self = this;

			$(document.body).on('updated_checkout', function () {
				self.isUpdating = false;
				$('.fb-checkout-submit-btn, #place_order, #fb_mobile_place_order_btn').prop('disabled', false).removeClass('fb-loading');

				// Highlight currently selected payment method
				var checkedMethod = $('input[name="payment_method"]:checked').val();
				if (checkedMethod) {
					$('.fb-payment-gateway-item').removeClass('fb-active');
					$('.payment_method_' + checkedMethod).addClass('fb-active');
				}

				// Highlight currently selected shipping method
				self.syncShippingSelection();

				// Update amount in custom Pay button and mobile bar
				self.formatPlaceOrderButton();
				setTimeout(function () {
					self.formatPlaceOrderButton();
				}, 60);

				// Sync header mini-cart fragments
				$(document.body).trigger('wc_fragment_refresh');

				// Maintain accordion open/closed state upon checkout fragment re-render
				var isOpen = sessionStorage.getItem('fb_checkout_items_open') === 'true';
				self.applyAccordionState(isOpen);

				// Update trash icons for items with quantity 1
				self.updateTrashIcons();

				// Ensure phone field requirement consistency (prevent address-i18n from stripping required star)
				var $phoneField = $('#billing_phone_field');
				if ($phoneField.length) {
					$phoneField.addClass('validate-required');
					$phoneField.find('label .optional').remove();
					if ($phoneField.find('label .required').length === 0) {
						$phoneField.find('label').append('&nbsp;<span class="required" aria-hidden="true">*</span>');
					}
				}
			});

			$(document.body).on('country_to_state_changing country_to_state_changed', function () {
				setTimeout(function () {
					var $phoneField = $('#billing_phone_field');
					if ($phoneField.length) {
						$phoneField.addClass('validate-required');
						$phoneField.find('label .optional').remove();
						if ($phoneField.find('label .required').length === 0) {
							$phoneField.find('label').append('&nbsp;<span class="required" aria-hidden="true">*</span>');
						}
					}
				}, 20);
			});

			$(document.body).on('payment_method_selected', function () {
				setTimeout(function () {
					self.formatPlaceOrderButton();
				}, 30);
			});

			$(document).on('change', 'input[name="payment_method"]', function () {
				setTimeout(function () {
					self.formatPlaceOrderButton();
				}, 30);
			});

			$(document.body).on('checkout_place_order', function () {
				$('.fb-checkout-submit-btn, #place_order, #fb_mobile_place_order_btn').addClass('fb-loading');
			});

			$(document.body).on('checkout_error', function () {
				self.isUpdating = false;
				$('.fb-checkout-submit-btn, #place_order, #fb_mobile_place_order_btn').prop('disabled', false).removeClass('fb-loading');
				self.formatPlaceOrderButton();
			});

			// Reset loading state on Back-Forward Cache (BFCache) restoration
			window.addEventListener('pageshow', function () {
				self.isUpdating = false;
				$('.fb-loading').removeClass('fb-loading');
				$('#fb_mobile_place_order_btn, #place_order, .fb-checkout-submit-btn').prop('disabled', false).removeClass('fb-loading');
				self.formatPlaceOrderButton();
			});

			window.addEventListener('pagehide', function () {
				$('.fb-loading').removeClass('fb-loading');
				$('#fb_mobile_place_order_btn, #place_order, .fb-checkout-submit-btn').prop('disabled', false).removeClass('fb-loading');
			});
		},

		/**
		 * Format the Place Order button and mobile bottom bar to always include the total price
		 */
		formatPlaceOrderButton: function () {
			var totalHtml = '';
			var $currentTotal = $('.fb-breakdown-total .fb-total-current, .order-total .fb-total-current, .fb-order-total-value-main .fb-total-current');
			if ($currentTotal.length) {
				totalHtml = $currentTotal.first().html() || '';
			} else {
				var $orderTotal = $('.fb-breakdown-total .woocommerce-Price-amount, .order-total .woocommerce-Price-amount');
				if ($orderTotal.length) {
					totalHtml = $orderTotal.last().html() || '';
				}
			}

			if (!totalHtml) return;

			// Clean any del tags or old prices if accidentally present
			var $temp = $('<div>').html(totalHtml);
			$temp.find('del, .fb-total-old').remove();
			totalHtml = $temp.html().trim();

			// 1. Update the fixed mobile bottom bar amount
			$('.fb-mobile-bar-price-amount, .fb-mobile-place-order-amount').html(totalHtml);

			// 2. Update the main desktop submit button (#place_order)
			var $btn = $('#place_order');
			if ($btn.length) {
				var $amountContainer = $btn.find('.fb-checkout-submit-amount');
				if ($amountContainer.length) {
					$amountContainer.html(totalHtml);
				} else {
					var btnText = 'پرداخت';
					if ($btn.find('.fb-btn-text').length) {
						btnText = $btn.find('.fb-btn-text').text().trim();
					}
					$btn.html('<span class="fb-btn-text">' + btnText + '</span> <span class="fb-btn-amount">(<span class="fb-checkout-submit-amount">' + totalHtml + '</span>)</span><span class="fb-btn-loader" aria-hidden="true"></span>');
				}
			}
		}
	};

	$(document).ready(function () {
		if ($('form.checkout').length) {
			fbCheckout.init();
			// Ensure header mini-cart is freshly synced on page load
			$(document.body).trigger('wc_fragment_refresh');
		}
	});

})(jQuery);
