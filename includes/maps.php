<?php

/**
 * Display Google Map
 *
 * Only latitude and longitude are required.
 */

if ( ! function_exists( 'ctc_google_map' ) ) {

	function ctc_google_map( $options = false ) {

		if ( ! empty( $options['latitude'] ) && ! empty( $options['longitude'] ) ) {

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

return <<< HTML
<div class="ctc-google-map-container">
	<div id="$google_map_id" class="ctc-google-map" data-ctc-map-lat="$data_latitude" data-ctc-map-lng="$data_longitude" data-ctc-map-type="$data_type" data-ctc-map-zoom="$data_zoom"$map_style></div>
</div>
HTML;

		} else if ( ! empty( $options['show_error'] ) ) {
			return __( '<p><b>Google Map Error:</b> <i>latitude</i> and <i>longitude</i> attributes are required. See documentation for help.</p>', 'church-theme' );
		}
	
	}
	
}
