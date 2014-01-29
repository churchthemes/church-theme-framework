<?php
/**
 * Non-content Shortcodes
 *
 * IMPORTANT: These shortcodes are not intended for use in content. They are for options, customizer, etc.
 * Content shortcodes do not belong in a theme but rather in a plugin so they still work after a theme switch.
 *
 * WARNING: Never change core WordPress content filtering to "fix" shortcode formatting. Third party
 * plugins and other shortcodes will very likely be adversely affected.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/******************************************
 * REGISTER SHORTCODES
 ******************************************/

/**
 * Shortcodes to handle
 *
 * @since 0.9
 * @return array Shortcodes to register or unregister
 */
function ctfw_shortcodes() {

	$shortcodes = array(
		'ctcom_site_name'			=> 'ctfw_shortcode_site_name',
		'ctcom_rss_url'			=> 'ctfw_shortcode_rss_url',
		'ctcom_current_year'		=> 'ctfw_shortcode_current_year'
	);

	return apply_filters( 'ctfw_shortcodes', $shortcodes );

}

/**
 * Add shortcodes
 *
 * @since 0.9
 */
function ctfw_add_shortcodes() {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-non-content-shortcodes' ) ) {
		return;
	}

	$shortcodes = ctfw_shortcodes();

	foreach ( $shortcodes as $tag => $function ) {
		add_shortcode( $tag, $function );
	}

}

add_action( 'init', 'ctfw_add_shortcodes' );

/******************************************
 * DISALLOW IN CONTENT
 ******************************************/

// Thanks to Justin Tadlock for this tip: http://justintadlock.com/archives/2013/01/08/disallow-specific-shortcodes-in-post-content

/**
 * Remove shortcodes from post content
 *
 * @since 0.9
 * @param string $content Post content
 * @return string Post content (unmodified)
 */
function ctfw_content_remove_shortcodes( $content ) {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-non-content-shortcodes' ) ) {
		return $content;
	}

	$shortcodes = ctfw_shortcodes();

	foreach ( $shortcodes as $tag => $function ) {
		remove_shortcode( $tag );
	}

	return $content;
}

add_filter( 'the_content', 'ctfw_content_remove_shortcodes', 0 );

/**
 * Add them back after post content for use elsewhere
 *
 * @since 0.9
 * @param string $content Post content
 * @return string Post content (unmodified)
 */
function ctfw_content_add_shortcodes( $content ) {

	ctfw_add_shortcodes();

	return $content;
}

add_filter( 'the_content', 'ctfw_content_add_shortcodes', 99 );

/******************************************
 * SHORTCODE FUNCTIONS
 ******************************************/

/**
 * Site name
 *
 * @since 0.9
 * @return string Site name
 */
function ctfw_shortcode_site_name() {
	return get_bloginfo( 'name' );
}

/**
 * Current year
 *
 * @since 0.9
 * @return string Four digit year
 */
function ctfw_shortcode_current_year() {
	return date( 'Y' );
}

/**
 * RSS feed URL
 * 
 * [ctcom_rss_url] can be used by social media icons
 * 
 * @since 0.9
 * @return string RSS feed URL
 */
function ctfw_shortcode_rss_url() {
	return get_bloginfo( 'rss_url' );
}
