<?php
/**
 * Framework Admin JavaScript
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

/**
 * Enqueue Admin JavaScript
 *
 * Note: CT Meta Box and other framework components handle their own scripts.
 *
 * @since 0.9
 */
function ctfw_admin_enqueue_scripts() {

	$screen = get_current_screen();

	// Post Add/Edit.
	if ( 'post' === $screen->base ) { // don't enqueue unless needed

		wp_enqueue_script( 'ctfw-admin-post', get_theme_file_uri( CTFW_JS_DIR . '/admin-post.js' ), array( 'jquery' ), CTFW_THEME_VERSION ); // bust cache on update
		wp_localize_script( 'ctfw-admin-post', 'ctfw_post', array(
			'featured_image_note' => ctfw_featured_image_note(), // get note to show on current post type's Featured Image (Gutenberg).
		) );

	}

	// Widgets JavaScript
	// wp_enqueue_media() is run in classes/widget.php
	if ( 'widgets' === $screen->base ) { // don't enqueue unless needed

		// New media uploader in WP 3.5+
		wp_enqueue_media();

		// Color picker
		// Improvement to enqueue only when there is a widget with color field?
		wp_enqueue_script( 'wp-color-picker' );

		// Main widgets script
		wp_enqueue_script( 'ctfw-admin-widgets', get_theme_file_uri( CTFW_JS_DIR . '/admin-widgets.js' ), array( 'jquery' ), CTFW_THEME_VERSION ); // bust cache on update
		wp_localize_script( 'ctfw-admin-widgets', 'ctfw_widgets', ctfw_admin_widgets_js_data() ); // see admin-widgets.php

	}

}

add_action( 'admin_enqueue_scripts', 'ctfw_admin_enqueue_scripts' ); // admin-end only
