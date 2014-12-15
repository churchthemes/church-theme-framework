<?php
/**
 * <head> Functions
 *
 * Functions that add to <head>. Also see frontend-css.php for adding styles.
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

/*******************************************
 * HEAD
 *******************************************/

/**
 * Title tag theme support backwards compatibility
 *
 * Themes should use add_theme_support( 'title-tag' ) to add the <title> tag as of WordPRess 4.1.
 *
 * This provides backward-compatibility by adding the title tag to <head> in older versions.
 *
 * @since 1.4
 */
function ctfw_title_tag_compat() {

	// Manually insert <title> tag if WordPress doesn't support title-tag
	// But, only if the theme is trying to add title-tag support
	if ( ! function_exists( '_wp_render_title_tag' ) && current_theme_supports( 'title-tag' ) ) {
		echo "<title>" . wp_title( '', false, 'right' ) . "</title>\n"; // ctfw_head_title() below modifies this
	}

}

add_action( 'wp_head', 'ctfw_title_tag_compat', 1 );

/**
 * Filter <title> tag to be friendly
 *
 * An SEO plugin can be used to fine-tune the <title> for various areas of the site.
 *
 * @since 0.9
 * @param string $title Page title determined by WordPress core
 * @param string $sep Optional, default is '&raquo;'. How to separate the various items within the page title.
 * @param string $seplocation Optional. Direction to display title, 'right'.
 * @return string Formatted title
 */
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

add_filter( 'wp_title', 'ctfw_head_title', 10, 3 );
