<?php
/**
 * Admin Content Editor
 *
 * @package    Church_Theme_Framework
 * @subpackage Admin
 * @copyright  Copyright (c) 2015 - 2018, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      1.7.2
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/*******************************************
 * EDITOR STYLES
 *******************************************/

/**
 * Add editor styles with colors/fonts from Customizer
 *
 * For classic editor, this loads style.css into admin editor and outputs CSS contents from a function
 * in order to render colors and fonts like on front-end (can use same function).
 *
 * style.css, heading_font and body_font are loaded by default so can specify just themename_head_styles()
 *
 * For Gutenberg, specify a stylesheet for the block editor.
 *
 * See ctfw_editor_styles_callback() for how Customizer colors/fonts are applied
 *
 * Usage example:
 *
 * add_theme_support( 'ctfw-editor-styles', array(
 * 		'stylesheet'		=> 'style.css', 							// For Classic Editor. style.css will be used if not specified.
 * 		'block_stylesheet'	=> 'css/admin/block-editor.css', 			// For Gutenberg Editor to style block controls.
 * 		'css_function'		=> 'themename_head_styles',					// function outputting dynamic CSS in <head> (exclude <style> tag)
 * 		'body_function'		=> 'themename_body_classes',				// function returning array of classes to add to <body>
 *		'fonts'				=> array( 'heading_font', 'body_font' ),	// Customizer setting names for Google Fonts
 *		'font_subsets'		=> 'font_subsets',							// Customizer setting name for Google Font subsets
 * ) );
 *
 * This is based on http://wordpress.stackexchange.com/a/120849.
 *
 * @since 1.7.2
 */
function ctfw_editor_styles() {

	// Not on admin.
	// add_editor_style() only runs on admin.
	if ( ! is_admin() ) {
		return;
	}

	// Theme support?
	if ( ! current_theme_supports( 'ctfw-editor-styles' ) ) {
		return;
	}

	// Get arguments.
	$support = get_theme_support( 'ctfw-editor-styles' );
	$args = $support[0];

	// Defaults.
	$args = wp_parse_args( $args, array(
		'stylesheet'		=> 'style.css',								// For Classic Editor. style.css will be used if not specified.
		'block_stylesheet'	=> '',										// For Gutenberg Editor to style block controls.
 		'css_function'		=> '',										// function outputting dynamic CSS in <head> (exclude <style> tag).
 		'body_function'		=> '',										// function returning array of classes to add to <body>.
 		'fonts'				=> array( 'heading_font', 'body_font' ),	// Customizer setting names for Google Fonts.
 		'font_subsets'		=> 'font_subsets',							// Customizer setting name for Google Font subsets.
	) );

	// Load Google Fonts.
	$google_fonts_url = ctfw_editor_get_google_fonts_url();
	if ( $google_fonts_url ) {

		// Classic editor.
		add_editor_style( $google_fonts_url );

		// Gutenberg editor.
		add_action( 'enqueue_block_editor_assets', 'ctfw_enqueue_block_editor_fonts' );

	}

	// Load theme stylesheet for Classic Editor.
	add_editor_style(
		array(
			$args['stylesheet'],
			add_query_arg( 'action', 'ctfw_editor_styles', admin_url( 'admin-ajax.php' ) ),
		)
	);

	// Load Gutenberg styles for Gutenberg editor.
	if ( ! empty( $args['stylesheet'] ) ) {
		add_action( 'enqueue_block_editor_assets', 'ctfw_enqueue_block_editor_styles' );
	}

	// Apply Customizer fonts and colors CSS to editor.
	if ( $args['css_function'] && function_exists( $args['css_function'] ) ) {

		// Classic editor.

			// Output as CSS.
			add_action( 'wp_ajax_ctfw_editor_styles', 'ctfw_editor_styles_header' );
			add_action( 'wp_ajax_nopriv_ctfw_editor_styles', 'ctfw_editor_styles_header' );

			// Call CSS output function.
			add_action( 'wp_ajax_ctfw_editor_styles', $args['css_function'] );
			add_action( 'wp_ajax_nopriv_ctfw_editor_styles', $args['css_function'] );

		// Gutenberg editor.
		add_action( 'admin_init', 'ctfw_block_editor_styles', 11 );

	}

	// Apply body classes to editor.
	if ( $args['body_function'] ) {

		// Apply body classes to Classic Editor.
		add_filter( 'tiny_mce_before_init', 'ctfw_add_editor_body_classes' );

	   	// Add body classes to Block Editor (Gutenberg).
		add_filter( 'admin_body_class', 'ctfw_add_block_editor_body_classes' );

	}

}

add_action( 'after_setup_theme', 'ctfw_editor_styles', 11 );

/**
 * Get Google Fonts URL for editors.
 *
 * @since  2.4
 */
