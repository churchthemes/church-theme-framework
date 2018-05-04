/**
 * Admin Add/Edit Post JavaScript
 */

jQuery( document ).ready( function( $ ) {

	/*******************************************
	 * GUTENBERG EDITOR
	 *******************************************/

	 // Gutenberg in use.
	 if ( $( '.gutenberg' ).length ) {

		// After Gutenberg inits.
		setTimeout( function() {

			// Add size notes to Gutenberg's Featured Image box for custom post types.
			// This is handled in classic editor by filtering.
			if ( $( '.gutenberg .editor-post-featured-image' ).length && ctfw_post.featured_image_note.length ) { // Gutenberg editor and have post type note.

					$( '.gutenberg .editor-post-featured-image' )
						.append( '<p class="description" id="ctfw-featured-image-note">' + ctfw_post.featured_image_note + '</p>' );
					$( '#ctfw-featured-image-note' )
						.hide()
						.fadeIn( 'fast' );
			}

		}, 100 );

	}

} );
