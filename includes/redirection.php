<?php
/**
 * Redirection Functions
 *
 * Note: redirection for other post types are in their respective files.
 */

/******************************************
 * NO SINGLE
 ******************************************/

/**
 * Singular Home Redirect
 *
 * Some post types don't have a single view, so redirect to homepage.
 */

add_action( 'template_redirect', 'ctc_singular_home_redirect' );
 
function ctc_singular_home_redirect() {

	$post_types = array( 'ccm_slide', 'ccm_highlight' );
	$post_types = apply_filters( 'ctc_singular_home_redirect_post_types', $post_types );
	
	if ( is_singular( $post_types ) ) {	
		wp_redirect( site_url() );
		exit;
	}

}
