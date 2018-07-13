<?php
/**
 * ChurchThemes.com Widget Layer
 *
 * The framework widgets extend this class which extends WP_Widget.
 * This extra layer adds methods for automatic field output, field filtering, sanitization, updating and front-end display via template.
 *
 * @package    Church_Theme_Framework
 * @subpackage Classes
 * @copyright  Copyright (c) 2013 - 2017, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Main widget class
 *
 * @since 0.9
 */
class CTFW_Widget extends WP_Widget {

	/**
	 * Constructor
	 *
	 * @since 0.9
	 * @param string $id_base Widget ID
	 * @param string $name Widget name
	 * @param array $widget_options Widget options
	 * @param array $control_options Control options
	 */
	function __construct( $id_base = false, $name, $widget_options = array(), $control_options = array() ) {

		parent::__construct( $id_base, $name, $widget_options, $control_options );

		// Called class
		// get_called_class() only works for PHP 5.3+
		$widgets = ctfw_widgets();
		$this->ctfw_class = isset( $widgets[$this->id_base]['class'] ) ? $widgets[$this->id_base]['class'] : '';

	}

	/**
	 * Prepared fields
	 *
	 * Filter the fields from extending class.
	 *
	 * @since 0.9
	 * @return array Modified fields
	 */
	function ctfw_prepared_fields() { // prefix in case WP core adds method with same name

		// Get fields from extending class
		$fields = array();
		if ( method_exists( $this->ctfw_class, 'ctfw_fields' ) ) {
			$fields = $this->ctfw_fields();
		}

		// Fill array of visible fields with all by default
		$visible_fields = array();
		foreach ( $fields as $id => $field ) {
			$visible_fields[] = $id;
		}

		// Let themes/plugins set explicit visibility for fields for specific widget
		$visible_fields = apply_filters( 'ctfw_widget_visible_fields-' . $this->id_base, $visible_fields, $this->id_base );

		// Let themes/plugins override specific data for field of specific post type
		$field_overrides = apply_filters( 'ctfw_widget_field_overrides-' . $this->id_base, array(), $this->id_base ); // by default no overrides

		// Loop fields to modify them with filtered data
		foreach ( $fields as $id => $field ) {

			// Selectively override field data based on filtered array
			if ( ! empty( $field_overrides[$id] ) && is_array( $field_overrides[$id] ) ) {
				$fields[$id] = array_merge( $field, $field_overrides[$id] ); // merge filtered in data over top existing data
			}

			// Set visibility of field based on filtered or unfiltered array
			$fields[$id]['hidden'] = ! in_array( $id, (array) $visible_fields ) ? true : false; // set hidden true if not in array

			// Set visibility of field based on required taxonomy support (in case unsupported by theme or via plugin settings, etc.)
			if ( ! empty( $fields[$id]['taxonomies'] ) ) {

				// Loop taxonomies
				foreach ( (array) $fields[$id]['taxonomies'] as $taxonomy_name ) {

					// Taxonomy not supported by theme (or possibly disabled via Church Content)
					if ( ! ctfw_ctc_taxonomy_supported( $taxonomy_name ) ) { // check show_ui
						$fields[$id]['hidden'] = true;
						break; // one strike and you're out
					}

				}

			}

		}

		return $fields;

	}

