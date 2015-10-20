<?php
/**
 * Theme Activation
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013 - 2015, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/********************************************
 * AFTER ACTIVATION
 ********************************************/

/**
 * After Theme Activation
 *
 * Themes can request certain things to be done after activation:
 *
 *		add_theme_support( 'ctfw-after-activation', array(
 *			'flush_rewrite_rules'	=> true,
 *			'replace_notice'		=> sprintf( __( 'Please follow the <a href="%s">Next Steps</a> now that the theme has been activated.', 'your-theme-textdomain' ), 'http://churchthemes.com/guides/user/getting-started/' )
 *   	) );
 *
 * This does not affect the Customizer preview.
 *
 * @since 0.9
 */
function ctfw_after_activation() {

	// Does theme support this?
	$support = get_theme_support( 'ctfw-after-activation' );
	if ( $support ) {

		// What to do
		$activation_tasks = isset( $support[0] ) ? $support[0] : array();

		// Update .htaccess to make sure friendly URL's are in working order
		if ( ! empty( $activation_tasks['flush_rewrite_rules'] ) ) {
			flush_rewrite_rules();
		}

		// Show notice to user
		if ( ! empty( $activation_tasks['notice'] ) ) {

			add_action( 'admin_notices', 'ctfw_activation_notice', 5 ); // show above other notices

			// Hide default notice
			if ( ! empty( $activation_tasks['hide_default_notice'] ) ) {
				add_action( 'admin_head', 'ctfw_hide_default_activation_notice' );
			}

			// Remove other notices when showing activation notice -- keep it simple
			ctfw_activation_remove_notices();

		}

	}

}

add_action( 'after_switch_theme', 'ctfw_after_activation' );

/********************************************
 * NOTICES
 ********************************************/

/**
 * Message to show to user after activation
 *
 * Hooked in ctfw_after_activation().
 *
 * @since 0.9
 */
function ctfw_activation_notice() {

	// Get notice if supported by theme
	$support = get_theme_support( 'ctfw-after-activation' );
	$notice = ! empty( $support[0]['notice'] ) ? $support[0]['notice'] : '';

	// Show notice if have it
	if ( $notice ) {

		?>
		<div id="ctfw-activation-notice" class="updated">
			<p>
				<?php echo $notice; ?>
			</p>
		</div>
		<?php

	}

}

/**
 * Hide default activation notice
 *
 * @since 0.9
 */
function ctfw_hide_default_activation_notice() {

	echo '<style>#message2{ display: none; }</style>';

}

/**
 * Remove activation notices
 *
 * Remove all other notices from theme as not to overwhelm user on activation.
 * This way only the success (go to Next Steps) or fail (old WP version) notice for activation shows.
 *
 * See ctfw_after_activation() above and compatibility.php for where this is used.
 *
 * @since 0.9.1
 */
function ctfw_activation_remove_notices() {

	remove_action( 'admin_notices', 'ctfw_edd_license_notice', 7 ); // Theme License
	remove_action( 'admin_notices', 'ctfw_ctc_plugin_notice' ); // Church Theme Content

}
