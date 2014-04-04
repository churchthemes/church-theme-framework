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
 * @since 1.1.2
 * @param array $classes Classes currently being added to body tag
 * @return array Modified classes
 */
function ctfw_add_body_classes( $classes ) {

	// Theme supports body helper classes?
	if ( current_theme_supports( 'ctfw-body-classes' ) ) {

		// Mobile Detection
		if ( wp_is_mobile() ) { // from WordPress core
			$classes[] = 'ctfw-is-mobile';
		} else {
			$classes[] = 'ctfw-not-mobile';
		}

		// iOS Detection
		// Especially useful for re-styling form submit buttons
		if ( wp_is_mobile() && preg_match( '/iPad|iPod|iPhone/', $_SERVER['HTTP_USER_AGENT'] ) ) { // from WordPress core
			$classes[] = 'ctfw-is-ios';
		} else {
			$classes[] = 'ctfw-not-ios';
		}

	}

	return $classes;

}
 
add_filter( 'body_class', 'ctfw_add_body_classes' );
