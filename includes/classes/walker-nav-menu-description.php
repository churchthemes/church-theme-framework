<?php
/**
 * Custom walker for adding Description to menu items
 *
 * @package    Church_Theme_Framework
 * @subpackage Classes
 * @copyright  Copyright (c) 2013, churchthemes.com
 * @link       https://github.com/churchthemes/church-theme-framework
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @since      0.9
 */

// No direct access
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Navigation walker class
 *
 * @since 0.9
 */
class CTFW_Walker_Nav_Menu_Description extends Walker_Nav_Menu {

	/**
	 * Replace the start_el() method from Walker::start_el()
	 *
	 * Based on source from /wp-includes/nav-menu-template.php (WordPress 3.4.1)
	 *
	 * @since 0.9
	 * @access public
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $item Menu item data object.
	 * @param int $depth Depth of menu item. Used for padding.
	 * @param int $current_page Menu item ID.
	 * @param object $args
	 *
	 */
	public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {

		$indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

		$class_names = $value = '';

		$classes = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		// Modification: Add class to top level links with no descriptions so dropdown can be moved up
		if ( 0 == $depth && empty ( $item->description ) ) {
			$classes[] = 'ctfw-header-menu-link-no-description';
		}

		$class_names = join( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
		$class_names = $class_names ? ' class="' . esc_attr( $class_names ) . '"' : '';

		$id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
		$id = $id ? ' id="' . esc_attr( $id ) . '"' : '';

		$output .= $indent . '<li' . $id . $value . $class_names .'>';

		$attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr( $item->attr_title ) .'"' : '';
		$attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $item->target     ) .'"' : '';
		$attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $item->xfn        ) .'"' : '';
		$attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $item->url        ) .'"' : '';

		$item_output = $args->before;
		$item_output .= '<a'. $attributes .'>';

		// Original source from WordPress core
		//$item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

		// Modified version of line above
		$link_content = apply_filters( 'the_title', $item->title, $item->ID );
		if ( 0 == $depth ) { // top-level links only
			$link_content  = '<div class="ctfw-header-menu-link-title">' . $link_content . '</div>'; // wrap title portion
			if ( ! empty ( $item->description ) ) { // append description if available
				$link_content .= '<div class="ctfw-header-menu-link-description">' . $item->description . '</div>'; // HTML5 allows div in a
			}
			$link_content = '<div class="ctfw-header-menu-link-inner">' . $link_content . '</div>'; // wrap title and description in inner container
		}
		$item_output .= $args->link_before . $link_content . $args->link_after;

		$item_output .= '</a>';
		$item_output .= $args->after;

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );

	}

}