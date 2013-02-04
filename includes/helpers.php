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

if ( ! function_exists( 'ctc_array_merge_after_key' ) ) {
	 
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

		return $modified_array;

	}
	
}

