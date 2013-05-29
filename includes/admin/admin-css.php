<?php
/**
 * Framework Admin Styles
 */

/*******************************************
 * ENQUEUE STYLESHEETS
 *******************************************/

/**
 * Enqueue admin stylesheets
 *
 * Note: ct-options, ct-meta-boxes handle their own stylesheets.
 */

add_action( 'admin_enqueue_scripts', 'ctfw_admin_enqueue_styles' );

function ctfw_admin_enqueue_styles() {

	$screen = get_current_screen();

	// Admin widgets
	if ( 'widgets' == $screen->base ) {
		wp_enqueue_style( 'ctc-widgets', ctc_theme_url( CTFW_CSS_DIR . '/admin-widgets.css' ), false, CTFW_THEME_VERSION ); // bust cache on update
	}

	// Theme license
	if ( 'appearance_page_theme-license' == $screen->base ) {
		wp_enqueue_style( 'ctc-license', ctc_theme_url( CTFW_CSS_DIR . '/admin-license.css' ), false, CTFW_THEME_VERSION ); // bust cache on update
	}
	
}

/*******************************************
 * BODY CLASSES
 *******************************************/

/**
 * Add helper classes to admin <body> for easier style tweaks (such as hiding "Preview" button per post type)
 */

add_filter( 'admin_body_class', 'ctfw_admin_body_classes' );
 
function ctfw_admin_body_classes( $classes ) {

	// Add useful get_current_screen() values
	$screen = get_current_screen();
	$screen_keys = array( 'action', 'base', 'id', 'post_type', 'taxonomy' );
	foreach( $screen_keys as $screen_key ) {
		if ( ! empty( $screen->$screen_key ) ) {
			$classes .= 'ctc-screen-' . $screen_key . '-' . $screen->$screen_key . ' '; // space at end to prevent run-together
		}
	}
	
	return $classes;
	
}
