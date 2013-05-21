<?php
/**
 * Google Maps Functions
 */

/**
 * Display Google Map
 *
 * Only latitude and longitude are required.
 */

function ctc_google_map( $options = false ) {

	$html = '';
	
	if ( ! empty( $options['latitude'] ) && ! empty( $options['longitude'] ) ) {

		// Enqueue map scripts to handle Google Maps init
		// this way the scripts are loaded only when feature is used, not on every page
		wp_enqueue_script( 'google-maps', ctc_current_protocol() . '://maps.googleapis.com/maps/api/js?sensor=false', false, null ); // no version, generic name to share w/plugins
		wp_enqueue_script( 'ctc-fw-maps', ctc_theme_url( CTC_FW_JS_DIR . '/maps.js' ), array( 'jquery', 'google-maps' ), CTC_FW_VERSION ); // bust cache on theme update

		// Type and zoom are optional
		$options['type'] = isset( $options['type'] ) ? strtoupper( $options['type'] ) : '';
		$options['zoom'] = isset( $options['zoom'] ) ? (int) $options['zoom'] : '';

		// Height percentage of width?
		$map_style = '';
		if ( ! empty( $options['height_percent'] ) ) {
			$options['height_percent'] = str_replace( '%', '', $options['height_percent'] );
			$map_style = ' style="padding-bottom: ' . $options['height_percent'] . '%;"';
		}

		// Unique ID for this map so can have multiple maps on a page
		$google_map_id_num = rand( 1000000, 9999999 );
		$google_map_id = 'ctc-google-map-' . $google_map_id_num;

		// Data Attributes
		$data_latitude = esc_attr( $options['latitude'] );
		$data_longitude = esc_attr( $options['longitude'] );
		$data_type = esc_attr( $options['type'] );
		$data_zoom = esc_attr( $options['zoom'] );

$html = <<< HTML
<div class="ctc-google-map-container">
	<div id="$google_map_id" class="ctc-google-map" data-ctc-map-lat="$data_latitude" data-ctc-map-lng="$data_longitude" data-ctc-map-type="$data_type" data-ctc-map-zoom="$data_zoom"$map_style></div>
</div>
HTML;

	} else if ( ! empty( $options['show_error'] ) ) {
		$html = __( '<p><b>Google Map Error:</b> <i>latitude</i> and <i>longitude</i> attributes are required. See documentation for help.</p>', 'ct-framework' );
	}

	return apply_filters( 'ctc_google_map', $html, $options );

}


/**
 * Build Google Maps directions URL from address
 */

function ctc_directions_url( $address ) {

	$directions_url = '';

	if ( $address ) {

		// Convert address to one line (replace newlines with commas)
		$directions_address = ctc_address_one_line( $address );

		// Build URL to Google Maps
		$directions_url = ctc_current_protocol() . '://maps.google.com/maps?f=d&q=' . urlencode( $directions_address );

	}

	return apply_filters( 'ctc_directions_url', $directions_url, $address );

}