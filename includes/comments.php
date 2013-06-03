<?php
 
/**
 * Comment Callback
 * 
 * comments.php can use this as wp_list_comments callback to load comment.php for rendering each comment.
 */

function ctfw_comment( $comment, $args, $depth ) {

	$GLOBALS['comment'] = $comment;
	
	// Get template to use from args
	$template = isset( $args['ctfw_template'] ) ? $args['ctfw_template'] : 'comment.php'; // default template when ctfw_template argument not passed
	$template = apply_filters( 'ctfw_comment_template', $template, $comment, $args, $depth );

	// Load comment template for each
	if ( $template_path = locate_template( $template ) ) {
		include $template_path; // do manual include so variables get passed (versus using load with locate_template)
	}

}

/**
 * Attachment inherit discussion status
 * 
 * add_theme_support( 'ctfw-attachment-inherit-discussion' ) will cause all attachments to use the comment
 * and ping settings from the parent item. If file is not attached to a post/page, discussions will be turned off.
 */

add_filter( 'comments_open', 'ctfw_attachment_inherit_comment_status', 10 , 2 );

function ctfw_attachment_inherit_comment_status( $open, $post_id ) {

	return ctfw_attachment_inherit_discussion_status( 'comment', $open, $post_id );

}

add_filter( 'pings_open', 'ctfw_attachment_inherit_ping_status', 10 , 2 );

function ctfw_attachment_inherit_ping_status( $open, $post_id ) {

	return ctfw_attachment_inherit_discussion_status( 'ping', $open, $post_id );

}

function ctfw_attachment_inherit_discussion_status( $type, $open, $post_id ) {

	// Theme supports this
	if ( current_theme_supports( 'ctfw-attachment-inherit-discussion' ) ) {

		// Affect attachments only
		$post = get_post( $post_id );
		if ( 'attachment' == $post->post_type ) {

			// Has parent? Use its status
			if ( ! empty( $post->post_parent ) ) { // not 0

				$parent_post = get_post( $post->post_parent );

				$key = $type . '_status'; // comment_status or ping_status
				$open = 'open' == $parent_post->$key ? true : false;

			}

			// No parent - comments off
			else {
				$open = false;
			}

		}

	}

	// Return changed or original status
	return $open;

}

/**
 * Shorten comment author
 * 
 * Useful for keeping long trackback titles in check.
 *
 * Use add_theme_support( 'ctfw-shorten-comment-author', 50 );
 */

add_filter( 'get_comment_author', 'resurrect_shorten_comment_author' );

function resurrect_shorten_comment_author( $author ) {

	// Theme uses this feature
	if ( $support = get_theme_support( 'ctfw-shorten-comment-author' ) ) { // returns false if feature not supported

		// Get character limit
		$characters = isset( $support[0] ) ? $support[0] : 50; // default

		return ctfw_shorten( $author, $characters );

	}

}
