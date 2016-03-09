<?php
/**
 * Church Theme Framework
 *
 * The framework provides code and assets common to multiple themes for more organized and efficient development/updates.
 * It is intended for use in themes that use the Church Theme Content plugin.
 *
 * @package   Church_Theme_Framework
 * @copyright Copyright (c) 2013 - 2015, churchthemes.com
 * @link      https://github.com/churchthemes/church-theme-framework
 * @license   http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/********************************************
 * CONSTANTS
 ********************************************/

/**
 * Get theme data
 *
 * If child theme, get parent theme data.
 */
$theme_data = wp_get_theme();
$theme_data = is_child_theme() ? wp_get_theme( $theme_data->template ) : $theme_data;

/**
 * Framework constants
 */
if ( ! defined( 'CTFW_VERSION' ) )				define( 'CTFW_VERSION', 			'1.7.6' );

/**
 * Theme constants
 */
if ( ! defined( 'CTFW_THEME_VERSION' ) )		define( 'CTFW_THEME_VERSION',		$theme_data->Version );						// parent theme version
if ( ! defined( 'CTFW_THEME_NAME' ) )			define( 'CTFW_THEME_NAME',			$theme_data->Name );						// parent theme name, specified in style.css
if ( ! defined( 'CTFW_THEME_SLUG' ) )			define( 'CTFW_THEME_SLUG',			$theme_data->template );					// parent theme's folder (theme slug)
if ( ! defined( 'CTFW_THEME_AUTHOR' ) )			define( 'CTFW_THEME_AUTHOR',		strip_tags( $theme_data->Author ) );		// parent theme's author
if ( ! defined( 'CTFW_THEME_PATH' ) )			define( 'CTFW_THEME_PATH',			get_template_directory() );					// parent theme path
if ( ! defined( 'CTFW_THEME_URL' ) )			define( 'CTFW_THEME_URL',			get_template_directory_uri() );				// parent theme URI
if ( ! defined( 'CTFW_THEME_CHILD_PATH' ) )		define( 'CTFW_THEME_CHILD_PATH',	get_stylesheet_directory() );				// child theme path
if ( ! defined( 'CTFW_THEME_CHILD_URL' ) )		define( 'CTFW_THEME_CHILD_URL',		get_stylesheet_directory_uri() );			// child theme URI

/**
 * Theme directory constants
 *
 * Theme and framework structures mirror each other.
 */
if ( ! defined( 'CTFW_THEME_INC_DIR' ) )		define( 'CTFW_THEME_INC_DIR',		'includes' );								// includes directory
if ( ! defined( 'CTFW_THEME_ADMIN_DIR' ) )		define( 'CTFW_THEME_ADMIN_DIR',		CTFW_THEME_INC_DIR . '/admin' );			// admin directory
if ( ! defined( 'CTFW_THEME_CLASS_DIR' ) )		define( 'CTFW_THEME_CLASS_DIR',		CTFW_THEME_INC_DIR . '/classes' );			// classes directory
if ( ! defined( 'CTFW_THEME_LIB_DIR' ) )		define( 'CTFW_THEME_LIB_DIR',		CTFW_THEME_INC_DIR . '/libraries' );		// libraries directory
if ( ! defined( 'CTFW_THEME_PAGE_TPL_DIR' ) )	define( 'CTFW_THEME_PAGE_TPL_DIR',	'page-templates' );							// page templates directory
if ( ! defined( 'CTFW_THEME_PARTIAL_DIR' ) )	define( 'CTFW_THEME_PARTIAL_DIR',	'partials' );								// partials directory (re-usable template parts)
if ( ! defined( 'CTFW_THEME_WIDGET_DIR' ) )		define( 'CTFW_THEME_WIDGET_DIR',	'widget-templates' );						// widget templates directory
if ( ! defined( 'CTFW_THEME_CSS_DIR' ) )		define( 'CTFW_THEME_CSS_DIR',		'css' );									// stylesheets directory
if ( ! defined( 'CTFW_THEME_JS_DIR' ) )			define( 'CTFW_THEME_JS_DIR',		'js' );										// JavaScript directory
if ( ! defined( 'CTFW_THEME_IMG_DIR' ) )		define( 'CTFW_THEME_IMG_DIR',		'images' );									// images directory
if ( ! defined( 'CTFW_THEME_BG_DIR' ) )			define( 'CTFW_THEME_BG_DIR',		CTFW_THEME_IMG_DIR . '/backgrounds' );		// background images directory
if ( ! defined( 'CTFW_THEME_COLOR_DIR' ) )		define( 'CTFW_THEME_COLOR_DIR',		'colors' );									// color schemes directory
if ( ! defined( 'CTFW_THEME_LANG_DIR' ) )		define( 'CTFW_THEME_LANG_DIR',		'languages' );								// languages directory

/**
 * Framework directory constants
 *
 * Note use of theme constants. Theme and framework structures mirror each other.
 */
