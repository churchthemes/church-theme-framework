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
 * Use add_theme_support( 'ctfw-import-correct-urls', 'http://wp.dev/site' );
 *
 * @since 0.9.3
 * @global object $wpdb
 */
function ctfw_correct_imported_urls() {

	global $wpdb;

	// Theme supports this?
	$support = get_theme_support( 'ctfw-import-correct-urls' );
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

add_action( 'import_end', 'ctfw_correct_imported_urls' ); // WordPress Importer plugin hook

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


/******************************************
 * WIDGET IMPORTER
 ******************************************/

