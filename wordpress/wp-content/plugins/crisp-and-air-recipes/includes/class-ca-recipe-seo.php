<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'document_title_parts', 'ca_recipe_document_title_parts' );
function ca_recipe_document_title_parts( $parts ) {
	if ( ca_recipe_seo_plugin_active() || ! is_singular( 'post' ) ) {
		return $parts;
	}
	$custom = trim( (string) ca_recipe_meta( get_queried_object_id(), 'seo_title' ) );
	if ( '' !== $custom ) {
		$parts['title'] = $custom;
	}
	return $parts;
}

function ca_recipe_seo_plugin_active() {
	$active = (array) get_option( 'active_plugins', array() );
	if ( is_multisite() ) {
		$active = array_merge( $active, array_keys( (array) get_site_option( 'active_sitewide_plugins', array() ) ) );
	}
	$known = array(
		'wordpress-seo/wp-seo.php',
		'seo-by-rank-math/rank-math.php',
		'all-in-one-seo-pack/all_in_one_seo_pack.php',
		'aioseo/aioseo.php',
		'wp-seopress/seopress.php',
		'theseoframework/the-seo-framework.php',
		'autodescription/autodescription.php',
	);
	return (bool) array_intersect( $known, $active );
}

add_filter( 'wp_robots', 'ca_recipe_robots_directives' );
add_filter( 'wp_sitemaps_taxonomies', 'ca_recipe_sitemap_taxonomies' );
function ca_recipe_sitemap_taxonomies( $taxonomies ) {
	// Tags are intentionally noindex; do not spend sitemap URLs on duplicate tag archives.
	unset( $taxonomies['post_tag'] );
	return $taxonomies;
}
function ca_recipe_robots_directives( $robots ) {
	if ( is_search() || is_tag() || ( is_category() && ! have_posts() ) || ( is_author() && ! have_posts() ) ) {
		$robots['noindex'] = true;
		unset( $robots['index'] );
	}
	return $robots;
}

add_action( 'wp_head', 'ca_recipe_remove_core_canonical', 1 );
function ca_recipe_remove_core_canonical() {
	if ( ! ca_recipe_seo_plugin_active() ) {
		remove_action( 'wp_head', 'rel_canonical' );
	}
}

add_action( 'wp_head', 'ca_recipe_print_seo_metadata', 2 );
function ca_recipe_print_seo_metadata() {
	if ( ca_recipe_seo_plugin_active() ) {
		return;
	}
	$title       = ca_recipe_current_seo_title();
	$description = ca_recipe_current_description();
	$canonical   = ca_recipe_current_canonical();
	$image       = ca_recipe_current_image();
	$is_article  = is_singular( 'post' );
	$site_name   = get_bloginfo( 'name' );
	?>
	<?php if ( $description ) : ?><meta name="description" content="<?php echo esc_attr( $description ); ?>" /><?php endif; ?>
	<?php if ( $canonical ) : ?><link rel="canonical" href="<?php echo esc_url( $canonical ); ?>" /><?php endif; ?>
	<meta property="og:locale" content="<?php echo esc_attr( get_locale() ); ?>" />
	<meta property="og:type" content="<?php echo $is_article ? 'article' : 'website'; ?>" />
	<meta property="og:site_name" content="<?php echo esc_attr( $site_name ); ?>" />
	<meta property="og:title" content="<?php echo esc_attr( $title ); ?>" />
	<?php if ( $description ) : ?><meta property="og:description" content="<?php echo esc_attr( $description ); ?>" /><?php endif; ?>
	<?php if ( $canonical ) : ?><meta property="og:url" content="<?php echo esc_url( $canonical ); ?>" /><?php endif; ?>
	<?php if ( $image ) : ?><meta property="og:image" content="<?php echo esc_url( $image ); ?>" /><?php endif; ?>
	<meta name="twitter:card" content="<?php echo $image ? 'summary_large_image' : 'summary'; ?>" />
	<meta name="twitter:title" content="<?php echo esc_attr( $title ); ?>" />
	<?php if ( $description ) : ?><meta name="twitter:description" content="<?php echo esc_attr( $description ); ?>" /><?php endif; ?>
	<?php if ( $image ) : ?><meta name="twitter:image" content="<?php echo esc_url( $image ); ?>" /><?php endif; ?>
	<?php
}

