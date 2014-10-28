<?php
/**
 * Event Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2014, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**********************************
 * EVENTS DATA
 **********************************/

/**
 * Get events based on arguments
 *
 * Arguments match those used by events widget
 * May also be used elsewhere (e.g. upcoming events in header)
 *
 * @since 0.9
 * @param array $args Arguments for getting events
 * @return array Event posts
 */
function ctfw_get_events( $args = array() ) {

	// Defaults
	$args['timeframe'] = ! empty( $args['timeframe'] ) ? $args['timeframe'] : 'upcoming';
	$args['limit'] = isset( $args['limit'] ) ? absint( $args['limit'] ) : -1; // default no limit

	// Show upcoming events
	$compare = '>=';  // all events with start OR end date today or later
	$meta_key = '_ctc_event_start_date_start_time'; // order by this; want earliest starting date/time first
	$order = 'ASC'; // sort from soonest to latest

	// Show past events
	if ( 'past' == $args['timeframe'] ) {
		$compare = '<'; // all events with start AND end date BEFORE today
		$meta_key = '_ctc_event_end_date_start_time'; // order by this; want finish date first (not end time because may be empty)
		$order = 'DESC'; // sort from most recently past to oldest
	}

	// Get events
	$posts = get_posts( array(
		'post_type'			=> 'ctc_event',
		'numberposts'		=> $args['limit'],
		'meta_query' => array(
			array(
				'key' => '_ctc_event_end_date', // the latest date that the event goes to (could be start date)
				'value' => date_i18n( 'Y-m-d' ), // today's date, localized
				'compare' => $compare,
				'type' => 'DATE'
			),
		),
		'meta_key' 			=> $meta_key,
		'meta_type' 		=> 'DATETIME', // 0000-00-00 00:00:00
		'orderby'			=> 'meta_value',
		'order'				=> $order,
		'suppress_filters'	=> false // keep WPML from showing posts from all languages: http://bit.ly/I1JIlV + http://bit.ly/1f9GZ7D
	) );

	// Return filtered
	return apply_filters( 'ctfw_get_events', $posts, $args );

}

/**
 * Get event data
 *
 * @since 0.9
 * @param int $post_id Post ID to get data for; null for current post
 * @return array Event data
 */
function ctfw_event_data( $post_id = null ) {

	// Get meta values
	$meta = ctfw_get_meta_data( array(
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

	// Timestamps
	$start_date_timestamp = strtotime( $meta['start_date'] );
	$end_date_timestamp = strtotime( $meta['end_date'] );

	// Add friendly date
	$date_format = get_option( 'date_format' );
	if ( $meta['end_date'] != $meta['start_date'] ) { // date range

		// Date formats
		// Make compact range of "June 1 - June 5, 2013 if using "F j, Y" format (year removed from start date as not to be redundant)
		if ( 'F j, Y' == $date_format && date_i18n( 'Y', $start_date_timestamp ) == date_i18n( 'Y', $end_date_timestamp ) ) { // Year on both dates must be same
			$start_date_format = 'F j'; // remove year
		} else {
			$start_date_format = $date_format;
		}
		$end_date_format = $date_format;

		// Format dates
		$start_date_formatted = date_i18n( $start_date_format, $start_date_timestamp );
		$end_date_formatted = date_i18n( $end_date_format, $end_date_timestamp );

		// Build range
		/* translators: date range */
		$meta['date'] = sprintf(
			__( '%s &ndash; %s', 'church-theme-framework' ),
			$start_date_formatted,
			$end_date_formatted
		);

	} else { // start date only
		$meta['date'] = date_i18n( $date_format, $start_date_timestamp );
	}

	// Add directions URL (empty if show_directions_link not set)
	$meta['directions_url'] = $meta['show_directions_link'] ? ctfw_directions_url( $meta['address'] ) : '';

	// Return filtered
	return apply_filters( 'ctfw_event_data', $meta, $post_id );

}

/**********************************
 * EVENT NAVIGATION
 **********************************/

/**
 * Prev/next event sorting
 *
 * This makes get_previous_post() and get_next_post() sort by event Start Date instead of Publish Date
 *
 * @since 0.9
 */
function ctfw_previous_next_event_sorting() {

	// Theme supports it?
	if ( ! current_theme_supports( 'ctfw-event-navigation' ) ) {
		return;
	}

	// While on single event, if theme supports Events from Church Theme Content
	// IMPORTANT: Without ! is_page(), is_singular() runs, somehow causing /page/#/ URL's on static front page to break
	if ( ! is_page() && is_singular( 'ctc_event' ) && current_theme_supports( 'ctc-events' ) ) {

		// SQL JOIN
		add_filter( 'get_previous_post_join', 'ctfw_previous_next_event_join' );
		add_filter( 'get_next_post_join', 'ctfw_previous_next_event_join' );

		// SQL WHERE
		add_filter( 'get_previous_post_where', 'ctfw_previous_event_where' );
		add_filter( 'get_next_post_where', 'ctfw_next_event_where' );

		// SQL ORDER BY
		add_filter( 'get_previous_post_sort', 'ctfw_previous_event_sort' );
		add_filter( 'get_next_post_sort', 'ctfw_next_event_sort' );

	}

}

add_action( 'wp', 'ctfw_previous_next_event_sorting' ); // is_singular() not available until wp action (after posts_selection)

/**
 * SQL JOIN for Prev/Next Event
 *
 * Get events meta for WHERE and ORDER BY to use.
 *
 * @since 0.9
 * @global object $wpdb
 * @param string $join Original JOIN SQL
 * @return string Modified JOIN SQL
 */
function ctfw_previous_next_event_join( $join ) {

	global $wpdb;

	$join = "INNER JOIN $wpdb->postmeta pm ON pm.post_id = p.ID";

	return $join;

}

/**
 * SQL WHERE for previous or next event
 *
 * @since 0.9
 * @global object $wpdb
 * @param string $direction 'previous' or 'next'
 * @return string SQL WHERE clause
 */
function ctfw_previous_next_event_where( $direction ) {

	global $wpdb;

	// Start Date meta
	$meta_key = '_ctc_event_start_date';
	$meta_value = get_post_meta( get_the_ID(), '_ctc_event_start_date', true );

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

/**
 * SQL WHERE for previous event
 *
 * @since 0.9
 * @param string $where Current WHERE clause
 * @return string Custom WHERE clause
 */
function ctfw_previous_event_where( $where ) {
	return ctfw_previous_next_event_where( 'previous' );
}

/**
 * SQL WHERE for next event
 *
 * @since 0.9
 * @param string $where Current WHERE clause
 * @return string Custom WHERE clause
 */
function ctfw_next_event_where( $where ) {
	return ctfw_previous_next_event_where( 'next' );
}

/**
 * SQL ORDER BY for previous event
 *
 * @since 0.9
 * @param string $sort Current ORDER BY clause
 * @return string Custom ORDER BY clause
 */
function ctfw_previous_event_sort( $sort ) {
	return "ORDER BY pm.meta_value DESC, p.ID DESC LIMIT 1";
}

/**
 * SQL ORDER BY for next event
 *
 * @since 0.9
 * @param string $sort Current ORDER BY clause
 * @return string Custom ORDER BY clause
 */
function ctfw_next_event_sort( $sort ) {
	return "ORDER BY pm.meta_value ASC, p.ID ASC LIMIT 1";
}
