<?php
/**
 * File Functions
 */

/*************************************************
 * FILE URLs
 *************************************************/

/**
 * Retrieve the url of a file in the theme. 
 * 
 * Searches in the stylesheet directory before the template directory so themes 
 * which inherit from a parent theme can just override one file.
 * 
 * This is from here and will likely be part of WordPress core. At that time, move this to deprecated.php.
 * http://core.trac.wordpress.org/attachment/ticket/18302/18302.12.diff
 * http://core.trac.wordpress.org/ticket/18302
 * 
 * @param string $file File to search for in the stylesheet directory. 
 * @return string The URL of the file. 
 */

function ctc_theme_url( $file = '' ) {

	$file = ltrim( $file, '/' ); 
 
	if ( empty( $file ) ) { 
		$url = get_stylesheet_directory_uri(); 
	} elseif( is_child_theme() && file_exists( get_stylesheet_directory() . "/$file" ) ) { 
		$url = get_stylesheet_directory_uri() . "/$file"; 
	} else { 
		$url = get_template_directory_uri() . "/$file"; 
	}
	
	return apply_filters( 'ctc_theme_url', $url, $file ); 
	
}

/**
 * Convert rewritten file upload URL to real URL
 *
 * When multisite used, uploaded files can be served from /files/* using mod_rewrite.
 * This converts a URL like that to the actual file location at /wp-content/blogs.dir/files/*.
 *
 * Someimes a rewritten URL is not sufficient, such as with MediaElement.js in Safari (progress bar does not render).
 */

function ctc_real_file_url( $url ) {

	$real_url = $url;

	if ( is_multisite() ) {

		$upload_dir = wp_upload_dir();

		$basedir_relative = str_replace( WP_CONTENT_DIR, '', $upload_dir['basedir'] );

		$baseurl_real = WP_CONTENT_URL . $basedir_relative;

		$real_url = str_replace( $upload_dir['baseurl'], $baseurl_real, $url );

	}

	return apply_filters( 'ctc_real_file_url', $real_url, $url );

}

/*************************************************
 * DOWNLOADS
 *************************************************/

// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS

/**
 * Force download of certain file types via /download-file/filename.type URL
 * This information was useful: http://wordpress.stackexchange.com/questions/3480/how-can-i-force-a-file-download-in-the-wordpress-backend
 */

add_action( 'template_redirect', 'ctc_force_download' );
	
function ctc_force_download() {

	// valid extensions for download - security check
	// these are commonly downloaded file types
	$file_extensions = array(
		'mp3'	=> 'audio/mpeg',	// extension, content-type
		'pdf'	=> 'application/pdf'
	);
	
	// allow extensions to be filtered
	$file_extensions = apply_filters( 'ctc_download_file_extensions', $file_extensions ); // allow filtering
	
	// prepare file extensions for regex
	$file_extensions_regex = array();
	foreach( $file_extensions as $extension => $content_type ) {
		$file_extensions_regex[] = $extension;
	}
	$file_extensions_regex = implode( '|', $file_extensions_regex );
	
	// check if this URL is a request for file download of valid type
	$base_path = '/download-file/';
	$regex = '/^.*' . preg_quote( $base_path, '/' ) . '(.+\.(' . $file_extensions_regex . '))$/i'; // .* at beginning allows for WP not being a domain level install
	if ( preg_match( $regex, $_SERVER['REQUEST_URI'], $matches ) && ! empty( $matches[1] ) ) {

		// relative file path
		$relative_file_path = $matches[1];
		list( $relative_file_path ) = explode( '?', $relative_file_path ); // chop query string off, although it should not be able to get through
		
		// file extension
		$file_extension = strtolower( $matches[2] );
		
		// check for directory traversal attack
		if ( ! validate_file( $relative_file_path ) ) { // false means it passed validation
			
			// path to file in uploads folder (only those can be downloaded)
			$upload_dir = wp_upload_dir();
			$upload_file_path = $upload_dir['basedir'] . '/' . $relative_file_path;
			
			// file exists?
			if ( file_exists( $upload_file_path ) ) {
				
				// force download
				$content_type = $file_extensions[$file_extension];
				if ( ! empty( $content_type ) ) {

					// headers
					$filename = basename( $upload_file_path );
					$filesize = filesize( $upload_file_path );
					header( 'Content-Type: application/octet-stream', true, 200 ); // replace WordPress 404 Not Found with 200 Okay
					header( 'Content-Disposition: attachment; filename=' . $filename );
					header( 'Expires: 0' );
					header( 'Cache-Control: must-revalidate' );
					header( 'Pragma: public' );
					header( 'Content-Length: ' . $filesize );
					
					// clear buffering just in case
					@ob_end_clean();
					flush();
					
					// output file contents
					@readfile( $upload_file_path ); // @ to prevent printing any error messages
					
					// we're done, stop further execution
					exit;
					
				}
				
			}
			
		}
		
	}
		
	// failure of any type results in 404 file not found

}

/**
 * Convert regular URL to URL that forces download
 * 
 * Note that file must be in uploads folder and extension must be defined in ctc_force_download()
 * /download-file/2012/05/uploaded-file.ext (relative to wp-content/uploads)
 */
	 
function ctc_force_download_url( $url ) {

	$upload_dir = wp_upload_dir();

	$force_url = str_replace( $upload_dir['baseurl'], site_url( 'download-file' ), $url );		

	return apply_filters( 'ctc_force_download_url', $force_url, $url );

}

/*************************************************
 * MIME TYPES
 *************************************************/

/**
 * Friendly mime type name
 *
 * See wp_get_mime_types() for more matches to add.
 */

function ctc_mime_type_name( $mime_type ) {

	// Default if no match
	$friendly_name = _x( 'File', 'mime type', 'church-theme' );

	// Friendly mime type names
	$mime_type_names = array(
		'image'				=> 'Image',
		'audio'				=> 'Audio',
		'video'				=> 'Video',
		'application/pdf'	=> 'PDF',
	);
	$mime_type_names = apply_filters( 'ctc_mime_type_names', $mime_type_names );

	// Check for match
	foreach ( $mime_type_names as $mime_type_match => $mime_type_name ) {

		// Match the first part and keep that name (e.g. image/jpeg matches image)
		if ( preg_match( '/^' . preg_quote( $mime_type_match, '/' ) . '/i', $mime_type ) ) {
			$friendly_name = $mime_type_name;
			break;
		}

	}

	return apply_filters( 'ctc_mime_type_name', $friendly_name , $mime_type );

}