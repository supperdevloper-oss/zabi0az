<?php
/**
 * Post types and taxonomies.
 *
 * @package ZabiSoft_Software_Manager
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Registers the software catalogue structure.
 */
class ZSM_Post_Types {

	/**
	 * Hook everything up.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_post_type' ), 5 );
		add_action( 'init', array( $this, 'register_taxonomies' ), 6 );
		add_action( 'init', array( $this, 'register_meta' ), 7 );
		add_filter( 'rewrite_rules_array', array( $this, 'protect_root_rules' ), 99 );
		add_filter( 'request', array( $this, 'resolve_root_slug' ) );
	}

	/**
	 * Archive slug used for the software post type.
	 *
	 * @return string
	 */
	public static function archive_slug() {
		$slug = get_option( 'zsm_archive_slug', 'softwares' );

		return $slug ? sanitize_title( $slug ) : 'softwares';
	}

	/**
	 * Base used for the category URLs.
	 *
	 * Empty means “no base”: terms live at the root of the site, exactly like
	 * the reference site (/office-tools/, /antivirus/ …).
	 *
	 * @return string
	 */
	public static function category_base() {
		$base = get_option( 'zsm_category_base', '' );

		return $base ? sanitize_title( $base ) : '';
	}

	/**
	 * Register the software post type.
	 *
	 * @return void
	 */
	public function register_post_type() {
		if ( post_type_exists( 'software' ) ) {
			return;
		}

		$labels = array(
			'name'                  => _x( 'Software', 'Post type general name', 'zabisoft-software-manager' ),
			'singular_name'         => _x( 'Software entry', 'Post type singular name', 'zabisoft-software-manager' ),
			'menu_name'             => _x( 'Software', 'Admin menu', 'zabisoft-software-manager' ),
			'name_admin_bar'        => _x( 'Software entry', 'Add new on admin bar', 'zabisoft-software-manager' ),
			'add_new'               => _x( 'Add new', 'software', 'zabisoft-software-manager' ),
			'add_new_item'          => __( 'Add new software', 'zabisoft-software-manager' ),
			'edit_item'             => __( 'Edit software', 'zabisoft-software-manager' ),
			'new_item'              => __( 'New software', 'zabisoft-software-manager' ),
			'view_item'             => __( 'View software page', 'zabisoft-software-manager' ),
			'view_items'            => __( 'View software pages', 'zabisoft-software-manager' ),
			'search_items'          => __( 'Search software', 'zabisoft-software-manager' ),
			'not_found'             => __( 'No software found.', 'zabisoft-software-manager' ),
			'not_found_in_trash'    => __( 'No software found in the trash.', 'zabisoft-software-manager' ),
			'all_items'             => __( 'All software', 'zabisoft-software-manager' ),
			'archives'              => __( 'Software archive', 'zabisoft-software-manager' ),
			'featured_image'        => __( 'Cover image', 'zabisoft-software-manager' ),
			'set_featured_image'    => __( 'Set the cover image', 'zabisoft-software-manager' ),
			'remove_featured_image' => __( 'Remove the cover image', 'zabisoft-software-manager' ),
			'item_published'        => __( 'Software page published.', 'zabisoft-software-manager' ),
			'item_updated'          => __( 'Software page updated.', 'zabisoft-software-manager' ),
		);

		register_post_type(
			'software',
			array(
				'labels'             => $labels,
				'public'             => true,
				'has_archive'        => true,
				'show_in_rest'       => true,
				'rest_base'          => 'software',
				'menu_icon'          => 'dashicons-download',
				'menu_position'      => 20,
				'supports'           => array( 'title', 'editor', 'excerpt', 'thumbnail', 'comments', 'revisions', 'author', 'custom-fields' ),
				'taxonomies'         => array( 'software_category', 'software_platform', 'software_tag' ),
				'rewrite'            => array(
					'slug'       => self::archive_slug(),
					'with_front' => false,
				),
				'capability_type'    => 'post',
				'map_meta_cap'       => true,
				'delete_with_user'   => false,
			)
		);
	}

