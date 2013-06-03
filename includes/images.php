<?php
/**
 * Image Functions
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/***********************************************
 * IMAGES
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
 */

add_filter( 'image_resize_dimensions', 'ctfw_image_resize_dimensions_upscale', 10, 6 );

function ctfw_image_resize_dimensions_upscale( $output, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {

	// force upscaling if theme supports it and crop is being done
	// otherwise $output = null causes regular behavior
	if ( current_theme_supports( 'ctfw-image-upscaling' ) && $crop ) {

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

	return $output;

}

/**
 * Output image size dimensions
 *
 * Pass in image size to return 123x123
 */

function ctfw_image_size_dimensions( $size ) {

	global $_wp_additional_image_sizes;

	$dimensions = '';

	if ( isset( $_wp_additional_image_sizes[$size] ) ) {
		$dimensions = $_wp_additional_image_sizes[$size]['width'] . 'x' . $_wp_additional_image_sizes[$size]['height'];
	}

	return apply_filters( 'ctfw_image_size_dimensions', $dimensions );


}