	/**
	 * Back-end widget form
	 *
	 * @since 0.9
	 * @param array $instance Widget instance
	 */
	function form( $instance ) {

		// Insert content before fields
		do_action( 'ctfw_widget_before_fields', $this );

		// Loop fields
		$fields = $this->ctfw_prepared_fields();
		foreach ( $fields as $id => $field ) {

			/**
			 * Field Data
			 */

			// Store data in array so custom output callback can use it
			$data = array();

			// Get field config
			$data['id'] = $id;
			$data['field'] = $field;

			// Prepare strings
			$data['default'] = isset( $data['field']['default'] ) ? $data['field']['default'] : '';
			$data['value'] = isset( $instance[$id] ) ? $instance[$id] : $data['default']; // get saved value or use default if first save
			$data['esc_value'] = esc_attr( $data['value'] );
			$data['esc_element_id'] = $this->get_field_id( $data['id'] );

			// Prepare styles for elements (core WP styling)
			$default_classes = array(
				'text'			=> 'regular-text',
				'url'			=> 'regular-text',
				'textarea'		=> '',
				'checkbox'		=> '',
				'radio'			=> '',
				'radio_inline'	=> '',
				'select'		=> '',
				'number'		=> 'small-text',
				'image'			=> '',
				'color'			=> '',

			);
			$classes = array();
			$classes[] = 'ctfw-widget-' . $data['field']['type'];
			if ( ! empty( $default_classes[$data['field']['type']] ) ) {
				$classes[] = $default_classes[$data['field']['type']];
			}
			if ( ! empty( $data['field']['class'] ) ) {
				$classes[] = $data['field']['class'];
			}
			$data['classes'] = implode( ' ', $classes );

			// Common attributes
			$data['common_atts'] = 'name="' . $this->get_field_name( $data['id'] ) . '" class="' . esc_attr( $data['classes'] ) . '"';
			if ( ! empty( $data['field']['attributes'] ) ) { // add custom attributes
				foreach ( $data['field']['attributes'] as $attr_name => $attr_value ) {
					$data['common_atts'] .= ' ' . $attr_name . '="' . esc_attr( $attr_value ) . '"';
				}
			}

			// Field container classes
			$data['field_class'] = array();
			$data['field_class'][] = 'ctfw-widget-field';
			$data['field_class'][] = 'ctfw-widget-field-' . $data['id'];
			if ( ! empty( $data['field']['hidden'] ) ) { // Hidden (for internal use only, via prepare() filter)
				$data['field_class'][] = 'ctfw-widget-hidden';
			}
			if ( ! empty( $data['field']['field_class'] ) ) {
				$data['field_class'][] = $data['field']['field_class']; // append custom classes
			}
			$data['field_class'] = implode( ' ', $data['field_class'] );

			// Field container styles
			$data['field_attributes'] = '';
			if ( ! empty( $data['field']['field_attributes'] ) ) { // add custom attributes
				foreach ( $data['field']['field_attributes'] as $attr_name => $attr_value ) {
					$data['field_attributes'] .= ' ' . $attr_name . '="' . esc_attr( $attr_value ) . '"';
				}
			}

			/**
			 * Form Input
			 */

			// Use custom function to render custom field content
			if ( ! empty( $data['field']['custom_field'] ) ) {
				$input = call_user_func( $data['field']['custom_field'], $data );
			}

			// Standard output based on type
			else {

				// Switch thru types to render differently
				$input = '';
				switch ( $data['field']['type'] ) {

					// Text
					case 'text':

						$input = '<input type="text" ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '" value="' . $data['esc_value'] . '" />';

						break;

					// URL
					case 'url':

						// Input same as text
						$input = '<input type="url" ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '" value="' . $data['esc_value'] . '" />';

						// Append button if upload_* used
						if ( ! empty( $data['field']['upload_button'] ) ) {

							// Button and defult title and file type
							$upload_button = $data['field']['upload_button'];
							$upload_title = isset( $data['field']['upload_title'] ) ? $data['field']['upload_title'] : '';
							$upload_type = isset( $data['field']['upload_type'] ) ? $data['field']['upload_type'] : '';

							// Button to choose or upload file
							$input .= ' <input type="button" value="' . esc_attr( $upload_button ) . '" class="upload_button button ctfw-widget-upload-file" data-ctfw-widget-upload-type="' . esc_attr( $upload_type ) . '" data-ctfw-widget-upload-title="' . esc_attr( $upload_title ) . '" /> ';

						}

						break;

					// Textarea
					case 'textarea':

						$input = '<textarea ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '">' . esc_textarea( $data['value'] ) . '</textarea>';

						// special esc func for textarea

						break;

					// Checkbox
					case 'checkbox':

						$input  = '<input type="hidden" ' . $data['common_atts'] . ' value="" />'; // causes unchecked box to post empty value (helps with default handling)
						$input .= '<label for="' . $data['esc_element_id'] . '">';
						$input .= '	<input type="checkbox" ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '" value="1"' . checked( '1', $data['value'], false ) . '/>';
						if ( ! empty( $data['field']['checkbox_label'] ) ) {
							$input .= ' ' . $data['field']['checkbox_label'];
						}
						$input .= '</label>';

						break;

					// Radio
					case 'radio':

						if ( ! empty( $data['field']['options'] ) ) {

							foreach ( $data['field']['options'] as $option_value => $option_text ) {

								$esc_radio_id = $data['esc_element_id'] . '-' . $option_value;

								$input .= '<div' . ( ! empty( $data['field']['radio_inline'] ) ? ' class="ctfw-widget-radio-inline"' : '' ) . '>';
								$input .= '	<label for="' . $esc_radio_id . '">';
								$input .= '		<input type="radio" ' . $data['common_atts'] . ' id="' . $esc_radio_id . '" value="' . esc_attr( $option_value ) . '"' . checked( $option_value, $data['value'], false ) . '/> ' . esc_html( $option_text );
								$input .= '	</label>';
								$input .= '</div>';

							}

						}

						break;

					// Select
					case 'select':

						if ( ! empty( $data['field']['options'] ) ) {

							$input .= '<select ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '">';
							foreach ( $data['field']['options'] as $option_value => $option_text ) {
								$input .= '<option value="' . esc_attr( $option_value ) . '" ' . selected( $option_value, $data['value'], false ) . '> ' . esc_html( $option_text ) . '</option>';
							}
							$input .= '</select>';

						}

						break;

					// Number
					case 'number':

						// Min and max attributes
						$min = isset( $field['number_min'] ) && '' !== $field['number_min'] ? (int) $field['number_min'] : ''; // force number if set
						$max = isset( $field['number_max'] ) && '' !== $field['number_max'] ? (int) $field['number_max'] : ''; // force number if set

						$input = '<input type="number" ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '" value="' . $data['esc_value'] . '" min="' . esc_attr( $min ) . '" max="' . esc_attr( $max ) . '" />';

						break;

					// Image
					case 'image':

						// Is image set and still exists?
						$value_container_classes = 'ctfw-widget-image-unset';
						if ( ! empty( $data['value'] ) && wp_get_attachment_image_src( $data['value'] ) ) {
							$value_container_classes = 'ctfw-widget-image-set';
						}

						// Hidden input for image ID
						$input .= '<input type="hidden" ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '" value="' . $data['esc_value'] . '" />';

						// Show image
						$input .= '<div class="ctfw-widget-image-preview">' . wp_get_attachment_image( $data['value'], 'medium' ) . '</div>';

						// Button to open media library
						$input .= '<a href="#" class="button ctfw-widget-image-choose" data-ctfw-field-id="' . $data['esc_element_id'] . '">' . _x( 'Choose Image', 'widget image field', 'church-theme-framework' ) . '</a>';

						// Button to remove image
						$input .= '<a href="#" class="button ctfw-widget-image-remove">' . _x( 'Remove Image', 'widget image field', 'church-theme-framework' ) . '</a>';

						break;

					// Color
					// Thank you Andy Wilkerson: https://github.com/churchthemes/church-theme-framework/pull/11
					case 'color':

						$input = '<input type="text" ' . $data['common_atts'] . ' id="' . $data['esc_element_id'] . '" value="' . $data['esc_value'] . '" />';

						break;

				}

			}

			/**
			 * Field Container
			 */

			// Output field
			if ( ! empty( $input ) ) { // don't render if type invalid

				?>
				<div class="<?php echo esc_attr( $data['field_class'] ); ?>"<?php echo $data['field_attributes']; ?>>

					<div class="ctfw-widget-name">

						<?php if ( ! empty( $data['field']['name'] ) ) : ?>

							<?php echo esc_html( $data['field']['name'] ); ?>

							<?php if ( ! empty( $data['field']['after_name'] ) ) : ?>
								<span><?php echo esc_html( $data['field']['after_name'] ); ?></span>
							<?php endif; ?>

						<?php endif; ?>

					</div>

					<div class="ctfw-widget-value<?php echo ! empty( $value_container_classes ) ? ' ' . $value_container_classes : ''; ?>">

						<?php echo $input; ?>

						<?php if ( ! empty( $data['field']['desc'] ) ) : ?>
						<p class="description">
							<?php echo $data['field']['desc']; ?>
						</p>
						<?php endif; ?>

					</div>

				</div>
				<?php

			}

		}

		// Insert content after fields
		do_action( 'ctfw_widget_after_fields', $this );

	}

