<?php
/**
 * Download Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2019, ChurchThemes.com, LLC
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*************************************************
 * DOWNLOADS
 *************************************************/

/**
 * Get download URL
 *
 * URL returned if is downloadable (has extension, not YouTube, SoundCloud, etc.).
 * This will accommodate Dropbox URLs by make sure ?dl=1 is used.
 *
 * On <a> tags use download="download" attribute to attempt "Save As".
 * As of October, 2019, most browsers support (not IE11 or iOS, which doesn't save files anyway).
 *
 * Prior to framework version 2.6, this would use ctfw_force_download_url() to force downloads via headers.
 * Now we're relying on download attribute which is simpler, safer and not error-prone.
 *
 * @since 1.7.2
 * @param string $url URL for file
 * @return string URL modified to force Save As if local or as is if external and has extension
 */
function ctfw_download_url( $url ) {

	$download_url = $url;

	// Dropbox URL?
	$is_dropbox = '';
	if ( preg_match( '/dropbox/', $url ) ) {

		$is_dropbox = true;

		// Force ?dl=1 since download="download" attribute won't work with remote URL in most browser.
		$download_url = remove_query_arg( 'dl', $download_url );
		$download_url = remove_query_arg( 'raw', $download_url );
		$download_url = add_query_arg( 'dl', '1', $download_url );

	}

	// Must have extension or be Dropbox URL to be downloadable.
	// It may be URL to SoundCloud, YouTube, etc.
	$filetype = wp_check_filetype( $download_url ); // remove any query string.
	if ( empty( $filetype['ext'] ) && ! $is_dropbox ) {
		$download_url = ''; // Return nothing, there is no file to download.
	}

	return apply_filters( 'ctfw_download_url', $download_url, $url );

}
