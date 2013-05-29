<?php
/**
 * Background Functions
 */

/*********************************************
 * CUSTOM BACKGROUND
 *********************************************/

/**
 * Remove Custom Background from Admin Menu
 * 
 * Use add_theme_support( 'ctfw-force-customizer-background' ) to force users to edit
 * the custom background via the Customiser.
 */
 
add_action( 'admin_menu', 'ctc_admin_remove_menu_pages', 11 ); // after add theme support for background

function ctc_admin_remove_menu_pages() {

	global $menu;

	// If theme supports this
	if ( current_theme_supports( 'ctfw-force-customizer-background' ) ) {

		// Remove Background
		// Encourage access by Theme Customizer since it has Fullscreen and Preset enhancements
		remove_submenu_page( 'themes.php', 'custom-background' );

	}

}

/**
 * Redirect Custom Background to Theme Customizer
 *
 * Use add_theme_support( 'ctfw-force-customizer-background' ) to force users to edit
 * the custom background via the Customiser.
 */

add_action( 'admin_init', 'ctc_admin_redirect_background' );
	
function ctc_admin_redirect_background() {

	// If theme supports this
	if ( current_theme_supports( 'ctfw-force-customizer-background' ) ) {

		// We're on custom background page
		if ( 'themes.php?page=custom-background' == basename( $_SERVER['REQUEST_URI'] ) ) {

			// Redirect to Theme Customizer
			wp_redirect( admin_url( 'customize.php' ) );
			exit;
		
		}

	}

}