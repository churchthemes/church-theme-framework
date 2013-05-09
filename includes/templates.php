<?php
/**
 * Template Functions
 */

/**
 * Load content template
 *
 * Load content-*.php according to post type and post format.
 */
 
 function ctc_load_content_template() {

	// Default template slug and name
	$slug = 'content';
	$name = '';

	// Get post type and format
	$post_type = get_post_type();
	$post_format = get_post_format();

	// Regular post using post format
	// image, video, aside, link, quote, etc.
	if ( 'post' == $post_type && $post_format ) {
		$name = $post_format;
	}

	// Other post type
	// page, custom post type, attachment, etc.
	else {
		$name = ctc_make_friendly( $post_type ); // ctc_make_friendly() turns "ccm_post_type" into "post-type"
	}

	// Load template
	get_template_part( $slug, $name );

}
