<?php
/**
 * Gallery Functions
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

/***********************************************
 * GALLERIES
 ***********************************************/

/**
 * Make gallery shortcode use rectangular size by default
 *
 * Otherwise, it uses 'thumbnail' size as defined in Settings > Media, which by default is square.
 *
 * Basic usage:
 *
 * 		add_theme_support( 'ctfw-gallery-thumb-size', 'custom-size' );
 *
 * Column-specific usage:
 *
 *		add_theme_support( 'ctfw-gallery-thumb-size', array(
 *			'1' => 'large',					// use this size when 1 column
 *			'2' => 'custom-size',	 		// use this size when 2 columns
 *			'3' => 'another-custom-size', 	// use this size when 3 columns
 *			'9' => 'other-custom-size',  	// use this size when any other number of columns used
 *		) );
 *
 * @since 0.9
 */
function ctfw_gallery_thumb_size( $out, $pairs, $atts ) {

	// Always use size specifically set on shortcode
	if ( ! empty( $atts['size'] ) ) {
		return $out;
	}

	// Use custom size based on column only if theme supports it
	$support = $support = get_theme_support( 'ctfw-gallery-thumb-size' );
	if ( ! empty( $support[0] ) ) {

		$sizes = $support[0];

		// Single size specified
		if ( ! is_array( $sizes ) ) {
			$out['size'] = $sizes;
		}

		// Sizes for different columns specified
		else {

			// Sort highest column to lowest
			krsort( $sizes );

			// Number of columns showing based on shortcode attribute or default
			$columns = ! empty( $atts['columns'] ) ? $atts['columns'] : $pairs['columns'];

			// Loop sizes to set most appropriate
			foreach ( $sizes as $size_column => $size ) {
				if ( $columns <= $size_column ) {
					$out['size'] = $size;
				}
			}

		}

	}

	return $out;

}

add_filter( 'shortcode_atts_gallery', 'ctfw_gallery_thumb_size', 10, 3 );

/**
 * Remove default gallery styles
 *
 * WordPress injects <style> with gallery styles in shortcode output.
 * It is better to do all styling in style.css.
 *
 * @since 0.9
 */
function ctfw_remove_gallery_styles() {

	if ( current_theme_supports( 'ctfw-remove-gallery-styles' ) ) {
		add_filter( 'use_default_gallery_style', '__return_false' );
	}

}

add_filter( 'init', 'ctfw_remove_gallery_styles' );

/**
 * Remove prepend_attachment content filter
 *
 * This keeps the_content() from outputting a thumbnail or link to file.
 * WordPress does this when an attachment template is used (images.php, attachment.php, etc.)
 * Do the same thing when custom attachment templates such as content-attachment.php are used.
 *
 * Enable with add_theme_support( 'ctfw-remove-prepend-attachment' )
 *
 * @since 0.9
 */
function ctfw_remove_prepend_attachment() {

	if ( is_attachment() && current_theme_supports( 'ctfw-remove-prepend-attachment' ) ) {
		remove_filter( 'the_content', 'prepend_attachment' );
	}

}

add_filter( 'wp', 'ctfw_remove_prepend_attachment' ); // conditionals like is_attachment() not available until 'wp' action

/**
 * Get post's gallery data
 *
 * Extract gallery shortcode data from content  (unique image IDs, total count, shortcode attribures, etc.).
 *
 * @since 0.9
 * @param object $post Post object
 * @param array $options Options for getting data
 * @return array Galleries data
 */
function ctfw_post_galleries_data( $post, $options = array() ) {

	// Default data
	$data = array(
		'image_ids'		=> array(),
		'image_count'	=> 0,
		'galleries'		=> array(),
	);

	// Gather IDs from all gallery shortcodes in content
	// This is based on the core get_content_galleries() function but slimmed down to do only what is needed
	if ( preg_match_all( '/' . get_shortcode_regex() . '/s', $post->post_content, $matches, PREG_SET_ORDER ) && ! empty( $matches ) ) {

		$galleries_data = array();
		$galleries_image_ids = array();
		$got_attached_images = false;

		// Loop matching shortcodes
		foreach ( $matches as $shortcode ) {

			// Gallery shortcodes only
			if ( 'gallery' === $shortcode[2] ) {

				// Get shortcode attributes
				$gallery_data = shortcode_parse_atts( $shortcode[3] );
				$galleries_data[] = $galleries_data;

				// Has ID attributes, get 'em
				if ( ! empty( $gallery_data['ids'] ) ) {

					// Loop IDs from gallery shortcode
					$gallery_ids_raw = explode( ',', $gallery_data['ids'] );
					foreach ( $gallery_ids_raw as $gallery_id ) {

						// Remove whitespace and exclude empty values (ie. ", 12, ,42,")
						if ( $gallery_id = trim( $gallery_id ) ) {

							// Add to array containing imag IDs from all galleries in post
							$galleries_image_ids[] = $gallery_id;

						}

					}

				}

				// No ID attributes, in which case all attached images shown - get 'em
				elseif ( ! $got_attached_images ) {

					// Don't run more than once per post
					$got_attached_images = true;

					// Get all attached images for this post
					$images = get_children( array(
						'post_parent' => $post->ID,
						'post_type' => 'attachment',
						'post_status' => 'inherit', // for attachments
						'post_mime_type' => 'image',
						'numberposts' => -1, // all
						'orderby' => 'menu_order', // want first manually ordered ('Add Media > Uploaded to this page' lets drag order)
						'order' => 'ASC'
					) ) ;

					// Found some?
					if ( ! empty( $images ) ) {

						// Add to array containing image IDs from all galleries in post
						$attached_image_ids = array_keys( $images );
						$galleries_image_ids = array_merge( $galleries_image_ids, $attached_image_ids );

					}

				}

			}

		}

		// Did we find some images?
		if ( $galleries_image_ids ) {

			// Remove duplicates
			$galleries_image_ids = array_unique( $galleries_image_ids );

			// Build array of data
			$data['image_ids'] = $galleries_image_ids;
			$data['image_count'] = count( $galleries_image_ids );
			$data['galleries'] = $galleries_data;

		}

	}

	// Return filterable
	return apply_filters( 'ctfw_post_galleries_data', $data, $post );

}

