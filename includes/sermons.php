<?php
/**
 * Sermon Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.0
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
 * Use add_theme_support( 'ctfw-sermon-date-archive' ) and f
 */

add_action( 'generate_rewrite_rules', 'ctfw_sermon_date_archive' ); // enable date archive for sermon post type
 
function ctfw_sermon_date_archive( $wp_rewrite ) {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-sermon-date-archive' ) ) {
		return;
	}

	// Post types to setup date archives for
	$post_types = array(
		'ccm_sermon'
	);

	// Do it
	ctfw_cpt_date_archive_setup( $post_types, $wp_rewrite );

}

/**********************************
 * SERMON DATA
 **********************************/

/**
 * Get sermon meta data
 */

function ctfw_sermon_data( $post_id = null ) {

	// Get meta values
	$data = ctfw_get_meta_data( array( // without _ccm_sermon_ prefix
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

	// Return filtered
	return apply_filters( 'ctfw_sermon_data', $data );

}

