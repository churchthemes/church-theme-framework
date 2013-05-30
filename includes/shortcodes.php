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

function ctfw_shortcodes() {

	$shortcodes = array(
		'ctc_site_name'			=> 'ctfw_shortcode_site_name',
		'ctc_tagline'			=> 'ctfw_shortcode_tagline',
		'ctc_home_url'			=> 'ctfw_shortcode_home_url',
		'ctc_rss_url'			=> 'ctfw_shortcode_feed_url',
		'ctc_copyright_symbol'	=> 'ctfw_shortcode_copyright_symbol',
		'ctc_current_year'		=> 'ctfw_shortcode_current_year',
		'ctc_powered_logo'		=> 'ctfw_shortcode_powered_logo'
	);

	return apply_filters( 'ctfw_shortcodes', $shortcodes );

}

/**
 * Add shortcodes
 */

add_action( 'init', 'ctfw_add_shortcodes' );

function ctfw_add_shortcodes() {

	$shortcodes = ctfw_shortcodes();

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

add_filter( 'the_content', 'ctfw_content_remove_shortcodes', 0 );

function ctfw_content_remove_shortcodes( $content ) {

	$shortcodes = ctfw_shortcodes();

	foreach ( $shortcodes as $tag => $function ) {
		remove_shortcode( $tag );
	}

	return $content;
}

/**
 * Add them back after post content for use elsewhere
 */

add_filter( 'the_content', 'ctfw_content_add_shortcodes', 99 );

function ctfw_content_add_shortcodes( $content ) {

	ctfw_add_shortcodes();

	return $content;
}

/******************************************
 * SHORTCODE FUNCTIONS
 ******************************************/

// Site Name
function ctfw_shortcode_site_name() {

	return get_bloginfo( 'name' );
	
}

// Tagline
function ctfw_shortcode_tagline() {

	return get_bloginfo( 'description' );
	
}

// Current Year
function ctfw_shortcode_current_year() {

	return date( 'Y' );
	
}

// Copyright Symbol (since &copy; doesn't behave directly in theme options input field)
function ctfw_shortcode_copyright_symbol() {

	return '&copy;';
	
}

// RSS Feed URL
// [ctc_rss_url] can be used by social media icons
function ctfw_shortcode_feed_url() {

	return get_bloginfo( 'rss_url' );
	
}

// Home URL
function ctfw_shortcode_home_url() {

	return home_url( '/' );
	
}

// "Powered By" Logo
// This is handy for use in footer notice
// Enable with add_theme_support( 'ctfw-powered-logo-shortcode' );
function ctfw_shortcode_powered_logo() {

	$output = '';

	// Theme supports this shortcode?
	if ( $support = get_theme_support( 'ctfw-powered-logo-shortcode' ) ) {

		// Get URL for clicks
		$url = ! empty( $support[0] ) ? $support[0] : false;

		// Link and image for footer
		$output = '<a href="' . esc_url( $url ) . '" rel="nofollow" class="ctfw-powered-logo" target="_blank"></a>';

	}

	return $output;
	
}

