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

function ctc_location_data( $post_id = null ) {

	// Get meta values
	$meta = ctc_get_meta_data( array(
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
	$meta['directions_url'] = $meta['show_directions_link'] ? ctc_directions_url( $meta['address'] ) : '';

	// Return filtered
	return apply_filters( 'ctc_location_meta', $meta, $post_id );

}

