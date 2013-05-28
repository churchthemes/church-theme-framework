<?php
/**
 * Compatibility Functions
 *
 * Require minimum version of WordPress, Church Content Manager plugin, Internet Explorer, etc.
 */

/*******************************************
 * WORDPRESS VERSION
 *******************************************/

/* Based on code from default Twenty Thirteen theme */

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

	return __( 'The theme you selected requires a newer version of WordPress. Please update and try again.', 'ct-framework' );

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
		wp_die( ctc_old_wp_message() . sprintf( ' <a href="javascript:history.go(-1);">%s</a>', __( 'Go back.', 'ct-framework' ) ) );

	}

}

/*****************************************************
 * FUNCTIONALITY PLUGIN
 *****************************************************/

/**
 * Plugin file
 */

function ctc_functionality_plugin_file() {

	return 'church-content-manager/church-content-manager.php';

}

/**
 * Plugin slug
 */

function ctc_functionality_plugin_slug() {

	return dirname( ctc_functionality_plugin_file() );

}

/**
 * Plugin is installed and has been activated
 */
 
function ctc_functionality_plugin_active() {

	$activated = false;

	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	
	if ( is_plugin_active( ctc_functionality_plugin_file() ) ) {
		$activated = true;
	}

	return apply_filters( 'ctc_functionality_plugin_active', $activated );
		
}

/**
 * Plugin is installed but not necessarily activated
 */
 
function ctc_functionality_plugin_installed() {

	$installed = false;

	if ( array_key_exists( ctc_functionality_plugin_file(), get_plugins() ) ) {
		$installed = true;
	}

	return apply_filters( 'ctc_functionality_plugin_installed', $installed );
		
}

/**
 * Admin Notice
 *
 * Show notice at top of admin until plugin is both installed and activated.
 */

add_action( 'admin_notices', 'ctc_functionality_plugin_notice' );

function ctc_functionality_plugin_notice() {

	// Plugin not installed
	if ( ! ctc_functionality_plugin_installed() ) {

		$notice = sprintf(
			__( '<b>Plugin Required:</b> Please install and activate the <a href="%s" class="thickbox">Church Content Manager</a> plugin to use with this theme.', 'ct-framework' ),
			network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . ctc_functionality_plugin_slug() . '&TB_iframe=true&width=700&height=450' )
		);

	}

	// Plugin installed but not activated
	elseif ( ! ctc_functionality_plugin_active() ) {

		$notice = sprintf(
			__( 'Please <a href="%s">activate</a> the <b>Church Content Manager</b> plugin required by this theme.', 'ct-framework' ),
			wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . ctc_functionality_plugin_file() ), 'activate-plugin_' . ctc_functionality_plugin_file() )
		);

	}

	// Show notice
	if (  isset( $notice ) ) {

		?>
		<div class="updated">
			<p>
				<?php echo $notice; ?>
			</p>
		</div>
		<?php

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
				'message' => __( 'You are using an outdated version of Internet Explorer. Please upgrade your browser to use this site.', 'ct-framework' ),
				'redirect_url' => apply_filters( 'ctc_upgrade_browser_url', 'http://churchthemes.com/upgrade-browser' )
			) );

		}

	}	

}