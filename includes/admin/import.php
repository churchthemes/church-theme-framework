<?php
/**
 * Import Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9.3
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

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
 * Use add_theme_support( 'ctfw-correct-imported-urls', 'http://wp.dev/site' );
 *
 * @since 0.9.3
 * @global object $wpdb
 */
function ctfw_correct_imported_urls() {

	global $wpdb;

	// Theme supports this?
	$support = get_theme_support( 'ctfw-correct-imported-urls' );
	if ( ! empty( $support[0] ) ) {

		// Base URLs
		$old_url = untrailingslashit( $support[0] ); // URL to replace
		$new_url = untrailingslashit( home_url() ); // this site's home URL

		// Upload URLs
		$upload_dir = wp_upload_dir();
		$old_uploads_url = $old_url . '/' . basename( WP_CONTENT_DIR ) . '/uploads'; // we assume import data uses single, not multisite
		$new_uploads_url = $upload_dir['baseurl']; // could be multisite

		// This site is not the same site sample content came from
		if ( $new_url != $old_url ) {

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
				$wpdb->query( $wpdb->prepare( $query, $old_uploads_url, $new_uploads_url ) );

				// Update URLs in general
				$wpdb->query( $wpdb->prepare( $query, $old_url, $new_url ) );

			}

		}

	}

}

add_filter( 'import_end', 'ctfw_correct_imported_urls' ); // WordPress Importer plugin hook

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
 * @global $ctfw_no_homepage_before_import
 */
function ctfw_import_check_static_front() {

	global $ctfw_no_homepage_before_import;

	// Theme supports this?
	if ( current_theme_supports( 'ctfw-import-set-static-front' ) ) {

		// Use content types to determine Homepage page template.
	    
		// Check if page using that template exists

			// If not, we'll want to set homepage after import
			// $ctfw_import_set_static_front = true;

	}

}

add_filter( 'import_start', 'ctfw_correct_imported_urls' ); // WordPress Importer plugin hook

/**
 * Set homepage as static front page after import
 * 
 * If no static front is set and page using homepage template did not exist before import, set it.
 * Page using blog template is set as Posts Page if nothing already set.
 *
 * add_theme_support( 'ctfw-import-set-static-front' );
 *
 * @since 0.9.3
 * @global $ctfw_no_homepage_before_import
 */
function ctfw_import_set_static_front() {

	global $ctfw_no_homepage_before_import;

	// Theme supports this?
	if ( current_theme_supports( 'ctfw-import-set-static-front' ) ) {

		// No static front page is set

			// Page using homepage template did not exist before import
			if ( $ctfw_no_homepage_before_import ) {

				// Use content types to determine Homepage page template
				
				// Get page using homepage template

				// Set it as static front page

				// Posts Page is not set...

					// Use content types to determine Blog page template

					// Get page using Blog template

					// Set as Posts Page

			}

	}

}

add_filter( 'import_end', 'ctfw_correct_imported_urls' ); // WordPress Importer plugin hook

/******************************************
 * WIDGET IMPORTER
 ******************************************/

