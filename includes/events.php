<?php
/**
 * Event Functions
 */

/**********************************
 * DATA
 **********************************/

/**
 * Get events based on arguments
 *
 * Arguments match those used by events widget
 * May also be used elsewhere (e.g. upcoming events in header)
 */

function ctc_get_events( $args = array() ) {

	// Defaults
	$args['timeframe'] = ! empty( $args['timeframe'] ) ? $args['timeframe'] : 'upcoming';
	$args['limit'] = isset( $args['limit'] ) ? absint( $args['limit'] ) : -1; // default no limit

	// Show upcoming events
	$compare = '>=';  // all events with start OR end date today or later
	$meta_key = '_ccm_event_start_date'; // order by this; want earliest starting date first
	$order = 'ASC'; // sort from soonest to latest

	// Show past events
	if ( 'past' == $args['timeframe'] ) {
		$compare = '<'; // all events with start AND end date BEFORE today
		$meta_key = '_ccm_event_end_date'; // order by this; want finish date first
		$order = 'DESC'; // sort from most recently past to oldest
	}

	// Get events
	$posts = get_posts( array(
		'post_type'			=> 'ccm_event',
		'numberposts'		=> $args['limit'],
		'meta_query' => array(
			array(
				'key' => '_ccm_event_end_date', // the latest date that the event goes to (could be start date)
				'value' => date_i18n( 'Y-m-d' ), // today's date, localized
				'compare' => $compare,
				'type' => 'DATE'
			),
		),
		'meta_key' 			=> $meta_key,
		'orderby'			=> 'meta_value',
		'order'				=> $order
	) );

	// Return filtered
	return apply_filters( 'ctc_get_events', $posts, $args );

}

/**
 * Get event meta data
 */

function ctc_event_data( $post_id = null ) {

	// Get meta values
	$meta = ctc_get_meta_data( array(
		'start_date',
		'end_date',
		'time',
		'venue',
		'address',
		'show_directions_link',
		'map_lat',
		'map_lng',
		'map_type',
		'map_zoom'
	), $post_id );

	// Add directions URL (empty if show_directions_link not set)
	$meta['directions_url'] = $meta['show_directions_link'] ? ctc_directions_url( $meta['address'] ) : '';

	// Return filtered
	return apply_filters( 'ctc_event_meta', $meta, $post_id );

}

