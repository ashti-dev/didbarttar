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
	 * Move focus to the first editable control of a row.
	 *
	 * @param {HTMLElement|null} row Row receiving focus.
	 */
	function focusRow( row ) {
		var first = row ? row.querySelector( 'input, textarea, select' ) : null;
		if ( first ) {
			first.focus();
		}
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

	/**
	 * Apply the row cap of a repeater (data-tt-max): the add button is disabled
	 * and the guidance line switches to the "limit reached" text once full. A
	 * repeater without a cap (max = 0) behaves exactly as before.
	 *
	 * @param {HTMLElement} wrap Repeater wrapper.
	 */
	function updateLimit( wrap ) {
		var limit = parseInt( wrap.getAttribute( 'data-tt-max' ), 10 ) || 0;
		if ( limit < 1 ) {
			return;
		}
		var body = wrap.querySelector( 'tbody' );
		var add  = wrap.querySelector( '[data-tt-repeater-add]' );
		var note = wrap.querySelector( '[data-tt-repeater-note]' );
		var full = body ? body.children.length >= limit : false;

		if ( add ) {
			add.disabled = full;
			add.setAttribute( 'aria-disabled', full ? 'true' : 'false' );
		}
		if ( note ) {
			var text = full ? note.getAttribute( 'data-tt-text-limit' ) : note.getAttribute( 'data-tt-text-guide' );
			if ( text ) {
				note.textContent = text;
			}
			note.classList.toggle( 'is-limit', full );
		}
	}

	document.querySelectorAll( '[data-tt-repeater]' ).forEach( function ( wrap ) {
		var body = wrap.querySelector( 'tbody' );
		var add  = wrap.querySelector( '[data-tt-repeater-add]' );
		var last = body ? body.querySelector( 'tr:last-child' ) : null;

		if ( add && body && last ) {
			add.addEventListener( 'click', function () {
				var limit = parseInt( wrap.getAttribute( 'data-tt-max' ), 10 ) || 0;
				if ( limit > 0 && body.children.length >= limit ) {
					updateLimit( wrap );
					return;
				}
				var row = last.cloneNode( true );
				clearRow( row );
				body.appendChild( row );
				renumber( wrap );
				updateLimit( wrap );
				focusRow( row );
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
			var rowIndex = Array.prototype.indexOf.call( body.children, row );
			// Keep at least one row: the last one is emptied instead of removed,
			// otherwise there would be nothing left to clone.
			if ( body.children.length > 1 ) {
				row.remove();
			} else {
				clearRow( row );
			}
			renumber( wrap );
			updateLimit( wrap );
			// Removing the focused button must not drop keyboard users on body.
			// Stay at the same row position, or the previous row when the last
			// row was removed; clearing the only row keeps that row in place.
			var targetIndex = Math.min( Math.max( rowIndex, 0 ), body.children.length - 1 );
			focusRow( body.children[ targetIndex ] );
		} );

		updateLimit( wrap );
	} );
}() );
