/**
 * Theme settings — repeater rows (add / remove / re-number).
 *
 * Loaded only on the theme settings screen (see TT_Settings::enqueue_styles).
 * Without JavaScript the markup still works: every repeater renders one extra
 * blank row, so filling it and saving adds a row.
 */
( function () {
	'use strict';

	/**
	 * Every control inside a repeater row.
	 *
	 * @param {HTMLElement} row Row element.
	 * @return {NodeList} Controls.
	 */
	function controls( row ) {
		return row.querySelectorAll( 'input, textarea, select' );
	}

	/**
	 * Blank a row without touching checkbox values (a checkbox keeps value="1"
	 * and only loses its checked state).
	 *
	 * @param {HTMLElement} row Row element.
	 */
	function clearRow( row ) {
		controls( row ).forEach( function ( control ) {
			if ( 'checkbox' === control.type || 'radio' === control.type ) {
				control.checked = false;
			} else {
				control.value = '';
			}
		} );
	}

	/**
	 * Rebuild every name attribute from its "%i%" template, so submitted rows
	 * are always a clean, gap-free 0..n list.
	 *
	 * @param {HTMLElement} wrap Repeater wrapper.
	 */
	function renumber( wrap ) {
		wrap.querySelectorAll( 'tbody > tr' ).forEach( function ( row, index ) {
			controls( row ).forEach( function ( control ) {
				var template = control.getAttribute( 'data-tt-name' );
				if ( template ) {
					control.name = template.replace( '%i%', index );
				}
			} );
		} );
	}

	document.querySelectorAll( '[data-tt-repeater]' ).forEach( function ( wrap ) {
		var body = wrap.querySelector( 'tbody' );
		var add  = wrap.querySelector( '[data-tt-repeater-add]' );
		var last = body ? body.querySelector( 'tr:last-child' ) : null;

		if ( add && body && last ) {
			add.addEventListener( 'click', function () {
				var row = last.cloneNode( true );
				clearRow( row );
				body.appendChild( row );
				renumber( wrap );
				var first = row.querySelector( 'input, textarea, select' );
				if ( first ) {
					first.focus();
				}
			} );
		}

		wrap.addEventListener( 'click', function ( event ) {
			var button = event.target.closest( '[data-tt-repeater-remove]' );
			if ( ! button || ! body ) {
				return;
			}
			var row = button.closest( 'tr' );
			if ( ! row ) {
				return;
			}
			// Keep at least one row: the last one is emptied instead of removed,
			// otherwise there would be nothing left to clone.
			if ( body.children.length > 1 ) {
				row.remove();
			} else {
				clearRow( row );
			}
			renumber( wrap );
		} );
	} );
}() );
