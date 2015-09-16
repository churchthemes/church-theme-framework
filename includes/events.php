<?php
/**
 * Event Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2015, churchthemes.com
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
	$args['category'] = ! empty( $args['category'] ) ? $args['category'] : 'all';
	$args['limit'] = isset( $args['limit'] ) ? absint( $args['limit'] ) : -1; // default no limit

	// Upcoming or past
	$meta_type = 'DATETIME'; // 0000-00-00 00:00:00

	// Upcoming events
	$compare = '>=';  // all events with start OR end date today or later
	$meta_key = '_ctc_event_start_date_start_time'; // order by this; want earliest starting date/time first
	$order = 'ASC'; // sort from soonest to latest

	// Past events
	if ( 'past' == $args['timeframe'] ) {
		$compare = '<'; // all events with start AND end date BEFORE today
		$meta_key = '_ctc_event_end_date_start_time'; // order by this; want finish date first (not end time because may be empty)
		$order = 'DESC'; // sort from most recently past to oldest
	}

	// Backwards compatibility
	// Church Theme Content added rigid time fields in version 1.2
	// Continue ordering by old field for old versions of plugin
	if ( defined( 'CTC_VERSION' ) && version_compare( CTC_VERSION, '1.2', '<' ) ) { // CTC plugin is active and old

		// Upcoming or past
		$meta_type = 'DATE'; // 0000-00-00

		// Upcoming events
		$meta_key = '_ctc_event_start_date'; // order by this; want earliest starting date/time first

		// Past events
		if ( 'past' == $args['timeframe'] ) {
			$meta_key = '_ctc_event_end_date'; // order by this; want finish date first (not end time because may be empty)
		}

	}

	// Arguments
	$query_args = array(
		'post_type'			=> 'ctc_event',
		'numberposts'		=> $args['limit'],
		'meta_query' 		=> array(
			array(
				'key'			=> '_ctc_event_end_date', // the latest date that the event goes to (could be start date)
				'value' 		=> date_i18n( 'Y-m-d' ), // today's date, localized
				'compare' 		=> $compare,
				'type' 			=> 'DATE'
			),
		),
		'meta_key' 			=> $meta_key,
		'meta_type' 		=> $meta_type,
		'orderby'			=> 'meta_value',
		'order'				=> $order,
		'suppress_filters'	=> false // keep WPML from showing posts from all languages: http://bit.ly/I1JIlV + http://bit.ly/1f9GZ7D
	);

	// Filter by category
	if ( 'all' != $args['category'] ) {

		$category_term = get_term( $args['category'], 'ctc_event_category' );

		if ( $category_term ) {
			$query_args['ctc_event_category'] = $category_term->slug;
		}

	}

	// Filter get post arguments
	$query_args = apply_filters( 'ctfw_get_events_query_args', $query_args );

	// Get events
	$posts = get_posts( $query_args );

	// Return filtered
	return apply_filters( 'ctfw_get_events', $posts, $args );

}

/**
 * Get event data
 *
 * @since 0.9
 * @param array|int $args post_id or array of arguments; If no post ID, current post used
 * @return array Event data
 */
