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

/**
 * Output image size dimensions
 *
 * Pass in image size to return 123x123
 */

function ctc_image_size_dimensions( $size ) {

	global $_wp_additional_image_sizes;

	$dimensions = '';

	if ( isset( $_wp_additional_image_sizes[$size] ) ) {
		$dimensions = $_wp_additional_image_sizes[$size]['width'] . 'x' . $_wp_additional_image_sizes[$size]['height'];
	}

	return apply_filters( 'ctc_image_size_dimensions', $dimensions );


}

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
 * 		add_theme_support( 'ctc-gallery-thumb-size', 'custom-size' );
 *
 * Column-specific usage:
 *
 *		add_theme_support( 'ctc-gallery-thumb-size', array(
 *			'1' => 'large',					// use this size when 1 column
 *			'2' => 'custom-size',	 		// use this size when 2 columns
 *			'3' => 'another-custom-size', 	// use this size when 3 columns
 *			'9' => 'other-custom-size',  	// use this size when any other number of columns used
 *		) );
 */

add_filter( 'shortcode_atts_gallery', 'ctc_gallery_thumb_size', 10, 3 );

function ctc_gallery_thumb_size( $out, $pairs, $atts ) {

	// Always use size specifically set on shortcode
	if ( ! empty( $atts['size'] ) ) {
		return $out;
	}

	// Use custom size only if theme supports it
	if ( $support = get_theme_support( 'ctc-gallery-thumb-size' ) ) { // returns false if feature not supported

		// Use custom size based on column
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

	}

	return $out;

}

/**
 * Remove default gallery styles
 *
 * WordPress injects <style> with gallery styles in shortcode output.
 * It is better to do all styling in style.css.
 */

add_filter( 'init', 'ctc_remove_gallery_styles' );

function ctc_remove_gallery_styles() {

	if ( current_theme_supports( 'ctc-remove-gallery-styles' ) ) {
		add_filter( 'use_default_gallery_style', '__return_false' );
	}

}

/**
 * Remove prepend_attachment content filter
 *
 * WordPress does this when an attachment template is used (images.php, attachment.php, etc.)
 * Do the same thing automatically when content-attachment.php is used.
 *
 * This keeps the_content() from outputting a thumbnail or link to file.
 */

add_filter( 'get_template_part_content', 'ctc_remove_prepend_attachment', 10, 2 );

function ctc_remove_prepend_attachment( $slug, $name ) {

	if ( 'attachment' == $name ) {
		remove_filter( 'the_content', 'prepend_attachment' );
	}

}

/**
 * Get Gallery Pages
 *
 * This gets all pages that have a gallery.
 */

