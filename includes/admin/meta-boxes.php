<?php
/**
 * Meta Boxes
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.0
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
