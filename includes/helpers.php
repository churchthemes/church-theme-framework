<?php
/**
 * Helper Functions
 *
 * General helper functions.
 */

/*************************************************
 * URLs
 *************************************************/

/**
 * Current URL
 */
 
function ctc_current_url() {

	$url = ctc_current_protocol() . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	return $url;

}

/**
 * http or https protocol
 */
 
function ctc_current_protocol() {

	$protocol = is_ssl() ? 'https' : 'http';

	return apply_filters( 'ctc_current_protocol', $protocol );

}

/*************************************************
 * ARRAYS
 *************************************************/

/**
 * Merge an array into another array after a specific key
 *
 * Meant for one dimensional associative arrays.
 * Used to insert post type overview columns.
 */
 
function ctc_array_merge_after_key( $original_array, $insert_array, $after_key ) {

	$modified_array = array();

	// loop original array items
	foreach( $original_array as $item_key => $item_value ) {
	
		// rebuild the array one item at a time
		$modified_array[$item_key] = $item_value;
		
		// insert array after specific key
		if ( $item_key == $after_key ) {
			$modified_array = array_merge( $modified_array, $insert_array );
		}
	
	}

	return apply_filters( 'ctc_array_merge_after_key', $modified_array, $original_array, $insert_array, $after_key );

}

/*************************************************
 * STRINGS
 *************************************************/

/**
 * Shorten string within character limit while preserving words
 *
 * An alternative to wp_trim_words().
 * Useful when need strict character limit but don't want to cut words in half.
 */

function ctc_shorten( $string, $max_chars ) {

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
			$processed_string .= _x( '&hellip;', 'shortened text', 'ct-framework' );
		}

	}

	// Use original string if none shortened
	if ( empty( $processed_string ) ) {
		$processed_string = $string;
	}

	// Return filtered
	return apply_filters( 'ctc_shorten', $processed_string, $string, $max_chars );

}

/**
 * Convert address to one line
 *
 * It replaces line breaks with commas.
 */

function ctc_address_one_line( $address ) {

	$address_one_line = $address;

	if ( $address ) {
		$address_one_line = strip_tags( $address ); // remove HTML
		$address_one_line = str_replace( "\n", ', ', $address_one_line ); // replace line breaks with commas
		$address_one_line = trim( $address_one_line ); // remove whitespace
	}

	return apply_filters( 'ctc_address_one_line', $address_one_line, $address );

}

/**
 * Make a Church Content Manager post type or taxonomy name friendly
 *
 * This is handy for get_template_part( 'content', ctc_make_friendly( get_post_type() ) );
 * which produces content-post-type.php instead of content-ccm_post_type.php
 */

function ctc_make_friendly( $string ) {

	$friendly_string = str_replace( array( 'ccm_', '_'), array( '', '-'), $string );

	return apply_filters( 'ctc_make_friendly', $friendly_string, $string );
}

