<?php
/**
 * <head> Functions
 *
 * Functions that add to <head>. Also see frontend-css.php for adding styles.
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

/*******************************************
 * HEAD
 *******************************************/

/**
 * Add text to <title>
 *
 * An SEO plugin can be used to fine-tune the <title> for various areas of the site.
 */
 
add_filter( 'wp_title', 'ctfw_head_title', 10, 3 );

function ctfw_head_title( $title, $sep, $seplocation ) {

	$new_title = $title;

	if ( current_theme_supports( 'ctfw-auto-title' ) ) {

		// Feed
		if ( is_feed() ) {
			return $title;
		}

		// Page number
		$page_number = '';
		$page = ctfw_page_num();
		if ( $page > 1 ) {
			/* translators: page number in <title> */
			$page_number = sprintf( _x( ' (Page %d)', 'head title', 'church-theme-framework' ), $page );
		}

		// Homepage (site name - tagline )
		if ( is_front_page() ) {
			$before = get_bloginfo( 'name', 'display' );
			$after = $page <= 1 ? get_bloginfo( 'description', 'display' ) : ''; // show tagline if on first page (not showing page number)
		}

		// Subpage (page title - site name)
		else {
			$before = $title;
			$after = get_bloginfo( 'name' );
		}
		
		// Build title
		$before = trim( $before ) . $page_number;
		$after = trim( $after );
		$new_title = $before;
		if ( $after ) {
			/* translators: delimiter for <title> content */ 
			$new_title .= _x( ' - ', 'head title', 'church-theme-framework' ) . $after;
		}

	}
	
	return $new_title;
	
}

