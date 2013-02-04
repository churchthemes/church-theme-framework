<?php
/**
 * Meta Boxes
 */

/**
 * CT Meta Boxes Setup
 *
 * The actual class is included by ctc_fw_include_files() in framework.php.
 * This is done here instead of there so ctc_theme_url() is available to use.
 */

add_filter( 'after_setup_theme', 'ctc_ctmb_setup' ); // very early
	
function ctc_ctmb_setup() {

	if ( ! defined( 'CTMB_URL' ) ) { // in case also used in plugin
		define( 'CTMB_URL', ctc_theme_url( CTC_FW_LIB_DIR . '/ct-meta-box' ) ); // for enqueing JS/CSS
	}
		
}
