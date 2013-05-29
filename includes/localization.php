<?php
/**
 * Localization Functions
 */

/**
 * Load Theme Textdomain
 *
 * Language file should go in wp-content/languages/themes/textdomain-locale.mo.
 * There is no option for using languages folder in theme, because this is dangerous.
 * This folder is only for storing the .pot file and any pre-made translations.
 * It is absolutely best to keep it outside of theme.
 * 
 * See http://core.trac.wordpress.org/changeset/22346
 */

add_action( 'after_setup_theme', 'ctc_load_theme_textdomain' );
 
function ctc_load_theme_textdomain() {

	// Textdomain same as theme's directory
	$textdomain = apply_filters( 'ctc_theme_textdomain', CTC_TEMPLATE );

	// First, check for language file from the 'languages' folder in theme (recommended only for pre-made translations coming with theme)
	// Secondarily, load custom language file from outside the theme at wp-content/languages/themes/textdomain-locale.mo (safe from theme updates)
	load_theme_textdomain( $textdomain, CTC_LANG_DIR );

}

/**
 * Use theme's translation file for framework text strings
 *
 * The framework's textdomain is 'church-theme-framework' while the theme has its own textdomain.
 * This makes it so one translation file (the theme's) can be used for both domains.
 *
 * Thank you to Justin Tadlock: https://github.com/justintadlock/hybrid-core/blob/master/functions/i18n.php
 */

add_filter( 'gettext', 'ctc_gettext', 1, 3 );

function ctc_gettext( $translated, $text, $domain ) {

	// Framework textdomain?
	if ( 'church-theme-framework' == $domain ) {

		// Use theme's translation
		$translations = get_translations_for_domain( CTC_TEMPLATE ); // theme's directory name
		$translated = $translations->translate( $text );

	}

	return $translated;

}
