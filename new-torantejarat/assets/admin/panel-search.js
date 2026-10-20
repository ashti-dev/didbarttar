/**
 * Panel-wide settings search (ARIA combobox).
 *
 * The index is built server-side in inc/settings/panel-nav.php
 * (torantejarat_panel_search_index) and handed over through
 * wp_localize_script: this file never discovers fields from the DOM and never
 * searches stored values. Picking a result navigates to the section /
 * sub-section and scrolls the field into view.
 */
( function () {
	'use strict';

	var data = window.ttPanelSearch;
	if ( ! data || ! data.index || ! data.index.length ) {
		return;
	}

	var wrap = document.querySelector( '[data-tt-search]' );
	if ( ! wrap ) {
		return;
	}

	var input = wrap.querySelector( '.tt-search-input' );
	var list  = wrap.querySelector( '.tt-search-results' );
	if ( ! input || ! list ) {
		return;
	}

	var MAX    = 8;
	var active = -1;
	var shown  = [];

	/**
	 * Fold Persian spelling variants and the zero-width non-joiner so a search
	 * for "نیم فاصله" also matches "نیم‌فاصله".
	 *
	 * @param {string} value Raw text.
	 * @return {string} Normalised text.
	 */
	function normalize( value ) {
		return String( value )
			.toLowerCase()
			.replace( /\u200c/g, ' ' )
			.replace( /[\u064a\u06cc]/g, '\u06cc' )
			.replace( /[\u0643\u06a9]/g, '\u06a9' )
			.replace( /\s+/g, ' ' )
			.trim();
	}

	function close() {
		list.hidden = true;
		list.innerHTML = '';
		input.setAttribute( 'aria-expanded', 'false' );
		input.removeAttribute( 'aria-activedescendant' );
		active = -1;
		shown  = [];
	}

	/**
	 * Navigate to a result: section/sub select the tree branch, the fragment
	 * scrolls the field into view.
	 *
	 * @param {Object} entry Index entry.
	 */
	function go( entry ) {
		var url = data.baseUrl + '&section=' + encodeURIComponent( entry.section );
		if ( entry.sub ) {
			url += '&sub=' + encodeURIComponent( entry.sub );
		}
		window.location.assign( url + '#' + entry.field_id );
	}

	function highlight( index ) {
		Array.prototype.forEach.call( list.children, function ( li, i ) {
			li.classList.toggle( 'is-active', i === index );
		} );
		active = index;
		if ( index >= 0 && list.children[ index ] ) {
			input.setAttribute( 'aria-activedescendant', list.children[ index ].id );
		}
	}

	function render( query ) {
		var q = normalize( query );
		shown = [];
		list.innerHTML = '';

		if ( '' === q ) {
			close();
			return;
		}

		data.index.forEach( function ( entry ) {
			if ( shown.length >= MAX ) {
				return;
			}
			if ( -1 !== normalize( entry.text ).indexOf( q ) ) {
				shown.push( entry );
			}
		} );

		if ( ! shown.length ) {
			var empty = document.createElement( 'li' );
			empty.className = 'tt-search-empty';
			empty.textContent = data.noResults;
			list.appendChild( empty );
			list.hidden = false;
			input.setAttribute( 'aria-expanded', 'true' );
			active = -1;
			return;
		}

		shown.forEach( function ( entry, i ) {
			var li = document.createElement( 'li' );
			li.className = 'tt-search-item';
			li.id = 'tt-search-opt-' + i;
			li.setAttribute( 'role', 'option' );
			li.setAttribute( 'aria-selected', 'false' );
			li.textContent = entry.text;
			li.addEventListener( 'mousedown', function ( event ) {
				event.preventDefault();
				go( entry );
			} );
			list.appendChild( li );
		} );

		list.hidden = false;
		input.setAttribute( 'aria-expanded', 'true' );
		active = -1;
	}

	input.addEventListener( 'input', function () {
		render( input.value );
	} );

	input.addEventListener( 'keydown', function ( event ) {
		if ( 'Escape' === event.key ) {
			close();
			return;
		}
		if ( list.hidden ) {
			return;
		}
		if ( 'ArrowDown' === event.key || 'ArrowUp' === event.key ) {
			event.preventDefault();
			var count = shown.length;
			if ( ! count ) {
				return;
			}
			var next = 'ArrowDown' === event.key ? active + 1 : active - 1;
			if ( next < 0 ) {
				next = count - 1;
			}
			if ( next >= count ) {
				next = 0;
			}
			highlight( next );
			return;
		}
		if ( 'Enter' === event.key && active >= 0 && shown[ active ] ) {
			event.preventDefault();
			go( shown[ active ] );
		}
	} );

	document.addEventListener( 'click', function ( event ) {
		if ( ! wrap.contains( event.target ) ) {
			close();
		}
	} );
}() );
