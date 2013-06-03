<?php
/**
 * Mime Type Functions
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*************************************************
 * MIME TYPES
 *************************************************/

/**
 * Friendly mime type name
 *
 * See wp_get_mime_types() for more matches to add.
 */

function ctfw_mime_type_name( $mime_type ) {

	// Default if no match
	$friendly_name = _x( 'File', 'mime type', 'church-theme-framework' );

	// Friendly mime type names
	$mime_type_names = array(
		'image'				=> 'Image',
		'audio'				=> 'Audio',
		'video'				=> 'Video',
		'application/pdf'	=> 'PDF',
	);
	$mime_type_names = apply_filters( 'ctfw_mime_type_names', $mime_type_names );

	// Check for match
	foreach ( $mime_type_names as $mime_type_match => $mime_type_name ) {

		// Match the first part and keep that name (e.g. image/jpeg matches image)
		if ( preg_match( '/^' . preg_quote( $mime_type_match, '/' ) . '/i', $mime_type ) ) {
			$friendly_name = $mime_type_name;
			break;
		}

	}

	return apply_filters( 'ctfw_mime_type_name', $friendly_name , $mime_type );

}