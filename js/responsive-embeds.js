/**
 * Responsive Embeds
 */

jQuery(document).ready(function($) {

	// Remove <object> element from Blip.tv (use iframe only) - creates a gap w/FitVid
	$( "embed[src*='blip.tv']").remove();

	// Use FitVid for responsive videos and other embeds
	// YouTube and Vimeo work out of the box
	// Rdio and Spotify are correct when loading at final size (browser resize is bad demo)
	$( 'body' ).fitVids({ // content and sidebar
		customSelector: [
			"iframe[src*='youtu.be']",
			"iframe[src*='blip.tv']",
			"iframe[src*='hulu.com']",
			"iframe[src*='dailymotion.com']",
			"iframe[src*='revision3.com']",
			"iframe[src*='slideshare.net']",
			"iframe[src*='scribed.com']",
			"iframe[src*='viddler.com']",
			"iframe[src*='rd.io']",
			"iframe[src*='rdio.com']",
			"iframe[src*='spotify.com']"
		]
	});

	// Other embedded media only need max-width: 100% (height is static) - SoundCloud, MediaElement.js, etc.
	// Important: when done via stylesheet, MediaElement.js volume control flickers
	$( "iframe[src*='soundcloud.com'], iframe[src*='snd.sc'], .wp-video-shortcode, .wp-audio-shortcode" ).css( 'max-width', '100%' );


});
