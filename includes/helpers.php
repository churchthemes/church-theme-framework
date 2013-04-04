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

	$url = 'http';
	if ( is_ssl() ) {
		$url .= 's';
	}
	
	$url .= '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

	return $url;

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
			$processed_string .= _x( '&hellip;', 'shortened text', 'church-theme' );
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
 * Make a Church Content Manager post type or taxonomy name friendly
 *
 * This is handy for get_template_part( 'content', ctc_make_friendly( get_post_type() ) );
 * which produces content-gallery-item.php instead of content-ccm_gallery_item.php
 */

function ctc_make_friendly( $string ) {

	$friendly_string = str_replace( array( 'ccm_', '_'), array( '', '-'), $string );

	return apply_filters( 'ctc_make_friendly', $friendly_string, $string );
}

