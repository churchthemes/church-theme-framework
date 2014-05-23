<?php
/**
 * Localization Functions
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013 - 2014, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;


/*******************************************
 * LOAD TEXTDOMAIN
 *******************************************/

/**
 * Load Theme Textdomain
 *
 * Language file should go in wp-content/languages/themes/textdomain-locale.mo.
 * There is no option for using languages folder in theme, because this is dangerous.
 * This folder is only for storing the .pot file and any pre-made translations.
 * It is absolutely best to keep it outside of theme.
 *
 * See http://core.trac.wordpress.org/changeset/22346
 *
 * @since 0.9
 */
function ctfw_load_theme_textdomain() {

	// Theme supports?
	if ( current_theme_supports( 'ctfw-load-translation' ) ) {

		// Textdomain same as theme's directory
		$textdomain = apply_filters( 'ctfw_theme_textdomain', CTFW_THEME_SLUG );

		// First, check for language file from the 'languages' folder in theme (recommended only for pre-made translations coming with theme)
		// Secondarily, load custom language file from outside the theme at wp-content/languages/themes/textdomain-locale.mo (safe from theme updates)
		load_theme_textdomain( $textdomain, CTFW_THEME_LANG_DIR );

	}

}

add_action( 'after_setup_theme', 'ctfw_load_theme_textdomain' );


/*******************************************
 * THEME TRANSLATION
 *******************************************/

/**
 * Use theme's translation file for framework text strings
 *
 * The framework's textdomain is 'church-theme-framework' while the theme has its own textdomain.
 * This makes it so one translation file (the theme's) can be used for both domains.
 *
 * These functions are based on work by Justin Tadlock in the Hybrid Core framework:
 * https://github.com/justintadlock/hybrid-core/blob/master/functions/i18n.php
 */

/**
 * Filter gettext to use theme's translation file for framework text strings
 *
 * @since 0.9
 * @param string $translated Translated text
 * @param string $text Original text
 * @param string $domain Textdomain
 * @return string Modified translated string
 */
function ctfw_gettext( $translated, $text, $domain ) {

	// Theme supports?
	if ( current_theme_supports( 'ctfw-load-translation' ) ) {

		// Framework textdomain?
		if ( 'church-theme-framework' == $domain ) {

			// Use theme's translation
			$translations = get_translations_for_domain( CTFW_THEME_SLUG ); // theme's directory name
			$translated = $translations->translate( $text );

		}

	}

	return $translated;

}

add_filter( 'gettext', 'ctfw_gettext', 1, 3 );

/**
 * Filter gettext_with_context to use theme's translation file for framework text strings
 *
 * @since 1.1.3
 * @param string $translated Translated text
 * @param string $text Original text
 * @param string $context Context of the text
 * @param string $domain Textdomain
 * @return string Modified translated string
 */
function ctfw_gettext_with_context( $translated, $text, $context, $domain ) {

	// Theme supports?
	if ( current_theme_supports( 'ctfw-load-translation' ) ) {

		// Framework textdomain?
		if ( 'church-theme-framework' == $domain ) {

			// Use theme's translation
			$translations = get_translations_for_domain( CTFW_THEME_SLUG ); // theme's directory name
			$translated = $translations->translate( $text, $context );

		}

	}

	return $translated;

}

add_filter( 'gettext_with_context', 'ctfw_gettext_with_context', 1, 4 );

/**
 * Filter ngettext to use theme's translation file for framework text strings
 *
 * @since 1.1.3
 * @param string $translated Translated text
 * @param string $single Singular form of original text
 * @param string $plural Plural form of original text
 * @param int $number Number determining whether singular or plural
 * @param string $domain Textdomain
 * @return string Modified translated string
 */
function ctfw_ngettext( $translated, $single, $plural, $number, $domain ) {

	// Theme supports?
	if ( current_theme_supports( 'ctfw-load-translation' ) ) {

		// Framework textdomain?
		if ( 'church-theme-framework' == $domain ) {

			// Use theme's translation
			$translations = get_translations_for_domain( CTFW_THEME_SLUG ); // theme's directory name
			$translated = $translations->translate_plural( $single, $plural, $number );

		}

	}

	return $translated;

}

add_filter( 'ngettext', 'ctfw_ngettext', 1, 5 );

/**
 * Filter ngettext_with_context to use theme's translation file for framework text strings
 *
 * @since 1.1.3
 * @param string $translated Translated text
 * @param string $single Singular form of original text
 * @param string $plural Plural form of original text
 * @param int $number Number determining whether singular or plural
 * @param string $context Context of the text
 * @param string $domain Textdomain
 * @return string Modified translated string
 */
function ctfw_ngettext_with_context( $translated, $single, $plural, $number, $context, $domain ) {

	// Theme supports?
	if ( current_theme_supports( 'ctfw-load-translation' ) ) {

		// Framework textdomain?
		if ( 'church-theme-framework' == $domain ) {

			// Use theme's translation
			$translations = get_translations_for_domain( CTFW_THEME_SLUG ); // theme's directory name
			$translated = $translations->translate_plural( $single, $plural, $number, $context );

		}

	}

	return $translated;

}

add_filter( 'ngettext_with_context', 'ctfw_ngettext_with_context', 1, 6 );

/*******************************************
 * REPLACE TEXT
 *******************************************/

/**
 * Replace WordPress core text strings
 *
 * WordPress core and its translations sometimes use text that is not preferred.
 *
 * Example: Spanish translation uses "Correo electrónico" on comment forms, which is too long
 * so "Email" would be better to use since it is just as valid in Spanish.
 *
 * Use with add_theme_support() like this:
 *
 *		add_theme_support( 'ctfw-replace-wp-text', array(
 *			'Correo electrónico'	=> __( 'Email', 'textdomain' ), // Spanish: too long for comment form
 *		) );
 *
 * @since 1.2.2
 * @param string Translated string
 * @param string Original string
 * @param string Textdomain
 * @return string Translated string, possibly modified
 */
function ctfw_replace_wp_text( $translated, $original, $domain ) {

	// Get theme support
	$support = get_theme_support( 'ctfw-replace-wp-text' );

	// Feature is used
	if ( ! empty( $support[0] ) ) {

		// WordPress core text strings only
		if ( 'default' == $domain ) {

			// Get strings to replace
			$strings = $support[0];

			// Replace original and translated strings
			if ( ! empty( $strings[$translated] ) ) {
				$translated = $strings[$translated];
			}

		}

	}

	return $translated;

}

add_filter( 'gettext', 'ctfw_replace_wp_text', 10, 3 );
