<?php
/**
 * Post Format Functions
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
 *
 * @since 0.9
 * @param array $args post_format_compat arguments
 * @return array Modified arguments
 */
function ctfw_post_format_content_after( $args ) {

	if ( current_theme_supports( 'ctfw-post-format-content-after' ) ) {
		$args['position'] = 'before';
	}

	return $args;

}

add_filter( 'post_format_compat', 'ctfw_post_format_content_after' );
