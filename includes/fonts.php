<?php
/**
 * Font Functions
 *
 * These functions help setup and integrate custom fonts such as Google Fonts.
 */

/*******************************************
 * FONT STACKS
 *******************************************/

/**
 * Define Font Stacks
 *
 * Default font stacks for each type of font
 */

function ctc_default_font_stacks() {

	// These fonts in the given order when available will be used for each type if for whatever reason the browser cannot load the custom font
	$default_font_stacks = array(
		'serif'			=> "Georgia, 'Bitstream Vera Serif', 'Times New Roman', Times, serif",
		'sans-serif'	=> "Arial, Helvetica, sans-serif",
		'display'		=> "Arial, Helvetica, sans-serif",
		'handwriting'	=> "Georgia, 'Bitstream Vera Serif', 'Times New Roman', Times, cursive"
	);

	// Enable filtering to change default font stacks
	$default_font_stacks = apply_filters( 'ctc_default_font_stacks', $default_font_stacks );

	return $default_font_stacks;

}

/**
 * Font Stack based on font's type
 *
 * Build a font stack based on font and its type - use in CSS
 */

function ctc_font_stack( $font, $available_fonts ) {

	// Get the default font stack for each type
	$default_font_stacks = ctc_default_font_stacks();

	// Build font stack with custom font as primary
	if ( ! empty( $available_fonts[$font] ) && ! empty( $default_font_stacks[$available_fonts[$font]['type']] ) ) {
		$default_font_stack = $default_font_stacks[$available_fonts[$font]['type']];
	} else { // if invalid, type use first in list (should be serif)
		$default_font_stack = current( $default_font_stacks );
	}
	$font_stack = "'" . $font . "', " . $default_font_stack;

	// Filterable
	$font_stack = apply_filters( 'ctc_font_stack', $font_stack, $font, $available_fonts );
	
	return $font_stack;

}

/*******************************************
 * Google Fonts
 *******************************************/

/**
 * Google Fonts stylesheet URL for enqueuing
 */
 
function ctc_google_fonts_style_url( $fonts, $available_fonts, $font_subsets = false ) {
	
	$url = '';
	
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
		$font_list = implode( '|', $font_array );
		
		// Subset passed in? Format it
		$subset_attr = '';
		if ( ! empty( $font_subsets ) ) {
			$font_subsets = str_replace( ' ', '', $font_subsets ); // in case spaces between commas
			if ( ! empty( $font_subsets ) && 'latin' != $font_subsets ) {
				$subset_attr = '&subset=' . $font_subsets;
			}
		}

		// Build URL
		$url = ctc_current_protocol() . '://fonts.googleapis.com/css?family=' . $font_list . $subset_attr;
		
	}
	
	// Return filtered
	return apply_filters( 'ctc_google_fonts_style_url', $url, $fonts, $available_fonts, $font_subsets );
	
}
