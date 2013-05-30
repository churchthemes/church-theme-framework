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
 * http or https protocol
 */
 
function ctfw_current_protocol() {

	$protocol = is_ssl() ? 'https' : 'http';

	return apply_filters( 'ctfw_current_protocol', $protocol );

}

/**
 * Check if string is a URL
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
 */

function ctfw_site_path() {

	// Just get everything after the domain in the site URL
	list( , $path ) = explode( $_SERVER['HTTP_HOST'], site_url( '/' ) );

	return apply_filters( 'ctfw_site_path', $path );

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
 
function ctfw_array_merge_after_key( $original_array, $insert_array, $after_key ) {

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
 */

function ctfw_make_friendly( $string ) {

	$friendly_string = str_replace( array( 'ccm_', '_'), array( '', '-'), $string );

	return apply_filters( 'ctfw_make_friendly', $friendly_string, $string );
}

