<?php
/**
 * People Functions
 */

/**********************************
 * DATA
 **********************************/

/**
 * Get person meta data
 */

function ctc_person_data( $post_id = null ) {

	// Get meta values
	$meta = ctc_get_meta_data( array( // without _ccm_person_ prefix
		'position',
		'phone',
		'email',
		'urls',
	), $post_id );

	// Return filtered
	return apply_filters( 'ctc_person_data', $meta );

}

/**********************************
 * NAVIGATION
 **********************************/

/**
 * Prev/Next People Sorting
 * 
 * This makes get_previous_post() and get_next_post() sort by manual order instead of Publish Date
 */

add_action( 'wp', 'ctc_previous_next_person_sorting' ); // is_singular() not available until wp action (after posts_selection)

function ctc_previous_next_person_sorting() {

	// While on single person, if theme supports People from Church Content Manager
	// IMPORTANT: Without ! is_page(), is_singular() runs, somehow causing /page/#/ URL's on static front page to break
	if ( ! is_page() && is_singular( 'ccm_person' ) && current_theme_supports( 'ccm-people' ) ) {

		// SQL WHERE
		add_filter( 'get_previous_post_where', 'ctc_previous_person_where' );
		add_filter( 'get_next_post_where', 'ctc_next_person_where' );

		// SQL ORDER BY
		add_filter( 'get_previous_post_sort', 'ctc_previous_person_sort' );
		add_filter( 'get_next_post_sort', 'ctc_next_person_sort' );

	}

}

/**
 * SQL WHERE for Prev/Next Person
 */

function ctc_previous_next_person_where( $direction ) {

	global $wpdb, $post;

	// Direction
	if ( 'previous' == $direction ) {
		$op = '>';
	} else {
		$op = '<';
	}

	// SQL WHERE
	// Note that Order may not be a unique value, so in that case sorting by ID is also done.
	// Otherwise people with same Order would get skipped over. More details: http://bit.ly/15pUv2j
	$where = $wpdb->prepare(
		"WHERE
			(
				(
					p.menu_order = %s
					AND p.ID $op %d
				)
				OR p.menu_order $op %s
			)
			AND p.post_type = %s
			AND p.post_status = 'publish'
		",
		$post->menu_order,
		get_the_ID(),
		$post->menu_order,
		get_post_type()
	);

	return $where;

}

function ctc_previous_person_where( $where ) {

	return ctc_previous_next_person_where( 'previous' );

}


function ctc_next_person_where( $where ) {

	return ctc_previous_next_person_where( 'next' );

}

/**
 * SQL ORDER BY for Prev/Next person
 */

function ctc_previous_person_sort( $sort ) {

	return "ORDER BY p.menu_order ASC LIMIT 1";

}


function ctc_next_person_sort( $sort ) {

	return "ORDER BY p.menu_order DESC LIMIT 1";

}