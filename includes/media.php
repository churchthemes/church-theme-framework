<?php
/**
 * Media
 *
 * Image and video functions.
 */

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
 * Note: This framework feature must be enabled using add_theme_support( 'ctc-image-upscaling' )
 */

add_filter( 'image_resize_dimensions', 'ctc_image_resize_dimensions_upscale', 10, 6 );

function ctc_image_resize_dimensions_upscale( $output, $orig_w, $orig_h, $dest_w, $dest_h, $crop ) {

	// force upscaling if theme supports it and crop is being done
	// otherwise $output = null causes regular behavior
	if ( current_theme_supports( 'ctc-image-upscaling' ) && $crop ) {

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

/***********************************************
 * VIDEO
 ***********************************************/

/**
 * Video Code
 *
 * Return YouTube or Vimeo data, ID and HTML player code based on URL
 */
 
// CAN WP OEMBED HELP MAKE THIS SIMPLER?
// CAN WP OEMBED HELP MAKE THIS SIMPLER?
// CAN WP OEMBED HELP MAKE THIS SIMPLER?
// CAN WP OEMBED HELP MAKE THIS SIMPLER?
// CAN WP OEMBED HELP MAKE THIS SIMPLER?
// CAN WP OEMBED HELP MAKE THIS SIMPLER?

// MAKE NAME MORE DESCRIPTIVE
// MAKE NAME MORE DESCRIPTIVE
// MAKE NAME MORE DESCRIPTIVE
// MAKE NAME MORE DESCRIPTIVE
// MAKE NAME MORE DESCRIPTIVE

// MAKE FILTERABLE WITH ATTS?
// MAKE FILTERABLE WITH ATTS?
// MAKE FILTERABLE WITH ATTS?
// MAKE FILTERABLE WITH ATTS?
// MAKE FILTERABLE WITH ATTS?
 
function ctc_video( $video_url, $width = false, $height = false, $options = array() ) {

	$video = array();
	
	$video_url = isset( $video_url ) ? trim( $video_url ) : '';
	
	if ( ! empty( $video_url ) ) {

		// Default options
		$options['autoplay'] = ! empty( $options['autoplay'] ) ? '1' : '0';
			
		// YouTube
		if ( preg_match( '/youtu/i', $video_url ) ) {
		
			// source
			$video['source'] = 'youtube';
			
			// default size
			$width = ! empty( $width ) ? $width : 560;
			$height = ! empty( $height ) ? $height : 350;
			
			// video ID and embed code
			$video['video_id'] = '';
			$video['embed_code'] = '';
			preg_match( '/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/', $video_url, $match );
			if ( ! empty( $match[1] ) && strlen( $match[1] ) == 11 ) {
				$video['video_id'] = $match[1];
				$video['embed_code'] = '<iframe src="http://www.youtube.com/embed/' . $video['video_id'] . '?wmode=transparent&amp;autoplay=' . $options['autoplay'] . '&amp;rel=0&amp;showinfo=0&amp;color=white&amp;modestbranding=1" width="' . $width . '" height="' . $height . '" frameborder="0" allowfullscreen></iframe>';
			}				
			
		}
		
		// Vimeo
		else if ( preg_match( '/vimeo/i', $video_url ) ) {
		
			// source
			$video['source'] = 'vimeo';
			
			// default size
			$width = ! empty( $width ) ? $width : 500;
			$height = ! empty( $height ) ? $height : 281;
			
			// video ID and embed code
			$video['video_id'] = '';
			$video['embed_code'] = '';
			preg_match( '/\d+/', $video_url, $match );
			if ( ! empty( $match[0] ) ) {
				$video['video_id'] = $match[0];
				$video['embed_code'] = '<iframe src="http://player.vimeo.com/video/' . $video['video_id'] . '?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff&amp;autoplay=' . $options['autoplay'] . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			}
			
		}
		
		// Video Wrappers
		if ( ! empty( $video['embed_code'] ) ) {
			$video['embed_code'] = '<div class="ctc-embed"><div class="ctc-embed-inner">' . $video['embed_code'] . '</div></div>';
		}
	
	}
	
	return $video;		

}

/**
 * Wrap WordPress Embeds for Responsive Videos
 *
 * Use add_theme_support( 'ctc-embed-wrapping' ) to add containers to WordPress embeds so videos can be styled responsively.
 * See WordPress Embeds: http://codex.wordpress.org/Embeds.
 * 
 * You must add styles to the theme that make the embeds responsive. For example:
 *
 *		.ctc-embed {
 *			max-width: 100%;
 *			margin: 32px 0;
 *		}
 *
 *			.ctc-embed-inner {
 *				position: relative;
 *				height: 0;
 *				padding-bottom: 56.25%;
 *			}
 *
 *				.ctc-embed iframe,
 *				.ctc-embed embed,
 *				.ctc-embed object {
 *					position: absolute;
 *					top: 0;
 *					left: 0;
 *					width: 100%;
 *					height: 100%;
 *				}
 *
 * You can use jQuery to add the original video width to the .ctc-embed wrapper, preventing videos from always having 100% width.
 *
 *		// Add original video width as max-width to .ct-embed wrapper to prevent oversized videos
 *		$( '.ctc-embed' ).each( function() {
 *			$( this ).css( 'width', $( 'iframe, embed, object', this ).prop( 'width' ) + 'px' );
 *		});
 *
 */

add_filter( 'embed_oembed_html', 'ctc_responsive_embeds', 10, 4 ); // make WordPress video embeds responsive by giving container to style	

function ctc_responsive_embeds( $html, $url, $attr, $post_ID ) {

	if ( current_theme_supports( 'ctc-embed-wrapping' ) ) {

		// Future: could detect media source then apply class for assigning source-specific ratios

		// Only certain embed types (no img)
		if ( preg_match( '/^<(iframe|embed|object)/', $html ) ) {
			$html = '<div class="ctc-embed"><div class="ctc-embed-inner">' . $html . '</div></div>';
		}

	}

	return $html;

}
