<?php
/**
 * People Functions
 */

/**********************************
 * DATA
 **********************************/

/**
 * Get person meta data
 */

function ctc_person_data( $post_id = null ) {

	// Get meta values
	$meta = ctc_get_meta_data( array( // without _ccm_person_ prefix
		'position',
		'phone',
		'email',
		'urls',
	), $post_id );

	// Return filtered
	return apply_filters( 'ctc_person_data', $meta );

}