function ctfw_editor_get_google_fonts_url() {

	$font_url = '';

	// Get font support.
	$support = get_theme_support( 'ctfw-editor-styles' );
	$font_settings = isset( $support[0]['fonts'] ) ? $support[0]['fonts'] : false;
	$font_subsets_setting = isset( $support[0]['font_subsets'] ) ? $support[0]['font_subsets'] : false;

	// Have font settings.
	if ( $font_settings ) {

		// Get fonts from Customizer settings.
		$fonts = array();
		foreach ( $font_settings as $font_setting ) {

			$font = ctfw_customization( $font_setting );

			if ( $font ) {
				$fonts[] = $font;
			}

		}

		// Get Google Fonts URL.
		if ( $fonts ) {

			$google_fonts_url = ctfw_google_fonts_style_url( $fonts, ctfw_customization( $font_subsets_setting ) );

			if ( $google_fonts_url ) {
				$font_url = str_replace( ',', '%2C', $google_fonts_url );
			}

		}

	}

	return apply_filters( 'ctfw_editor_get_google_fonts_url', $font_url );

}

/**
 * Output editor styles CSS header (Classic Editor).
 *
 * @since 1.7.2
 */
function ctfw_editor_styles_header() {
	header( 'Content-type: text/css; charset: UTF-8' );
}

/**
 * Output editor styles CSS for Gutenberg editor.
 *
 * @since 2.4
 */
function ctfw_block_editor_styles() {

	// Gutenberg editor in use.
	//if ( ctfw_is_gutenberg_editor() ) {

		// Get callback function.
		$support = get_theme_support( 'ctfw-editor-styles' );
		$css_function = isset( $support[0]['css_function'] ) ? $support[0]['css_function'] : false;

		// Run function to output editor styles.
		if ( $css_function && function_exists( $css_function ) ) {
			add_action( 'admin_head', $css_function );
		}

	//}

}

/**
 * Get <body> classes for editors (Classic and Gutenberg).
 *
 * @since 2.4
 * @return array Body classes
 */
function ctfw_get_editor_body_classes() {

	$classes = '';

	// Get function name when 'ctfw-editor-styles' supported.
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
			$classes .= ' ' . $body_classes;

		}

	}

	return $classes;

}

/**
 * Add <body> classes to Classic editor.
 *
 * This is called by ctfw_editor_styles().
 *
 * @since 2.0
 * @param array $mce Editor data
 * @return array Editor data with extra body classes
 */
function ctfw_add_editor_body_classes( $mce ) {

	// Get body classes.
	$body_classes = ctfw_get_editor_body_classes();

	// Have classes.
	if ( $body_classes ) {

		// Append to existing classes string
		$mce['body_class'] = isset( $mce['body_class'] ) ? $mce['body_class'] : '';
		$mce['body_class'] .= ' ' . $body_classes;

	}

	return $mce;

}

/**
 * Add <body> classes to Gutenberg editor.
 *
 * This is called by ctfw_editor_styles().
 *
 * @since 2.4
 * @param string $classes Existing body classes.
 * @return string Body classes, appended.
 */
function ctfw_add_block_editor_body_classes( $classes ) {

	// Gutenberg editor in use.
	if ( ctfw_is_gutenberg_editor() ) {

		// Get body classes.
		$body_classes = ctfw_get_editor_body_classes();

		// Have classes.
		if ( $body_classes ) {

			// Append to existing classes string.
			$classes .= ' ' . $body_classes;

		}

	}

	return $classes;

}

/**
 * Enqueue Gutenberg editor block styles.
 *
 * This is called by ctfw_editor_styles() via enqueue_block_editor_assets action.
 *
 * @since 2.4
 */
function ctfw_enqueue_block_editor_styles() {

	// Get path to stylesheet.
	$support = get_theme_support( 'ctfw-editor-styles' );
	$block_stylesheet = isset( $support[0]['block_stylesheet'] ) ? $support[0]['block_stylesheet'] : false;

	if ( $block_stylesheet ) {
		wp_enqueue_style( 'ctfw-block-editor', get_theme_file_uri( $block_stylesheet ), false, CTFW_THEME_VERSION );
	}

}

/**
 * Enqueue Gutenberg editor Google Fonts.
 *
 * This is called by ctfw_editor_styles() via enqueue_block_editor_assets action.
 *
 * @since 2.4
 */
function ctfw_enqueue_block_editor_fonts() {

	// Get Google Fonts URL.
	$google_fonts_url = ctfw_editor_get_google_fonts_url();

	// Have Google Fonts URL.
	if ( $google_fonts_url ) {
		wp_enqueue_style( 'ctfw-block-editor-fonts', get_theme_file_uri( $google_fonts_url ), false, CTFW_THEME_VERSION );
	}

}

/*******************************************
 * HELPERS
 *******************************************/

/**
 * Gutenberg in use on add/edit screen.
 */
function ctfw_is_gutenberg_editor() {

	global $post, $pagenow;

	// Default false.
	$is = false;

	// Editing single post and Gutenberg is available.
	// Using $pagenow instead of get_current_screen() since it's sometimes too early to be available.
	if ( 'post.php' === $pagenow && function_exists( 'gutenberg_can_edit_post' ) ) {

		// Not using classic editor.
		if ( isset( $_GET['classic-editor'] ) ) {
			$is = false;
		}

		// Not able to edit with Gutenberg.
		elseif ( ! gutenberg_can_edit_post( $post ) ) {
			$is = false;
		}

		// Gutenburg running.
		else {
			$is = true;
		}

	}

	return $is;

}