	/**
	 * Sanitize field values
	 *
	 * Used before saving and before providing instance to widget template.
	 *
	 * @since 0.9
	 * @param array $instance Widget instance
	 * @return array Sanitized instance
	 */
	function ctfw_sanitize( $instance ) { // prefix in case WP core adds method with same name

		global $allowedposttags;

		// Array to add sanitized values to
		$sanitized_instance = array();

		// Loop valid fields to sanitize
		$fields = $this->ctfw_prepared_fields();
		foreach ( $fields as $id => $field ) {

			// Get posted value
			$input = isset( $instance[$id] ) ? $instance[$id] : '';

			// General sanitization
			$output = trim( stripslashes( $input ) );

			// Sanitize based on type
			switch ( $field['type'] ) {

				// Text
				// Textarea
				case 'text':
				case 'textarea':

					// Strip tags if config does not allow HTML
					if ( empty( $field['allow_html'] ) ) {
						$output = trim( strip_tags( $output ) );
					}

					// Sanitize HTML in case used (remove evil tags like script, iframe) - same as post content
					$output = stripslashes( wp_filter_post_kses( addslashes( $output ), $allowedposttags ) );

					break;

				// Checkbox
				case 'checkbox':

					$output = ! empty( $output ) ? '1' : '';

					break;

				// Radio
				// Select
				case 'radio':
				case 'select':

					// If option invalid, blank it so default will be used
					if ( ! isset( $field['options'][$output] ) ) {
						$output = '';
					}

					break;

				// Number
				case 'number':

					// Force number
					$output = (int) $output;

					// Enforce minimum value
					$min = isset( $field['number_min'] ) && '' !== $field['number_min'] ? (int) $field['number_min'] : ''; // force number if set
					if ( '' !== $min && $output < $min ) { // allow 0, don't process if no value given ('')
						$output = $min;
					}

					// Enforce maximum value
					$max = isset( $field['number_max'] ) && '' !== $field['number_max'] ? (int) $field['number_max'] : ''; // force number if set
					if ( '' !== $max && $output > $max ) { // allow 0, don't process if no value given ('')
						$output = $max;
					}

					break;

				// URL
				case 'url':

					$output = esc_url_raw( $output ); // force valid URL or use nothing

					break;

				// Image
				case 'image':

					// Sanitize attachment ID
					$output = absint( $output );

					// Set empty if value is 0, attachment does not exist, or is not an image
					if ( empty( $output ) || ! wp_get_attachment_image_src( $output ) ) {
						$output = '';
					}

					break;

				// Color
				case 'color':

					// Add # if missing
					$output = maybe_hash_hex_color( $output );

					// Empty if hex code invalid (including if is only #, which is possible)
					$output = sanitize_hex_color( $output );

					break;

			}

			// Run additional custom sanitization function if field config requires it
			if ( ! empty( $field['custom_sanitize'] ) ) {
				$output = call_user_func( $field['custom_sanitize'], $output );
			}

			// Sanitization left value empty, use default if empty not allowed
			$output = trim( $output );
			if ( empty( $output ) && ! empty( $field['default'] ) && ! empty( $field['no_empty'] ) ) {
				$output = $field['default'];
			}

			// Add output to instance array
			$sanitized_instance[$id] = $output;

		}

		// Return for saving
		return $sanitized_instance;

	}

