/**
 * Maps JavaScript
 */

jQuery( document ).ready( function( $ ) {

	// Loop map elements
	$( '.ctfw-google-map' ).each( function() {

		var id, lat, lng, type, zoom, latlng, map_type, map, image, marker;

		// Get map data from element attributes
		id = $( this ).attr( 'id' );
		lat = $( this ).attr( 'data-ctfw-map-lat' );
		lng = $( this ).attr( 'data-ctfw-map-lng' );
		type = $( this ).attr( 'data-ctfw-map-type' );
		zoom = $( this ).attr( 'data-ctfw-map-zoom' );
		marker = $( this ).attr( 'data-ctfw-map-marker' );

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

			// Keep marker centered on window resize
			google.maps.event.addDomListener( window, 'resize', function() {
				map.setCenter( latlng );
			} );

			// Store map object in data attribute so can manipulate the instance later
			// Useful for adding custom styles, panning, etc.
			// var map = $( 'element' ).data( 'ctfw-map' );
			$( this ).data( 'ctfw-map', map );
			$( this ).data( 'ctfw-map-latlng', latlng );

		}

	} );

} );
