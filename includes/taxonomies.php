<?php
/**
 * Taxonomy-related Functions
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

/**
 * Taxonomy term options
 *
 * Returns ID/name pairs useful for creating select options.
 * Prepend can be an array to start with, such as "All" or similar.
 */

function ctc_term_options( $taxonomy_name, $prepend = array() ) {

	$options = array();

	if ( ctc_taxonomy_supported( $taxonomy_name ) ) {

		$terms = $categories = get_terms( $taxonomy_name );

		if ( ! empty( $prepend ) ) {
			$options = $prepend;
		}

		foreach ( $terms as $term ) {
			$options[$term->term_id] = $term->name;
		}

	}

	return apply_filters( 'ctc_term_options', $options, $taxonomy_name, $prepend );

}
