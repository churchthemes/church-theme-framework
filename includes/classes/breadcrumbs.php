<?php
/**
 * Breadcrumbs Class
 *
 * This will output a breadcrumb path for WordPress: echo new CTFW_Breadcrumbs( $options );
 *
 * @package    Church_Theme_Framework
 * @subpackage Classes
 * @copyright  Copyright (c) 2013 - 2014, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Breadcrumbs class
 *
 * @since 0.9
 */
class CTFW_Breadcrumbs {

	/**
	 * Display options
	 *
	 * @since 0.9
	 * @var array
	 */
	public $options;

	/**
	 * Constructor
	 *
	 * Set options passed in.
	 * 
	 * @since 0.9
	 * @access public
	 * @param array $options Options affecting display of breadcrumb path
	 */
	public function __construct( $options = array() ) {
		$this->set_options( $options );
	}

	/**
	 * Set options
	 * 
	 * @since 0.9
	 * @access public
	 */
	public function set_options( $options ) {

		$defaults = array(
			'separator'	=> _x( ' > ', 'breadcrumb separator', 'church-theme-framework' ),
			'classes'	=> '', // additional classes
		);

		$this->options = wp_parse_args( $options, $defaults );

	}

	/**
	 * Add breadcrumb to array (single)
	 * 
	 * @since 0.9
	 * @access public
	 * @param array &$breadcrumbs Breadcrumbs data to update
	 * @param array $add_breadcrumb Breadcrumb data to add
	 */
	public function add_breadcrumb( &$breadcrumbs, $add_breadcrumb = array() ) {

		if ( ! empty( $add_breadcrumb ) ) {
			$this->add_breadcrumbs_array( $breadcrumbs, array( $add_breadcrumb ) );
		}

	}

	/**
	 * Add breadcrumbs to array (multiple)
	 * 
	 * @since 0.9
	 * @param array &$breadcrumbs Breadcrumbs data to update
	 * @param array $add_breadcrumbs Multiple breadcrumbs to add
	 */
	public function add_breadcrumbs_array( &$breadcrumbs, $add_breadcrumbs = array() ) {

		if ( ! empty( $add_breadcrumbs ) ) {
			$breadcrumbs = array_merge( $add_breadcrumbs, $breadcrumbs );
		}

	}

	/**
	 * Get post/page breadcrumbs
	 * 
	 * @since 0.9
	 * @access public
	 * @global object Post object
	 * @param string $post_id ID of current post
	 * @param array $options Options, if any
	 * @return array Post breadcrumb ancestors
	 */
	public function post_breadcrumbs( $post_id, $options = array() ) {

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
			$title = ctfw_shorten( get_the_title(), $options['shorten'] );
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
						ctfw_shorten( get_the_title( $parent_post->ID ), $options['shorten'] ),
						get_permalink( $parent_post->ID )
					);
					
				}
				
				// Reverse parent post array and merge into main breadcrumbs
				$this->add_breadcrumbs_array( $post_breadcrumbs, array_reverse( $parent_post_breadcrumbs ) );
				
			}
		
		}
		
		return apply_filters( 'ctfw_post_breadcrumbs', $post_breadcrumbs, $post_id );

	}

	/**
	 * Get taxonomy term breadcrumbs
	 * 
	 * @since 0.9
	 * @access public
	 * @param mixed $term Taxonomy term as ID or object
	 * @param string $taxonomy Taxonomy slug to get breadcrumb ancestors for
	 * @return array Breadcrumb ancestors for taxonomy term
	 */
	public function taxonomy_term_breadcrumbs( $term, $taxonomy ) {

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
		
		$term_breadcrumbs = apply_filters( 'ctfw_taxonomy_term_breadcrumbs', $term_breadcrumbs, $term, $taxonomy );
		
		return $term_breadcrumbs;

	}

	/**
	 * Get date breadcrumbs
	 * 
	 * @since 0.9
	 * @access public
	 * @param string $base_url Provide a base URL for custom post type archives
	 * @return array Date breadcrumbs
	 */
	public function date_breadcrumbs( $base_url = false ) {

		$date_breadcrumbs = array();

		// Year
		$year = get_query_var( 'year' );
		if ( ! empty( $year ) ) {
		
			$dateformatstring = _x( 'Y', 'breadcrumb year format', 'church-theme-framework' );

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
			
				$dateformatstring = _x( 'F', 'breadcrumb month format', 'church-theme-framework' );
				
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
				
					$dateformatstring = _x( 'jS', 'breadcrumb day format', 'church-theme-framework' );
					
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

		return apply_filters( 'ctfw_date_breadcrumbs', $date_breadcrumbs, $base_url );

	}

	/**
	 * Build array
	 * 
	 * @since 0.9
	 * @access public
	 * @global object $post Post object
	 */
	public function build_array() {

		global $post;

		$breadcrumbs = array();

		// Not for 404 Not Found
		if ( ! is_404() ) {

			// Get post type data
			$post_type = get_post_type();
			$post_type_obj = get_post_type_object( $post_type );

			// Page Number
			$page_num = ctfw_page_num();
			if ( $page_num > 1 ) {
				$this->add_breadcrumb( $breadcrumbs, array(
					sprintf( _x( 'Page %s', 'breadcrumb', 'church-theme-framework' ), $page_num ),
					$_SERVER['REQUEST_URI']
				) );
			}

			// Not on front page
			if ( ! is_front_page() ) {

				// Search Results
				if ( is_search() ) {
					$this->add_breadcrumb( $breadcrumbs, array(
						_x( 'Search Results', 'breadcrumb', 'church-theme-framework' ),
						get_search_link()
					) );
				}

				// Posts/Pages & Archives
				else {

					// Get post type data
					$post_type = get_post_type();

					// No post type found
					// Get post type via content type (section)
					$post_types = ctfw_current_content_type_data( 'post_types' );
					if ( is_array( $post_types ) && 1 == count( $post_types ) && isset( $post_types[0] ) ) { // use section's post type if only one
						$post_type = $post_types[0];
					}

					// Get post type object
					$post_type_obj = get_post_type_object( $post_type );

					// Attachment
					if ( is_attachment() ) {

						/* translators: %s is mime type */
						$this->add_breadcrumb( $breadcrumbs, array(
							ctfw_mime_type_name( $post->post_mime_type ),
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
					_x( 'Home', 'breadcrumbs', 'church-theme-framework' ),
					home_url( '/' )
				) );
			}

		}

		return apply_filters( 'ctfw_breadcrumbs_array', $breadcrumbs );

	}

	/**
	 * Build string
	 * 
	 * @since 0.9
	 * @access public
	 */
	public function build_string() {

		$string = '';

		$breadcrumbs = $this->build_array();

		// Output breadcrumbs
		if ( ! empty( $breadcrumbs ) ) {

			// Additional classes?
			$classes = $this->options['classes'] ? ' ' . $this->options['classes'] : '';

			// Output
			$i = 0;
			$count = count( $breadcrumbs );
			$string .= '<div class="ctfw-breadcrumbs' . $classes . '">';
			foreach ( $breadcrumbs as $breadcrumb ) {
				
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

		return apply_filters( 'ctfw_breadcrumbs_string', $string );

	}

	/**
	 * Return string
	 * 
	 * @since 0.9
	 * @access public
	 * @return string Breadcrumbs HTML
	 */
	
	public function __toString() {
		return $this->build_string();
	}

}
