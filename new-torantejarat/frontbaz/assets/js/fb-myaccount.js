/**
 * FrontBaz My Account Scripts
 * Confirmation dialogs, interactive tabs and account utilities.
 */
(function ($) {
	'use strict';

	function scrollActiveNavIntoView(smooth) {
		var $navList = $('.fb-nav-list');
		if (!$navList.length) {
			return;
		}

		// Look for active item (.fb-active or .is-active)
		var $activeItem = $navList.find('.fb-nav-item.fb-active, .fb-nav-item.is-active, .fb-nav-item.active').first();
		if (!$activeItem.length) {
			$activeItem = $navList.find('li.is-active, li.active').first();
		}

		if ($activeItem.length && $activeItem[0]) {
			var activeEl = $activeItem[0];
			if (typeof activeEl.scrollIntoView === 'function') {
				try {
					activeEl.scrollIntoView({
						behavior: smooth ? 'smooth' : 'auto',
						inline: 'center',
						block: 'nearest'
					});
				} catch (e) {
					activeEl.scrollIntoView(false);
				}
			}
		}
	}

	$(document).ready(function () {
		// Confirm Logout on My Account navigation or any logout links
		$(document).on('click', '.woocommerce-MyAccount-navigation-link--customer-logout a, a[href*="customer-logout"], a[href*="action=logout"]', function (e) {
			var confirmMsg = 'آیا از خروج از حساب کاربری خود اطمینان دارید؟';
			if (!window.confirm(confirmMsg)) {
				e.preventDefault();
				e.stopImmediatePropagation();
				return false;
			}
		});

		// Ensure active tab is visible in viewport on mobile horizontal navigation
		scrollActiveNavIntoView(false);
		setTimeout(function () {
			scrollActiveNavIntoView(false);
		}, 120);
	});

	$(window).on('load', function () {
		scrollActiveNavIntoView(false);
	});

})(jQuery);
