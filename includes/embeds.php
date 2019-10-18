<?php
/**
 * Embed Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2019, ChurchThemes.com, LLC
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/***********************************************
 * EMBEDS
 ***********************************************/

/**
 * Embed code based on audio/video URL or provided embed code
 *
 * If content is URL, use oEmbed to get embed code. If content is not URL, assume it is
 * embed code and run do_shortcode() in case of [video], [audio] or [embed]
 *
 * @since 0.9
 * @param string $content URL
 */
function ctfw_embed_code( $content ) {

	global $wp_embed;

	// Convert URL into media shortcode like [audio] or [video]
	if ( ctfw_is_url( $content ) ) {

		$embed_code = '';

		// Use native HTML 5 <audio> or <video> player.
		if ( current_theme_supports( 'ctfw-native-player' ) ) {

			$url = $content;

			// Get file type.
			$filetype = wp_check_filetype( $url );

			// Audio or video player?
			$tag = '';
			if ( // Audio (ideally mp3)
				'mp3' === $filetype['ext']
				|| 'wav' === $filetype['ext']
				|| ( 'mp4' === $filetype['ext'] && 'audio/mp4' === $filetype['type'] ) // can be audio or video (usually video)
				// ogg/audio and acc better off with MediaElementJS due to browser support
			) {
				$tag = 'audio';
			} elseif ( // Video (ideally mp4)
				'mp4' === $filetype['ext'] // video if wasn't audio above
				// ogg/video and webm better off with MediaElementJS due to browser support
			) {
				$tag = 'video';
			}

			// Build tag.
			if ( $tag ) {
				$embed_code = '<' . $tag . ' controls="" controlsList="nodownload" src="' . esc_url( $url ) . '"></' . $tag . '>';
			}

		}

		// If not using native player or could not detect an audio/video file type.
		if ( ! $embed_code ) {

			// Make Dropbox URL's work.
			if ( preg_match( '/dropbox/', $content ) ) {

				// URL.
				$url = $content;
				$url_no_qs = strtok( $url, '?' );

				// Replace ?dl=0 (or ?dl=1) with ?raw=1.
				$url = remove_query_arg( 'dl', $url );
				$url = add_query_arg( 'raw', '1', $url );

				// Audio or video shortcode?
				$format = '';
				$ext = pathinfo( $url_no_qs , PATHINFO_EXTENSION );

				if ( in_array( $ext, array( 'mp3', 'wav' ) ) ) { // Audio (ideally mp3)
					$format = 'audio';
				} else if ( in_array( $ext, array( 'mp4' ) ) ) { // Video (assuming mp4 is video, not audio)
					$format = 'video';
				}

				// Build shortcode since WP won't auto-convert URL.
				if ( $format ) {

					$shortcode = '[' . $format . ' src="' . $url . '"]';

					add_filter( 'wp_' . $format . '_extensions', 'ctfw_embed_allow_dropbox' ); // temporarily allow parameters like ?raw=1 in media URL
					$embed_code = do_shortcode( $shortcode );
					remove_filter( 'wp_' . $format . '_extensions', 'ctfw_embed_allow_dropbox' );

				}

			}

			// Process shortcode
			if ( ! $embed_code ) {
				$embed_code = $wp_embed->shortcode( array(), $content );
			}

		}

	}

	// HTML or shortcode embed may have been provided
	else {
		$embed_code = $content;
	}

	// Run shortcode
	// [video], [audio] or [embed] converted from URL or already existing in $content
	$embed_code = do_shortcode( $embed_code );

	// Return filtered
	return apply_filters( 'ctfw_embed_code', $embed_code, $content );

}

/**
 * Allow Dropbox URL's with ?raw=1 in shortcodes
 *
 * Used temporarily by ctfw_embed_code() for wp_audio_extensions and wp_video_extensions.
 *
 * This is a workaround until WordPress allows media URLs with query strings:
 * https://wordpress.stackexchange.com/questions/220572/how-can-i-get-the-video-shortcode-to-allow-query-string-parameters
 *
 * @param array $ext Array of extensions
 * @return array Modified extensions
 */
function ctfw_embed_allow_dropbox( $ext ) {

	// Allows any extension (ie. mp3?raw=1 for Dropbox)
    $ext[] = '';

    return $ext;

}

/**
 * Responsive embeds JavaScript
 */
