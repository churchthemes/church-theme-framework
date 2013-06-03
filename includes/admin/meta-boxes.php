<?php
/**
 * Meta Boxes
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * CT Meta Boxes Setup
 *
 * The actual class is included by ctfw_include_files() in framework.php.
 * This is done here instead of there so ctfw_theme_url() is available to use.
 */

add_filter( 'after_setup_theme', 'ctfw_ctmb_setup' ); // very early
	
function ctfw_ctmb_setup() {

	if ( ! defined( 'CTMB_URL' ) ) { // in case also used in plugin
		define( 'CTMB_URL', ctfw_theme_url( CTFW_LIB_DIR . '/ct-meta-box' ) ); // for enqueing JS/CSS
	}
		
}