function ctfw_event_data( $args = array() ) {

	// Post ID given instead of args array?
	if ( is_numeric( $args ) ) {
		$args = array(
			'post_id' => $args,
		);
	}

	// Default arguments
	$args = wp_parse_args( $args, array(
		'post_id'				=> null, // use current
		/* translators: time range (%1$s) and description (%2$s) for an event */
		'time_and_desc_format'	=> __( '%1$s <span>(%2$s)</span>', 'church-theme-framework' ),
	) );

	// Extract arguments to variables
	extract( $args );

	// Get meta values
	$meta = ctfw_get_meta_data( array(
		'start_date',
		'end_date',
		'time', // Time Description
		'start_time',
		'end_time',
		'hide_time_range',
		'recurrence',
		'recurrence_end_date',
		'recurrence_weekly_interval',	// Custom Recurring Events add-on
		'recurrence_monthly_interval',	// Custom Recurring Events add-on
		'recurrence_monthly_type',		// Custom Recurring Events add-on
		'recurrence_monthly_week',		// Custom Recurring Events add-on
		'venue',
		'address',
		'show_directions_link',
		'map_lat',
		'map_lng',
		'map_type',
		'map_zoom'
	), $post_id );

	// Empty Custom Recurring Events add-on values if plugin not active
	// This keeps theme from displaying recurrence data that may be stored but is not effective
	if ( ! defined( 'CTC_CRE_VERSION' ) ) {
		$meta['recurrence_weekly_interval'] = 1;
		$meta['recurrence_monthly_interval'] = 1;
		$meta['recurrence_monthly_type'] = 'day';
		$meta['recurrence_monthly_week'] = '';
	}

	// Timestamps
	$start_date_timestamp = strtotime( $meta['start_date'] );
	$end_date_timestamp = strtotime( $meta['end_date'] );

	// Add friendly date
	$date_format = get_option( 'date_format' );
	if ( $meta['end_date'] != $meta['start_date'] ) { // date range

		// Date formats
		// Make compact range of "June 1 - 5, 2015 if using "F j, Y" format (month and year removed from start date as not to be redundant)
		// If year is same but month different, becomes "June 30 - July 1, 2015"
		$start_date_format = $date_format;
		$end_date_format = $date_format;
		if ( 'F j, Y' == $date_format && date_i18n( 'Y', $start_date_timestamp ) == date_i18n( 'Y', $end_date_timestamp ) ) { // Year on both dates must be same

			// Remove year from start date
			$start_date_format = 'F j';

			// Months and year is same
			// Remove month from end date
			if ( date_i18n( 'F', $start_date_timestamp ) == date_i18n( 'F', $end_date_timestamp ) ) {
				$end_date_format = 'j, Y';
			}

		}

		// Format dates
		$start_date_formatted = date_i18n( $start_date_format, $start_date_timestamp );
		$end_date_formatted = date_i18n( $end_date_format, $end_date_timestamp );

		// Build range
		/* translators: date range */
		$meta['date'] = sprintf(
			_x( '%1$s &ndash; %2$s', 'dates', 'church-theme-framework' ),
			$start_date_formatted,
			$end_date_formatted
		);

	} else { // start date only
		$meta['date'] = date_i18n( $date_format, $start_date_timestamp );
	}

	// Format Start and End Time
	$time_format = get_option( 'time_format' );
	$meta['start_time_formatted'] = $meta['start_time'] ? date_i18n( $time_format, strtotime( $meta['start_time'] ) ) : '';
	$meta['end_time_formatted'] = $meta['end_time'] ? date_i18n( $time_format, strtotime( $meta['end_time'] ) ) : '';

	// Time Range
	// Show Start/End Time range (or only Start Time)
	$meta['time_range'] = '';
	if ( $meta['start_time_formatted'] ) {

		// Start Time Only
		$meta['time_range'] = $meta['start_time_formatted'];

		// Start and End Time (Range)
		if ( $meta['end_time_formatted'] ) {

			// Time Range
			/* translators: time range */
			$meta['time_range'] = sprintf(
				_x( '%1$s &ndash; %2$s', 'times', 'church-theme-framework' ),
				$meta['start_time_formatted'],
				$meta['end_time_formatted']
			);

		}

	}

	// Time and/or Description
	// Show Start/End Time (if given) and maybe Time Description (if given) in parenthesis
	// If no Start/End Time (or it is set to hide), show Time Description by itself
	// This is useful for event post header
	$meta['time_range_and_description'] = '';
	$meta['time_range_or_description'] = '';
	if ( $meta['time_range'] && ! $meta['hide_time_range'] ) { // Show Time Range and maybe Description after it

		// Definitely show time range
		$meta['time_range_and_description'] = $meta['time_range'];
		$meta['time_range_or_description'] = $meta['time_range'];

		// Maybe show description after time range
		if ( $meta['time'] ) {

			// Time and Description
			$meta['time_range_and_description'] = sprintf(
				$time_and_desc_format,
				$meta['time_range'],
				$meta['time']
			);

		}

	} else { // Show description only
		$meta['time_range_and_description'] = $meta['time'];
		$meta['time_range_or_description'] = $meta['time'];
	}

	// Add directions URL (empty if show_directions_link not set)
	$meta['directions_url'] = $meta['show_directions_link'] ? ctfw_directions_url( $meta['address'] ) : '';

	// Recurrence note
	$recurrence_note = ctfw_event_recurrence_note( false, $meta );
	$meta['recurrence_note'] = isset( $recurrence_note['full'] ) ? $recurrence_note['full'] : ''; // sentence such as "Every 3 months on the second Wednesday until January 24, 2018"
	$meta['recurrence_note_short'] = isset( $recurrence_note['short'] ) ? $recurrence_note['short'] : ''; // short version such as "Every 3 Months" (can show this with full on tooltip)

	// Map has coordinates?
	$meta['map_has_coordinates'] = ( $meta['map_lat'] && $meta['map_lng'] ) ? true : false;

	// Return filtered
	return apply_filters( 'ctfw_event_data', $meta, $post_id );

}

/**********************************
 * EVENT CATEGORIES
 **********************************/

/**
 * Modify category archive query
 *
 * Order by event date, not publish date
 *
 * Use add_theme_support( 'ctfw-event-category-query' ) to enable.
 * Must have support for Church Theme Content event category taxonomy
 *
 * @since 1.5
 * @param object $query WP_Query
 */
function ctfw_event_category_query( $query ) {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-event-category-query' ) ) {
		return;
	}

	// Don't manipulate feed
	if ( $query->is_feed ) {
		return;
	}

	// Only manipulate event category taxonomy archive query
	if ( ! $query->is_archive || ! $query->is_tax || empty( $query->query_vars['ctc_event_category'] ) ) {
		return;
	}

	// Modify query to show upcoming events soonest to latest
	$query->query_vars['meta_query'] 	= array(
			array( // only get upcoming events (ending today or in future)
				'key'		=> '_ctc_event_end_date', // the latest date that the event goes to (could be same as start date)
				'value' 	=> date_i18n( 'Y-m-d' ), // today's date, localized
				'compare' 	=> '>=', // all events with start OR end date today or later
				'type' 		=> 'DATE'
			),
		);
	$query->query_vars['meta_key'] 		= '_ctc_event_start_date_start_time'; // want earliest start date/time first
	$query->query_vars['meta_type'] 	= 'DATETIME'; // 0000-00-00 00:00:00
	$query->query_vars['orderby']		= 'meta_value';
	$query->query_vars['order']			= 'ASC'; // sort from soonest to latest

	// Backwards compatibility not needed for time fields because category taxonomy introduced after new time fields

}

