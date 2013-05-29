<?php
/**
 * Post Functions
 *
 * These related to posts in general - all types.
 */

/**
 * Add useful post classes
 */

add_filter( 'post_class', 'ctc_add_post_classes' );

function ctc_add_post_classes( $classes ) {

	// Theme asks for this enhancement?
	if ( current_theme_supports( 'ctfw-post-classes' ) ) {

		// Has featured image?
		if ( has_post_thumbnail() ) {
			$classes[] = 'ctfw-has-image';
		} else {
			$classes[] = 'ctfw-no-image';
		}

	}

	return $classes;

}