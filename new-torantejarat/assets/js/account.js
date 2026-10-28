/**
 * Account panel — AJAX tabs, wishlist toggle (REST), toasts.
 */
( function () {
	'use strict';

	var panel = document.getElementById( 'acp-panel' );
	var content = document.getElementById( 'acp-content' );

	var toastEl = document.getElementById( 'acp-toast' );
	var toastTimer;

	function toast( msg ) {
		if ( ! toastEl ) {
			return;
		}
		toastEl.textContent = msg;
		toastEl.classList.add( 'show' );
		clearTimeout( toastTimer );
		toastTimer = setTimeout( function () {
			toastEl.classList.remove( 'show' );
		}, 2600 );
	}

	/* ---------- More sheet ---------- */
	var sheet = document.getElementById( 'acp-sheet' );
	var scrim = document.querySelector( '.acp-scrim' );

	function openSheet() {
		if ( ! sheet ) {
			return;
		}
		sheet.hidden = false;
		if ( scrim ) {
			scrim.hidden = false;
		}
	}

	function closeSheet() {
		if ( sheet ) {
			sheet.hidden = true;
		}
		if ( scrim ) {
			scrim.hidden = true;
		}
	}

	document.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-sheet-open]' ) ) {
			openSheet();
			return;
		}
		if ( e.target.closest( '[data-sheet-close]' ) || ( sheet && ! sheet.hidden && e.target.closest( '.acp-sheet a' ) ) ) {
			closeSheet();
		}
	} );

	document.addEventListener( 'keydown', function ( e ) {
		if ( e.key === 'Escape' ) {
			closeSheet();
			closeConfirm();
		}
	} );

	/* ---------- Confirm dialog ---------- */
	var confirmBox = document.getElementById( 'acp-confirm' );
	var confirmTitle = document.getElementById( 'acp-confirm-title' );
	var confirmText = document.getElementById( 'acp-confirm-text' );
	var confirmYes = document.getElementById( 'acp-confirm-yes' );
	var confirmCb = null;

	function closeConfirm() {
		if ( confirmBox ) {
			confirmBox.hidden = true;
		}
		confirmCb = null;
	}

	function openConfirm( title, text, href, onYes ) {
		if ( confirmTitle ) {
			confirmTitle.textContent = title;
		}
		if ( confirmText ) {
			confirmText.textContent = text;
		}
		if ( confirmYes ) {
			if ( href ) {
				confirmYes.setAttribute( 'href', href );
			} else {
				confirmYes.removeAttribute( 'href' );
			}
			confirmYes.style.display = ( href || typeof onYes === 'function' ) ? '' : 'none';
		}
		confirmCb = ( typeof onYes === 'function' ) ? onYes : null;
		closeSheet();
		if ( confirmBox ) {
			confirmBox.hidden = false;
		}
	}

	if ( confirmYes ) {
		confirmYes.addEventListener( 'click', function ( e ) {
			if ( confirmCb ) {
				e.preventDefault();
				var cb = confirmCb;
				closeConfirm();
				cb();
			}
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-confirm-no]' ) ) {
			closeConfirm();
			return;
		}
		var el = e.target.closest( '[data-confirm]' );
		if ( ! el ) {
			return;
		}
		e.preventDefault();
		var form = ( 'SUBMIT' === el.type || 'submit' === el.type ) ? el.closest( 'form' ) : null;
		openConfirm(
			el.textContent.trim().slice( 0, 60 ),
			el.dataset.confirm,
			form ? null : el.getAttribute( 'href' ),
			form ? ( function () { form.submit(); } ) : null
		);
	} );

	/* ---------- AJAX tabs ---------- */
	function markActive( url ) {
		document.querySelectorAll( '.acp__nav a, .acp-tabs a' ).forEach( function ( a ) {
			if ( a.href === url || a.getAttribute( 'href' ) === url ) {
				a.setAttribute( 'aria-current', 'page' );
				var li = a.closest( 'li' );
				if ( li ) {
					li.classList.add( 'is-active' );
				}
			} else {
				a.removeAttribute( 'aria-current' );
				var li2 = a.closest( 'li' );
				if ( li2 ) {
					li2.classList.remove( 'is-active' );
				}
			}
		} );
	}

	function loadTab( url, push ) {
		if ( ! panel || ! content ) {
			window.location.href = url;
			return;
		}
		content.setAttribute( 'aria-busy', 'true' );
		content.innerHTML = '<div class="acp-skel" aria-hidden="true"><div class="acp-skeleton" style="height:22px;width:40%"></div><div class="acp-skeleton"></div><div class="acp-skeleton"></div><div class="acp-skeleton" style="width:70%"></div></div>';
		fetch( url, {
			method: 'GET',
			credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		} ).then( function ( res ) {
			if ( ! res.ok ) {
				throw new Error( 'http' );
			}
			return res.text();
		} ).then( function ( html ) {
			var doc = new DOMParser().parseFromString( html, 'text/html' );
			var next = doc.getElementById( 'acp-content' );
			if ( ! next ) {
				window.location.href = url;
				return;
			}
			content.innerHTML = next.innerHTML;
			var title = doc.querySelector( 'title' );
			if ( title ) {
				document.title = title.textContent;
			}
			markActive( url );
			if ( push !== false ) {
				history.pushState( { acp: true }, '', url );
			}
			content.removeAttribute( 'aria-busy' );
			panel.scrollIntoView( { block: 'start' } );
		} ).catch( function () {
			content.removeAttribute( 'aria-busy' );
			window.location.href = url;
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		var a = e.target.closest( '.acp__nav a, .acp-tabs a' );
		if ( ! a ) {
			return;
		}
		var url = a.href;
		if ( ! url || a.target === '_blank' || e.metaKey || e.ctrlKey || e.shiftKey ) {
			return;
		}
		var same = false;
		try {
			same = new URL( url ).origin === window.location.origin;
		} catch ( err ) {
			return;
		}
		if ( ! same ) {
			return;
		}
		/* Logout must run as a full request. */
		if ( /customer-logout|_wpnonce/.test( url ) ) {
			return;
		}
		e.preventDefault();
		if ( url === window.location.href ) {
			return;
		}
		loadTab( url, true );
	} );

	window.addEventListener( 'popstate', function () {
		loadTab( window.location.href, false );
	} );

	/* ---------- Wishlist remove (REST + native form fallback) ---------- */
	function removeWishlistItem( btn ) {
		btn.disabled = true;
		var body = new URLSearchParams();
		body.set( 'product_id', btn.dataset.removeWish || '' );
		fetch( window.torantejaratAccount.restToggle, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-WP-Nonce': window.torantejaratAccount.restNonce
			},
			body: body.toString()
		} ).then( function ( response ) {
			return response.json().then( function ( data ) {
				if ( ! response.ok ) {
					throw new Error( 'request' );
				}
				return data;
			} );
		} ).then( function ( data ) {
			if ( ! data || data.wishlisted !== false ) {
				throw new Error( 'response' );
			}
			var card = btn.closest( '.acp-wish' );
			if ( card ) {
				card.remove();
			}
			toast( 'از علاقه‌مندی‌ها حذف شد.' );
			if ( ! document.querySelector( '#wish-grid .acp-wish' ) ) {
				var empty = document.getElementById( 'wish-empty' );
				if ( empty ) {
					empty.hidden = false;
				}
			}
		} ).catch( function () {
			btn.disabled = false;
			toast( 'حذف انجام نشد. دوباره تلاش کنید.' );
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-remove-wish]' );
		if ( ! btn || ! confirmBox || ! window.torantejaratAccount || ! window.torantejaratAccount.restNonce ) {
			return;
		}
		e.preventDefault();
		openConfirm(
			'حذف از علاقه‌مندی‌ها',
			'این محصول از فهرست علاقه‌مندی‌های شما حذف می‌شود.',
			null,
			function () {
				removeWishlistItem( btn );
			}
		);
	} );

	/* ---------- Toggle ticket threads ---------- */
	document.addEventListener( 'click', function ( e ) {
		var btn = e.target.closest( '[data-toggle-ticket]' );
		if ( ! btn ) {
			return;
		}
		var panel = document.getElementById( btn.dataset.toggleTicket );
		if ( ! panel ) {
			return;
		}
		var open = panel.hidden;
		panel.hidden = ! open;
		btn.setAttribute( 'aria-expanded', String( open ) );
	} );

	/* ---------- Ticket form validation ---------- */
	var ticketForm = document.getElementById( 'acp-ticket-form' );
	if ( ticketForm ) {
		ticketForm.addEventListener( 'submit', function ( e ) {
			var msg = document.getElementById( 'acp-tk-msg' );
			var err = ticketForm.querySelector( '.acp-err' );
			if ( msg && msg.value.trim().length < 10 ) {
				e.preventDefault();
				if ( err ) {
					err.textContent = 'متن پیام حداقل ۱۰ کاراکتر باشد.';
					err.hidden = false;
				}
				msg.focus();
			}
		} );
	}
} )();
