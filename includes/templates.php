<?php
/**
 * Template Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2015, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Get content template
 *
 * Loads content-*.php according to post type, post format and whether or not is singular (full) or not (short).
 * Templates will be loaded from partials directory first (if exist); otherwise from root
 *
 * The order in which templates existence is checked is from most specific to least specific.
 * For example, content-post-short.php is checked for before content-post.php and content-post-audio-short.php before content-post-audio.php.
 *
 * These examples are NOT listed in order of priority (read above):
 *
 * content-post.php 				Standard blog post - is_singular or not
 * content-post-full.php 			Standard blog post - is_singular
 * content-post-short.php 			Standard blog post - not is_singular
 * content-post-audio.php			Blog post using audio post format - is_singular or not
 * content-post-audio-full.php		Blog post using audio post format - is_singular
 * content-post-audio-short.php		Blog post using audio post format - not is_singular
 * content-audio.php 				Same as above (but be careful, an 'audio' post type may use this) - is_singular or not
 * content-audio-full.php 			Same as above (but be careful, an 'audio' post type may use this) - is_singular
 * content-audio-short.php 			Same as above (but be careful, an 'audio' post type may use this) - not is_singular
 * content-post-type.php 			Custom post type 'ctc_post_type' made friendly - is_singular or not
 * content-post-type-full.php 		Custom post type 'ctc_post_type' made friendly - is_singular
 * content-post-type-short.php 		Custom post type 'ctc_post_type' made friendly - not is_singular
 * content-ctc_post_type.php  		Same as above but using actual name - is_singular or not
 * content-ctc_post_type-full.php  Same as above but using actual name - is_singular
 * content-ctc_post_type-short.php  Same as above but using actual name - not is_singular
 * content-attachment.php 			Media attachment
 *
 * Now here's an example of how prioerity loading works. *-single and *-full are available depending on
 * whether or not is_singular is or is not true. This example is for when is_singular is not true. If it
 * were true, -full would be checked for instead -short.
 *
 * partials/content-sermon-short.php (or *-full if is_singular)
 * partials/content-sermon.php
 * partials/content-ctc_sermon-short.php
 * partials/content-ctc_sermon.php
 * partials/content-short.php
 * partials/content.php
 * content-sermon-short.php
 * content-sermon.php
 * content-ctc_sermon-short.php
 * content-ctc_sermon.php
 * content-short.php
 * content.php
 *
 * One strategy is to use content-sermon.php for both single (full) and archive (short)
 * by checking is_singular( get_post_type() ) in the partial, which may save on redundant code.
 * The pro of separate partials is better organization and easier child theme overrides.
 *
 * This is based on Justin Tadlock's Hybrid Base 0.1 hybrid_base_get_content_template()
 * function: https://github.com/justintadlock/hybrid-base/blob/0.1/functions.php#L154
 *
 * @since 0.9
 * @return string Template file name if template loaded
 */
function ctfw_get_content_template() {

	// Templates will be attempted to be loaded in the order they are added to this array
	$templates = array();

	// Get post type
	$post_type = get_post_type();
	$post_type_friendly = ctfw_make_friendly( $post_type ); // "ctc_post_type" is made into "post-type" for friendlier template naming

	// Singular post?
	$singular = is_singular( $post_type ) ? true : false;

	// Does post type support post formats?
	if ( post_type_supports( $post_type, 'post-formats' ) ) {

		// Get post format
		$post_format = get_post_format();

		// Has post format
		if ( $post_format ) {

			// First check for something like content-post-audio.php (blog post using audio post format)
			if ( $singular ) $templates[] = "content-{$post_type}-{$post_format}-full";
			if ( ! $singular ) $templates[] = "content-{$post_type}-{$post_format}-short";
			$templates[] = "content-{$post_type}-{$post_format}";

			// If that doesn't exist, check simply for content-audio.php (shorter but may conflict with post type name)
			if ( $singular ) $templates[] = "content-{$post_format}-full";
			if ( ! $singular ) $templates[] = "content-{$post_format}-short";
			$templates[] = "content-{$post_format}";

		}

	}

	// If no post format, load content-post-type.php, where "post-type" is a friendly version of "ctc_post_type"
	if ( $post_type_friendly != $post_type ) {
		if ( $singular ) $templates[] = "content-{$post_type_friendly}-full";
		if ( ! $singular ) $templates[] = "content-{$post_type_friendly}-short";
		$templates[] = "content-{$post_type_friendly}";
	}

	// If no friendly post type template, load content-ctc_post_type.php, using the actual post type name
	if ( $singular ) $templates[] = "content-{$post_type}-full";
	if ( ! $singular ) $templates[] = "content-{$post_type}-short";
	$templates[] = "content-{$post_type}";

	// If all else fails, use the plain vanilla template
	if ( $singular ) $templates[] = 'content-full';
	if ( ! $singular ) $templates[] = 'content-short';
	$templates[] = 'content';

	// Append .php extension
	foreach ( $templates as $template_key => $template ) {
		$templates[$template_key] = $template . '.php';
	}

	// Check in partials directory first
	$templates_partials = array();
	foreach ( $templates as $template ) {
		$templates_partials[] = CTFW_THEME_PARTIAL_DIR . '/' . $template;
	}
	$templates = array_merge( $templates_partials, $templates );

	// Load template and return filename if succeeded
	return locate_template( $templates, true, false );

}
