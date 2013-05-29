<?php
/**
 * Theme license and automatic updates
 *
 * For use with remote install of Easy Digital Downloads Software Licensing extension.
 * Integration is based on Pippin Williamson's sample theme for the extension.
 *
 * Add support for this framework feature like this:
 *
 *		add_theme_support( 'ctc-edd-license', array(
 *  		'store_url'				=> 'yourstore.com',			// URL of store running EDD with Software Licensing extension
 *			'updates'				=> true,					// default true; enable automatic updates
 *			'options_page'			=> true,					// default true; provide options page for license entry/activaton
 *			'options_page_message'	=> '',						// optional message to show on options page
 *			'inactive_notice'		=> true,					// default true; show notice with link to license options page before license active
 *    	) );
 *
 * This default configuration assumes download's name in EDD is same as theme name.
 * See ctc_edd_license_config() below for other arguments and their defaults.
 */

/*******************************************
 * CONFIGURATION
 *******************************************/

/**
 * License feature configuration
 *
 * Return arguments specified for licensing feature.
 * If no argument passed, whole array is returned.
 */

function ctc_edd_license_config( $arg = false ) {

	$config = array();

	// Get theme support
	if ( $support = get_theme_support( 'ctc-edd-license' ) ) {

		// Get arguments
		$config = ! empty( $support[0] ) ? $support[0] : array();

		// Set defaults
		$config = wp_parse_args( $config, array(
			'store_url'				=> '',						// URL of store running EDD with Software Licensing extension
			'version'				=> CTC_VERSION,				// default is to auto-determine from theme
			'license'				=> ctc_edd_license_key(),	// default is to use '{theme}_license_key' option
			'item_name'				=> CTC_NAME,				// default is to use theme name; must match download name in EDD
			'author'				=> CTC_AUTHOR,				// default is to auto-determine from theme
			'updates'				=> true,					// default true; enable automatic updates
			'options_page'			=> true,					// default true; provide options page for license entry/activaton
			'options_page_message'	=> '',						// optional message to show on options page
			'inactive_notice'		=> true,					// default true; show notice with link to license options page before license active
		) );

		// Get specific argument?
		$config = isset( $config[$arg] ) ? $config[$arg] : $config;

	}

	// Return filtered
	return apply_filters( 'ctc_edd_license_config', $config );

}

/*******************************************
 * AUTOMATIC UPDATES
 *******************************************/

/**
 * Theme Updater
 */

add_action( 'after_setup_theme', 'ctc_edd_license_updater', 99 ); // after any use of add_theme_support() at 10

function ctc_edd_license_updater() {

	// Theme supports updates?
	if ( ctc_edd_license_config( 'updates' ) ) {

		// Include updater class
		locate_template( CTFW_CLASS_DIR . '/CTC_EDD_SL_Theme_Updater.php', true );

		// Activate updates
		$edd_updater = new CTC_EDD_SL_Theme_Updater( array( 
			'remote_api_url' 	=> ctc_edd_license_config( 'store_url' ), 		// Store URL running EDD with Software Licensing extension
			'version' 			=> ctc_edd_license_config( 'version' ), 		// Current version of theme
			'license' 			=> ctc_edd_license_key(), 						// The license key entered by user
			'item_name' 		=> ctc_edd_license_config( 'item_name' ),		// The name of this theme
			'author'			=> ctc_edd_license_config( 'author' )			// The author's name
		) );

	}

}

/*******************************************
 * OPTIONS DATA
 *******************************************/

/**
 * License Key Option Name
 *
 * Specific to the current theme.
 */

function ctc_edd_license_key_option( $append = '' ) {

	$field = CTC_TEMPLATE . '_license_key';

	if ( $append ) {
		$field .= '_' . ltrim( $append, '_' );
	}

	return apply_filters( 'ctc_edd_license_key_option', $field, $append );

}

/**
 * License Key Value
 */

