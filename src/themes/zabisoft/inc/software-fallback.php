<?php
/**
 * Fallback content types.
 *
 * The ZabiSoft Software Manager plugin owns these post types. This file only
 * registers them when the plugin is not installed, so the theme still works and
 * existing entries stay visible.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register the software post type when the plugin is not present.
 *
 * @return void
 */
function zabisoft_fallback_post_types() {
	if ( post_type_exists( 'software' ) ) {
		return;
	}

	register_post_type(
		'software',
		array(
			'labels'          => array(
				'name'          => __( 'Software', 'zabisoft' ),
				'singular_name' => __( 'Software entry', 'zabisoft' ),
				'add_new_item'  => __( 'Add new software', 'zabisoft' ),
				'edit_item'     => __( 'Edit software', 'zabisoft' ),
				'search_items'  => __( 'Search software', 'zabisoft' ),
				'not_found'     => __( 'No software found', 'zabisoft' ),
			),
			'public'          => true,
			'has_archive'     => true,
			'show_in_rest'    => true,
			'menu_icon'       => 'dashicons-download',
			'supports'        => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions', 'custom-fields', 'author' ),
			'rewrite'         => array(
				'slug'       => 'softwares',
				'with_front' => false,
			),
			'capability_type' => 'post',
		)
	);

	register_taxonomy(
		'software_category',
		array( 'software' ),
		array(
			'labels'            => array(
				'name'          => __( 'Software categories', 'zabisoft' ),
				'singular_name' => __( 'Software category', 'zabisoft' ),
			),
			'public'            => true,
			'hierarchical'      => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rewrite'           => false,
		)
	);

	// Root level term URLs (/office-tools/), exactly like the plugin does it.
	global $wp_rewrite;

	if ( $wp_rewrite instanceof WP_Rewrite ) {
		add_rewrite_tag( '%software_category%', '([^/]+)', 'software_category=' );
		$wp_rewrite->add_permastruct(
			'software_category',
			'%software_category%',
			array(
				'with_front' => false,
				'paged'      => true,
				'feed'       => true,
			)
		);
	}

	register_taxonomy(
		'software_platform',
		array( 'software' ),
		array(
			'labels'            => array(
				'name'          => __( 'Platforms', 'zabisoft' ),
				'singular_name' => __( 'Platform', 'zabisoft' ),
			),
			'public'            => true,
			'hierarchical'      => false,
			'show_in_rest'      => true,
			'show_admin_column' => true,
		)
	);

	register_taxonomy(
		'software_tag',
		array( 'software' ),
		array(
			'labels'       => array(
				'name'          => __( 'Software tags', 'zabisoft' ),
				'singular_name' => __( 'Software tag', 'zabisoft' ),
			),
			'public'       => true,
			'hierarchical' => false,
			'show_in_rest' => true,
		)
	);
}
add_action( 'init', 'zabisoft_fallback_post_types', 99 );

// The guards below are only needed while the plugin is not installed: the
// plugin registers the same two filters itself.
if ( ! class_exists( 'ZSM_Plugin' ) ) {
	add_filter( 'rewrite_rules_array', 'zabisoft_protect_root_category_rules', 99 );
	add_filter( 'request', 'zabisoft_resolve_root_category_slug' );
}

/**
 * Keep technical URLs away from the root level category rule.
 *
 * Registered only while the plugin is absent – the plugin installs its own,
 * identical guard. Without it the “([^/]+)/?$” rule would also swallow /feed/,
 * /robots.txt and /wp-json.
 *
 * @param array $rules Generated rewrite rules.
 * @return array
 */
function zabisoft_protect_root_category_rules( $rules ) {
	if ( ! is_array( $rules ) ) {
		return $rules;
	}

	$excluded = 'feed|page|paged|comments|embed|author|search|trackback|robots\.txt|wp-[^/]+|[^/]+\.(?:php|xml|txt|json|html?)';
	$pattern  = '^(?!(?:' . $excluded . ')(?:/|$))([^/]+)/?$';
	$rebuild  = array();

	foreach ( $rules as $match => $query ) {
		if ( '([^/]+)/?$' === $match && is_string( $query ) && false !== strpos( $query, 'software_category=' ) ) {
			$match = $pattern;
		}

		$rebuild[ $match ] = $query;
	}

	return $rebuild;
}

/**
 * When the root level rule matched something that is not a category, hand the
 * request back to the page / post resolver instead of showing a 404.
 *
 * @param array $query_vars Parsed query variables.
 * @return array
 */
function zabisoft_resolve_root_category_slug( $query_vars ) {
	if ( is_admin() || empty( $query_vars['software_category'] ) || ! is_string( $query_vars['software_category'] ) ) {
		return $query_vars;
	}

	$slug = $query_vars['software_category'];

	if ( get_term_by( 'slug', $slug, 'software_category' ) || get_term_by( 'id', (int) $slug, 'software_category' ) ) {
		return $query_vars;
	}

	unset( $query_vars['software_category'] );

	$object = get_page_by_path( $slug, OBJECT, array( 'page', 'post' ) );

	if ( $object instanceof WP_Post ) {
		if ( 'page' === $object->post_type ) {
			$query_vars['pagename'] = $slug;
		} else {
			$query_vars['name']      = $slug;
			$query_vars['post_type'] = 'post';
		}

		return $query_vars;
	}

	$query_vars['error'] = '404';

	return $query_vars;
}

/**
 * Flush rewrite rules once after activation so /softwares/ works immediately.
 *
 * @return void
 */
function zabisoft_maybe_flush_rewrites() {
	if ( get_option( 'zabisoft_rewrites_flushed' ) === ZABISOFT_VERSION ) {
		return;
	}

	flush_rewrite_rules( false );
	update_option( 'zabisoft_rewrites_flushed', ZABISOFT_VERSION );
}
add_action( 'wp_loaded', 'zabisoft_maybe_flush_rewrites' );