	/**
	 * Register the taxonomies.
	 *
	 * @return void
	 */
	public function register_taxonomies() {
		if ( ! taxonomy_exists( 'software_category' ) ) {
			register_taxonomy(
				'software_category',
				array( 'software' ),
				array(
					'labels'            => array(
						'name'              => _x( 'Software categories', 'taxonomy general name', 'zabisoft-software-manager' ),
						'singular_name'     => _x( 'Software category', 'taxonomy singular name', 'zabisoft-software-manager' ),
						'search_items'      => __( 'Search categories', 'zabisoft-software-manager' ),
						'all_items'         => __( 'All categories', 'zabisoft-software-manager' ),
						'parent_item'       => __( 'Parent category', 'zabisoft-software-manager' ),
						'parent_item_colon' => __( 'Parent category:', 'zabisoft-software-manager' ),
						'edit_item'         => __( 'Edit category', 'zabisoft-software-manager' ),
						'update_item'       => __( 'Update category', 'zabisoft-software-manager' ),
						'add_new_item'      => __( 'Add new category', 'zabisoft-software-manager' ),
						'new_item_name'     => __( 'New category name', 'zabisoft-software-manager' ),
						'menu_name'         => __( 'Categories', 'zabisoft-software-manager' ),
					),
					'public'            => true,
					'hierarchical'      => true,
					'show_admin_column' => true,
					'show_in_rest'      => true,
					'rewrite'           => self::category_base()
						? array(
							'slug'         => self::category_base(),
							'with_front'   => false,
							'hierarchical' => true,
						)
						: false,
				)
			);

			$this->add_category_permastruct();
		}

		if ( ! taxonomy_exists( 'software_platform' ) ) {
			register_taxonomy(
				'software_platform',
				array( 'software' ),
				array(
					'labels'            => array(
						'name'          => _x( 'Platforms', 'taxonomy general name', 'zabisoft-software-manager' ),
						'singular_name' => _x( 'Platform', 'taxonomy singular name', 'zabisoft-software-manager' ),
						'menu_name'     => __( 'Platforms', 'zabisoft-software-manager' ),
					),
					'public'            => true,
					'hierarchical'      => false,
					'show_admin_column' => true,
					'show_in_rest'      => true,
				)
			);
		}

		if ( ! taxonomy_exists( 'software_tag' ) ) {
			register_taxonomy(
				'software_tag',
				array( 'software' ),
				array(
					'labels'       => array(
						'name'          => _x( 'Software tags', 'taxonomy general name', 'zabisoft-software-manager' ),
						'singular_name' => _x( 'Software tag', 'taxonomy singular name', 'zabisoft-software-manager' ),
						'menu_name'     => __( 'Tags', 'zabisoft-software-manager' ),
					),
					'public'       => true,
					'hierarchical' => false,
					'show_in_rest' => true,
				)
			);
		}
	}