/**
 * Get gallery posts
 *
 * This gets all posts that have a gallery.
 *
 * @since 0.9
 * @param array $options Options for getting posts
 * @return array Gallery posts
 */
function ctfw_gallery_posts( $options = array() ) {

	$gallery_posts = array();

	// Defaults
	$options = wp_parse_args( $options, array(
		'orderby'		=> 'modified',
		'order'			=> 'desc',
		'limit'			=> -1, // no limit
		'extract_data'	=> true, // false to skip that for optimization
		'exclude_empty'	=> true, // works only when 'extract_data' is true
		'post_id'		=> ''
	) );

	// If no extract_data, force exclude_empty false (since it is not possible)
	$options['exclude_empty'] = ! $options['extract_data'] ? false : $options['exclude_empty'];

	// Query arguments
	$args = array(
		'p'					=> $options['post_id'], // if getting one
		'post_type'			=> array( 'page', 'post', 'ctc_sermon', 'ctc_event', 'ctc_person', 'ctc_location' ),
		'orderby'			=> $options['orderby'],
		'order'				=> $options['order'],
		'posts_per_page'	=> $options['limit'],
		'no_found_rows'		=> true, // faster
	);
	$args = apply_filters( 'ctfw_gallery_posts_args', $args, $options );

	// Get posts
    add_filter( 'posts_where', 'ctfw_gallery_posts_where' ); // modify query to search content for [gallery] shortcode so not all posts are gotten
	$posts_query = new WP_Query( $args );
    remove_filter( 'posts_where', 'ctfw_gallery_posts_where' ); // stop filtering WP_Query

	// Compile post's gallery data
	if ( ! empty( $posts_query->posts ) ) {

		// Loop posts
		foreach ( $posts_query->posts as $post ) {

			// Get gallery data unless option prevents it
			$galleries_data = $options['extract_data'] ? ctfw_post_galleries_data( $post ) : array();

			// Add post and gallery data to array
			if ( ! ( $options['exclude_empty'] && empty( $galleries_data['image_count'] ) ) ) {

				// Add post data to array
				$gallery_posts[$post->ID]['post'] = $post;

				// Add gallery data to array
				$gallery_posts[$post->ID] = array_merge( $gallery_posts[$post->ID], $galleries_data );

			}

		}

	}

	// Return filterable
	return apply_filters( 'ctfw_gallery_posts', $gallery_posts, $options );

}

/**
 * Filter gallery posts query to get only those with [gallery] shortcode
 *
 * This way not all posts are gotten; only post with galleries.
 *
 * @since 0.9
 * @param string $where Original SQL WHERE clause
 * @return string Modified WHERE clause
 */
function ctfw_gallery_posts_where( $where ) {

	global $wpdb;

	// Append search for gallery shortcode
	$where .= $wpdb->prepare(
		" AND $wpdb->posts.post_content LIKE %s",
		'%[gallery%'
	);

	return $where;

}

/**
 * Get gallery posts IDs
 *
 * Get IDs of all pages/posts with gallery content.
 *
 * @since 0.9
 * @param array $options Options for getting gallery posts IDs
 * @return array Posts IDs
 */
function ctfw_gallery_posts_ids( $options = array() ) {

	// Do not extract data in this case, just need IDS
	$options['extract_data'] = false; // optimization

	// Get posts/pages with IDs
	$gallery_posts = ctfw_gallery_posts( $options );

	// Put IDs into array
	$ids = array_keys( $gallery_posts );

	// Return filtered
	return apply_filters( 'ctfw_gallery_posts_ids', $ids );

}

/**
 * Post gallery preview
 *
 * Show X rows of thumbnails from post content with gallery shortcode(s).
 * The shortcode column attribute from the first gallery will be used.
 *
 * @since 0.9
 * @param object $post Post to make gallery preview for
 * @param array $options Options for preview display
 * @return string Gallery shortcode output
 */
function ctfw_post_gallery_preview( $post, $options = array() ) {

	$preview = '';

	// Option defaults
	$options = wp_parse_args( $options, array(
		'rows' => 2,
		'columns' => '' // inherit from shortcode
	) );
	$options = apply_filters( 'ctfw_post_gallery_preview_options', $options );

	// Get data from galleries used in post
	$galleries_data = ctfw_post_galleries_data( $post );

	// Found at least one gallery with image?
	if ( ! empty( $galleries_data['image_count'] ) ) {

		// Get columns attribute from first gallery shortcode
		$first_gallery_columns = ! empty( $galleries_data['galleries'][0]['columns'] ) ? $galleries_data['galleries'][0]['columns'] : '';

		// Show limited number of rows
		$rows = $options['rows'];
		$columns = ! empty( $options['columns'] ) ? $options['columns'] : $first_gallery_columns; // inherit from first shortcode or use default
		$limit = $rows * $columns; // based on columns
		$ids = array_slice( $galleries_data['image_ids'], 0, $limit ); // truncate
		$ids = implode( ',', $ids ); // form as list

		// Build gallery HTML
		$preview = gallery_shortcode( array(
			'columns'	=> $columns,
			'ids'		=> $ids
		) );

	}

	// Return filterable
	return apply_filters( 'ctfw_post_gallery_preview', $preview, $post, $options );

}