function ctc_edd_license_key( $append = '' ) {

	$option = trim( get_option( ctc_edd_license_key_option( $append ) ) );

	return apply_filters( 'ctc_edd_license_key', $option, $append );

}

/**
 * License is locally active
 */

function ctc_edd_license_active() {

	$active = false;

	if ( get_option( ctc_edd_license_key_option( 'status' ) ) == 'active' ) {
		$active = true;
	}

	return apply_filters( 'ctc_edd_license_active', $active );

}

/*******************************************
 * OPTIONS PAGE
 *******************************************/

/**
 * Add menu item and page
 */

add_action( 'admin_menu', 'ctc_edd_license_menu' );

function ctc_edd_license_menu() {

	// Theme supports license options page?
	if ( ctc_edd_license_config( 'options_page' ) ) {

		// Add menu item and page
		add_theme_page(
			_x( 'Theme License', 'page title', 'church-theme-framework' ),
			_x( 'Theme License', 'menu title', 'church-theme-framework' ),
			'manage_options',
			'theme-license',
			'ctc_edd_license_page' // see below for output
		);

	}

}

/**
 * Options page content
 */

function ctc_edd_license_page() {

	$license 	= ctc_edd_license_key();
	$status 	= ctc_edd_license_key( 'status' ); // local status

	?>
	<div class="wrap">

		<?php screen_icon(); ?>

		<h2><?php _ex( 'Theme License', 'page title', 'church-theme-framework' ); ?></h2>

		<?php if ( $message = ctc_edd_license_config( 'options_page_message' ) ) : ?>
		<p>
			<?php echo $message; ?>
		</p>
		<?php endif; ?>

		<form method="post" action="options.php">
		
			<?php settings_fields( 'ctc_edd_license' ); ?>
			
			<?php wp_nonce_field( 'ctc_edd_license_nonce', 'ctc_edd_license_nonce' ); ?>

			<h3 class="title"><?php _ex( 'License Key', 'heading', 'church-theme-framework' ); ?></h3>

			<table class="form-table">

				<tbody>

					<tr valign="top">	

						<th scope="row" valign="top">
							<?php _e( 'License Key', 'church-theme-framework' ); ?>
						</th>

						<td>
							<input id="<?php echo esc_attr( ctc_edd_license_key_option() ); ?>" name="<?php echo esc_attr( ctc_edd_license_key_option() ); ?>" type="text" class="regular-text" value="<?php echo esc_attr( $license ); ?>" />
						</td>

					</tr>

				</tbody>

			</table>

			<?php submit_button( __( 'Save Key', 'church-theme-framework' ) ); ?>


			<?php if ( $license ) : ?>

			<h3 class="title"><?php _e( 'License Activation', 'church-theme-framework' ); ?></h3>

			<table class="form-table">

				<tbody>

					<tr valign="top">	

						<th scope="row" valign="top">
							<?php _e( 'License Status', 'church-theme-framework' ); ?>
						</th>

						<td>
							<?php if ( ctc_edd_license_active() ) : ?>
								<span class="ctc-license-active"><?php _ex( 'Active', 'license key', 'church-theme-framework' ); ?></span>
							<?php else : ?>
								<span class="ctc-license-inactive"><?php _ex( 'Inactive', 'license key', 'church-theme-framework' ); ?></span>
							<?php endif; ?>
						</td>

					</tr>

				</tbody>

			</table>

			<p class="submit">
				<?php if ( ctc_edd_license_active() ) : ?>
					<input type="submit" class="button button-primary" name="ctc_edd_license_deactivate" value="<?php _e( 'Deactivate License', 'church-theme-framework' ); ?>" />
				<?php else : ?>
					<input type="submit" class="button button-primary" name="ctc_edd_license_activate" value="<?php _e( 'Activate License', 'church-theme-framework' ); ?>" />
				<?php endif; ?>
			</p>

			<?php endif; ?>


		</form>

	</div>
	<?php
}

/**
 * Register option
 *
 * Create setting in options table
 */

