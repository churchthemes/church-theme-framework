<?php
/**
 * Template Tags
 *
 * These output common elements for different post types.
 */

/**
 * X Days Ago or Date
 *
 * This takes a timestamp and shows "X days ago"
 * If it is older than a certain number of days, the full date it shown
 */

function ctc_date_ago( $timestamp, $max_days = false ) {

	// the current time
	$current_time = current_time( 'timestamp' );
	
	// at what time in past would $max_days be exceeded?
	$max_seconds = $max_days * 86400; // days multiplied by number of seconds in a day
	$cutoff_time = $current_time - $max_seconds; // now minus X days ago
	
	// if cutoff time is newer than X days ago, show human time difference (X days/hours/minutes/seconds ago)
	if ( $timestamp > $cutoff_time ) {
		$time_diff = human_time_diff( $timestamp, $current_time ); // http://codex.wordpress.org/Function_Reference/human_time_diff
		/* translators: helps form 'X days ago' with 'X days' being localized by core WordPress translation */
		$date = sprintf( __( '%s ago' , 'church-theme' ), $time_diff ); // localized text for "X days ago" where "X days" is from WP core - to change days, must set core translation
	}
	
	// timestamp is older than X days ago, show full date format
	else {
		$date_format = get_option( 'date_format' ); // this is from WordPress general settings
		$date = date_i18n( $date_format, $timestamp ); // translated date
	}	

	// return formatted date
	return apply_filters( 'ctc_date_ago', $date, $timestamp, $max_days );

}
