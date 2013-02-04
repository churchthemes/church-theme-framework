<?php
/**
 * Media
 *
 * Image and video functions.
 */

/**
 * Responsive Embeds
 *
 * Add container to WordPress video embeds so it can be styled responsive
 * See embed_oembed_html filter
 * See WordPress embeds: http://codex.wordpress.org/Embeds
 */

add_filter( 'embed_oembed_html', 'ctc_responsive_embeds', 10, 4 ); // make WordPress video embeds responsive by giving container to style	

function ctc_responsive_embeds( $html, $url, $attr, $post_ID ) {

	if ( preg_match( '/^<(iframe|embed|object)/', $html ) ) { // no img
		$html = '<div class="ctc-responsive-embed">' . $html . '</div>';
	}

	return $html;

}

/**
 * Video
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
		
		// Video Container
		if ( ! empty( $video['embed_code'] ) ) {
			$video['embed_code'] = '<div class="ctc-video-container ctc-' . $video['source'] . '-video">' . $video['embed_code'] . '</div>';
		}
	
	}
	
	return $video;		

}

/**
 * Prevent extra 10px width WordPress adds to .wp-caption via shortcode
 *
 * Cleaner Caption - Cleans up the WP [caption] shortcode.
 *
 * WordPress adds an inline style to its [caption] shortcode which specifically adds 10px of extra width to 
 * captions, making theme authors jump through hoops to design captioned elements to their liking.  This extra
 * width makes the assumption that all captions should have 10px of extra padding to account for a box that 
 * wraps the element.  This script changes the width to match that of the 'width' attribute passed in through
 * the shortcode, allowing themes to better handle how their captions are designed.
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU 
 * General Public License version 2, as published by the Free Software Foundation.  You may NOT assume 
 * that you can use any other version of the GPL.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without 
 * even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * @package CleanerCaption
 * @version 0.1.1
 * @author Justin Tadlock <justin@justintadlock.com>
 * @copyright Copyright (c) 2011, Justin Tadlock
 * @link http://justintadlock.com
 * @license http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */

// IS THIS STILL AN ISSUE W/WP 3.5+?
// IS THIS STILL AN ISSUE W/WP 3.5+?
// IS THIS STILL AN ISSUE W/WP 3.5+?
// IS THIS STILL AN ISSUE W/WP 3.5+?
// IS THIS STILL AN ISSUE W/WP 3.5+?
// IS THIS STILL AN ISSUE W/WP 3.5+?
// IS THIS STILL AN ISSUE W/WP 3.5+?
// IS THIS STILL AN ISSUE W/WP 3.5+?
 
add_filter( 'img_caption_shortcode', 'ctc_cleaner_caption', 10, 3 ); // fix an issue with WordPress adding 10px to caption shortcode

function ctc_cleaner_caption( $output, $attr, $content ) {

	/* We're not worried abut captions in feeds, so just return the output here. */
	if ( is_feed() )
		return $output;

	/* Set up the default arguments. */
	$defaults = array(
		'id' => '',
		'align' => 'alignnone',
		'width' => '',
		'caption' => ''
	);

	/* Allow developers to override the default arguments. */
	$defaults = apply_filters( 'cleaner_caption_defaults', $defaults );

	/* Apply filters to the arguments. */
	$attr = apply_filters( 'cleaner_caption_args', $attr );

	/* Merge the defaults with user input. */
	$attr = shortcode_atts( $defaults, $attr );

	/* If the width is less than 1 or there is no caption, return the content wrapped between the [caption] tags. */
	if ( 1 > $attr['width'] || empty( $attr['caption'] ) )
		return $content;

	/* Set up the attributes for the caption <div>. */
	$attributes = ( ! empty( $attr['id'] ) ? ' id="' . esc_attr( $attr['id'] ) . '"' : '' );
	$attributes .= ' class="wp-caption ' . esc_attr( $attr['align'] ) . '"';
	$attributes .= ' style="width: ' . esc_attr( $attr['width'] ) . 'px"';

	/* Open the caption <div>. */
	$output = '<div' . $attributes .'>';

	/* Allow shortcodes for the content the caption was created for. */
	$output .= do_shortcode( $content );

	/* Append the caption text. */
	$output .= '<p class="wp-caption-text">' . $attr['caption'] . '</p>';

	/* Close the caption </div>. */
	$output .= '</div>';

	/* Return the formatted, clean caption. */
	return apply_filters( 'cleaner_caption', $output );

}
