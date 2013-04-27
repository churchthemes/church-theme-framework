<?php
 
/**
 * Comment Callback
 * 
 * comments.php can use this as wp_list_comments callback to load comment.php for rendering each comment.
 */

function ctc_comment( $comment, $args, $depth ) {

	$GLOBALS['comment'] = $comment;
	
	// Get template to use from args
	$template = isset( $args['ctc_template'] ) ? $args['ctc_template'] : 'comment.php'; // default template when ctc_template argument not passed
	$template = apply_filters( 'ctc_comment_template', $template, $comment, $args, $depth );

	// Load comment template for each
	if ( $template_path = locate_template( $template ) ) {
		include $template_path; // do manual include so variables get passed (versus using load with locate_template)
	}

}

/**
 * Attachment inherit discussion status
 * 
 * add_theme_support( 'ctc-attachment-inherit-discussion' ) will cause all attachments to use the comment
 * and ping settings from the parent item. If file is not attached to a post/page, discussions will be turned off.
 */

add_filter( 'comments_open', 'ctc_attachment_inherit_comment_status', 10 , 2 );

function ctc_attachment_inherit_comment_status( $open, $post_id ) {

	return ctc_attachment_inherit_discussion_status( 'comment', $open, $post_id );

}

add_filter( 'pings_open', 'ctc_attachment_inherit_ping_status', 10 , 2 );

function ctc_attachment_inherit_ping_status( $open, $post_id ) {

	return ctc_attachment_inherit_discussion_status( 'ping', $open, $post_id );

}

function ctc_attachment_inherit_discussion_status( $type, $open, $post_id ) {

	// Theme supports this
	if ( current_theme_supports( 'ctc-attachment-inherit-discussion' ) ) {

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