	/**
	 * Give the categories a root level permastruct.
	 *
	 * WordPress always prefixes a taxonomy rewrite slug, so a taxonomy that
	 * should live at /office-tools/ has to declare its own permastruct. The
	 * rule is registered before the page rules are generated, which is what
	 * makes both /about/ (a page) and /office-tools/ (a term) work.
	 *
	 * @return void
	 */
	public function add_category_permastruct() {
		global $wp_rewrite;

		if ( '' !== self::category_base() || ! $wp_rewrite instanceof WP_Rewrite ) {
			return;
		}

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

	/**
	 * Keep technical URLs away from the root level category rule.
	 *
	 * The category permastruct generates a “([^/]+)/?$” rule. Without a guard it
	 * would also swallow /feed/, /robots.txt, /wp-json and similar addresses, so
	 * the pattern is rewritten with a negative lookahead.
	 *
	 * @param array $rules Generated rewrite rules.
	 * @return array
	 */
	public function protect_root_rules( $rules ) {
		if ( '' !== self::category_base() || ! is_array( $rules ) ) {
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
	public function resolve_root_slug( $query_vars ) {
		if ( is_admin() || empty( $query_vars['software_category'] ) || ! is_string( $query_vars['software_category'] ) ) {
			return $query_vars;
		}

		$slug = $query_vars['software_category'];

		// A real category (or the term id) – nothing to do.
		if ( get_term_by( 'slug', $slug, 'software_category' ) || get_term_by( 'id', (int) $slug, 'software_category' ) ) {
			return $query_vars;
		}

		unset( $query_vars['software_category'] );

		$object = get_page_by_path( $slug, OBJECT, array( 'page', 'post' ) );

		if ( $object instanceof WP_Post ) {
			if ( 'page' === $object->post_type ) {
				$query_vars['pagename'] = $slug;
			} else {
				$query_vars['name']       = $slug;
				$query_vars['post_type']  = 'post';
			}

			return $query_vars;
		}

		// Not a category, not a page, not a post: keep the request as it is, so
		// WordPress shows its normal 404 page.
		$query_vars['error'] = '404';

		return $query_vars;
	}

	/**
	 * Expose the field keys to the REST API so headless setups work too.
	 *
	 * @return void
	 */
	public function register_meta() {
		$fields = ZSM_Fields::all_keys();

		foreach ( $fields as $key ) {
			$args = array(
				'show_in_rest'      => true,
				'single'            => true,
				'type'              => 'string',
				'auth_callback'     => function () {
					return current_user_can( 'edit_posts' );
				},
				'sanitize_callback' => null,
			);

			switch ( $key ) {
				case '_zs_screenshots':
					$args['type']          = 'array';
					$args['show_in_rest']  = array(
						'schema' => array(
							'type'  => 'array',
							'items' => array( 'type' => 'integer' ),
						),
					);
					break;

				case '_zs_mirrors':
					// Mirrors are structured rows; keep them out of the REST API
					// instead of pretending they are a plain string.
					$args['type']         = 'array';
					$args['show_in_rest'] = false;
					break;

				default:
					break;
			}

			register_post_meta( 'software', $key, $args );
		}
	}

	/**
	 * Create helpful default terms on activation (only once).
	 *
	 * @return void
	 */
	public function seed_default_terms() {
		if ( get_option( 'zsm_terms_seeded' ) ) {
			return;
		}

		$categories = array(
			'operating-systems' => array(
				'name'     => __( 'Operating Systems', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'office-tools'      => array(
				'name'     => __( 'Office Tools', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'video-editing'     => array(
				'name'     => __( 'Video & Audio Editing', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'3d-designing'      => array(
				'name'     => __( '3D Designing', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'3d-visualization'  => array(
				'name'     => __( '3D Visualization', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'graphic-design'    => array(
				'name'     => __( 'Graphic Design', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'pdf-editors'       => array(
				'name'     => __( 'PDF Editors', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'antivirus'         => array(
				'name'     => __( 'Antivirus & Security', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'data-recovery'     => array(
				'name'     => __( 'Data Recovery', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'download-managers' => array(
				'name'     => __( 'Download Managers', 'zabisoft-software-manager' ),
				'children' => array(),
			),
			'disk-management'   => array(
				'name'     => __( 'Disk & File Utilities', 'zabisoft-software-manager' ),
				'children' => array(),
			),
		);

		foreach ( $categories as $slug => $category ) {
			$parent_term = term_exists( $slug, 'software_category' );

			if ( ! $parent_term ) {
				$parent_term = wp_insert_term( $category['name'], 'software_category', array( 'slug' => $slug ) );
			}

			if ( is_wp_error( $parent_term ) ) {
				continue;
			}

			$parent_id = is_array( $parent_term ) ? (int) $parent_term['term_id'] : (int) $parent_term;

			foreach ( (array) $category['children'] as $child_slug => $child_name ) {
				if ( ! term_exists( (string) $child_slug, 'software_category' ) ) {
					wp_insert_term(
						(string) $child_name,
						'software_category',
						array(
							'parent' => $parent_id,
							'slug'   => (string) $child_slug,
						)
					);
				}
			}
		}

		foreach ( array( 'Windows', 'macOS', 'Linux', 'Android', 'iOS' ) as $platform ) {
			if ( ! term_exists( $platform, 'software_platform' ) ) {
				wp_insert_term( $platform, 'software_platform' );
			}
		}

		update_option( 'zsm_terms_seeded', 1 );
	}
}