function ca_recipe_current_seo_title() {
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$custom  = ca_recipe_meta( $post_id, 'seo_title' );
		return $custom ? $custom : get_the_title( $post_id );
	}
	if ( is_front_page() ) {
		return get_bloginfo( 'name' );
	}
	if ( is_category() || is_tag() ) {
		return single_term_title( '', false ) . ' | ' . get_bloginfo( 'name' );
	}
	if ( is_search() ) {
		return sprintf( __( 'Search results for %s', 'crisp-and-air-recipes' ), get_search_query() ) . ' | ' . get_bloginfo( 'name' );
	}
	if ( is_author() ) {
		$author = get_queried_object();
		return ( $author && isset( $author->display_name ) ? $author->display_name : __( 'Author', 'crisp-and-air-recipes' ) ) . ' | ' . get_bloginfo( 'name' );
	}
	return wp_get_document_title();
}

function ca_recipe_current_description() {
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$custom  = ca_recipe_meta( $post_id, 'meta_description' );
		if ( $custom ) {
			return wp_trim_words( wp_strip_all_tags( $custom ), 32, '' );
		}
		$post = get_post( $post_id );
		if ( $post && has_excerpt( $post ) ) {
			return wp_trim_words( wp_strip_all_tags( get_the_excerpt( $post ) ), 32, '' );
		}
		return $post ? wp_trim_words( wp_strip_all_tags( strip_shortcodes( $post->post_content ) ), 32, '' ) : '';
	}
	if ( is_category() || is_tag() ) {
		$term = get_queried_object();
		return $term && ! is_wp_error( $term ) ? wp_trim_words( wp_strip_all_tags( term_description( $term ) ), 32, '' ) : '';
	}
	if ( is_front_page() ) {
		$description = get_theme_mod( 'ca_hero_description', get_bloginfo( 'description' ) );
		return wp_trim_words( wp_strip_all_tags( $description ), 32, '' );
	}
	if ( is_search() ) {
		return sprintf( __( 'Search air fryer recipes for %s.', 'crisp-and-air-recipes' ), get_search_query() );
	}
	return wp_trim_words( wp_strip_all_tags( get_bloginfo( 'description' ) ), 32, '' );
}

function ca_recipe_current_canonical() {
	if ( is_search() || is_404() ) {
		return '';
	}
	if ( is_front_page() ) {
		return home_url( '/' );
	}
	if ( is_singular() ) {
		return wp_get_canonical_url( get_queried_object_id() );
	}
	if ( is_archive() || is_home() ) {
		$paged = max( 1, (int) get_query_var( 'paged' ) );
		$base  = '';
		if ( is_category() || is_tag() || is_tax() ) {
			$term = get_queried_object();
			$link = $term ? get_term_link( $term ) : '';
			$base = is_wp_error( $link ) ? '' : $link;
		} elseif ( is_author() ) {
			$base = get_author_posts_url( (int) get_queried_object_id() );
		} elseif ( is_home() && get_option( 'page_for_posts' ) ) {
			$base = get_permalink( (int) get_option( 'page_for_posts' ) );
		}
		if ( ! $base ) {
			$base = get_pagenum_link( 1 );
		}
		if ( 1 === $paged ) {
			return $base;
		}
		if ( get_option( 'permalink_structure' ) ) {
			return user_trailingslashit( trailingslashit( $base ) . 'page/' . $paged, 'paged' );
		}
		return add_query_arg( 'paged', $paged, $base );
	}
	return '';
}

function ca_recipe_current_image() {
	if ( is_singular() ) {
		$post_id = get_queried_object_id();
		$image    = get_the_post_thumbnail_url( $post_id, 'large' );
		if ( $image ) {
			return $image;
		}
	}
	if ( is_author() ) {
		$author = get_queried_object();
		if ( $author ) {
			$image_id = absint( get_user_meta( $author->ID, 'ca_author_image_id', true ) );
			if ( $image_id ) {
				return wp_get_attachment_image_url( $image_id, 'large' );
			}
		}
	}
	return '';
}

