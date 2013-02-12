<?php
/**
 * Update Functions
 */

// TEST THIS AGAIN
// TEST THIS AGAIN
// TEST THIS AGAIN
// TEST THIS AGAIN
// TEST THIS AGAIN

// IS add_theme_support TOO LATE? If so, change support.php:ctc-block-wporg-updates - use a constant in functions.php instead
// IS add_theme_support TOO LATE? If so, change support.php:ctc-block-wporg-updates - use a constant in functions.php instead
// IS add_theme_support TOO LATE? If so, change support.php:ctc-block-wporg-updates - use a constant in functions.php instead
// IS add_theme_support TOO LATE? If so, change support.php:ctc-block-wporg-updates - use a constant in functions.php instead
// IS add_theme_support TOO LATE? If so, change support.php:ctc-block-wporg-updates - use a constant in functions.php instead

// MAKE SURE THIS DOES NOT BREAK EDD UPDATES
// MAKE SURE THIS DOES NOT BREAK EDD UPDATES
// MAKE SURE THIS DOES NOT BREAK EDD UPDATES
// MAKE SURE THIS DOES NOT BREAK EDD UPDATES
// MAKE SURE THIS DOES NOT BREAK EDD UPDATES

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

	if ( current_theme_supports( 'ctc-block-wporg-updates' ) ) { // this should not be set for free wordpress.org hosted themes
		if ( 0 !== strpos( $url, 'http://api.wordpress.org/themes/update-check' ) )
			return $r; // Not a theme update request. Bail immediately.
		$themes = unserialize( $r['body']['themes'] );
		unset( $themes[ get_option( 'template' ) ] );
		unset( $themes[ get_option( 'stylesheet' ) ] );
		$r['body']['themes'] = serialize( $themes );
	}
		
	return $r;

}
