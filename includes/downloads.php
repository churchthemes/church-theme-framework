<?php
/**
 * Download Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2015, ChurchThemes.com
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
 * If URL is local and theme supports 'ctfw-force-downloads', it will be piped through script to send "Save As" headers.
 * Otherwise, original URL will be returned (local or external) but only if it has an extension (ie. not URL to YouTube, SoundCloud, etc.)
 *
 * On <a> tags use download="download" attribute to attempt "Save As" for externally hosted files.
 * As of October, 2015, download attribute works on 60% browser use. When near 100%, will deprecate ctfw_force_download_url().
 *
 * Makes this:	http://yourname.com/?download=%2F2009%2F10%2Ffile.pdf
 * Out of:		http://yourname.com/wp-content/uploads/2013/05/file.pdf
 * 				http://yourname.com/wp-content/uploads/sites/6/2013/05/file.pdf (multisite)
 *
 * @since 1.7.2
 * @param string $url URL for file
 * @return string URL modified to force Save As if local or as is if external and has extension
 */
function ctfw_download_url( $url ) {

	// May return original URL if is external and has extension
	$download_url = $url;

	// Has extension?
	// If not, is not actual file (may be URL to SoundCloud, YouTube, etc.)
	$filetype = wp_check_filetype( $download_url ); // remove any query string
	if ( empty( $filetype['ext'] ) ) {

		// Return nothing; there is no file to download
		$download_url = '';

	} else {

		// If local and theme supports it, force "Save As" headers by piping via special URL
		$download_url = ctfw_force_download_url( $download_url );

	}

	return apply_filters( 'ctfw_download_url', $download_url, $url );

}

/**
 * Convert download URL to one that forces "Save As" via headers
 *
 * This keeps the browser from doing what it wants with the file (such as play MP3 or show PDF).
 * Note that file must be in uploads folder and extension must be allowed by WordPress.
 *
 * See ctfw_download_url() which uses this. Use it with download="download" attribute as fallback.
 * This function will be deprecated when near 100% browser support exists for the attribute.
 *
 * Makes this:	http://yourname.com/?download=%2F2009%2F10%2Ffile.pdf
 * Out of:		http://yourname.com/wp-content/uploads/2013/05/file.pdf
 * 				http://yourname.com/wp-content/uploads/sites/6/2013/05/file.pdf (multisite)
 *
 * @since 0.9
 * @param string $url URL for file
 * @return string URL forcing "Save As" on file if local
 */
function ctfw_force_download_url( $url ) {

	// In case URL is not local or feature not supported by theme
	$download_url = $url;

	// Theme supports this?
	if ( current_theme_supports( 'ctfw-force-downloads' ) ) {

		// Is URL local?
		if ( ctfw_is_local_url( $url ) ) {

			// Get URL to upload directory
			$upload_dir = wp_upload_dir();
			$upload_dir_url = $upload_dir['baseurl'];

			// Get relative URL for file
			$relative_url = str_replace( $upload_dir_url, '', $url ); // remove base URL
			$relative_url = ltrim( $relative_url ); // remove preceding slash

			// Is it actually relative?
			// If file is outside of upload directory, it won't be
			// And in that case it cannot be piped through ?download
			if ( ! preg_match( '/\:\/\//', $relative_url ) ) {

				// Add ?download=file to site URL
				$download_url = home_url( '/' ) . '?download=' . urlencode( $relative_url ) . '&nocache';

			}

		}

	}

	return apply_filters( 'ctfw_force_download_url', $download_url, $url );

}