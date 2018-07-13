<?php
/**
 * Template Tags
 *
 * These output common elements for different post types.
 *
 * @package    Church_Theme_Framework
 * @subpackage Functions
 * @copyright  Copyright (c) 2013, ChurchThemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    GPLv2 or later
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Post date
 *
 * Output or return the localized, formatted post date, "Today" or "Yesterday", according to options.
 *
 * @since 0.9
 * @param array $options Options for display
 * @param object $post Post object, optional
 * @return string Post date
 */
function ctfw_post_date( $options = array(), $post = null ) {

	$date_formatted = '';

	// Default options
	$defaults = apply_filters( 'ctfw_post_date_default_options', array(
		'return'			=> false,
		'today'				=> true,						// show "Today" if post is from today
		'yesterday'			=> true,						// show "Yesterday" instead of yesterday's date
		'date_format' 		=> get_option( 'date_format' ), // from WordPress general settings
		'abbreviate_date'	=> false,						// true or pass arguments for ctfw_abbreviate_date_format()
	) );
	$options = wp_parse_args( $options, $defaults );

	// Abbreviate date format
	// If $options['abbreviate_date'] is true, default arguments will be used (abbreviate)
	if ( $options['abbreviate_date'] ) {

		// Use the date format passed in already
		$abbreviate_date_args = array(
			'date_format' => $options['date_format'],
		);

		// Passing arguments for abbreviating date
		// Default both true: abbreviate_month, remove_year
		if ( is_array( $options['abbreviate_date'] ) ) {
			$abbreviate_date_args = array_merge( $options['abbreviate_date'], $abbreviate_date_args );
		}

		$options['date_format'] = ctfw_abbreviate_date_format( $abbreviate_date_args );

	}

	// Today and yesterday in local time
	$today_ymd = date_i18n( 'Y-m-d' );
	$yesterday_ymd = date_i18n( 'Y-m-d', strtotime( $today_ymd ) - DAY_IN_SECONDS );

	// Post date
	$date_timestamp = get_the_time( 'U', $post );
	$date_ymd = date_i18n( 'Y-m-d', $date_timestamp );

	// Show "Today"
	if ( $options['today'] && $today_ymd == $date_ymd ) {
		$date_formatted = __( 'Today', 'church-theme-framework' );
	}

	// Show "Yesterday"
	elseif ( $options['yesterday'] && $yesterday_ymd == $date_ymd ) {
		$date_formatted = __( 'Yesterday', 'church-theme-framework' );
	}

	// Show date
	else {
		$date_formatted = date_i18n( $options['date_format'], $date_timestamp ); // translated date
	}

	// Date filtering
	$date_formatted = apply_filters( 'ctfw_post_date', $date_formatted, $options );

	// Output or return
	if ( $options['return'] ) {
		return $date_formatted;
	} else {
		echo  $date_formatted;
	}

}
