<?php
/**
 * Widget Setup
 *
 * Register widgets that are supported by theme and filter fields according to theme support.
 * Widgets are also restricted to certai sidebars as configured.
 *
 * Also see classes/widget.php which widgets extend for automatic field rendering, template loading, etc.
 */

/**********************************
 * WIDGET DATA
 **********************************/

/**
 * Available Widgets
 *
 * This can be filtered to register more widgets extending the framework's CTFW_Widgets class from the theme or child theme.
 * Widget classes existing in theme's includes/classes folder are loaded the same as those in framework/includes/classes.
 * Likewise, templates in the theme's widget-templates directory will be auto-loaded.
 */

function ctfw_widgets() {

	// Available widgets
	$widgets = array(
		'ctc-categories' => array(										// id_base as specified in widget's class
			'class'						=> 'CTFW_Widget_Categories',		// widget class name
			'class_file'				=> 'widget-categories.php',		// filename of class in framework class directory
			'template_file'				=> 'widget-categories.php',		// filename of template in widget-templates directory
			'ccm_required'				=> false,						// requires Church Content Manager plugin to be active
			'theme_support'				=> 'ctc-widget-categories',		// add_theme_support() feature required (can be empty)
			'theme_support_required'	=> array(),						// additional features theme must support for widget to register
			'unregister'	=> array(									// widgets to unregister when this is registered
				'WP_Widget_Categories'
			),
		),
		'ctc-posts' => array(
			'class'						=> 'CTFW_Widget_Posts',
			'class_file'				=> 'widget-posts.php',
			'template_file'				=> 'widget-posts.php',
			'ccm_required'				=> false,
			'theme_support'				=> 'ctc-widget-posts',
			'theme_support_required'	=> array(),
			'unregister'				=> array(
				'WP_Widget_Recent_Posts'
			)
		),		
		'ctc-sermons' => array(
			'class'						=> 'CTFW_Widget_Sermons',
			'class_file'				=> 'widget-sermons.php',
			'template_file'				=> 'widget-sermons.php',
			'ccm_required'				=> true,
			'theme_support'				=> 'ctc-widget-sermons',
			'theme_support_required'	=> array(
				'ccm-sermons',
			),
			'unregister'				=> array(),
		),
		'ctc-events' => array(
			'class'						=> 'CTFW_Widget_Events',
			'class_file'				=> 'widget-events.php',
			'template_file'				=> 'widget-events.php',
			'ccm_required'				=> true,
			'theme_support'				=> 'ctc-widget-events',
			'theme_support_required'	=> array(
				'ccm-events',
			),
			'unregister'				=> array(),
		),
		'ctc-gallery' => array(
			'class'						=> 'CTFW_Widget_Gallery',
			'class_file'				=> 'widget-gallery.php',
			'template_file'				=> 'widget-gallery.php',
			'ccm_required'				=> false, // uses native WordPress galleries
			'theme_support'				=> 'ctc-widget-gallery',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctc-galleries' => array(
			'class'						=> 'CTFW_Widget_Galleries',
			'class_file'				=> 'widget-galleries.php',
			'template_file'				=> 'widget-galleries.php',
			'ccm_required'				=> false, // uses native WordPress galleries
			'theme_support'				=> 'ctc-widget-galleries',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctc-people' => array(
			'class'						=> 'CTFW_Widget_People',
			'class_file'				=> 'widget-people.php',
			'template_file'				=> 'widget-people.php',
			'ccm_required'				=> true,
			'theme_support'				=> 'ctc-widget-people',
			'theme_support_required'	=> array(
				'ccm-people',
			),
			'unregister'				=> array(),
		),
		'ctc-locations' => array(
			'class'						=> 'CTFW_Widget_Locations',
			'class_file'				=> 'widget-locations.php',
			'template_file'				=> 'widget-locations.php',
			'ccm_required'				=> true,
			'theme_support'				=> 'ctc-widget-locations',
			'theme_support_required'	=> array(
				'ccm-locations',
			),
			'unregister'				=> array(),
		),
		'ctc-archives' => array(
			'class'						=> 'CTFW_Widget_Archives',
			'class_file'				=> 'widget-archives.php',
			'template_file'				=> 'widget-archives.php',
			'ccm_required'				=> false,
			'theme_support'				=> 'ctc-widget-archives',
			'theme_support_required'	=> array(),
			'unregister'	=> array(
				'WP_Widget_Archives'
			)
		),
		'ctc-giving' => array(
			'class'						=> 'CTFW_Widget_Giving',
			'class_file'				=> 'widget-giving.php',
			'template_file'				=> 'widget-giving.php',
			'ccm_required'				=> false,
			'theme_support'				=> 'ctc-widget-giving',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctc-slide' => array(
			'class'						=> 'CTFW_Widget_Slide',
			'class_file'				=> 'widget-slide.php',
			'template_file'				=> 'widget-slide.php',
			'ccm_required'				=> false,
			'theme_support'				=> 'ctc-widget-slide',
			'theme_support_required'	=> array(),
			'unregister'				=> array(),
		),
		'ctc-highlight' => array(
			'class'						=> 'CTFW_Widget_Highlight',
			'class_file'				=> 'widget-highlight.php',
			'template_file'				=> 'widget-highlight.php',
			'ccm_required'				=> false,
			'theme_support'				=> 'ctc-widget-highlight',
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
 */

add_action( 'widgets_init', 'ctfw_register_widgets' ); // same as init 1
 
function ctfw_register_widgets() {

	// Available widgets
	$widgets = ctfw_widgets();

	// Church Content Manager plugin is installed and activated?
	$ccm_active = ctc_functionality_plugin_active();
	
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
		
			// (here could use get_option() to check if feature is disabled by Church Content Manager plugin settings)

			// Church Content Manager is active or not required for widget
			if ( empty( $widget_data['ccm_required'] ) || $ccm_active ) {
				
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
						foreach( $widget_data['unregister'] as $unregister_widget ) {
							unregister_widget( $unregister_widget );
						}				
					}
				
				}
				
			}
		
		}
	
	}

}

