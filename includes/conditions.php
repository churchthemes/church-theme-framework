<?php
/**
 * Detect Conditions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2019, ChurchThemes.com, LLC
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * CONTENT
 *******************************************/

/**
 * Is multipage
 *
 * @since 0.9
 * @global bool $multipage
 * @return bool True if current post has multiple pages
 */
function ctfw_is_multipage() {

	global $multipage;

	$is_multipage = false;

	if ( ! empty( $multipage ) ) {
		$is_multipage = true;
	}

	return apply_filters( 'ctfw_is_multipage', $is_multipage );

}

/**
 * Has <!--more--> quicktag
 *
 * @since 0.9
 * @global object $post
 * @return bool True if uses more tag
 */
function ctfw_has_more_tag() {

	global $post;

	$has_more_tag = false;

	if ( preg_match( '/<!--more(.*?)?-->/', $post->post_content ) ) {
		$has_more_tag = true;
	}

	return apply_filters( 'ctfw_has_more_tag', $has_more_tag );

}

/**
 * Has title
 *
 * @since 0.9
 * @return bool True if has title
 */
function ctfw_has_title() {

	$has_title = false;

	if ( trim( strip_tags( get_the_title() ) ) ) {
		$has_title = true;
	}

	return apply_filters( 'ctfw_has_title', $has_title );

}

/**
 * Has content
 *
 * It strips tags because sometimes content-less tags are left behind (breaks).
 * This is useful for not outputting content wrapping tags when there is no content.
 * Content tags like img and iframe are preserved so user can add image content
 * with no text content and still have this return true.
 *
 * @since 0.9
 * @return bool True if has content
 */
function ctfw_has_content() {

	$has_content = false;

	// Check for content and allow certain tags if no content.
	// This way, for example, if has just an image tag, the content still renders.
	$content = trim( get_the_content() );
	if ( strip_tags( $content, '<img><iframe><script><embed><audio><video>' ) || preg_match( '/wp\:block/', $content ) ) {
		$has_content = true;
	}

	// Check for page buiders like Elementor and Beaver Builder.
	if ( ctfw_using_builder_plugin() ) {
		$has_content = true;
	}

	return apply_filters( 'ctfw_has_content', $has_content );

}

/**
 * Has excerpt
 *
 * @since 0.9
 * @return bool True if has manual or automatic excerpt
 */
function ctfw_has_excerpt() {

	$has_excerpt = false;

	if ( trim( strip_tags( get_the_excerpt() ) ) ) {
		$has_excerpt = true;
	}

	return apply_filters( 'ctfw_has_excerpt', $has_excerpt );

}

/**
 * Has manual excerpt
 *
 * @since 0.9
 * @global object $post
 * @return bool True if has manual excerpt
 */
function ctfw_has_manual_excerpt() {

	global $post;

	$bool = false;

	if ( trim( strip_tags( $post->post_excerpt ) ) ) {
		$bool = true;
	}

	return apply_filters( 'ctfw_has_manual_excerpt', $bool );

}

/**
 * Has excerpt or more tag
 *
 * @since 1.7.1
 * @global object $post
 * @return bool True if has manual excerpt
 */
function ctfw_has_excerpt_or_more() {

	$bool = false;

	if ( ctfw_has_excerpt() || ctfw_has_more_tag() ) {
		$bool = true;
	}

	return apply_filters( 'ctfw_has_excerpt_or_more', $bool );

}

/**
 * Check if theme's "loop after content" used
 *
 * This tells if a loop is being output on a  regular page, such as via page templates like Sermons, People, etc.
 * It helps ctfw_has_loop_multiple() below.
 *
 * Usage follows. Second argument is function to call for the check.
 *
 * add_theme_support( 'ctfw-loop-after-content-used', 'saved_loop_after_content_used' );
 *
 * @since 2.1.1
 * @return bool True if given function is true
 */
function ctfw_loop_after_content_used() {

	$result = false;

	// Check theme support and function to call
	$support = get_theme_support( 'ctfw-loop-after-content-used' );

	// Function given in theme support?
	if ( ! empty( $support[0] ) ) {

		// Get function
		$function = $support[0];

		// Run function if exists
		if ( function_exists( $function ) ) {
			$result = call_user_func( $function );
		}

	}

	return apply_filters( 'ctfw_loop_after_content_used', $result );

}

/**
 * Has loop for multiple entries
 *
 * This page is looping multiple entries
 *
 * @since 1.9.2
 * @return bool true if looping multiple entries
 */
