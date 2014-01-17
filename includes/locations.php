<?php
/**
 * Location Functions
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
 * LOCATION DATA
 **********************************/

/**
 * Get location data
 *
 * @since 0.9
 * @param int $post_id Post ID to get data for; null for current post
 * @return array Location data
 */
function ctfw_location_data( $post_id = null ) {

	// Get meta values
	$data = ctfw_get_meta_data( array(
		'address',
		'show_directions_link',
		'phone',
		'times',
		'map_lat',
		'map_lng',
		'map_type',
		'map_zoom'
	), $post_id );

	// Add directions URL (empty if show_directions_link not set)
	$data['directions_url'] = $data['show_directions_link'] ? ctfw_directions_url( $data['address'] ) : '';

	// Return filtered
	return apply_filters( 'ctfw_location_data', $data, $post_id );

}

/**
 * Get first location
 *
 * Can be useful for showing primary location in footer or place of prominence
 *
 * @since 1.0.9
 * @return array Location data
 */
function ctfw_first_location() {

	$location_id = 0;
	$location = array();
	
	// Get first location
	$locations = get_posts( array(
		'post_type'			=> 'ctc_location',
		'orderby'			=> 'menu_order', // first manually ordered
		'order'				=> 'ASC',
		'numberposts'		=> 1,
		'suppress_filters'	=> false // assist multilingual
	) );

	// Get location ID
	if ( isset( $locations[0] ) && ! empty( $locations[0]->ID ) ) {
		$location_id = $locations[0]->ID;
	}

	// Get location data
	if ( $location_id ) {
		$location = ctfw_location_data( $location_id );
	}

	// Return filtered
	return apply_filters( 'ctfw_first_location', $location );

}

/**********************************
 * LOCATION NAVIGATION
 **********************************/

/**
 * Prev/next location sorting
 * 
 * This makes get_previous_post() and get_next_post() sort by manual order instead of Publish Date
 *
 * @since 0.9.1
 */
function ctfw_previous_next_location_sorting() {

	// Theme supports it?
	if ( ! current_theme_supports( 'ctfw-location-navigation' ) ) {
		return;
	}

	// While on single location, if theme supports Locations from Church Theme Content
	// IMPORTANT: Without ! is_page(), is_singular() runs, somehow causing /page/#/ URL's on static front page to break
	if ( ! is_page() && is_singular( 'ctc_location' ) && current_theme_supports( 'ctc-locations' ) ) {

		// SQL WHERE
		add_filter( 'get_previous_post_where', 'ctfw_previous_post_where' );
		add_filter( 'get_next_post_where', 'ctfw_next_post_where' );

		// SQL ORDER BY
		add_filter( 'get_previous_post_sort', 'ctfw_previous_post_sort' );
		add_filter( 'get_next_post_sort', 'ctfw_next_post_sort' );

	}

}

add_action( 'wp', 'ctfw_previous_next_location_sorting' ); // is_singular() not available until wp action (after posts_selection)
