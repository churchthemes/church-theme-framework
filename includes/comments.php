<?php
 
/**
 * Comment List
 * 
 * comments.php can use this as callback to load a template.
 */

function ctc_comment_list( $comment, $args, $depth ) {

	global $post;

	$GLOBALS['comment'] = $comment;
	
	// Get template to use from args
	$template = isset( $args['ctc_template'] ) ? $args['ctc_template'] : 'comment-list.php'; // default template
	$template = apply_filters( 'ctc_comment_list_template', $template, $comment, $args, $depth );

	// Load comment list template
	if ( $template_path = locate_template( $template ) ) {
		include $template_path; // do manual include so variables get passed (versus using load with locate_template)
	}

}