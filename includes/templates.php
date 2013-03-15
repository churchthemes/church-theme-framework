<?php
/**
 * Template Functions
 */

/*****************************************
 * LOAD TEMPLATES
 *****************************************/
 
/**
 * Get template part from directory
 *
 * This is a replacement for get_template_parts() for the purpose of loading templates from a directory.
 * This provides a cleaner, more organized structure for the theme contents.
 * If no directory is specified, the template-parts directory is used.
 *
 * Note: WordPress's get_template_part() may support something along these lines in the future, but it will not
 * break this use of a directory in any way: http://core.trac.wordpress.org/ticket/15086#comment:51
 */

function ctc_get_template_part( $slug, $name = false, $dir = false ) {

	// Default directory
	if ( empty( $dir ) ) {
		$dir = CTC_PARTS_DIR;
	}
	
	// Prepend directory to slug
	$slug = trailingslashit( $dir ) . $slug;
	
	// Load template part
	get_template_part( $slug, $name );

}

/**
 * Load content template based on current post type
 *
 * ctc_make_friendy() turns ccm_gallery_item into gallery-item for cleaner template names.
 */

function ctc_load_content_template() {

	get_template_part( 'content', ctc_make_friendly( get_post_type() ) ); 

}