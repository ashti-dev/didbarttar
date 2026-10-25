/* Wishlist progressive enhancement: native POST fallback + authenticated REST. */
( function () {
	'use strict';

	document.addEventListener( 'submit', function ( event ) {
		var form = event.target;
		if ( ! form.matches( '[data-wishlist-form]' ) || ! window.torantejaratWishlist || ! window.torantejaratWishlist.restNonce ) {
			return;
		}

		var button = form.querySelector( '[data-wishlist-toggle]' );
		var productInput = form.querySelector( 'input[name="product_id"]' );
		var productId = productInput ? productInput.value : '';
		if ( ! button || ! productId ) {
			return;
		}

		event.preventDefault();
		if ( form.getAttribute( 'aria-busy' ) === 'true' ) {
			return;
		}

		var label = button.querySelector( '[data-wishlist-label]' );
		var message = form.querySelector( '[data-wishlist-message]' );
		var body = new URLSearchParams();
		body.set( 'product_id', productId );
		form.setAttribute( 'aria-busy', 'true' );
		button.disabled = true;
		if ( message ) {
			message.textContent = '';
			message.classList.remove( 'is-error' );
		}

		fetch( window.torantejaratWishlist.restToggle, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-WP-Nonce': window.torantejaratWishlist.restNonce
			},
			body: body.toString()
		} ).then( function ( response ) {
			return response.json().then( function ( data ) {
				if ( ! response.ok ) {
					throw new Error( data && data.message ? data.message : 'درخواست انجام نشد.' );
				}
				return data;
			} );
		} ).then( function ( data ) {
			if ( ! data || typeof data.wishlisted !== 'boolean' ) {
				throw new Error( 'پاسخ سرور معتبر نیست.' );
			}
			var active = data.wishlisted;
			form.dataset.wishlisted = String( active );
			button.setAttribute( 'aria-pressed', String( active ) );
			button.classList.toggle( 'is-active', active );
			if ( label ) {
				label.textContent = active ? 'حذف از علاقه‌مندی‌ها' : 'افزودن به علاقه‌مندی‌ها';
			}
			if ( message ) {
				message.textContent = active ? 'به علاقه‌مندی‌ها اضافه شد.' : 'از علاقه‌مندی‌ها حذف شد.';
			}
		} ).catch( function ( error ) {
			if ( message ) {
				message.textContent = error && error.message ? error.message : 'خطا در ارتباط با سرور. دوباره تلاش کنید.';
				message.classList.add( 'is-error' );
			}
		} ).finally( function () {
			button.disabled = false;
			form.removeAttribute( 'aria-busy' );
		} );
	} );
} )();