add_action( 'pre_get_posts', 'ctfw_event_category_query' );

/**********************************
 * EVENT CALENDAR
 **********************************/

/**
 * Event calendar month data
 *
 * Take in YYYY-MM and convert it to a valid year, month and month timestamp.
 * If missing or invalid, use current year/month.
 *
 * @param string $year_month YYYY-MM such as 2015-01 for January, 2015
 * @return array Year, month (no leading 0) and month timestamp
 */
function ctfw_event_calendar_month_data( $year_month ) {

	$data = array();

	// Year/month given and valid
	if ( ! empty( $year_month ) && preg_match( '/^[0-9]{4}-[0-9]{2}$/', $year_month ) ) {

		// Get year and month
		list( $year, $month ) = explode( '-', $year_month );

		// Remove preceding 0 from month
		$month = ltrim( $month, '0' );

		// Invalid month and year?
		// Unset to use default
		if ( ! checkdate( $month, 1, $year ) ) {
			unset( $year );
			unset( $month );
		}

	}

	// Set defaults
	if ( ! isset( $year ) || ! isset( $month ) ) {
		$year = date_i18n( 'Y' );
		$month = date_i18n( 'n' );
	}

	// Make timestamp for year/month
	$month_ts = mktime( 0, 0, 0, $month, 1, $year );

	// Set $year_month in case was empty or invalid
	$year_month = date_i18n( 'Y-m', $month_ts );

	// First day of month
	$first_of_month = date_i18n( 'Y-m-d', $month_ts );

	// Prev and next months
	$prev_month = date_i18n( 'Y-m', ( $month_ts - 1 ) );
	$prev_month_ts = strtotime( $prev_month );
	$next_month = date_i18n( 'Y-m', ( $month_ts + ( DAY_IN_SECONDS * 32 ) ) );
	$next_month_ts = strtotime( $next_month );

	// Data in array
	$data['year_month'] = $year_month;
	$data['year'] = $year;
	$data['month'] = $month;
	$data['month_ts'] = $month_ts;
	$data['first_of_month'] = $first_of_month;
	$data['prev_month'] = $prev_month;
	$data['prev_month_ts'] = $prev_month_ts;
	$data['next_month'] = $next_month;
	$data['next_month_ts'] = $next_month_ts;

	// Filter the data
	$data = apply_filters( 'ctfw_event_calendar_month_data', $data, $year_month );

	return $data;

}

/**
 * Event calendar data
 *
 * Returns a month's headings, weeks, days and events for use in rendering an HTML calendar.
 * Considers start day of week in Settings > General and localization.
 *
 * @param array $args Arguments for year, month, etc.
 * @return array Array with days of weeks, weeks with days and days with events
 */
