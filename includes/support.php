<?php
/**
 * Feature Support
 *
 * Functions relating to feature support as manipulated by theme and Church Content Manager plugin.
 */

/**
 * Detect taxonomy support
 *
 * If not supported, theme or plugin causes taxonomy to register with show_ui to false
 * This is used in widgets to show and render fields dependent on taxonomies.
 *
 * Note: this is intended for use only after Church Content Manager registers taxonomies since that is when show_ui is available.
 */
 
function ctc_taxonomy_supported( $taxonomy_name ) {

	// Get taxonomy data
	$taxonomy = get_taxonomy( $taxonomy_name );

	// If show_ui is true, taxonomy is supported by theme and plugin
	$supported = ! empty( $taxonomy->show_ui ) ? true : false;
		
	// Return filterable
	return apply_filters( 'ctc_taxonomy_supported', $supported, $taxonomy_name );

}

