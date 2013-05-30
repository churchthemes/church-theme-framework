<?php
/**
 * Menu Functions
 */

/**
 * Correct imported custom menu link base URLs
 *
 * This assumes the WordPress Importer plugin is used.
 * 
 * Sample import XML file may have Custom Link menu items with a dev or demo site's base URL.
 * This will replace all of those instances with the current site's base URL.
 *
 * Use add_theme_support( 'ctfw-correct-imported-menu-urls', 'http://wp.dev/site' );
 */
 
add_filter( 'import_end', 'ctfw_correct_imported_menu_urls' ); // correct custom menu URLs from sample XML content to use actual site's home URL
	
function ctfw_correct_imported_menu_urls() {

	// Theme supports this?
	if ( $support = get_theme_support( 'ctfw-correct-imported-menu-urls' ) ) {

		// Have URL to replace?
		if ( $sample_url = ! empty( $support[0] ) ? $support[0] : '' ) {

			// This site's home URL
			$home_url = home_url();

			// Remove trailing slashes for consistency
			$sample_url = untrailingslashit( $sample_url );
			$home_url = untrailingslashit( $home_url );

			// This site is not the same site sample content came from
			if ( $home_url != $sample_url ) {

				// Get menu links that have sample content's home URL
				$posts = get_posts( array(
					'post_type'	=> 'nav_menu_item',
					'numberposts' => -1,
					'meta_query' => array(
						array(
							'key'		=> '_menu_item_url',
							'value'		=> $sample_url,
							'compare'	=> 'LIKE'
						)
					)
				) );
				
				// Loop 'em to change
				foreach( $posts as $post ) {
				
					// Get URL
					$url = get_post_meta( $post->ID, '_menu_item_url', true );

					// Change it to this install's home URL
					$new_url = str_replace( $sample_url, $home_url, $url );
					update_post_meta( $post->ID, '_menu_item_url', esc_url_raw( $new_url ) );

					// Debug
					//echo "\n\n$url\n$new_url\n";
					//print_r( $post );

				}

			}

		}

	}
	
}