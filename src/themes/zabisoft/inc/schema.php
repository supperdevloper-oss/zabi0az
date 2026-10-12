<?php
/**
 * Structured data (Schema.org / JSON-LD).
 *
 * Output is skipped automatically when a dedicated SEO plugin is active, to
 * avoid duplicated graphs.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Whether another plugin already prints structured data.
 *
 * @return bool
 */
function zabisoft_seo_plugin_active() {
	return ( defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' ) );
}

/**
 * Print the JSON-LD graph in the head.
 *
 * @return void
 */
function zabisoft_print_schema() {
	if ( zabisoft_seo_plugin_active() || ! apply_filters( 'zabisoft_enable_schema', true ) ) {
		return;
	}

	$graph = array();

	$graph[] = array(
		'@type'       => 'WebSite',
		'@id'         => home_url( '/#website' ),
		'url'         => home_url( '/' ),
		'name'        => get_bloginfo( 'name' ),
		'description' => get_bloginfo( 'description' ),
		'inLanguage'  => get_bloginfo( 'language' ),
		'publisher'   => array( '@id' => home_url( '/#organization' ) ),
		'potentialAction' => array(
			'@type'       => 'SearchAction',
			'target'      => array(
				'@type'       => 'EntryPoint',
				'urlTemplate' => home_url( '/?s={search_term_string}' ),
			),
			'query-input' => 'required name=search_term_string',
		),
	);

	$organization = array(
		'@type' => 'Organization',
		'@id'   => home_url( '/#organization' ),
		'name'  => get_bloginfo( 'name' ),
		'url'   => home_url( '/' ),
	);

	$logo = get_theme_mod( 'custom_logo' );
	if ( $logo ) {
		$src = wp_get_attachment_image_src( $logo, 'full' );
		if ( $src ) {
			$organization['logo'] = array(
				'@type' => 'ImageObject',
				'url'   => $src[0],
			);
		}
	}

	$graph[] = $organization;

	if ( is_singular() ) {
		$post_id = get_the_ID();

		$article = array(
			'@type'            => is_singular( 'software' ) ? 'SoftwareApplication' : 'BlogPosting',
			'@id'              => get_permalink( $post_id ) . '#article',
			'mainEntityOfPage' => array( '@type' => 'WebPage', '@id' => get_permalink( $post_id ) ),
			'headline'         => wp_strip_all_tags( get_the_title( $post_id ) ),
			'description'      => wp_strip_all_tags( get_the_excerpt( $post_id ) ),
			'datePublished'    => get_the_date( 'c', $post_id ),
			'dateModified'     => get_the_modified_date( 'c', $post_id ),
			'inLanguage'       => get_bloginfo( 'language' ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ),
				'url'   => get_author_posts_url( (int) get_post_field( 'post_author', $post_id ) ),
			),
			'publisher'        => array( '@id' => home_url( '/#organization' ) ),
		);

		if ( has_post_thumbnail( $post_id ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'zabisoft-wide' );
			if ( $image ) {
				$article['image'] = array(
					'@type'  => 'ImageObject',
					'url'    => $image[0],
					'width'  => $image[1],
					'height' => $image[2],
				);
			}
		}

		if ( is_singular( 'software' ) ) {
			$version = (string) get_post_meta( $post_id, '_zs_version', true );
			$os      = (string) get_post_meta( $post_id, '_zs_req_os', true );
			$size    = (string) get_post_meta( $post_id, '_zs_file_size', true );

			$article['name']             = wp_strip_all_tags( get_the_title( $post_id ) );
			$article['applicationCategory'] = 'DesktopApplication';
			$article['operatingSystem']  = $os ? $os : 'Windows';

			if ( $version ) {
				$article['softwareVersion'] = $version;
			}

			if ( $size ) {
				$article['fileSize'] = $size;
			}

			$developer = (string) get_post_meta( $post_id, '_zs_developer', true );
			if ( $developer ) {
				$article['author'] = array(
					'@type' => 'Organization',
					'name'  => $developer,
				);
			}

			$mirrors = zabisoft_get_mirrors( $post_id );
			if ( $mirrors ) {
				$reviews = array();
				foreach ( array_slice( $mirrors, 0, 5 ) as $mirror ) {
					$reviews[] = array(
						'@type'               => 'Offer',
						'price'               => '0',
						'priceCurrency'       => 'USD',
						'availability'        => 'https://schema.org/InStock',
						'url'                 => $mirror['url'],
						'name'                => $mirror['label'],
					);
				}
				$article['offers'] = $reviews;
			}
		}

		$graph[] = $article;

		$breadcrumbs = zabisoft_breadcrumb_schema();
		if ( $breadcrumbs ) {
			$graph[] = $breadcrumbs;
		}
	}

	$output = array(
		'@context' => 'https://schema.org',
		'@graph'   => $graph,
	);

	echo '<script type="application/ld+json">' . wp_json_encode( $output, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE ) . '</script>' . "\n"; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- JSON-LD, encoded.
}
add_action( 'wp_head', 'zabisoft_print_schema', 20 );

/**
 * Breadcrumb schema for the current view.
 *
 * @return array
 */
function zabisoft_breadcrumb_schema() {
	$items    = array();
	$position = 1;

	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $position++,
		'name'     => __( 'Home', 'zabisoft' ),
		'item'     => home_url( '/' ),
	);

	if ( ! is_singular() ) {
		return array();
	}

	if ( is_singular( 'software' ) && taxonomy_exists( 'software_category' ) ) {
		$terms = get_the_terms( get_the_ID(), 'software_category' );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $terms[0]->name,
				'item'     => get_term_link( $terms[0] ),
			);
		}
	} elseif ( is_singular( 'post' ) ) {
		$cats = get_the_category();
		if ( ! empty( $cats ) ) {
			$items[] = array(
				'@type'    => 'ListItem',
				'position' => $position++,
				'name'     => $cats[0]->name,
				'item'     => get_category_link( $cats[0] ),
			);
		}
	}

	$items[] = array(
		'@type'    => 'ListItem',
		'position' => $position,
		'name'     => wp_strip_all_tags( get_the_title() ),
		'item'     => get_permalink(),
	);

	return array(
		'@type'           => 'BreadcrumbList',
		'@id'             => get_permalink() . '#breadcrumb',
		'itemListElement' => $items,
	);
}
