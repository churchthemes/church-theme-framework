<?php
/**
 * Post Functions
 *
 * Helpers shared by all post types.
 */

/**********************************
 * CONTENT OUTPUT
 **********************************/

// SOME OF THESE MIGHT NOT BE NEEDED IN THEME? Remove them
// SOME OF THESE MIGHT NOT BE NEEDED IN THEME? Remove them
// SOME OF THESE MIGHT NOT BE NEEDED IN THEME? Remove them
// SOME OF THESE MIGHT NOT BE NEEDED IN THEME? Remove them
// SOME OF THESE MIGHT NOT BE NEEDED IN THEME? Remove them
// SOME OF THESE MIGHT NOT BE NEEDED IN THEME? Remove them

/**
  * Comma-separated categories list (any taxonomy)
  *
  * Alternative to wp_list_categories()
  */
  
 // IS THIS BLOG ONLY? SHD GO IN blog.php
 // IS THIS BLOG ONLY? SHD GO IN blog.php
 // IS THIS BLOG ONLY? SHD GO IN blog.php
 // IS THIS BLOG ONLY? SHD GO IN blog.php
 // IS THIS BLOG ONLY? SHD GO IN blog.php
 // IS THIS BLOG ONLY? SHD GO IN blog.php

function ctc_blog_categories_list( $taxonomy = false, $show_count = true ) {
	
	$list = '';

	$taxonomy = empty( $taxonomy ) ? 'category' : $taxonomy;
	
	// get categories
	$categories = get_categories( array(
		'taxonomy' => $taxonomy,
		'hierarchical'	=> false
	) );
	
	// make comma separated list
	foreach( $categories as $category ) {
		
		// link
		$list .= '<a href="' . esc_attr( get_term_link( $category ) ) . '">' . $category->name . '</a>';
		
		// count
		if ( ! empty( $show_count ) ) {
			$list .= ' <span>(' . $category->count . ')</span>';
		}
	
	}
	
	return apply_filters( 'ctc_blog_categories_list', $list, $taxonomy, $show_count );
	
}


// MAYBE FOR THESE ITEMS MAKE A template-tags.php file?
// MAYBE FOR THESE ITEMS MAKE A template-tags.php file?
// MAYBE FOR THESE ITEMS MAKE A template-tags.php file?
// MAYBE FOR THESE ITEMS MAKE A template-tags.php file?
// MAYBE FOR THESE ITEMS MAKE A template-tags.php file?
// MAYBE FOR THESE ITEMS MAKE A template-tags.php file?
// MAYBE FOR THESE ITEMS MAKE A template-tags.php file?

/**
 *  X - X of X Posts
 */
 
// MAKE THIS LET SET "Item" to whatever
// MAKE THIS LET SET "Item" to whatever
// MAKE THIS LET SET "Item" to whatever
// MAKE THIS LET SET "Item" to whatever
// MAKE THIS LET SET "Item" to whatever
// MAKE THIS LET SET "Item" to whatever
// MAKE THIS LET SET "Item" to whatever

 
function ctc_post_count_message( $query = false ) {

	global $wp_query;

	$message = '';
	
	// use standard query if no custom query given
	$query = empty( $query ) && isset( $wp_query ) ? $wp_query : $query;
	
	// have correct data?
	if ( isset( $query->found_posts ) && isset( $query->query_vars['paged'] ) && isset( $query->query_vars['posts_per_page'] ) && isset( $query->max_num_pages ) ) {

		// what page are we on?
		$page = ! empty( $query->query_vars['paged'] ) ? $query->query_vars['paged'] : 1; // 0 means page 1, given numbers are literal
		$post_max = $query->query_vars['posts_per_page'] * $page; // last post on page
		$post_min = $post_max - $query->query_vars['posts_per_page'] + 1; // first post on page
		$post_max = $post_max > $query->found_posts ? $query->found_posts : $post_max; // lastly, don't let actual max shown exceed total

		// If 0 items, show "0 Items"
		if ( empty( $query->found_posts ) ) {
			$message = sprintf( __( '<b>0</b> Items', 'church-theme' ), $query->found_posts );
		}
		
		// If 1 item, show "1 Item"
		else if ( 1 == $query->found_posts ) {
			$message = __( '<b>1</b> Item', 'church-theme' );
		}
		
		// If more than 1, but one page, show "X Items"
		else if ( 1 == $query->max_num_pages ) {
			$message = sprintf( __( '<b>%s</b> Items', 'church-theme' ), $query->found_posts );
		}
		
		// If more than 1 , but multiple pages, show "X - X of X items"
		else if ( $query->max_num_pages > 1 ) {
			/* translators: first item on page, last item on page, total items on all pages */
			$message = sprintf( __( '<b>%1$s</b> &ndash; <b>%2$s</b> of <b>%3$s</b> items', 'church-theme' ), $post_min, $post_max, $query->found_posts );

		}
		
	}

	echo apply_filters( 'ctc_post_count_message', $message, $query );

}

