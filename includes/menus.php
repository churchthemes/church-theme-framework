<?php
/**
 * Menu Functions
 */

/**
 * Add a menu to a location if it is not already
 *
 * This is handy for running on theme activation.
 *
 * Set $use_first to true to use first menu already existing if cannot find a menu by name that already exists
 * Specify an array of menu slugs for the same result but excluding certain menus (for example, wouldn't want to use first menu item if it is a footer menu)
 */

if ( ! function_exists( 'ctc_set_menu' ) ) {

	function ctc_set_menu( $menu_name, $menu_slug, $location_slug, $use_first = false ) {
	
		// No menu added to the location yet
		$locations = get_nav_menu_locations();
		if ( empty( $locations[$location_slug] ) || is_wp_error( $locations[$location_slug] ) ) { // this instead of has_nav_menu() because that sometimes returned true because it doesn't consider error object

			// Get existing menus
			$menus = get_terms( 'nav_menu', array(
				'orderby' 		=> 'id', // oldest first
				'order'			=> 'ASC',
				'hide_empty'	=> false, // get menus without items
				'hierarchical'	=> false
			) );

			// Get menu if it exists
			if ( is_nav_menu( $menu_slug ) ) { // menu exists
				foreach ( $menus as $menu ) {
					if ( $menu_slug == $menu->slug ) {
						$menu_id = $menu->term_id;
						break;
					}
				}
			}
			
			// Otherwise, get first menu created (unless it is contained in $use_first array as exception)
			else if ( ! empty( $use_first ) && isset( $menus[0]->term_id ) ) {
			
				$use_first = is_array( $use_first ) ? $use_first : array(); // empty array if was set to true (no exceptions)
			
				if ( ! in_array( $menus[0]->slug, $use_first ) ) {
					$menu_id = $menus[0]->term_id;
				}

			}

			// If no menus exist, create Header Menu so we can add it to location
			if ( empty( $menu_id ) ) {
				$menu_id = wp_create_nav_menu( $menu_name );			
			}

			// Add menu to Header location
			if ( ! empty( $menu_id ) ) {
				$locations = (array) $locations;
				set_theme_mod( 'nav_menu_locations', array_merge( $locations, array(
					$location_slug => $menu_id,
				) ) );		
			}

		}
	
	}
	
}

/**
 * Correct "Custom" menu link URLs from sample XML content to use actual site's home URL
 * This fires after Importer plugin finishes - see hook in functions.php
 */
 
add_filter( 'import_end', 'ctc_import_correct_menu_urls' ); // correct custom menu URLs from sample XML content to use actual site's home URL
	
function ctc_import_correct_menu_urls() {

	$home_url = home_url(); // this install
	$dev_home_url = CTC_IMPORT_URL; // the "home" URL used in XML for Custom menu links

	// This WP install is not the dev install
	if ( $home_url != CTC_IMPORT_URL ) {

		// Get menu links that have dev home URL (from XML import)
		$posts = get_posts( array(
			'post_type'	=> 'nav_menu_item',
			'numberposts' => -1,
			'meta_query' => array(
				array(
					'key'		=> '_menu_item_url',
					'value'		=> CTC_IMPORT_URL,
					'compare'	=> 'LIKE'
				)
			)
		) );
		
		// Loop 'em to change
		foreach( $posts as $post ) {
		
			// Get URL
			$url = get_post_meta( $post->ID, '_menu_item_url', true );

			// Change it to this install's home URL
			$new_url = str_replace( CTC_IMPORT_URL, $home_url, $url );
			update_post_meta( $post->ID, '_menu_item_url', esc_url_raw( $new_url ) );

			// Debug
			//echo "\n\n$url\n$new_url\n";
			//print_r( $post );

		}

	}
	
}