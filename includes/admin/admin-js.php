<?php
/**
 * Admin JavaScript
 */

/*******************************************
 * ENQUEUE JAVASCRIPT
 *******************************************/
 
add_action( 'admin_enqueue_scripts', 'ctc_fw_admin_enqueue_scripts' ); // admin-end only
 
function ctc_fw_admin_enqueue_scripts() {

	$screen = get_current_screen();

	// Themes JavaScript
	if ( 'themes' == $screen->base ) { // don't enqueue unless needed
		wp_enqueue_script( 'ctc-fw-admin-themes', ctc_theme_url( CTC_FW_JS_DIR . '/admin-themes.js' ), false, CTC_VERSION ); // bust cache on update		
	}
	
}
