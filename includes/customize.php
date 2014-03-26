<?php
/**
 * Theme Customizer
 *
 * Helper functions for theme customizer use.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
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

	// Return filtered
	return apply_filters( 'ctfw_customization', $value, $option );
	
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
	return apply_filters( 'ctfw_customize_defaults', array() );
}

/*******************************************
 * SANITIZATION
 *******************************************/

/**
 * Sanitize Radio Choices
 *
 * Check if input matches choices given and if not use default value when empty value not permitted.
 *
 * @since 1.1.4
 * @param string $setting The setting being sanitized, as provided in defaults array
 * @param string $input The user-entered value
 * @param array $choices Valid choices to check against
 * @return string Sanitized value
 */
function ctfw_customize_sanitize_radio( $setting, $input, $choices ) {

	// Default values
	$defaults = ctfw_customize_defaults();

	// Not valid choice; use default if empty value not permitted
	if ( ! isset( $choices[$input] ) && ! empty( $defaults[$setting]['no_empty'] ) ) {
		$output = $defaults[$setting]['value'];
	} else {
		$output = $input; // valid choice
	}

	// Return sanitized, filterable
	return apply_filters( 'ctfw_customize_sanitize_radio', $output, $setting, $input, $choices );

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