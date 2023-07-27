/**
 * Maps JavaScript
 */

function ctfw_load_maps() {

	// Loop map elements
	jQuery('.ctfw-google-map').each(function () {

		var id, lat, lng, type, zoom, latlng, map_type, map, marker, marker_image, marker_image_size;

		// Get map data from element attributes
		id = jQuery(this).attr('id');
		lat = jQuery(this).data('ctfw-map-lat');
		lng = jQuery(this).data('ctfw-map-lng');
		type = jQuery(this).data('ctfw-map-type');
		zoom = jQuery(this).data('ctfw-map-zoom');
		marker = jQuery(this).data('ctfw-map-marker');
		center_resize = jQuery(this).data('ctfw-map-center-resize');
		callback_loaded = jQuery(this).data('ctfw-map-callback-loaded');
		callback_resize = jQuery(this).data('ctfw-map-callback-resize');

		// Map being used? Have coordinates?
		if (jQuery('#' + id).length && lat && lng) {

			// Location Latitude / Longitude
			latlng = new google.maps.LatLng(lat, lng);

			// Map Type
			map_type = google.maps.MapTypeId.HYBRID;
			if (type == 'ROADMAP') {
				map_type = google.maps.MapTypeId.ROADMAP;
			} else if (type == 'SATELLITE') {
				map_type = google.maps.MapTypeId.SATELLITE;
			} else if (type == 'TERRAIN') {
				map_type = google.maps.MapTypeId.TERRAIN;
			}

			// Zoom Default
			if (!zoom) {
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
			if (typeof ctfw_map_styles !== 'undefined') {
				styles = ctfw_map_styles;
			}

			// Load Map
			map = new google.maps.Map(document.getElementById(id), {
				zoom: parseInt(zoom),
				mapTypeId: map_type, // ROADMAP, SATELLITE, HYBRID or TERRAIN
				disableDefaultUI: true, // remove map controls
				scrollwheel: false,
				draggable: false, // this can catch on mobile page touch-scrolling
				disableDoubleClickZoom: true,
				center: latlng,
				styles: styles,
			});

			// Custom Marker
			if (marker && typeof ctfw_map_marker_image !== 'undefined' && ctfw_map_marker_image.length) {

				// Global marker image
				marker_icon = ctfw_map_marker_image;

				// HiDPI/Retina?
				if (window.devicePixelRatio > 1.5 && typeof ctfw_map_marker_image_hidpi !== 'undefined' && typeof ctfw_map_marker_image_width !== 'undefined' && typeof ctfw_map_marker_image_height !== 'undefined') {

					marker_image_size = new google.maps.Size(ctfw_map_marker_image_width, ctfw_map_marker_image_height);

					marker_icon = {
						url: ctfw_map_marker_image_hidpi,
						size: marker_image_size,
						scaledSize: marker_image_size,
					}

				}

				// Add marker
				marker = new google.maps.Marker({
					position: latlng,
					map: map,
					clickable: false,
					icon: marker_icon
				});

			}

			// Trigger browser resize event to correct misplaced marker on Chrome
			setTimeout(function () {
				window.dispatchEvent(new Event('resize'));
			}, 100);
			setTimeout(function () {
				window.dispatchEvent(new Event('resize'));
			}, 1000); // and a second later just in case

			// Store map object in data attribute so can manipulate the instance later
			// Useful for adding custom styles, panning, etc.
			// var map = jQuery( 'element' ).data( 'ctfw-map' );
			jQuery(this).data('ctfw-map', map);
			jQuery(this).data('ctfw-map-latlng', latlng);

			// After load callback
			if (callback_loaded) {
				window[callback_loaded](); // run function
			}

			// On window resize
			if (center_resize || callback_resize) {

				//google.maps.event.addDomListener( window, 'resize', function() {
				jQuery(window).on('resize', function () {

					// Centered latitude/longitude on window resize
					if (center_resize) {

						// Slight delay improve accuracy
						setTimeout(function () {
							map.setCenter(latlng);
						}, 100);

					}

					// On resize callback
					if (callback_resize) {
						window[callback_resize](); // run function
					}

				});

			}

		}

	});

}
