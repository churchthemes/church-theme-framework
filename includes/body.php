<?php
/**
 * <body> Functions
 */ 

/*******************************************
 * BODY CLASSES
 *******************************************/

/**
 * Add helper classes to <body>
 */
 
add_filter( 'body_class', 'ctc_fw_add_body_classes' );

function ctc_fw_add_body_classes( $classes ) {

	// Check for theme support
	if ( current_theme_supports( 'ctc-body-classes' ) ) {

		// Front page showing posts or static page
		if ( is_front_page() && get_option( 'show_on_front' ) ) {
			$classes[] = 'ctc-show-on-front-' . get_option( 'show_on_front' );
		}

	}

	return $classes;

}
