/**
 * Maps JavaScript
 */

jQuery( document ).ready( function( $ ) {

	// Loop map elements
	$( '.ctfw-google-map' ).each( function() {

		var id, lat, lng, type, zoom, latlng, map_type, map, image, marker;

		// Get map data from element attributes
		id = $( this ).attr( 'id' );
		lat = $( this ).data( 'ctfw-map-lat' );
		lng = $( this ).data( 'ctfw-map-lng' );
		type = $( this ).data( 'ctfw-map-type' );
		zoom = $( this ).data( 'ctfw-map-zoom' );
		marker = $( this ).data( 'ctfw-map-marker' );
		center_resize = $( this ).data( 'ctfw-map-center-resize' );
		callback_loaded = $( this ).data( 'ctfw-map-callback-loaded' );
		callback_resize = $( this ).data( 'ctfw-map-callback-resize' );

		// Map being used? Have coordinates?
		if ( $( '#' + id ).length && lat && lng ) {

			// Location Latitude / Longitude
			latlng = new google.maps.LatLng( lat, lng );

			// Map Type
			map_type = google.maps.MapTypeId.HYBRID;
			if ( type == 'ROADMAP' ) {
				map_type = google.maps.MapTypeId.ROADMAP;
			} else if ( type == 'SATELLITE' ) {
				map_type = google.maps.MapTypeId.SATELLITE;
			} else if ( type == 'TERRAIN' ) {
				map_type = google.maps.MapTypeId.TERRAIN;
			}

			// Zoom Default
			if ( ! zoom ) {
				zoom = 14;
			}

			// Default Styles
			// Hide business name labels
			styles = [{
				featureType: "poi",
				stylers: [{
					visibility: "off"
				}]
			}]

			// Custom Styles
			// Apply globally if ctfw_map_styles is defined
			if ( typeof ctfw_map_styles !== 'undefined' ) {
				styles = ctfw_map_styles;
			}

			// Load Map
			map = new google.maps.Map( document.getElementById( id ), {
				zoom: parseInt( zoom ),
				mapTypeId: map_type, // ROADMAP, SATELLITE, HYBRID or TERRAIN
				disableDefaultUI: true, // remove map controls
				scrollwheel: false,
				draggable: false, // this can catch on mobile page touch-scrolling
				disableDoubleClickZoom: true,
				center: latlng,
				styles: styles,
			} );

			// Using marker?
			if ( marker ) {

				// Custom Marker
				image = new google.maps.MarkerImage( ctfw_maps.icon,
					new google.maps.Size( 26, 26 ),
					new google.maps.Point( 0,0 ),
					new google.maps.Point( 13, 26 ) );
				marker = new google.maps.Marker( {
					position: latlng,
					map: map,
					clickable: false,
					icon: image
				} );

			}

			// Store map object in data attribute so can manipulate the instance later
			// Useful for adding custom styles, panning, etc.
			// var map = $( 'element' ).data( 'ctfw-map' );
			$( this ).data( 'ctfw-map', map );
			$( this ).data( 'ctfw-map-latlng', latlng );

			// After load callback
			if ( callback_loaded ) {
				window[callback_loaded](); // run function
			}

			// On window resize
			if ( center_resize || callback_resize ) {

				//google.maps.event.addDomListener( window, 'resize', function() {
				$( window ).resize( function() {

					// Centered latitude/longitude on window resize
					if ( center_resize ) {
						map.setCenter( latlng );
					}

					// On resize callback
					if ( callback_resize ) {
						window[callback_resize](); // run function
					}

				} );

			}

		}

	} );

} );
