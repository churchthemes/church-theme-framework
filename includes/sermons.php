<?php
/**
 * Sermon Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**********************************
 * SERMON ARCHIVES
 **********************************/

/**
 * Enable date archives for sermon posts
 *
 * At time of making, WordPress (3.6 and possibly later) does not support dated archives for custom post types as it does for standard posts
 * This injects rules so that URL's like /cpt/2012/05 can be used with the custom post type archive template.
 * Refer to ctfw_cpt_date_archive_setup() for full details.
 *
 * Use add_theme_support( 'ctfw-sermon-date-archive' )
 *
 * @since 0.9
 * @param object $wp_rewrite object
 */
function ctfw_sermon_date_archive( $wp_rewrite ) {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-sermon-date-archive' ) ) {
		return;
	}

	// Post types to setup date archives for
	$post_types = array(
		'ctc_sermon'
	);

	// Do it
	ctfw_cpt_date_archive_setup( $post_types, $wp_rewrite );

}

add_action( 'generate_rewrite_rules', 'ctfw_sermon_date_archive' ); // enable date archive for sermon post type

/**********************************
 * SERMON DATA
 **********************************/

/**
 * Get sermon data
 *
 * @since 0.9
 * @param int $post_id Post ID to get data for; null for current post
 * @return array Sermon data
 */
function ctfw_sermon_data( $post_id = null ) {

	// Get URL to upload directory
	$upload_dir = wp_upload_dir();
	$upload_dir_url = $upload_dir['baseurl'];

	// Get meta values
	$data = ctfw_get_meta_data( array( // without _ctc_sermon_ prefix
		'video',		// URL to uploaded file, external file, external site with oEmbed support, or manual embed code (HTML or shortcode)
		'audio',		// URL to uploaded file, external file, external site with oEmbed support, or manual embed code (HTML or shortcode)
		'pdf',			// URL to uploaded file or external file
		'has_full_text'
	), $post_id );

	// Get media player code
	// Embed code generated from uploaded file, URL for file on other site, page on oEmbed-supported site, or manual embed code (HTML or shortcode)
	$data['video_player'] = ctfw_embed_code( $data['video'] );
	$data['audio_player'] = ctfw_embed_code( $data['audio'] );

	// Get download URL's
	// Only local files can have "Save As" forced
	// Only local files can are always actual files, not pages (ie. YouTube, SoundCloud, etc.)
	// Video and Audio URL's may be pages on other site (YouTube, SoundCloud, etc.), so provide download URL only for local files
	// PDF is likely always to be actual file, so provide download URL no matter what (although cannot force "Save As" on external sites)
	$data['video_download_url'] = ctfw_is_local_url( $data['video'] ) ? ctfw_force_download_url( $data['video'] ) : ''; // provide URL only if local so know it is actual file (not page) and can force "Save As"
	$data['audio_download_url'] = ctfw_is_local_url( $data['audio'] ) ? ctfw_force_download_url( $data['audio'] ) : ''; // provide URL only if local so know it is actual file (not page) and can force "Save As"
	$data['pdf_download_url'] = ctfw_force_download_url( $data['pdf'] ); // provide URL only if local so know it is actual file (not page) and can force "Save As"

	// Has at least one download?
	$data['has_download'] = false;
	if ( $data['video_download_url'] || $data['audio_download_url'] || $data['pdf_download_url'] ) {
		$data['has_download'] = true;
	}

	// Get file data for media
	// This will be populated for local files only
	$media_types = array( 'audio', 'video', 'pdf' );
	foreach ( $media_types as $media_type ) {

		$data[$media_type . '_extension'] = '';
		$data[$media_type . '_path'] = '';
		$data[$media_type . '_size_bytes'] = '';
		$data[$media_type . '_size'] = '';

		// Local only
		if ( ctfw_is_local_url( $data[$media_type] ) ) { // only if it is local and downloadable

			// File type
			$filetype = wp_check_filetype( $data[$media_type] );
			$data[$media_type . '_extension'] = $filetype['ext'];

			// File size
			$data[$media_type . '_path'] = $upload_dir['basedir'] . str_replace( $upload_dir_url, '', $data[$media_type] );
			$data[$media_type . '_size_bytes'] = filesize( $data[$media_type . '_path'] );
			$data[$media_type . '_size'] = size_format( $data[$media_type . '_size_bytes'] ); // 30 MB, 2 GB, 220 kB, etc.

		}

	}

	// Return filtered
	return apply_filters( 'ctfw_sermon_data', $data );

}
