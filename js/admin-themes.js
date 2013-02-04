/**
 * Admin Themes Page JavaScript
 */

// Document is loaded...
jQuery(document).ready(function($) {

	// Hide duplicate "Customize" link on themes page (from adding submenu page)
	$('.ctc-screen-base-themes .theme-options li a[href="customize.php"]').parent('li').hide();
	 
});