<?php
/**
 * Post Functions
 *
 * These relate to posts in general -- all types.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Add useful post classes
 *
 * @since 0.9
 * @param array $classes Classes currently being added to <body>
 * @return array Modified array of classes
 */
function ctfw_add_post_classes( $classes ) {

	// Theme asks for this enhancement?
	if ( current_theme_supports( 'ctfw-post-classes' ) ) {

		// Has featured image?
		if ( has_post_thumbnail() ) {
			$classes[] = 'ctfw-has-image';
		} else {
			$classes[] = 'ctfw-no-image';
		}

	}

	return $classes;

}

add_filter( 'post_class', 'ctfw_add_post_classes' );
