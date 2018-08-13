<?php
/**
 * Import Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013 - 2017, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9.3
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/******************************************
 * IMPORTATION
 ******************************************/

/**
 * Remove image upscaling support on theme import
 *
 * It can slow things down, risking execution timeout.
 * Regenerate Thumbnails plugin can be used afterward if necessary.
 *
 * @since 1.0.5
 */
function ctfw_import_remove_upscaling() {
	remove_theme_support( 'ctfw-image-upscaling' );
}

add_action( 'import_start', 'ctfw_import_remove_upscaling' );

/******************************************
 * PERMALINK STRUCTURE
 ******************************************/

/**
 * Switch to "Post name" permalink structure
 *
 * This is the format sample content uses.
 * Only fires when importing from "samplecontent" user.
 *
 * Later could make this accept structure and user -  if needed.
 *
 * add_theme_support( 'ctfw-import-set-permalink-structure' );
 *
 * @since 1.8.6
 * @global object $wp_rewrite
 */
function ctfw_import_set_permalink_structure() {

	global $wp_rewrite;

	// Theme supports this?
	if ( current_theme_supports( 'ctfw-import-set-permalink-structure' ) ) {

		// Get current permalink structure
		$permalink_structure = get_option( 'permalink_structure' );

		// Stop if pretty permalinks not supported
		// This will be true if so since WordPress already would have set "Day and name"
		if ( ! $permalink_structure ) {
			return;
		}

		// Stop if pretty permalinks is already "Post name"
		if ( '/%postname%/' == $permalink_structure ) {
			return;
		}

		// Is content from "sampleuser"?
		// We don't want to change user's setting except when they import our sample content
		if ( isset( $_POST['imported_authors'] ) && in_array( 'sampleuser', $_POST['imported_authors'] ) ) {

			// Update setting and flush rewrite rules
			$wp_rewrite->set_permalink_structure( '/%postname%/' );

			// Hard flush rewrite rules
			$wp_rewrite->flush_rules( true );

		}

	}

}

add_action( 'import_start', 'ctfw_import_set_permalink_structure' );

/******************************************
 * URL CORRECTION
 ******************************************/

/**
 * Correct imported URL's in menu, content, etc.
 *
 * This assumes the WordPress Importer plugin is used.
 *
 * Sample import XML file may have URLs from the dev site in menu, content, meta fields, etc.
 * This will replace all of those instances with the current site's base URL.
 *
 * Note: this does not replace URLs in widgets. See ctfw_correct_imported_widget_urls() for that.
 *
 * add_theme_support( 'ctfw-import-correct-urls', array(
 *	'url'			=> 'https://demos.churchthemes.com/' . CTFW_THEME_SLUG . '-sample', // base URL to replace for imported files
 *	'multisite_id'	=> 9, // site ID if imported files are coming off of a multisite installation
 * ) );
 *
 * @since 0.9.3
 * @global object $wpdb
 */
function ctfw_correct_imported_urls() {

	global $wpdb;

	// Theme supports this?
	$url_data = ctfw_url_correction_data(); // empty if no theme support
	if ( $url_data ) {

		// Queries to run
		// Based on code from the Velvet Blues Update URLs plugin: http://wordpress.org/plugins/velvet-blues-update-urls/developers/
		$queries = array(

			// Posts, pages, custom post types, revisions
			"UPDATE $wpdb->posts SET post_content = REPLACE(post_content, %s, %s)",

			// Excerpts
			"UPDATE $wpdb->posts SET post_excerpt = REPLACE(post_excerpt, %s, %s)",

			// Attachments
			"UPDATE $wpdb->posts SET guid = REPLACE(guid, %s, %s) WHERE post_type = 'attachment'",

			// Custom fields, menu items
			"UPDATE $wpdb->postmeta SET meta_value = REPLACE(meta_value, %s, %s)",

			// GUIDs
			"UPDATE $wpdb->posts SET guid = REPLACE(guid, %s, %s)"

		);

		// Run queries to make replacements
		foreach ( $queries as $query ) {

			// Update file upload URLs
			// This accounts for differing URLs between single and multisite installs
			$wpdb->query( $wpdb->prepare( $query, $url_data['old_uploads_url'], $url_data['new_uploads_url'] ) );

			// Update URLs in general
			$wpdb->query( $wpdb->prepare( $query, $url_data['old_url'], $url_data['new_url'] ) );

		}

	}

}

add_action( 'import_end', 'ctfw_correct_imported_urls' ); // WordPress Importer plugin hook

/**
 * Correct imported URL's in widgets
 *
 * This assumes the Widget Importer & Exporter.
 *
 * A sample widget file may have URLs from the dev site in widget settings.
 * This will replace all of those instances with the current site's base URL.
 *
 * Example use: add_theme_support( 'https://demos.churchthemes.com/' . CTFW_THEME_SLUG . '-sample' );
 *
 * @since 0.9.3
 * @param array $widget Widget settings
 * @return array Modified widget settings
 */
