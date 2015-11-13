/**
 * Responsive Embeds
 */

// Use FitVids.js for responsive videos and other embeds
// Note: Rdio and Spotify are correct when loading at final size ( browser resize is bad demo )
ctfw_embed_fitvids_selectors = [

	// Default (from fitVids.js)
	// We redefine these here so they can bi hidden prior to FitVids.js
	"iframe[src*='player.vimeo.com']",
	"iframe[src*='youtube.com']",
	"iframe[src*='youtube-nocookie.com']",
	"iframe[src*='kickstarter.com'][src*='video.html']",
	"object",
	"embed",

	// Custom
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
	"iframe[src*='spotify.com']",
	"iframe[src*='soundcloud.com']:not([width$='%'])", // Jetpack soundcloud shortcode is already responsive with %, so exclude
	"iframe[src*='snd.sc']",
	"iframe[src*='livestream.com']",

];
ctfw_embed_fitvids_selectors_list = ctfw_embed_fitvids_selectors.join( ', ' );

// Other embedded media only need max-width: 100% ( height is static ) - MediaElement.js
// Important: when done via stylesheet, MediaElement.js volume control flickers
ctfw_embed_other_selectors_list = '.wp-video-shortcode, .wp-audio-shortcode';

// Hide videos before resizing
// This keeps them from showing briefly at small size before showing at full width
ctfw_embed_all_selectors_list = ctfw_embed_fitvids_selectors_list + ', ' + ctfw_embed_other_selectors_list;
jQuery( 'head' ).prepend( '<style type="text/css" id="ctfw-hide-responsive-embeds">' + ctfw_embed_all_selectors_list + ' { visibility: hidden; }</style>' + "\n" );

// Resize videos to 100% width
jQuery( document ).ready( function( $ ) {

	// Remove <object> element from Blip.tv ( use iframe only ) - creates a gap w/FitVid
	$( "embed[src*='blip.tv']" ).remove();

	// FitVids.js for most embeds
	$( 'body' ).fitVids( {
		customSelector: ctfw_embed_fitvids_selectors
	} );

	// Other embeds (MediaElement.js)
	$( ctfw_embed_other_selectors_list ).css( 'max-width', '100%' );

	// Show embeds after resize
	$( '#ctfw-hide-responsive-embeds' ).remove();

} );
