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
 * Note: Use // in place of http:// or https:// instead of this.
 * http://stackoverflow.com/questions/550038/is-it-valid-to-replace-http-with-in-a-script-src-http
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

	if ( ctfw_is_url( $url ) && preg_match( '/^' . preg_quote( home_url(), '/' ) . '/', $url ) ) {
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

	$path = '';

	$parsed_url = parse_url( home_url( '/' ) );

	if ( isset( $parsed_url['path'] ) ) {
		$path = $parsed_url['path'];
	}

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

/**
 * Sanitize URL List
 *
 * Make sure URL is not empty and not invalid.
 * Shortcodes can be whitelisted with $allowed_strings.
 *
 * This is useful for social media icon URLs in Customizer.
 *
 * @since 1.1.4
 * @param string|array $urls String having URLs one per line, or array
 * @param array Strings to always allow, such as shortcodes
 * @return string Sanitized list of URLs
 */
function ctfw_sanitize_url_list( $urls, $allowed_strings = array() ) {

	// Convert to array
	$urls_array = $urls;
	if ( ! is_array( $urls ) ) {
		$urls_array = explode( "\n", $urls_array );
	}
	$urls_array = (array) $urls_array; // in case one as string

	// Loop each URL line to build sanitized array
	$sanitized_urls = array();
	foreach ( $urls_array as $key => $url ) {

		// Remove whitespace from ends
		$url = trim( $url );

		// Sanitize URL
		// Unless string is explicitly allowed, use as is (such as a shortcode)
		if ( ! in_array( $url, $allowed_strings ) ) {

			$url = esc_url_raw( $url, array(
				'http',
				'https',
				'feed',
				'itms', // iTunes Music Store
				'skype',
				'mailto',
			) );

		}

		// Add to new list
		// May have been empty to begin with, after trim or after URL escaping
		if ( ! empty( $url ) ) {
			$sanitized_urls[] = $url;
		}

	}

	// Convert sanitized array to list
	$sanitized_urls = implode( "\n", $sanitized_urls );

	// Return sanitized filterable
	return apply_filters( 'ctfw_sanitize_url_list', $sanitized_urls, $urls, $allowed_strings );

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

/**
 * Show array as HTML
 *
 * This is helpful for development / debugging
 *
 * @since 1.4
 * @param array $array Array to format
 * @param bool $return Return or echo output
 */
function ctfw_print_array( $array, $return = false ) {

	$result = '<pre>' . print_r( $array, true ) . '</pre>';

	if ( empty($return) ) {
		echo $result;
	} else {
		return $result;
	}

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

	// Use multibyte functions if available (helpful for non-English characters)
	// Some hosts disable multibyte functions
	if ( function_exists( 'mb_strlen' ) && function_exists( 'mb_substr' ) && function_exists( 'mb_strrpos' ) ) {

		// Shorten to within X characters without cutting words in half
		if ( $max_chars && mb_strlen( $string ) > $max_chars ) {

			// Shorten
			$haystack = mb_substr( $string, 0, $max_chars );
			$length = mb_strrpos( $haystack, ' ' );
			$processed_string = mb_substr( $string, 0, $length );

			// Append ... if string was shortened
			if ( mb_strlen( $processed_string ) < mb_strlen( $string ) ) {
				/* translators: ... after shortened string */
				$processed_string .= _x( '&hellip;', 'shortened text', 'church-theme-framework' );
			}

		}

	}

	// Same code as above but using non-multibyte functions
	else {

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
function ctfw_one_line( $string ) {

	$one_line = $string;

	if ( $string ) {
		$one_line = strip_tags( $string ); // remove HTML
		$one_line = preg_replace( '/\r\n|\n|\r/', ', ', $one_line ); // replace line breaks with commas
		$one_line = trim( $one_line ); // remove whitespace
	}

	return apply_filters( 'ctfw_one_line', $one_line, $string );

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

	$address_one_line = ctfw_one_line( $address );

	return apply_filters( 'ctfw_address_one_line', $address_one_line, $address );

}

/**
 * Make a Church Theme Content post type or taxonomy name friendly
 *
 * This is handy for get_template_part( CTFW_THEME_PARTIAL_DIR . '/content', ctfw_make_friendly( get_post_type() ) );
 * which produces content-post-type.php instead of content-ctc_post_type.php
 *
 * @since 0.9
 * @param string $string Post type or other prefixed CTC slug to make friendly
 * @return string Friendlier string without prefix
 */
function ctfw_make_friendly( $string ) {

	$friendly_string = str_replace( array( 'ctc_', '_'), array( '', '-'), $string );

	return apply_filters( 'ctfw_make_friendly', $friendly_string, $string );

}
