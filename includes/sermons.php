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
 * Refer to includes/posts.php:ctc_cpt_date_archive_setup() for full details
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
 * Get URL of MP3, PDF, etc. and return with [upload_url] replaced
 * (useful for imported sample content)
 */

function ctc_sermon_url( $post_id, $media_type ) { // audio, video or pdf

	// Validate type
	$media_types = array( 'audio', 'video', 'pdf' );
	if ( ! in_array( $media_type, $media_types ) ) {
		return false;
	}

	// Get it
	$url = get_post_meta( $post_id, '_ccm_sermon_' . $media_type . '_url', true );

	// Return filtered
	return apply_filters( 'ctc_sermon_url', $url, $post_id, $media_type );

}



