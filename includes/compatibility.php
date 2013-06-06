<?php
/**
 * Compatibility Functions
 *
 * Require minimum version of WordPress, Church Content Manager plugin, Internet Explorer, etc.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * WORDPRESS VERSION
 *******************************************/

// Based on code from default Twenty Thirteen theme

/**
 * Detect if old WordPress version used
 *
 * Use add_theme_support( 'ctfw-wordpress-version', 7 ); // 7 and under not supported
 *
 * @since 0.9
 * @return bool True if theme supports feature and version is old
 */
function ctfw_old_wp() {

	$old = false;

	// Theme uses this feature
	$support = get_theme_support( 'ctfw-wordpress-version' );
	if ( ! empty( $support[0] ) ) {

		// Get minimum required version
		$required_version = $support[0];

		// Is old version used?
		if ( version_compare( $GLOBALS['wp_version'], $required_version, '<' ) ) {
			$old = true;
		}

	}

	return apply_filters( 'ctfw_old_wp', $old );

}

/**
 * Message to show when old version used
 *
 * @since 0.9
 * @return string Message saying version is old
 */
function ctfw_old_wp_message() {
	return __( 'The theme you selected requires a newer version of WordPress. Please update and try again.', 'church-theme-framework' );
}

/**
 * Prevent switching to theme on old version of WordPress
 * 
 * Switches to the previously activated theme or the default theme.
 *
 * @since 0.9
 * @param string $theme_name Theme slug
 * @param object $theme Theme object
 */
function ctfw_old_wp_switch_theme( $theme_name, $theme ) {

	// Is WordPress version too old for theme?
	if ( ctfw_old_wp() ) {

		if ( CTFW_THEME_SLUG != $theme->get_template() ) {
			switch_theme( $theme->get_template(), $theme->get_stylesheet() );
		} elseif ( CTFW_THEME_SLUG != WP_DEFAULT_THEME ) {
			switch_theme( WP_DEFAULT_THEME );
		}

		unset( $_GET['activated'] );

		add_action( 'admin_notices', 'ctfw_old_wp_switch_theme_notice' );

	}

}

add_action( 'after_switch_theme', 'ctfw_old_wp_switch_theme', 10, 2 );

/**
 * Show notice if try to switch to theme while using old version of WordPress
 *
 * @since 0.9
 */
function ctfw_old_wp_switch_theme_notice() {

	?>
	<div class="error">
		<p>
			<?php echo ctfw_old_wp_message(); ?>
		</p>
	</div>
	<?php

}

/**
 * Prevent Customizer preview from showing theme while using old version of WordPress
 *
 * @since 0.9
 */
function ctfw_old_wp_customizer_notice() {

	// Is WordPress version too old for theme?
	if ( ctfw_old_wp() ) {

		// Show message
		wp_die( ctfw_old_wp_message() . sprintf( ' <a href="javascript:history.go(-1);">%s</a>', __( 'Go back.', 'church-theme-framework' ) ) );

	}

}

add_action( 'load-customize.php', 'ctfw_old_wp_customizer_notice' );

/*****************************************************
 * CHURCH CONTENT MANAGER
 *****************************************************/

/**
 * Plugin file
 *
 * @since 0.9
 * @return string Main plugin file relative to plugin directory
 */
function ctfw_ccm_plugin_file() {
	return 'church-content-manager/church-content-manager.php';
}

/**
 * Plugin slug
 *
 * @since 0.9
 * @return string Plugin slug based on its directory
 */
function ctfw_ccm_plugin_slug() {
	return dirname( ctfw_ccm_plugin_file() );
}

/**
 * Plugin is installed and has been activated
 *
 * @since 0.9
 * @return bool True if plugin installed and active
 */
function ctfw_ccm_plugin_active() {

	$activated = false;

	include_once ABSPATH . 'wp-admin/includes/plugin.php';
	
	if ( is_plugin_active( ctfw_ccm_plugin_file() ) ) {
		$activated = true;
	}

	return apply_filters( 'ctfw_ccm_plugin_active', $activated );
		
}

/**
 * Plugin is installed but not necessarily activated
 *
 * @since 0.9
 * @return bool True if plugin is installed
 */
function ctfw_ccm_plugin_installed() {

	$installed = false;

	if ( array_key_exists( ctfw_ccm_plugin_file(), get_plugins() ) ) {
		$installed = true;
	}

	return apply_filters( 'ctfw_ccm_plugin_installed', $installed );
		
}

/**
 * Admin notice
 *
 * Show notice at top of admin until plugin is both installed and activated.
 *
 * @since 0.9
 */
function ctfw_ccm_plugin_notice() {

	// Show only on relevant pages as not to overwhelm the admin
	$screen = get_current_screen();
	if ( ! in_array( $screen->base, array( 'dashboard', 'themes', 'plugins' ) ) ) {
		return;
	}

	// Plugin not installed
	if ( ! ctfw_ccm_plugin_installed() && current_user_can( 'install_plugins' ) ) {

		$notice = sprintf(
			__( '<b>Plugin Required:</b> Please install and activate the <a href="%s" class="thickbox">Church Content Manager</a> plugin to use with the current theme.', 'church-theme-framework' ),
			network_admin_url( 'plugin-install.php?tab=plugin-information&plugin=' . ctfw_ccm_plugin_slug() . '&TB_iframe=true&width=700&height=450' )
		);

	}

	// Plugin installed but not activated
	elseif ( ! ctfw_ccm_plugin_active() && current_user_can( 'activate_plugins' ) ) {

		$notice = sprintf(
			__( 'Please <a href="%s">activate</a> the <b>Church Content Manager</b> plugin required by the current theme.', 'church-theme-framework' ),
			wp_nonce_url( self_admin_url( 'plugins.php?action=activate&plugin=' . ctfw_ccm_plugin_file() ), 'activate-plugin_' . ctfw_ccm_plugin_file() )
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

add_action( 'admin_notices', 'ctfw_ccm_plugin_notice' );

/*******************************************
 * BROWSERS
 *******************************************/

/**
 * Unsupported Internet Explorer
 *
 * Theme can use add_theme_support( 'ctfw-ie-unsupported', 7 )
 * to prompt Internet Explorer 7 users to upgrade to a modern browser.
 *
 * @since 0.9
 */
function ctfw_enqueue_ie_unsupported() {

	// Only if theme requests this
	$support = get_theme_support( 'ctfw-ie-unsupported' );
	if ( $support ) {

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

			wp_enqueue_script( 'ctfw-ie-unsupported', ctfw_theme_url( CTFW_JS_DIR . '/ie-unsupported.js' ), array( 'jquery' ), CTFW_THEME_VERSION ); // bust cache on theme update

			wp_localize_script( 'ctfw-ie-unsupported', 'ctfw_ie_unsupported', array( // pass WP data into JS from this point on
				'message' => __( 'You are using an outdated version of Internet Explorer. Please upgrade your browser to use this site.', 'church-theme-framework' ),
				'redirect_url' => apply_filters( 'ctfw_upgrade_browser_url', 'http://churchthemes.com/upgrade-browser' )
			) );

		}

	}	

}

add_action( 'wp_enqueue_scripts', 'ctfw_enqueue_ie_unsupported' ); // front-end only
