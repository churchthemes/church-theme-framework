<?php
/**
 * Deprecated Functions
 *
 * Deprecated functions will go here; avoid breakage and trigger _deprecated_function().
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

/**
 * Possible candidate may be ctfw_theme_url() which could end up in WordPress core as
 * something like theme_url() at some point: http://core.trac.wordpress.org/ticket/18302
 */

/**
 * Filter <title> tag to be friendly
 *
 * An SEO plugin can be used to fine-tune the <title> for various areas of the site.
 *
 * Deprecation: add_theme_support( 'title-tag' ) in WordPress 4.1 handles this nicely.
 *
 * @since 0.9
 * @deprecated 1.7.3
 * @param string $title Page title determined by WordPress core
 * @param string $sep Optional, default is '&raquo;'. How to separate the various items within the page title.
 * @param string $seplocation Optional. Direction to display title, 'right'.
 * @return string Formatted title
 */
function ctfw_head_title( $title, $sep, $seplocation ) {

	_deprecated_function( __FUNCTION__, '1.7.3', "add_theme_support( 'title-tag' )" );

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
			/* translators: separator for <title> content */
			$new_title .= _x( ' - ', 'head title', 'church-theme-framework' ) . $after;
		}

	}

	return $new_title;

}

add_filter( 'wp_title', 'ctfw_head_title', 10, 3 );

/**
 * @since 0.9
 * @deprecated 1.3
 */
function ctfw_edd_license_check_deactivation() {

	_deprecated_function( __FUNCTION__, '1.3', 'ctfw_edd_license_sync()' );

	ctfw_edd_license_sync();

}

/**
 * @since 0.9
 * @deprecated 1.3
 */
function ctfw_edd_license_auto_check_deactivation() {

	_deprecated_function( __FUNCTION__, '1.3', 'ctfw_edd_license_auto_sync()' );

	ctfw_edd_license_sync();

}
