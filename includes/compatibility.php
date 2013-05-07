<?php
/**
 * Compatibility Functions
 */

/*******************************************
 * WORDPRESS
 *******************************************/

/* Based on code from default Twenty Thirteen theme */



// ADD THEME SUPPORT / VERSION TO FUNCS BELOW!
// ADD THEME SUPPORT / VERSION TO FUNCS BELOW!
// ADD THEME SUPPORT / VERSION TO FUNCS BELOW!
// ADD THEME SUPPORT / VERSION TO FUNCS BELOW!
// ADD THEME SUPPORT / VERSION TO FUNCS BELOW!

/**
 * Detect if old WordPress version used
 */

function ctc_old_wp() {

	$old = false;

	// Theme uses this feature
	if ( $support = get_theme_support( 'ctc-wordpress-version' ) ) { // returns false if feature not supported

		// Get minimum required version
		$required_version = isset( $support[0] ) ? $support[0] : '';

		// Is old version used?
		if ( version_compare( $GLOBALS['wp_version'], $required_version, '<' ) ) {
			$old = true;
		}

	}

	return apply_filters( 'ctc_old_wp', $old );

}

/**
 * Message to show when old version used
 */

function ctc_old_wp_message() {

	return __( 'The theme you selected requires a newer version of WordPress. Please update and try again.', 'church-theme' );

}

/**
 * Prevent switching to theme on old version of WordPress
 * 
 * Switches to the previously activated theme or the default theme.
 */

add_action( 'after_switch_theme', 'ctc_old_wp_switch_theme', 10, 2 );

function ctc_old_wp_switch_theme( $theme_name, $theme ) {

	// Is WordPress version too old for theme?
	if ( ctc_old_wp() ) {

		if ( CTC_TEMPLATE != $theme->get_template() ) {
			switch_theme( $theme->get_template(), $theme->get_stylesheet() );
		} elseif ( CTC_TEMPLATE != WP_DEFAULT_THEME ) {
			switch_theme( WP_DEFAULT_THEME );
		}

		unset( $_GET['activated'] );

		add_action( 'admin_notices', 'ctc_old_wp_switch_theme_notice' );

	}

}

/**
 * Show notice if try to switch to theme while using old version of WordPress
 */

function ctc_old_wp_switch_theme_notice() {

	?>
	<div class="error">
		<p>
			<?php echo ctc_old_wp_message(); ?>
		</p>
	</div>
	<?php

}

/**
 * Prevent Customizer preview from showing theme while using old version of WordPress
 */

add_action( 'load-customize.php', 'ctc_old_wp_customizer_notice' );

function ctc_old_wp_customizer_notice() {

	// Is WordPress version too old for theme?
	if ( ctc_old_wp() ) {

		// Show message
		wp_die( ctc_old_wp_message() . sprintf( ' <a href="javascript:history.go(-1);">%s</a>', __( 'Go back.', 'church-theme' ) ) );

	}

}

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