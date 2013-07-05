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
 * WORDPRESS IMPORTER
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
 * WIDGET IMPORTER
 ******************************************/

