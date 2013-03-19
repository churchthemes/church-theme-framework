<?php
/**
 * Template Tags
 *
 * These output common elements for different post types.
 */

/**
 *  X - X of X Posts
 */
 
function ctc_post_count_message( $query = false ) {

	global $wp_query;

	$message = '';
	
	// use standard query if no custom query given
	$query = empty( $query ) && isset( $wp_query ) ? $wp_query : $query;
	
	// have correct data?
	if ( isset( $query->found_posts ) && isset( $query->query_vars['paged'] ) && isset( $query->query_vars['posts_per_page'] ) && isset( $query->max_num_pages ) ) {

		// what page are we on?
		$page = ! empty( $query->query_vars['paged'] ) ? $query->query_vars['paged'] : 1; // 0 means page 1, given numbers are literal
		$post_max = $query->query_vars['posts_per_page'] * $page; // last post on page
		$post_min = $post_max - $query->query_vars['posts_per_page'] + 1; // first post on page
		$post_max = $post_max > $query->found_posts ? $query->found_posts : $post_max; // lastly, don't let actual max shown exceed total
		
		// If 1 item, show "1 Item"
		if ( 1 == $query->found_posts ) {
			$message = __( '<b>1</b> Item', 'church-theme' );
		}
		
		// If more than 1, but one page, show "X Items"
		else if ( 1 == $query->max_num_pages ) {
			$message = sprintf( __( 'Showing <b>%s</b>', 'church-theme' ), $query->found_posts );
		}
		
		// If more than 1 , but multiple pages, show "X - X of X items"
		else if ( $query->max_num_pages > 1 ) {
			/* translators: first item on page, last item on page, total items on all pages */
			$message = sprintf( __( 'Showing <b>%1$s</b> &ndash; <b>%2$s</b> of <b>%3$s</b>', 'church-theme' ), $post_min, $post_max, $query->found_posts );

		}
		
	}

	echo apply_filters( 'ctc_post_count_message', $message, $query );

}

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
