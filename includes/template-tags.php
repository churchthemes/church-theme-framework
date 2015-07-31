<?php
/**
 * Template Tags
 *
 * These output common elements for different post types.
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
		'return'	=> false,
		'today'		=> true,	// show "Today" if post is from today
		'yesterday'	=> true,	// show "Yesterday" instead of yesterday's date
	) );
	$options = wp_parse_args( $options, $defaults );

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
		$date_format = get_option( 'date_format' ); // this is from WordPress general settings
		$date_formatted = date_i18n( $date_format, $date_timestamp ); // translated date
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
