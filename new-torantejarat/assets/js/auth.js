/**
 * Login / Register — بلکوکم
 * Tabs, validation, password strength, Persian digit normalization,
 * show/hide password. Server-ready: set data-endpoint on each form.
 */
( function () {
	'use strict';

	var REDUCED = window.matchMedia( '(prefers-reduced-motion: reduce)' ).matches;

	/* ---------- Persian / Arabic digit normalization ---------- */
	function toEnDigits( value ) {
		var fa = '۰۱۲۳۴۵۶۷۸۹';
		var ar = '٠١٢٣٤٥٦٧٨٩';
		return String( value ).replace( /[۰-۹]/g, function ( ch ) {
			return fa.indexOf( ch );
		} ).replace( /[٠-٩]/g, function ( ch ) {
			return ar.indexOf( ch );
		} );
	}

	function normalizeMobile( value ) {
		var digits = toEnDigits( value ).replace( /[^0-9]/g, '' );
		if ( digits.startsWith( '0098' ) ) {
			digits = '0' + digits.slice( 4 );
		} else if ( digits.startsWith( '98' ) && digits.length === 12 ) {
			digits = '0' + digits.slice( 2 );
		}
		return digits;
	}

	function isValidMobile( value ) {
		return /^09\d{9}$/.test( value );
	}

	function isValidEmail( value ) {
		return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test( value );
	}

	function isMobileLike( value ) {
		var digits = toEnDigits( value ).replace( /[^0-9]/g, '' );
		return /^0?9\d{9}$/.test( digits ) || /^09\d{9}$/.test( digits );
	}

	/* ---------- Tabs ---------- */
	var tabs = document.querySelectorAll( '.auth__tab' );
	var panes = {
		login: document.getElementById( 'auth-panel-login' ),
		register: document.getElementById( 'auth-panel-register' )
	};

	function activateTab( name, updateHash ) {
		tabs.forEach( function ( tab ) {
			var isActive = tab.dataset.tab === name;
			tab.classList.toggle( 'is-active', isActive );
			tab.setAttribute( 'aria-selected', isActive ? 'true' : 'false' );
		} );
		Object.keys( panes ).forEach( function ( key ) {
			var show = key === name;
			panes[ key ].hidden = ! show;
			panes[ key ].classList.toggle( 'is-active', show );
		} );
		if ( updateHash !== false ) {
			var hash = '#' + name;
			if ( window.location.hash !== hash ) {
				history.replaceState( null, '', hash );
			}
		}
		var first = panes[ name ].querySelector( 'input' );
		if ( first ) {
			first.focus( { preventScroll: true } );
		}
	}

	tabs.forEach( function ( tab ) {
		tab.addEventListener( 'click', function () {
			activateTab( tab.dataset.tab, true );
		} );
	} );

	/* hash deep link: #login / #register */
	var initial = window.location.hash.replace( '#', '' );
	activateTab( panes[ initial ] ? initial : 'login', false );

	/* switch links inside forms */
	document.querySelectorAll( '.auth__switch-btn' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			activateTab( btn.dataset.goto, true );
		} );
	} );

	/* arrow keys between tabs */
	document.querySelector( '.auth__tabs' ).addEventListener( 'keydown', function ( e ) {
		if ( e.key !== 'ArrowLeft' && e.key !== 'ArrowRight' && e.key !== 'Home' && e.key !== 'End' ) {
			return;
		}
		e.preventDefault();
		var names = [ 'login', 'register' ];
		var current = names.indexOf( window.location.hash.replace( '#', '' ) );
		if ( current === -1 ) {
			current = 0;
		}
		var next = current;
		if ( e.key === 'ArrowLeft' || e.key === 'ArrowRight' ) {
			next = ( current + ( e.key === 'ArrowRight' ? 1 : -1 ) + names.length ) % names.length;
		} else if ( e.key === 'Home' ) {
			next = 0;
		} else if ( e.key === 'End' ) {
			next = names.length - 1;
		}
		activateTab( names[ next ], true );
	} );

	/* ---------- Password visibility ---------- */
	document.querySelectorAll( '.auth__pass-toggle' ).forEach( function ( btn ) {
		btn.addEventListener( 'click', function () {
			var input = btn.parentElement.querySelector( 'input' );
			if ( ! input ) {
				return;
			}
			var show = input.type === 'password';
			input.type = show ? 'text' : 'password';
			btn.setAttribute( 'aria-pressed', show ? 'true' : 'false' );
			btn.setAttribute( 'aria-label', show ? 'مخفی کردن رمز عبور' : 'نمایش رمز عبور' );
		} );
	} );

	/* ---------- Password strength ---------- */
	var passInput = document.getElementById( 'reg-pass' );
	var meter = document.querySelector( '.auth__meter' );
	if ( passInput && meter ) {
		passInput.addEventListener( 'input', function () {
			var v = passInput.value;
			var level = 0;
			if ( v.length >= 8 ) {
				level = 1;
			}
			if ( /[0-9]/.test( v ) && /[a-zA-Z]/.test( v ) ) {
				level = 2;
			}
			if ( level >= 2 && /[^a-zA-Z0-9]/.test( v ) ) {
				level = 3;
			}
			if ( level >= 3 && v.length >= 12 ) {
				level = 4;
			}
			meter.dataset.level = String( level );
		} );
	}

	/* ---------- Field error helpers ---------- */
	function setFieldError( input, message ) {
		var field = input.closest( '.auth__field' );
		if ( ! field ) {
			return;
		}
		var p = field.querySelector( '.auth__error' );
		if ( p ) {
			p.textContent = message || '';
		}
		if ( message ) {
			input.setAttribute( 'aria-invalid', 'true' );
		} else {
			input.removeAttribute( 'aria-invalid' );
		}
	}

	function clearErrors( form ) {
		form.querySelectorAll( '.auth__error' ).forEach( function ( p ) {
			p.textContent = '';
		} );
		form.querySelectorAll( '[aria-invalid]' ).forEach( function ( el ) {
			el.removeAttribute( 'aria-invalid' );
		} );
	}

	function firstInvalid( inputs ) {
		for ( var i = 0; i < inputs.length; i++ ) {
			if ( inputs[ i ].getAttribute( 'aria-invalid' ) === 'true' ) {
				return inputs[ i ];
			}
		}
		return null;
	}

	/* ---------- Validation rules ---------- */
	function validateLogin( form ) {
		clearErrors( form );
		var user = form.querySelector( '#login-user' );
		var pass = form.querySelector( '#login-pass' );
		var ok = true;
		var userVal = String( user.value || '' ).trim();
		if ( ! userVal ) {
			setFieldError( user, 'موبایل، ایمیل یا نام کاربری را وارد کنید.' );
			ok = false;
		} else if ( isMobileLike( userVal ) && ! isValidMobile( normalizeMobile( userVal ) ) ) {
			setFieldError( user, 'شماره موبایل معتبر نیست (مثال: 09123456789).' );
			ok = false;
		} else if ( ! isMobileLike( userVal ) && ! isValidEmail( userVal ) && userVal.length < 3 ) {
			setFieldError( user, 'ایمیل معتبر نیست.' );
			ok = false;
		}
		if ( ! pass.value ) {
			setFieldError( pass, 'رمز عبور را وارد کنید.' );
			ok = false;
		}
		return ok;
	}

	function validateRegister( form ) {
		clearErrors( form );
		var name = form.querySelector( '#reg-name' );
		var mobile = form.querySelector( '#reg-mobile' );
		var email = form.querySelector( '#reg-email' );
		var pass1 = form.querySelector( '#reg-pass' );
		var pass2 = form.querySelector( '#reg-pass2' );
		var terms = form.querySelector( 'input[name="terms"]' );
		var ok = true;

		if ( String( name.value || '' ).trim().length < 3 ) {
			setFieldError( name, 'نام و نام خانوادگی را کامل وارد کنید.' );
			ok = false;
		}
		if ( ! isValidMobile( normalizeMobile( mobile.value ) ) ) {
			setFieldError( mobile, 'شماره موبایل معتبر نیست (مثال: 09123456789).' );
			ok = false;
		}
		var emailVal = String( email.value || '' ).trim();
		if ( emailVal && ! isValidEmail( emailVal ) ) {
			setFieldError( email, 'ایمیل معتبر نیست.' );
			ok = false;
		}
		if ( pass1.value.length < 8 ) {
			setFieldError( pass1, 'رمز عبور حداقل ۸ کاراکتر باشد.' );
			ok = false;
		}
		if ( pass2.value !== pass1.value ) {
			setFieldError( pass2, 'تکرار رمز عبور مطابقت ندارد.' );
			ok = false;
		}
		if ( terms && ! terms.checked ) {
			var termsErr = form.querySelector( '[data-error-for="terms"]' );
			if ( termsErr ) {
				termsErr.textContent = 'پذیرش قوانین الزامی است.';
			}
			ok = false;
		}
		return ok;
	}

	/* ---------- Submit (server-ready) ---------- */
	function handleSubmit( form, validator, type ) {
		if ( ! validator( form ) ) {
			var first = firstInvalid( form.querySelectorAll( 'input' ) );
			if ( first ) {
				first.focus( { preventScroll: false } );
			}
			return;
		}
		var msg = form.querySelector( '.auth__message' );
		var endpoint = form.dataset.endpoint;
		var submitBtn = form.querySelector( 'button[type="submit"]' );

		function setLoading( loading ) {
			if ( ! submitBtn ) {
				return;
			}
			submitBtn.disabled = loading;
			if ( loading ) {
				submitBtn.dataset.label = submitBtn.textContent;
				submitBtn.textContent = 'لطفاً منتظر بمانید…';
			} else if ( submitBtn.dataset.label ) {
				submitBtn.textContent = submitBtn.dataset.label;
			}
		}

		function showMessage( success, text ) {
			if ( ! msg ) {
				return;
			}
			msg.textContent = text || '';
			msg.className = 'auth__message ' + ( success ? 'is-success' : 'is-error' );
			msg.setAttribute( 'role', 'alert' );
		}

		if ( ! endpoint ) {
			showMessage( false, 'حالت نمایشی: اتصال سرور فعال نیست.' );
			return;
		}

		setLoading( true );
		var body = new URLSearchParams();
		body.append( 'action', type );
		body.append( 'nonce', window.torantejaratAuth ? window.torantejaratAuth.nonce : '' );
		new FormData( form ).forEach( function ( value, key ) {
			body.append( key, value );
		} );
		fetch( endpoint, {
			method: 'POST',
			headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
			body: body.toString()
		} ).then( function ( res ) {
			return res.json();
		} ).then( function ( data ) {
			setLoading( false );
			showMessage( data.success, data.message || '' );
			if ( data.success && data.redirect ) {
				window.location.href = data.redirect;
			}
		} ).catch( function () {
			setLoading( false );
			showMessage( false, 'خطا در ارتباط با سرور. دوباره تلاش کنید.' );
		} );
	}

	var loginForm = document.getElementById( 'auth-login-form' );
	var registerForm = document.getElementById( 'auth-register-form' );

	if ( loginForm ) {
		loginForm.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			handleSubmit( loginForm, validateLogin, 'torantejarat_login' );
		} );
	}

	if ( registerForm ) {
		registerForm.addEventListener( 'submit', function ( e ) {
			e.preventDefault();
			handleSubmit( registerForm, validateRegister, 'torantejarat_register' );
		} );
	}
} )();
