<?php
/**
 * Template tags and helper functions.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Return the inline SVG sprite for the theme icons.
 *
 * A single hidden SVG is printed in the footer and referenced with <use>,
 * which keeps the markup tiny and avoids extra HTTP requests.
 *
 * @return string
 */
function zabisoft_icon_symbols() {
	$icons = array(
		'search'   => '<path d="M10 2a8 8 0 105.3 14l4.4 4.4 1.4-1.4-4.4-4.4A8 8 0 0010 2zm0 2a6 6 0 110 12 6 6 0 010-12z"/>',
		'calendar' => '<path d="M7 2v2H5a2 2 0 00-2 2v13a2 2 0 002 2h14a2 2 0 002-2V6a2 2 0 00-2-2h-2V2h-2v2H9V2H7zm12 7v10H5V9h14z"/>',
		'user'     => '<path d="M12 12a5 5 0 100-10 5 5 0 000 10zm0 2c-4.4 0-8 2.2-8 5v3h16v-3c0-2.8-3.6-5-8-5z"/>',
		'folder'   => '<path d="M4 4h6l2 2h8a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>',
		'comment'  => '<path d="M4 4h16a2 2 0 012 2v10a2 2 0 01-2 2H9l-5 4v-4H4a2 2 0 01-2-2V6a2 2 0 012-2z"/>',
		'clock'    => '<path d="M12 2a10 10 0 100 20 10 10 0 000-20zm1 5v5.6l4 2.4-1 1.7-5-3V7h2z"/>',
		'download' => '<path d="M12 3v10.6l3.3-3.3 1.4 1.4L12 17.4l-4.7-5.7 1.4-1.4L12 13.6V3h0zM5 19h14v2H5z"/>',
		'home'     => '<path d="M12 3l9 8h-3v10h-5v-6h-2v6H6V11H3z"/>',
		'menu'     => '<path d="M3 6h18v2H3zm0 5h18v2H3zm0 5h18v2H3z"/>',
		'tag'      => '<path d="M10 3H4a1 1 0 00-1 1v6l10 10 8-8L10 3zm-3 5a2 2 0 110-4 2 2 0 010 4z"/>',
		'check'    => '<path d="M9 16.2l-3.5-3.5L4 14.2l5 5 11-11-1.5-1.5z"/>',
		'arrow-up' => '<path d="M12 4l8 8h-5v8h-6v-8H4z"/>',
		'list'     => '<path d="M4 6h2v2H4zm4 0h12v2H8zM4 11h2v2H4zm4 0h12v2H8zM4 16h2v2H4zm4 0h12v2H8z"/>',
		'chevron'  => '<path d="M12 15.4L5.6 9l1.4-1.4 5 5 5-5L18.4 9z"/>',
		'external' => '<path d="M14 3h7v7h-2V6.4l-8.3 8.3-1.4-1.4L17.6 5H14V3zM5 5h5v2H7v10h10v-3h2v5H5V5z"/>',
	);

	$out = '<svg class="zs-sprite" aria-hidden="true" focusable="false" style="position:absolute;width:0;height:0;overflow:hidden" xmlns="http://www.w3.org/2000/svg"><defs>';
	foreach ( $icons as $id => $path ) {
		$out .= '<symbol id="zs-icon-' . esc_attr( $id ) . '" viewBox="0 0 24 24">' . $path . '</symbol>';
	}
	$out .= '</defs></svg>';

	return $out;
}

/**
 * Print an icon.
 *
 * @param string $name  Icon name.
 * @param string $class Extra CSS classes.
 * @return void
 */
/**
 * Classes for a listing wrapper (`.zs-post-list`).
 *
 * @param string $extra Extra class.
 * @return string
 */
function zabisoft_list_class( $extra = '' ) {
	$classes = array( 'zs-post-list' );

	if ( zabisoft_mod( 'list_boxed' ) ) {
		$classes[] = 'zs-post-list--boxed';
	}

	if ( $extra ) {
		$classes[] = $extra;
	}

	return implode( ' ', $classes );
}

