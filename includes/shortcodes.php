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
	add_shortcode( 'ctc_feed_url', 			'ctc_shortcode_feed_url' );
	add_shortcode( 'ctc_copyright_symbol',	'ctc_shortcode_copyright_symbol' );
	add_shortcode( 'ctc_current_year',		'ctc_shortcode_current_year' );	

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
// [ctc_feed_url] can be used by social media icons
function ctc_shortcode_feed_url() {

	return get_bloginfo( 'rss_url' );
	
}

// Home URL
function ctc_shortcode_home_url() {

	return home_url();
	
}