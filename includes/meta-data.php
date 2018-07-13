<?php
/**
 * Meta Data Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*********************************
 * META DATA
 *********************************

/**
 * Get meta data for a post/type (without prefix)
 *
 * @since 0.9
 * @param array $fields Provide $fields as array without meta field's post type prefix (_ctc_sermon_ for example)
 * @param int $post_id Optional post ID; otherwise current post used
 * @param string $prefix Optional prefix override; otherwise post type used as prefix
 * @return array Meta data
 */
function ctfw_get_meta_data( $fields, $post_id = null, $prefix = null ) {

	$meta = array();

	// Have fields
	if ( ! empty( $fields ) ) {

		// Use current post ID if none set
		if ( ! isset( $post_id ) ) {
			$post_id = get_the_ID();
		}

		// Have post ID
		if ( $post_id ) {

			// Post type as prefix for meta field
			if ( ! isset( $prefix ) ) {
				$post_type = get_post_type( $post_id );
				$prefix = '_' . $post_type . '_';
			}

			// Loop fields to get values
			foreach ( $fields as $field ) {
				$meta[$field] = get_post_meta( $post_id, $prefix . $field, true );
			}

		}

	}

	return apply_filters( 'ctfw_get_meta_data', $meta, $post_id );

}
