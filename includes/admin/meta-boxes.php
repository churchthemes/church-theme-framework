<?php
/**
 * Meta Boxes
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013 - 2018, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * CT Meta Box setup
 *
 * The actual class is included by ctfw_include_files() in framework.php.
 * This is done here instead of there so get_theme_file_uri() is available to use.
 *
 * @since 0.9
 */
function ctfw_ctmb_setup() {

	if ( ! defined( 'CTMB_URL' ) ) { // in case also used in plugin
		define( 'CTMB_URL', get_theme_file_uri( CTFW_LIB_DIR . '/ct-meta-box' ) ); // for enqueing JS/CSS
	}

}

add_filter( 'after_setup_theme', 'ctfw_ctmb_setup' ); // very early
