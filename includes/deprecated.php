<?php
/**
 * Deprecated Functions
 *
 * Deprecated functions are moved here; avoid breakage and trigger _deprecated_function().
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

	$new_title = $title;

	if ( current_theme_supports( 'ctfw-auto-title' ) ) {

		_deprecated_function( __FUNCTION__, '1.7.3', "add_theme_support( 'title-tag' )" );

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
 * Remove custom background from admin menu
 *
 * Note: This only has effect on WordPRess 4.0 and earlier because 4.1 removed background image screen
 *
 * Use add_theme_support( 'ctfw-force-customizer-background' ) to force users to edit
 * the custom background via the Customizer.
 *
 * @since 0.9
 */
function ctfw_admin_remove_menu_pages() {

	global $menu;

	// If theme supports this
	if ( current_theme_supports( 'ctfw-force-customizer-background' ) ) {

		_deprecated_function( __FUNCTION__, '1.7.3' );

		if ( version_compare( get_bloginfo( 'version' ), '4.1', '<' ) ) {

			// Remove background link
			// Encourage access by Theme Customizer since it has Fullscreen and Preset enhancements
			remove_submenu_page( 'themes.php', 'custom-background' );

		}

	}

}

add_action( 'admin_menu', 'ctfw_admin_remove_menu_pages', 11 ); // after add theme support for background

/**
 * Redirect custom background to Customizer
 *
 * Note: This only has effect on WordPRess 4.0 and earlier because 4.1 removed background image screen
 *
 * Use add_theme_support( 'ctfw-force-customizer-background' ) to force users to edit
 * the custom background via the Customizer when trying to use background image screen.
 *
 * @since 0.9
 */
function ctfw_admin_redirect_background() {

	// If theme supports this
	if ( current_theme_supports( 'ctfw-force-customizer-background' ) ) {

		_deprecated_function( __FUNCTION__, '1.7.3' );

		// We're on custom background page
		if ( 'themes.php?page=custom-background' == basename( $_SERVER['REQUEST_URI'] ) ) {

			// Redirect to Theme Customizer
			wp_redirect( admin_url( 'customize.php' ) );
			exit;

		}

	}

}

add_action( 'admin_init', 'ctfw_admin_redirect_background' );

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
