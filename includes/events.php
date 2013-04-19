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

	// Add friendly date
	$date_format = get_option( 'date_format' );
	if ( $meta['end_date'] != $meta['start_date'] ) { // date range
		/* translators: date range */
		$meta['date'] = sprintf(
			__( '%s &ndash; %s', 'church-theme' ),
			date_i18n( $date_format, strtotime( $meta['start_date'] ) ),
			date_i18n( $date_format, strtotime( $meta['end_date'] ) )
		);
	} else { // start date only
		$meta['date'] = date_i18n( $date_format, strtotime( $meta['start_date'] ) );
	}

	// Add directions URL (empty if show_directions_link not set)
	$meta['directions_url'] = $meta['show_directions_link'] ? ctc_directions_url( $meta['address'] ) : '';

	// Return filtered
	return apply_filters( 'ctc_event_meta', $meta, $post_id );

}

/**********************************
 * NAVIGATION
 **********************************/

/**
 * Prev/Next Event Sorting
 * 
 * This makes get_previous_post() and get_next_post() sort by event Start Date instead of Publish Date
 */

add_action( 'wp', 'ctc_previous_next_event_sorting' ); // is_singular() not available until wp action (after posts_selection)

function ctc_previous_next_event_sorting() {

	// While on single event, if theme supports Events from Church Content Manager
	if ( is_singular( 'ccm_event' ) && current_theme_supports( 'ccm-events' ) ) {

		// SQL JOIN
		add_filter( 'get_previous_post_join', 'ctc_previous_next_event_join' );
		add_filter( 'get_next_post_join', 'ctc_previous_next_event_join' );

		// SQL WHERE
		add_filter( 'get_previous_post_where', 'ctc_previous_event_where' );
		add_filter( 'get_next_post_where', 'ctc_next_event_where' );

		// SQL ORDER BY
		add_filter( 'get_previous_post_sort', 'ctc_previous_event_sort' );
		add_filter( 'get_next_post_sort', 'ctc_next_event_sort' );

	}

}

/**
 * SQL JOIN for Prev/Next Event
 *
 * Get events meta for WHERE and ORDER BY to use.
 */

function ctc_previous_next_event_join( $join ) {

	global $wpdb;

	return "INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID";

}

/**
 * SQL WHERE for Prev/Next Event
 */

function ctc_previous_next_event_where( $direction ) {

	global $wpdb;

	// Start Date meta
	$meta_key = '_ccm_event_start_date';
	$meta_value = get_post_meta( get_the_ID(), '_ccm_event_start_date', true );

	// Direction
	if ( 'previous' == $direction ) {
		$op = '<';
	} else {
		$op = '>';
	}

	// SQL WHERE
	// Note that Start Date is not a unique value, so in that case sorting by ID is also done.
	// Otherwise events with same date would get skipped over. More details: http://bit.ly/15pUv2j
	$where = $wpdb->prepare(
		"WHERE
			pm.meta_key = %s
			AND (
				(
					pm.meta_value = %s
					AND p.ID $op %d
				)
				OR pm.meta_value $op %s
			)
			AND p.post_type = %s
			AND p.post_status = 'publish'
		",
		$meta_key,
		$meta_value,
		get_the_ID(),
		$meta_value,
		get_post_type()
	);

	return $where;

}

function ctc_previous_event_where( $where ) {

	return ctc_previous_next_event_where( 'previous' );

}


function ctc_next_event_where( $where ) {

	return ctc_previous_next_event_where( 'next' );

}

/**
 * SQL ORDER BY for Prev/Next Event
 */

function ctc_previous_event_sort( $sort ) {

	return "ORDER BY pm.meta_value DESC, p.ID DESC LIMIT 1";

}


function ctc_next_event_sort( $sort ) {

	return "ORDER BY pm.meta_value ASC, p.ID ASC LIMIT 1";

}
