<?php
/**
 * Admin Widgets Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013-2014, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * DATA
 *******************************************/

/**
 * Admin widget JavaScript data
 *
 * This is for passing into localization for JavaScript when admin-widget.js is enqueued.
 * It's functionized because admin-widget.js is inqueued in two places: Appearance > Widgets and Customize
 *
 * @since 1.2
 * @return array Data for JavaScript
 */

function ctfw_admin_widgets_js_data() {

	$data = array( // make data available
		'image_library_title'	=> _x( 'Choose Image for Widget', 'widget image library', 'church-theme-framework' ),
		'image_library_button'	=> _x( 'Use in Widget', 'widget image library', 'church-theme-framework' ),
		'incompatible_message'	=> __( 'Sorry, this widget is not made for use in this area. Please delete.', 'church-theme-framework' ),
		'widget_restrictions'	=> current_theme_supports( 'ctfw-sidebar-widget-restrictions' )
	);

	return apply_filters( 'ctfw_admin_widgets_js_data', $data );

}

/*******************************************
 * WIDGET RESTRICTIONS
 *******************************************/

/**
 * Show widget incompatibility messages
 *
 * If a user drags a widget into a sidebar that it is not compatible with, a message is shown.
 * Also see admin-widgets.css, admin-widgets.js and widgets.php.
 *
 * Note: ctfw_restrict_sidebars_widgets() handles removing widgets from sidebars on both front-end
 * and back-end in case user does not.
 *
 * @since 0.9
 */
function ctfw_admin_restrict_widgets_css() {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-sidebar-widget-restrictions' ) )  {
		return;
	}

	// Current admin screen
	$screen = get_current_screen();

	// Widgets page or Customizer only
	if ( 'widgets' == $screen->base || 'customize' == $screen->base ) {

		// Elements will be captured into these
		$form_elements = array();
		$message_elements = array();

		// Get all registered widgets
		$widgets = ctfw_get_registered_widgets();

		// Loop all sidebars
		$sidebars = wp_get_sidebars_widgets();
		foreach ( $sidebars as $sidebar_id => $sidebar ) {

			// Leave core sidebars like "Inactive" alone
			if ( preg_match( '/^wp_/', $sidebar_id ) ) {
				continue;
			}

			// Loop widgets
			foreach ( $widgets as $widget_id ) {

				// Check if sidebar and widget are not compatible
				if ( ! ctfw_sidebar_widget_compatible( $sidebar_id, $widget_id ) ) {

					// Appearance > Widgets
					if ( 'widgets' == $screen->base ) {

						// Elements for hiding form and save button
						$form_elements[] = "#$sidebar_id div[id*=_$widget_id-] .widget-content";
						$form_elements[] = "#$sidebar_id div[id*=_$widget_id-] .widget-control-save";

						// Element for showing message
						$message_elements[] = "#$sidebar_id div[id*=_$widget_id-] .ctfw-widget-incompatible";

					}

					// Customizer
					elseif ( 'customize' == $screen->base ) {

						// Elements for hiding form and save button
						$form_elements[] = "#accordion-section-sidebar-widgets-$sidebar_id div[id*=_$widget_id-] .widget-content";
						$form_elements[] = "#accordion-section-sidebar-widgets-$sidebar_id div[id*=_$widget_id-] .widget-control-save";

						// Element for showing message
						$message_elements[] = "#accordion-section-sidebar-widgets-$sidebar_id div[id*=_$widget_id-] .ctfw-widget-incompatible";

					}

				}

			}

		}

		// Output stylesheet
		if ( ! empty( $form_elements ) && ! empty( $message_elements ) ) {

			// Compile elements
			$form_elements = implode( ",\n", $form_elements );
			$message_elements = implode( ",\n", $message_elements );

			// Output stylesheet
echo <<< HTML
<style type="text/css">
$form_elements {
	display: none;
}
$message_elements {
	display: block;
}
</style>

HTML;

		}

	}

}

add_action( 'admin_head', 'ctfw_admin_restrict_widgets_css' );
add_action( 'customize_controls_print_scripts', 'ctfw_admin_restrict_widgets_css' ); // Customizer too
