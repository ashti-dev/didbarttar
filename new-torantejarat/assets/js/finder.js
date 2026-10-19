/**
 * Homepage finder — three dropdowns, live AJAX results in a separate section.
 * Read-only endpoint with nonce. Debounced, abortable, ordered responses.
 */
( function () {
	'use strict';

	var box = document.querySelector( '[data-finder]' );
	if ( ! box ) {
		return;
	}

	var typeSel = box.querySelector( 'select[name="fcat"]' );
	var lenSel = box.querySelector( 'select[name="flen"]' );
	var bandSel = box.querySelector( 'select[name="fband"]' );
	var results = box.querySelector( '[data-finder-results]' );
	var resultsWrap = box.querySelector( '[data-finder-results-wrap]' );
	var resultCount = box.querySelector( '[data-finder-result-count]' );
	var moreLink = box.querySelector( '[data-finder-shop-link]' );
	var resetBtn = box.querySelector( '[data-finder-reset]' );
	var applyBtn = box.querySelector( '[data-finder-apply]' );
	var countEl = box.querySelector( '[data-finder-count]' );
	var seq = 0;
	var timer = null;
	var controller = null;
	var REDUCED = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	function faNum( value ) {
		return String( value ).replace( /\d/g, function ( digit ) {
			return '۰۱۲۳۴۵۶۷۸۹'.charAt( parseInt( digit, 10 ) );
		} );
	}

	function selected() {
		return {
			cats: typeSel && typeSel.value ? [ typeSel.value ] : [],
			lengths: lenSel && lenSel.value ? [ lenSel.value ] : [],
			band: bandSel && bandSel.value ? parseInt( bandSel.value, 10 ) || 0 : 0
		};
	}

	function activeCount( sel ) {
		return ( sel.cats.length ? 1 : 0 ) + ( sel.lengths.length ? 1 : 0 ) + ( sel.band ? 1 : 0 );
	}

	function hasSelection( sel ) {
		return activeCount( sel ) > 0;
	}

	function syncUrl( sel ) {
		/* Keep unrelated query params (utm, etc.); only own the three finder keys. */
		var params = new URLSearchParams( window.location.search );
		params.delete( 'fcat' );
		params.delete( 'flen' );
		params.delete( 'fband' );
		if ( sel.cats.length ) {
			params.set( 'fcat', sel.cats.join( ',' ) );
		}
		if ( sel.lengths.length ) {
			params.set( 'flen', sel.lengths.join( ',' ) );
		}
		if ( sel.band ) {
			params.set( 'fband', String( sel.band ) );
		}
		var query = params.toString();
		history.replaceState( { finder: true }, '', window.location.pathname + ( query ? '?' + query : '' ) );
	}

	function applyFromUrl() {
		var params = new URLSearchParams( window.location.search );
		var touched = false;
		var map = [
			[ typeSel, params.get( 'fcat' ) || '' ],
			[ lenSel, params.get( 'flen' ) || '' ],
			[ bandSel, params.get( 'fband' ) || '' ]
		];
		map.forEach( function ( pair ) {
			var sel = pair[ 0 ];
			var val = pair[ 1 ];
			if ( ! sel ) {
				return;
			}
			sel.value = '';
			if ( ! val ) {
				return;
			}
			var first = val.split( ',' )[ 0 ];
			var valid = false;
			Array.prototype.forEach.call( sel.options, function ( opt ) {
				if ( opt.value === first ) {
					valid = true;
				}
			} );
			if ( valid ) {
				sel.value = first;
				touched = true;
			}
		} );
		return touched;
	}

	function setActiveCount( sel ) {
		if ( ! countEl ) {
			return;
		}
		var n = activeCount( sel );
		countEl.textContent = n ? faNum( n ) + ' فیلتر فعال' : 'بدون فیلتر';
	}

	function setResultCount( count ) {
		if ( resultCount ) {
			resultCount.textContent = count > 0 ? faNum( count ) + ' دستگاه' : 'بدون نتیجه';
		}
	}

	function skeleton() {
		if ( resultCount ) {
			resultCount.textContent = 'در حال جست‌وجو…';
		}
		results.innerHTML = '<div class="torantejarat-finder__skel" aria-hidden="true"><span></span><span></span><span></span><span></span></div>';
	}

	function showError() {
		results.innerHTML = '<div class="torantejarat-finder__error" role="alert">' +
			'<span>خطا در دریافت نتایج. اتصال اینترنت را بررسی کنید.</span><br>' +
			'<button type="button" data-finder-retry>تلاش مجدد</button></div>';
	}

	function shopUrl( sel ) {
		var base = window.torantejaratFinder ? window.torantejaratFinder.shopUrl : '/shop/';
		if ( sel.cats.length === 1 && ! sel.lengths.length && ! sel.band ) {
			return base + '?pcat=' + encodeURIComponent( sel.cats[ 0 ] );
		}
		return base;
	}

	function setMoreLink( sel ) {
		if ( moreLink ) {
			moreLink.href = shopUrl( sel );
		}
	}

	function fetchResults( sel, mySeq ) {
		if ( controller ) {
			controller.abort();
		}
		controller = new AbortController();
		var params = new URLSearchParams();
		params.set( 'action', box.dataset.action || 'torantejarat_finder' );
		params.set( 'nonce', box.dataset.nonce || '' );
		if ( sel.cats.length ) {
			params.set( 'cats', sel.cats.join( ',' ) );
		}
		if ( sel.lengths.length ) {
			params.set( 'lengths', sel.lengths.join( ',' ) );
		}
		if ( sel.band ) {
			params.set( 'band', String( sel.band ) );
		}
		fetch( box.dataset.ajax + '?' + params.toString(), {
			method: 'GET',
			credentials: 'same-origin',
			signal: controller.signal,
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		} ).then( function ( res ) {
			if ( ! res.ok ) {
				throw new Error( 'http' );
			}
			return res.json();
		} ).then( function ( data ) {
			if ( mySeq !== seq || ! data || typeof data.count === 'undefined' ) {
				return;
			}
			setResultCount( data.count );
			if ( data.count > 0 ) {
				results.innerHTML = data.html;
			} else {
				var contact = window.torantejaratFinder ? window.torantejaratFinder.contactUrl : '/contact/';
				results.innerHTML = '<div class="torantejarat-finder__empty" role="status">' +
					'با این ترکیب فیلتر، دستگاهی یافت نشد. لطفاً شرایط دیگری را امتحان کنید.<br>' +
					'<a class="torantejarat-btn torantejarat-btn-outline" href="' + contact + '">مشاوره بگیرید</a></div>';
			}
		} ).catch( function ( err ) {
			if ( err && err.name === 'AbortError' ) {
				return;
			}
			if ( mySeq !== seq ) {
				return;
			}
			showError();
		} );
	}

	function update( immediate ) {
		var sel = selected();
		if ( resetBtn ) {
			resetBtn.hidden = ! hasSelection( sel );
		}
		syncUrl( sel );
		setActiveCount( sel );

		if ( ! hasSelection( sel ) ) {
			clearTimeout( timer );
			seq++;
			if ( controller ) {
				controller.abort();
			}
			results.innerHTML = '';
			setResultCount( 0 );
			setMoreLink( sel );
			if ( resultsWrap ) {
				resultsWrap.hidden = true;
			}
			return;
		}

		if ( resultsWrap ) {
			resultsWrap.hidden = false;
		}
		setMoreLink( sel );
		if ( resultCount ) {
			resultCount.textContent = 'در حال جست‌وجو…';
		}
		clearTimeout( timer );
		var run = function () {
			skeleton();
			fetchResults( sel, ++seq );
		};
		if ( immediate ) {
			run();
		} else {
			timer = setTimeout( run, 250 );
		}
	}

	[ typeSel, lenSel, bandSel ].forEach( function ( sel ) {
		if ( sel ) {
			sel.addEventListener( 'change', function () {
				update( false );
			} );
		}
	} );

	if ( resetBtn ) {
		resetBtn.addEventListener( 'click', function () {
			[ typeSel, lenSel, bandSel ].forEach( function ( sel ) {
				if ( sel ) {
					sel.value = '';
				}
			} );
			update( true );
			if ( typeSel ) {
				typeSel.focus();
			}
		} );
	}

	if ( applyBtn ) {
		applyBtn.addEventListener( 'click', function () {
			update( true );
			if ( resultsWrap && ! resultsWrap.hidden ) {
				resultsWrap.scrollIntoView( { behavior: REDUCED ? 'auto' : 'smooth', block: 'start' } );
			}
		} );
	}

	document.addEventListener( 'click', function ( e ) {
		if ( e.target.closest( '[data-finder-retry]' ) ) {
			update( true );
		}
	} );

	window.addEventListener( 'popstate', function () {
		applyFromUrl();
		update( true );
	} );

	applyFromUrl();
	update( true );
} )();
