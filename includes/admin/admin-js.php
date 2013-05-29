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

	// Widgets JavaScript
	// wp_enqueue_media() is run in classes/widget.php
	if ( 'widgets' == $screen->base ) { // don't enqueue unless needed

		// New media uploader in WP 3.5+
		wp_enqueue_media(); 

		// Main widgets script
		wp_enqueue_script( 'ctc-fw-admin-widgets', ctc_theme_url( CTC_FW_JS_DIR . '/admin-widgets.js' ), array( 'jquery' ), CTC_VERSION ); // bust cache on update
		wp_localize_script( 'ctc-fw-admin-widgets', 'ctc_widgets', array( // make data available
			'image_library_title'	=> _x( 'Choose Image for Widget', 'widget image library', 'church-theme-framework' ),
			'image_library_button'	=> _x( 'Use in Widget', 'widget image library', 'church-theme-framework' ),
			'incompatible_message'	=> __( 'Sorry, this widget is not made for use in this area. Please delete.', 'church-theme-framework' ),
		));

	}
	
}