add_action( 'wp_head', 'ca_recipe_print_recipe_schema', 20 );
function ca_recipe_print_recipe_schema() {
	if ( ! is_singular( 'post' ) || 'publish' !== get_post_status( get_queried_object_id() ) ) {
		return;
	}
	if ( class_exists( 'WPRM_Recipe_Manager' ) || class_exists( 'Tasty_Recipes' ) || class_exists( 'WPZOOM_Recipe_Card_Block' ) ) {
		return;
	}
	if ( ! apply_filters( 'ca_recipe_schema_enabled', true, get_queried_object_id() ) ) {
		return;
	}
	$post_id     = get_queried_object_id();
	$ingredients = ca_recipe_lines( ca_recipe_meta( $post_id, 'ingredients' ) );
	$instructions = ca_recipe_lines( ca_recipe_meta( $post_id, 'instructions' ) );
	$prep        = absint( ca_recipe_meta( $post_id, 'prep_minutes' ) );
	$cook        = absint( ca_recipe_meta( $post_id, 'cook_minutes' ) );
	$servings    = trim( (string) ca_recipe_meta( $post_id, 'servings' ) );
	$thumbnail   = get_post_thumbnail_id( $post_id );
	$author_id   = (int) get_post_field( 'post_author', $post_id );
	$description = ca_recipe_current_description();
	if ( empty( $ingredients ) || empty( $instructions ) || ! $prep || ! $cook || '' === $servings || ! $thumbnail || ! $description ) {
		return;
	}
	$image_urls = array();
	foreach ( array( 'large', 'ca-card', 'full' ) as $size ) {
		$image = wp_get_attachment_image_src( $thumbnail, $size );
		if ( $image && ! in_array( $image[0], $image_urls, true ) ) {
			$image_urls[] = $image[0];
		}
	}
	if ( empty( $image_urls ) ) {
		return;
	}
	$author_name = get_the_author_meta( 'display_name', $author_id );
	$recipe      = array(
		'@context'           => 'https://schema.org',
		'@type'              => 'Recipe',
		'name'               => get_the_title( $post_id ),
		'image'              => $image_urls,
		'description'        => $description,
		'author'             => array(
			'@type' => 'Person',
			'name'  => $author_name ? $author_name : get_bloginfo( 'name' ),
			'url'   => get_author_posts_url( $author_id ),
		),
		'datePublished'      => get_the_date( 'c', $post_id ),
		'dateModified'       => get_the_modified_date( 'c', $post_id ),
		'prepTime'           => ca_recipe_iso_duration( $prep ),
		'cookTime'           => ca_recipe_iso_duration( $cook ),
		'totalTime'          => ca_recipe_iso_duration( $prep + $cook ),
		'recipeYield'        => $servings,
		'recipeIngredient'   => $ingredients,
		'recipeInstructions' => array_map(
			static function ( $instruction ) {
				return array(
					'@type' => 'HowToStep',
					'text'  => $instruction,
				);
			},
			$instructions
		),
		'mainEntityOfPage'   => get_permalink( $post_id ),
	);
	$course  = ca_recipe_meta( $post_id, 'course' );
	$cuisine = ca_recipe_meta( $post_id, 'cuisine' );
	if ( $course ) {
		$recipe['recipeCategory'] = $course;
	} else {
		$categories = get_the_category( $post_id );
		if ( $categories ) {
			foreach ( $categories as $category ) {
				if ( 'air-fryer-recipes' !== $category->slug ) {
					$recipe['recipeCategory'] = $category->name;
					break;
				}
			}
		}
	}
	if ( $cuisine ) {
		$recipe['recipeCuisine'] = $cuisine;
	}
	$calories = absint( ca_recipe_meta( $post_id, 'calories' ) );
	if ( $calories ) {
		$recipe['nutrition'] = array(
			'@type'    => 'NutritionInformation',
			'calories' => $calories . ' calories',
		);
	}
	$author_image_id = absint( get_user_meta( $author_id, 'ca_author_image_id', true ) );
	if ( $author_image_id ) {
		$author_image = wp_get_attachment_image_url( $author_image_id, 'thumbnail' );
		if ( $author_image ) {
			$recipe['author']['image'] = $author_image;
		}
	}
	$publisher = array(
		'@type' => 'Organization',
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
	);
	$logo_id = get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$logo = wp_get_attachment_image_url( $logo_id, 'full' );
		if ( $logo ) {
			$publisher['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $logo,
			);
		}
	}
	$recipe['publisher'] = $publisher;
	$json = wp_json_encode( $recipe, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT );
	if ( $json ) {
		echo "\n<script type=\"application/ld+json\">" . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON is encoded and HTML-sensitive characters are hex escaped.
	}
}
