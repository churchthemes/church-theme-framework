<?php
/**
 * Localization
 */

add_action( 'after_setup_theme', 'ctc_fw_localization' );
 
function ctc_fw_localization() {

	// Localization
	// See the sample child theme's functions.php file if you want to keep your language files there
	$languages_path = CTC_THEME_PATH . '/languages';
	$languages_path = apply_filters( 'ctc_languages_path', $languages_path );
	load_theme_textdomain( 'church-theme', $languages_path );
	
}