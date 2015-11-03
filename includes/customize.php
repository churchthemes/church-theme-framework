<?php
/**
 * Theme Customizer
 *
 * Helper functions for theme customizer use.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2015, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * VALUES
 *******************************************/

/**
 * Customization option ID
 *
 * Used for storing and getting customizer option values from a master array.
 * The option name is based on the parent theme's name.
 * Settings API used instead of theme mod for greater flexibility.
 *
 * @since 0.9
 * @return string Option ID for customizations, unique to theme
 */
function ctfw_customize_option_id() {

	$option_id = CTFW_THEME_SLUG . '_customizations'; // unique to parent theme

	return apply_filters( 'ctfw_customize_option_id', $option_id ); // prefix with theme name so options are unique to theme

}

/**
 * Get customization value
 *
 * This gets a customization value for convenient use in templates, etc.
 * It automatically fills in default
 *
 * @since 0.9
 * @param string $option Customization option
 * @return string Option value
 */
function ctfw_customization( $option ) {

	$value = '';

	// Get options array to pull value from
	$options = get_option( ctfw_customize_option_id() );

	// Get default value
	$defaults = ctfw_customize_defaults();
	$default = isset( $defaults[$option]['value'] ) ? $defaults[$option]['value'] : '';

	// Option not saved - use default value
	if ( ! isset( $options[$option] ) ) {
		$value = $default;
	}

	// Option has been saved
	else {

		// Value is empty when not allowed, use default
		if ( empty( $options[$option] ) && ! empty( $defaults[$option]['no_empty'] ) ) {
			$value = $default;
		}

		// Otherwise, stick with current value
		else {
			$value = $options[$option];
		}

	}

	// Replace psuedo shortcodes (used in Footer notice, Icon URLs, etc.)
	$value = str_replace( '[ctcom_site_name]', get_bloginfo( 'name' ), $value );
	$value = str_replace( '[ctcom_current_year]', date( 'Y' ), $value );
	$value = str_replace( '[ctcom_rss_url]', get_bloginfo( 'rss_url' ), $value );

	// Return filtered
	return apply_filters( 'ctfw_customization', $value, $option );

}

/**
 * Get raw customization value
 *
 * Get raw customization value without filling in of defaults
 *
 * @since 1.4.1
 * @param string $option Customization option
 * @return string Option value
 */
function ctfw_customization_raw( $option ) {

	$value = '';

	// Get options array to pull value from
	$options = get_option( ctfw_customize_option_id() );

	// Get value if set
	if ( isset( $options[$option] ) ) {
		$value = $options[$option];
	} else { // empty if not set
		$value = '';
	}

	// Return filtered
	return apply_filters( 'ctfw_customization_raw', $value, $option );

}

/**
 * Update customization value
 *
 * This updates a customization value in the option's array of settings
 *
 * @since 1.4.1
 * @param string $option Customization option to update
 * @param mixed $value Value to update with
 */
function ctfw_update_customization( $option, $value ) {

	// Get array of options
	$options = get_option( ctfw_customize_option_id() );

	// Update the value
	$options[$option] = $value;

	// Save modified array back to option
	update_option( ctfw_customize_option_id(), $options );

}

/**
 * Unset customization
 *
 * This removes a customization from array of settings
 *
 * @since 1.4.1
 * @param string $option Customization option to remove from array
 */
function ctfw_unset_customization( $option, $value ) {

	// Get array of options
	$options = get_option( ctfw_customize_option_id() );

	// Update the value
	unset( $options[$option] );

	// Save modified array back to option
	update_option( ctfw_customize_option_id(), $options );

}

/**
 * Get Defaults
 *
 * Theme can make array of defaults available to framework via ctfw_customize_defaults filter.
 * This way they can be accessed via this function from anywhere.
 *
 * @since 0.9
 * @return array Customizer defaults
 */
function ctfw_customize_defaults() {

	// Get cached defaults
	// Customization settings are gotten dozens of times per pageload
	// Caching them is a good idea in case default values every come from a function or query
	$transient = 'ctfw_customize_defaults';
	$defaults = get_transient( $transient );

	// No cache; get defaults from theme, then cache
	if ( false === $defaults ) {

		// Get defaults from theme
		$defaults = apply_filters( 'ctfw_customize_defaults', array() );

		// Cache defaults
		// 10 seconds good enough for one page load
		set_transient( $transient, $defaults, 10 );

	}

	return $defaults;

}

/*******************************************
 * SANITIZATION
 *******************************************/

