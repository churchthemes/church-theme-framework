<?php
/**
 * Google Maps Functions
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

/*******************************************
 * JAVASCRIPT MAP
 *******************************************/

/**
 * Display Google Map
 *
 * Only latitude and longitude are required.
 *
 * @since 0.9
 * @param array $options Options for showing map
 * @return string Google Maps HTML
 */
function ctfw_google_map( $options = false ) {

	$html = '';

	if ( ! empty( $options['latitude'] ) && ! empty( $options['longitude'] ) ) {

		// Defaults
		$options['type'] = isset( $options['type'] ) ? strtoupper( $options['type'] ) : '';
		$options['zoom'] = isset( $options['zoom'] ) ? (int) $options['zoom'] : '';
		$options['container'] = isset( $options['container'] ) ? $options['container'] : true; // default true
		$options['responsive'] = isset( $options['responsive'] ) ? $options['responsive'] : true; // default true
		$options['marker'] = isset( $options['marker'] ) ? $options['marker'] : true; // default true
		$options['center_resize'] = isset( $options['center_resize'] ) ? $options['center_resize'] : true; // default true
		$options['callback_loaded'] = isset( $options['callback_loaded'] ) ? $options['callback_loaded'] : '';
		$options['callback_resize'] = isset( $options['callback_resize'] ) ? $options['callback_resize'] : '';

		// Unique ID for this map so can have multiple maps on a page
		// Can pass map_id as option for custom ID
		$google_map_id_default = 'ctfw-google-map-' . rand( 1000000, 9999999 );
		$google_map_id = isset( $options['canvas_id'] ) ? $options['canvas_id'] : $google_map_id_default;

		// Classes for map canvas element
		$canvas_classes = array( 'ctfw-google-map' );

			if ( ! empty( $options['canvas_class'] ) ) {
				$canvas_classes[] = $options['canvas_class'];
			}

			if ( $options['responsive'] ) {
				$canvas_classes[] = 'ctfw-google-map-responsive';
			}

			$canvas_classes = implode( ' ', $canvas_classes );

		// Height percentage of width?
		$map_style = '';
		if ( ! empty( $options['height_percent'] ) ) {
			$options['height_percent'] = str_replace( '%', '', $options['height_percent'] );
			$map_style = ' style="padding-bottom: ' . $options['height_percent'] . '%;"';
		}

		// Data Attributes
		$data_latitude = esc_attr( $options['latitude'] );
		$data_longitude = esc_attr( $options['longitude'] );
		$data_type = esc_attr( $options['type'] );
		$data_zoom = esc_attr( $options['zoom'] );
		$data_marker = esc_attr( $options['marker'] );
		$data_center_resize = esc_attr( $options['center_resize'] );
		$data_callback_loaded = esc_attr( $options['callback_loaded'] );
		$data_callback_resize = esc_attr( $options['callback_resize'] );

		// Map canvas tag with attributes
		$html = '<div id="' . esc_attr( $google_map_id ) . '" class="' . $canvas_classes . '" data-ctfw-map-lat="' . esc_attr( $data_latitude ) . '" data-ctfw-map-lng="' . esc_attr( $data_longitude ) . '" data-ctfw-map-type="' . esc_attr( $data_type ) . '" data-ctfw-map-zoom="' . esc_attr( $data_zoom ) . '" data-ctfw-map-marker="' . esc_attr( $data_marker ) . '" data-ctfw-map-center-resize="' . esc_attr( $data_center_resize ) . '" data-ctfw-map-callback-loaded="' . esc_attr( $data_callback_loaded ) . '" data-ctfw-map-callback-resize="' . esc_attr( $data_callback_resize ) . '"' . $map_style . '></div>';

		// Use container?
		if ( $options['container'] ) {
			$html = '<div class="ctfw-google-map-container">' . $html . '</div>';
		}

		// Enqueue map scripts to handle Google Maps init
		// this way the scripts are loaded only when feature is used, not on every page
		wp_enqueue_script( 'google-maps', '//maps.googleapis.com/maps/api/js?key=' . ctfw_google_maps_api_key(), false, null ); // no version, generic name to share w/plugins
		wp_enqueue_script( 'ctfw-maps', get_theme_file_uri( CTFW_JS_DIR . '/maps.js' ), array( 'jquery', 'google-maps' ), CTFW_VERSION ); // bust cache on theme update

	} elseif ( ! empty( $options['show_error'] ) ) {
		$html = __( '<p><b>Google Map Error:</b> <i>latitude</i> and <i>longitude</i> attributes are required. See documentation for help.</p>', 'church-theme-framework' );
	}

	return apply_filters( 'ctfw_google_map', $html, $options );

}