add_action( 'admin_init', 'ctc_edd_license_register_option' );

function ctc_edd_license_register_option() {

	// If theme supports it
	if ( ctc_edd_license_config( 'options_page' ) ) {
		register_setting( 'ctc_edd_license', ctc_edd_license_key_option(), 'ctc_edd_license_sanitize' );
	}

}

/**
 * Sanitize license key
 */

function ctc_edd_license_sanitize( $new ) {

	$old = ctc_edd_license_key();

	// Unset local status as active when changing key -- need to activate new key
	if ( $old && $old != $new ) {
		delete_option( ctc_edd_license_key_option( 'status' ) );
	}

	$new = trim( $new );

	return $new;

}

/**
 * Activate or deactivate license key
 */

add_action( 'admin_init', 'ctc_edd_license_activation' );

function ctc_edd_license_activation( ) {

	// Activate or Deactivate button clicked
	if ( isset( $_POST['ctc_edd_license_activate'] ) || isset( $_POST['ctc_edd_license_deactivate'] ) ) {

		// Security check
	 	if( ! check_admin_referer( 'ctc_edd_license_nonce', 'ctc_edd_license_nonce' ) ) {
			return;
		}

		// Activate or deactivate?
		$action = isset( $_POST['ctc_edd_license_activate'] ) ? 'activate_license' : 'deactivate_license';

		// Call action via API
		if ( $license_data = ctc_edd_license_action( $action ) ) {

			// If activated remotely, set local status; or set local status if was already active remotely -- keep in sync
			if ( 'activate_license' == $action ) {

				// Success
				if ( 'valid' == $license_data->license || 'valid' == ctc_edd_license_check() ) {
					update_option( ctc_edd_license_key_option( 'status' ), 'active' );
				}

				// Failure - note error for next page load
				else {
					set_transient( 'ctc_edd_license_activation_result', 'fail', 15 ); // will be deleted after shown or in 15 seconds
				}

			}

			// If deactivated remotely, set local status; or set local status if was already inactive remotely -- keep in sync
			elseif ( 'deactivate_license' == $action && ( 'deactivated' == $license_data->license || 'inactive' == ctc_edd_license_check() ) ) {
				delete_option( ctc_edd_license_key_option( 'status' ) );
			}

		}

	}

}

/**
 * Show notice on activation failure
 */

add_action( 'admin_notices', 'ctc_edd_license_activation_failure_notice' );

function ctc_edd_license_activation_failure_notice() {

	// Only on Theme License page
	$screen = get_current_screen();
	if ( 'appearance_page_theme-license' != $screen->base ) {
		return;
	}

	// Have a result transient?
	if ( $activation_result = get_transient( 'ctc_edd_license_activation_result' ) ) {

		// Failed
		if ( 'fail' == $activation_result ) {

			?>
			<div id="ctc-license-activation-error-notice" class="error">
				<p>
					<?php _e( '<b>License key could not be activated.</b> Please make sure the saved key is correct.', 'church-theme-framework' ); ?>
				</p>
			</div>
			<?php

		}

		// Delete transient
		delete_transient( 'ctc_edd_license_activation_result' );

	}

}

/*******************************************
 * LICENSE NOTICE
 *******************************************/

/**
 * Show inactive license notice
 */

add_action( 'admin_notices', 'ctc_edd_license_notice', 7 ); // higher priority than functionality plugin notice

function ctc_edd_license_notice() {

	// Theme supports this notice?
	if ( ! ctc_edd_license_config( 'inactive_notice' ) ) {
		return;
	}

	// License is already locally active
	if ( ctc_edd_license_active() ) {
		return;
	}

	// User can edit theme options?
	if ( ! current_user_can( 'edit_theme_options' ) ) {
		return;
	}

	// Show only on relevant pages as not to overwhelm the admin
	$screen = get_current_screen();
	if ( ! in_array( $screen->base, array( 'dashboard', 'themes', 'update-core' ) ) ) {
		return;
	}

	// Notice
	?>
	<div id="ctc-license-notice" class="updated">
		<p>
			<?php
			printf(
				__( '<b>License Activation:</b> Please activate your <a href="%s">License Key</a> for the %s theme.', 'church-theme-framework' ),
				admin_url( 'themes.php?page=theme-license' ),
				CTC_NAME
			);
			?>
		</p>
	</div>
	<?php

}

