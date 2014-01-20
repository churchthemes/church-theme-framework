<?php
/**
 * Download Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/*************************************************
 * DOWNLOADS
 *************************************************/

/**
 * Force download of certain file types via ?download=path/filename.type
 *
 * This prompts "Save As" -- handy for MP3, PDF, etc. Only works on local files.
 * 
 * This information was useful: http://wordpress.stackexchange.com/questions/3480/how-can-i-force-a-file-download-in-the-wordpress-backend
 *
 * Use add_theme_support( 'ctfw_force_downloads' );
 *
 * @since 0.9
 * @global object $wp_query
 */
function ctfw_force_download() {
	
    global $wp_query;

	// Theme supports this?
	if ( ! current_theme_supports( 'ctfw-force-downloads' ) ) {
		return;
	}

	// Check if this URL is a request for file download
	if ( is_front_page() && ! empty( $_GET['download'] ) ) {

		// relative file path
		$relative_file_path = ltrim( $_GET['download'], '/' ); // remove preceding slash, if any

		// check for directory traversal attack
		if ( ! validate_file( $relative_file_path ) ) { // false means it passed validation
			
			// path to file in uploads folder (only those can be downloaded)
			$upload_dir = wp_upload_dir();
			$upload_file_path = $upload_dir['basedir'] . '/' . $relative_file_path;

			// make sure file valid as upload (valid type, extension, etc.)
			$validate = wp_check_filetype_and_ext( $upload_file_path, basename( $upload_file_path ) );
			if ( $validate['type'] && $validate['ext'] ) { // empty if type not in upload_mimes, doesn't exist, etc.

				// headers to prompt "save as"
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

		// failure of any type results in 404 file not found
	    $wp_query->set_404();
	    status_header( 404 );
		
	}

}

add_action( 'template_redirect', 'ctfw_force_download' );

/**
 * Convert regular URL to one that forces download ("Save As")
 *
 * This keeps the browser from doing what it wants with the file (such as play MP3 or show PDF).
 * Note that file must be in uploads folder and extension must be allowed by WordPress.
 * 
 * Makes this:	http://yourname.com/?download=%2F2009%2F10%2Ffile.pdf
 * Out of:		http://yourname.com/wp-content/uploads/2013/05/file.pdf
 * 				http://yourname.com/wp-content/uploads/sites/6/2013/05/file.pdf (multisite)
 *
 * @since 0.9
 * @param string $url URL for file
 * @return string URL forcing "Save As" on file if local
 */
function ctfw_force_download_url( $url ) {

	// In case URL is not local or feature not supported by theme
	$download_url = $url;

	// Theme supports this?
	if ( current_theme_supports( 'ctfw-force-downloads' ) ) {

		// Is URL local?
		if ( ctfw_is_local_url( $url ) ) {

			// Get URL to upload directory
			$upload_dir = wp_upload_dir();
			$upload_dir_url = $upload_dir['baseurl'];

			// Get relative URL for file
			$relative_url = str_replace( $upload_dir_url, '', $url ); // remove base URL
			$relative_url = ltrim( $relative_url ); // remove preceding slash

			// Add ?download=file to site URL
			$download_url = site_url( '/' ) . '?download=' . urlencode( $relative_url );

		}

	}

	return apply_filters( 'ctfw_force_download_url', $download_url, $url );

}