function ctfw_event_calendar_data( $args ) {

	// Arguments
	$args = wp_parse_args( $args, array(
		'year_month' 		=> '', // YYYY-MM (e.g. 2015-01 for January, 2015)
		'get_events'		=> true, // get events for each day in array
		'category'			=> '', // category term slug or empty for all
		'recurrence_limit'	=> 160, // max number of future event occurences to calculate (160 will cover 3 years of weekly recurrence)
	) );

	// Extract arguments for easy use
	extract( $args );

	// Date format
	$date_format = get_option( 'date_format' );

	// Start calendar data array
	$calendar = array();

	// Get $year, $month and $month_ts, validated
	// If invalid date passed, current month/year used
	// This also removed preceding 0 from month
	$calendar['month_data'] = ctfw_event_calendar_month_data( $year_month );
	extract( $calendar['month_data'] );

	// Get today
	$today = date_i18n( 'Y-m-d' );
	$today_ts = strtotime( $today );

	// Days in the month
	$days_in_month = date_i18n( 't', $month_ts );

	// Get day of week for first day of month (0 - 6 representing Sunday - Saturday)
	// This is useful for determining where to start the calendar
	$first_day_in_month_ts = mktime( 0, 0, 0, $month, 1, $year );
	$first_day_in_month_info = getdate( $first_day_in_month_ts );
	$first_day_in_month_day_of_week = $first_day_in_month_info['wday'];

	// Previous month
	$previous_month_days = date_i18n( 't', ( $first_day_in_month_ts - DAY_IN_SECONDS ) );

	// Build days of week array
	// Make start of week first in array
	$days_of_week = array();

		// Place days of week in array
		// Using first week of month specifically so can determine localized day of week names
		for ( $day_in_month = 1; $day_in_month <= 7; $day_in_month++ ) {

			// This day's info
			$day_in_month_ts = mktime( 0, 0, 0, $month, $day_in_month, $year );
			$day_in_month_info = getdate( $day_in_month_ts );
			$day_in_month_day_of_week = $day_in_month_info['wday'];

			// Numeric day of week
			$days_of_week[$day_in_month_day_of_week]['numeric'] = $day_in_month_day_of_week; // on 0 - 6 scake
			$days_of_week[$day_in_month_day_of_week]['numeric_friendly'] = $day_in_month_day_of_week + 1; // on 1 - 7 scale

			// Localized names
			$days_of_week[$day_in_month_day_of_week]['name'] = date_i18n( 'l', $day_in_month_ts );
			$days_of_week[$day_in_month_day_of_week]['name_short'] = date_i18n( 'D', $day_in_month_ts );

		}

		// Sort by day of week 0 - 6
		ksort( $days_of_week );

		// Change start of week (e.g. Monday instead of Sunday)
		// Settings > General controls this
		$start_of_week = get_option( 'start_of_week' ); // Day week starts on; numeric (0 - 6 representing Sunday - Saturday)
		$removed_days = array_splice( $days_of_week, $start_of_week ); // remove days before new first day from front
		$days_of_week = array_merge( $removed_days, $days_of_week ); // move them to end to effect new first day of week

		// Add to calendar array
		$calendar['days_of_week'] = $days_of_week;

	// Loop days of month to build rows
	$day = 1;
	$week = 0;
	$day_of_week = $first_day_in_month_day_of_week;
	$day_of_week = $day_of_week - $start_of_week;
	if ( $day_of_week < 0 ) {
		$day_of_week = 7 + $day_of_week;
	}
	while ( $day <= $days_in_month ) {

		// Add day to array
		$calendar['weeks'][$week]['days'][$day_of_week] = array(
			'day'			=> $day,
			'month'			=> $month,
			'year'			=> $year,
			'other_month'	=> false,
		);

		// Increment day, day of week and week
		$day++;
		if ( $day_of_week == 6 ) {
			$week++; // next week/row
			$day_of_week = 0; // start week over on first day
		} else {
			$day_of_week++; // increment day

		}

	}

	// Fill in days from last month for first row
	$last_month_ts = $month_ts - DAY_IN_SECONDS; // timestamp is first of month so subtract one day
	$last_month_ts = strtotime( date_i18n( 'Y-m-d', $last_month_ts ) ); // make it first second of first day of month for consistency
	$last_month = date_i18n( 'n', $last_month_ts );
	$last_month_year = date_i18n( 'Y', $last_month_ts );
	$first_row_missing_days = 7 - count( $calendar['weeks'][0]['days'] );
	$day_of_week = 0;
	if ( $first_row_missing_days ) {

		// Days in last month
		$days_in_last_month = date_i18n( 't', $last_month_ts );

		// Add last days of last month to first row (week) in calendar
		$last_month_start_day = $days_in_last_month - $first_row_missing_days + 1;
		for ( $day = $last_month_start_day; $day <= $days_in_last_month; $day++ ) {

			// Add day to array
			$calendar['weeks'][0]['days'][$day_of_week] = array(
				'day'			=> $day,
				'month'			=> $last_month,
				'year'			=> $last_month_year,
				'other_month'	=> true,
			);

			$day_of_week++;

		}

		// Sort by day of week 0 - 6
		ksort( $calendar['weeks'][0]['days'] );

	}

	// Fill in days from next month for last row
	$next_month_ts = $month_ts + ( DAY_IN_SECONDS * 32 ); // this will always push into the next month
	$next_month_ts = strtotime( date_i18n( 'Y-m-d', $next_month_ts ) ); // make it first second of first day of month for consistency
	$next_month = date_i18n( 'n', $next_month_ts );
	$next_month_year = date_i18n( 'Y', $next_month_ts );
	$last_row = count( $calendar['weeks'] ) - 1;
	$next_month_last_day_of_week = count( $calendar['weeks'][$last_row]['days'] ) - 1;
	$last_row_missing_days = 6 - $next_month_last_day_of_week;
	$day_of_week = $next_month_last_day_of_week; // start incrementing from last day's day of week
	if ( $last_row_missing_days ) {

		// Add first days of next month to last row (week) in calendar
		$next_month_stop_day = $last_row_missing_days;
		for ( $day = 1; $day <= $next_month_stop_day; $day++ ) {

			// Increment day of week (picks up off of last day of week)
			$day_of_week++;

			// Add day to array
			$calendar['weeks'][$last_row]['days'][$day_of_week] = array(
				'day'			=> $day,
				'month'			=> $next_month,
				'year'			=> $next_month_year,
				'other_month'	=> true,
			);

		}

	}

	// Add additional data to each day
	foreach ( $calendar['weeks'] as $week_key => $week ) {

		foreach ( $week['days'] as $day_key => $day ) {

			$date = date_i18n( 'Y-m-d', mktime( 0, 0, 0, $day['month'], $day['day'], $day['year'] ) );
			$date_ts = strtotime( $date );
			$date_formatted = date_i18n( $date_format, $date_ts );

			$calendar['weeks'][$week_key]['days'][$day_key]['date'] = $date;
			$calendar['weeks'][$week_key]['days'][$day_key]['date_ts'] = $date_ts;
			$calendar['weeks'][$week_key]['days'][$day_key]['date_formatted'] = $date_formatted;


			$last_of_previous_month = false;
			if ( $day['other_month'] && $previous_month_days == $day['day'] ) {
				$last_of_previous_month = true;
			}
			$calendar['weeks'][$week_key]['days'][$day_key]['last_of_previous_month'] = $last_of_previous_month;

			$first_of_next_month = false;
			if ( $day['other_month'] && 1 == $day['day'] ) {
				$first_of_next_month = true;
			}
			$calendar['weeks'][$week_key]['days'][$day_key]['first_of_next_month'] = $first_of_next_month;

			if ( $args['get_events'] ) {
				$calendar['weeks'][$week_key]['days'][$day_key]['event_ids'] = array();
			}

		}

	}

	// Get events for days in calendar array
	if ( $args['get_events'] ) {

		$calendar['events'] = array();

		// First date is today
		// Today is useful for months in future, because recurrence is caught up and can project into future
		// We also do not get events that are in past for current month
		$first_date_ts = $today_ts;
		$first_date = date_i18n( 'Y-m-d', $first_date_ts );

		// Last date is one week into next month
		// Some months will show the first days of the next month in calendar
		// We don't need events beyond that because nothing is calculated backwards

		// Backwards compatibility
		// Church Theme Content added rigid time fields in version 1.2
		// Continue ordering by old field for old versions of plugin
		$meta_type = 'DATETIME'; // 0000-00-00 00:00:00
		$meta_key = '_ctc_event_start_date_start_time'; // order by this
		if ( defined( 'CTC_VERSION' ) && version_compare( CTC_VERSION, '1.2', '<' ) ) { // CTC plugin is active and old
			$meta_type = 'DATE'; // 0000-00-00
			$meta_key = '_ctc_event_start_date'; // order by this; want earliest starting date/time first
		}

		// Arguments
		$query_args = array(
			'post_type'			=> 'ctc_event',
			'numberposts'		=> -1, // no limit
			'meta_query' 		=> array(
				array(
					'key'			=> '_ctc_event_end_date', // the latest date that the event goes to (could be start date)
					'value' 		=> $first_date,
					'compare' 		=> '>=', // all events with start OR end date later than last week of prior month
					'type' 			=> 'DATE'
				),
			),
			'meta_key' 			=> $meta_key,
			'meta_type' 		=> $meta_type,
			'orderby'			=> 'meta_value',
			'order'				=> 'ASC',
			'suppress_filters'	=> false // keep WPML from getting posts from all languages: http://bit.ly/I1JIlV + http://bit.ly/1f9GZ7D
		);

		// Filter by category if not all
		if ( ! empty( $args['category'] ) ) {
			$query_args['ctc_event_category'] = $args['category'];
		}

		// Get events
		$events = get_posts( $query_args );

		// Prepare for recurrence calculations
		$ctfw_recurrence = new CT_Recurrence();

		// Loop events
		foreach ( $events as $event ) {

			// Get meta data
			$event_data = ctfw_event_data( $event->ID ); // friendly data

			// Prepare to capture every day event occurs on
			$event_dates = array();

			// Add Start Date to array
			$event_dates[] = $event_data['start_date'];

			// Recurring event?
			if ( $event_data['recurrence'] && $event_data['recurrence'] != 'none' ) {

				// Recurrence interval
				$interval = 1;
				if ( 'weekly' == $event_data['recurrence'] ) {
					$interval = $event_data['recurrence_weekly_interval'];
				} elseif ( 'monthly' == $event_data['recurrence'] ) {
					$interval = $event_data['recurrence_monthly_interval'];
				}

				// Until date
				// This is either 38 days from first of month (+ 7 for first week of next, which may show)
				// Or, the recurrence end date if earlier
				$DateTime = new DateTime( $first_of_month );
				$DateTime->modify( '+38 days' );
				$until_date = $DateTime->format( 'Y-m-d' ); // PHP 5.2 cannot chain methods
				if ( ! empty( $event_data['recurrence_end_date'] ) && $event_data['recurrence_end_date'] < $until_date ) {
					$until_date = $event_data['recurrence_end_date'];
				}

				// Calculate future occurences of Start Date
				$recurrence_args = array(
					'start_date'	=> $event_data['start_date'],				// first day of event, YYYY-mm-dd (ie. 2015-07-20 for July 15, 2015)
					'until_date'	=> $until_date,						 		// date recurrence should not extend beyond (has no effect on calc_* functions)
					'frequency'		=> $event_data['recurrence'], 				// weekly, monthly, yearly
					'interval'		=> $interval, 								// every 1, 2 or 3, etc. weeks, months or years
					'monthly_type'	=> $event_data['recurrence_monthly_type'], 	// day (same day of month) or week (on a specific week); if recurrence is monthly (day is default)
					'monthly_week'	=> $event_data['recurrence_monthly_week'], 	// 1 - 4 or 'last'; if recurrence is monthly and monthly_type is 'week'
					'limit'			=> $recurrence_limit, 						// maximum dates to return (if no until_date, default is 100 to prevent infinite loop)
				);
				$calculated_dates = $ctfw_recurrence->get_dates( $recurrence_args );

				// Add calculated dates to array
				$event_dates = array_merge( $event_dates, $calculated_dates );

			}

			// Event is on multiple days
			if ( $event_data['start_date'] != $event_data['end_date'] ) {

				// Loop all occurences of Start Date
				foreach ( $event_dates as $date ) {

					// Add X days to Start Date to complete the range for all occurences
					$while_date = $event_data['start_date'];
					$WhileDateTime = new DateTime( $while_date );
					$DateTime = new DateTime( $date );
					while ( $while_date <= $event_data['end_date'] ) { // loop dates in original range

						// Add date to array if today or future
						// And is not beyond event's recur until date
						if ( strtotime( $date ) >= $today_ts && ( ! $event_data['recurrence_end_date'] || strtotime( $date ) <= strtotime( $event_data['recurrence_end_date'] ) ) ) {
							$event_dates[] = $date;
						}

						// Move to next day
						$WhileDateTime->modify( '+1 day' );
						$while_date = $WhileDateTime->format( 'Y-m-d' ); // PHP 5.2 cannot chain methods
						$DateTime->modify( '+1 day' );
						$date = $DateTime->format( 'Y-m-d' ); // PHP 5.2 cannot chain methods

					}

				}

			}

			// Remove duplicate dates
			$event_dates = array_unique( $event_dates );

			// Store event ID in days for which it occurs
			// This is so can reference event from other array
			foreach ( $calendar['weeks'] as $week_key => $week ) {

				// Loop days in week
				foreach ( $week['days'] as $day_key => $day ) {

					// Event occurs on this day
					if ( in_array( $day['date'], $event_dates ) ) {

						// Add event ID to day
						$calendar['weeks'][$week_key]['days'][$day_key]['event_ids'][] = $event->ID;

						// Add event to separate array, if doesn't already exist
						// This array can be referenced so multiple events not added per month, just IDs
						if ( ! isset( $calendar['events'][$event->ID] ) ) {
							$calendar['events'][$event->ID]['post'] = $event;
							$calendar['events'][$event->ID]['data'] = $event_data;
						}

					}

				}

			}

		}

		// Re-order events on each day earliest to latest and events with no time first (likely means all day / major event)
		// This considers that future occurences may have been injected so times are not in same order as original query
		foreach ( $calendar['weeks'] as $week_key => $week ) {

			// Loop days in week
			foreach ( $week['days'] as $day_key => $day ) {

				// Make new ID's array with keys to order by re-order events in
				$event_ids = array();
				foreach ( $day['event_ids'] as $event_id ) {

					// Get event
					$event = $calendar['events'][$event_id];

					// Make key to order by
					// In effect this sorts ascending primarily by start time then secondarily by ID (date of entry)
					$order_key = $event['data']['start_time'];
					if ( empty( $order_key ) ) {
						$order_key = '00:00'; // if has no time value; backwards compatibility (CTC < 1.2)
					}
					$order_key .= ' ' . str_pad( $event_id, 12, '0', STR_PAD_LEFT ); // for uniqueness and secondary ordering

					// Add to array to be re-ordered
					$event_ids[$order_key] = $event_id;

				}

				// Re-order events and remove ordering keys from array
				ksort( $event_ids );
				$event_ids = array_values( $event_ids );

				// Replace ID's array with array in new order
				$calendar['weeks'][$week_key]['days'][$day_key]['event_ids'] = $event_ids;

			}

		}

	}

	// DEBUG
	//ctfw_print_array( $calendar );

	// Filter
	$calendar = apply_filters( 'ctfw_event_calendar_data', $calendar, $args );

	return $calendar;

}

