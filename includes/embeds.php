<?php
/**
 * Embed Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      1.0
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/***********************************************
 * EMBEDS
 ***********************************************/

/**
 * Embed code based on audio/video URL or provided embed code
 *
 * If content is URL, use oEmbed to get embed code. If content is not URL, assume it is
 * embed code and run do_shortcode() in case of [video], [audio] or [embed]
 */

function ctfw_embed_code( $content ) {

	global $wp_embed;

	// Convert URL into media shortcode like [audio] or [video]
	if ( ctfw_is_url( $content ) ) {
		$embed_code = $wp_embed->shortcode( array(), $content );
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
 * Responsive embeds JavaScript
 */ 

add_action( 'wp_enqueue_scripts', 'ctfw_responsive_embeds_enqueue_scripts' ); // front-end only (yes, wp_enqueue_scripts is correct for styles)
 
function ctfw_responsive_embeds_enqueue_scripts() {

	// If theme supports this feature
	if ( current_theme_supports( 'ctfw-responsive-embeds' ) ) {

		// FitVids.js
		wp_enqueue_script( 'fitvids', ctfw_theme_url( CTFW_JS_DIR . '/jquery.fitvids.js' ), array( 'jquery' ), CTFW_THEME_VERSION ); // bust cache on theme update

		// Responsive embeds script
		wp_enqueue_script( 'ctfw-responsive-embeds', ctfw_theme_url( CTFW_JS_DIR . '/responsive-embeds.js' ), array( 'fitvids' ), CTFW_THEME_VERSION ); // bust cache on theme update

	}

}

/**
 * Generic embeds
 *
 * This helps make embeds more generic by setting parameters to remove
 * related videos, set neutral colors, reduce branding, etc.
 *
 * Enable with: add_theme_support( 'ctfw-generic-embeds' );
 */

add_filter( 'embed_oembed_html', 'ctfw_generic_embeds' );

function ctfw_generic_embeds( $html ) {

	// Does theme support this?
	if ( current_theme_supports( 'ctfw-generic-embeds' ) ) {

		// Get iframe source URL
		preg_match_all( '/<iframe[^>]+src=([\'"])(.+?)\1[^>]*>/i', $html, $matches );
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
			$new_url = add_query_arg( $args, $url );

			// Replace source with modified URL
			if ( $new_url != $url ) {
				$html = str_replace( $url, $new_url, $html );
			}

		}

	}

	return $html;

}