if ( ! defined( 'CTFW_DIR' ) )					define( 'CTFW_DIR',					basename( dirname( __FILE__) ) );			// framework directory (where this file is)
if ( ! defined( 'CTFW_INC_DIR' ) )				define( 'CTFW_INC_DIR',				CTFW_DIR . '/' . CTFW_THEME_INC_DIR );		// framework includes directory
if ( ! defined( 'CTFW_ADMIN_DIR' ) )			define( 'CTFW_ADMIN_DIR',			CTFW_DIR . '/' . CTFW_THEME_ADMIN_DIR );	// framework admin directory
if ( ! defined( 'CTFW_CLASS_DIR' ) )			define( 'CTFW_CLASS_DIR',			CTFW_DIR . '/' . CTFW_THEME_CLASS_DIR );	// framework classes directory
if ( ! defined( 'CTFW_LIB_DIR' ) )				define( 'CTFW_LIB_DIR',				CTFW_DIR . '/' . CTFW_THEME_LIB_DIR );		// framework libraries directory
if ( ! defined( 'CTFW_CSS_DIR' ) )				define( 'CTFW_CSS_DIR',				CTFW_DIR . '/' . CTFW_THEME_CSS_DIR );		// framework stylesheets directory
if ( ! defined( 'CTFW_JS_DIR' ) )				define( 'CTFW_JS_DIR',				CTFW_DIR . '/' . CTFW_THEME_JS_DIR );		// framework JavaScript directory
if ( ! defined( 'CTFW_IMG_DIR' ) )				define( 'CTFW_IMG_DIR',				CTFW_DIR . '/' . CTFW_THEME_IMG_DIR );		// framework images directory

/********************************************
 * INCLUDES
 ********************************************/

/**
 * Includes to load
 */
$ctfw_includes = array(

	// Frontend or Admin
	'always' => array(

		// Functions
		CTFW_INC_DIR . '/archives.php',
		CTFW_INC_DIR . '/background.php',
		CTFW_INC_DIR . '/body.php',
		CTFW_INC_DIR . '/colors.php',
		CTFW_INC_DIR . '/comments.php',
		CTFW_INC_DIR . '/compatibility.php',
		CTFW_INC_DIR . '/conditions.php',
		CTFW_INC_DIR . '/content-types.php',
		CTFW_INC_DIR . '/customize.php',
		CTFW_INC_DIR . '/deprecated.php',
		CTFW_INC_DIR . '/downloads.php',
		CTFW_INC_DIR . '/embeds.php',
		CTFW_INC_DIR . '/events.php',
		CTFW_INC_DIR . '/fonts.php',
		CTFW_INC_DIR . '/gallery.php',
		CTFW_INC_DIR . '/head.php',
		CTFW_INC_DIR . '/helpers.php',
		CTFW_INC_DIR . '/images.php',
		CTFW_INC_DIR . '/localization.php',
		CTFW_INC_DIR . '/locations.php',
		CTFW_INC_DIR . '/maps.php',
		CTFW_INC_DIR . '/meta-data.php',
		CTFW_INC_DIR . '/mime-types.php',
		CTFW_INC_DIR . '/page-nav.php',
		CTFW_INC_DIR . '/pages.php',
		CTFW_INC_DIR . '/people.php',
		CTFW_INC_DIR . '/posts.php',
		CTFW_INC_DIR . '/taxonomies.php',
		CTFW_INC_DIR . '/template-tags.php',
		CTFW_INC_DIR . '/templates.php',
		CTFW_INC_DIR . '/sermons.php',
		CTFW_INC_DIR . '/sidebars.php',
		CTFW_INC_DIR . '/widgets.php',

		// Classes
		CTFW_CLASS_DIR . '/ct-recurrence.php',
		CTFW_CLASS_DIR . '/customize-controls.php',
		CTFW_CLASS_DIR . '/widget.php',

	),

	// Admin Only
	'admin' => array(

		// Functions
		CTFW_ADMIN_DIR . '/activation.php',
		CTFW_ADMIN_DIR . '/admin-enqueue-styles.php',
		CTFW_ADMIN_DIR . '/admin-enqueue-scripts.php',
		CTFW_ADMIN_DIR . '/admin-taxonomies.php',
		CTFW_ADMIN_DIR . '/admin-widgets.php',
		CTFW_ADMIN_DIR . '/edd-license.php',
		CTFW_ADMIN_DIR . '/editor.php',
		CTFW_ADMIN_DIR . '/import.php',
		CTFW_ADMIN_DIR . '/meta-boxes.php',

		// Libraries
		CTFW_LIB_DIR . '/ct-meta-box/ct-meta-box.php',

	),

	// Frontend Only
	'frontend' => array (

		// Classes
		CTFW_CLASS_DIR . '/breadcrumbs.php',
		CTFW_CLASS_DIR . '/walker-nav-menu-description.php',

	),

);

/**
 * Filter includes
 */
$ctfw_includes = apply_filters( 'ctfw_includes', $ctfw_includes ); // make filterable

/**
 * Load includes
 */
ctfw_load_includes( $ctfw_includes );

/**
 * Include loader function
 *
 * Used by framework above and functions.php for theme-specific includes.
 * If include exists in child theme, it will be used. Otherwise, parent theme file is used.
 *
 * @since 0.5
 * @param array $includes Files to include
 */
function ctfw_load_includes( $includes ) {

	// Loop conditions
	foreach ( $includes as $condition => $files ) {

		// Check condition
		$do_includes = false;
		switch( $condition ) {

			// Admin Only
			case 'admin':

				if ( is_admin() ) {
					$do_includes = true;
				}

				break;

			// Frontend Only
			case 'frontend':

				if ( ! is_admin() ) {
					$do_includes = true;
				}

				break;

			// Admin or Frontend (always)
			default:

				$do_includes = true;

				break;

		}

		// Loop files if condition met
		if ( $do_includes ) {

			foreach ( $files as $file ) {
				locate_template( $file, true ); // include from child theme first, then parent theme
			}

		}

	}

}