function ctc_gallery_pages( $options = array() ) {

	// Defaults
	$options = wp_parse_args( $options, array(
		'orderby'		=> 'title',
		'order'			=> 'ASC',
		'image_ids'		=> true,
		'post_id'		=> ''
	) );

	// Get gallery page template(s)
	$page_templates = ctc_content_type_data( 'gallery', 'page_templates' );
	foreach ( $page_templates as $page_template_key => $page_template ) { // prepend page templates dir to each
		$page_templates[$page_template_key] = CTC_PAGE_TPL_DIR . '/' . $page_template;
	}

	// Get pages using a gallery template
	$pages_query = new WP_Query( array(
		'p'				=> $options['post_id'], // if getting one
		'post_type'		=> 'page',
		'nopaging'		=> true,
		'meta_query'	=> array(
			array(
	        	'key' => '_wp_page_template',
	        	'value' => $page_templates,
	        	'compare' => 'IN',
			)
		),
		'orderby'		=> $options['orderby'],
		'order'			=> $options['order'],
		'no_found_rows'		=> true // faster
	) );

	// Narrow to those having gallery shortcode, compile gallery data
	$pattern = get_shortcode_regex();
	$gallery_pages = array();
	if ( ! empty( $pages_query->posts ) ) {

		// Loop pages
		foreach ( $pages_query->posts as $page ) {

			// Continue only if has [gallery] shortcode(s)
			if ( preg_match_all( '/'. $pattern . '/s', $page->post_content, $matches ) && array_key_exists( 2, $matches ) && in_array( 'gallery', $matches[2] ) ) {

				$ids = array();
				$all_attached_images = false;

				// Get the gallery IDs
				if ( $options['image_ids'] ) {

					// Loop shortcodes found
					foreach ( $matches[2] as $key => $shortcode_name ) {

						// Is it a gallery shortcode?
						if ( 'gallery' == $shortcode_name ) {

							// Get attributes
							$attributes = shortcode_parse_atts( $matches[3][$key] ); // convert string to array

							// Get IDs from attribute, if any
							$extracted_ids = array();
							if ( ! empty( $attributes['ids'] ) ) {

								// Convert ID list to array
								$extracted_ids = explode( ',', $attributes['ids'] );

								// Clean up
								$extracted_ids = array_map( 'trim', $extracted_ids ); // Trim all ID's (in case "1, 2, 3" instead of "1,2,3")
								$extracted_ids = array_filter( $extracted_ids ); // Remove empty values (ie. ",1, ,2")

							}

							// No IDs attribute found in shortcode or it was empty
							// In that case, shortcode shows all attached images, so get them here
							if ( empty( $extracted_ids ) && empty( $all_attached_images ) ) {

								// Don't run more than once per page
								$all_attached_images = true;

								// Get all attached images for this page
								$images = get_children( array(
									'post_parent' => $page->ID,
									'post_type' => 'attachment',
									'post_status' => 'inherit', // for attachments
									'post_mime_type' => 'image',
									'numberposts' => -1 // all
								) ) ;

								// Found some?
								if ( ! empty( $images ) ) {
									$extracted_ids = array_keys( $images );
								}

							}

							// Add ID's from this shortcode to array for page
							if ( ! empty( $extracted_ids ) ) {
								$ids = array_merge( $ids, $extracted_ids );
							}

						}

					}

					// Remove duplicates
					$ids = array_unique( $ids ); // Remove duplicates

				}

				// Add page data to array
				$gallery_pages[$page->ID]['page'] = $page;
				$gallery_pages[$page->ID]['image_ids'] = $ids;
				$gallery_pages[$page->ID]['image_count'] = count( $ids );

			}

		}

	}

	// Return filterable
	return apply_filters( 'ctc_gallery_pages', $gallery_pages, $options );

}

/**
 * Get Gallery Page IDs
 */

function ctc_gallery_pages_ids() {

	$ids = array();

	$gallery_pages = ctc_gallery_pages( array( 'image_ids' => false ) );

	foreach ( $gallery_pages as $page_id => $page_data ) {
		$ids[] = $page_id;
	}

	return apply_filters( 'ctc_gallery_pages_ids', $ids );

}

/**
 * Gallery preview
 *
 * Show X rows of thumbnails from content with gallery shortcode.
 * The shortcodes column attribute will be used. Only the first gallery shortcode is searched.
 */
