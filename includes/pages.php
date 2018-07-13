<?php
/**
 * Page Functions
 *
 * These functions apply to the page post type only.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2015, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**********************************
 * DATA
 **********************************/

/**
 * Get page by template
 *
 * Get newest page using a specific template file name.
 *
 * Multiple templates can be specified as array and the first match will be used.
 * This is handy when one template rather than the usual is used for primary content.
 *
 * @since 0.9
 * @param string|array $templates Template or array of templates (first match used)
 * @return object Page data
 */
function ctfw_get_page_by_template( $templates ) {

	$page = false;

	// Force single template string into array
	$templates = (array) $templates;

	// Loop template by priority
	foreach ( $templates as $template ) {

		// Templates are stored in directory
		$template = CTFW_THEME_PAGE_TPL_DIR . '/' . basename( $template );

		// If more than one, gets the newest
		// Note: Using get_posts() fails for pages that have parent(s) so using WP_Query directly
		$page_query = new WP_Query( array(
			'post_type'			=> 'page',
			'nopaging'			=> true,
			'posts_per_page'	=> 1,
			'meta_key' 			=> '_wp_page_template',
			'meta_value' 		=> $template,
			'orderby'			=> 'ID',
			'order'				=> 'DESC',
			'no_found_rows'		=> true, // faster (no pagination)
		) );

		// Got one?
		if ( ! empty( $page_query->post ) ) {
			$page = $page_query->post;
			break; // if not check next template
		}

	}

	return apply_filters( 'ctfw_get_page_by_template', $page, $templates );

}

/**
 * Get page ID by template
 *
 * Get newest page ID using a specific template file name.
 *
 * Multiple templates can be specified as array and the first match will be used.
 * This is handy when one template rather than the usual is used for primary content.
 *
 * @since 0.9
 * @param string|array $templates Template or array of templates (first match used)
 * @return int Page ID
 */
function ctfw_get_page_id_by_template( $templates ) {

	$page = ctfw_get_page_by_template( $templates );

	$page_id = ! empty( $page->ID ) ? $page->ID : '';

	return apply_filters( 'ctfw_get_page_id_by_template', $page_id, $templates );

}


/**
 * Get page URL by template
 *
 * @since 1.7.1
 * @param string|array $templates Template or array of templates (first match used)
 * @return int Page URL, if page was found; otherwise empty
 */
function ctfw_get_page_url_by_template( $templates ) {

	$url = '';

	$page = ctfw_get_page_by_template( $templates );

	if ( $page ) {
		$url = get_permalink( $page );
	}

	return apply_filters( 'ctfw_get_page_url_by_template', $url, $templates );

}

/**
 * Page options
 *
 * Handy for making select options
 *
 * @since 0.9
 * @param bool $allow_none Whether or not to include option for none
 * @return array Page options
 */
function ctfw_page_options( $allow_none = true ) {

	$pages = get_pages( array(
		'hierarchical' => false,
	) );

	$page_options = array();

	if ( ! empty( $allow_none ) ) {
		$page_options[] = '';
	}

	foreach ( $pages as $page ) {
		$page_options[$page->ID] = $page->post_title;
	}

	return $page_options;

}
