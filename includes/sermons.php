<?php
/**
 * Sermon Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2017, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**********************************
 * SERMON WORDING
 **********************************/

/**
 * "Sermon" singular word from post type.
 *
 * This will be post type label, English or translated.
 * It may also be what Church Contet Pro settings dictate.
 *
 * When Church Content plugin inactive (post type not registered), a default string is used.
 *
 * @since 2.3
 * @return string Word for "Sermon"
 */
function ctfw_sermon_word_singular() {

	// Default in case Church Content plugin is inactive.
	$word = _x( 'Sermon', 'singular', 'church-theme-framework' );

	// Get registered post type label.
	$post_type_obj = get_post_type_object( 'ctc_sermon' );
	if ( ! empty( $post_type_obj->labels->singular_name ) ) { // post type registered.
		$word = $post_type_obj->labels->singular_name;
	}

	return apply_filters( 'ctfw_sermon_word_singular', $word );

}

/**
 * "Sermon" plural word from post type.
 *
 * This will be post type label, English or translated.
 * It may also be what Church Contet Pro settings dictate.
 *
 * When Church Content plugin inactive (post type not registered), a default string is used.
 *
 * @since 2.3
 * @return string Word for "Sermons"
 */
function ctfw_sermon_word_plural() {

	// Default in case Church Content plugin is inactive.
	$word = _x( 'Sermons', 'plural', 'church-theme-framework' );

	// Get registered post type label.
	$post_type_obj = get_post_type_object( 'ctc_sermon' );
	if ( ! empty( $post_type_obj->labels->name ) ) { // post type registered.
		$word = $post_type_obj->labels->name;
	}

	return apply_filters( 'ctfw_sermon_word_plural', $word );

}

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
	// Path and size will be populated for local files only
	$media_types = array( 'audio', 'video', 'pdf' );
	foreach ( $media_types as $media_type ) {

		$data[$media_type . '_extension'] = '';
		$data[$media_type . '_path'] = '';
		$data[$media_type . '_size_bytes'] = '';
		$data[$media_type . '_size'] = '';

		// Get extension
		// This can be determined for local and external files
		// Empty for YouTube, SoundCloud, etc.
		$filetype = wp_check_filetype( $data[$media_type] );
		$data[$media_type . '_extension'] = $filetype['ext'];

		// File is local, so can get path and size
		if ( $data[$media_type] && ctfw_is_local_url( $data[$media_type] ) ) {

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
	// URL is returned if is local or external and has an extension.
	// Those without an extension (YouTube, SoundCloud, etc. page URL) return empty (nothing to download).
	// If locally hosted, URL is changed to force "Save As" via headers.
	// Use <a href="" download="download"> to attempt Save As via limited browser support for externally hosted files.
	$data['video_download_url'] = ctfw_download_url( $data['video'] );
	$data['audio_download_url'] = ctfw_download_url( $data['audio'] );
	$data['pdf_download_url'] = ctfw_download_url( $data['pdf'] );

	// Has at least one downloadable file URL?
	$data['has_download'] = false;
	if ( $data['video_download_url'] || $data['audio_download_url'] || $data['pdf_download_url'] ) { // path empty if doesn't exist
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
 * More data may be added later, such as abbreviations.
 *
 * @since 1.7
 * @return Array Multidimentional array with keys for old_testament, new_testament and all
 */
function ctfw_bible_books() {

	$books = array();

	$books['old_testament'] = array(
		array(
			'name'	=> __( 'Genesis', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Exodus', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Leviticus', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Numbers', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Deuteronomy', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Joshua', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Judges', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Ruth', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '1 Samuel', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '2 Samuel', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '1 Kings', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '2 Kings', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '1 Chronicles', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '2 Chronicles', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Ezra', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Nehemiah', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Esther', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Job', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Psalms', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Proverbs', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Ecclesiastes', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Song of Solomon', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Isaiah', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Jeremiah', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Lamentations', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Ezekiel', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Daniel', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Hosea', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Joel', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Amos', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Obadiah', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Jonah', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Micah', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Nahum', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Habakkuk', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Zephaniah', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Haggai', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Zechariah', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Malachi', 'church-theme-framework' ),
		),
	);

	$books['new_testament'] = array(
		array(
			'name'	=> __( 'Matthew', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Mark', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Luke', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'John', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Acts', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Romans', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '1 Corinthians', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '2 Corinthians', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Galatians', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Ephesians', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Philippians', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Colossians', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '1 Thessalonians', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '2 Thessalonians', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '1 Timothy', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '2 Timothy', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Titus', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Philemon', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Hebrews', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'James', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '1 Peter', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '2 Peter', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '1 John', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '2 John', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( '3 John', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Jude', 'church-theme-framework' ),
		),
		array(
			'name'	=> __( 'Revelation', 'church-theme-framework' ),
		),
	);

	// Make filterable
	$books['old_testament'] = apply_filters( 'ctfw_bible_books_new_testament', $books['old_testament'] );
	$books['new_testament'] = apply_filters( 'ctfw_bible_books_old_testament', $books['new_testament'] );

	// Add testament to each book
	foreach ( $books['old_testament'] as $book_key => $book ) {
		$books['old_testament'][$book_key]['testament'] = 'old';
	}
	foreach ( $books['new_testament'] as $book_key => $book ) {
		$books['new_testament'][$book_key]['testament'] = 'new';
	}

	// Combine arrays for convenience
	$books['all'] = array_merge( $books['old_testament'], $books['new_testament'] );

	// Return everything filtered
	return apply_filters( 'ctfw_bible_books', $books );

}

/**
 * Sermon books by testament
 *
 * Return sermon books in order and organized by testament and with URL, number of sermons, etc.
 *
 * @since 1.7.2
 * @return array Books by testament
 */
function ctfw_sermon_books_by_testament() {

	// Get books, alphabetical
	$books = ctfw_content_type_archives( array(
		'specific_archive' => 'ctc_sermon_book',
	) );

	// Old new and other testaments
	$books_by_testament = array(
		'old' => array(
			'name' => __( 'Old Testament', 'church-theme-framework' ),
		),
		'new' => array(
			'name' => __( 'New Testament', 'church-theme-framework' ),
		),
		'other' => array(
			/* translators: Label for books not in the Old or New Testaments */
			'name' => __( 'Other Books', 'church-theme-framework' ),
		),
	);

	// Loop books to add per testament
	if ( ! empty( $books['items'] ) ) {

		foreach ( $books['items'] as $book ) {

			$testament = isset( $book->book_data['testament'] ) ? $book->book_data['testament'] : '';

			if ( 'old' == $testament ) {
				$books_by_testament['old']['books'][] = $book;
			} else if ( 'new' == $testament ) {
				$books_by_testament['new']['books'][] = $book;
			} else {
				$books_by_testament['other']['books'][] = $book;
			}

		}

	}

	return apply_filters( 'ctfw_sermon_books_by_testament', $books_by_testament );

}
