<?php
/**
 * Admin Widgets Functions
 */

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
 */

add_filter( 'admin_head', 'ctfw_admin_restrict_widgets_css' ); 

function ctfw_admin_restrict_widgets_css() {

	$screen = get_current_screen();

	// Widgets page only
	if ( 'widgets' == $screen->base ) {

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

					// Elements for hiding form and save button
					$form_elements[] = "#$sidebar_id div[id*=_$widget_id-] .widget-content";
					$form_elements[] = "#$sidebar_id div[id*=_$widget_id-] .widget-control-save";

					// Element for showing message
					$message_elements[] = "#$sidebar_id div[id*=_$widget_id-] .ctfw-widget-incompatible";

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