/**
 * Sanitize Checkbox
 *
 * This is useful for using directly with sanitize_callback and sanitize_js_callback.
 *
 * @since 1.1.4
 * @param string $input The user-entered value
 * @return string Sanitized value
 */
function ctfw_customize_sanitize_checkbox( $input, $object ) {

	// True or empty
	if ( 1 == $input ) {
		$output = $input;
	} else {
		$output = '';
	}

	// Return sanitized, filterable
	return apply_filters( 'ctfw_customize_sanitize_checkbox', $output, $input, $object );

}

/**
 * Sanitize Single Choice
 *
 * Sanitize radio or single select.
 *
 * Check if input matches choices given and if not use default value when empty value not permitted.
 *
 * @since 1.1.4
 * @param string $setting The setting being sanitized, as provided in defaults array
 * @param string $input The user-entered value
 * @param array $choices Valid choices to check against
 * @return string Sanitized value
 */
function ctfw_customize_sanitize_single_choice( $setting, $input, $choices ) {

	// Default values
	$defaults = ctfw_customize_defaults();

	// Not valid choice; use default if empty value not permitted
	if ( ! isset( $choices[$input] ) && ! empty( $defaults[$setting]['no_empty'] ) ) {
		$output = $defaults[$setting]['value'];
	} else {
		$output = $input; // valid choice
	}

	// Return sanitized, filterable
	return apply_filters( 'ctfw_customize_sanitize_single_choice', $output, $setting, $input, $choices );

}

/**
 * Sanitize Color
 *
 * If hex code empty or invalid, use default value when empty value is not permitted.
 * Add # to front of hex code if missing.
 *
 * @since 1.1.4
 * @param string $setting The setting being sanitized, as provided in defaults array
 * @param string $input The user-entered value
 * @return string Sanitized value
 */
function ctfw_customize_sanitize_color( $setting, $input ) {

	// Default values
	$defaults = ctfw_customize_defaults();

	// Return null if hex code invalid
	$output = sanitize_hex_color( $input );

	// If invalid or empty and empty value not permitted, use default
	if ( empty( $output ) && ! empty( $defaults[$setting]['no_empty'] ) ) {
		$output = $defaults[$setting]['value'];
	}

	// Add # if missing
	$output = maybe_hash_hex_color( $output );

	// Return sanitized, filterable
	return apply_filters( 'ctfw_customize_sanitize_color', $output, $setting, $input );

}

/**
 * Sanitize Google Font
 *
 * Check if input matches choices given and if not use default value when empty value not permitted.
 *
 * @since 1.1.4
 * @param string $setting The setting being sanitized, as provided in defaults array
 * @param string $input Unsanitized value submitted by user
 * @return string Sanitized value
 */
function ctfw_customize_sanitize_google_font( $setting, $input ) {

	// Valid choices
	$choices = ctfw_google_font_options_array( array( 'target' => $setting ) );

	// Check input against options; use default if empty value not permitted
	// ctfw_customize_sanitize_single_choice() is for radio or single select
	$output = ctfw_customize_sanitize_single_choice( $setting, $input, $choices );

	// Return sanitized, filterable
	return apply_filters( 'ctfw_customize_sanitize_google_font', $output, $setting, $input );

}

/**
 * Also see ctfw_sanitize_url_list() in includes/helpers.php.
 * It is useful for social media URL lists in Customizer.
 */


/*********************************************
 * SCRIPTS & STYLES
 *********************************************/

/**
 * Enqueue JavaScript for customizer controls
 *
 * @since 1.2
 */
function ctfw_customize_enqueue_scripts() {

	// New media uploader in WP 3.5+
	wp_enqueue_media();

	// Main widgets script
	wp_enqueue_script( 'ctfw-admin-widgets', ctfw_theme_url( CTFW_JS_DIR . '/admin-widgets.js' ), array( 'jquery' ), CTFW_THEME_VERSION ); // bust cache on update
	wp_localize_script( 'ctfw-admin-widgets', 'ctfw_widgets', ctfw_admin_widgets_js_data() ); // see admin-widgets.php

}

add_action( 'customize_controls_print_scripts', 'ctfw_customize_enqueue_scripts' );

/**
 * Enqueue styles for customizer controls
 *
 * @since 1.2
 */
function ctfw_customize_enqueue_styles() {

	// Admin widgets
	// Same stylesheet used for Appearance > Widgets
	wp_enqueue_style( 'ctfw-widgets', ctfw_theme_url( CTFW_CSS_DIR . '/admin-widgets.css' ), false, CTFW_THEME_VERSION ); // bust cache on update

}

add_action( 'customize_controls_print_styles', 'ctfw_customize_enqueue_styles' );
