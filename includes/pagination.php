<?php
/**
 * Pagination Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.0
 */

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
