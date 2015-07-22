<?php
/**
 * Image Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2015, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/***********************************************
 * SIZES
 ***********************************************/

/**
 * Output image size dimensions
 *
 * Pass in image size to return 123x123
 *
 * @since 0.9
 * @global array $_wp_additional_image_sizes
 * @param string $size Image size name to get dimensions for
 * @return string Dimensions for image size
 */
function ctfw_image_size_dimensions( $size ) {

	global $_wp_additional_image_sizes;

	$dimensions = '';

	if ( isset( $_wp_additional_image_sizes[$size] ) ) {
		$dimensions = $_wp_additional_image_sizes[$size]['width'] . 'x' . $_wp_additional_image_sizes[$size]['height'];
	}

	return apply_filters( 'ctfw_image_size_dimensions', $dimensions );


}

/**
 * Add note below Featured Image
 *
 * Each post type has a recommended image size.
 * See includes/support.php for notes on Highlight and Slide widgets.
 *
 * @since 0.9
 * @param string $content Featured image meta box content
 * @return string Content with image size note appended
 */
function ctfw_featured_image_notes( $content ) {

	// Theme supports this?
	$support = get_theme_support( 'ctfw-featured-image-notes' );
	if ( ! empty( $support[0] ) ) {

		// Get post type size data
		$post_types = $support[0];

		// Get admin screen
		$screen = get_current_screen();
		if ( ! empty( $screen->post_type ) && ! empty( $post_types[$screen->post_type] ) ) {

			// Get post type data
			$post_type_data = $post_types[$screen->post_type];

			// Size and specific message specified
			if ( is_array( $post_type_data ) && ! empty( $post_type_data[0] ) && ! empty( $post_type_data[1] ) ) {
				$size = $post_type_data[0];
				$message = $post_type_data[1];
			}

			// Only size specified (use default message)
			elseif ( ! empty( $post_types[$screen->post_type] ) ) {
				$size = $post_types[$screen->post_type];
				$message = ! empty( $support[1] ) ? $support[1] : __( 'The target image size is %s.', 'church-theme-framework' ); // third argument, if any
			}

			// Show message
			if ( isset( $size ) && isset( $message ) ) {

				// Get dimensions for size
				$dimensions = ctfw_image_size_dimensions( $size );
				if ( $dimensions ) {

					// Add dimensions to message
					$message = sprintf( $message, $dimensions );

					// Apply the note for the appropriate post type
					$content .= '<p class="description">' . esc_html( $message  ) . '</p>';

				}

			}

		}

	}

	// Return content with note appended
	return $content;

}

add_filter( 'admin_post_thumbnail_html', 'ctfw_featured_image_notes' );

/***********************************************
 * RESIZING
 ***********************************************/

/**
 * Enable upscaling of images
 *
 * Normally WordPress will only generate resized/cropped images if the source is larger than the target.
 * This forces an image to be made for all sizes, even if the source is smaller than the target.
 * This makes responsive images work more consistently (automatic height via CSS, for example).
 *
 * This code is based on the core image_resize_dimensions() function in wp-content/media.php.
 *
 * Note: This framework feature must be enabled using add_theme_support( 'ctfw-image-upscaling' )
 *
 * Second argument is array of arguments. You can exclude upscaling for certain image size names.
 * This is helpful for make sure a large background image version is not generated for every image uploaded.
 *
 * add_theme_support( 'ctfw-image-upscaling', array(
 *    'excluded_sizes' => array( 'themename-sizename' ),
 * ) );
 *
 * @since 0.9
 * @param array $output Current output
 * @param int $orig_w Original width
 * @param int $orig_h Original height
 * @param int $dest_w New width
 * @param int $dest_h New height
 * @param bool $crop Whether or not to crop
 * @return array Modified $output
 * @global array $_wp_additional_image_sizes
 */
function ctfw_image_resize_dimensions_upscale( $output, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {

	global $_wp_additional_image_sizes;

	$support = get_theme_support( 'ctfw-image-upscaling' );

	// force upscaling if theme supports it and crop is being done
	// otherwise $output = null causes regular behavior
	if ( $support && $crop ) {

		// check if size is excluded
		$exclude = false;
		if ( ! empty( $support[0]['excluded_sizes'] ) ) {

			$excluded_sizes = (array) $support[0]['excluded_sizes'];

			foreach ( $excluded_sizes as $excluded_size ) {

				// Stop upscaling if excluded image size matches destination size
				if ( $_wp_additional_image_sizes[$excluded_size]['width'] == $dest_w && $_wp_additional_image_sizes[$excluded_size]['height'] == $dest_h ) {

					$exclude = true;

					break;

				}

			}

		}

		// force upscaling
		if ( ! $exclude ) {

			// resize to target dimensions, upscaling if necessary
			$new_w = $dest_w;
			$new_h = $dest_h;

			$size_ratio = max( $new_w / $orig_w, $new_h / $orig_h );

			$crop_w = round( $new_w / $size_ratio );
			$crop_h = round( $new_h / $size_ratio );

			$s_x = floor( ( $orig_w - $crop_w ) / 2 );
			$s_y = floor( ( $orig_h - $crop_h ) / 2 );

			// the return array matches the parameters to imagecopyresampled()
			// int dst_x, int dst_y, int src_x, int src_y, int dst_w, int dst_h, int src_w, int src_h
			$output = array( 0, 0, (int) $s_x, (int) $s_y, (int) $new_w, (int) $new_h, (int) $crop_w, (int) $crop_h );

		}

	}

	return $output;

}

add_filter( 'image_resize_dimensions', 'ctfw_image_resize_dimensions_upscale', 10, 6 );
