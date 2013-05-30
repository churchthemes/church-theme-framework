<?php
/**
 * Location Functions
 */

/**********************************
 * DATA
 **********************************/

/**
 * Get location meta data
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

