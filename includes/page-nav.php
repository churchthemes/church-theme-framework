<?php
/**
 * Page Navigation Functions
 *
 * These functions relate to navigating between pages: page numbering, pagination, prev/next, etc.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/***********************************************
 * PAGE NUMBERS
 ***********************************************/

/**
 * Get/set $paged
 *
 * For use in templates that can be used as static front page.
 * get_query_var( 'paged' ) returns nothing on front page, but get_query_var( 'page' ) does.
 * This returns and sets globally $paged so that the query and pagination work.
 *
 * @since 0.9
 * @global int $paged
 * @return int Current page number
 */
function ctfw_page_num() {

	global $paged;

	// Use paged if given; otherwise page; otherwise 1
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

	return apply_filters( 'ctfw_page_num', $paged );

}

/***********************************************
 * PREV/NEXT MANUAL ORDER
 ***********************************************/

/**
 * These can be used to sort prev/next for people and locations in manual order (instead of publish date).
 */

/**
 * SQL WHERE for manual order of previous or next post
 *
 * @since 0.9.1
 * @global object $wpdb
 * @param string $direction 'previous' or 'next'
 * @return string SQL WHERE clause
 */
function ctfw_previous_next_post_where( $direction ) {

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

/**
 * SQL WHERE for manual order of previous post
 *
 * @since 0.9.1
 * @param string $where Current WHERE clause
 * @return string Custom WHERE clause
 */
function ctfw_previous_post_where( $where ) {
	return ctfw_previous_next_post_where( 'previous' );
}

/**
 * SQL WHERE for manual order of for next post
 *
 * @since 0.9.1
 * @param string $where Current WHERE clause
 * @return string Custom WHERE clause
 */
function ctfw_next_post_where( $where ) {
	return ctfw_previous_next_post_where( 'next' );
}

/**
 * SQL ORDER BY for manual order of previous post
 *
 * @since 0.9.1
 * @param string $sort Current ORDER BY clause
 * @return string Custom ORDER BY clause
 */
function ctfw_previous_post_sort( $sort ) {
	return "ORDER BY p.menu_order ASC LIMIT 1";
}

/**
 * SQL ORDER BY for manual order of next post
 *
 * @since 0.9.1
 * @param string $sort Current ORDER BY clause
 * @return string Custom ORDER BY clause
 */
function ctfw_next_post_sort( $sort ) {
	return "ORDER BY p.menu_order DESC LIMIT 1";
}