	/**
	 * Save sanitized form values
	 *
	 * @since 0.9
	 * @param array $new_instance New widget instance
	 * @param array $old_instance Old widget instance
	 * @return array Sanitized data for saving
	 */
	function update( $new_instance, $old_instance ) {

		// Sanitize values
		$instance = $this->ctfw_sanitize( $new_instance );

		// Return for saving
		return $instance;

	}

	/**
	 * Front-end display of widget
	 *
	 * Load template from parent or child theme if exists.
	 *
	 * @since 0.9
	 * @param array $args Widget arguments
	 * @param array $instance Widget instance
	 */
	function widget( $args, $instance ) {

		global $post; // setup_postdata() needs this

		$template_files = array();

		// Available widgets
		$widgets = ctfw_widgets();

		// Get default template filename
		$default_template_file = $widgets[$this->id_base]['template_file'];

		// Load template for current widget area, if template file exists
		// e.g. widget-sermons.php becomes widget-sermons-footer.php for widget area named 'footer'
		if ( ! empty( $args['id'] ) ) {

			// Template filename having widget area ID
			$widget_area_template_file = str_replace( '.php', '-' . $args['id'] . '.php', $default_template_file );

			// Use template filename without prefix for cleaner template filenames
			// Example: widget-sermons-ctcom-home.php becomes widget-sermons-home.php
			// Usage: add_theme_support( 'ctfw-widget-template-no-prefix', 'ctcom-' );
			$remove_prefix_support = get_theme_support( 'ctfw-widget-template-no-prefix' );
			if ( ! empty( $remove_prefix_support[0] ) ) {

				// Prefix to remove
				$prefix = $remove_prefix_support[0];

				// Attempt to load filename having no prefix before filename with prefix (see below)
				$template_files[] = str_replace( '-' . $prefix, '-', $widget_area_template_file );

			}

			// Add template file (e.g. widget-sermons-ctcom-home.php)
			// Try to load prefixed version even if ctfw-widget-template-no-prefix in case file is missing
			$template_files[] = $widget_area_template_file;

		}

		// Otherwise, load standard template
		$template_files[] = $default_template_file;

		// Loop templates to load highest priority existing
		foreach ( $template_files as $template_file ) {

			// Check if template exists
			if ( $template_path = locate_template( CTFW_THEME_WIDGET_DIR . '/' . $template_file ) ) { // false if does not exist

				// Sanitize widget instance (field values) before loading template
				$instance = $this->ctfw_sanitize( $instance );

				// Make instance available to other methods used by template (e.g. get_posts())
				$this->ctfw_instance = $instance;

				// Set global to provide widget data inside get_template_part();
				$GLOBALS['ctfw_current_widget'] = array(
					'this'		=> $this,
					'args'		=> $args,
					'instance'	=> $instance,
				);

				// Load template with variables available (unlike locate_template())
				include $template_path;

				// Don't load another template
				break;

			}

		}

	}

}
