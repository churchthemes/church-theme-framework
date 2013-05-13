<?php
/**
 * <head> Functions
 *
 * Functions that add to <head>. Also see frontend-css.php for adding styles.
 */

/*******************************************
 * HEAD
 *******************************************/

/**
 * Add text to <title>
 *
 * An SEO plugin can be used to fine-tune the <title> for various areas of the site.
 */
 
add_filter( 'wp_title', 'ctc_fw_head_title', 10, 3 );

function ctc_fw_head_title( $title, $sep, $seplocation ) {

	// Homepage (site name - tagline )
	if ( is_front_page() ) {
		$before = get_bloginfo( 'name' );
		$after = get_bloginfo( 'description' );
	}
	
	// Subpage (page title - site name)
	else {
		$before = $title;
		$after = get_bloginfo( 'name' );
	}
	
	// Build title
	$before = trim( $before );
	$after = trim( $after );
	$new_title = $before;
	if ( $after ) {
		$new_title .= _x( ' - ', 'title delimiter', 'ct-framework' ) . $after;
	}
	
	return $new_title;
	
}

