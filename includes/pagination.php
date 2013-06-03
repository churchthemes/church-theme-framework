<?php

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;
 
/**
 * Get/Set $paged
 * 
 * For use in templates that can be used as static front page.
 * get_query_var( 'paged' ) returns nothing on front page, but get_query_var( 'page' ) does.
 * This returns and sets globally $paged so that the query and pagination work.
 */
 
function ctfw_page_num() {

	global $paged;

	// Use paged if given; otherwise page; otherwise 1
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

	return apply_filters( 'ctfw_page_num', $paged );
	
}
