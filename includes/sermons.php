<?php
/**
 * Sermon Functions
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

	// Get file data for media
	// This will be populated for local files only
	$media_types = array( 'audio', 'video', 'pdf' );
	foreach ( $media_types as $media_type ) {

		$data[$media_type . '_extension'] = '';
		$data[$media_type . '_path'] = '';
		$data[$media_type . '_size_bytes'] = '';
		$data[$media_type . '_size'] = '';

		// Local URL only, if file actually exists
		if ( $data[$media_type] && ctfw_is_local_url( $data[$media_type] ) ) { // only if it is local and downloadable

			// Local path
			$data[$media_type . '_path'] = $upload_dir['basedir'] . str_replace( $upload_dir_url, '', $data[$media_type] );

			// Exists?
			if ( ! file_exists( $data[$media_type . '_path'] ) ) {
				$data[$media_type . '_path'] = ''; // clear it
			} else {

				// File type
				$filetype = wp_check_filetype( $data[$media_type] );
				$data[$media_type . '_extension'] = $filetype['ext'];

				// File size
				$data[$media_type . '_size_bytes'] = filesize( $data[$media_type . '_path'] );
				$data[$media_type . '_size'] = size_format( $data[$media_type . '_size_bytes'] ); // 30 MB, 2 GB, 220 kB, etc.

			}

		}

	}

	// Get download URL's
	// Only local files can have "Save As" forced
	// Only local files can are always actual files, not pages (ie. YouTube, SoundCloud, etc.)
	// Video and Audio URL's may be pages on other site (YouTube, SoundCloud, etc.), so provide download URL only for local files
	// PDF is likely always to be actual file, so provide download URL no matter what (although cannot force "Save As" on external sites)
	// $data['video_path'] and $data['audio_path'] are empty if not local or if local but file does not exist
	$data['video_download_url'] = $data['video_path'] ? ctfw_force_download_url( $data['video'] ) : ''; // provide URL only if local so know it is actual file (not page) and can force "Save As"
	$data['audio_download_url'] = $data['audio_path'] ? ctfw_force_download_url( $data['audio'] ) : ''; // provide URL only if local so know it is actual file (not page) and can force "Save As"
	$data['pdf_download_url'] = ctfw_force_download_url( $data['pdf'] ); // PDF is likely always to be actual file, so provide download URL no matter what (although cannot force "Save As" on external sites)

	// Has at least one download that exists locally?
	$data['has_download'] = false;
	if ( $data['video_path'] || $data['audio_path'] || $data['pdf_path'] ) { // path empty if doesn't exist
		$data['has_download'] = true;
	}

	// Return filtered
	return apply_filters( 'ctfw_sermon_data', $data );

}

/**********************************
 * BOOKS
 **********************************/

/**
 * Books of the Bible
 *
 * Books of the Bible in old and new testaments, listed in canonical order.
 * This can assist with ordering the Book taxonomy terms and creating a Scripture archive template.
 *
 * @since 1.7
 * @return Array Multidimentional array with keys for old_testament, new_testament and all
 */
function ctfw_bible_books() {

	$books = array();

	$books['old_testament'] = array(
		__( 'Genesis', 'church-theme-framework' ),
		__( 'Exodus', 'church-theme-framework' ),
		__( 'Leviticus', 'church-theme-framework' ),
		__( 'Numbers', 'church-theme-framework' ),
		__( 'Deuteronomy', 'church-theme-framework' ),
		__( 'Joshua', 'church-theme-framework' ),
		__( 'Judges', 'church-theme-framework' ),
		__( 'Ruth', 'church-theme-framework' ),
		__( '1 Samuel', 'church-theme-framework' ),
		__( '2 Samuel', 'church-theme-framework' ),
		__( '1 Kings', 'church-theme-framework' ),
		__( '2 Kings', 'church-theme-framework' ),
		__( '1 Chronicles', 'church-theme-framework' ),
		__( '2 Chronicles', 'church-theme-framework' ),
		__( 'Ezra', 'church-theme-framework' ),
		__( 'Nehemiah', 'church-theme-framework' ),
		__( 'Esther', 'church-theme-framework' ),
		__( 'Job', 'church-theme-framework' ),
		__( 'Psalm', 'church-theme-framework' ),
		__( 'Proverbs', 'church-theme-framework' ),
		__( 'Ecclesiastes', 'church-theme-framework' ),
		__( 'Song of Solomon', 'church-theme-framework' ),
		__( 'Isaiah', 'church-theme-framework' ),
		__( 'Jeremiah', 'church-theme-framework' ),
		__( 'Lamentations', 'church-theme-framework' ),
		__( 'Ezekiel', 'church-theme-framework' ),
		__( 'Daniel', 'church-theme-framework' ),
		__( 'Hosea', 'church-theme-framework' ),
		__( 'Joel', 'church-theme-framework' ),
		__( 'Amos', 'church-theme-framework' ),
		__( 'Obadiah', 'church-theme-framework' ),
		__( 'Jonah', 'church-theme-framework' ),
		__( 'Micah', 'church-theme-framework' ),
		__( 'Nahum', 'church-theme-framework' ),
		__( 'Habakkuk', 'church-theme-framework' ),
		__( 'Zephaniah', 'church-theme-framework' ),
		__( 'Haggai', 'church-theme-framework' ),
		__( 'Zechariah', 'church-theme-framework' ),
		__( 'Malachi', 'church-theme-framework' ),
	);

	$books['new_testament'] = array(
		__( 'Matthew', 'church-theme-framework' ),
		__( 'Mark', 'church-theme-framework' ),
		__( 'Luke', 'church-theme-framework' ),
		__( 'John', 'church-theme-framework' ),
		__( 'Acts', 'church-theme-framework' ),
		__( 'Romans', 'church-theme-framework' ),
		__( '1 Corinthians', 'church-theme-framework' ),
		__( '2 Corinthians', 'church-theme-framework' ),
		__( 'Galatians', 'church-theme-framework' ),
		__( 'Ephesians', 'church-theme-framework' ),
		__( 'Philippians', 'church-theme-framework' ),
		__( 'Colossians', 'church-theme-framework' ),
		__( '1 Thessalonians', 'church-theme-framework' ),
		__( '2 Thessalonians', 'church-theme-framework' ),
		__( '1 Timothy', 'church-theme-framework' ),
		__( '2 Timothy', 'church-theme-framework' ),
		__( 'Titus', 'church-theme-framework' ),
		__( 'Philemon', 'church-theme-framework' ),
		__( 'Hebrews', 'church-theme-framework' ),
		__( 'James', 'church-theme-framework' ),
		__( '1 Peter', 'church-theme-framework' ),
		__( '2 Peter', 'church-theme-framework' ),
		__( '1 John', 'church-theme-framework' ),
		__( '2 John', 'church-theme-framework' ),
		__( '3 John', 'church-theme-framework' ),
		__( 'Jude', 'church-theme-framework' ),
		__( 'Revelation', 'church-theme-framework' ),
	);

	// Make filterable
	$books['old_testament'] = apply_filters( 'ctfw_bible_books_new_testament', $books['old_testament'] );
	$books['new_testament'] = apply_filters( 'ctfw_bible_books_old_testament', $books['new_testament'] );

	// Combine arrays for convenience
	$books['all'] = array_merge( $books['new_testament'], $books['new_testament'] );

	// Return everything filtered
	return apply_filters( 'ctfw_bible_books', $books );

}