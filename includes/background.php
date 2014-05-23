<?php
/**
 * Background Functions
 *
 * Functions to help theme show and Customizer manage background images.
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

/*********************************************
 * CUSTOM BACKGROUND
 *********************************************/

/**
 * Remove Custom Background from Admin Menu
 *
 * Use add_theme_support( 'ctfw-force-customizer-background' ) to force users to edit
 * the custom background via the Customizer.
 *
 * @since 0.9
 */
function ctfw_admin_remove_menu_pages() {

	global $menu;

	// If theme supports this
	if ( current_theme_supports( 'ctfw-force-customizer-background' ) ) {

		// Remove Background
		// Encourage access by Theme Customizer since it has Fullscreen and Preset enhancements
		remove_submenu_page( 'themes.php', 'custom-background' );

	}

}

add_action( 'admin_menu', 'ctfw_admin_remove_menu_pages', 11 ); // after add theme support for background

/**
 * Redirect Custom Background to Theme Customizer
 *
 * Use add_theme_support( 'ctfw-force-customizer-background' ) to force users to edit
 * the custom background via the Customiser.
 *
 * @since 0.9
 */
function ctfw_admin_redirect_background() {

	// If theme supports this
	if ( current_theme_supports( 'ctfw-force-customizer-background' ) ) {

		// We're on custom background page
		if ( 'themes.php?page=custom-background' == basename( $_SERVER['REQUEST_URI'] ) ) {

			// Redirect to Theme Customizer
			wp_redirect( admin_url( 'customize.php' ) );
			exit;

		}

	}

}

add_action( 'admin_init', 'ctfw_admin_redirect_background' );

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

				$backgrounds_clean[$file]['thumb'] 		= $data['thumb'];

				$backgrounds_clean[$file]['fullscreen'] = ! empty( $data['fullscreen'] ) ? true : false;
				if ( $backgrounds_clean[$file]['fullscreen'] ) {
					$data['repeat'] = 'no-repeat';
					$data['attachment'] = 'fixed';
					$data['position'] = 'left';
				}

				$backgrounds_clean[$file]['repeat'] 	= isset( $data['repeat'] ) && in_array( $data['repeat'], array( 'no-repeat', 'repeat', 'repeat-x', 'repeat-y' ) ) ? $data['repeat'] : 'no-repeat';

				$backgrounds_clean[$file]['attachment'] = isset( $data['attachment'] ) && in_array( $data['attachment'], array( 'scroll', 'fixed' ) ) ? $data['attachment'] : 'scroll';

				$backgrounds_clean[$file]['position'] 	= isset( $data['position'] ) && in_array( $data['position'], array( 'left', 'center', 'right' ) ) ? $data['position'] : '';

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

	$url = ctfw_theme_url( CTFW_THEME_BG_DIR . '/' . $filename );

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

	$first_preset = key( ctfw_background_image_presets() );

	$url = ctfw_background_image_preset_url( $first_preset );

	return apply_filters( 'ctfw_background_image_first_preset_url', $url );

}
