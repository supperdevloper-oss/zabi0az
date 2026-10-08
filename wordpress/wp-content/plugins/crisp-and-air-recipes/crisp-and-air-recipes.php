<?php
/**
 * Plugin Name: Crisp & Air Recipes
 * Description: Recipe card fields, responsible Recipe JSON-LD, search support, author photos, and a draft-first CSV importer for the Crisp & Air WordPress theme.
 * Version: 1.0.0
 * Requires at least: 6.4
 * Requires PHP: 8.1
 * Author: Crisp & Air
 * Text Domain: crisp-and-air-recipes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'CA_RECIPES_VERSION', '1.0.0' );
define( 'CA_RECIPES_FILE', __FILE__ );
define( 'CA_RECIPES_DIR', plugin_dir_path( __FILE__ ) );
define( 'CA_RECIPES_URL', plugin_dir_url( __FILE__ ) );

require_once CA_RECIPES_DIR . 'includes/helpers.php';
require_once CA_RECIPES_DIR . 'includes/class-ca-recipe-editor.php';
require_once CA_RECIPES_DIR . 'includes/class-ca-recipe-seo.php';
require_once CA_RECIPES_DIR . 'includes/class-ca-recipe-importer.php';
require_once CA_RECIPES_DIR . 'includes/class-ca-contact.php';

add_filter( 'post_type_labels_post', 'ca_recipe_post_labels' );
/** Present native WordPress posts as recipes without changing the familiar editor. */
function ca_recipe_post_labels( $labels ) {
	$labels->name                  = __( 'Recipes', 'crisp-and-air-recipes' );
	$labels->singular_name         = __( 'Recipe', 'crisp-and-air-recipes' );
	$labels->add_new               = __( 'Add Recipe', 'crisp-and-air-recipes' );
	$labels->add_new_item          = __( 'Add New Recipe', 'crisp-and-air-recipes' );
	$labels->edit_item             = __( 'Edit Recipe', 'crisp-and-air-recipes' );
	$labels->new_item              = __( 'New Recipe', 'crisp-and-air-recipes' );
	$labels->view_item             = __( 'View Recipe', 'crisp-and-air-recipes' );
	$labels->view_items            = __( 'View Recipes', 'crisp-and-air-recipes' );
	$labels->search_items          = __( 'Search Recipes', 'crisp-and-air-recipes' );
	$labels->not_found             = __( 'No recipes found.', 'crisp-and-air-recipes' );
	$labels->not_found_in_trash    = __( 'No recipes found in Trash.', 'crisp-and-air-recipes' );
	$labels->all_items             = __( 'All Recipes', 'crisp-and-air-recipes' );
	$labels->archives              = __( 'Recipe Archive', 'crisp-and-air-recipes' );
	$labels->attributes            = __( 'Recipe Attributes', 'crisp-and-air-recipes' );
	$labels->insert_into_item      = __( 'Insert into recipe', 'crisp-and-air-recipes' );
	$labels->uploaded_to_this_item = __( 'Uploaded to this recipe', 'crisp-and-air-recipes' );
	$labels->filter_items_list     = __( 'Filter recipes list', 'crisp-and-air-recipes' );
	$labels->items_list_navigation = __( 'Recipes list navigation', 'crisp-and-air-recipes' );
	$labels->items_list            = __( 'Recipes list', 'crisp-and-air-recipes' );
	$labels->menu_name             = __( 'Recipes', 'crisp-and-air-recipes' );
	$labels->name_admin_bar        = __( 'Recipe', 'crisp-and-air-recipes' );
	return $labels;
}

add_filter( 'taxonomy_labels_category', 'ca_recipe_category_labels' );
function ca_recipe_category_labels( $labels ) {
	$labels->name          = __( 'Recipe Categories', 'crisp-and-air-recipes' );
	$labels->singular_name = __( 'Recipe Category', 'crisp-and-air-recipes' );
	$labels->menu_name     = __( 'Categories', 'crisp-and-air-recipes' );
	$labels->all_items     = __( 'All Recipe Categories', 'crisp-and-air-recipes' );
	return $labels;
}

add_filter( 'taxonomy_labels_post_tag', 'ca_recipe_tag_labels' );
function ca_recipe_tag_labels( $labels ) {
	$labels->name          = __( 'Recipe Tags', 'crisp-and-air-recipes' );
	$labels->singular_name = __( 'Recipe Tag', 'crisp-and-air-recipes' );
	$labels->menu_name     = __( 'Tags', 'crisp-and-air-recipes' );
	return $labels;
}

add_action( 'init', 'ca_recipe_set_default_category', 20 );
function ca_recipe_set_default_category() {
	if ( ! get_option( 'default_category' ) ) {
		$root = get_term_by( 'slug', 'air-fryer-recipes', 'category' );
		if ( $root && ! is_wp_error( $root ) ) {
			update_option( 'default_category', (int) $root->term_id );
		}
	}
}

register_activation_hook( __FILE__, 'ca_recipe_plugin_activate' );
function ca_recipe_plugin_activate() {
	ca_recipe_seed_categories();
	flush_rewrite_rules();
}