function zabisoft_icon( $name, $class = 'zs-icon' ) {
	printf(
		'<svg class="%1$s" aria-hidden="true" focusable="false"><use href="#zs-icon-%2$s"></use></svg>',
		esc_attr( $class ),
		esc_attr( $name )
	);
}

/**
 * Post meta line (date, author, category, comments) for archive cards.
 *
 * @return void
 */
function zabisoft_entry_meta() {
	echo '<ul class="zs-meta">';

	printf(
		'<li>%1$s<span class="zs-meta__text">%2$s</span></li>',
		zabisoft_get_icon_markup( 'calendar' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- theme generated markup.
		esc_html( get_the_date() )
	);

	printf(
		'<li>%1$s%2$s</li>',
		zabisoft_get_icon_markup( 'user' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		'<a href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a>'
	);

	$terms = zabisoft_primary_terms();
	if ( $terms ) {
		printf(
			'<li>%1$s%2$s</li>',
			zabisoft_get_icon_markup( 'folder' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			$terms // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- escaped in zabisoft_primary_terms().
		);
	}

	if ( get_comments_number() > 0 ) {
		printf(
			'<li>%1$s%2$s</li>',
			zabisoft_get_icon_markup( 'comment' ), // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			esc_html( sprintf( _n( '%s comment', '%s comments', get_comments_number(), 'zabisoft' ), number_format_i18n( get_comments_number() ) ) )
		);
	}

	echo '</ul>';
}

/**
 * Icon markup string (safe, theme controlled).
 *
 * @param string $name Icon name.
 * @return string
 */
function zabisoft_get_icon_markup( $name ) {
	return '<svg class="zs-icon" aria-hidden="true" focusable="false"><use href="#zs-icon-' . esc_attr( $name ) . '"></use></svg>';
}

/**
 * Primary category / taxonomy links for the current post.
 *
 * @return string Escaped HTML.
 */
function zabisoft_primary_terms() {
	$taxonomy = 'category';
	$links    = array();

	if ( 'software' === get_post_type() ) {
		$taxonomy = 'software_category';
		if ( ! taxonomy_exists( $taxonomy ) ) {
			$taxonomy = 'category';
		}
	}

	$terms = get_the_terms( get_the_ID(), $taxonomy );

	if ( is_wp_error( $terms ) || empty( $terms ) ) {
		return '';
	}

	$terms = array_slice( $terms, 0, 2 );

	foreach ( $terms as $term ) {
		$links[] = '<a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a>';
	}

	return implode( ', ', $links );
}

/**
 * Thumbnail with graceful fallback.
 *
 * @param string $size Image size.
 * @param string $context 'list' or 'card'.
 * @return void
 */
function zabisoft_thumbnail( $size = 'zabisoft-thumb', $context = 'list' ) {
	if ( 'card' === $context ) {
		if ( has_post_thumbnail() ) {
			echo '<a class="zs-card__thumb" href="' . esc_url( get_permalink() ) . '" aria-hidden="true" tabindex="-1">';
			the_post_thumbnail( $size, array( 'loading' => 'lazy' ) );
			echo '</a>';
		}
		return;
	}

	if ( has_post_thumbnail() ) {
		echo '<div class="zs-post__thumb">';
		echo '<a href="' . esc_url( get_permalink() ) . '" aria-hidden="true" tabindex="-1">';
		the_post_thumbnail( $size, array( 'loading' => 'lazy' ) );
		echo '</a>';
		echo '</div>';
		return;
	}

	echo '<div class="zs-post__thumb"><div class="zs-post__thumb--placeholder">' . zabisoft_get_icon_markup( 'download' ) . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
}

/**
 * Numbered pagination.
 *
 * @return void
 */
function zabisoft_pagination() {
	$links = paginate_links(
		array(
			'mid_size'  => 2,
			'end_size'  => 1,
			'prev_text' => '&laquo; ' . __( 'Previous', 'zabisoft' ),
			'next_text' => __( 'Next', 'zabisoft' ) . ' &raquo;',
			'type'      => 'array',
		)
	);

	if ( empty( $links ) ) {
		return;
	}

	echo '<nav class="zs-pagination-wrap" aria-label="' . esc_attr__( 'Posts navigation', 'zabisoft' ) . '"><ul class="zs-pagination">';
	foreach ( $links as $link ) {
		echo '<li>' . str_replace( 'page-numbers', 'page-numbers', $link ) . '</li>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- core generated markup.
	}
	echo '</ul></nav>';
}

/**
 * Breadcrumbs with Schema.org markup.
 *
 * @return void
 */
function zabisoft_breadcrumbs() {
	if ( is_front_page() ) {
		return;
	}

	$items = array();
	$home  = esc_url( home_url( '/' ) );

	$items[] = '<a href="' . $home . '">' . esc_html__( 'Home', 'zabisoft' ) . '</a>';

	if ( is_singular() ) {
		$post_type = get_post_type();
		if ( 'post' === $post_type ) {
			$cats = get_the_category();
			if ( ! empty( $cats ) ) {
				$items[] = '<a href="' . esc_url( get_category_link( $cats[0] ) ) . '">' . esc_html( $cats[0]->name ) . '</a>';
			}
		} elseif ( 'software' === $post_type && taxonomy_exists( 'software_category' ) ) {
			$terms = get_the_terms( get_the_ID(), 'software_category' );
			if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
				$parent = $terms[0];
				if ( $parent->parent ) {
					$ancestor = get_term( $parent->parent, 'software_category' );
					if ( $ancestor && ! is_wp_error( $ancestor ) ) {
						$items[] = '<a href="' . esc_url( get_term_link( $ancestor ) ) . '">' . esc_html( $ancestor->name ) . '</a>';
					}
				}
				$items[] = '<a href="' . esc_url( get_term_link( $parent ) ) . '">' . esc_html( $parent->name ) . '</a>';
			}
		} elseif ( 'page' !== $post_type ) {
			$obj = get_post_type_object( $post_type );
			if ( $obj && ! empty( $obj->has_archive ) ) {
				$items[] = '<a href="' . esc_url( get_post_type_archive_link( $post_type ) ) . '">' . esc_html( $obj->labels->name ) . '</a>';
			}
		}

		$items[] = '<span class="zs-current">' . esc_html( wp_trim_words( get_the_title(), 8, '…' ) ) . '</span>';
	} elseif ( is_search() ) {
		$items[] = '<span class="zs-current">' . esc_html__( 'Search results', 'zabisoft' ) . '</span>';
	} elseif ( is_404() ) {
		$items[] = '<span class="zs-current">' . esc_html__( 'Page not found', 'zabisoft' ) . '</span>';
	} elseif ( is_archive() ) {
		$items[] = '<span class="zs-current">' . esc_html( wp_strip_all_tags( get_the_archive_title() ) ) . '</span>';
	}

	echo '<nav class="zs-breadcrumbs" aria-label="' . esc_attr__( 'Breadcrumb', 'zabisoft' ) . '">';
	echo wp_kses_post( implode( '<span class="zs-sep">/</span>', $items ) );
	echo '</nav>';
}

/**
 * Does a menu location really resolve to a menu with at least one item?
 *
 * has_nav_menu() only checks that an assignment exists. When the assigned menu
 * has since been deleted (or emptied), wp_nav_menu() prints nothing at all and
 * the navigation bar would collapse into an empty strip — the check below sends
 * those cases to the fallback menu instead.
 *
 * @param string $location Theme location name.
 * @return bool
 */
function zabisoft_has_menu( $location ) {
	$locations = get_nav_menu_locations();

	if ( empty( $locations[ $location ] ) ) {
		return false;
	}

	$menu = wp_get_nav_menu_object( $locations[ $location ] );

	if ( ! $menu || is_wp_error( $menu ) ) {
		return false;
	}

	$items = wp_get_nav_menu_items( $menu->term_id );

	return ! empty( $items );
}

/**
 * Fallback menu when no menu is assigned to the primary location.
 *
 * @return void
 */
function zabisoft_fallback_menu() {
	$items = array();

	$items[] = '<li class="zs-home-link menu-item"><a href="' . esc_url( home_url( '/' ) ) . '">' . zabisoft_get_icon_markup( 'home' ) . esc_html__( 'Home', 'zabisoft' ) . '</a></li>';

	$taxonomy = taxonomy_exists( 'software_category' ) ? 'software_category' : 'category';
	$terms    = get_terms(
		array(
			'taxonomy'   => $taxonomy,
			'hide_empty' => false,
			'number'     => 6,
			'parent'     => 0,
		)
	);

	if ( ! is_wp_error( $terms ) ) {
		foreach ( $terms as $term ) {
			$items[] = '<li class="menu-item"><a href="' . esc_url( get_term_link( $term ) ) . '">' . esc_html( $term->name ) . '</a></li>';
		}
	}

	$pages = get_pages(
		array(
			'sort_column' => 'menu_order,post_title',
			'number'      => 2,
			'parent'      => 0,
		)
	);

	foreach ( $pages as $page ) {
		$items[] = '<li class="menu-item"><a href="' . esc_url( get_permalink( $page ) ) . '">' . esc_html( get_the_title( $page ) ) . '</a></li>';
	}

	echo '<ul id="zs-primary-menu" class="zs-nav__list">' . implode( '', $items ) . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- built from escaped parts.
}

/**
 * Reading time estimate.
 *
 * @param int $post_id Post ID.
 * @return string
 */
function zabisoft_reading_time( $post_id = 0 ) {
	$post_id = $post_id ? $post_id : get_the_ID();
	$content = wp_strip_all_tags( get_post_field( 'post_content', $post_id ) );
	$words   = max( 1, str_word_count( $content ) );
	$minutes = max( 1, (int) ceil( $words / 200 ) );

	/* translators: %s: number of minutes. */
	return sprintf( _n( '%s min read', '%s min read', $minutes, 'zabisoft' ), number_format_i18n( $minutes ) );
}

/**
 * Whether the current request should display a sidebar.
 *
 * @return bool
 */
function zabisoft_has_sidebar() {
	if ( 'none' === zabisoft_mod( 'sidebar_position' ) ) {
		return false;
	}

	if ( is_page_template( 'page-templates/template-full-width.php' ) ) {
		return false;
	}

	if ( is_front_page() ) {
		return is_active_sidebar( 'front-page' ) || is_active_sidebar( 'sidebar-1' );
	}

	return is_active_sidebar( 'sidebar-1' );
}

/**
 * Body attribute used by the JS (no inline scripts needed).
 *
 * @return void
 */
function zabisoft_js_config() {
	echo '<div class="zs-js-config" hidden data-countdown-seconds="' . esc_attr( (int) zabisoft_mod( 'countdown_seconds' ) ) . '"></div>';
}

/**
 * Escaped SVG allowed in post content.
 *
 * @param array $tags Allowed tags.
 * @return array
 */
function zabisoft_kses_svg( $tags ) {
	$tags['svg']  = array(
		'class'       => true,
		'viewbox'     => true,
		'viewBox'     => true,
		'width'       => true,
		'height'      => true,
		'fill'        => true,
		'aria-hidden' => true,
		'role'        => true,
		'xmlns'       => true,
	);
	$tags['use']  = array( 'href' => true, 'xlink:href' => true );
	$tags['path'] = array( 'd' => true, 'fill' => true );

	return $tags;
}
