<?php
/**
 * Theme Options
 */

/**
 * CT Options Setup
 *
 * The actual class is included by ctc_fw_include_files() in framework.php.
 * This is done here instead of there so ctc_theme_url() is available to use.
 */

add_filter( 'after_setup_theme', 'ctc_ctoptions_setup' ); // very early
	
function ctc_ctoptions_setup() {

	if ( ! defined( 'CTOPTIONS_URL' ) ) { // in case also used in plugin
		define( 'CTOPTIONS_URL', ctc_theme_url( CTC_FW_LIB_DIR . '/ct-options' ) ); // for enqueing JS/CSS
	}
		
}
 
/**
 * Get Option Value
 *
 * This is a wrapper for the CT_Options getter for more convenient use in templates, etc.
 */
 
// MAKE THIS ALSO UTILIZE get_customization() TO USE ONE FUNCTION (if ever change field from one to other, don't have to change function)
// MAKE THIS ALSO UTILIZE get_customization() TO USE ONE FUNCTION (if ever change field from one to other, don't have to change function)
// MAKE THIS ALSO UTILIZE get_customization() TO USE ONE FUNCTION (if ever change field from one to other, don't have to change function)
// MAKE THIS ALSO UTILIZE get_customization() TO USE ONE FUNCTION (if ever change field from one to other, don't have to change function)
// MAKE THIS ALSO UTILIZE get_customization() TO USE ONE FUNCTION (if ever change field from one to other, don't have to change function)
// MAKE THIS ALSO UTILIZE get_customization() TO USE ONE FUNCTION (if ever change field from one to other, don't have to change function) 
 
function ctc_option( $option ) {

	global $ctc_options;

	// Get option value
	$value = $ctc_options->get( $option ); // this handles defaults
	
	// Return filterable value
	return apply_filters( 'ctc_option', $value );
	
}
