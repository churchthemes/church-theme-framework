<?php
/**
 * Admin Widgets Functions (Framework)
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2013 - 2017, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
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
	);

	return apply_filters( 'ctfw_admin_widgets_js_data', $data );

}

/*******************************************
 * WIDGET RESTRICTIONS
 *******************************************/

/**
 * Insert widget incompatibility message
 *
 * This is added to all widgets but hidden by default with framework's js/admin-widgets.css
 * admin_head (below) outputs CSS to show this and hide form content
 *
 * @since 1.0
 */
function ctfw_widget_incompatible_message( $widget ) {

	// Only if feature is supported
	if ( ! current_theme_supports( 'ctfw-sidebar-widget-restrictions' ) ) {
		return;
	}

	// Output message
	?>
	<div class="ctfw-widget-incompatible">
		<?php _e( 'Sorry, this widget is not made for use in this area. Please delete.', 'church-theme-framework' ); ?>
	</div>
	<?php

}

add_action( 'ctfw_widget_before_fields', 'ctfw_widget_incompatible_message' );

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

						// Elements for hiding form fields and save button
						//$form_elements[] = "#$sidebar_id div[id*=_$widget_id-] .widget-content";
						$form_elements[] = "#$sidebar_id > .widget[id*=$widget_id] .widget-content > *:not(.ctfw-widget-incompatible)";
						$form_elements[] = "#$sidebar_id > .widget[id*=$widget_id] .widget-control-save";

						// Element for showing message
						$message_elements[] = "#$sidebar_id > .widget[id*=$widget_id] .ctfw-widget-incompatible";

					}

					// Customizer
					elseif ( 'customize' == $screen->base ) {

						// Elements for hiding form fields
						$form_elements[] = "#sub-accordion-section-sidebar-widgets-$sidebar_id .widget[id*=$widget_id] .widget-content > *:not(.ctfw-widget-incompatible)";

						// Element for showing message
						$message_elements[] = "#sub-accordion-section-sidebar-widgets-$sidebar_id .widget[id*=$widget_id] .ctfw-widget-incompatible";

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
