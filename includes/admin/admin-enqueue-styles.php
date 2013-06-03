<?php
/**
 * Framework Admin Styles
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * ENQUEUE STYLESHEETS
 *******************************************/

/**
 * Enqueue admin stylesheets
 *
 * Note: ct-meta-boxes handles its own stylesheet.
 */

add_action( 'admin_enqueue_scripts', 'ctfw_admin_enqueue_styles' );

function ctfw_admin_enqueue_styles() {

	$screen = get_current_screen();

	// Admin widgets
	if ( 'widgets' == $screen->base && current_theme_supports( 'ctfw-sidebar-widget-restrictions' ) ) {
		wp_enqueue_style( 'ctfw-widgets', ctfw_theme_url( CTFW_CSS_DIR . '/admin-widgets.css' ), false, CTFW_THEME_VERSION ); // bust cache on update
	}

	// Theme license
	if ( 'appearance_page_theme-license' == $screen->base ) {
		wp_enqueue_style( 'ctfw-license', ctfw_theme_url( CTFW_CSS_DIR . '/admin-license.css' ), false, CTFW_THEME_VERSION ); // bust cache on update
	}
	
}
