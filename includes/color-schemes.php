<?php
/**
 * Color Scheme Functions
 */

/*******************************************
 * COLOR SCHEMES
 *******************************************/

/**
 * Return array of color schemes from color-schemes directory
 *
 * If styles exist in child theme then use those instead of parent
 */

function ctc_color_schemes() {

	$parent_styles_dir = CTC_THEME_PATH . '/' . CTC_COLOR_DIR; // parent theme
	$child_styles_dir = CTC_CHILD_PATH . '/' . CTC_COLOR_DIR;
	$styles_dir = file_exists( $child_styles_dir ) ? $child_styles_dir : $parent_styles_dir;  // if a styles dir was made for child theme, use it
	
	$color_schemes = array();
	if ( file_exists( $styles_dir ) && $handle = opendir( $styles_dir ) ) { // if color-schemes directory exists in child theme, use it
		while ( false !== ( $entry = readdir($handle) ) ) { // loop style schemes available in style directory
			if ( ! preg_match( '/\./', $entry ) ) { // directories only
				$style_name = str_replace( array( '-', '_' ), ' ', $entry ); // replace - and _ with space
				$style_name = ucwords( $style_name ); // capitalize words
				$color_schemes[$entry] = $style_name;
			}
		}
		closedir( $handle );
	}

	$color_schemes = apply_filters( 'ctc_color_schemes', $color_schemes );
	
	return $color_schemes;
 
}

/**
 * Check if color scheme is valid
 * Make sure active color scheme is valid so nobody tries to mess with file path via front-end style picker cookie
 */

if ( ! function_exists( 'ctc_valid_color_scheme' ) ) {

	function ctc_valid_color_scheme() {

		$color_schemes = ctc_color_schemes();
		$color_scheme = ctc_customization( 'color_scheme' );

		if ( ! empty( $color_schemes[$color_scheme] ) ) {
			return true;
		}
		
		return false;
	
	}
	
}

/**
 * Check if child is overriding the active color scheme
 *
 * Used by child theme
 */

if ( ! function_exists( 'ctc_child_color_scheme_exists' ) ) {

	function ctc_child_color_scheme_exists() {

		if ( ctc_valid_color_scheme() ) { // make sure active color scheme is valid as security precaution

			$color_scheme = ctc_customization( 'color_scheme' );
			$color_scheme_child_path = CTC_CHILD_PATH . '/' . CTC_COLOR_DIR . '/' . $color_scheme . '/style.css';
		
			if ( file_exists( $color_scheme_child_path ) ) {
				return true;
			}
			
		}
		
		return false;
	
	}
	
}

/**
 * Color Scheme Style URL
 *
 * This can be used to enqueue the stylesheet.
 */

function ctc_color_scheme_style_url( $theme = false ) {

	$url = '';

	// Make sure active color scheme is valid so nobody tries to mess with file path (ie. via front-end style picker cookie)
	if ( ctc_valid_color_scheme() ) {

		$color_scheme = ctc_customization( 'color_scheme' );

		$color_scheme_rel = CTC_COLOR_DIR . '/' . $color_scheme . '/style.css';
		
		$color_scheme_parent_path = CTC_THEME_PATH . '/' . $color_scheme_rel;
		$color_scheme_parent_url = CTC_THEME_URL . '/' . $color_scheme_rel;
		
		$color_scheme_child_path = CTC_CHILD_PATH . '/' . $color_scheme_rel;
		$color_scheme_child_url = CTC_CHILD_URL . '/' . $color_scheme_rel;
	
		// Force parent version
		if ( 'parent' == $theme && file_exists( $color_scheme_parent_path ) ) {
			$url = $color_scheme_parent_url;
		}

		// Force child version
		else if ( 'child' == $theme && file_exists( $color_scheme_child_path ) ) {
			$url = $color_scheme_child_url;
		}
		
		// Auto-detect (default)
		// If parent or child not explicit, use default behavior (child if exists, otherwise parent)
		else {
			$url = ctc_theme_url( $color_scheme_rel ); // use child theme version if provided
		}

	}
	
	// Return filtered
	return apply_filters( 'ctc_color_scheme_style_url', $url, $theme );
	
}
