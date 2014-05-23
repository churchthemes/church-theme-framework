<?php
/**
 * Framework Admin Styles
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * ENQUEUE STYLESHEETS
 *******************************************/

/**
 * Enqueue admin stylesheets
 *
 * Note: CT Meta Box and other framework components handle their own stylesheets.
 *
 * @since 0.9
 */
function ctfw_admin_enqueue_styles() {

	$screen = get_current_screen();

	// Admin widgets
	// Framework also enqueues this for Customizer in framework/includes/customize.php
	if ( 'widgets' == $screen->base ) {
		wp_enqueue_style( 'ctfw-widgets', ctfw_theme_url( CTFW_CSS_DIR . '/admin-widgets.css' ), false, CTFW_THEME_VERSION ); // bust cache on update
	}

	// Theme license
	if ( 'appearance_page_theme-license' == $screen->base ) {
		wp_enqueue_style( 'ctfw-license', ctfw_theme_url( CTFW_CSS_DIR . '/admin-license.css' ), false, CTFW_THEME_VERSION ); // bust cache on update
	}

}

add_action( 'admin_enqueue_scripts', 'ctfw_admin_enqueue_styles' );
