<?php
/**
 * Detect Conditions
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * CONTENT
 *******************************************/

/**
 * Is multipage
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
 * Has Title
 */

function ctfw_has_title() {

	$has_title = false;

	if ( trim( strip_tags( get_the_title() ) ) ) {
		$has_title = true;
	}

	return apply_filters( 'ctfw_has_title', $has_title );

}

/**
 * Has Content
 */

function ctfw_has_content() {

	$has_content = false;

	if ( trim( strip_tags( get_the_content() ) ) ) {
		$has_content = true;
	}

	return apply_filters( 'ctfw_has_content', $has_content );

}

/**
 * Has Excerpt
 *
 * True if has manual or automatic excerpt
 */

function ctfw_has_excerpt() {

	$has_excerpt = false;

	if ( trim( strip_tags( get_the_excerpt() ) ) ) {
		$has_excerpt = true;
	}

	return apply_filters( 'ctfw_has_excerpt', $has_excerpt );

}

/*******************************************
 * USERS
 *******************************************/

/**
 * User can edit post
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
 */

function ctfw_is_posts_page() {

	$bool = false;

	if ( is_home() && ! is_front_page() ) {
		$bool = true;
	}

	return apply_filters( 'ctfw_is_posts_page', $bool );

}
