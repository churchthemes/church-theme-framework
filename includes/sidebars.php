<?php
/**
 * Sidebar Functions
 */

/**********************************
 * RESTRICT SIDEBARS/WIDGETS
 **********************************/

/**
 * Get sidebar/widget restrictions
 *
 * Theme passes this data in via appropriate filter (see theme's includes/sidebars.php)
 */

function ctc_get_sidebar_widget_restrictions( $which = 'sidebar_widget' ) {

	if ( in_array( $which, array( 'sidebar_widget', 'widget_sidebar' ) ) ) {
		return apply_filters( 'ctc_' . $which . '_restrictions', array() );
	}

	return false;

}

/**
 * Sidebar and widget compatibility
 *
 * Check to see if a certain widget is allowed to be used with a certain sidebar and vice-versa.
 *
 * See the ctc_sidebar_widget_restrictions and ctc_widget_sidebar_restrictions filters.
 * Both are necessary in consideration of third party widgets and sidebars (via plugin or child theme).
 */

function ctc_sidebar_widget_compatible( $sidebar_id, $widget_id ) {

	$compatible = true;

	// Does this sidebar allow this widget?
	$sidebar_widget_restrictions = ctc_get_sidebar_widget_restrictions( 'sidebar_widget' );
	$include_widgets = isset( $sidebar_widget_restrictions[$sidebar_id]['include_widgets'] ) ? $sidebar_widget_restrictions[$sidebar_id]['include_widgets'] : array();
	$exclude_widgets = isset( $sidebar_widget_restrictions[$sidebar_id]['exclude_widgets'] ) ? $sidebar_widget_restrictions[$sidebar_id]['exclude_widgets'] : array();
	if (
		( ! empty( $include_widgets ) && ! in_array( $widget_id, $include_widgets ) )	// include_widgets is not empty and this widget is not in it
		|| ( ! empty( $exclude_widgets ) && in_array( $widget_id, $exclude_widgets ) )	// or exclude_widgets is not empty and this widget is in it
	) {
		$compatible = false;
	}

	// Does this widget allow use in this sidebar?
	$widget_sidebar_restrictions = ctc_get_sidebar_widget_restrictions( 'widget_sidebar' );
	$include_sidebars = isset( $widget_sidebar_restrictions[$widget_id]['include_sidebars'] ) ? $widget_sidebar_restrictions[$widget_id]['include_sidebars'] : array();
	$exclude_sidebars = isset( $widget_sidebar_restrictions[$widget_id]['exclude_sidebars'] ) ? $widget_sidebar_restrictions[$widget_id]['exclude_sidebars'] : array();
	if (
		( ! empty( $include_sidebars ) && ! in_array( $sidebar_id, $include_sidebars ) )	// include_sidebars is not empty and this sidebar is not in it
		|| ( ! empty( $exclude_sidebars ) && in_array( $sidebar_id, $exclude_sidebars ) )	// or exclude_sidebars is not empty and this sidebar is in it
	) {
		$compatible = false;
	}

	// Return filterable
	return apply_filters( 'ctc_sidebar_widget_compatible', $compatible );

}

/**
 * Restrict sidebar widgets
 *
 * See ctc_sidebar_widget_compatible() for how this works.
 * admin-widgets.php uses CSS to show message to incompatible widgets.
 *
 * Note: this is global and removed incompatible widget from both front-end
 * and from admin area, in case user does not remove after the warning.
 */

add_filter( 'sidebars_widgets', 'ctc_restrict_sidebars_widgets', 5 );

function ctc_restrict_sidebars_widgets( $sidebars_widgets ) {

	// Loop sidebars
	foreach( $sidebars_widgets as $sidebar_id => $widgets ) {

		// Leave core sidebars like "Inactive" alone
		if ( preg_match( '/^wp_/', $sidebar_id ) ) {
			continue;
		}
		// Sidebar widget restrictions
		// (used for checking limit)
		$sidebar_widget_restrictions = ctc_get_sidebar_widget_restrictions( 'sidebar_widget' );
		$sidebar_limit = ! empty( $sidebar_widget_restrictions[$sidebar_id]['limit'] ) ? $sidebar_widget_restrictions[$sidebar_id]['limit'] : false;

		// Loop widgets in sidebar
		$widget_i = 0;
		foreach( $widgets as $widget_key => $widget ) {

			$widget_i++;

			$remove = false;

			// Determine widget id of this instance
			$widget_id = substr( $widget, 0, strrpos( $widget, '-') ); // chop -# instance off end

			// Remove if either disallows the other
			if ( ! ctc_sidebar_widget_compatible( $sidebar_id, $widget_id ) ) {
				$remove = true;
			}

			// Remove if widget limit for sidebar has been reached
			// (front-end only since no warning shown and don't want re-arranging to cause loss)
			if ( ! is_admin() && $sidebar_limit && $widget_i > $sidebar_limit ) {
				$remove = true;
			}

			// Remove widget from sidebar
			if ( $remove)  {
				unset( $sidebars_widgets[$sidebar_id][$widget_key] );
			}

		}

	}

	// Re-index so keys are 0, 1, 2, etc. (fill in the gaps from unset)
	$sidebars_widgets[$sidebar_id] = array_values( $sidebars_widgets[$sidebar_id] );

	// Return pruned array
    return $sidebars_widgets;

}