<?php
/**
 * Taxonomy-related Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Detect CTC taxonomy support
 *
 * If not supported, theme or plugin causes taxonomy to register with show_ui to false
 * This is used in widgets to show and render fields dependent on taxonomies.
 *
 * Note: this is intended for use only after Church Content registers taxonomies since that is when show_ui is available.
 *
 * @since 0.9
 * @param string $taxonomy_name Taxonomy name
 */
function ctfw_ctc_taxonomy_supported( $taxonomy_name ) {

	// Get taxonomy data
	$taxonomy = get_taxonomy( $taxonomy_name );

	// If show_ui is true, taxonomy is supported by theme and plugin
	$supported = ! empty( $taxonomy->show_ui ) ? true : false;

	// Return filterable
	return apply_filters( 'ctfw_ctc_taxonomy_supported', $supported, $taxonomy_name );

}

/**
 * Taxonomy term options
 *
 * Returns ID/name pairs useful for creating select options and sanitizing on front-end.
 *
 * @since 0.9
 * @param string $taxonomy_name Taxonomy slug
 * @param array $prepend Array to start with such as "All" or similar
 * @return array ID/name pairs
 */
function ctfw_term_options( $taxonomy_name, $prepend = array() ) {

	$options = array();

	if ( ! preg_match( '/^ctc_/', $taxonomy_name ) || ctfw_ctc_taxonomy_supported( $taxonomy_name ) ) { // make sure CTC taxonomy support

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
