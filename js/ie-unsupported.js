// This can be loaded for old versions of Internet Explorer

// Hide content
jQuery(document).ready(function($) {
	alert('test');
	$('body')
		.empty() // remove content
		.css('background', 'none'); // hide background
});

// Tell user to upgrade to a modern browser
alert(ctc_ie_unsupported.message);

// Redirect to a site with upgrade details
window.location = ctc_ie_unsupported.redirect_url;
