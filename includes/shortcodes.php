<?php
/**
 * Non-content Shortcodes
 *
 * IMPORTANT: These shortcodes are not intended for use in content. They are for options, customizer, etc.
 * Content shortcodes do not belong in a theme but rather in a plugin so they still work after a theme switch.
 *
 * WARNING: Never change core WordPress content filtering to "fix" shortcode formatting. Third party
 * plugins and other shortcodes will very likely be adversely affected.
 */

/******************************************
 * REGISTER SHORTCODES
 ******************************************/
 
add_action( 'init', 'ctc_fw_register_shortcodes' );

function ctc_fw_register_shortcodes() {

	add_shortcode( 'ctc_site_name', 		'ctc_shortcode_site_name' );
	add_shortcode( 'ctc_tagline', 			'ctc_shortcode_tagline' );
	add_shortcode( 'ctc_home_url', 			'ctc_shortcode_home_url' );
	add_shortcode( 'ctc_rss_url', 			'ctc_shortcode_feed_url' );
	add_shortcode( 'ctc_copyright_symbol',	'ctc_shortcode_copyright_symbol' );
	add_shortcode( 'ctc_current_year',		'ctc_shortcode_current_year' );

	if ( current_theme_supports( 'ctc-powered-logo-shortcode' ) ) {
		add_shortcode( 'ctc_powered_logo',	'ctc_shortcode_powered_logo' );
	}

}

/******************************************
 * SHORTCODE FUNCTIONS
 ******************************************/

// Site Name
function ctc_shortcode_site_name() {

	return get_bloginfo( 'name' );
	
}

// Tagline
function ctc_shortcode_tagline() {

	return get_bloginfo( 'description' );
	
}

// Current Year
function ctc_shortcode_current_year() {

	return date( 'Y' );
	
}

// Copyright Symbol (since &copy; doesn't behave directly in theme options input field)
function ctc_shortcode_copyright_symbol() {

	return '&copy;';
	
}

// RSS Feed URL
// [ctc_rss_url] can be used by social media icons
function ctc_shortcode_feed_url() {

	return get_bloginfo( 'rss_url' );
	
}

// Home URL
function ctc_shortcode_home_url() {

	return home_url( '/' );
	
}

// "Powered By" Logo
// This is handy for use in footer notice
// Enable with add_theme_support( 'ctc-powered-logo-shortcode' );
function ctc_shortcode_powered_logo() {

	$output = '';

	// Theme supports this shortcode?
	if ( $support = get_theme_support( 'ctc-powered-logo-shortcode' ) ) {

		// Get URL for clicks
		$url = ! empty( $support[0] ) ? $support[0] : false;

		// Link and image for footer
		$output = '<a href="' . esc_url( $url ) . '" rel="nofollow" class="ctc-powered-logo" target="_blank"></a>';

	}

	return $output;
	
}

