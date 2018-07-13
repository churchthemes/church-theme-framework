<?php
/**
 * Galleries Widget
 *
 * This lists all gallery pages.
 *
 * @package    Church_Theme_Framework
 * @subpackage Classes
 * @copyright  Copyright (c) 2013 - 2018, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Galleries widget class
 *
 * @since 0.9
 */
class CTFW_Widget_Galleries extends CTFW_Widget {

	/**
	 * Register widget with WordPress
	 *
	 * @since 0.9
	 */
	function __construct() {

		parent::__construct(
			'ctfw-galleries',
			_x( 'CT Galleries', 'widget', 'church-theme-framework' ),
			array(
				'description' => __( 'Shows list of gallery pages', 'church-theme-framework' )
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
				'name'				=> _x( 'Title', 'galleries widget', 'church-theme-framework' ),
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
				'default'			=> _x( 'Galleries', 'galleries widget title default', 'church-theme-framework' ), // value to pre-populate option with (before first save or on reset)
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

			// Order By
			'orderby' => array(
				'name'				=> _x( 'Order By', 'galleries widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'select', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array( // array of keys/values for radio or select
					'date'		=> _x( 'Date Added', 'galleries widget order by', 'church-theme-framework' ),
					'title'		=> _x( 'Title', 'galleries widget order by', 'church-theme-framework' ),
				),
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> 'date', // value to pre-populate option with (before first save or on reset)
				'no_empty'			=> true, // if user empties value, force default to be saved instead
				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)
				'attributes'		=> array(), // attributes to add to input element
				'class'				=> '', // class(es) to add to input
				'field_attributes'	=> array(), // attr => value array for field container
				'field_class'		=> 'ctfw-widget-no-bottom-margin', // class(es) to add to field container
				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))
				'custom_field'		=> '', // function for custom display of field input
				'taxonomies'		=> array(), // hide field if taxonomies are not supported
			),

			// Order
			'order' => array(
				'name'				=> '',
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> '',
				'type'				=> 'radio', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', // show text after checkbox
				'radio_inline'		=> true, // show radio inputs inline or on top of each other
				'number_min'		=> '', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array( // array of keys/values for radio or select
					'asc'	=> _x( 'Low to High', 'galleries widget order', 'church-theme-framework' ),
					'desc'	=> _x( 'High to Low', 'galleries widget order', 'church-theme-framework' ),
				),
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> 'desc', // value to pre-populate option with (before first save or on reset)
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

			// Limit
			'limit' => array(
				'name'				=> _x( 'Limit', 'galleries widget', 'church-theme-framework' ),
				'after_name'		=> '', // (Optional), (Required), etc.
				'desc'				=> _x( 'Set to 0 for unlimited.', 'galleries widget', 'church-theme-framework' ),
				'type'				=> 'number', // text, textarea, checkbox, radio, select, number, url, image, color
				'checkbox_label'	=> '', //show text after checkbox
				'radio_inline'		=> false, // show radio inputs inline or on top of each other
				'number_min'		=> '0', // lowest possible value for number type
				'number_max'		=> '', // highest possible value for number type
				'options'			=> array(), // array of keys/values for radio or select
				'upload_button'		=> '', // for url field; text for button that opens media frame
				'upload_title'		=> '', // for url field; title appearing at top of media frame
				'upload_type'		=> '', // for url field; optional type of media to filter by (image, audio, video, application/pdf)
				'default'			=> '5', // value to pre-populate option with (before first save or on reset)
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

		);

		// Return config
		return apply_filters( 'ctfw_galleries_widget_fields', $fields );

	}

	/**
	 * Get posts
	 *
	 * This can optionally be used by the template.
	 * $this->instance is sanitized before being made available here.
	 *
	 * @since 0.9
	 * @return array Posts for widget template
	 */
	function ctfw_get_posts() {

		// Get gallery pages/posts
		$posts = ctfw_gallery_posts( array(
			'order'			=> $this->ctfw_instance['order'],
			'orderby'		=> $this->ctfw_instance['orderby'],
			'limit'			=> $this->ctfw_instance['limit']
		) );

		// Return filtered
		return apply_filters( 'ctfw_galleries_widget_get_posts', $posts );

	}


}