function ctc_gallery_preview( $content = false, $options = array() ) {

	$gallery_preview = '';

	// Use current post content if none given
	if ( $search_content = ! empty( $content ) ? $content : get_the_content() ) {

		// Option defaults
		$options = wp_parse_args( $options, array(
			'rows' => 2,
			'columns' => '' // inherit from shortcode
		) );

		// Get data from first gallery shortcode in post
		$galleries_data = get_content_galleries( $search_content, false, false, 1 );

		// Gallery data found
		if ( isset( $galleries_data[0] ) && $gallery_data = $galleries_data[0] ) {

			// Clean up gallery IDs
			$gallery_ids = array();
			$gallery_ids_raw = explode( ',', $gallery_data['ids'] );
			foreach ( $gallery_ids_raw as $gallery_id ) {
				if ( $gallery_id = trim( $gallery_id ) ) { // remove whitespace and empty values from IDs attribute
					$gallery_ids[] = $gallery_id;
				}
			}

			// Show limited number of rows
			$gallery_rows = apply_filters( 'ctc_short_content_gallery_rows', $options['rows'] );
			$gallery_columns = ! empty( $options['columns'] ) ? $options['columns'] : $gallery_data['columns']; // inherit from shortcode or use default
			$gallery_items = $gallery_rows *$gallery_columns; // based on columns
			$gallery_ids = array_slice( $gallery_ids, 0, $gallery_items ); // truncate
			$gallery_ids = implode( ',', $gallery_ids ); // reform as list

			// Build gallery HTML
			$gallery_preview = do_shortcode( '[gallery columns="' .$gallery_columns . '" ids="' . $gallery_ids . '"]' );

		}

	}

	// Return filterable
	return apply_filters( 'ctc_gallery_preview', $gallery_preview, $content );

}

/***********************************************
 * VIDEO
 ***********************************************/

/**
 * Video data and embed code
 *
 * Return YouTube or Vimeo data, ID and HTML player code based on URL
 */
 
function ctc_video_data( $video_url, $options = array() ) {

	$video_data = array(
		'source'		=> '',
		'video_id'		=> '',
		'embed_code'	=> ''
	);
	
	$video_url = isset( $video_url ) ? trim( $video_url ) : '';
	
	if ( ! empty( $video_url ) ) {

		// Default options
		$options['autoplay'] = ! empty( $options['autoplay'] ) ? '1' : '0';
			
		// YouTube
		if ( preg_match( '/youtu/i', $video_url ) ) {
		
			// source
			$video_data['source'] = 'youtube';
			
			// default size
			$width = ! empty( $options['width'] ) ? $options['width'] : 560;
			$height = ! empty( $options['height'] ) ? $options['height'] : 350;
			
			// video ID and embed code
			$video_data['video_id'] = '';
			$video_data['embed_code'] = '';
			preg_match( '/.*(?:youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=)([^#\&\?]*).*/', $video_url, $match );
			if ( ! empty( $match[1] ) && strlen( $match[1] ) == 11 ) {
				$video_data['video_id'] = $match[1];
				$video_data['embed_code'] = '<iframe src="' . ctc_current_protocol() . '://www.youtube.com/embed/' . $video_data['video_id'] . '?wmode=transparent&amp;autoplay=' . $options['autoplay'] . '&amp;rel=0&amp;showinfo=0&amp;color=white&amp;modestbranding=1" width="' . $width . '" height="' . $height . '" frameborder="0" allowfullscreen></iframe>';
			}				
			
		}
		
		// Vimeo
		else if ( preg_match( '/vimeo/i', $video_url ) ) {
		
			// source
			$video_data['source'] = 'vimeo';
			
			// default size
			$width = ! empty( $options['width'] ) ? $options['width'] : 500;
			$height = ! empty( $options['height'] ) ? $options['height'] : 281;
			
			// video ID and embed code
			$video_data['video_id'] = '';
			$video_data['embed_code'] = '';
			preg_match( '/\d+/', $video_url, $match );
			if ( ! empty( $match[0] ) ) {
				$video_data['video_id'] = $match[0];
				$video_data['embed_code'] = '<iframe src="' . ctc_current_protocol() . '://player.vimeo.com/video/' . $video_data['video_id'] . '?title=0&amp;byline=0&amp;portrait=0&amp;color=ffffff&amp;autoplay=' . $options['autoplay'] . '" width="' . $width . '" height="' . $height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
			}
			
		}
		
		// Video Wrappers
		if ( ! empty( $video_data['embed_code'] ) ) {
			$video_data['embed_code'] = '<div class="ctc-embed"><div class="ctc-embed-inner">' . $video_data['embed_code'] . '</div></div>';
		}
	
	}

	return apply_filters( 'ctc_video_data', $video_data, $video_url, $options );

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
