<?php
/**
 * Helper Functions
 *
 * General helper functions.
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

/*************************************************
 * URLs
 *************************************************/

/**
 * http or https protocol
 *
 * @since 0.9
 * @return string http or https protocol
 */
function ctfw_current_protocol() {

	$protocol = is_ssl() ? 'https' : 'http';

	return apply_filters( 'ctfw_current_protocol', $protocol );

}

/**
 * Check if string is a URL
 *
 * @since 0.9
 * @param string $string String to check for URL format
 * @return bool True if string i=s URL
 */
function ctfw_is_url( $string ) {

	$bool = false;

	$url_pattern = '/^(http(s*)):\/\//i';

	if ( preg_match( $url_pattern, $string ) ) { // URL
		$bool = true;
	}

	return apply_filters( 'ctfw_is_url', $bool, $string );

}

/**
 * Check if URL is local
 *
 * @since 0.9
 * @param string $url URL to test
 * @return bool True if URL is local
 */ 
function ctfw_is_local_url( $url ) {

	$bool = false;

	if ( ctfw_is_url( $url ) && preg_match( '/^' . preg_quote( site_url(), '/' ) . '/', $url ) ) {
		$bool = true;
	}

	return apply_filters( 'ctfw_is_local_url', $bool, $url );

}

/**
 * Site path (base URL relative to domain)
 *
 * yourname.com/site becomes /site (useful for cookie path)
 *
 * @since 0.9
 * @return string Site path
 */
function ctfw_site_path() {

	// Just get everything after the domain in the site URL
	list( , $path ) = explode( $_SERVER['HTTP_HOST'], site_url( '/' ) );

	return apply_filters( 'ctfw_site_path', $path );

}

/**
 * Retrieve the url of a file in the theme. 
 * 
 * Searches in the stylesheet directory before the template directory so themes 
 * which inherit from a parent theme can just override one file.
 * 
 * This is from here and will likely be part of WordPress core. At that time, move this to deprecated.php.
 * http://core.trac.wordpress.org/attachment/ticket/18302/18302.12.diff
 * http://core.trac.wordpress.org/ticket/18302
 * 
 * @since 0.9
 * @param string $file File to search for in the stylesheet directory
 * @return string The URL of the file
 */
function ctfw_theme_url( $file = '' ) {

	$file = ltrim( $file, '/' ); 
 
	if ( empty( $file ) ) { 
		$url = get_stylesheet_directory_uri(); 
	} elseif( is_child_theme() && file_exists( get_stylesheet_directory() . "/$file" ) ) { 
		$url = get_stylesheet_directory_uri() . "/$file"; 
	} else { 
		$url = get_template_directory_uri() . "/$file"; 
	}
	
	return apply_filters( 'ctfw_theme_url', $url, $file );
	
}

/*************************************************
 * ARRAYS
 *************************************************/

/**
 * Merge an array into another array after a specific key
 *
 * Meant for one dimensional associative arrays.
 * Used to insert post type overview columns.
 *
 * @since 0.9
 * @param array $original_array Array to merge another into
 * @param array $insert_array Array to merge into original
 * @param mixed $after_key Key in original array to merge second array after
 * @return array Modified array
 */
function ctfw_array_merge_after_key( $original_array, $insert_array, $after_key ) {

	$modified_array = array();

	// loop original array items
	foreach ( $original_array as $item_key => $item_value ) {
	
		// rebuild the array one item at a time
		$modified_array[$item_key] = $item_value;
		
		// insert array after specific key
		if ( $item_key == $after_key ) {
			$modified_array = array_merge( $modified_array, $insert_array );
		}
	
	}

	return apply_filters( 'ctfw_array_merge_after_key', $modified_array, $original_array, $insert_array, $after_key );

}

/*************************************************
 * STRINGS
 *************************************************/

/**
 * Shorten string within character limit while preserving words
 *
 * An alternative to wp_trim_words().
 * Useful when need strict character limit but don't want to cut words in half.
 *
 * @since 0.9
 * @param string $string Text string to shorten
 * @param int $max_chars Maximum number of characters shortened string should have
 * @return string Modified string if shortening necesary
 */
function ctfw_shorten( $string, $max_chars ) {

	$max_chars = absint( $max_chars );

	// Shorten to within X characters without cutting words in half
	if ( $max_chars && strlen( $string ) > $max_chars ) {

		// Shorten
		$haystack = substr( $string, 0, $max_chars );
		$length = strrpos( $haystack, ' ' );
		$processed_string = substr( $string, 0, $length );

		// Append ... if string was shortened
		if ( strlen( $processed_string ) < strlen( $string ) ) {
			/* translators: ... after shortened string */
			$processed_string .= _x( '&hellip;', 'shortened text', 'church-theme-framework' );
		}

	}

	// Use original string if none shortened
	if ( empty( $processed_string ) ) {
		$processed_string = $string;
	}

	// Return filtered
	return apply_filters( 'ctfw_shorten', $processed_string, $string, $max_chars );

}

/**
 * Convert address to one line
 *
 * It replaces line breaks with commas.
 *
 * @since 0.9
 * @param string $address Multi-line address
 * @return string Single line address
 */
function ctfw_address_one_line( $address ) {

	$address_one_line = $address;

	if ( $address ) {
		$address_one_line = strip_tags( $address ); // remove HTML
		$address_one_line = str_replace( "\n", ', ', $address_one_line ); // replace line breaks with commas
		$address_one_line = trim( $address_one_line ); // remove whitespace
	}

	return apply_filters( 'ctfw_address_one_line', $address_one_line, $address );

}

/**
 * Make a Church Content Manager post type or taxonomy name friendly
 *
 * This is handy for get_template_part( 'content', ctfw_make_friendly( get_post_type() ) );
 * which produces content-post-type.php instead of content-ccm_post_type.php
 *
 * @since 0.9
 * @param string $string Post type or other prefixed CCM slug to make friendly
 * @return string Friendlier string without prefix
 */
function ctfw_make_friendly( $string ) {

	$friendly_string = str_replace( array( 'ccm_', '_'), array( '', '-'), $string );

	return apply_filters( 'ctfw_make_friendly', $friendly_string, $string );

}
