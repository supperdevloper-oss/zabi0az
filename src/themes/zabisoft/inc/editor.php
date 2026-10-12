<?php
/**
 * Block editor integration: styles and ready made patterns.
 *
 * @package ZabiSoft
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register block patterns that mirror the software page sections.
 *
 * @return void
 */
function zabisoft_register_block_patterns() {
	if ( ! function_exists( 'register_block_pattern' ) ) {
		return;
	}

	register_block_pattern_category(
		'zabisoft',
		array( 'label' => __( 'ZabiSoft', 'zabisoft' ) )
	);

	$patterns = array(
		'features' => array(
			'title'   => __( 'Software features list', 'zabisoft' ),
			'content' => "<!-- wp:heading -->\n<h2>" . esc_html__( 'Key features', 'zabisoft' ) . "</h2>\n<!-- /wp:heading -->\n\n<!-- wp:list -->\n<ul><li>" . esc_html__( 'Feature one', 'zabisoft' ) . "</li><li>" . esc_html__( 'Feature two', 'zabisoft' ) . "</li><li>" . esc_html__( 'Feature three', 'zabisoft' ) . "</li></ul>\n<!-- /wp:list -->",
		),
		'specs'    => array(
			'title'   => __( 'Technical setup details table', 'zabisoft' ),
			'content' => "<!-- wp:heading -->\n<h2>" . esc_html__( 'Technical setup details', 'zabisoft' ) . "</h2>\n<!-- /wp:heading -->\n\n<!-- wp:table {\"className\":\"zs-specs-table\"} -->\n<figure class=\"wp-block-table zs-specs-table\"><table><tbody><tr><td>" . esc_html__( 'Software name', 'zabisoft' ) . "</td><td></td></tr><tr><td>" . esc_html__( 'File size', 'zabisoft' ) . "</td><td></td></tr><tr><td>" . esc_html__( 'Setup type', 'zabisoft' ) . "</td><td></td></tr></tbody></table></figure>\n<!-- /wp:table -->",
		),
		'notice'   => array(
			'title'   => __( 'Info / disclaimer box', 'zabisoft' ),
			'content' => "<!-- wp:group {\"className\":\"zs-toc\"} -->\n<div class=\"wp-block-group zs-toc\"><!-- wp:paragraph -->\n<p>" . esc_html__( 'Always check the licence of a program before installing it and only download from sources you trust.', 'zabisoft' ) . "</p>\n<!-- /wp:paragraph --></div>\n<!-- /wp:group -->",
		),
		'download' => array(
			'title'   => __( 'Download call to action', 'zabisoft' ),
			'content' => "<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"className\":\"zs-btn--download\"} -->\n<div class=\"wp-block-button\"><a class=\"wp-block-button__link zs-btn--download\" href=\"#\">" . esc_html__( 'Download now', 'zabisoft' ) . "</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons -->",
		),
	);

	foreach ( $patterns as $slug => $pattern ) {
		register_block_pattern(
			'zabisoft/' . $slug,
			array(
				'title'      => $pattern['title'],
				'categories' => array( 'zabisoft' ),
				'content'    => $pattern['content'],
			)
		);
	}
}
add_action( 'init', 'zabisoft_register_block_patterns' );

/**
 * Register a few block styles so editors can match the front end.
 *
 * @return void
 */
function zabisoft_register_block_styles() {
	if ( ! function_exists( 'register_block_style' ) ) {
		return;
	}

	register_block_style(
		'core/table',
		array(
			'name'  => 'zs-specs-table',
			'label' => __( 'ZabiSoft specifications table', 'zabisoft' ),
		)
	);

	register_block_style(
		'core/button',
		array(
			'name'  => 'zs-btn--download',
			'label' => __( 'ZabiSoft download button', 'zabisoft' ),
		)
	);
}
add_action( 'init', 'zabisoft_register_block_styles' );
