/**
 * Admin Add/Edit Post
 */

jQuery( document ).ready( function( $ ) {

	/*******************************************
	 * FEATURED IMAGES
	 *******************************************/

	// Add size notes to Gutenberg's Featured Image box for custom post types.
	// This is handled in classic editor by filtering.
	setTimeout( function() {

		if ( $( '.gutenberg .editor-post-featured-image' ).length && ctfw_post.featured_image_note.length ) { // Gutenberg editor and have post type note.

				$( '.gutenberg .editor-post-featured-image' )
					.append( '<p class="description" id="risen-featured-image-note">' + ctfw_post.featured_image_note + '</p>' );
				$( '#risen-featured-image-note' )
					.hide()
					.fadeIn( 'fast' );
		}

	}, 100 ); // after Gutenberg.

} );
