/**
 * Copy-to-clipboard for the tracking code button (account order page).
 */
document.addEventListener( 'click', function ( e ) {
	var btn = e.target.closest( '.torantejarat-copy-btn' );
	if ( ! btn ) {
		return;
	}
	var el = document.getElementById( btn.getAttribute( 'data-copy-target' ) );
	if ( ! el || ! navigator.clipboard ) {
		return;
	}
	var label = btn.querySelector( '[data-copy-label]' );
	var original = label ? label.textContent : '';
	navigator.clipboard.writeText( el.textContent.trim() ).then( function () {
		if ( label ) {
			label.textContent = 'کپی شد';
		}
		setTimeout( function () {
			if ( label ) {
				label.textContent = original;
			}
		}, 1800 );
	} );
} );
