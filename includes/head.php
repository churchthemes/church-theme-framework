<?php
/**
 * <head> Functions
 *
 * Functions that add to <head>. Also see frontend-css.php for adding styles.
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

/*******************************************
 * <title> Tag
 *******************************************/

/**
 * Add event calendar month and category to <title> tag
 *
 * This compliments any existing title whether default or a plugin like Yoast SEO.
 * It runs at priority 99 which should be after other manipulations of <title>.
 * This is useful because SEO plugins will not know how to deal with calendar template queries.
 *
 * Use add_theme_support( 'ctfw-event-calendar-head-title' ) to enable this.
 * Argument can be passed for page_template if different than events-calendar.php.
 *
 * This expects query parameters month and category (?month=2015-02-17&category=slug)
 *
 * @since 1.5
 * @param string $title Page title determined by WordPress core
 * @param string $sep Optional, default is '&raquo;'. How to separate the various items within the page title.
 * @param string $seplocation Optional. Direction to display title, 'right'.
 * @return string Title with month and category inserted or appended
 */
// This method compatible with SEO plugins like Yoast SEO by Yoast
// It appends month and category to end of existing <title>
// If site name is found at end of title, it is appended before that
function ctfw_event_calendar_head_title( $title, $sep, $seplocation ) {

	global $post;

	$new_title = $title;

	// Theme supports this?
	$support = get_theme_support( 'ctfw-event-calendar-head-title' );
	if ( ! empty( $support ) ) {

		// Arguments from theme support or default
		$args = isset( $support[0] ) ? $support[0] : array();
		$args = wp_parse_args( $args, array(
			'page_template'	=> 'events-calendar.php',
		) );

		// Only on event calendar page template
		if ( is_page_template( CTFW_THEME_PAGE_TPL_DIR . '/' . $args['page_template'] ) ) {

			$parts = array();

			// Get month
			if ( ! empty( $_GET['month'] ) ) {

				/* translators: this is the PHP date format used for <title> on event calendar months */
				$parts[] = date_i18n( _x( 'F Y', 'event calendar', 'church-theme-framework' ), strtotime( $_GET['month'] ) );

			}

			// Get category
			if ( ! empty( $_GET['category'] ) ) {

				// Get term from slug
				$category = get_term_by( 'slug', $_GET['category'], 'ctc_event_category' );

				// Get name
				if ( ! empty( $category->name ) ) {
					$parts[] = wptexturize( $category->name );
				}

			}

			// Combine parts
			if ( $parts ) {

				// Is Yoast SEO plugin active?
				$wpseo_separator = '';
				if ( function_exists( 'wpseo_replace_vars' ) ) {

					$wpseo_separator = trim( wpseo_replace_vars( '%%sep%%', array() ) );

					if ( ! empty( $wpseo_separator ) ) {
						$wpseo_separator = ' ' . $wpseo_separator . ' ';
					}

				}

				// Get separator
				if ( $wpseo_separator ) { // use Yoast SEO plugin's separator if available
					$separator = $wpseo_separator;
				} else { // otherwise fall back to ' | ' WordPress default
					/* translators: separator for <title> content */
					$separator = _x( ' | ', 'head title', 'church-theme-framework' );
				}

				// Combine month and category into suffix
				$month_category = $separator . implode( $separator, $parts ); // separator at left
				$month_category_right = implode( $separator, $parts ) . $separator; // separator at right

				// Post title was found in <title>
				// Insert month and category after that (e.g. Monthly Calendar - Month - Category - Site Title)
				$post_title = isset( $post->post_title ) ? wptexturize( $post->post_title ) : '';
				$post_title_esc = preg_quote( $post_title );
				if ( ! empty( $post_title ) && preg_match( '/' . $post_title_esc . preg_quote( $separator ) . '/', $title ) ) {
					$new_title = preg_replace( '/(' . $post_title_esc . ')/', '$1' . $month_category, $title );
				}

				// Post title was not found in <title>
				// It's possible user has used an SEO plugin to write something different
				else {

					$site_title = get_bloginfo( 'name', 'display' ); // wptexturize applied
					$site_title_esc = preg_quote( $site_title );

					if ( ! empty( $site_title ) ) {

						// Is site title found at end of title?
						// Insert month and category before that (e.g. Custom Title - Month - Category - Site Name)
						if ( preg_match( '/' . $site_title_esc . '$/', $title ) ) {
							$new_title = preg_replace( '/(' . $site_title_esc . ')$/', $month_category_right . '$1', $title );
						}

						// No site title found; SEO plugin may not use it
						// Simply append month and category to end
						else {
							$new_title .= $month_category;
						}

					}

				}

			}

		}

	}

	return $new_title;

}

add_filter( 'wp_title', 'ctfw_event_calendar_head_title', 99, 3 );
