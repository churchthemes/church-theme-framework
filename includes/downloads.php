<?php
/**
 * Download Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2019, ChurchThemes.com
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

	$download_url = $url;

	// Must have extension to be downloadable.
	// It may be URL to SoundCloud, YouTube, etc.
	$filetype = wp_check_filetype( $download_url ); // remove any query string.
	if ( empty( $filetype['ext'] ) ) {
		$download_url = ''; // Return nothing, there is no file to download.
	}

	return apply_filters( 'ctfw_download_url', $download_url, $url );

}