/**
 * Event calendar redirection
 *
 * Redirect's event calendar URLs in certain situations:
 *
 * - Month is invalid (must be YYYY-MM-DD)
 * - Month is in past
 * - Month is beyond next year
 * - Category slug is invalid
 *
 * This assumes a page template is showing calendar with URL having query ?month=YYYY-MM&category=slug
 *
 * Use add_theme_support( 'ctfw-event-calendar-redirection' );
 *
 * Arguments can be supplied. Defaults are as follows.
 *
 * add_theme_support( 'ctfw-event-calendar-redirection', array(
 *		'page_template'	=> 'events-calendar.php', // in CTFW_THEME_PAGE_TPL_DIR directory
 *  	'years_future'	=> 1,
 * ) );
 */
function ctfw_event_calendar_redirection() {

	// Theme supports this?
	$support = get_theme_support( 'ctfw-event-calendar-redirection' );
	if ( empty( $support ) ) {
		return;
	}

	// Arguments from theme support or default
	$args = isset( $support[0] ) ? $support[0] : array();
	$args = wp_parse_args( $args, array(
		'page_template'	=> 'events-calendar.php',
		'years_future'	=> 1,
	) );

	// Only on event calendar page template
	if ( ! is_page_template( CTFW_THEME_PAGE_TPL_DIR . '/' . $args['page_template'] ) ) {
		return;
	}

	// No redirect by default
	$redirect = false;

	// URL
	$url = home_url( $_SERVER['REQUEST_URI'] ); // current URL with query string
	$redirect_url = $url;

	// Month in query
	$year_month_query = isset( $_GET['month'] ) ? $_GET['month'] : ''; // default will be this month
	if ( $year_month_query ) {

		// Month data
		// $year_month (validated), $year, $month (without leading 0), $month_ts (first day of month), $prev_month, $next_month
		extract( ctfw_event_calendar_month_data( $year_month_query ) );

		// Today
		$today = date_i18n( 'Y-m-d' );
		$today_ts = strtotime( $today );

		// This month
		$this_month = date_i18n( 'Y-m', $today_ts );
		$this_month_ts = strtotime( $this_month );

		// This and next year
		$this_year = date_i18n( 'Y' );
		$next_year = $this_year + $args['years_future'];

		// If month in URL parameter has passed or is beyond next year...
		// Or, is invalid (doesn't match validated $year_month, which defaults to current month)
		// Then, redirect to the permalink to show current month
		if ( ! ( ( ( $year == $this_year && $month_ts >= $this_month_ts ) || $year == $next_year ) && $year_month_query == $year_month ) ) {

			// Do redirect
			$redirect = true;

			// Remove month from URL
			$redirect_url = remove_query_arg( 'month', $redirect_url );

		}

	}

	// Month in query
	$category_query = isset( $_GET['category'] ) ? $_GET['category'] : '';
	if ( $category_query ) {

		// Get category by slug
		$category = get_term_by( 'slug', $category_query, 'ctc_event_category' );

		// Redirect if doesn't exist
		if ( empty( $category ) ) {

			// Do redirect
			$redirect = true;

			// Remove month from URL
			$redirect_url = remove_query_arg( 'category', $redirect_url );

		}

	}

	// Redirect?
	// Avoid infinite redirect if new URL is same as current
	if ( $redirect && $redirect_url != $url ) {

		// Redirect to corrected URL
		wp_redirect( $redirect_url );

		// Stop execution
		exit;

	}

}

