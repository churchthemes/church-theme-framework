<?php
/**
 * Taxonomy-related Admin Functions
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

/**
 * Taxonomy term options
 *
 * Returns ID/name pairs useful for creating select options.
 *
 * @since 0.9
 * @param string $taxonomy_name Taxonomy slug
 * @param array $prepend Array to start with such as "All" or similar
 * @return array ID/name pairs
 */
function ctfw_term_options( $taxonomy_name, $prepend = array() ) {

	$options = array();

	if ( ! preg_match( '/^ccm_/', $taxonomy_name ) || ctfw_ccm_taxonomy_supported( $taxonomy_name ) ) { // make sure CCM taxonomy support

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
 * Show custom ordering notes
 *
 * add_theme_support( 'ctfw-taxonomy-order-note', $url ) will cause a taxonomy ordering plugin
 * to be recommended in a note beneath taxonomy lists. $url is to override the default recommendation.
 *
 * This is handy in particular for people groups and sermon speakers.
 *
 * @since 0.9
 */
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

add_action( 'admin_init', 'ctfw_taxonomy_order_notes' );

/**
 * Show custom ordering note
 *
 * @since 0.9
 * @param string $taxonomy Taxonomy to affect
 */
function ctfw_taxonomy_order_note( $taxonomy ) {

	// Only if theme requests this
	$support = get_theme_support( 'ctfw-taxonomy-order-note' );
	if ( $support ) { // returns false if feature not supported

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
