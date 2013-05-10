<?php
/**
 * Breadcrumbs Class
 *
 * This will output a breadcrumb path for WordPress:
 *
 * echo new CTC_Breadcrumbs( $options );
 */

class CTC_Breadcrumbs {

	function __construct( $options = array() ) {

		$this->set_options( $options );

	}

	/**
	 * Set options
	 */

	function set_options( $options ) {

		$defaults = array(
			'separator'	=> _x( ' > ', 'breadcrumb separator', 'church-theme' ),
		);

		$this->options = wp_parse_args( $options, $defaults );

	}

	/**
	 * Add breadcrumb to array (single)
	 */

	function add_breadcrumb( &$breadcrumbs, $add_breadcrumb = array() ) {

		if ( ! empty( $add_breadcrumb ) ) {
			$this->add_breadcrumbs_array( $breadcrumbs, array( $add_breadcrumb ) );
		}

	}

	/**
	 * Add breadcrumbs to array (multiple)
	 */

	function add_breadcrumbs_array( &$breadcrumbs, $add_breadcrumbs = array() ) {

		if ( ! empty( $add_breadcrumbs ) ) {
			$breadcrumbs = array_merge( $add_breadcrumbs, $breadcrumbs );
		}

	}

	/**
	 * Get Post/Page Breadcrumbs
	 */

	function post_breadcrumbs( $post_id, $options = array() ) {

		global $post;

		// Default options
		$defaults = array(
			'shorten'			=> 30,		// shorten post titles
			'show_parents'		=> true,	// show parents posts
		);
		$options = wp_parse_args( $options, $defaults );

		// Start breadcrumbs
		$post_breadcrumbs = array();

		// Get post
		if ( $post = get_post( $post_id ) ) {

			// Current post
			$title = ctc_shorten( get_the_title(), $options['shorten'] );
			if ( empty( $title ) ) { // no title? use post type or post format name?
				if ( $post_format = get_post_format() ) { // show post format if have it
					$title = get_post_format_string( $post_format );
				} elseif ( $post_type_obj = get_post_type_object( get_post_type() ) ) { // otherwise show post type name
					$title = $post_type_obj->labels->singular_name;
				}
			}

				// Add breadcrumb
				$this->add_breadcrumb( $post_breadcrumbs, array(
					$title,
					get_permalink()
				) );

			// Parent posts?
			if ( $options['show_parents'] && ! empty( $post->post_parent ) ) {

				$parent_post_breadcrumbs = array();
				
				// Traverse through parent posts
				$parent_post_id = $post->post_parent;
				while ( $parent_post_id ) { // keep moving down levels until there are no more parent posts

					$parent_post = get_post( $parent_post_id );
					$parent_post_id  = $parent_post->post_parent; // if this parent has a parent, while loop will continue
					
					$parent_post_breadcrumbs[] = array(
						ctc_shorten( get_the_title( $parent_post->ID ), $options['shorten'] ),
						get_permalink( $parent_post->ID )
					);
					
				}
				
				// Reverse parent post array and merge into main breadcrumbs
				$this->add_breadcrumbs_array( $post_breadcrumbs, array_reverse( $parent_post_breadcrumbs ) );
				
			}
		
		}
		
		return apply_filters( 'ctc_post_breadcrumbs', $post_breadcrumbs, $post_id );

	}

	/**
	 * Get Taxonomy Term Breadcrumbs
	 *
	 * $term can be object or ID
	 */

	function taxonomy_term_breadcrumbs( $term, $taxonomy ) {

		$term_breadcrumbs = array();
			
		if ( ! empty( $term ) ) {
		
			$term_obj = get_term( $term, $taxonomy ); // in case $term is ID, not already object

			// Current term
			$this->add_breadcrumb( $term_breadcrumbs, array(
				$term_obj->name,
				get_term_link( $term_obj, $taxonomy )
			) );

			// Parent terms?
			if ( ! empty( $term_obj->parent ) ) {

				$parent_term_breadcrumbs = array();
				
				// Traverse through parent terms
				$parent_term_id = $term_obj->parent;
				while ( $parent_term_id ) { // keep moving down levels until there are no more parent terms

					$parent_term = get_term( $parent_term_id, $taxonomy );
					$parent_term_id  = $parent_term->parent; // if this parent has a parent, while loop will continue

					$parent_term_breadcrumbs[] = array(
						$parent_term->name,
						get_term_link( $parent_term, $taxonomy )
					);		

				}
				
				// Reverse parent term array and marge into main breadcrumbs
				$this->add_breadcrumbs_array( $term_breadcrumbs, $parent_term_breadcrumbs );
				
			}
		
		}
		
		$term_breadcrumbs = apply_filters( 'ctc_taxonomy_term_breadcrumbs', $term_breadcrumbs, $term, $taxonomy );
		
		return $term_breadcrumbs;

	}

