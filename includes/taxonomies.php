<?php
/**
 * Taxonomy-related Functions
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Detect taxonomy support
 *
 * If not supported, theme or plugin causes taxonomy to register with show_ui to false
 * This is used in widgets to show and render fields dependent on taxonomies.
 *
 * Note: this is intended for use only after Church Content Manager registers taxonomies since that is when show_ui is available.
 */
 
function ctfw_taxonomy_supported( $taxonomy_name ) {

	// Get taxonomy data
	$taxonomy = get_taxonomy( $taxonomy_name );

	// If show_ui is true, taxonomy is supported by theme and plugin
	$supported = ! empty( $taxonomy->show_ui ) ? true : false;
		
	// Return filterable
	return apply_filters( 'ctfw_taxonomy_supported', $supported, $taxonomy_name );

}

/**
 * Taxonomy term options
 *
 * Returns ID/name pairs useful for creating select options.
 * Prepend can be an array to start with, such as "All" or similar.
 */

function ctfw_term_options( $taxonomy_name, $prepend = array() ) {

	$options = array();

	if ( ctfw_taxonomy_supported( $taxonomy_name ) ) {

		$terms = $categories = get_terms( $taxonomy_name );

		if ( ! empty( $prepend ) ) {
			$options = $prepend;
		}

		foreach ( $terms as $term ) {
			$options[$term->term_id] = $term->name;
		}

	}

	return apply_filters( 'ctfw_term_options', $options, $taxonomy_name, $prepend );

}

/**
 * Show custom ordering tip
 *
 * add_theme_support( 'ctfw-taxonomy-order-note', $url ) will cause a taxonomy ordering plugin
 * to be recommended in a note beneath taxonomy lists. $url is to override the default recommendation.
 *
 * This is handy in particular for people groups and sermon speakers.
 */

add_action( 'admin_init', 'ctfw_taxonomy_order_notes' );

function ctfw_taxonomy_order_notes() {

	// Only if theme supports it
	if ( ! current_theme_supports( 'ctfw-taxonomy-order-note' ) ) {
		return false;
	}

	// Get public taxonomies
	$taxonomies = get_taxonomies( array(
		'public'	=> true,
		'show_ui'	=> true // weed out post_format
	) );

	// Add note to each
	foreach ( $taxonomies as $taxonomy ) {
		add_action( 'after-' . $taxonomy . '-table', 'ctfw_taxonomy_order_note' );
	}

}

function ctfw_taxonomy_order_note( $taxonomy ) {

	// Only if theme requests this
	if ( $support = get_theme_support( 'ctfw-taxonomy-order-note' ) ) { // returns false if feature not supported

		// Get URL if not using default
		$url = isset( $support[0] ) ? $support[0] : 'http://churchthemes.com/go/taxonomy-order';

		// Get taxonomy plural
		$taxonomy_obj = get_taxonomy( $taxonomy );
		$taxonomy_plural = strtolower( $taxonomy_obj->labels->name );

		// Show message
		echo '<p class="description">';
		printf(
			__( '<b>Custom Ordering:</b> Try <a href="%s" target="_blank">this plugin</a> for custom ordering your %s.', 'church-theme-framework' ),
			$url,
			$taxonomy_plural
		);
		echo '</p>';

	}

}
