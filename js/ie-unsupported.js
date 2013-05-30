/**
 * This can be loaded for old versions of Internet Explorer
 */

// Hide content
jQuery( document ).ready( function( $ ) {
	$( 'body' )
		.empty() // remove content
		.css( 'background', 'none' ); // hide background
} );

// Tell user to upgrade to a modern browser
alert( ctfw_ie_unsupported.message );

// Redirect to a site with upgrade details
window.location = ctfw_ie_unsupported.redirect_url;
