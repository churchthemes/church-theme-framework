<?php
/**
 * Compatibility Functions
 */
 
/*******************************************
 * BROWSERS
 *******************************************/

/**
 * Unsupported Internet Explorer
 *
 * Theme can use add_theme_support( 'ctc-ie-unsupported', 7 )
 * to prompt Internet Explorer 7 users to upgrade to a modern browser.
 */

add_action( 'wp_enqueue_scripts', 'ctc_enqueue_ie_unsupported' ); // front-end only
	
function ctc_enqueue_ie_unsupported() {

	// Only if theme requests this
	if ( $support = get_theme_support( 'ctc-ie-unsupported' ) ) { // returns false if feature not supported

		// Default and valid version range
		// Currently specified version must be between 5 and 9 (10 could require more complex regex, but that may never be needed)
		$default_version = 7;
		$min_version = 5;
		$max_version = 9;

		// Get version
		$version = isset( $support[0] ) ? $support[0] : $default_version;

		// Check version range
		$version = absint( $version );
		if ( $version < $min_version || $version > $max_version ) {
			$version = $default_version;
		}

		// Use that version or older?
		if ( preg_match( '/MSIE [5-' . preg_quote( $version ) . ']/i', $_SERVER['HTTP_USER_AGENT'] ) ) {

			wp_enqueue_script( 'jquery' ); // version packaged with WordPress

			wp_enqueue_script( 'ctc-ie-unsupported', ctc_theme_url( CTC_FW_JS_DIR . '/ie-unsupported.js' ), array( 'jquery' ), CTC_VERSION ); // bust cache on theme update

			wp_localize_script( 'ctc-ie-unsupported', 'ctc_ie_unsupported', array( // pass WP data into JS from this point on
				'message' => __( 'You are using an outdated version of Internet Explorer. Please upgrade your browser to use this site.', 'church-theme' ),
				'redirect_url' => apply_filters( 'ctc_upgrade_browser_url', 'http://churchthemes.com/upgrade-browser' )
			) );

		}

	}	

}