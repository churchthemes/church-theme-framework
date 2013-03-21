<?php
/**
 * Archive Functions
 */

/**********************************
 * DATE ARCHIVES
 **********************************/

/**
 * Custom Post Type Date Archive Setup
 * 
 * At time of making, WordPress (3.5 and possibly later) does not support dated archives for custom post types as it does for standard posts.
 * This injects rules so that URL's like /cpt/2012/05 can be used with the custom post type archive template.
 * 
 * Note: resave permalinks if ever change this
 *
 * Thanks to Milan Petrovic for his guide: http://www.dev4press.com/2012/tutorials/wordpress/practical/url-rewriting-custom-post-types-date-archive/
 * (with an assist from Brian Krogsgard: http://krogsgard.com)
 */

function ctc_cpt_date_archive_setup( $post_types, $wp_rewrite ) {

	// Enable override by child theme
	$rules = apply_filters( 'ctc_cpt_date_archive_setup_rules', array(), $post_types, $wp_rewrite ); // empty if nothing passed in by filter

	// If rules not already provided via filter
	if ( empty( $rules ) ) {

		// Cast single post type as array
		$post_types = (array) $post_types;
		
		// Loop given post types to build rules
		foreach( $post_types as $post_type_slug ) {

			// Post type data
			$post_type = get_post_type_object( $post_type_slug );

			// Post type has archive enabled
			if ( isset( $post_type->has_archive ) && true === $post_type->has_archive ) {

				// Date archive rules
				$date_rules = array(
				
					// Year, Month, Day: /cpt-slug/2012/01/1
					array(
						'rule' => '([0-9]{4})/([0-9]{1,2})/([0-9]{1,2})', // URL pattern
						'vars' => array( 'year', 'monthnum', 'day' ) // corresponding query parameters
					),
					
					// Year, Month: /cpt-slug/2012/01
					array(
						'rule' => '([0-9]{4})/([0-9]{1,2})',
						'vars' => array( 'year', 'monthnum' )
					),
					
					// Day: /cpt-slug/2012
					array(
						'rule' => '([0-9]{4})',
						'vars' => array( 'year' )
					)
					
				);

				// Build rewrite rules and queries
				foreach ( $date_rules as $date_rule ) {
				
					// Base query
					$query = 'index.php?post_type=' . $post_type_slug;

					// Append date parameters and match index
					$i = 1;
					foreach ( $date_rule['vars'] as $var ) {
						$query .= '&' . $var . '=' . $wp_rewrite->preg_index( $i );
						$i++; // last in loop will serve as next index feed and pagination queries below
					}

					// Base rule
					$archive_slug = ! empty( $post_type->rewrite['slug'] ) ? $post_type->rewrite['slug'] : $post_type->name; // use rewrite slug if provided; otherwise post type slug
					$rule = $archive_slug . '/'. $date_rule['rule'];
					
					// Date URL
					$rules[$rule . '/?$' ] = $query;
					
					// Feed URLs
					$rules[$rule . '/feed/(feed|rdf|rss|rss2|atom)/?$' ] = $query . '&feed=' . $wp_rewrite->preg_index( $i );
					$rules[$rule . '/(feed|rdf|rss|rss2|atom)/?$' ] = $query . '&feed=' . $wp_rewrite->preg_index( $i );
					
					// Paginated URLs
					$rules[$rule . '/page/([0-9]{1,})/?$' ] = $query . '&paged=' . $wp_rewrite->preg_index( $i );
					
				}
				
			}
			
		}

	}

	// Apply the rules for given post types
	if ( ! empty( $rules ) ) {
		$wp_rewrite->rules = array_merge( $rules, $wp_rewrite->rules );
	}

}

/**
 * Get permalink for post type month archive
 *
 * Modified version of WordPress core get_month_link() with post type argument.
 */
 
function ctc_post_type_get_month_link( $year, $month, $post_type = false ) {

	global $wp_rewrite;

	if ( ! $year )
		$year = gmdate( 'Y', current_time( 'timestamp' ) );

	if ( ! $month )
		$month = gmdate( 'm', current_time( 'timestamp' ) );

	$monthlink = $wp_rewrite->get_month_permastruct();

	if ( !empty( $monthlink ) ) { // using pretty permalinks

		// Get rewrite slug for post type
		$slug = '';
		if ( ! empty( $post_type ) ) {
			$post_type_object = get_post_type_object( $post_type );
			if ( isset( $post_type_object->rewrite['slug'] ) ) {
				$slug = $post_type_object->rewrite['slug'];
			}
		}

		$monthlink = str_replace( '%year%', $year, $monthlink );
		$monthlink = str_replace( '%monthnum%', zeroise( intval( $month ), 2 ), $monthlink );

		return apply_filters( 'ctc_post_type_month_link', home_url( $slug . user_trailingslashit( $monthlink, 'month' ) ), $year, $month);

	} else { // default with query string

		$post_type_param = '';
		if ( 'post' != $post_type ) { // not necessary for default post type
			$post_type_param = '&post_type=' . $post_type;
		}

		return apply_filters( 'ctc_post_type_month_link', home_url( '?m=' . $year . zeroise( $month, 2 ) . $post_type_param ), $year, $month );

	}

}

/**********************************
 * REDIRECTION
 **********************************/

/**
 * Redirect a post type archive to page using specific template
 *
 * This is done only for non-date archive and avoids duplicate content.
 * The page template should output the same loop but with custom title, featured image, etc.
 *
 * Run this on template_redirect hook.
 */

function ctc_redirect_archive_to_page( $post_type, $page_template ) {

	// Check if is non-date archive for the post type
	if ( is_post_type_archive( $post_type ) && ! is_year() && ! is_month() && ! is_day() ) {

		// Check if a page is using sermons template
		if ( $page = ctc_get_page_by_template( $page_template ) ) {

			if ( ! empty( $page->ID ) ) {

				$page_url = get_permalink( $page->ID );

				wp_redirect( $page_url, 301 );
				exit;

			}

		}

	}

}

/**
 * Redirect post type archives to pages
 */

function ctc_redirect_archives_to_pages( $redirects ) {

	foreach( $redirects as $post_type => $page_template ) {
		ctc_redirect_archive_to_page( $post_type, $page_template );
	}

}