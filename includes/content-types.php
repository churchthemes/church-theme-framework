<?php
/**
 * Content Type Functions
 *
 * Content types help theme determine which area of the site a user is viewing (blog, sermons, etc.)
 * It is useful for showing relevant sidebars, header banners, etc.
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

/*********************************
 * CONTENT TYPES
 *********************************

/**
 * Content types
 *
 * Theme should filter ctfw_content_types to add page_templates since they are theme-specific.
 * The filter can also be used to add other content types.
 *
 * @since 0.9
 * @return array Default content types configuration
 */
function ctfw_content_types() {

	$content_types = array(

		'sermon' => array(
			'post_types'		=> array( 'ctc_sermon' ),
			'taxonomies'		=> array( 'ctc_sermon_topic', 'ctc_sermon_book', 'ctc_sermon_speaker', 'ctc_sermon_series', 'ctc_sermon_tag' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'event' => array(
			'post_types'		=> array( 'ctc_event' ),
			'taxonomies'		=> array( 'ctc_event_category' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'people' => array(
			'post_types'		=> array( 'ctc_person' ),
			'taxonomies'		=> array( 'ctc_person_group' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'location' => array(
			'post_types'		=> array( 'ctc_location' ),
			'taxonomies'		=> array( 'ctc_location' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'gallery' => array(
			'post_types'		=> array(),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'contact' => array(
			'post_types'		=> array(),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'blog' => array(
			'post_types'		=> array( 'post' ),
			'taxonomies'		=> array( 'category', 'tag' ),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array( 'is_author', 'is_archive', 'is_home' ), // is_home() is "Your latest posts" on homepage or "Posts page" when static front page used
		),

		'page' => array(
			'post_types'		=> array( 'page' ),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array(),
		),

		'search' => array(
			'post_types'		=> array(),
			'taxonomies'		=> array(),
			'page_templates'	=> array(), // should be populated via ctfw_content_types filter in theme
			'conditions'		=> array( 'is_search' ),
		),

	);

	// Allow filtering
	$content_types = apply_filters( 'ctfw_content_types', $content_types );

	// Sanitize types (particularly for filtered in data)
	$data_keys = array( 'post_types', 'taxonomies', 'page_templates', 'conditions' );
	foreach ( $content_types as $content_type => $content_type_data ) {
		foreach ( $data_keys as $data_key ) {
			$content_types[$content_type][$data_key] = isset( $content_type_data[$data_key] ) ? (array) $content_type_data[$data_key] : array(); // array if string, empty array if null
		}
	}

	// Return
	return $content_types;

}

/**
 * Detect type of content being shown
 *
 * Useful for showing content-specific elements (breadcrumbs, sidebars, header images).
 * The returned values should correspond to sidebar names in includes/sidebars.php.
 *
 * @since 0.9
 * @global object $post
 * @return string The current page's content type
 */
function ctfw_current_content_type() {

	global $post;

	$current_type = false;

	$content_types = ctfw_content_types();

	// Get content type based on post type, taxonomy or template
	foreach ( $content_types as $type => $type_data ) {

		// Check attachment parent post type
		if ( is_attachment() && ! empty( $post->post_parent ) && ! empty( $type_data['post_types'] ) && in_array( get_post_type( $post->post_parent ), $type_data['post_types'] ) ) {

			$current_type = $type;

			// If parent is a page, base its type on the template it uses
			if ( 'page' == $type && $parent_page_template_type = ctfw_content_type_by_page_template( get_post_meta( $post->post_parent, '_wp_page_template', true ) ) ) {
				$current_type = $parent_page_template_type;
			}

			break;

		}

		// Check post type
		if ( ! empty( $type_data['post_types'] ) && is_singular( $type_data['post_types'] ) || is_post_type_archive( $type_data['post_types'] ) ) {
			$current_type = $type;
			break;
		}

		// Check taxonomy
		foreach ( $type_data['taxonomies'] as $taxonomy ) {
			if ( is_tax( $taxonomy ) ) {
				$current_type = $type;
				break 2;
			}
		}

		// Check page template
		foreach ( $type_data['page_templates'] as $page_template ) {
			if ( is_page_template( CTFW_THEME_PAGE_TPL_DIR . '/' . basename( $page_template ) ) ) {
				$current_type = $type;
				break 2;
			}
		}

		// Check conditions
		foreach ( $type_data['conditions'] as $condition ) {
			if ( function_exists( $condition ) && call_user_func( $condition ) ) {
				$current_type = $type;
				break 2;
			}
		}

	}

	// Return filterable
	return apply_filters( 'ctfw_current_content_type', $current_type );

}

/**
 * Get content type based on page template
 *
 * @since 0.9
 * @param string $page_template Page template to get content type for
 * @return string Content type
 */
function ctfw_content_type_by_page_template( $page_template ) {

	$page_template_content_type = '';

	// Prepare page template
	$page_template = basename( $page_template ); // remove dir if has

	// Get types
	$content_types = ctfw_content_types();

	// Loop conent types
	foreach ( $content_types as $content_type => $content_type_data ) {

		// Check for page template
		if ( in_array( $page_template, $content_type_data['page_templates'] ) ) {
			$page_template_content_type = $content_type;
			break;
		}

	}

	// Return filtered
	return apply_filters( 'ctfw_content_type_by_page_template', $page_template_content_type, $page_template );

}

/**
 * Get primary page template based on content type
 *
 * @since 0.9.3
 * @param string $content_type Content type to get page template for
 * @return string Page template
 */
function ctfw_page_template_by_content_type( $content_type ) {

	$page_template = '';

	// Page templates
	$page_templates = ctfw_content_type_data( $content_type, 'page_templates' );

	// Get first page template
	if ( ! empty( $page_templates[0] ) ) {
		$page_template = $page_templates[0];
	}

	// Return filtered
	return apply_filters( 'ctfw_page_template_by_content_type', $page_template, $content_type );

}

/*********************************
 * DATA
 *********************************/

/**
 * Get data for a specific content type
 *
 * Specify a key, such as "page_templates"; otherwise, all data is retrieved.
 *
 * @since 0.9
 * @param string $content_type Content type to get data for
 * @param string $key Optionally specify a key to get data for
 * @return mixed All data (array) for content type or data for specific key
 */
function ctfw_content_type_data( $content_type, $key = false ) {

	$data = false;

	if ( ! empty( $content_type ) ) {

		$type_data = ctfw_content_types();

		if ( ! empty( $type_data[$content_type] ) ) {

			if ( ! empty( $key ) ) {
				if ( ! empty( $type_data[$content_type][$key] ) ) { // check for data
					$data = $type_data[$content_type][$key];
				}
			} else { // no key given, return all
				$data = $type_data[$content_type];
			}

		}

	}

	return apply_filters( 'ctfw_content_type_data', $data, $content_type, $key );

}

/**
 * Get data for current content type
 *
 * Specify a key, such as "page_templates"; otherwise, all data is retrieved.
 *
 * @since 0.9
 * @param string $key Optionally get data for specific key
 * @return mixed All data (array) for content type or data for specific key
 */
function ctfw_current_content_type_data( $key = false ) {

	// Get current content type
	$content_type = ctfw_current_content_type();

	// Get data
	$data = ctfw_content_type_data( $content_type, $key );

	// Return filterable
	return apply_filters( 'ctfw_current_content_type_data', $data, $key );

}


/**
 * Get archives for content type
 *
 * Specify content type to get taxonomy and month archives.
 * This can be useful for creating taxonomy page templates, navigation, etc.
 *
 * @since 1.7.1
 * @global object $wp_locale
 * @param string $content_type Content type to get archives for
 * @return array Archive data
 */
function ctfw_content_type_archives( $content_type ) {

	global $wp_locale;

	$archives = array();

	// Blog
	if ( 'blog' == $content_type ) {

		// Categories (alphabetical)
		$archives['category'] = get_terms(
			'category',
			array(
				'pad_counts'	=> true, // count children in parent since they do show in archive
			)
		);

		// Tag (biggest first)
		$archives['tag'] = get_terms(
			'post_tag',
			array(
				'orderby'		=> 'count',
				'order'			=> 'DESC',
				'pad_counts'	=> true, // count children in parent since they do show in archive
			)
		);

		// Months
		$archives['months'] = ctfw_get_month_archives( 'post' );

	}

	// Sermon
	if ( 'sermon' == $content_type ) {

		// Topics (alphabetical)
		$taxonomy = 'ctc_sermon_topic';
		if ( ctfw_ctc_taxonomy_supported( $taxonomy ) ) {
			$archives['topic'] = get_terms(
				$taxonomy,
				array(
					'pad_counts'	=> true, // count children in parent since they do show in archive
				)
			);
		}

		// Series (newest first)
		$taxonomy = 'ctc_sermon_series';
		if ( ctfw_ctc_taxonomy_supported( $taxonomy ) ) {
			$archives['series'] = get_terms(
				$taxonomy,
				array(
					'orderby'		=> 'id',
					'order'			=> 'DESC',
					'pad_counts'	=> true, // count children in parent since they do show in archive
				)
			);
		}

		// Book (in order of books in Bible)
		$taxonomy = 'ctc_sermon_book';
		if ( ctfw_ctc_taxonomy_supported( $taxonomy ) ) {

			$archives['book'] = get_terms(
				$taxonomy,
				array(
					'pad_counts'	=> true, // count children in parent since they do show in archive
				)
			);

			// Re-order according to books in Bible
			if ( $archives['book'] ) {

				$reordered_books = array();
				$unmatched_books = array();
				$bible_books = ctfw_bible_books();

				// Loop books in Bible
				foreach ( $bible_books['all'] as $bible_book_key => $bible_book ) {

					// Include this book if found in terms
					foreach ( $archives['book'] as $book_term ) {

						if ( trim( strtolower( $book_term->name ) ) == strtolower( $bible_book['name'] ) ) {

							// Add it
							$reordered_books[] = $book_term;

							// Stop looking
							break;

						}

					}

				}

				// Add those not found to end
				foreach ( $archives['book'] as $book_term ) {

					// Not added to new array?
					foreach ( $bible_books['all'] as $bible_book_key => $bible_book ) {

						$found = false;

						// Found it?
						if ( $bible_book['name'] == $book_term->name ) {
							$found = true;
							break;
						}

					}

					// Not found, append to end
					if ( ! $found ) {
						$reordered_books[] = $book_term;
					}

				}

				// Replace books with reordered array
				$archives['book'] = $reordered_books;

			}

		}

		// Speakers -- (alphabetical)
		$taxonomy = 'ctc_sermon_speaker';
		if ( ctfw_ctc_taxonomy_supported( $taxonomy ) ) {
			$archives['speaker'] = get_terms(
				$taxonomy,
				array(
					'pad_counts'	=> true, // count children in parent since they do show in archive
				)
			);
		}

		// Months
		$archives['months'] = ctfw_get_month_archives( 'ctc_sermon' );

	}

	// Event
	if ( 'event' == $content_type ) {

		// Category (alphabetical)
		$taxonomy = 'ctc_event_category';
		if ( ctfw_ctc_taxonomy_supported( $taxonomy ) ) {
			$archives['category'] = get_terms(
				$taxonomy,
				array(
					'pad_counts'	=> true, // count children in parent since they do show in archive
				)
			);
		}

		// Months
		/*

		Each theme decides how to handle archives, if any (such as on a monthly calendar via page template)
		Therefore, add_theme_support() must be used to enable this feature and pass on the proper URL format.

		Example:

		add_theme_support( 'ctfw-event-month-archives', array(
			'url_format'	=> get_permalink( ctfw_get_page_by_template( 'events-calendar.php' ) )
							   . '?month={year}-{month_padded}', // {year}, {month}, {month_padded}
			'month_limit'	=> 12, // default is to get up to 12 months future
		) );

		*/
		$support = get_theme_support( 'ctfw-event-month-archives' );
		if ( ! empty( $support['0'] ) ) {

			// Default args
			$args = wp_parse_args( $support['0'], array(
				'url_format'	=> '', // required via add_theme_support()
				'month_limit'	=> 12, // default is to show up to 12 months future
			) );

			// Make args usable as variables
			extract( $args );

			// Is URL format valid?
			// It's possible that the proper page doesn't exist yet, so stop here
			if ( ctfw_is_url( $url_format ) ) {

				// Date info
				$year_month = date_i18n( 'Y-m' ); // start with current
				$DateTime = new DateTime( $year_month );

				// Loop next X months
				$months_looped = 0;
				while ( $months_looped < $month_limit ) {

					// Get number of event occurences in month
					$count = ctfw_month_events_count( $year_month );

					// Add month to archives array if has events
					if ( $count ) {

						// Date
						$month_ts = strtotime( $year_month );
						$month = date( 'n', $month_ts ); // e.g. 1
						$month_padded = date( 'm', $month_ts );// e.g. 01
						$year = date( 'Y', $month_ts ); // e.g.  2015

						// Name
						// 'name' that is automatically localized (key matches taxonomy term object)
						/* translators: 1: month name, 2: 4-digit year */
			            $name = sprintf( _x('%1$s %2$d', 'month archive', 'church-theme-framework' ), $wp_locale->get_month( $month ), $year );

						// URL
						$url = $url_format;
						$url = str_replace( '{year}', $year, $url );
						$url = str_replace( '{month}', $month, $url );
						$url = str_replace( '{month_padded}', $month_padded, $url );

						// Add data
						// Use same format as ctfw_get_month_archives()
						$archives['months'][$months_looped] = new stdClass();
				        $archives['months'][$months_looped]->year = $year;
				        $archives['months'][$months_looped]->month = $month;
				        $archives['months'][$months_looped]->count = $count;
				        $archives['months'][$months_looped]->post = $count;
				        $archives['months'][$months_looped]->name = $name;
				        $archives['months'][$months_looped]->url = $url;

					}

					// Next month
					$DateTime->modify( '+1 month' );
					$year_month = $DateTime->format( 'Y-m' ); // PHP 5.2 cannot chain methods
					$months_looped++;

				}

			}

		}

	}

	// Add archive URL to terms
	foreach ( $archives as $archive_key => $archive_terms ) {

		// It's a taxonomy archive if not a date archive
		if ( $archive_key == 'months' ) {
			continue;
		}

		// Loop terms
		foreach ( $archive_terms as $archive_term_key => $archive_term ) {
			$archives[$archive_key][$archive_term_key]->url = get_term_link( $archive_term );
		}

	}

	// Make filterable
	$archives = apply_filters( 'ctfw_content_type_archives', $archives, $content_type );
	$archives = apply_filters( 'ctfw_content_type_archives-' . $content_type, $archives );

	return $archives;

}
