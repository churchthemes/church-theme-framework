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

	if ( ! empty( $post->post_content ) && preg_match( '/<!--more-->/', $post->post_content ) ) {
		$has_more_tag = true;
	}

	return apply_filters( 'ctc_has_more_tag', $has_more_tag );

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
