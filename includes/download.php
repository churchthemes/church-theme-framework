<?php
/**
 * Download Functions
 */

// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS
// TO DO: TRIPLE-CHECK THE SECURITY ON THIS

/**
 * Force download of certain file types via /download-file/filename.type URL
 * Thanks for guidance from: http://wordpress.stackexchange.com/questions/3480/how-can-i-force-a-file-download-in-the-wordpress-backend
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
 * Note that file must be in uploads folder and extension must be defined in ctc_force_download()
 * /download-file/2012/05/uploaded-file.ext (relative to wp-content/uploads)
 */

if ( ! function_exists( 'ctc_force_download_url' ) ) {
	 
	function ctc_force_download_url( $url ) {

		$upload_dir = wp_upload_dir();

		$url = str_replace( $upload_dir['baseurl'], site_url( 'download-file' ), $url );		
	
		return $url;
	
	}

}