add_action( 'template_redirect', 'ctfw_event_calendar_redirection' );

/**********************************
 * EVENT RECURRENCE
 **********************************/

/**
 * Recurrence note
 *
 * This describes the recurrence pattern.
 * It considers the Custom Recurring Events add-on.
 *
 * Returns array with short and full keys.
 * short - "Every 3 Months"
 * full - "Every 3 months on second Tuesday until January 14, 2018"
 *
 * Tip: Show the short version with full in tooltip
 *
 * @since 1.5
 * @param object|int $post Post object or post ID for event
 * @return array Keys are full and short
 */
function ctfw_event_recurrence_note( $post_id = false, $data = false ) {

	$note = array();

	// Get event data if not provided
	if ( empty( $data ) ) {
		$data = ctfw_event_data( $post_id );
	}

	// Is this a recurring event?
	$recurrence = $data['recurrence'];
	if ( $recurrence && $recurrence != 'none' ) {

		$note['full'] = '';
		$note['short'] = '';

		// Get recurrence data
		$recurrence_end_date = $data['recurrence_end_date'];
		$weekly_interval = $data['recurrence_weekly_interval'];
		$monthly_interval = $data['recurrence_monthly_interval'];
		$monthly_type = $data['recurrence_monthly_type'];
		$monthly_week = $data['recurrence_monthly_week'];

		// Localized end date
		$recurrence_end_date_localized = '';
		if ( $recurrence_end_date ) {
			$date_format = get_option( 'date_format' );
			$end_date_ts = strtotime( $recurrence_end_date );
			$recurrence_end_date_localized = date_i18n( $date_format, $end_date_ts );
		}

		// Get day of week for start date
		$start_date = $data['start_date'];
		$start_day_of_week = ! empty( $start_date ) ? date_i18n( 'l', strtotime( $start_date ) ) : '';

		// Words for week of month
		$monthly_week_word = '';
		if ( $monthly_week ) {

			$monthly_week_words = array(
				'1'		=> _x( 'first', 'week of month', 'church-theme-framework' ),
				'2'		=> _x( 'second', 'week of month', 'church-theme-framework' ),
				'3'		=> _x( 'third', 'week of month', 'church-theme-framework' ),
				'4'		=> _x( 'fourth', 'week of month', 'church-theme-framework' ),
				'last'	=> _x( 'last', 'week of month', 'church-theme-framework' ),
			);

			$monthly_week_word = $monthly_week_words[$monthly_week];

		}

		// Frequency
		switch ( $recurrence ) {

			case 'weekly' :

				// Full
				if ( $recurrence_end_date ) {

					/* translators: %1$s is interval, %2$s is recurrence end date */
					$note['full'] = sprintf(
						_n(
							'Every week until %2$s',
							'Every %1$s weeks until %2$s',
							$weekly_interval,
							'church-theme-framework'
						),
						$weekly_interval,
						$recurrence_end_date_localized
					);

				} else {

					/* translators: %1$s is interval */
					$note['full'] = sprintf(
						_n(
							'Every week',
							'Every %1$s weeks',
							$weekly_interval,
							'church-theme-framework'
						),
						$weekly_interval
					);

				}

				// Short
				/* translators: %1$s is interval */
				$note['short'] = sprintf(
					_n(
						'Every Week',
						'Every %1$s Weeks',
						$weekly_interval,
						'church-theme-framework'
					),
					$weekly_interval
				);

				break;

			case 'monthly' :

				// On specific week
				if ( 'week' == $monthly_type && $start_day_of_week ) { // only if start date is present

					// Has recurrence end date
					if ( $recurrence_end_date ) {

						/* translators: %1$s is interval, %2$s is week of month, %3$s is day of week, %4$s is recurrence end date */
						$note['full'] = sprintf(
							_n(
								'Every month on the %2$s %3$s until %4$s',
								'Every %1$s months on the %2$s %3$s until %4$s',
								$monthly_interval,
								'church-theme-framework'
							),
							$monthly_interval,
							$monthly_week_word,
							$start_day_of_week,
							$recurrence_end_date_localized
						);

					}

					// No recurrence end date
					else {

						/* translators: %1$s is interval, %2$s is week of month, %3$s is day of week */
						$note['full'] = sprintf(
							_n(
								'Every month on the %2$s %3$s',
								'Every %1$s months on the %2$s %3$s',
								$monthly_interval,
								'church-theme-framework'
							),
							$monthly_interval,
							$monthly_week_word,
							$start_day_of_week
						);

					}

				// On same day of month
				} else {

					// Has recurrence end date
					if ( $recurrence_end_date ) {

						/* translators: %1$s is interval, %2$s is recurrence end date */
						$note['full'] = sprintf(
							_n(
								'Every month until %2$s',
								'Every %1$s months until %2$s',
								$monthly_interval,
								'church-theme-framework'
							),
							$monthly_interval,
							$recurrence_end_date_localized
						);

					}

					// No recurrence end date
					else {

						/* translators: %1$s is interval */
						$note['full'] = sprintf(
							_n(
								'Every month',
								'Every %1$s months',
								$monthly_interval,
								'church-theme-framework'
							),
							$monthly_interval
						);

					}

				}

				/* translators: %1$s is interval */
				$note['short'] = sprintf(
					_n(
						'Every Month',
						'Every %1$s Months',
						$monthly_interval,
						'church-theme-framework'
					),
					$monthly_interval
				);

				break;

			case 'yearly' :

				// Full
				if ( $recurrence_end_date ) {

					/* translators: %1$s is recurrence end date */
					$note['full'] = sprintf(
						__( 'Every year until %1$s', 'church-theme-framework' ),
						$recurrence_end_date_localized
					);

				} else {
					$note['full'] = __( 'Every year', 'church-theme-framework' );
				}

				// Short
				$note['short'] = __( 'Every Year', 'church-theme-framework' );

				break;

		}

	}

	// Filter
	$note = apply_filters( 'ctfw_event_recurrence_note', $note, $post_id, $data );

	return $note;

}

