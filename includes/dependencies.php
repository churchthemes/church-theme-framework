<?php
/**
 * Dependencies
 *
 * Require the functionality plugin.
 */

/*****************************************************
 * FUNCTIONALITY PLUGIN
 *****************************************************/
 
/**
 * Check for Church Content Manager plugin activation
 */
 
function ctc_functionality_plugin_active() {

	$active = false;

	include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
	
	if ( is_plugin_active( 'church-content-manager/church-content-manager.php' ) ) {
		$active = true;
	}
	
	return apply_filters( 'ctc_functionality_plugin_active', $active );
		
}

/**
 * Admin Message
 *
 * Show message at top of all admin pages until the plugin is activated.
 */

add_action( 'admin_notices', 'ctc_functionality_plugin_notice' );

function ctc_functionality_plugin_notice() {

	// No theme support
	if ( ! ctc_functionality_plugin_active() ) {
	
    ?>
	<div class="updated">
       <p><?php _e( '<b>Plugin Required:</b> Please install and activate the <a href=/use path to ct.com redirecting to wp.org/>Church Content Manager</a> plugin to use with this theme.', 'church-theme' ); ?></p>
    </div>
	<?php
	
	}

}
