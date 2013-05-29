<?php/** * Sermons Widget */class CTFW_Widget_Sermons extends CTFW_Widget {	/**	 * Register widget with WordPress	 */	function __construct() {			parent::__construct(			'ctc-sermons',			_x( 'CT Sermons', 'widget', 'church-theme-framework' ),			array(				'description' => __( 'Shows sermons according to options', 'church-theme-framework' )			)					);		}	/**	 * Field configuration	 *	 * This is used by CTFW_Widget class for automatic field output, filtering, sanitization and saving.	 */	 	function ctc_fields() { // prefix in case WP core adds method with same name		// Fields		$fields = array(			// Example			/*			'field_id' => array(				'name'				=> __( 'Field Name', 'church-theme-framework' ),				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> __( 'This is the description below the field.', 'church-theme-framework' ),				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> '', //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> array(), // array of keys/values for radio or select				'default'			=> '', // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> '', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			);			*/			// Title			'title' => array(				'name'				=> _x( 'Title', 'sermons widget', 'church-theme-framework' ),				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'text', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> '', //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> array(), // array of keys/values for radio or select				'default'			=> _x( 'Sermons', 'sermons widget title default', 'church-theme-framework' ), // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> '', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			),			// Category			'category' => array(				'name'				=> _x( 'Category', 'sermons widget', 'church-theme-framework' ),				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'select', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> '', //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> ctc_term_options( 'ccm_sermon_category', array( // array of keys/values for radio or select					'all' => _x( 'All Categories', 'sermons widget', 'church-theme-framework' )				) ),				'default'			=> 'all', // value to pre-populate option with (before first save or on reset)				'no_empty'			=> true, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> '', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array( 'ccm_sermon_category' ), // hide field if taxonomies are not supported			),			// Speaker			'speaker' => array(				'name'				=> _x( 'Speaker', 'sermons widget', 'church-theme-framework' ),				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'select', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> '', //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> ctc_term_options( 'ccm_sermon_speaker', array( // array of keys/values for radio or select					'all' => _x( 'All Speakers', 'sermons widget', 'church-theme-framework' )				) ),				'default'			=> 'all', // value to pre-populate option with (before first save or on reset)				'no_empty'			=> true, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> '', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array( 'ccm_sermon_speaker' ), // hide field if taxonomies are not supported			),						// Order By			'orderby' => array(				'name'				=> _x( 'Order By', 'sermons widget', 'church-theme-framework' ),				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'select', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> '', //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> array( // array of keys/values for radio or select					'title'				=> _x( 'Title', 'sermons widget order by', 'church-theme-framework' ),					'publish_date'		=> _x( 'Date', 'sermons widget order by', 'church-theme-framework' ),					'comment_count'		=> _x( 'Comment Count', 'sermons widget order by', 'church-theme-framework' ),				),				'default'			=> 'publish_date', // value to pre-populate option with (before first save or on reset)				'no_empty'			=> true, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> 'ctc-widget-no-bottom-margin', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			),						// Order			'order' => array(				'name'				=> '',				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'radio', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> '', // show text after checkbox				'radio_inline'		=> true, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> array( // array of keys/values for radio or select					'asc'	=> _x( 'Low to High', 'sermons widget order', 'church-theme-framework' ),					'desc'	=> _x( 'High to Low', 'sermons widget order', 'church-theme-framework' ),				),				'default'			=> 'desc', // value to pre-populate option with (before first save or on reset)				'no_empty'			=> true, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> '', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			),						// Limit			'limit' => array(				'name'				=> _x( 'Limit', 'sermons widget', 'church-theme-framework' ),				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'number', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> '', //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '1', // lowest possible value for number type				'number_max'		=> '50', // highest possible value for number type				'options'			=> array(), // array of keys/values for radio or select				'default'			=> '5', // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> '', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			),						// Image			'show_image' => array(				'name'				=> '',				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> _x( 'Show image', 'sermons widget', 'church-theme-framework' ), //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> array(), // array of keys/values for radio or select				'default'			=> true, // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> 'ctc-widget-no-bottom-margin', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			),			// Date			'show_date' => array(				'name'				=> '',				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'checkbox_label'	=> _x( 'Show date', 'sermons widget', 'church-theme-framework' ), //show text after checkbox				'options'			=> array(), // array of keys/values for radio or select				'default'			=> true, // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> 'ctc-widget-no-bottom-margin', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			),			// Speaker			'show_speaker' => array(				'name'				=> '',				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'checkbox_label'	=> _x( 'Show speaker', 'sermons widget', 'church-theme-framework' ), //show text after checkbox				'options'			=> array(), // array of keys/values for radio or select				'default'			=> false, // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> 'ctc-widget-no-bottom-margin', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array( 'ccm_sermon_speaker' ), // hide field if taxonomies are not supported			),			// Media Types			'show_media_types' => array(				'name'				=> '',				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> _x( 'Show media types', 'sermons widget', 'church-theme-framework' ), //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> array(), // array of keys/values for radio or select				'default'			=> true, // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> 'ctc-widget-no-bottom-margin', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			),						// Excerpt			'show_excerpt' => array(				'name'				=> '',				'after_name'		=> '', // (Optional), (Required), etc.				'desc'				=> '',				'type'				=> 'checkbox', // text, textarea, checkbox, radio, select, number, url, image				'checkbox_label'	=> _x( 'Show excerpt', 'sermons widget', 'church-theme-framework' ), //show text after checkbox				'radio_inline'		=> false, // show radio inputs inline or on top of each other				'number_min'		=> '', // lowest possible value for number type				'number_max'		=> '', // highest possible value for number type				'options'			=> array(), // array of keys/values for radio or select				'default'			=> false, // value to pre-populate option with (before first save or on reset)				'no_empty'			=> false, // if user empties value, force default to be saved instead				'allow_html'		=> false, // allow HTML to be used in the value (text, textarea)				'attributes'		=> array(), // attributes to add to input element				'class'				=> '', // class(es) to add to input				'field_attributes'	=> array(), // attr => value array for field container				'field_class'		=> 'ctc-widget-no-bottom-margin', // class(es) to add to field container				'custom_sanitize'	=> '', // function to do additional sanitization (or array( &$this, 'method' ))				'custom_field'		=> '', // function for custom display of field input				'page_templates'	=> array(), // field will not appear or save if one of these page templates are not selected (or array( &$this, 'method' ))				'taxonomies'		=> array(), // hide field if taxonomies are not supported			),		);				// Return config		return $fields;		}	/**	 * Get posts	 *	 * This can optionally be used by the template.	 * $this->instance is sanitized before being made available here.	 */	 	function ctc_get_posts() {		// Base arguments		$args = array(			'post_type'       	=> 'ccm_sermon',			'orderby'         	=> $this->ctc_instance['orderby'],			'order'           	=> $this->ctc_instance['order'],			'numberposts'     	=> $this->ctc_instance['limit'],		);		// Category argument		if ( 'all' != $this->ctc_instance['category'] && $category_term = get_term( $this->ctc_instance['category'], 'ccm_sermon_category' ) ) {			$args['ccm_sermon_category'] = $category_term->slug;		}		// Speaker argument		if ( 'all' != $this->ctc_instance['speaker'] && $speaker_term = get_term( $this->ctc_instance['speaker'], 'ccm_sermon_speaker' ) ) {			$args['ccm_sermon_speaker'] = $speaker_term->slug;		}		// Get posts		$posts = get_posts( $args );					// Return filtered		return apply_filters( 'ctc_sermons_widget_get_posts', $posts );			}}