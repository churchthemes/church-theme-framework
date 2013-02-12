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

	// Replace [upload_url]
	$upload_dir = wp_upload_dir();
	$url = str_ireplace( '[upload_url]', $upload_dir['baseurl'], $url );

	return $url;

}

 
/**********************************
 * PAGINATION
 **********************************/

// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
 
add_filter( 'option_posts_per_page', 'ctc_sermon_archive_posts_per_page' ); // correct posts_per_page for sermon archives (date, category, tag, speaker)
	
function ctc_sermon_archive_posts_per_page( $value ) {

	// Don't let this mess with saving of default posts per page in admin
	if ( ! is_admin() ) {

		// Is per page set? If theme does not use this option, don't do this
		$per_page = ctc_option( 'sermons_per_page' );
		if ( ! empty( $per_page ) ) {

			// Which post type archives should this affect?
			$post_type_slugs = array( 'ccm_sermon' );
			
			// Which taxonomies should this affect?
			$taxonomies = array( 'ccm_sermon_category', 'ccm_sermon_tag', 'ccm_sermon_speaker' );

			// Use the sermon items per page value from Theme Options if there is a match
			if ( is_post_type_archive( $post_type_slugs ) || is_tax( $taxonomies ) ) {
				return $per_page;
			}
			
		}
		
	}

	// Otherwise let it behave normally
	return $value;

}
