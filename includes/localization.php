<?php
/**
 * Localization Functions
 */

/**
 * Load Theme Textdomain
 *
 * Language file should go in wp-content/languages/themes/textdomain-locale.mo.
 * This keeps translation from being updated when theme is updated.
 * 
 * See http://core.trac.wordpress.org/changeset/22346
 */

add_action( 'after_setup_theme', 'ctc_load_theme_textdomain' );
 
function ctc_load_theme_textdomain() {

	// Textdomain same as theme's directory
	$textdomain = apply_filters( 'ctc_theme_textdomain', CTC_TEMPLATE );

	// By default, this loads locale.mo from theme's directory
	// Secondarily, it loads wp-content/languages/themes/textdomain-locale.mo (much better for updates)
	load_theme_textdomain( $textdomain );

}

/**
 * Use theme's translation file for framework text strings
 *
 * The framework's textdomain is 'ct-framework' while the theme has its own textdomain.
 * This makes it so one translation file (the theme's) can be used for both domains.
 *
 * Thank you to Justin Tadlock: https://github.com/justintadlock/hybrid-core/blob/master/functions/i18n.php
 */

add_filter( 'gettext', 'ctc_gettext', 1, 3 );

function ctc_gettext( $translated, $text, $domain ) {

	// Framework textdomain?
	if ( 'ct-framework' == $domain ) {

		// Use theme's translation
		$translations = get_translations_for_domain( CTC_TEMPLATE ); // theme's directory name
		$translated = $translations->translate( $text );

	}

	return $translated;

}
