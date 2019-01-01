/**
 * Admin Add/Edit Post JavaScript
 */

jQuery( document ).ready( function( $ ) {

	/*******************************************
	 * GUTENBERG EDITOR
	 *******************************************/

	 // Gutenberg in use.
	 if ( $( '.block-editor-page' ).length && ctfw_post.featured_image_note.length ) {

		// After Featured Image section opened.
		var interval = setInterval( function() {

			// Add size notes to Gutenberg's Featured Image box for custom post types.
			// This is handled in classic editor by filtering.
			if ( $( '.block-editor-page .editor-post-featured-image' ).length ) {

				$( '.block-editor-page .editor-post-featured-image' )
					.append( '<p class="description" id="ctfw-featured-image-note">' + ctfw_post.featured_image_note + '</p>' );
				$( '#ctfw-featured-image-note' )
					.hide()
					.fadeIn( 'fast' );

	        	clearInterval( interval );

			}

		}, 1000 );

	}

} );
