<?php
/**
 * Navigation walker: accessible submenu toggles for touch devices.
 *
 * The markup matches what WordPress core produces (`<li class="menu-item …">`
 * around every link) so theme styles, plugins that filter the menu classes and
 * the “current page” highlighting keep working. On top of that, every item that
 * has children gets a real button, so a dropdown can be opened with a finger
 * (a tap on a link inside a dropdown would only follow the link).
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class ZabiSoft_Nav_Walker
 */
class ZabiSoft_Nav_Walker extends Walker_Nav_Menu {

	/**
	 * Start sub-level markup.
	 *
	 * @param string   $output Passed by reference.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function start_lvl( &$output, $depth = 0, $args = null ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n{$indent}<ul class=\"sub-menu depth-{$depth}\">\n";
	}

	/**
	 * Start element.
	 *
	 * @param string   $output Passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @param int      $id     Item id.
	 * @return void
	 */
	public function start_el( &$output, $item, $depth = 0, $args = null, $id = 0 ) {
		// WordPress hands over an object, but filters and plugins sometimes pass
		// an array – support both so the walker never fatals.
		$args = (object) ( is_object( $args ) ? get_object_vars( $args ) : (array) $args );

		// Item classes, including the ones WordPress adds for the current page.
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$classes = apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args, $depth );
		$classes = array_map( 'sanitize_html_class', array_unique( $classes ) );

		$has_children = in_array( 'menu-item-has-children', (array) $item->classes, true );

		$item_id = apply_filters( 'nav_menu_item_id', 'menu-item-' . $item->ID, $item, $args, $depth );

		$output .= str_repeat( "\t", $depth );
		$output .= '<li' . ( $item_id ? ' id="' . esc_attr( $item_id ) . '"' : '' );
		$output .= $classes ? ' class="' . esc_attr( implode( ' ', $classes ) ) . '"' : '';
		$output .= '>';

		// Link attributes ----------------------------------------------------.
		$atts = array(
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
			'href'   => ! empty( $item->url ) ? $item->url : '',
		);

		$atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

		$attributes = '';

		foreach ( $atts as $attr => $value ) {
			if ( '' === $value || false === $value ) {
				continue;
			}

			$value       = ( 'href' === $attr ) ? esc_url( $value ) : esc_attr( $value );
			$attributes .= ' ' . $attr . '="' . $value . '"';
		}

		$title = apply_filters( 'the_title', $item->title, $item->ID );
		$title = apply_filters( 'nav_menu_item_title', $title, $item, $args, $depth );

		$item_output  = isset( $args->before ) ? $args->before : '';
		$item_output .= '<a' . $attributes . '>';
		$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
		$item_output .= '</a>';

		if ( $has_children && $depth < 3 ) {
			$item_output .= '<button class="zs-submenu-toggle" type="button" aria-expanded="false">'
				. '<span class="screen-reader-text">' . esc_html__( 'Show submenu', 'zabisoft' ) . '</span>'
				. zabisoft_get_icon_markup( 'chevron' )
				. '</button>';
		}

		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}

	/**
	 * End element.
	 *
	 * @param string   $output Passed by reference.
	 * @param WP_Post  $item   Menu item.
	 * @param int      $depth  Depth.
	 * @param stdClass $args   Args.
	 * @return void
	 */
	public function end_el( &$output, $item, $depth = 0, $args = null ) {
		$output .= "</li>\n";
	}
}