/*******************************************
 * EDD API
 *******************************************/

/**
 * Call API with specific action
 *
 * https://easydigitaldownloads.com/docs/software-licensing-api/
 * activate_license, deactivate_license or check_license
 */

function ctc_edd_license_action( $action ) {

	$license_data = array();

	// Theme stores local option?
	if ( ctc_edd_license_config( 'options_page' ) ) {

		// Valid action?
		$actions = array( 'activate_license', 'deactivate_license', 'check_license' );
		if ( in_array( $action, $actions ) ) {

			// Get license
			$license = ctc_edd_license_key();

			// Have license
			if ( $license ) {

				// Data to send in API request
				$api_params = array( 
					'edd_action'	=> $action, 
					'license' 		=> $license, 
					'item_name'		=> urlencode( ctc_edd_license_config( 'item_name' ) ) // name of download in EDD
				);

				// Call the API
				$response = wp_remote_get( add_query_arg( $api_params, ctc_edd_license_config( 'store_url' ) ), array( 'timeout' => 15, 'sslverify' => false ) );

				// Got a valid response?
				if ( ! is_wp_error( $response ) ) {

					// Decode the license data
					$license_data = json_decode( wp_remote_retrieve_body( $response ) );

				}

			}

		}

	}

	return apply_filters( 'ctc_edd_license_action', $license_data, $action );

}

/**
 * Check license key status
 *
 * Check if license is valid on remote end.
 */

function ctc_edd_license_check() {

	$status = '';

	// Call action via API
	if ( $license_data = ctc_edd_license_action( 'check_license' ) ) {
		$status = $license_data->license;
	}

	return apply_filters( 'ctc_edd_license_check', $status );

}

/**
 * Check for remote deactivation and update local
 *
 * It's handy to run this periodically in case license has been remotely deactivated.
 * Otherwise, they may think they are up to date when they are not.
 */

function ctc_edd_license_check_deactivation() {

	// Theme stores local option?
	if ( ! ctc_edd_license_config( 'options_page' ) ) {
		return;
	}

	// Only if locally active
	if ( ! ctc_edd_license_active() ) { // already inactive locally
		return;
	}

	// Check remote status
	$status = ctc_edd_license_check();

	// Continue only if got a response
	if ( ! empty( $status ) ) { // don't do anything if times out

		// Deactivated remotely
		if ( 'inactive' == $status ) { // status is not valid

			// Deactivate locally
			delete_option( ctc_edd_license_key_option( 'status' ) );

		}

	}

}

/**
 * Run remote deactivation check automatically
 *
 * Check for remote deactivation periodically on relevant pages: Theme License, Themes, Updates
 */

add_action( 'current_screen', 'ctc_edd_license_auto_check_deactivation' );

function ctc_edd_license_auto_check_deactivation() {

	// Admin only
	if ( ! is_admin() ) {
		return;
	}

	// Theme stores local option?
	if ( ! ctc_edd_license_config( 'options_page' ) ) {
		return;
	}

	// Only in relevant areas
	$screen = get_current_screen();
	if ( in_array( $screen->base, array( 'appearance_page_theme-license', 'themes', 'update-core' ) ) ) {

		// Has this been checked in last day?
		if ( ! get_transient( 'ctc_edd_license_auto_check_deactivation' ) ) {

			// Check remote status and deactivate locally if necessary
			ctc_edd_license_check_deactivation();

			// Set transient to prevent check until next day
			set_transient( 'ctc_edd_license_auto_check_deactivation', true, DAY_IN_SECONDS );

		}

	}

}