function ctfw_has_loop_multiple() {

	$showing = false;

	// Loop being output on regular page, such as via page templates like Sermons, People, etc.
	// Requires 'ctfw-loop-after-content-used' via add_theme_support()
	if ( ctfw_loop_after_content_used() ) {
		$showing = true;
	}

	// Archives like Sermon Topics, People Groups, etc.
	// Also covers post type archives if a page with archive template isn't setup yet
	elseif ( is_archive() ) {
		$showing = true;
	}

	// Blog requires special handling with regard to Settings > Reading
	elseif ( is_home() ) { // is_home() returns blog page
		$showing = true;
	}

	// Search shows short entries
	elseif ( is_search() ) {
		$showing = true;
	}

	// Return filterable
	return apply_filters( 'ctfw_has_loop_multiple', $showing );

}

/**
 * Is page template used
 *
 * An shorter way to determine if page is using a template
 *
 * Usage: ctfw_is_page_template( 'homepage' ) // homepage.php in template directory
 *
 * @since  1.9.3
 * @param  $name Filename with or without .php and with or without path
 * @return bool True if current page is using that template
 */
function ctfw_is_page_template( $name ) {

	// Remove path and .php
	$name = basename( $name, '.php' );

	// Check it
	$result = is_page_template( CTFW_THEME_PAGE_TPL_DIR . '/' . $name . '.php' ) ? true : false;

	// Return filtered
	return apply_filters( 'ctfw_is_page_template', $result, $name );

}

/**
 * Using page builder plugin?
 *
 * This is useful for assisting ctfw_has_content() so that the_content() can be run.
 *
 * @since 2.6.3
 * @global object $post
 * @return bool True if so
 */
function ctfw_using_builder_plugin() {

  global $post;

	$result = false;

	// Elementor.
	if ( did_action( 'elementor/loaded' ) && isset( $post->ID ) && \Elementor\Plugin::$instance->db->is_built_with_elementor( $post->ID ) ) {
		$result = true;
	}

	// Beaver Builder.
	elseif ( method_exists( 'FLBuilderModel', 'is_builder_enabled' ) && FLBuilderModel::is_builder_enabled() ) {
		$result = true;
	}

	return apply_filters( 'ctfw_using_builder_plugin', $result );

}

/*******************************************
 * WIDGETS
 *******************************************/

/**
 * Determine if inside a particular sidebar / widget area
 *
 * This uses global set by ctfw_set_current_sidebar_id() in sidebars.php.
 *
 * @since 2.0
 * @param string $sidebar_id Sidebar ID / widget area
 * @return bool True if so
 */
function ctfw_is_sidebar( $sidebar_id ) {

	global $ctfw_current_sidebar_id;

	$is = false;

	if ( isset( $ctfw_current_sidebar_id ) && $sidebar_id == $ctfw_current_sidebar_id ) {
		$is = true;
	}

	return apply_filters( 'ctfw_is_sidebar', $is, $sidebar_id );

}

/**
 * Determine if is first widget in sidebar
 *
 * This uses global set by ctfw_increment_widget_position() in widgets.php.
 *
 * @since 2.0
 * @return bool True if so
 */
function ctfw_is_first_widget() {

	global $ctfw_current_widget_position;

	$is_first = false;

	if ( isset( $ctfw_current_widget_position ) && 1 == $ctfw_current_widget_position ) {
		$is_first = true;
	}

	return apply_filters( 'ctfw_is_first_widget', $is_first );

}

/*******************************************
 * USERS
 *******************************************/

/**
 * User can edit post
 *
 * @since 0.9
 * @global object $post
 * @return bool True if user can edit post
 */
function ctfw_can_edit_post() {

	global $post;

	$can_edit = false;

	if ( ! empty( $post ) ) {

		$post_type_object = get_post_type_object( $post->post_type );

		if ( ! empty( $post_type_object ) ) {

			if ( current_user_can( $post_type_object->cap->edit_post, $post->ID ) ) {
				$can_edit = true;
			}

		}

	}

	return apply_filters( 'ctfw_can_edit_post', $can_edit );

}

/*******************************************
 * OTHER
 *******************************************/

/**
 * Is this the posts page?
 *
 * If a static front page is used and the "Posts page" is set, this is true.
 *
 * @since 0.9
 * @return bool True if is "Posts page"
 */
function ctfw_is_posts_page() {

	$bool = false;

	if ( is_home() && ! is_front_page() ) {
		$bool = true;
	}

	return apply_filters( 'ctfw_is_posts_page', $bool );

}