/**
 * X Days Ago or Date
 *
 * This takes a timestamp and shows "X days ago"
 * If it is older than a certain number of days, the full date it shown
 */

function ctc_date_ago( $timestamp, $max_days = false ) {

	// the current time
	$current_time = current_time( 'timestamp' );
	
	// at what time in past would $max_days be exceeded?
	$max_seconds = $max_days * 86400; // days multiplied by number of seconds in a day
	$cutoff_time = $current_time - $max_seconds; // now minus X days ago
	
	// if cutoff time is newer than X days ago, show human time difference (X days/hours/minutes/seconds ago)
	if ( $timestamp > $cutoff_time ) {
		$time_diff = human_time_diff( $timestamp, $current_time ); // http://codex.wordpress.org/Function_Reference/human_time_diff
		/* translators: helps form 'X days ago' with 'X days' being localized by core WordPress translation */
		$date = sprintf( __( '%s ago' , 'church-theme' ), $time_diff ); // localized text for "X days ago" where "X days" is from WP core - to change days, must set core translation
	}
	
	// timestamp is older than X days ago, show full date format
	else {
		$date_format = get_option( 'date_format' ); // this is from WordPress general settings
		$date = date_i18n( $date_format, $timestamp ); // translated date
	}	

	// return formatted date
	return apply_filters( 'ctc_date_ago', $date, $timestamp, $max_days );

}

/**
 * Post Archive Nav
 * 
 * Shows previous and next links for archives
 */

function ctc_posts_nav( $query = false, $prev_text = false, $next_text = false ) {

	global $wp_query;

	$html = '';

	// use default query if no custom query given
	$query = ! empty( $query ) ? $query : $wp_query;

	// have posts on more than 1 page
	if ( $query->have_posts() && $query->max_num_pages > 1 ) {
	
		// use given or default text
		$prev_text = ! empty( $prev_text ) ? $prev_text : __( '<span>&larr;</span> Newer Items', 'church-theme' );
		$next_text = ! empty( $next_text ) ? $next_text : __( 'Older Items <span>&rarr;</span>', 'church-theme' );
	
		$html .= '<nav class="ctc-nav-left-right ctc-clearfix" id="sermon-posts-nav">';
		$html .= '	<div class="ctc-nav-left">' . get_previous_posts_link( $prev_text ) . '</div>';
		$html .= '	<div class="ctc-nav-right">' . get_next_posts_link( $next_text, $query->max_num_pages ) . '</div>';
		$html .= '</nav>';
	
	}

	echo apply_filters( 'ctc_posts_nav', $html, $query, $prev_text, $next_text );

}

/**********************************
 * COMMENTS
 **********************************/
 
/**
 * Comment List
 * 
 * comments.php can use this as callback to load a template.
 */

function ctc_comment_list( $comment, $args, $depth ) {

	global $post;

	$GLOBALS['comment'] = $comment;
	
	// Get template to use from args
	$template = isset( $args['ctc_template'] ) ? $args['ctc_template'] : CTC_PARTS_DIR . '/comment-list.php'; // default template
	$template = apply_filters( 'ctc_comment_list_template', $template, $comment, $args, $depth );

	// Load comment list template
	if ( $template_path = locate_template( $template ) ) {
		include $template_path; // do manual include so variables get passed (versus using load with locate_template)
	}

}

/**********************************
 * DATA
 **********************************/


// IF MAKE A PAGINATION INCLUDE, PUT THIS THERE
// IF MAKE A PAGINATION INCLUDE, PUT THIS THERE
// IF MAKE A PAGINATION INCLUDE, PUT THIS THERE
// IF MAKE A PAGINATION INCLUDE, PUT THIS THERE
// IF MAKE A PAGINATION INCLUDE, PUT THIS THERE
// IF MAKE A PAGINATION INCLUDE, PUT THIS THERE
// IF MAKE A PAGINATION INCLUDE, PUT THIS THERE
 
/**
 * Get/Set $paged
 * 
 * For use in templates that can be used as static front page
 * get_query_var( 'paged' ) returns nothing on front page, but get_query_var( 'page' ) does
 * This returns and sets globally $paged so that the query and pagination work
 */
 
function ctc_page_num() {

	global $paged;

	// Use paged if given; otherwise page; otherwise 1
	$paged = get_query_var( 'paged' ) ? get_query_var( 'paged' ) : ( get_query_var( 'page' ) ? get_query_var( 'page' ) : 1 );

	return apply_filteres( 'ctc_page_num', $paged );
	
}