/**
 * Unregister Widgets
 */

add_action( 'widgets_init', 'ctfw_unregister_widgets' ); // same as init 1
 
function ctfw_unregister_widgets() {

	// Links
	unregister_widget( 'WP_Widget_Links' );

}

/*********************************************
 * THEME SUPPORT
 *********************************************/

/**
 * Get theme support data for a widget
 */
 
function ctc_get_widget_theme_support( $widget_id, $argument ) {
	
	// Null by default so if argument data not found, isset() returns false
	$data = null;

	// Get widgets
	$widgets = ctfw_widgets();

	// Get widget data
	if ( isset( $widgets[$widget_id] ) ) { // valid widget

		// Widget data
		$widget = $widgets[$widget_id];
		
		// Theme has support for widget
		if ( $support = get_theme_support( $widget['theme_support'] ) ) {
		
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
	return apply_filters( 'ctc_get_widget_theme_support', $data, $widget_id, $argument );
	
}

/*********************************************
 * FIELD FILTERING
 *********************************************/
 
/**
 * Filter widget fields
 *
 * Add filters for classes/widget.php to set visibility and override data on fields based on theme support.
 */
 
add_action( 'widgets_init', 'ctc_filter_widget_fields' );

function ctc_filter_widget_fields() {

	// Loop widgets to filter their fields
	$widgets = ctfw_widgets();
	foreach ( $widgets as $widget_id => $widget_data ) {

		// Set Visible Fields
		add_filter( 'ctc_widget_visible_fields-' . $widget_id, 'ctc_set_visible_widget_fields', 10, 2 );
		
		// Set Field Overrides
		add_filter( 'ctc_widget_field_overrides-' . $widget_id, 'ctc_set_widget_field_overrides', 10, 2 );

	}

}

/**
 * Set Visible Fields
 *
 * Show or hide fields for a widget based on add_theme_support.
 * If no fields specifically supported, all are used.
 * If an empty array of fields is passed, no fields are used.
  */

function ctc_set_visible_widget_fields( $visible_fields, $widget_id ) {

	// Get theme's supported fields for widget
	$supported_fields = ctc_get_widget_theme_support( $widget_id, 'fields' );

	// Check if fields are set (even if empty)
	if ( isset( $supported_fields ) ) {
	
		// Make new array out of fields theme supports
		$visible_fields = $supported_fields;
		
		// (here could access Church Content Manager plugin settings to override theme's feature support)
		
	}
	
	// Return default or filtered field list
	return $visible_fields;

}

/**
 * Set Field Overrides
 *
 * Override widget field data based on add_theme_support.
 */
 
function ctc_set_widget_field_overrides( $field_overrides, $widget_id ) {

	// Return field overrides, if any
	return ctc_get_widget_theme_support( $widget_id, 'field_overrides' );

}

/*********************************************
 * DATA
 *********************************************/

/**
 * Get all registered widgets
 *
 * Is there a better way to do this?
 */

function ctc_get_registered_widgets() {

	global $wp_registered_widgets;

	$widgets = array();

	foreach ( $wp_registered_widgets as $widget ) {
		if ( ! empty( $widget['callback'][0]->id_base ) ) {
			$widgets[] = $widget['callback'][0]->id_base;
		}
	}

	$widgets = array_unique( $widgets ); // no duplicates

	return apply_filters( 'ctc_get_registered_widgets', $widgets );

}
