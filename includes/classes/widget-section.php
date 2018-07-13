<?php
/**
 * Section Widget
 *
 * A widget for producing a section, such as on a long homepage with multiple similar sections.
 *
 * @package    Church_Theme_Framework
 * @subpackage Classes
 * @copyright  Copyright (c) 2015 - 2017, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      1.7
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Section widget class
 *
 * @since 0.9
 */
class CTFW_Widget_Section extends CTFW_Widget {

	/**
	 * Register widget with WordPress
	 *
	 * @since 0.9
	 */
	function __construct() {

		parent::__construct(
			'ctfw-section',
			_x( 'CT Section', 'widget', 'church-theme-framework' ),
			array(
				'description' => __( 'Shows a section', 'church-theme-framework' )
			)
		);

	}

	/**
	 * Field configuration
	 *
	 * This is used by CTFW_Widget class for automatic field output, filtering, sanitization and saving.
	 *
	 * @since 0.9
	 * @return array Fields for widget
	 */
	function ctfw_fields() { // prefix in case WP core adds method with same name

		// Fields
		$fields = array(

			// Example
			/*
			'field_id' => array(
				'name'				=> __( 'Field Name', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> __( 'This is the description below the field.', 'church-theme-framework' ),
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			);
			*/

			// Title
			'title' => array(
				'name'				=> _x( 'Title', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Content
			'content' => array(
				'name'				=> _x( 'Content', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'textarea', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> true, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> 'ctfw-widget-medium', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Text Size
			'text_size' => array(
				'name'				=> _x( 'Text Size', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'radio', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', // show text after checkbox
				'radio_inline'		=> true, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array( // array of keys/values for radio or select
					'small'		=> _x( 'Small', 'section widget text size', 'church-theme-framework' ),
					'regular'	=> _x( 'Regular', 'section widget text size', 'church-theme-framework' ),
					'large'		=> _x( 'Large', 'section widget text size', 'church-theme-framework' ),
				),
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> 'regular', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Centered
			'centered' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image, color
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'checkbox_label'	=> _x( 'Centered', 'sermons widget', 'church-theme-framework' ), //show text after checkbox
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> false, // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Image
			'image_id' => array(
				'name'				=> _x( 'Image', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'image', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Video
			'video' => array(		// generic "video" (vs "video_url") in case ever want to allow shortcode, embed code, switch to ID, etc.
				'name'				=> _x( 'Video', 'slide widget', 'church-theme-framework' ),
				'after_name'		=> _x( '(Optional)', 'widget field', 'church-theme-framework' ), // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'url', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> __( 'Choose Video', 'church-theme-framework' ), // for url field; text for button that opens media frame
				'upload_title'		=> __( 'Choose Video for Widget', 'church-theme-framework' ), // for url field; title appearing at top of media frame
				'upload_type'		=> 'video', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Brightness
			'image_brightness' => array(
				'name'				=> _x( 'Brightness', 'section widget', 'church-theme-framework' ),
				'after_name'		=> _x( '(Percentage)', 'section widget', 'church-theme-framework' ), // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'number', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '1', // lowest possible value for number type
				'number_max'		=> '100', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '100', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'style="width:60px"',
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Opacity
			'image_opacity' => array(
				'name'				=> _x( 'Opacity', 'section widget', 'church-theme-framework' ),
				'after_name'		=> _x( '(Percentage)', 'section widget', 'church-theme-framework' ), // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'number', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '1', // lowest possible value for number type
				'number_max'		=> '100', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '100', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'style="width:60px"',
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Attachment
			'attachment' => array(
				'name'				=> _x( 'Attachment', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'radio', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', // show text after checkbox
				'radio_inline'		=> true, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array( // array of keys/values for radio or select
					'scroll'	=> _x( 'Scroll', 'section widget attachment', 'church-theme-framework' ),
					'fixed'		=> _x( 'Fixed', 'section widget attachment', 'church-theme-framework' ),
				),
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> 'scroll', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Height
			'height' => array(
				'name'				=> _x( 'Height', 'section widget', 'church-theme-framework' ),
				'after_name'		=>  _x( '(Percentage)', 'widget field', 'church-theme-framework' ), // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'number', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '0', // lowest possible value for number type
				'number_max'		=> '100', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'style="width:60px"',
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Text Size
			'orientation' => array(
				'name'				=> _x( 'Orientation', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'radio', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', // show text after checkbox
				'radio_inline'		=> true, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array( // array of keys/values for radio or select
					'vertical'		=> _x( 'Vertical', 'section widget orientation', 'church-theme-framework' ),
					'horizontal'	=> _x( 'Horizontal', 'section widget orientation', 'church-theme-framework' ),
				),
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> 'vertical', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Link 1 Text
			'link1_text' => array(
				'name'				=> _x( 'Link 1', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'placeholder' => _x( 'Text', 'section widget', 'church-theme-framework' )
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Link 1 URL
			'link1_url' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> __( 'Enter text for link then URL.', 'church-theme-framework' ),
				'type'				=> 'url', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'placeholder' => _x( 'URL', 'section widget', 'church-theme-framework' )
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Link 2 Text
			'link2_text' => array(
				'name'				=> _x( 'Link 2', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'placeholder' => _x( 'Text', 'section widget', 'church-theme-framework' )
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Link 2 URL
			'link2_url' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'url', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'placeholder' => _x( 'URL', 'section widget', 'church-theme-framework' )
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Link 3 Text
			'link3_text' => array(
				'name'				=> _x( 'Link 3', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'placeholder' => _x( 'Text', 'section widget', 'church-theme-framework' )
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Link 3 URL
			'link3_url' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'url', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'placeholder' => _x( 'URL', 'section widget', 'church-theme-framework' )
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Link 4 Text
			'link4_text' => array(
				'name'				=> _x( 'Link 4', 'section widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'placeholder' => _x( 'Text', 'section widget', 'church-theme-framework' )
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Link 4 URL
			'link4_url' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'url', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> false, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(
										'placeholder' => _x( 'URL', 'section widget', 'church-theme-framework' )
									), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

		);

		// Return config
		return apply_filters( 'ctfw_section_widget_fields', $fields );

	}

	/**
	 * Links array
	 *
	 * Return data from multiple link fields as array for easy looping in widget's template.
	 *
	 * @since 1.7
	 * @return array Links with text and URL, if they were both provided
	 */
	function ctfw_links() {

		$instance = $this->ctfw_instance;

		$links = array();

		$max_links = 4;

		// Check each set of link fields for data
		for ( $i = 1; $i <= $max_links; $i++ ) {

			$text = $instance['link' . $i . '_text'];
			$url = $instance['link' . $i . '_url'];

			// Add to array if text and URL exist
			if ( $text && $url ) {

				$links[] = array(
					'text'	=> $text,
					'url'	=> $url,
				);

			}

		}

		// Return filtered
		return apply_filters( 'ctfw_section_widget_links', $links );

	}

}