function ctfw_correct_imported_widget_urls( $widget ) {

	// Theme supports this?
	$url_data = ctfw_url_correction_data(); // empty if no theme support
	if ( $url_data ) {

		// Loop widget's settings to modify values
		foreach ( $widget as $field => $value ) {

			// Replace file upload URLs
			// This accounts for differing URLs between single and multisite installs
			$widget->$field = str_replace( $url_data['old_uploads_url'], $url_data['new_uploads_url'], $value );

			// Replace URLs in general
			$widget->$field = str_replace( $url_data['old_url'], $url_data['new_url'], $value );

		}

	}

	// Return for importer to use
	return $widget;

}

add_filter( 'wie_widget_settings', 'ctfw_correct_imported_widget_urls' ); // Widget Importer & Exporter plugin hook

/**
 * URL correction data
 *
 * Returns URL correction data if feature is supported and URL needs changed.
 *
 * @since 0.9.3
 */
function ctfw_url_correction_data() {

	$data = array();

	// Theme supports this?
	$support = get_theme_support( 'ctfw-import-correct-urls' );
	if ( ! empty( $support[0] ) ) {

		// If arguments is URL, create array of arguments out of it
		// Back-compat for URL string as second parameter: add_theme_support( 'ctfw-import-correct-urls', 'http://wp.dev/site' );
		if ( ! is_array( $support[0] ) ) {
			$support[0] = array(
				'url' => $support[0],
			);
		}

		// Default arguments
		$args = wp_parse_args( $support[0], array(
			'url'			=> '', // base URL to replace for imported files
			'multisite_id'	=> '', // site ID if imported files coming off of multisite network
		) );

		// Base URLs
		$data['old_url'] = untrailingslashit( $args['url'] ); // URL to replace
		$data['new_url'] = untrailingslashit( home_url() ); // this site's home URL

		// Upload URLs
		$upload_dir = wp_upload_dir();
		$multisite_path = ! empty( $args['multisite_id'] ) ? '/sites/' . $args['multisite_id'] : ''; // append path for multisite
		$data['old_uploads_url'] = $data['old_url'] . '/' . basename( WP_CONTENT_DIR ) . '/uploads' . $multisite_path;
		$data['new_uploads_url'] = $upload_dir['baseurl']; // could be multisite

	}

	return apply_filters( 'ctfw_url_correction_data', $data );

}

add_action( 'admin_init', 'ctfw_url_correction_data' );

/******************************************
 * STATIC FRONT PAGE
 ******************************************/

/**
 * Check if homepage set for static front before import
 *
 * Set a global if page using homepage template does not exist before import.
 * This helps determine after import if the homepage was imported.
 *
 * add_theme_support( 'ctfw-import-set-static-front' );
 *
 * @since 0.9.3
 * @global $ctfw_import_no_homepage_before
 */
function ctfw_import_check_static_front() {

	global $ctfw_import_no_homepage_before;

	// Default
	$ctfw_import_no_homepage_before = true;

	// Theme supports this?
	$support = get_theme_support( 'ctfw-import-set-static-front' );
	if ( ! empty( $support[0] ) ) {

		// Get homepage template
		$homepage_tpl = $support[0];

		// Check if page using that template exists
		$homepage_page = ctfw_get_page_by_template( $homepage_tpl );
		if ( ! $homepage_page ) {

			// If not, we'll want to set homepage after import
			$ctfw_import_no_homepage_before = true;

		}

	}

}

add_action( 'import_start', 'ctfw_import_check_static_front' ); // WordPress Importer plugin hook

/**
 * Set homepage as static front page after import
 *
 * If no static front is set and page using homepage template did not exist before import, set it.
 * Page using blog template is set as Posts Page if nothing already set.
 *
 * add_theme_support( 'ctfw-import-set-static-front' );
 *
 * @since 0.9.3
 * @global $ctfw_import_no_homepage_before
 */
function ctfw_import_set_static_front() {

	global $ctfw_import_no_homepage_before;

	// Theme supports this?
	$support = get_theme_support( 'ctfw-import-set-static-front' );
	if ( ! empty( $support[0] ) ) {

		// Page using homepage template did not exist before import
		if ( $ctfw_import_no_homepage_before ) {

			// No static front page is set
			if ( get_option( 'show_on_front' ) != 'page' || ! get_option( 'page_on_front' ) ) {

				// Get homepage template
				$homepage_tpl = $support[0];

				// Check if page using that template exists now
				$homepage_id = ctfw_get_page_id_by_template( $homepage_tpl );
				if ( $homepage_id ) {

					// Set it as static front page
					update_option( 'show_on_front', 'page' );
					update_option( 'page_on_front', $homepage_id );

					// Is Posts Page set?
					if ( ! get_option( 'page_for_posts' ) ) {

						// Use content types to determine Blog page template
						$blog_tpl = ctfw_page_template_by_content_type( 'blog' );

						// Does Blog page exist?
						$blog_page_id = ctfw_get_page_id_by_template( $blog_tpl );
						if ( $blog_page_id ) {

							// Set as Posts Page
							update_option( 'page_for_posts', $blog_page_id );

						}

					}

				}

			}

		}

	}

}

