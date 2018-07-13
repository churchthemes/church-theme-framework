<?php
/**
 * Widget Setup
 *
 * Register widgets that are supported by theme and filter fields according to theme support.
 * Widgets are also restricted to certain sidebars as configured.
 *
 * Also see classes/widget.php which widgets extend for automatic field rendering, template loading, etc.
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
 * WIDGET DATA
 **********************************/

/**
 * Available Widgets
 *
 * This can be filtered to register more widgets extending the framework's CTFW_Widgets class from the theme or child theme.
 * Widget classes existing in theme's includes/classes folder are loaded the same as those in framework/includes/classes.
 * Likewise, templates in the theme's widget-templates directory will be auto-loaded.
 *
 * @since 0.9
 * @return array $widgets Available widgets configuration
 */
function ctfw_widgets() {

	// Available widgets
	$widgets = array(
		'ctfw-categories' => array(										// id_base as specified in widget's class
			'class'						=> 'CTFW_Widget_Categories',	// widget class name
			'class_file'				=> 'widget-categories.php',		// filename of class in framework class directory
			'template_file'				=> 'widget-categories.php',		// filename of template in widget-templates directory
			'ctc_required'				=> false,						// requires Church Content plugin to be active
			'theme_support'				=> 'ctfw-widget-categories',	// add_theme_support() feature required (can be empty)
			'theme_support_required'	=> array(),						// additional features theme must support for widget to register
			'icon'						=> 'dashicons-microphone',
			'unregister' 				=> array(						// widgets to unregister when this is registered
											'WP_Widget_Categories'
										),
		),
		'ctfw-posts' => array(
			'class'						=> 'CTFW_Widget_Posts',
			'class_file'				=> 'widget-posts.php',
			'template_file'				=> 'widget-posts.php',
			'ctc_required'				=> false,
			'theme_support'				=> 'ctfw-widget-posts',
			'theme_support_required'	=> array(),
			'unregister'				=> array(
				'WP_Widget_Recent_Posts'
			)
		),
		'ctfw-sermons' => array(
			'class'						=> 'CTFW_Widget_Sermons',
			'class_file'				=> 'widget-sermons.php',
			'template_file'				=> 'widget-sermons.php',
			'ctc_required'				=> true,
			'theme_support'				=> 'ctfw-widget-sermons',
			'theme_support_required'	=> array(
				'ctc-sermons',
			),
			'unregister'				=> array(),
		),
		'ctfw-events' => array(
			'class'						=> 'CTFW_Widget_Events',
			'class_file'				=> 'widget-events.php',
			'template_file'				=> 'widget-events.php',
			'ctc_required'				=> true,
			'theme_support'				=> 'ctfw-widget-events',
			'theme_support_required'	=> array(
				'ctc-events',
			),
			'unregister'				=> array(),
		),
		'ctfw-gallery' => array(
			'class'						=> 'CTFW_Widget_Gallery',
			'class_file'				=> 'widget-gallery.php',
			'template_file'				=> 'widget-gallery.php',
			'ctc_required'				=> false, // uses native WordPress galleries
			'theme_support'				=> 'ctfw-widget-gallery',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctfw-galleries' => array(
			'class'						=> 'CTFW_Widget_Galleries',
			'class_file'				=> 'widget-galleries.php',
			'template_file'				=> 'widget-galleries.php',
			'ctc_required'				=> false, // uses native WordPress galleries
			'theme_support'				=> 'ctfw-widget-galleries',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctfw-people' => array(
			'class'						=> 'CTFW_Widget_People',
			'class_file'				=> 'widget-people.php',
			'template_file'				=> 'widget-people.php',
			'ctc_required'				=> true,
			'theme_support'				=> 'ctfw-widget-people',
			'theme_support_required'	=> array(
				'ctc-people',
			),
			'unregister'				=> array(),
		),
		'ctfw-locations' => array(
			'class'						=> 'CTFW_Widget_Locations',
			'class_file'				=> 'widget-locations.php',
			'template_file'				=> 'widget-locations.php',
			'ctc_required'				=> true,
			'theme_support'				=> 'ctfw-widget-locations',
			'theme_support_required'	=> array(
				'ctc-locations',
			),
			'unregister'				=> array(),
		),
		'ctfw-archives' => array(
			'class'						=> 'CTFW_Widget_Archives',
			'class_file'				=> 'widget-archives.php',
			'template_file'				=> 'widget-archives.php',
			'ctc_required'				=> false,
			'theme_support'				=> 'ctfw-widget-archives',
			'theme_support_required'	=> array(),
			'unregister' 				=> array(
											'WP_Widget_Archives'
										)
		),
		'ctfw-giving' => array(
			'class'						=> 'CTFW_Widget_Giving',
			'class_file'				=> 'widget-giving.php',
			'template_file'				=> 'widget-giving.php',
			'ctc_required'				=> false,
			'theme_support'				=> 'ctfw-widget-giving',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctfw-slide' => array(
			'class'						=> 'CTFW_Widget_Slide',
			'class_file'				=> 'widget-slide.php',
			'template_file'				=> 'widget-slide.php',
			'ctc_required'				=> false,
			'theme_support'				=> 'ctfw-widget-slide',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctfw-highlight' => array(
			'class'						=> 'CTFW_Widget_Highlight',
			'class_file'				=> 'widget-highlight.php',
			'template_file'				=> 'widget-highlight.php',
			'ctc_required'				=> false,
			'theme_support'				=> 'ctfw-widget-highlight',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctfw-section' => array(
			'class'						=> 'CTFW_Widget_Section',
			'class_file'				=> 'widget-section.php',
			'template_file'				=> 'widget-section.php',
			'ctc_required'				=> false,
			'theme_support'				=> 'ctfw-widget-section',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
	);

	// Return filterable
	return apply_filters( 'ctfw_widgets', $widgets );

}

/**********************************
 * REGISTER WIDGETS
 **********************************/

/**
 * Register Widgets
 *
 * Include and register widgets that theme supports.
 *
 * @since 0.9
 */
function ctfw_register_widgets() {

	// Available widgets
	$widgets = ctfw_widgets();

	// Church Content plugin is installed and activated?
	$ctc_active = ctfw_ctc_plugin_active();

	// Loop widgets
	foreach ( $widgets as $widget_id => $widget_data ) {

		// Theme supports widget and required features?
		$supported = true;
		$theme_support = array_merge(
			(array) $widget_data['theme_support'],
			(array) $widget_data['theme_support_required']
		);
		foreach ( $theme_support as $feature ) {
			if ( ! empty( $feature ) && ! current_theme_supports( $feature ) ) { // support can be empt / not required (filtering in a non-framework widget)
				$supported = false; // one strike and you're out
				break;
			}
		}

		// Theme support is okay
		if ( $supported ) {

			// Church Content is active or not required for widget
			if ( empty( $widget_data['ctc_required'] ) || $ctc_active ) {

				// Include class if exists
				$widget_class_paths = array(
					trailingslashit( CTFW_THEME_CLASS_DIR ) . $widget_data['class_file'], // check non-framework dir first in case is theme-provided
					trailingslashit( CTFW_CLASS_DIR ) . $widget_data['class_file']
				);
				if ( locate_template( $widget_class_paths, true ) ) { // includes and returns true if exists

					// Register the widget
					register_widget( $widget_data['class'] );

					// Unregister widgets it replaces
					if ( isset( $widget_data['unregister'] ) ) {
						foreach ( $widget_data['unregister'] as $unregister_widget ) {
							unregister_widget( $unregister_widget );
						}
					}

				}

			}

		}

	}

}

add_action( 'widgets_init', 'ctfw_register_widgets' ); // same as init 1

/*********************************************
 * THEME SUPPORT
 *********************************************/

/**
 * Get theme support data for a widget
 *
 * @since 0.9
 * @param string $widget_id Widget ID
 * @param string $argument Specific argument
 * @return mixed Theme support data
 */
function ctfw_get_widget_theme_support( $widget_id, $argument ) {

	// Null by default so if argument data not found, isset() returns false
	$data = null;

	// Get widgets
	$widgets = ctfw_widgets();

	// Get widget data
	if ( isset( $widgets[$widget_id] ) ) { // valid widget

		// Widget data
		$widget = $widgets[$widget_id];

		// Theme has support for widget
		$support = get_theme_support( $widget['theme_support'] );
		if ( $support ) {

			// Get theme support data
			$support = isset( $support[0] ) && is_array( $support ) ? $support[0] : false;

			// Get fields supported
			if ( isset( $support[$argument] ) ) { // argument is set (even if empty)

				// Make new array out of fields theme supports
				$data = $support[$argument];

			}

		}

	}

	// Return filterable
	return apply_filters( 'ctfw_get_widget_theme_support', $data, $widget_id, $argument );

}

/*********************************************
 * FIELD FILTERING
 *********************************************/

/**
 * Filter widget fields
 *
 * Add filters for classes/widget.php to set visibility and override data on fields based on theme support.
 *
 * @since 0.9
 */
function ctfw_filter_widget_fields() {

	// Loop widgets to filter their fields
	$widgets = ctfw_widgets();
	foreach ( $widgets as $widget_id => $widget_data ) {

		// Set Visible Fields
		add_filter( 'ctfw_widget_visible_fields-' . $widget_id, 'ctfw_set_visible_widget_fields', 10, 2 );

		// Set Field Overrides
		add_filter( 'ctfw_widget_field_overrides-' . $widget_id, 'ctfw_set_widget_field_overrides', 10, 2 );

	}

}

add_action( 'widgets_init', 'ctfw_filter_widget_fields' );

/**
 * Set Visible Fields
 *
 * Show or hide fields for a widget based on add_theme_support.
 * If no fields specifically supported, all are used.
 * If an empty array of fields is passed, no fields are used.
 *
 * @since 0.9
 * @param array $visible_fields Default visible fields
 * @param string $widget_id Widget ID
 * @return array Modified visible fields
 */
function ctfw_set_visible_widget_fields( $visible_fields, $widget_id ) {

	// Get theme's supported fields for widget
	$supported_fields = ctfw_get_widget_theme_support( $widget_id, 'fields' );

	// Check if fields are set (even if empty)
	if ( isset( $supported_fields ) ) {

		// Make new array out of fields theme supports
		$visible_fields = $supported_fields;

		// (here could access Church Content plugin settings to override theme's feature support)

	}

	// Return default or filtered field list
	return $visible_fields;

}

/**
 * Set Field Overrides
 *
 * Override widget field data based on add_theme_support.
 *
 * @since 0.9
 * @param array $field_overrides Field data to override current settings with
 * @param string $widget_id The widget's ID
 * @return array Field override data
 */
function ctfw_set_widget_field_overrides( $field_overrides, $widget_id ) {

	// Return field overrides, if any
	return ctfw_get_widget_theme_support( $widget_id, 'field_overrides' );

}

/*********************************************
 * DATA
 *********************************************/

/**
 * Get all registered widgets
 *
 * Is there a better way to do this?
 *
 * @since 0.9
 * @global array $wp_registered_widgets
 * @return array Registered widgets
 */
function ctfw_get_registered_widgets() {

	global $wp_registered_widgets;

	$widgets = array();

	foreach ( $wp_registered_widgets as $widget ) {
		if ( ! empty( $widget['callback'][0]->id_base ) ) {
			$widgets[] = $widget['callback'][0]->id_base;
		}
	}

	$widgets = array_unique( $widgets ); // no duplicates

	return apply_filters( 'ctfw_get_registered_widgets', $widgets );

}

/**
 * Increment current widget's position in sidebar
 *
 * Store widgets position in its sidebar in a global.
 * Useful for determining if a position is first in a sidebar.
 *
 * @since 2.0
 * @global int $ctfw_current_widget_position Current widget position within its sidebar
 * @global string $ctfw_last_sidebar_id Last sidebar to match against current sidebar
 * @param array $sidebar_params Sidebar parameters
 */
function ctfw_increment_widget_position( $sidebar_params ) {

	global $ctfw_current_widget_position, $ctfw_last_sidebar_id;

	// Not in admin area
	if ( ! is_admin() ) {

		// Current sidebar
		$current_sidebar_id = isset( $sidebar_params[0]['id'] ) ? $sidebar_params[0]['id'] : '';

		// If no position set (first widget on page), start at 0
		// Or, if starting in new sidebar, restart at 0
		if (
			! isset( $ctfw_current_widget_position )
			||
			( isset( $ctfw_last_sidebar_id ) && $current_sidebar_id != $ctfw_last_sidebar_id )
		) {
			$ctfw_current_widget_position = 0;
		}

		// Increment position
		$ctfw_current_widget_position++;

		// Store last sidebar
		$ctfw_last_sidebar_id = $current_sidebar_id;

	}

	return $sidebar_params;

}

add_filter( 'dynamic_sidebar_params', 'ctfw_increment_widget_position' );
