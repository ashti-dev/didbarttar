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

	/**
	 * Progressive-enhancement drawer. Without this script the sidebar remains
	 * in normal document flow and the hidden toggle never becomes available.
	 */
	( function () {
		var panel  = document.querySelector( '[data-tt-panel]' );
		var shell  = panel ? panel.querySelector( '[data-tt-nav-shell]' ) : null;
		var toggle = panel ? panel.querySelector( '[data-tt-nav-toggle]' ) : null;
		var main   = panel ? panel.querySelector( '.tt-main' ) : null;
		if ( ! panel || ! shell || ! toggle || ! main || ! window.matchMedia ) {
			return;
		}

		var query     = window.matchMedia( '(max-width: 1023px)' );
		var overlay   = panel.querySelector( '.tt-nav-overlay' );
		var close     = panel.querySelector( '.tt-sidebar-close' );
		var lastFocus = null;

		function setInert( element, value ) {
			if ( element && 'inert' in element ) {
				element.inert = value;
			}
		}

		function closeDrawer( restoreFocus ) {
			if ( restoreFocus ) {
				var target = lastFocus && document.contains( lastFocus ) ? lastFocus : toggle;
				target.focus();
			}
			panel.classList.remove( 'tt-drawer-open' );
			document.body.classList.remove( 'tt-panel-drawer-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			if ( overlay ) {
				overlay.hidden = true;
			}
			setInert( main, false );
			if ( query.matches ) {
				shell.setAttribute( 'aria-hidden', 'true' );
				setInert( shell, true );
			} else {
				shell.removeAttribute( 'aria-hidden' );
				setInert( shell, false );
			}
		}

		function openDrawer() {
			if ( ! query.matches ) {
				return;
			}
			lastFocus = toggle;
			setInert( shell, false );
			shell.setAttribute( 'aria-hidden', 'false' );
			panel.classList.add( 'tt-drawer-open' );
			document.body.classList.add( 'tt-panel-drawer-open' );
			toggle.setAttribute( 'aria-expanded', 'true' );
			if ( overlay ) {
				overlay.hidden = false;
			}
			setInert( main, true );
			if ( close ) {
				close.focus();
			}
		}

		function syncMode() {
			panel.classList.add( 'tt-drawer-enhanced' );
			toggle.hidden = ! query.matches;
			if ( close ) {
				close.hidden = ! query.matches;
			}
			closeDrawer( false );
		}

		toggle.addEventListener( 'click', openDrawer );
		panel.querySelectorAll( '[data-tt-nav-close]' ).forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				closeDrawer( true );
			} );
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( ! panel.classList.contains( 'tt-drawer-open' ) ) {
				return;
			}
			if ( 'Escape' === event.key ) {
				event.preventDefault();
				closeDrawer( true );
				return;
			}
			if ( 'Tab' !== event.key ) {
				return;
			}
			var focusable = Array.prototype.filter.call(
				shell.querySelectorAll( 'a[href], button:not([disabled]), [tabindex]:not([tabindex="-1"])' ),
				function ( element ) {
					return element.getClientRects().length > 0;
				}
			);
			if ( ! focusable.length ) {
				return;
			}
			var first = focusable[ 0 ];
			var last  = focusable[ focusable.length - 1 ];
			if ( event.shiftKey && ( document.activeElement === first || ! shell.contains( document.activeElement ) ) ) {
				event.preventDefault();
				last.focus();
			} else if ( ! event.shiftKey && ( document.activeElement === last || ! shell.contains( document.activeElement ) ) ) {
				event.preventDefault();
				first.focus();
			}
		} );

		if ( query.addEventListener ) {
			query.addEventListener( 'change', syncMode );
		} else {
			query.addListener( syncMode );
		}
		syncMode();
	}() );

	/**
	 * Dirty-state indicator and native beforeunload warning. Snapshots include
	 * only submitted controls, so repeater renumbering is handled as well.
	 */
	( function () {
		var forms = Array.prototype.map.call( document.querySelectorAll( 'form[data-tt-dirty]' ), function ( form ) {
			return { form: form, initial: '', submitting: false };
		} );
		if ( ! forms.length ) {
			return;
		}

		function snapshot( form ) {
			var values = [];
			Array.prototype.forEach.call( form.elements, function ( control ) {
				if ( ! control.name || control.disabled || 'submit' === control.type || 'button' === control.type ) {
					return;
				}
				if ( ( 'checkbox' === control.type || 'radio' === control.type ) && ! control.checked ) {
					return;
				}
				values.push( control.name + '=' + encodeURIComponent( control.value ) );
			} );
			return values.join( '&' );
		}

		function isDirty( state ) {
			return ! state.submitting && snapshot( state.form ) !== state.initial;
		}

		function update( state ) {
			var dirty = isDirty( state );
			state.form.classList.toggle( 'is-dirty', dirty );
			var panel = state.form.closest( '[data-tt-panel]' );
			if ( panel ) {
				panel.classList.toggle( 'tt-has-dirty', dirty );
			}
			var status = document.querySelector( '[data-tt-dirty-status]' );
			if ( status ) {
				var label = status.getAttribute( dirty ? 'data-tt-dirty-label' : 'data-tt-clean-label' );
				if ( label ) {
					status.textContent = label;
				}
			}
		}

		forms.forEach( function ( state ) {
			state.initial = snapshot( state.form );
			state.form.addEventListener( 'input', function () { update( state ); } );
			state.form.addEventListener( 'change', function () { update( state ); } );
			state.form.addEventListener( 'click', function ( event ) {
				if ( event.target.closest( '[data-tt-repeater-add], [data-tt-repeater-remove]' ) ) {
					window.setTimeout( function () { update( state ); }, 0 );
				}
			} );
			state.form.addEventListener( 'submit', function () {
				state.submitting = true;
			} );
			update( state );
		} );

		window.addEventListener( 'beforeunload', function ( event ) {
			var dirty = forms.some( isDirty );
			if ( ! dirty ) {
				return;
			}
			event.preventDefault();
			event.returnValue = '';
		} );
	}() );
}() );