add_action( 'import_end', 'ctfw_import_set_static_front' ); // WordPress Importer plugin hook

/******************************************
 * MENU LOCATIONS
 ******************************************/

/**
 * Set menu locations after import
 *
 * If zero locations already set, sample menus (if exist) are set to appropriate location.
 * If at least one location is set, assume admin is done configuring.
 *
 * Use add_theme_support( 'ctfw-import-set-menu-locations' );
 *
 * @since 0.9.3
 */
function ctfw_import_set_menu_locations() {

	// Theme supports this?
	$support = get_theme_support( 'ctfw-import-set-menu-locations' );
	if ( ! empty( $support[0] ) ) {

		// Locations and menus
		$locations = $support[0];

		// Get currently set locations
		$nav_menu_locations = get_nav_menu_locations();

		// If zero locations set, set them
		$check_nav_menu_locations = array_filter( $nav_menu_locations ); // remove empty values (may be set to 0)
		if ( empty( $check_nav_menu_locations ) ) {

			// Loop locations and corresponding menu
			foreach ( $locations as $location => $menu ) {

				// Does menu exist?
				if ( is_nav_menu( $menu ) ) {

					// Get menu ID
					$menu_obj = wp_get_nav_menu_object( $menu );

					// Set it on appropriate location
					if ( ! empty( $menu_obj->term_id ) ) {
						$nav_menu_locations[$location] = $menu_obj->term_id;
					}

				}

			}

			// Update menu theme mod
			set_theme_mod( 'nav_menu_locations', $nav_menu_locations );

		}

	}

}

add_action( 'import_end', 'ctfw_import_set_menu_locations' ); // WordPress Importer plugin hook

/******************************************
 * WP SAMPLE CONTENT
 ******************************************/

/**
 * Delete WordPress sample content before import
 *
 * Move the sample post, page and comment that fresh WordPress installs have into Trash.
 *
 * Use add_theme_support( 'ctfw-import-delete-wp-content' );
 *
 * @since 0.9.3
 */
function ctfw_import_delete_wp_sample_content() {

	// Theme supports this?
	if ( current_theme_supports( 'ctfw-import-delete-wp-content' ) ) {

		// Sample post
		wp_delete_post( 1 ); // move to trash

		// Sample page
		wp_delete_post( 2 ); // move to trash

		// Sample content
		wp_delete_comment( 1 );

	}

}

add_action( 'import_start', 'ctfw_import_delete_wp_sample_content' ); // WordPress Importer plugin hook

/**
 * Delete WordPress sample widgets on import of .xml or .wie import (Wigdet Importer & Exporter)
 *
 * Remove search, comments and meta WordPress widgets added to the first widget area.
 * Does this only when those and only those widgets exist, so can be nearly certain user didn't add them like that.
 *
 * Use add_theme_support( 'ctfw-import-delete-wp-widgets' );
 *
 * @since 2.0
 */
function ctfw_import_delete_wp_sample_widgets() {

	// Theme supports this?
	if ( current_theme_supports( 'ctfw-import-delete-wp-widgets' ) ) {

		// Get widget areas and their widget instances
		$sidebars_widgets = get_option( 'sidebars_widgets' ); // get sidebars and their unique widgets IDs

		// Have widget area data
		if ( ! empty( $sidebars_widgets ) && count( $sidebars_widgets ) > 1 ) {

			// Get first widget area
			// It's second item because wp_inactive_widgets exists at beginning of array
			$first_sidebar = array_slice( $sidebars_widgets, 1, 1 );

			// Have first sidebar
			if ( $first_sidebar ) {

				// Get widgets and sidebar ID
				$widget_instances = reset( $first_sidebar );
				$sidebar_id = key( $first_sidebar );

				// Are there 3 and they are 0 = search, 1 = comments and 2 = meta?
				if (
					count( $widget_instances ) == 3
					&& preg_match( '/search/', $widget_instances[0] )
					&& preg_match( '/comments/', $widget_instances[1] )
					&& preg_match( '/meta/', $widget_instances[2] )
				) {

					// Remove them from array
					$sidebars_widgets[$sidebar_id] = array();

					// Update sidebars_widgets option without them
					update_option( 'sidebars_widgets', $sidebars_widgets );

				}

			}

		}

	}

}

add_action( 'import_start', 'ctfw_import_delete_wp_sample_widgets' );
add_action( 'wie_before_import', 'ctfw_import_delete_wp_sample_widgets' ); // WordPress Importer & Exporter plugin hook

/******************************************
 * WP SETTINGS
 ******************************************/

/**
 * Update settings when importing content from "sampleuser"
 *
 * add_theme_support( 'ctfw-import-update-settings' );
 *
 * @since 2.0
 */
function ctfw_import_update_settings() {

	// Theme supports this?
	$support = get_theme_support( 'ctfw-import-update-settings' );
	if ( ! empty( $support[0] ) ) {

		// Get new settings
		$settings = $support[0];

		// Loop to update settings
		foreach ( $settings as $name => $value ) {
			update_option( $name, $value );
		}

	}

}

add_action( 'import_start', 'ctfw_import_update_settings' );
