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
		'video',
		'audio',
		'pdf',
		'has_full_text'
	), $post_id );

	// Derive audio URL or embed code data from $video
	$data['video_url'] = '';
	$data['video_embed'] = '';
	if ( ctc_is_url( $data['video'] ) ) { // URL
		$data['video_url'] = $data['video'];
	} else { // otherwise it is embed code
		$data['video_embed'] = $data['video'];
	}

	// Derive video URL or embed code data from $audio
	$data['audio_url'] = '';
	$data['audio_embed'] = '';
	if ( ctc_is_url( $data['audio'] ) ) { // URL
		$data['audio_url'] = $data['audio'];
	} else { // otherwise it is embed code
		$data['audio_embed'] = $data['audio'];
	}

	// Derive PDF URL or embed code data from $pdf (future possibility; good to use $pdf_url instead of $pdf)
	$data['pdf_url'] = '';
	$data['pdf_embed'] = '';
	if ( ctc_is_url( $data['pdf'] ) ) { // URL
		$data['pdf_url'] = $data['pdf'];
	} else { // otherwise it is embed code
		$data['pdf_embed'] = $data['pdf'];
	}

	// Return filtered
	return apply_filters( 'ctc_sermon_data', $data );

}

