<?php
/**
 * Meta Data Functions
 */

/*********************************
 * META DATA
 *********************************

/**
 * Get meta data for a post/type (without prefix)
 *
 * Provide $fields as array without meta field's post type prefix (_ccm_sermon_ for example)
 */

function ctfw_get_meta_data( $fields, $post_id = null, $prefix = null ) {

	$meta = array();

	// Have fields
	if ( ! empty( $fields ) ) {

		// Use current post ID if none set
		if ( ! isset( $post_id ) ) {
			$post_id = get_the_ID();
		}

		// Have post ID
		if ( $post_id ) {

			// Post type as prefix for meta field
			if ( ! isset( $prefix ) ) {
				$post_type = get_post_type( $post_id );
				$prefix = '_' . $post_type . '_';
			}

			// Loop fields to get values
			foreach( $fields as $field ) {
				$meta[$field] = get_post_meta( $post_id, $prefix . $field, true );
			}

		}

	}

	return apply_filters( 'ctfw_get_meta_data', $meta, $post_id );

}
