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
