<?php
/**
 * Navigation walker: adds accessible submenu toggles for touch devices.
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
		$classes   = empty( $item->classes ) ? array() : (array) $item->classes;
		$classes[] = 'menu-item-' . $item->ID;

		$has_children = in_array( 'menu-item-has-children', $classes, true );
		$atts         = array(
			'href'   => ! empty( $item->url ) ? $item->url : '',
			'title'  => ! empty( $item->attr_title ) ? $item->attr_title : '',
			'target' => ! empty( $item->target ) ? $item->target : '',
			'rel'    => ! empty( $item->xfn ) ? $item->xfn : '',
		);

		if ( $atts['target'] && empty( $atts['rel'] ) ) {
			$atts['rel'] = 'noopener';
		}

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

		$icon = '';
		if ( $depth > 0 ) {
			$icon = '';
		} elseif ( ! empty( $item->object ) && 'page' === $item->object && ! empty( $args->theme_location ) && 'primary' === $args->theme_location ) {
			$icon = '';
		}

		$item_output .= ( isset( $args->link_before ) ? $args->link_before : '' ) . $icon . $title . ( isset( $args->link_after ) ? $args->link_after : '' );
		$item_output .= '</a>';

		if ( $has_children ) {
			$item_output .= '<button class="zs-submenu-toggle" type="button" aria-expanded="false"><span class="screen-reader-text">' .
				esc_html__( 'Show submenu', 'zabisoft' ) . '</span><svg class="zs-icon" aria-hidden="true" focusable="false"><use href="#zs-icon-list"></use></svg></button>';
		}

		$item_output .= isset( $args->after ) ? $args->after : '';

		$output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
	}
}
