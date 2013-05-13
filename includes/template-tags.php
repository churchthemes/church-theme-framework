<?php
/**
 * Template Tags
 *
 * These output common elements for different post types.
 */

/**
 * Post Date
 *
 * Output or return the localized, formatted post date, "Today" or "Yesterday", according to options.
 */

function ctc_post_date( $options = array() ) {

	$date_formatted = '';

	// Default options
	$defaults = apply_filters( 'ctc_post_date_default_options', array(
		'return'	=> false,
		'today'		=> true,	// show "Today" if post is from today
		'yesterday'	=> true,	// show "Yesterday" instead of yesterday's date
	) );
	$options = wp_parse_args( $options, $defaults );

	// Today and yesterday in local time
	$today_ymd = date_i18n( 'Y-m-d' );
	$yesterday_ymd = date_i18n( 'Y-m-d', strtotime( $today_ymd ) - DAY_IN_SECONDS );

	// Post date
	$date_timestamp = get_the_time( 'U' );
	$date_ymd = date_i18n( 'Y-m-d', $date_timestamp );

	// Show "Today"
	if ( $options['today'] && $today_ymd == $date_ymd ) {
		$date_formatted = __( 'Today', 'ct-framework' );
	}

	// Show "Yesterday"
	elseif ( $options['yesterday'] && $yesterday_ymd == $date_ymd ) {
		$date_formatted = __( 'Yesterday', 'ct-framework' );
	}

	// Show date
	else {
		$date_format = get_option( 'date_format' ); // this is from WordPress general settings
		$date_formatted = date_i18n( $date_format, $date_timestamp ); // translated date
	}

	// Date filtering
	$date_formatted = apply_filters( 'ctc_post_date', $date_formatted, $options );

	// Output or return
	if ( $options['return'] ) {
		return $date_formatted;
	} else {
		echo  $date_formatted;
	}

}
