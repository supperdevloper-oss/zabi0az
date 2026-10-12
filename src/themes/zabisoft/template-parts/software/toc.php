<?php
/**
 * Table of contents for software pages.
 *
 * @package ZabiSoft
 * @var array $args {
 *     @type array $items   Content headings collected from the editor.
 *     @type bool  $section Whether the software data sections are printed.
 * }
 */

$zs_items   = isset( $args['items'] ) && is_array( $args['items'] ) ? $args['items'] : array();
$zs_section = ! empty( $args['section'] );

$zs_toc = array(
	array(
		'id'    => 'zs-overview',
		'text'  => __( 'Overview', 'zabisoft' ),
		'level' => 2,
	),
);

foreach ( $zs_items as $zs_item ) {
	$zs_toc[] = $zs_item;
}

if ( $zs_section ) {
	if ( zabisoft_mod( 'show_screenshots' ) && get_post_meta( get_the_ID(), '_zs_screenshots', true ) ) {
		$zs_toc[] = array(
			'id'    => 'zs-screenshots',
			'text'  => __( 'Screenshots', 'zabisoft' ),
			'level' => 2,
		);
	}

	if ( zabisoft_mod( 'show_specs' ) ) {
		$zs_toc[] = array(
			'id'    => 'zs-specs',
			'text'  => __( 'Technical setup details', 'zabisoft' ),
			'level' => 2,
		);
	}

	if ( zabisoft_mod( 'show_requirements' ) ) {
		$zs_toc[] = array(
			'id'    => 'zs-requirements',
			'text'  => __( 'System requirements', 'zabisoft' ),
			'level' => 2,
		);
	}

	if ( get_post_meta( get_the_ID(), '_zs_features', true ) ) {
		$zs_toc[] = array(
			'id'    => 'zs-features',
			'text'  => __( 'Key features', 'zabisoft' ),
			'level' => 2,
		);
	}

	if ( zabisoft_mod( 'show_install_steps' ) && get_post_meta( get_the_ID(), '_zs_install_steps', true ) ) {
		$zs_toc[] = array(
			'id'    => 'zs-install',
			'text'  => __( 'Installation instructions', 'zabisoft' ),
			'level' => 2,
		);
	}
}

$zs_toc[] = array(
	'id'    => 'zs-download',
	'text'  => __( 'Download', 'zabisoft' ),
	'level' => 2,
);

if ( count( $zs_toc ) < 3 ) {
	return;
}
?>
<nav class="zs-toc" aria-label="<?php esc_attr_e( 'Page sections', 'zabisoft' ); ?>">
	<p class="zs-toc__title"><?php zabisoft_icon( 'list' ); ?> <?php esc_html_e( 'On this page', 'zabisoft' ); ?></p>
	<ol>
		<?php foreach ( $zs_toc as $zs_item ) : ?>
			<li class="zs-toc__level-<?php echo esc_attr( $zs_item['level'] ); ?>">
				<a href="#<?php echo esc_attr( $zs_item['id'] ); ?>"><?php echo esc_html( $zs_item['text'] ); ?></a>
			</li>
		<?php endforeach; ?>
	</ol>
</nav>