	/**
	 * Get Date Breadcrumbs
	 */

	function date_breadcrumbs( $base_url = false ) {

		$date_breadcrumbs = array();

		// Year
		$year = get_query_var( 'year' );
		if ( ! empty( $year ) ) {
		
			$dateformatstring = _x( 'Y', 'breadcrumb year format', 'church-theme' );

			if ( ! empty( $base_url ) ) { // if base URL given, use it (such as custom post type date archive)
				$date_url = trailingslashit( $base_url ) . trailingslashit( $year );
			} else {
				$date_url = get_year_link( $year );
			}
			
			$this->add_breadcrumb( $date_breadcrumbs, array(
				date_i18n( $dateformatstring, mktime( 0, 0, 0, 1, 1, $year ) ),
				$date_url
			) );
		
			// Month
			$month = get_query_var( 'monthnum' );
			if ( ! empty( $month ) ) {
			
				$dateformatstring = _x( 'F', 'breadcrumb month format', 'church-theme' );
				
				if ( ! empty( $base_url ) ) { // if base URL given, use it (such as custom post type date archive)
					$date_url .= trailingslashit( $month );
				} else {
					$date_url = get_month_link( $year, $month );
				}
				
				$this->add_breadcrumb( $date_breadcrumbs, array(
					date_i18n( $dateformatstring, mktime( 0, 0, 0, $month, 1, $year ) ),
					$date_url
				) );

				// Day
				$day = get_query_var( 'day' );
				if ( ! empty( $day ) ) {
				
					$dateformatstring = _x( 'jS', 'breadcrumb day format', 'church-theme' );
					
					if ( ! empty( $base_url ) ) { // if base URL given, use it (such as custom post type date archive)
						$date_url .= trailingslashit( $day );
					} else {
						$date_url = get_day_link( $year, $month, $day );
					}
					
					$this->add_breadcrumb( $date_breadcrumbs, array(
						date_i18n( $dateformatstring, mktime( 0, 0, 0, $month, $day, $year ) ),
						$date_url
					) );

				}
				
			}					
			
		}
		
		// Reverse order
		$date_breadcrumbs = array_reverse( $date_breadcrumbs );

		return apply_filters( 'ctc_date_breadcrumbs', $date_breadcrumbs, $base_url );

	}

	/**
	 * Build array
	 */
	