/**********************************
 * MONTH ARCHIVES
 **********************************/

/**
 * Month events count
 *
 * The number of event occurences in a calendar month.
 *
 * Note: Will not be accurate for months more than 3 years beyond today (see $recurrence_limit)
 *
 * @param string $year_month YYYY-MM such as 2015-01 for January, 2015
 * @return numeric Count of event occurences
 */
function ctfw_month_events_count( $year_month ) {

	// Get calendar data for each
	$calendar_data = ctfw_event_calendar_data( array(
		'year_month' 		=> $year_month, // YYYY-MM (e.g. 2015-01 for January, 2015)
		'get_events'		=> true, // get events for each day in array
		'category'			=> '', // category term slug or empty for all
		'recurrence_limit'	=> 160, // max number of future event occurences to calculate (160 will handle 3 years in future)
	) );

	// Count event occurences in the month
	$count = 0;
	foreach ( $calendar_data['weeks'] as $week ) {

		foreach ( $week['days'] as $day ) {

			if ( $day['month'] == $calendar_data['month_data']['month'] ) { // calendar shows prior month's last week and next month's first week
				$count += count( $day['event_ids'] );
			}

		}

	}

	return apply_filters( 'ctfw_month_events_count', $count, $year_month );

}

/**
 * Events month archive URL
 *
 * Theme can filter ctfw_content_types to specify event month archive URL format.
 * This makes replacements and returns working URL.
 *
 * For example, a theme may have a monthly calendar page template supporting months via query string.
 * This URL helps the framework know what URL to use, such as in ctfw_content_type_archives(), which
 * may be used to create archives nav, page template, widget, etc.
 *
 * @since 1.7.1
 * @param string $year_month Month as YYYY-MM (e.g. 2015-01 for January, 2015)
 * @return string URL to events month archive for the theme
 */
function ctfw_events_month_archive_url( $year_month ) {

	$url = '';

	// Get URL format filered in by theme via ctfw_content_types
	$url_format = ctfw_content_type_data( 'event', 'month_archive_url_format' );

	// Is URL provided and valid?
	// It may not be valid if, for example, page having a certain page template not yet setup
	if ( ctfw_is_url( $url_format ) ) { // valid URL (e.g. page for a page template exists)

		// Date
		$ts = strtotime( $year_month );
		$year = date_i18n( 'Y', $ts ); // e.g.  2015
		$month = date_i18n( 'n', $ts ); // e.g. 1
		$month_padded = date_i18n( 'm', $ts );// e.g. 01

		// Make replacements
		$url = $url_format;
		$url = str_replace( '{year}', $year, $url );
		$url = str_replace( '{month}', $month, $url );
		$url = str_replace( '{month_padded}', $month_padded, $url );

	}

	// Return filtered
	return apply_filters( 'ctfw_month_archive_url', $url, $year_month );

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
