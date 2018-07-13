<?php
/**
 * Background Functions
 *
 * Functions to help theme show and Customizer manage background images.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2018, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*********************************************
 * PRESET BACKGROUNDS
 *********************************************/

/**
 * Get sanitized background presets
 *
 * Sanitize and return presets added via add_theme_support( 'ctfw-preset-backgrounds', array() );
 *
 * @since 0.9
 * @return array Preset background images configuration
 */
function ctfw_background_image_presets() {

	$backgrounds_clean = array();

	// Theme supports this?
	$support = get_theme_support( 'ctfw-preset-backgrounds' );
	if ( ! empty( $support[0] ) ) {

		$backgrounds = apply_filters( 'ctfw_background_image_presets_raw', $support[0] ); // filter before cleaning

		// Fill, clean and set defaults to prevent errors elsewhere
		foreach ( $backgrounds as $file => $data ) {

			if ( ! empty( $data['thumb'] ) ) {

				// Thumbnail
				$backgrounds_clean[$file]['thumb'] 		= $data['thumb'];

				// WordPress core "Presets" <select> option
				$backgrounds_clean[$file]['preset'] 	= isset( $data['preset'] ) && in_array( $data['preset'], array( 'default', 'fill', 'fit', 'repeat', 'custom' ) ) ? $data['preset'] : '';

				// Is it colorable?
				$backgrounds_clean[$file]['colorable'] 	= ! empty( $data['colorable'] ) ? true : false;

				// Also add absolute URL's (theme customizer uses)
				$backgrounds_clean[$file]['url'] = ! empty( $data['url'] ) ? $data['url'] : ctfw_background_image_preset_url( $file );
				$backgrounds_clean[$file]['thumb_url'] = ! empty( $data['thumb_url'] ) ? $data['thumb_url'] : ctfw_background_image_preset_url( $data['thumb'] );

			}

		}

	}

	// Return filterable
	return apply_filters( 'ctfw_background_image_presets', $backgrounds_clean );

}

/**
 * Get preset background URLs
 *
 * Returns array of absolute URLs. Handy for Theme Customizer input.
 *
 * @since 0.9
 * @return array Absolute URL's for all background presets.
 */
function ctfw_background_image_preset_urls() {

	$backgrounds = ctfw_background_image_presets();

	$background_urls = array();

	while( list( $filename ) = each( $backgrounds ) ) {

		$url = ctfw_background_image_preset_url( $filename );

		if ( $url ) {
			$background_urls[] = $url;
		}

	}

	return apply_filters( 'ctfw_background_image_preset_urls', $background_urls );

}

/**
 * Get preset background URL (single)
 *
 * Return preset background image URL based on filename.
 *
 * @since 0.9
 * @param string $filename File name of background image
 * @return string Absolute URL for single background image preset
 */
function ctfw_background_image_preset_url( $filename ) {

	$url = get_theme_file_uri( CTFW_THEME_BG_DIR . '/' . $filename );

	return apply_filters( 'ctfw_background_image_preset_url', $url );

}

/**
 * First preset background's URL
 *
 * Handy for using as default with add_theme_support( 'custom-background', array() );
 *
 * @since 0.9
 * @return string URL of firest preset background
 */
function ctfw_background_image_first_preset_url() {

	$first_preset_filename = ctfw_background_image_first_preset_filename();

	$url = ctfw_background_image_preset_url( $first_preset_filename );

	return apply_filters( 'ctfw_background_image_first_preset_url', $url );

}

/**
 * First preset background's filename
 *
 * @since 1.4.1
 * @return string URL of firest preset background
 */
function ctfw_background_image_first_preset_filename() {

	$filename = key( ctfw_background_image_presets() );

	return apply_filters( 'ctfw_background_image_first_preset_url', $filename );

}

