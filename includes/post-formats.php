<?php
/**
 * Post Format Functions
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*********************************
 * POST FORMATS
 *********************************

/**
 * Show content after structured post format data
 *
 * By default, the_content() shows the image, video, audio, etc. after content.
 * This causes content to show after in case of large content and to put focus on the key content.
 *
 * Use add_theme_support( 'ctfw-post-format-content-after' );
 */

add_filter( 'post_format_compat', 'ctfw_post_format_content_after' );

function ctfw_post_format_content_after( $args ) {

	if ( current_theme_supports( 'ctfw-post-format-content-after' ) ) {
		$args['position'] = 'before';
	}

	return $args;

}