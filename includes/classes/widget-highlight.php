<?php
/**
 * Highlight Widget
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
 * Highlight widget class
 *
 * @since 0.9
 */
class CTFW_Widget_Highlight extends CTFW_Widget {

	/**
	 * Register widget with WordPress
	 *
	 * @since 0.9
	 */
	function __construct() {

		parent::__construct(
			'ctfw-highlight',
			_x( 'CT Highlight', 'widget', 'church-theme-framework' ),
			array(
				'description' => __( 'Shows a highlight', 'church-theme-framework' )
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
				'name'				=> _x( 'Title', 'highlight widget', 'church-theme-framework' ),
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
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Description
			'description' => array(
				'name'				=> _x( 'Description', 'highlight widget', 'church-theme-framework' ),
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
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> '', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Click URL
			'click_url' => array(
				'name'				=> _x( 'Click URL', 'highlight widget', 'church-theme-framework' ),
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
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// New Window
			'click_new' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> _x( 'Open in new tab', 'highlight widget', 'church-theme-framework' ), //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
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
				'field_class'		=> 'ctfw-widget-no-top-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Image
			'image_id' => array(
				'name'				=> _x( 'Image', 'highlight widget', 'church-theme-framework' ),
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

			// Image Brightness
			'image_brightness' => array(
				'name'				=> _x( 'Brightness', 'highlight widget', 'church-theme-framework' ),
				'after_name'		=> _x( '(Percentage)', 'highlight widget', 'church-theme-framework' ), // (Optional), (Required), etc.
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

			// Image Opacity
			'image_opacity' => array(
				'name'				=> _x( 'Opacity', 'highlight widget', 'church-theme-framework' ),
				'after_name'		=> _x( '(Percentage)', 'highlight widget', 'church-theme-framework' ), // (Optional), (Required), etc.
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

		);

		// Return config
		return apply_filters( 'ctfw_highlight_widget_fields', $fields );

	}

}
