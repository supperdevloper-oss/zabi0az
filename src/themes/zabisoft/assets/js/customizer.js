/**
 * Live preview helpers for the Customizer.
 *
 * @package ZabiSoft
 */
( function ( $ ) {
	'use strict';

	wp.customize( 'blogname', function ( value ) {
		value.bind( function ( to ) {
			$( '.zs-site-title a' ).text( to );
		} );
	} );

	wp.customize( 'blogdescription', function ( value ) {
		value.bind( function ( to ) {
			$( '.zs-site-description' ).text( to );
		} );
	} );
}( jQuery ) );
