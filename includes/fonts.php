<?php
/**
 * Font Functions
 *
 * These functions help setup and integrate custom fonts such as Google Fonts.
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
 * FONT STACKS
 *******************************************/

/**
 * Define font stacks
 *
 * Default font stacks for each type of font
 *
 * @since 0.9
 * @return array Default font stacks
 */
function ctfw_default_font_stacks() {

	// These fonts in the given order when available will be used for each type if for whatever reason the browser cannot load the custom font
	$default_font_stacks = array(
		'serif'			=> "Georgia, 'Bitstream Vera Serif', 'Times New Roman', Times, serif",
		'sans-serif'	=> "Arial, Helvetica, sans-serif",
		'display'		=> "Arial, Helvetica, sans-serif",
		'handwriting'	=> "Georgia, 'Bitstream Vera Serif', 'Times New Roman', Times, cursive"
	);

	// Enable filtering to change default font stacks
	$default_font_stacks = apply_filters( 'ctfw_default_font_stacks', $default_font_stacks );

	return $default_font_stacks;

}

/**
 * Font stack based on font's type
 *
 * Build a font stack based on font and its type -- use in CSS
 *
 * @since 0.9
 * @param string $font Font in $available_fonts
 * @param array $available_fonts Fonts available for use
 * @return array Font stack for font
 */
function ctfw_font_stack( $font, $available_fonts ) {

	// Get the default font stack for each type
	$default_font_stacks = ctfw_default_font_stacks();

	// Build font stack with custom font as primary
	if ( ! empty( $available_fonts[$font] ) && ! empty( $default_font_stacks[$available_fonts[$font]['type']] ) ) {
		$default_font_stack = $default_font_stacks[$available_fonts[$font]['type']];
	} else { // if invalid, type use first in list (should be serif)
		$default_font_stack = current( $default_font_stacks );
	}
	$font_stack = "'" . $font . "', " . $default_font_stack;

	// Filterable
	$font_stack = apply_filters( 'ctfw_font_stack', $font_stack, $font, $available_fonts );

	return $font_stack;

}

/*******************************************
 * GOOGLE FONTS
 *******************************************/

/**
 * Return Google Fonts array
 *
 * Optionally filter by target to narrrow results.
 * Theme should filter ctfw_google_fonts to make fonts available to framework.
 * It should use this function for getting fonts.
 *
 * @since 0.9.2
 * @param string $target If want to narrow results to specific target
 * @return array Fonts with size and type
 */
function ctfw_google_fonts( $target = false ) {

	// Get fonts from theme
	$fonts = apply_filters( 'ctfw_google_fonts', array() );

	// Narrow to specific target
	if ( ! empty( $target ) ) {

		foreach ( $fonts as $font => $font_data ) {

			if ( ! empty( $font_data['targets'] ) && ! in_array( $target, $font_data['targets'] ) ) { // if no targets, use in all
				unset( $fonts[$font] );
			}

		}

	}

	// Return fonts
	return $fonts;

}

/**
 * Google Font options array
 *
 * Alphabetical array of font names useful for Customizer fields and other select inputs.
 *
 * @since 0.9.2
 * @param array $options Narrow fonts by specific target, show type, etc.
 * @return array Array of fonts with name as key and friendly name as value
 */
function ctfw_google_font_options_array( $options = array() ) {

	$font_options = array();

	// Default options
	$options = wp_parse_args( $options, array(
		'target'	=> '',
		'show_type'	=> false
	) );

	// Get fonts, optionally by target
	$google_fonts = ctfw_google_fonts( $options['target'] );

	// Loop fonts
	foreach ( $google_fonts as $font_name => $font_data ) {

		$font_options[$font_name] = $font_name;

		// Show type
		if ( $options['show_type'] && ! empty( $font_data['type'] ) ) {
			$font_options[$font_name] .= ' (' . $font_data['type'] . ')';
		}

	}

	// Sort alphabetical
	ksort( $font_options );

	return apply_filters( 'ctfw_google_font_options_array', $font_options, $options );

}

/**
 * Google Fonts stylesheet URL for enqueuing
 *
 * @since 0.9
 * @param array $fonts Fonts to load from Google Fonts
 * @param array $available_fonts Fonts available for use
 * @param string $font_subsets Optional character sets to load
 * @return string Google Fonts stylesheet URL
 */
function ctfw_google_fonts_style_url( $fonts, $font_subsets = false ) {

	$url = '';

	$available_fonts = ctfw_google_fonts();

	// In case there is one
	$fonts = (array) $fonts;

	// No duplicates
	$fonts = array_unique( $fonts );

	// Build array of fonts
	$font_array = array();
	foreach ( $fonts as $font ) {
		if ( ! empty( $available_fonts[$font] ) ) { // font is valid
			$font_array[] = urlencode( $font ) . ( ! empty( $available_fonts[$font]['sizes'] ) ? ':' . $available_fonts[$font]['sizes'] : '' );
		}
	}

	// Have font(s)...
	if ( ! empty( $font_array ) ) {

		// Build list from array
		//$font_list = implode( '|', $font_array );
		$font_list = implode( '%7C', $font_array ); // HTML5-valid: http://bit.ly/1xfv8yA

		// Subset passed in? Format it
		$subset_attr = '';
		if ( ! empty( $font_subsets ) ) {
			$font_subsets = str_replace( ' ', '', $font_subsets ); // in case spaces between commas
			if ( ! empty( $font_subsets ) && 'latin' != $font_subsets ) {
				$subset_attr = '&subset=' . $font_subsets;
			}
		}

		// Build URL
		$url = '//fonts.googleapis.com/css?family=' . $font_list . $subset_attr;

	}

	// Return filtered
	return apply_filters( 'ctfw_google_fonts_style_url', $url, $fonts, $available_fonts, $font_subsets );

}
