/**
 * Theme settings — field controls, repeater, drawer and dirty state.
 *
 * Loaded only on the theme settings screen (see TT_Settings::enqueue_styles).
 * Without JavaScript the named inputs and native submit still work; repeaters
 * also render one extra blank row so filling it and saving adds a row.
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
		setCollapsed( row, false );
		updateSummary( row );
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
	 * Collapse is visual only and never disables or renames submitted inputs.
	 *
	 * @param {HTMLElement} row       Repeater row.
	 * @param {boolean}     collapsed Target state.
	 */
	function setCollapsed( row, collapsed ) {
		if ( ! row ) {
			return;
		}
		var body   = row.querySelector( '[data-tt-repeater-body]' );
		var toggle = row.querySelector( '[data-tt-repeater-toggle]' );
		if ( body ) {
			body.hidden = collapsed;
		}
		row.classList.toggle( 'is-collapsed', collapsed );
		if ( toggle ) {
			toggle.setAttribute( 'aria-expanded', collapsed ? 'false' : 'true' );
			var text = toggle.querySelector( '[data-tt-repeater-toggle-text]' );
			if ( text ) {
				text.textContent = toggle.getAttribute( collapsed ? 'data-tt-expand-label' : 'data-tt-collapse-label' );
			}
			var icon = toggle.querySelector( '.dashicons' );
			if ( icon ) {
				icon.classList.toggle( 'dashicons-arrow-up-alt2', ! collapsed );
				icon.classList.toggle( 'dashicons-arrow-down-alt2', collapsed );
			}
		}
	}

	function updateSummary( row ) {
		var summary = row ? row.querySelector( '[data-tt-repeater-summary]' ) : null;
		if ( ! summary ) {
			return;
		}
		var first = row.querySelector( 'input, textarea, select' );
		var value = first ? String( first.value || '' ).replace( /\s+/g, ' ' ).trim() : '';
		summary.textContent = value.length > 70 ? value.substring( 0, 70 ) + '…' : value;
	}

	/**
	 * Rebuild every name attribute from its "%i%" template, so submitted rows
	 * are always a clean, gap-free 0..n list. Card numbers are visual only.
	 *
	 * @param {HTMLElement} wrap Repeater wrapper.
	 */
	function renumber( wrap ) {
		var list = wrap.querySelector( '[data-tt-repeater-list]' );
		if ( ! list ) {
			return;
		}
		Array.prototype.forEach.call( list.children, function ( row, index ) {
			controls( row ).forEach( function ( control ) {
				var template = control.getAttribute( 'data-tt-name' );
				if ( template ) {
					control.name = template.replace( '%i%', index );
				}
			} );
			var value = index + 1;
			try {
				value = value.toLocaleString( document.documentElement.lang || 'fa-IR' );
			} catch ( error ) {
				value = String( value );
			}
			var label = ( wrap.getAttribute( 'data-tt-row-label' ) || 'Row' ) + ' ' + value;
			var number = row.querySelector( '[data-tt-repeater-number]' );
			if ( number ) {
				number.textContent = label;
			}
			row.setAttribute( 'aria-label', label );
			updateSummary( row );
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
		var list = wrap.querySelector( '[data-tt-repeater-list]' );
		var add  = wrap.querySelector( '[data-tt-repeater-add]' );
		var note = wrap.querySelector( '[data-tt-repeater-note]' );
		var full = list ? list.children.length >= limit : false;

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
		var list = wrap.querySelector( '[data-tt-repeater-list]' );
		var add  = wrap.querySelector( '[data-tt-repeater-add]' );
		if ( ! list ) {
			return;
		}

		if ( add ) {
			add.hidden = false;
		}
		wrap.querySelectorAll( '[data-tt-repeater-toggle], [data-tt-repeater-remove]' ).forEach( function ( button ) {
			button.hidden = false;
		} );
		Array.prototype.forEach.call( list.children, updateSummary );

		if ( add ) {
			add.addEventListener( 'click', function () {
				var limit = parseInt( wrap.getAttribute( 'data-tt-max' ), 10 ) || 0;
				if ( limit > 0 && list.children.length >= limit ) {
					updateLimit( wrap );
					return;
				}
				var template = list.querySelector( '[data-tt-repeater-row]:last-child' );
				if ( ! template ) {
					return;
				}
				var row = template.cloneNode( true );
				clearRow( row );
				list.appendChild( row );
				renumber( wrap );
				updateLimit( wrap );
				focusRow( row );
			} );
		}

		wrap.addEventListener( 'input', function ( event ) {
			var row = event.target.closest( '[data-tt-repeater-row]' );
			if ( row ) {
				updateSummary( row );
			}
		} );

		wrap.addEventListener( 'click', function ( event ) {
			var toggle = event.target.closest( '[data-tt-repeater-toggle]' );
			if ( toggle ) {
				var toggleRow = toggle.closest( '[data-tt-repeater-row]' );
				setCollapsed( toggleRow, 'true' === toggle.getAttribute( 'aria-expanded' ) );
				return;
			}

			var button = event.target.closest( '[data-tt-repeater-remove]' );
			if ( ! button ) {
				return;
			}
			var row = button.closest( '[data-tt-repeater-row]' );
			if ( ! row ) {
				return;
			}
			var rowIndex = Array.prototype.indexOf.call( list.children, row );
			// Keep at least one row: the last one is emptied instead of removed,
			// otherwise there would be nothing left to clone.
			if ( list.children.length > 1 ) {
				row.remove();
			} else {
				clearRow( row );
			}
			renumber( wrap );
			updateLimit( wrap );
			// Removing the focused button must not drop keyboard users on body.
			// Stay at the same row position, or the previous row when the last
			// row was removed; clearing the only row keeps that row in place.
			var targetIndex = Math.min( Math.max( rowIndex, 0 ), list.children.length - 1 );
			var targetRow = list.children[ targetIndex ];
			setCollapsed( targetRow, false );
			focusRow( targetRow );
		} );

		updateLimit( wrap );
	} );

	/**
	 * URL actions are progressively enhanced and only expose absolute HTTP(S)
	 * values. The input name/value and native submit never change.
	 */
	document.querySelectorAll( 'input[data-tt-url-input]' ).forEach( function ( input ) {
		var wrap = input.closest( '.tt-url-control' );
		var link = wrap ? wrap.querySelector( '[data-tt-url-open]' ) : null;
		if ( ! link ) {
			return;
		}

		function updateLink() {
			var href = '';
			var value = input.value.trim();
			if ( /^https?:\/\//i.test( value ) && window.URL ) {
				try {
					var parsed = new window.URL( value );
					if ( 'http:' === parsed.protocol || 'https:' === parsed.protocol ) {
						href = parsed.href;
					}
				} catch ( error ) {
					href = '';
				}
			}
			link.hidden = ! href;
			if ( href ) {
				link.href = href;
			} else {
				link.removeAttribute( 'href' );
			}
		}

		input.addEventListener( 'input', updateLink );
		updateLink();
	} );

	/**
	 * Media + URL for the login logo. Selection writes only attachment.url;
	 * clearing the input never requests or implies attachment deletion.
	 */
	( function () {
		var config = window.ttSettingsControls && window.ttSettingsControls.media;
		if ( ! config || ! config.enabled || ! window.wp || 'function' !== typeof window.wp.media ) {
			return;
		}

		document.querySelectorAll( '[data-tt-media]' ).forEach( function ( wrap ) {
			var input   = wrap.querySelector( '[data-tt-media-url]' );
			var select  = wrap.querySelector( '[data-tt-media-select]' );
			var clear   = wrap.querySelector( '[data-tt-media-clear]' );
			var preview = wrap.querySelector( '[data-tt-media-preview]' );
			var image   = wrap.querySelector( '[data-tt-media-image]' );
			if ( ! input || ! select || ! clear ) {
				return;
			}
			var frame = null;

			function updateClear() {
				clear.hidden = ! input.value.trim();
			}

			function updatePreview() {
				if ( ! preview || ! image ) {
					return;
				}
				var value = input.value.trim();
				if ( ! /^https?:\/\//i.test( value ) ) {
					preview.hidden = true;
					image.removeAttribute( 'src' );
					return;
				}
				preview.hidden = true;
				image.src = value;
			}

			function notify() {
				input.dispatchEvent( new Event( 'input', { bubbles: true } ) );
				input.dispatchEvent( new Event( 'change', { bubbles: true } ) );
			}

			if ( image && preview ) {
				image.addEventListener( 'load', function () {
					if ( image.getAttribute( 'src' ) ) {
						preview.hidden = false;
					}
				} );
				image.addEventListener( 'error', function () {
					preview.hidden = true;
				} );
			}

			select.hidden = false;
			updateClear();
			input.addEventListener( 'input', updateClear );
			input.addEventListener( 'change', updatePreview );

			clear.addEventListener( 'click', function () {
				input.value = '';
				notify();
				updateClear();
				select.focus();
			} );

			select.addEventListener( 'click', function () {
				if ( ! frame ) {
					frame = window.wp.media( {
						title: config.title,
						button: { text: config.button },
						library: { type: 'image' },
						multiple: false
					} );
					frame.on( 'select', function () {
						var selection = frame.state().get( 'selection' ).first();
						if ( ! selection ) {
							return;
						}
						var attachment = 'function' === typeof selection.toJSON ? selection.toJSON() : selection.attributes;
						if ( ! attachment || ! attachment.url ) {
							return;
						}
						input.value = attachment.url;
						notify();
						updateClear();
						input.focus();
					} );
				}
				frame.open();
			} );
		} );
	}() );

	/**
	 * Colour + editable HEX. The server renders only the named HEX input, so
	 * no-JS submit is unchanged; the unnamed native swatch is an enhancement.
	 */
	document.querySelectorAll( 'input[data-tt-color]' ).forEach( function ( text ) {
		var swatch = document.createElement( 'input' );
		swatch.type = 'color';
		swatch.id = text.id + '-swatch';
		swatch.className = 'tt-color-swatch';
		swatch.setAttribute( 'aria-label', text.getAttribute( 'data-tt-color-label' ) || 'Select colour' );

		function hex( value ) {
			var match = String( value ).trim().match( /^#([0-9a-f]{3}|[0-9a-f]{6})$/i );
			if ( ! match ) {
				return '';
			}
			if ( 3 === match[ 1 ].length ) {
				return '#' + match[ 1 ].split( '' ).map( function ( part ) { return part + part; } ).join( '' );
			}
			return '#' + match[ 1 ];
		}

		swatch.value = hex( text.value ) || hex( text.defaultValue ) || '#000000';
		text.parentNode.insertBefore( swatch, text );

		text.addEventListener( 'input', function () {
			var value = hex( text.value );
			if ( value ) {
				swatch.value = value;
			}
		} );
		swatch.addEventListener( 'input', function () {
			text.value = swatch.value;
			text.dispatchEvent( new Event( 'input', { bubbles: true } ) );
		} );
		swatch.addEventListener( 'change', function () {
			text.dispatchEvent( new Event( 'change', { bubbles: true } ) );
		} );
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
			if ( restoreFocus || ( query.matches && shell.contains( document.activeElement ) ) ) {
				var target = restoreFocus && lastFocus && document.contains( lastFocus ) ? lastFocus : toggle;
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

	/**
	 * Product picker: search products and add them as slides, instead of
	 * typing IDs. Rows post the same names as the repeater (product_id, label,
	 * caption, link), so the server-side sanitizer and the front end do not
	 * care how a row was created.
	 */
	( function () {
		var cfg = ( window.ttSettingsControls && window.ttSettingsControls.productSearch ) || {};
		var pickers = document.querySelectorAll( '[data-tt-product-picker]' );
		if ( ! pickers.length ) {
			return;
		}

		function el( tag, className, text ) {
			var node = document.createElement( tag );
			if ( className ) {
				node.className = className;
			}
			if ( 'undefined' !== typeof text ) {
				node.textContent = text;
			}
			return node;
		}

		function icon( name ) {
			var span = el( 'span', 'dashicons dashicons-' + name );
			span.setAttribute( 'aria-hidden', 'true' );
			return span;
		}

		document.querySelectorAll( '[data-tt-product-picker]' ).forEach( function ( wrap ) {
			var list      = wrap.querySelector( '[data-tt-picker-list]' );
			var search    = wrap.querySelector( '[data-tt-picker-search]' );
			var results   = wrap.querySelector( '[data-tt-picker-results]' );
			var status    = wrap.querySelector( '[data-tt-picker-status]' );
			var emptyNote = wrap.querySelector( '[data-tt-picker-empty]' );
			var note      = wrap.querySelector( '[data-tt-picker-note]' );
			var spinner   = wrap.querySelector( '[data-tt-picker-spinner]' );
			var base      = wrap.getAttribute( 'data-tt-base' ) || '';
			var ajaxUrl   = wrap.getAttribute( 'data-tt-ajax' ) || '';
			var nonce     = wrap.getAttribute( 'data-tt-nonce' ) || '';
			var max       = parseInt( wrap.getAttribute( 'data-tt-max' ), 10 ) || 0;
			var timer     = 0;
			var request   = null;

			if ( ! list || ! search || ! results ) {
				return;
			}

			function fmt( number ) {
				try {
					return number.toLocaleString( document.documentElement.lang || 'fa-IR' );
				} catch ( error ) {
					return String( number );
				}
			}

			function items() {
				return Array.prototype.slice.call( list.querySelectorAll( '[data-tt-picker-item]' ) );
			}

			function renumber() {
				items().forEach( function ( item, index ) {
					item.querySelectorAll( '[data-tt-name]' ).forEach( function ( input ) {
						var template = input.getAttribute( 'data-tt-name' );
						if ( template ) {
							input.name = template.replace( '%i%', index );
						}
					} );
					var position = item.querySelector( '[data-tt-picker-position], .tt-picker-position' );
					if ( position ) {
						position.textContent = fmt( index + 1 );
					}
				} );
				var count = items().length;
				if ( emptyNote ) {
					emptyNote.hidden = count > 0;
				}
				if ( note ) {
					var full = max > 0 && count >= max;
					note.textContent = note.getAttribute( full ? 'data-tt-text-limit' : 'data-tt-text-guide' );
					note.classList.toggle( 'is-limit', full );
				}
				if ( max > 0 ) {
					var fullNow = count >= max;
					search.disabled = fullNow;
					if ( fullNow ) {
						hideResults();
					}
				}
				list.dispatchEvent( new Event( 'input', { bubbles: true } ) );
			}

			function makeField( key, label, type, value ) {
				var field = el( 'label', 'tt-picker-field' );
				field.appendChild( el( 'span', 'tt-picker-field-label', label ) );
				var input = el( 'input' );
				input.type = type;
				input.value = value || '';
				input.setAttribute( 'data-tt-name', base + '[%i%][' + key + ']' );
				field.appendChild( input );
				return field;
			}

			function buildItem( product ) {
				var item = el( 'li', 'tt-picker-item' );
				item.setAttribute( 'data-tt-picker-item', '' );
				item.setAttribute( 'data-product-id', String( product.id ) );

				var main = el( 'div', 'tt-picker-main' );
				var thumb = el( 'span', 'tt-picker-thumb' + ( product.thumb ? '' : ' is-empty' ) );
				if ( product.thumb ) {
					var img = el( 'img' );
					img.src = product.thumb;
					img.alt = '';
					img.width = 48;
					img.height = 48;
					img.loading = 'lazy';
					thumb.appendChild( img );
				} else {
					thumb.appendChild( icon( 'format-image' ) );
				}
				main.appendChild( thumb );

				var info = el( 'div', 'tt-picker-info' );
				info.appendChild( el( 'strong', 'tt-picker-name', product.name || cfg.fallback || '' ) );
				var meta = el( 'span', 'tt-picker-meta' );
				meta.appendChild( el( 'span', 'tt-picker-position', '' ) );
				meta.lastChild.setAttribute( 'data-tt-picker-position', '' );
				meta.appendChild( el( 'span', 'tt-picker-sep', '·' ) );
				var idText = el( 'span', '', '#' + product.id );
				idText.setAttribute( 'dir', 'ltr' );
				meta.appendChild( idText );
				if ( product.sku ) {
					meta.appendChild( el( 'span', 'tt-picker-sep', '·' ) );
					var skuText = el( 'span', '', product.sku );
					skuText.setAttribute( 'dir', 'ltr' );
					meta.appendChild( skuText );
				}
				info.appendChild( meta );
				main.appendChild( info );

				var actions = el( 'div', 'tt-picker-actions' );
				var up = el( 'button', 'button-link tt-picker-move' );
				up.type = 'button';
				up.setAttribute( 'data-tt-picker-up', '' );
				up.appendChild( icon( 'arrow-up-alt2' ) );
				up.appendChild( el( 'span', 'screen-reader-text', cfg.up || '' ) );
				var down = el( 'button', 'button-link tt-picker-move' );
				down.type = 'button';
				down.setAttribute( 'data-tt-picker-down', '' );
				down.appendChild( icon( 'arrow-down-alt2' ) );
				down.appendChild( el( 'span', 'screen-reader-text', cfg.down || '' ) );
				var remove = el( 'button', 'button-link tt-picker-remove' );
				remove.type = 'button';
				remove.setAttribute( 'data-tt-picker-remove', '' );
				remove.appendChild( el( 'span', '', cfg.remove || '' ) );
				remove.appendChild( el( 'span', 'screen-reader-text', cfg.remove || '' ) );
				actions.appendChild( up );
				actions.appendChild( down );
				actions.appendChild( remove );
				main.appendChild( actions );
				item.appendChild( main );

				var details = el( 'details', 'tt-picker-more' );
				details.appendChild( el( 'summary', '', cfg.more || '' ) );
				var fields = el( 'div', 'tt-picker-fields' );
				var labels = cfg.fields || {};
				fields.appendChild( makeField( 'product_id', labels.product_id || 'ID', 'number', product.id ) );
				fields.appendChild( makeField( 'label', labels.label || 'Label', 'text', '' ) );
				fields.appendChild( makeField( 'caption', labels.caption || 'Caption', 'text', '' ) );
				fields.appendChild( makeField( 'link', labels.link || 'Link', 'url', '' ) );
				details.appendChild( fields );
				details.appendChild( el( 'p', 'description', cfg.hint || '' ) );
				item.appendChild( details );

				return item;
			}

			function hideResults() {
				results.hidden = true;
				results.textContent = '';
			}

			function showStatus( text ) {
				if ( status ) {
					status.textContent = text || '';
				}
			}

			function renderResults( products ) {
				results.textContent = '';
				results.hidden = false;

				if ( ! products.length ) {
					results.appendChild( el( 'p', 'tt-picker-no-result', cfg.noResult || '' ) );
					return;
				}

				products.forEach( function ( product ) {
					var button = el( 'button', 'tt-picker-result' );
					button.type = 'button';
					button.setAttribute( 'data-id', String( product.id ) );

					var thumb = el( 'span', 'tt-picker-thumb' + ( product.thumb ? '' : ' is-empty' ) );
					if ( product.thumb ) {
						var img = el( 'img' );
						img.src = product.thumb;
						img.alt = '';
						img.width = 40;
						img.height = 40;
						img.loading = 'lazy';
						thumb.appendChild( img );
					} else {
						thumb.appendChild( icon( 'format-image' ) );
					}
					button.appendChild( thumb );

					var text = el( 'span', 'tt-picker-result-text' );
					text.appendChild( el( 'strong', '', product.name ) );
					var meta = el( 'small', '' );
					var bits = [ '#' + product.id, product.sku, product.price ].filter( Boolean );
					meta.textContent = bits.join( ' · ' );
					if ( product.sku || product.price ) {
						meta.setAttribute( 'dir', 'ltr' );
					}
					text.appendChild( meta );
					button.appendChild( text );

					button.appendChild( el( 'span', 'tt-picker-add', cfg.add || '' ) );

					button.addEventListener( 'click', function () {
						if ( list.querySelector( '[data-product-id="' + product.id + '"]' ) ) {
							showStatus( product.name );
							hideResults();
							search.value = '';
							return;
						}
						if ( max > 0 && items().length >= max ) {
							renumber();
							return;
						}
						list.appendChild( buildItem( product ) );
						renumber();
						showStatus( product.name );
						hideResults();
						search.value = '';
						search.focus();
					} );

					results.appendChild( button );
				} );
			}

			function runSearch() {
				var query = search.value.replace( /\s+/g, ' ' ).trim();
				if ( query.length < 2 ) {
					hideResults();
					showStatus( '' );
					return;
				}

				if ( request ) {
					request.abort();
				}
				request = window.AbortController ? new AbortController() : null;

				if ( spinner ) {
					spinner.classList.add( 'is-active' );
				}
				showStatus( cfg.loading || '' );

				var url = new URL( ajaxUrl, window.location.origin );
				url.searchParams.set( 'action', 'torantejarat_product_search' );
				url.searchParams.set( 'nonce', nonce );
				url.searchParams.set( 'q', query );
				items().forEach( function ( item ) {
					url.searchParams.append( 'exclude[]', item.getAttribute( 'data-product-id' ) );
				} );

				fetch( url.toString(), {
					credentials: 'same-origin',
					signal: request ? request.signal : undefined
				} )
					.then( function ( response ) {
						return response.json();
					} )
					.then( function ( json ) {
						if ( ! json || ! json.success ) {
							throw new Error( 'request failed' );
						}
						showStatus( '' );
						renderResults( json.data || [] );
					} )
					.catch( function ( error ) {
						if ( 'AbortError' === error.name ) {
							return;
						}
						showStatus( cfg.error || '' );
					} )
					.finally( function () {
						if ( spinner ) {
							spinner.classList.remove( 'is-active' );
						}
					} );
			}

			search.addEventListener( 'input', function () {
				window.clearTimeout( timer );
				timer = window.setTimeout( runSearch, 250 );
			} );

			search.addEventListener( 'keydown', function ( event ) {
				if ( 'Escape' === event.key ) {
					hideResults();
				}
			} );

			document.addEventListener( 'click', function ( event ) {
				if ( ! wrap.contains( event.target ) ) {
					hideResults();
				}
			} );

			wrap.addEventListener( 'click', function ( event ) {
				var item = event.target.closest( '[data-tt-picker-item]' );
				if ( ! item ) {
					return;
				}

				if ( event.target.closest( '[data-tt-picker-remove]' ) ) {
					item.remove();
					renumber();
					search.focus();
					return;
				}

				if ( event.target.closest( '[data-tt-picker-up]' ) && item.previousElementSibling ) {
					list.insertBefore( item, item.previousElementSibling );
					renumber();
					return;
				}

				if ( event.target.closest( '[data-tt-picker-down]' ) && item.nextElementSibling ) {
					list.insertBefore( item.nextElementSibling, item );
					renumber();
				}
			} );

			renumber();
		} );
	}() );
}() );
