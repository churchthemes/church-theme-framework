<?php
/**
 * Detect Conditions
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
 * @since 0.9
 * @return bool True if has content
 */
function ctfw_has_content() {

	$has_content = false;

	if ( trim( strip_tags( get_the_content() ) ) ) {
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
