<?php
/**
 * Update Functions
 */

// MAKE SURE THIS DOES NOT BREAK EDD UPDATES
// MAKE SURE THIS DOES NOT BREAK EDD UPDATES
// MAKE SURE THIS DOES NOT BREAK EDD UPDATES
// MAKE SURE THIS DOES NOT BREAK EDD UPDATES
// MAKE SURE THIS DOES NOT BREAK EDD UPDATES

// MOVE OUTSIDE OF FRAMEWORK? CONSIDER WP.ORG THEMES
// MOVE OUTSIDE OF FRAMEWORK? CONSIDER WP.ORG THEMES
// MOVE OUTSIDE OF FRAMEWORK? CONSIDER WP.ORG THEMES
// MOVE OUTSIDE OF FRAMEWORK? CONSIDER WP.ORG THEMES
// MOVE OUTSIDE OF FRAMEWORK? CONSIDER WP.ORG THEMES

// TEST THIS AGAIN
// TEST THIS AGAIN
// TEST THIS AGAIN
// TEST THIS AGAIN
// TEST THIS AGAIN

/*******************************************
 * UPDATE PROTECTION
 *******************************************/
	
/**
 * Prevent mistaken theme updates from wordpress.org
 * by Mark Jaquith (http://markjaquith.wordpress.com/2009/12/14/excluding-your-plugin-or-theme-from-update-checks/)
 *
 * This is to prevent the theme from being updated if a theme of same name exists on wordpress.org
 * Details of this issue: http://wpcandy.com/reports/developers-theme-commune-upgraded-to-different-commune
 *
 * Note: this only protects when theme is ACTIVE (plugin could protect inactive themes)
 */

add_filter( 'http_request_args', 'ctc_prevent_wrong_theme_update', 5, 2 );
	
function ctc_prevent_wrong_theme_update( $r, $url ) {

	if ( defined( 'CTC_WPORG_UPDATES' ) && CTC_WPORG_UPDATES == true ) { // set true for free wordpress.org themes
		if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
			return $r; // Not a theme update request. Bail immediately.
		$themes = unserialize( $r['body']['themes'] );
		unset( $themes[ get_option( 'template' ) ] );
		unset( $themes[ get_option( 'stylesheet' ) ] );
		$r['body']['themes'] = serialize( $themes );
	}
		
	return $r;

}
