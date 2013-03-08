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
 * Retrieve the url of a file in the theme. 
 * 
 * Searches in the stylesheet directory before the template directory so themes 
 * which inherit from a parent theme can just override one file.
 * 
 * This is from here and will likely be part of WordPress core. At that time, move this to deprecated.php.
 * http://core.trac.wordpress.org/attachment/ticket/18302/18302.12.diff
 * http://core.trac.wordpress.org/ticket/18302
 * 
 * @param string $file File to search for in the stylesheet directory. 
 * @return string The URL of the file. 
 */

function ctc_theme_url( $file = '' ) {

	$file = ltrim( $file, '/' ); 
 
	if ( empty( $file ) ) { 
		$url = get_stylesheet_directory_uri(); 
	} elseif( is_child_theme() && file_exists( get_stylesheet_directory() . "/$file" ) ) { 
		$url = get_stylesheet_directory_uri() . "/$file"; 
	} else { 
		$url = get_template_directory_uri() . "/$file"; 
	}
	
	return apply_filters( 'ctc_theme_url', $url, $file ); 
	
}

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