function ca_recipe_seed_categories() {
	$root = term_exists( 'air-fryer-recipes', 'category' );
	if ( ! $root ) {
		$root = wp_insert_term(
			__( 'Air Fryer Recipes', 'crisp-and-air-recipes' ),
			'category',
			array(
				'slug'        => 'air-fryer-recipes',
				'description' => __( 'Browse every kind of practical air fryer recipe.', 'crisp-and-air-recipes' ),
			)
		);
	}
	if ( is_wp_error( $root ) ) {
		return;
	}
	$root_id   = is_array( $root ) ? (int) $root['term_id'] : (int) $root;
	$categories = array(
		'chicken'       => array( 'Chicken', 'Crispy wings, juicy chicken dinners, and easy air fryer chicken recipes.' ),
		'beef'          => array( 'Beef', 'Straightforward air fryer beef recipes and weeknight favorites.' ),
		'pork'          => array( 'Pork', 'Air fryer pork chops, tenderloin, and more.' ),
		'seafood'       => array( 'Seafood', 'Air fryer fish, shrimp, and seafood suppers.' ),
		'vegetables'    => array( 'Vegetables', 'Colorful vegetable sides and mains from the air fryer.' ),
		'potatoes'      => array( 'Potatoes', 'Fries, wedges, and other crisp air fryer potato recipes.' ),
		'breakfast'     => array( 'Breakfast', 'Air fryer breakfasts for relaxed mornings and busy weekdays.' ),
		'lunch'         => array( 'Lunch', 'Air fryer lunches, wraps, and quick midday ideas.' ),
		'dinner'        => array( 'Dinner', 'Practical air fryer dinners for any night of the week.' ),
		'appetizers'    => array( 'Appetizers', 'Shareable air fryer starters and party bites.' ),
		'snacks'        => array( 'Snacks', 'Crispy, satisfying air fryer snacks.' ),
		'desserts'      => array( 'Desserts', 'Small-batch air fryer desserts and sweet treats.' ),
		'frozen-foods'  => array( 'Frozen Foods', 'Useful timing guides for popular frozen foods.' ),
		'healthy'       => array( 'Healthy Recipes', 'Balanced air fryer recipe ideas with practical ingredient notes.' ),
		'quick-and-easy' => array( 'Quick & Easy', 'Air fryer recipes with shorter prep and simple steps.' ),
		'vegetarian'    => array( 'Vegetarian', 'Vegetarian air fryer meals, sides, and snacks.' ),
	);
	foreach ( $categories as $slug => $data ) {
		if ( ! term_exists( $slug, 'category' ) ) {
			wp_insert_term(
				$data[0],
				'category',
				array(
					'slug'        => $slug,
					'parent'      => $root_id,
					'description' => $data[1],
				)
			);
		}
	}
	if ( ! get_option( 'default_category' ) || 1 === (int) get_option( 'default_category' ) ) {
		update_option( 'default_category', $root_id );
	}
}

// Add recipe-card facts to WordPress search without fetching or indexing the entire recipe library.
add_filter( 'posts_search', 'ca_recipe_search_ingredients', 10, 2 );
function ca_recipe_search_ingredients( $search, $query ) {
	global $wpdb;
	if ( is_admin() || ! $query->is_search() || ! $query->is_main_query() || '' === $search ) {
		return $search;
	}
	$terms = $query->get( 'search_terms' );
	if ( ! is_array( $terms ) || empty( $terms ) ) {
		$terms = array( $query->get( 's' ) );
	}
	$extra = array();
	foreach ( $terms as $term ) {
		$term = trim( (string) $term );
		if ( '' === $term ) {
			continue;
		}
		$like   = '%' . $wpdb->esc_like( $term ) . '%';
		$extra[] = $wpdb->prepare(
			"EXISTS (SELECT 1 FROM {$wpdb->postmeta} AS ca_search_meta WHERE ca_search_meta.post_id = {$wpdb->posts}.ID AND ca_search_meta.meta_key = '_ca_ingredients' AND ca_search_meta.meta_value LIKE %s)",
			$like
		);
		$extra[] = $wpdb->prepare(
			"EXISTS (SELECT 1 FROM {$wpdb->term_relationships} AS ca_search_relationship INNER JOIN {$wpdb->term_taxonomy} AS ca_search_taxonomy ON ca_search_relationship.term_taxonomy_id = ca_search_taxonomy.term_taxonomy_id INNER JOIN {$wpdb->terms} AS ca_search_term ON ca_search_taxonomy.term_id = ca_search_term.term_id WHERE ca_search_relationship.object_id = {$wpdb->posts}.ID AND ca_search_taxonomy.taxonomy IN ('category','post_tag') AND (ca_search_term.name LIKE %s OR ca_search_term.slug LIKE %s))",
			$like,
			$like
		);
	}
	if ( ! empty( $extra ) ) {
		$base_search = preg_replace( '/^\\s*AND\\s*/', '', $search, 1 );
		$search      = ' AND (' . $base_search . ' OR (' . implode( ' OR ', $extra ) . '))';
	}
	return $search;
}

// Keep native WordPress search restricted to recipe posts and never index its query URLs.
add_action( 'pre_get_posts', 'ca_recipe_search_defaults', 5 );
function ca_recipe_search_defaults( $query ) {
	if ( ! is_admin() && $query->is_main_query() && $query->is_search() ) {
		$query->set( 'post_type', 'post' );
		$query->set( 'posts_per_page', 12 );
	}
}

add_action( 'admin_enqueue_scripts', 'ca_recipe_admin_assets' );
function ca_recipe_admin_assets( $hook ) {
	$screen = function_exists( 'get_current_screen' ) ? get_current_screen() : null;
	$is_recipe_edit = $screen && in_array( $screen->base, array( 'post', 'post-new' ), true ) && 'post' === $screen->post_type;
	$is_profile     = in_array( $hook, array( 'profile.php', 'user-edit.php' ), true );
	if ( ! $is_recipe_edit && ! $is_profile ) {
		return;
	}
	wp_enqueue_style( 'ca-recipe-admin', CA_RECIPES_URL . 'assets/admin.css', array(), CA_RECIPES_VERSION );
	wp_enqueue_script( 'ca-recipe-admin', CA_RECIPES_URL . 'assets/admin.js', array( 'jquery' ), CA_RECIPES_VERSION, true );
	if ( $is_profile ) {
		wp_enqueue_media();
	}
}