	function build_array() {

		global $post;

		$breadcrumbs = array();

		// Not for 404 Not Found
		if ( ! is_404() ) {

			// Get post type data
			$post_type = get_post_type();
			$post_type_obj = get_post_type_object( $post_type );

			// Page Number
			$page_num = ctc_page_num();
			if ( $page_num > 1 ) {
				$this->add_breadcrumb( $breadcrumbs, array(
					sprintf( _x( 'Page %s', 'breadcrumb', 'church-theme' ), $page_num ),
					$_SERVER['REQUEST_URI']
				) );
			}

			// Not on front page
			if ( ! is_front_page() ) {

				// Search Results
				if ( is_search() ) {
					$this->add_breadcrumb( $breadcrumbs, array(
						_x( 'Search Results', 'breadcrumb', 'church-theme' ),
						get_search_link()
					) );
				}

				// Posts/Pages & Archives
				else {

					// Get post type data
					$post_type = get_post_type();
					$post_type_obj = get_post_type_object( $post_type );

					// Attachment
					if ( is_attachment() ) {

						/* translators: %s is mime type */
						$this->add_breadcrumb( $breadcrumbs, array(
							ctc_mime_type_name( $post->post_mime_type ),
							get_permalink()
						) );

						// Make parent(s) show, if any
						if ( ! empty( $post->post_parent ) ) {
							$parent_post_id = $post->post_parent;
						}

					}

					// Post or Page
					if ( ( is_singular() && ! is_attachment() ) || ! empty( $parent_post_id ) ) { // $parent_post_id can be attachment parent

						// Get post/page ID
						// Parent post (from attachment?) or current post
						$post_id = $post->ID;
						if ( ! empty( $parent_post_id ) ) {
							$post_id = $parent_post_id;
							$post_type = get_post_type( $post_id );
							$post_type_obj = get_post_type_object( $post_type );
						}

						// Show taxonomy before if has one
						// Use primary taxonomy's first term
						$taxonomies = get_object_taxonomies( $post_type );
						$taxonomy = isset( $taxonomies[0] ) ? $taxonomies[0] : false;
						if ( $taxonomy ) {
							$taxonomy_terms = get_the_terms( $post_id, $taxonomy );
							$taxonomy_term = is_array( $taxonomy_terms ) ? current( $taxonomy_terms ) : $taxonomy_terms; // use first term in list
						}

						// Post breadcrumb options
						$options = array();

							// Show parents only if not showing taxonomy
							if ( ! empty( $taxonomy ) ) {
								$options['show_parents'] = false; // default true
							}

						// Show post and parent(s)
						$this->add_breadcrumbs_array( $breadcrumbs, $this->post_breadcrumbs( $post_id, $options ) );

					}
					
					// Archives
					if ( is_archive() || ! empty( $taxonomy_term ) ) {

						// Blog Category
						if ( is_category() ) {
							$this->add_breadcrumbs_array( $breadcrumbs, $this->taxonomy_term_breadcrumbs( get_query_var( 'cat' ), 'category' ) );
						}

						// Blog Tag
						if ( is_tag() ) {
							$this->add_breadcrumb( $breadcrumbs, array(
								get_query_var( 'tag' ),
								get_tag_link( get_query_var( 'tag_id' ) )
							) );
						}

						// Custom Taxonomy and Parents
						if ( is_tax() || ! empty( $taxonomy_term ) ) { //  $taxonomy_term can come from post

							// Taxonomy passed in from post or are we on actual taxonomy?
							if ( is_tax() ) { // actual taxonomy
								$taxonomy = get_query_var( 'taxonomy' );
								$taxonomy_term = get_term_by( 'slug', get_query_var( 'term' ), get_query_var( 'taxonomy' ) );
							}

							// Add taxonomies
							$this->add_breadcrumbs_array( $breadcrumbs, $this->taxonomy_term_breadcrumbs( $taxonomy_term, $taxonomy ) );

						}

						// Date Archive
						elseif ( is_year() || is_month() || is_day() ) {
							
							// Append date breadcrumbs
							$base_url = get_post_type_archive_link( $post_type );
							$this->add_breadcrumbs_array( $breadcrumbs, $this->date_breadcrumbs( $base_url ) );

						}

						// Author Archive
						elseif ( is_author() ) {
							$this->add_breadcrumb( $breadcrumbs, array(
								get_the_author(),
								get_author_posts_url( get_query_var( 'author' ) )
							) );
						}

					}

					// Post Type Archive
					// Single posts, taxonomies, etc.
					if ( $post_type_obj && ! is_page() ) { // not for pages

						// If static front page used and "Posts page" set, use that
						if ( 'post' == $post_type && $posts_page_id = get_option( 'page_for_posts' ) ) {
							$posts_page = get_page( $posts_page_id );
							$archive_name = $posts_page->post_title;
							$archive_url = get_permalink( $posts_page_id );
						}

						// Otherwise use current post type
						else {
							$archive_name = $post_type_obj->labels->name;
							$archive_url = get_post_type_archive_link( $post_type );
						}

						// Show only if have URL
						// When static front used and no "Posts page" set, there is no archive URL
						if ( $archive_url ) {
							$this->add_breadcrumb( $breadcrumbs, array(
								$archive_name,
								$archive_url
							) );
						}

					}
					
				}

			}

			// Add "Home" to front if have other breadcrumb(s)
			if ( ! empty( $breadcrumbs ) ) {
				$this->add_breadcrumb( $breadcrumbs, array(
					_x( 'Home', 'breadcrumbs', 'church-theme' ),
					home_url( '/' )
				) );
			}

		}

		return apply_filters( 'ctc_breadcrumbs_array', $breadcrumbs );

	}

	/**
	 * Build string
	 */

	function build_string() {

		$string = '';

		$breadcrumbs = $this->build_array();

		// Output breadcrumbs
		if ( ! empty( $breadcrumbs ) ) {

			// Output
			$i = 0;
			$count = count( $breadcrumbs );
			$string .= '<div class="ctc-breadcrumbs">';
			foreach( $breadcrumbs as $breadcrumb ) {
				
				$i++;
				
				$breadcrumb = (array) $breadcrumb;
				
				if ( ! empty( $breadcrumb[0] ) ) {

					// Separator
					if ( $i > 1 ) {
						$string .= $this->options['separator'];
					}

					// If no link given (just in case)
					if ( empty( $breadcrumb[1] ) ) { // add  || $i == $count if don't wany any last item linked, but it's more helpful and reable with it linked
						$string .= '<span>' . esc_html( $breadcrumb[0] ) . '</span>';
					}
					
					// Linked
					else {
						$string .= '<a href="' . esc_url( $breadcrumb[1] ) . '">' . esc_html( $breadcrumb[0] ) . '</a>';
					}

				}
				
			}
			$string .= '</div>';

		}

		// Restore original $post data for proper code execution after breadcrumbs
		wp_reset_postdata();

		return apply_filters( 'ctc_breadcrumbs_string', $string );

	}

	/**
	 * Return string
	 */
	
	function __toString() {

		return $this->build_string();

	}

}
