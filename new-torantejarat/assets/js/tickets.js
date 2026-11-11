/**
 * Ticket endpoint interactions: text/visual editor, attachment uploads and
 * progressive-enhancement AJAX for creating tickets and replying.
 */
(function () {
	'use strict';

	var cfg = window.ttkData;
	if ( ! cfg ) {
		return;
	}

	var i18n = cfg.i18n || {};
	var uploads = {};

	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	ready( function () {
		initEditor( document.getElementById( 'ttk-create-form' ) );
		initUploads( 'create', document.getElementById( 'ttk-create-form' ) );
		initUploads( 'reply', document.getElementById( 'ttk-reply-form' ) );
		initCreateForm();
		initReplyForm();
	} );

	/* ----------------------------------------------------------------
	 * Editor: متن / دیداری
	 * ---------------------------------------------------------------- */
	function initEditor( root ) {
		if ( ! root ) {
			return;
		}

		var editor = root.querySelector( '[data-ttk-editor]' );
		if ( ! editor ) {
			return;
		}

		var tabs = editor.querySelectorAll( '[data-ttk-tab]' );
		var panes = editor.querySelectorAll( '[data-ttk-pane]' );
		var toolbar = editor.querySelector( '[data-ttk-toolbar]' );
		var textarea = editor.querySelector( 'textarea' );
		var rich = editor.querySelector( '[data-ttk-rich]' );
		var mode = 'text';

		editor.dataset.mode = mode;

		function setMode( next ) {
			if ( next === mode || ! textarea || ! rich ) {
				return;
			}

			if ( next === 'visual' ) {
				rich.innerHTML = textarea.value;
			} else {
				textarea.value = rich.innerHTML;
			}

			mode = next;
			editor.dataset.mode = mode;

			tabs.forEach( function ( tab ) {
				var active = tab.getAttribute( 'data-ttk-tab' ) === mode;
				tab.classList.toggle( 'is-active', active );
				tab.setAttribute( 'aria-selected', active ? 'true' : 'false' );
			} );

			panes.forEach( function ( pane ) {
				pane.hidden = pane.getAttribute( 'data-ttk-pane' ) !== mode;
			} );
		}

		tabs.forEach( function ( tab ) {
			tab.addEventListener( 'click', function () {
				setMode( tab.getAttribute( 'data-ttk-tab' ) );
			} );
		} );

		if ( toolbar ) {
			toolbar.addEventListener( 'mousedown', function ( event ) {
				if ( event.target.closest( '[data-ttk-cmd]' ) ) {
					event.preventDefault();
				}
			} );

			toolbar.addEventListener( 'click', function ( event ) {
				var button = event.target.closest( '[data-ttk-cmd]' );
				if ( ! button ) {
					return;
				}

				var command = button.getAttribute( 'data-ttk-cmd' );
				if ( mode === 'visual' ) {
					applyRichCommand( command, rich );
				} else if ( textarea ) {
					applyTextCommand( command, textarea );
				}
			} );
		}
	}

	function applyRichCommand( command, rich ) {
		rich.focus();

		if ( command === 'createLink' ) {
			var url = window.prompt( i18n.linkPrompt || 'URL', 'https://' );
			if ( url ) {
				document.execCommand( 'createLink', false, url );
			}
			return;
		}

		if ( command === 'formatBlock' ) {
			document.execCommand( 'formatBlock', false, '<p>' );
			return;
		}

		try {
			document.execCommand( command, false, null );
		} catch ( error ) {
			// Older browsers may not support every command.
		}
	}

	function applyTextCommand( command, textarea ) {
		switch ( command ) {
			case 'bold':
				wrapText( textarea, '<b>', '</b>' );
				break;
			case 'italic':
				wrapText( textarea, '<i>', '</i>' );
				break;
			case 'underline':
				wrapText( textarea, '<u>', '</u>' );
				break;
			case 'createLink':
				var url = window.prompt( i18n.linkPrompt || 'URL', 'https://' );
				if ( url ) {
					wrapText( textarea, '<a href="' + escapeAttribute( url ) + '">', '</a>' );
				}
				break;
			case 'insertUnorderedList':
				prefixLines( textarea, '• ' );
				break;
			case 'insertOrderedList':
				prefixLines( textarea, '1. ' );
				break;
			case 'removeFormat':
				stripSelectionTags( textarea );
				break;
			case 'undo':
			case 'redo':
				textarea.focus();
				try {
					document.execCommand( command, false, null );
				} catch ( error ) {
					// no-op
				}
				break;
			default:
				break;
		}
	}

	function wrapText( textarea, before, after ) {
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var value = textarea.value;
		var selected = value.slice( start, end ) || 'متن';

		textarea.value = value.slice( 0, start ) + before + selected + after + value.slice( end );
		textarea.focus();
		textarea.selectionStart = start + before.length;
		textarea.selectionEnd = start + before.length + selected.length;
		textarea.dispatchEvent( new Event( 'input', { bubbles: true } ) );
	}

	function prefixLines( textarea, prefix ) {
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		var value = textarea.value;

		if ( start === end ) {
			textarea.value = value.slice( 0, start ) + prefix + value.slice( start );
			textarea.selectionStart = textarea.selectionEnd = start + prefix.length;
		} else {
			var selected = value.slice( start, end );
			var replaced = selected.split( '\n' ).map( function ( line ) {
				return prefix + line;
			} ).join( '\n' );
			textarea.value = value.slice( 0, start ) + replaced + value.slice( end );
			textarea.selectionStart = start;
			textarea.selectionEnd = start + replaced.length;
		}

		textarea.focus();
		textarea.dispatchEvent( new Event( 'input', { bubbles: true } ) );
	}

	function stripSelectionTags( textarea ) {
		var start = textarea.selectionStart;
		var end = textarea.selectionEnd;
		if ( start === end ) {
			return;
		}

		var value = textarea.value;
		var selected = value.slice( start, end ).replace( /<[^>]*>/g, '' );
		textarea.value = value.slice( 0, start ) + selected + value.slice( end );
		textarea.focus();
		textarea.selectionStart = start;
		textarea.selectionEnd = start + selected.length;
		textarea.dispatchEvent( new Event( 'input', { bubbles: true } ) );
	}

	function escapeAttribute( value ) {
		var div = document.createElement( 'div' );
		div.textContent = value;
		return div.innerHTML.replace( /"/g, '&quot;' );
	}

	/* ----------------------------------------------------------------
	 * Uploads
	 * ---------------------------------------------------------------- */
	function initUploads( key, form ) {
		if ( ! form ) {
			return;
		}

		var input = form.querySelector( 'input[type="file"][data-ttk-files]' );
		var drop = form.querySelector( '[data-ttk-drop]' );
		var chips = form.querySelector( '[data-ttk-chips]' );
		var hidden = form.querySelector( 'input[name="attachments"]' );
		var pickers = form.querySelectorAll( '[data-ttk-pick]' );

		if ( ! input || ! chips ) {
			return;
		}

		var store = {
			form: form,
			input: input,
			drop: drop,
			chips: chips,
			hidden: hidden,
			items: [],
			pending: 0
		};
		uploads[ key ] = store;

		pickers.forEach( function ( button ) {
			button.addEventListener( 'click', function () {
				input.click();
			} );
		} );

		if ( drop ) {
			drop.addEventListener( 'click', function () {
				input.click();
			} );
			drop.addEventListener( 'keydown', function ( event ) {
				if ( event.key === 'Enter' || event.key === ' ' ) {
					event.preventDefault();
					input.click();
				}
			} );
			drop.addEventListener( 'dragover', function ( event ) {
				event.preventDefault();
				drop.classList.add( 'is-dragover' );
			} );
			drop.addEventListener( 'dragleave', function () {
				drop.classList.remove( 'is-dragover' );
			} );
			drop.addEventListener( 'drop', function ( event ) {
				event.preventDefault();
				drop.classList.remove( 'is-dragover' );
				if ( event.dataTransfer && event.dataTransfer.files ) {
					uploadFiles( store, event.dataTransfer.files );
				}
			} );
		}

		input.addEventListener( 'change', function () {
			uploadFiles( store, input.files );
			input.value = '';
		} );

		renderChips( store );
	}

	function uploadFiles( store, fileList ) {
		var files = Array.prototype.slice.call( fileList || [] );
		if ( ! files.length ) {
			return;
		}

		var remaining = ( cfg.maxFiles || 3 ) - store.items.length - store.pending;
		if ( remaining <= 0 ) {
			showFormError( store.form, i18n.tooMany || 'حداکثر ۳ فایل مجاز است.' );
			return;
		}

		if ( files.length > remaining ) {
			showFormError( store.form, i18n.tooMany || 'حداکثر ۳ فایل مجاز است.' );
			files = files.slice( 0, remaining );
		}

		files.forEach( function ( file ) {
			uploadOne( store, file );
		} );
	}

	function uploadOne( store, file ) {
		if ( file.size > ( cfg.maxSize || 5242880 ) ) {
			showFormError( store.form, i18n.tooLarge || 'حجم فایل بیش از حد مجاز است.' );
			return;
		}

		var chip = document.createElement( 'div' );
		chip.className = 'ttk-chip is-uploading';
		chip.textContent = ( i18n.uploading || 'در حال بارگذاری...' ) + ' ' + file.name;
		store.chips.appendChild( chip );
		store.pending += 1;

		var data = new FormData();
		data.append( 'action', 'torantejarat_ticket_upload' );
		data.append( 'nonce', cfg.uploadNonce );
		data.append( 'file', file );

		window.fetch( cfg.ajaxUrl, {
			method: 'POST',
			body: data,
			credentials: 'same-origin'
		} )
			.then( function ( response ) {
				return response.json();
			} )
			.then( function ( result ) {
				store.pending -= 1;
				chip.remove();

				if ( ! result || ! result.success || ! result.data || ! result.data.attachment ) {
					showFormError( store.form, ( result && result.data && result.data.message ) || i18n.uploadError || 'بارگذاری انجام نشد.' );
					return;
				}

				store.items.push( result.data.attachment );
				renderChips( store );
			} )
			.catch( function () {
				store.pending -= 1;
				chip.remove();
				showFormError( store.form, i18n.uploadError || 'بارگذاری انجام نشد.' );
			} );
	}

	function renderChips( store ) {
		store.chips.innerHTML = '';

		store.items.forEach( function ( item, index ) {
			var chip = document.createElement( 'div' );
			chip.className = 'ttk-chip';

			var name = document.createElement( 'span' );
			name.className = 'ttk-chip__name';
			name.textContent = item.name || '';

			var size = document.createElement( 'span' );
			size.className = 'ttk-chip__size';
			size.textContent = formatSize( item.size );

			var remove = document.createElement( 'button' );
			remove.type = 'button';
			remove.className = 'ttk-chip__remove';
			remove.setAttribute( 'aria-label', i18n.remove || 'حذف فایل' );
			remove.textContent = '×';
			remove.addEventListener( 'click', function () {
				store.items.splice( index, 1 );
				renderChips( store );
			} );

			chip.appendChild( name );
			if ( size.textContent ) {
				chip.appendChild( size );
			}
			chip.appendChild( remove );
			store.chips.appendChild( chip );
		} );

		if ( store.hidden ) {
			store.hidden.value = JSON.stringify( store.items.map( function ( item ) {
				return item.token;
			} ) );
		}
	}

	function formatSize( bytes ) {
		bytes = parseInt( bytes, 10 ) || 0;
		if ( bytes <= 0 ) {
			return '';
		}
		if ( bytes >= 1048576 ) {
			return ( bytes / 1048576 ).toFixed( 1 ) + ' مگابایت';
		}
		return Math.max( 1, Math.round( bytes / 1024 ) ) + ' کیلوبایت';
	}

	/* ----------------------------------------------------------------
	 * Forms
	 * ---------------------------------------------------------------- */
	function initCreateForm() {
		var form = document.getElementById( 'ttk-create-form' );
		if ( ! form || ! window.fetch || ! window.FormData ) {
			return;
		}

		form.addEventListener( 'submit', function ( event ) {
			if ( form.dataset.ttkBusy === '1' ) {
				event.preventDefault();
				return;
			}

			if ( ! form.checkValidity() ) {
				return;
			}

			syncEditor( form );
			var body = getBody( form );
			if ( plainLength( body ) < 10 ) {
				event.preventDefault();
				showFormError( form, 'متن پیام باید حداقل ۱۰ کاراکتر باشد.' );
				return;
			}

			event.preventDefault();
			var button = form.querySelector( 'button[type="submit"]' );
			var original = button ? button.textContent : '';

			setBusy( form, button, i18n.sending || 'در حال ارسال...' );

			var data = new FormData( form );
			data.set( 'action', 'torantejarat_ticket_create_ajax' );
			data.set( 'torantejarat_ticket_create_nonce', cfg.createNonce );

			window.fetch( cfg.ajaxUrl, {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( result ) {
					if ( result && result.success ) {
						var target = cfg.ticketsUrl;
						if ( result.data && result.data.ticket_id ) {
							target = addQuery( target, 'view_ticket=' + encodeURIComponent( result.data.ticket_id ) + '&ticket_sent=1&ticket_created=1' );
						} else {
							target = addQuery( target, 'ticket_sent=1&ticket_created=1' );
						}
						window.location.href = target;
						return;
					}
					throw new Error( ( result && result.data && result.data.message ) || i18n.generic || 'خطای نامشخص.' );
				} )
				.catch( function ( error ) {
					setBusy( form, button, original, false );
					showFormError( form, error.message );
				} );
		} );
	}

	function initReplyForm() {
		var form = document.getElementById( 'ttk-reply-form' );
		if ( ! form || ! window.fetch || ! window.FormData ) {
			return;
		}

		form.addEventListener( 'submit', function ( event ) {
			if ( form.dataset.ttkBusy === '1' ) {
				event.preventDefault();
				return;
			}

			if ( ! form.checkValidity() ) {
				return;
			}

			var body = getBody( form );
			if ( plainLength( body ) < 2 ) {
				event.preventDefault();
				showFormError( form, 'متن پاسخ را بنویسید.' );
				return;
			}

			event.preventDefault();
			var button = form.querySelector( 'button[type="submit"]' );
			var original = button ? button.textContent : '';

			setBusy( form, button, i18n.sending || 'در حال ارسال...' );

			var data = new FormData( form );
			data.set( 'action', 'torantejarat_ticket_reply_ajax' );
			data.set( 'torantejarat_ticket_reply_nonce', cfg.replyNonce );

			window.fetch( cfg.ajaxUrl, {
				method: 'POST',
				body: data,
				credentials: 'same-origin'
			} )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( result ) {
					if ( result && result.success ) {
						var target = replaceQuery( window.location.href, 'ticket_sent', '1' );
						window.location.href = removeQuery( target, 'ticket_created' );
						return;
					}
					throw new Error( ( result && result.data && result.data.message ) || i18n.generic || 'خطای نامشخص.' );
				} )
				.catch( function ( error ) {
					setBusy( form, button, original, false );
					showFormError( form, error.message );
				} );
		} );
	}

	function setBusy( form, button, label, busy ) {
		if ( busy === false ) {
			form.dataset.ttkBusy = '';
			if ( button ) {
				button.disabled = false;
				button.textContent = label;
			}
			return;
		}

		form.dataset.ttkBusy = '1';
		if ( button ) {
			button.disabled = true;
			button.textContent = label;
		}
	}

	function showFormError( form, message ) {
		var box = form.querySelector( '[data-ttk-error]' );
		if ( ! box ) {
			window.alert( message );
			return;
		}
		box.textContent = message;
		box.hidden = false;
		if ( box.scrollIntoView ) {
			box.scrollIntoView( { behavior: 'smooth', block: 'nearest' } );
		}
	}

	function syncEditor( form ) {
		var editor = form.querySelector( '[data-ttk-editor]' );
		if ( ! editor ) {
			return;
		}
		var textarea = editor.querySelector( 'textarea' );
		var rich = editor.querySelector( '[data-ttk-rich]' );
		if ( textarea && rich && editor.dataset.mode === 'visual' ) {
			textarea.value = rich.innerHTML.trim();
		}
	}

	function getBody( form ) {
		var textarea = form.querySelector( 'textarea[name="body"]' );
		return textarea ? textarea.value : '';
	}

	function plainLength( html ) {
		var div = document.createElement( 'div' );
		div.innerHTML = html;
		return ( div.textContent || '' ).replace( /\s+/g, ' ' ).trim().length;
	}

	function addQuery( url, query ) {
		return url + ( url.indexOf( '?' ) === -1 ? '?' : '&' ) + query;
	}

	function replaceQuery( url, key, value ) {
		try {
			var parsed = new URL( url );
			parsed.searchParams.set( key, value );
			return parsed.toString();
		} catch ( error ) {
			return addQuery( url, key + '=' + encodeURIComponent( value ) );
		}
	}

	function removeQuery( url, key ) {
		try {
			var parsed = new URL( url );
			parsed.searchParams.delete( key );
			return parsed.toString();
		} catch ( error ) {
			return url.replace( new RegExp( '[?&]' + key + '=[^&]*' ), '' );
		}
	}
} )();
