/**
 * Admin Widgets Page JavaScript
 *
 * Appearance > Widgets and Customizer both use this.
 */

jQuery(document).ready(function ($) {

	/*******************************************
	 * IMAGE FIELD
	 *******************************************/

	// Choose Image
	$('body').on('click', '.ctfw-widget-image-choose', function (event) {

		var value_container, frame;

		// Stop click to URL
		event.preventDefault();

		// Get field value container
		value_container = $(this).parent('.ctfw-widget-value');

		// Media frame
		frame = wp.media({
			title: ctfw_widgets.image_library_title,
			multiple: false,
			library: { type: 'image' },
			button: { text: ctfw_widgets.image_library_button }
		});

		// Open media frame and set current image
		// This information was useful: http://bit.ly/13FATWB
		frame.on('open', function () {

			var image_id, current_attachment;

			image_id = $('.ctfw-widget-image', value_container).val();

			if (image_id) {
				current_attachment = wp.media.attachment(image_id);
				current_attachment.fetch();
				frame.state().get('selection').add(current_attachment);
			}

		}).open(); // do open after binding

		// Set attachment ID and preview on click of "Use in Widget"
		// ( don't do on 'close' so user can cancel )
		frame.on('select', function () {

			var attachments, attachment, attachment_id, attachment_preview_url;

			// Get attachment data
			attachments = frame.state().get('selection').toJSON();
			attachment = attachments[0];

			// An image is selected
			if (typeof attachment != 'undefined') {

				// Get attachment ID
				attachment_id = attachment.id;

				// Get medium size image for preview if exists
				attachment_preview_url = attachment.url; // use full if no medium
				if (typeof attachment.sizes.medium != 'undefined') {
					attachment_preview_url = attachment.sizes.medium.url;
				}

				// Have attachment ID and preview image
				if (attachment_id && attachment_preview_url) {

					// Set attachment ID on hidden input
					// Also trigger change event to make Customizer refresh preview
					$('.ctfw-widget-image', value_container)
						.val(attachment_id)
						.trigger('change');

					// Set image preview
					$('.ctfw-widget-image-preview', value_container).html('<img src="' + attachment_preview_url + '" />');

					// Set class on value container to tell image and remove button to show
					$(value_container)
						.removeClass('ctfw-widget-image-unset')
						.addClass('ctfw-widget-image-set');

				}

			}

		});

	});

	// Remove Image
	$('body').on('click', '.ctfw-widget-image-remove', function (event) {

		var value_container;

		// Stop click to URL
		event.preventDefault();

		// Get field value container
		value_container = $(this).parent('.ctfw-widget-value');

		// Set attachment ID on hidden input
		// Also trigger change event to make Customizer refresh preview
		$('.ctfw-widget-image', value_container)
			.val('')
			.trigger('change');

		// Set image preview
		$('.ctfw-widget-image-preview', value_container).empty();

		// Set class on value container to tell image and remove button NOT to show
		$(value_container)
			.removeClass('ctfw-widget-image-set')
			.addClass('ctfw-widget-image-unset');

	});

	/**************************************
	 * VIDEO FIELD
	 **************************************/

	// Open media uploader on button click
	$('body').on('click', '.ctfw-widget-upload-file', function (event) {

		var frame;

		// Stop click to URL
		event.preventDefault();

		// Input element
		$input_element = $(this).prev('input');

		// Media frame
		frame = wp.media({
			title: $(this).attr('data-ctfw-widget-upload-title'),
			library: { type: $(this).attr('data-ctfw-widget-upload-type') },
			multiple: false
		});

		// Open media frame
		// To Do: Set current attachment after opening
		// ( How with only URL? For doing with ID, see this: http://bit.ly/Zut80f )
		frame.open();

		// Set attachment URL on click of button
		// (don't do on 'close' so user can cancel)
		frame.on('select', function () {

			var attachments, attachment;

			// Get attachment data
			attachments = frame.state().get('selection').toJSON();
			attachment = attachments[0];

			// An attachment is selected
			if (typeof attachment != 'undefined') {

				// Set attachment URL on input
				// Also trigger change event to make Customizer refresh preview
				if (attachment.url) {

					$input_element
						.val(attachment.url) // input is directly before button
						.trigger('change');
				}

			}

		});

	});

	/*******************************************
	 * COLORPICKER
	 *******************************************/

	// Add colorpicker
	$(document)

		// Init colorpicker
		.on('widget-added widget-updated', function (event, widget) {
			ctfw_init_widget_colorpicker(widget);
		})

		// Persist after AJAX save, without this the field turns into a plain text input
		.ready(function () {

			// Init for each field
			$('#widgets-right .widget:has(.ctfw-widget-color)').each(function () {
				ctfw_init_widget_colorpicker($(this));
			});

		});

	/*******************************************
	 * RESTRICT WIDGETS/SIDEBARS
	 *******************************************/

	// Customizer: Pre-fill Search when "Add a Widget" clicked
	// Show only Slide for Slider widget area and same for Homepage Highlights
	if ($('.wp-customizer').length) { // on Customizer only

		// "Add a Widget" clicked
		$('.add-new-widget').on('click', function (e) {

			var accordion_section, accordion_section_id, sidebar, search;

			// Which widget area is open?
			accordion_section = $(this).parents('.accordion-section');
			accordion_section_id = $(accordion_section).prop('id');

			// Get widget area name
			sidebar = false;
			if (accordion_section_id) {
				sidebar = accordion_section_id.replace(/^accordion-section-sidebar-widgets-/, '');
			}

			// Limit Slide and Highlight widget areas to their appropriate widgets
			search = '';
			if (sidebar) {

				if (sidebar.match(/slider/)) {
					search = 'Slide';
				} else if (sidebar.match(/highlights/)) {
					search = 'Highlight';
				}

			}

			// Update Search input
			if (search) {

				setTimeout(function () {
					$('#widgets-search')
						.val(search)
						.trigger('change');
				}, 100)

			}

		});

	}

});

/**********************************************
 * FUNCTIONS
 **********************************************/

// Init colorpicker
function ctfw_init_widget_colorpicker(widget) {

	jQuery(widget).find('.ctfw-widget-color').wpColorPicker({

		// Cause Customizer to refresh
		// Bug? With this, first color selection doesn't take effect
		change: _.throttle(function () {
			jQuery(this).trigger('change');
		}, 3000)

	});

}
