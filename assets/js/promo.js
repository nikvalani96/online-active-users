( function () {
	'use strict';

	document.addEventListener( 'click', function ( event ) {
		var button = event.target.closest( '.wpoau-campaign__copy-button' );
		if ( ! button ) {
			return;
		}
		var coupon = button.dataset.coupon || '';
		if ( ! coupon ) {
			return;
		}
		var copied = navigator.clipboard ? navigator.clipboard.writeText( coupon ) : fallbackCopy( coupon );
		Promise.resolve( copied ).then( function () {
			button.textContent = button.dataset.copiedLabel || 'Copied!';
			button.classList.add( 'is-copied' );
			window.setTimeout( function () {
				button.textContent = button.dataset.copyLabel || 'Copy';
				button.classList.remove( 'is-copied' );
			}, 1800 );
		} ).catch( function () {
			button.textContent = coupon;
		} );
	} );

	function fallbackCopy( value ) {
		var input = document.createElement( 'textarea' );
		input.value = value;
		input.setAttribute( 'readonly', '' );
		input.style.position = 'fixed';
		input.style.opacity = '0';
		document.body.appendChild( input );
		input.select();
		var copied = document.execCommand( 'copy' );
		document.body.removeChild( input );
		return copied ? Promise.resolve() : Promise.reject();
	}
}() );
