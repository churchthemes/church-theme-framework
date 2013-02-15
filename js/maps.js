/**
 * Maps JavaScript
 */

// Document is loaded...
jQuery(document).ready(function($) {

	// Loop map elements
	$('.ctc-google-map').each(function() {

		// Get map data from element attributes
		var id = $(this).attr('id');
		var lat = $(this).attr('data-ctc-map-lat');
		var lng = $(this).attr('data-ctc-map-lng');
		var type = $(this).attr('data-ctc-map-type');
		var zoom = $(this).attr('data-ctc-map-zoom');

		// Map being used? Have coordinates?
		if ($('#' + id).length && lat && lng) {

			// Location Latitude / Longitude
			var latlng = new google.maps.LatLng(lat, lng);

			// Map Type
			var map_type = google.maps.MapTypeId.HYBRID;
			if (type == 'ROADMAP') {
				map_type = google.maps.MapTypeId.ROADMAP;
			} else if (type == 'SATELLITE') {
				map_type = google.maps.MapTypeId.SATELLITE;
			} else if (type == 'TERRAIN') {
				map_type = google.maps.MapTypeId.TERRAIN;
			}
			
			// Zoom
			zoom = zoom ? zoom : 14; // default
			
			// Load the Map
			var map = new google.maps.Map(document.getElementById(id), {
				zoom: parseInt(zoom),
				mapTypeId: map_type, // ROADMAP, SATELLITE, HYBRID or TERRAIN
				disableDefaultUI: true, // remove map controls
				center: latlng,
				styles: [{ // hide business name labels
					featureType: "poi",
					stylers: [{
						visibility: "off"
					}]
				}]
			});

			// Custom Marker
			var image = new google.maps.MarkerImage(ctc_wp.gmaps_icon,
				new google.maps.Size(26, 26),
				new google.maps.Point(0,0),
				new google.maps.Point(13, 26));
			var shadow = new google.maps.MarkerImage(ctc_wp.gmaps_icon_shadow,
				new google.maps.Size(40, 26),
				new google.maps.Point(0,0),
				new google.maps.Point(13, 26));
			var marker = new google.maps.Marker({
				position: latlng,
				map: map,
				clickable: false,
				icon: image,
				shadow: shadow
			});
			
			// Keep marker centered on window resize
			google.maps.event.addDomListener(window, 'resize', function() {
				map.setCenter(latlng);
			});
			
			// Maps in hidden elements (Accordion, Tabs) must be re-initialized for correct size
			/*
			$($('#' + id)).parents('.ctc-tabber, .ctc-accordion').click(function() {
				ctc_init_google_map(id, lat, lng, type, zoom);
			});
			*/
		
		}
		
	});

});
