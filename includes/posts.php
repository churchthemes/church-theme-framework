<?php
/**
 * Post Functions
 *
 * These relate to posts in general -- all types.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
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

/**
 * Get first ordered post
 *
 * Get first post according to manual order
 *
 * @since 1.0.9
 * @param string $post_type Post type to use
 * @return Array Post data
 */
function ctfw_first_ordered_post( $post_type ) {

	$post = array();

	// Get first post
	$posts = get_posts( array(
		'post_type'			=> $post_type,
		'orderby'			=> 'menu_order', // first manually ordered
		'order'				=> 'ASC',
		'numberposts'		=> 1,
		'suppress_filters'	=> false // assist multilingual
	) );

	// Get post as array
	if ( isset( $posts[0] ) ) {
		$post = (array) $posts[0];
	}

	// Return filtered
	return apply_filters( 'ctfw_first_ordered_post', $post );

}