function ctfw_responsive_embeds_enqueue_scripts() {

	// If theme supports this feature
	if ( current_theme_supports( 'ctfw-responsive-embeds' ) ) {

		// FitVids.js
		wp_enqueue_script( 'fitvids', get_theme_file_uri( CTFW_JS_DIR . '/jquery.fitvids.js' ), array( 'jquery' ), CTFW_THEME_VERSION ); // bust cache on theme update

		// Responsive embeds script
		wp_enqueue_script( 'ctfw-responsive-embeds', get_theme_file_uri( CTFW_JS_DIR . '/responsive-embeds.js' ), array( 'fitvids' ), CTFW_THEME_VERSION ); // bust cache on theme update
		wp_localize_script( 'ctfw-responsive-embeds', 'ctfw_responsive_embeds', array(
			'wp_responsive_embeds' => current_theme_supports( 'responsive-embeds' ),
		) );

	}

}

add_action( 'wp_enqueue_scripts', 'ctfw_responsive_embeds_enqueue_scripts' ); // front-end only (yes, wp_enqueue_scripts is correct for styles)

/**
 * Generic embeds
 *
 * This helps make embeds more generic by setting parameters to remove.
 * related videos, set neutral colors, reduce branding, etc.
 *
 * Enable with: add_theme_support( 'ctfw-generic-embeds' );
 *
 * @since 0.9
 * @param string $html Embed HTML code
 * @return string Modified embed HTML code
 */
function ctfw_generic_embeds( $html ) {

	// Does theme support this?
	if ( current_theme_supports( 'ctfw-generic-embeds' ) ) {

		// Get frame source URL
		// Separating i from frame avoids Theme Check false positive
		preg_match_all( '/<i' . 'frame[^>]+src=([\'"])(.+?)\1[^>]*>/i', $html, $matches );
		$url = ! empty( $matches[2][0] ) ? $matches[2][0] : '';

		// URL found
		if ( $url ) {

			$new_url = '';
			$source = '';
			$args = array();

			// YouTube
			if ( preg_match( '/youtube/i', $url ) ) {
				$source = 'youtube';
				$args = array(
					'wmode'				=> 'transparent',
					'rel'				=> '0', // don't show related videos at end
					'showinfo'			=> '0',
					'color'				=> 'white',
					'modestbranding'	=> '1'
				);
			}

			// Vimeo
			elseif ( preg_match( '/vimeo/i', $url ) ) {
				$source = 'vimeo';
				$args = array(
					'title'				=> '0',
					'byline'			=> '0',
					'portrait'			=> '0',
					'color'				=> 'ffffff'
				);
			}

			// Modify URL
			$args = apply_filters( 'ctfw_generic_embeds_add_args', $args, $source );
			$new_url = esc_url( add_query_arg( $args, $url ) );

			// Replace source with modified URL
			if ( $new_url != $url ) {
				$html = str_replace( $url, $new_url, $html );
			}

		}

	}

	return $html;

}

add_filter( 'embed_oembed_html', 'ctfw_generic_embeds' );

/**
 * HTML5 valid embeds
 *
 * This will correct YouTube embed code that is not HTML5 valid.
 * Other sources may be added later.
 *
 * Enable with add_theme_support( 'ctfw-valid-embeds' );
 *
 * @since 1.7
 * @param string $html Embed HTML code
 * @return string Modified embed HTML code
 */
function ctfw_valid_embeds( $html ) {

	// Does theme support this?
	if ( current_theme_supports( 'ctfw-valid-embeds' ) ) {

		// YouTube, Vimeo, etc.
	 	$html = str_replace( 'frameborder="0"', 'style="border: none;"', $html );

		// Vimeo
	 	$html = preg_replace( '( webkitallowfullscreen| mozallowfullscreen)', '$1', $html );

	}

	return $html;

}

add_filter( 'embed_oembed_html', 'ctfw_valid_embeds' );

/**
 * Clean media shortcode URLs
 *
 * Safari sometimes struggles with video shortcode's src URL when ?_=1 is appended.
 * Remove it with add_theme_support( 'ctfw-clean-media-shortcode-url' );
 *
 * This may also help with firewall cache issues.
 *
 * @since 2.6.4
 * @param string $output Audio or video shortcode HTML output.
 * @param array $atts Array of video shortcode attributes.
 * @param string $media Audio or video file.
 * @param int $post_id Post ID.
 * @param string $library Media library used for the video shortcode.
 * @return string Filtered video output.
 */
function ctfw_clean_media_shortcode_url( $output, $atts, $media, $post_id, $library ) {

	// Theme supports this.
	if ( current_theme_supports( 'ctfw-clean-media-shortcode-url' ) ) {

		// Remove ?_=1 from video URL.
		$output = str_replace( '?_=1', '', $output );

	}

	return $output;

}

add_action( 'wp_audio_shortcode', 'ctfw_clean_media_shortcode_url', 10, 5 );
add_action( 'wp_video_shortcode', 'ctfw_clean_media_shortcode_url', 10, 5 );