/*******************************************
 * IMAGE MAP
 *******************************************/

/**
 * Google Map Image Tag
 *
 * Return a HiDPI-ready Google Map <img> URL.
 *
 * @since 1.0.9
 * @param array $options Options for showing map
 * @return string Google Maps HTML
 */
function ctfw_google_map_image( $options = array() ) {

	// Default arguments
	$options = wp_parse_args( $options, apply_filters( 'ctfw_google_map_image_options', array(
		'latitude'		=> 31.768319, // Jerusalem
		'longitude'		=> 35.213710,
		'type'			=> 'road',
		'zoom'			=> '14',
		'width'			=> 480,
		'height'		=> 320,
		'alt'			=> '',
		'scale'			=> 2,			// double for HiDPI devices
		'marker_color'	=> 'f2f2f2'	// hex without # (light gray)
	) ) );

	// Extract options
	extract( $options, EXTR_SKIP );

	// Clean options
	$type = strtolower( $type );
	$marker_color = str_replace( '#', '', $marker_color );

	// Start arguments
	$map_args = array();

	// Required arguments
	$map_args['size'] = $width . 'x' . $height;
	$map_args['center'] = $latitude . ',' . $longitude;
	$map_args['scale'] = $scale; // double for Retina
	//$map_args['markers'] = 'color:0x' . $marker_color . '|' . $map_args['center'];
	$map_args['markers'] = 'color:0x' . $marker_color . '%7C' . $map_args['center']; // HTML5-valid: http://bit.ly/1xfv8yA
	$map_args['key'] = ctfw_google_maps_api_key(); // from Church Content plugin settings

	// Have zoom?
	if ( ! empty( $zoom ) ) {
		$map_args['zoom'] = $zoom;
	}

	// Have type?
	if ( ! empty( $type ) ) {
		$map_args['maptype'] = strtolower( $type );
	}

	// Sensor last
	$map_args['sensor'] = 'false';

	// Filter map arguments
	$map_args = apply_filters( 'ctfw_google_map_image_args', $map_args );

	// Add arguments to URL
	$map_url = add_query_arg( $map_args, '//maps.googleapis.com/maps/api/staticmap' );

	// Filter URL
	$map_args = apply_filters( 'ctfw_google_map_image_url', $map_args );

	// Build image tag
	$img_tag = '<img src="' . esc_url( $map_url ) . '" class="ctfw-google-map-image" alt="' . esc_attr( $alt ) . '" width="' . esc_attr( $width ) . '" height="' . esc_attr( $height ) . '">';

	// Return
	return apply_filters( 'ctfw_google_map_image', $img_tag );

}

/*******************************************
 * HELPERS
 *******************************************/

/**
 * Build Google Maps directions URL from address
 *
 * @since 0.9
 * @param string $address Address to get directions URL for
 * @return string URL for directions on Google Maps
 */
function ctfw_directions_url( $address ) {

	$directions_url = '';

	if ( $address ) {

		// Convert address to one line (replace newlines with commas)
		$directions_address = ctfw_address_one_line( $address );

		// Build URL to Google Maps
		$directions_url = 'https://www.google.com/maps/dir//' . urlencode( $directions_address ) . '/'; // works with new and old maps

	}

	return apply_filters( 'ctfw_directions_url', $directions_url, $address );

}

/**
 * Get API Key from Church Content settings
 *
 * It's set in Church Content settings because "Get From Address" button needs it.
 * Themes can use the key from the plugin in this way.
 *
 * @since 1.8
 * @return string Google Maps API Key
 */
function ctfw_google_maps_api_key() {

	$key = '';

	// Make sure the plugin's function is available
	if ( function_exists( 'ctc_setting' ) ) {
		$key = ctc_setting( 'google_maps_api_key' );
	}

	return apply_filters( 'ctfw_google_maps_api_key', $key );

}
