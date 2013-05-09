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

/**
 * Shortcodes to handle
 */

function ctc_fw_shortcodes() {

	$shortcodes = array(
		'ctc_site_name'			=> 'ctc_shortcode_site_name',
		'ctc_tagline'			=> 'ctc_shortcode_tagline',
		'ctc_home_url'			=> 'ctc_shortcode_home_url',
		'ctc_rss_url'			=> 'ctc_shortcode_feed_url',
		'ctc_copyright_symbol'	=> 'ctc_shortcode_copyright_symbol',
		'ctc_current_year'		=> 'ctc_shortcode_current_year',
		'ctc_powered_logo'		=> 'ctc_shortcode_powered_logo'
	);

	return apply_filters( 'ctc_fw_shortcodes', $shortcodes );

}

/**
 * Add shortcodes
 */

add_action( 'init', 'ctc_fw_add_shortcodes' );

function ctc_fw_add_shortcodes() {

	$shortcodes = ctc_fw_shortcodes();

	foreach ( $shortcodes as $tag => $function ) {
		add_shortcode( $tag, $function );
	}

}

/******************************************
 * DISALLOW IN CONTENT
 ******************************************/

// Thanks to Justin Tadlock for this tip: http://justintadlock.com/archives/2013/01/08/disallow-specific-shortcodes-in-post-content

/**
 * Remove shortcodes from post content
 */

add_filter( 'the_content', 'ctc_fw_content_remove_shortcodes', 0 );

function ctc_fw_content_remove_shortcodes( $content ) {

	$shortcodes = ctc_fw_shortcodes();

	foreach ( $shortcodes as $tag => $function ) {
		remove_shortcode( $tag );
	}

	return $content;
}

/**
 * Add them back after post content for use elsewhere
 */

add_filter( 'the_content', 'ctc_fw_content_add_shortcodes', 99 );

function ctc_fw_content_add_shortcodes( $content ) {

	ctc_fw_add_shortcodes();

	return $content;
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

