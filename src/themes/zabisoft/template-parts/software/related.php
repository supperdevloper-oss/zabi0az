<?php
/**
 * Related software entries.
 *
 * Uses the software categories first, then the platform taxonomy, then falls
 * back to the newest entries.
 *
 * @package ZabiSoft
 */

if ( ! post_type_exists( 'software' ) ) {
	return;
}

$zs_args = array(
	'post_type'           => 'software',
	'post__not_in'        => array( get_the_ID() ),
	'posts_per_page'      => 3,
	'ignore_sticky_posts' => true,
	'no_found_rows'       => true,
);

$zs_terms = get_the_terms( get_the_ID(), 'software_category' );

if ( ! is_wp_error( $zs_terms ) && ! empty( $zs_terms ) ) {
	$zs_ids = wp_list_pluck( $zs_terms, 'term_id' );

	$zs_args['tax_query'] = array(
		array(
			'taxonomy' => 'software_category',
			'field'    => 'term_id',
			'terms'    => $zs_ids,
		),
	);
}

$zs_related = new WP_Query( $zs_args );

if ( ! $zs_related->have_posts() ) {
	return;
}
?>
<section class="zs-related">
	<h2 class="zs-related__title"><?php esc_html_e( 'Related software', 'zabisoft' ); ?></h2>

	<div class="zs-grid zs-grid--3">
		<?php
		while ( $zs_related->have_posts() ) :
			$zs_related->the_post();
			get_template_part( 'template-parts/card' );
		endwhile;
		?>
	</div>
</section>
<?php
wp_reset_postdata();
