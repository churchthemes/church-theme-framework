<?php
/**
 * Framework Admin Styles
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013 - 2018, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*******************************************
 * ENQUEUE STYLESHEETS
 *******************************************/

/**
 * Enqueue admin stylesheets
 *
 * Note: CT Meta Box and other framework components handle their own stylesheets.
 *
 * @since 0.9
 */
function ctfw_admin_enqueue_styles() {

	$screen = get_current_screen();

	// Post Add/Edit.
	if ( 'post' === $screen->base ) {

		// CSS for add/edit post screen.
		wp_enqueue_style( 'ctfw-post', get_theme_file_uri( CTFW_CSS_DIR . '/admin-post.css' ), false, CTFW_THEME_VERSION ); // bust cache on update.

	}

	// Admin Widgets.
	if ( 'widgets' === $screen->base ) {

		// For color widget field type.
		// Improvement to enqueue only when there is a widget with color field?
		wp_enqueue_style( 'wp-color-picker' );

		// CSS for admin widgets.
		// Framework also enqueues this for Customizer in framework/includes/customize.php.
		wp_enqueue_style( 'ctfw-widgets', get_theme_file_uri( CTFW_CSS_DIR . '/admin-widgets.css' ), false, CTFW_THEME_VERSION ); // bust cache on update.

	}

	// Theme License.
	if ( 'appearance_page_theme-license' == $screen->base ) {
		wp_enqueue_style( 'ctfw-license', get_theme_file_uri( CTFW_CSS_DIR . '/admin-license.css' ), false, CTFW_THEME_VERSION ); // bust cache on update.
	}

}

add_action( 'admin_enqueue_scripts', 'ctfw_admin_enqueue_styles' );
