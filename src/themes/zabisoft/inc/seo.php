<?php
/**
 * Lightweight SEO helpers.
 *
 * Everything here is disabled when a dedicated SEO plugin is active, so the
 * theme is fully compatible with Yoast, Rank Math, SEOPress and AIOSEO.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Open Graph and Twitter cards.
 *
 * @return void
 */
function zabisoft_print_og_tags() {
	if ( zabisoft_seo_plugin_active() ) {
		return;
	}

	$title       = is_singular() ? wp_strip_all_tags( get_the_title() ) : wp_get_document_title();
	$description = is_singular() ? wp_strip_all_tags( get_the_excerpt() ) : get_bloginfo( 'description' );
	$url         = is_singular() ? get_permalink() : home_url( add_query_arg( array(), $GLOBALS['wp']->request ?? '' ) );
	$image       = '';

	if ( is_singular() && has_post_thumbnail() ) {
		$src = wp_get_attachment_image_src( get_post_thumbnail_id(), 'zabisoft-wide' );
		if ( $src ) {
			$image = $src[0];
		}
	}

	printf( '<meta property="og:site_name" content="%s">' . "\n", esc_attr( get_bloginfo( 'name' ) ) );
	printf( '<meta property="og:type" content="%s">' . "\n", is_singular() && ! is_page() ? 'article' : 'website' );
	printf( '<meta property="og:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta property="og:description" content="%s">' . "\n", esc_attr( $description ) );
	printf( '<meta property="og:url" content="%s">' . "\n", esc_url( $url ) );
	printf( '<meta property="og:locale" content="%s">' . "\n", esc_attr( get_locale() ) );

	if ( $image ) {
		printf( '<meta property="og:image" content="%s">' . "\n", esc_url( $image ) );
	}

	echo '<meta name="twitter:card" content="summary_large_image">' . "\n";

	if ( $image ) {
		printf( '<meta name="twitter:image" content="%s">' . "\n", esc_url( $image ) );
	}

	printf( '<meta name="twitter:title" content="%s">' . "\n", esc_attr( $title ) );
	printf( '<meta name="twitter:description" content="%s">' . "\n", esc_attr( $description ) );
}
add_action( 'wp_head', 'zabisoft_print_og_tags', 6 );

/**
 * Add a description meta tag when no SEO plugin is present.
 *
 * @return void
 */
function zabisoft_print_meta_description() {
	if ( zabisoft_seo_plugin_active() ) {
		return;
	}

	$description = '';

	if ( is_singular() ) {
		$description = wp_strip_all_tags( get_the_excerpt() );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$description = wp_strip_all_tags( term_description() );
	}

	if ( '' === $description ) {
		$description = get_bloginfo( 'description' );
	}

	if ( '' === $description ) {
		return;
	}

	printf( '<meta name="description" content="%s">' . "\n", esc_attr( wp_trim_words( $description, 30, '…' ) ) );
}
add_action( 'wp_head', 'zabisoft_print_meta_description', 5 );

/**
 * Keep the archive titles clean (no “Category:” prefixes).
 *
 * @param string $prefix Prefix.
 * @return string
 */
function zabisoft_archive_title_prefix( $prefix ) {
	if ( is_category() ) {
		return esc_html__( 'Category: ', 'zabisoft' );
	}

	return $prefix;
}
add_filter( 'get_the_archive_title_prefix', 'zabisoft_archive_title_prefix' );

/**
 * Add rel attributes to download links printed by the theme.
 *
 * @param string $rel  Existing rel value.
 * @param string $url  URL.
 * @param string $text Link text.
 * @return string
 */
function zabisoft_external_link_rel( $rel, $url, $text = '' ) {
	$host = zabisoft_url_host( $url );

	if ( $host && $host !== zabisoft_url_host( home_url( '/' ) ) ) {
		$rel = trim( $rel . ' nofollow noopener external' );
	}

	return $rel;
}
add_filter( 'zabisoft_link_rel', 'zabisoft_external_link_rel', 10, 3 );
