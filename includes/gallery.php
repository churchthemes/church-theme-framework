<?php

/**********************************
 * PAGINATION
 **********************************/

/**
 * Filter the posts per page value for gallery archives (albums)
 *
 * This solves an issue with pagination not working correctly for custom post type archives and taxonomy templates
 * The solution is thanks to Justin Carroll in the fourth post here: http://wordpress.org/support/topic/custom-post-type-taxonomy-pagination
 */
 
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
// COULD FUNCTIONIZE THIS TO USE SAME FOR BOTH GALLERY AND SERMONS (make pagination.php)?
 
add_filter( 'option_posts_per_page', 'ctc_gallery_archive_posts_per_page' ); // correct posts_per_page for sermon archives (album)

function ctc_gallery_archive_posts_per_page( $value ) {

	// Don't let this mess with saving of default posts per page in admin
	if ( ! is_admin() ) {

		// Is per page set? If theme does not use this option, don't do this
		$per_page = ctc_option( 'gallery_items_per_page' );
		if ( ! empty( $per_page ) ) {
		
			// Which post type archives should this affect?
			$post_type_slugs = array( 'ccm_gallery_item' );
			
			// Which taxonomies should this affect?
			$taxonomies = array( 'ccm_gallery_album' );

			// Use the gallery items per page value from Theme Options if there is a match
			if ( is_post_type_archive( $post_type_slugs ) || is_tax( $taxonomies ) ) {
				return $per_page;
			}
			
		}
		
	}

	// Otherwise let it behave normally
	return $value;

}