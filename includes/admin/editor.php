<?php
/**
 * Admin Content Editor
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2015 - 2017, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.7.2
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*******************************************
 * EDITOR STYLES
 *******************************************/

/**
 * Add editor styles with colors/fonts from Customizer
 *
 * This loads style.css into admin editor and outputs CSS contents from a function
 * in order to render colors and fonts like on front-end (can use same function).
 *
 * style.css, heading_font and body_font are loaded by default so can specify just themename_head_styles()
 *
 * See ctfw_editor_styles_callback() for how Customizer colors/fonts are applied
 *
 * Usage example:
 *
 * add_theme_support( 'ctfw-editor-styles', array(
 * 		'stylesheet'	=> 'style.css', 							// style.css will be used if not specified
 * 		'css_function'	=> 'themename_head_styles',					// function outputting dynamic CSS in <head> (exclude <style> tag)
 * 		'body_function'	=> 'themename_body_classes',				// function returning array of classes to add to <body>
 *		'fonts'			=> array( 'heading_font', 'body_font' ),	// Customizer setting names for Google Fonts
 *		'font_subsets'	=> 'font_subsets',							// Customizer setting name for Google Font subsets
 * ) );
 *
 * This is based on http://wordpress.stackexchange.com/a/120849
 *
 * @since 1.7.2
 */
function ctfw_editor_styles() {

	// Not on admin
	// add_editor_style() only runs on admin
	if ( ! is_admin() ) {
		return;
	}

	// Theme support?
	if ( ! current_theme_supports( 'ctfw-editor-styles' ) ) {
		return;
	}

	// Get arguments
	$support = get_theme_support( 'ctfw-editor-styles' );
	$args = $support[0];

	// Defaults
	$args = wp_parse_args( $args, array(
		'stylesheet'	=> 'style.css',								// style.css will be used if not specified
 		'css_function'	=> '',										// function outputting dynamic CSS in <head> (exclude <style> tag)
 		'body_function'	=> '',										// function returning array of classes to add to <body>
 		'fonts'			=> array( 'heading_font', 'body_font' ),	// Customizer setting names for Google Fonts
 		'font_subsets'	=> 'font_subsets',							// Customizer setting name for Google Font subsets
	) );

	// Load Google Fonts
	if ( $args['fonts'] ) {

		// Get fonts from Customizer settings
		$fonts = array();
		foreach ( $args['fonts'] as $font_setting ) {

			$font = ctfw_customization( $font_setting );

			if ( $font ) {
				$fonts[] = $font;
			}

		}

		// Load fonts
		if ( $fonts ) {

			$google_fonts_url = ctfw_google_fonts_style_url( $fonts, ctfw_customization( $args['font_subsets'] ) );

			if ( $google_fonts_url ) {
				$font_url = str_replace( ',', '%2C', $google_fonts_url );
				add_editor_style( $font_url );
			}

		}

	}

	// Load theme stylesheet
	add_editor_style(
		array(
			$args['stylesheet'],
			add_query_arg( 'action', 'ctfw_editor_styles', admin_url( 'admin-ajax.php' ) ),
		)
	);

	// Apply Customizer fonts and colors to editor
	if ( $args['css_function'] && function_exists( $args['css_function'] ) ) {

		// Output as CSS
		add_action( 'wp_ajax_ctfw_editor_styles', 'ctfw_editor_styles_header' );
		add_action( 'wp_ajax_nopriv_ctfw_editor_styles', 'ctfw_editor_styles_header' );

		// Call CSS output function
		add_action( 'wp_ajax_ctfw_editor_styles', $args['css_function'] );
		add_action( 'wp_ajax_nopriv_ctfw_editor_styles', $args['css_function'] );

	}

	// Apply body classes to editor
	if ( $args['body_function'] ) {
		add_filter( 'tiny_mce_before_init', 'ctfw_add_editor_body_classes' );
    }

}

add_action( 'after_setup_theme', 'ctfw_editor_styles', 11 );

/**
 * Output editor styles CSS header
 *
 * @since 1.7.2
 */
function ctfw_editor_styles_header() {
	header( 'Content-type: text/css; charset: UTF-8' );
}

/**
 * Add <b>body</b> classes to editor
 *
 * @since 1.9.3
 * @param array $mce Editor data
 * @return array Editor data with extra body classes
 */
function ctfw_add_editor_body_classes( $mce ) {

	// Get function name when 'ctfw-editor-styles' supported
	$support = get_theme_support( 'ctfw-editor-styles' );
	$body_function = isset( $support[0]['body_function'] ) ? $support[0]['body_function'] : false;

	// Function exists?
	if ( $body_function && function_exists( $body_function ) ) {

		// Get classes array
		$body_classes = call_user_func( $body_function );

		// Have classes
		if ( $body_classes ) {

			// Convert to string
			$body_classes = implode( ' ', $body_classes );

			// Append to existing classes string
			$mce['body_class'] .= ' ' . $body_classes;

		}

	}

	return $mce;

}
