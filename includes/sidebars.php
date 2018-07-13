<?php
/**
 * Sidebar Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2017, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**********************************
 * RESTRICT SIDEBARS/WIDGETS
 **********************************/

/**
 * Get sidebar/widget restrictions
 *
 * Theme passes this data in via appropriate filter (see theme's includes/sidebars.php)
 *
 * @since 0.9
 * @param string $which 'sidebar_widget' or 'widget_sidebar'
 */
function ctfw_get_sidebar_widget_restrictions( $which = 'sidebar_widget' ) {

	if ( in_array( $which, array( 'sidebar_widget', 'widget_sidebar' ) ) ) {
		return apply_filters( 'ctfw_' . $which . '_restrictions', array() );
	}

	return false;

}

/**
 * Sidebar and widget compatibility
 *
 * Check to see if a certain widget is allowed to be used with a certain sidebar and vice-versa.
 *
 * See the ctfw_sidebar_widget_restrictions and ctfw_widget_sidebar_restrictions filters.
 * Both are necessary in consideration of third party widgets and sidebars (via plugin or child theme).
 *
 * @since 0.9
 * @param string $sidebar_id Sidebar ID
 * @param string $widget_ID Widget ID
 * @return bool Whether or not widget is allowed in sidebar
 */
function ctfw_sidebar_widget_compatible( $sidebar_id, $widget_id ) {

	$compatible = true;

	// Does this sidebar allow this widget?
	$sidebar_widget_restrictions = ctfw_get_sidebar_widget_restrictions( 'sidebar_widget' );
	$include_widgets = isset( $sidebar_widget_restrictions[$sidebar_id]['include_widgets'] ) ? $sidebar_widget_restrictions[$sidebar_id]['include_widgets'] : array();
	$exclude_widgets = isset( $sidebar_widget_restrictions[$sidebar_id]['exclude_widgets'] ) ? $sidebar_widget_restrictions[$sidebar_id]['exclude_widgets'] : array();
	if (
		( ! empty( $include_widgets ) && ! in_array( $widget_id, $include_widgets ) )	// include_widgets is not empty and this widget is not in it
		|| ( ! empty( $exclude_widgets ) && in_array( $widget_id, $exclude_widgets ) )	// or exclude_widgets is not empty and this widget is in it
	) {
		$compatible = false;
	}

	// Does this widget allow use in this sidebar?
	$widget_sidebar_restrictions = ctfw_get_sidebar_widget_restrictions( 'widget_sidebar' );
	$include_sidebars = isset( $widget_sidebar_restrictions[$widget_id]['include_sidebars'] ) ? $widget_sidebar_restrictions[$widget_id]['include_sidebars'] : array();
	$exclude_sidebars = isset( $widget_sidebar_restrictions[$widget_id]['exclude_sidebars'] ) ? $widget_sidebar_restrictions[$widget_id]['exclude_sidebars'] : array();
	if (
		( ! empty( $include_sidebars ) && ! in_array( $sidebar_id, $include_sidebars ) )	// include_sidebars is not empty and this sidebar is not in it
		|| ( ! empty( $exclude_sidebars ) && in_array( $sidebar_id, $exclude_sidebars ) )	// or exclude_sidebars is not empty and this sidebar is in it
	) {
		$compatible = false;
	}

	// Return filterable
	return apply_filters( 'ctfw_sidebar_widget_compatible', $compatible );

}

/**
 * Restrict sidebar widgets
 *
 * See ctfw_sidebar_widget_compatible() for how this works.
 * admin-widgets.php uses CSS to show message to incompatible widgets.
 *
 * Note: This affects both saving and displaying of widgets.
 *
 * @since 0.9
 * @param array $sidebars_widgets
 * @return array Modified $sidebars_widgets
 */
function ctfw_restrict_sidebars_widgets( $sidebars_widgets ) {

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-sidebar-widget-restrictions' ) ) {
		return $sidebars_widgets;
	}

	// Loop sidebars
	foreach ( $sidebars_widgets as $sidebar_id => $widgets ) {

		// Any widgets?
		if ( empty( $widgets ) ) {
			continue;
		}

		// Leave core sidebars like "Inactive" alone
		if ( preg_match( '/^wp_/', $sidebar_id ) ) {
			continue;
		}
		// Sidebar widget restrictions
		// (used for checking limit)
		$sidebar_widget_restrictions = ctfw_get_sidebar_widget_restrictions( 'sidebar_widget' );
		$sidebar_limit = ! empty( $sidebar_widget_restrictions[$sidebar_id]['limit'] ) ? $sidebar_widget_restrictions[$sidebar_id]['limit'] : false;

		// Loop widgets in sidebar
		$widget_i = 0;
		foreach ( $widgets as $widget_key => $widget ) {

			$widget_i++;

			$remove = false;

			// Determine widget id of this instance
			$widget_id = substr( $widget, 0, strrpos( $widget, '-') ); // chop -# instance off end

			// Remove if either disallows the other
			// Front-end only because this can cause JavaScript error with WordPress 3.9 Customizer when "Add a Widget" clicked
			// The user will always see "Not compatible" in widget editors until they remove, but will never show on frontend
			if ( ! is_admin() && ! ctfw_sidebar_widget_compatible( $sidebar_id, $widget_id ) ) {
				$remove = true;
			}

			// Remove if widget limit for sidebar has been reached
			// Front-end only since no warning shown and don't want re-arranging to cause loss
			if ( ! is_admin() && $sidebar_limit && $widget_i > $sidebar_limit ) {
				$remove = true;
			}

			// Remove widget from sidebar
			if ( $remove )  {
				unset( $sidebars_widgets[$sidebar_id][$widget_key] );
			}

		}

	}

	// Re-index so keys are 0, 1, 2, etc. (fill in the gaps from unset)
	if ( isset( $sidebars_widgets[$sidebar_id] ) ) {
		$sidebars_widgets[$sidebar_id] = array_values( $sidebars_widgets[$sidebar_id] );
	}

	// Return pruned array
    return $sidebars_widgets;

}

add_filter( 'sidebars_widgets', 'ctfw_restrict_sidebars_widgets', 5 );

/**********************************
 * SIDEBAR DATA
 **********************************/

/**
 * Set current sidebar ID
 *
 * See ctfw_is_sidebar() in conditions.php which uses this global.
 *
 * @since 2.0
 * @param  string $index Sidebar ID
 */

function saved_set_current_sidebar_id( $index ) {

	global $ctfw_current_sidebar_id;

	if ( ! empty( $index ) ) {
		$ctfw_current_sidebar_id = $index;
	}

}

add_action( 'dynamic_sidebar_before', 'saved_set_current_sidebar_id' );

/**
 * Unset current sidebar ID
 *
 * We unset so that this bool is not true even after leave the sidebar.
 *
 * See saved_set_current_sidebar_id() above for more.
 *
 * @since 2.0
 * @param  string $index Sidebar ID
 */
function saved_unset_current_sidebar_id() {

	global $ctfw_current_sidebar_id;

	if ( isset( $ctfw_current_sidebar_id ) ) {
		unset( $ctfw_current_sidebar_id );
	}

}

add_action( 'dynamic_sidebar_after', 'saved_unset_current_sidebar_id' );
