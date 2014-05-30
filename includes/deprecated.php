<?php
/**
 * Deprecated Functions
 *
 * Deprecated functions will go here; avoid breakage and trigger _deprecated_function().
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

/**
 * Possible candidate may be ctfw_theme_url() which could end up in WordPress core as
 * something like theme_url() at some point: http://core.trac.wordpress.org/ticket/18302
 */

/**
 * @since 0.9
 * @deprecated 1.3
 */
function ctfw_edd_license_check_deactivation() {

	_deprecated_function( __FUNCTION__, '1.3', 'ctfw_edd_license_sync()' );

	ctfw_edd_license_sync();

}

/**
 * @since 0.9
 * @deprecated 1.3
 */
function ctfw_edd_license_auto_check_deactivation() {

	_deprecated_function( __FUNCTION__, '1.3', 'ctfw_edd_license_auto_sync()' );

	ctfw_edd_license_sync();

}
