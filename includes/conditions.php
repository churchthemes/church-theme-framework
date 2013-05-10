<?php
/**
 * Detect Conditions
 */

/*******************************************
 * CONTENT
 *******************************************/

/**
 * Is multipage
 */

function ctc_is_multipage() {

	global $multipage;

	$is_multipage = false;

	if ( ! empty( $multipage ) ) {
		$is_multipage = true;
	}

	return apply_filters( 'ctc_is_multipage', $is_multipage );

}

/**
 * Has <!--more--> quicktag
 */

function ctc_has_more_tag() {

	global $post;

	$has_more_tag = false;

	if ( preg_match( '/<!--more(.*?)?-->/', $post->post_content ) ) {
		$has_more_tag = true;
	}

	return apply_filters( 'ctc_has_more_tag', $has_more_tag );

}

/**
 * Has Title
 */

function ctc_has_title() {

	$has_title = false;

	if ( trim( strip_tags( get_the_title() ) ) ) {
		$has_title = true;
	}

	return apply_filters( 'ctc_has_title', $has_title );

}

/**
 * Has Content
 */

function ctc_has_content() {

	$has_content = false;

	if ( trim( strip_tags( get_the_content() ) ) ) {
		$has_content = true;
	}

	return apply_filters( 'ctc_has_content', $has_content );

}

/**
 * Has Excerpt
 */

function ctc_has_excerpt() {

	$has_excerpt = false;

	if ( trim( strip_tags( get_the_excerpt() ) ) ) {
		$has_excerpt = true;
	}

	return apply_filters( 'ctc_has_excerpt', $has_excerpt );

}

/*******************************************
 * USERS
 *******************************************/

/**
 * User can edit post
 */

function ctc_can_edit_post() {

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

	return apply_filters( 'ctc_can_edit_post', $can_edit );

}
