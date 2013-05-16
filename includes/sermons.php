<?php
/**
 * Sermon Functions
 */

/**********************************
 * ARCHIVES (Dates, Categories, Tags, Speakers)
 **********************************/

/**
 * Enable date archives for sermon posts
 * At time of making, WordPress (3.4 and possibly later) does not support dated archives for custom post types as it does for standard posts
 * This injects rules so that URL's like /cpt/2012/05 can be used with the custom post type archive template
 * Refer to ctc_cpt_date_archive_setup() for full details
 */

add_action( 'generate_rewrite_rules', 'ctc_sermon_date_archive' ); // enable date archive for sermon post type
 
function ctc_sermon_date_archive( $wp_rewrite ) {

	// Post types to setup date archives for
	$post_types = array(
		'ccm_sermon'
	);

	// Do it
	ctc_cpt_date_archive_setup( $post_types, $wp_rewrite );

}

/**********************************
 * DATA
 **********************************/

/**
 * Get sermon meta data
 */

function ctc_sermon_data( $post_id = null ) {

	// Get meta values
	$data = ctc_get_meta_data( array( // without _ccm_sermon_ prefix
		'video',		// URL to uploaded file, external file, external site with oEmbed support, or manual embed code (HTML or shortcode)
		'audio',		// URL to uploaded file, external file, external site with oEmbed support, or manual embed code (HTML or shortcode)
		'pdf',			// URL to uploaded file or external file
		'has_full_text'
	), $post_id );

	// Get media player code
	// Embed code generated from uploaded file, URL for file on other site, page on oEmbed-supported site, or manual embed code (HTML or shortcode)
	$data['video_player'] = ctc_embed_code( $data['video'] );
	$data['audio_player'] = ctc_embed_code( $data['audio'] );

	// Get download URL's
	// Only local files can have "Save As" forced
	// Only local files can are always actual files, not pages (ie. YouTube, SoundCloud, etc.)
	// Video and Audio URL's may be pages on other site (YouTube, SoundCloud, etc.), so provide download URL only for local files
	// PDF is likely always to be actual file, so provide download URL no matter what (although cannot force "Save As" on external sites)
	$data['video_download_url'] = ctc_is_local_url( $data['video'] ) ) ? ctc_force_download_url( $data['video'] ) : ''; // provide URL only if local so know it is actual file (not page) and can force "Save As"
	$data['audio_download_url'] = ctc_is_local_url( $data['audio'] ) ) ? ctc_force_download_url( $data['audio'] ) : ''; // provide URL only if local so know it is actual file (not page) and can force "Save As"
	$data['pdf_download_url'] = ctc_force_download_url( $data['pdf'] ); // provide URL only if local so know it is actual file (not page) and can force "Save As"

	// Return filtered
	return apply_filters( 'ctc_sermon_data', $data );

}

