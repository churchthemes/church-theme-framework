<?php
/**
 * Template Functions
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
 * Get content template
 *
 * Loads content-*.php according to post type and post format. Some examples:
 *
 * content-post.php 			Standard blog post
 * content-post-audio.php		Blog post using audio post format
 * content-audio.php 			Same as above (but be careful, an 'audio' post type may use this)
 * content-post-type.php 		Custom post type 'ccm_post_type' made friendly
 * content-ccm_post_type.php  	Same as above but using actual name
 * content-attachment.php 		Media attachment
 *
 * This is based heavily on Justin Tadlock's hybrid_base_get_content_template() function:
 * https://github.com/justintadlock/hybrid-base/blob/master/functions.php
 *
 * @since 0.9
 * @return string Template file name if template loaded
 */
function ctfw_get_content_template() {

	// Templates will be attempted to be loaded in the order they are added to this array
	$templates = array();

	// Get post type
	$post_type = get_post_type();
	$post_type_friendly = ctfw_make_friendly( $post_type ); // "ccm_post_type" is made into "post-type" for friendlier template naming

	// Get post format
	$post_format = get_post_format();

	// Does post type support post formats?
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		// First check for something like content-post-audio.php (blog post using audio post format)
		$templates[] = "content-{$post_type}-{$post_format}.php";

		// If that doesn't exist, check simply for content-audio.php (shorter but may conflict with post type name)
		$templates[] = "content-{$post_format}.php";

	}

	// If no post format, load content-post-type.php, where "post-type" is a friendly version of "ccm_post_type"
	if ( $post_type_friendly != $post_type ) {
		$templates[] = "content-{$post_type_friendly}.php";
	}

	// If no friendly post type template, load content-ccm_post_type.php, using the actual post type name
	$templates[] = "content-{$post_type}.php";

	// If all else fails, use the plain vanilla template
	$templates[] = 'content.php';

	// Load template and return filename if succeeded
	return locate_template( $templates, true, false );

}
