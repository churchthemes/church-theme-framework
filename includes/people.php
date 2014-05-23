<?php
/**
 * People Functions
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

/**********************************
 * PEOPLE ORDERING
 **********************************/

/**
 * Make group taxonomy archive order manually (like People template)
 *
 * Use add_theme_support( 'ctfw-people-group-manual-order' ); to enable
 *
 * @since 0.9.1
 * @param object $query Query object to modify
 * @return object Modified query
 */
function ctfw_people_group_order( $query ) {

	// Theme supports this?
	if ( current_theme_supports( 'ctfw-people-group-manual-order' ) ) {

		// On group archive
		if ( $query->is_archive && ! empty( $query->query['ctc_person_group'] ) ) {
			$query->set( 'orderby', 'menu_order' );
			$query->set( 'order', 'ASC' );
		}

	}

	return $query;

}

add_filter( 'pre_get_posts' , 'ctfw_people_group_order' );

/**********************************
 * PEOPLE DATA
 **********************************/

/**
 * Get person data
 *
 * @since 0.9
 * @param int $post_id Post ID to get data for; null for current post
 * @return array Person data
 */
function ctfw_person_data( $post_id = null ) {

	// Get meta values
	$data = ctfw_get_meta_data( array( // without _ctc_person_ prefix
		'position',
		'phone',
		'email',
		'urls',
	), $post_id );

	// Return filtered
	return apply_filters( 'ctfw_person_data', $data );

}

/**********************************
 * PEOPLE NAVIGATION
 **********************************/

/**
 * Prev/next people sorting
 *
 * This makes get_previous_post() and get_next_post() sort by manual order instead of Publish Date
 *
 * @since 0.9.0
 */
function ctfw_previous_next_person_sorting() {

	// Theme supports it?
	if ( ! current_theme_supports( 'ctfw-person-navigation' ) ) {
		return;
	}

	// While on single person, if theme supports People from Church Theme Content
	// IMPORTANT: Without ! is_page(), is_singular() runs, somehow causing /page/#/ URL's on static front page to break
	if ( ! is_page() && is_singular( 'ctc_person' ) && current_theme_supports( 'ctc-people' ) ) {

		// SQL WHERE
		add_filter( 'get_previous_post_where', 'ctfw_previous_post_where' );
		add_filter( 'get_next_post_where', 'ctfw_next_post_where' );

		// SQL ORDER BY
		add_filter( 'get_previous_post_sort', 'ctfw_previous_post_sort' );
		add_filter( 'get_next_post_sort', 'ctfw_next_post_sort' );

	}

}

add_action( 'wp', 'ctfw_previous_next_person_sorting' ); // is_singular() not available until wp action (after posts_selection)
