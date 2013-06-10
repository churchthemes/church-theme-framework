<?php
/**
 * Content Type Functions
 *
 * Content types help theme determine which area of the site a user is viewing (blog, sermons, etc.)
 * It is useful for showing relevant sidebars, header banners, etc.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*********************************
 * CONTENT TYPES
 *********************************

/**
 * Content types
 *
 * Theme should filter ctfw_content_types to add page_templates since they are theme-specific.
 * The filter can also be used to add other content types.
 *
 * @since 0.9
 * @return array Default content types configuration
 */
function ctfw_content_types() {

	$content_types = array(

		'sermon' => array(
			'post_types'		=> array( 'ccm_sermon' ),
			'taxonomies'		=> array( 'ccm_sermon_topic', 'ccm_sermon_book', 'ccm_sermon_speaker', 'ccm_sermon_series', 'ccm_sermon_tag' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'event' => array(
			'post_types'		=> array( 'ccm_event' ),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'people' => array(
			'post_types'		=> array( 'ccm_person' ),
			'taxonomies'		=> array( 'ccm_person_group' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'location' => array(
			'post_types'		=> array( 'ccm_location' ),
			'taxonomies'		=> array( 'ccm_location' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'gallery' => array(
			'post_types'		=> array(),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'contact' => array(
			'post_types'		=> array(),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'blog' => array(
			'post_types'		=> array( 'post' ),
			'taxonomies'		=> array( 'category', 'tag' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array( 'is_author', 'is_archive', 'is_home' ), // is_home() is "Your latest posts" on homepage or "Posts page" when static front page used
		),

		'page' => array(
			'post_types'		=> array( 'page' ),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'search' => array(
			'post_types'		=> array(),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array( 'is_search' ),
		),

	);

	// Allow filtering
	$content_types = apply_filters( 'ctfw_content_types', $content_types );

	// Sanitize types (particularly for filtered in data)
	$data_keys = array( 'post_types', 'taxonomies', 'page_templates', 'conditions' );
	foreach ( $content_types as $content_type => $content_type_data ) {
		foreach ( $data_keys as $data_key ) {
			$content_types[$content_type][$data_key] = isset( $content_type_data[$data_key] ) ? (array) $content_type_data[$data_key] : array(); // array if string, empty array if null
		}
	}

	// Return
	return $content_types;

}

/**
 * Detect type of content being shown
 *
 * Useful for showing content-specific elements (breadcrumbs, sidebars, header images).
 * The returned values should correspond to sidebar names in includes/sidebars.php.
 *
 * @since 0.9
 * @global object $post
 * @return string The current page's content type
 */
function ctfw_current_content_type() {

	global $post;

	$current_type = false;

	$content_types = ctfw_content_types();

	// Get content type based on post type, taxonomy or template
	foreach ( $content_types as $type => $type_data ) {

		// Check attachment parent post type
		if ( is_attachment() && ! empty( $post->post_parent ) && ! empty( $type_data['post_types'] ) && in_array( get_post_type( $post->post_parent ), $type_data['post_types'] ) ) {

			$current_type = $type;

			// If parent is a page, base its type on the template it uses
			if ( 'page' == $type && $parent_page_template_type = ctfw_content_type_by_page_template( get_post_meta( $post->post_parent, '_wp_page_template', true ) ) ) {
				$current_type = $parent_page_template_type;
			}

			break;

		}

		// Check post type
		if ( ! empty( $type_data['post_types'] ) && is_singular( $type_data['post_types'] ) || is_post_type_archive( $type_data['post_types'] ) ) {
			$current_type = $type;
			break;
		}

		// Check taxonomy
		foreach ( $type_data['taxonomies'] as $taxonomy ) {
			if ( is_tax( $taxonomy ) ) {
				$current_type = $type;
				break 2;
			}
		}

		// Check page template
		foreach ( $type_data['page_templates'] as $page_template ) {
			if ( is_page_template( CTFW_THEME_PAGE_TPL_DIR . '/' . basename( $page_template ) ) ) {
				$current_type = $type;
				break 2;
			}
		}

		// Check conditions
		foreach ( $type_data['conditions'] as $condition ) {
			if ( function_exists( $condition ) && call_user_func( $condition ) ) {
				$current_type = $type;
				break 2;
			}
		}

	}

	// Return filterable
	return apply_filters( 'ctfw_current_content_type', $current_type );

}

/**
 * Get data for a specific content type
 *
 * Specify a key, such as "page_templates"; otherwise, all data is retrieved.
 *
 * @since 0.9
 * @param string $content_type Content type to get data for
 * @param string $key Optionally specify a key to get data for
 * @return mixed All data (array) for content type or data for specific key
 */
function ctfw_content_type_data( $content_type, $key = false ) {

	$data = false;

	if ( ! empty( $content_type ) ) {

		$type_data = ctfw_content_types();

		if ( ! empty( $type_data[$content_type] ) ) {

			if ( ! empty( $key ) ) {
				if ( ! empty( $type_data[$content_type][$key] ) ) { // check for data
					$data = $type_data[$content_type][$key];
				}
			} else { // no key given, return all
				$data = $type_data[$content_type];
			}

		}

	}

	return apply_filters( 'ctfw_content_type_data', $data, $content_type, $key );

}

/**
 * Get data for current content type
 *
 * Specify a key, such as "page_templates"; otherwise, all data is retrieved.
 *
 * @since 0.9
 * @param string $key Optionally get data for specific key
 * @return mixed All data (array) for content type or data for specific key
 */
function ctfw_current_content_type_data( $key = false ) {

	// Get current content type
	$content_type = ctfw_current_content_type();

	// Get data
	$data = ctfw_content_type_data( $content_type, $key );

	// Return filterable
	return apply_filters( 'ctfw_current_content_type_data', $data, $key );

}

/**
 * Get content type based on page template
 *
 * @since 0.9
 * @param string $page_template Page template to get content type for
 * @return string Content type
 */
function ctfw_content_type_by_page_template( $page_template ) {

	$page_template_content_type = '';

	// Prepare page template
	$page_template = basename( $page_template ); // remove dir if has

	// Get types
	$content_types = ctfw_content_types();

	// Loop conent types
	foreach ( $content_types as $content_type => $content_type_data ) {

		// Check for page template
		if ( in_array( $page_template, $content_type_data['page_templates'] ) ) {
			$page_template_content_type = $content_type;
			break;
		}

	}

	// Return filtered
	return apply_filters( 'ctfw_content_type_by_page_template', $page_template_content_type, $page_template );

}
