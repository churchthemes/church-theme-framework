<?php
/**
 * Color Scheme Functions
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

/*******************************************
 * COLOR SCHEMES
 *******************************************/

/**
 * Return array of color schemes from colors directory
 *
 * If styles exist in child theme then use those instead of parent
 *
 * @since 0.9
 * @return array Color schemes
 */
function ctfw_colors() {

	$parent_styles_dir = CTFW_THEME_PATH . '/' . CTFW_THEME_COLOR_DIR; // parent theme
	$child_styles_dir = CTFW_THEME_CHILD_PATH . '/' . CTFW_THEME_COLOR_DIR;
	$styles_dir = file_exists( $child_styles_dir ) ? $child_styles_dir : $parent_styles_dir;  // if a styles dir was made for child theme, use it

	$colors = array();
	if ( file_exists( $styles_dir ) && $handle = opendir( $styles_dir ) ) { // if colors directory exists in child theme, use it
		while ( false !== ( $entry = readdir($handle) ) ) { // loop style schemes available in style directory
			if ( ! preg_match( '/\./', $entry ) ) { // directories only
				$style_name = str_replace( array( '-', '_' ), ' ', $entry ); // replace - and _ with space
				$style_name = ucwords( $style_name ); // capitalize words
				$colors[$entry] = $style_name;
			}
		}
		closedir( $handle );
	}

	$colors = apply_filters( 'ctfw_colors', $colors );

	return $colors;

}

/**
 * Check if color scheme is valid
 *
 * If none is provided, it will check the active color scheme.
 *
 * @since 0.9
 * @param string $color Color scheme; empty to use currently active color
 * @return bool True if color scheme exists
 */
function ctfw_valid_color( $color = false ) {

	$valid = false;

	// Use active if none given
	if ( empty( $color ) ) {
		$color = ctfw_customization( 'color' );
	}

	$colors = ctfw_colors();

	if ( ! empty( $colors[$color] ) ) {
		$valid = true;
	}

	return apply_filters( 'ctfw_valid_color', $valid, $color );

}

/**
 * Retrieve URL of a file in color scheme
 *
 * Checks first in child (if exists), then parent.
 * If no color scheme given, active color scheme used.
 *
 * @since 0.9
 * @param string $file File in a color scheme
 * @param string $color Color scheme; empty to use currently active color
 * @return string URL of file in color scheme
 */
function ctfw_color_url( $file, $color = false ) {

	// Use active color scheme if none specified
	if ( empty( $color ) ) {
		$color = ctfw_customization( 'color' );
	}

	// Validate color scheme
	// (even active one, to prevent any messing with cookies in front-end style customizer)
	if ( ctfw_valid_color( $color ) ) {
		$url = ctfw_theme_url( CTFW_THEME_COLOR_DIR . '/' . $color . '/' . ltrim( $file, '/' ) );
	} else {
		$url = '';
	}

	// Return filterable
	return apply_filters( 'ctfw_color_url', $url, $file, $color );

}

/**
 * Color scheme stylesheet URL
 *
 * This can be used to enqueue the stylesheet.
 *
 * @since 0.9
 * @param string $theme 'child' or 'parent'
 * @return string URL of color scheme stylesheet
 */
function ctfw_color_style_url( $theme = false ) {

	$url = '';

	// Make sure active color scheme is valid so nobody tries to mess with file path (ie. via front-end style picker cookie)
	if ( ctfw_valid_color() ) {

		$color = ctfw_customization( 'color' );

		$color_rel = CTFW_THEME_COLOR_DIR . '/' . $color . '/style.css';

		$color_parent_path = CTFW_THEME_PATH . '/' . $color_rel;
		$color_parent_url = CTFW_THEME_URL . '/' . $color_rel;

		$color_child_path = CTFW_THEME_CHILD_PATH . '/' . $color_rel;
		$color_child_url = CTFW_THEME_CHILD_URL . '/' . $color_rel;

		// Force parent version
		if ( 'parent' == $theme && file_exists( $color_parent_path ) ) {
			$url = $color_parent_url;
		}

		// Force child version
		else if ( 'child' == $theme && file_exists( $color_child_path ) ) {
			$url = $color_child_url;
		}

		// Auto-detect (default)
		// If parent or child not explicit, use default behavior (child if exists, otherwise parent)
		else {
			$url = ctfw_theme_url( $color_rel ); // use child theme version if provided
		}

	}

	// Return filtered
	return apply_filters( 'ctfw_color_style_url', $url, $theme );

}
