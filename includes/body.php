<?php
/**
 * <body> Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2014, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.1.2
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * BODY CLASSES
 *******************************************/

/**
 * Add various helper classes to <body>
 *
 * Enable with add_theme_support( 'ctfw-body-classes' );
 *
 * IMPORTANT: Do not do client detection (mobile, browser, etc.) here.
 * Instead, do in theme's JS so works with caching plugins.
 *
 * @since 1.1.2
 * @param array $classes Classes currently being added to body tag
 * @return array Modified classes
 */
function ctfw_add_body_classes( $classes ) {

	// Theme supports body helper classes?
	if ( current_theme_supports( 'ctfw-body-classes' ) ) {

		// Currently none

	}

	return $classes;

}

add_filter( 'body_class', 'ctfw_add_body_classes' );
